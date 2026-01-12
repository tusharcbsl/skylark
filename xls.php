<?php
require_once './loginvalidate.php';
require_once './application/config/database.php';
require_once './classes/fileManager.php';

//for user role
$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));
$uid = base64_decode(urldecode($_GET['uid']));
$rwgetRole = mysqli_fetch_assoc($chekUsr);
$rwgetRole['excel_file'];
if ($uid != $_SESSION['cdes_user_id']) {
    header('Location:index');
}
if ($rwgetRole['excel_file'] != '1') {
    header('Location: index');
}
// A complex example that shows excel worksheets data appropiate to excel file
//$excel_file = "test.xls";
$dcid = @$_GET['file'];
$docId = base64_decode(urldecode($dcid));
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
    if ($_GET['chk'] == "rw") {
        //@sk(261118):for review log    
        $file = mysqli_query($db_con, "select doc_name, doc_path, doc_extn, old_doc_name from tbl_document_reviewer where doc_id='$docId'") or die('error' . mysqli_error($db_con));
    } else {
        $file = mysqli_query($db_con, "select doc_name,filename,doc_path,old_doc_name,doc_extn,checkin_checkout from tbl_document_master where doc_id='$docId'") or die('error' . mysqli_error($db_con));
    }
    $rwFile = mysqli_fetch_assoc($file);

    $filePath = $rwFile['doc_path'];
    $fname = $rwFile['old_doc_name'];
    $doc_extn = $rwFile['doc_extn'];
    $slid = $rwFile['doc_name'];
    $CheckinCheckout = $rwFile['checkin_checkout'];
    
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
    <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <meta http-equiv="X-UA-Compatible" content="IE=edge" />
            <title><?= $fname; ?></title>
            <link rel="apple-touch-icon" sizes="180x180" href="assets/images/favicons/apple-touch-icon.png">
            <link rel="icon" type="image/png" href="assets/images/favicons/favicon-32x32.png" sizes="32x32">
            <link rel="icon" type="image/png" href="assets/images/favicons/favicon-16x16.png" sizes="16x16">
            <link rel="manifest" href="assets/images/favicons/manifest.json">
            <link rel="mask-icon" href="assets/images/favicons/safari-pinned-tab.svg" color="#5bbad5">
            <link rel="shortcut icon" href="assets/images/favicon_1.ico">
            <link href="assets/css/icons.css" rel="stylesheet" type="text/css" />
            <script src="assets/js/jquery.min.js"></script> 
            <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>

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
                        overflow: auto;
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
                        /*                overflow: auto;*/
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

                </style>   

    <?php } ?>
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


        /*
         * file download end
         */
        $sheet_data = '';         // to store html tables with excel data, added in page
        $table_output = array();  // store tables with worksheets data

        $max_rows = 0;        // USE 0 for no max
        $max_cols = 0;        // USE 0 for no max
        $force_nobr = 0;      // USE 1 to Force the info in cells Not to wrap unless stated explicitly (newline)

        require_once './excel-viewer/excel_reader.php';      // include the class
        $excel = new PhpExcelReader();
        $excel->setOutputEncoding('UTF-8');     // sets encoding UTF-8 for output data
        $excel->read($localPath);       // read excel file data
        $nr_sheets = count($excel->sheets);       // gets the number of worksheets
// function used to add A, B, C, ... for columns (like in excel)

        function make_alpha_from_numbers($number) {
            $numeric = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
            if ($number < strlen($numeric))
                return $numeric[$number];
            else {
                $dev_by = floor($number / strlen($numeric));
                return make_alpha_from_numbers($dev_by - 1) . make_alpha_from_numbers($number - ($dev_by * strlen($numeric)));
            }
        }

// create html table data
        for ($sheet = 0; $sheet < $nr_sheets; $sheet++) {
            $table_output[$sheet] = '<table class="table_body"><tr><td>&nbsp;</td>';
            for ($i = 0; $i < $excel->sheets[$sheet]['numCols'] && ($i <= $max_cols || $max_cols == 0); $i++) {
                $table_output[$sheet] .= '<td class="table_sub_heading">' . make_alpha_from_numbers($i) . '</td>';
            }
            for ($row = 1; $row <= $excel->sheets[$sheet]['numRows'] && ($row <= $max_rows || $max_rows == 0); $row++) {
                $table_output[$sheet] .= '<tr><td class="table_sub_heading">' . $row . '</td>';
                for ($col = 1; $col <= $excel->sheets[$sheet]['numCols'] && ($col <= $max_cols || $max_cols == 0); $col++) {
                    if (isset($excel->sheets[$sheet]['cellsInfo'][$row][$col]['colspan']) && $excel->sheets[$sheet]['cellsInfo'][$row][$col]['colspan'] >= 1 && isset($excel->sheets[$sheet]['cellsInfo'][$row][$col]['rowspan']) && $excel->sheets[$sheet]['cellsInfo'][$row][$col]['rowspan'] >= 1) {
                        $this_cell_colspan = ' colspan="' . $excel->sheets[$sheet]['cellsInfo'][$row][$col]['colspan'] . '" ';
                        $this_cell_rowspan = ' rowspan="' . $excel->sheets[$sheet]['cellsInfo'][$row][$col]['rowspan'] . '" ';

                        for ($i = 1; $i < $excel->sheets[$sheet]['cellsInfo'][$row][$col]['colspan']; $i++) {
                            $excel->sheets[$sheet]['cellsInfo'][$row][$col + $i]['dontprint'] = 1;
                        }
                        for ($i = 1; $i < $excel->sheets[$sheet]['cellsInfo'][$row][$col]['rowspan']; $i++) {
                            for ($j = 0; $j < $excel->sheets[$sheet]['cellsInfo'][$row][$col]['colspan']; $j++) {
                                $excel->sheets[$sheet]['cellsInfo'][$row + $i][$col + $j]['dontprint'] = 1;
                            }
                        }
                    } else if (isset($excel->sheets[$sheet]['cellsInfo'][$row][$col]['colspan']) && $excel->sheets[$sheet]['cellsInfo'][$row][$col]['colspan'] >= 1) {
                        $this_cell_colspan = ' colspan="' . $excel->sheets[$sheet]['cellsInfo'][$row][$col]['colspan'] . '" ';
                        $this_cell_rowspan = '';
                        for ($i = 1; $i < $excel->sheets[$sheet]['cellsInfo'][$row][$col]['colspan']; $i++) {
                            $excel->sheets[$sheet]['cellsInfo'][$row][$col + $i]['dontprint'] = 1;
                        }
                    } else if (isset($excel->sheets[$sheet]['cellsInfo'][$row][$col]['rowspan']) && $excel->sheets[$sheet]['cellsInfo'][$row][$col]['rowspan'] >= 1) {
                        $this_cell_colspan = "";
                        $this_cell_rowspan = ' rowspan="' . $excel->sheets[$sheet]['cellsInfo'][$row][$col]['rowspan'] . '" ';
                        for ($i = 1; $i < $excel->sheets[$sheet]['cellsInfo'][$row][$col]['rowspan']; $i++) {
                            $excel->sheets[$sheet]['cellsInfo'][$row + $i][$col]['dontprint'] = 1;
                        }
                    } else {
                        $this_cell_colspan = "";
                        $this_cell_rowspan = "";
                    }
                    if (!isset($excel->sheets[$sheet]['cellsInfo'][$row][$col]['dontprint'])) {
                        $table_output[$sheet] .= '<td class="table_data" ' . $this_cell_colspan . $this_cell_rowspan . '>';
                        if ($force_nobr == 1)
                            $table_output[$sheet] .= '<nobr>';
                        if (isset($excel->sheets[$sheet]['cells'][$row][$col]))
                            $table_output[$sheet] .= nl2br(htmlentities($excel->sheets[$sheet]['cells'][$row][$col]));
                        if ($force_nobr == 1)
                            $table_output[$sheet] .= '</nobr>';
                        $table_output[$sheet] .= "</td>";
                    }
                }
                $table_output[$sheet] .= "</tr>";
            }
            $table_output[$sheet] .= "</table>";
            $table_output[$sheet] = str_replace(array("\n", "\r", "\t"), '', $table_output[$sheet]);
            if ($excel->sheets[$sheet]['numRows'] <= 0) {
                $table_output[$sheet] = 'No data';
            }
            $sheet_data .= '<div class="hide_div" id="sheet_div_' . $sheet . '">' . $table_output[$sheet] . "</div>\n";
        }

// Tabs witth WorkSheets Name
        $sheet_tabs = '<table class="table_body" name="tab_table"><tr>';
        for ($sheet = 0; $sheet < $nr_sheets; $sheet++) {
            $sheet_tabs .= '<td class="tab_base" id="sheet_tab_' . $sheet . '" onclick="changeWSTabs(' . $sheet . ');">' . $excel->boundsheets[$sheet]['name'] . '</td>';
        }
        $sheet_tabs .= '<tr></table>';
        ?>


        <?php
        if ($rwgetRole['xls_download'] == '1' && $CheckinCheckout == '1') {
            echo'<a href="' . $localPath . '" download class="tab_base" style="float:right;text-decoration:none;"><i class="fa fa-download" style="margin-right:2px"></i>Download File</a>';
        } else {
            echo'';
        }
        if ($rwgetRole['xls_print'] == '1' && $CheckinCheckout == '1') {
            echo'<a onclick="printxls()" class="tab_base" style="float:right;text-decoration:none;margin-right:10px;"><i class="fa fa-print"  style="margin-right:2px"></i>Print File</a>';
        } else {
            echo'';
        }
        // adds tabs and Divs with tables with worksheets data
        echo $sheet_tabs;
        echo $sheet_data;
        ?>
                <?php require_once 'checkin-checkout-html.php'; ?>
    <?php } ?>
            </body>
            <script>
                // doc id for time log
                var doc_id = "<?php echo urlencode(base64_encode($docId)); ?>";

                // shows the Div with worksheet content according to clicked tab
                function changeWSTabs(sheet) {
                    for (i = 0; i < <?php echo $nr_sheets; ?>; i++) {
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
        <?php if (($_SESSION['pass'] != $pass_word) && ($pass_check['is_protected'] == 1 || $pass_check['is_protected'] == 2)) { ?>  
                <script>
                    $(document).ready(function () {
                        var id = '#dialog';
                        var maskHeight = $(document).height();
                        var maskWidth = $(window).width();
                        $('#mask').css({'width': maskWidth, 'height': maskHeight});
                        $('#mask').show();
                        $(".tab_base").hide();
                        $(".tab_loaded").hide();
                        $(".hide_div").hide();
                        $(".show_div").hide();
                        //$('#mask').fadeTo("fast",3.5); 
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

            <?php require_once 'checkin-checkout-js.php'; ?>

        </body>
        </html>
        <?php require_once 'checkin-checkout-php.php'; ?>
    <?php } else { ?>
        <script>
            alert("File Is Locked Please Contact To Administrator");
            window.close();
        </script>
    <?php } ?>