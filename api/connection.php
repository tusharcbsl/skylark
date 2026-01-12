<?php
error_reporting(E_ALL&~E_WARNING&~E_NOTICE&~E_DEPRECATED);

print_r($_POST);
if(isset($_POST['apikey']) && !empty($_POST['apikey'])){
$dbhost='localhost';
$dbuser='root';
$dbpwd='password';
$key=$_POST['apikey'];
// $decKey=decryptLicenseKey($key);
// $decKey= explode("%", $decKey);
//$dbname=$decKey[0];
$dbname="ezeeoffice";


//error_reporting(E_ALL);
$con= mysqli_connect($dbhost, $dbuser, $dbpwd, $dbname)or die("unable to connect");
define('FTP_ENABLED',true);
define('FILE_SERVER','144.48.78.35');
define('PORT',21);
define('FTP_USER','testing');
define('FTP_PASS','Cbsl@%$#@');

$levelResult = mysqli_query($con, "select sl_name from tbl_storage_level where sl_depth_level = '0'") or die('Error:' . mysqli_error($con));
$level = mysqli_fetch_assoc($levelResult);

define('ROOT_FTP_FOLDER', $level['sl_name']);

}else{

    echo json_encode(array('Error'=>'API Key Missing'));

    die();
} 

// function decryptLicenseKey($licenseKey){
        
//         $key = '987654123';
//         $data = base64_decode($licenseKey);
//         $iv = substr($data, 0, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC));

//         $decrypted = rtrim(
//             mcrypt_decrypt(
//                 MCRYPT_RIJNDAEL_128,
//                 hash('sha256', $key, true),
//                 substr($data, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC)),
//                 MCRYPT_MODE_CBC,
//                 $iv
//             ),
//             "\0"
//         );

//         return $decrypted;
// }        
?>
