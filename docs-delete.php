
<?php
session_start();
require_once './loginvalidate.php';
require_once './application/config/database.php';	
require_once './application/pages/function.php';
require_once './classes/ftp.php';
error_reporting(E_ALL);
require __DIR__ . '/vendor/autoload.php';


ini_set('display_errors', '1');
$client = new Google_Client();
//$client = getClient();
//$service = new Google_Service_Drive($client);
// Get your credentials from the console
//$client->setClientId('926182991587-qi51qhmdjkh26bu94tg1ocfscb4ff6op.apps.googleusercontent.com');
//$client->setClientSecret('Z9pk4gS2lCY90FAoGhuktiUM');
//$client->setRedirectUri('https://royankit.ezeepea.com');
//$client->setScopes(array('https://www.googleapis.com/auth/drive.file'));

session_start();

//echo'ok';
$tokenPath = __DIR__.'/token.json';
    if (file_exists($tokenPath)) {
        $accessToken = json_decode(file_get_contents($tokenPath), true);
        //$client->setAccessToken($accessToken);
        //print_r($accessToken);
	$_SESSION['access_token']=$accessToken['access_token'];
    }
	
if (isset($_GET['code']) || (isset($_SESSION['access_token']) && $_SESSION['access_token'])) {
    if (isset($_GET['code'])) {
       $client->authenticate($_GET['code']);
       $_SESSION['access_token'] = $client->getAccessToken();
    } else{
       $client->setAccessToken($_SESSION['access_token']);
    }
    $service = new Google_Service_Drive($client);
    $fileId=$_POST['dcid'];
    $perm=$_POST['perm'];
		if($perm=="edit")
		{
			$file = $service->files->get($fileId);
			$fileName = $file->getName();
			
			// Download a file.
			$content = $service->files->get($fileId, array("alt" => "media"));
			$host = $host . '/' . $_SESSION['custom_ip'];
			//sk@120219:restrict up to two ip.
			$lip=$host;
			$ipos=strpos($lip, '/', strpos($lip, '/') + 1);
			$host=($ipos ? substr($lip, 0, $ipos) : $lip);
			$user_id = $_SESSION['cdes_user_id'];

			$getDocName = mysqli_query($db_con, "select * from tbl_document_master  where gdocid='$fileId'"); // or die('Errorv:' . mysqli_error($db_con));
			$rwgetDocName = mysqli_fetch_assoc($getDocName);
			$docName = $rwgetDocName['doc_name'];
			$doc_id = $rwgetDocName['doc_id'];
			$old_file_name = $rwgetDocName['old_doc_name'];
			$oldextn = substr($old_file_name, strrpos($old_file_name, '.') + 1); // old file extn
			$oldfname = substr($old_file_name, 0, strrpos($old_file_name, '.')); // old file name
			$file_name = $old_file_name;	
			$imgs=explode(".",$old_file_name);
			$fname=$imgs[0];
			$doc_extn=$imgs[1];
			$extn = substr($file_name, strrpos($file_name, '.') + 1);
			$fname = substr($file_name, 0, strrpos($file_name, '.'));
			$fileExtn = substr($file_name, strrpos($file_name, ".") + 1);

			$updateDocName = $docName . '_' . $doc_id; //storage id followed by doc id
			$chekFileVersion = mysqli_query($db_con, "SELECT * FROM `tbl_document_master` WHERE find_in_set('$updateDocName', doc_name)"); //or die('Errorv2:' . mysqli_error($db_con));
			$flVersion = mysqli_num_rows($chekFileVersion);
			$flVersion = $flVersion + 1;
			$nfilename = $oldfname . '_' . $flVersion;

			$strgName = mysqli_query($db_con, "select * from tbl_storage_level where sl_id = '$docName'");  //or die('Errorv3:' . mysqli_error($db_con));
			$rwstrgName = mysqli_fetch_assoc($strgName);
			$storageName = $rwstrgName['sl_name'];
			$storageName = str_replace(" ", "", $storageName);
			$storageName = preg_replace('/[^A-Za-z0-9\-]/', '', $storageName);
			$uploaddir = "extract-here/" . ROOT_FTP_FOLDER . "/" . $storageName . '/';
			if (!is_dir($uploaddir)) {
				mkdir($uploaddir, 0777, TRUE) or die(print_r(error_get_last()));
			}
			$nfilename = preg_replace('/[^A-Za-z0-9_\-]/', '', $nfilename);
			$filenameEnct = urlencode(base64_encode($nfilename));
			$filenameEnct = preg_replace('/[^A-Za-z0-9_\-]/', '', $filenameEnct);
			$filenameEnct = $filenameEnct . '.' . $extn;
			$filenameEnct = time() . $filenameEnct;

			 $uploaddir = $uploaddir . $filenameEnct;	
			$upload=false;
			$handle = fopen($uploaddir, "w+"); // Modified
			while (!$content->getBody()->eof()) { // Modified
			fwrite($handle, $content->getBody()->read(1024)); // Modified
			}
			$upload=true;
			fclose($handle);

			$file_size=filesize($uploaddir);		
			$uploadInToFTP = false;
			$unlink_dir=false;
			// encypt file
			encrypt_my_file($uploaddir);
			
			if ($upload) {
				require __DIR__ .'/classes/fileManager.php';
				$fileManager->conntFileServer();
				$uploadInToFTP = $fileManager->uploadFile($uploaddir, ROOT_FTP_FOLDER . '/' . $filepath, false);
			}
			
			if ($uploadInToFTP) { 
			decrypt_my_file($uploaddir);

			$cols = '';
			$columns = mysqli_query($db_con, "SHOW COLUMNS FROM tbl_document_master");
			while ($rwCols = mysqli_fetch_array($columns)) {
				if ($rwCols['Field'] != 'doc_id') {
					if (empty($cols)) {
						$cols = '`' . $rwCols['Field'] . '`';
					} else {
						$cols = $cols . ',`' . $rwCols['Field'] . '`';
					}
				}
			}
			$createVrsn = mysqli_query($db_con, "INSERT INTO tbl_document_master($cols) select $cols from tbl_document_master where doc_id='$doc_id'"); // or die('Errorv4:' . mysqli_error($db_con));
			$insertDocID = mysqli_insert_id($db_con);

			 $olddocname = base64_encode($insertDocID);
			//rename old thumbnail
			rename('thumbnail/'.base64_encode($doc_id).'.jpg', 'thumbnail/'.$olddocname.'.jpg');
			//create thumbnail
			$newdocname = base64_encode($doc_id);
			if($extn=='jpg' || $extn=='jpeg' || $extn=='png'){
				createThumbnail2($uploaddir,$newdocname);
			}elseif($extn=='pdf'){
				changePdfToImage($uploaddir,$newdocname);
			}
			if($unlink_dir){
				unlink($uploaddir);
			}
			$log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Versioning Document $file_name Added','$date',null,'$host',null)");
			if ($createVrsn) {
				$updateNew = mysqli_query($db_con, "update tbl_document_master set doc_name='$updateDocName', checkin_checkout='1',gdocid='' where doc_id='$insertDocID'");
				}
				$updateOld = mysqli_query($db_con, "update tbl_document_master set old_doc_name='$file_name',filename='$fname', doc_extn='$extn', doc_path='$storageName/$filenameEnct', uploaded_by='$user_id', doc_size='$file_size', noofpages='1', dateposted='$date' where doc_id='$doc_id'");
			
			}
		}
    try {
		$qry = mysqli_query($db_con, "update tbl_document_master set `gdocid`='' where gdocid='$fileId'")or die('Error alter column:' . mysqli_error($db_con));
        $service->files->delete($fileId);
    } catch (Exception $e) {
      print "An error occurred: " . $e->getMessage();
    }
//echo'ok';
} else {
    $authUrl = $client->createAuthUrl();
    header('Location: ' . $authUrl);
    exit();
}	
if(isset($_POST['filepath'])){

    $filepath = $_POST['filepath'];
    
    if(FTP_ENABLED){
        
         if(unlink('./'.$filepath)){
        
            $file_dir =   substr($filepath,0, strrpos($filepath, "/"));

            if (is_dir_empty($file_dir)) {

                rmdir($file_dir);
            }
         }
        
    }


    $filepath = end(explode("/", $filepath));

    $downloadpath = '../../viewer-pdf/'.$filepath;

    $file=fopen('errors.txt', "a");
    fwrite($file, $downloadpath);

    fclose($file);

    if(file_exists($downloadpath)){

        unlink($downloadpath);
    }
    
}

function is_dir_empty($dir) {
  if (!is_readable($dir)) return NULL; 
  return (count(scandir($dir)) == 2);
}
function deleteFile($service, $fileId) {
  try {
    $service->files->delete($fileId);
  } catch (Exception $e) {
    print "An error occurred: " . $e->getMessage();
  }
}


?>