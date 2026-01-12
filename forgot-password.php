<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
require_once 'logo-project.php';
include('sessionstart.php');
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
                    <div class="card-box transparent-back text-white" style="padding:5px !important;">
                        <div class="panel-body text-white">
                            <div class="col-md-8">
                                <h1 class="text-white"><?php echo $lang['Welcome']; ?> !!</h1>
                                <p class="m-t-10"><strong><?php
                                        echo $projectName. ' ';
                                        if ($projectName == 'Ezeeoffice') {
                                            echo $lang['Erprs_wF'];
                                        }
                                        echo $lang['DcMgmt_Sys_Tk_mng_n_stre_ur_docs_n_reduce_ppr']; ?></strong></p>
                                <p class="bold m-t-40"><i class="fa fa-phone"></i> <strong><?php echo $lang['hp_no']; ?> :</strong> 1800 - 212 - 1526</p> 
                                <p class="email"><i class="fa fa-envelope"></i><strong> <?php echo $lang['Email_Address']; ?> :</strong> <a href="mailto:support@ezeedigitalsolutions.in" class="text-white">support@ezeedigitalsolutions.in</a></p>
                                <p style="margin-top: 70px;"> &copy <?= date('Y'); ?> <a href="http://www.cbslgroup.in/" target="_blank"> <strong class="text-white"><?= $lang['cbslgroup']; ?></strong></a> <?php echo $lang['Copyright_CBSL_Grp_All_rights_rsrvd']; ?></p>
                            </div>
                            <div class="col-md-4" id="login-form" style="border: 1px solid white; padding: 24px;">
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <form method="post" class="form-horizontal restpass">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="text-center">
                                                        <h3><i class="fa fa-lock fa-4x text-white"></i></h3>
                                                        <h3 class="text-center text-white"><?=$lang['Frgt_pwd']?></h3>
                                                        <div class="form-group">
                                                            <div class="input-group">
                                                                <span class="input-group-addon"><i class="fa fa-envelope ico"></i></span>
                                                                <input type="email"  name="resetPwd" class="form-control"  required placeholder="<?=$lang['erea']?>" required>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                                <input name="changepwd" class="btn btn-lg btn-primary btn-block" value="<?=$lang['reset_password']?>" type="submit">
                                            </div>
                                        </form> 

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 m-t-15">
                                <a href="http://www.cbslgroup.in/" target="_blank"><img src="assets/images/logo_cbsl.png" class="pull-right"></a>
                            </div>

                        </div>   
                    </div>                              

                </div>
            </div>
        </div>
       
        <script src="assets/js/jquery.min.js"></script>
        <!-- Sweet-Alert  -->
        <script src="assets/plugins/sweetalert2/sweetalert2.min.js"></script>
        <script src="assets/pages/jquery.sweet-alert.init.js"></script>
        
         <script>
            var resizefunc = [];
             $(document).ready(function(){
                
               $('<input>').attr({type: 'hidden',value: '<?php echo csrfToken::generate();?>',name: 'token'}).appendTo('form');
            });
        </script>

        <?php
        require_once './application/config/database.php';
      
        session_start();
        if (isset($_POST['changepwd'], $_POST['token'])) {
            $email = $_POST['resetPwd'];
            $email = mysqli_real_escape_string($db_con, $email);
            $_SESSION['email'] = $email;
            mysqli_set_charset($db_con, 'utf8');
            $chkUserMail = mysqli_query($db_con, "select * from tbl_user_master where user_email_id='$email'"); //or die('Error:' . mysqli_error($db_con));
            if (mysqli_num_rows($chkUserMail) > 0) {
                $rwCheck = mysqli_fetch_assoc($chkUserMail);
                $rndno = rand(100000, 999999); //OTP generate
                $to = $email;
                $name = $rwCheck['first_name'] . ' ' . $rwCheck['last_name'];
                $txt = $rndno;
                require_once './mail.php';
                $mail = mailPasschange($txt, $to, $projectName, $name);
                $_SESSION['otp'] = $rndno;
                $_SESSION['forgottime'] = time();
                if ($mail) {
                    echo'<script> taskSuccess("reset-password","'.$lang['vcss'].'");</script>';
                }else{
					 echo'<script> taskFailed("forgot-password","'.$lang['Error_occurred_while_sending_mail'].'");</script>';
				}
            } else {
                echo'<script> taskFailed("forgot-password", "'.$lang['pl_ent_vld_email'].'");</script>';
            }
            mysqli_close($db_con);
        }
        ?>

    </body>
</html>


