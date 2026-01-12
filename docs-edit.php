
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

// echo'ok';
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
    $fileId=$_POST['fileId'];
    $perm=$_POST['perm'];
    // echo $fname = $_POST['fileName'];exit;
    if($perm=="writer"){

       
        $file = $service->files->get($fileId);
        $fileName = $file->getName();

        $fname = explode('.', $fileName);

        $filenameEnct = time() . $fname[0];
        $filenameEnct = urlencode(base64_encode($filenameEnct));
        $filenameEnct = preg_replace('/[^A-Za-z0-9_\-]/', '', $filenameEnct);
        $filenameEnct = $filenameEnct . '.' . $fname[1];
        $uploaddir = 'temp/docs/'.$filenameEnct;

        // Download a file.
        $content = $service->files->get($fileId, array("alt" => "media"));

        $handle = fopen($uploaddir, "w+"); // Modified
        while (!$content->getBody()->eof()) { // Modified
            fwrite($handle, $content->getBody()->read(1024)); // Modified
        }
        fclose($handle);
        echo "https://royankit.ezeepea.com/".$uploaddir;
    }

} else {
    $authUrl = $client->createAuthUrl();
    header('Location: ' . $authUrl);
    exit();
}



?>