<?php
require_once './application/config/database.php';
require_once './loginvalidate.php';
if(isset($_POST['updaten'])){
    // $_SESSION['lang'] = $_POST['lang'];
    // $UpdateLang=mysqli_query($db_con,"UPDATE tbl_user_master SET lang='$_SESSION[lang]' WHERE user_id='$_SESSION[cdes_user_id]'");
    $uid = $_SESSION['cdes_user_id'];
    $sql = "SELECT * FROM `tbl_alert_notifications` WHERE `to_user_id` = ".$uid. " ORDER BY created_at DESC";
    $query = mysqli_query($db_con, $sql);
    while ($res = mysqli_fetch_assoc($query)) 
    {
        if($res['read_at'] != "")
            {
                continue;
            }
                date_default_timezone_set("Asia/Kolkata");
                $val = date("Y-m-d h:m:s");
                $currr_id=$res['id'];
                $qq = mysqli_query($db_con, "UPDATE tbl_alert_notifications SET read_at='$val' WHERE id='$currr_id'");
    }
}

