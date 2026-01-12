
<?php
error_reporting(0);
require './vendor/autoload.php';
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
require_once './loginvalidate.php';
require_once './application/config/database.php';
require_once 'application/pages/function.php';

error_reporting(E_ALL);
//for user role
$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));
$uid = base64_decode(urldecode($_GET['uid']));
$rwgetRole = mysqli_fetch_assoc($chekUsr);
$rwgetRole['excel_file'];
if ($uid != $_SESSION['cdes_user_id']) {
    header('Location:index');
}

/*if ($rwgetRole['excel_file'] != '1') {
    header('Location: index');
}*/
// A complex example that shows excel worksheets data appropiate to excel file
//$excel_file = "test.xls";
$dcid = @$_GET['file'];
$perm = @$_GET['perm'];

$perm = base64_decode(urldecode($perm));
$docId = base64_decode(urldecode($dcid));


$checkColumn = mysqli_query($db_con, "SHOW COLUMNS from tbl_document_master LIKE 'gdocid'");
if (mysqli_num_rows($checkColumn) <= 0) {
    $qry = mysqli_query($db_con, "ALTER TABLE tbl_document_master ADD COLUMN  `gdocid` Text DEFAULT NULL")or die('Error alter column:' . mysqli_error($db_con));
}

$file = mysqli_query($db_con, "select doc_name, filename, doc_path, doc_extn, old_doc_name, checkin_checkout,gdocid from tbl_document_master where doc_id='$docId'") or die('error' . mysqli_error($db_con));
$rwFile = mysqli_fetch_assoc($file);
$slid = $rwFile['doc_name'];
$fileName = $rwFile['old_doc_name'];
$filePath = $rwFile['doc_path'];
$slid = $rwFile['doc_name'];
$doc_extn = $rwFile['doc_extn'];
$CheckinCheckout = $rwFile['checkin_checkout'];
$gdocid = $rwFile['gdocid'];

if(!empty($gdocid) and urldecode(base64_decode($_GET['perm']))=='reader')
{

   $tokenPath = __DIR__.'/token.json';
    if (file_exists($tokenPath)) {                                               
        $accessToken = json_decode(file_get_contents($tokenPath), true);
        //$client->setAccessToken($accessToken);
        //print_r($accessToken);
    $_SESSION['access_token']=$accessToken['access_token'];
    }
    if ($client->isAccessTokenExpired()) {
            // Refresh the token if possible, else fetch a new one.
        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        }
    }
    if (isset($_GET['code']) || (isset($_SESSION['access_token']) && $_SESSION['access_token'])) {
    if (isset($_GET['code'])) {
       $client->authenticate($_GET['code']);
       $_SESSION['access_token'] = $client->getAccessToken();
    } else{
       $client->setAccessToken($_SESSION['access_token']);
    }
    $service = new Google_Service_Drive($client);
    $permission = new Google_Service_Drive_Permission();
    $permission->setRole( $perm );//writer/'reader'
    $permission->setType( 'anyone' );
    //$permission->setValue( 'me' );
        try {
            $service->permissions->create( $gdocid, $permission );
            //echo json_encode(array('fileid'=>$fileid,'extn'=>$fileExtn));
        } catch (Exception $e) {
        print "An error occurred: " . $e->getMessage();
      }
    } 
    

    if(urldecode(base64_decode($_GET['perm']))=='reader'){
        
        $authUrl="doc_viewer?dcid=".urlencode(base64_encode($gdocid))."&perm=".$_GET['perm']."&type=".urlencode(base64_encode($doc_extn));
        $logviewDocument = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`, `sl_id`, `doc_id`, `action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','$slid','$docId','Document Viewed','$date','$host','Document $fileName Viewed by $_SESSION[admin_user_name] $_SESSION[admin_user_last]')") or die('error : ' . mysqli_error($db_con));
    }else{
        $authUrl="doc_editor?dcid=".urlencode(base64_encode($gdocid))."&perm=".$_GET['perm']."&type=".urlencode(base64_encode($doc_extn))."&id=".$dcid;
    }

    /*if($doc_extn=="xlsx" || $doc_extn=="xls")
    {
    $authUrl="excel_viewer.php?dcid=".urlencode(base64_encode($gdocid))."&perm=".$_GET['perm'];
    }
    else
    {
    $authUrl="doc_viewer.php?dcid=".urlencode(base64_encode($gdocid))."&perm=".$_GET['perm'];
    }*/
    
}
else
{
    if(!empty($gdocid) and urldecode(base64_decode($_GET['perm']))=='writer'){
        //delete file from google drive
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
            $service->files->delete($gdocid);
        } catch (Exception $e) {
            print "An error occurred: " . $e->getMessage();
        }  

        //remove from db
        $updateNew = mysqli_query($db_con, "update tbl_document_master set gdocid='' where doc_id='$docId'");
    }
    
	require_once './classes/fileManager.php';
	
	$fileManager = new fileManager();
	// Connect to file server
	$fileManager->conntFileServer();
	$localPath = $fileManager->getFile($rwFile);
	
	
    // decrypt_my_file($localPath);
    // echo $localPath;  die();
        // $url = "http://144.48.78.35/Testing/ezeefile_sar/docs.php"; // e.g. http://localhost/test.php // request URL
        $url = "http://royankit.ezeepea.com/docs.php";
        // $url = BASE_URL.'/docs.php';
        
        // $url = "https://sargroup.ezeeoffice.co.in/docs.php";
        $headers = array("Content-Type:multipart/form-data"); // cURL headers for file uploading
        $ch = curl_init();
        $file = new CURLFile($localPath
        //'Input_Image/test1.jpg','image/jpeg','test.jpg'
//        $_FILES['file']['tmp_name'],
//        $_FILES['file']['type'],
//        $_FILES['file']['name']
        );
        $postfields = array('file'=>$file,'perm'=>$perm);
        // print_r($postfields);exit;
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
        $data=json_decode($result,true);
        // print_r($data);
        $qry = mysqli_query($db_con, "update tbl_document_master set `gdocid`='".$data['fileid']."' where doc_id='$docId'")or die('Error alter column:' . mysqli_error($db_con));
        
        if(base64_decode(urldecode($_GET['perm']))=='reader'){
			
            $authUrl="doc_viewer?dcid=".urlencode(base64_encode($data['fileid']))."&perm=".$_GET['perm']."&type=".urlencode(base64_encode($data['extn']));
            $logviewDocument = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,  `doc_id`, `action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','$docId','Document Viewed','$date','$host','Document $fileName Viewed by $_SESSION[admin_user_name] $_SESSION[admin_user_last]')") or die('error : ' . mysqli_error($db_con));
			
        }else{
			
            $authUrl="doc_editor?dcid=".urlencode(base64_encode($data['fileid']))."&perm=".$_GET['perm']."&type=".urlencode(base64_encode($data['extn']))."&id=".$dcid;
        }
		
        /*if($data['extn']=="xlsx" || $data['extn']=="xls")
        {
        $authUrl="excel_viewer.php?dcid=".urlencode(base64_encode($data['fileid']))."&perm=".$_GET['perm'];
        }
        else
        {
        $authUrl="doc_viewer.php?dcid=".urlencode(base64_encode($data['fileid']))."&perm=".$_GET['perm'];
        }*/
}
header('Location: ' . $authUrl);
exit();
?>