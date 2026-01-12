<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.

 require_once '../../sessionstart.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout.php");
}
require './../config/database.php';
//$notified=array();
$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

$rwgetRole = mysqli_fetch_assoc($chekUsr);

if ($rwgetRole['dashboard_mytask'] != '1') {
  //  header('Location: ../../index');
}

//$task = mysqli_query($db_con,"SELECT tdawf.id,tsm.task_name,tdawf.doc_id,tdawf.task_status,tdawf.task_remarks,tdawf.start_date,tsm.deadline,tsm.deadline_type,tsm.priority_id,tdawf.assign_by,tdawf.NextTask FROM tbl_task_master tsm inner join tbl_doc_assigned_wf tdawf on tsm.task_id=tdawf.task_id where (tsm.assign_user='$_SESSION[cdes_user_id]' and tdawf.NextTask='0') or (alternate_user='$_SESSION[cdes_user_id]' and tdawf.NextTask= '3') or (supervisor='$_SESSION[cdes_user_id]' and tdawf.NextTask= '4') and (tdawf.task_status = 'Pending' or tdawf.task_status = 'Approved')") or die('error'.mysqli_error($db_con));
$task = mysqli_query($db_con,"SELECT tdawf.id,tsm.task_name,tdawf.doc_id,tdawf.task_status,tdawf.task_remarks,tdawf.start_date,tsm.deadline,tsm.deadline_type,tsm.priority_id,tdawf.assign_by,tdawf.NextTask FROM tbl_task_master tsm inner join tbl_doc_assigned_wf tdawf on tsm.task_id=tdawf.task_id where (tsm.assign_user='$_SESSION[cdes_user_id]' and tdawf.NextTask='0') or (alternate_user='$_SESSION[cdes_user_id]' and tdawf.NextTask= '3') or (supervisor='$_SESSION[cdes_user_id]' and tdawf.NextTask= '4') and (tdawf.task_status = 'Approved')") or die('error'.mysqli_error($db_con));
while($rwTask=mysqli_fetch_assoc($task)){
    
if ($rwTask['deadline_type'] == 'Date') {
$deadDate=strtotime($rwTask['start_date'])+($rwTask['deadline']*60);
   $remainTime=$deadDate-(strtotime($date));
  
  // echo intdiv($remainTime, 60) . ':' . ($remainTime % 60) . ' Hrs';
    
} else if ($rwTask['deadline_type'] == 'Days') {
   $deadDate=strtotime($rwTask['start_date'])+($rwTask['deadline']*24*60*60);
   $remainTime=$deadDate-(strtotime($date));
   //echo round($remainTime/(24*60*60)) . ' '. $rwTask['deadline_type'];
 

}else{
   $deadDate=strtotime($rwTask['start_date'])+($rwTask['deadline']*60);
   $remainTime=$deadDate-(strtotime($date));
   //echo round($remainTime/(60*60)) . ' '. $rwTask['deadline_type'];
  

       }
       
       if($remainTime>0){
          $time= humanTiming($remainTime);
           if(($remainTime/60)>=30 && ($remainTime/60)<=60){ 
               if(!in_array($rwTask['id'], $_SESSION['notified1'])){ 
            ?>
        <script>
        $.Notification.notify('warning','bottom right','Pending Task <strong style="text-decoration:underline;" >" <?php echo $rwTask['task_name'];?> "</strong> in your tray! <br>Remaining Time - <strong style="text-decoration:underline;">" <?php echo $time;?> "</strong>', '<a href="process_task?id=<?php echo urlencode(base64_encode($rwTask['id']));?>" class="btn btn-white">Process Now</a>');
        </script>
               <?php 
               
               if(!empty($_SESSION['notified1'])){
                if(!empty($rwTask['id'])){
                array_push($_SESSION['notified1'],$rwTask['id']);
                array_unique($_SESSION['notified1']);
                }
                }else{
                   array_push($_SESSION['notified1'],$rwTask['id']);
                }
               }
              
                }
                else if(($remainTime/60)>=0 && ($remainTime/60)<30){
               if(!in_array($rwTask['id'], $_SESSION['notified2'])){
          ?>
        <script>
        $.Notification.notify('error','bottom right','Pending Task "<?php echo $rwTask['task_name'];?>" in your tray! <br> Remaining Time - <strong style="text-decoration:underline;"> "<?php echo $time;?> "</strong>', '<a href="process_task?id=<?php echo urlencode(base64_encode($rwTask['id']));?>" class="btn btn-white">Process Now</a>');
        </script>
           <?php 
           
           if(!empty($_SESSION['notified2'])){
                if(!empty($rwTask['id'])){
                array_push($_SESSION['notified2'],$rwTask['id']);
                array_unique($_SESSION['notified2']);
                }
            }else{
                array_push($_SESSION['notified2'],$rwTask['id']);
            }
               }
           }
           else{ 
               if(!in_array($rwTask['id'], $_SESSION['notified'])){
               ?>
        <script>
        $.Notification.notify('success','bottom right','New Task "<strong style="text-decoration:underline;"> <?php echo $rwTask['task_name'];?> </strong>"   in your tray! <br>   Remaining Time - <strong style="text-decoration:underline;">" <?php echo $time;?> "</strong>', '<a href="process_task?id=<?php echo urlencode(base64_encode($rwTask['id']));?>" class="btn btn-white">Process Now</a>');
        </script>
           <?php 
           
           if(!empty($_SESSION['notified'])){
                if(!empty($rwTask['id'])){
                    array_push($_SESSION['notified'],$rwTask['id']);
                array_unique($_SESSION['notified']);
                }
            }else{
                array_push($_SESSION['notified'],$rwTask['id']);
            }
               }
           }
       }  
}

//print_r($_SESSION['notified']);
 function humanTiming($time)
{
  
    $tokens = array (
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
        if ($time < $unit) continue;
        if ($counter > 2) break;

        $numberOfUnits = floor($time / $unit);
        $result .= "$numberOfUnits $text ";
        $time -= $numberOfUnits * $unit;
        ++$counter;
    }

    return "{$result}";
} 
?>
 */
 require_once '../../sessionstart.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout.php");
}
require './../config/database.php';
require_once '../../mail.php';
require_once '../../alternateworkflow.php';