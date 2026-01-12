<?php

require_once '../../sessionstart.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout.php");
}
require_once '../../loginvalidate.php';
require_once '../../config/database.php';
require_once '../../application/pages/function.php';
$old_doc = preg_replace("/[^0-9 ]/", "", $_POST['old_doc']);
$log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$to','Storage file $old_doc printed','$date',null,'$host','')") or die('error : ' . mysqli_error($db_con));
//for user role


?>
    
       
                      
              