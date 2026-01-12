<?php

if ( $_SERVER['REQUEST_METHOD']=='GET' && realpath(__FILE__) == realpath( $_SERVER['SCRIPT_FILENAME'] ) ) {
        header( 'HTTP/1.0 403 Forbidden', TRUE, 403 );
        die( header( 'location:../../error.html' ) );
    }
require_once('../../sessionstart.php');
require_once '../config/database.php';
date_default_timezone_set("Asia/Kolkata");
if($_POST['lgt']==2){
if(isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 900)) {
    //$fdate=$time+(600);'0000-00-00 00:00:00'
    //$stamp = date('Y-m-d h:i:sa',$fdate);
    //header('location: logout');
    echo'2';
}else{
    
    echo '1';
   // echo $date;
    $_SESSION['LAST_ACTIVITY'] = time();
    $update1= mysqli_query($db_con, "update tbl_user_master set last_activity='$date' where user_id='$_SESSION[cdes_user_id]'") or die('Error: '. mysqli_error($db_con));
    $update2=mysqli_query($db_con, "update tbl_user_master set current_login_status='0',last_active_logout=(last_activity+INTERVAL 15 MINUTE) where current_login_status='1' and (last_activity < NOW() - INTERVAL 15 MINUTE)") or die('Error: '. mysqli_error($db_con));
    $update3= mysqli_query($db_con, "update tbl_ezeefile_logs set end_date='$date' where action_name='Login/Logout' and end_date is null and (start_date < NOW() - INTERVAL 15 MINUTE)") or die('Error: '. mysqli_error($db_con));
}
}else{
    if(isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 900)) {
  
    echo'2';
    }else{
       // echo $date;
        echo '1';
    }
}

mysqli_close($db_con);

?>
