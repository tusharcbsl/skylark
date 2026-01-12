<?php 
// $ex= exec("php /var/www/Testing/ezeefile_sar/schedule-file.php");

// require __DIR__ . '/loginvalidate.php';
require __DIR__ . '/application/config/database.php';
/*require __DIR__ . '/application/pages/function.php';
require __DIR__ . '/application/pages/head.php';*/
require __DIR__ . '/classes/ftp.php';
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/application/pages/function.php';
require __DIR__ .'/classes/fileManager.php';


// $test = mysqli_query($db_con, "update tbl_document_master set edited_user_id='1' where doc_id='444'");  


$checkGoogleDoc = mysqli_query($db_con, "select * from tbl_document_master tdm where gdocid!='' and checkin_checkout='1' ") or die('Error:' . mysqli_error($db_con));

if(mysqli_num_rows($checkGoogleDoc)>0){
	while($doc = mysqli_fetch_assoc($checkGoogleDoc)){
		$checkVersionTime = mysqli_query($db_con, "SELECT id, end_date FROM tbl_ezeefile_logs WHERE doc_id='$doc[doc_id]' and action_name like 'Versioning Document%' ORDER BY id DESC LIMIT 1");
		$fetchVersionTime = mysqli_fetch_assoc($checkVersionTime);
		$checkedInTime = strtotime($fetchVersionTime['end_date']);
		$currentTime = strtotime($date);
		$fileId = $doc['gdocid'];
		$fileName = $doc['old_doc_name'];
		$perm = 'writer';

		if(($currentTime-$checkedInTime) > 240){
			$url = "http://royankit.ezeepea.com/docs-edit.php";
			// $url = BASE_URL."docs-edit.php"1;
			// $url = "https://sargroup.ezeeoffice.co.in/docs-edit.php";

			$headers = array("Content-Type:multipart/form-data"); // cURL headers for file uploading
			$ch = curl_init();
			$postfields = array('fileId'=>$fileId,'perm'=>$perm,'fileName'=>$fileName);
			$options = array(
			    CURLOPT_URL => $url,
			    //CURLOPT_HEADER => true,
			    CURLOPT_POST => 1,
			    CURLOPT_SSL_VERIFYHOST => 0,
			    CURLOPT_SSL_VERIFYPEER => false,
			    CURLOPT_HTTPHEADER => $headers,
			    CURLOPT_POSTFIELDS => $postfields,
			    CURLOPT_RETURNTRANSFER => true

			); // cURL options
			curl_setopt_array($ch, $options);
			$result=curl_exec($ch);
			$newfile = preg_replace('/\s+/', '', $result);

			$newfile_cont = file_get_contents($newfile);
			$oldfile = __DIR__ . "/extract-here/" . $doc['doc_path'];

			$oldfile_cont = file_get_contents($oldfile);

			if($newfile_cont != $oldfile_cont){
			    unlink($oldfile);
			    copy($newfile, $oldfile);
    			
    			$docName = $doc['doc_name'];
			    $strgName = mysqli_query($db_con, "select * from tbl_storage_level where sl_id = '$docName'");  //or die('Errorv3:' . mysqli_error($db_con));
			    $rwstrgName = mysqli_fetch_assoc($strgName);
			    $storageName = $rwstrgName['sl_name'];
			    $storageName = str_replace(" ", "", $storageName);
			    $storageName = preg_replace('/[^A-Za-z0-9\-]/', '', $storageName);

			    $filename = explode('/', $doc['doc_path']);
			    $filenameEnct = end($filename);
				
				
				$fileManager->conntFileServer();
				$uploadInToFTP = $fileManager->uploadFile($oldfile, ROOT_FTP_FOLDER . '/' . $filepath, false);
				
		
			    /* if (FTP_ENABLED) {
		            $ftp = new ftp();
		            $ftp->conn("$fileserver", "$port", "$ftpUser", "$ftpPwd");
		            $filepath = $storageName . '/' . $filenameEnct;
		            $uploadfile = $ftp->put(ROOT_FTP_FOLDER . '/' . $filepath, $oldfile);
		            $arr = $ftp->getLogData();
		        } */
			}
			$updateNew = mysqli_query($db_con, "update tbl_document_master set gdocid='', edited_user_id='' where doc_id='$doc[doc_id]'");  

			//delete temp file
			$url = "http://royankit.ezeepea.com/docs-del.php";
			// $url2 = BASE_URL."docs-del.php";
			$headers2 = array("Content-Type:multipart/form-data"); // cURL headers for file uploading
			$ch2 = curl_init();
			$postfields2 = array('filePath'=>$newfile);
			
			$options2 = array(
			    CURLOPT_URL => $url2,
			    //CURLOPT_HEADER => true,
			    CURLOPT_POST => 1,
			    CURLOPT_SSL_VERIFYHOST => 0,
			    CURLOPT_SSL_VERIFYPEER => false,
			    CURLOPT_HTTPHEADER => $headers2,
			    CURLOPT_POSTFIELDS => $postfields2,
			    CURLOPT_RETURNTRANSFER => true

			); // cURL options
			curl_setopt_array($ch2, $options2);
			$res = curl_exec($ch2);

			//delete file from google drive
			// Get the API client and construct the service object.
    		$tokenPath = __DIR__ .'/token.json';
			$client = new Google_Client();
			$client->setApplicationName('Google Drive API PHP Quickstart');
		    $client->setScopes(Google_Service_Drive::DRIVE);
		    $client->setAuthConfig(__DIR__ .'/credentials.json');
		    $client->setAccessType('offline');
		    $client->setPrompt('select_account consent');
			
			if (file_exists($tokenPath)) {
		        $accessToken = json_decode(file_get_contents($tokenPath), true);
		        $client->setAccessToken($accessToken);
		    }
		    		    
			$service = new Google_Service_Drive($client);
			try {
				$service->files->delete($fileId);
			} catch (Exception $e) {
				print "An error occurred: " . $e->getMessage();
			}

		}
	}
}

?>