
<?php
//echo exec('php quickstart.php');
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
//echo'ok';
    //Insert a file
    $file = new Google_Service_Drive_DriveFile();
    //$data = file_get_contents('extract-here/faceauth.png');
    if(!empty($_FILES['file']['name']) && !empty($_POST['perm'])){
    $file->setName($_FILES['file']['name']);
    $file->setDescription('test docs');
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $_FILES['file']['tmp_name']);
    //$file->setMimeType($mime);
//    //$permission->setValue( 'me' );
    //$file->setPermissions(array("permissions"=>array("role"=> "writer","type"=> "anyone")));
//    $file->setOwners('anyone');
    
    $data=$_FILES['file']['tmp_name'];
    $data = file_get_contents($_FILES['file']['tmp_name']);
//echo'ok';
    $createdFile = $service->files->create($file, array(
          'data' => $data,
          'mimeType' => $mime,
          'uploadType' => 'multipart'
           //'role'=>'reader',
           //'type'=> 'anyone'
        ));

   // print_r($createdFile);
    $fileid=$createdFile->getID();

	$file_name=$_FILES['file']['name'];
	$name = explode(".", $file_name);
	$fileExtn = $name[1];
	
    
    $permission = new Google_Service_Drive_Permission();
    $permission->setRole( $_POST['perm'] );//writer/'reader'
    $permission->setType( 'anyone' );
    //$permission->setValue( 'me' );
        try {
            $service->permissions->create( $fileid, $permission );
            echo json_encode(array('fileid'=>$fileid,'extn'=>$fileExtn));
        } catch (Exception $e) {
        print "An error occurred: " . $e->getMessage();
      }
    }
//echo'ok';
} else {
    $authUrl = $client->createAuthUrl();
    header('Location: ' . $authUrl);
    exit();
}



?>