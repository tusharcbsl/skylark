<?php

require_once '../sessionstart.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout.php");
}
//require_once '../../loginvalidate.php';
require '../application/config/database.php';
$docid = base64_decode(urldecode($_POST['docid']));
$docName=$_POST['docname'];
$slid=$_POST['slid'];
$remark=$_POST['remark'];
$log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`,`doc_id`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$slid','Document $docName printed or tried to printed by $_SESSION[adminMail]','$date',null,'$host','$remark','$docid')") or die('error : ' . mysqli_error($db_con));
//for user role


      
    
       
                      
              