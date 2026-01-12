<?php

require_once 'connection.php';

//list of group members
if(isset($_POST['userid'])&&!empty($_POST['userid'])){

	 $userid=$_POST['userid'];
	 $sameGroupIDs=array();

     $group= mysqli_query($con, "select * from tbl_bridge_grp_to_um where find_in_set('$userid',user_ids)") or die('Error '. mysqli_error($con));
		
    while($rwGroup= mysqli_fetch_assoc($group)){
        $sameGroupIDs[]=$rwGroup['user_ids'];
    }
	
	$sameGroupIDs= implode(',', $sameGroupIDs);
	
    $sameGroupIDs= explode(",", $sameGroupIDs);
	
    $sameGroupIDs=array_unique($sameGroupIDs);
    sort($sameGroupIDs);
    $sameGroupIDs= implode(',', $sameGroupIDs);
	// print_r($sameGroupIDs);
    $result =array();
    if(!empty($sameGroupIDs)){
   $user = "SELECT distinct user_name,user_id FROM tbl_ezeefile_logs where user_id in($sameGroupIDs) AND user_id!=1  order by user_name asc";	
    $user_run = mysqli_query($con, $user) or die('Error 1:' . mysqli_error($con));
    while ($rwUser = mysqli_fetch_assoc($user_run)){
    if($rwUser['user_id']!=1){
	$ch=$rwUser['user_name']."&&".$ch1=$rwUser['user_id'] ;
	  array_push($result,$ch);
	}
    }
	echo json_encode($result);
    }else{
        $result["status"]=101;
        $result["Error"]='No Group Found';
        echo json_encode($result);
    }	
}

//list of logs of specific username and id
if(isset($_POST['username'])&&!empty($_POST['username']) &&isset($_POST['userID'])&&!empty($_POST['userID']) ){

	 $userid=$_POST['userID'];
	 $username=$_POST['username'];
	
	$sql =mysqli_query($con, "select * from tbl_ezeefile_logs where action_name!='Login/Logout' AND user_name ='$username' and user_id = '$userid' order by start_date desc");
	 
    $row=mysqli_fetch_all($sql,MYSQLI_ASSOC);
	
	
    echo json_encode($row);
	
	
}

//page starts the whole audit data of specific user
if(isset($_POST['intUserId'])&&!empty($_POST['intUserId'])){

	 $userid=$_POST['intUserId'];

	  $response =array();

    // echo $userid;
    // die;

	 $sameGroupIDs=array();
	// echo "select * from tbl_bridge_grp_to_um where find_in_set('$userid',user_ids)"; 
     $group= mysqli_query($con, "select * from tbl_bridge_grp_to_um where find_in_set('$userid',user_ids)") or die('Error'. mysqli_error($con));
		
    while($rwGroup= mysqli_fetch_assoc($group)){
        $sameGroupIDs[]=$rwGroup['user_ids'];
    }
	
	$sameGroupIDs= implode(',', $sameGroupIDs);
	
    $sameGroupIDs= explode(",", $sameGroupIDs);
	
    $sameGroupIDs=array_unique($sameGroupIDs);
    sort($sameGroupIDs);
    $sameGroupIDs= implode(',', $sameGroupIDs);
	//print_r($sameGroupIDs);


     if($userid ==1 ){

     	//echo 'admin';

    $sql =mysqli_query($con, "select * from tbl_ezeefile_logs where action_name !='Login/Logout' and user_id in($sameGroupIDs) order by start_date desc");

     }

     else{

     	//echo 'user';

     	//echo "select * from tbl_ezeefile_logs where action_name !='Login/Logout' and user_id in($sameGroupIDs) and user_id!=1 order by start_date desc";
 
    $sql =mysqli_query($con, "select * from tbl_ezeefile_logs where action_name !='Login/Logout' and user_id in($sameGroupIDs) and user_id!=1 order by start_date desc");


     }
	 

	 //print_r($sql);
	// die;
	
	
	//$row=mysqli_fetch_all($sql,MYSQLI_ASSOC);
  // echo json_encode($row);  
	
	
 //  die;
	
    while ($row = mysqli_fetch_assoc($sql)){
    if($row['user_id']!=1){
		
    $temp = array();

   // echo $row['user_name'];

    $temp['user_name'] = utf8_encode($row['user_name']);
    $temp['action_name'] = utf8_encode($row['action_name']);
	$temp['start_date']=utf8_encode($row['start_date']);
   $temp['end_date'] = utf8_encode($row['end_date']);
    $temp['system_ip']=utf8_encode($row['system_ip']);
    $temp['remarks']=utf8_encode($row['remarks']);
   
	array_push($response,$temp);

	}	  

   }

   //print_r($response);


	 
    //print_r(json_encode($response));
	echo json_encode($response);
	

	
}



?>