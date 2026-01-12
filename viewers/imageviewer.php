<!DOCTYPE html>
<html>
    <?php
    error_reporting(0);
    $docId = base64_decode(urldecode($_GET['i']));
    $uid = base64_decode(urldecode($_GET['uid']));
    //die;
    //$docId = base64_decode(urldecode($_GET['file']));
    //ini_set('display_errors', 1);
    require_once './sessionstart.php';
    require_once './application/config/database.php';
    require_once './loginvalidate.php';
    if ($uid != $_SESSION['cdes_user_id']) {
        header('Location:index');
    }
    $chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));
    $rwgetRole = mysqli_fetch_assoc($chekUsr);
    if ($rwgetRole['image_file'] != '1') {
        header('Location:index');
    }

    $file = mysqli_query($db_con, "select * from tbl_document_master where doc_id='$docId'") or die('error d' . mysqli_error($db_con));
    $rwFile = mysqli_fetch_assoc($file);
    $filePath = $rwFile['doc_path'];
    $fname = $rwFile['old_doc_name'];
    $doc_extn = $rwFile['doc_extn'];
    $slid = $rwFile['doc_name'];
    $CheckinCheckout = $rwFile['checkin_checkout'];

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
    }
    list($width, $height) = getimagesize($localPath)
    //for user role
    //$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));
    //$rwgetRole = mysqli_fetch_assoc($chekUsr);
    ?>
    <head><title>Image viewer</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta name="google" content="notranslate">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/core.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/icons.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/pages.css" rel="stylesheet" type="text/css" />
        <link href="./viewer-pdf/modal.css" rel="stylesheet" type="text/css" />
     
        <script type="text/javascript" src="assets/js/jquery.min.js"></script>
        <link href="viewer-pdf/modal.css" rel="stylesheet" type="text/css" />

        <script src="assets/js/bootstrap.min.js"></script>
        <!-- This snippet is used in production (included from viewer.html) -->
        <link href="assets/css/imageviewer.css" rel="stylesheet" type="text/css" />

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
                label{
                    font-weight: 400;
                    font-size: 14px;
                }

            </style> 
        <?php } ?>
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
        <div id="mainContainer">
            <div class="toolbar" style="background: #808080a6;height: 36px;">
                <div id="toolbarContainer">
                    <div id="toolbarViewer">
                        <div id="toolbarViewerRight" class="pull-right">
                            <button id="btnPrint" class="btn btn-default btn-lg" title="Print" <?php
                            if ($rwgetRole['pdf_print'] == '1') {
                                
                            } else {
                                echo'disabled';
                            }
                            ?>>
                                <span class="glyphicon glyphicon-print"></span>
                            </button>
                            <a href="<?= $localPath ?>" id="down" class="btn btn-default btn-lg" title="Download" download <?php
                            if ($rwgetRole['pdf_download'] == '1') {
                                
                            } else {
                                echo'disabled';
                            }
                            ?>>
                                <span class="glyphicon glyphicon-download-alt"></span>
                            </a>
                            <button id="zoomOut" onclick="zoomin()" class="btn btn-default" title="Zoom In" >
                                <span  >Zoom In</span>
                            </button>
                            <button id="zoomOut" onclick="zoomout()" class="btn btn-default" title="Zoom Out" >
                                <span >Zoom Out</span>
                            </button>

                        </div>

                    </div>

                </div>
            </div> 

            <div id="viewerContainer" tabindex="0" style="margin-top: 20px">
                <?php require_once 'checkin-checkout-html.php';?>
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

        <script>
            var canvas = document.getElementById('viewport'),
                    context = canvas.getContext('2d');

            make_base();

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
        
        <?php require_once 'checkin-checkout-js.php';?>

    </body>
</html>

<script src="assets/plugins/sweetalert2/sweetalert2.min.js"></script>
<script src="assets/pages/jquery.sweet-alert.init.js"></script>

<script src="assets/js/jquery.core.js"></script>
<script src="assets/plugins/notifyjs/js/notify.js"></script>
<script src="assets/plugins/notifications/notify-metro.js"></script>
<!---editable modified storage level js code-->
<script>
$(document).ready(function(e){
//file button validation
    $("#myImage1").change(function () {
        var size = document.getElementById("myImage1").files[0].size;
        // alert(size);
        var name = document.getElementById("myImage1").files[0].name;
        //alert(lbl);
        if (name.length < 100)
        {
            $.post("application/ajax/valiadate_client_memory.php", {size: size}, function (result, status) {
                if (status == 'success') {
                    //$("#stp").html(result);
                    var res = JSON.parse(result);
                    if (res.status == "true")
                    {
                        // $("#memoryres").html("<span style=color:green>" + res.msg + "</span>");
                        $.Notification.autoHideNotify('success', 'top center', 'Success', res.msg)
                    } else {
                        $.Notification.autoHideNotify('warning', 'top center', 'Oops', res.msg)
                        //$("#memoryres").html("<span style=color:red>" + res.msg + "</span>");
                    }

                }
            });
        } else {
            var input = $("#myImage1");
            var fileName = input.val();

            if (fileName) { // returns true if the string is not empty
                input.val('');
            }
            $.Notification.autoHideNotify('error', 'top center', 'Error', "File Name Too Long");
        }
    });
})
</script>
<div id="notifi"></div>
<?php require_once 'checkin-checkout-php.php';?>
