<?php
set_time_limit(0);
require_once './loginvalidate.php';
require_once './application/config/database.php';
require_once './classes/ftp.php';
require_once './application/pages/function.php';
require_once './classes/fileManager.php';

$data = file_get_contents($file);
$lang = json_decode($data, true);

//$user_id = @$_GET['id']; 
$vid = base64_decode(urldecode($_GET['id']));
$videopath = mysqli_query($db_con, "select doc_path,old_doc_name,doc_extn,doc_name from tbl_document_master where doc_id = '$vid'") or die('Error in path: ' . mysqli_error($db_con));
$rwvideopath = mysqli_fetch_assoc($videopath);
$fname = $rwvideopath['old_doc_name'];
$doc_extn = $rwvideopath['doc_extn'];
$slid = $rwvideopath['doc_name'];

$filePath = $rwvideopath['doc_path'];

$sql = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid'") or die('Error');
$pass_check = mysqli_fetch_assoc($sql);
$pass_word = $pass_check['password'];
$errorMsg = false;
if (isset($_POST['checkpass'])) {

    $pass = $_POST['password'];
    unset($_SESSION['pass']);
    if (SHA1($pass) == $pass_word) {
        $_SESSION['pass'] = $pass_word;
    } else {
        $errorMsg = 'Password is not valid';
    }
}
/* ------------lock file-0--------------- */
$status = 0;
$checkfileLockqry = mysqli_query($db_con, "SELECT * FROM `tbl_locked_file_master` WHERE doc_id='$docId' and is_active='1' and user_id='$_SESSION[cdes_user_id]'");
if (mysqli_num_rows($checkfileLockqry) > 0) {
    $checkfileLock = mysqli_query($db_con, "SELECT * FROM `tbl_locked_file_master` WHERE doc_id='$docId' and is_locked='1' and user_id='$_SESSION[cdes_user_id]'");
    if (mysqli_num_rows($checkfileLock) > 0) {
        $status = 1;
    } else {
        $status = 0;
    }
} else {
    $status = 1;
}
/* ------------lock file end---------------- */
if ($status == 1) {
    ?>
    <html>
        <head>
            <title>Video Player</title>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
            <meta name="google" content="notranslate">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <script src="assets/js/jquery.min.js"></script>
            <link href="assets/plugins/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css">
            <!--<script src="assets/plugins/sweetalert2/sweetalert2.min.js"></script>
            <script src="assets/pages/jquery.sweet-alert.init.js"></script>-->
            <script src="assets/plugins/sweetalert2/sweetalert2-new.js"></script>
            <script src="https://cdn.polyfill.io/v2/polyfill.min.js"></script>
            <script src="assets/plugins/sweetalert2/sweet-alert.init.js"></script>
            <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
            <style>
                #mask {
                    position:absolute;
                    left:0;
                    top:0;
                    z-index:9000;
                    background-color:black;
                    display:none;
                } 

                #boxes .window {
                    position:absolute;
                    left:0;
                    top:0;
                    width:440px;
                    height:850px;
                    display:none;
                    z-index:9999;
                    padding:20px;
                    border-radius: 5px;
                    text-align: center;
                }
                #boxes #dialog {
                    width:550px; 
                    height:auto;
                    padding: 10px 10px 10px 10px;
                    background-color:#ffffff;
                    font-size: 15pt;
                }

                .agree:hover{
                    background-color: #D1D1D1;
                }
                .popupoption:hover{
                    background-color:#D1D1D1;
                    color: green;
                }
                .popupoption2:hover{
                    color: red;
                }
            </style>
        </head>
        <body>
            <?php if (($_SESSION['pass'] != $pass_word) && ($pass_check['is_protected'] == 1 || $pass_check['is_protected'] == 2)) { ?>
                <div id="boxes">
                    <div style="top: 50%; left: 50%; display: none;" id="dialog" class="window">
                        <?php if ($errorMsg != false) { ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $errorMsg; ?>
                            </div>
                        <?php } ?>
                        <form method="post">

                            <div class="modal-header">
                                <h4 class="modal-title">Please enter password</h4>
                            </div>

                            <input type="password" class="form-control" name="password" id="password" autocomplete="off" autofocus >

                            <div class="modal-footer">
                                <input type="submit" class="btn btn-danger" name="checkpass" id="enter_btn"  value="Enter" >


                            </div>
                        </form>
                    </div>                                                          

                    <div style="width: 2478px; font-size: 32pt; color:white; height: 1202px; display: none; opacity: 0.4;" id="mask"></div>

                </div>
                <?php
            } else {
                $fileManager = new fileManager();
				// Connect to file server
				$fileManager->conntFileServer();
				$localPath = $fileManager->getFile($rwvideopath);
                
                ?>
                <input type="hidden" id="btnccals">
                <div id="safarimyModal" class="modal">
                    <div class="modal-dialog  modal-sm"> 
                        <div class="panel panel-color panel-primary"> 
                            <div class="panel-body text-center">
                                <p  id="iconsim"></p>
                                <br>
                                <h2 id="modaltitle"></h2>
                                <p id="abc" style="font-size: 22px; text-transform: capitalize;"></p>
                                <br>
                                <span id="sbmtbtn"></span>
                            </div>
                        </div> 
                    </div>		
                </div>

                <div class="modal-body" style="margin-left: 130px;">
                    <video controls style="border-radius: 10px; border: 5px solid #193860; width:90%;" <?php if(isFolderReadable($db_con, $slid) && $rwgetRole['pdf_download'] == '1'){  }else{ ?> controlsList="nodownload" <?php } ?> >
                        <source src="<?php echo BASE_URL . $localPath; ?> " type="video/mp4">
                        <source src="<?php echo BASE_URL . $localPath; ?>" type="video/ogg">
                        <?php echo $lang['ur_bwsr_ds_nt_sppt_the_elmt']; ?>
                    </video>
                </div>
            <?php } ?>
        </body>
    </html>
    <script>
        $(document).ready(function () {

            var id = '#dialog';
            var maskHeight = $(document).height();
            var maskWidth = $(window).width();
            $('#mask').css({'width': maskWidth, 'height': maskHeight});
            $('#mask').show();
            $("body").show();
            var winH = $(window).height();
            var winW = $(window).width();
            $(id).css('top', winH / 2 - $(id).height() / 2);
            $(id).css('left', winW / 2 - $(id).width() / 2);
            $(id).fadeIn(1000);
            $('.window .close').click(function (e) {
                e.preventDefault();
                $('#mask').hide();
                $('.window').hide();
            });
        });

        function clearForm()
        {
            $("#pass_value").reset();
        }

        function password_check(event)
        {
            event.preventDefault();
            var pass = $("#pass_value").val();
            var password = $("#doc_pass").val();
            var fpass = SHA1(pass);

            if (password == fpass)
            {
                $("#dialog").hide();
                $("#mask").hide();

            } else
            {
                $("#boxes").hide();
                $("#mask").show();
                $(".modal-body").hide();
                taskFailed("<?php echo basename($_SERVER['REQUEST_URI']); ?>", "Password is not valid");
        }

    }
</script>
<?php } else { ?>
    <script>
        alert("File Is Locked Please Contact To Administrator");
        window.close();
    </script>
<?php } ?>