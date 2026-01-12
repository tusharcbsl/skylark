<?php
set_time_limit(0);
require_once './loginvalidate.php';
require_once './application/pages/function.php';
require_once './classes/fileManager.php';

//getting role
$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));
$rwgetRole = mysqli_fetch_assoc($chekUsr);

$audioid = base64_decode(urldecode(xss_clean($_GET['id'])));
$audioid = preg_replace("/[^0-9]/", "", $audioid);
$uid = base64_decode(urldecode(xss_clean($_GET['i'])));
$uid = preg_replace("/[^0-9]/", "", $uid);
if ($uid != $_SESSION['cdes_user_id']) {
    header('Location:index');
}
$Audiopath = mysqli_query($db_con, "select doc_path,old_doc_name,doc_extn,doc_name,doc_id from tbl_document_master where doc_id = '$audioid'") or die('Error in path: ' . mysqli_error($db_con));
$rwAudiopath = mysqli_fetch_assoc($Audiopath);
$fname = $rwAudiopath['old_doc_name'];
$fname = preg_replace('/.[^.]*$/', '', $fname);
$doc_extn = $rwAudiopath['doc_extn'];
$slid = $rwAudiopath['doc_name'];
$filePath = $rwAudiopath['doc_path'];

 $fileManager = new fileManager();
// Connect to file server
$fileManager->conntFileServer();
$localPath = $fileManager->getFile($rwAudiopath);
?>

<html>
    <head>
        <link rel="apple-touch-icon" sizes="180x180" href="assets/images/favicons/apple-touch-icon.png">
        <link rel="icon" type="image/png" href="assets/images/favicons/favicon-32x32.png" sizes="32x32">
        <link rel="icon" type="image/png" href="assets/images/favicons/favicon-16x16.png" sizes="16x16">
        <link rel="manifest" href="assets/images/favicons/manifest.json">
        <link rel="mask-icon" href="assets/images/favicons/safari-pinned-tab.svg" color="#5bbad5">
        <link rel="stylesheet" href="assets/plugins/morris/morris.css">
        <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/core.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/components.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/icons.css" rel="stylesheet" type="text/css" />
        <!-- Called of font style  file -->
        <style>
            /* Global Reset */
            * {
                font-family: 'Allerta', arial, Tahoma;
                box-sizing: border-box;
            }
            body {
                background:#000;
/*                text-align:center;*/
                color:white;
            }
            h3{
                text-shadow:1px 1px 1px #fff;
            }
            /* Start  styling the page */
            .container-audio {
                width: 90%;
                height: auto;
                padding: 0px;
                border-radius: 5px;
                background-color: #eee;
                color: #333333;
                margin: 0px auto;
                overflow: hidden;
                margin-top: 10px;
            }
            audio {
                width:100%;
            }
            audio:nth-child(2), audio:nth-child(4), audio:nth-child(6) {
                margin: 0;
            }
            .container-audio .colum1 {
                width: 23px;
                height: 5em;
                border-radius: 5px;
                margin-top:30px;
                display: inline-block;
                position: relative;
            }
            .container-audio .colum1:last-child {
                margin: 0;
            }
            .container-audio .colum1 .row {
                width: 100%;
                height: 100%;
                border-radius: 5px;
                background: linear-gradient(to up, #7700aa, #8800ff);
                position: absolute;
                -webkit-animation: Rofa 10s infinite ease-in-out;
                animation: Rofa 10s infinite ease-in-out;
                bottom: 0;
            }
            .container-audio .colum1:nth-of-type(1) .row {
                -webkit-animation-delay: 3.99s;
                animation-delay: 3.99s;
            }
            .container-audio .colum1:nth-of-type(2) .row {
                -webkit-animation-delay: 3.80s;
                animation-delay: 3.80s;
            }
            .container-audio .colum1:nth-of-type(3) .row {
                -webkit-animation-delay: 3.70s;
                animation-delay: 3.70s;
            }
            .container-audio .colum1:nth-of-type(4) .row {
                -webkit-animation-delay: 3.60s;
                animation-delay: 3.60s;
            }
            .container-audio .colum1:nth-of-type(5) .row {
                -webkit-animation-delay: 3.50s;
                animation-delay: 3.50s;
            }
            .container-audio .colum1:nth-of-type(6) .row {
                -webkit-animation-delay: 3.40s;
                animation-delay: 3.40s;
            }
            .container-audio .colum1:nth-of-type(7) .row {
                -webkit-animation-delay: 3.30s;
                animation-delay: 3.30s;
            }
            .container-audio .colum1:nth-of-type(8) .row {
                -webkit-animation-delay: 3.20s;
                animation-delay: 3.20s;
            }
            .container-audio .colum1:nth-of-type(9) .row {
                -webkit-animation-delay: 3.10s;
                animation-delay: 3.10s;
            }
            .container-audio .colum1:nth-of-type(10) .row {
                -webkit-animation-delay: 2.1s;
                animation-delay: 2.1s;
            }
            .container-audio .colum1:nth-of-type(11) .row {
                -webkit-animation-delay: 2.1s;
                animation-delay: 2.1s;
            }
            .container-audio .colum1:nth-of-type(12) .row {
                -webkit-animation-delay: 2.10s;
                animation-delay: 2.10s;
            }
            .container-audio .colum1:nth-of-type(13) .row {
                -webkit-animation-delay: 2.20s;
                animation-delay: 2.20s;
            }
            .container-audio .colum1:nth-of-type(14) .row {
                -webkit-animation-delay: 1.30s;
                animation-delay: 1.30s;
            }
            .container-audio .colum1:nth-of-type(15) .row {
                -webkit-animation-delay: 1.40s;
                animation-delay: 1.40s;
            }
            .container-audio .colum1:nth-of-type(16) .row {
                -webkit-animation-delay: 1.50s;
                animation-delay: 1.50s;
            }
            .container-audio .colum1:nth-of-type(17) .row {
                -webkit-animation-delay: 1.60s;
                animation-delay: 1.60s;
            }
            .container-audio .colum1:nth-of-type(18) .row {
                -webkit-animation-delay: 1.70s;
                animation-delay: 1.70s;
            }
            .container-audio .colum1:nth-of-type(19) .row {
                -webkit-animation-delay: 1.80s;
                animation-delay: 1.80s;
            }
            .container-audio .colum1:nth-of-type(20) .row {
                -webkit-animation-delay: 2s;
                animation-delay: 2s;
            }

            @-webkit-keyframes Rofa {
                0% {
                    height: 0%;
                    -webkit-transform: translatey(0);
                    transform: translatey(0);
                    background-color: yellow;
                }

                5% {
                    height: 100%;
                    -webkit-transform: translatey(15px);
                    transform: translatey(15px);
                    background-color: fuchsia;
                }
                10% {
                    height: 90%;
                    transform: translatey(0);
                    -webkit-transform: translatey(0);
                    background-color: bisque;
                }

                15% {
                    height: 80%;
                    -webkit-transform: translatey(0);
                    transform: translatey(0);

                    background-color: cornflowerblue;
                }
                20% {
                    height: 70%;
                    -webkit-transform: translatey(0);
                    transform: translatey(0);
                    background-color: cornflowerblue;
                }
                25% {
                    height: 0%;
                    -webkit-transform: translatey(0);
                    transform: translatey(0);
                    background-color: cornflowerblue;
                }
                30% {
                    height: 70%;
                    -webkit-transform: translatey(0);
                    transform: translatey(0);
                    background-color: cornflowerblue;
                }
                35% {
                    height: 0%;
                    -webkit-transform: translatey(0);
                    transform: translatey(0);

                    background-color: cornflowerblue;
                }
                40% {
                    height: 60%;
                    -webkit-transform: translatey(0);
                    transform: translatey(0);

                    background-color: cornflowerblue;
                }
                45% {
                    height: 0%;
                    -webkit-transform: translatey(0);
                    transform: translatey(0);

                    background-color: cornflowerblue;
                }
                50% {
                    height: 50%;
                    -webkit-transform: translatey(0);
                    transform: translatey(0);

                    background-color: cornflowerblue;
                }
                55% {
                    height: 0%;
                    -webkit-transform: translatey(0);
                    transform: translatey(0);

                    background-color: cornflowerblue;
                }
                60% {
                    height: 40%;
                    -webkit-transform: translatey(0);
                    transform: translatey(0);

                    background-color: cornflowerblue;
                }
                65% {
                    height: 0%;
                    -webkit-transform: translatey(0);
                    transform: translatey(0);

                    background-color: cornflowerblue;
                }
                70% {
                    height: 30%;
                    -webkit-transform: translatey(0);
                    transform: translatey(0);

                    background-color: cornflowerblue;
                }
                75% {
                    height: 0%;
                    -webkit-transform: translatey(0);
                    transform: translatey(0);

                    background-color: cornflowerblue;
                }
                80% {
                    height: 20%;
                    -webkit-transform: translatey(0);
                    transform: translatey(0);

                    background-color: cornflowerblue;
                }
                85% {
                    height: 0%;
                    -webkit-transform: translatey(0);
                    transform: translatey(0);

                    background-color: cornflowerblue;
                }
                90% {
                    height: 10%;
                    -webkit-transform: translatey(0);
                    transform: translatey(0);

                    background-color: cornflowerblue;
                }
                95% {
                    height: 5%;
                    -webkit-transform: translatey(0);
                    transform: translatey(0);

                    background-color: cornflowerblue;
                }
                100% {
                    height: 0;
                    -webkit-transform: translatey(0);
                    transform: translatey(0);

                    background-color: cornflowerblue;
                }
            }
            .downbtn{
                margin-top: 5px;
                text-decoration: none;
                margin-right: 85px;
                font-family: roboto;
            }
        </style>
    </head>
    <?php
    if ($rwStor['is_protected'] == 1) {
        ?>
        <body>
            <style>
                #mask {
                    position:absolute;
                    left:0;
                    top:0;
                    z-index:9000;
                    background-color:grey;
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
/*                    text-align: center;*/

                }
                #boxes #dialog {
                    width:550px; 
                    height:auto;
                    padding: 0px;
                    background-color:#ffffff;
                    font-size: 15px;
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

            <div id="boxes">
                <div style="top: 50%; left: 50%; display: none;" id="dialog" class="window">
                    <form>
                        <div class="panel panel-color panel-danger p-b-0"> 
                            <div class="panel-heading">
                                <h2 class="panel-title"><?= $lang['folder_isprotected_password']; ?></h2>
                            </div>
                            <div class="panel-body">
                                <label class="text-primary"><?= $lang['peyp']; ?><span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="pass_value" required placeholder="<?= $lang['peyp']; ?>" autocomplete="off" autofocus>
                            </div>
                            <div class="modal-footer">
                                <input type="hidden" value="<?php echo $rwStor['password']; ?>" id="doc_pass">			  
                                <input type="submit" class="btn btn-primary" id="enter_btn" value="<?= $lang['Submit']; ?>" onclick="return password_check(event)">
                            </div>
                        </div>
                    </form>
                </div> 		
                <div style="z-index: 2000; position: fixed; top:0; color:white; display: none; opacity: 2.9;" id="mask"></div>
            </div>

        </body>
    <?php } ?>

    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery.min.js"></script> 
    <script src="assets/plugins/sweetalert2/sweet-alert.init.js"></script>
    <script src="https://cdn.polyfill.io/v2/polyfill.min.js"></script>
    <script src="assets/plugins/sweetalert2/sweetalert2-new.js"></script>
    <div id="audioprotected">
        <?php //if ($rwgetRole['pdf_download'] == '1' && isFolderReadable($db_con, $slid)) { ?>
            <div class="row">
                <a href="<?php echo BASE_URL . $localPath; ?>" class="btn btn-default btn-md pull-right downbtn" download="download"> <i class="fa fa-download"></i> Download File</a>
            </div>
        <?php //} ?>
        <div class="container-audio">
            <audio controls loop controlsList="nodownload">
                <source src="<?php echo BASE_URL . $localPath; ?>" type="audio/ogg">
                Your browser dose not Support the audio Tag
            </audio>
        </div>
        <div class="container-audio">
            <div class="colum1">
                <div class="row"></div>
            </div>
            <div class="colum1">
                <div class="row"></div>
            </div>
            <div class="colum1">
                <div class="row"></div>
            </div>
            <div class="colum1">
                <div class="row"></div>
            </div>
            <div class="colum1">
                <div class="row"></div>
            </div>
            <div class="colum1">
                <div class="row"></div>
            </div>
            <div class="colum1">
                <div class="row"></div>
            </div>
            <div class="colum1">
                <div class="row"></div>
            </div>
            <div class="colum1">
                <div class="row"></div>
            </div>
            <div class="colum1">
                <div class="row"></div>
            </div>
            <div class="colum1">
                <div class="row"></div>
            </div>
            <div class="colum1">
                <div class="row"></div>
            </div>
            <div class="colum1">
                <div class="row"></div>
            </div>
            <div class="colum1">
                <div class="row"></div>
            </div>
            <div class="colum1">
                <div class="row"></div>
            </div>
            <div class="colum1">
                <div class="row"></div>
            </div>
            <div class="colum1">
                <div class="row"></div>
            </div>
            <div class="colum1">
                <div class="row"></div>
            </div>
            <div class="colum1">
                <div class="row"></div>
            </div>
            <div class="colum1">
                <div class="row"></div>
            </div>
            <div class="colum1">
                <div class="row"></div>
            </div>
            <div class="colum1">
                <div class="row"></div>
            </div>
            <div class="colum1">
                <div class="row"></div>
            </div>
        </div>
    </div>
    <?php if ($rwStor['is_protected'] == 1) { ?>
        <script>
                                    $(document).ready(function () {
                                        var id = '#dialog';
                                        var maskHeight = $(document).height();
                                        var maskWidth = $(window).width();
                                        $('#mask').css({'width': maskWidth, 'height': maskHeight});
                                        $('#mask').show();
                                        $("#audioprotected").hide();

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
        </script>
    <?php } ?>
    <script>
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
                $("#audioprotected").show();
            } else
            {
                $("#boxes").hide();
                $("#mask").hide();
                $("#audioprotected").hide();
                taskFailed("<?php echo basename($_SERVER['REQUEST_URI']); ?>", "<?php echo $lang['pass_valid']; ?>");
            }

        }
    </script>	
    <script>
        $(".downbtn").click(function () {
            $.post("./application/ajax/downloadpdflog.php", {docid: "<?php echo $rwAudiopath['doc_id']; ?>", docname: "<?php echo $rwAudiopath['old_doc_name']; ?>", slid: "<?php echo $rwAudiopath['doc_name']; ?>"}, function (result, status) {
            });
        });
    </script>

    <?php
    require_once ('timelog-js.php');
    ?> 