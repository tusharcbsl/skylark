<?php
require_once './sessionstart.php';
require_once './application/config/database.php';
require_once './loginvalidate.php';

//for user role
$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));
$uid = base64_decode(urldecode($_GET['uid']));
$rwgetRole = mysqli_fetch_assoc($chekUsr);
if ($uid != $_SESSION['cdes_user_id']) {
    header('Location:index');
}

if ($rwgetRole['tif_file'] != '1') {
    header('Location: ../index');
}
$id = base64_decode(urldecode($_GET['file']));

$file = mysqli_query($db_con, "select * from tbl_document_master where doc_id='$id'") or die('error d' . mysqli_error($db_con));
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

// ASSign docid in different variable.
$docId=$id;
?>

<html>
    <head>
        <title>Tiff Image Viewer</title>
<!--        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>-->
        <script src="assets/js/jquery.min.js"></script>
        <script src="assets/tiff.min.js" ></script>
        <link href="assets/css/core.css" rel="stylesheet" type="text/css"/>
        <link href="assets/css/components.css" rel="stylesheet" type="text/css"/>
        <link href="assets/css/icons.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/pages.css" rel="stylesheet" type="text/css" />
        
        <link rel="shortcut icon" href="assets/images/favicon/favicon.ico">

        <?php if ($CheckinCheckout == '0') { ?>
            <link href="assets/plugins/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css">
            <link href="assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet">
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
                #comment-wrapper2{
                    position: fixed;
                    right: 0;
                    top: 40px;
                    bottom: 0;
                    overflow: auto;
                    width: 268px;
                    background: rgb(255, 255, 255);
                    border-left: 1px solid #d0d0d0; 
                }
                #comment-wrapper h4 {
                    margin: 10px;
                }
                #comment-wrapper {
                    position: fixed;
                    left: 0%;
                    top: 0;
                    right: 0;
                    bottom: 0;
                    /* overflow: auto;*/
                    width: 280px;
                    background: rgb(255, 255, 255);
                    border-left: 1px solid #d0d0d0;
                }
                #comment-wrapper h4 {
                    margin: 10px;
                }
                #comment-wrapper .comment-list {
                    font-size: 12px;
                    position: absolute;
                    top: 38px;
                    left: 0;
                    right: 0;
                    bottom: 0;

                }
                .ctext-wrap i {
                    float: right;
                }
                .comment-list-item li i {
                    float: right;
                    margin-left: 6px;

                }
                #comment-wrapper .comment-list-item {
                    border-bottom: 1px solid #d0d0d0;
                    padding: 10px;
                    color:#ffffff;
                    list-style-type: none;
                }
                #comment-wrapper .comment-list-container {
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 10px;
                   overflow: auto;
                }
                #comment-wrapper .comment-list-form {
                    position: absolute;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    padding: 10px;
                }
                #comment-wrapper .comment-list-form input {
                    padding: 5px;
                    width: 100%;
                }
                #comment-wrapper .comment-list-form1 {
                    position: absolute;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    padding: 10px;
                }
                #comment-wrapper .comment-list-form1 input {
                    padding: 5px;
                    width: 100%;
                }
                .m-t-15{
                    margin-top: 15px;
                }
            </style>   

        <?php } ?>
    </head>

    <body>
        <?php 
        require_once 'checkin-checkout-html.php'; 
        ?>
        
    <script>
        function msieversion()
        {
            var ua = window.navigator.userAgent;
            var msie = ua.indexOf("MSIE ");

            if (msie > 0)
            {
                return true;

            } else
            {
                return false;
            }

            return false;
        }


        $(function () {
            if (msieversion() == false) {
                var xhr = new XMLHttpRequest();
                xhr.responseType = 'arraybuffer';
                xhr.open('GET', "<?php echo $localPath; ?>");
                xhr.onload = function (e) {

                    Tiff.initialize({
                        TOTAL_MEMORY: 100000000
                    });
                    var tiff = new Tiff({buffer: xhr.response});
                    var canvas = tiff.toCanvas();
<?php if ($CheckinCheckout == 0) { ?>
                        $(canvas).css({
                            "max-width": "100%",
                            "width": "79%",
                            "height": "100%",
                            "display": "block",
                            "margin-left": "285px",
                        });
<?php } else { ?>
                        $(canvas).css({
                            "max-width": "100%",
                            "width": "100%",
                            "height": "100%",
                            "display": "block",
                            //"padding-top": "10px"
                        });
<?php } ?>
                    document.body.append(canvas);
                };
                xhr.send();
            } else {
                var imgElem = $('<img src="<?php echo $localPath; ?>"');
                $('body').append(imgElem);
            }
        })


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