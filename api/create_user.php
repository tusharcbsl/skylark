<?php
   require_once 'client_validate.php';
   if (isset($_POST['firstname']) && !empty($_POST['firstname']) 
        && isset($_POST['lastname']) && !empty($_POST['lastname']) 
        && isset($_POST['email']) && !empty($_POST['email']) 
        && isset($_POST['phone']) && !empty($_POST['phone'])
        && isset($_POST['clientid']) && !empty($_POST['clientid'])
        && isset($_POST['password']) && !empty($_POST['password'])
        && isset($_POST['loguser_firstname']) && !empty($_POST['loguser_firstname'])
        && isset($_POST['loguser_lastname']) && !empty($_POST['loguser_lastname'])
        && isset($_POST['loguser_id']) && !empty($_POST['loguser_id'])
        && isset($_POST['empId']) && !empty($_POST['empId'])
        && isset($_POST['designation']) && !empty($_POST['designation'])
        && isset($_POST['groups']) && !empty($_POST['groups'])
        && isset($_POST['userRole']) && !empty($_POST['userRole'])
        && isset($_POST['slparentId']) && !empty($_POST['slparentId'])
        && isset($_POST['superiorName']) && !empty($_POST['superiorName'])
        && isset($_POST['superiorEmail']) && !empty($_POST['superiorEmail'])
        ) {
   
        $clientid = filter_input(INPUT_POST, "clientid");
        $loguser_firstname = filter_input(INPUT_POST, "loguser_firstname");
        $loguser_lastname = filter_input(INPUT_POST, "loguser_lastname");
        $loguser_id = filter_input(INPUT_POST, "loguser_id");
        
        $clientid = filter_input(INPUT_POST, "clientid");
        $clientid = filter_input(INPUT_POST, "clientid");
        $firstname = filter_input(INPUT_POST, "firstname");
        $firstname=preg_replace("/[^a-zA-Z ]/", "", $firstname);//filter name
        $firstname = mysqli_real_escape_string($db_con, $firstname);

        $lastname = filter_input(INPUT_POST, "lastname");
        $lastname=preg_replace("/[^a-zA-Z ]/", "", $lastname);//filter name
        $lastname = mysqli_real_escape_string($db_con, $lastname);
        $email = filter_input(INPUT_POST, "email");
        $email=preg_replace("/[^a-zA-Z0-9_@.-]/", "", $email);//filter email
        $email = mysqli_real_escape_string($db_con, $email);
        $empId = filter_input(INPUT_POST, "empId");
        $empId=preg_replace("/[^0-9_]/", "", $empId);//filter empid
        $empId = mysqli_real_escape_string($db_con, $empId);
        $phone = filter_input(INPUT_POST, "phone");
        $phone=preg_replace("/[^0-9]/", "", $phone);//filter phone
        $phone = mysqli_real_escape_string($db_con, $phone);
        
        $password = filter_input(INPUT_POST, "password");
        $password = mysqli_real_escape_string($db_con, $password);
        $designation = filter_input(INPUT_POST, "designation");
        $designation=preg_replace("/[^a-zA-Z ]/", "", $designation);//filter name
        $designation = mysqli_real_escape_string($db_con, $designation);
        $ip = $_POST["ip"];

        $groups = $_POST["groups"];
        $userRole = $_POST["userRole"];
        $userRole = mysqli_real_escape_string($db_con, $userRole);
        $slparentNameid = $_POST['slparentId'];
        $slparentNameid = mysqli_real_escape_string($db_con, $slparentNameid);
        $superiorName = filter_input(INPUT_POST, "superiorName");
        //order is should be like this 
        $superiorEmail = filter_input(INPUT_POST, "superiorEmail");
        $image = "";
        //$image = addslashes(file_get_contents($_FILES['image']['tmp_name']));

        $json_response = array();
        if(isset($_POST['clientid']))
        {
            $check_validity_qry= mysqli_query($db_con, "select * from  tbl_client_master where client_id='$clientid'");//Query get validity of particular company user
            $validity_date= mysqli_fetch_assoc($check_validity_qry);
            $cliendb = $validity_date['db_name'];
            $t_user=preg_replace("/[^0-9]/", "", $validity_date['total_user']);//total user allow 
            $validate_num_user = mysqli_query($db_con, "select count(email) as total_user from tbl_aggregate_user_master where client_id='$clientid'") or die('Error:' . mysqli_error($db_con));
            $total_user= mysqli_fetch_assoc($validate_num_user);
           // print_r($total_user);
            if($total_user['total_user']>= $t_user)
            {
               // echo '<script>taskFailed("createUser", "Can not Create User,User Limit Exceeded!")</script>'; 
                $json_response['status']="false";
                $json_response['message']="Can not Create User,User Limit Exceeded!";
                $json= json_encode($json_response);
                echo $json;


            }else{
            $chkUserMail = mysqli_query($db_con, "select * from tbl_aggregate_user_master where email='$email'") or die('Error:' . mysqli_error($db_con));

            if (mysqli_num_rows($chkUserMail) > 0) {
                //echo '<script>taskFailed("createUser", "User Already Registerd Using this Email Id !")</script>';
                $json_response['status']="false";
                $json_response['message']="User Already Registerd Using this Email Id !";
                $json= json_encode($json_response);
                echo $json;

            } else {
                 $aggr_create= mysqli_query($db_con, "insert into `tbl_aggregate_user_master`(`email`,`password`,`client_id`,`db_name`)values('$email',sha1('$password'),'$clientid','$cliendb')");//insert into aggregate table
                 if($aggr_create)
                 {
                      require_once './connection.php';
                     $con= mysqli_connect($dbhost, $dbuser, $dbpwd, $dbname)or die("unable to connect");
                    $create = mysqli_query($con, "insert into tbl_user_master (`user_id`, `user_email_id`, `first_name`, `last_name`, `password`, `designation`, `phone_no`, `profile_picture`, `superior_name`, `superior_email`, `user_created_date`, `emp_id`) values(null,'$email','$firstname','$lastname',sha1('$password'),'$designation','$phone','$image','$superiorName','$superiorEmail','$date', '$empId')") or die('Error' . mysqli_error($con));

                    if ($create) {
                        $user_id = mysqli_insert_id($con);
                        if (!empty($slparentNameid)) {
                            $insertPerm = mysqli_query($con, "insert into tbl_storagelevel_to_permission (user_id,sl_id) values('$user_id','$slparentNameid')") or die('Error: sl permission' . mysqli_error($con));
                            //$insertPerm_run = mysqli_query($con, $insertStp) or die('Error:' . mysqli_error($con));
                        }
                        $checkRole = mysqli_query($con, "select * from tbl_bridge_role_to_um where role_id='$userRole'");

                        if (mysqli_num_rows($checkRole) <= 0) {
                            $roleAsin = mysqli_query($con, "insert into tbl_bridge_role_to_um(role_id,user_ids) values('$userRole','$user_id')") or die('Error' . mysqli_error($con));
                        } else {
                            $rwCheckRole = mysqli_fetch_assoc($checkRole);
                            $useridsRole = $rwCheckRole['user_ids'];
                            if (!empty($useridsRole)) {
                                $useridsRole = $useridsRole . ',' . $user_id;
                            } else {
                                $useridsRole = $user_id;
                            }
                            $roleAsin = mysqli_query($con, "update tbl_bridge_role_to_um set user_ids ='$useridsRole' where role_id='$userRole'") or die('Error' . mysqli_error($con));
                        }

                        $groups = array_filter($groups, function($value) {
                            return $value !== '';
                        });
                        if (!empty($groups)) {
                            $flag = 0;
                            foreach ($groups as $groupid) {
                                $check = mysqli_query($con, "select * from tbl_bridge_grp_to_um where group_id='$groupid'");

                                if (mysqli_num_rows($check) <= 0) {
                                    $grpmap = mysqli_query($con, "insert into tbl_bridge_grp_to_um(group_id,user_ids) values('$groupid','$user_id')") or die('Error' . mysqli_error($con));
                                    if ($grpmap) {
                                        $flag = 1;
                                    }
                                } else {
                                    $rwCheck = mysqli_fetch_assoc($check);
                                    $userids = $rwCheck['user_ids'];
                                    if (!empty($userids)) {
                                        $userids = $userids . ',' . $user_id;
                                    } else {
                                        $userids = $user_id;
                                    }
                                    $grpmap = mysqli_query($con, "update tbl_bridge_grp_to_um set user_ids ='$userids' where group_id='$groupid'") or die('Error' . mysqli_error($con));
                                    if ($grpmap) {
                                        $flag = 1;
                                    }
                                }
                            }
                            if ($flag == 1) {
                                $log = mysqli_query($con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$loguser_id', '$loguser_firstname $loguser_lastname',null,null,'user $firstname $lastname created. user id : $user_id','$date',null,'$host/$ip','')") or die('error : ' . mysqli_error($con));
                                $logid = mysqli_insert_id($con);
                                $Cuser = $loguser_firstname . ' ' . $loguser_lastname;
                                //require_once './mail.php';
                                $subject = 'New user created';
                                $host = $_SERVER['REMOTE_ADDR'];
                                $mail = mailUserCreate($superiorEmail, $superiorName, $email, $firstname . ' ' . $lastname, $user_id, $password, $subject, $con, $projectName, $Cuser, $loguser_id);
                                if ($mail) {
                                    //echo'<script> taskSuccess("createUser","User created successfully"); </script>';

                                    $json_response['status']="true";
                                    $json_response['message']="User created successfully";
                                    $json= json_encode($json_response);
                                    echo $json;

                                }
                            } else {
                                $user = mysqli_query($con, "delete from tbl_user_master where user_id='$user_id'");
                                $logDel = mysqli_query($con, "delete from tbl_ezeefile_logs where id='$logid'");

                                $json_response['status']="false";
                                $json_response['message']="User creation failed";
                                $json= json_encode($json_response);
                                echo $json;

                            }
                        }
                    }
                 }
                else
                {
                    $json_response['status']="false";
                    $json_response['message']="User creation failed";
                    $json= json_encode($json_response);
                    echo $json;

                }
            }
            mysqli_close($con);
        }
        }
    }else{
        
        $json_response['status']="false";
        $json_response['message']="Required parameter missing";
        $json= json_encode($json_response);
        echo $json;
    }        
            
function mailUserCreate($superiorEmail, $superiorName, $email, $username, $user_id, $password, $subject, $con, $projectName,$Cuser,$userid) {

   
    $msgbody = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" style="font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
<head>
<style type="text/css">
img {
max-width: 100%;
}
body {
-webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6em;
}
body {
background-color: #f6f6f6;
}
@media only screen and (max-width: 640px) {
  body {
    padding: 0 !important;
  }
  h1 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h2 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h3 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h4 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h1 {
    font-size: 22px !important;
  }
  h2 {
    font-size: 18px !important;
  }
  h3 {
    font-size: 16px !important;
  }
  .container {
    padding: 0 !important; width: 100% !important;
  }
  .content {
    padding: 0 !important;
  }
  .content-wrap {
    padding: 10px !important;
  }
  .invoice {
    width: 100% !important;
  }
}
</style>
</head>

<body style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6em; background-color: #f6f6f6; margin: 0;" bgcolor="#f6f6f6">

<table class="body-wrap" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; background-color: #f6f6f6; margin: 0;" bgcolor="#f6f6f6"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top"></td>
		<td class="container" width="600" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; display: block !important; max-width: 600px !important; clear: both !important; margin: 0 auto;" valign="top">
			<div class="content" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; max-width: 600px; display: block; margin: 0 auto; padding: 20px;">
				<table class="main" width="100%" cellpadding="0" cellspacing="0" itemprop="action" itemscope itemtype="http://schema.org/ConfirmAction" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; border-radius: 3px; background-color: #fff; margin: 0; border: 1px solid #e9e9e9;" bgcolor="#fff"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="content-wrap" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 20px;" valign="top">
							<meta itemprop="name" content="Confirm Email" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;" /><table width="100%" cellpadding="0" cellspacing="0" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top">
										New User ' . $username . '(Email ID - ' . $email . ') has been created by ' . $Cuser . '!!!
                                                                                <strong>URL </script> - http://'. $_SERVER['HTTP_HOST'] . '<br>
										<strong>Username </strong> - ' . $email . '<br>
										<strong>Password </strong> - ' . $password . '
									</td>
								</tr><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="content-block" itemprop="handler" itemscope itemtype="http://schema.org/HttpActionHandler" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top">
										This is a System Generated mail please do not reply.
									</td>
								</tr><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; font-weight: bold;"><td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top">
										&mdash; ' . $projectName . ' 
                                                                                <br> Admin 
									</td>
								</tr></table></td>
					</tr></table><div class="footer" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; clear: both; color: #999; margin: 0; padding: 20px;">
					<table width="100%" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="aligncenter content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; vertical-align: top; color: #999; text-align: center; margin: 0; padding: 0 0 20px;" align="center" valign="top"> <a href="#" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; color: #ff0000; text-decoration: underline; margin: 0;">Track, manage and store your documents and reduce paper.</a></td>
						</tr></table></div></div>
		</td>
		<td style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top"></td>
	</tr></table></body>
</html>';


    date_default_timezone_set('Asia/Kolkata');
    $date = date("Y-m-d H:i:s");
   
    require_once '../application/PHPMailer/PHPMailerAutoload.php';
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->SMTPDebug = 0;
    $mail->Debugoutput = 'html';
    $mail->Host = "mail.cbsl-india.com";
    $mail->Port = 587;
    $mail->SMTPAuth = true;
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    $mail->Username = "ezeefileadmin@cbsl-india.com";
    $mail->Password = "Kdcs@08065";
    $mail->setFrom('no-reply@cbsl-india.com', 'System');
    //$mail->addReplyTo('', '');
    $mail->addAddress($email, $username);
    $mail->addAddress($superiorEmail, $superiorName);
    $mail->addBCC('ezeefileadmin@cbsl-india.com');
    $mail->Subject = $subject;
    $mail->msgHTML($msgbody);
    $mail->AltBody = 'New Notifiation from ' . $projectName . '';
    //$mail->addAttachment('PHPMailer/examples/images/phpmailer_mini.png');
    if (!$mail->send()) {
        return false;
    } else {
        $sender = $userid;
        $host = $_SERVER['REMOTE_ADDR'];
        $msgbody = mysqli_real_escape_string($con, $msgbody);
        $mailsent = mysqli_query($con, "insert into tbl_mail_list(`send_by`, `send_to`, `send_cc`, `send_bcc`, `subject`, `mail_body`, `alt_body`, `mail_time`, `ip`, `module_name`) "
                . "value('$sender','$email','$superiorEmail','','$subject','$msgbody','New Notifiation from $projectName','$date','$host','User Creation')") or die('Error' . mysqli_error($con));
        return true;
    }
}
 
       

?>