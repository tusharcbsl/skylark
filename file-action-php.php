<?php
// download selected files and search file
if (isset($_POST['downloadselectedfile'], $_POST['token'])) {
	
	
    $docIds = $_POST['totalfiledownload'];
    $reason = preg_replace('/[^\w$\x{0080}-\x{FFFF}@#$&!%()_ <>]+/u', "", $_POST['reason']);;
   
    $slname = mysqli_query($db_con, "SELECT sl_name FROM `tbl_storage_level` where sl_id='$slid'");
    $rwslname = mysqli_fetch_assoc($slname);
    $archive_file_name = 'Download' . '.zip';
     
    $download = mysqli_query($db_con, "select doc_path,old_doc_name,doc_extn,doc_id,doc_name from tbl_document_master where  flag_multidelete=1 and doc_id in($docIds)") or die('Error'.mysqli_error($db_con));
    $zip = new ZipArchive();
    if ($zip->open($archive_file_name, ZIPARCHIVE::CREATE) !== TRUE) {
        exit("cannot open <$archive_file_name>\n");
    }
    $zippedFilePath = array();
	
		 
    $fileManager->conntFileServer();
	
    while ($row = mysqli_fetch_assoc($download)) {
		
		if(isFolderReadable($db_con, $row['doc_name'])){
		
			mysqli_set_charset($db_con, "utf8");
			$docPath = $row['doc_path'];
			$file_path = 'extract-here/' . substr($docPath, 0, strrpos($docPath, "/") + 1);
			$files = substr($docPath, strrpos($docPath, "/") + 1);
			$file1 = $row['old_doc_name'];
			$file1 = $row['old_doc_name'] . '.' . $row['doc_extn'];

            // print_r(ROOT_FTP_FOLDER . '/' . $docPath);
            // die(ss);
		
			if (!is_dir($file_path)) {
				mkdir($file_path, 0777, TRUE) or die(print_r(error_get_last()));
			}
			if(!file_exists('extract-here/' . $docPath)){
                echo ROOT_FTP_FOLDER;
                

				if($fileManager->downloadFile('DMS/' . ROOT_FTP_FOLDER . '/' . $docPath,  'extract-here/' . $docPath)){
					decrypt_my_file('extract-here/' . $docPath);
					if ($zip->addFile($file_path . $files, $file1)) {
						$zippedFilePath[] = 'extract-here/' . $docPath;
					}
				}
			}else{
	  
				decrypt_my_file('extract-here/' . $docPath);
				if ($zip->addFile($file_path . $files, $file1)) {
					$zippedFilePath[] = 'extract-here/' . $docPath;
				}
			}
		}else{
			echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI'])  . '","' . $lang['you_are_not_allowed_to_access_one_of_these_files'] . '");</script>';
			
			exit;
		}
        
    }
	
 
    
    if ($zip->close()) {
    }
    
    //then send the headers to foce download the zip file
    header("Pragma: public");
    header("Expires: 0");
    header("Content-type: application/zip");
    header("Content-Disposition: attachment; filename=\"" . $archive_file_name . "");
    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`, `doc_id`,`action_name`, `start_date`,`system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]', '$row[doc_id]','$rwslname[sl_name] files $file1 compressed to download.','$date','$host','$reason')") or die('error : ' . mysqli_error($db_con));
    readfile($archive_file_name);
    unlink($archive_file_name);
    exit;
    mysqli_close($db_con);
	
	
	
}
?>
<?php
//edit metadata which viewer not available
require_once 'checkin-checkout-php.php';
?>
<?php
//single delete
if (isset($_POST['deleteDoc'], $_POST['token'])) {
 
    $id = preg_replace("/[^A-Za-z0-9 ]/", "", $_POST['uid']);
    $id = mysqli_real_escape_string($db_con, $id);
    $permission = preg_replace("/[^A-Za-z0-9 ]/", "", trim($_POST['deleteDoc']));
    $permission = mysqli_real_escape_string($db_con, $permission);
    $getDocPath = mysqli_query($db_con, "select * from tbl_document_master where doc_id='$id'") or die('Error:' . mysqli_error($db_con));
    $rwgetDocPath = mysqli_fetch_assoc($getDocPath);
    $filePath = $rwgetDocPath['doc_path'];
    $delfilename = $rwgetDocPath['old_doc_name'];
    $deldocId = $rwgetDocPath['doc_id'];
    $storgId = $rwgetDocPath['doc_name'];
    if ($rwcheckUser['role_id'] == 1 && $permission == "Yes") {
        $path = substr($rwgetDocPath['doc_path'], 0, strrpos($rwgetDocPath['doc_path'], '/') + 1);
        $pathtxt = 'extract-here/' . $path . 'TXT/' . $id . '.txt';

        $del = mysqli_query($db_con, "DELETE FROM tbl_document_master WHERE doc_id ='$id'") or die('Error:' . mysqli_error($db_con));
        $delDocShare = mysqli_query($db_con, "DELETE FROM tbl_document_share WHERE doc_ids ='$id'") or die('Error:' . mysqli_error($db_con));
        if ($del) {
            
            if(!file_exists('extract-here/' . $filePath)){
                
                //connect file server
                $fileManager->conntFileServer();
                $fileManager->deleteFile(ROOT_FTP_FOLDER . '/' . $filePath); // delete file from file server
                
                
            }else{
                unlink('extract-here/' . $filePath);
                if(file_exists($pathtxt)){
                    unlink($pathtxt);
                }
            }
            
             //delete thumbnail
            if(CREATE_THUMBNAIL && file_exists('thumbnail/'.base64_encode($id).'.jpg')){
                unlink('thumbnail/'.base64_encode($id).'.jpg');
             }
            
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `doc_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null, '$deldocId', 'Storage Document $delfilename Deleted','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));

            $insertId = mysqli_insert_id($db_con);
            $subdocId = $deldocId;
            $getAction = mysqli_query($db_con, "SELECT remarks FROM tbl_ezeefile_logs WHERE id='$insertId'");
            $rwgetAction = mysqli_fetch_assoc($getAction);
            $fileaction = $rwgetAction['remarks'];
            $documentName = $delfilename;
            require_once './subscribe-document-mail-data.php';
            $getAction = mysqli_query($db_con, "DELETE FROM tbl_document_subscriber WHERE subscribe_docid='$subdocId'");

            echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI'])  . '","' . $lang['Doc_Dltd_Sucesfly'] . '");</script>';
        } else {
            echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI'])  . '","' . $lang['Doc_Nt_Dltd'] . '");</script>';
        }
    } elseif ($rwcheckUser['role_id'] == 1 && $permission == "No") {
        
        $deletefilename = mysqli_query($db_con, "UPDATE tbl_document_master SET flag_multidelete=0 WHERE doc_id='$id'") or die('Error:' . mysqli_error($db_con));
        $deletefilename = mysqli_query($db_con, "UPDATE tbl_document_master SET flag_multidelete=0 WHERE substring_index(doc_name,'_',-1)='$id'") or die('Error:' . mysqli_error($db_con));
        if ($deletefilename) {
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `doc_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null, '$deldocId', 'Storage Document $delfilename Deleted','$date',null,'$host','Storage Document $delfilename Deleted')") or die('error : ' . mysqli_error($db_con));


            $insertId = mysqli_insert_id($db_con);
            $subdocId = $id;
            $getAction = mysqli_query($db_con, "SELECT remarks FROM tbl_ezeefile_logs WHERE id='$insertId'");
            $rwgetAction = mysqli_fetch_assoc($getAction);
            $fileaction = $rwgetAction['remarks'];
            $documentName = $delfilename;
            require_once './subscribe-document-mail-data.php';

            $getAction = mysqli_query($db_con, "DELETE FROM tbl_document_subscriber WHERE subscribe_docid='$subdocId'");

            echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI'])  . '","' . $lang['Doc_Dltd_Sucesfly'] . '");</script>';
        } else {
            echo '<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Doc_Nt_Dltd'] . '")</script>';
        }
    } else {
        $deletefilename = mysqli_query($db_con, "UPDATE tbl_document_master SET flag_multidelete=0 WHERE doc_id='$id'") or die('Error:' . mysqli_error($db_con));
       
        $deletefilename = mysqli_query($db_con, "UPDATE tbl_document_master SET flag_multidelete=0 WHERE substring_index(doc_name,'_',-1)='$id'") or die('Error:' . mysqli_error($db_con));
        if ($deletefilename) {
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `doc_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null, '$deldocId', 'Storage Document $delfilename Deleted','$date',null,'$host','Storage Document $delfilename Deleted')") or die('error : ' . mysqli_error($db_con));

            $insertId = mysqli_insert_id($db_con);
            $subdocId = $id;
            $getAction = mysqli_query($db_con, "SELECT remarks FROM tbl_ezeefile_logs WHERE id='$insertId'");
            $rwgetAction = mysqli_fetch_assoc($getAction);
            $fileaction = $rwgetAction['remarks'];
            $documentName = $delfilename;
            require_once './subscribe-document-mail-data.php';
            $getAction = mysqli_query($db_con, "DELETE FROM tbl_document_subscriber WHERE subscribe_docid='$subdocId'");


            echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI'])  . '","' . $lang['Doc_Dltd_Sucesfly'] . '");</script>';
        } else {
            echo '<script>taskSuccess("' . basename($_SERVER['REQUEST_URI'])  . '","' . $lang['Doc_Nt_Dltd'] . '")</script>';
        }
    }
    mysqli_close($db_con);
}
//delete version document
if (isset($_POST['deleteVersionDoc'], $_POST['token'])) { 
    $id = preg_replace("/[^A-Za-z0-9 ]/", "", $_POST['docid']);
    $id = mysqli_real_escape_string($db_con, $id);
    $getDocPath = mysqli_query($db_con, "select * from tbl_document_master where doc_id='$id'") or die('Error:' . mysqli_error($db_con));
    $rwgetDocPath = mysqli_fetch_assoc($getDocPath);
    $filePath = $rwgetDocPath['doc_path'];
    $delvrsnfile = $rwgetDocPath['old_doc_name'];
    $del = mysqli_query($db_con, "delete from tbl_document_master where doc_id='$id'") or die('Error:' . mysqli_error($db_con));
   
       
    if ($del) {
        
        if(!file_exists('extract-here/' . $filePath)){
            //connect file server
            $fileManager->conntFileServer();
            $fileManager->deleteFile(ROOT_FTP_FOLDER . '/' . $filePath);
           
       }else{
           unlink('extract-here/' . $filePath);
       }
        
        
        //delete thumbnail
         if(CREATE_THUMBNAIL && file_exists('thumbnail/'.base64_encode($id).'.jpg')){
             
            unlink('thumbnail/'.base64_encode($id).'.jpg');
         }
        
        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Storage Document $delvrsnfile Deleted','$date',null,'$host','Storage Document $delvrsnfile Deleted')") or die('error : ' . mysqli_error($db_con));


        $insertId = mysqli_insert_id($db_con);
        $subdocId = $id;
//        $getAction = mysqli_query($db_con, "SELECT remarks FROM tbl_ezeefile_logs WHERE id='$insertId'");
//        $rwgetAction = mysqli_fetch_assoc($getAction);
//        $fileaction = $rwgetAction['remarks'];
        $documentName = $delvrsnfile;
        $fileaction = "Version document $documentName deleted from storage.";
        $userId = array();
        $checksubs = mysqli_query($db_con, "SELECT * FROM tbl_document_subscriber WHERE subscribe_docid='$subdocId' and find_in_set('1',action_id)");
        while ($rwcheckSubs = mysqli_fetch_assoc($checksubs)) {
            $userId[] = $rwcheckSubs['subscriber_userid'];
        }
        $userIds = implode(',', $userId);
        $mailto = array();
        $k = 1;
        $touser = mysqli_query($db_con, "SELECT user_email_id,first_name FROM tbl_user_master WHERE user_id in($userIds)");
        while ($rwtouser = mysqli_fetch_assoc($touser)) {
            $mailto[$k]['user_email_id'] = $rwtouser['user_email_id'];
            $mailto[$k]['first_name'] = $rwtouser['first_name'];
            $k++;
        }
        $documentName = $dname;
        require_once './mail.php';
        foreach ($mailto as $to) {
            
            $email = $to['user_email_id'];
            $name = $to['first_name'];
            if (MAIL_BY_SOCKET) {
                $paramsArray = array(
                    'email' => $email,
                    'filenamed' => $filenamed,
                    'action' => 'filesubscribe',
                    'projectName' => $projectName,
                    'fileaction' => $fileaction,
                    'name' => $name
                );

                mailBySocket($paramsArray);

            } else {

                $mailsent = mailsenttoDocumentSubscribeUsers($email, $filenamed, $projectName, $fileaction, $name);
            }
        }

        $getAction = mysqli_query($db_con, "DELETE FROM tbl_document_subscriber WHERE subscribe_docid='$subdocId'");


        $docName = explode("_", $rwgetDocPath['doc_name']);
        $storgId = $docName[0];
        echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI'])  . '","' . $lang['file_version_delt_success'] . '");</script>';
        //echo'<script>taskSuccess("storageFiles","Document Deleted Successfully !");</script>';
    } else {
        echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI'])  . '","' . $lang['file_version_not_delt'] . '")</script>';
    }
    mysqli_close($db_con);
}

//asign doc to workflow
if (isset($_POST['assignTo'], $_POST['token'])) {
    $wfid = preg_replace("/[^A-Za-z0-9 ]/", "", $_POST['wfid']);
    $dcId = preg_replace("/[^A-Za-z0-9 ]/", "", $_POST['mTowf']);
    $wfd = mysqli_query($db_con, "select * from tbl_workflow_master where workflow_id='$wfid'");
    $rwWfd = mysqli_fetch_assoc($wfd);
    $workFlowName = $rwWfd['workflow_name'];
    $workFlowArray = explode(" ", $workFlowName);
    $ticket = '';
    for ($w = 0; $w < count($workFlowArray); $w++) {
        $name = $workFlowArray[$w];
        $ticket = $ticket . substr($name, 0, 1);
    }
    $user_id = $_SESSION['cdes_user_id'];
    $ticket = $ticket . '_' . $user_id . '_' . strtotime($date);
    $id = preg_replace("/[^A-Za-z0-9 ]/", "", base64_decode(urldecode(@$_GET['id'])));  //get docId from url
    $id = $id . '_' . $wfid;
    if (!empty($wfid)) {

        $chkrw = mysqli_query($db_con, "select * from tbl_task_master where workflow_id = '$wfid'") or die('Error:' . mysqli_error($db_con));

        if (mysqli_num_rows($chkrw) > 0) {

            $uptDocName = mysqli_query($db_con, "UPDATE tbl_document_master SET doc_name = '$id', workflow_id='$wfid' where doc_id = '$dcId'") or die('error update:' . mysqli_error($db_con));

            $getStep = mysqli_query($db_con, "select * from tbl_step_master where workflow_id = '$wfid' ORDER BY step_order ASC LIMIT 1") or die('Error:' . mysqli_error($db_con));
            $getStpId = mysqli_fetch_assoc($getStep);
            $stpId = $getStpId['step_id'];

            $getTask = mysqli_query($db_con, "select * from tbl_task_master where step_id = '$stpId' ORDER BY task_order ASC LIMIT 1") or die('Error:' . mysqli_error($db_con));
            $getTaskId = mysqli_fetch_assoc($getTask);
            $tskId = $getTaskId['task_id'];

            $getTaskDl = mysqli_query($db_con, "select * from tbl_task_master where task_id='$tskId'") or die('Error:' . mysqli_error($db_con));
            $rwgetTaskDl = mysqli_fetch_assoc($getTaskDl);

            if ($rwgetTaskDl['deadline_type'] == 'Date' || $rwgetTaskDl['deadline_type'] == 'Hrs') {

                $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 60 * 60));
            }
            if ($rwgetTaskDl['deadline_type'] == 'Days') {

                $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 24 * 60 * 60));
            }
            $insertInTask = mysqli_query($db_con, "INSERT INTO tbl_doc_assigned_wf(task_id, doc_id, start_date, end_date, task_status, assign_by, task_remarks,ticket_id) VALUES ('$tskId', '$dcId', '$date', '$endDate', 'Pending', '$user_id', '$taskRemark','$ticket')") or die('Erorr: hh' . mysqli_error($db_con));
            $idins = mysqli_insert_id($db_con);

            $getTask = mysqli_query($db_con, "select * from tbl_task_master where task_id = '$tskId'") or die('Error:' . mysqli_error($db_con));
            $rwgetTask = mysqli_fetch_assoc($getTask);
            $TskStpId = $rwgetTask['step_id'];
            $TskWfId = $rwgetTask['workflow_id'];
            $TskOrd = $rwgetTask['task_order'];
            $nextTaskOrd = $TskOrd + 1;
            nextTaskAsin($nextTaskOrd, $TskWfId, $TskStpId, $dcId, $date, $user_id, $db_con, $taskRemark, $ticket);
            if ($insertInTask) {
                require_once './mail.php';

                ///$mail = assignTask($ticket, $idins, $db_con, $projectName);
                //if ($mail) {


                    if (MAIL_BY_SOCKET) {
                    $paramsArray = array(
                        'ticket' => $ticket,
                        'idins' => $idins,
                        'action' => 'assignTask',
                        'projectName' => $projectName,
                        'assignuserid' => $_SESSION['cdes_user_id']
                    );
                     mailBySocket($paramsArray);
                } else {
                    $mail = assignTask($ticket, $idins, $db_con, $projectName);
                }
                //if ($mail) {
                    //for document alert to subscribe user
                    $subdocId = $dcId;
                    $getdocname = mysqli_query($db_con, "select old_doc_name from tbl_document_master where doc_id = '$subdocId'") or die('Error:' . mysqli_error($db_con));
                    $getdoc = mysqli_fetch_assoc($getdocname);
                    $userId = array();
                    $checksubs = mysqli_query($db_con, "SELECT * FROM tbl_document_subscriber WHERE subscribe_docid='$subdocId' and find_in_set('3',action_id)");
                    while ($rwcheckSubs = mysqli_fetch_assoc($checksubs)) {
                        $userId[] = $rwcheckSubs['subscriber_userid'];
                    }
                    $userIds = implode(',', $userId);
                    $mailto = array();
                    $k = 1;
                    $touser = mysqli_query($db_con, "SELECT user_email_id,first_name FROM tbl_user_master WHERE user_id in($userIds)");
                    while ($rwtouser = mysqli_fetch_assoc($touser)) {
                        $mailto[$k]['user_email_id'] = $rwtouser['user_email_id'];
                        $mailto[$k]['first_name'] = $rwtouser['first_name'];
                        $k++;
                    }
                    $documentName = $getdoc['old_doc_name'];
                    $fileaction = "$documentName added in $workFlowName Workflow by $_SESSION[admin_user_name] $_SESSION[admin_user_last] from storage.";

                    require_once './mail.php';

                    foreach ($mailto as $to) {
                        $email = $to['user_email_id'];
                        $name = $to['first_name'];
                        if (MAIL_BY_SOCKET) {
                            
                            $paramsArray = array(
                                'email' => $email,
                                'filenamed' => $filenamed,
                                'action' => 'filesubscribe',
                                'projectName' => $projectName,
                                'fileaction' => $fileaction,
                                'name' => $name
                            );
                            mailBySocket($paramsArray);
                        } else {

                            $mailsent = mailsenttoDocumentSubscribeUsers($email, $filenamed, $projectName, $fileaction, $name);
                        }
                    }


                    echo '<script>uploadSuccess("' .basename($_SERVER['REQUEST_URI'])  . '", "' . $lang['Sumitd_in_wf_Sucsfly'] . '");</script>';
                // } else {

                //     echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI'])  . '", "' . $lang['Ops_Ml_nt_snt'] . '")</script>';
                // }
            } else {
                echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI'])  . '", "' . $lang['Opps_Sbmsn_fld'] . '")</script>';
            }
        } else {
            echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI'])  . '", "' . $lang['Tre_is_no_tsk_in_ts_wfw'] . '")</script>';
        }
    } else {
        echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI'])  . '","' . $lang['Pls_Slt_WF'] . '");</script>';
    }
    mysqli_close($db_con);
}
// if (isset($_POST['Delmultiple'], $_POST['token'])) {
//     $filePath = array();
//     $pathtxt = array();
//     $filename = array();
//     $permission = trim($_POST['Delmultiple']);
//     $del_sl_id = explode($_POST['sl_id1']);
//     $docDelete = trim($_POST['DelFile']);
// 	$logDelDocId = explode(',', $docDelete);
//     $user_id4 = $_SESSION['cdes_user_id'];
//     $chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um where FIND_IN_SET('$user_id4', user_ids) > 0") or die('Error:' . mysqli_error($db_con));
//     $rwcheckUser = mysqli_fetch_assoc($chekUsr);
//     $getDocPath = mysqli_query($db_con, "select doc_path,old_doc_name,doc_name from tbl_document_master where doc_id in($docDelete)") or die('Error:' . mysqli_error($db_con));
//     //connect file server
//     $fileManager->conntFileServer();
//     while ($rwgetDocPath = mysqli_fetch_assoc($getDocPath)) {
		
// 		if(isFolderReadable($db_con, $rwgetDocPath['doc_name'])){
			
// 			$filePath[] = $rwgetDocPath['doc_path'];
// 			$path = substr($rwgetDocPath['doc_path'], 0, strrpos($rwgetDocPath['doc_path'], '/') + 1);
// 			//$pathtxt[] = 'extract-here/' . $path;
// 			$filename[] = $rwgetDocPath['old_doc_name'];
// 			$storgId[] = $rwgetDocPath['doc_name'];
			
// 		}else{
// 			echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI'])  . '","' . $lang['you_are_not_allowed_to_access_one_of_these_files'] . '");</script>';
			
// 			exit;
// 		}
        
//     }
//     if ($rwcheckUser['role_id'] == 1 && $permission == "Yes") {

//         $subdocId = $docDelete;
//         $filenamed = implode(',', $filename);
//         $userId = array();
//         $checksubs = mysqli_query($db_con, "SELECT * FROM tbl_document_subscriber WHERE subscribe_docid in($subdocId) and find_in_set('1',action_id)");
//         while ($rwcheckSubs = mysqli_fetch_assoc($checksubs)) {
//             $userId[] = $rwcheckSubs['subscriber_userid'];
//         }
//         $userIds = implode(',', $userId);
//         $mailto = array();
//         $k = 1;
//         $touser = mysqli_query($db_con, "SELECT user_email_id,first_name FROM tbl_user_master WHERE user_id in($userIds)");
//         while ($rwtouser = mysqli_fetch_assoc($touser)) {
//             $mailto[$k]['user_email_id'] = $rwtouser['user_email_id'];
//             $mailto[$k]['first_name'] = $rwtouser['first_name'];
//             $k++;
//         }

//         $fileaction = "$filenamed deleted from storage.";
//         require_once './mail.php';
//         foreach ($mailto as $to) {
//             $email = $to['user_email_id'];
//             $name = $to['first_name'];
//             $mailsent = mailsenttoDocumentSubscribeUsers($email, $filenamed, $projectName, $fileaction, $name);
//         }


//         $del = mysqli_query($db_con, "DELETE FROM tbl_document_master WHERE doc_id in($docDelete)") or die('Error:' . mysqli_error($db_con));
//         $delshareDoc = mysqli_query($db_con, "DELETE FROM tbl_document_share WHERE doc_ids in($docDelete)") or die('Error:' . mysqli_error($db_con));
//         $delDocs = explode(',', $docDelete);
        
        
//         //unlink thumbnail
//         if(CREATE_THUMBNAIL){
            
//             $deleted_ids = explode(',', $docDelete);
//             foreach($deleted_ids as $del_id){
//                 unlink('thumbnail/'.base64_encode($del_id).'.jpg');
//             }
//         }
//         for ($i = 0; $i < count($filePath[$i]); $i++) {
//             $pathtxt = 'extract-here/' . $path . 'TXT/' . $delDocs[$i] . '.txt';
//             $file_dir = 'extract-here/' . $path . 'TXT/';
//             $path = 'extract-here/' . $filePath[$i];
//             $ftppath = explode('/', $filePath[$i]);
//             //die('DMS/' .ROOT_FTP_FOLDER . '/' . $filePath[$i]);
            
//             if(!file_exists($path)){
//                 // delete file from file server
//                 //$fileManager->deleteFile(ROOT_FTP_FOLDER . '/' . $filePath[$i]);
//                 $fileManager->deleteFile('DMS/' . ROOT_FTP_FOLDER . '/' . $filePath[$i]);
//                 unlink('DMS/' . ROOT_FTP_FOLDER . '/' . $filePath[$i]);
//                 //unlink(ROOT_FTP_FOLDER . '/' . $filePath[$i]);
                 
//             }else{
//                 unlink($path);
//             }
//             if(file_exists($pathtxt)){
//                 unlink($pathtxt);
//             }
//             /* if (FTP_ENABLED) {
//                 $ftp = new ftp();
//                 $ftp->conn("$fileserver", "$port", "$ftpUser", "$ftpPwd");
//                 $ftp->singleFileDelete(ROOT_FTP_FOLDER . '/' . $filePath[$i]);
//                 $arr = $ftp->getLogData();
//                 if ($arr['error'] != "") {

//                     echo '<h2>Error:</h2>' . implode('<br />', $arr['error']);
//                 }
//                 unlink($path);
//                 unlink($pathtxt);
//             } else {
//                 unlink($path);
//                 unlink($pathtxt);
//             } */
//         }

//         if ($del) {
//             foreach ($filename as $key => $value) {
//                 $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`, `sl_id`,`doc_id`, `action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]', '$storgId[$key]', '$logDelDocId[$key]', 'Document recycle','$date','$host','Storage Document $filename[$key] Deleted')") or die('error : ' . mysqli_error($db_con));
//             }

//             echo'<script>taskSuccess("' .basename($_SERVER['REQUEST_URI'])  . '","' . $lang['Doc_Dltd_Sucesfly'] . '");</script>';
//         } else {
//             echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI'])  . '","' . $lang['Doc_Nt_Dltd'] . '");</script>';
//         }
//     } elseif ($rwcheckUser['role_id'] == 1 && $permission == "No") {
//         $deletefilename1 = mysqli_query($db_con, "UPDATE tbl_document_master SET flag_multidelete=0 WHERE doc_id in($docDelete)") or die('Error:' . mysqli_error($db_con));
        
//         foreach ($_POST['DelFile'] as $docId){
            
//             $deletefilename = mysqli_query($db_con, "UPDATE tbl_document_master SET flag_multidelete=0 WHERE substring_index(doc_name,'_',-1)='$docId'") or die('Error:' . mysqli_error($db_con));
//         }
        
//         if ($deletefilename1) {

//             $fileactions = array();

//             foreach ($filename as $key => $value) {

//                 $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`, `sl_id`,`doc_id`, `action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]', '$storgId[$key]', '$logDelDocId[$key]', 'Document recycle','$date','$host','Storage Document $filename[$key] Deleted')") or die('error : ' . mysqli_error($db_con));
//             }


//             //for document alert to subscribe user
//             $subdocId = $docDelete;
//             $filenamed = implode(',', $filename);
//             $userId = array();
//             $checksubs = mysqli_query($db_con, "SELECT * FROM tbl_document_subscriber WHERE subscribe_docid in($subdocId) and find_in_set('1',action_id)");
//             while ($rwcheckSubs = mysqli_fetch_assoc($checksubs)) {
//                 $userId[] = $rwcheckSubs['subscriber_userid'];
//             }
//             $userIds = implode(',', $userId);
//             $mailto = array();
//             $k = 1;
//             $touser = mysqli_query($db_con, "SELECT user_email_id,first_name FROM tbl_user_master WHERE user_id in($userIds)");
//             while ($rwtouser = mysqli_fetch_assoc($touser)) {
//                 $mailto[$k]['user_email_id'] = $rwtouser['user_email_id'];
//                 $mailto[$k]['first_name'] = $rwtouser['first_name'];
//                 $k++;
//             }

//             $fileaction = "$filenamed deleted and sent for recycle again.";
//             require_once './mail.php';
//             // print_r($mailto); die;
//             foreach ($mailto as $to) {
//                 $email = $to['user_email_id'];
//                 $name = $to['first_name'];
//                 if (MAIL_BY_SOCKET) {
//                     $paramsArray = array(
//                         'email' => $email,
//                         'filenamed' => $filenamed,
//                         'action' => 'filesubscribe',
//                         'projectName' => $projectName,
//                         'fileaction' => $fileaction,
//                         'name' => $name
//                     );
//                     mailBySocket($paramsArray);
//                 } else {
//                     $mailsent = mailsenttoDocumentSubscribeUsers($email, $filenamed, $projectName, $fileaction, $name);
//                 }
//             }

//             echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI'])  . '","' . $lang['Doc_Dltd_Sucesfly'] . '");</script>';
//         } else {
//             echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI'])  . '","' . $lang['Doc_Nt_Dltd'] . '");</script>';
//         }
//     } else {
        
//         $deletefilename1 = mysqli_query($db_con, "UPDATE tbl_document_master SET flag_multidelete=0 WHERE doc_id in($docDelete)") or die('Error:' . mysqli_error($db_con));
        
//         $docIdss=  explode(",", $docDelete);
        
//         foreach($docIdss as $docId){
//             $deletefilename = mysqli_query($db_con, "UPDATE tbl_document_master SET flag_multidelete=0 WHERE substring_index(doc_name,'_',-1)='$docId'") or die('Error:' . mysqli_error($db_con));
//         }
//         if ($deletefilename1) {
			
//             foreach ($filename as $key => $value) {

//                 $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`, `sl_id`,`doc_id`, `action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]', '$storgId[$key]', '$logDelDocId[$key]', 'Document recycle','$date','$host','Storage Document $filename[$key] Deleted')") or die('error : ' . mysqli_error($db_con));
//             }

//             //for document alert to subscribe user
//             $subdocId = $docDelete;
//             $filenamed = implode(',', $filename);
//             $userId = array();
//             $checksubs = mysqli_query($db_con, "SELECT * FROM tbl_document_subscriber WHERE subscribe_docid in($subdocId) and find_in_set('1',action_id)");
//             while ($rwcheckSubs = mysqli_fetch_assoc($checksubs)) {
//                 $userId[] = $rwcheckSubs['subscriber_userid'];
//             }
//             $userIds = implode(',', $userId);
//             $mailto = array();
//             $k = 1;
//             $touser = mysqli_query($db_con, "SELECT user_email_id,first_name FROM tbl_user_master WHERE user_id in($userIds)");
//             while ($rwtouser = mysqli_fetch_assoc($touser)) {
//                 $mailto[$k]['user_email_id'] = $rwtouser['user_email_id'];
//                 $mailto[$k]['first_name'] = $rwtouser['first_name'];
//                 $k++;
//             }

//             $fileaction = "$filenamed deleted and sent for recycle again.";
//             require_once './mail.php';
//             // print_r($mailto); die;
//             foreach ($mailto as $to) {
//                 $email = $to['user_email_id'];
//                 $name = $to['first_name'];
//                 if (MAIL_BY_SOCKET) {
//                     $paramsArray = array(
//                         'email' => $email,
//                         'filenamed' => $filenamed,
//                         'action' => 'filesubscribe',
//                         'projectName' => $projectName,
//                         'fileaction' => $fileaction,
//                         'name' => $name
//                     );
//                     mailBySocket($paramsArray);
//                 } else {
//                     $mailsent = mailsenttoDocumentSubscribeUsers($email, $filenamed, $projectName, $fileaction, $name);
//                 }
//             }
            
//             echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI'])  . '","' . $lang['Doc_Dltd_Sucesfly'] . '");</script>';
//         } else {
//             echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI'])  . '","' . $lang['Doc_Nt_Dltd'] . '");</script>';
//         }
//     }
//     mysqli_close($db_con);
// }
if (isset($_POST['Delmultiple'], $_POST['token'])) {
    $filePath = array();
    $pathtxt = array();
    $filename = array();
    $storgId = array();
    $permission = preg_replace("/[^A-Za-z]/", "", trim($_POST['Delmultiple']));
    $docDelete = preg_replace("/[^0-9,]/", "", trim($_POST['DelFile']));
    $logDelDocId = explode(',', $docDelete);
    // print_r(count($logDelDocId));
    // die;
    $user_id4 = $_SESSION['cdes_user_id'];
    $getDocPath = mysqli_query($db_con, "select doc_path,old_doc_name,doc_name from tbl_document_master where doc_id in($docDelete)") or die('Error:' . mysqli_error($db_con));
    while ($rwgetDocPath = mysqli_fetch_assoc($getDocPath)) {
        $filePath[] = $rwgetDocPath['doc_path'];
        // print_r($filePath[1]);
        // die('sss');
        $path = substr($rwgetDocPath['doc_path'], 0, strrpos($rwgetDocPath['doc_path'], '/') + 1);
        //$pathtxt[] = 'extract-here/' . $path;
        $filename[] = $rwgetDocPath['old_doc_name'];
        $storgId[] = $rwgetDocPath['doc_name'];
    }
    if ($rwgetRole['role_id'] == 1 && $permission == "Yes") {
        //for document alert to subscribe user
        $subdocId = $docDelete;
        $filenamed = implode(',', $filename);
        $userId = array();
        $checksubs = mysqli_query($db_con, "SELECT * FROM tbl_document_subscriber WHERE subscribe_docid in($subdocId) and find_in_set('1',action_id)");
        while ($rwcheckSubs = mysqli_fetch_assoc($checksubs)) {
            $userId[] = $rwcheckSubs['subscriber_userid'];
        }
        $userIds = implode(',', $userId);
        $mailto = array();
        $k = 1;
        $touser = mysqli_query($db_con, "SELECT user_email_id,first_name FROM tbl_user_master WHERE user_id in($userIds)");
        while ($rwtouser = mysqli_fetch_assoc($touser)) {
            $mailto[$k]['user_email_id'] = $rwtouser['user_email_id'];
            $mailto[$k]['first_name'] = $rwtouser['first_name'];
            $k++;
        }

        $fileaction = "$filenamed deleted from storage.";
        require_once './mail.php';
        foreach ($mailto as $to) {
            $email = $to['user_email_id'];
            $name = $to['first_name'];
            $mailsent = mailsenttoDocumentSubscribeUsers($email, $filenamed, $projectName, $fileaction, $name);
        }
        $del = mysqli_query($db_con, "DELETE FROM tbl_document_master WHERE doc_id in($docDelete)") or die('Error:' . mysqli_error($db_con));
        $delshareDoc = mysqli_query($db_con, "DELETE FROM tbl_document_share WHERE doc_ids in($docDelete)") or die('Error:' . mysqli_error($db_con));
        $getAction = mysqli_query($db_con, "DELETE FROM tbl_document_subscriber WHERE subscribe_docid in($docDelete)");
        $delDocs = explode(',', $docDelete);
        
        //for ($i = 0; $i < count($filePath[$i]); $i++) {
        for ($i = 0; $i < count($logDelDocId); $i++) {
            $pathtxt = 'extract-here/' . $path . 'TXT/' . $delDocs[$i] . '.txt';
            $file_dir = 'extract-here/' . $path . 'TXT/';
            $path = 'extract-here/' . $filePath[$i];
            $ftppath = explode('/', $filePath[$i]);
            if (FTP_ENABLED) {
                $ftp = new ftp();
                $ftp->conn("$fileserver", "$port", "$ftpUser", "$ftpPwd");
                $ftp->singleFileDelete('DMS/' . ROOT_FTP_FOLDER . '/' . $filePath[$i]);
                $arr = $ftp->getLogData();
                if ($arr['error'] != "") {

                    echo '<h2>Error:</h2>' . implode('<br />', $arr['error']);
                }
                unlink($path);
                unlink($pathtxt);
            } else {
                unlink($path);
                unlink($pathtxt);
            }
        }

        if ($del) {
            foreach ($filename as $key => $value) {
                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`, `sl_id`,`doc_id`, `action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]', '$storgId[$key]', '$logDelDocId[$key]', 'Document recycle','$date','$host','Storage Document $filename[$key] Deleted')") or die('error : ' . mysqli_error($db_con));
                //unlink thumbnails from thumbnail folder
                $thumbnailPath = 'thumbnail/' . base64_encode($logDelDocId[$key]) . '.jpg';
                unlink($thumbnailPath);
            }
            echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Doc_Dltd_Sucesfly'] . '");</script>';
        } else {
            echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Doc_Nt_Dltd'] . '");</script>';
        }
    } elseif ($rwgetRole['role_id'] == 1 && $permission == "No") {

        $deletefilename1 = mysqli_query($db_con, "UPDATE tbl_document_master SET flag_multidelete=0 WHERE doc_id in($docDelete)") or die('Error:' . mysqli_error($db_con));
        if ($deletefilename1) {
            $fileactions = array();
            foreach ($filename as $key => $value) {
                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`, `sl_id`,`doc_id`, `action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]', '$storgId[$key]', '$logDelDocId[$key]', 'Document recycle','$date','$host','Storage Document $filename[$key] Deleted')") or die('error : ' . mysqli_error($db_con));
            }
            //for document alert to subscribe user
            $subdocId = $docDelete;
            $filenamed = implode(',', $filename);
            $userId = array();
            $checksubs = mysqli_query($db_con, "SELECT * FROM tbl_document_subscriber WHERE subscribe_docid in($subdocId) and find_in_set('1',action_id)");
            while ($rwcheckSubs = mysqli_fetch_assoc($checksubs)) {
                $userId[] = $rwcheckSubs['subscriber_userid'];
            }
            $userIds = implode(',', $userId);
            $mailto = array();
            $k = 1;
            $touser = mysqli_query($db_con, "SELECT user_email_id,first_name FROM tbl_user_master WHERE user_id in($userIds)");
            while ($rwtouser = mysqli_fetch_assoc($touser)) {
                $mailto[$k]['user_email_id'] = $rwtouser['user_email_id'];
                $mailto[$k]['first_name'] = $rwtouser['first_name'];
                $k++;
            }

            $fileaction = "$filenamed deleted and sent for recycle again.";
            require_once './mail.php';
            // print_r($mailto); die;
            foreach ($mailto as $to) {
                $email = $to['user_email_id'];
                $name = $to['first_name'];
                if (SOCKET_ENABLED) {
                    $paramsArray = array(
                        'email' => $email,
                        'filenamed' => $filenamed,
                        'action' => 'filesubscribe',
                        'projectName' => $projectName,
                        'fileaction' => $fileaction,
                        'name' => $name
                    );
                    mailBySocket($paramsArray);
                } else {
                    $mailsent = mailsenttoDocumentSubscribeUsers($email, $filenamed, $projectName, $fileaction, $name);
                }
            }
            echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Doc_Dltd_Sucesfly'] . '");</script>';
        } else {
            echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Doc_Nt_Dltd'] . '");</script>';
        }
    } else {
        $deletefilename1 = mysqli_query($db_con, "UPDATE tbl_document_master SET flag_multidelete=0 WHERE doc_id in($docDelete)") or die('Error:' . mysqli_error($db_con));
        if ($deletefilename1) {
            foreach ($filename as $key => $value) {

                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`, `sl_id`,`doc_id`, `action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]', '$storgId[$key]', '$logDelDocId[$key]', 'Document recycle','$date','$host','Storage Document $filename[$key] Deleted')") or die('error : ' . mysqli_error($db_con));
            }
            //for document alert to subscribe user
            $subdocId = $docDelete;
            $filenamed = implode(',', $filename);
            $userId = array();
            $checksubs = mysqli_query($db_con, "SELECT * FROM tbl_document_subscriber WHERE subscribe_docid in($subdocId) and find_in_set('1',action_id)");
            while ($rwcheckSubs = mysqli_fetch_assoc($checksubs)) {
                $userId[] = $rwcheckSubs['subscriber_userid'];
            }
            $userIds = implode(',', $userId);
            $mailto = array();
            $k = 1;
            $touser = mysqli_query($db_con, "SELECT user_email_id,first_name FROM tbl_user_master WHERE user_id in($userIds)");
            while ($rwtouser = mysqli_fetch_assoc($touser)) {
                $mailto[$k]['user_email_id'] = $rwtouser['user_email_id'];
                $mailto[$k]['first_name'] = $rwtouser['first_name'];
                $k++;
            }

            $fileaction = "$filenamed deleted and sent for recycle again.";
            require_once './mail.php';
            // print_r($mailto); die;
            foreach ($mailto as $to) {
                $email = $to['user_email_id'];
                $name = $to['first_name'];
                if (SOCKET_ENABLED) {
                    $paramsArray = array(
                        'email' => $email,
                        'filenamed' => $filenamed,
                        'action' => 'filesubscribe',
                        'projectName' => $projectName,
                        'fileaction' => $fileaction,
                        'name' => $name
                    );
                    mailBySocket($paramsArray);
                } else {
                    $mailsent = mailsenttoDocumentSubscribeUsers($email, $filenamed, $projectName, $fileaction, $name);
                }
            }
            echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Doc_Dltd_Sucesfly'] . '");</script>';
        } else {
            echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Doc_Nt_Dltd'] . '");</script>';
        }
    }
    mysqli_close($db_con);
}

?>
<?php

//for move multi files
// if (isset($_POST['movemulti'], $_POST['token'])) {
//     $to = $_POST['lastMoveId'];
//     $mutiId = $_POST['doc_id_smove_multi'];
//     //$doc_id_smove_multi = explode(',', $mutiId);
//     $moveToParentId = $_POST['moveToParentId'];
//     $fromSlid = $_POST['sl_id_move_multi'];
//     $checkDupDoc = mysqli_query($db_con, "select old_doc_name, doc_id, doc_name from tbl_document_master where doc_id in($mutiId) and doc_name='$fromSlid'") or die('Error' . mysqli_error($db_con));
//     $successFlag = array();
//     $documentmove = array();
    
//     // connect file server
//     $fileManager->conntFileServer();
    
//     while ($rwcheckDupDoc = mysqli_fetch_assoc($checkDupDoc)) {
//         $docdupname = $rwcheckDupDoc['old_doc_name'];
//         $documentmove[] = $rwcheckDupDoc['old_doc_name'];
//         $doc_id = $rwcheckDupDoc['doc_id'];
//        // echo  "select old_doc_name from tbl_document_master where doc_name='$to' and old_doc_name='$docdupname' and flag_multidelete='1'"; die;
//         $duplicate = mysqli_query($db_con, "select old_doc_name from tbl_document_master where doc_name='$to' and old_doc_name='$docdupname' and flag_multidelete='1'") or die('Errorasds' . mysqli_error($db_con));
//         if (mysqli_num_rows($duplicate) < 1) {
//            $from_moveDocNm = mysqli_query($db_con, "select old_doc_name,doc_path from tbl_document_master where doc_id='$doc_id'") or die('Error' . mysqli_error($db_con));
//             $from_rwMoveNm = mysqli_fetch_assoc($from_moveDocNm);
//             $fromDocPath = "extract-here/" . $from_rwMoveNm['doc_path'];
// 			$fromdir = "extract-here/" .substr($from_rwMoveNm['doc_path'], 0, strrpos($from_rwMoveNm['doc_path'], "/"));
//             $updateMoveDoc = mysqli_query($db_con, "update tbl_document_master set doc_name = '$to' where doc_id='$doc_id'") or die('Error' . mysqli_error($db_con));
//             $movestrgeNm = mysqli_query($db_con, "select sl_name, sl_parent_id, sl_depth_level from tbl_storage_level where sl_id ='$to'") or die('Error' . mysqli_error($db_con));
//             $rwmovestrgeNm = mysqli_fetch_assoc($movestrgeNm);
			
			
//             $doc_EncryptFile = explode('/', $fromDocPath);
//             $doc_Encrypt_nm = end($doc_EncryptFile);
//             $dir_to = "extract-here/" . $rwmovestrgeNm['sl_name'];
            
//             $dir = "extract-here/" . $rwmovestrgeNm['sl_name'];
//             $doc_Path_copy_to = $dir . "/" . $doc_Encrypt_nm;
//             $pathArray = explode('/', $doc_Path_copy_to);
//             array_shift($pathArray);
//             $db_copy_Path_to = implode('/', $pathArray);
            
//             $slname = str_replace(" ", "", $rwmovestrgeNm['sl_name']);
//             $updir = getStoragePath($db_con, $rwmovestrgeNm['sl_parent_id'], $rwmovestrgeNm['sl_depth_level']);

//             if(!empty($updir)){
//                 $updir = $updir . '/';
//             }else{
//                 $updir = '';
//             }
			
//             $destinationPath = $updir . $slname . '/' . $doc_Encrypt_nm;
//             if(!file_exists($fromDocPath)){
                
//                 if ($sourcePath = $fileManager->getFile($from_rwMoveNm)) {

//                     $uploadfile = $fileManager->uploadFile($sourcePath, ROOT_FTP_FOLDER . '/' . $destinationPath);
                    
//                     if ($uploadfile){
						
// 						// move text file 
// 						$ftxtdir = $fromdir.'/TXT/'.$doc_id.'.txt';
// 						if(file_exists($ftxtdir)){
// 							$txtnewpath = 'extract-here/'. $updir . $slname.'/TXT';
// 							if (!is_dir($txtnewpath)) {
// 								 mkdir($txtnewpath, 0777, TRUE) or die(print_r(error_get_last()));
// 							}
// 							$totxtdir = $txtnewpath.'/'.$doc_id.'.txt';
// 							if(copy($ftxtdir, $totxtdir)){
// 								unlink($ftxtdir);
// 							}
// 						}
						
//                         $uploadInToFTP = true;
//                         $fileManager->deleteFile('DMS/' . ROOT_FTP_FOLDER . '/' . $from_rwMoveNm['doc_path']);
//                         unlink($sourcePath);
//                     } else {
                        
//                     }
//                 }
//             }else{
//                 $uploadfile = $fileManager->uploadFile($fromDocPath, ROOT_FTP_FOLDER . '/' . $destinationPath);
//                 if ($uploadfile) {
// 					// move text file 
// 					$ftxtdir = $fromdir.'/TXT/'.$doc_id.'.txt';
// 					if(file_exists($ftxtdir)){
// 						$txtnewpath = 'extract-here/'.$updir . $slname.'/TXT';
// 						if (!is_dir($txtnewpath)) {
// 							 mkdir($txtnewpath, 0777, TRUE) or die(print_r(error_get_last()));
// 						}
// 						$totxtdir = $txtnewpath.'/'.$doc_id.'.txt';
// 						if(copy($ftxtdir, $totxtdir)){
// 							unlink($ftxtdir);
// 						}
// 					}
					
//                     $uploadInToFTP = true;
//                     unlink($fromDocPath);
//                 } else{
                    
//                 }
//             }
            
//             $updateDocPath = mysqli_query($db_con, "update tbl_document_master set doc_path = '$destinationPath', ftp_done='1' where doc_id='$doc_id'") or die('Error' . mysqli_error($db_con));
//             if ($updateDocPath) {
//                 $successFlag[] = "success";
//             }
//         } else {
//             $message = 2;
//         }
//     }
//     if ($uploadInToFTP) {
//         $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`,`doc_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$to', '$doc_id','$rwFolder[sl_name] storage document $from_rwMoveNm[old_doc_name] moved to storage $rwmovestrgeNm[sl_name]','$date',null,'$host','')") or die('error : ' . mysqli_error($db_con));
//         if ($log) {
//             $message = 1;
//         }
//     }
//     if (count($successFlag) > 0) {

//         //for document alert to subscribe user
//         $subdocId = $mutiId;
//         $filenamed = implode(',', $documentmove);
//         $userId = array();
//         $checksubs = mysqli_query($db_con, "SELECT * FROM tbl_document_subscriber WHERE subscribe_docid in($subdocId) and find_in_set('6',action_id)");
//         while ($rwcheckSubs = mysqli_fetch_assoc($checksubs)) {
//             $userId[] = $rwcheckSubs['subscriber_userid'];
//         }
//         $userIds = implode(',', $userId);
//         $mailto = array();
//         $k = 1;
//         $touser = mysqli_query($db_con, "SELECT user_email_id,first_name FROM tbl_user_master WHERE user_id in($userIds)");
//         while ($rwtouser = mysqli_fetch_assoc($touser)) {
//             $mailto[$k]['user_email_id'] = $rwtouser['user_email_id'];
//             $mailto[$k]['first_name'] = $rwtouser['first_name'];
//             $k++;
//         }
//         $movetostrgeNm = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id ='$to'") or die('Error' . mysqli_error($db_con));
//         $rwmovetostrgeNm = mysqli_fetch_assoc($movetostrgeNm);
//         $movefromstrgeNm = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id ='$fromSlid'") or die('Error' . mysqli_error($db_con));
//         $rwmovefromstrgeNm = mysqli_fetch_assoc($movefromstrgeNm);
//         $toslname = $rwmovetostrgeNm['sl_name'];
//         $fromslname = $rwmovefromstrgeNm['sl_name'];
//         $fileaction = "$filenamed moved from $fromslname to $toslname storage.";
//         require_once './mail.php';
//         foreach ($mailto as $to) {
			
//             $email = $to['user_email_id'];
//             $name = $to['first_name'];
//             if (MAIL_BY_SOCKET) {
//                 $paramsArray = array(
//                     'email' => $email,
//                     'filenamed' => $filenamed,
//                     'action' => 'filesubscribe',
//                     'projectName' => $projectName,
//                     'fileaction' => $fileaction,
//                     'name' => $name
//                 );
//                 mailBySocket($paramsArray);
//             } else {
//                 $mailsent = mailsenttoDocumentSubscribeUsers($email, $filenamed, $projectName, $fileaction, $name);
//             }
//         }


//         echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI'])  . '","' . $lang['Fls_mvd_Scsfly'] . '");</script>';
//     } else if ($message == 2) {
//         echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI'])  . '","' . $lang['uploaded_already'] . '");</script>';
//     } else {
//         echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI'])  . '","' . $lang['Fld_to_mv_Fls'] . '");</script>';
//     }
// }
if (isset($_POST['movemulti'], $_POST['token'])) {
    $to = $_POST['lastMoveId'];
    $level = $_POST['lastMoveIdLevel'];
    $mutiId = $_POST['doc_id_smove_multi'];
    //$doc_id_smove_multi = explode(',', $mutiId);
    $moveToParentId = $_POST['moveToParentId'];
    if ($to == '') {
        $to = $moveToParentId;
    }
    $fromSlid = $_POST['sl_id_move_multi'];
    mysqli_autocommit($db_con, FALSE);
    $checkDupDoc = mysqli_query($db_con, "select old_doc_name, doc_id, doc_name from tbl_document_master where doc_id in($mutiId)") or die('Error' . mysqli_error($db_con));
    $successFlag = array();
    while ($rwcheckDupDoc = mysqli_fetch_assoc($checkDupDoc)) {
        $docdupname = $rwcheckDupDoc['old_doc_name'];
        $doc_id = $rwcheckDupDoc['doc_id'];

        $duplicate = mysqli_query($db_con, "select doc_id from tbl_document_master where doc_name='$to' and old_doc_name='$docdupname' and flag_multidelete='1'") or die('Errorasds' . mysqli_error($db_con));
        if (mysqli_num_rows($duplicate) < 1) {

            $from_moveDocNm = mysqli_query($db_con, "select * from tbl_document_master where doc_id='$doc_id'") or die('Error' . mysqli_error($db_con));
            $from_rwMoveNm = mysqli_fetch_assoc($from_moveDocNm);
            echo $fromDocPath = "extract-here/" . $from_rwMoveNm['doc_path'];
            $fromdir = "extract-here/" . substr($from_rwMoveNm['doc_path'], 0, strrpos($from_rwMoveNm['doc_path'], "/"));
            $updateMoveDoc = mysqli_query($db_con, "update tbl_document_master set doc_name = '$to' where doc_id='$doc_id'") or die('Error' . mysqli_error($db_con));

            $movestrgeNm = mysqli_query($db_con, "select * from tbl_storage_level where sl_id ='$to'") or die('Error' . mysqli_error($db_con));
            $rwmovestrgeNm = mysqli_fetch_assoc($movestrgeNm);
            $doc_EncryptFile = explode('/', $fromDocPath);
            $doc_Encrypt_nm = end($doc_EncryptFile);

            $storageName = $rwmovestrgeNm['sl_name'];
            $storageName = str_replace(" ", "", $storageName);
            $storageName = preg_replace('/[^A-Za-z0-9\-]/', '', $storageName);

            /* @dv heirarchy wise folder creation duing move file from one folder to another */
            $updir = getStoragePath($db_con, $rwmovestrgeNm['sl_parent_id'], $rwmovestrgeNm['sl_depth_level']);
            if (!empty($updir)) {
                $updir = $updir . '/';
            } else {
                $updir = '';
            }
            $uploaddir = $updir . $storageName . '/';
            $dir_to = 'extract-here/' . $uploaddir;
            /* end */
            if (!is_dir($dir_to)) {
                mkdir($dir_to);
            }
            $dir = "extract-here/" . $updir . $storageName;
           
            $doc_Path_copy_to = $dir . "/" . $doc_Encrypt_nm;
         
            copy($fromDocPath, $doc_Path_copy_to);
           
            $destinationPath = 'DMS/' . ROOT_FTP_FOLDER . '/' . $uploaddir . $doc_Encrypt_nm;
            $sourcePath = $fromDocPath;

            $uploadInToFTP = false;
            if (FTP_ENABLED) {

                $ftp = new ftp();

                $ftp->conn("$fileserver", "$port", "$ftpUser", "$ftpPwd");

                if ($ftp->get($sourcePath, 'DMS/' . ROOT_FTP_FOLDER . '/' . $from_rwMoveNm['doc_path'])) {

                    $uploadfile = $ftp->put($destinationPath, $sourcePath);
                    $arr = $ftp->getLogData();
                    if ($uploadfile) {
                        // @dv06-08-21 move text file 
                        $ftxtdir = $fromdir . '/TXT/' . $doc_id . '.txt';
                        if (file_exists($ftxtdir)) {
                            $txtnewpath = 'extract-here/' . $updir . $storageName . '/TXT';
                            if (!is_dir($txtnewpath)) {
                                mkdir($txtnewpath, 0777, TRUE) or die(print_r(error_get_last()));
                            }
                            $totxtdir = $txtnewpath . '/' . $doc_id . '.txt';
                            if (copy($ftxtdir, $totxtdir)) {
                                unlink($ftxtdir);
                            }
                        }
                        $uploadInToFTP = true;
                        $ftp->singleFileDelete( 'DMS/' . ROOT_FTP_FOLDER . '/' . $from_rwMoveNm['doc_path']);
                        unlink($fromDocPath);
                    } else {
                        $uploadInToFTP = false;
                        echo '<h2>Error:</h2>' . implode('<br />', $arr['error']);
                    }
                }
            } else {
                $uploadInToFTP = true;
                // @dv06-08-21 move text file 
                $ftxtdir = $fromdir . '/TXT/' . $doc_id . '.txt';
                if (file_exists($ftxtdir)) {
                    $txtnewpath = 'extract-here/' . $updir . $storageName . '/TXT';
                    if (!is_dir($txtnewpath)) {
                        mkdir($txtnewpath, 0777, TRUE) or die(print_r(error_get_last()));
                    }
                    $totxtdir = $txtnewpath . '/' . $doc_id . '.txt';
                    if (copy($ftxtdir, $totxtdir)) {
                        unlink($ftxtdir);
                    }
                }
                unlink($fromDocPath);
            }

            $updateDocPath = mysqli_query($db_con, "update tbl_document_master set doc_path = '$uploaddir$doc_Encrypt_nm' where doc_id='$doc_id'") or die('Error' . mysqli_error($db_con));
            if ($updateDocPath) {
                $successFlag[] = "success";
                //$message = 3;
                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`, `sl_id`,`doc_id`, `action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','$to', '$doc_id','Document Moved','$date','$host','$rwFolder[sl_name] storage document $from_rwMoveNm[old_doc_name] moved to storage $rwmovestrgeNm[sl_name]')") or die('error : ' . mysqli_error($db_con));
            }
        } else {
            $message = 2;
        }
    }

    if (count($successFlag) > 0) {
        mysqli_autocommit($db_con, TRUE);
        echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Fls_mvd_Scsfly'] . '");</script>';
    } else if ($message == 2) {
        echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['uploaded_already'] . '");</script>';
    }
}


if (isset($_POST['subscribe'], $_POST['token'])) {

    $subId = $_POST['singlesubsdocId'];
    $fileactions = $_POST['fileactions'];
    $actionIds = implode(',', $fileactions);

    $checksubs = mysqli_query($db_con, "SELECT * FROM tbl_document_subscriber WHERE subscribe_docid='$subId' and subscriber_userid='" . $_SESSION['cdes_user_id'] . "'");
    if (mysqli_num_rows($checksubs) < 1) {
        $subscribe = mysqli_query($db_con, "INSERT INTO `tbl_document_subscriber`(`subscribe_docid`, `subscriber_userid`, `action_id`) VALUES ('$subId','" . $_SESSION['cdes_user_id'] . "', '$actionIds')");
        $filename = mysqli_query($db_con, "select old_doc_name from tbl_document_master where doc_id='$subId'");
        $rwfilename = mysqli_fetch_assoc($filename);
        if ($subscribe) {
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`, `doc_id`,`action_name`, `start_date`,`system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]', '$doc_ids','Document Subscribe','$date','$host','Document Name :  $rwfilename[old_doc_name] subscribe.')") or die('error : ' . mysqli_error($db_con));
            echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Doc_subscribe_Sucesfly'] . '");</script>';
            exit();
        }
    } else {
        echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Doc_subscribe_exist'] . '");</script>';
    }
}


if (isset($_POST['subscribenow'], $_POST['token'])) {
        $docIds = preg_replace("/[^0-9,]/", "", $_POST['totalfilesubscribe']);
        $slid = preg_replace("/[^0-9]/", "", $_POST['slid']);
        $slid = mysqli_real_escape_string($db_con, $slid);
        $docIds = explode(',', $docIds);

        foreach ($docIds as $subdocId) {
			
			$docCheck = mysqli_query($db_con, "SELECT doc_name FROM tbl_document_master WHERE doc_id='$subdocId'");
			$rowd = mysqli_fetch_assoc($docCheck);
			if(isFolderReadable($db_con, $rowd['doc_name'])){
				
				$checksubs = mysqli_query($db_con, "SELECT * FROM tbl_document_subscriber WHERE subscribe_docid='$subdocId' and subscriber_userid='" . $_SESSION['cdes_user_id'] . "'");
				
				
				if (mysqli_num_rows($checksubs) < 1) {
					$fileactions = $_POST['fileactions'];
					$actionIds = implode(',', $fileactions);
					$Subscribe = mysqli_query($db_con, "INSERT INTO `tbl_document_subscriber`(`subscribe_docid`, `subscriber_userid`, `action_id`) VALUES ('$subdocId','" . $_SESSION['cdes_user_id'] . "', '$actionIds')");
					$subsFlag = 2;
				}
			}else{
				echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI'])  . '","' . $lang['you_are_not_allowed_to_access_one_of_these_files'] . '");</script>';
			
				exit;
			}
        }
        if ($subsFlag == '2') {
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`sl_id`, `doc_id`,`action_name`, `start_date`,`system_ip`, `remarks`, `reason`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','$slid', '$subdocId','Document Downloaded','$date','$host','Folder of $rwslname[sl_name] files $file1 compressed to download.', '$reason')") or die('error : ' . mysqli_error($db_con));
            echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Doc_subscribe_Sucesfly'] . '");</script>';
        } else {
            echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Doc_subscribe_exist'] . '");</script>';
        }
        mysqli_close($db_con);
}

?>
