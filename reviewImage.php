<?php
require_once 'sessionstart.php';
require_once 'application/config/database.php';
require_once 'application/pages/function.php';
require_once './classes/fileManager.php';

// ini_set('display_errors', 1); 
// ini_set('display_startup_errors', 1); 

error_reporting(0);


if (isset($_SESSION['lang'])) {
    $file = "./" . $_SESSION['lang'] . ".json";
} else {
    $file = "./English.json";
}

$jsFile = file_get_contents($file);
$lang = json_decode($jsFile, true);
$uploadedBy = $_SESSION['cdes_user_id'];

$docId = $_POST['docId'];
$data = $_POST['image'];
$revId = $_POST['reid'];
$file = mysqli_query($db_con, "select * from tbl_document_reviewer where doc_id='$docId'") or die('error d' . mysqli_error($db_con));
$rwFile = mysqli_fetch_assoc($file);
$filePath = $rwFile['doc_path'];
$fname = $rwFile['old_doc_name'];
$doc_extn = $rwFile['doc_extn'];
$filesize = $rwFile['doc_size'];
$slid = $rwFile['doc_name'];
$docName = $rwFile['doc_name'];
$File_Number = isset($rwFile['File_Number']) ? $rwFile['File_Number'] : '';
if(!empty($rwFile['doc_desc'])){
   $docDesc = json_decode($rwFile['doc_desc'], TRUE);
    $subject = $docDesc['subject'];
}else{
    $subject = "Review Document";
}

$user_id = $_SESSION['cdes_user_id'];
$storagePath = substr($filePath, 0, strrpos($filePath, '/'));

$strgName = mysqli_query($db_con, "select * from tbl_storage_level where sl_id = '$docName'"); // or die('Error:' . mysqli_error($db_con));
$rwstrgName = mysqli_fetch_assoc($strgName);
$storageName = $rwstrgName['sl_name'];
$storageName = str_replace(" ", "", $storageName);
$storageName = preg_replace('/[^A-Za-z0-9\-_]/', '', $storageName);

$updir = getStoragePath($db_con, $rwstrgName['sl_parent_id'], $rwstrgName['sl_depth_level']);

if(!empty($updir)){
    $updir = $updir . '/';
}else{
    $updir = '';
}

//$uploaddir = './extract-here/'.$storagePath.'/';

$uploaddir = "./extract-here/" . $updir . $storagePath . '/';
 
if (!is_dir($uploaddir)) {
    mkdir($uploaddir, 0777, TRUE) or die(print_r(error_get_last()));
}

$fname = substr($fname, 0, strrpos($fname, '.'));
$fname = str_replace("-", "", $fname);
$nfilename = $fname.time();
$filenameEnct = urlencode(base64_encode($nfilename)).'.'.$doc_extn;

$filepath = $uploaddir.$filenameEnct;


list($type, $data) = explode(';', $data);
list(, $data)      = explode(',', $data);
$data = base64_decode($data);

$upload = file_put_contents($filepath, $data);
$docPath = $storagePath.'/'.$filenameEnct;

if($upload){
    
    if(uploadFileInFtpServer($docPath, $filepath)){
        
        //if($action == "savenew"){
            
            $chekFileVersion = mysqli_query($db_con, "SELECT doc_id FROM `tbl_document_reviewer` WHERE substring_index(`doc_name`,'_',-1)='$docId'") or die('Error:' . mysqli_error($db_con));
            $flVersion = mysqli_num_rows($chekFileVersion);
            $flVersion = $flVersion;
            $fileNamewtVersion = $fname.'_'.$flVersion.'.'.$doc_extn;
            $docName = explode("_",$docName);
            $updateDocName = $docName[0].'_'.$docId .((!empty($docName[1]))?'_'.$docName[1]:'');
            $fetchReview = mysqli_query($db_con, "select * from `tbl_doc_review` where id='$revId'");
            $reviewInfo = mysqli_fetch_assoc($fetchReview);
            $ticketId = $reviewInfo['ticket_id'];
            $currentOredr = $reviewInfo['review_order'];
            $maxOrderCurrentTicket = mysqli_query($db_con, "select max(review_order) as lastOrder from `tbl_doc_review` where ticket_id='$ticketId'");
            $lastOrder = mysqli_fetch_assoc($maxOrderCurrentTicket);
            $nextOredr = (($currentOredr + 1) < $lastOrder['lastOrder']) ? $currentOredr + 1 : $lastOrder['lastOrder'];
            $updatenextReview = mysqli_query($db_con, "update tbl_doc_review set next_task='0' where review_order='$nextOredr' and ticket_id='$ticketId'");
      
            $updateReview = mysqli_query($db_con, "update tbl_doc_review set next_task='1',review_status='1',task_status='Reviewed',action_time='$date' where id='$revId'");
            $fetchQry = mysqli_query($db_con, "select id from `tbl_doc_review` where review_order='$nextOredr' and ticket_id='$ticketId' and next_task='0'");
            if (mysqli_num_rows($fetchQry) > 0) {
                $idins = mysqli_fetch_assoc($fetchQry);
                $nextOrderAvai = 1;
            } else {

                $nextOrderAvai = 0;
            }
          
            if ($updateReview) {
                
                if ($updateReview) {
                    $cols = '';
                    $columns = mysqli_query($db_con, "SHOW COLUMNS FROM tbl_document_reviewer");
                    while ($rwCols = mysqli_fetch_array($columns)) {
                        if ($rwCols['Field'] != 'doc_id') {
                            if (empty($cols)) {
                                $cols = '`' . $rwCols['Field'] . '`';
                            } else {
                                $cols = $cols . ',`' . $rwCols['Field'] . '`';
                            }
                        }
                    }
                    
                    $createVrsn = mysqli_query($db_con, "INSERT INTO tbl_document_reviewer($cols) select $cols from tbl_document_reviewer where doc_id='$docId'") or die('Error:' . mysqli_error($db_con));
                    $insertDocID = mysqli_insert_id($db_con);
                    if ($createVrsn) {
                        
                        $slid = $slid.'_'.$docId;
                        $updateNew = mysqli_query($db_con, "update tbl_document_reviewer set doc_name='$slid' where doc_id='$insertDocID'");
                        $qry = mysqli_query($db_con, "update tbl_document_reviewer set doc_extn='$doc_extn', old_doc_name='$fileNamewtVersion', doc_path='$docPath', uploaded_by='$uploadedBy', doc_size='$filesize', noofpages='1', dateposted='$date',File_Number='$File_Number' where doc_id='$docId'");


                        //$qry = mysqli_query($db_con, "insert into `tbl_document_reviewer`(`doc_name`,`old_doc_name`,`doc_extn`,`doc_path`,`uploaded_by`,`doc_size`,`noofpages`,`dateposted`,`File_Number`)values('$slid','$fname','docx','$normalPath','$uploadedBy','$Fsize','1','$date','$File_Number')");
                        if ($qry) {
                           
                            $qry = mysqli_query($db_con, "INSERT INTO `tbl_reviews_log`(`user_id`,`doc_id`,`action_name`,`start_date`,`end_date`,`system_ip`,`remarks`)values('$_SESSION[cdes_user_id]','$docId','Document Reviewed ','$date','$date','$host','')") or die(mysqli_error($db_con));
                            
                            if ($qry) {
                                
                                    $olddocname = base64_encode($insertDocID);
                                    
                                    //rename old thumbnail
                                     rename('thumbnail/'.base64_encode($docId).'.jpg', 'thumbnail/'.$olddocname.'.jpg');
                                    //create thumbnail
                                    $newdocname = base64_encode($docId);
                                    if($doc_extn=='jpg' || $doc_extn=='jpeg' || $doc_extn=='png'){
                                        createThumbnail2($filepath,$olddocname);
                                    }
                                    
                                    require 'mail.php';
                                    
                                    if ($nextOrderAvai == 1) {
                                        //echo "run1";
                                        $mail = assignNextReview($ticketId, $idins['id'], $db_con, $projectName, $subject, true);
                                    } else {
                                       // echo "run2".$ticketId;
                                        $mail = completeReview($ticketId, $db_con, $projectName, $subject, true);
                                    }

                                    if ($mail) {
                                        $response = array("status" => 'success', "msg" => $lang['Document_Review_Successfully'], "conn" => $db_con);
                                    } else {
                                        $response = array("status" => False, "msg" => "Mail Not Sent");
                                    }
                            } else {
                                $response = array("status" => False, "msg" => $lang['Log_Create_Failed']);
                            }
                        } else {
                            $response = array("status" => False, "msg" => $lang['Failed_To_Register_Document']);
                        }
                    } else {
                        $response = array("status" => False, "msg" => $lang['Failed_To_Version_Document']);
                    }
                } else {
                    $response = array("status" => False, "msg" => $lang['Update_Review_Failed']);
                }
            } else {
                $response = array("status" => False, "msg" => $lang['Invalid_Order_ID']);
            }
                
            
//            $createVrsn = mysqli_query($db_con, "INSERT INTO tbl_document_master(doc_name, old_doc_name, doc_extn, doc_path, doc_size, uploaded_by, noofpages, dateposted) VALUES ('$updateDocName', '$file_name', '$doc_extn', '$docPath', '$filesize', '$user_id', '1', '$date')") or die('Error:' . mysqli_error($db_con));
//            $insertDociD = mysqli_insert_id($db_con);
//            
//            if($insertDociD){
//               $redirurl = 'imageAnnotation?uid='.urlencode(base64_encode($_SESSION['cdes_user_id'])).'&i='.urlencode(base64_encode($insertDociD)).'';
//               $response = array("status" => "success", "msg" => "File save as new successfully.", "redirurl" => $redirurl);
//            }else{
//               $response = array("status" => "failed", "msg" => "File could not save.");
//            }
        //}else{
            
            
//            $update = mysqli_query($db_con, "update tbl_document_master set doc_path='$docPath' where doc_id='$docId'");
//            if($update){
//                $redirurl = 'imageAnnotation?uid='.urlencode(base64_encode($_SESSION['cdes_user_id'])).'&i='.urlencode(base64_encode($docId)).'';
//                $response = array("status" => "success", "msg" =>"File Override successfully.", "redirurl" => $redirurl);
//            }else{
//                $response = array("status" => "failed", "msg" => "File could not override.");
//            }
       // }
         
    }else{
        $response = array("status" => "failed", "msg" => "Failed to upload file");
       
    }
    
}else{
    $response = array("status" => "failed", "msg" => "Failed to upload file");
}

echo json_encode($response);

function uploadFileInFtpServer($destinationPath, $sourcePath) {

    //encrypt_my_file($sourcePath);
	
	$fileManager = new fileManager();
	// Connect to file server
	$fileManager->conntFileServer();
	if($fileManager->uploadFile($sourcePath, ROOT_FTP_FOLDER . '/' . $destinationPath)){
		 return true;
	}else{
		 return false;
	}
}

function setFileVersionName($filename, $vno) {
    //check for if file name is with extension
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    // List of Extension to be considered. 
    $allow_exts = array('doc', 'docx', 'pdf');
    if (!empty($ext)) {
        if (in_array($ext, $allow_exts)) {
            // set filename without extension
            $new_filename = basename($filename, ".$ext");
        } else {
            $new_filename = $filename;
            $ext = '';
        }
    } else {
        $new_filename = $filename;
    }
    $exploded_filename = explode('_', $new_filename);
    $new_vno = $vno + 1;
    if ($vno > 0) {
        if (end($exploded_filename) == $vno) {
            array_pop($exploded_filename);
            $new_filename = implode("_", $exploded_filename);
        }
    }
    //echo $new_filename;
    $version_filename = $new_filename . "_" . $new_vno . (!empty($ext) ? '.' . $ext : '');
    //echo $version_filename;
    return $version_filename;
}
?>