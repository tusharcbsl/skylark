<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
$projectName = 'EzeeFile';
$projectLogo = 'ezeefile.png';
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
if (isset($_SESSION['lang'])) {
    $file = $_SESSION['lang'] . ".json";
} else {
    $file = "English.json";
}
$data = file_get_contents($file);
$lang = json_decode($data, true);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc.">
    <meta name="author" content="Coderthemes">
    <link rel="shortcut icon" href="assets/images/favicon_1.ico">
    <title><?php echo $projectName; ?> :: Document Management System & Workflow</title>
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
            <ul class="nav navbar-nav navbar-right pull-right">
                <li>
                    <select name="lang" id="lang" class="lang">
                        <option><?php echo $lang['Ch_lang']; ?></option>

                        <option <?php
                                if ($_SESSION['lang'] == "Hindi") {
                                    echo 'Selected';
                                }
                                ?> value="Hindi"><?php echo $lang['Hindi']; ?></option>
                        <option <?php
                                if ($_SESSION['lang'] == "English") {
                                    echo 'Selected';
                                }
                                ?> value="English"> <?php echo $lang['English']; ?> </option>
                    </select>
                </li>
            </ul>
            <div class="col-md-12 text-center" style="background:rgba(255,255,255,0.1)">
                <a href="login?ref=aW5kZXgucGhw"><img src="assets/images/<?php echo $projectLogo; ?> " height="145px"></a>
            </div>

        </div>
        <div class="col-md-12">
            <div class="wrapper-page">
                <div class=" card-box transparent-back text-white">

                    <div class="panel-body text-white">
                        <div class="col-md-8">
                            <h1 class="text-white"><?php echo $lang['Welcome']; ?></h1>
                            <p class="m-t-10"><strong><?php echo $projectName;
                                                        if ($projectName == 'EzeePea') {
                                                            echo $lang['Erprs_wF'];
                                                        } ?><?php echo $lang['DcMgmt_Sys_Tk_mng_n_stre_ur_docs_n_reduce_ppr']; ?>
                                    <p class="bold m-t-40"><?php echo $lang['hp_no']; ?> : 011-47547700 <br> <?php echo $lang['Email_Address']; ?> : ezeefileadmin@cbsl-india.com</p>
                        </div>
                        <div class="col-md-4" id="login-form">
                            <h3 class="text-left text-white"><?php echo $lang['Sn_In_to_EzFle'] ?></h3>
                            <form class="form-horizontal m-t-20" method="post">

                                <div class="form-group has-feedback">
                                    <div class="col-xs-12">
                                        <input class="form-control" type="text" required="" name="username" placeholder="Username" value="<?php
                                                                                                                                            if (isset($_POST['usename'])) {
                                                                                                                                                echo $_POST['username'];
                                                                                                                                            } else if (isset($_COOKIE['username'])) {
                                                                                                                                                echo base64_decode(urldecode($_COOKIE['username']));
                                                                                                                                            }
                                                                                                                                            ?>">
                                        <i class="fa fa-envelope form-control-feedback l-h-34"></i>
                                    </div>
                                </div>

                                <div class="form-group has-feedback">
                                    <div class="col-xs-12">
                                        <input class="form-control" type="password" required="" name="password" placeholder="Password" autocomplete="off">
                                        <i class="fa fa-lock form-control-feedback l-h-34"></i>
                                    </div>
                                </div>

                                <div class="form-group ">
                                    <div class="col-xs-4">
                                        <button class="btn btn-primary text-uppercase waves-effect waves-light" type="submit" name="login" data-target="#con-close-modalOtp"><?php echo $lang['Lg_In']; ?></button>
                                    </div>
                                    <div class="col-xs-8">
                                        <div class="checkbox checkbox-primary">
                                            <input id="checkbox-signup" type="checkbox" value="1" name="remember" <?php
                                                                                                                    if (isset($_COOKIE['remember'])) {
                                                                                                                        echo 'checked';
                                                                                                                    }
                                                                                                                    ?>>
                                            <label for="checkbox-signup">
                                                <?php echo $lang['Rember_me']; ?>
                                            </label>
                                        </div>

                                    </div>
                                </div>
                                <div class="form-group m-t-30 m-b-0">
                                    <div class="col-sm-12">
                                        <a href="forgot-password" class="text-white"><i class="fa fa-lock m-r-5"></i><?php echo $lang['Frgt_pwd']; ?></a>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-4" id="otp-form" style="display:none">
                            <h4 class="text-left text-white"><?php echo $lang['Pls_ntr_OTP_snt_to_ur_eml']; ?><span id="eml"></span></h4>
                            <form class="form-horizontal m-t-20" method="post">

                                <div class="form-group has-feedback">
                                    <div class="col-xs-12">
                                        <input class="form-control" type="text" required="" name="otp1" id="otp" placeholder="<?php echo $lang['Etr_OTP_hre']; ?>">
                                        <p id="error" class="text-danger" style="display:none;"><?php echo $lang['OTP_is_Invlid']; ?></p>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="col-xs-4">
                                        <button class="btn btn-primary text-uppercase waves-effect waves-light" type="submit" name="otp-valid"><?php echo $lang['Submit']; ?></button>
                                    </div>

                                </div>

                            </form>
                        </div>
                        <div class="col-md-12 ">
                            <?php echo $lang['Copyright_CBSL_Grp_All_rights_rsrvd']; ?>
                            <img src="assets/images/logo_cbsl.png" class="pull-right">

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
    <?php
    require_once './application/config/database.php';

    if (isset($_POST['login'])) {

        if (isset($_POST['remember'])) {
            /* Set cookie to last 1 year */
            setcookie('username', urlencode(base64_encode($_POST['username'])), time() + 60 * 60 * 24 * 365, "$path", $host1, isset($_SERVER["HTTPS"]), true);
            setcookie('remember', $_POST['remember'], time() + 60 * 60 * 24 * 365, "$path", $host1, isset($_SERVER["HTTPS"]), true);
        } else {
            setcookie('username', urlencode(base64_encode($_POST['username'])), false, '/ezeefile-cbsl', $host1, isset($_SERVER["HTTPS"]), true);
            setcookie('remember', $_POST['remember'], false, '/ezeefile-cbsl', $host1, isset($_SERVER["HTTPS"]), true);
        }

        require_once('login-function.php');

        list($check, $data) = check_login($db_con, $_POST['username'], $_POST['password']);
        if ($check) {
            session_regenerate_id();

            $email = $_POST['username'];
            $email = mysqli_real_escape_string($db_con, $email);
            //$_SESSION['email'] = $email;
            $_SESSION['email'] = $data['user_email_id'];
            $_SESSION['data'] = $data;
            $_SESSION['login_time'] = time();
            $rndno = rand(100000, 999999); //OTP generate
            $to = $_SESSION['email'];
            $name = $rwCheck['first_name'] . ' ' . $rwCheck['last_name'];

            require_once './mail.php';
            $mail = otpemail($rndno, $to, $projectName, $name);
            $_SESSION['otp'] = $rndno;
            $_SESSION['login_time'] = date("h:i:sa");
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
            }
            /*
                  $_SESSION['data'] = $data;
                  $ph = substr($data['phone_no'], -4);
                 * 
                 */
            ?>

            <?php
            //                      $string = '0123456789';
            //                      $string_shuffled = str_shuffle($string);
            //                      $otp = substr($string_shuffled, 0, 4);
            //                      $_SESSION['otp'] = $otp;
            //                      echo $_SESSION['otp'];
            //                    if(isset($_SESSION['otp_time']) && (time() - $_SESSION['otp_time'] > 1800)) {
            //                      $fdate=$time+(600);
            //                      $stamp = date('Y-m-d h:i:sa',$fdate);
            //
            //                      }else{
            //                      $_SESSION['otp_time'] = time();
            //                      }
            //                     $msgOtp = 'Your OTP is : ' . $otp;
            //                      $sendMsgToMbl = smsgatewaycenter_com_Send($data['phone_no'], $msgOtp, $debug = false);
            //                      } else {
            //                      echo '<script> loginfailed("index");</script>';
            //                      }
        } else {
            echo '<script>loginfailed("login?ref=' . $_GET['ref'] . '")</script>';
        }
    }
    if (isset($_POST['otp-valid'])) {
        session_regenerate_id();
        $data = $_SESSION['data'];
        $otpenter = $_POST['otp1'];
        $Cur_time = date('h:i:sa');
        $tm_diff = strtotime($Cur_time) - strtotime($_SESSION['login_time']);
        if ($mins = floor(($tm_diff) / 60) < 10) {
            if ($otpenter == $_SESSION['otp'] or $otp == $otpenter) {
                $_SESSION['adminMail'] = $data['user_email_id'];
                if (empty($data['last_active_login'])) {
                    header("location:pwd-reset");
                    $_SESSION['cdes_user_id'] = $data['user_id'];
                    $_SESSION['admin_user_name'] = $data['first_name'];
                    $_SESSION['admin_user_last'] = $data['last_name'];
                    $_SESSION['designation'] = $data['designation'];
                    $_SESSION['lastLogin'] = $data['last_active_login'];
                    $_SESSION['adminMail'] = $data['user_email_id'];
                    //die();
                } else {
                    $_SESSION['cdes_user_id'] = $data['user_id'];
                    $_SESSION['admin_user_name'] = $data['first_name'];
                    $_SESSION['admin_user_last'] = $data['last_name'];
                    $_SESSION['designation'] = $data['designation'];
                    $_SESSION['lastLogin'] = $data['last_active_login'];
                    $_SESSION['adminMail'] = $data['user_email_id'];

                    //user profile type
                    $privileges = array();
                    $priv = mysqli_query($db_con, "select * from tbl_bridge_role_to_um where find_in_set('$data[user_id]',user_ids)") or die('Error' . mysqli_error($db_con));
                    while ($rwPriv = mysqli_fetch_assoc($priv)) {
                        array_push($privileges, $rwPriv['role_id']);
                    }
                    $privileges = array_filter($privileges, function ($value) {
                        return $value !== '';
                    });
                    //print_r($privileges);
                    $_SESSION['admin_privileges'] = array_unique($privileges);
                    // print_r($_SESSION['admin_privileges']);
                    $_SESSION['notified'] = array();
                    $_SESSION['notified1'] = array();
                    $_SESSION['notified2'] = array();
                    $remoteHost = $_SERVER['REMOTE_ADDR'] . '/' . $_SESSION['custom_ip'];
                    $remoteHost = mysqli_real_escape_string($db_con, $remoteHost);

                    $lastlogin = mysqli_query($db_con, "select start_date from tbl_ezeefile_logs where id in ( select max(id) from tbl_ezeefile_logs where user_id = '$data[user_id]' and action_name = 'Login/Logout')") or die('Error' . mysqli_error($db_con));
                    $rwlastlogin = mysqli_fetch_assoc($lastlogin);
                    //update usermaster current logout
                    $update = mysqli_query($db_con, "update tbl_user_master set current_login_status='1',system_ip='$remoteHost', last_active_login='$rwlastlogin[start_date]'  where user_id='$data[user_id]'") or die('Error : ' . mysqli_error($db_con));
                    //update log
                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$data[user_id]', '$data[first_name] $data[last_name]',null,null,'Login/Logout','$date',null,'$remoteHost','')") or die('error : ' . mysqli_error($db_con));

                    unset($_SESSION['data']);
                    if (isset($_GET['ref'])) {
                        $ref = $_GET['ref'];
                        $ref = base64_decode(urldecode($ref));
                        if ($ref == "") {
                            header("location:index");
                        } else {
                            header("location:index");
                        }
                    } else {
                        header("location:index");
                    }
                }
            } else {
            ?>
                <script>
                    swal({
                        text: "<?php echo $lang['Etr_Vld_OTP']; ?>",
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
                    text: "<?php echo $lang['ur_OTP_exprd']; ?>",
                    type: "error"
                });
                $("#login-form").css("display", "none");
                $("#otp-form").css("display", "block");
            </script>
    <?php
        }
        mysqli_close($db_con);
    }
    ?>
    <script>
        //for otp input only number enter
        $("input#otp").keypress(function(e) {
            //if the letter is not digit then display error and don't type anything
            if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57) && e.which != 46) {
                //display error message
                return false;
            }
            str = $(this).val();
            str = str.split(".").length - 1;
            if (str > 0 && e.which == 46) {
                return false;
            }
        });
    </script>
    <script>
        $('input[type="text"]').keyup(function() {
            var GrpNme = $(this).val();
            re = /[`~!#$%^&*()|+\=?;:'",<>\{\}\[\]\\\/]/gi;
            var isSplChar = re.test(GrpNme);
            if (isSplChar) {
                var no_spl_char = GrpNme.replace(/[`~!#$%^&*()|+\=?;:'",<>\{\}\[\]\\\/]/gi, '');
                $(this).val(no_spl_char);
            }
        });
        $('input[type="text"]').bind(function() {
            $(this).val($(this).val().replace(/[<>]/g, ""))
        });
    </script>
</body>

</html>