<?php
require_once './application/config/database.php';
require_once './application/pages/function.php';
require_once './classes/fileManager.php';

$id = base64_decode(urldecode($_GET['file']));
$emailId = base64_decode(urldecode($_GET['em']));
if($_GET['chk']=='rw'){
$file = mysqli_query($db_con, "SELECT * FROM tbl_document_reviewer where doc_id='$id'") or die("error:" . mysqli_errno($db_con));
} else {
	echo "SELECT * FROM tbl_document_master where doc_id='$id'";
$file = mysqli_query($db_con, "SELECT * FROM tbl_document_master where doc_id='$id'") or die("error:" . mysqli_errno($db_con));   
}
$rwFile = mysqli_fetch_assoc($file);
$fileName = $rwFile['old_doc_name'];
$filePath = $rwFile['doc_path'];
$slid=$rwFile['doc_name'];
$doc_extn=$rwFile['doc_extn'];
if(FTP_ENABLED){
$fileManager = new fileManager();
// Connect to file server
$fileManager->conntFileServer();
$localPath = $fileManager->getFile($rwFile);
}else{
	$localPath = "extract-here/".$filePath;
}

header('Content-Description: File Transfer');
header('Cache-Control: public');
header('Content-Type: '.$doc_extn);
header("Content-Transfer-Encoding: binary");
header('Content-Disposition: attachment; filename='. $fileName);
header('Content-Length: '.filesize($localPath));
ob_clean(); #THIS!
flush();

if(readfile($localPath)){
    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `doc_id`,`action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, null, '$emailId',null,'$slid', '$id','File download by $emailId .','$date',null,'$host',NULL)") or die('error : ' . mysqli_error($db_con));
 // remove temp file if ftp is enabled.   
 //if(FTP_ENABLED){  } 
 
 //unlink($localPath);
}
?>