<?php

require_once 'connection.php';




if(isset($_POST['userid']) && !empty($_POST['userid'])){


$userid = $_POST['userid'];
date_default_timezone_set("Asia/Kolkata");
$date =date("Y-m-d H:i:s");
$deadline = ""; 

$response = array();

if($userid == '1'){


 $inTrayQry =  mysqli_query($con,"SELECT tdawf.id,tsm.task_name,tdawf.doc_id,tdawf.task_status,tdawf.task_remarks,tdawf.start_date,tdawf.end_date,tsm.deadline,tsm.deadline_type,tsm.priority_id,tdawf.assign_by,tdawf.NextTask FROM tbl_task_master tsm inner join tbl_doc_assigned_wf tdawf on tsm.task_id=tdawf.task_id order by tdawf.start_date desc");  


}

else{

$inTrayQry = mysqli_query($con, "SELECT tdawf.id,tsm.task_name,tdawf.doc_id,tdawf.task_status,tdawf.task_remarks,tdawf.start_date,tdawf.end_date,tsm.deadline,tsm.deadline_type,tsm.priority_id,tdawf.assign_by,tdawf.NextTask FROM tbl_task_master tsm inner join tbl_doc_assigned_wf tdawf on tsm.task_id=tdawf.task_id where ((tsm.assign_user='$userid' and tdawf.NextTask='0') or (alternate_user='$userid' and tdawf.NextTask= '3') or (supervisor='$userid' and tdawf.NextTask= '4')) order by tdawf.id desc");


}


 while ($r = mysqli_fetch_assoc($inTrayQry)){

      $prId = $r['priority_id'];
      $assign_by_id = $r['assign_by'];
      $dead_type = $r['deadline_type'];
      $taskStatus = "";
      $warningMsg= "";
      $warning = "";



        $nameQry = mysqli_query($con, "SELECT first_name ,last_name FROM tbl_user_master where user_id = '$assign_by_id'");
        
        $namelist = mysqli_fetch_assoc($nameQry);
        $name = $namelist['first_name']." ".$namelist['last_name'];
         
      $priority = '';
      $deadline = "";

      if($prId == '1'){

        $priority = 'Urgent';
        
      }

      else if ($prId == '2'){


        $priority = 'Medium';

      }


      else if ($prId == '3'){


        $priority = 'Normal';

         }
    

      if ($dead_type == 'Date') {

     $deadDate = strtotime($r['start_date']) + ($r['deadline'] * 60); // convert in sec
     $remainTime = $deadDate - (strtotime($date));

    
     if ($remainTime > 0) {

    

      $deadline = humanTiming($remainTime);


     } else {

        $deadline = "0 Secounds";
    }
    }
    
   else if ($dead_type == 'Days') 

   {

  $deadDate = strtotime($r['start_date']) + ($r['deadline'] * 24 * 60 * 60); // convert in sec
   $remainTime = $deadDate - (strtotime($date));
    $deadline = humanTiming($remainTime);

   if ($remainTime > 0) {

  

      $deadline = humanTiming($remainTime);


   } else {

    $deadline = "0 Secounds";

   }

  }

   else {
     $deadDate = strtotime($r['start_date']) + ($r['deadline'] * 60); //  convert in sec
      $remainTime = $deadDate - (strtotime($date));
       
           if ($remainTime > 0) {
          
            $deadline = humanTiming($remainTime);
           
            } else {
    

              $deadline = "0 Secounds";
        }
}



 if ($r['task_status'] == 'Pending') {

  $taskStatus = $r['task_status']; 

  } else if ($r['task_status'] == 'Approved' || $r['task_status'] == 'Complete' || $r['task_status'] == 'Processed' || $r['task_status'] == 'Done') {

   $taskStatus = $r['task_status']; 

if ($r['NextTask'] == 0) {
     
     $warning = "Rejected from Ahead Task";
     $warningMsg = "Please view the comments to know the rejection reason";

   }
 } else if ($r['task_status'] == 'Rejected') {
 $taskStatus = $r['task_status']; 
} else if ($r['task_status'] == 'Aborted') {
 $taskStatus = $r['task_status']; 
}

if(empty($warning)){

  $warning = "null";

}

if(empty($warningMsg)){

  $warningMsg = "null";

}
                                                                    

      $temp = array();
      $temp['id'] = $r ['id'];
      $temp['task_name'] =$r['task_name'];
      $temp['doc_id'] = $r['doc_id'];
      $temp['task_status'] = $r['task_status'];
      $temp['priority'] = $priority;
      $temp['warning'] = $warning;
      $temp['warningMsg'] = $warningMsg;
      $temp['start_date'] =$r['start_date'];
      $temp ['end_date'] = $r['end_date'];
      $temp['deadline'] = $deadline;
     // $temp['deadline_type'] = $r['deadline_type'];
      $temp['assign_by'] = $name;

     array_push($response, $temp);



 }
   
// $res =  mysqli_fetch_all($inTrayQry,MYSQLI_ASSOC);


 echo json_encode($response);



}



//asignby username
if(isset($_POST['useridAssignBy']) && !empty($_POST['useridAssignBy'])){

 $userid = $_POST['useridAssignBy'];

   $assign_by = array();
   $response = array();

   $users = mysqli_query($con, "select assign_by from tbl_task_master inner join tbl_doc_assigned_wf on tbl_task_master.task_id=tbl_doc_assigned_wf.task_id where assign_user='$userid' or alternate_user='$userid' or supervisor='$userid'");
   while ($rwUsers = mysqli_fetch_assoc($users)) {

    $assign_by[] = $rwUsers['assign_by'];

   }

   $assign_by = implode(',', $assign_by);

   if ($userid == 1) {

    $userAsinBy = "SELECT * from tbl_user_master order by first_name , last_name asc";

   } else {

    $userAsinBy = "SELECT * from tbl_user_master where user_id in($assign_by) order by first_name , last_name asc";

   }
   if (!empty($assign_by) || $userid == 1) {

    $userAsinBy_query = mysqli_query($con, $userAsinBy) or die("Error: " . mysqli_error($con));

    while ($userAsinBy_row = mysqli_fetch_assoc($userAsinBy_query)) 
      {

      if ($userAsinBy_row['user_id'] != 1 && $userAsinBy_row['user_id'] != $userid) {
        

        /*
        if ($_GET['asinBy'] == $userAsinBy_row['user_id']) {

          //echo 'selected';


        }*/
    
      $assignbyName = $userAsinBy_row['first_name'] . ' ' . $userAsinBy_row['last_name'];
      
      array_push($response,$assignbyName);
      

    // echo $userAsinBy_row['first_name'] . ' ' . $userAsinBy_row['last_name'];

}
}
}

echo json_encode($response);

}

if(
isset($_POST['userId'])&&!empty($_POST['userId'])
 
){

 $where = "";
$response= array();
date_default_timezone_set("Asia/Kolkata");
$date =date("Y-m-d H:i:s");
$deadline = ""; 

 //echo "working";
 $taskStats = $_POST['taskStats'];
 $asinBy = $_POST['asinBy'];
 $ticketid = $_POST['ticketid'];
 $startDate = $_POST['startDate'];
 $endDate = $_POST['endDate'];
 $taskPrioty = $_POST['taskPrioty'];
 $userid = $_POST['userId'];
  $warningMsg= "";
  $warning = "";
 


 if($userid=='1'){

    if(!empty($taskStats)){

        if(empty($where)){

        $where="where  tdawf.task_status = '$taskStats' ";

    }else{

        $where.="and  tdawf.task_status = '$taskStats' ";
    }
    }
     if(!empty($taskPrioty) ){
     if(!empty($where)){
         $where.=" and tsm.priority_id = '$taskPrioty' ";
        }else {
        $where="where tsm.priority_id = '$taskPrioty' ";
    }
     }
    if(!empty($asinBy)){

    if(!empty($where)){

       $where.="and tdawf.assign_by = '$asinBy' ";
    } 
    else {

        $where="where  tdawf.assign_by = '$asinBy'";
    }
}
    if(!empty($ticketid)){
    if(!empty($where)){
       $where.="and tsm.ticket_id = '$ticketid' ";
    } 
    else {
        $where="where  tsm.ticket_id = '$ticketid'";
    }
}
if(!empty($startDate) && !empty($endDate)){
    $startDate=$_POST['startDate'];
    $endDate=$_POST['endDate'];
    $startDate= strtotime($startDate);
    $endDate= strtotime($endDate);
    $endDate=$endDate+23*60*60+59*60+59;
    $startDate=date("Y-m-d H:i:s",$startDate);
    $endDate=date("Y-m-d H:i:s",$endDate);
    
  if(!empty($where)){

      $where.="and tdawf.start_date between '$startDate' and '$endDate'";

    } 
    else {
      
       $where="where tdawf.start_date between '$startDate' and '$endDate'";
    }
  
}

}else{

  if(!empty($taskStats) && $taskStats !='Pending' ){
   
    if(empty($where)){
        $where="where action_by='$userid' and tdawf.task_status = '$taskStats' ";
    }else{
        $where.="and action_by='$userid' and tdawf.task_status = '$taskStats' ";
    }
}


else if(!empty($taskStats)&& $taskStats =='Pending'){
    
     if(empty($where)){
        $where="where tsm.assign_user='$userid' and tdawf.NextTask='0' and tdawf.task_status = '$taskStats' ";
    }else{
        $where.="and tsm.assign_user='$userid' and tdawf.NextTask='0' and tdawf.task_status = '$taskStats' ";
    }
    
}


else{
    if(!empty($where)){
        $where.="and ((tsm.assign_user='$userid' and tdawf.NextTask='0') or (alternate_user='$userid and tdawf.NextTask= '3') or (supervisor='$userid' and tdawf.NextTask= '4'))";
        }else{
        $where="where ((tsm.assign_user='$userid' and tdawf.NextTask='0') or (alternate_user='$userid' and tdawf.NextTask= '3') or (supervisor='$userid' and tdawf.NextTask= '4'))";
    }
}



 if(!empty($taskPrioty) ){

     if(!empty($where)){
         $where.=" and tsm.priority_id = '$taskPrioty' ";
    } 
    else {
        $where="where tsm.assign_user='$userid'  and  tsm.priority_id = '$taskPrioty' ";
    }
}
 if(!empty($ticketid) ){
     if(!empty($where)){
         $where.=" and tdawf.ticket_id = '$ticketid' ";
    } 
    else {
        $where="where tsm.assign_user='$userid'  and  tdawf.ticket_id = '$ticketid' ";
    }
}

if(!empty($asinBy)){
    if(!empty($where)){
       $where.="and tdawf.assign_by = '$asinBy' ";
    } 
    else {
        $where="where tsm.assign_user='$userid'  and tdawf.assign_by = '$asignby'";
    }
}

if(!empty($startDate) && !empty($endDate)){
    $startDate=$_POST['startDate'];
    $endDate=$_POST['endDate'];
    $startDate= strtotime($startDate);
    $endDate= strtotime($endDate);
    $endDate=$endDate+23*60*60+59*60+59;
    $startDate=date("Y-m-d H:i:s",$startDate);
    $endDate=date("Y-m-d H:i:s",$endDate);
    
  if(!empty($where)){
      $where.="and tdawf.start_date between '$startDate' and '$endDate'";
    } 
    else {
       $where="where tdawf.start_date between '$startDate' and '$endDate' and (tsm.assign_user='$userid' or tdawf.action_by='$userid')";
    }
  
}
}


if(!empty($_POST['taskStats']) &&($_POST['taskStats']=='Approved' || $_POST['taskStats']=='Processed' || $_POST['taskStats']=='Complete'  || $_POST['taskStats']=='Done'  || $_POST['taskStats']=='Aborted' || $_POST['taskStats']=='Rejected' )){
    $where .=" order by action_time desc";
} else{
    $where .=" order by tdawf.id desc";
}



  if ($userid == 1) {

        $allot = "SELECT tdawf.id,tsm.task_name,tdawf.doc_id,tdawf.task_status,tdawf.task_remarks,tdawf.start_date,tdawf.end_date,tsm.deadline,tsm.deadline_type,tsm.priority_id,tdawf.assign_by,tdawf.NextTask FROM tbl_task_master tsm inner join tbl_doc_assigned_wf tdawf on tsm.task_id=tdawf.task_id $where";


    } 

    else 

   {

    $allot = "SELECT tdawf.id,tdawf.ticket_id,tsm.task_name,tdawf.doc_id,tdawf.task_status,tdawf.task_remarks,tdawf.start_date,tdawf.end_date,tsm.deadline,tsm.deadline_type,tsm.priority_id,tdawf.assign_by,tdawf.NextTask FROM tbl_task_master tsm inner join tbl_doc_assigned_wf tdawf on tsm.task_id=tdawf.task_id $where";


     }

/*  echo $allot ;
  die; */

  $q = mysqli_query($con,$allot);

 while ($r = mysqli_fetch_assoc($q)){

      $prId = $r['priority_id'];
      $assign_by_id = $r['assign_by'];
      $dead_type = $r['deadline_type'];



        $nameQry = mysqli_query($con, "SELECT first_name ,last_name FROM tbl_user_master where user_id = '$assign_by_id'");
        
        $namelist = mysqli_fetch_assoc($nameQry);
        $name = $namelist['first_name']." ".$namelist['last_name'];
         
      $priority = '';

      if($prId == '1'){

        $priority = 'Urgent';
        
      }

      else if ($prId == '2'){


        $priority = 'Medium';

      }


      else if ($prId == '3'){


        $priority = 'Normal';

         }
    

      if ($dead_type == 'Date') {

     $deadDate = strtotime($r['start_date']) + ($r['deadline'] * 60); // convert in sec
      $remainTime = $deadDate - (strtotime($date));

   
    
     if ($remainTime > 0) {

    

      $deadline = humanTiming($remainTime);


     } else {

        $deadline = "0 Secounds";
    }
    }
    
   else if ($dead_type == 'Days') 
   {

  $deadDate = strtotime($r['start_date']) + ($r['deadline'] * 24 * 60 * 60); // convert in sec
$remainTime = $deadDate - (strtotime($date));
  


 //echo round($remainTime/(24*60*60)) . ' '. $r['deadline_type'];
 

   if ($remainTime > 0) {

       // echo $remainTime;

        $deadline = humanTiming($remainTime);




   } else {

    $deadline = "0 Secounds";

   }

  }

   else {
     $deadDate = strtotime($r['start_date']) + ($r['deadline'] * 60); //  convert in sec
      $remainTime = $deadDate - (strtotime($date));
         //echo round($remainTime/(60*60)) . ' '. $r['deadline_type'];
           if ($remainTime > 0) {
          
             $deadline = humanTiming($remainTime);
           
            } else {
          //echo '<span class="error">0 Seconds</span>';
        // echo '3';

              $deadline = "0 Secounds";
        }
}


 if ($r['task_status'] == 'Pending') {

  $taskStatus = $r['task_status']; 

  } else if ($r['task_status'] == 'Approved' || $r['task_status'] == 'Complete' || $r['task_status'] == 'Processed' || $r['task_status'] == 'Done') {

   $taskStatus = $r['task_status']; 

if ($r['NextTask'] == 0) {
     
     $warning = "Rejected from Ahead Task";
     $warningMsg = "Please view the comments to know the rejection reason";

   }
 } else if ($r['task_status'] == 'Rejected') {
 $taskStatus = $r['task_status']; 
} else if ($r['task_status'] == 'Aborted') {
 $taskStatus = $r['task_status']; 
}

if(empty($warning)){

  $warning = "null";

}

if(empty($warningMsg)){

  $warningMsg = "null";

}




      $temp = array();
      $temp['id'] = $r ['id'];
      $temp['task_name'] =$r['task_name'];
      $temp['doc_id'] = $r['doc_id'];
      $temp['task_status'] = $r['task_status'];
      $temp['priority'] = $priority;
      $temp['start_date'] =$r['start_date'];
      $temp ['end_date'] = $r['end_date'];
      $temp['deadline'] = $deadline;
       $temp['warning'] = $warning;
      $temp['warningMsg'] = $warningMsg;
     // $temp['deadline_type'] = $r['deadline_type'];
      $temp['assign_by'] = $name;

     array_push($response, $temp);



 }

echo json_encode($response);



/*echo $where;
*/

}




function humanTiming($time) {
    // date_default_timezone_set("Asia/Kolkata");
    // $date=date("Y-m-d H:i:s");
    //echo$time = strtotime($date)- $time; // to get the time since that moment
//echo $time;
    $tokens = array(
        31536000 => 'year',
        2592000 => 'month',
        604800 => 'week',
        86400 => 'day',
        3600 => 'hour',
        60 => 'minute',
        1 => 'second'
    );

    $result = '';
    $counter = 1;
    foreach ($tokens as $unit => $text) {
        if ($time < $unit)
            continue;
        if ($counter > 2)
            break;

        $numberOfUnits = floor($time / $unit);
        $result .= "$numberOfUnits $text ";
        $time -= $numberOfUnits * $unit;
        ++$counter;
    }

    return "{$result}";
}

?>