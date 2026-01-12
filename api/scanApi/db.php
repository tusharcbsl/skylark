<?php

error_reporting(E_ALL&~E_WARNING&~E_NOTICE);
//error_reporting(E_ALL);
$con= mysqli_connect($dbhost, $dbuser, $dbpwd, $dbname)or die("unable to connect");
define('FTP_ENABLED',true);
define('FILE_SERVER','13.126.104.102');
define('PORT',22);
define('FTP_USER','cbsl');
define('FTP_PASS','Cbsl@123');

//$levelResult = mysqli_query($con, "select sl_name from tbl_storage_level where sl_depth_level = '0'") or die('Error:' . mysqli_error($con));
//$level = mysqli_fetch_assoc($levelResult);
//define('ROOT_FTP_FOLDER', $level['sl_name']);
        
        
?>
