<?php

echo "<pre>";

error_reporting(0);
require '/var/www/ezeefile_saas/application/config/conf.php';

$db_connect = mysqli_connect($dbHost, $dbUser, $dbPwd, $dbName) or die("Connection Error" . mysqli_connect_error());
$time = time();
//$client = mysqli_query($db_connect, "SELECT db_name FROM `tbl_client_master` where valid_upto > '$time' and db_name not in ('DMS_C_N_Patel___Company_1578301300','DMS_Casa__Stays_Pvt_Ltd_1590995478')") or die(mysqli_error($db_connect));
$client = mysqli_query($db_connect, "SELECT db_name FROM `tbl_client_master` where valid_upto > '$time' and db_name in ('DMS_Niit_1625044599')") or die(mysqli_error($db_connect));
while ($rwClient = mysqli_fetch_assoc($client)) {
    //echo $rwClient['db_name'];
    //echo "<br>";
    $db_con = mysqli_connect($dbHost, $dbUser, $dbPwd, $rwClient['db_name']);
    $storage = mysqli_query($db_con, "SELECT sl_name FROM `tbl_storage_level` where sl_parent_id=0");
    $rwStorage = mysqli_fetch_assoc($storage);
    $rootStorage = $rwStorage['sl_name'];
    $check = mysqli_query($db_con, "select doc_id,doc_path from tbl_document_master where flag_multidelete=1");
    while ($rwCheck = mysqli_fetch_assoc($check)) {
       echo $path = "/home/niit-bucket/" . $rootStorage . "/" . $rwCheck['doc_path'];
        if (file_exists($path)) {
            $update = mysqli_query($db_con, "update tbl_document_master set ftp_exists=1 where doc_id='$rwCheck[doc_id]'");
        }else{
            $update = mysqli_query($db_con, "update tbl_document_master set ftp_exists=0 where doc_id='$rwCheck[doc_id]'");
        }
    }
}

mysqli_close($db_con);
?>
