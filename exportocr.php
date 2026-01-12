<?php
require_once './application/config/database.php';
require_once './application/pages/function.php';
require_once './classes/fileManager.php';
require_once './ocr.php';
error_reporting(0);

$fileManager = new fileManager();


if(isset($_POST['exportocr'])){
	
	if($_POST['exaction']){
		
		$docId = $_POST['exdocid'];
		$exaction = $_POST['exaction'];
		
		$exportData = mysqli_query($db_con, "select doc_id, doc_name, doc_extn, old_doc_name, doc_path from tbl_document_master where doc_id='$docId'");
		$rw = mysqli_fetch_assoc($exportData);
		
		$fileName = $rw['old_doc_name'];
        $filepath = $rw['doc_path'];
        $slid = $rw['doc_name'];
        $doc_extn = $rw['doc_extn'];
		
		/* $sl_name = mysqli_query($db_con, "select sl_name,sl_parent_id,sl_depth_level from tbl_storage_level where sl_id='$rw[doc_name]'");
		$sl_row = mysqli_fetch_assoc($sl_name);
		$storageName = str_replace(" ", "", $sl_row['sl_name']);
		$storageName = preg_replace('/[^A-Za-z0-9\-_]/', '', $storageName);
		$updir = getStoragePath($db_con, $sl_row['sl_parent_id'], $sl_row['sl_depth_level']);
		if(!empty($updir)){
			$updir = $updir . '/';
		}else{
			$updir = '';
		} */
		
		$uploaddir ='extract-here/'.substr($filepath, 0, strrpos($filepath, "/"));
		
		//$uploaddir = "extract-here/" .$updir.$storageName;
		
		$server_path = ROOT_FTP_FOLDER . '/' . $filepath;
       
		$img_array = array('jpg', 'jpeg', 'png', 'bmp', 'pnm', 'jfif', 'jpeg', 'tiff', 'pdf');
		
		if (in_array(strtolower($rw['doc_extn']), $img_array)) {
			
			$file = $uploaddir . "/TXT/" . $rw['doc_id'].".txt";
			
			if(!file_exists($file)){ // Check file exist or not if not than download file from ftp and ocr that file
			
				if(file_exists('extract-here/'.$filepath)){
					
					echo $localPath = 'extract-here/'.$filepath;	
				}else{
					$fileManager->conntFileServer();
					$localPath = $fileManager->downloadFile($server_path,'extract-here/'.$filepath);
				}
				
				if($localPath){
					
					decrypt_my_file($localPath); 
					// Decrypt file before ocr
					
					$ocr = new OCR($uploaddir);
					$ocr->ocrDone($localPath, "", $docId);
					$file = $uploaddir. "/TXT/" . $rw['doc_id'].".txt";
					
				}
			}
			
		$contents = file_get_contents($file);
			
		}else if ($rw['doc_extn'] == 'text' || $rw['doc_extn'] == 'txt' 
				|| $rw['doc_extn'] == "xls" || strtolower($rw['doc_extn']) == 'xlsx'
				|| $rw['doc_extn'] == "doc" || $rw['doc_extn'] == "docx" || $rw['doc_extn']=="pptx" || $rw['doc_extn']=="ppt"){
					
				$file = $uploaddir . "/TXT/" . $rw['doc_id'] . ".txt";
			
			if(!file_exists($file)){  // Check file exist or not if not than download file from ftp and get text from that file
				
				if(file_exists('extract-here/'.$filepath)){
					
					$localPath = 'extract-here/'.$filepath;	
				}else{
					$fileManager->conntFileServer();
					$localPath = $fileManager->downloadFile($server_path,'extract-here/'.$filepath);
				}
				
				if($localPath){
					
					decrypt_my_file($localPath); // Decrypt file before ocr
					
					$txtpath = $uploaddir . '/TXT/';
					if (!is_dir($txtpath)) {
						mkdir($txtpath, 0777, TRUE) or die(print_r(error_get_last()));
					}
					
					$extractHereDirfile = $localPath;
					
					if (strtolower($rw['doc_extn']) == "doc") {
						$docText = read_doc($extractHereDirfile);
					} elseif (strtolower($rw['doc_extn']) == "docx") {
						$docText = read_docx($extractHereDirfile);
					} elseif (strtolower($rw['doc_extn']) == "xlsx") {
						$docText = xlsx_to_text($extractHereDirfile);
					} elseif (strtolower($rw['doc_extn']) == "xls") {
						$docText = xls_to_txt($extractHereDirfile);
					} elseif (strtolower($rw['doc_extn']) == "pptx" || strtolower($extn) == "ppt") {
						$docText = pptx_to_text($extractHereDirfile);
					} else if(strtolower($rw['doc_extn']) == "txt" || strtolower($extn) == "text"){
						$docText = txt_to_text($extractHereDirfile);
					}
					
					if($docText!=""){
						
						$fp = fopen($txtpath . $rw['doc_id'] . ".txt", "wb");
						fwrite($fp, $docText);
						fclose($fp);
						
						unlink($extractHereDirfile);
					}
				}
				
				$file = $uploaddir . "/TXT/" . $rw['doc_id'] . ".txt";
			}
			
			echo $contents = file_get_contents($file);
		}
		

		
		if($exaction=='RTF'){
			
			header("Content-type: application/msword");
			header("Content-disposition: attachment;      filename=exportocer.rtf");
			header("Content-length: " . strlen($contents));
			echo $contents;
			
		}else if($exaction=='HTML'){
			
			header("Content-type: text/html");
			header("Content-disposition: attachment;      filename=exportocr.html");
			header("Content-length: " . strlen($contents));
			echo nl2br($contents);
			
		}else if($exaction=='XML'){
			
			//or create $array with file('file.txt');
			
			$array = explode("\n",$contents);
			$xml = new SimpleXMLElement('<xml />');
			foreach($array as $k=>$v){
				
				$track = $xml->addChild('ocrdesc');
				$track->addChild('description', $v);
			}
			
			header('Content-type: text/xml');

			header("Content-disposition: attachment;  filename=exportocr.xml");
			
			print($xml->asXML());
		}
		
		
	}
	
}

function getFileFromFTP($filePath, $fileserver, $port, $ftpUser, $ftpPwd){
	
	if (FTP_ENABLED) {
		
			$localPath = 'extract-here/'.$filePath;
			$ftp = new ftp();
			$ftp->conn("$fileserver", "$port", "$ftpUser", "$ftpPwd");
			$server_path = ROOT_FTP_FOLDER . '/' . $filePath;
			
			if($ftp->get($localPath, $server_path)){ // download live "$server_path"  to local "$localpath"
				
			}else{
				
			} 
			/* $arr = $ftp->getLogData();
			if ($arr['error'] != "")
			// echo '<h2>Error:</h2>' . implode('<br />', $arr['error']);
			if ($arr['ok'] != "") {
				//echo 'success';
				//header("location:pdf/web/viewer.php?file=$folderName/view_pdf.pdf");
			} */
		
	} else {
		$localPath = 'extract-here/' . $filePath;
	}
	
	return $localPath;
}


?>