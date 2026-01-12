<?php
require_once 'classes/ftp.php';

function uploadFileInFtpServer($destinationPath, $sourcePath){
   
    
    if(FTP_ENABLED){

        $ftp = new ftp();
		
        $ftp->conn(FILE_SERVER, PORT, FTP_USER, FTP_PASS);

        if($ftp->put(ROOT_FTP_FOLDER.'/'.$destinationPath,$sourcePath))
        {
            //unlink($sourcePath);
			return true;
        }
		else
		{
			
			$arr = $ftp->getLogData();
			if ($arr['error'] != ""){

				echo '<h2>Error:</h2>' . implode('<br />', $arr['error']);
			}
			
			return false;
			
		}       
        
    }else{
		
		return true;
	}
}
function getDocumentPath($db_con, $doc_id, $fileName, $filePath, $doc_extn, $slid, $userId){
	
	$storage = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id='$slid'") or die('Error');
	$rwStor = mysqli_fetch_assoc($storage);
	$folderName="../temp";
	if (!dir($folderName)) {
		mkdir($folderName, 0777, TRUE);
	}
	$folderName=$folderName.'/'.$userId;
	if (!dir($folderName)) {
		mkdir($folderName, 0777, TRUE);
	}
	if(!empty($rwStor['sl_name'])){
		$folderName = $folderName.'/'.preg_replace('/[^A-Za-z0-9\-]/', '',$rwStor['sl_name']);//preg_replace('/[^A-Za-z0-9\-]/', '', $string);
		if (!dir($folderName)) {
			mkdir($folderName, 0777, TRUE);
		}
	}

	if(FTP_ENABLED){
		
		$fileName = preg_replace('/\\.[^.\\s]{3,4}$/', '', $fileName);
		$localPath = $folderName . '/' . preg_replace('/[^A-Za-z0-9\-]/', '',$fileName).'.'.$doc_extn;
		if (!empty($fileName)) {
			$ftp = new ftp();
			$ftp->conn(FILE_SERVER, PORT, FTP_USER, FTP_PASS);
			
			$server_path = ROOT_FTP_FOLDER.'/'.$filePath;
		  
			if($ftp->get($localPath, $server_path)){
				
			}else{ 
				$arr = $ftp->getLogData();
				if ($arr['error'] != "")
				echo '<h2>Error:</h2>' . implode('<br />', $arr['error']);
				
			}	
		} 
		
	}else{
		
		$localPath ='extract-here/'.$filePath;
	}
	
	return $localPath;
}
?>