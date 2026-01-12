<?php

function findChildss($lockslId) {
    global $db_con;
    global $slChild;
    $sql_child = "select * FROM tbl_storage_level WHERE sl_parent_id = '$lockslId' order by sl_name asc";
    $sql_child_run = mysqli_query($db_con, $sql_child) or die('Error:' . mysqli_error($db_con));
    if (mysqli_num_rows($sql_child_run) > 0) {
        while ($rwchild = mysqli_fetch_assoc($sql_child_run)) {
            $child = $rwchild['sl_id'];

            findChildss($child);
        }
    }
    $slChild[] = $lockslId;

    return $slChild;
}

/* --for lock folder-- */
if (isset($_POST['lock'])) {
    $lockfolder = $_POST['lockfolder'];
    $lockslId = $_POST['lockslId'];
    $strgChlid = findChildss($lockslId);
    $allChilds = implode(',', $strgChlid);


    $check = mysqli_query($db_con, "select * from tbl_storage_level where is_protected ='1' or is_protected='2'");
    $checkStorage = mysqli_fetch_assoc($check);
    $protected_slid = $checkStorage['sl_id'];


    $sql1 = mysqli_query($db_con, "UPDATE `tbl_storage_level` set is_protected = '1', password=sha1('$lockfolder'),user_id = '$_SESSION[cdes_user_id]' where sl_id in($allChilds)")or die('Error DB child: ' . mysqli_error($db_con));

    $sql2 = mysqli_query($db_con, "UPDATE `tbl_storage_level` set is_protected = '2',user_id = '$_SESSION[cdes_user_id]' where sl_id ='$lockslId'")or die('Error DB child: ' . mysqli_error($db_con));

    if ($sql1 && $sql2) {

        echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Folder_locked_successfully'] . '");</script>';
    }
}

/* * *** Folder Unlock **** */
if (isset($_POST['unlock'])) {
    $lockslId = $_POST['lockslId'];
    $strgChlid = findChildss($lockslId);
    $allChilds = implode(',', $strgChlid);

    $pass = $_POST['unlockfolder'];

    $password = $abs['password'];

    $fpass = SHA1($pass);
    if ($password == $fpass) {

        $unlock = mysqli_query($db_con, "UPDATE `tbl_storage_level` set is_protected = '0', password=NULL,user_id=NULL where sl_id IN ($allChilds)")or die(mysqli_error($db_con));

        echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['unlock_folder_msg'] . '");</script>';
    } else {
        echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['unable_unlock_folder_msg'] . '");</script>';
    }
}

//forgot password
if (isset($_POST['forgotPassword'])) {

    $user_id = $_SESSION['cdes_user_id'];
    mysqli_set_charset($db_con, "utf8");
    $chkUserMail = mysqli_query($db_con, "select * from tbl_user_master where user_id='$user_id'") or die('Error:' . mysqli_error($db_con));
    if (mysqli_num_rows($chkUserMail) > 0) {
        $rwCheck = mysqli_fetch_assoc($chkUserMail);
        $email = $rwCheck['user_email_id'];
        $rndno = rand(100000, 999999); //OTP generate
        $to = $email;
        $name = $rwCheck['first_name'] . ' ' . $rwCheck['last_name'];
        $txt = $rndno;
        require_once './mail.php';
        $mail = mailPasschange($txt, $to, $projectName, $name);

        $_SESSION['otp'] = $rndno;
        $_SESSION['forgottime'] = time();
        if ($mail) {
            echo'<script>swal({
								
								text: "' . $lang['vcss'] . '",
								type: "success"
							}).then(function(){
								
								$("#resetpassword").modal("show");
							});
							</script>';
        }
    } else {
        echo'<script>swal({
								title: "Failed",
								text: "Please enter valid email id.",
								type: "warning",
								showCancelButton: true
							}).then(function(){
								$("#forgot-password").modal("show");
							});</script>';
    }
}

//reset password	
if (isset($_POST['resetPass'])) {

    $pass = $_POST['paswd'];
    $pass = mysqli_real_escape_string($db_con, $pass);
    $otp = $_POST['otp'];
    $otp = mysqli_real_escape_string($db_con, $otp);
    $to = $_SESSION['adminMail'];

    $lockslId = $_POST['lockslId'];
    $storage = $_POST['folder'];
    $strgChlid = findChildss($lockslId);
    $allChilds = implode(',', $strgChlid);
    $expirytime = $_SESSION['forgottime']+600;
    if(time() < $expirytime) {
        if (!strcmp($otp, $_SESSION['otp'])) {

            $Resetpass = mysqli_query($db_con, "UPDATE `tbl_storage_level` set password=sha1('$pass') where sl_id IN($allChilds)")or die(mysqli_error($db_con));
            mysqli_set_charset($db_con, "utf8");
            $chkUser = mysqli_query($db_con, "select * from tbl_user_master where user_email_id='$to '") or die('Error:' . mysqli_error($db_con));

            $rwUser = mysqli_fetch_assoc($chkUser);
            $username = $rwUser['first_name'] . ' ' . $rwUser['last_name'];
            if ($Resetpass) {
                require_once './mail.php';
                $mail = mailResetPassFolder($to, $pass, $storage, $username);

                if ($mail) {
                    echo'<script> taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","'.$lang['prs'].'");</script>';
                } else {
                    echo'<script>swal({
								
								text: "' . $lang['ftc'] . '",
								type: "warning",
								showCancelButton: true
							}).then(function() {
								$("#resetpassword").modal("show");
							});</script>';
                }
            }

            unset($_SESSION['otp']);
        } else {
            echo'<script>swal({
								
								text: "' . $lang['ev_otp_code'] . '",
								type: "warning",
								showCancelButton: true
							}).then(function() {
								$("#resetpassword").modal("show");
							});</script>';
        }
    } else {
        
        echo'<script>swal({
								
								text: "' . $lang['ur_OTP_exprd'] . '",
								type: "warning",
								showCancelButton: true
							}).then(function() {
								$("#forgot-password").modal("show");
							});</script>';
        
    }
}
?>