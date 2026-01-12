<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once './application/config/database.php';
$bkp_id = intval(base64_decode(urldecode($_GET['bkp_id'])));
//$bkp_id=68;
if (isset($bkp_id) && !empty($bkp_id)) {
    $sql = "select * from tbl_db_backup_log where backup_type='Scheduled' and id='$bkp_id'";
    //  echo $sql; die;
    $res = mysqli_fetch_assoc(mysqli_query($db_con, $sql));
    //print_r($res); die;
    if (!empty($res['backup_name'])) {
        $db_filepath = "db_file/" . $res['backup_name'];
        $cmd = "mysql -h $dbHost -u $dbUser -p$dbPwd dms_test < $db_filepath";
        //echo $cmd; die;
        $resp = exec($cmd);
       // var_dump($resp);
        if($resp==''){
            $msg="Database Restored Successfully";
        }
    }
}



