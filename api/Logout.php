<?php

require_once 'connection.php';
if(isset($_POST['userid'])&&!empty($_POST['userid'])){
	
 	$userid = $_POST['userid'];
           date_default_timezone_set("Asia/Kolkata");
                 $date = date("Y-m-d H:i");  
 $update=mysqli_query($con,"update tbl_user_master set current_login_status='0', last_active_logout='$date' where  user_id='$userid'") or die('error : '.mysqli_error($con));
	
        $lastlogin=mysqli_query($con,"select id from tbl_ezeefile_logs where id in ( select max(id) from tbl_ezeefile_logs where user_id = '$userid' and action_name = 'Login/Logout')") or die('Error'.mysqli_error($con));
        $rwlastlogin= mysqli_fetch_assoc($lastlogin);
        $logUpdate= mysqli_query($con, "update tbl_ezeefile_logs set end_date='$date' where id='$rwlastlogin[id]'") or die('Error'. mysqli_error($con));
	     if($logUpdate){
				 
				 $res = array();
				 $res['message'] ='Logout successful';
				 $res['error']= 'false';
				 echo json_encode($res);
				 
				 
			 }
	
	else{
		
		 $res = array();
				 $res['message'] ="Server Error can't logout";
				 $res['error']= 'true';
				 echo json_encode($res);
				 
		
	}



}



?>