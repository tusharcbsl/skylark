<?php
require_once 'client_validate.php';
if(isset($_POST['name']) && isset($_POST['pwd']) && !empty($_POST['name']) && !empty($_POST['pwd'])
   && isset($_POST['ip']) && !empty($_POST['ip']))
{
$name=$_POST['name'];
$pwd=$_POST['pwd'];
$remoteHost =$_POST['ip'];

        $result=array();
        $validate_user= mysqli_escape_string($db_con, $_POST['name']);
        $validate_pwd= mysqli_escape_string($db_con, $_POST['pwd']);
        $validate_pwd=sha1($validate_pwd);
        $validate_user_qry= mysqli_query($db_con, "select tbl_ag_id,client_id,db_name from tbl_aggregate_user_master where email='$validate_user' and password='$validate_pwd'") or die(mysqli_error($db_con));
        if(mysqli_num_rows($validate_user_qry)>0)
        {
          $datas= mysqli_fetch_assoc($validate_user_qry);
          $check_validity_qry= mysqli_query($db_con, "select client_id,valid_upto,total_memory,total_user,license_key,product_type from  tbl_client_master where client_id='$datas[client_id]'");//Query get validity of particular company user
          $validity_date= mysqli_fetch_assoc($check_validity_qry);//fetch validity timestamp from client table
          date_default_timezone_set("Asia/Kolkata");
          if(strtotime(date("Y-m-d"))<$validity_date['valid_upto'])
          {
            $result['status']=100;
            $dbname=$datas['db_name'];// set db name of the company
            $_POST['apikey']=$validity_date['license_key'];
            require_once './connection.php';
            $con= mysqli_connect($dbhost, $dbuser, $dbpwd, $dbname)or die("unable to connect");
            $qry= mysqli_query($con, "select * from tbl_user_master where user_email_id='$name' and password=sha1('$pwd')") or die('Error'. mysqli_error($con));
            if(mysqli_num_rows($qry)>=1)
            {
                $fetch= mysqli_fetch_assoc($qry);
                 date_default_timezone_set("Asia/Kolkata");
                 $date = date("Y-m-d H:i");
                $id=$fetch['user_id'];
                $update= mysqli_query($con,"update tbl_user_master set current_login_status='1',last_active_login='$date' where user_id='$id'");

                if($update){

                $result=array();
                $result["status"]='True';//"status"=>"true","Login_id"=>$id
                $result['userID']=$fetch['user_id'];
                $result['Email']=$fetch['user_email_id'];
                $result['FirstName']=$fetch['first_name'];
                $result['LastName']=$fetch['last_name'];
                $result['Phone']=$fetch['phone_no'];
                $result['Designation']=$fetch['designation'];
                $result['tokenId'] = $fetch['fb_tokenid'];
                $result['apikey']=$validity_date['license_key'];
                $result['product']=$validity_date['product_type'];
                $result['clientid']=$validity_date['client_id'];
                     $userid = $fetch['user_id'];
                     $username =$fetch['first_name']." ".$fetch['last_name'];

                      $log = mysqli_query($con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$userid','$username',null,null,'Login/Logout','$date',null,'$remoteHost','')") or die('error : ' . mysqli_error($con));

                if($log){

                $json= json_encode($result);
                echo $json;

                }

                }


            }
             else 
                 {

                $result=array("status"=>"False","Login_id"=>"0");
                $json= json_encode($result);
                echo $json;
             }

          }else{
              $result=array("status"=>102,"Error"=>"Validity Expired.");
              $json= json_encode($result);
              echo $json;
          }
        }else{
            $result=array("status"=>101,"Error"=>"Username or Password do not match.");
            $json= json_encode($result);
            echo $json;
        }

}
?>
