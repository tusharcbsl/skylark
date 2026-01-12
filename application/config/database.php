<?php
// diabling direct access of file
 if ( $_SERVER['REQUEST_METHOD']=='GET' && realpath(__FILE__) == realpath( $_SERVER['SCRIPT_FILENAME'] ) ) {
        header( 'HTTP/1.0 403 Forbidden', TRUE, 403 );
        die( header( 'location:../../error.html' ) );
    }
    //disable end
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// ini_set('display_errors', 1); 
// ini_set('display_startup_errors', 1); 

error_reporting(0);

require_once 'conf.php';

if (session_status() == PHP_SESSION_NONE) {
    //require_once '../../sessionstart.php';
    require_once 'sessionstart.php';
}

//This file contains the database access information. It will included on every file requiring database access.
date_default_timezone_set("Asia/Kolkata");
$date=date("Y-m-d H:i:s");
//db con 
$db_con = @mysqli_connect($dbHost,$dbUser,$dbPwd,$dbName) OR die ('could not connect:' . mysqli_connect_error());
// db con

$levelResult = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_depth_level = '0'") or die('Error:' . mysqli_error($db_con));
$level = mysqli_fetch_assoc($levelResult);
//define('ROOT_FTP_FOLDER', 'DMS/'.$level['sl_name']);
define('ROOT_FTP_FOLDER', $level['sl_name']);
if(!empty($_SESSION['custom_ip'])){
    $host=$host.'/'.$_SESSION['custom_ip'];
}

$extenResult = mysqli_query($db_con, "select name from tbl_file_extensions where status = '1' order by name asc") or die('Error:' . mysqli_error($db_con));
$extArray = [];
 while ($ext = mysqli_fetch_assoc($extenResult)){
 	$extArray[] = $ext['name'];
 }

 //print_r($extArray);

//define('ALLOWED_EXTN', array('png', 'jpg', 'jpeg', 'gif', 'tiff', 'odt', 'rtf', 'bmp', 'tif', 'pdf', 'doc', 'docx', 'txt',  'xls', 'xlxs', 'csv', 'mp3', 'mp4', '3gp', 'mkv', 'zip', 'rar'));

 define('ALLOWED_EXTN', $extArray);
 
$emaildata = mysqli_query($db_con, "SELECT * FROM  `tbl_email_configuration_credential`") or die('Could not get data: ' . mysqli_error($db_con));
$emailcredential = mysqli_fetch_assoc($emaildata);

 define('EMAIL_HOST', $emailcredential['host_name']);
 define('EMAIL_PORT', $emailcredential['port_number']);
 define('EMAIL_USERNAME', base64_decode($emailcredential['username']));
 define('EMAIL_PASSWORD', base64_decode($emailcredential['password']));
 define('EMAIL_SETFROM', $emailcredential['setfrom']);
 define('storage_letter_id', '1977');
 define('storage_rfi_id', '1978');
?>
