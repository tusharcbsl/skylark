<?php
require 'sessionstart.php';
require_once './application/config/database.php';
require_once './loginvalidate.php';
//  require_once './application/pages/head.php';
//for user role
$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

$rwgetRole = mysqli_fetch_assoc($chekUsr);
$uid = base64_decode(urldecode($_GET['i']));
if ($uid != $_SESSION['cdes_user_id']) {
    // header('Location:./index');
}
if (isset($_SESSION['lang'])) {
    $file = $_SESSION['lang'] . ".json";
} else {
    if (isset($_SESSION['cdes_user_id'])) {
        $LangQuery = mysqli_query($db_con, "SELECT * FROM `tbl_user_master` WHERE user_id='$_SESSION[cdes_user_id]'") or die('error : ' . mysqli_error($db_con));
        $LangRow = mysqli_fetch_array($LangQuery);
        if (!empty($LangRow['lang'])) {
            $file = "./" . $LangRow['lang'] . ".json";
        } else {
            $file = "./English.json";
        }
    }
}
$data = file_get_contents($file);
$lang = json_decode($data, true);
$id1 = base64_decode(urldecode($_GET['id'])); //doc_id
//$id = base64_decode(urldecode($_GET['id']));  //doc asign id
if ($_GET['chk'] == "rw") {
    $file = mysqli_query($db_con, "select doc_name, doc_path, doc_extn, old_doc_name,File_Number from tbl_document_reviewer where doc_id='$id1'") or die('error' . mysqli_error($db_con));
} else {
    $file = mysqli_query($db_con, "select doc_name, filename, doc_path, doc_extn, old_doc_name,checkin_checkout from tbl_document_master where doc_id='$id1'") or die('error' . mysqli_error($db_con));
}
$rwFile = mysqli_fetch_assoc($file);
$fileName = $rwFile['old_doc_name'];
$doc_old_name = $rwFile['old_doc_name'];
$filePath = $rwFile['doc_path'];
$slid = $rwFile['doc_name'];
$doc_extn = $rwFile['doc_extn'];
$CheckinCheckout = $rwFile['checkin_checkout'];
//$doc_temp_extn = isset($rwFile['doc_tem_ext']) ? $rwFile['doc_tem_ext'] : '';
$File_Number = isset($rwFile['File_Number']) ? $rwFile['File_Number'] : '';
$user = mysqli_query($db_con, "select * from tbl_user_master where user_id='$_SESSION[cdes_user_id]'");
$rwUser = mysqli_fetch_assoc($user);
$userSign = $rwUser['user_sign'];
$storage = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id='$slid'") or die('Error');
$rwStor = mysqli_fetch_assoc($storage);
$folderName = "./temp";
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
$lpath = explode("/", $filePath);
$ectns = explode(".", end($lpath));
if ($ectns[1] == "html") {
    $localPath = $folderName . '/' . preg_replace('/[^A-Za-z0-9\-]/', '', $fileName) . '.' . "html";
} else {
    $localPath = $folderName . '/' . str_replace("doc", "d", preg_replace('/[^A-Za-z0-9\-]/', '', $fileName)) . '.' . $doc_extn;
}

if (FTP_ENABLED) {
    if (!empty($fileName)) {
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
//echo $localPath;
//die;

// ASSign docid in different variable.
$docId=$id1;

if ($ectns[1] != "html") {
    require './phpWordOffice/OfficeConverter.php';
    $path = "phpWordOffice/TEMP";
    $converter = new OfficeConverter($localPath, $path . "/");

    if (!is_dir($path)) {
        mkdir($path, 0777, true);
    }

    $filename1 = preg_replace("/[^A-Za-z0-9]/", "", $fileName);
    $fnamepdf = $filename1 . '.pdf';
    $converter->convertTo($fnamepdf);
    $localPath = "phpWordOffice/TEMP/" . $fnamepdf;
    ?>
    <html dir="ltr" mozdisallowselectionprint moznomarginboxes>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
            <meta name="google" content="notranslate">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <title>Docx/Doc Viewer</title>
            <script type="text/javascript" src="./assets/js/jquery.min.js"></script>
            <link href="./viewer-pdf/modal.css" rel="stylesheet" type="text/css" />
            <link rel="stylesheet" href="./viewer-pdf/viewer.css">
            <script src="./viewer-pdf/compatibility.js"></script>
            <!-- This snippet is used in production (included from viewer.html) -->
            <link rel="resource" type="application/l10n" href="./viewer-pdf/locale/locale.properties">
            <script src="./viewer-pdf/l10n.js"></script>
            <script src="./viewer-pdf/build/pdf.js"></script>
            <link href="assets/css/components.css" rel="stylesheet" type="text/css"/>
            <link href="assets/css/core.css" rel="stylesheet" type="text/css"/>
            <link href="assets/css/icons.css" rel="stylesheet" type="text/css" />
            <link href="assets/css/pages.css" rel="stylesheet" type="text/css" />
            <link rel="apple-touch-icon" sizes="180x180" href="./assets/images/favicons/apple-touch-icon.png">
            <link rel="icon" type="image/png" href="./assets/images/favicons/favicon-32x32.png" sizes="32x32">
            <link rel="icon" type="image/png" href="./assets/images/favicons/favicon-16x16.png" sizes="16x16">
            <link rel="manifest" href="./assets/images/favicons/manifest.json">
            <link rel="mask-icon" href="./assets/images/favicons/safari-pinned-tab.svg" color="#5bbad5">
            <link href="assets/plugins/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css">
            <?php require_once"./viewer-pdf/viewerjs.php"; ?>
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
                        top: 45px;
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
                    .m-t-15{
                        margin-top: 15px;
                    }
                </style>   

            <?php } ?>
        </head>

        <body tabindex="1" class="loadingInProgress" >
            <div id="outerContainer">
                <div id="sidebarContainer">
                    <div id="toolbarSidebar">
                        <div class="splitToolbarButton toggled">
                            <button id="viewThumbnail" class="toolbarButton group toggled" title="Show Thumbnails" tabindex="2" data-l10n-id="thumbs">
                                <span data-l10n-id="thumbs_label">Thumbnails</span>
                            </button>
                            <button id="viewOutline" class="toolbarButton group" title="Show Document Outline (double-click to expand/collapse all items)" tabindex="3" data-l10n-id="document_outline">
                                <span data-l10n-id="document_outline_label">Document Outline</span>
                            </button>
                            <button id="viewAttachments" class="toolbarButton group" title="Show Attachments" tabindex="4" data-l10n-id="attachments">
                                <span data-l10n-id="attachments_label">Attachments</span>
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
                </div>  <!-- sidebarContainer -->

                <div id="mainContainer">
                    <div class="findbar hidden doorHanger hiddenSmallView" id="findbar">
                        <label for="findInput" class="toolbarLabel" data-l10n-id="find_label">Find:</label>
                        <input id="findInput" class="toolbarField" tabindex="91">
                        <div class="splitToolbarButton">
                            <button class="toolbarButton findPrevious" title="" id="findPrevious" tabindex="92" data-l10n-id="find_previous">
                                <span data-l10n-id="find_previous_label">Previous</span>
                            </button>
                            <div class="splitToolbarButtonSeparator"></div>
                            <button class="toolbarButton findNext" title="" id="findNext" tabindex="93" data-l10n-id="find_next">
                                <span data-l10n-id="find_next_label">Next</span>
                            </button>
                        </div>

                        <!--highlight-->
                        <input type="checkbox" id="findHighlightAll" class="toolbarField" tabindex="94">
                        <label for="findHighlightAll" class="toolbarLabel" data-l10n-id="find_highlight">Highlight all</label>

                        <input type="checkbox" id="findMatchCase" class="toolbarField" tabindex="95">
                        <label for="findMatchCase" class="toolbarLabel" data-l10n-id="find_match_case_label">Match case</label>
                        <span id="findResultsCount" class="toolbarLabel hidden"></span>
                        <span id="findMsg" class="toolbarLabel"></span>
                    </div>  <!-- findbar -->

                    <div id="secondaryToolbar" class="secondaryToolbar hidden doorHangerRight">
                        <div id="secondaryToolbarButtonContainer">
                            <button id="secondaryPresentationMode" class="secondaryToolbarButton presentationMode visibleLargeView" title="Switch to Presentation Mode" tabindex="51" data-l10n-id="presentation_mode">
                                <span data-l10n-id="presentation_mode_label">Presentation Mode</span>
                            </button>

                            <button id="secondaryOpenFile" class="secondaryToolbarButton openFile visibleLargeView" title="Open File" tabindex="52" data-l10n-id="open_file" disabled>
                                <span data-l10n-id="open_file_label">Open</span>
                            </button>
                            <button id="secondaryPrint" class="secondaryToolbarButton print visibleMediumView" title="Print" tabindex="53" data-l10n-id="print" <?php
                            if ($rwgetRole['pdf_print'] == '1') {
                                
                            } else {
                                echo'disabled';
                            }
                            ?>>
                                <span data-l10n-id="print_label">Print</span>
                            </button>

                            <button id="secondaryDownload" class="secondaryToolbarButton download visibleMediumView" title="Download" tabindex="54" data-l10n-id="download" <?php
                            if ($rwgetRole['pdf_download'] == '1') {
                                
                            } else {
                                echo'disabled';
                            }
                            ?>>
                                <span data-l10n-id="download_label">Download</span>
                            </button>

                            <a href="#" id="secondaryViewBookmark" class="secondaryToolbarButton bookmark visibleSmallView" title="Current view (copy or open in new window)" tabindex="55" data-l10n-id="bookmark" disabled>
                                <span data-l10n-id="bookmark_label">Current View</span>
                            </a>

                            <div class="horizontalToolbarSeparator visibleLargeView"></div>

                            <button id="firstPage" class="secondaryToolbarButton firstPage" title="Go to First Page" tabindex="56" data-l10n-id="first_page">
                                <span data-l10n-id="first_page_label">Go to First Page</span>
                            </button>
                            <button id="lastPage" class="secondaryToolbarButton lastPage" title="Go to Last Page" tabindex="57" data-l10n-id="last_page">
                                <span data-l10n-id="last_page_label">Go to Last Page</span>
                            </button>

                            <div class="horizontalToolbarSeparator"></div>

                            <button id="pageRotateCw" class="secondaryToolbarButton rotateCw" title="Rotate Clockwise" tabindex="58" data-l10n-id="page_rotate_cw">
                                <span data-l10n-id="page_rotate_cw_label">Rotate Clockwise</span>
                            </button>
                            <button id="pageRotateCcw" class="secondaryToolbarButton rotateCcw" title="Rotate Counterclockwise" tabindex="59" data-l10n-id="page_rotate_ccw">
                                <span data-l10n-id="page_rotate_ccw_label">Rotate Counterclockwise</span>
                            </button>

                            <div class="horizontalToolbarSeparator"></div>

                            <button id="toggleHandTool" class="secondaryToolbarButton handTool" title="Enable hand tool" tabindex="60" data-l10n-id="hand_tool_enable">
                                <span data-l10n-id="hand_tool_enable_label">Enable hand tool</span>
                            </button>

                            <div class="horizontalToolbarSeparator"></div>

                            <button id="documentProperties" class="secondaryToolbarButton documentProperties" title="Document Properties…" tabindex="61" data-l10n-id="document_properties">
                                <span data-l10n-id="document_properties_label">Document Properties…</span>
                            </button>
                        </div>
                    </div>  <!-- secondaryToolbar -->
                    <div class="toolbar">
                        <div id="toolbarContainer">
                            <div id="toolbarViewer">
                                <div id="toolbarViewerLeft">
                                    <button id="sidebarToggle" class="toolbarButton" title="Toggle Sidebar" tabindex="11" data-l10n-id="toggle_sidebar">
                                        <span data-l10n-id="toggle_sidebar_label">Toggle Sidebar</span>
                                    </button>
                                    <div class="toolbarButtonSpacer"></div>
                                    <button id="viewFind" class="toolbarButton group hiddenSmallView" title="Find in Document" tabindex="12" data-l10n-id="findbar">
                                        <span data-l10n-id="findbar_label">Find</span>
                                    </button>
                                    <div class="splitToolbarButton">
                                        <button class="toolbarButton pageUp" title="Previous Page" id="previous" tabindex="13" data-l10n-id="previous">
                                            <span data-l10n-id="previous_label">Previous</span>
                                        </button>
                                        <div class="splitToolbarButtonSeparator"></div>
                                        <button class="toolbarButton pageDown" title="Next Page" id="next" tabindex="14" data-l10n-id="next">
                                            <span data-l10n-id="next_label">Next</span>
                                        </button>
                                    </div>
                                    <input type="number" id="pageNumber" class="toolbarField pageNumber" title="Page" value="1" size="4" min="1" tabindex="15" data-l10n-id="page">
                                    <span id="numPages" class="toolbarLabel"></span>
                                </div>

                                <div id="toolbarViewerRight">
                                    <button id="presentationMode" class="toolbarButton presentationMode hiddenLargeView" title="Switch to Presentation Mode" tabindex="31" data-l10n-id="presentation_mode">
                                        <span data-l10n-id="presentation_mode_label">Presentation Mode</span>
                                    </button>

                                    <button id="openFile" class="toolbarButton openFile hiddenLargeView" title="Open File" tabindex="32" data-l10n-id="open_file" disabled>
                                        <span data-l10n-id="open_file_label">Open</span>
                                    </button>

                                    <button id="print1" class="toolbarButton print hiddenMediumView" title="Print" tabindex="33" data-l10n-id="print" <?php
                                    if ($rwgetRole['pdf_print'] == '1') {
                                        
                                    } else {
                                        echo'disabled';
                                    }
                                    ?>>
                                        <span data-l10n-id="print_label">Print</span>
                                    </button>
                                    <button id="download" class="toolbarButton download hiddenMediumView" title="Download" tabindex="-1" data-l10n-id="download" <?php
                                    if ($rwgetRole['pdf_download'] == '1') {
                                        
                                    } else {
                                        echo'disabled';
                                    }
                                    ?>>
                                        <span data-l10n-id="download_label">Download</span>
                                    </button>

                                    <a href="#" id="viewBookmark" class="toolbarButton bookmark hiddenSmallView" title="Current view (copy or open in new window)" tabindex="35" data-l10n-id="bookmark" disabled>
                                        <span data-l10n-id="bookmark_label">Current View</span>
                                    </a>
                                    <div class="verticalToolbarSeparator hiddenSmallView"></div>


                                    <button id="secondaryToolbarToggle" class="toolbarButton" title="Tools" tabindex="36" data-l10n-id="tools">
                                        <span data-l10n-id="tools_label">Tools</span>
                                    </button>


                                </div>
                                <div id="toolbarViewerMiddle">
                                    <div class="splitToolbarButton">
                                        <button id="zoomOut" class="toolbarButton zoomOut" title="Zoom Out" tabindex="21" data-l10n-id="zoom_out">
                                            <span data-l10n-id="zoom_out_label">Zoom Out</span>
                                        </button>
                                        <div class="splitToolbarButtonSeparator"></div>
                                        <button id="zoomIn" class="toolbarButton zoomIn" title="Zoom In" tabindex="22" data-l10n-id="zoom_in">
                                            <span data-l10n-id="zoom_in_label">Zoom In</span>
                                        </button>
                                    </div>
                                    <span id="scaleSelectContainer" class="dropdownToolbarButton">
                                        <select id="scaleSelect" title="Zoom" tabindex="23" data-l10n-id="zoom">
                                            <option id="pageAutoOption" title="" value="auto" selected="selected" data-l10n-id="page_scale_auto">Automatic Zoom</option>
                                            <option id="pageActualOption" title="" value="page-actual" data-l10n-id="page_scale_actual">Actual Size</option>
                                            <option id="pageFitOption" title="" value="page-fit" data-l10n-id="page_scale_fit">Fit Page</option>
                                            <option id="pageWidthOption" title="" value="page-width" data-l10n-id="page_scale_width">Full Width</option>
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
                                  data-l10n-id="first_page"></menuitem>
                        <menuitem id="contextLastPage" label="Last Page"
                                  data-l10n-id="last_page"></menuitem>
                        <menuitem id="contextPageRotateCw" label="Rotate Clockwise"
                                  data-l10n-id="page_rotate_cw"></menuitem>
                        <menuitem id="contextPageRotateCcw" label="Rotate Counter-Clockwise"
                                  data-l10n-id="page_rotate_ccw"></menuitem>
                    </menu>

                    <div id="viewerContainer" tabindex="0">

                        <div id="viewer" class="pdfViewer">    

                        </div>

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
                                <p id="passwordText" data-l10n-id="password_label">Enter the password to open this PDF file:</p>
                            </div>
                            <div class="row">
                                <!-- The type="password" attribute is set via script, to prevent warnings in Firefox for all http:// documents. -->
                                <input id="password" class="toolbarField">
                            </div>
                            <div class="buttonRow">
                                <button id="passwordCancel" class="overlayButton"><span data-l10n-id="password_cancel">Cancel</span></button>
                                <button id="passwordSubmit" class="overlayButton"><span data-l10n-id="password_ok">OK</span></button>
                            </div>
                        </div>
                    </div>
                    <div id="documentPropertiesOverlay" class="container hidden">
                        <div class="dialog">
                            <div class="row">
                                <span data-l10n-id="document_properties_file_name">File name:</span> <p id="fileNameField">-</p>
                            </div>
                            <div class="row">
                                <span data-l10n-id="document_properties_file_size">File size:</span> <p id="fileSizeField">-</p>
                            </div>
                            <div class="separator"></div>
                            <div class="row">
                                <span data-l10n-id="document_properties_title">Title:</span> <p id="titleField">-</p>
                            </div>
                            <div class="row">
                                <span data-l10n-id="document_properties_author">Author:</span> <p id="authorField">-</p>
                            </div>
                            <div class="row">
                                <span data-l10n-id="document_properties_subject">Subject:</span> <p id="subjectField">-</p>
                            </div>
                            <div class="row">
                                <span data-l10n-id="document_properties_keywords">Keywords:</span> <p id="keywordsField">-</p>
                            </div>
                            <div class="row">
                                <span data-l10n-id="document_properties_creation_date">Creation Date:</span> <p id="creationDateField">-</p>
                            </div>
                            <div class="row">
                                <span data-l10n-id="document_properties_modification_date">Modification Date:</span> <p id="modificationDateField">-</p>
                            </div>
                            <div class="row">
                                <span data-l10n-id="document_properties_creator">Creator:</span> <p id="creatorField">-</p>
                            </div>
                            <div class="separator"></div>
                            <div class="row">
                                <span data-l10n-id="document_properties_producer">PDF Producer:</span> <p id="producerField">-</p>
                            </div>
                            <div class="row">
                                <span data-l10n-id="document_properties_version">PDF Version:</span> <p id="versionField">-</p>
                            </div>
                            <div class="row">
                                <span data-l10n-id="document_properties_page_count">Page Count:</span> <p id="pageCountField">-</p>
                            </div>
                            <div class="buttonRow">
                                <button id="documentPropertiesClose" class="overlayButton"><span data-l10n-id="document_properties_close">Close</span></button>
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
                </div>  <!-- overlayContainer -->

            </div> <!-- outerContainer -->
            <div id="printContainer"></div>
            <input type="hidden" name="old_doc" id="old_doc" value="<?php echo $rwFile['old_doc_name'] . 'ok'; ?>">
            <?php
//} else {
//echo 'you have no permission to view this pdf file';
//}
            ?>
            <script>
                //             $("body").on("contextmenu",function(e){
                //       // return false;
                //    });
                document.title = "<?php echo $rwFile['old_doc_name'] . 'ok'; ?>";
                var pageTitle = document.title;
                //alert(pageTitle);
            </script>

            <?php
            if ($rwgetRole['pdf_print'] == '1') {
                
            } else {
                ?>
                <style type="text/css" media="print">
                    * { display: none; }
                </style>
            <?php } ?>
            <div id="myModal" class="modal modal-primary">

                <!-- Modal content -->

                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <span class="close">×</span>
                            <h2>Reason For Print</h2>
                        </div>

                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">

                                        <textarea  id="reason" class="form-control" placeholder="please give valid reason for printing this file."></textarea>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" name="id" id="ID">
                            <button type="button" class="btn btn-outline  pull-left" data-dismiss="modal" id="cls">Close</button>
                            <button id="print" class="btn btn-primary disabled" disabled>
                                Submit
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

            // When the user clicks the button, open the modal
            $("#print1").click(function () {
                // var ival=$(this).attr("data");
                modal.style.display = "block";
            });

            // When the user clicks on <span> (x), close the modal
            span.onclick = function () {
                modal.style.display = "none";
            }
            $("#cls").click(function () {
                modal.style.display = "none";
            });
            // When the user clicks anywhere outside of the modal, close it
            window.onclick = function (event) {
                if (event.target == modal) {
                    //modal.style.display = "none";
                }
            }
            $("#reason").bind("keydown keyup", function () {
                if ($(this).val().length != 0) {
                    $("#print").prop('disabled', false);
                    $("#print").removeClass("disabled");
                } else {
                    $("#print").prop('disabled', true);
                    $("#print").addClass("disabled");
                }
            });
            $("#print").click(function () {

                var reason = $("#reason").val();
                //alert(reason);
                printwithlog(reason);
            });

            function printwithlog(reason) {

                if (reason.length === 0) {
                    //  
                    alert("please give a reason for print");
                    return false;
                } else {
                    modal.style.display = "none";
                    $.post("viewer-pdf/printajaxlog.php", {remark: reason, docid: "<?php echo $dcid; ?>", docname: "<?php echo $docName; ?>", slid: "<?php echo $slid; ?>"}, function (result, status) {
                        if (status == 'success') {
                            //alert(result);
                            //$("#child1").html(result);
                            //alert(result);
                        }
                    });
                }
            }

        </script>
    </html>
    <?php
    //unlink("TEMP/".$fileName.".html"); //delete temp file after geting data
} else {

    $data = file_get_contents($localPath);
    $content = $data;
    if (FTP_ENABLED) {
        unlink($localPath); //delete temp file after geting data
    }
//
    ?>

    <html>
        <head>
            <title><?= $fileName ?></title>
            <link href="./assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        </head>
        <body>
            <div class="container-fluid" style="background-color: #006dcc;">

                <div style="padding-top: 1px;padding: 1px;background-color:whitesmoke ">
                    <center><p><h3><?= $doc_old_name . "." . $doc_extn ?></h3></p></center>

                </div>
                <div class="col-md-10 " style="background-color: whitesmoke;height: 100%">

                    <textarea class="form-control"  name="taskRemark" id="editor" ><?= $content ?></textarea>
                </div>
                <div class="col-md-2" style="background-color: white;height: 100%">
                    <div id="comment-wrapper">
                        <h4><center><?php echo $lang['Review_Log'] ?></center></h4> 
                        <div class="comment-list">
                            <div class="comment-list-container">
                                <!--div class="comment-list-item"-->
                                <div id="comentAdd">
                                    <?php
                                    $docReview = mysqli_query($db_con, "select * from `tbl_reviews_log` where doc_id='$id1' and in_review='0' order by id asc");
                                    if (mysqli_num_rows($docReview) > 0) {
                                        while ($rwcomment = mysqli_fetch_assoc($docReview)) {

                                            $usr = mysqli_query($db_con, "select first_name, last_name,profile_picture from tbl_user_master where user_id='$rwcomment[user_id]'");
                                            $rwUsr = mysqli_fetch_assoc($usr);
                                            ?>

                                            <div class="comment-list-item">   
                                                <li class="clearfix">
                                                    <div class="conversation-text">

                                                        <div class="ctext-wrap">
                                                            <span style="float:left;">   <?php
                                                                if (!empty($rwcomment['action_name'])) {
                                                                    echo '<strong>Action: </strong>' . $rwcomment['action_name'] . '<br>';
                                                                }
                                                                ?> </span> <div class="clearfix"></div>
                                                            <span style="float:right;">
                                                                <i><?php echo '<strong>Action By: </strong>' . $rwUsr['first_name'] . ' ' . $rwUsr['last_name']; ?></i>
                                                                <br/>
                                                                <?php echo '<strong>Action Time: </strong>' . date("j F, Y, H:i", strtotime($rwcomment['start_date'])); ?></span>
                                                        </div>
                                                    </div>
                                                </li>
                                            </div>

                                            <?php
                                        }
                                    } else {
                                        ?>
                                        <div class="comment-list-item"><center><?php echo $lang['No_Review_Log'] ?></center></div>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <!--/div-->
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </body>
        <script src="./assets/js/jquery.min.js"></script>
        <script src="./assets/plugins/tinymce/tinymce.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                if ($("#editor").length > 0) {
                    tinymce.init({
                        selector: "textarea#editor",
                        theme: "modern",
                        height: 500,
                        plugins: [
                            "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
                            "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                            "save table contextmenu directionality emoticons template paste textcolor"
                        ],
                        toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | forecolor backcolor emoticons",
                        style_formats: [
                            {title: 'Bold text', inline: 'b'},
                            {title: 'Red text', inline: 'span', styles: {color: '#ff0000'}},
                            {title: 'Red header', block: 'h1', styles: {color: '#ff0000'}},
                        ]
                    });
                }
            });

        </script>
    </html> 
<?php } ?>
<script src="assets/plugins/sweetalert2/sweetalert2.min.js"></script>
<script src="assets/pages/jquery.sweet-alert.init.js"></script>
<?php require_once 'checkin-checkout-html.php';?>
<?php require_once 'checkin-checkout-js.php';?>    
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

