<?php

//This will redirect the user to the login page, if they haven't logged in. 
//UNIX_TIMESTAMP(UserLastLogin) < UNIX_TIMESTAMP(DATE_SUB('$date', INTERVAL 60 DAY))
//SELECT * FROM `queries` WHERE `QUERY_DATE` > DATE_SUB(NOW(), INTERVAL 24 HOUR) AND `QUERY_DATE` <= NOW()
include('./application/config/database.php');
date_default_timezone_set("Asia/Kolkata");
include('sessionstart.php');
$loggedInuserId = $_SESSION['cdes_user_id'];
if (!isset($_SESSION['cdes_user_id'])) {
    require_once('login-function.php');
    //$ref = substr(strrchr($_SERVER['PHP_SELF'], '/'), 1);
    $ref = basename($_SERVER['PHP_SELF']); //die;

    $ref = substr($ref, 0, strpos($ref, '.php'));
    $ref = urlencode(base64_encode($ref));
    $url = absolute_url('./login?ref=' . $ref);
    header("Location: $url");
    exit();
}
//$privileges = $_SESSION['admin_privileges'];
?>
<?php

//check user is loggedin and if any other user deactivate login user it will logout automatically.
//$checklogin = mysqli_query($db_con, "SELECT * FROM `tbl_user_master` WHERE user_id='" . $_SESSION['cdes_user_id'] . "'") or die('error : ' . mysqli_error($db_con));
//$rwchecklogin = mysqli_fetch_array($checklogin);
//if ($rwchecklogin['current_login_status'] == '1' && $rwchecklogin['active_inactive_users'] == '0') {
//    mysqli_set_charset($db_con, "utf8");
//    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`, `action_name`, `start_date`, `system_ip`, `remarks`) values ('" . $_SESSION['cdes_user_id'] . "', '" . $_SESSION['admin_user_name'] . ' ' . $_SESSION['admin_user_last'] . "','Logout','$date', '$host','User logout due to anyone deactivate $_SESSION[admin_user_name] $_SESSION[admin_user_last].')") or die('error :' . mysqli_error($db_con));
//    $update = mysqli_query($db_con, "update tbl_user_master set current_login_status='0', last_active_logout='$date' where user_id='" . $_SESSION['cdes_user_id'] . "'") or die('error : ' . mysqli_error($db_con));
//    $lastlogin = mysqli_query($db_con, "select id from tbl_ezeefile_logs where id in(select max(id) from tbl_ezeefile_logs where user_id = '" . $_SESSION['cdes_user_id'] . "' and action_name = 'Login/Logout')") or die('Error' . mysqli_error($db_con));
//    $rwlastlogin = mysqli_fetch_assoc($lastlogin);
//    $logUpdate = mysqli_query($db_con, "update tbl_ezeefile_logs set end_date='$date' where id='$rwlastlogin[id]'") or die('Error' . mysqli_error($db_con));
//    unset($_SESSION['admin_first_name']);
//    unset($_SESSION['admin_last_name']);
//    unset($_SESSION['cdes_user_id']);
//    unset($_SESSION['designation']);
//    unset($_SESSION['admin_privileges']);
//    unset($_SESSION['custom_ip']);
//    unset($_SESSION['fpstring']);
//    session_regenerate_id();
//    if (!isset($_SESSION['cdes_user_id'])) {
//        require_once('login-function.php');
//        $ref = basename($_SERVER['PHP_SELF']);
//
//        $ref = substr($ref, 0, strpos($ref, '.php'));
//        $ref = urlencode(base64_encode($ref));
//        $url = absolute_url('./login?ref=' . $ref);
//        header("Location: $url");
//        exit();
//    }
//}

$loginStatus = mysqli_query($db_con, "call sp_UserStatus('" . $_SESSION['cdes_user_id'] . "','$host')");
mysqli_next_result($db_con);
$loginRw = mysqli_fetch_assoc($loginStatus);
if ($loginRw['errFlag'] == 1) {
    unset($_SESSION['admin_first_name']);
    unset($_SESSION['admin_last_name']);
    unset($_SESSION['cdes_user_id']);
    unset($_SESSION['designation']);
    unset($_SESSION['admin_privileges']);
    unset($_SESSION['custom_ip']);
    unset($_SESSION['fpstring']);
    session_regenerate_id();
    if (!isset($_SESSION['cdes_user_id'])) {
        require_once('login-function.php');
        $ref = basename($_SERVER['PHP_SELF']);
        $ref = substr($ref, 0, strpos($ref, '.php'));
        $ref = urlencode(base64_encode($ref));

        $url = absolute_url('./login?ref=' . $ref);

        header("Location: $url");
        exit();
    }
}

//for checking user permission don't remove this code used in every page
$chekUsr = mysqli_query($db_con, "call commoninallpages(" . $_SESSION['cdes_user_id'] . ",'" . $loginRw['lang'] . "')") or die('Error:' . mysqli_error($db_con));
mysqli_next_result($db_con);
$rwgetRole = mysqli_fetch_assoc($chekUsr);

if (isset($_SESSION['lang'])) {
    $file = $_SESSION['lang'] . ".json";
    if (!file_exists($file)) {
        $file = "English.json";
    }
} else {
    if (isset($_SESSION['cdes_user_id'])) {
        mysqli_set_charset($db_con, "utf8");
        if (!empty($loginRw['lang'])) {
            $file = $loginRw['lang'] . ".json";
        } else {
            $file = "English.json";
        }
    }
}
$data = file_get_contents($file);
$lang = json_decode($data, true);
//for checking password policy

?>