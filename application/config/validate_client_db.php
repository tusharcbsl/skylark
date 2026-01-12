<?php

// diabling direct access of file
if ($_SERVER['REQUEST_METHOD'] == 'GET' && realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME'])) {
    header('HTTP/1.0 403 Forbidden', TRUE, 403);
    die(header('location:../../error.html'));
}
//disable end
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


error_reporting(0);
require 'conf.php';
//This file contains the database access information. It will included on every file requiring database access.
date_default_timezone_set("Asia/Kolkata");
$date = date("Y-m-d H:i:s");
//db con 
$db_valid_con = @mysqli_connect($dbHost, $dbUser, $dbPwd, $mainDbName) OR die('could not connect:' . mysqli_connect_error());
// db con
if (!empty($_SESSION['custom_ip'])) {
    $host = $host . '/' . $_SESSION[custom_ip];
}
?>
