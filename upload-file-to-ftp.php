<?php
require __DIR__ . '/application/config/conf.php';
require __DIR__ . '/application/pages/function.php';

$db_con = @mysqli_connect($dbHost, $dbUser, $dbPwd, $dbName) OR die('could not connect:' . mysqli_connect_error());

require_once  __DIR__ . '/classes/fileManager.php';
error_reporting(E_ALL);

$fileManager = new fileManager();
$fileManager->conntFileServer();
$levelResult = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_depth_level = '0'") or die('Error:' . mysqli_error($db_con));
$level = mysqli_fetch_assoc($levelResult);
define('ROOT_FTP_FOLDER', $level['sl_name']);

$fetchDoc = mysqli_query($db_con, "SELECT doc_id, doc_path FROM tbl_document_master WHERE ftp_done='0' and flag_multidelete='1'");

while($pending_doc = mysqli_fetch_assoc($fetchDoc)){
	
	//print_r($pending_doc);
	//update status to initiated
  	$updtFtpStatus = mysqli_query($db_con, "UPDATE tbl_document_master set ftp_done='2' WHERE doc_id='$pending_doc[doc_id]'");
	
	$filePath = __DIR__ . DIRECTORY_SEPARATOR . "extract-here" . DIRECTORY_SEPARATOR . $pending_doc['doc_path'];
	
	if(file_exists($filePath)){
		
		$server_file = ROOT_FTP_FOLDER . '/' . $pending_doc['doc_path'];
		
		if ($fileManager->uploadFile($filePath, $server_file)) {
			
			echo "Success";
			
			if(file_exists($filePath)){
				
				gc_collect_cycles();
				
				unlink($filePath);
			}
			
		  //update status to done
		  $updtFtpStatus = mysqli_query($db_con, "UPDATE tbl_document_master set ftp_done='1' WHERE doc_id='$pending_doc[doc_id]'");
		}else{
			echo "Failed";
		  	//update status to declined
		  	$updtFtpStatus = mysqli_query($db_con, "UPDATE tbl_document_master set ftp_done='3' WHERE doc_id='$pending_doc[doc_id]'");
		}
		//$arr = $ftp->getLogData();
	}
}


?>