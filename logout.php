<?php

include('sessionstart.php');

if (!isset($_SESSION['cdes_user_id'])) {
    //require_once('login-function.php');
    //$url=absolute_url();
    //header("Location: $url");
    //exit();
} else {
    require './application/config/database.php';
    $logUpdate = mysqli_query($db_con, "call logout(".$_SESSION['cdes_user_id'].",'$date')") or die('Error' . mysqli_error($db_con));
    unset($_SESSION['admin_first_name']);
    unset($_SESSION['admin_last_name']);
    unset($_SESSION['cdes_user_id']);
    unset($_SESSION['designation']);
    unset($_SESSION['admin_privileges']);
    //unset($_SESSION['notified']);
    //unset($_SESSION['notified1']);
    //unset($_SESSION['notified2']);
    unset($_SESSION['custom_ip']);
    unset($_SESSION['fpstring']);
    session_regenerate_id();
}
$url = 'index';
header("Location: $url");
?>