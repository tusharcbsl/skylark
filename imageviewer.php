<!DOCTYPE html>
<html>
    <?php
    error_reporting(0);
    $docId = base64_decode(urldecode($_GET['i']));
    $uid = base64_decode(urldecode($_GET['uid']));

    require_once './loginvalidate.php';
    require_once './application/pages/function.php';
	require_once 'classes/ftp.php';
	require_once './classes/fileManager.php';
    if ($uid != $_SESSION['cdes_user_id']) {
        header('Location:index');
    }
    $chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));
    $rwgetRole = mysqli_fetch_assoc($chekUsr);
    if ($rwgetRole['image_file'] != '1') {
        header('Location:index');
    }
    if ($_GET['chk'] == "rw") {
        //@dv(15/04/19) for review version file   
        //$in_review = " and in_review='0'"; //
        $file = mysqli_query($db_con, "select doc_name, doc_path, doc_extn, old_doc_name from tbl_document_reviewer where doc_id='$docId'") or die('error' . mysqli_error($db_con));
    } else {
        $file = mysqli_query($db_con, "select * from tbl_document_master where doc_id='$docId'") or die('error d' . mysqli_error($db_con));
    }
    
    $rwFile = mysqli_fetch_assoc($file);
    $filePath = $rwFile['doc_path'];
    $fname = $rwFile['old_doc_name'];
    $doc_extn = $rwFile['doc_extn'];
    $slid = $rwFile['doc_name'];
    $CheckinCheckout = $rwFile['checkin_checkout'];

    /* ------------lock file-0--------------- */
    $status = 0;
    $checkfileLockqry = mysqli_query($db_con, "SELECT * FROM `tbl_locked_file_master` WHERE doc_id='$docId' and is_active='1' and user_id='$_SESSION[cdes_user_id]'");
    if (mysqli_num_rows($checkfileLockqry) > 0) {
        
        $checkfileLock = mysqli_query($db_con, "SELECT * FROM `tbl_locked_file_master` WHERE doc_id='$docId' and is_locked='1'  and user_id='$_SESSION[cdes_user_id]'");
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
        ?>

        <head>
            <title>Image viewer</title>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
            <meta name="google" content="notranslate">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
            <script type="text/javascript" src="assets/js/jquery.min.js"></script>
            <link href="viewer-pdf/modal.css" rel="stylesheet" type="text/css" />

            <script src="assets/js/bootstrap.min.js"></script>
            <!-- This snippet is used in production (included from viewer.html) -->
            <link href="assets/css/imageviewer.css" rel="stylesheet" type="text/css" />
            
            <link href="assets/css/icons.css" rel="stylesheet" type="text/css" />
            <link rel="apple-touch-icon" sizes="180x180" href="assets/images/favicons/apple-touch-icon.png">
            <link rel="icon" type="image/png" href="assets/images/favicons/favicon-32x32.png" sizes="32x32">
            <link rel="icon" type="image/png" href="assets/images/favicons/favicon-16x16.png" sizes="16x16">
            <link href="assets/plugins/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css">
            <link href="assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet">
            <?php if ($CheckinCheckout == '0') { ?>
                <style type="text/css">
                    .toolbar button {
                        background-color: #454545 !important;
                    }
                    #the-canvas {
                        border:1px solid black;
                    }

                    body {
                        background-color: #eee;
                        font-family: sans-serif;
                        margin: 0;
                    }

                    #comment-wrapper {
                        position: fixed;
                        left: 0%;
                        top: 45px;
                        right: 0;
                        bottom: 0;
                        overflow: auto;
                        width: 265px;
                        background: rgb(255, 255, 255);
                        border-left: 1px solid #d0d0d0;
                    }
                    omment-wrapper .comment-list {
                        font-size: 12px;
                        position: absolute;
                        top: 38px;
                        left: 0;
                        right: 0;
                        bottom: 0;

                    }
                    .m-b-15{
                        margin-bottom: 15px;

                    }
                    .m-l-15{
                        margin-left: 15px;

                    }
                    .m-t-20{
                        margin-top: 10px !important;

                    }
                    label{
                        font-weight: 400;
                        font-size: 14px;
                    }

                </style> 
            <?php } ?>
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
        <script>
            $(document).on('keydown keyup', function (e) {

                if (e.ctrlKey && (e.key == "p" || e.charCode == 16 || e.charCode == 112 || e.keyCode == 80)) {
                    alert("Please use the Print PDF button on top right of the page for a better rendering on the document");
                    e.cancelBubble = true;
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    abort();
                }
            });
        </script>
        <body  class="loadingInProgress" style="background: aliceblue;">
            <?php if (($_SESSION['pass'] != $pass_word) && ($pass_check['is_protected'] == 1 || $pass_check['is_protected'] == 2)) { ?>
                <div id="boxes">
                    <div style="top: 50%; left: 50%; display: none;" id="dialog" class="window">
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
				$localPath = $fileManager->getFile($rwFile);
/* 
				mysqli_set_charset($db_con, "utf8");
                $storage = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id='$slid'") or die('Error');
                $rwStor = mysqli_fetch_assoc($storage);

                $folderName = "temp";
                if (!dir($folderName)) {
                    mkdir($folderName, 0777, TRUE);
                }
                $folderName = $folderName . '/' . $_SESSION['cdes_user_id'];
                if (!dir($folderName)) {
                    mkdir($folderName, 0777, TRUE);
                }
                $folderName = $folderName . '/' . preg_replace('/[^A-Za-z0-9\-]/', '', $rwStor['sl_name']); //preg_replace('/[^A-Za-z0-9\-]/', '', $string);
                if (!dir($folderName)) {
                    mkdir($folderName, 0777, TRUE);
                }
                if (FTP_ENABLED) {
                    $localPath = $folderName . '/' . preg_replace('/[^A-Za-z0-9\-]/', '', $fname) . '.' . $doc_extn;

                    if (!empty($fname)) {
                        require_once './classes/ftp.php';

                        $ftp = new ftp();
                        $ftp->conn("$fileserver", "$port", "$ftpUser", "$ftpPwd");

                        $server_path = ROOT_FTP_FOLDER . '/' . $filePath;

                        $ftp->get($localPath, $server_path); // download live "$server_path"  to local "$localpath"
                        $arr = $ftp->getLogData();
                        if ($arr['error'] != "")
                        // echo '<h2>Error:</h2>' . implode('<br />', $arr['error']);
                            if ($arr['ok'] != "") {
                                //echo 'success';
                                //header("location:pdf/web/viewer.php?file=$folderName/view_pdf.pdf");
                            }
                    }
                } else {
                    $localPath = 'extract-here/' . $filePath;
                }  */
                
                //decrypt file
                decrypt_my_file($localPath);

                list($width, $height) = getimagesize($localPath);
                
                ?>
                <div id="mainContainer">
                    <div class="toolbar" style="background: #808080a6;height: 36px;">
                        <div id="toolbarContainer">
                            <div id="toolbarViewer">
                                 <div id="toolbarViewerRight" class="pull-right">
                            <?php if ($rwgetRole['pdf_print'] == '1' && isFolderReadable($db_con, $slid)) { ?>
                                <button id="btnPrint" class="btn btn-default btn-sm" title="Print">
                                    <span class="glyphicon glyphicon-print"></span> Print
                                </button>
                            <?php } ?>
                            <?php if ($rwgetRole['pdf_download'] == '1' && isFolderReadable($db_con, $slid)) { ?>
                                <a href="<?php echo BASE_URL . $localPath; ?>" id="down" class="btn btn-default downloadImage btn-sm" title="Download" download="<?php echo $fname . '.' . $doc_extn; ?>"> <span class="glyphicon glyphicon-download-alt"></span> Download</a>
                            <?php } ?>
                            <button id="zoomOut" onClick="rotateImage(this.value);" value="90" class="btn btn-default btn-sm btnRotate" title="Rotate Clockwise">
                                <span class="fa fa-rotate-right"></span> Rotate Clockwise
                            </button>
                            <button id="zoomOut" onClick="rotateImage(this.value);" value="-90" class="btn btn-default btn-sm btnRotate" title="Rotate Counterclockwise">
                                <span class="fa fa-rotate-left"></span> Rotate Counterclockwise
                            </button>
                            <button id="zoomOut" onClick="rotateImage(this.value);" value="180" class="btn btn-default btn-sm btnRotate" title="Rotate 180 Degree">
                                <span class="fa fa-rotate-right"></span> Rotate 180 Degree
                            </button>
                            <button id="zoomOut" onClick="rotateImage(this.value);" value="360" class="btn btn-default btn-sm btnRotate" title="Rotate 360 Degree">
                                <span class="fa fa-refresh"></span> Rotate 360 Degree
                            </button>
                            <button id="zoomOut" onclick="zoomin()" class="btn btn-default btn-sm" title="Zoom In">
                                <span class="fa fa-search-plus"></span> Zoom In
                            </button>
                            <button id="zoomOut" onclick="zoomout()" class="btn btn-default btn-sm" title="Zoom Out">
                                <span class="fa fa-search-minus"></span> Zoom Out
                            </button>

                        </div>

                            </div>

                        </div>
                    </div> 

                    <div id="viewerContainer" tabindex="0" style="margin-top: 20px">

                        <?php require_once 'checkin-checkout-html.php'; ?>
                        <div class="middle" style="<?php
                        if ($CheckinCheckout == '0') {
                            echo 'width:60%';
                        } else {
                            echo 'width:90%';
                        }
                        ?>; margin: auto; text-align: center; vertical-align: middle;" >

                            <canvas id="viewport" width="<?php echo $width; ?>" height="<?php echo $height; ?>" style="border:5px solid #d3d3d3;">
                            </canvas> 
                        </div>
                    </div>

                </div> 
            <?php } ?>

            <script>
            function rotateImage(degree) {
                $('#viewport').animate({transform: degree}, {
                    step: function (now, fx) {
                        $(this).css({
                            '-webkit-transform': 'rotate(' + now + 'deg)',
                            '-moz-transform': 'rotate(' + now + 'deg)',
                            'transform': 'rotate(' + now + 'deg)'
                        });
                    }
                });
            }
        </script>
            
            
            <script>
                // doc id for time log
                var doc_id = "<?php echo urlencode(base64_encode($docId)); ?>";

                var canvas = document.getElementById('viewport'),
                        context = canvas.getContext('2d');

                make_base();
                //taskSuccess("storageFiles","Mtadta_Updted_sucsfly");
                function make_base()
                {
                    base_image = new Image();
                    base_image.src = '<?php echo $localPath; ?>';
                    base_image.onload = function () {
                        context.drawImage(base_image, 0, 0);
                    }
                }
                function zoomin() {
                    var myImg = document.getElementById("viewport");
                    var currWidth = myImg.clientWidth;
                    if (currWidth == 500) {
                        alert("Maximum zoom-in level reached.");
                    } else {
                        myImg.style.width = (currWidth + 50) + "px";
                    }
                }
                function zoomout() {
                    var myImg = document.getElementById("viewport");
                    var currWidth = myImg.clientWidth;
                    if (currWidth == 50) {
                        alert("Maximum zoom-out level reached.");
                    } else {
                        myImg.style.width = (currWidth - 50) + "px";
                    }
                }
                //            function zoomin()
                //            {
                //                var Page = document.getElementById('viewport');
                //                var zoom = parseInt(Page.style.zoom) + 10 + '%'
                //                Page.style.zoom = zoom;
                //                return false;
                //            }
                //
                //            function zoomout()
                //            {
                //                var Page = document.getElementById('viewport');
                //                var zoom = parseInt(Page.style.zoom) - 10 + '%'
                //                Page.style.zoom = zoom;
                //                return false;
                //            }
                $("#scaleSelectContainer").on("change", function () {

                    var size = $(this).val();
                    var myImg = document.getElementById("viewport");
                    var currWidth = myImg.clientWidth;
                    if (currWidth == 500) {
                        alert("Maximum zoom-in level reached.");
                    } else {
                        myImg.style.width = (currWidth + size) + "px";
                    }

                });

                $("#btnPrint").on("click", function () {

                    //alert('hi');
                    //                var divContents = $("#viewport").html();
                    var printWindow = window.open('', '', 'height=600px,width=600px');
                    //printWindow.document.write('<html><head><title>Image</title>');
                    //printWindow.document.write('</head><body >');
                    printWindow.document.write("<img src='" + canvas.toDataURL() + "' style='width:<?php echo $width; ?>;hieght:<?php echo $height; ?>'/>");
                    //                printWindow.document.write(divContents);
                    //printWindow.document.write('</body></html>');

                    printWindow.document.close();
                    setTimeout(function () {
                        printWindow.print()
                    }, 2000);
                });
                function downloadCanvas(link, canvasId, filename) {
                    link.href = document.getElementById(canvasId).toDataURL();
                    link.download = filename;
                }

                document.getElementById('download').addEventListener('click', function () {
                    downloadCanvas(this, 'viewport', '<?php echo $rwFile['old_doc_name']; ?>');
                }, false);
                $(document).ready(function () {
                    $("html").bind("contextmenu", function (e) {
                        e.preventDefault();
                    });
                });

                window.onbeforeunload = function () {

                    $.post("application/ajax/removeTempFiles.php", {filepath: "<?php echo '../' . $localPath; ?>"}, function (result) {

                    });
                    return;
                };

            </script>
            <?php if (($_SESSION['pass'] != $pass_word) && ($pass_check['is_protected'] == 1 || $pass_check['is_protected'] == 2)) { ?>	        
                <script>
                    $(document).ready(function () {

                        var id = '#dialog';
                        var maskHeight = $(document).height();
                        var maskWidth = $(window).width();
                        $('#mask').css({'width': maskWidth, 'height': maskHeight});
                        $('#mask').fadeIn(10);
                        $('#mask').fadeTo("slow", 0.5);
                        $('#mainContainer').hide();
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



            <?php require_once 'checkin-checkout-js.php'; ?>
            <?php require_once 'checkin-checkout-php.php'; ?>
        <?php } else { ?>
            <script>
                alert("File Is Locked Please Contact To Administrator");
                window.close();
            </script>
        <?php } ?>
    </body>
</html>