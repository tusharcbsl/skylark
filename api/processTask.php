<?php

//process task api 
/*



*/

require_once 'connection.php';
require_once 'classes/function.php';

if(isset($_POST['userid'])&&!empty($_POST['userid'])
  && isset($_POST['taskid']) &&!empty($_POST['taskid'])
){

$userid = $_POST['userid'];
date_default_timezone_set("Asia/Kolkata");
$date =date("Y-m-d H:i:s");
$priority = '';
$deadline = '';
$doc_list = array();


 
// echo "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$userid', user_ids) > 0";

 //die;

  $chekUsr = mysqli_query($con,"select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$userid', user_ids) > 0") or die('Error:' . mysqli_error($con));
 
 $rwgetRole = mysqli_fetch_assoc($chekUsr);

    if ($rwgetRole['dashboard_mytask'] != '1') {
       // header('Location: ./index');
    }
    // for showing group wise  user
    $sameGroupIDs = array();
    $group = mysqli_query($con, "select * from tbl_bridge_grp_to_um where find_in_set('$userid',user_ids)") or die('Error' . mysqli_error($con));
    while ($rwGroup = mysqli_fetch_assoc($group)) {
        $sameGroupIDs[] = $rwGroup['user_ids'];
    }
    $sameGroupIDs = array_unique($sameGroupIDs);
    sort($sameGroupIDs);
    $sameGroupIDs = implode(',', $sameGroupIDs);

    //$user_id = $_SESSION['cdes_user_id'];

    //task id 
    //$id = base64_decode(urldecode($_GET['id']));
    $id = $_POST['taskid'];

    //echo $id .'\n';


    $task = mysqli_query($con, "select * from tbl_doc_assigned_wf where id='$id' and (task_status='Pending' or task_status='Approved') ");
    $rwTask = mysqli_fetch_assoc($task);

    if ($userid != '1') {

        $work = mysqli_query($con, "select * from tbl_task_master where task_id='$rwTask[task_id]' and (assign_user = '$userid' or alternate_user='$userid' or supervisor='$userid')");
        if (mysqli_num_rows($work) > 0) {
            $rwWork = mysqli_fetch_assoc($work);
            //echo "ok 1";
             
         // echo  $ltaskName = $rwWork['task_name'];


        } else {


          // echo "ok2";

           $temp = array();
           $temp['error'] = 'true';
           $temp['message'] = 'No Task Present';
           echo json_encode($temp);

           die;

            //header("Location:index");
           // echo 'index';
        }
    } 


    else {

        //$rwTask[task_id];
        //echo "select * from tbl_task_master where task_id='$rwTask[task_id]'";

        //die;

        if(empty($rwTask['task_id'])){

           $temp = array();
           $temp['error'] ='true';
           $temp['message'] = 'server error';
           echo json_encode($temp); 

           //echo "ok"; 
          

           die;

        }

        else{

          echo  "select * from tbl_task_master where task_id='$rwTask[task_id]'";
          die;

          $work = mysqli_query($con, "select * from tbl_task_master where task_id='$rwTask[task_id]'");
       
        if (mysqli_num_rows($work) > 0) {

            $rwWork = mysqli_fetch_assoc($work);

          

        } else {

            //echo 'index';
            //header("Location:index");
      

           $temp = array();
           $temp['error'] = 'true';
           $temp['message'] = 'No Task Present';
           echo json_encode($temp);

           die;
        }


        }

    
    }

   $assignBy = $rwTask['assign_by'];
   $docID = $rwTask['doc_id'];
   $ctaskID = $rwWork['task_id'];

   $ctaskOrder = $rwWork['task_order'];
   $stepId = $rwWork['step_id'];
   $wfid = $rwWork['workflow_id'];
   $ticket = $rwTask['ticket_id'];

    $priorityID = $rwWork['priority_id'];

    $taskRemark = mysqli_real_escape_string($con, $rwTask['task_remarks']);

   //echo $assignBy . " " . $docID;

  // echo "select * from tbl_document_master where doc_id='$docID'";

   $dms = mysqli_query($con, "select * from tbl_document_master where doc_id='$docID'");
   $rwDms = mysqli_fetch_assoc($dms);

   $docName = $rwDms['doc_name'];
  // $docName = explode("_", $docName);

  // echo $docName;

   //proiority

   if ($rwWork['priority_id'] == 1) {
    $priority = 'Urgent';
     } else if ($rwWork['priority_id'] == 2) {
     $priority = 'Medium';
    } else if ($rwWork['priority_id'] == 3) {
        $priority = 'Normal';
          }

    //deadline
    
          if ($rwWork['deadline_type'] == 'Date') {

             $deadDate = strtotime($rwTask['start_date']) + ($rwWork['deadline'] * 60);
             $remainTime = $deadDate - (strtotime($date));
           
           // echo intdiv($remainTime, 60) . ':' . ($remainTime % 60) . ' Hrs';
           
             if ($remainTime > 0) {


              //  echo '<span class="success">' . humanTiming($remainTime) . '</span>';
 

                  $deadline =  humanTiming($remainTime);



            } else {


                //echo '<span class="error">0 Seconds</span>';

                  $deadline = '0 Seconds';
            }
        } else if ($rwWork['deadline_type'] == 'Days') {
            $deadDate = strtotime($rwTask['start_date']) + ($rwWork['deadline'] * 24 * 60 * 60);
            $remainTime = $deadDate - (strtotime($date));
                                                        //echo round($remainTime/(24*60*60)) . ' '. $rwTask['deadline_type'];
            if ($remainTime > 0) {
               // echo '<span class="success">' . humanTiming($remainTime) . '</span>';

                   $deadline =  humanTiming($remainTime);

            } else {
                //echo '<span class="error">0 Seconds</span>';

                  $deadline = '0 Seconds';
            }
        } else {

            $deadDate = strtotime($rwTask['start_date']) + ($rwWork['deadline'] * 60);
            $remainTime = $deadDate - (strtotime($date));
                                                        //echo round($remainTime/(60*60)) . ' '. $rwTask['deadline_type'];
            if ($remainTime > 0) {
                

                //echo '<span class="success">' . humanTiming($remainTime) . '</span>';
                  
                  $deadline =  humanTiming($remainTime);
                   
            } else {
              //  echo '<span class="error">0 Seconds</span>';

                $deadline = '0 Seconds';
            }
        }      


//workflow name 

   $wfn = mysqli_query($con, "select * from tbl_workflow_master where workflow_id='$rwWork[workflow_id]'");
   $rwWfn = mysqli_fetch_assoc($wfn);
   $workflowName = $rwWfn['workflow_name'];      

   //task name

  $taskName = $rwWork['task_name'];
  
  //Submitted By

   $user = mysqli_query($con, "select first_name,last_name from tbl_user_master where user_id='$rwTask[assign_by]'");
   $rwUser = mysqli_fetch_assoc($user);
   $submittedBy =  $rwUser['first_name'] . ' ' . $rwUser['last_name'];
                                                      
//Assigned To
   $users = mysqli_query($con, "select first_name,last_name from tbl_user_master where user_id in ($rwWork[assign_user])");
  while ($rwUsers = mysqli_fetch_assoc($users))
  $assignedTo = $rwUsers['first_name'] . ' ' . $rwUsers['last_name'];

//Supervisor

 $user = mysqli_query($con, "select first_name,last_name from tbl_user_master where user_id='$rwWork[supervisor]'");
 $rwUser = mysqli_fetch_assoc($user);
 $supervisor = $rwUser['first_name'] . ' ' . $rwUser['last_name'];

// Alertnate User

  $user = mysqli_query($con, "select first_name,last_name from tbl_user_master where user_id='$rwWork[alternate_user]'");
  $rwUser = mysqli_fetch_assoc($user);
  $alternateUser =$rwUser['first_name'] . ' ' . $rwUser['last_name'];


   $docid = $rwDms['doc_id'];

    //for the pdf doc
    $DocQry = "SELECT * FROM tbl_document_master where doc_id ='$docid' order by dateposted desc";
    $Doc = mysqli_query($con, $DocQry) or die('Error' . mysqli_error($con));

    while($pdf = mysqli_fetch_assoc($Doc)){

	     $docPath = getDocumentPath($con, $pdf['doc_id'],$pdf['old_doc_name'],$pdf['doc_path'], $pdf['doc_extn'], $pdf['doc_name'], $_POST['userid']);
         $temp =array();
         $temp['old_doc_name'] = $pdf['old_doc_name'];
         $temp['doc_id'] = $pdf['doc_id'];
         $temp['doc_path'] = $docPath;
         $temp['doc_extn'] = $pdf['doc_extn'];

         array_push($doc_list,$temp);

		$docid = $pdf['doc_id'];
         
		$getDocQry = "SELECT * FROM tbl_document_master where doc_name  LIKE CONCAT('%', '$docid', '%') order by dateposted desc";
		$getDoc = mysqli_query($con, $getDocQry) or die('Error' . mysqli_error($con));

		while($doclist = mysqli_fetch_assoc($getDoc)){

			 $docPath = getDocumentPath($con, $doclist['doc_id'],$doclist['old_doc_name'],$doclist['doc_path'], $doclist['doc_extn'], $doclist['doc_name'], $_POST['userid']);
			$t =array();
			 $t['old_doc_name'] = $doclist['old_doc_name'];
			 $t['doc_id'] = $doclist['doc_id'];
			 $t['doc_path'] = $docPath;
			 $t['doc_extn'] = $doclist['doc_extn'];

			  array_push($doc_list,$t);

		}

    }
    


  $temp = array();
  
  $temp['error'] = 'false';
  $temp['priority'] = $priority;
  $temp['task_status'] = $rwTask['task_status'];
  $temp ['deadline'] = $deadline;
  $temp ['workflow_name'] = $workflowName;
  $temp ['task_name'] = $taskName;
  $temp ['submitted_by'] = $submittedBy;
  $temp ['assigned_to'] = $assignedTo;
  $temp ['supervisor'] = $supervisor;
  $temp ['alternate_user'] = $alternateUser;
  $temp ['description'] = $rwTask['task_remarks'];
  $temp ['doc'] = $doc_list;
  $temp ['error'] = 'false';

  echo json_encode($temp);





}


//add comment 

if(isset($_POST['useridComment']) && !empty($_POST['useridComment'])
     && isset($_POST['comment']) && !empty($_POST['comment'])
  && isset($_POST['taskidComment']) && !empty($_POST['taskidComment'])){

   $comment = mysqli_real_escape_string($con, $_POST['comment']);

   $id = $_POST['taskidComment'];
   $date =date("Y-m-d H:i:s");
   
   $task = mysqli_query($con, "select * from tbl_doc_assigned_wf where id='$id' and (task_status='Pending' or task_status='Approved') ");
   $rwTask = mysqli_fetch_assoc($task);
   

    if (!empty($comment)) {
        
        $user_id = $_POST['useridComment'];
        $ticketid = $rwTask['ticket_id'];
        $taskid = $rwTask['task_id'];

        $cmttask = "INSERT INTO tbl_task_comment (tickt_id, user_id, comment, task_status, comment_time, task_id) VALUES ('$ticketid', '$user_id','$comment', 'comment', '$date', '$taskid')";
        $run = mysqli_query($con, $cmttask) or die('Error query failed' . mysqli_error($con));
       
         $temp = array();
        if($run){

          $temp['error'] = 'false';
          $temp['message'] = 'Comment Added Successfully !';

        }

        else{

          $temp['error'] = 'true';
          $temp['message'] = 'Error in adding Comment ';

        }

      /*  echo '<script>uploadSuccess("process_task?id=' . urlencode($_GET['id']) . '", "Comment Added Successfully !");</script>';
*/


  echo json_encode($temp);

}
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