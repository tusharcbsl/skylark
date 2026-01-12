<?php
require_once 'connection.php';

if(isset($_POST['userid'])&&!empty('userid')){
  
  $userid = $_POST['userid'];
  
  
  $sameGroupIDs = array();
    $group = mysqli_query($con, "select * from tbl_bridge_grp_to_um where find_in_set('$userid',user_ids)") or die('Error' . mysqli_error($con));
    while ($rwGroup = mysqli_fetch_assoc($group)) {
        $sameGroupIDs[] = $rwGroup['roleids'];
    }

    // $sameGroupIDs[1];
    $sameGroupIDs = implode(',', $sameGroupIDs);
  
    

    $sameGroupIDs = explode(",", $sameGroupIDs);
     
    $sameGroupIDs = array_unique($sameGroupIDs);
  
    $sameGroupIDs = implode(',',$sameGroupIDs);
	
	//removing the last comma of the list 12,13,5, 
	$sameGroupIDs =  rtrim($sameGroupIDs,',');
	
	//now 12,13,5
	
	$sql =mysqli_query($con, "SELECT * FROM tbl_user_roles where role_id in ($sameGroupIDs)") or die('Error' . mysqli_error($con));
	
	$profile = mysqli_fetch_all($sql,MYSQLI_ASSOC);
	
	//$row=mysqli_fetch_all($getRolePrivQry,MYSQLI_ASSOC);
    echo json_encode($profile);    
	

     //print_r($sameGroupIDs);
	 
	 }
?>