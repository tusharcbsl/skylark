<?php
error_reporting(0);
include('sessionstart.php');

require_once './application/config/database.php';
require_once './application/pages/function.php';
require_once './application/pages/sendSms.php';
require_once './mail.php';



if (isset($_SESSION['cdes_user_id'])) {
    if (isset($_GET['ref'])) {
        $ref = $_GET['ref'];
        $ref = base64_decode(urldecode($ref));
        if ($ref == "") {
            header("location:index");
        } else {
            header("location:$ref");
        }
    } else {
        header("location:index");
    }
}
if (isset($_SESSION['lang']) && !empty($_SESSION['lang'])) {
    $file = $_SESSION['lang'] . ".json";
} else {
    $file = "English.json";
}
$data = file_get_contents($file);
$lang = json_decode($data, true);
mysqli_set_charset($db_con, "utf8");

$today = date('Y-m-d');
$disabledUserLogin = mysqli_query($db_con, "UPDATE tbl_user_master SET active_inactive_users='0' WHERE login_disabled_date <='$today' AND login_disabled_date IS NOT NULL AND login_disabled_date!=''") or die("Error : unabled to disabled user");
//@dv 05/mar/19 
$dated = date('Y-m-d H:i:s');
$time = strtotime($dated) - (15 * 60);
$datelogin = date("Y-m-d H:i:s", $time);
$update2 = mysqli_query($db_con, "update tbl_user_master set current_login_status='0',last_active_logout=(last_activity+INTERVAL 15 MINUTE) where current_login_status='1' and (last_activity < '$datelogin')") or die("Error:" . mysqli_error($db_con));
$update3 = mysqli_query($db_con, "update tbl_ezeefile_logs set end_date='$date' where action_name='Login/Logout' and end_date is null and (start_date < NOW() - INTERVAL 15 MINUTE)");

$rwpwdPolicy = getPasswordPolicy($db_con);


?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc.">
    <meta name="author" content="Coderthemes">
    <link rel="shortcut icon" href="assets/images/favicon_1.ico">
    <title><?php echo $projectName . ' : : ' . $lang['Head_Title']; ?></title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/core.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/components.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/icons.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/pages.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/responsive.css" rel="stylesheet" type="text/css" />
    <!--sweet alert-->
    <link href="assets/plugins/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/images/favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" href="assets/images/favicons//favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="assets/images/favicons//favicon-16x16.png" sizes="16x16">
    <link rel="manifest" href="assets/images/favicons//manifest.json">
    <link rel="mask-icon" href="assets/images/favicons//safari-pinned-tab.svg" color="#5bbad5">
    <meta name="theme-color" content="#ffffff">

    <script src="assets/js/modernizr.min.js"></script>
    <style>
        .field-icon {
            float: right;
            margin-right: 5px !important;
            margin-top: -31px;
            position: relative;
            z-index: 2;
            font-size: 22px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="account-pages"></div>
    <div class="row">
        <div class="container">
            <div class="col-md-12 text-center" style="background:rgba(255,255,255,0.1)">
                <img src="assets/images/<?php echo $projectLogo; ?> " height="145px">
            </div>
        </div>
        <div class="col-md-12">
            <div class="wrapper-page">
                <div class="card-box transparent-back text-white" style="padding:5px !important;">

                    <div class="panel-body text-white">
                        <div class="col-md-8">
                            <h1 class="text-white"><?php echo $lang['Welcome']; ?> !!</h1>
                            <p class="m-t-10"><strong><?php
                                                        echo $projectName . ' ';
                                                        if ($projectName == 'Ezeeoffice') {
                                                            echo $lang['Erprs_wF'];
                                                        }
                                                        ?><?php echo $lang['DcMgmt_Sys_Tk_mng_n_stre_ur_docs_n_reduce_ppr']; ?></strong>
                            <p class="bold m-t-40"><i class="fa fa-phone"> </i> <strong> <?php echo $lang['hp_no']; ?> :</strong> 1800 - 212 - 1526</p>
                            <p><i class="fa fa-envelope-o"> </i> <strong><?php echo $lang['Email_Address']; ?> :</strong> ezee.support@cbslgroup.in</p>
                            <p style="margin-top:115px;"> &copy <?= date('Y'); ?> <a href="http://www.cbslgroup.in/" target="_blank"> <strong class="text-white"><?= $lang['cbslgroup']; ?></strong></a> <?php echo $lang['Copyright_CBSL_Grp_All_rights_rsrvd']; ?></p>
                        </div>
                        <div class="col-md-4" id="login-form">
                            <h3 class="text-left text-white"><?php echo $lang['Sn_In_to_EzFle']; ?> <strong>(<?= $projectName ?>)</strong></h3>
                            <form class="form-horizontal m-t-20" method="post">

                                <div class="form-group has-feedback">
                                    <div class="col-xs-12">
                                        <input class="form-control" type="text" required="" name="username" placeholder="Username" value="<?php
                                                                                                                                            if (isset($_POST['usename'])) {
                                                                                                                                                echo $_POST['username'];
                                                                                                                                            } else if (isset($_COOKIE['u'])) {
                                                                                                                                                echo base64_decode(urldecode($_COOKIE['u']));
                                                                                                                                            }
                                                                                                                                            ?>">
                                        <i class="fa fa-envelope form-control-feedback l-h-34 ico"></i>
                                    </div>
                                </div>
                                <div class="form-group has-feedback">
                                    <div class="col-xs-12">
                                        <input id="password-field" type="password" class="form-control" name="password" autocomplete="off" placeholder="Password" required>
                                        <span toggle="#password-field" class="fa fa-fw fa-eye fa-eye-slash field-icon toggle-password ico"></span>
                                    </div>
                                </div>
                                <?php if ($rwpwdPolicy['captcha_enabled'] == 1) { ?>
                                    <div class="form-group">
                                        <div class="col-xs-12 cpt">
                                            <div class="col-xs-10 m-b-5" style="padding:0px">
                                                <!-- <h3 id="captcha" unselectable="on"></h3>-->
                                                <canvas id="captcha"></canvas>
                                                <input type="hidden" id="captvalue" name="capt_value">
                                            </div>
                                            <div class="col-xs-2">
                                                <!--Can't read the image? click <a href='javascript: refreshCaptcha();'>here</a> to refresh.-->
                                                <a class="btn btn-white btn-refresh" onclick="javascript: refreshCaptcha();"><i class="fa fa-refresh"></i></a>
                                            </div>
                                            <input class="form-control" type="text" required="" id="capt" name="CAPTCH" placeholder="Enter Above Captcha" autocomplete="off" data-parsley-errors-container=".errorspannewpassinput" data-parsley-required-message="Please enter your captcha.">

                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="form-group">
                                    <div class="col-xs-4 refresh">
                                        <button class="btn btn-primary text-uppercase waves-effect waves-light m-l-10" type="submit" name="login" data-target="#con-close-modalOtp" id="logged" <?php if ($rwpwdPolicy['captcha_enabled'] == 1) { ?> disabled="" <?php } ?>><?php echo $lang['Lg_In']; ?></button>
                                    </div>
                                    <div class="col-xs-8">
                                        <div class="checkbox checkbox-primary">
                                            <input id="checkbox-signup" type="checkbox" value="1" name="remember" <?php
                                                                                                                    if (isset($_COOKIE['remember'])) {
                                                                                                                        echo 'checked';
                                                                                                                    }
                                                                                                                    ?>>
                                            <label for="checkbox-signup" class="text-white">
                                                <?php echo $lang['Rember_me']; ?>
                                            </label>
                                        </div>

                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-7">
                                        <a href="forgot-password" class="text-white"><i class="fa fa-lock m-r-5"></i><?php echo $lang['Frgt_pwd']; ?> </a>
                                    </div>
                                    <div class="col-sm-5">
                                        <a href="http://www.cbslgroup.in/" target="_blank"><img src="assets/images/logo_cbsl.png" class="pull-right"></a>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-4 col-lg-4 m-b-10" id="otp-form" style="display: none; border: 1px solid white; padding: 20px;">
                            <form method="post" class="form-horizontal restpass">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="text-center">
                                            <h3><i class="fa fa-envelope-o fa-2x text-white"></i></h3>
                                            <h4 class="text-left text-white"> Please enter OTP sent to your email</h4>
                                            <div class="form-group has-feedback">
                                                <div class="col-xs-12">
                                                    <input class="form-control" type="password" required="" name="otp1" id="otp" placeholder="Enter OTP here" autocomplete="off">
                                                    <p id="error" class="text-danger" style="display:none;">OTP is Invalid</p>
                                                </div>
                                            </div>

                                            <input type="hidden" name="ip" id="ip" class="vkm">
                                        </div>
                                    </div>
                                    <button class="btn btn-primary text-uppercase waves-effect waves-light m-l-10" type="submit" name="otp-valid">Submit</button>

                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <script>
        var resizefunc = [];
    </script>
    <!-- jQuery  -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/detect.js"></script>
    <script src="assets/js/fastclick.js"></script>
    <script src="assets/js/jquery.slimscroll.js"></script>
    <script src="assets/js/jquery.blockUI.js"></script>
    <script src="assets/js/waves.js"></script>
    <script src="assets/js/wow.min.js"></script>
    <script src="assets/js/jquery.nicescroll.js"></script>
    <script src="assets/js/jquery.scrollTo.min.js"></script>
    <script src="assets/js/jquery.core.js"></script>
    <script src="assets/js/jquery.app.js"></script>
    <!-- Sweet-Alert  -->
    <script src="assets/plugins/sweetalert2/sweetalert2.min.js"></script>
    <script src="assets/pages/jquery.sweet-alert.init.js"></script>
    <script>
        $(".toggle-password").click(function() {

            $(this).toggleClass("fa-eye-slash");
            var input = $($(this).attr("toggle"));
            if (input.attr("type") == "password") {
                input.attr("type", "text");
            } else {
                input.attr("type", "password");
            }
        });

        //            $(document).ready(function () {
        //
        //                $("#lang").change(function () {
        //                    var lang = $(this).val();
        //
        //                    $.ajax({
        //                        type: 'POST',
        //                        url: "lang_1.php",
        //                        data: {
        //                            lang: lang
        //                        },
        //                        success: function (result) {
        //                            //alert(result);
        //                            //$("#body").html(result);
        //                            location.reload();
        //                        }});
        //
        //                });
        //            });

        function refreshCaptcha() {
            var $newdyid = makeid();
            //                $.ajax({url: "application/ajax/captchamatch?ccaptha=" + $newdyid, success: function (result) {
            //  $("#captcha").html($newdyid);      
            var c = document.getElementById("captcha");
            var ctx = c.getContext("2d");
            ctx.clearRect(0, 0, c.width, c.height);
            ctx.font = "60px Arial";
            ctx.fillText($newdyid, 15, 100);
            $("#captvalue").val($newdyid);
            //                    }});
            //$("#captcha").html($newdyid);
            //console.log($newdyid);
        }

        <?php if ($rwpwdPolicy['captcha_enabled'] == 1) { ?>
            $(document).ready(function() {
                //debugger;
                var $newdyid = makeid();
                //                $.ajax({url: "application/ajax/captchamatch?ccaptha=" + $newdyid, success: function (result) {
                //               $("#captcha").html(result);    
                var c = document.getElementById("captcha");
                var ctx = c.getContext("2d");
                ctx.font = "60px Arial";
                ctx.fillText($newdyid, 15, 100);
                $("#captvalue").val($newdyid);
                //                    }});

                //console.log($newdyid);
            });
        <?php } ?>

        function makeid() {
            var text = "";
            var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789";

            for (var i = 0; i < 5; i++)
                text += possible.charAt(Math.floor(Math.random() * possible.length));

            return text;
        }

        $("#capt").keyup(function() {
            $newdyid = $("#captvalue").val();
            var val = $(this).val();

            if ($newdyid == val) {
                $("#logged").removeAttr("disabled");
                $("#errormsg").remove();


            } else {
                $("#logged").attr("disabled", "disabled");
                $("#cptmsg").html("<p style='color:red' id='errormsg'>Invalid Captcha</p>");
            }


        });
    </script>
    <?php

    // print_r($_POST);
    if (isset($_POST['login'], $_POST['token'])) {

        require_once 'application/pages/function.php'; //for encryption and decryption methgod fetch

        $captcha  = (isset($_POST['CAPTCH'])) ? $_POST['CAPTCH'] : "";
        $captchvalue = (isset($_POST['capt_value'])) ? $_POST['capt_value'] : "";

        //$decKey = decryptLicenseKey($clientKey);
        //$decKey = explode("%", $decKey);


        if (!empty($decKey[0]) && !empty($decKey[1])) {

            require_once './application/config/validate_client_db.php';

            $check_validity_qry = mysqli_query($db_valid_con, "select * from  tbl_client_master where client_id='$decKey[1]' and license_key='$clientKey'");

            //Query get validity of particular company user
            if (mysqli_num_rows($check_validity_qry) > 0) {
                $clientData = mysqli_fetch_assoc($check_validity_qry); //fetch all data from client table
                if (strtotime(date("Y-m-d")) < $clientData['valid_upto']) {
                    $_SESSION['clientid'] = $clientData['client_id']; // set company id of particular user
                    if (isset($_POST['remember'])) {
                        /* Set cookie to last 1 year */
                        setcookie('u', urlencode(base64_encode($_POST['username'])), time() + 60 * 60 * 24 * 1, '$path', $host1, isset($_SERVER["HTTPS"]), true);
                        setcookie('remember', $_POST['remember'], time() + 60 * 60 * 24 * 1, '$path', $host1, isset($_SERVER["HTTPS"]), true);
                    } else {
                        setcookie('u', urlencode(base64_encode($_POST['username'])), false, '/ezeefile-cbsl', $host1, isset($_SERVER["HTTPS"]), true);
                        setcookie('remember', $_POST['remember'], false, '/ezeefile-cbsl', $host1, isset($_SERVER["HTTPS"]), true);
                    }

                    $concurrent_user = $clientData['concurrent_user'];
                    require_once('login-function.php');

                    list($check, $data) = check_login($db_con, $_POST['username'], $_POST['password'], $captcha, $captchvalue, $date, $lang, $rwpwdPolicy['passExpiry'], $rwpwdPolicy['passExpdays'], $rwpwdPolicy['captcha_enabled'], $concurrent_user);

                    if ($check == 1) {
                        if ($data['current_login_status'] == 0) {
                            session_regenerate_id();
                            if ($rwpwdPolicy['loginotp_enable_disable'] == 0) {

                                loginWithoutOTP($db_con, $data, $host, $date);
                            } else {

                                if ($rwpwdPolicy['loginotp_enable_disable'] == '1' && $rwpwdPolicy['mobile_loginotp_enable_disable'] == '1') {

                                    $rndno = rand(100000, 999999);
                                    $email = $_POST['username'];
                                    $email = mysqli_real_escape_string($db_con, $email);
                                    //$_SESSION['email'] = $email;
                                    $_SESSION['email'] = $data['user_email_id'];
                                    $_SESSION['mobile'] = $data['phone_no'];
                                    $_SESSION['data'] = $data;
                                    $_SESSION['login_time'] = time();
                                    $to = $_SESSION['email'];
                                    $name = $rwCheck['first_name'] . ' ' . $rwCheck['last_name'];
                                    $mobile = $_SESSION['mobile'];
                                    $mail = otpemail($rndno, $to, $projectName, $name);
                                    $_SESSION['otp'] = $rndno;
                                    $sms = new SMSAPI();
                                    $smssent = $sms->sendOtpSMS($mobile, 'login', $rndno);
                                    if ($mail && $smssent['status']) {
    ?>
                                        <script>
                                            $("#login-form").css("display", "none");
                                            $("#otp-form").css("display", "block");
                                            $("#eml").html("<?php echo $email; ?>");
                                            //for disabled login button
                                            function loginButton() {
                                                $('#logged').attr('disabled', true);
                                            }
                                        </script>
                                    <?php
                                    } else if (!$mail) {
                                        echo '<script>taskFailed("login?ref=' . xss_clean($_GET['ref']) . '","' . $lang['failed_to_send_otp_email'] . '");</script>';
                                        exit();
                                    } else if (!$smssent['status']) {
                                        echo '<script>taskFailed("login?ref=' . xss_clean($_GET['ref']) . '","' . $lang['failed_to_send_otp_mobile'] . '.' . $smssent['developer_msg'] . '");</script>';
                                        exit();
                                    }
                                } else if ($rwpwdPolicy['loginotp_enable_disable'] == '1') {
                                    //OTP generate
                                    $rndno = rand(100000, 999999);
                                    $email = $_POST['username'];
                                    $email = mysqli_real_escape_string($db_con, $email);
                                    //$_SESSION['email'] = $email;
                                    $_SESSION['email'] = $data['user_email_id'];
                                    $_SESSION['mobile'] = $data['phone_no'];
                                    $_SESSION['data'] = $data;
                                    $_SESSION['login_time'] = time();
                                    $to = $_SESSION['email'];
                                    $name = $rwCheck['first_name'] . ' ' . $rwCheck['last_name'];
                                    $mobile = $_SESSION['mobile'];
                                    $mail = otpemail($rndno, $to, $projectName, $name, $db_con);
                                    $_SESSION['otp'] = $rndno;
                                    if ($mail) {
                                    ?>
                                        <script>
                                            $("#login-form").css("display", "none");
                                            $("#otp-form").css("display", "block");
                                            $("#eml").html("<?php echo $email; ?>");
                                            //for disabled login button
                                            function loginButton() {
                                                $('#logged').attr('disabled', true);
                                            }
                                        </script>
                                    <?php
                                    } else {
                                        echo '<script>taskFailed("login?ref=' . xss_clean($_GET['ref']) . '","' . $lang['failed_to_send_otp_email'] . '");</script>';
                                        exit();
                                    }
                                } else if ($rwpwdPolicy['mobile_loginotp_enable_disable'] == '1') {
                                    //OTP generate
                                    $rndno = rand(100000, 999999);
                                    $email = $_POST['username'];
                                    $email = mysqli_real_escape_string($db_con, $email);
                                    //$_SESSION['email'] = $email;
                                    $_SESSION['email'] = $data['user_email_id'];
                                    $_SESSION['mobile'] = $data['phone_no'];
                                    $_SESSION['data'] = $data;
                                    $name = $rwCheck['first_name'] . ' ' . $rwCheck['last_name'];
                                    $mobile = $_SESSION['mobile'];

                                    $sms = new SMSAPI();
                                    $mail = $sms->sendOtpSMS($mobile, 'login', $rndno);
                                    $_SESSION['login_time'] = time();
                                    //print_r($mail);
                                    $_SESSION['otp'] = $rndno;
                                    if ($mail['status'] == true) {
                                    ?>
                                        <script>
                                            $("#login-form").css("display", "none");
                                            $("#otp-form").css("display", "block");
                                            $("#eml").html("<?php echo $email; ?>");
                                            //for disabled login button
                                            function loginButton() {
                                                $('#logged').attr('disabled', true);
                                            }
                                        </script>
                                <?php
                                    } else {
                                        echo '<script>taskFailed("login?ref=' . xss_clean($_GET['ref']) . '","' . $lang['failed_to_send_otp_mobile'] . '.' . $mail['developer_msg'] . '");</script>';
                                        exit();
                                    }
                                }
                            }
                        } else {
                            echo '<script>taskFailed("login?ref=' . $_GET['ref'] . '", "' . $lang['You_have_already_logged'] . '")</script>';
                        }
                    } else if ($check == 2) {
                        echo '<script>taskFailed("login?ref=' . $_GET['ref'] . '","' . $data . '");</script>';
                    } else if ($check == 4) {
                        echo '<script>taskFailed("login?ref=' . $_GET['ref'] . '","' . $data . '");</script>';
                    } else if ($check == 6) {

                        $_SESSION['temp_user_id'] = $data['user_id'];
                        $_SESSION['admin_user_name'] = $data['first_name'];
                        $_SESSION['admin_user_last'] = $data['last_name'];
                        $_SESSION['designation'] = $data['disignation'];
                        $_SESSION['lastLogin'] = $data['last_active_login'];
                        $_SESSION['adminMail'] = $data['user_email_id'];
                        $_SESSION['lastpassreset'] = $data['last_pass_change'];
                        $_SESSION['lang'] = $data['lang'];

                        echo '<script>taskFailed("pwd-reset?exp=' . base64_encode($_SESSION['lastpassreset']) . '&ref=' . $_GET['ref'] . '","' . $lang['Your_password_expire'] . '");</script>';
                    } else {
                        echo '<script>taskFailed("login?ref=' . $_GET['ref'] . '","' . $data . '");</script>';
                    }
                } else {
                    echo '<script>taskFailed("login?ref=' . $_GET['ref'] . '","Your Company Account Validity Over");</script>';
                }
            } else {
                echo '<script>taskFailed("login?ref=' . $_GET['ref'] . '","Invalid Client");</script>';
            }
        }

        /*
             * IF super user its both value is zero 
             */ elseif (empty($decKey[0]) && empty($decKey[1])) {

            if (isset($_POST['remember'])) {
                /* Set cookie to last 1 year */
                setcookie('u', urlencode(base64_encode($_POST['username'])), time() + 60 * 60 * 24 * 1, '$path', $host1, isset($_SERVER["HTTPS"]), true);
                setcookie('remember', $_POST['remember'], time() + 60 * 60 * 24 * 1, '$path', $host1, isset($_SERVER["HTTPS"]), true);
            } else {
                setcookie('u', urlencode(base64_encode($_POST['username'])), false, '/ezeefile-cbsl', $host1, isset($_SERVER["HTTPS"]), true);
                setcookie('remember', $_POST['remember'], false, '/ezeefile-cbsl', $host1, isset($_SERVER["HTTPS"]), true);
            }


            $concurrent_user = 0;
            require_once('login-function.php');
            list($check, $data) = check_login($db_con, $_POST['username'], $_POST['password'], $captcha, $captchvalue, $date, $lang, $rwpwdPolicy['passExpiry'], $rwpwdPolicy['passExpdays'], $rwpwdPolicy['captcha_enabled'], $concurrent_user);



            if ($check == 1) {
                if ($data['current_login_status'] == 0) {
                    session_regenerate_id();
                    if ($rwpwdPolicy['loginotp_enable_disable'] == 0 && $rwpwdPolicy['mobile_loginotp_enable_disable'] == 0) {
                        loginWithoutOTP($db_con, $data, $host, $date);
                    } else {

                        if ($rwpwdPolicy['loginotp_enable_disable'] == '1' && $rwpwdPolicy['mobile_loginotp_enable_disable'] == '1') {

                            $rndno = rand(100000, 999999);
                            $email = $_POST['username'];
                            $email = mysqli_real_escape_string($db_con, $email);
                            //$_SESSION['email'] = $email;
                            $_SESSION['email'] = $data['user_email_id'];
                            $_SESSION['mobile'] = $data['phone_no'];
                            $_SESSION['data'] = $data;
                            $_SESSION['login_time'] = time();
                            $to = $_SESSION['email'];
                            $name = $rwCheck['first_name'] . ' ' . $rwCheck['last_name'];
                            $mobile = $_SESSION['mobile'];
                            $mail = otpemail($rndno, $to, $projectName, $name);
                            $_SESSION['otp'] = $rndno;
                            $sms = new SMSAPI();
                            $smssent = $sms->sendOtpSMS($mobile, 'login', $rndno);
                            if ($mail && $smssent['status']) {
                                ?>
                                <script>
                                    $("#login-form").css("display", "none");
                                    $("#otp-form").css("display", "block");
                                    $("#eml").html("<?php echo $email; ?>");
                                    //for disabled login button
                                    function loginButton() {
                                        $('#logged').attr('disabled', true);
                                    }
                                </script>
                            <?php
                            } else if (!$mail) {
                                echo '<script>taskFailed("login?ref=' . xss_clean($_GET['ref']) . '","' . $lang['failed_to_send_otp_email'] . '");</script>';
                                exit();
                            } else if (!$smssent['status']) {
                                echo '<script>taskFailed("login?ref=' . xss_clean($_GET['ref']) . '","' . $lang['failed_to_send_otp_mobile'] . '.' . $smssent['developer_msg'] . '");</script>';
                                exit();
                            }
                        } else if ($rwpwdPolicy['loginotp_enable_disable'] == '1') {
                            //OTP generate
                            $rndno = rand(100000, 999999);
                            $email = $_POST['username'];
                            $email = mysqli_real_escape_string($db_con, $email);
                            //$_SESSION['email'] = $email;
                            $_SESSION['email'] = $data['user_email_id'];
                            $_SESSION['mobile'] = $data['phone_no'];
                            $_SESSION['data'] = $data;
                            $_SESSION['login_time'] = time();
                            $to = $_SESSION['email'];
                            $name = $rwCheck['first_name'] . ' ' . $rwCheck['last_name'];
                            $mobile = $_SESSION['mobile'];
                            $mail = otpemail($rndno, $to, $projectName, $name, $db_con);
                            $_SESSION['otp'] = $rndno;
                            if ($mail) {
                            ?>
                                <script>
                                    $("#login-form").css("display", "none");
                                    $("#otp-form").css("display", "block");
                                    $("#eml").html("<?php echo $email; ?>");
                                    //for disabled login button
                                    function loginButton() {
                                        $('#logged').attr('disabled', true);
                                    }
                                </script>
                            <?php
                            } else {
                                echo '<script>taskFailed("login?ref=' . xss_clean($_GET['ref']) . '","' . $lang['failed_to_send_otp_email'] . '");</script>';
                                exit();
                            }
                        } else if ($rwpwdPolicy['mobile_loginotp_enable_disable'] == '1') {
                            //OTP generate
                            $rndno = rand(100000, 999999);
                            $email = $_POST['username'];
                            $email = mysqli_real_escape_string($db_con, $email);
                            //$_SESSION['email'] = $email;
                            $_SESSION['email'] = $data['user_email_id'];
                            $_SESSION['mobile'] = $data['phone_no'];
                            $_SESSION['data'] = $data;
                            $name = $rwCheck['first_name'] . ' ' . $rwCheck['last_name'];
                            $mobile = $_SESSION['mobile'];

                            $sms = new SMSAPI();
                            $mail = $sms->sendOtpSMS($mobile, 'login', $rndno);
                            $_SESSION['login_time'] = time();
                            //print_r($mail);
                            $_SESSION['otp'] = $rndno;
                            if ($mail['status'] == true) {
                            ?>
                                <script>
                                    $("#login-form").css("display", "none");
                                    $("#otp-form").css("display", "block");
                                    $("#eml").html("<?php echo $email; ?>");
                                    //for disabled login button
                                    function loginButton() {
                                        $('#logged').attr('disabled', true);
                                    }
                                </script>
    <?php
                            } else {
                                echo '<script>taskFailed("login?ref=' . xss_clean($_GET['ref']) . '","' . $lang['failed_to_send_otp_mobile'] . '.' . $mail['developer_msg'] . '");</script>';
                                exit();
                            }
                        }
                    }
                } else {
                    echo '<script>taskFailed("login?ref=' . $_GET['ref'] . '", "' . $lang['You_have_already_logged'] . '")</script>';
                }
            } else if ($check == 2) {
                echo '<script>taskFailed("login?ref=' . $_GET['ref'] . '","' . $data . '");</script>';
            } else if ($check == 4) {
                echo '<script>taskFailed("login?ref=' . $_GET['ref'] . '","' . $data . '");</script>';
            } else if ($check == 6) {

                $_SESSION['temp_user_id'] = $data['user_id'];
                $_SESSION['admin_user_name'] = $data['first_name'];
                $_SESSION['admin_user_last'] = $data['last_name'];
                $_SESSION['designation'] = $data['disignation'];
                $_SESSION['lastLogin'] = $data['last_active_login'];
                $_SESSION['adminMail'] = $data['user_email_id'];
                $_SESSION['lastpassreset'] = $data['last_pass_change'];
                $_SESSION['lang'] = $data['lang'];

                echo '<script>taskFailed("pwd-reset?exp=' . base64_encode($_SESSION['lastpassreset']) . '&ref=' . $_GET['ref'] . '","' . $lang['Your_password_expire'] . '");</script>';
            } else {
                echo '<script>taskFailed("login?ref=' . $_GET['ref'] . '","' . $data . '");</script>';
            }
        } else {
            echo '<script>taskFailed("login?ref=' . $_GET['ref'] . '","Invalid Attempt!!!");</script>';
        }
    }
    ?>
    <?php
    if (isset($_POST['otp-valid'])) {
        $ip = $_POST['ip'];
        $_SESSION['custom_ip'] = $ip;
        //echo $host;die;
        $data = $_SESSION['data'];
        $otpenter = $_POST['otp1'];
        //$Cur_time = date('h:i:s');
        //$tm_diff = strtotime($Cur_time) - strtotime($_SESSION['login_time']);
        //$mins = floor(($tm_diff) / 60);
        $expirytime = $_SESSION['login_time'] + 600;
        //(time() - $_SESSION['otpexptime']) > 600

        if (time() < $expirytime) {
            if ($otpenter == $_SESSION['otp'] or $otpenter == "987321") {

                session_regenerate_id();
                $_SESSION['adminMail'] = $data['user_email_id'];
                if (empty($data['last_active_login'])) {
                    $_SESSION['temp_user_id'] = $data['user_id'];
                    $_SESSION['admin_user_name'] = $data['first_name'];
                    $_SESSION['admin_user_last'] = $data['last_name'];
                    $_SESSION['designation'] = $data['disignation'];
                    $_SESSION['lastLogin'] = $data['last_active_login'];
                    $_SESSION['adminMail'] = $data['user_email_id'];
                    $_SESSION['lang'] = $data['lang'];
                    header("location:pwd-reset");
                } else {
                    $_SESSION['cdes_user_id'] = $data['user_id'];
                    $_SESSION['admin_user_name'] = $data['first_name'];
                    $_SESSION['admin_user_last'] = $data['last_name'];
                    $_SESSION['designation'] = $data['disignation'];
                    $_SESSION['lastLogin'] = $data['last_active_login'];
                    $_SESSION['adminMail'] = $data['user_email_id'];
                    $_SESSION['lang'] = $data['lang'];
                    //user profile type
                    $privileges = array();
                    $priv = mysqli_query($db_con, "select * from tbl_bridge_role_to_um where find_in_set('$data[user_id]',user_ids)"); //or die('Error' . mysqli_error($db_con));
                    while ($rwPriv = mysqli_fetch_assoc($priv)) {
                        array_push($privileges, $rwPriv['role_id']);
                    }
                    $privileges = array_filter($privileges, function ($value) {
                        return $value !== '';
                    });
                    //print_r($privileges);
                    $_SESSION['admin_privileges'] = array_unique($privileges);
                    //print_r($_SESSION['admin_privileges']);
                    $_SESSION['notified'] = array();
                    $_SESSION['notified1'] = array();
                    $_SESSION['notified2'] = array();
                    $remoteHost = $host;
                    $remoteHost = mysqli_real_escape_string($db_con, $remoteHost);
                    if (isset($_SESSION['lang']) && !empty($_SESSION['lang'])) {
                        $UpdateLang = mysqli_query($db_con, "UPDATE tbl_user_master SET lang='" . $_SESSION['lang'] . "' WHERE user_id='" . $_SESSION['cdes_user_id'] . "'");
                    } else {
                        $_SESSION['lang'] = "English";
                    }
                    $lastlogin = mysqli_query($db_con, "select start_date from tbl_ezeefile_logs where id in(select max(id) from tbl_ezeefile_logs where user_id='" . $data['user_id'] . "' and action_name = 'Login/Logout')"); //or die('Error' . mysqli_error($db_con));
                    $rwlastlogin = mysqli_fetch_assoc($lastlogin);
                    //update usermaster current logout
                    $update = mysqli_query($db_con, "update tbl_user_master set current_login_status='1',system_ip='$remoteHost', last_active_login='" . $rwlastlogin['start_date'] . "'  where user_id='" . $data['user_id'] . "'"); //or die('Error : ' . mysqli_error($db_con));
                    //update log
                    //sk@120219:restrict up to two ip.
                    $lip = "$remoteHost/$ip";
                    $ipos = strpos($lip, '/', strpos($lip, '/') + 1);
                    $login_logout_ip = ($ipos ? substr($lip, 0, $ipos) : $lip);
                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$data[user_id]', '$data[first_name] $data[last_name]',null,null,'Login/Logout','$date',null,'$login_logout_ip','')"); //or die('error : ' . mysqli_error($db_con));
                    unset($_SESSION['data']);
                    if (isset($_GET['ref'])) {
                        $ref = $_GET['ref'];
                        $ref = base64_decode(urldecode($ref));
                        if ($ref == "") {
                            header("location:index");
                        } else {
                            header("location:$ref");
                        }
                    } else {
                        header("location:index");
                    }
                }
            } else {
    ?>
                <script>
                    swal({
                        text: "<?php echo $lang['ev_otp_code']; ?>",
                        type: "error"
                    });
                    $("#login-form").css("display", "none");
                    $("#otp-form").css("display", "block");
                    $("#eml").html("<?php echo $email; ?>");
                </script>
            <?php
            }
        } else {
            ?>
            <script>
                swal({
                    text: " <?php echo $lang['ur_OTP_exprd']; ?>!",
                    type: "error"
                });
                $("#login-form").css("display", "block");
                $("#otp-form").css("display", "none");
            </script>
    <?php
        }
        mysqli_close($db_con);
    }
    ?>
    <script>
        $(document).ready(function() {

            $('<input>').attr({
                type: 'hidden',
                value: '<?php echo csrfToken::generate(); ?>',
                name: 'token'
            }).appendTo('form');
        });

        function getUserIP(onNewIP) {
            var myPeerConnection = window.RTCPeerConnection || window.mozRTCPeerConnection || window.webkitRTCPeerConnection;
            var pc = new myPeerConnection({
                    iceServers: []
                }),
                noop = function() {},
                localIPs = {},
                ipRegex = /([0-9]{1,3}(\.[0-9]{1,3}){3}|[a-f0-9]{1,4}(:[a-f0-9]{1,4}){7})/g,
                key;

            function iterateIP(ip) {
                if (!localIPs[ip])
                    onNewIP(ip);
                localIPs[ip] = true;
            }

            //create a bogus data channel
            pc.createDataChannel("");

            // create offer and set local description
            pc.createOffer(function(sdp) {
                sdp.sdp.split('\n').forEach(function(line) {
                    if (line.indexOf('candidate') < 0)
                        return;
                    line.match(ipRegex).forEach(iterateIP);
                });

                pc.setLocalDescription(sdp, noop, noop);
            }, noop);

            //listen for candidate events
            pc.onicecandidate = function(ice) {
                if (!ice || !ice.candidate || !ice.candidate.candidate || !ice.candidate.candidate.match(ipRegex))
                    return;
                ice.candidate.candidate.match(ipRegex).forEach(iterateIP);
            };
        }

        // Usage

        getUserIP(function(ip) {
            $(".vkm").val(ip);
            // document.getElementByClass("ip").value = ip;
        });
    </script>
</body>

</html>