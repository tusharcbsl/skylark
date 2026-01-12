<!DOCTYPE html>
<?php
require_once 'loginvalidate.php';
require_once './application/pages/function.php';

$uid = base64_decode(urldecode($_GET['id']));
if ($uid != $_SESSION['cdes_user_id']) {
    header('Location:index');
}
if ($rwgetRole['pdf_file'] != '1') {
    header('Location:index');
}
/*
 * file download
 */
$docId = base64_decode(urldecode($_GET['i']));

/* ------------lock file-0--------------- */
$status = 0;

$checkfileLockqry = mysqli_query($db_con, "SELECT * FROM `tbl_locked_file_master` WHERE doc_id='$docId' and is_active='1' and user_id='$_SESSION[cdes_user_id]'");
if (mysqli_num_rows($checkfileLockqry) > 0) {

    $checkfileLock = mysqli_query($db_con, "SELECT * FROM `tbl_locked_file_master` WHERE doc_id='$docId' and is_active='1' and user_id='$_SESSION[cdes_user_id]'");
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
    // assign doc id
    $id1 = $docId;
    if ($_GET['chk'] == "rw") {
        //@sk(261118):for review log   
        mysqli_set_charset($db_con, "utf8");
        $in_review = " and in_review='0'"; //
        $file = mysqli_query($db_con, "select doc_name, doc_path, doc_extn, old_doc_name from tbl_document_reviewer where doc_id='$docId'") or die('error' . mysqli_error($db_con));
    } else {
        mysqli_set_charset($db_con, "utf8");
        //@sk(261118):for review log    
        $in_review = " and in_review='1'"; //
        $file = mysqli_query($db_con, "select doc_name, filename, doc_path, doc_extn, old_doc_name, checkin_checkout, ticket_id from tbl_document_master where doc_id='$docId'") or die('error' . mysqli_error($db_con));
    }

    $rwFile = mysqli_fetch_assoc($file);
    // print_r($rwFile);die;
    $fileName = $rwFile['old_doc_name'];
    if (strpos($fileName, ".")) {
        $fname = preg_replace('/.[^.]*$/', '', $fileName);
    } else {
        $fname = $fileName;
    }
    $filePath = $rwFile['doc_path'];
    $slid = $rwFile['doc_name'];
    $doc_extn = $rwFile['doc_extn'];
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

    if(file_exists('extract-here/' . $filePath)){
          
        $localPath = 'extract-here/' . $filePath;
    }
    else if (FTP_ENABLED) {

        $localPath = $folderName . '/' . $fileName;
        if (!empty($fileName)) {
            require_once './classes/ftp.php';
            $ftp = new ftp();
            $ftp->conn("$fileserver", "$port", "$ftpUser", "$ftpPwd");

            $server_path = 'DMS/' . ROOT_FTP_FOLDER . '/' . $filePath;
            // die($server_path);

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
        // die($filePath);
        $localPath = 'extract-here/' . $filePath;
    }
    //decrypt file
    decrypt_my_file($localPath);


    $sql = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid'") or die('Error');
    $pass_check = mysqli_fetch_assoc($sql);

    if ($pass_check['is_protected'] == 1) {
?>
        <html>

        <head>
            <meta charset="utf-8">
            <script src="assets/js/jquery.min.js"></script>
            <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />

            <style>
                #mask {
                    position: absolute;
                    left: 0;
                    top: 0;
                    z-index: 9000;
                    background-color: grey;
                    display: none;
                }

                #boxes .window {
                    position: absolute;
                    left: 0;
                    top: 0;
                    width: 440px;
                    height: 850px;
                    display: none;
                    z-index: 9999;
                    padding: 20px;
                    border-radius: 5px;
                    /*                    text-align: center;*/

                }

                #boxes #dialog {
                    width: 550px;
                    height: auto;
                    padding: 0px;
                    background-color: #ffffff;
                    font-size: 15px;
                }

                .agree:hover {
                    background-color: #D1D1D1;
                }

                .popupoption:hover {
                    background-color: #D1D1D1;
                    color: green;
                }

                .popupoption2:hover {
                    color: red;
                }
            </style>
        </head>

        <body>

            <div id="boxes">
                <div style="top: 50%; left: 50%; display: none;" id="dialog" class="window">
                    <form>
                        <div class="panel panel-color panel-danger p-b-0">
                            <div class="panel-heading">
                                <h2 class="panel-title"><?= $lang['folder_isprotected_password']; ?></h2>
                            </div>
                            <div class="panel-body">
                                <label class="text-primary"><?= $lang['peyp']; ?><span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="pass_value" placeholder="<?= $lang['peyp']; ?>" autocomplete="off" autofocus>
                            </div>
                            <div class="modal-footer">
                                <input type="hidden" value="<?php echo $pass_check['password']; ?>" id="doc_pass">
                                <input type="submit" class="btn btn-primary" id="enter_btn" value="<?php echo $lang['Submit']; ?>" onclick="return password_check(event)">
                            </div>
                        </div>
                    </form>
                </div>
                <div style="z-index: 2000; position: fixed; top:0; color:white; display: none; opacity: 2.9;" id="mask"></div>
            </div>

        </body>

        </html>
    <?php } ?>
    <html dir="ltr" mozdisallowselectionprint moznomarginboxes>

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta name="google" content="notranslate">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!--            <title>PDF viewer</title>-->
        <link href="assets/css/icons.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/components.css" rel="stylesheet" type="text/css" />
        <link href="anott/toolbar.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/core.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="assets/js/jquery.min.js"></script>
        <link href="viewer-pdf/modal.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="viewer-pdf/viewer.css">
        <script src="viewer-pdf/compatibility.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
        <!-- This snippet is used in production (included from viewer.html) -->
        <link rel="resource" type="application/l10n" href="viewer-pdf/locale/locale.properties">
        <script src="viewer-pdf/l10n.js"></script>
        <script src="viewer-pdf/build/pdf.js"></script>
        <link rel="apple-touch-icon" sizes="180x180" href="assets/images/favicons/apple-touch-icon.png">
        <link rel="icon" type="image/png" href="assets/images/favicons/favicon-32x32.png" sizes="32x32">
        <link rel="icon" type="image/png" href="assets/images/favicons/favicon-16x16.png" sizes="16x16">
        <link rel="manifest" href="assets/images/favicons/manifest.json">
        <link rel="mask-icon" href="assets/images/favicons/safari-pinned-tab.svg" color="#5bbad5">
        <link href="assets/plugins/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css">
        <?php require_once "viewer-pdf/viewerjs.php"; ?>
        <script>
            $(document).on('keydown keyup', function(e) {

                if (e.ctrlKey && (e.key == "p" || e.charCode == 16 || e.charCode == 112 || e.keyCode == 80)) {
                    alert("<?= $lang['Plzuseprintbutton']; ?>");
                    e.cancelBubble = true;
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    abort();
                }
            });
        </script>


        <style type="text/css">
            .toolbar button {
                background-color: #454545 !important;
            }

            #the-canvas {
                border: 1px solid black;
            }

            body {
                background-color: #eee;
                font-family: sans-serif;
                margin: 0;
            }

            #comment-wrapper2 {
                position: fixed;
                right: 0;
                top: 40px;
                bottom: 0;
                overflow: auto;
                width: 268px;
                background-color: #eaeaea;
                border-left: 1px solid #d0d0d0;
            }

            #comment-wrapper h4 {
                margin: 10px;
            }

            #comment-wrapper {
                position: fixed;
                left: 0%;
                top: 45px;
                right: 0;
                bottom: 0;
                overflow: auto;
                width: 280px;
                background: rgb(11, 175, 32);
                ;
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
                color: #ffffff;
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
        </style>

        <?php if ($CheckinCheckout == '0') { ?>
            <style type="text/css">
                .toolbar button {
                    background-color: #454545 !important;
                }

                #the-canvas {
                    border: 1px solid black;
                }

                body {
                    background-color: #eee;
                    font-family: sans-serif;
                    margin: 0;
                }

                #comment-wrapper2 {
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
                    top: 45px;
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
                    color: #ffffff;
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

    <body tabindex="1" class="loadingInProgress">

        <div id="outerContainer">

            <div id="sidebarContainer">
                <div id="toolbarSidebar">
                    <div class="splitToolbarButton toggled">
                        <button id="viewThumbnail" class="toolbarButton group toggled" title="<?= $lang['Thumbnails']; ?>" tabindex="2" data-l10n-id="thumbs">
                            <span data-l10n-id="thumbs_label"><?= $lang['Thumbnails']; ?></span>
                        </button>
                        <button id="viewOutline" class="toolbarButton group" title="Show Document Outline (double-click to expand/collapse all items)" tabindex="3" data-l10n-id="document_outline">
                            <span data-l10n-id="document_outline_label"><?= $lang['Doc_Otlne']; ?></span>
                        </button>
                        <button id="viewAttachments" class="toolbarButton group" title="Show Attachments" tabindex="4" data-l10n-id="attachments">
                            <span data-l10n-id="attachments_label"><?= $lang['Attachments']; ?></span>
                        </button>
                    </div>
                </div>
                <div id="sidebarContent">
                    <div id="thumbnailView">
                    </div>
                    <div id="outlineView" class="hidden">
                    </div>
                    <div id="attachmentsView" class="hidden">
                    </div>
                </div>
            </div> <!-- sidebarContainer -->

            <div id="mainContainer">
                <div class="findbar hidden doorHanger hiddenSmallView" id="findbar">
                    <label for="findInput" class="toolbarLabel" data-l10n-id="find_label"><?= $lang['Find']; ?></label>
                    <input id="findInput" class="toolbarField" tabindex="91">
                    <div class="splitToolbarButton">
                        <button class="toolbarButton findPrevious" title="" id="findPrevious" tabindex="92" data-l10n-id="find_previous">
                            <span data-l10n-id="find_previous_label"><?= $lang['Previous']; ?></span>
                        </button>
                        <div class="splitToolbarButtonSeparator"></div>
                        <button class="toolbarButton findNext" title="" id="findNext" tabindex="93" data-l10n-id="find_next">
                            <span data-l10n-id="find_next_label"><?= $lang['Next']; ?></span>
                        </button>
                    </div>

                    <!--highlight-->
                    <input type="checkbox" id="findHighlightAll" class="toolbarField" tabindex="94">
                    <label for="findHighlightAll" class="toolbarLabel" data-l10n-id="find_highlight"><?= $lang['Highlight_all']; ?></label>

                    <input type="checkbox" id="findMatchCase" class="toolbarField" tabindex="95">
                    <label for="findMatchCase" class="toolbarLabel" data-l10n-id="find_match_case_label"><?= $lang['Mtch_cse']; ?></label>
                    <span id="findResultsCount" class="toolbarLabel hidden"></span>
                    <span id="findMsg" class="toolbarLabel"></span>
                </div> <!-- findbar -->

                <div id="secondaryToolbar" class="secondaryToolbar hidden doorHangerRight">
                    <div id="secondaryToolbarButtonContainer">
                        <button id="secondaryPresentationMode" class="secondaryToolbarButton presentationMode visibleLargeView" title="Switch to Presentation Mode" tabindex="51" data-l10n-id="presentation_mode">
                            <span data-l10n-id="presentation_mode_label"><?= $lang['Prssttion_Mode']; ?></span>
                        </button>

                        <button id="secondaryOpenFile" class="secondaryToolbarButton openFile visibleLargeView" title="Open File" tabindex="52" data-l10n-id="open_file" disabled>
                            <span data-l10n-id="open_file_label"><?= $lang['Open']; ?></span>
                        </button>
                        <button id="secondaryPrint" class="secondaryToolbarButton print visibleMediumView" title="Print" tabindex="53" data-l10n-id="print" <?php
                                                                                                                                                            if ($rwgetRole['pdf_print'] == '1') {
                                                                                                                                                            } else {
                                                                                                                                                                echo 'disabled';
                                                                                                                                                            }
                                                                                                                                                            ?>>
                            <span data-l10n-id="print_label"><?= $lang['Print']; ?></span>
                        </button>

                        <button id="secondaryDownload" class="secondaryToolbarButton download visibleMediumView" title="<?= $lang['Download']; ?>" tabindex="54" data-l10n-id="download" <?php
                                                                                                                                                                                            if ($rwgetRole['pdf_download'] == '1') {
                                                                                                                                                                                            } else {
                                                                                                                                                                                                echo 'disabled';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <span data-l10n-id="download_label"><?= $lang['Download']; ?></span>
                        </button>

                        <a href="#" id="secondaryViewBookmark" class="secondaryToolbarButton bookmark visibleSmallView" title="Current view (copy or open in new window)" tabindex="55" data-l10n-id="bookmark" disabled>
                            <span data-l10n-id="bookmark_label"><?= $lang['Current_View']; ?></span>
                        </a>

                        <div class="horizontalToolbarSeparator visibleLargeView"></div>

                        <button id="firstPage" class="secondaryToolbarButton firstPage" title="<?= $lang['Go_to_Fst_Pge']; ?>" tabindex="56" data-l10n-id="first_page">
                            <span data-l10n-id="first_page_label"><?= $lang['Go_to_Fst_Pge']; ?></span>
                        </button>
                        <button id="lastPage" class="secondaryToolbarButton lastPage" title="<?= $lang['Go_to_Lst_Pge']; ?>" tabindex="57" data-l10n-id="last_page">
                            <span data-l10n-id="last_page_label"><?= $lang['Go_to_Lst_Pge']; ?></span>
                        </button>

                        <div class="horizontalToolbarSeparator"></div>

                        <button id="pageRotateCw" class="secondaryToolbarButton rotateCw" title="<?= $lang['Rtte_Clckwse']; ?>" tabindex="58" data-l10n-id="page_rotate_cw">
                            <span data-l10n-id="page_rotate_cw_label"><?= $lang['Rtte_Clckwse']; ?></span>
                        </button>
                        <button id="pageRotateCcw" class="secondaryToolbarButton rotateCcw" title="<?= $lang['Rotate_Counterclockwise']; ?>" tabindex="59" data-l10n-id="page_rotate_ccw">
                            <span data-l10n-id="page_rotate_ccw_label"><?= $lang['Rotate_Counterclockwise']; ?></span>
                        </button>

                        <div class="horizontalToolbarSeparator"></div>

                        <button id="toggleHandTool" class="secondaryToolbarButton handTool" title="<?= $lang['Eble_hnd_tl']; ?>" tabindex="60" data-l10n-id="hand_tool_enable">
                            <span data-l10n-id="hand_tool_enable_label"><?= $lang['Eble_hnd_tl']; ?></span>
                        </button>

                        <div class="horizontalToolbarSeparator"></div>

                        <button id="documentProperties" class="secondaryToolbarButton documentProperties" title="<?= $lang['Document_Ppts']; ?>" tabindex="61" data-l10n-id="document_properties">
                            <span data-l10n-id="document_properties_label"><?= $lang['Document_Ppts']; ?></span>
                        </button>
                    </div>
                </div> <!-- secondaryToolbar -->

                <div class="toolbar">
                    <div id="toolbarContainer">
                        <div id="toolbarViewer">
                            <div id="toolbarViewerLeft">
                                <button id="sidebarToggle" class="toolbarButton" title="Toggle Sidebar" tabindex="11" data-l10n-id="toggle_sidebar">
                                    <span data-l10n-id="toggle_sidebar_label">Toggle Sidebar</span>
                                </button>
                                <div class="toolbarButtonSpacer"></div>
                                <button id="viewFind" class="toolbarButton group hiddenSmallView" title="Find in Document" tabindex="12" data-l10n-id="findbar">
                                    <span data-l10n-id="findbar_label"><?= $lang['Find']; ?></span>
                                </button>
                                <div class="splitToolbarButton">
                                    <button class="toolbarButton pageUp" title="Previous Page" id="previous" tabindex="13" data-l10n-id="previous">
                                        <span data-l10n-id="previous_label"><?= $lang['Previous']; ?></span>
                                    </button>
                                    <div class="splitToolbarButtonSeparator"></div>
                                    <button class="toolbarButton pageDown" title="Next Page" id="next" tabindex="14" data-l10n-id="next">
                                        <span data-l10n-id="next_label"><?= $lang['Next']; ?></span>
                                    </button>
                                </div>
                                <input type="number" id="pageNumber" class="toolbarField pageNumber" title="Page" value="1" size="4" min="1" tabindex="15" data-l10n-id="page">
                                <span id="numPages" class="toolbarLabel"></span>

                                <button type="button" title="Next Page" id="comment_button" style="color: #ffffff; margin-top: 3px;"><?= $lang['Cmnt']; ?></button>


                            </div>

                            <div id="toolbarViewerRight">
                                <button type="button" title="<?= $lang['show_log_file']; ?>" id="log" style="color: #ffffff; margin-top: 3px;"><?= $lang['log']; ?></button>
                                <button id="presentationMode" class="toolbarButton presentationMode hiddenLargeView" title="Switch to Presentation Mode" tabindex="31" data-l10n-id="presentation_mode">
                                    <span data-l10n-id="presentation_mode_label"><?= $lang['Prssttion_Mode']; ?></span>
                                </button>

                                <button id="openFile" class="toolbarButton openFile hiddenLargeView" title="Open File" tabindex="32" data-l10n-id="open_file" disabled>
                                    <span data-l10n-id="open_file_label"><?= $lang['Open']; ?></span>
                                </button>

                                <button id="print1" class="toolbarButton print hiddenMediumView" title="Print" tabindex="33" data-l10n-id="print" <?php
                                                                                                                                                    if ($rwgetRole['pdf_print'] == '1') {
                                                                                                                                                    } else {
                                                                                                                                                        echo 'disabled';
                                                                                                                                                    }
                                                                                                                                                    ?>>
                                    <span data-l10n-id="print_label"><?= $lang['Print']; ?></span>
                                </button>
                                <button id="download" class="toolbarButton download hiddenMediumView" title="<?= $lang['Download']; ?>" tabindex="-1" data-l10n-id="download" <?php
                                                                                                                                                                                if ($rwgetRole['pdf_download'] == '1') {
                                                                                                                                                                                } else {
                                                                                                                                                                                    echo 'disabled';
                                                                                                                                                                                }
                                                                                                                                                                                ?>>
                                    <span data-l10n-id="download_label"><?= $lang['Download']; ?></span>
                                </button>

                                <a href="#" id="viewBookmark" class="toolbarButton bookmark hiddenSmallView" title="Current view (copy or open in new window)" tabindex="35" data-l10n-id="bookmark" disabled>
                                    <span data-l10n-id="bookmark_label"><?= $lang['Current_View']; ?></span>
                                </a>
                                <div class="verticalToolbarSeparator hiddenSmallView"></div>


                                <button id="secondaryToolbarToggle" class="toolbarButton" title="Tools" tabindex="36" data-l10n-id="tools">
                                    <span data-l10n-id="tools_label"><?= $lang['Tools']; ?></span>
                                </button>


                            </div>
                            <div id="toolbarViewerMiddle">
                                <div class="splitToolbarButton">
                                    <button id="zoomOut" class="toolbarButton zoomOut" title="<?= $lang['Zoom_Out']; ?>" tabindex="21" data-l10n-id="zoom_out">
                                        <span data-l10n-id="zoom_out_label"><?= $lang['Zoom_Out']; ?></span>
                                    </button>
                                    <div class="splitToolbarButtonSeparator"></div>
                                    <button id="zoomIn" class="toolbarButton zoomIn" title="<?= $lang['Zoom_In']; ?>" tabindex="22" data-l10n-id="zoom_in">
                                        <span data-l10n-id="zoom_in_label"><?= $lang['Zoom_In']; ?></span>
                                    </button>
                                </div>
                                <span id="scaleSelectContainer" class="dropdownToolbarButton">
                                    <select id="scaleSelect" title="Zoom" tabindex="23" data-l10n-id="zoom">
                                        <option id="pageFitOption" title="" value="page-fit" selected="selected" data-l10n-id="page_scale_fit"><?= $lang['Fit_Page']; ?></option>
                                        <option id="pageAutoOption" title="" value="auto" data-l10n-id="page_scale_auto"><?= $lang['Atmtc_Zoom']; ?></option>
                                        <option id="pageActualOption" title="" value="page-actual" data-l10n-id="page_scale_actual"><?= $lang['Act_Sz']; ?></option>

                                        <option id="pageWidthOption" title="" value="page-width" data-l10n-id="page_scale_width"><?= $lang['Fl_Wth']; ?></option>
                                        <option id="customScaleOption" title="" value="custom" disabled="disabled" hidden="true"></option>
                                        <option title="" value="0.5" data-l10n-id="page_scale_percent" data-l10n-args='{ "scale": 50 }'>50%</option>
                                        <option title="" value="0.75" data-l10n-id="page_scale_percent" data-l10n-args='{ "scale": 75 }'>75%</option>
                                        <option title="" value="1" data-l10n-id="page_scale_percent" data-l10n-args='{ "scale": 100 }'>100%</option>
                                        <option title="" value="1.25" data-l10n-id="page_scale_percent" data-l10n-args='{ "scale": 125 }'>125%</option>
                                        <option title="" value="1.5" data-l10n-id="page_scale_percent" data-l10n-args='{ "scale": 150 }'>150%</option>
                                        <option title="" value="2" data-l10n-id="page_scale_percent" data-l10n-args='{ "scale": 200 }'>200%</option>
                                        <option title="" value="3" data-l10n-id="page_scale_percent" data-l10n-args='{ "scale": 300 }'>300%</option>
                                        <option title="" value="4" data-l10n-id="page_scale_percent" data-l10n-args='{ "scale": 400 }'>400%</option>
                                    </select>
                                </span>
                            </div>
                        </div>
                        <div id="loadingBar">
                            <div class="progress">
                                <div class="glimmer">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <menu type="context" id="viewerContextMenu">
                    <menuitem id="contextFirstPage" label="First Page"
                        data-l10n-id="first_page">
                    </menuitem>
                    <menuitem id="contextLastPage" label="Last Page"
                        data-l10n-id="last_page">
                    </menuitem>
                    <menuitem id="contextPageRotateCw" label="Rotate Clockwise"
                        data-l10n-id="page_rotate_cw">
                    </menuitem>
                    <menuitem id="contextPageRotateCcw" label="Rotate Counter-Clockwise"
                        data-l10n-id="page_rotate_ccw">
                    </menuitem>
                </menu>

                <div id="viewerContainer" tabindex="0">

                    <div id="viewer" class="pdfViewer">

                    </div>

                </div>
                <?php require_once 'checkin-checkout-html.php'; ?>
                <div id="comment-wrapper" style="display:none;">
                    <h4><?= $lang['Cmnt']; ?></h4>
                    <div class="comment-list">
                        <div class="comment-list-container">
                            <!--div class="comment-list-item"-->
                            <div id="comentAdd">
                                <?php
                                mysqli_set_charset($db_con, "utf8");
                                $getTiketid = mysqli_query($db_con, "select  ticket_id from tbl_doc_assigned_wf where doc_id='$id1' order by id desc") or die('Error: ' . mysqli_error($db_con));
                                $rwgetTiketid = mysqli_fetch_assoc($getTiketid);
                                //get workflow name
                                $getWfId = mysqli_query($db_con, "select ttm.workflow_id from tbl_doc_assigned_wf daw inner join tbl_task_master ttm on daw.task_id = ttm.task_id where daw.ticket_id='$rwgetTiketid[ticket_id]'");
                                $rwgetWfId = mysqli_fetch_assoc($getWfId);

                                $getWfName = mysqli_query($db_con, "select workflow_name from tbl_workflow_master where workflow_id='$rwgetWfId[workflow_id]'");
                                $rwgetWfName = mysqli_fetch_assoc($getWfName);
                                $proclist = mysqli_query($db_con, "select * from tbl_doc_assigned_wf where ticket_id='$rwgetTiketid[ticket_id]'");

                                $rwProclist = mysqli_fetch_assoc($proclist);

                                $comment = mysqli_query($db_con, "select * from tbl_task_comment where tickt_id= '$rwProclist[ticket_id]' order by comment_time desc");
                                if (mysqli_num_rows($comment) > 0) {
                                    while ($rwcomment = mysqli_fetch_assoc($comment)) {
                                        mysqli_set_charset($db_con, "utf8");
                                        $usr = mysqli_query($db_con, "select first_name, last_name,profile_picture from tbl_user_master where user_id='$rwcomment[user_id]'");
                                        $rwUsr = mysqli_fetch_assoc($usr);
                                        $ext = pathinfo($rwcomment['comment_desc'], PATHINFO_EXTENSION);
                                        // echo( $ext)."<br>";
                                ?>
                                        <div class="chat-conversation">
                                            <div class="comment-list-item">
                                                <ul class="conversation-list nicescroll anotecoment" style="height: Auto;">
                                                    <li class="clearfix">
                                                        <div class="chat-avatar">
                                                            <?php if (!empty($rwUsr['profile_picture'])) { ?>
                                                                <img src="data:image/jpeg;base64,<?php echo base64_encode($rwUsr['profile_picture']); ?>" alt="Image">
                                                            <?php } else { ?>
                                                                <img src="<?= BASE_URL ?>assets/images/avatar.png" alt="Image">
                                                            <?php } ?>

                                                        </div>

                                                        <div class="conversation-text">

                                                            <div class="ctext-wrap">
                                                                <span><?php
                                                                        echo '<strong>' . $rwUsr['first_name'] . ' ' . $rwUsr['last_name'] . '</strong>' . '<br>';
                                                                        if (!empty($rwcomment['comment']) || !empty($rwcomment['comment_desc'])) {
                                                                            if ($rwcomment['comment_desc'] != "") {
                                                                                echo "<p>";
                                                                                if ($ext) {
                                                                        ?>
                                                                                <p><a href="anott/view?cid=<?= urlencode(base64_encode($rwcomment['id'])) ?>" target="_blank"><i class="fa fa-file cmt-file"></i></a></p>
                                                                        <?php
                                                                                }
                                                                                echo "</p>";
                                                                            }
                                                                            if (!empty($rwcomment['comment'])) {
                                                                                echo "<p><br>" . $rwcomment['comment'] . "</p>";
                                                                            }
                                                                        ?>
                                                                        <br />

                                                                    <?php
                                                                        }
                                                                        if (!empty($rwcomment['task_status'])) {
                                                                            echo '<strong>Action: </strong>' . $rwcomment['task_status'] . '<br>';
                                                                        }
                                                                    ?>
                                                                </span>
                                                                <div class="clearfix"></div>
                                                                <span>
                                                                    <?php echo date("j F, Y, H:i", strtotime($rwcomment['comment_time'])); ?></span>
                                                            </div>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    <?php
                                    }
                                } else {
                                    ?>
                                    <div class="comment-list-item"><?= $lang['no_comment']; ?></div>
                                <?php } ?>
                            </div>
                            <!--/div-->
                        </div>
                        <?php if ($rwgetRole['file_coment'] == '1' && (isset($rwTask['ticket_id']) && isset($rwTask['task_id']))) { ?>
                            <div class="row">
                                <div class="comment-list-form1">
                                    <button data-toggle="modal" data-target="#con-close-modal-comment" class="btn btn-primary btn-sm add m-l-5" id="comment" style="margin-top:4px;" title="Add Comment"><?= $lang['add_comment']; ?>
                                    </button>

                                </div>

                            </div>
                        <?php } ?>

                    </div>
                </div>
                <div id="comment-wrapper2" style="display:none;">

                    <?php if ($rwgetRole['wf_log'] == '1' || $rwgetRole['review_log'] == '1') { ?>
                        <div class="panel-body">
                            <ul class="nav nav-pills">
                                <?php if ($rwgetRole['wf_log'] == '1') { ?>
                                    <li class="active tbs"><a href="#navpills-1" data-toggle="tab" aria-expanded="true"><?= $lang['activity_log']; ?></a></li>
                                <?php } ?>
                                <?php if ($rwgetRole['review_log'] == '1') { ?>
                                    <li class="<?= ($rwgetRole['wf_log'] != '1' ? 'active' : '') ?> tbs"><a href="#navpills-2" data-toggle="tab" aria-expanded="false" style="padding: 0px 11px;"><?= $lang['review_log']; ?></a></li>
                                <?php } ?>
                            </ul>
                            <div class="tab-content br-n pn">
                                <?php if ($rwgetRole['wf_log'] == '1') { ?>
                                    <div id="navpills-1" class="back tab-pane active">
                                        <?php
                                        $pdflogSql = "select * from tbl_ezeefile_logs_wf where doc_id='$id1' ";

                                        if ($_SESSION[cdes_user_id] != 1) {
                                            // $pdflogSql.=" and user_id='$_SESSION[cdes_user_id]'";   
                                        }
                                        $pdflog = mysqli_query($db_con, $pdflogSql);
                                        $j = 0;
                                        $logrw = mysqli_num_rows($pdflog);
                                        if ($logrw > 0) {
                                            while ($rwpdflog = mysqli_fetch_assoc($pdflog)) {
                                                echo '<p class="m-b-5 font-13"><strong>' . $lang['Action'] . ' : </strong>' . $rwpdflog['action_name'] . '</p>';
                                                echo '<p class="m-b-5 font-13"><strong>' . $lang['action_by'] . ' : </strong>' . $rwpdflog['user_name'] . '</p>';
                                                echo '<p class="m-b-5 font-13"><strong>' . $lang['action_time'] . ' : </strong>' . date('d M Y, H:i ', strtotime($rwpdflog['start_date'])) . '</p>';
                                                if ($j < $rwcount) {
                                                    echo '<hr>';
                                                }
                                                $j++;
                                            }
                                        } else {
                                            echo '<center>' . $lang['activity_logs'] . '</center><hr style="color:#000;">';
                                        }
                                        ?>
                                    </div>
                                <?php } ?>
                                <?php if ($rwgetRole['review_log'] == '1') { ?>
                                    <div id="navpills-2" class="back tab-pane<?= ($rwgetRole['wf_log'] != '1' ? 'active' : '') ?>">
                                        <?php
                                        $rlog_sql = "select rl.*,u.first_name,u.last_name from tbl_reviews_log rl left join tbl_user_master u on rl.user_id=u.user_id where 1=1 " . $in_review;
                                        if ($_SESSION[cdes_user_id] != 1) {
                                            //   $rlog_sql.=" and rl.user_id='$_SESSION[cdes_user_id]'";   
                                        }
                                        $rlog_sql .= " and rl.doc_id='$id1' order by id desc";

                                        $rlog_query = mysqli_query($db_con, $rlog_sql);
                                        $i = 1;
                                        $rwcount = mysqli_num_rows($rlog_query);
                                        if ($rwcount > 0) {

                                            while ($rlog_res = mysqli_fetch_assoc($rlog_query)) {
                                                echo '<p class="m-b-5 font-13"><strong>' . $lang['Action'] . ' : </strong> ' . $rlog_res['action_name'] . '</p>';
                                                echo '<p class="m-b-5 font-13"><strong> ' . $lang['action_by'] . ' : </strong> ' . $rlog_res['first_name'] . ' ' . $rlog_res['last_name'] . '</p>';
                                                echo '<p class="m-b-5 font-13"><strong> ' . $lang['action_time'] . ' : </strong>' . date('d M Y, H:i', strtotime($rlog_res['start_date'])) . '</p>';
                                                if ($i < $rwcount) {
                                                    echo '<hr>';
                                                }
                                                $i++;
                                            }
                                        } else {
                                            echo '<center>' . $lang['No_Review_Log_found'] . '</center><hr style="color:#000;">';
                                        }
                                        ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <div id="errorWrapper" hidden='true'>
                    <div id="errorMessageLeft">
                        <span id="errorMessage"></span>
                        <button id="errorShowMore" data-l10n-id="error_more_info">
                            More Information
                        </button>
                        <button id="errorShowLess" data-l10n-id="error_less_info" hidden='true'>
                            Less Information
                        </button>
                    </div>
                    <div id="errorMessageRight">
                        <button id="errorClose" data-l10n-id="error_close">
                            Close
                        </button>
                    </div>
                    <div class="clearBoth"></div>
                    <textarea id="errorMoreInfo" hidden='true' readonly="readonly"></textarea>
                </div>
            </div> <!-- mainContainer -->

            <div id="overlayContainer" class="hidden">
                <div id="passwordOverlay" class="container hidden">
                    <div class="dialog">
                        <div class="row">
                            <p id="passwordText" data-l10n-id="password_label"><?= $lang['E_pwd_to_open_ths_PDF_fle']; ?></p>
                        </div>
                        <div class="row">
                            <!-- The type="password" attribute is set via script, to prevent warnings in Firefox for all http:// documents. -->
                            <input id="password" class="toolbarField">
                        </div>
                        <div class="buttonRow">
                            <button id="passwordCancel" class="overlayButton"><span data-l10n-id="password_cancel"><?= $lang['Cancel']; ?></span></button>
                            <button id="passwordSubmit" class="overlayButton"><span data-l10n-id="password_ok">ok</span></button>
                        </div>
                    </div>
                </div>
                <div id="documentPropertiesOverlay" class="container hidden">
                    <div class="dialog">
                        <div class="row">
                            <span data-l10n-id="document_properties_file_name">File name:</span>
                            <p id="fileNameField">-</p>
                        </div>
                        <div class="row">
                            <span data-l10n-id="document_properties_file_size">File size:</span>
                            <p id="fileSizeField">-</p>
                        </div>
                        <div class="separator"></div>
                        <div class="row">
                            <span data-l10n-id="document_properties_title">Title:</span>
                            <p id="titleField">-</p>
                        </div>
                        <div class="row">
                            <span data-l10n-id="document_properties_author">Author:</span>
                            <p id="authorField">-</p>
                        </div>
                        <div class="row">
                            <span data-l10n-id="document_properties_subject">Subject:</span>
                            <p id="subjectField">-</p>
                        </div>
                        <div class="row">
                            <span data-l10n-id="document_properties_keywords">Keywords:</span>
                            <p id="keywordsField">-</p>
                        </div>
                        <div class="row">
                            <span data-l10n-id="document_properties_creation_date">Creation Date:</span>
                            <p id="creationDateField">-</p>
                        </div>
                        <div class="row">
                            <span data-l10n-id="document_properties_modification_date">Modification Date:</span>
                            <p id="modificationDateField">-</p>
                        </div>
                        <div class="row">
                            <span data-l10n-id="document_properties_creator">Creator:</span>
                            <p id="creatorField">-</p>
                        </div>
                        <div class="separator"></div>
                        <div class="row">
                            <span data-l10n-id="document_properties_producer">PDF Producer:</span>
                            <p id="producerField">-</p>
                        </div>
                        <div class="row">
                            <span data-l10n-id="document_properties_version">PDF Version:</span>
                            <p id="versionField">-</p>
                        </div>
                        <div class="row">
                            <span data-l10n-id="document_properties_page_count">Page Count:</span>
                            <p id="pageCountField">-</p>
                        </div>
                        <div class="buttonRow">
                            <button id="documentPropertiesClose" class="overlayButton" onclick="window.location.reload();"><span data-l10n-id="document_properties_close">Close</span></button>
                        </div>
                    </div>
                </div>
                <div id="printServiceOverlay" class="container hidden">
                    <div class="dialog">
                        <div class="row">
                            <span data-l10n-id="print_progress_message">Preparing document for printing…</span>
                        </div>
                        <div class="row">
                            <progress value="0" max="100"></progress>
                            <span data-l10n-id="print_progress_percent" data-l10n-args='{ "progress": 0 }' class="relative-progress">0%</span>
                        </div>
                        <div class="buttonRow">
                            <button id="printCancel" class="overlayButton"><span data-l10n-id="print_progress_close">Cancel</span></button>
                        </div>
                    </div>
                </div>
            </div> <!-- overlayContainer -->

        </div> <!-- outerContainer -->
        <div id="printContainer"></div>
        <input type="hidden" name="old_doc" id="old_doc" value="<?php echo $rwFile['old_doc_name']; ?>">

        <script>
            $("body").on("contextmenu", function(e) {
                // return false;
            });
            document.title = "<?php echo $rwFile['old_doc_name']; ?>";
            var pageTitle = document.title;
            //alert(pageTitle);
        </script>

        <?php
        if ($rwgetRole['pdf_print'] == '1') {
        } else {
        ?>
            <style type="text/css" media="print">
                * {
                    display: none;
                }
            </style>
        <?php } ?>
        <div id="myModal" class="modal modal-primary">
            <!-- Modal content -->
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <span class="close">×</span>
                        <h4> <?= $lang['please_print_reason']; ?></h4>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <textarea id="reason" class="form-control" placeholder="<?= $lang['pls_gv_vld_rson_fr_pntg_ts_fle']; ?>"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="id" id="ID">
                        <button type="button" class="btn btn-outline  pull-left" data-dismiss="modal" id="cls"> <?= $lang['Close']; ?></button>
                        <button id="print" class="btn btn-primary disabled" disabled>
                            <?= $lang['Submit']; ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </body>

    <script>
        // Get the modal
        var modal = document.getElementById('myModal');

        // Get the button that opens the modal
        var btn = document.getElementById("myBtn");

        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];

        // define document id
        var did = encodeURIComponent(btoa(<?= $docId ?>));

        // doc id for time log
        var doc_id = did;

        // When the user clicks the button, open the modal
        $("#print1").click(function() {
            // var ival=$(this).attr("data");
            modal.style.display = "block";
        });

        // When the user clicks on <span> (x), close the modal
        span.onclick = function() {
            modal.style.display = "none";
        }
        $("#cls").click(function() {
            modal.style.display = "none";
        });
        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                //modal.style.display = "none";
            }
        }
        $("#reason").bind("keydown keyup", function() {
            if ($(this).val().length != 0) {
                $("#print").prop('disabled', false);
                $("#print").removeClass("disabled");
            } else {
                $("#print").prop('disabled', true);
                $("#print").addClass("disabled");
            }
        });
        $("#print").click(function() {

            var reason = $("#reason").val();
            //alert(reason);
            printwithlog(reason);
        });

        function printwithlog(reason) {

            if (reason.length === 0) {
                //  
                alert("<?= $lang['please_print_reason']; ?>");
                return false;
            } else {
                modal.style.display = "none";
                $.post("viewer-pdf/printajaxlog.php", {
                    remark: reason,
                    docid: "<?php echo $dcid; ?>",
                    docname: "<?php echo $docName; ?>",
                    slid: "<?php echo $slid; ?>"
                }, function(result, status) {
                    if (status == 'success') {
                        window.location = 'printInfo?id=' + did + "<?php echo ($_GET['chk'] == 'rw' ? '&chk=rw' : '') ?>";
                        //alert(result);
                        //$("#child1").html(result);
                        //alert(result);
                    }
                });
            }
        }
        $(document).ready(function() {
            $("html").bind("contextmenu", function(e) {
                e.preventDefault();
            });
        });

        window.onbeforeunload = function() {
            $.post("application/ajax/removeTempFiles.php", {
                filepath: "<?php echo '../' . $localPath; ?>"
            }, function(result) {});
            return;
        };
    </script>
    <!--  sk@271018: Toggle comment and log on button click  -->
    <script type="text/javascript">
        $(document).ready(function(e) {
            // Show Log 
            $("#log").click(function(e) {
                $('#comment-wrapper2').toggle();
            });

            // Show Comment.
            $("#comment_button").click(function(e) {
                $('#comment-wrapper').toggle();
            });

        });
    </script>
    <script>
        window.onbeforeunload = function () {
            $.post("application/ajax/removeTempFiles.php", {filepath: "<?php //echo '../' . $localPath; ?>"}, function (result) {
            });
            return;
        };
    </script>
    <?php if ($pass_check['is_protected'] == 1) { ?>
        <script>
            $(document).ready(function() {
                var id = '#dialog';
                var maskHeight = $(document).height();
                var maskWidth = $(window).width();
                $('#mask').css({
                    'width': maskWidth,
                    'height': maskHeight
                });
                $('#mask').show();
                $("#outerContainer").hide();

                var winH = $(window).height();
                var winW = $(window).width();
                $(id).css('top', winH / 2 - $(id).height() / 2);
                $(id).css('left', winW / 2 - $(id).width() / 2);
                $(id).fadeIn(1000);
                $('.window .close').click(function(e) {
                    e.preventDefault();
                    $('#mask').hide();
                    $('.window').hide();
                });
            });

            function clearForm() {
                $("#pass_value").reset();
            }
        </script>
    <?php } ?>
    <script>
        function password_check(event) {
            event.preventDefault();
            var pass = $("#pass_value").val();
            var password = $("#doc_pass").val();
            var fpass = SHA1(pass);

            if (password == fpass) {
                $("#dialog").hide();
                $("#mask").hide();
                $("#outerContainer").show();
            } else {
                $("#boxes").hide();
                $("#mask").hide();
                $("#outerContainer").hide();
                taskFailed("<?php echo basename($_SERVER['REQUEST_URI']); ?>", "Password is not valid");
            }

        }
    </script>
    <?php require_once 'checkin-checkout-js.php'; ?>

    </html>

    <?php require_once 'checkin-checkout-php.php'; ?>
<?php } else { ?>
    <script>
        alert("File Is Locked Please Contact To Administrator");
        window.close();
    </script>
<?php } ?>