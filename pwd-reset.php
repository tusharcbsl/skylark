<?php
require_once ('./sessionstart.php');
require_once('./application/config/database.php');
require_once './application/pages/function.php';

if (!isset($_SESSION['temp_user_id'])) {
    header('location:index');
    exit();
}
//user role wise view
$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[temp_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));
$rwgetRole = mysqli_fetch_assoc($chekUsr);

if (isset($_GET['i'])) {
    $id = base64_decode(urldecode($_GET['i']));
    $user = mysqli_query($db_con, "select * from tbl_user_master where user_id='$id'");
} else {
    $id = $_SESSION['temp_user_id'];
    $user = mysqli_query($db_con, "select * from tbl_user_master where user_id='$id'");
}
$rwUser = mysqli_fetch_assoc($user);
if (isset($_SESSION['lang']) && !empty($_SESSION['lang'])) {
    $file = $_SESSION['lang'] . ".json";
} else {
    $file = "English.json";
}
$data = file_get_contents($file);
$lang = json_decode($data, true);

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
        <title><?php echo $projectName; ?>  :: <?php echo $lang['Head_Title']; ?></title>
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
                    <a href="index"> <img src="assets/images/<?= $projectLogo ?>" height="145px"></a>
                </div>

            </div>
            <div class="col-md-12">
                <div class="wrapper-page">
                    <div class=" card-box transparent-back text-white">

                        <div class="panel-body text-white">
                            <div class="col-md-7">
                                <h1 class="text-white"><?php echo $lang['Welcome']; ?> !!</h1>
                                <p class="m-t-10"><strong><?php
                                        echo $projectName . ' ';
                                        if ($projectName == 'Ezeeoffice') {
                                            echo $lang['Erprs_wF'];
                                        }
                                        echo $lang['DcMgmt_Sys_Tk_mng_n_stre_ur_docs_n_reduce_ppr'];
                                        ?></strong></p>
                                <p class="bold m-t-40"><i class="fa fa-phone"></i> <strong><?php echo $lang['hp_no']; ?> :</strong> 1800 - 212 - 1526</p>
                                <p> <i class="fa fa-envelope"></i> <strong>Email Us :</strong> <a href="mailto:support@ezeedigitalsolutions.in" class="text-white"> support@ezeedigitalsolutions.in </a></p>
                                <p style="margin-top: 115px;">&copy <?php echo date('Y'); ?> <a href="http://cbslgroup.in" class="text-white"><?= $lang['cbslgroup']; ?></a> <?php echo $lang['Copyright_CBSL_Grp_All_rights_rsrvd'] . '.'; ?></p>

                            </div>
                            <div class="col-md-5" id="login-form" style="border: 1px solid white; padding: 20px;">
                                <?php if (isset($_GET['exp']) && (base64_decode($_GET['exp']) == $rwUser['last_pass_change'])) { ?>
                                    <h3 class="text-center text-white exp"><?= $lang['pass_expired']; ?></h3>  
                                <?php } else { ?>
                                    <h3 class="text-center text-white exp"><?php echo $lang['set_your_new_login_password']; ?></h3>
                                <?php } ?>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <form method="post" class="form-horizontal">
                                            <div class="row">
                                                <div class="col-md-12">

                                                    <div class="form-group">
                                                        <label  class="text-white"><?php echo $lang['Nw_Pwd']; ?><span class="text-alert">*</span></label>
                                                        <input type="password" id="pass2" parsley-trigger="change" name="password" class="form-control" data-parsley-minlength="<?= (!empty($rwpwdPolicy['minlen']) ? $rwpwdPolicy['minlen'] : '8'); ?>" data-parsley-maxlength="<?= (!empty($rwpwdPolicy['maxlen']) ? $rwpwdPolicy['maxlen'] : '8'); ?>" data-parsley-uppercase="<?= $rwpwdPolicy['uppercase']; ?>" data-parsley-lowercase="<?= $rwpwdPolicy['lowercase']; ?>" data-parsley-number="<?= $rwpwdPolicy['numbers']; ?>" data-parsley-special="<?= $rwpwdPolicy['s_char']; ?>" data-parsley-errors-container=".errorspannewpassinput" data-parsley-required-message="<?= $lang['peyp']; ?>" required placeholder="<?= $lang['enp']; ?>" />
                                                    </div>
                                                    <div class="form-group">
                                                        <label  class="text-white"><?php echo $lang['Confirm_Password']; ?><span class="text-alert">*</span></label>
                                                        <input type="password" parsley-trigger="change" name="pwd" class="form-control" required data-parsley-equalto="#pass2"  placeholder="<?php echo $lang['Confirm_Password']; ?>" />
                                                    </div>

                                                </div>
                                            </div>
                                            <button type="Reset" class="btn btn-default waves-effect" data-dismiss="modal"><?= $lang['Reset'] ?></button>
                                            <?php if (isset($_GET['exp']) && (base64_decode($_GET['exp']) == $rwUser['last_pass_change'])) { ?>
                                                <button type="submit" name="change-exp-pwd" class="btn btn-primary waves-effect waves-light"><?php echo $lang['Submit']; ?></button>
                                            <?php } else { ?>
                                                <button type="submit" name="change-pwd" class="btn btn-primary waves-effect waves-light"><?= $lang['Submit'] ?></button>
                                            <?php } ?>
                                        </form>
                                    </div>
                                </div>
                                </form> 
                            </div>
                            <!--                            <div class="col-md-8 ">
                                                            Copyright © <?php echo date('Y'); ?> <a href="http://cbslgroup.in" class="text-white">CBSL Group</a>. All rights reserved
                                                        </div>-->

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
                $('<input>').attr({type: 'hidden', value: '<?php echo csrfToken::generate(); ?>', name: 'token'}).appendTo('form');
            });
        </script>
        <script>
            function getUserIP(onNewIP)
            {
                var myPeerConnection = window.RTCPeerConnection || window.mozRTCPeerConnection || window.webkitRTCPeerConnection;
                var pc = new myPeerConnection({
                    iceServers: []
                }),
                        noop = function () {},
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
                pc.createOffer(function (sdp) {
                    sdp.sdp.split('\n').forEach(function (line) {
                        if (line.indexOf('candidate') < 0)
                            return;
                        line.match(ipRegex).forEach(iterateIP);
                    });

                    pc.setLocalDescription(sdp, noop, noop);
                }, noop);

                //listen for candidate events
                pc.onicecandidate = function (ice) {
                    if (!ice || !ice.candidate || !ice.candidate.candidate || !ice.candidate.candidate.match(ipRegex))
                        return;
                    ice.candidate.candidate.match(ipRegex).forEach(iterateIP);
                };
            }

            // Usage

            getUserIP(function (ip) {

                $(".vkm").val(ip);
                // document.getElementByClass("ip").value = ip;
            });
        </script>
    </body>
</html>
<?php
if (isset($_POST['change-exp-pwd'], $_POST['token'])) {
    if (!empty($_POST['password'])) {
        $pwd = $_POST['password'];
        $pwd = mysqli_real_escape_string($db_con, $pwd);
        $update = mysqli_query($db_con, "update tbl_user_master set password=sha1('$pwd'),last_pass_change='$date' where user_id='$id'");
        if ($update) {
            mysqli_set_charset($db_con, "utf8");
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`,`system_ip`) values ('$id', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','User changed own password, due to password has been expired.','$date','$host')") or die('error : ' . mysqli_error($db_con));
            echo '<script>taskSuccess("login?ref=' . $_GET['ref'] . '","' . $lang['password_updated_success'] . '");</script>';
        }
    }
}
?>
<?php
if (isset($_POST['change-pwd'], $_POST['token'])) {
    if (!empty($_POST['password'])) {
        $confirmpwd = $_POST['pwd'];
        $pwd = $_POST['password'];
        $pwd = mysqli_real_escape_string($db_con, $pwd);
        $confirmpwd = mysqli_real_escape_string($db_con, $confirmpwd);
        $id = $_SESSION['temp_user_id'];
        if ($pwd == $confirmpwd) {
            $update = mysqli_query($db_con, "update tbl_user_master set password=sha1('$pwd'), last_pass_change='$date' where user_id='$id'");
            if ($update) {
                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`, `action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[temp_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','password change','$date','$host','Password updated')") or die('error : ' . mysqli_error($db_con));
                header('location:' . $_SERVER['HTTP_REFERER']);
                $privileges = array();
                $priv = mysqli_query($db_con, "select * from tbl_bridge_role_to_um where find_in_set('$_SESSION[temp_user_id]',user_ids)") or die('Error' . mysqli_error($db_con));
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
                $remoteHost = $host;
                $remoteHost = mysqli_real_escape_string($db_con, $remoteHost);
                //update usermaster current logout
                $update = mysqli_query($db_con, "update tbl_user_master set current_login_status='1',system_ip='$remoteHost', last_active_login='$date'  where user_id='$_SESSION[temp_user_id]'") or die('Error : ' . mysqli_error($db_con));
                //update log
                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`, `action_name`, `start_date`, `system_ip`) values ('$_SESSION[temp_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Login/Logout','$date','$host')") or die('error : ' . mysqli_error($db_con));
                $_SESSION['cdes_user_id'] = $_SESSION['temp_user_id'];
                unset($_SESSION['temp_user_id']);
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
            echo'<script>taskFailed("' . basename($_SERVER['SCRIPT_NAME']) . '","' . $lang['pwd_confirm_pwd_same'] . '");</script>';
        }
    }
    mysqli_close($db_con);
}
?>