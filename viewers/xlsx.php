<?php
require_once './excel-viewer/excel_reader.php';
require_once './loginvalidate.php';
require_once './application/config/database.php';
//for user role
$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));
$uid = base64_decode(urldecode($_GET['uid']));
$rwgetRole = mysqli_fetch_assoc($chekUsr);
//$rwgetRole['pdf_file'];
if ($uid != $_SESSION['cdes_user_id']) {
    header('Location:index');
}
if ($rwgetRole['excel_file'] != '1') {
    header('Location: index');
}
if (isset($_GET['file'])) {
    $dcid = $_GET['file'];
    $docId = base64_decode(urldecode($dcid));
    $file = mysqli_query($db_con, "select doc_name,filename,doc_path,old_doc_name,doc_extn,checkin_checkout from tbl_document_master where doc_id='$docId'") or die('error' . mysqli_error($db_con));
    $rwFile = mysqli_fetch_assoc($file);
    $filePath = $rwFile['doc_path'];
    $fname = $rwFile['old_doc_name'];
    $filePath = $rwFile['doc_path'];
    $doc_extn = $rwFile['doc_extn'];
    $slid = $rwFile['doc_name'];
    $CheckinCheckout = $rwFile['checkin_checkout'];
}
$storage = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id='$slid'") or die('Error Stroge name' . mysqli_error($db_con));
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

/*
 * file download end
 */

date_default_timezone_set('UTC');
require_once('./excel-viewer/XLSXReader.php');

//$xlsx = new XLSXReader('sample.xlsx');

$xlsx = new XLSXReader($localPath);
$sheetNames = $xlsx->getSheetNames();
?>
<!DOCTYPE html>
<html>
    <head>
        <title><?= $fname ?></title>
        <script src="assets/js/jquery.min.js"></script>
        <link rel="apple-touch-icon" sizes="180x180" href="assets/images/favicons/apple-touch-icon.png">
        <link rel="icon" type="image/png" href="assets/images/favicons/favicon-32x32.png" sizes="32x32">
        <link rel="icon" type="image/png" href="assets/images/favicons/favicon-16x16.png" sizes="16x16">
        <link rel="manifest" href="assets/images/favicons/manifest.json">
        <link href="viewer-pdf/modal.css" rel="stylesheet" type="text/css" />
        
        <link href="assets/css/core.css" rel="stylesheet" type="text/css"/>
        <link href="assets/css/icons.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/pages.css" rel="stylesheet" type="text/css" />
        
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
        <link rel="mask-icon" href="assets/images/favicons/safari-pinned-tab.svg" color="#5bbad5">
        <link href="assets/plugins/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css">
        <style>
            .table_data {
                border:2px ridge #000;
                padding:1px 3px;
            }
            .tab_base {
                background:#C8DaDD;
                font-weight:bold;
                border:2px ridge #000;
                cursor:pointer;
                padding: 2px 4px;
            }
            .table_sub_heading {
                background:#CCCCCC;
                font-weight:bold;
                border:2px ridge #000;
                text-align:center;
            }
            .table_body {
                background:#F0F0F0;
                font-wieght:normal;
                font-family:Calibri, sans-serif;
                font-size:16px;
                border:2px ridge #000;
                border-spacing: 0px;
                border-collapse: collapse;
            }
            .tab_loaded {
                background:#222222;
                color:white;
                font-weight:bold;
                border:2px groove #000;
                cursor:pointer;
            }
            .hide_div { display:none;}
        </style>
        <?php if ($CheckinCheckout == '0') { ?>
            <style type="text/css">
                .show_div{
                    margin-left: 285px;
                }
                .hide_div{
                    margin-left: 285px; 
                }

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
                    top: 27px;
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
                .form-control {
                    background-color: #FFFFFF;
                    border: 1px solid #E3E3E3;
                    border-radius: 4px;
                    color: #565656;
                    padding: 7px 12px;
                    height: 20px;
                    max-width: 100%;
                    margin-bottom: 10px;
                }
            </style>   

        <?php } ?>
    </head>
    <body>


        <?php //debug($sheetNames); ?>
        <?php
//download button
        if ($rwgetRole['pdf_download'] == '1' && $CheckinCheckout == '1') {
            echo'<a href="' . $localPath . '" download class="tab_base" style="float:right;text-decoration:none"><i class="fa fa-download" style="margin-right:2px"></i> File</a>';
        } else {
            echo'';
        }
//download button ends
        if ($rwgetRole['pdf_print'] == '1' && $CheckinCheckout == '1') {
            echo'<a onclick="printxls()" class="tab_base" style="float:right;text-decoration:none;margin-right:10px"><i class="fa fa-print" style="margin-right:2px"></i> File</a>';
        } else {
            echo'';
        }
        $sheet_tabs = '<table class="table_body" name="tab_table"><tr>';
        $n = 0;
        foreach ($sheetNames as $sheetName) {

            $sheet_tabs .= '<td class="tab_base" id="sheet_tab_' . $n . '" onclick="changeWSTabs(' . $n . ');">' . escape($sheetName) . '</td>';
            $n++;
        }
        $sheet_tabs .= '<tr></table>';
        echo $sheet_tabs;
        $n = 0;
        foreach ($sheetNames as $sheetName) {
            $sheet = $xlsx->getSheet($sheetName);
            //$sheetsTabs.='<td class="tab_base" id="sheet_tab_'. $sheet .'" onclick="changeWSTabs('. $sheet .');">'. escape($sheetName) .'</td>';
            if($sheet){
            $sheetdata = array2Table($sheet->getData(), $n);
            }else {
              echo '<div class="hide_div" id="sheet_div_' .$n. '">';
              echo '<table class="table_body" >'; 
              echo '<tr><td class="table_data">No data</td></tr>';
              echo '</table> </div>';
            }
            $n++;
        }
        ?>
        <?php
        /* $data = array_map(function($row) {
          $converted = XLSXReader::toUnixTimeStamp($row[0]);
          return array($row[0], $converted, date('c', $converted), $row[1]);
          }, $xlsx->getSheetData('Dates'));
          array_unshift($data, array('Excel Date', 'Unix Timestamp', 'Formatted Date', 'Data'));
          //array2Table($data);
         */
        ?>

    </body>
</html>


<?php

function array2Table($data, $sheet) {
    echo '<div class="hide_div" id="sheet_div_' . $sheet . '">';
    echo '<table class="table_body" >';
    if (count($data) > 0) {
        $i = 0;
        echo '<tr><td>&nbsp;</td>';
        foreach ($data[0] as $row) {

            echo '<td class="table_sub_heading">' . make_alpha_from_numbers($i) . '</td>';
            $i++;
        }

        echo '<tr>';
        $rn = 1;
        foreach ($data as $row) {
            echo "<tr>";
            echo '<td class="table_data">' . $rn . '</td>';
            foreach ($row as $cell) {
                echo "<td class='table_data'>" . escape($cell) . "</td>";
            }
            echo "</tr>";
            $rn++;
        }
    } else {
        echo '<td class="table_data">No data</td>';
    }
    echo '</table> </div>';
}

function make_alpha_from_numbers($number) {
    $numeric = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    if ($number < strlen($numeric))
        return $numeric[$number];
    else {
        $dev_by = floor($number / strlen($numeric));
        return make_alpha_from_numbers($dev_by - 1) . make_alpha_from_numbers($number - ($dev_by * strlen($numeric)));
    }
}

function debug($data) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}

function escape($string) {
    return htmlspecialchars($string, ENT_QUOTES);
}
?>
<?php require_once 'checkin-checkout-html.php';?>
<script>
// shows the Div with worksheet content according to clicked tab
    function changeWSTabs(sheet) {
        for (i = 0; i < <?php echo $n; ?>; i++) {
            document.getElementById('sheet_tab_' + i).className = 'tab_base';
            document.getElementById('sheet_div_' + i).className = 'hide_div';
        }
        document.getElementById('sheet_tab_' + sheet).className = 'tab_loaded';
        document.getElementById('sheet_div_' + sheet).className = 'show_div';
    }

// displays the first sheet 
    changeWSTabs(0);
    function printxls()
    {
        window.print();
    }
    jQuery(document).bind("keyup keydown", function (e) {
        if (e.ctrlKey && e.keyCode == 80) {
            alert("Please use the Print PDF button on top right of the page for a better rendering on the document");
            return false;
        }
    });
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
<?php require_once 'checkin-checkout-php.php';?>