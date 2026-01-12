<?php

require_once 'connection.php';

if(isset($_POST['modi'])&&!empty($_POST['modi'])&&isset($_POST['modify_slname'])&&!empty($_POST['modify_slname'])&&isset($_POST['ip'])&&!empty($_POST['ip'])&&isset($_POST['name'])&&!empty($_POST['name'])&&isset($_POST['userid'])&&!empty($_POST['userid'])){

    $sl_id = $_POST['modi'];
    $modify = $_POST['modify_slname'];
    $ip=$_POST['ip'];
	 $name=$_POST['name'];
	 $userid=$_POST['userid'];
	 $response = array();
	  $modiStorage = mysqli_query($con, "Select * from tbl_storage_level where sl_id = '$sl_id'") or die('Error in checkDublteStorage:' . mysqli_error($con));
            $rwmodiStorage = mysqli_fetch_assoc($modiStorage);
            $updateToName = $rwmodiStorage['sl_name'];

            $sql = "update tbl_storage_level set sl_name = '$modify' WHERE sl_id = '$sl_id' ";
            $sql_run = mysqli_query($con, $sql) or die("error:" . mysqli_errno($con));
            if ($sql_run) {
					
					 date_default_timezone_set("Asia/Kolkata");
				   	$date = date("Y-m-d H:i");
                $log = mysqli_query($con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$userid', '$name',null,'$sl_id','Storage Name $updateToName rename to $modify.','$date', null,'$ip','')") or die('error : ' . mysqli_error($con));
               // echo'<script>taskSuccess("storage?id=' . urlencode(base64_encode($sl_id)) . '","Storage Updaetd Successfully !");</script>';
					
					$response['error']="false";
					$response['message']="Storage Updated Successfully !";
					echo json_encode($response);	
					
            } else {
              //  echo'<script>taskFailed("storage?id=' . urlencode(base64_encode($sl_id)) . '","Storage Updation Failed !");</script>';
						$response['error']="true";
					$response['message']="Storage Updation Failed !";
					echo json_encode($response);	
            }
            mysqli_close($con);
	
	
	
}

?>