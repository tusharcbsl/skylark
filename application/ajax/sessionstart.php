<?php

// diabling direct access of file
if ($_SERVER['REQUEST_METHOD'] == 'GET' && realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME'])) {
    header('HTTP/1.0 403 Forbidden', TRUE, 403);
    die(header('location:../../error.html'));
}
//disable end

$projectName = "EzeePea";
$projectLogo = "ezeefile.jpg";
$dbHost = "192.168.2.109";
$dbUser = "root";
$dbPwd = "php123";
$dbName = "ezeefiledms";
$key = "1234";
//comment by mukesh becoz it create all pages updates options error
//require './application/config/database.php';
//$clientCheck= mysqli_query($db_con, "select * from ");
if ($key == '1234') {
//ini_set('session.cookie_httponly', 1);
//
//// **PREVENTING SESSION FIXATION**
//// Session ID cannot be passed through URLs
//ini_set('session.use_only_cookies', 1);
//
//// Uses a secure connection (HTTPS) if possible
//ini_set('session.cookie_secure', 1);

    ob_start();


    @session_start();
} else {
    
}
?>