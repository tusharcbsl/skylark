<?php

require_once 'connection.php';


//Audit trail workflow logs list 
if(isset($_POST['userid'])&&!empty($_POST['userid'])){

 //Userid 
 $userid = $_POST['userid'];
 
 $response = array();
 

 //for user role
  /*  $chekUsr = mysqli_query($con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$userid', user_ids) > 0") or die('Error:' . mysqli_error($con));

    $rwgetRole = mysqli_fetch_assoc($chekUsr);

   $role = $rwgetRole['workflow_audit'] ;
     echo $role;
     die;



    if ($rwgetRole['workflow_audit'] != '1') {
        //header('Location: ./index');

        $temp = array();
        $temp['error'] = 'true';
        $temp['message'] = 'Not have right to view workflow';

        array_push($response, $temp);
        echo json_encode($response);  
        
    }*/

    $sameGroupIDs = array();
    $group = mysqli_query($con, "select * from tbl_bridge_grp_to_um where find_in_set('$userid',user_ids)") or die('Error' . mysqli_error($con));
    while ($rwGroup = mysqli_fetch_assoc($group)) {
        $sameGroupIDs[] = $rwGroup['user_ids'];
    }

    $sameGroupIDs = implode(',', $sameGroupIDs);
    $sameGroupIDs = explode(",", $sameGroupIDs);
    $sameGroupIDs = array_unique($sameGroupIDs);
    sort($sameGroupIDs);
    $sameGroupIDs = implode(',', $sameGroupIDs);

   // print_r($sameGroupIDs);

    $auditlistQry =  $group = mysqli_query($con, "SELECT * FROM  tbl_ezeefile_logs_wf where user_id in($sameGroupIDs) and user_id !='1' order by start_date desc") or die('Error' . mysqli_error($con));

    while ($list = mysqli_fetch_assoc($auditlistQry)){

       $temp = array();
       $temp['user_id'] = $list['user_id'];
       $temp['user_name'] = $list['user_name'];
       $temp['action_name'] = $list['action_name'];
       $temp['start_date'] = $list['start_date'];
       $temp['system_ip'] = $list['system_ip'];


       array_push($response,$temp);

    }


    echo json_encode($response);

}

//workflow group member list 

if(isset($_POST['UserID'])&&!empty($_POST['UserID'])){

//Userid 
 $userid = $_POST['UserID'];

 $response = array();
 

 //for user role
  /*  $chekUsr = mysqli_query($con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$userid', user_ids) > 0") or die('Error:' . mysqli_error($con));

    $rwgetRole = mysqli_fetch_assoc($chekUsr);

  /* $role = $rwgetRole['workflow_audit'] ;
     echo $role;
     die;



    if ($rwgetRole['workflow_audit'] != '1') {

        $temp = array();
        $temp['error'] = 'true';
        $temp['message'] = 'Dont have right to view workflow';

        array_push($response, $temp);
        echo json_encode($response);  
        
    }*/

    $sameGroupIDs = array();
    $group = mysqli_query($con, "select * from tbl_bridge_grp_to_um where find_in_set('$userid',user_ids)") or die('Error' . mysqli_error($con));
    while ($rwGroup = mysqli_fetch_assoc($group)) {
        $sameGroupIDs[] = $rwGroup['user_ids'];
    }

    $sameGroupIDs = implode(',', $sameGroupIDs);
    $sameGroupIDs = explode(",", $sameGroupIDs);
    $sameGroupIDs = array_unique($sameGroupIDs);
    sort($sameGroupIDs);
    $sameGroupIDs = implode(',', $sameGroupIDs);

   // print_r($sameGroupIDs);

  $auditlistQry =  $group = mysqli_query($con, "SELECT distinct user_name,user_id FROM tbl_ezeefile_logs_wf where user_id in($sameGroupIDs) AND user_id!=1 order by user_name") or die('Error' . mysqli_error($con));


 echo json_encode(mysqli_fetch_all($auditlistQry,MYSQLI_ASSOC));

}





// get specific audit list of specific person

if(isset($_POST['username'])&&!empty($_POST['username'])){

//Userid 
 $username = $_POST['username'];


  $listQry = mysqli_query($con, "SELECT * FROM tbl_ezeefile_logs_wf where user_name in('$username') AND user_id!=1 order by user_name") or die('Error' . mysqli_error($con));


 echo json_encode(mysqli_fetch_all($listQry,MYSQLI_ASSOC));


}

?>