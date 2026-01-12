<?php
include('sessionstart.php');
require_once './application/config/database.php';
require_once './application/pages/function.php';

$rwpwdPolicy = getPasswordPolicy($db_con);

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
        <div class="row">
            <div class="container">
                <div class="col-md-12 text-center" style="background:rgba(255,255,255,0.1)">
                    <a href="login?ref=aW5kZXgucGhw"><img src="assets/images/<?php echo $projectLogo; ?> " height="145px" ></a>
                </div>
            </div>
            <div class="col-md-12">
                <div class="wrapper-page">
                    <div class=" card-box transparent-back text-white" style="padding:5px !important;">
                        <div class="panel-body text-white">
                            <div class="col-md-8">
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
                                <p style="margin-top: 115px;">&copy <?php echo date('Y'); ?> <a href="http://cbslgroup.in" class="text-white"><?= $lang['cbslgroup']; ?></a><?php echo $lang['Copyright_CBSL_Grp_All_rights_rsrvd']; ?></p>

                            </div>

                            <div class="col-md-4" id="login-form"  style="border: 1px solid white; padding: 24px;">
                                <h3 class="text-left text-white"><?= $lang['reset_ur_password'] ?></h3>

                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <form method="post" class="form-horizontal restpass">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="pass1"><?= $lang['otp'] ?><span style="color:red;">*</span></label>
                                                        <input  name="otp" type="password"  placeholder="<?= $lang['Etr_OTP_hre'] ?>" id="otps" required class="form-control" data-parsley-required-message@="<?= $lang['tvr'] ?>">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="pass1"><?= $lang['Password'] ?><span style="color:red;">*</span></label>
                                                        <input type="password" id="pass1" name="paswd" class="form-control" data-parsley-minlength="<?= (!empty($rwpwdPolicy['minlen']) ? $rwpwdPolicy['minlen'] : '8'); ?>" data-parsley-maxlength="<?= (!empty($rwpwdPolicy['maxlen']) ? $rwpwdPolicy['maxlen'] : '8'); ?>" data-parsley-uppercase="<?= $rwpwdPolicy['uppercase']; ?>" data-parsley-lowercase="<?= $rwpwdPolicy['lowercase']; ?>" data-parsley-number="<?= $rwpwdPolicy['numbers']; ?>" data-parsley-special="<?= $rwpwdPolicy['s_char']; ?>" data-parsley-required-message@="<?= $lang['peyp'] ?>" data-parsley-pattern-message@="<?= $lang['tv_invalid'] ?>" placeholder="<?= $lang['enp'] ?>" required>

                                                    </div>
                                                    <div class="form-group">
                                                        <label for="passWord2"><?= $lang['Confirm_Password'] ?><span style="color:red;"></span></label>
                                                        <input data-parsley-equalto="#pass1" type="password" id="cpass" required placeholder="<?= $lang['Confirm_Password'] ?>" class="form-control" data-parsley-required-message@="<?= $lang['tvr'] ?>" data-parsley-equalto-message@="<?= $lang['tvsbs'] ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <button type="Reset" class="btn btn-default waves-effect" data-dismiss="modal"><strong><?= $lang['Reset'] ?></strong></button>
                                                    <button type="submit" name="resetpwd" class="btn btn-primary waves-effect waves-light"><strong><?= $lang['Submit'] ?></strong></button>
                                                </div>
                                                <div class="col-md-4">
                                                    <a href="http://www.cbslgroup.in/" target="_blank"><img src="assets/images/logo_cbsl.png" class="pull-right"></a>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
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
        <script src="assets/js/jquery.min.js"></script>
        <!-- Sweet-Alert  -->
        <script src="assets/plugins/sweetalert2/sweetalert2.min.js"></script>
        <script src="assets/pages/jquery.sweet-alert.init.js"></script>
        <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>
        <script>

            $(document).ready(function () {

                $('<input>').attr({type: 'hidden', value: '<?php echo csrfToken::generate(); ?>', name: 'token'}).appendTo('form');
            });
        </script>
        <script>
            $('form').parsley();
            //for otp input only number enter
            $("input#otps").keypress(function (e) {
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
        <?php
        session_start();
        if (isset($_POST['resetpwd'], $_POST['token'])) {
            $pass = $_POST['paswd'];
            $pass = mysqli_real_escape_string($db_con, $pass);
            $otp = $_POST['otp'];
            $otp = mysqli_real_escape_string($db_con, $otp);
            $to = $_SESSION['email'];
            $expirytime = $_SESSION['forgottime'] + 600;
            if (time() < $expirytime) {
                if (!strcmp($otp, $_SESSION['otp'])) {
                    $Resetpass = mysqli_query($db_con, "update `tbl_user_master` set password = sha1('$pass') where user_email_id = '$to'"); //or die('ERROR: in upadte pass' . mysqli_error($db_con));
                    $rwUser = mysqli_fetch_assoc($Resetpass);
                    $username = $rwUser['first_name'] . ' ' . $rwUser['last_name'];
                    if ($Resetpass) {
                        require_once './mail.php';
                        $mail = mailResetPass($to, $pass, $projectName, $username);
                        if ($mail) {
                            echo'<script> taskSuccess("login", "' . $lang['prs'] . '");</script>';
                        } else {
                            echo'<script> taskFailed("reset-password", "' . $lang['ftc'] . '");</script>';
                        }
                    }
                    session_destroy();
                    unset($_SESSION['otp']);
                    mysqli_close($db_con);
                } else {
                    echo'<script> taskFailed("reset-password", "' . $lang['ev_otp_code'] . '");</script>';
                }
            }else{
                 echo'<script> taskFailed("login", "'.$lang['ur_OTP_exprd'].'");</script>';
            }
        }
        ?>
    </body>
</html>


