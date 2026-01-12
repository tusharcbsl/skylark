<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
$projectName = 'EzeePea';
$projectLogo = 'ezeefile.jpg';
include('sessionstart.php');
require_once './application/pages/sendSms.php';

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
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc.">
        <meta name="author" content="Coderthemes">
        <link rel="shortcut icon" href="assets/images/favicon_1.ico">
        <title><?php echo $projectName; ?>  :: Document Management System & Workflow</title>
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

    </head>
    <body>

        <div class="account-pages"></div>
        <div class="clearfix"></div>
        <div class="row m-t-10">
            <div class="container">
                <div class="col-md-12 text-center" style="background:rgba(255,255,255,0.1)">
                    <img src="assets/images/<?php echo $projectLogo; ?> " height="145px" >
                </div>

            </div>
            <div class="col-md-12">
                <div class="wrapper-page">
                    <div class=" card-box transparent-back text-white">

                        <div class="panel-body text-white">
                            <div class="col-md-8">
                                <h1 class="text-white">Welcome!</h1>
                                <p class="m-t-10"><strong><?php echo $projectName; ?>  Enterprise workFlow & Document Management System.</strong><br>
                                    Track, manage and store your documents and reduce paper.</p>
                                <p class="bold m-t-40">Help Desk - Tel no. : 011-47547700 <br> Email Id : ezeefileadmin@cbsl-india.com</p>
                            </div>
                            <div class="col-md-4" id="login-form">
                                <h3 class="text-left text-white"> Sign In to <strong class="text-custom"><?php echo $projectName; ?> </strong> </h3>
                                <form class="form-horizontal m-t-20" method="post">

                                    <div class="form-group has-feedback">
                                        <div class="col-xs-12">
                                            <input class="form-control" type="email" required name="username" placeholder="Username" parsley-trigger="change" value="<?php
                                            if (isset($_POST['usename'])) {
                                                echo $_POST['username'];
                                            } else if (isset($_COOKIE['username'])) {
                                                echo base64_decode(urldecode($_COOKIE['username']));
                                            }
                                            ?>" autocomplete="off">
                                            <i class="fa fa-envelope form-control-feedback l-h-34"></i>
                                        </div>
                                    </div>

                                    <div class="form-group has-feedback">
                                        <div class="col-xs-12">
                                            <input type="password" class="form-control" id="pwd"    data-parsley-errors-container=".errorspannewpassinput"    data-parsley-required-message="Please enter your password."     name="password" placeholder="Password" autocomplete="off">
                                            <i class="fa fa-lock form-control-feedback l-h-34"></i>
                                        </div>
                                    </div>

                                    <div class="form-group ">
                                        <div class="col-xs-4">
                                            <input type="hidden" name="ip" id="ip" class="vkm" >
                                            <button class="btn btn-primary text-uppercase waves-effect waves-light" type="submit" name="login" data-target="#con-close-modalOtp">Log In</button>
                                        </div>
                                        <div class="col-xs-8">
                                            <div class="checkbox checkbox-primary">
                                                <input id="checkbox-signup" type="checkbox" value="1" name="remember" <?php
                                                if (isset($_COOKIE['remember'])) {
                                                    echo 'checked';
                                                }
                                                ?>>
                                                <label for="checkbox-signup">
                                                    Remember me
                                                </label>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="form-group m-t-30 m-b-0">
                                        <div class="col-sm-12">
                                            <a href="forgot-password" class="text-white"><i class="fa fa-lock m-r-5"></i> Forgot your password?</a>
                                        </div>
                                    </div>
                                </form> 
                            </div>
                            <div class="col-md-4" id="otp-form" style="display:none">
                                <h3 class="text-left text-white"> Please Enter OTP sent to <strong id="mob"></strong></h3>
                                <form class="form-horizontal m-t-20" method="post">

                                    <div class="form-group has-feedback">
                                        <div class="col-xs-12">
                                            <input class="form-control" type="text" required="" name="otp1" placeholder="Enter OTP here" >
                                            <p id="error" class="text-danger" style="display:none;">OTP is Invalid</p>
                                        </div>
                                    </div>
                                    <div class="form-group ">
                                        <div class="col-xs-4">
                                            <button class="btn btn-primary text-uppercase waves-effect waves-light" type="submit" name="otp-valid">Submit</button>
                                        </div>

                                    </div>

                                </form> 
                            </div>
                            <div class="col-md-12 ">
                                Copyright © <?php echo date('Y'); ?> <a href="http://cbslgroup.in" class="text-white">CBSL Group</a>. All rights reserved
                                <img src="assets/images/logo_cbsl.png" class="pull-right">

                            </div>
                            <div class="form-group">
<!--                                <center> <button class="btn btn-default" href="javascript:void(0)" data-toggle="modal" data-target="#con-close-modal4"> FAQ</button> </center> -->
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
        <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>
        <script>
            $(document).ready(function () {
                $('form').parsley();
                //has uppercase


            });
        </script>
        <?php
        require_once './application/config/database.php';
        if (isset($_POST['login'])) {
            $ip = $_POST['ip'];
            if (isset($_POST['remember'])) {
                /* Set cookie to last half year */
                setcookie('username', urlencode(base64_encode($_POST['username'])), time() + 60 * 60 * 24 * 6, "$path", $host1, isset($_SERVER["HTTPS"]), true);
                setcookie('remember', $_POST['remember'], time() + 60 * 60 * 24 * 365, "$path", $host1, isset($_SERVER["HTTPS"]), true);
            } else {
                setcookie('username', urlencode(base64_encode($_POST['username'])), false, '/ezeefile-cbsl', $host1, isset($_SERVER["HTTPS"]), true);
                setcookie('remember', $_POST['remember'], false, '/ezeefile-cbsl', $host1, isset($_SERVER["HTTPS"]), true);
            }

            require_once('login-function.php');

            list($check, $data) = check_login($db_con, $_POST['username'], $_POST['password']);

            if ($check) {
                session_regenerate_id();
                /*
                  $_SESSION['data'] = $data;
                  $ph = substr($data['phone_no'], -4);
                 * 
                 */
                ?>
                             <!--   <script>
                                    $("#login-form").css("display", "none");
                                    $("#otp-form").css("display", "block");
                                    $("#mob").html("******<?php //echo $ph;   ?>");
                                </script> -->
                <?php
                /*
                  $string = '0123456789';
                  $string_shuffled = str_shuffle($string);
                  $otp = substr($string_shuffled, 0, 4);
                  $_SESSION['otp'] = $otp;
                  echo $_SESSION['otp'];
                  /* if(isset($_SESSION['otp_time']) && (time() - $_SESSION['otp_time'] > 1800)) {
                  $fdate=$time+(600);
                  $stamp = date('Y-m-d h:i:sa',$fdate);

                  }else{
                  $_SESSION['otp_time'] = time();
                  } */
                /*  $msgOtp = 'Your OTP is : ' . $otp;
                  $sendMsgToMbl = smsgatewaycenter_com_Send($data['phone_no'], $msgOtp, $debug = false);
                  } else {
                  echo '<script> loginfailed("index");</script>';
                  }
                  }
                  if (isset($_POST['otp-valid'])) {
                  $data = $_SESSION['data'];
                  $ph = substr($data['phone_no'], -4);
                 */
                ?>
                       <!--     <script>
                                $("#login-form").css("display", "none");
                                $("#otp-form").css("display", "block");
                                $("#mob").html("******<?php //echo $ph;  ?>");
                            </script> -->
                <?php
                //echo $_SESSION['otp'];
                // $otpenter = $_POST['otp1'];
                //echo $_SESSION['otp'];
                // $otp=1418;
                // if ($otpenter == $_SESSION['otp'] or $otp==$otpenter){
                //$_SESSION['adminMail']=$data['user_email_id'];
                //echo 'll'. $data['last_active_login'];    print_r($data); die();
                if (empty($data['last_active_login'])) {
                    header("location:pwd-reset");
                    $_SESSION['cdes_user_id'] = $data['user_id'];
                    $_SESSION['admin_user_name'] = $data['first_name'];
                    $_SESSION['admin_user_last'] = $data['last_name'];
                    $_SESSION['designation'] = $data['designation'];
                    $_SESSION['designation'] = $data['disignation'];
                    $_SESSION['lastLogin'] = $data['last_active_login'];
                    $_SESSION['adminMail'] = $data['user_email_id'];
                } else {
                    $_SESSION['cdes_user_id'] = $data['user_id'];
                    $_SESSION['admin_user_name'] = $data['first_name'];
                    $_SESSION['admin_user_last'] = $data['last_name'];
                    $_SESSION['designation'] = $data['designation'];
                    $_SESSION['designation'] = $data['disignation'];
                    $_SESSION['lastLogin'] = $data['last_active_login'];
                    $_SESSION['adminMail'] = $data['user_email_id'];
                    //user profile type
                    $privileges = array();
                    $priv = mysqli_query($db_con, "select * from tbl_bridge_role_to_um where find_in_set('$data[user_id]',user_ids)") or die('Error' . mysqli_error($db_con));
                    while ($rwPriv = mysqli_fetch_assoc($priv)) {
                        array_push($privileges, $rwPriv['role_id']);
                    }
                    $privileges = array_filter($privileges, function($value) {
                        return $value !== '';
                    });
                    //print_r($privileges);
                    $_SESSION['admin_privileges'] = array_unique($privileges);
                    //       print_r($_SESSION['admin_privileges']);
                    $_SESSION['notified'] = array();
                    $_SESSION['notified1'] = array();
                    $_SESSION['notified2'] = array();
                    $remoteHost = $_SERVER['REMOTE_ADDR'];
                    $remoteHost = mysqli_real_escape_string($db_con, $remoteHost);

                    $lastlogin = mysqli_query($db_con, "select start_date from tbl_ezeefile_logs where id in ( select max(id) from tbl_ezeefile_logs where user_id = '$data[user_id]' and action_name = 'Login/Logout')") or die('Error' . mysqli_error($db_con));
                    $rwlastlogin = mysqli_fetch_assoc($lastlogin);
                    //update usermaster current logout
                    $update = mysqli_query($db_con, "update tbl_user_master set current_login_status='1',system_ip='$remoteHost', last_active_login='$rwlastlogin[start_date]'  where user_id='$data[user_id]'") or die('Error : ' . mysqli_error($db_con));
                    //update log
                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$data[user_id]', '$data[first_name] $data[last_name]',null,null,'Login/Logout','$date',null,'$remoteHost/$ip','')") or die('error : ' . mysqli_error($db_con));

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
                //  echo '<script>$("#error").css("display","block");</script>';
                echo '<script> loginfailed("index");</script>';
            }
            mysqli_close($db_con);
        }
        ?> 
        <script>
            $('input[type="text"]').keyup(function ()
            {
                var GrpNme = $(this).val();
                re = /[`~!#$%^&*()|+\=?;:'",<>\{\}\[\]\\\/]/gi;
                var isSplChar = re.test(GrpNme);
                if (isSplChar)
                {
                    var no_spl_char = GrpNme.replace(/[`~!#$%^&*()|+\=?;:'",<>\{\}\[\]\\\/]/gi, '');
                    $(this).val(no_spl_char);
                }
            });
            $('input[type="text"]').bind(function () {
                $(this).val($(this).val().replace(/[<>]/g, ""))
            });
        </script>
    </body>
</html>


