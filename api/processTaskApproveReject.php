<?php

require_once 'connection.php';

if(isset($_POST['userid'])&&!empty($_POST['userid'])
  && isset($_POST['taskid']) &&!empty($_POST['taskid'])
){


    $userid = $_POST['userid'];
    $taskid = $_POST['taskid'];

      $response = array();
      $auditlist = array();
      $t = array();


   $chekUsr = mysqli_query($con,"select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$userid', user_ids) > 0") or die('Error:' . mysqli_error($con));
 
   $rwgetRole = mysqli_fetch_assoc($chekUsr);

   
    // for showing group wise  user
    $sameGroupIDs = array();
    $group = mysqli_query($con, "select * from tbl_bridge_grp_to_um where find_in_set('$userid',user_ids)") or die('Error' . mysqli_error($con));
    while ($rwGroup = mysqli_fetch_assoc($group)) {
        $sameGroupIDs[] = $rwGroup['user_ids'];
    }

    $sameGroupIDs = array_unique($sameGroupIDs);
    sort($sameGroupIDs);
    $sameGroupIDs = implode(',', $sameGroupIDs);
    
     $auditlistQry = mysqli_query($con, "SELECT distinct first_name,last_name,user_id FROM tbl_user_master where user_id in($sameGroupIDs) AND user_id!=1 and user_id!='$userid' order by first_name") or die('Error' . mysqli_error($con));     

     while($r = mysqli_fetch_assoc($auditlistQry)){


         $temp2= array();
         $temp2['user_name'] =trim($r['first_name']." ".$r['last_name']."&&".$r['user_id']);
         //$temp2['user_id'] =$r['user_id'];

         array_push($auditlist, $temp2);

     }

    //print_r($sameGroupIDs);
    

/*    echo  "select * from tbl_doc_assigned_wf where id='$taskid' and (task_status='Pending' or task_status='Approved') ";
    die;*/


    $task = mysqli_query($con, "select * from tbl_doc_assigned_wf where id='$taskid' and (task_status='Pending' or task_status='Approved') ");
    $rwTask = mysqli_fetch_assoc($task);
    $taskID = $rwTask['task_id'];

  /*  echo "SELECT * FROM tbl_task_master where task_id = '$taskID'";
    die;*/

    $workflowId = mysqli_query($con, "SELECT * FROM tbl_task_master where task_id = '$taskID'");
    $rwTask = mysqli_fetch_assoc($workflowId);
    $wId = $rwTask['workflow_id'];

    
      //getting the current order actions
    $workflowId = mysqli_query($con, "SELECT * FROM tbl_task_master where task_id = '$taskID'");
    $rwTask = mysqli_fetch_assoc($workflowId);
    $wId = $rwTask['workflow_id'];
    $taskOrd = $rwTask['task_order'];
    $workflowId = mysqli_query($con, "SELECT * FROM tbl_task_master where workflow_id = '$wId' and task_order ='$taskOrd'");
    $rWid = mysqli_fetch_assoc($workflowId);
    $actions = explode(",", $rWid['actions']);


     $taskOrd = $rwTask['task_order'] + 1;
     $checklast = mysqli_query($con, "SELECT * FROM tbl_task_master where workflow_id = '$wId' order by task_order desc") or die('Error' . mysqli_error($con));
    $ordr = mysqli_fetch_assoc($checklast);
    $taskOrder= $ordr['task_order'];

    if($taskOrder<$taskOrd){

     $temp = array();
      // echo "error taskorder excced";
    $temp['assign_user'] = "null";
    $temp['alternate_user'] = "null";
    $temp['supervisor'] = "null";
    $temp['priority_id'] = "null";
    $temp['deadline'] = "null";
    $temp['task_order'] = $rwTask['task_order'];
    $temp['deadline_type'] = "null";

   
    //$temp['actions'] = $actions;
    array_push($t, $temp);

 
    }

    else{


 //this is for taskorder 2 or above
    $workflowId = mysqli_query($con, "SELECT * FROM tbl_task_master where workflow_id = '$wId' and task_order ='$taskOrd'");
    $rWid = mysqli_fetch_assoc($workflowId);
    $taskID = $rWid['task_id'];

    $task_id = mysqli_query($con, "SELECT * FROM tbl_task_master where task_id = '$taskID'");
    $rwTaskid = mysqli_fetch_assoc($task_id);

    //Assigned To
   $users = mysqli_query($con, "select first_name,last_name from tbl_user_master where user_id in ($rwTaskid[assign_user])");
   $rwUsers = mysqli_fetch_assoc($users);
   
   if(empty($rwUsers['first_name'])  && empty($rwUsers['last_name'])){


   $assignedTo = "null&&null";

   }

   else{


   $assignedTo = $rwUsers['first_name'] . ' ' . $rwUsers['last_name']."&&".$rwTaskid['assign_user'];

   }


//Supervisor

 $user = mysqli_query($con, "select first_name,last_name from tbl_user_master where user_id='$rwTaskid[supervisor]'");
 $rwUser = mysqli_fetch_assoc($user);

  if(empty($rwUser['first_name'])  && empty($rwUser['last_name'])){

   $supervisor = "null&&null";
  }

  else{


     $supervisor = $rwUser['first_name'] . ' ' . $rwUser['last_name']."&&".$rwTaskid['supervisor'];
  }



// Alertnate User

  $user = mysqli_query($con, "select first_name,last_name from tbl_user_master where user_id='$rwTaskid[alternate_user]'");
  $rwUser = mysqli_fetch_assoc($user);
  
      if(empty($rwUser['first_name'])  && empty($rwUser['last_name'])){

   $alternateUser = "null&&null";
  }

  else{


  $alternateUser =$rwUser['first_name'] . ' ' . $rwUser['last_name']."&&".$rwTaskid['alternate_user'];
  }


  
  

  $temp = array(); 

    $t = array();
    $temp['task_id'] = $rwTaskid['task_id'];
    $temp['task_name'] = $rwTaskid['task_name'];


    
    if(IsNullOrEmptyString($assignedTo)){

      $assignedTo = "null";

    }

    if(IsNullOrEmptyString($alternateUser)){

      $alternateUser = "null";

}


    if(IsNullOrEmptyString($supervisor)){

      $supervisor = "null";

    }
     
    $temp = array();
    $temp['assign_user'] = $assignedTo;
    $temp['alternate_user'] = $alternateUser;
    $temp['supervisor'] = $supervisor;
    $temp['priority_id'] = $rwTaskid['priority_id'];
    $temp['deadline'] = $rwTaskid['deadline'];
    $temp['task_order'] = $rwTaskid['task_order'];
    $temp['deadline_type'] = $rwTaskid['deadline_type'];

   
    //$temp['actions'] = $actions;
    array_push($t, $temp);

    }


    $response['task_info'] = $t;
    $response['actions'] = $actions;
    $response['userlist'] = $auditlist;  

    echo json_encode($response);



}

//get group members

if(isset($_POST['useridGM'])&&!empty($_POST['useridGM'])){


  $groupMemberList = array();

  $userid = $_POST['useridGM'];
  $chekUsr = mysqli_query($con,"select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$userid', user_ids) > 0") or die('Error:' . mysqli_error($con));
 
   $rwgetRole = mysqli_fetch_assoc($chekUsr);

   
    // for showing group wise  user
    $sameGroupIDs = array();
    $group = mysqli_query($con, "select * from tbl_bridge_grp_to_um where find_in_set('$userid',user_ids)") or die('Error' . mysqli_error($con));
    while ($rwGroup = mysqli_fetch_assoc($group)) {
        $sameGroupIDs[] = $rwGroup['user_ids'];
    }

    $sameGroupIDs = array_unique($sameGroupIDs);
    sort($sameGroupIDs);
    $sameGroupIDs = implode(',', $sameGroupIDs);


  
   $auditlistQry = mysqli_query($con, "SELECT distinct first_name,last_name,user_id FROM tbl_user_master where user_id in($sameGroupIDs) AND user_id!=1 AND user_id!='$userid' order by first_name") or die('Error' . mysqli_error($con));

     

     while($r = mysqli_fetch_assoc($auditlistQry)){


         $temp2= array();
         $temp2['user_name'] =trim($r['first_name']." ".$r['last_name']."&&".$r['user_id']);
         //$temp2['user_id'] =$r['user_id'];

         array_push($groupMemberList, $temp2);

     }

    echo json_encode($groupMemberList);
    


}




// get comments

if(isset($_POST['useridComment'])&&!empty($_POST['useridComment'])
  && isset($_POST['taskidComment']) &&!empty($_POST['taskidComment'])
){
    
  $userid = $_POST['useridComment'];
  $taskid = $_POST['taskidComment'];


  
  $ticketid = mysqli_query($con, "select * from tbl_doc_assigned_wf where id='$taskid'");
  $rwticketId = mysqli_fetch_assoc($ticketid);
   
  $ticketid = $rwticketId['ticket_id'];


  $response = array();


  $comment = mysqli_query($con, "select comment_time, comment,user_id, task_id from tbl_task_comment where tickt_id= '$ticketid' order by comment_time desc");
  while ($rwcomment = mysqli_fetch_assoc($comment)) {

    $userid = $rwcomment['user_id'];
   $usr = mysqli_query($con, "select first_name, last_name,profile_picture from tbl_user_master where user_id='$userid'");
   $rwUsr = mysqli_fetch_assoc($usr);
   
   $temp = array();
   $temp['username'] = $rwUsr['first_name']." ".$rwUsr['last_name'];
   $temp['profile_picture'] = base64_encode($rwUsr['profile_picture']);
   $temp['comment_time'] = $rwcomment['comment_time'];
   $temp['comment'] = $rwcomment['comment'];



  $getTaskName = mysqli_query($con, "select * from tbl_task_master where task_id='$rwcomment[task_id]'");
  $rwgetTaskName = mysqli_fetch_assoc($getTaskName);


   $taskname = $rwgetTaskName['task_name'];

   $temp['taskname'] = $rwgetTaskName['task_name'];

   array_push($response, $temp);


}


echo json_encode($response);

}


function IsNullOrEmptyString($str){
    return (!isset($str) || trim($str) === '');
}


// uploading and stuff
//code for the popup with uploading and others 





?>