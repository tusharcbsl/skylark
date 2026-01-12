<?php

require_once "connection.php";
require_once 'classes/function.php';

//Upload document in workflow 

    // getAllWorkloflow

    if(isset($_POST['UseridWork'])&&!empty($_POST['UseridWork'])){

    $res = array();

     $getWorkflw = mysqli_query($con, "select * from tbl_workflow_master order by workflow_name") or die('Error in getWorkflw upload:' . mysqli_error($con));
     while($rwWork = mysqli_fetch_assoc($getWorkflw)){

       $temp = array();
       $temp['workflow_id'] = $rwWork['workflow_id'];
       $temp['workflow_name'] = $rwWork['workflow_name'];

       array_push($res, $temp);
       
        
     }

     echo json_encode($res);


    }


    //get Step 

    if(isset($_POST['workflowidStep'])&&!empty($_POST['workflowidStep'])){


     $workflowid = $_POST['workflowidStep'];
   
     $getStep= mysqli_query($con, "SELECT * FROM tbl_step_master where workflow_id =$workflowid ") or die('Error in getWorkflw upload 2:' . mysqli_error($con));
    
     echo json_encode(mysqli_fetch_all($getStep,MYSQLI_ASSOC));

    }

    //get Step 

    if(isset($_POST['workflowidTask'])&&!empty($_POST['workflowidTask'])){


     $workflowid = $_POST['workflowidTask'];
   
     $getTask= mysqli_query($con, "SELECT * FROM tbl_task_master where workflow_id = $workflowid") or die('Error in getWorkflw upload 3:' . mysqli_error($con));
    
     echo json_encode(mysqli_fetch_all($getTask,MYSQLI_ASSOC));

    }


    //upload doc in workflow

//isset($_POST['metaName'] $user_id = $_SESSION['cdes_user_id'];

     if (isset($_POST['wtsk'])&&
         isset($_POST['wstp'])&&
         isset($_POST['wfid'])&&
         isset($_POST['storageId'])&&
         isset($_POST['userId'])&&
         isset($_POST['pageCount'])&&
         isset($_POST['metaName'])&&
         isset($_FILES['fileName'])

      ) {

     

        $wTaskId = $_POST['wtsk'];
        $wTaskId= preg_replace("/[^A-Za-z0-9 ]/", "", $wTaskId);
        $wTaskId = mysqli_real_escape_string($con, $wTaskId);
        $wStpId = $_POST['wstp'];
        $wStpId= preg_replace("/[^A-Za-z0-9 ]/", "", $wStpId);
        $wStpId = mysqli_real_escape_string($con, $wStpId); 
        $wfid = $_POST['wfid'];
        $wStpId= preg_replace("/[^A-Za-z0-9 ]/", "", $wfid);
        $wfid = mysqli_real_escape_string($con, $wfid);
        $id = base64_decode(urldecode($_POST['storageId']));
        $id= preg_replace("/[^A-Za-z0-9 ]/", "", $id);

          date_default_timezone_set("Asia/Kolkata");
          $date = date("Y-m-d H:i");
        

         $id = $id . '_' . $wfid;

       

        $wfd = mysqli_query($con, "select * from tbl_workflow_master where workflow_id='$wfid'");
        $rwWfd = mysqli_fetch_assoc($wfd);
        $workFlowName = $rwWfd['workflow_name'];
        $workFlowArray = explode(" ", $workFlowName);
        $ticket = '';
        for ($w = 0; $w < count($workFlowArray); $w++) {
            $name = $workFlowArray[$w];
            $ticket = $ticket . substr($name, 0, 1);
        }
        $taskRemark = mysqli_real_escape_string($con, $_POST['taskRemark']);

        $user_id = $_POST['userId'];
        $ticket = $ticket . '_' . $user_id . '_' . strtotime($date);
        //
        $errors = array();
        $file_name = $_FILES['fileName']['name'];
        $file_size = $_FILES['fileName']['size'];
        $file_type = $_FILES['fileName']['type'];
        $file_tmp = $_FILES['fileName']['tmp_name'];
        $pageCount = $_POST['pageCount'];
        $metavals = '';
        $columns = '';
        if (isset($_POST['metaName'])) {

      $metadata=json_decode($_POST['metaName'],true);

      //echo print_r($metadata);

     // die;
            
        $metavals = '';
        $columns = '';
        $m = array();

  
		  for($i= 0;$i<count($metadata['meta']);$i++){

			  $metalabel =$metadata['meta'][$i]['metaLabel'] ;
			  $metavalue = $metadata['meta'][$i]['metaEntered'];  
				
			  
			  if(!empty($metavalue)){
			  
				
				array_push($m,$metavalue);

				
				
			  } 
			   else{
				 
				  array_push($m,'null');  
			   }
		}

		
		if(count($m)>0){
			$m = ",'" . implode ( "', '", $m ) . "'";
		}else{
			$m ="";
		}
			

            $mata = "SELECT tmm.field_name FROM tbl_metadata_to_storagelevel tms INNER JOIN tbl_metadata_master tmm  ON tms.metadata_id = tmm.id where tms.sl_id='$id'";
            $meta_run = mysqli_query($con, $mata);
            $i = 1;
            while ($rwmeta = mysqli_fetch_assoc($meta_run)) {
                if (!empty($columns)) {
                    $columns = $columns . ',`' . $rwmeta['field_name'] . '`';
                } else {
                    $columns = ',`' . $rwmeta['field_name'] . '`';
                }
                //$colval.$i=$_POST[''];
                $i++;
            }
        }

        //$docs_name =  $rwslname['sl_name'];
        //$user_id = $_SESSION['cdes_user_id'];
        $name = explode(".", $file_name);
        $encryptName = urlencode(base64_encode($name[0]));
        $fileExtn = $name[1];
		$folderName = str_replace(" ", "", $workFlowName);
		$folderName = trim($workFlowName).'/';
		$path = "../extract-here/".$folderName;
		if (!is_dir($path)) {
			mkdir($path, 0777, true);
		}
        $image_path = "../extract-here/".$folderName.$file_name;
        $upload = move_uploaded_file($file_tmp, $image_path) or die(print_r(error_get_last()));

        if ($upload) {
			
			$destinationPath =$folderName.$file_name;
			$sourcePath = $image_path; 
			 if(uploadFileInFtpServer($destinationPath, $sourcePath)){
				 
				unlink($sourcePath);
			}
			else
			{	
				$temp = array();
				$temp['error'] = 'false';
				$temp['msg'] = 'File upload failed'; 
				echo json_encode($temp); 
				exit();
			}

        /*  echo "upload ok";
          echo "select * from tbl_task_master where workflow_id = '$wfid'";
          die;*/

            $chkrw = mysqli_query($con, "select * from tbl_task_master where workflow_id = '$wfid'") or die('Error: ' . mysqli_error($con));

            if (mysqli_num_rows($chkrw) > 0) {

                $query = "INSERT INTO tbl_document_master(doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages, dateposted $columns) VALUES ('$id', '$file_name', '$fileExtn', '$file_name', '$user_id', '$file_size', '$pageCount', '$date' $m)";
                $exe = mysqli_query($con, $query) or die('Error n query failed' . mysqli_error($con));
                $docId = mysqli_insert_id($con);

                if (!empty($wTaskId)) {

                  //echo "task id ok";

                    $endDate = date('Y-m-d H:i:s'); 
                    $getTaskDl = mysqli_query($con, "select * from tbl_task_master where task_id='$wTaskId'") or die('Error:' . mysqli_error($con));
                    $rwgetTaskDl = mysqli_fetch_assoc($getTaskDl);

                    if ($rwgetTaskDl['deadline_type'] == 'Date' || $rwgetTaskDl['deadline_type'] == 'Hrs') {

                        $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 60 * 60));
                    }
                    if ($rwgetTaskDl['deadline_type'] == 'Days') {

                        $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 24 * 60 * 60));
                    }

                  /*  echo "INSERT INTO tbl_doc_assigned_wf(task_id, doc_id, start_date, end_date, task_status, assign_by, ticket_id) VALUES ('$wTaskId', '$docId', '$date', '$endDate', 'Pending', '$user_id', '$ticket')";
                    die;*/

                    $insertInTask = mysqli_query($con, "INSERT INTO tbl_doc_assigned_wf(task_id, doc_id, start_date, end_date, task_status, assign_by, ticket_id) VALUES ('$wTaskId', '$docId', '$date', '$endDate', 'Pending', '$user_id', '$ticket')") or die('Erorr:' . mysqli_error($con));
                    $idins = mysqli_insert_id($con);

                    //
                    $getTask = mysqli_query($con, "select * from tbl_task_master where task_id = '$wTaskId'") or die('Error:' . mysqli_error($con));
                    $rwgetTask = mysqli_fetch_assoc($getTask);
                    $TskStpId = $rwgetTask['step_id'];
                    $TskWfId = $rwgetTask['workflow_id'];
                    $TskOrd = $rwgetTask['task_order'];
                    $nextTaskOrd = $TskOrd + 1;
                    //

                    nextTaskAsin($nextTaskOrd, $TskWfId, $TskStpId, $docId, $date, $user_id, $con, '', $ticket);

                    if ($insertInTask) {
                        require_once 'mail.php';
                        $projectName = "EzeePea";


                        $mail = assignTask($ticket, $idins, $con,$projectName,$user_id);

                        if ($mail) {

                            $getTskName = mysqli_query($con, "select * from tbl_task_master where task_id = '$wTaskId' ") or die('Error' . mysqli_error($con));
                            $rwgetTskName = mysqli_fetch_assoc($getTskName);
							
							$temp = array();
							$temp['error'] = "false";
							$temp['msg'] ="File Uploaded Successfully!!";
							echo json_encode($temp); 
 

                         // echo '<script>uploadSuccess("storage?id=' . $_GET['id'] . '", "File Uploaded Successfully!!");</script>';
                        }
                    } else {

                                $temp = array();
                                $temp['error'] = "true";
                                $temp['msg'] ="Opps! File upload failed";
                                echo json_encode($temp); 
                      

                        //echo '<script>taskFailed("storage?id=' . $_GET['id'] . '", "Opps!! File upload failed")</script>';
                    }
                }

                 else if (!empty($wStpId)) {

                   /*  echo "select * from tbl_task_master where step_id = '$wStpId' ORDER BY task_order ASC LIMIT 1";
                      die;*/
                    $getTask = mysqli_query($con, "select * from tbl_task_master where step_id = '$wStpId' ORDER BY task_order ASC LIMIT 1") or die('Error:' . mysqli_error($con));

                    if (mysqli_num_rows($getTask) > 0) {


                        $getTaskId = mysqli_fetch_assoc($getTask);


                        $tskId = $getTaskId['task_id'];

                        $getTaskDl = mysqli_query($con, "select * from tbl_task_master where task_id='$tskId'") or die('Error:' . mysqli_error($con));
                        $rwgetTaskDl = mysqli_fetch_assoc($getTaskDl);

                        if ($rwgetTaskDl['deadline_type'] == 'Date' || $rwgetTaskDl['deadline_type'] == 'Hrs') {

                            $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 60 * 60));
                        }
                        if ($rwgetTaskDl['deadline_type'] == 'Days') {

                            $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 24 * 60 * 60));
                        }



                        $insertInTask = mysqli_query($con, "INSERT INTO tbl_doc_assigned_wf(task_id, doc_id, start_date, end_date, task_status, assign_by, ticket_id) VALUES ('$tskId', '$docId', '$date', '$endDate', 'Pending', '$user_id', '$ticket')") or die('Erorr:' . mysqli_error($con));
                        $idins = mysqli_insert_id($con);

                        //
                        $getTask = mysqli_query($con, "select * from tbl_task_master where task_id = '$tskId'") or die('Error:' . mysqli_error($con));
                        $rwgetTask = mysqli_fetch_assoc($getTask);
                        $TskStpId = $rwgetTask['step_id'];
                        $TskWfId = $rwgetTask['workflow_id'];
                        $TskOrd = $rwgetTask['task_order'];
                        $nextTaskOrd = $TskOrd + 1;
                        //

                        nextTaskAsin($nextTaskOrd, $TskWfId, $TskStpId, $docId, $date, $user_id, $con, '', $ticket);

                        if ($insertInTask) {

                            require_once 'mail.php';
                             $projectName = "EzeePea";


                            $mail = assignTask($ticket, $idins, $con,$projectName,$user_id);
                            //$mail = assignTask($ticket, $idins, $con);
                            if ($mail) {


                                $getTskName = mysqli_query($con, "select * from tbl_task_master where task_id = '$tskId' ") or die('Error' . mysqli_error($con));
                                $rwgetTskName = mysqli_fetch_assoc($getTskName);

                                //send sms to mob
                                // require_once('login-function.php');
//                   $getMobNum = mysqli_query($con, "select * from tbl_user_master where user_id = '$user_id'") or die('Error:'.mysqli_error($con));
//                   $rwgetMobNum = mysqli_fetch_assoc($getMobNum);
//                   $submtByMob = $rwgetMobNum['phone_no'];
//                   $msg = 'Your Ticket Id is '.$ticket.' and Task is in Process.';
//                   $sendMsgToMbl = smsgatewaycenter_com_Send($submtByMob, $msg, $debug = false);
                                //

                                $temp = array();
                                $temp['error'] = "false";
                                $temp['msg'] ="File Uploaded Successfully!!";
                                echo json_encode($temp); 

                                //echo '<script>uploadSuccess("storage?id=' . $_GET['id'] . '", "File Uploaded Successfully!!");</script>';

                            }
                        } else {

                               $temp = array();
                                $temp['error'] = "true";
                                $temp['msg'] ="Opps!! File upload failed";
                                echo json_encode($temp); 


                            //echo '<script>taskFailed("storage?id=' . $_GET['id'] . '", "Opps!! File upload failed")</script>';
                        }


                    } else {
                                $temp = array();
                                $temp['error'] = "true";
                                $temp['msg'] ="There is no task in this step !";
                                echo json_encode($temp); 



                       // echo '<script>taskFailed("storage?id=' . $_GET['id'] . '", "There is no task in this step !")</script>';
                    
                    }

                } 

                else {

                    $getStep = mysqli_query($con, "select * from tbl_step_master where workflow_id = '$wfid' ORDER BY step_order ASC LIMIT 1") or die('Error:' . mysqli_error($con));
                    $getStpId = mysqli_fetch_assoc($getStep);
                    $stpId = $getStpId['step_id'];

                    $getTask = mysqli_query($con, "select * from tbl_task_master where step_id = '$stpId' ORDER BY task_order ASC LIMIT 1") or die('Error:' . mysqli_error($con));
                    $getTaskId = mysqli_fetch_assoc($getTask);
                    $tskId = $getTaskId['task_id'];

                    $getTaskDl = mysqli_query($con, "select * from tbl_task_master where task_id='$tskId'") or die('Error:' . mysqli_error($con));
                    $rwgetTaskDl = mysqli_fetch_assoc($getTaskDl);

                    if ($rwgetTaskDl['deadline_type'] == 'Date' || $rwgetTaskDl['deadline_type'] == 'Hrs') {

                        $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 60 * 60));
                    }
                    if ($rwgetTaskDl['deadline_type'] == 'Days') {

                        $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 24 * 60 * 60));
                    }

                    $insertInTask = mysqli_query($con, "INSERT INTO tbl_doc_assigned_wf(task_id, doc_id, start_date, end_date, task_status, assign_by, ticket_id) VALUES ('$tskId', '$docId', '$date', '$endDate', 'Pending', '$user_id', '$ticket')") or die('Erorr:' . mysqli_error($con));
                    $idins = mysqli_insert_id($con);

                    //
                    $getTask = mysqli_query($con, "select * from tbl_task_master where task_id = '$tskId'") or die('Error:' . mysqli_error($con));
                    $rwgetTask = mysqli_fetch_assoc($getTask);
                    $TskStpId = $rwgetTask['step_id'];
                    $TskWfId = $rwgetTask['workflow_id'];
                    $TskOrd = $rwgetTask['task_order'];
                    $nextTaskOrd = $TskOrd + 1;
                    //

                    $chk = nextTaskAsin($nextTaskOrd, $TskWfId, $TskStpId, $docId, $date, $user_id, $con, '', $ticket);


                    if ($insertInTask) {
                        require_once 'mail.php';
                        $mail = assignTask($ticket, $idins, $con);
                        if ($mail) {


                            $getTskName = mysqli_query($con, "select * from tbl_task_master where task_id = '$tskId' ") or die('Error' . mysqli_error($con));
                            $rwgetTskName = mysqli_fetch_assoc($getTskName);

                            //send sms to mob
                            //require_once('login-function.php');
//                   $getMobNum = mysqli_query($con, "select * from tbl_user_master where user_id = '$user_id'") or die('Error:'.mysqli_error($con));
//                   $rwgetMobNum = mysqli_fetch_assoc($getMobNum);
//                   $submtByMob = $rwgetMobNum['phone_no'];
//                   $msg = 'Your Ticket Id is '.$ticket.' and Task is in Process.';
//                   $sendMsgToMbl = smsgatewaycenter_com_Send($submtByMob, $msg, $debug = false);
                            //
                                
                                $temp = array();
                                $temp['error'] = "false";
                                $temp['msg'] ="File Uploaded Successfully!!";
                                echo json_encode($temp); 

                            //echo '<script>uploadSuccess("storage?id=' . $_GET['id'] . '", "File Uploaded Successfully!!");</script>';
                        }
                    } else {


                                $temp = array();
                                $temp['error'] = "false";
                                $temp['msg'] ="Opps!! File upload failed";
                                echo json_encode($temp); 

                       // echo '<script>taskFailed("storage?id=' . $_GET['id'] . '", "Opps!! File upload failed")</script>';
                    }
                }
            } else {

                                $temp = array();
                                $temp['error'] = "false";
                                $temp['msg'] ="There is no Task in this Workflow";
                                echo json_encode($temp); 


                //echo '<script>taskFailed("storage?id=' . $_GET['id'] . '", "There is no Task in this Workflow ")</script>';
            }
        }
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