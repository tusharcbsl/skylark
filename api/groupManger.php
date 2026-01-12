<?php

require_once 'connection.php';

//group list 

if(isset($_POST['userid'])&&!empty($_POST['userid'])){
	
	
	 $userid =$_POST['userid'];
	
	 $sameGroupIDs=array();
	
	 $response = array();
	
	 $group= mysqli_query($con, "select group_id from tbl_bridge_grp_to_um where find_in_set('$userid',user_ids)") or die('Error'. mysqli_error($con));
    while($rwGroup= mysqli_fetch_assoc($group)){
        $sameGroupIDs[]=$rwGroup['group_id'];
    }
    $sameGroupIDs=array_unique($sameGroupIDs);
    sort($sameGroupIDs);
    $sameGroupIDs= implode(',',$sameGroupIDs);
	
	$users = mysqli_query($con, "select * from tbl_group_master where group_id in($sameGroupIDs) order by group_name asc ");
	
	while($rwUsers = mysqli_fetch_assoc($users)){
		
		 $temp = array();
		 $temp['groupname'] = $rwUsers['group_name'];
		 $temp['groupid'] = $rwUsers['group_id'];
		 
		array_push($response,$temp);
	
	
	}
	
	echo json_encode($response);
	
	//print_r($sameGroupIDs);

	
}

//group member list according to group 

if(isset($_POST['grpUserid'])&&!empty($_POST['grpUserid'])&&isset($_POST['sUserid'])&&!empty($_POST['sUserid'])){
 
 $id =$_POST['grpUserid'];
 $sUserid = $_POST['sUserid'];
 $grouplist = array();

 $groupUser=mysqli_query($con,"select * from tbl_bridge_grp_to_um where group_id='$id'");
        $rwGroupuser= mysqli_fetch_assoc($groupUser);
        $userIds=$rwGroupuser['user_ids'];
        $userIds= explode(",", $userIds);
        
            $user= mysqli_query($con, "select * from tbl_user_master ");
            while($rwUser= mysqli_fetch_assoc($user)){
                if($rwUser['user_id']!=1 && $rwUser['user_id']!=$sUserid){
                if(in_array($rwUser['user_id'], $userIds)){
                   // echo '<option selected value="'.$rwUser['user_id'].'">'.$rwUser['first_name'].' //'.$rwUser['last_name'].'</option>';
						 
						 $temp = array();
						 $temp['userid']= $rwUser['user_id'];
					     $temp['name']= $rwUser['first_name']." ".$rwUser['last_name'];                  
						 array_push($grouplist,$temp);
						
						 }
                    
                    else{
                    
                    
                    }
						 
               
            }
                
                


}
     
     echo json_encode($grouplist);
}

//getting all dms user list
if(isset($_POST['id'])&&!empty($_POST['id'])){
 
 $id =$_POST['id'];
 $grouplist = array();

 
   $user= mysqli_query($con, "select * from tbl_user_master");
            while($rwUser= mysqli_fetch_assoc($user)){
                if($rwUser['user_id']!=1 && $rwUser['user_id']!=$id){
                
                   // echo '<option selected value="'.$rwUser['user_id'].'">'.$rwUser['first_name'].' //'.$rwUser['last_name'].'</option>';
						 
						 $temp = array();
						 $temp['user_id']= $rwUser['user_id'];
					    $temp['name']= $rwUser['first_name']." ".$rwUser['last_name'];
						 array_push($grouplist,$temp);
					
						 }	 
                  }
	 echo json_encode($grouplist);

}

//Modify group

 if (isset($_POST['gId'])&&!empty($_POST['gId'])
 &&isset($_POST['userID'])&&!empty($_POST['userID'])
 &&isset($_POST['userIDS'])&&!empty($_POST['userIDS'])
 &&isset($_POST['userName'])&&!empty($_POST['userName'])
  &&isset($_POST['iP'])&&!empty($_POST['iP'])
	   &&isset($_POST['grpName'])&&!empty($_POST['grpName'])
 
 
 ) {
 
             date_default_timezone_set("Asia/Kolkata");
             $date = date("Y-m-d H:i");

            $gid = $_POST['gId'];
            $groupName = $_POST['grpName'];
			$host = $_POST['iP'];
            $userid = $_POST['userID'];
            $username = $_POST['userName'];    
            $userIds = $_POST['userIDS'];
     
     //["8","9"]
     //8,9
                 $userIds = json_decode($_POST['userIDS'],true);    
                 $userIds = implode(',', $userIds);
                 $userIds =explode(",",$userIds);
                 $userIds=array_unique($userIds);
                 sort($userIds);
                 $userIds= implode(',', $userIds);   
     
     
     //print_r($userIds);
     //die;
          
     
           //print_r($userIds);
           //die;
     
             $groupName = mysqli_real_escape_string($con, $groupName);
            $edit = mysqli_query($con, "update tbl_group_master set `group_name`='$groupName' where group_id='$gid'") or die('Error : ' . mysqli_error($con));
            if ($edit) {
//                $GroupName = mysqli_query($con, "select group_name from tbl_group_master where group_id='$gid'");
//                $rwGrpName = mysqli_fetch_assoc($GroupName);
//                $OldgrpName = $rwupdateName['group_name'];
                if (!empty($userIds)) {
                    
                    $grp = mysqli_query($con, "select * from tbl_bridge_grp_to_um where group_id='$gid'");
                    if (mysqli_num_rows($grp) > 0) {
                        $grpToUm = mysqli_query($con, "update tbl_bridge_grp_to_um set user_ids='$userIds' where group_id='$gid'");
                    } else {
                        $grpToUm = mysqli_query($con, "insert into tbl_bridge_grp_to_um(id,group_id, user_ids) values(null,'$gid','$userIds')");
                    }
                }

                $log = mysqli_query($con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$userid', '$username',null,null,'Group Name $groupName Updated','$date', null,'$host',null)") or die('error :' . mysqli_error($con));
					 
					 if($log){
					 
				    $response = array();
                         
					$response['message'] ='Group Updated Successfully !';
					$response['error'] ='false';
                         
                    
					echo json_encode($response);
					 
					 }
					 
					 else{
					 
					$response = array();
					$response['message'] ='Group not updated ';
					$response['error'] ='true';
					echo json_encode($response);
					 
					 }
					 
               // echo'<script>taskSuccess("groupList","Group Updated Successfully !");</script>';
			
					
					
            }
             mysqli_close($con); 
        }

//delete group

 if (isset($_POST['delGid'])&&!empty($_POST['delGid'])
 &&isset($_POST['delUserid'])&&!empty($_POST['delUserid'])
 &&isset($_POST['delUserName'])&&!empty($_POST['delUserName'])
  &&isset($_POST['delip'])&&!empty($_POST['delip'])
	  
 
 
 ) {
             date_default_timezone_set("Asia/Kolkata");
             $date = date("Y-m-d H:i");        
     
            $id = $_POST['delGid'];
            $host = $_POST['delip'];
            $username = $_POST['delUserName'];
            $userid = $_POST['delUserid'];
               
            $delNme = mysqli_query($con, "select group_name from tbl_group_master where group_id='$id'");
            $rwdel = mysqli_fetch_assoc($delNme);
            $delName = $rwdel['group_name'];
            $log = mysqli_query($con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$userid', '$username',null,null,'Group $delName Deleted','$date', null,'$host','')") or die('error :' . mysqli_error($con));
            $del = mysqli_query($con, "delete from tbl_group_master where group_id='$id'");
            if ($del) {
                $delbridge = mysqli_query($con, "delete from tbl_bridge_grp_to_um where group_id='$id'");

               // echo'<script>taskSuccess("groupList","Group Deleted Successfully !");</script>';
				
					    $response = array();
                         
					$response['message'] ='Group Deleted Successfully !';
					$response['error'] ='false';
                         
                    
					echo json_encode($response);
                
                
            }
     else{
     
					    $response = array();
                         
					$response['message'] ='Group not deleted  !';
					$response['error'] ='true';
                         
                    
					echo json_encode($response);
     
     
     }
             mysqli_close($con); 
        }

//add group


    if (
 isset($_POST['addUserid'])&&!empty($_POST['addUserid'])
 &&isset($_POST['addUserids'])&&!empty($_POST['addUserids'])
 &&isset($_POST['addUsername'])&&!empty($_POST['addUsername'])
 &&isset($_POST['addGrpName'])&&!empty($_POST['addGrpName'])
  &&isset($_POST['addIp'])&&!empty($_POST['addIp'])) {
		  
		      //$userid = array();
	                   date_default_timezone_set("Asia/Kolkata");
                      $date = date("Y-m-d H:i");

                    $userid = json_decode($_POST['addUserids'],true);
	                 $userid = implode(',', $userid);
                    $userid =explode(",",$userid);
                    $userid=array_unique($userid);
                    sort($userid);
                    $userid= implode(',', $userid);   
           // $userid[]='1';
           // $userid[]=$_SESSION['cdes_user_id'];
            //$userid= array_unique($userid);
            
          // $userid = implode(",", $userid);
	  
	         $host = $_POST['addIp'];
	         $username = $_POST['addUsername'];
	         $seesionUserid = $_POST['addUserid'];
	     
           //  $groupName = filter_input(INPUT_POST, "groupName");
	         $groupName = $_POST['addGrpName'];
            $groupName = mysqli_real_escape_string($con, $groupName);
            $check = mysqli_query($con, "select group_id from tbl_group_master where group_name='$groupName'");
            if (mysqli_num_rows($check) <= 0) {
                $insert = mysqli_query($con, "insert into tbl_group_master(group_id,group_name) values(null,'$groupName')") or die('Error : ' . mysqli_error($con));
                $gid = mysqli_insert_id($con);
                if ($insert) {
                    if (!empty($userid)) {
                       
                        $grp = mysqli_query($con, "select * from tbl_bridge_grp_to_um where group_id='$gid'");
                        if (mysqli_num_rows($grp) > 0) {
                            $grpToUm = mysqli_query($con, "update tbl_bridge_grp_to_um set user_ids='$userid' where group_id='$gid'");
                        } else {
                            $grpToUm = mysqli_query($con, "insert into tbl_bridge_grp_to_um(id,group_id, user_ids,roleids) values(null,'$gid','$userid','')");
                        }
                    }
                    $log = mysqli_query($con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$seesionUserid', '$username',null,null,'Group $groupName Added','$date', null,'$host','')") or die('error :' . mysqli_error($con));
						 if($log){
                   //echo'<script>taskSuccess("groupList","Group Added Successfully !");</script>';
							 $response = array();
							 $response['message'] = 'Group Added Successfully';
							 $response ['error'] = 'false';
							 echo json_encode($response);
							 
                }
            } else {
               // echo'<script>taskFailed("groupList","Group already exist !");</script>';
						 
						 	 $response = array();
							 $response['message'] = 'Group already exist';
							 $response ['error'] = 'true';
							 echo json_encode($response);
            }
             mysqli_close($con); 
        }
	
	}


        


?>