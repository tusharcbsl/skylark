<?php

require_once 'connection.php';

if(isset($_POST['userid']) && !empty($_POST['userid'])){

 $userid = $_POST['userid'];
$where = ''; 
$date =date("Y-m-d H:i:s");
$response = array();

 if($userid == '1'){

 $where = ''; 

//task status 
if(isset($_POST['userid']) && !empty($_POST['userid'])
  && isset($_POST['taskStats'])&& !empty($_POST['taskStats'])){

    
  
   $where ="and tdawf.task_status = '$_POST[taskStats]' ";
   

}


 //task prirority
if(isset($_POST['userid']) && !empty($_POST['userid'])
  && isset($_POST['taskPrioty'])&& !empty($_POST['taskPrioty'])){

 $taskPrioty = $_POST['taskPrioty'];

if($taskPrioty  == 'Normal'){

  $taskPrioty = '3';


}
else if ($taskPrioty == 'Medium'){

  $taskPrioty = '2';


}

else if($taskPrioty == 'Urgent'){

  $taskPrioty = '1';

}

$where="where tsm.priority_id = '$taskPrioty' ";

}


//assign by 
if(isset($_POST['userid']) && !empty($_POST['userid'])
  && isset($_POST['asinBy'])&& !empty($_POST['asinBy'])){

  $asinBy = $_POST['asinBy'];

 $where="where  tdawf.assign_by = '$asinBy'";


}

//ticket id 
if(isset($_POST['userid']) && !empty($_POST['userid'])
  && isset($_POST['ticketid'])&& !empty($_POST['ticketid'])){

 $ticketid = $_POST['ticketid'];

 $where="where  tsm.ticket_id = '$ticketid'";


}


if(isset($_POST['userid']) && !empty($_POST['userid'])
  && isset($_POST['startDate'])&& !empty($_POST['startDate'])
  && isset($_POST['endDate'])&& !empty($_POST['endDate'])){

    $startDate=$_POST['startDate'];
    $endDate=$_POST['endDate'];
    $startDate= strtotime($startDate);
    $endDate= strtotime($endDate);
    $endDate=$endDate+23*60*60+59*60+59;
    $startDate=date("Y-m-d H:i:s",$startDate);
    $endDate=date("Y-m-d H:i:s",$endDate);
    
 
    $where="where tdawf.start_date between '$startDate' and '$endDate'";
      

}


 }

 else{

  
$where = ''; 

//task status 
if(isset($_POST['userid']) && !empty($_POST['userid'])
  && isset($_POST['taskStats'])&& !empty($_POST['taskStats'])&& $_POST['taskStats'] !='Pending'){

    
     $where.=" and action_by='$_POST[userid]' and tdawf.task_status = '$_POST[taskStats]' ";
     
    // echo $where ;
  

}

//task status 
else if(isset($_POST['userid']) && !empty($_POST['userid'])
  && isset($_POST['taskStats'])&& !empty($_POST['taskStats']) && $_POST['taskStats'] =='Pending'){

    
  

    $where ="and tsm.assign_user='$_POST[userid]' and tdawf.NextTask='0' and tdawf.task_status = '$_POST[taskStats]' ";
  


  

}

else {

 $where="where ((tsm.assign_user='$_POST[userid]' and tdawf.NextTask='0') or (alternate_user='$_POST[userid]' and tdawf.NextTask= '3') or (supervisor='$_POST[userid]' and tdawf.NextTask= '4'))";

}




 //task prirority
 if(isset($_POST['userid']) && !empty($_POST['userid'])
  && isset($_POST['taskPrioty'])&& !empty($_POST['taskPrioty'])){

 $taskPrioty = $_POST['taskPrioty'];

if($taskPrioty  == 'Normal'){

  $taskPrioty = '3';


}
else if ($taskPrioty == 'Medium'){

  $taskPrioty = '2';


}

else if($taskPrioty == 'Urgent'){

  $taskPrioty = '1';

}

 $where="where tsm.assign_user='$_POST[userid]'  and  tsm.priority_id = '$taskPrioty' ";

}



//assign by
if(isset($_POST['userid']) && !empty($_POST['userid'])
  && isset($_POST['asinBy'])&& !empty($_POST['asinBy'])){

  $asinBy = $_POST['asinBy'];

 $where="where tsm.assign_user='$_POST[userid]'  and tdawf.assign_by = '$_POST[asinBy]'";


}

//ticket id 
if(isset($_POST['userid']) && !empty($_POST['userid'])
  && isset($_POST['ticketid'])&& !empty($_POST['ticketid'])){

 $ticketid = $_POST['ticketid'];

  $where="where tsm.assign_user='$_POST[userid]'  and  tdawf.ticket_id = '$_POST[ticketid]' ";


}


if(isset($_POST['userid']) && !empty($_POST['userid'])
  && isset($_POST['startDate'])&& !empty($_POST['startDate'])
  && isset($_POST['endDate'])&& !empty($_POST['endDate'])){

    $startDate=$_POST['startDate'];
    $endDate=$_POST['endDate'];
    $startDate= strtotime($startDate);
    $endDate= strtotime($endDate);
    $endDate=$endDate+23*60*60+59*60+59;
    $startDate=date("Y-m-d H:i:s",$startDate);
    $endDate=date("Y-m-d H:i:s",$endDate);
    
 
    $where="where tdawf.start_date between '$startDate' and '$endDate' and (tsm.assign_user='$_POST[userid]' or tdawf.action_by='$_POST[userid]]')";

}


 }


$qry = "SELECT tdawf.id,tsm.task_name,tdawf.doc_id,tdawf.task_status,tdawf.task_remarks,tdawf.start_date,tdawf.end_date,tsm.deadline,tsm.deadline_type,tsm.priority_id,tdawf.assign_by,tdawf.NextTask FROM tbl_task_master tsm inner join tbl_doc_assigned_wf tdawf on tsm.task_id=tdawf.task_id $where";

/*echo $qry;

die;*/


 $Qry = mysqli_query($con, $qry);


while ($r = mysqli_fetch_assoc($Qry)){

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


 //echo round($remainTime/(24*60*60)) . ' '. $allot_row['deadline_type'];
 

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
         //echo round($remainTime/(60*60)) . ' '. $allot_row['deadline_type'];
           if ($remainTime > 0) {
          
             $deadline = humanTiming($remainTime);
           
            } else {
          //echo '<span class="error">0 Seconds</span>';
        // echo '3';

              $deadline = "0 Secounds";
        }
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
     // $temp['deadline_type'] = $r['deadline_type'];
      $temp['assign_by'] = $name;

     array_push($response, $temp);



 }


echo json_encode($response);


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