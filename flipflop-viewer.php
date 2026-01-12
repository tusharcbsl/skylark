<!DOCTYPE html>
<?php
require_once './sessionstart.php';
require_once './application/config/database.php';
require_once './application/pages/function.php';
require_once './classes/fileManager.php';

//for user role
$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));
$rwgetRole = mysqli_fetch_assoc($chekUsr);
if ($rwgetRole['pdf_file'] != '1') {
    header('Location: ../index');
}
/*
 * file download
 */
$docId = base64_decode(urldecode($_GET['i']));
/* ------------lock file-0--------------- */
$status = 0;
$checkfileLockqry = mysqli_query($db_con, "SELECT * FROM `tbl_locked_file_master` WHERE doc_id='$docId' is_active='1' and user_id='$_SESSION[cdes_user_id]'");
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
$file = mysqli_query($db_con, "select doc_name,filename,doc_path, old_doc_name,doc_extn from tbl_document_master where doc_id='$docId'") or die('error' . mysqli_error($db_con));
$rwFile = mysqli_fetch_assoc($file);
$fileName = $rwFile['old_doc_name'];
$slid = $rwFile['doc_name'];
$filePath = $rwFile['doc_path'];
$doc_extn = $rwFile['doc_extn'];


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

    <html dir="ltr" mozdisallowselectionprint moznomarginboxes>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta name="google" content="notranslate">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Flip Book Document Viewer</title>	
        <link rel="stylesheet" href="flipbookviewer/css/pdf-flip.css"/>
        <link rel="stylesheet" href="flipbookviewer/css/viewer.css">

        <link rel="apple-touch-icon" sizes="180x180" href="assets/images/favicons/apple-touch-icon.png">
        <link rel="icon" type="image/png" href="assets/images/favicons/favicon-32x32.png" sizes="32x32">
        <link rel="icon" type="image/png" href="assets/images/favicons/favicon-16x16.png" sizes="16x16">
        <link rel="manifest" href="assets/images/favicons/manifest.json">
        <link rel="mask-icon" href="assets/images/favicons/safari-pinned-tab.svg" color="#5bbad5">
        <!--<script src="assets/plugins/sweetalert2/sweetalert2.min.js"></script>
        <script src="assets/pages/jquery.sweet-alert.init.js"></script>-->
        <script src="assets/plugins/sweetalert2/sweetalert2-new.js"></script>
        <script src="https://cdn.polyfill.io/v2/polyfill.min.js"></script>
        <!--script src="assets/plugins/sweetalert2/sweet-alert.init.js"></script>
         <link href="assets/plugins/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css"-->
        <!-- This snippet is used in production (included from viewer.html) -->
        <link rel="resource" type="application/l10n" href="flipbookviewer/locale/locale.properties">
         <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="assets/js/jquery.min.js"></script>
        <link href="viewer-pdf/modal.css" rel="stylesheet" type="text/css" />

        <script src="assets/js/bootstrap.min.js"></script>
        <script src="assets/js/jquery.min.js"></script>
        
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

    <body tabindex="1" class="loadingInProgress">
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
        ?>
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

                        <button id="secondaryPrint" class="secondaryToolbarButton print visibleMediumView" title="Print" tabindex="53" data-l10n-id="print"  <?php
                        if ($rwgetRole['pdf_print'] == '1' && isFolderReadable($db_con, $slid)) {
                            
                        } else {
                            echo'disabled';
                        }
                        ?>>
                            <span data-l10n-id="print_label">Print</span>
                        </button>

                        <button id="secondaryDownload" class="secondaryToolbarButton download visibleMediumView" title="Download" tabindex="54" data-l10n-id="download" <?php
                        if ($rwgetRole['pdf_download'] == '1' && isFolderReadable($db_con, $slid)) {
                            
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

                                <button id="print" class="toolbarButton print hiddenMediumView" title="Print" tabindex="33" data-l10n-id="print" <?php
                        if ($rwgetRole['pdf_print'] == '1' && isFolderReadable($db_con, $slid)) {
                            
                        } else {
                            echo'disabled';
                        }
                        ?>>
                                    <span data-l10n-id="print_label">Print</span>
                                </button>

                                <button id="download" class="toolbarButton download hiddenMediumView" title="Download" tabindex="-1" data-l10n-id="download" <?php
                                if ($rwgetRole['pdf_download'] == '1' && isFolderReadable($db_con, $slid)) {
                                    
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
                            <div id="toolbarViewerMiddle" style="float: left;left: 582px; position: absolute; display:none;">
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
                    <div id="viewer" class="pdfViewer"></div>
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

        <div id="mozPrintCallback-shim" hidden>
            <style>
                @media print {
                    #printContainer div {
                        page-break-after: always;
                        page-break-inside: avoid;
                    }
                }
            </style>
            <style scoped>
                #mozPrintCallback-shim {
                    position: fixed;
                    top: 0;
                    left: 0;
                    height: 100%;
                    width: 100%;
                    z-index: 9999999;

                    display: block;
                    text-align: center;
                    background-color: rgba(0, 0, 0, 0.5);
                }
                #mozPrintCallback-shim[hidden] {
                    display: none;
                }
                @media print {
                    #mozPrintCallback-shim {
                        display: none;
                    }
                }

                #mozPrintCallback-shim .mozPrintCallback-dialog-box {
                    display: inline-block;
                    margin: -50px auto 0;
                    position: relative;
                    top: 45%;
                    left: 0;
                    min-width: 220px;
                    max-width: 400px;

                    padding: 9px;

                    border: 1px solid hsla(0, 0%, 0%, .5);
                    border-radius: 2px;
                    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.3);

                    background-color: #474747;

                    color: hsl(0, 0%, 85%);
                    font-size: 16px;
                    line-height: 20px;
                }
                #mozPrintCallback-shim .progress-row {
                    clear: both;
                    padding: 1em 0;
                }
                #mozPrintCallback-shim progress {
                    width: 100%;
                }
                #mozPrintCallback-shim .relative-progress {
                    clear: both;
                    float: right;
                }
                #mozPrintCallback-shim .progress-actions {
                    clear: both;
                }
            </style>
            <div class="mozPrintCallback-dialog-box">
                <!-- TODO: Localise the following strings -->
                Preparing document for printing...
                <div class="progress-row">
                    <progress value="0" max="100"></progress>
                    <span class="relative-progress">0%</span>
                </div>
                <div class="progress-actions">
                    <input type="button" value="Cancel" class="mozPrintCallback-cancel">
                </div>
            </div>
        </div>
        
        <script src="flipbookviewer/js/pdf-flip.js"></script>
        <script src="flipbookviewer/js/l10n.js"></script>
        <script src="flipbookviewer/js/pdf.js"></script>
        <?php require_once './flipbookviewer/js/viewer.php'; ?>
        <script src="flipbookviewer/js/compatibility.js"></script>
        <script src="flipbookviewer/js/turn.min.js"></script>
        <script src="flipbookviewer/js/zoom.min.js"></script>
        <script src="flipbookviewer/js/debugger.js"></script>
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
    
        <script type="text/javascript">
         // doc id for time log
         var doc_id ="<?php echo urlencode(base64_encode($docId));?>";
        
            $(document).ready(function () {
                PdfFlip.init();
            });

        </script>
        <?php } ?>
        
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
            $("body").on("contextmenu", function (e) {
                return false;
            });

            window.onbeforeunload = function () {

                $.post("application/ajax/removeTempFiles.php", {filepath: "<?php echo '../' . $localPath; ?>"}, function (result) {

                });
                return;
            };
        </script>
<?php if (($_SESSION['pass'] != $pass_word) && ($pass_check['is_protected'] == 1 || $pass_check['is_protected'] == 2)) { ?>     
<script>
$(document).ready(function() { 

  var id = '#dialog';
  var maskHeight = $(document).height();
  var maskWidth = $(window).width();
  $('#mask').css({'width':maskWidth,'height':maskHeight}); 
  $('#mask').fadeIn(200); 
  $('#mask').fadeTo("slow",0.5); 
  $('#outerContainer').hide();
        var winH = $(window).height();
  var winW = $(window).width();
        $(id).css('top',  winH/2-$(id).height()/2);
  $(id).css('left', winW/2-$(id).width()/2);
     $(id).fadeIn(2000);  
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
<?php }?>

<?php require_once ('timelog-js.php'); ?> 

<?php if ($rwgetRole['pdf_print'] == '1') {
    
} else {
    ?>
            <style type="text/css" media="print">
                /* { display: none; }*/
            </style>
<?php } ?>
    </body>
</html>
<?php } else { ?>
    <script>
            alert("File Is Locked Please Contact To Administrator");
          
            window.close();
    </script>
<?php } ?>

