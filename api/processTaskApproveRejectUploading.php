<?php

require_once 'connection.php';
require_once 'classes/function.php';
require 'mail.php';
require_once 'notification.php';


//without Uploading

if 
(
   isset($_POST['comment'])&&!empty($_POST['comment'])&&
   isset($_POST['userid'])&&!empty($_POST['userid'])&&
   isset($_POST['username'])&&!empty($_POST['username'])&&
   isset($_POST['taskid'])&&!empty($_POST['taskid'])&&
   isset($_POST['taskstatus'])&&!empty($_POST['taskstatus'])&&  
   isset($_POST['asiusr'])&&!empty($_POST['asiusr'])&&  
   isset($_POST['taskOrder'])&&!empty($_POST['taskOrder'])&&  
   isset($_POST['altrUsr'])&&!empty($_POST['altrUsr'])&&  
   isset($_POST['supvsr'])&&!empty($_POST['supvsr'])&&  
   isset($_POST['assignUsrAdd'])&&!empty($_POST['assignUsrAdd'])&&  
   isset($_POST['altrUsrAdd'])&&!empty($_POST['altrUsrAdd'])&&  
   isset($_POST['supvsrAdd'])&&!empty($_POST['supvsrAdd'])&&  
   isset($_POST['radio'])&&!empty($_POST['radio'])&&  
   isset($_POST['daterangeAdd'])&&!empty($_POST['daterangeAdd'])&&  
   isset($_POST['daysAdd'])&&!empty($_POST['daysAdd'])&&  
   isset($_POST['hrsAdd'])&&!empty($_POST['hrsAdd'])&& 
   isset($_POST['ip'])&&!empty($_POST['ip'])
  
    
)

{


            date_default_timezone_set("Asia/Kolkata");
            $date = date("Y-m-d H:i");
            $docID = '0';
            $host = $_POST['ip'];

            $comment = mysqli_real_escape_string($con, $_POST['comment']);

            if($comment == "null"){

                $comment = "";
            }

           // $tktId = $_POST['ticketid'];
            $user_id = $_POST['userid'];
            $taskId = $_POST['taskid'];
            $username = $_POST['username'];
           // $file = $_FILES['fileName'];
            $taskid = $_POST['taskid'];
            $projectName = "EzeeProcess";


              $ticketid = mysqli_query($con, "select * from tbl_doc_assigned_wf where id='$taskid'");
              $rwtid = mysqli_fetch_assoc($ticketid);
              $id = $rwtid['id'];

          /*   echo "select * from tbl_doc_assigned_wf where id='$id' and (task_status='Pending' or task_status='Approved') ";
             die;*/

             $task = mysqli_query($con, "select * from tbl_doc_assigned_wf where id='$id' and (task_status='Pending' or task_status='Approved') ");
             $rwTask = mysqli_fetch_assoc($task);
             $rw = $rwTask['task_id'];

             //echo $rw;
             $ticketid = mysqli_query($con, "select * from tbl_doc_assigned_wf where id='$taskid'");
             $rwticketId = mysqli_fetch_assoc($ticketid);
             $tktId = $rwticketId['ticket_id'];

                $logTaskName = mysqli_query($con, "select task_name from tbl_task_master where task_id = '$taskid'") or die('Erorr getting Name:' . mysqli_error($con));
                $rwlogTaskName = mysqli_fetch_assoc($logTaskName);
                $ltaskName = $rwlogTaskName['task_name'];


        if ($_POST['userid'] != '1') {
            
        $work = mysqli_query($con, "select * from tbl_task_master where task_id='$rw' and (assign_user = '$user_id' or alternate_user='$user_id' or supervisor='$user_id')");
        if (mysqli_num_rows($work) > 0) {
            
            $rwWork = mysqli_fetch_assoc($work);
            
            $ltaskName = $rwWork['task_name'];
        } else {
           // header("Location:index");
        }
    } else {
        //$rwTask[task_id];
        $work = mysqli_query($con, "select * from tbl_task_master where task_id='$rw'");
        if (mysqli_num_rows($work) > 0) {
            $rwWork = mysqli_fetch_assoc($work);
        } else {
           // header("Location:index");
        }
    }

     $getOwnTask = mysqli_query($con, "select * from tbl_task_master where task_id='$rw'") or die('Error getOwntask:' . mysqli_error($con));
     $rwgetOwnTask = mysqli_fetch_assoc($getOwnTask);
     $TskStpId = $rwgetOwnTask['step_id'];
     $TskWfId = $rwgetOwnTask['workflow_id'];
     $TskOrd = $rwgetOwnTask['task_order'];
     $TskAsinToId = $rwgetOwnTask['assign_user'];
     $cTaskid = $rwgetOwnTask['task_id'];
     $cTaskOrd = $TskOrd;

     $ctaskID = "";

      $deadLineAdd ="";

                                      
                                   /*   echo $cTaskOrd."/n";
                                      echo $TskWfId."/n";
                                      echo $TskStpId."/n";
                                     die;*/
                                      //echo $con;
    $nextTskId = nextTaskToUpdate($cTaskOrd, $TskWfId, $TskStpId, $con);



                             /*     echo $nextTskId;
                               die;*/


    $getNxtTask = mysqli_query($con, "select * from tbl_task_master where task_id='$nextTskId'") or die('Error:' . mysqli_error($con));
    $rwgetNextTask = mysqli_fetch_assoc($getNxtTask);
    $rwgetNextTask['task_order'];


            if (isset($_FILES["fileName"]) && !empty( $_FILES["fileName"]["name"])) {

                $file_name = $_FILES['fileName']['name'];
                $file_size = $_FILES['fileName']['size'];
                $file_type = $_FILES['fileName']['type'];
                $file_tmp = $_FILES['fileName']['tmp_name'];
                $pageCount = $_POST['pageCount'];

                $extn = substr($file_name, strrpos($file_name, '.') + 1);
                $fname = substr($file_name, 0, strrpos($file_name, '.'));

                $fileExtn = substr($file_name, strrpos($file_name, ".") + 1);

                $getDocId = mysqli_query($con, "select * from tbl_doc_assigned_wf where id = '$id'") or die('Error getdocid:' . mysqli_error($con));
                $rwgetDocId = mysqli_fetch_assoc($getDocId);
                $doc_id = $rwgetDocId['doc_id'];

                $getDocName = mysqli_query($con, "select * from tbl_document_master where doc_id = '$doc_id'") or die('Error getdocname:' . mysqli_error($con));
                $rwgetDocName = mysqli_fetch_assoc($getDocName);
                $docName = $rwgetDocName['doc_name'];
                $docName = explode("_", $docName);

                if(count($docName)>2){

                      $updateDocName = $docName[0] . '_' . $doc_id . '_' . $docName[1];
                }

                else{

                 $updateDocName = $docName[0] . '_' .$rwTask['doc_id'];  

                }

              

                $chekFileVersion = mysqli_query($con, "SELECT * FROM `tbl_document_master` WHERE find_in_set('$updateDocName', doc_name)") or die('Error:' . mysqli_error($con));
                $flVersion = mysqli_num_rows($chekFileVersion);
                $flVersion = $flVersion + 1;
                $file_name = $tktId . '_' . $flVersion . '.' . $fileExtn;


                $strgName = mysqli_query($con, "select * from tbl_storage_level where sl_id = '$docName[0]'") or die('Error strgName:' . mysqli_error($con));
                $rwstrgName = mysqli_fetch_assoc($strgName);
                $storageName = $rwstrgName['sl_name'];
                $storageName = str_replace(" ", "", $storageName);
                $storageName = preg_replace('/[^A-Za-z0-9\-]/', '', $storageName);
                $uploaddir = "../../extract-here/" . $storageName . '/';
                if (!is_dir($uploaddir)) {
                    mkdir($uploaddir, 777, TRUE) or die(print_r(error_get_last()));
                }

                $fname = preg_replace('/[^A-Za-z0-9_\-]/', '', $fname);
                // $filenameEnct=$fname.'.'.$extn;// urlencode(base64_encode($fname)).'.'.$extn;
                $filenameEnct = urlencode(base64_encode($fname));
                $filenameEnct = preg_replace('/[^A-Za-z0-9_\-]/', '', $filenameEnct);
                $filenameEnct = $filenameEnct . '.' . $extn;
                $filenameEnct = time() . $filenameEnct;

                //  $image_path = "images/" . $file_name;
                $uploaddir = $uploaddir . $filenameEnct;
                $upload = move_uploaded_file($file_tmp, $uploaddir) or die(print_r(error_get_last()));
                $logTaskName = mysqli_query($con, "select task_name from tbl_task_master where task_id = '$taskId'") or die('Error getting Name:' . mysqli_error($con));
                $rwlogTaskName = mysqli_fetch_assoc($logTaskName);
                $ltaskName = $rwlogTaskName['task_name'];

                if ($upload) {
					
					$destinationPath =$storageName.'/'.$filenameEnct;
					$sourcePath = $uploaddir; 
					if(uploadFileInFtpServer($destinationPath, $sourcePath)){
						 
						//unlink($sourcePath);
					}
					else
					{	
						$temp = array();
						$temp['error'] = 'false';
						$temp['msg'] = 'File upload failed'; 
						echo json_encode($temp); 
						exit();
					}
					
					
                    $cols = '';
                    $columns = mysqli_query($con, "SHOW COLUMNS FROM tbl_document_master");
                    while ($rwCols = mysqli_fetch_array($columns)) {
                        if ($rwCols['Field'] != 'doc_id') {
                            if (empty($cols)) {
                                $cols = '`' . $rwCols['Field'] . '`';
                            } else {
                                $cols = $cols . ',`' . $rwCols['Field'] . '`';
                            }
                        }
                    }

                    //"INSERT INTO tbl_document_master($cols) select $cols from tbl_document_master where doc_id='$doc_id'";
                    $createVrsn = mysqli_query($con, "INSERT INTO tbl_document_master($cols) select $cols from tbl_document_master where doc_id='$doc_id'") or die('Error insert:' . mysqli_error($con));
                    $insertDocID = mysqli_insert_id($con);
                    //$createVrsn = mysqli_query($con, "INSERT INTO tbl_document_master(doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages, dateposted) VALUES ('$updateDocName', '$file_name', '$fileExtn', 'images/$storageName/$filenameEnct', '$user_id', '$file_size', '$pageCount', '$date')") or die('Error:' . mysqli_error($con));
                    $log = mysqli_query($con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `doc_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$user_id', '$username',null,null, '$doc_id','Versioning Document $file_name Added in task $ltaskName','$date',null,'$host',null)") or die('error log : ' . mysqli_error($con));
                    if ($createVrsn) {
                        $updateNew = mysqli_query($con, "update tbl_document_master set doc_name='$updateDocName' where doc_id='$insertDocID'");
                        $updateOld = mysqli_query($con, "update tbl_document_master set old_doc_name='$file_name',filename='$fname', doc_extn='$extn', doc_path='$storageName/$filenameEnct', uploaded_by='$user_id', doc_size='$file_size', noofpages='$pageCount', dateposted='$date' where doc_id='$doc_id'");
                        //echo'<script>taskSuccess("process_task?id=' . $_GET[id] . ((isset($_GET[start])) ? ('&start=' . $_GET[start]) : '') . '","Updated Successfully !");</script>';
                         
                        /* $temp = array();
                         $temp['error']  = 'false';
                         $temp['message'] = 'Updated Successfully';

                         echo json_encode($temp); */
                         

                    }
               
                    else{

                       /*  $temp = array();
                         $temp['error']  = 'true';
                         $temp['message'] = 'Update unsuccesful';

                         echo json_encode($temp); */

                    }

                }
            }

            //app = taskstatus

            if (!empty($_POST['taskstatus']) && $_POST['taskstatus'] != 'null') {


                $app = $_POST['taskstatus'];

                // if (!empty($comment) || !empty($app))

                if (!empty($comment) || !empty($app)) {

                    //$user_id = $_SESSION['cdes_user_id'];
                    $cmttask = "INSERT INTO tbl_task_comment (`id`, `tickt_id`, `user_id`, `comment`, task_status, `comment_time`, task_id) VALUES (null,'$tktId', '$user_id','$comment', '$app', '$date', '$cTaskid')";
                    $run = mysqli_query($con, $cmttask) or die('Error query failed' . mysqli_error($con));
                    $log = mysqli_query($con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$user_id', '$username',null,null,' Comment $comment Added in task $ltaskName','$date',null,'$host',null)") or die('error cmnt task: ' . mysqli_error($con));



                }


                if ($app == 'Approved' || $app == 'Processed' || $app == 'Done') {

                /*      echo "UPDATE tbl_doc_assigned_wf SET task_status = '$app', action_by = '$user_id', action_time = '$date' where id='$id' ";
                      die;*/
                    

                    $run = mysqli_query($con, "UPDATE tbl_doc_assigned_wf SET task_status = '$app', action_by = '$user_id', action_time = '$date' where id='$id' ") or die('Error query failed' . mysqli_error($con));


                    $log = mysqli_query($con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$user_id', '$username',null,null,'$ltaskName task $app ','$date',null,'$host',null)") or die('error run : ' . mysqli_error($con));

                    $assignBy = $rwTask['assign_by'];

                   
                    if (!empty($rwTask['doc_id'])) {
                        $docID = $rwTask['doc_id'];
                    }

                    $ctaskID = $rwWork['task_id'];
                    $ctaskOrder = $rwWork['task_order'];
                    $stepId = $rwWork['step_id'];
                    $wfid = $rwWork['workflow_id'];
                    $ticket = $rwTask['ticket_id'];

                
                    $taskRemark = mysqli_real_escape_string($con, $rwTask['task_remarks']);

                    //$tskAsinTOUsrId = $rwWork['assign_user'];

                    $getTskName = mysqli_query($con, "select * from tbl_task_master where task_id = '$ctaskID' ") or die('Error gettaskname' . mysqli_error($con));
                    $rwgetTskName = mysqli_fetch_assoc($getTskName);

                    //send sms to mob
//                    $getMobNum = mysqli_query($con, "select * from tbl_user_master where user_id = '$assignBy'") or die('Error:' . mysqli_error($con));
//                    $rwgetMobNum = mysqli_fetch_assoc($getMobNum);
//                    $submtByMob = $rwgetMobNum['phone_no'];
//                    $msg = 'Your Ticket Id ' . $ticket . ' is Approved in Task ' . $rwgetTskName['task_name'] . '.';
//                    $sendMsgToMbl = smsgatewaycenter_com_Send($submtByMob, $msg, $debug = false);
                    //
                    // $tt = taskAssignToUser($con, $stepId, $ctaskOrder, $docID, $ctaskID, $assignBy, $id, $wfid, $ticket, $taskRemark);
                    //upadte own Created user and order
                    //if (!empty($_POST['asiusr']) && !empty($_POST['altrUsr']) && !empty($_POST['supvsr'])) {

                    if (!empty($_POST['asiusr']) && $_POST['asiusr'] != 'null') {
                        $taskOrder = $_POST['taskOrder'];
                        $assiUsers = $_POST['asiusr'];
                        $altrusr = $_POST['altrUsr'];
                        $supvsr = $_POST['supvsr'];

                        /* echo $assiUsers;
                         echo $altrusr;
                         echo $supvsr;

                         die;*/
                         
                      /*  echo  "update tbl_task_master set assign_user='$assiUsers', alternate_user='$altrusr', supervisor='$supvsr', task_order='$taskOrder' where task_id = '$nextTskId'";

                        die;
*/
                        $updOwnTask = mysqli_query($con, "update tbl_task_master set assign_user='$assiUsers', alternate_user='$altrusr', supervisor='$supvsr'where task_id = '$nextTskId'") or die('Error hhh' . mysqli_error($con));
                       
                        //$updOwnTask = mysqli_query($con, "update tbl_task_master set assign_user='$assiUsers', alternate_user='$altrusr', deadline='$deadLine', deadline_type='$deadlineType', supervisor='$supvsr', task_order='$taskOrder', task_created_date='$date' where task_id = '$taskId") or die('Error' . mysqli_error($con));
                        //$log = mysqli_query($con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Assign User order updated in $ltaskName','$date',null,'$host',null)") or die('error : ' . mysqli_error($con));
                    }
                    
                    //Add new user to asign task
                    //if (!empty($_POST['assignUsrAdd']) && !empty($_POST['altrUsrAdd']) && !empty($_POST['supvsrAdd']) && !empty($_POST['radio'])) {
                   

                    if (!empty($_POST['assignUsrAdd']) && $_POST['assignUsrAdd']!="null")

                     {

                        
                         $assiUsersAdd = $_POST['assignUsrAdd'];
                         $altrusrAdd = $_POST['altrUsrAdd'];
                        $supvsrAdd = $_POST['supvsrAdd'];
                        $deadlineType = $_POST['radio'];

                        if ($deadlineType == 'Date') {

                            $daterange = $_POST['daterangeAdd'];

                            $daterangee = explode("To", $daterange);
                           
                          /*  print_r($daterangee);

                            die;*/

                            $startDate = date('Y-m-d H:i:s', strtotime($daterangee[0]));

                            $endDate = date('Y-m-d H:i:s', strtotime($daterangee[1]));


                           /* echo $startDate;
                            echo $endDate;

                            die;*/

                            $date1 = new DateTime($startDate);
                            $date2 = new DateTime($endDate);
                            //print_r($date1);
                            // print_r($date2);
                            $diff = $date1->diff($date2);

                            $deadLineAdd = $diff->h * 60 + $diff->days * 24 * 60 + $diff;  //convert in minute
                           /* echo $deadLine=$deadLine.'.'.$diff;
                            echo   $deadLine=round($deadLine/60*60,1);
                            die();*/
                            //echo $deadLine; 
                        } else if ($deadlineType == 'Days') {
                            
                            $deadLinee = $_POST['daysAdd'];
                            $deadLineAdd = $deadLinee;

                        } else if ($deadlineType == 'Hours') {

                            $deadLinee = $_POST['hrsAdd'];
                            $deadLineAdd = $deadLinee * 60;

                        }
                        // echo 'ok1';
                        $cTskOrd = $TskOrd;
                        $cTskId = $rwTask['task_id'];
                        $host = $_POST['ip'];
                        $user_id = $_POST['userid'];
                        $username = $_POST['username'];
                        //echo $TskWfId;
                        //$TskStpId = $rwgetOwnTask['step_id'];
                        //$TskWfId = $rwgetOwnTask['workflow_id'];
                        //$TskOrd = $rwgetOwnTask['task_order'];
                        // echo 'dedline: ';
                        // echo $deadLineAdd;
                        $addUsr = addNewTskUsr($cTskId, $TskWfId, $TskStpId, $cTskOrd, $assiUsersAdd, $altrusrAdd, $supvsrAdd, $deadLineAdd, $deadlineType, $date, $con,$user_id,$username,$host);

                     /*   echo "select * from tbl_task_master where task_id = '$cTskId'";

                        die;*/
                        
                        $getTask = mysqli_query($con, "select * from tbl_task_master where task_id = '$cTskId'") or die('Error gettask:' . mysqli_error($con));
                        $rwgetTask = mysqli_fetch_assoc($getTask);
                        $TskStpId = $rwgetTask['step_id'];
                        $TskWfId = $rwgetTask['workflow_id'];
                        $TskOrd = $rwgetTask['task_order'];
                        $TskAsinToId = $rwgetTask['assign_user'];
                        $ticketid = $rwgetTask['ticket_id'];
                        $nextTaskOrd = $TskOrd + 1;
                        $date = date("Y-m-d H:i");


                       /* echo $date;
                        die;*/

                        nextTaskAsin($nextTaskOrd, $TskWfId, $TskStpId, $docID, $date, $user_id, $con, $taskRemark, $ticket);


    $getAssignBy = mysqli_query($con, "select * from tbl_doc_assigned_wf where task_id='$ctaskID' and ticket_id ='$tktId' ") or die('Error:' . mysqli_error($con));
    $rwgetAssBy = mysqli_fetch_assoc($getAssignBy);
    $assignBy = $rwgetAssBy['assign_by'];
    $ticketid = $rwgetAssBy['ticket_id'];
    $task_id = $rwgetAssBy['task_id'];

    //getting the next taskasigned user 

     $gettaskOdr = mysqli_query($con, "select * from tbl_task_master where task_id = '$task_id'") or die('Error:' . mysqli_error($con));
     $rwgetOdr = mysqli_fetch_assoc($gettaskOdr);
     $stepid = $rwgetOdr['step_id'];
     $tsk_ordr = $rwgetOdr['task_order'] + 1;

    //echo "select * from tbl_task_master where step_id = '$stepid' and task_order ='$tsk_ordr'";
   

      $checkNextTask = mysqli_query($con, "select * from tbl_task_master where step_id = '$stepid' and task_order ='$tsk_ordr'") or die('Error:' . mysqli_error($con));
      //checking if there is any next task
     if(mysqli_num_rows($checkNextTask)>0){

       
 
      $rwgetCheckNxt = mysqli_fetch_assoc($checkNextTask);

        $nxtAsignUser = $rwgetCheckNxt['assign_user'];
        $nxtTaskId =$rwgetCheckNxt['task_id'];
        $nxtTaskName =$rwgetCheckNxt['task_name']; 

    /*   echo "select * from tbl_doc_assigned_wf where task_id='$nxtTaskId' and ticket_id ='$tktId'";
       die;  */
       
      $getId = mysqli_query($con, "select * from tbl_doc_assigned_wf where task_id='$nxtTaskId' and ticket_id ='$tktId'") or die('Error:' . mysqli_error($con));
      $rwgetGetId = mysqli_fetch_assoc($getId);
      $nxtId =$rwgetGetId['id']; 
    
          
    //getting the token of firebase for sending notification to respective user 
     $getTokenid = mysqli_query($con, "select * from tbl_user_master where user_id = $nxtAsignUser") or die('Error:' . mysqli_error($con));
     $rwgetToken= mysqli_fetch_assoc($getTokenid);
     $tokenid = $rwgetToken['fb_tokenid'];

   
     //This notification is for inTray
     sendPushNotification($tokenid,$nxtAsignUser,$nxtId,$nxtTaskName,$username,'In Tray','New Task '.$nxtTaskName.' has been assigned to you'); 



                          



                    }

                }

                   // echo 'stepid: ' . $stepId;

                    taskAssignToUser($con, $stepId, $ctaskOrder, $docID, $ctaskID, $assignBy, $id, $wfid, $ticket, $taskRemark, $date);

                         $temp = array();
                         $temp['error']  = 'false';
                         $temp['message'] = "Task Completed successfully !";
                         echo json_encode($temp); 

                          //notification

/* 1)Here one notification for the user who intiated the task
   2)One notification for the user who is next person assigned
*/


    //$ctaskID = $rwWork['task_id'];
  // $ctaskOrder = $rwWork['task_order'];
  // $stepId = $rwWork['step_id'] +1;
   // $wfid = $rwWork['workflow_id'];
   //  $ticket = $rwTask['ticket_id'];

               
 // $ctaskOrder = $rwWork['task_order']+1;
 
//getting the userid of the person who initiated the Worklflow


   // echo "select * from tbl_doc_assigned_wf where task_id='$ctaskID' and ticket_id ='$tktId' ";

   
    $getAssignBy = mysqli_query($con, "select * from tbl_doc_assigned_wf where task_id='$ctaskID' and ticket_id ='$tktId' ") or die('Error:' . mysqli_error($con));
    $rwgetAssBy = mysqli_fetch_assoc($getAssignBy);
    $assignBy = $rwgetAssBy['assign_by'];
    $ticketid = $rwgetAssBy['ticket_id'];
    $task_id = $rwgetAssBy['task_id'];

    //getting the next taskasigned user 

     $gettaskOdr = mysqli_query($con, "select * from tbl_task_master where task_id = '$task_id'") or die('Error:' . mysqli_error($con));
     $rwgetOdr = mysqli_fetch_assoc($gettaskOdr);
     $stepid = $rwgetOdr['step_id'];
     $tsk_ordr = $rwgetOdr['task_order'] + 1;

    //echo "select * from tbl_task_master where step_id = '$stepid' and task_order ='$tsk_ordr'";
   

      $checkNextTask = mysqli_query($con, "select * from tbl_task_master where step_id = '$stepid' and task_order ='$tsk_ordr'") or die('Error:' . mysqli_error($con));
      //checking if there is any next task
     if(mysqli_num_rows($checkNextTask)>0){
 
      $rwgetCheckNxt = mysqli_fetch_assoc($checkNextTask);

        $nxtAsignUser = $rwgetCheckNxt['assign_user'];
        $nxtTaskId =$rwgetCheckNxt['task_id'];
        $nxtTaskName =$rwgetCheckNxt['task_name']; 

    /*   echo "select * from tbl_doc_assigned_wf where task_id='$nxtTaskId' and ticket_id ='$tktId'";
       die;  */
       
      $getId = mysqli_query($con, "select * from tbl_doc_assigned_wf where task_id='$nxtTaskId' and ticket_id ='$tktId'") or die('Error:' . mysqli_error($con));
      $rwgetGetId = mysqli_fetch_assoc($getId);
      $nxtId =$rwgetGetId['id']; 
    
          
    //getting the token of firebase for sending notification to respective user 
     $getTokenid = mysqli_query($con, "select * from tbl_user_master where user_id = $nxtAsignUser") or die('Error:' . mysqli_error($con));
     $rwgetToken= mysqli_fetch_assoc($getTokenid);
     $tokenid = $rwgetToken['fb_tokenid'];

   
     //This notification is for inTray
     sendPushNotification($tokenid,$nxtAsignUser,$nxtId,$nxtTaskName,$username,'In Tray','New Task '.$nxtTaskName.' has been assigned to you'); 

     //echo 'ok';

       

     }


     //getting the token of firebase for sending notification to respective user 
     $getTokenid = mysqli_query($con, "select * from tbl_user_master where user_id = $assignBy") or die('Error:' . mysqli_error($con));
     $rwgetToken= mysqli_fetch_assoc($getTokenid);
     $tokenid = $rwgetToken['fb_tokenid'];


   /*  print_r($ticketid);

     die;  */  
    
     //This notification is for Task Track
    sendPushNotification($tokenid,$assignBy,$ticketid,$taskname,$username,'Task Track', $ltaskName. ' has been '. $app); 


/*//getting the next task id of task 
    $getNextTask = mysqli_query($con, "select * from tbl_task_master where task_order='$ctaskOrder' and step_id='$stepId'") or die('Error:' . mysqli_error($con));
    $rwgetNextTask = mysqli_fetch_assoc($getNextTask);
    $nxtuserid = $rwgetNextTask['assign_user'];
    $taskname = $rwgetNextTask['task_name'];

     $getTokenid = mysqli_query($con, "select * from tbl_user_master where user_id = $nxtuserid") or die('Error:' . mysqli_error($con));
     $rwgetToken= mysqli_fetch_assoc($getTokenid);
     $tokenid = $rwgetToken['fb_tokenid'];

     // getting id of the taskid of the task 
     $getTaskId = mysqli_query($con, "SELECT * FROM tbl_doc_assigned_wf where task_id = '$tskId' and NextTask ='0' order by start_date desc ") or die('Error:' . mysqli_error($con));
     $rwgetTaskId= mysqli_fetch_assoc($getTaskId);
     $id = $rwgetTaskId['id'];

    sendPushNotification($tokenid,$nxtuserid,$id,$taskname,'In Tray','New Task '."$taskname". ' has been assigned to you'); */






                   // echo '<script>taskSuccess("myTask","Task Completed successfully !");</script>';
                } 


                else if ($app == 'Rejected') {

                     /* echo $ctaskID;
                      die;*/
                     
                    //$ticket_query= mysqli_query($con, "SELECT NextTask,ticket_id FROM tbl_doc_assigned_wf where id='$id' ") or die('Error query failed pp:' . mysqli_error($con));
                    //$row_ticket_id=mysqli_fetch_array($ticket_query);


                    $run = mysqli_query($con, "UPDATE tbl_doc_assigned_wf SET task_status = '$app', action_by = '$user_id',action_time = '$date' where id='$id' ") or die('Error query failed pp:' . mysqli_error($con));

                    $log = mysqli_query($con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null,'$user_id', '$username' ,null,null,'$ltaskName task $app ','$date',null,'$host',null)") or die('error : ' . mysqli_error($con));

                   

                    $getTskName = mysqli_query($con, "select * from tbl_task_master where task_id = '$ctaskID' ") or die('Error gettaskName' . mysqli_error($con));
                    $rwgetTskName = mysqli_fetch_assoc($getTskName);
                    
                    
                    $ctaskID = $rwWork['task_id'];
                    $ctaskOrder = $rwWork['task_order'];
                    $stepId = $rwWork['step_id'];
                    $wfid = $rwWork['workflow_id'];
                    $ticket = $rwTask['ticket_id'];
                    $assignBy = $rwTask['assign_by'];
                    $taskRemark = mysqli_real_escape_string($con, $rwTask['task_remarks']);
                    $projectName = "EzeeProcess";
                    $userid = $user_id;


                  /*  echo "taskid : ".$ctaskID;
                    echo "taskOrder : ".$ctaskOrder;
                    echo "stepId : ".$stepId;
                    echo "wfid : ".$wfid;
                    echo "ticket id : ".$ticket;

                    die;*/


                    backToPrevTsk($con, $stepId, $ctaskOrder, $docID, $ctaskID, $assignBy, $id, $wfid, $tktId, $taskRemark, $date, $projectName);

                     //This notification is for inTray
                 // sendPushNotification($tokenid,$prevAsignUser,$prevId,$prevTaskName,$username,'In Tray','New Task '.$prevTaskName.' has been assigned to you'); 

                     
               
                    //echo 'mail send id = '.$id; die;
                    $mail = rejectTask($id, $ctaskID, $tktId, $con, $projectName,$comment,$docID,$userid);
                    //$delete = mysqli_query($con, "DELETE FROM tbl_doc_assigned_wf WHERE ticket_id='$row_ticket_id[ticket_id]' AND NextTask=2") or die('Error query failed pp:' . mysqli_error($con));
                    if ($mail) {



                        //send sms to mob
//                        $getMobNum = mysqli_query($con, "select * from tbl_user_master where user_id = '$assignBy'") or die('Error:' . mysqli_error($con));
//                        $rwgetMobNum = mysqli_fetch_assoc($getMobNum);
//                        $submtByMob = $rwgetMobNum['phone_no'];
//                        $msg = 'Your Ticket Id ' . $ticket . ' is Rejected in Task ' . $rwgetTskName['task_name'] . '.';
//                        $sendMsgToMbl = smsgatewaycenter_com_Send($submtByMob, $msg, $debug = false);
                        //

                       // echo '<script>taskSuccess("myTask", "Task has been rejected !");</script>';

                         $temp = array();
                         $temp['error']  = 'false';
                         $temp['message'] = "Task has been rejected !";
                         echo json_encode($temp); 



//getting the userid of the person who initiated the Worklflow

 //  echo "select * from tbl_doc_assigned_wf where task_id='$ctaskID' and ticket_id ='$tktId' ";

    $getAssignBy = mysqli_query($con, "select * from tbl_doc_assigned_wf where task_id='$ctaskID' and ticket_id ='$tktId' ") or die('Error:' . mysqli_error($con));
    $rwgetAssBy = mysqli_fetch_assoc($getAssignBy);
    $assignBy = $rwgetAssBy['assign_by'];
    $ticketid = $rwgetAssBy['ticket_id'];
    $task_id = $rwgetAssBy['task_id'];

     //getting the previous taskasigned user 

     $gettaskOdr = mysqli_query($con, "select * from tbl_task_master where task_id = '$task_id'") or die('Error:' . mysqli_error($con));
     $rwgetOdr = mysqli_fetch_assoc($gettaskOdr);
     $stepid = $rwgetOdr['step_id'];
     $tsk_ordr = $rwgetOdr['task_order']-1;

    // echo "select * from tbl_task_master where step_id = '$stepid' and task_order ='$tsk_ordr'";
   

      $checkPrevTask = mysqli_query($con, "select * from tbl_task_master where step_id = '$stepid' and task_order ='$tsk_ordr'") or die('Error:' . mysqli_error($con));
       

       //echo "num of rows rejected ".mysqli_num_rows($checkNextTask);

      //checking if there is any next task
     if(mysqli_num_rows($checkPrevTask)>0){

        // echo "entered rejectd";
 
      $rwgetCheckPrev = mysqli_fetch_assoc($checkPrevTask);

        echo $prevAsignUser = $rwgetCheckPrev['assign_user'];
        $prevTaskId =$rwgetCheckPrev['task_id'];
        $prevTaskName =$rwgetCheckPrev['task_name']; 

    /*   echo "select * from tbl_doc_assigned_wf where task_id='$nxtTaskId' and ticket_id ='$tktId'";
       die;  */
       
      $getId = mysqli_query($con, "select * from tbl_doc_assigned_wf where task_id='$prevTaskId' and ticket_id ='$tktId'") or die('Error:' . mysqli_error($con));
      $rwgetGetId = mysqli_fetch_assoc($getId);
      $prevId =$rwgetGetId['id']; 
    
          
    //getting the token of firebase for sending notification to respective user 
     $getTokenid = mysqli_query($con, "select * from tbl_user_master where user_id = $prevAsignUser") or die('Error:' . mysqli_error($con));
     $rwgetToken= mysqli_fetch_assoc($getTokenid);
     $tokenid = $rwgetToken['fb_tokenid'];

   
     //This notification is for inTray
     sendPushNotification($tokenid,$prevAsignUser,$prevId,$prevTaskName,$username,'In Tray','New Task '.$prevTaskName.' has been assigned to you'); 

  // echo "ok";

       

     }


     $getTokenid = mysqli_query($con, "select * from tbl_user_master where user_id = $assignBy") or die('Error:' . mysqli_error($con));
     $rwgetToken= mysqli_fetch_assoc($getTokenid);
     $tokenid = $rwgetToken['fb_tokenid'];    

     sendPushNotification($tokenid,$assignBy,$ticketid,$taskname,$username,'Task Track', $ltaskName. ' has been '. $app); 

                    } else {

                         $temp = array();
                         $temp['error']  = 'true';
                         $temp['message'] = "Opps!! Task is not rejected !";
                         echo json_encode($temp); 

                        //echo '<script>taskFailed("myTask", "Opps!! Task is not rejected !")</script>';


                    }
                   

                } else if ($app == 'Aborted') {

                    $ctaskID = $rwWork['task_id'];
                    $ctaskOrder = $rwWork['task_order'];
                    $stepId = $rwWork['step_id'];
                    $wfid = $rwWork['workflow_id'];
                    $ticket = $rwTask['ticket_id'];
                    $projectName = 'EzeeProcess';


                /*    echo "taskid : ".$ctaskID;
                    echo "taskOrder : ".$ctaskOrder;
                    echo "stepId : ".$stepId;
                    echo "wfid : ".$wfid;
                    echo "ticket id : ".$ticket;

                    die;
*/
                    
                    
                   $getTskName = mysqli_query($con, "select * from tbl_task_master where task_id = '$ctaskID' ") or die('Error getattsjjd' . mysqli_error($con));
                    
                    $rwgetTskName = mysqli_fetch_assoc($getTskName);
                    
                   
                    $update = mysqli_query($con, "update tbl_doc_assigned_wf set task_status='$app', action_by='$user_id',action_time='$date',NextTask='5' where id='$id'");
                    
                    $delete = mysqli_query($con, "DELETE FROM tbl_doc_assigned_wf WHERE ticket_id='$tktId' AND NextTask=2") or die('Error query failed pp:' . mysqli_error($con));
                    $log = mysqli_query($con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$user_id','$username',null,null,'$ltaskName task $app','$date',null,'$host',null)") or die('error Abort task : ' . mysqli_error($con));
                    
                    if ($update) {
                       
                        $userid = $user_id;
                        
                        $mailSent = abortTask($ticket, $id, $wfid, $con, $projectName,$userid);
                        
                        if ($mailSent) {

                            //send sms to mob
//                            $getMobNum = mysqli_query($con, "select * from tbl_user_master where user_id = '$assignBy'") or die('Error:' . mysqli_error($con));
//                            $rwgetMobNum = mysqli_fetch_assoc($getMobNum);
//                            $submtByMob = $rwgetMobNum['phone_no'];
//                            $msg = 'Your Ticket Id ' . $ticket . ' is Aborted in Task ' . $rwgetTskName['task_name'] . '.';
//                            $sendMsgToMbl = smsgatewaycenter_com_Send($submtByMob, $msg, $debug = false);
                            //

                         $temp = array();
                         $temp['error']  = 'false';
                         $temp['message'] = "Task has been aborted !";
                          echo json_encode($temp); 


                           
//getting the userid of the person who initiated the Worklflow
   $getAssignBy = mysqli_query($con, "select * from tbl_doc_assigned_wf where task_id='$ctaskID' and ticket_id ='$tktId' ") or die('Error:' . mysqli_error($con));
    $rwgetAssBy = mysqli_fetch_assoc($getAssignBy);
    $assignBy = $rwgetAssBy['assign_by'];
    $ticketid = $rwgetAssBy['ticket_id'];

     $getTokenid = mysqli_query($con, "select * from tbl_user_master where user_id = $assignBy") or die('Error:' . mysqli_error($con));
     $rwgetToken= mysqli_fetch_assoc($getTokenid);
     $tokenid = $rwgetToken['fb_tokenid'];    

    sendPushNotification($tokenid,$assignBy,$ticketid,$taskname,$username,'Task Track', $ltaskName. ' has been '. $app); 


                           // echo '<script>taskSuccess("myTask", "Task has been aborted !");</script>';


                        } else {


                         $temp = array();
                         $temp['error']  = 'true';
                         $temp['message'] = "Opps!! Task is not aborted !";
                          echo json_encode($temp);


                           //  echo '<script>taskFailed("myTask", "Opps!! Task is not aborted !")</script>';


                        }
                    } else {

                         $temp = array();
                         $temp['error']  = 'true';
                         $temp['message'] = "Opps!! Task is not aborted !";
                          echo json_encode($temp);

                       // echo '<script>taskFailed("myTask", "Opps!! Task is not aborted !")</script>';


                    }
                } else if ($app == 'Complete') {


                    $run = mysqli_query($con, "UPDATE tbl_doc_assigned_wf SET task_status = '$app', action_by = '$user_id', action_time = '$date', NextTask='1' where id='$id'") or die('Error query failed' . mysqli_error($con));
                    $ticket = mysqli_query($con, "SELECT NextTask,ticket_id FROM tbl_doc_assigned_wf where id='$id' ") or die('Error query failed pp:' . mysqli_error($con));
                    $row_ticket_id = mysqli_fetch_array($ticket);
                    $delete = mysqli_query($con, "DELETE FROM tbl_doc_assigned_wf WHERE ticket_id='$row_ticket_id[ticket_id]' AND NextTask=2") or die('Error query failed pp:' . mysqli_error($con));
                    if ($delete) {
                        $log = mysqli_query($con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$user_id', '$username',null,null,'$ltaskName task $app ','$date',null,'$host',null)") or die('error log  : ' . mysqli_error($con));
                        $assignBy = $rwTask['assign_by'];

                        if (!empty($rwTask['doc_id'])) {
                            $docID = $rwTask['doc_id'];
                        }
                        $ctaskID = $rwWork['task_id'];
                        $ctaskOrder = $rwWork['task_order'];
                        $stepId = $rwWork['step_id'];
                        $wfid = $rwWork['workflow_id'];
                        $ticket = $rwTask['ticket_id'];


                 /*   echo "taskid : ".$ctaskID;
                    echo "taskOrder : ".$ctaskOrder;
                    echo "stepId : ".$stepId;
                    echo "wfid : ".$wfid;
                    echo "ticket id : ".$ticket;

                    die;*/


                        $taskRemark = mysqli_real_escape_string($con, $rwTask['task_remarks']);

                        //$tskAsinTOUsrId = $rwWork['assign_user'];

                        $getTskName = mysqli_query($con, "select * from tbl_task_master where task_id = '$ctaskID' ") or die('Error gettsakname' . mysqli_error($con));
                        $rwgetTskName = mysqli_fetch_assoc($getTskName);

                        $userid = $user_id;
                      
                        $mailSent = completeTask($ticket, $id, $wfid, $con, $projectName,$userid);

                        if ($mailSent) {

                         $temp = array();
                         $temp['error']  = 'false';
                         $temp['message'] = "Task Completed successfully !";
                         echo json_encode($temp);


 
//getting the userid of the person who initiated the Worklflow

      $getAssignBy = mysqli_query($con, "select * from tbl_doc_assigned_wf where task_id='$ctaskID' and ticket_id ='$tktId' ") or die('Error:' . mysqli_error($con));
    $rwgetAssBy = mysqli_fetch_assoc($getAssignBy);
    $assignBy = $rwgetAssBy['assign_by'];
    $ticketid = $rwgetAssBy['ticket_id'];

     $getTokenid = mysqli_query($con, "select * from tbl_user_master where user_id = $assignBy") or die('Error:' . mysqli_error($con));
     $rwgetToken= mysqli_fetch_assoc($getTokenid);
     $tokenid = $rwgetToken['fb_tokenid'];    

    sendPushNotification($tokenid,$assignBy,$ticketid,$taskname,$username,'Task Track', $ltaskName. ' has been '. $app); 

   
    /* //getting the next task id of task 
    $getNextTask = mysqli_query($con, "select * from tbl_task_master where task_order='$ctaskOrder'") or die('Error:' . mysqli_error($con));
    $rwgetNextTask = mysqli_fetch_assoc($getNextTask);
    $nxtuserid = $rwgetNextTask['assign_user'];
    $taskname = $rwgetNextTask['task_name'];

     $getTokenid = mysqli_query($con, "select * from tbl_user_master where user_id = $nxtuserid") or die('Error:' . mysqli_error($con));
     $rwgetToken= mysqli_fetch_assoc($getTokenid);
     $tokenid = $rwgetToken['fb_tokenid'];

     // getting id of the taskid of the task 
     $getTaskId = mysqli_query($con, "SELECT * FROM tbl_doc_assigned_wf where task_id = '$tskId' and NextTask ='0' order by start_date desc ") or die('Error:' . mysqli_error($con));
     $rwgetTaskId= mysqli_fetch_assoc($getTaskId);
     $id = $rwgetTaskId['id'];

    sendPushNotification($tokenid,$nxtuserid,$id,$taskname,'In Tray','New Task '."$taskname". ' has been assigned to you'); 
*/



                           // echo '<script>taskSuccess("myTask","Task Completed successfully !");</script>';


                        } else {


                         $temp = array();
                         $temp['error']  = 'true';
                         $temp['message'] = "Task Completion Failed !";
                         echo json_encode($temp);


                          //  echo '<script>taskFailed("myTask","Task Completion Failed !");</script>';


                        }

                        //taskAssignToUser($con, $stepId, $ctaskOrder, $docID, $ctaskID, $assignBy, $id, $wfid, $ticket, $taskRemark, $date);
                    } else {

                         $temp = array();
                         $temp['error']  = 'true';
                         $temp['message'] = "Next Task Deletion Failed !";
                         echo json_encode($temp);


                        //echo '<script>taskFailed("myTask","Next Task Deletion Failed !");</script>';


                    }
                }
            }
          
}

else{

   $temp = array();
   $temp['message'] = "Error";
   $temp['error'] = 'true';
   echo json_encode($temp);


}

//with uploading 



//functions used


     function taskAssignToUser($con, $stepId, $ctaskOrder, $docID, $ctaskID, $assignBy, $id, $wfid, $ticket, $taskRemark, $date) {

            /*  echo "step id :".$stepId."\n";
              echo "ctaskOrder :".$ctaskOrder."\n"; 
              echo "docID :".$docID."\n";
              echo "ctaskID :".$ctaskID."\n"; 
              echo "assignBy :".$assignBy."\n";
              echo  "id :".$id."\n";
              echo  "wfid :".$wfid."\n"; 
              echo "ticket :".$ticket."\n"; 
              echo "taskremark :".$taskRemark."\n"; 
              echo "date :".$date."\n";

              die;*/

            $nextTaskIds = array();
            //require_once './application/pages/sendSms.php';
      
            //echo "stepId :";
            // echo $stepId;
             
            /* echo "select * from tbl_task_master where step_id='$stepId' ORDER BY task_order";
             die;*/

            $checkTaskNext = mysqli_query($con, "select * from tbl_task_master where step_id='$stepId' ORDER BY task_order");
            $k = 0;
            while ($rwCheckTask = mysqli_fetch_assoc($checkTaskNext)) {
                
                 
                if ($rwCheckTask['task_order'] > $ctaskOrder) {
                    array_push($nextTaskIds, $rwCheckTask['task_id']);
                    $k++;
                }
                if ($k > 1) {
                    break;
                }
            }


          /*  print_r($nextTaskIds);
             
             die;  */
            if (!empty($nextTaskIds)) {

                $i = 0;
                foreach ($nextTaskIds as $nextTaskId) {
                    //echo "next task id: ";
                    //echo $nextTaskId;
                    $nxtTaskDetail = mysqli_query($con, "select * from tbl_task_master where task_id='$nextTaskId'");

                    if (mysqli_num_rows($nxtTaskDetail) > 0) {
                        $rwNxtTaskDeatil = mysqli_fetch_assoc($nxtTaskDetail);

                        if ($rwNxtTaskDeatil['deadline_type'] == 'Days') {
                            $endDate = date('Y-m-d H:i:s', (strtotime($date) + ($rwNxtTaskDeatil['deadline'] * 24 * 60 * 60)));
                        } else if ($rwNxtTaskDeatil['deadline_type'] == 'Date') {
                            $endDate = date('Y-m-d H:i:s', (strtotime($date) + ($rwNxtTaskDeatil['deadline'] * 60)));
                        } else {
                            $endDate = date('Y-m-d H:i:s', (strtotime($date) + ($rwNxtTaskDeatil['deadline'] * 60 * 60)));
                        }

                        $taskCheck = mysqli_query($con, "select * from tbl_doc_assigned_wf where task_id='$nextTaskId' and doc_id='$docID' and ticket_id = '$ticket'");

                        if (mysqli_num_rows($taskCheck) < 1) {
                            //echo $nextTaskId; die();
                            if ($i == 0) {//insert to next task
                                if (!empty($docID) && $docID != 0) {
  
                                    /* echo "$i =0";  
                                    echo "insert into tbl_doc_assigned_wf(`id`, `task_id`, `doc_id`, `start_date`, `end_date`, `task_status`, `assign_by`,`NextTask`,`ticket_id`,`task_remarks`)"
                                            . "values(null,'$nextTaskId','$docID','$date','$endDate','Pending','$assignBy','0','$ticket','$taskRemark')";*/
                                     

                                    $assignToNextWf = mysqli_query($con, "insert into tbl_doc_assigned_wf(`id`, `task_id`, `doc_id`, `start_date`, `end_date`, `task_status`, `assign_by`,`NextTask`,`ticket_id`,`task_remarks`)"
                                            . "values(null,'$nextTaskId','$docID','$date','$endDate','Pending','$assignBy','0','$ticket','$taskRemark')") or die('Error to move next 1' . mysqli_error($con));
                                } else {

                                   /* echo "$i else ";
                                    echo "insert into tbl_doc_assigned_wf(`id`, `task_id`, `start_date`, `end_date`, `task_status`, `assign_by`,`NextTask`,`ticket_id`,`task_remarks`)"
                                            . "values(null,'$nextTaskId','$date','$endDate','Pending','$assignBy','0','$ticket','$taskRemark')";*/


                                    $assignToNextWf = mysqli_query($con, "insert into tbl_doc_assigned_wf(`id`, `task_id`, `start_date`, `end_date`, `task_status`, `assign_by`,`NextTask`,`ticket_id`,`task_remarks`)"
                                            . "values(null,'$nextTaskId','$date','$endDate','Pending','$assignBy','0','$ticket','$taskRemark')") or die('Error to move next 2' . mysqli_error($con));
                                }
                            } else if ($i == 1) {

                                if (!empty($docID) && $docID != 0) {

                                   /* echo "$i==1";
                                    echo "insert into tbl_doc_assigned_wf(`id`, `task_id`, `doc_id`, `start_date`, `end_date`, `task_status`, `assign_by`,`NextTask`,`ticket_id`,`task_remarks`)"
                                            . "values(null,'$nextTaskId','$docID','$date','$endDate','Pending','$assignBy','2','$ticket','$taskRemark')";*/


                                    $assignToNextWf = mysqli_query($con, "insert into tbl_doc_assigned_wf(`id`, `task_id`, `doc_id`, `start_date`, `end_date`, `task_status`, `assign_by`,`NextTask`,`ticket_id`,`task_remarks`)"
                                            . "values(null,'$nextTaskId','$docID','$date','$endDate','Pending','$assignBy','2','$ticket','$taskRemark')") or die('Error to move next3' . mysqli_error($con));
                                } else {

                                 /*  echo "$i not 1 else";
                                   echo "insert into tbl_doc_assigned_wf(`id`, `task_id`, `start_date`, `end_date`, `task_status`, `assign_by`,`NextTask`,`ticket_id`,`task_remarks`)"
                                            . "values(null,'$nextTaskId','$date','$endDate','Pending','$assignBy','2','$ticket','$taskRemark')";*/
                                     

                                    $assignToNextWf = mysqli_query($con, "insert into tbl_doc_assigned_wf(`id`, `task_id`, `start_date`, `end_date`, `task_status`, `assign_by`,`NextTask`,`ticket_id`,`task_remarks`)"
                                            . "values(null,'$nextTaskId','$date','$endDate','Pending','$assignBy','2','$ticket','$taskRemark')") or die('Error to move next4' . mysqli_error($con));
                                }
                            }
                            $idnxt = mysqli_insert_id($con);
                           /* echo "idnxt :".$idnxt;*/

                            if ($assignToNextWf) {
                                //update current task flag and completion time
                               /* echo "update current task flag and completion time";
                                echo  "update tbl_doc_assigned_wf set NextTask='1' , end_date='$endDate' where id='$id'";*/
                                $update = mysqli_query($con, "update tbl_doc_assigned_wf set NextTask='1' , end_date='$endDate' where id='$id'") or die('Error to update old' . mysqli_error($con));

                                $userid = $_POST['userid'];
                                $projectName = "EzeeProcess";

                                assignTask($ticket, $idnxt, $con, $projectName,$userid);

                                //send sms to mob task asin to user

                                $getNextTaskId = mysqli_query($con, "select * from tbl_task_master where task_id = '$nextTaskId'") or die('Error getnxttasjkid:' . mysqli_error($con));
                                $rwgetNextTaskId = mysqli_fetch_assoc($getNextTaskId);
                                $taskName = $rwgetNextTaskId['task_name'];

//                                $getMobNumAsinTo = mysqli_query($con, "select * from tbl_user_master where user_id = '$rwgetNextTaskId[assign_user]'") or die('Error:' . mysqli_error($con));
//                                $rwgetMobNumAsinTo = mysqli_fetch_assoc($getMobNumAsinTo);
//                                $AsinToMob = $rwgetMobNumAsinTo['phone_no'];
//                                $msgAsinTo = 'New Task With Ticket Id ' . $ticket . ' and Task Name ' . $taskName . ' has been assingned to you.';
//                                $sendMsgToMbl = smsgatewaycenter_com_Send($AsinToMob, $msgAsinTo, $debug = false);
                                // 
                            }
                        } else {
                            if ($i == 0) {

                                $assignToNextWf = mysqli_query($con, "update tbl_doc_assigned_wf set NextTask='0' , task_status='Pending' where task_id='$nextTaskId' and doc_id='$docID' and ticket_id = '$ticket'") or die('Error to move next update' . mysqli_error($con));
                            } else {
                                $assignToNextWf = mysqli_query($con, "update tbl_doc_assigned_wf set NextTask='2' , task_status='Pending' where task_id='$nextTaskId' and doc_id='$docID' and ticket_id = '$ticket'") or die('Error to move next update' . mysqli_error($con));
                            }
                            $rwtaskCheck = mysqli_fetch_assoc($taskCheck);
                            $idnxt = $rwtaskCheck['id'];

                            if ($assignToNextWf) {

                                $update = mysqli_query($con, "update tbl_doc_assigned_wf set NextTask='1' , end_date='$endDate' where id='$id'");

                                $projectName = "EzeeProcess";
                                $userid = $_POST['userid'];

                                assignTask($ticket, $idnxt, $con, $projectName,$userid);

                                //send sms to mob task asin to user

                                $getNextTaskId = mysqli_query($con, "select * from tbl_task_master where task_id = '$nextTaskId'") or die('Error:' . mysqli_error($con));
                                $rwgetNextTaskId = mysqli_fetch_assoc($getNextTaskId);
                                $taskName = $rwgetNextTaskId['task_name'];


//                                $getMobNumAsinTo = mysqli_query($con, "select * from tbl_user_master where user_id = '$rwgetNextTaskId[assign_user]'") or die('Error:' . mysqli_error($con));
//                                $rwgetMobNumAsinTo = mysqli_fetch_assoc($getMobNumAsinTo);
//                                $AsinToMob = $rwgetMobNumAsinTo['phone_no'];
//                                $msgAsinTo = 'New Task With Ticket Id ' . $ticket . ' and Task Name ' . $taskName . ' has been assingned to you.';
//                                $sendMsgToMbl = smsgatewaycenter_com_Send($AsinToMob, $msgAsinTo, $debug = false);
                                // 
                            }
                        }
                    }
                    $i++;
                    //echo 'kkk'.$nextTaskId.$docID; die();
                }
            } else {

                $nextStepIds = array();
                $stepo = mysqli_query($con, "select * from tbl_step_master where step_id='$stepId'");
                $rwStepo = mysqli_fetch_assoc($stepo);
                $step = mysqli_query($con, "select * from tbl_step_master where workflow_id='$wfid'");
                $s = 0;
                while ($rwStep = mysqli_fetch_assoc($step)) {
                    //echo $rwStep['step_id'].'/'.$rwStep['step_order'].'<br>';
                    //echo $rwStepo['step_id'].'/'.$rwStepo['step_order'];
                    if ($rwStep['step_order'] > $rwStepo['step_order']) {
                        array_push($nextStepIds, $rwStep['step_id']);
                        $s++;
                    }
                    if ($s > 1) {
                        break;
                    }
                }

                //print_r($nextStepIds);

                if (!empty($nextStepIds)) {

                    $i = 0;
                    foreach ($nextStepIds as $nextStepId) {
                        $taskn = mysqli_query($con, "select * from tbl_task_master where step_id='$nextStepId' order by task_order asc limit 2");

                        if (mysqli_num_rows($taskn) > 0) {

                            while ($rwTaskn = mysqli_fetch_assoc($taskn)) {

                                echo $nextTaskId = $rwTaskn['task_id'];

                                if ($rwTaskn['deadline_type'] == 'Days') {
                                    $endDate = date('Y-m-d H:i:s', (strtotime($date) + ($rwTaskn['deadline'] * 24 * 60 * 60)));
                                } else if ($rwTaskn['deadline_type'] == 'Date') {
                                    $endDate = date('Y-m-d H:i:s', (strtotime($date) + ($rwTaskn['deadline'] * 60)));
                                } else {
                                    $endDate = date('Y-m-d H:i:s', (strtotime($date) + ($rwTaskn['deadline'] * 60 * 60)));
                                }

                                $taskCheck = mysqli_query($con, "select * from tbl_doc_assigned_wf where task_id='$nextTaskId' and doc_id='$docID' and ticket_id = '$ticket'") or die('Error:' . mysqli_error($con));
                                //echo 'helo';  
                                // mysqli_num_rows($taskCheck); 

                                if (mysqli_num_rows($taskCheck) < 1) {
                                    echo 'ok ' . $i . ' ' . $docID;
                                    if ($i == 0) {
                                        if (!empty($docID) && $docID != 0) { //echo $endDate;
                                            $assignToNextWf = mysqli_query($con, "insert into tbl_doc_assigned_wf(`id`, `task_id`, `doc_id`, `start_date`, `end_date`, `task_status`, `assign_by`,`NextTask`,`ticket_id`,`task_remarks`)"
                                                    . "values(null,'$nextTaskId','$docID','$date','$endDate','Pending','$assignBy','0','$ticket','$taskRemark')") or die('Error to move next5' . mysqli_error($con));
                                        } else {
                                            $assignToNextWf = mysqli_query($con, "insert into tbl_doc_assigned_wf(`id`, `task_id`, `start_date`, `end_date`, `task_status`, `assign_by`,`NextTask`,`ticket_id`,`task_remarks`)"
                                                    . "values(null,'$nextTaskId','$date','$endDate','Pending','$assignBy','0','$ticket','$taskRemark')") or die('Error to move next6' . mysqli_error($con));
                                        }
                                    } else if ($i == 1) {
                                        if (!empty($docID) && $docID != 0) {
                                            $assignToNextWf = mysqli_query($con, "insert into tbl_doc_assigned_wf(`id`, `task_id`, `doc_id`, `start_date`, `end_date`, `task_status`, `assign_by`,`NextTask`,`ticket_id`,`task_remarks`)"
                                                    . "values(null,'$nextTaskId','$docID','$date','$endDate','Pending','$assignBy','2','$ticket','$taskRemark')") or die('Error to move next7' . mysqli_error($con));
                                        } else {
                                            $assignToNextWf = mysqli_query($con, "insert into tbl_doc_assigned_wf(`id`, `task_id`, `start_date`, `end_date`, `task_status`, `assign_by`,`NextTask`,`ticket_id`,`task_remarks`)"
                                                    . "values(null,'$nextTaskId','$date','$endDate','Pending','$assignBy','0','$ticket','$taskRemark')") or die('Error to move next 8' . mysqli_error($con));
                                        }
                                    }
                                    $idnxt = mysqli_insert_id($con);
                                    if ($assignToNextWf) {
                                        $update = mysqli_query($con, "update tbl_doc_assigned_wf set NextTask='1' , end_date='$endDate' where id='$id'");
                                        
                                  $projectName = "EzeeProcess";
                                  $userid = $_POST['userid'];

                                        assignTask($ticket, $idnxt, $con, $projectName,$userid);
                                        //send sms to mob task asin to user

                                        $getNextTaskId = mysqli_query($con, "select * from tbl_task_master where task_id = '$nextTaskId'") or die('Error:' . mysqli_error($con));
                                        $rwgetNextTaskId = mysqli_fetch_assoc($getNextTaskId);
                                        $taskName = $rwgetNextTaskId['task_name'];

//                                        $getMobNumAsinTo = mysqli_query($con, "select * from tbl_user_master where user_id = '$rwgetNextTaskId[assign_user]'") or die('Error:' . mysqli_error($con));
//                                        $rwgetMobNumAsinTo = mysqli_fetch_assoc($getMobNumAsinTo);
//                                        $AsinToMob = $rwgetMobNumAsinTo['phone_no'];
//                                        $msgAsinTo = 'New Task With Ticket Id ' . $ticket . ' and Task Name ' . $taskName . ' has been assingned to you.';
//                                        $sendMsgToMbl = smsgatewaycenter_com_Send($AsinToMob, $msgAsinTo, $debug = false);
                                        // 
                                    }
                                } else {
                                    if ($i == 0) {
                                        $assignToNextWf = mysqli_query($con, "update tbl_doc_assigned_wf set NextTask='0' where task_id='$nextTaskId' and doc_id='$docID' and ticket_id = '$ticket'");
                                    } else {
                                        $assignToNextWf = mysqli_query($con, "update tbl_doc_assigned_wf set NextTask='2' where task_id='$nextTaskId' and doc_id='$docID' and ticket_id = '$ticket'");
                                    }
                                    $rwtaskCheck = mysqli_fetch_assoc($taskCheck);
                                    $idnxt = $rwtaskCheck['id'];
                                    if ($assignToNextWf) {
                                        $update = mysqli_query($con, "update tbl_doc_assigned_wf set NextTask='1' , end_date='$endDate' where id='$id'");
                                        $projectName = "EzeeProcess";
                                        $userid = $_POST['userid'];


                                        assignTask($ticket, $idnxt, $con, $projectName,$userid);

                                        //send sms to mob task asin to user
//                                        $getNextTaskId = mysqli_query($con, "select * from tbl_task_master where task_id = '$nextTaskId'") or die('Error:' . mysqli_error($con));
//                                        $rwgetNextTaskId = mysqli_fetch_assoc($getNextTaskId);
//                                        $taskName = $rwgetNextTaskId['task_name'];
//
//                                        $getMobNumAsinTo = mysqli_query($con, "select * from tbl_user_master where user_id = '$rwgetNextTaskId[assign_user]'") or die('Error:' . mysqli_error($con));
//                                        $rwgetMobNumAsinTo = mysqli_fetch_assoc($getMobNumAsinTo);
//                                        $AsinToMob = $rwgetMobNumAsinTo['phone_no'];
//
//                                        $msgAsinTo = 'New Task With Ticket Id ' . $ticket . ' and Task Name ' . $taskName . ' has been assingned to you.';
//                                        $sendMsgToMbl = smsgatewaycenter_com_Send($AsinToMob, $msgAsinTo, $debug = false);
                                        // 
                                    }
                                }
                            }
                        }
                        if (mysqli_num_rows($taskn) > 1) {
                            break;
                        }
                        $i++;
                    }
                } 

                else {
                   
                   
                    $assignToNextWf = mysqli_query($con, "update tbl_doc_assigned_wf set NextTask='1' where id='$id'");
                    if ($assignToNextWf) {
                        'doc id' . $docID;
                        if (!empty($docID)) {
                            $updateDocMaster = mysqli_query($con, "update tbl_document_master set doc_name=replace(doc_name,'_$wfid','') where doc_id='$docID'");
                            $update = mysqli_query($con, "update tbl_document_master set doc_name=replace(doc_name,'_$wfid','') where substring_index(doc_name,'_',-2)=$docID");
                            //view version in storage after workflow complete
                        }

                        $projectName = "EzeeProcess";
                        $userid = $_POST['userid'];

                        completeTask($ticket, $id, $wfid, $con, $projectName,$userid);


                        //send sms to mob
//                        $getMobNum = mysqli_query($con, "select * from tbl_user_master where user_id = '$assignBy'") or die('Error:' . mysqli_error($con));
//                        $rwgetMobNum = mysqli_fetch_assoc($getMobNum);
//                        $submtByMob = $rwgetMobNum['phone_no'];
//                        $msg = 'Your Ticket Id ' . $ticket . ' is Approved Successfully.';
//                        $sendMsgToMbl = smsgatewaycenter_com_Send($submtByMob, $msg, $debug = false);
                        //

                        return TRUE;
                    }
                }
            }
        }

  //back to prev task when reject
        function backToPrevTsk($con, $stepId, $ctaskOrder, $docID, $ctaskID, $assignBy, $id, $wfid, $ticket, $taskRemark, $date, $projectName) {
          
          // $projectName = "EzeeProcess";
         //  global $projectName 

            $nextTaskIds = array();
          
            $checkTaskNext = mysqli_query($con, "select * from tbl_task_master where step_id='$stepId' order by task_order desc");
            $k = 0;
            while ($rwCheckTask = mysqli_fetch_assoc($checkTaskNext)) {
                if ($rwCheckTask['task_order'] < $ctaskOrder) {
                    array_push($nextTaskIds, $rwCheckTask['task_id']);
                    $k++;
                }
                if ($k > 0) {
                    break;
                }
            }

            if (!empty($nextTaskIds)) {
                foreach ($nextTaskIds as $nextTaskId) {

                   // echo '1->' . $nextTaskId;

                    $setflg = mysqli_query($con, "update tbl_doc_assigned_wf set NextTask='2' where id = '$id'") or die('Error setflag :' . mysqli_error($con));
                    $userID = (int)$_POST['userid'];
                    $updateTaskPrev = mysqli_query($con, "UPDATE tbl_doc_assigned_wf SET task_status = 'Approved', action_by = '$userID ', action_time = '$date', NextTask = '0' where task_id='$nextTaskId' and ticket_id = '$ticket' ") or die('Error query failed 1 ' . mysqli_error($con));

                }
            } else {
                $nextStepIds = array();
                $stepo = mysqli_query($con, "select * from tbl_step_master where step_id='$stepId'");
                $rwStepo = mysqli_fetch_assoc($stepo);
                $step = mysqli_query($con, "select * from tbl_step_master where workflow_id='$wfid' order by step_order desc");
                $s = 0;
                while ($rwStep = mysqli_fetch_assoc($step)) {
                    //echo $rwStep['step_id'].'/'.$rwStep['step_order'].'<br>';
                    //echo $rwStepo['step_id'].'/'.$rwStepo['step_order'];
                    if ($rwStep['step_order'] < $rwStepo['step_order']) {
                        array_push($nextStepIds, $rwStep['step_id']);
                        $s++;
                    }
                    if ($s > 1) {
                        break;
                    }
                }

                //print_r($nextStepIds);

                if (!empty($nextStepIds)) {


                    foreach ($nextStepIds as $nextStepId) {
                        $taskn = mysqli_query($con, "select * from tbl_task_master where step_id='$nextStepId' order by task_order desc limit 1");

                        if (mysqli_num_rows($taskn) > 0) {

                            //echo '2->' . $nextTaskId;

                            $getPrevTskId = mysqli_fetch_assoc($taskn);

                            $userID = (int)$_POST['userid'];
                            $setflg = mysqli_query($con, "update tbl_doc_assigned_wf set NextTask='2' where id = '$id'") or die('Error to move next update' . mysqli_error($con));
                            $updateTaskPrev = mysqli_query($con, "UPDATE tbl_doc_assigned_wf SET task_status = 'Approved', action_by = '$userID', action_time = '$date', NextTask = '0' where task_id='$getPrevTskId[task_id]' and ticket_id = '$ticket' ") or die('Error query failed2' . $user_id. mysqli_error($con));
                        }
                    }
                } else {
                    $setflg = mysqli_query($con, "update tbl_doc_assigned_wf set NextTask='2' where id = '$id'") or die('Error to move next update' . mysqli_error($con));
                }
            }
        }


        //find next task to update user
function nextTaskToUpdate($cTaskOrd, $TskWfId, $TskStpId, $con) {

  /*  echo "select * from tbl_task_master where step_id='$TskStpId' and task_order > $cTaskOrd limit 1";

    die;*/

    $getNextTask = mysqli_query($con, "select * from tbl_task_master where step_id='$TskStpId' and task_order > $cTaskOrd limit 1") or die('Error getnxttask:' . mysqli_error($con));

    $rwgetNextTask = mysqli_fetch_assoc($getNextTask);
    if (mysqli_num_rows($getNextTask) > 0) {
        $NextTaskId = $rwgetNextTask['task_id'];
        return $NextTaskId;
    } else {
        //echo "select * from tbl_step_master where workflow_id='$TskWfId' and step_id='$TskStpId'";
        $getStpOr = mysqli_query($con, "select * from tbl_step_master where workflow_id='$TskWfId' and step_id='$TskStpId'") or die('Error getstrpord:' . mysqli_error($con));
        $rwgetStpOr = mysqli_fetch_assoc($getStpOr);
        $nextStpOrd = $rwgetStpOr['step_order'];
        $getNexStp = mysqli_query($con, "select * from tbl_step_master where workflow_id='$TskWfId' and step_order > $nextStpOrd limit 1") or die('Error Wf:' . mysqli_error($con));
        $rwgetNexStp = mysqli_fetch_assoc($getNexStp);
        
        if (mysqli_num_rows($getNexStp) > 0) {
            $cTaskOrd=0;
            $TskStpId=$rwgetNexStp['step_id'];
           return nextTaskToUpdate($cTaskOrd, $TskWfId, $TskStpId,  $con);
           /* $nextStpId = $rwgetNexStp['step_id'];
            $getNextTask1 = mysqli_query($con, "select * from tbl_task_master where step_id = '$nextStpId' ORDER BY task_order ASC LIMIT 1") or die('Error:' . mysqli_error($con));
            $rwgetNextTask1 = mysqli_fetch_assoc($getNextTask1);
            if(mysqli_num_rows($getNextTask1)){
                $getNexTskId = $rwgetNextTask1['task_id'];
                return $getNexTskId;
            }else{
                nextTaskToUpdate($getNexTskOrd, $TskWfId, $getNexTskId, $con);
        }*/
        }
    }
}


//add new user to asign task
function addNewTskUsr($cTskId, $TskWfId, $TskStpId, $cTskOrd, $assiUsersAdd, $altrusrAdd, $supvsrAdd, $deadLineAdd, $deadlineType, $date, $con,$user_id,$username,$host) {


    //echo $cTskOrd;
    $tskOrdNxt = $cTskOrd + 1;
    $taskName = 'ExtdTask' . $tskOrdNxt;
    


    $updateTaskOrder = mysqli_query($con, "UPDATE  tbl_task_master  SET task_order = task_order + 1 WHERE step_id = '$TskStpId' and workflow_id = '$TskWfId' and task_order >= '$tskOrdNxt'") or die('Error:' . mysqli_error($con));
    //echo $tskOrdNxt;
    $insertTaskOrder = mysqli_query($con, "insert into tbl_task_master (task_name, assign_user, alternate_user, supervisor, task_order, step_id, workflow_id, task_created_date, deadline, deadline_type) values('$taskName', '$assiUsersAdd','$altrusrAdd', '$supvsrAdd', '$tskOrdNxt', '$TskStpId', '$TskWfId', '$date', '$deadLineAdd', '$deadlineType')") or die('Error' . mysqli_error($con));
    $log = mysqli_query($con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$user_id', '$username',null,null,'User Order Assign in $taskName','$date',null,'$host',null)") or die('error : ' . mysqli_error($con));
}


//find next task to asssin doc
function nextTaskAsin($nextTaskOrd, $TskWfId, $TskStpId, $docId, $date, $user_id, $con, $taskRemark, $ticket) {
   //echo "select * from tbl_task_master where task_order='$nextTaskOrd' and step_id='$TskStpId'";

     //global $date;
     
      //$endDateNexTsk = null ;
     $endDateNexTsk = date('Y-m-d H:i:s');

    $getNextTask = mysqli_query($con, "select * from tbl_task_master where task_order='$nextTaskOrd' and step_id='$TskStpId'") or die('Error:' . mysqli_error($con));
    $rwgetNextTask = mysqli_fetch_assoc($getNextTask);

    if (mysqli_num_rows($getNextTask) > 0) {

        $NextTaskId = $rwgetNextTask['task_id'];

        $getNextTaskDl = mysqli_query($con, "select * from tbl_task_master where task_id='$NextTaskId'") or die('Error:' . mysqli_error($con));
        $rwgetNextTaskDl = mysqli_fetch_assoc($getNextTaskDl);
//old code
      if ($rwgetNextTaskDl['deadline_type'] == 'Date' || $rwgetNextTaskDl['deadline_type'] == 'Hrs') {

            $endDateNexTsk = date('Y-m-d H:i:s', (strtotime($date) + $rwgetNextTaskDl['deadline'] * 60));
        }
        if ($rwgetNextTaskDl['deadline_type'] == 'Days') {

            $endDateNexTsk = date('Y-m-d H:i:s', (strtotime($date) + $rwgetNextTaskDl['deadline'] * 24 * 60 * 60));
        }
 
//                      if ($rwgetNextTaskDl['deadline_type'] == 'Date') {
//
//                    /* remove holidays */
//                    $holiqry = mysqli_query($con, "select date from  events") or die(mysqli_error($con));
//                    $holliday = array();
//                    while ($row = mysqli_fetch_assoc($holiqry)) {
//                        array_push($holliday, $row['date']);
//                    }
//                    $res = array();
//                    $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetNextTaskDl['deadline'] * 60 * 60));
//                    //echo $endDate;
//                    $period = new DatePeriod(
//                            new DateTime($date), new DateInterval('P1D'), new DateTime($endDate)
//                    );
//                    foreach ($period as $key => $value) {
//                        $result = $value->format('Y-m-d');
//                        //echo $result;
//                        array_push($res, $result);
//                    }
//                    // print_r($res);
//                    $fresult = array_intersect($res, $holliday);
//                    $finalres = count($fresult);
//                    // $finalres=$finalres+$rwgetTaskDl['deadline'] ;
//
//                    if (!empty($finalres)) {
//                        $finalres = $finalres + $rwgetNextTaskDl['deadline'] / 24;
//                    } else {
//                        $finalres = intval($rwgetNextTaskDl['deadline'] / 24);
//                    }
//
//                    $endDateNexTsk = date('Y-m-d H:i:s', (strtotime($date) + $finalres * 24 * 60 * 60));
//                }
//                if ($rwgetNextTaskDl['deadline_type'] == 'Days') {
//
//                    /* remove holidays */
//                    $holiqry = mysqli_query($con, "select date from  events") or die(mysqli_error($con));
//                    $holliday = array();
//                    while ($row = mysqli_fetch_assoc($holiqry)) {
//                        array_push($holliday, $row['date']);
//                    }
//                    $res = array();
//                    $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetNextTaskDl['deadline'] * 24 * 60 * 60));
//                    $period = new DatePeriod(
//                            new DateTime($date), new DateInterval('P1D'), new DateTime($endDate)
//                    );
//                    foreach ($period as $key => $value) {
//                        $result = $value->format('Y-m-d');
//                        array_push($res, $result);
//                    }
//                    $fresult = array_intersect($res, $holliday);
//                    $finalres = count($fresult);
//                    // $finalres=$finalres+$rwgetTaskDl['deadline'] ;
//
//                    if (!empty($finalres)) {
//                        $finalres = $finalres + $rwgetNextTaskDl['deadline'];
//                    } else {
//                        $finalres = intval($rwgetNextTaskDl['deadline']);
//                    }
//
//                    $endDateNexTsk = date('Y-m-d H:i:s', (strtotime($date) + $finalres * 24 * 60 * 60));
//                }
//                   if ($rwgetNextTaskDl['deadline_type'] == 'Hrs') {
//
//                    $endDateNexTsk = date('Y-m-d H:i:s', (strtotime($date) + $rwgetNextTaskDl['deadline'] * 60));
//                }
          
         
        if (!empty($docId) && $docId!=0){
            $insertInNextTask = mysqli_query($con, "INSERT INTO tbl_doc_assigned_wf(task_id, doc_id, start_date, end_date, task_status, assign_by, NextTask, task_remarks,ticket_id) VALUES ('$NextTaskId', '$docId', '$date', '$endDateNexTsk', 'Pending', '$user_id', 2, '$taskRemark','$ticket')") or die('Erorr: ff' . mysqli_error($con));
        } else {
            $insertInNextTask = mysqli_query($con, "INSERT INTO tbl_doc_assigned_wf(task_id, start_date, end_date, task_status, assign_by, NextTask, task_remarks,ticket_id) VALUES ('$NextTaskId', '$date', '$endDateNexTsk', 'Pending', '$user_id', 2, '$taskRemark','$ticket')") or die('Erorr: ff' . mysqli_error($con));
        }
    } else {
        $getStpOr = mysqli_query($con, "select * from tbl_step_master where workflow_id='$TskWfId' and step_id='$TskStpId'") or die('Error:' . mysqli_error($con));
        $rwgetStpOr = mysqli_fetch_assoc($getStpOr);
        $getStpOrd = $rwgetStpOr['step_order'];
        $nextStpOrd = $getStpOrd + 1;
        $getNexStp = mysqli_query($con, "select * from tbl_step_master where workflow_id='$TskWfId' and step_order='$nextStpOrd'") or die('Error:' . mysqli_error($con));
        $rwgetNexStp = mysqli_fetch_assoc($getNexStp);

        if (mysqli_num_rows($getNexStp) > 0) {


            $nextStpId = $rwgetNexStp['step_id'];
            $getNextTask1 = mysqli_query($con, "select * from tbl_task_master where step_id = '$nextStpId' ORDER BY task_order ASC LIMIT 1") or die('Error:' . mysqli_error($con));
            $rwgetNextTask1 = mysqli_fetch_assoc($getNextTask1);
            $getNexTskId = $rwgetNextTask1['task_id'];
            $getNexTskOrd = $rwgetNextTask1['task_order'];

            nextTaskAsin($getNexTskOrd, $TskWfId, $nextStpId, $docId, $date, $user_id, $con, $taskRemark, $ticket);
            // echo 'gg'; die;
            return;
        }
    }
}

?>