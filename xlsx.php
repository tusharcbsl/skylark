<?php
require_once './loginvalidate.php';
require_once './application/config/database.php';
require_once './application/pages/function.php';
require_once './classes/fileManager.php';
//for user role
$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));
$uid = base64_decode(urldecode($_GET['uid']));
$rwgetRole = mysqli_fetch_assoc($chekUsr);
if ($uid != $_SESSION['cdes_user_id']) {
    header('Location:index');
}
if ($rwgetRole['excel_file'] != '1') {
    header('Location: index');
}
if (isset($_GET['file'])) {
    $dcid = $_GET['file'];
    $docId = base64_decode(urldecode($dcid));
    $file = mysqli_query($db_con, "select doc_name, filename, doc_path, doc_extn, old_doc_name, checkin_checkout from tbl_document_master where doc_id='$docId'") or die('error' . mysqli_error($db_con));
    $rwFile = mysqli_fetch_assoc($file);
    $filePath = $rwFile['doc_path'];
    $fname = $rwFile['old_doc_name'];
    $filePath = $rwFile['doc_path'];
    $doc_extn = $rwFile['doc_extn'];
    $slid = $rwFile['doc_name'];
    $CheckinCheckout = $rwFile['checkin_checkout'];
}

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
if ($status == 0) {
    ?>
    <script>
        alert("File Is Locked Please Contact To Administrator");

        window.close();
    </script>
    <?php
}


require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
?>

<html lang="en">
    <head>
        <title>Excel Viewer</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="apple-touch-icon" sizes="180x180" href="assets/images/favicons/apple-touch-icon.png">
        <link rel="icon" type="image/png" href="assets/images/favicons/favicon-32x32.png" sizes="32x32">
        <link rel="icon" type="image/png" href="assets/images/favicons/favicon-16x16.png" sizes="16x16">
        <link rel="manifest" href="assets/images/favicons/manifest.json">
        <link href="viewer-pdf/modal.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/icons.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link rel="mask-icon" href="assets/images/favicons/safari-pinned-tab.svg" color="#5bbad5">
        <script src="assets/js/jquery.min.js"></script>
        <link href="assets/plugins/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css">
        <!--<script src="assets/plugins/sweetalert2/sweetalert2.min.js"></script>
        <script src="assets/pages/jquery.sweet-alert.init.js"></script>-->
        <script src="assets/plugins/sweetalert2/sweetalert2-new.js"></script>
        <script src="https://cdn.polyfill.io/v2/polyfill.min.js"></script>
        <script src="assets/plugins/sweetalert2/sweet-alert.init.js"></script>
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
        <style>

            html {
                font-family: Times New Roman;
                font-size: 9pt;
                background-color: white;
            }

            table tr td:hover{cursor:cell !important ; border: dotted 3px #01975B !important;} /* For all tables*/

            .cellformatter{
                cursor:cell ; border: dotted 3px #01975B !important;
            }

            .table-wrapper {
                max-width: 700px !important;
                overflow: scroll !important;
            }

            table {
                position: relative !important;
                border: 1px solid #ddd !important;
                border-collapse: collapse !important;
            }

            td, th {
                white-space: nowrap !important;
                border: 1px solid #ddd !important;
                padding: 3px !important;
            }


            tbody tr td:first-of-type {
                background-color: #eee !important;
                position: sticky !important;
                left: -1px !important;
                text-align: left !important;
                min-width: 50px;
            }
            tbody tr:first-of-type {
                background-color: #eee !important;
                z-index: 2 !important;
                text-align: center !important;

            }


            /* Header/Logo Title */
            .header{
                background: #dad8d8;
                font-size: 10px;
                max-width:100%;
                min-width: 100%;
            }

            .container{
                max-width: 100% !important;
            }
            body{

                margin-left: 0in !important;
                margin-right: 0in !important;
                margin-top: 0in !important;
                margin-bottom: 0in !important;
            }

            .stickyfooter {
                position: fixed;
                left: 0;
                bottom: 0;
                width: 100%;
                background-color: #dad8d8;
                color: black;
                text-align: center;
            }
            #sheetlist{
                padding: 10px;
            }
            #sheetlist li:hover{cursor:pointer}
            #sheetlist ul{
                list-style: none;
                float:left;
            }
            #sheetlist li{
                display: inline;
                margin-top: 2px;
                margin-left: 10px;
                margin-bottom: 10px;

            }

            .activespreadsheet{
                border-top:  solid 3px #01975B !important;
                border-right :  solid 3px #01975B !important;
                border-left:  solid 3px #01975B !important;
            }


            td{
                min-width: 100px;
            }
            .btn-right{
                float: right;
                margin-right: -54px; 
            }
            .btn-left{
                float: right;
                margin-right: -15px; 
            }
            .btncolor{
                background: #01975B !important
            }
            .col-md-2,.col-md-10{
                padding-right: 0px !important; 
                padding-left: 0px!important; 
            }

            .makemebold{
                font-weight: bold;
            }
            .makritalic{
                font-style: italic;

            }
            .dwn{
                margin-left: 1215px;
                text-decoration: none;
            }


        </style>
    </head>
    <body>
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
			
            $inputFileType = 'Xlsx';
            $inputFileName = $localPath;

            /**  Create a new Reader of the type defined in $inputFileType  * */
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
            $spreadsheet = $reader->load("$inputFileName"); //load whole worksheet
            $worksheetData = $reader->listWorksheetInfo($inputFileName);
            //echo $spreadsheet->getActiveSheet()->getCell('J5')->isFormula();
            //echo "ok".($spreadsheet->getActiveSheet()->getCell('J10')->setFormulaAttributes("J10"));
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Html($spreadsheet);

            $sheetnumber = isset($_GET['sheetindex']) ? $_GET['sheetindex'] : 0;
            $writer->setSheetIndex($sheetnumber); //load particular page
            ?>
            <input type="hidden" id="btnccals">
            <div id="safarimyModal" class="modal">
                <div class="modal-dialog  modal-sm"> 
                    <div class="panel panel-color panel-primary" style="width: 416px;height: 276px;"> 
                        <div class="panel-body text-center">
                            <p  id="iconsim"></p>
                            <br>
                            <h2 id="modaltitle"></h2>
                            <p id="abc" style="font-size: 22px;text-transform: capitalize;"></p>
                            <br>
                            <span id="sbmtbtn"></span>
                        </div>
                    </div> 
                </div>		
            </div>		

            <?php
            echo $writer->generateStyles(TRUE); // do not write <style> and </style>
            ?>


            <div id="xlsx_viewer">
                <div class="row m-t-20" style="background:#337ab7;">
                    <a href="<?php echo $localPath; ?>" class="btn btn-primary dwn" download=""> <i class="fa fa-download"></i> Download file</a>
                </div>
                <div class="row" id="loadviewer" style="overflow: scroll;">
                </div>

                <div class="row stickyfooter">
                    <div class="col-md-6">
                        <div id="sheetlist">
                            <ul class="nav nav-tabs">
                                <?php
                                $sheetindexincrement = 0;
                                foreach ($worksheetData as $worksheet) {
                                    $class = "";
                                    if ($sheetindexincrement == 0) {
                                        $class = "activespreadsheet";
                                    }
                                    $sheetName = $worksheet['worksheetName'];
                                    echo " <li data='$sheetindexincrement' class='sheetindexchnager $class'><i><b>$sheetName</b></i></li>";
                                    $sheetindexincrement++;
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6" style="margin-top:10px;">
                        <div class="col-md-3 btn-right">
                            <button class="btn btn-primary swipeRight btncolor"><i class="fa fa-arrow-right"></i></button>     
                        </div>

                        <div class="col-md-3 btn-right" >
                            <button class="btn btn-primary swipeLeft btncolor"><i class="fa fa-arrow-left"></i></button>    
                        </div>
                    </div>
                </div>

            </div>
            <!--show wait gif-->
            <div style=" display: block; background: rgba(0,0,0,0.5); width: 100%; z-index: 2000; position: fixed; top:0;" id="wait">
                <img src="assets/images/proceed.gif" alt="load"  style=" margin-left: 48%; margin-top: 250px; width: 100px; height:100px; position: fixed;" />
            </div>
<?php } ?>
        <script>
    // doc id for time log
    var doc_id = "<?php echo urlencode(base64_encode($docId)); ?>";

    var $k = 0;
    $(document).ready(function () {

        /*
         * get width of div for generate scroll bar
         */
        var pagewidth = $("#loadviewer").width() + "px";

        $('.swipeRight').click(function ()
        {

            $('#loadviewer').animate({scrollLeft: '+=460'}, 1000);
        });

        $('.swipeLeft').click(function ()
        {
            $('#loadviewer').animate({scrollLeft: '-=460'}, 1000);
        });
        /*
         * end of div
         */

        $k++;
        /*
         * sheet changer js start
         */
        $(".sheetid").click(function () {
            var num = $(this).attr("data");
            $.post("viewerxlsx.php", {sheetnumber: num}, function (result, status) {
                if (status == 'success') {
                    $("#loadviewer").html(result);
                }
            });

        });
        /*
         * load sheet by name
         */

        setTimeout(function () {
            if ($k == 1)
            {
                $("#loadviewer").empty();
                $.post("viewerxlsx.php", {sheetnumber: <?php echo $sheetnumber; ?>, filetype: "<?php echo $inputFileType; ?>", filename: "<?php echo $inputFileName; ?>", actiontype: 1}, function (result, status) {
                    if (status == 'success') {
                        $("#loadviewer").html(result);
                        $('#wait').hide();
                    }
                });
            }
        }, 3000);




        /*
         * 
         * @type type
         * load sheet dynamically starts
         */
        $(".sheetindexchnager").click(function () {
            $("#loadviewer").empty();
            $(".sheetindexchnager").removeClass("activespreadsheet");
            $(this).addClass("activespreadsheet");
            $('#wait').show();
            var sheetnumber = $(this).attr("data");
            $.post("viewerxlsx.php", {sheetnumber: sheetnumber, filetype: "<?php echo $inputFileType; ?>", filename: "<?php echo $inputFileName; ?>", actiontype: 1}, function (result, status) {
                if (status == 'success') {
                    $("#loadviewer").html(result);
                    $('#wait').hide();
                }
            });
        })

        /*
         * 
         * @type type
         * load sheet dynamically ends
         */


    });
    /*
     * 
     * @type type
     * close crtl+s
     */
    //            jQuery(document).bind("keyup keydown", function (e) {
    //                if (e.ctrlKey && e.keyCode == 83) {
    //                    $("#myModal").modal();
    //                    return false;
    //                }
    //            });
    //
    //            $(".savebylink").click(function () {
    //                $("#myModal").modal();
    //                return false;
    //            });

    function printDiv()
    {

        var divToPrint = document.getElementById('loadviewer');

        var newWin = window.open('', 'Print-Window');

        newWin.document.open();

        newWin.document.write('<html><body onload="window.print()">' + divToPrint.innerHTML + '</body></html>');

        newWin.document.close();

        setTimeout(function () {
            newWin.close();
        }, 10);

    }
    /*
     * 
     * @type type
     * End of CRTL +s
     */

    /*
     * load blanksheet when whole file not load start
     */
    $(window).on("load", function () {
        var heiht = $(document).height();
        $('#wait').css('height', heiht);
        $.post("blanksheet.php", {sheetnumber: <?php echo $sheetnumber; ?>, filetype: "<?php echo $inputFileType; ?>", filename: "<?php echo $inputFileName; ?>", actiontype: 1}, function (result, status) {
            if (status == 'success') {
                $("#loadviewer").html(result);

            }
        });
    });
    /*
     * when complete load
     */
        </script>
<?php if (($_SESSION['pass'] != $pass_word) && ($pass_check['is_protected'] == 1 || $pass_check['is_protected'] == 2)) { ?>
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
                    $("body").show();


                } else
                {

                    $("#boxes").hide();
                    $("#xlsx_viewer").hide();
                    //$("body").hide();	

                    taskFailed("<?php echo basename($_SERVER['REQUEST_URI']); ?>", "Password is not valid");


                }
            }
        </script>
<?php require_once('timelog-js.php'); ?>
        <!-- Modal -->
        <!--div class="modal fade" id="myModal" role="dialog">
            <div class="modal-dialog">
                <div class="panel panel-color panel-danger"> 
                    
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #007bff">
                            <h6 class="modal-title">Do You Want To Save This Sheet ?</h6>
                        </div>
                        <div class="modal-body">
                            <p>All Current Content Is Overwrite</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" id="save_mucurrent_sheet">Save</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
    
            </div>
        </div-->
<?php //require_once 'checkin-checkout-js.php';    ?>
        <!--script src="assets/plugins/sweetalert2/sweetalert2.min.js"></script>
        <script src="assets/pages/jquery.sweet-alert.init.js"></script-->
    </body>
</html>

<?php
//require_once 'checkin-checkout-php.php'; ?>