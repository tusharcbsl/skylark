<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
$projectName = 'EzeeFile';
$projectLogo = 'ezeefile.png';
include('sessionstart.php');
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
                                <h3 class="text-left text-white"> Enter Register Email Address</h3>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <form method="post" class="form-horizontal restpass">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <input type="text"  name="resetPwd" class="form-control"  required placeholder="Enter Email.." required>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="submit" name="changepwd" class="btn btn-primary waves-effect waves-light pull-right">Submit</button>
                                        </form> 
                                    </div>
                                </div>
                                </form> 
                            </div>
                            <div class="col-md-12 ">
                                Copyright © <?php echo date('Y'); ?> <a href="http://cbslgroup.in" class="text-white">CBSL Group</a>. All rights reserved
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

        <!-- Sweet-Alert  -->
        <script src="assets/plugins/sweetalert2/sweetalert2.min.js"></script>
        <script src="assets/pages/jquery.sweet-alert.init.js"></script>

        <?php
        require_once './application/config/database.php';
        ?>
        <?php
        session_start();
        if (isset($_POST['changepwd'])) {
            $email = $_POST['resetPwd'];
            $email = mysqli_real_escape_string($db_con, $email);
            $_SESSION['email'] = $email;
            $chkUserMail = mysqli_query($db_con, "select * from tbl_user_master where user_email_id='$email'") or die('Error:' . mysqli_error($db_con));
            if (mysqli_num_rows($chkUserMail) > 0) {
                $rwCheck = mysqli_fetch_assoc($chkUserMail);
                $rndno = rand(100000, 999999); //OTP generate
                $to = $email;
                $name = $rwCheck['first_name'] . ' ' . $rwCheck['last_name'];
                $txt = $rndno;
                require_once './mail.php';
                $mail = mailPasschange($txt, $to, $projectName, $name);
                $_SESSION['otp'] = $rndno;
                if ($mail) {
                    echo'<script> taskSuccess("reset-password", "Verification Code sent Successfully");</script>';
                }
            } else {
                echo'<script> taskFailed("forgot-password", "Please Enter Valid Email ID");</script>';
            }
            mysqli_close($db_con);
        }
        ?>

    </body>
</html>


