<?php

require_once 'connection.php';


if (isset($_POST['userid']) && !empty($_POST['userid'])
    && isset($_POST['fname']) && !empty($_POST['fname'])
    && isset($_POST['lname']) && !empty($_POST['lname'])
    && isset($_POST['phn']) && !empty($_POST['phn'])
    && isset($_POST['desg']) && !empty($_POST['desg'])
    && isset($_POST['email']) && !empty($_POST['email'])
         && isset($_POST['log']) && !empty($_POST['log'])
        && isset($_POST['ip']) && !empty($_POST['ip']))
{
    $userid = $_POST['userid'];
    $fname = $_POST['fname'];
    $fname = mysqli_real_escape_string($con, $fname);
    $lname =  $_POST['lname'];
    $lname = mysqli_real_escape_string($con, $lname);
    $phone = $_POST['phn'];
    $phone = mysqli_real_escape_string($con, $phone);
    $email = $_POST['email'];
    $email = mysqli_real_escape_string($con, $email);
    $logs = $_POST['log'];
    $logs = mysqli_real_escape_string($con,$logs);
    $designation = $_POST['desg'];
    $ip=$_POST['ip'];
	date_default_timezone_set("Asia/Kolkata");
    $date = date("Y-m-d h:i:s");   

    
    $edit = mysqli_query($con, "update tbl_user_master set `user_email_id`='$email', `first_name`='$fname', `last_name`='$lname', `phone_no`='$phone', designation='$designation' where user_id='$userid'"); 
    if ($edit) {

        $log = mysqli_query($con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$userid','$fname $lname',null,null,'Updated his profile : $logs ','$date',null,'$ip','')") or die('error : ' . mysqli_error($con));
        //  echo '<script>taskSuccess("profile","User Profile Updated Successfully !");</script>';

        if($log){

        $result = array();
        $result['message'] ='User Profile Updated Successfully !';
        $result['error']='false';

        echo  json_encode($result);  
        }
      
        //  header('location:'.$_SERVER['HTTP_REFERER']);
    } else {
        // echo '<script>taskFailed("profile","User Profile not Updated !");</script>';

        $result = array();
        $result['message'] ='User Profile not Updated !';
        $result['error']='true';

        echo  json_encode($result);
    }

}


//for change pasword
if (isset($_POST['userid'])&&!empty('userid')&&isset($_POST['pwd'])&&!empty('pwd')&& isset($_POST['name']) && !empty($_POST['name'])
   &&isset($_POST['ip']) && !empty($_POST['ip'])) {

    $pwd = $_POST['pwd'];
    $pwd = mysqli_real_escape_string($con, $pwd);
    $userid = $_POST['userid'];
    $ip=$_POST['ip'];
    $name = $_POST['name'];

	date_default_timezone_set("Asia/Kolkata");
    $date = date("Y-m-d h:i:s"); 

    $update = mysqli_query($con, "update tbl_user_master set password=sha1('$pwd') where user_id='$userid'");
    if ($update) {
        $log = mysqli_query($con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$userid','$name ',null,null,'password change','$date',null,'$ip',null)") or die('error : ' . mysqli_error($con));
        //echo'<script>taskSuccess("profile","password updated Successfully !");</script>';
       
		$result = array();
        $result['message'] ='Password updated Successfully !';
        $result['error']='false';

        echo  json_encode($result);


    }

    else{

        $result = array();
        $result['message'] ='Password not updated  !';
        $result['error']='true';

        echo  json_encode($result);

    }
}






?>