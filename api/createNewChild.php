<?php

require_once 'connection.php';

if(isset($_POST['add_child'])&&!empty($_POST['add_child'])&&isset($_POST['create_child'])&&!empty($_POST['create_child'])&&isset($_POST['ip'])&&!empty($_POST['ip'])&&isset($_POST['name'])&&!empty($_POST['name'])&&isset($_POST['userid'])&&!empty($_POST['userid'])){

     $sl_id = $_POST['add_child'];//parent slid
     $create = $_POST['create_child'];//string foldername 
	 $ip=$_POST['ip'];
	 $name=$_POST['name'];
	 $userid=$_POST['userid'];
	 $response = array();
	 $checkLvlName = mysqli_query($con, "select * from tbl_storage_level where sl_parent_id='$sl_id' AND sl_name = '$create'") or die('Error in checkLvlName:' . mysqli_error($con));

            if (mysqli_num_rows($checkLvlName) > 0) {

             					
					$response['error']="true";
					$response['message']="Storage of Same Name Already Exist";
					echo json_encode($response);	
			
					
            } else {

                $parent = mysqli_query($con, "select * from tbl_storage_level where sl_id='$sl_id'") or die('Error:' . mysqli_error($con));

                $rwParent = mysqli_fetch_assoc($parent);

                $level = $rwParent['sl_depth_level'] + 1;
                if (!empty($create)) {
                    $sql = "insert into tbl_storage_level(sl_id, sl_name, sl_parent_id, sl_depth_level)VALUES (null, '$create', '$sl_id', '$level')";
                    $sql_run = mysqli_query($con, $sql) or die("error:" . mysqli_error($con));
                    $newChildId = mysqli_insert_id($con);
					date_default_timezone_set("Asia/Kolkata");
					$date = date("Y-m-d H:i");
                    $log = mysqli_query($con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$userid', '$name',null,'$newChildId','New Child $create Created.','$date', null,'$ip',null)") or die('error :' . mysqli_error($con));
                   
						   $response['error']="false";
					      $response['message']="Sub Folder Created Successfully";
					      echo json_encode($response);	
					
                }
            }
         
            mysqli_close($con);
	
	
	
}

?>