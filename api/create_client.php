<?php
require_once 'client_validate.php';
$json_response = array();
if (isset($_POST['firstname']) && !empty($_POST['firstname']) 
        && isset($_POST['lastname']) && !empty($_POST['lastname']) 
        && isset($_POST['email']) && !empty($_POST['email']) 
        && isset($_POST['phone']) && !empty($_POST['phone'])
        && isset($_POST['cname']) && !empty($_POST['cname'])
        && isset($_POST['password']) && !empty($_POST['password'])
        && isset($_POST['validupto_month'])
        && isset($_POST['validupto_year']) 
        && isset($_POST['plantype']) && !empty($_POST['plantype'])
        && isset($_POST['product_type']) && !empty($_POST['product_type'])
        && isset($_POST['nouser']) && !empty($_POST['nouser'])
        && isset($_POST['tomemory']) && !empty($_POST['tomemory'])
        ) {
     

    if(!empty($_POST['validupto_month']) || !empty($_POST['validupto_year'])){
        
    
    // Set autocommit to off
    //mysqli_autocommit($db_con,FALSE);  

    $firstname = filter_input(INPUT_POST, "firstname");
    $firstname = preg_replace("/[^a-zA-Z ]/", "", $firstname); //filter name
    $firstname = mysqli_real_escape_string($db_con, $firstname);

    $lastname = filter_input(INPUT_POST, "lastname");
    $lastname = preg_replace("/[^a-zA-Z ]/", "", $lastname); //filter name
    $lastname = mysqli_real_escape_string($db_con, $lastname);
    $email = filter_input(INPUT_POST, "email");
    $email = preg_replace("/[^a-zA-Z0-9_@.-]/", "", $email); //filter email
    $email = mysqli_real_escape_string($db_con, $email);

    $phone = filter_input(INPUT_POST, "phone");
    $phone = preg_replace("/[^0-9]/", "", $phone); //filter phone
    $phone = mysqli_real_escape_string($db_con, $phone);

    $company = filter_input(INPUT_POST, "cname");
   // $company = preg_replace("/[^0-9A-Za-z]/", "", $company); //filter phone
    $company = mysqli_real_escape_string($db_con, $company);
    $password = filter_input(INPUT_POST, "password");
    $password = mysqli_real_escape_string($db_con, $password);
    $validupto = filter_input(INPUT_POST, "validupto_month");
    $validupto = mysqli_real_escape_string($db_con, $validupto);
    $validupto_year = filter_input(INPUT_POST, "validupto_year");
    $validupto = strtotime(date("Y-m-d", strtotime("+" . $validupto . " " . $validupto_year))); //end of validity in time stamp
    $plantype = filter_input(INPUT_POST, "plantype");
    $product_type = filter_input(INPUT_POST, "product_type");
    $product_type = mysqli_real_escape_string($db_con, $product_type);
    $total_user=$_POST['nouser'];
    $total_memory=$_POST['tomemory'];
    //$image = addslashes(file_get_contents($_FILES['image']['tmp_name']));
    $image="";
    $chkUserMail = mysqli_query($db_con, "select * from  tbl_aggregate_user_master where email='$email'") or die('Error:' . mysqli_error($db_con));
    $chkDuplicateCompany = mysqli_query($db_con, "select * from  `tbl_client_master` where company='$company'");
    if ((mysqli_num_rows($chkUserMail) > 0) || (mysqli_num_rows($chkDuplicateCompany) > 0)) {
        
        //echo '<script>alert("User Already Registerd Using this Email Id Or Company Already Exist!")</script>';
                $json_response['status']="false";
                $json_response['message']="User Already Registerd Using this Email Id Or Company Already Exist!";
                $json= json_encode($json_response);
                echo $json;
                exit();
    } else {
        $url = "http://ecrmclient.ezeepea.in:8732/ECRMServices.svc/RegisterLeadCustomer/?Name=$firstname&EmailId=$email&ContactNumber=$phone&CompanyName=$company&Password=$password"; // url of api
        $url = filter_var($url, FILTER_SANITIZE_URL); //filter bad url
        $data = file_get_contents($url);
        $response = json_decode($data, true);
        print_r($response);
        if ($response['Code'] == 000) {
           if (!empty($response['CustomerId'])) {

               $coustomer_id = $response['CustomerId'];
        
                $create_client = mysqli_query($db_con, "insert into `tbl_client_master`(`fname`,`lname`,`email`,`company`,`password`,`profile`,`plan_type`,`valid_upto`,`product_type`,`total_memory`,`total_user`)values('$firstname','$lastname','$email','$company',sha1('$password'),'$image','$plantype','$validupto','$product_type','$total_memory','$total_user')")or die(mysqli_error($db_con));
                $lastinsertid = mysqli_insert_id($db_con);
                if ($create_client) {
                    $client_status = createNewDB($company, $lastinsertid, $email, $password, $db_con, $date, $firstname, $lastname, $phone, $image, $dbhost, $dbuser, $dbpwd, $product_type, $projectName, $coustomer_id);
                  //print_r($client_status);
                    if ($client_status['status']) {


                        if (array_key_exists("aggrigate_id", $client_status)) {
                            $connection = $client_status['connect'];
                            if (!empty($client_status[db_name])) {
                                $qry_remove_db = mysqli_query($connection, "DROP DATABASE $client_status[db_name]");
                                $qry_remove_aggregate_id = mysqli_query($db_con, "Delete From `tbl_aggregate_user_master` where tbl_ag_id=$client_status[aggrigate_id]");
                                $qry_remove_client_id = mysqli_query($db_con, "Delete From `tbl_client_master` where client_id='$lastinsertid'");
                            }
                        } elseif (array_key_exists("connect", $client_status)) {
                            $connection = $client_status['connect'];
                            if (!empty($client_status['db_name'])) {
                                $qry_remove_db = mysqli_query($connection, "DROP DATABASE $client_status[db_name]");
                                $qry_remove_client_id = mysqli_query($db_con, "Delete From `tbl_client_master` where client_id='$lastinsertid'");
                            }
                        } else {

                            $qry_remove_client_id = mysqli_query($db_con, "Delete From `tbl_client_master` where client_id='$lastinsertid'");
                        }
                        //mysqli_rollback($db_con);
                        //echo '<script>taskFailed("client_create", "Company Creation Failed!")</script>';
                    } else {
                        $qry_update_crm = mysqli_query($db_con, "update tbl_client_master set crm_cid='$coustomer_id' where client_id='$lastinsertid'");
                        //mysqli_commit($db_con);
                       // echo'<script> taskSuccess("client_create","Company created successfully"); </script>';
                        
                        $json_response['status']="true";
                        $json_response['message']="Company created successfully";
                        $json= json_encode($json_response);
                        echo $json;
                        exit();
                    }
//             
                } else {
                    //echo '<script>taskFailed("client_create", "Company Create Failed!")</script>';
                }
//        }
            }
       }else{
//            $json_response['status']="false";
//            $json_response['message']="Could not get response from ecrm.";
//            $json= json_encode($json_response);
//            echo $json;
//            exit();
        }
    }
    if (($response['Code'] == 101) || ($response['Code'] == 130)) {
        //echo "<script>alert('Client Create Failed');</script>"; // client creation fail
        
            $json_response['status']="false";
            $json_response['message']="Client Create Failed";
            $json= json_encode($json_response);
            echo $json;
            exit();
    }
    if (($response['Code'] == 102) || ($response['Code'] == 103) || ($response['Code'] == 104)) {
       // echo "<script>$(document).ready(function(){" . "$('#error').html('Error:-" . $response['Message'] . "')});</script>";
        $json_response['status']="false";
        $json_response['message']=$response['Message'];
        $json= json_encode($json_response);
        echo $json;
        exit();
        
    }

    if (($response['Code'] == 105) || ($response['Code'] == 106) || ($response['Code'] == 110)) {
        //echo "<script>$(document).ready(function(){" . "$('#error').html('Error:-" . $response['Message'] . "')});</script>";
        $json_response['status']="false";
        $json_response['message']=$response['Message'];
        $json= json_encode($json_response);
        echo $json;
        exit();
    }

    if (($response['Code'] == 107) || ($response['Code'] == 108) || ($response['Code'] == 109)) {
        //echo "<script>$(document).ready(function(){" . "$('#error').html('Error:-" . $response['Message'] . "')});</script>";
        $json_response['status']="false";
        $json_response['message']=$response['Message'];
        $json= json_encode($json_response);
        echo $json;
        exit();
    }
    if (($response['Code'] == 127) || ($response['Code'] == 128) || ($response['Code'] == 129)) {
        //echo "<script>$(document).ready(function(){" . "$('#error').html('Error:-" . $response['Message'] . "')});</script>";
        $json_response['status']="false";
        $json_response['message']=$response['Message'];
        $json= json_encode($json_response);
        echo $json;
        exit();
    }
}
else{
        $json_response['status']="false";
        $json_response['message']="Required parameter missing";
        $json= json_encode($json_response);
        echo $json;
        exit();
}
}else{
        $json_response['status']="false";
        $json_response['message']="Required parameter missing";
        $json= json_encode($json_response);
        echo $json;
        exit();
}
function createNewDB($company, $lastinsertid, $email, $password, $db_con, $date, $firstname, $lastname, $phone, $image, $dbHost, $dbUser, $dbPwd, $product_type, $projectName, $coustomer_id) {
    $conn = mysqli_connect($dbHost, $dbUser, $dbPwd);
    if ($conn) {
        // Create database
        $db_company= preg_replace("/[^A-Za-z]/","_", $company); //filter phone
        $ddb_name = "Ezee_" . $db_company . "_" . strtotime($date); //name of database
        //$licenseKey=NULL;
        $licenseKey = generateLicenseKey($ddb_name, $lastinsertid);
        
        $sql = "CREATE DATABASE $ddb_name";
        $result = mysqli_query($conn, $sql); //create new database for particular client
        if ($result) {
            $conn = new mysqli($dbHost, $dbUser, $dbPwd, $ddb_name); // connection with dynamic database
            $query = '';
            $sqlScript = file('../db_file/ezeefile_fresh.sql'); //fresh database file
            
           
            foreach ($sqlScript as $line) {

                $startWith = substr(trim($line), 0, 2);
                $endWith = substr(trim($line), -1, 1);

                if (empty($line) || $startWith == '--' || $startWith == '/*' || $startWith == '//') {
                    continue;
                }

                $query = $query . $line;
                if ($endWith == ';') {
                    
                    $tbl_qry = mysqli_query($conn, $query);
                    $query = '';
                    if ($tbl_qry) {
                        
                    } else {
                        return array("status" => True, "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "qry_error:" . $query);
                    }
                }
            }
            $update_dbname_clientqry = mysqli_query($db_con, "update `tbl_client_master` SET db_name='$ddb_name', license_key='$licenseKey' where client_id='$lastinsertid'");
            if ($update_dbname_clientqry) {
                $qry = mysqli_query($db_con, "insert into `tbl_aggregate_user_master` (`email`,`password`,`client_id`,`db_name`)values('$email',sha1('$password'),'$lastinsertid','$ddb_name')"); //insert in aggregate table
                if ($qry) {
                    $aggrigate_id = mysqli_insert_id($db_con);
                    $role_fieldName = array();
                    $data_role_col = mysqli_query($db_con, "show COLUMNS FROM `tbl_user_roles`")or die(mysqli_error($db_con));
                    while ($row = mysqli_fetch_assoc($data_role_col)) {
                        $role_fieldName[] = $row['Field'];
                    }
                    unset($role_fieldName[0]); // remove role id becoz it is autoincrement
                    $data_cols_role = implode(",", $role_fieldName);
                    $result_cols = array();
                    $selected_roles_data = mysqli_query($db_con, "select $data_cols_role from `tbl_user_roles` where role_id='$product_type'");
                    $newdata = mysqli_fetch_all($selected_roles_data);
                    $new_imploded_data = "'" . implode("'" . "," . "'", $newdata[0]) . "'";
                    //echo  "insert into `tbl_user_roles`($data_cols_role)values($new_imploded_data)";
                    $Insert_New_User = mysqli_query($conn, "insert into `tbl_user_roles`(role_id,$data_cols_role)values('2',$new_imploded_data)");
                    // mysqli_error($conn);
                    $new_user_role = mysqli_insert_id($conn);
                    if ($Insert_New_User) {
                        $create = mysqli_query($conn, "insert into tbl_user_master (`user_email_id`, `first_name`, `last_name`, `password`, `designation`, `phone_no`, `profile_picture`, `superior_name`, `superior_email`, `user_created_date`, `emp_id`,`usr_acvt_dacvt`) values('$email','$firstname','$lastname',sha1('$password'),'null','$phone','$image','null','null','$date', 'null','1')");
                        $user_id = mysqli_insert_id($conn);
                        if ($create) {

                            $grp_to_um = mysqli_query($conn, "INSERT INTO `tbl_bridge_role_to_um` (`role_id`,`user_ids`) VALUES ('$new_user_role','$user_id')");
                            if ($grp_to_um) {
                                $create_Root_Strg = mysqli_query($conn, "insert into `tbl_storage_level`(`sl_id`,`sl_name`,`sl_parent_id`,`sl_depth_level`) values('113','$company','0',0)");
                                if ($create_Root_Strg) {
                                    $storage_permission = mysqli_query($conn, "insert into `tbl_storagelevel_to_permission`(`user_id`,`sl_id`) values('$user_id','113')");
                                    if ($storage_permission) {
//                                    $company = preg_replace('/[^A-Za-z0-9\-]/', '', $company);
//                                    $ftp_server = "192.168.2.112"; //connection to ftp
//                                    $ftp_conn = ftp_connect($ftp_server);
//                                    $login = ftp_login($ftp_conn, "Administrator", "yadav@1234"); //login to ftp server
//                                    //check befor folder exist or not
//                                    if (ftp_mkdir($ftp_conn, $company)) {
//                                        // echo "Successfully created $company";
//                                    
//                                    } else {
//                                        return array("status" => True, "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "Client database Storage Level Permission Problem(Could not connect to $ftp_server)", "aggrigate_id" => $aggrigate_id);
//                                    }
//                                    // then do something...
//                                    // close connection
//                                    ftp_close($ftp_conn);
                                        
                                       //require '../mail.php';
                                        $mail = mailClientCreate($email, $password, $projectName, $coustomer_id);
                                        if ($mail) {
                                            
                                        } else {

                                            return array("status" => True, "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "Mail Not Sent", "aggrigate_id" => $aggrigate_id);
                                        }
                                        
                                    
                                    } else {
                                        return array("status" => True, "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "Client database Storage Level Permission Problem-" . mysqli_error($conn), "aggrigate_id" => $aggrigate_id);
                                    }
                                } else {
                                    return array("status" => True, "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "Client database Storage Level Problem" . mysqli_error($conn), "aggrigate_id" => $aggrigate_id);
                                }
                            } else {
                                return array("status" => True, "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "Client database Group To User  Master Problem" . mysqli_error($conn), "aggrigate_id" => $aggrigate_id);
                            }
                        } else {
                            return array("status" => True, "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "Client database User Master Problem" . mysqli_error($conn), "aggrigate_id" => $aggrigate_id);
                        }
                    } else {
                        return array("status" => True, "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "Client User Role Creation Table" . mysqli_error($conn), "aggrigate_id" => $aggrigate_id);
                    }
                } else {
                    return array("status" => True, "msg" => "Error creating Company", "connect" => $conn, "db_name" => $ddb_name, "aggrigate_id" => $aggrigate_id, "dev_msg" => "Aggregate Table Error");
                }
            } else {
                return array("status" => True, "msg" => "Error creating Company", "connect" => $conn, "db_name" => $ddb_name, "dev_msg" => "Client master table failed");
            }
            mysqli_close($conn);
            //return TRUE;
        } else {
            return array("status" => True, "msg" => "Error creating Company", "dev_msg" => "Database Creation Failed");
        }
    } else {
        return array("status" => True, "msg" => "Error creating Connection", "dev_msg" => "Connection Failed");
    }
}

function generateLicenseKey($clientdb, $clientId){

            $key = '987654123';
            $string = $clientdb.'%'.$clientId;

            $iv = mcrypt_create_iv(
                mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC),
                MCRYPT_DEV_URANDOM
            );

            $encrypted = base64_encode(
                $iv .
                mcrypt_encrypt(
                    MCRYPT_RIJNDAEL_128,
                    hash('sha256', $key, true),
                    $string,
                    MCRYPT_MODE_CBC,
                    $iv
                )
            );

            return $encrypted;

}



function mailClientCreate($to, $pass, $projectName,$coustomer) {

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
										Your new Login password for '. $projectName . ' is <br>
                                                                                <strong>Your Customer Number is </strong> - ' . $coustomer . '<br>
                                                                                <strong>Your Username is </strong> - ' . $to . '<br>
										<strong>Password </strong> - ' . $pass . '<br>
                                                                                    
										<strong>Url - </strong>http://'. $_SERVER['HTTP_HOST'] . '<br>
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
    $mail->addAddress($to, $username);
    //$mail->addAddress($superiorEmail, $superiorName);
    //$mail->addBCC('soft.dev5@cbsl-india.com');
    $mail->Subject = "Your Email ID And  password for $projectName";
    $mail->msgHTML($msgbody);
    $mail->AltBody = 'New Notifiation from ' . $projectName . '';
    //$mail->addAttachment('PHPMailer/examples/images/phpmailer_mini.png');
    if (!$mail->send()) {
        return false;
    } else {
        
        return true;
    }
}
    

?>