<!DOCTYPE html>
<html>
    <?php
    error_reporting(0);
    $docId = base64_decode(urldecode($_GET['i']));
    $uid = base64_decode(urldecode($_GET['uid']));
    //die;
    //$docId = base64_decode(urldecode($_GET['file']));
    //ini_set('display_errors', 1);
    require_once './loginvalidate.php';
    require_once './application/config/database.php';
    
   require_once './application/pages/function.php';
   
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
        <script type="text/javascript" src="assets/js/jquery.min.js"></script>
        <link href="viewer-pdf/modal.css" rel="stylesheet" type="text/css" />

        <script src="assets/js/bootstrap.min.js"></script>
        <!-- This snippet is used in production (included from viewer.html) -->
        <link href="assets/css/imageviewer.css" rel="stylesheet" type="text/css" />

        <link rel="apple-touch-icon" sizes="180x180" href="assets/images/favicons/apple-touch-icon.png">
        <link rel="icon" type="image/png" href="assets/images/favicons/favicon-32x32.png" sizes="32x32">
        <link rel="icon" type="image/png" href="assets/images/favicons/favicon-16x16.png" sizes="16x16">
        <link href="assets/plugins/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css">
        
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
                /*                overflow: auto;*/
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
                            <a href="<?= $localPath ?>" id="download" class="btn btn-default btn-lg" title="Download" download <?php
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
                <div class="middle" style="width:40%; margin: auto; text-align: center; vertical-align: middle;" >
                    <div id="comment-wrapper" >

                        <h4>Edit Metadata</h4>
                        <div class="comment-list">
                            <div class="comment-list-container">
                                <form method="post" enctype="multipart/form-data">
                                    <?php
                                    $getMetaId = mysqli_query($db_con, "select * from tbl_document_master where doc_id = '$docId'") or die('Error:' . mysqli_error($db_con));
                                    $meta_row = mysqli_fetch_assoc($getMetaId);
                                    $getMetaId = mysqli_query($db_con, "select * from tbl_metadata_to_storagelevel where sl_id = '$meta_row[doc_name]'") or die('Error:' . mysqli_error($db_con));
                                    $i = 1;
                                    while ($rwgetMetaId = mysqli_fetch_assoc($getMetaId)) {
                                        $getMetaName = mysqli_query($db_con, "select * from tbl_metadata_master where id = '$rwgetMetaId[metadata_id]'") or die('Error:' . mysqli_error($db_con));

                                        while ($rwgetMetaName = mysqli_fetch_assoc($getMetaName)) {

                                            $meta = mysqli_query($db_con, "select `$rwgetMetaName[field_name]` from tbl_document_master where doc_id='$meta_row[doc_id]'");
                                            $rwMeta = mysqli_fetch_array($meta);

                                            if ($rwgetMetaName['field_name'] == 'noofpages') {
                                                
                                            } else {
                                                ?>                           

                                                <div class="form-group">

                                                    <div class="col-md-12">
                                                        <label class="pull-left"><?php echo $rwgetMetaName['field_name']; ?> <span style="color:red;">*</span></label>
                                                    </div>
                                                    <div class="col-md-12 m-b-15">
                                                        <?php if ($rwgetMetaName['data_type'] == 'datetime') { ?>
                                                            <div class="input-group">
                                                                <input type="text" class="form-control datepicker" name="fieldName<?php echo $i; ?>" placeholder="DD-MM-YYYY" value="<?php
                                                                if (!empty($rwMeta[$rwgetMetaName['field_name']])) {
                                                                    $date = strtotime($rwMeta[$rwgetMetaName['field_name']]);
                                                                    echo date('Y-m-d', $date);
                                                                }
                                                                ?>" <?php
                                                                       if ($rwgetMetaName['mandatory'] == "Yes") {
                                                                           echo "required";
                                                                       }
                                                                       ?>>
                                                                <span class="input-group-addon bg-custom b-0 text-white"><i class="icon-calender"></i></span>
                                                            </div>

                                                        <?php } else {
                                                            ?>
                                                            <input type="text" id="metaData1" class="form-control" name="fieldName<?php echo $i; ?>" value="<?php echo $rwMeta[$rwgetMetaName['field_name']]; ?>" <?php
                                                            if ($rwgetMetaName['mandatory'] == "Yes") {
                                                                echo "required";
                                                            }
                                                            ?>>
                                                                   <?php
                                                               }
                                                               ?>
                                                    </div>

                                                </div> 


                                                <?php
                                            }
                                        }
                                        $i++;
                                    }
                                    ?>
                                    <?php if ($rwgetRole['update_file'] == '1') { ?>
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <label class="pull-left"><?php echo $lang['UPDAT_DCUMNT']; ?></label>
                                            </div>
                                            <div class="col-md-12 m-b-15">
                                                <input class="form-control" id="myImage1" name="fileName" data-buttonname="btn-primary" type="file">
                                                <input type="hidden" id="pCount" name="pageCount">
                                                <input type="hidden" value="<?php echo $docId; ?>" name="docid"/>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <div class="row">
                                        <div class="col-md-12 m-l-15">
                                            <button type="submit" name="editMetaValue"  class="btn btn-primary pull-left">Save & Update</button>
                                        </div>

                                    </div>
                                </form>
                            </div>

                        </div>

                    </div>
                </div>
                <div class="middle" style="width:60%; margin: auto; text-align: center; vertical-align: middle;" >

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
            
            
 $(document).ready(function () {
                                                                var d1 = new Date();
                                                                d1 = d1.setDate(d1.getDate() - 30);
                                                                var d = new Date(d1);
                                                                var month = d.getMonth() + 1;
                                                                var day = d.getDate();
                                                                var output = d.getFullYear() + '-' +
                                                                        (('' + month).length < 2 ? '0' : '') + month + '-' +
                                                                        (('' + day).length < 2 ? '0' : '') + day;
                                                                //alert(output);
                                                                $('.datepicker').datepicker({
                                                                    format: "yyyy-mm-dd",
                                                                    startDate: output
                                                                });
                                                            });
        </script>
    </body>
</html>
<script src="assets/plugins/sweetalert2/sweetalert2.min.js"></script>
<script src="assets/pages/jquery.sweet-alert.init.js"></script>
<?php
if (isset($_POST['editMetaValue'])) {
    if (!empty($_FILES['fileName']['name'])) {
        $user_id = $_SESSION['cdes_user_id'];
       $doc_id = $_POST['docid'];
        $file_name = $_FILES['fileName']['name'];
        $file_size = $_FILES['fileName']['size'];
        $file_type = $_FILES['fileName']['type'];
        $file_tmp = $_FILES['fileName']['tmp_name'];
        $pageCount = $_POST['pageCount'];

            $extn = substr($file_name, strrpos($file_name, '.') + 1);
            $fname = substr($file_name, 0, strrpos($file_name, '.'));

            $fileExtn = substr($file_name, strrpos($file_name, ".") + 1);
            
            $getDocName = mysqli_query($db_con, "select * from tbl_document_master where doc_id = '$doc_id'") or die('Error:' . mysqli_error($db_con));
            $rwgetDocName = mysqli_fetch_assoc($getDocName);
            $docName = $rwgetDocName['doc_name'];
            //$docName = explode("_", $docName);
            $old_file_name = $rwgetDocName['old_doc_name'];
            $oldextn = substr($old_file_name, strrpos($old_file_name, '.') + 1); // old file extn
            $oldfname = substr($old_file_name, 0, strrpos($old_file_name, '.')); // old file name

            $updateDocName = $docName . '_' . $doc_id; //storage id followed by doc id
            $chekFileVersion = mysqli_query($db_con, "SELECT * FROM `tbl_document_master` WHERE find_in_set('$updateDocName', doc_name)") or die('Error:' . mysqli_error($db_con));
            $flVersion = mysqli_num_rows($chekFileVersion);
            $flVersion = $flVersion + 1;
            $nfilename = $oldfname . '_' . $flVersion;

            $strgName = mysqli_query($db_con, "select * from tbl_storage_level where sl_id = '$docName'") or die('Error:' . mysqli_error($db_con));
            $rwstrgName = mysqli_fetch_assoc($strgName);
            $storageName = $rwstrgName['sl_name'];
            $storageName = str_replace(" ", "", $storageName);
            $storageName = preg_replace('/[^A-Za-z0-9\-]/', '', $storageName);
            $uploaddir = "extract-here/" . ROOT_FTP_FOLDER . "/" . $storageName . '/';
            if (!is_dir($uploaddir)) {
                mkdir($uploaddir, 0777, TRUE) or die(print_r(error_get_last()));
            }
            $nfilename = preg_replace('/[^A-Za-z0-9_\-]/', '', $nfilename);
            // $filenameEnct=$fname.'.'.$extn;// urlencode(base64_encode($fname)).'.'.$extn;
            $filenameEnct = urlencode(base64_encode($nfilename));
            $filenameEnct = preg_replace('/[^A-Za-z0-9_\-]/', '', $filenameEnct);
            $filenameEnct = $filenameEnct . '.' . $extn;
            $filenameEnct = time() . $filenameEnct;

            //  $image_path = "images/" . $file_name;
            $uploaddir = $uploaddir . $filenameEnct;
            $upload = move_uploaded_file($file_tmp, $uploaddir) or die(print_r(error_get_last()));
            $uploadInToFTP = false;
            if ($upload) {
                if (FTP_ENABLED) {

                    $ftp = new ftp();
                    $ftp->conn("$fileserver", "$port", "$ftpUser", "$ftpPwd");

                    $filepath = $storageName . '/' . $filenameEnct;
                    $uploadfile = $ftp->put(ROOT_FTP_FOLDER . '/' . $filepath, $uploaddir);
                    $arr = $ftp->getLogData();
                    if ($uploadfile) {
                        $uploadInToFTP = true;
                        unlink($uploaddir);
                    } else {
                        $uploadInToFTP = false;
                        if ($arr['error'] != "") {
                            echo '<h2>Error:</h2>' . implode('<br />', $arr['error']);
                        }
                    }
                } else {
                    $uploadInToFTP = true;
                }
            }
            if ($uploadInToFTP) {

                $cols = '';
                $columns = mysqli_query($db_con, "SHOW COLUMNS FROM tbl_document_master");
                while ($rwCols = mysqli_fetch_array($columns)) {
                    if ($rwCols['Field'] != 'doc_id') {
                        if (empty($cols)) {
                            $cols = '`' . $rwCols['Field'] . '`';
                        } else {
                            $cols = $cols . ',`' . $rwCols['Field'] . '`';
                        }
                    }
                }
                $createVrsn = mysqli_query($db_con, "INSERT INTO tbl_document_master($cols) select $cols from tbl_document_master where doc_id='$doc_id'") or die('Error:' . mysqli_error($db_con));
                $insertDocID = mysqli_insert_id($db_con);
                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Versioning Document $file_name Added','$date',null,'$host',null)") ;


                $newdocname = base64_encode($insertDocID);
                //create thumbnail
                if($extn=='jpg' || $extn=='jpeg' || $extn=='png'){
                    createThumbnail2($uploaddir,$newdocname);
                }elseif($extn=='pdf'){
                    changePdfToImage($uploaddir,$newdocname);
                }
                
                if ($createVrsn) {
                    $updateNew = mysqli_query($db_con, "update tbl_document_master set doc_name='$updateDocName' where doc_id='$insertDocID'");
                    $updateOld = mysqli_query($db_con, "update tbl_document_master set old_doc_name='$file_name',filename='$fname', doc_extn='$extn', doc_path='$storageName/$filenameEnct', uploaded_by='$user_id', doc_size='$file_size', noofpages='$pageCount', dateposted='$date' where doc_id='$doc_id'");
                    echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Updtd_Sfly'] . '");</script>';
                }
            } else {
                echo'<script>taskFail("' . basename($_SERVER[REQUEST_URI]) . '","' . $lang['Document_not_copied'] . '");</script>';
            }
   
    } else {

        $getMetaId = mysqli_query($db_con, "select * from tbl_document_master where doc_id = '$_POST[docid]'") or die('Error:' . mysqli_error($db_con));
        //echo "select * from tbl_document_master where doc_id = '$_POST[docid]'";
        $meta_row = mysqli_fetch_assoc($getMetaId);
        $getMetaId = mysqli_query($db_con, "select * from tbl_metadata_to_storagelevel where sl_id = '$meta_row[doc_name]'") or die('Error:' . mysqli_error($db_con));
        //echo "select * from tbl_metadata_to_storagelevel where sl_id = '$meta_row[doc_name]'";
        $i = 1;

        while ($rwgetMetaId = mysqli_fetch_assoc($getMetaId)) {

            $getMetaName = mysqli_query($db_con, "select * from tbl_metadata_master where id = '$rwgetMetaId[metadata_id]'") or die('Error:' . mysqli_error($db_con));
            $StorageNme = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id='$rwgetMetaId[sl_id]'");
            $rwStrName = mysqli_fetch_assoc($StorageNme);
            while ($rwgetMetaName = mysqli_fetch_assoc($getMetaName)) {
                $meta = mysqli_query($db_con, "select `$rwgetMetaName[field_name]` from tbl_document_master where doc_id='$meta_row[doc_id]'");
                $rwMeta = mysqli_fetch_array($meta);
                //$metadatValue = $rwMeta[''];
                //echo $i; echo '-';
                if ($rwgetMetaName['field_name'] == 'noofpages') {
                    
                } else {

                    $fieldValue = $_POST['fieldName' . $i];
                    //echo "update tbl_document_master set `$rwgetMetaName[field_name]` = '$fieldValue' where doc_id = '$_POST[metaId]' or (substring_index(doc_name,'_',-1)='$_POST[metaId]' and substring_index(doc_name,'_',1)='$_POST[metaId]')";
                    $updateMeta = mysqli_query($db_con, "update tbl_document_master set `$rwgetMetaName[field_name]` = '$fieldValue' where doc_id = '$_POST[docid]' or (substring_index(doc_name,'_',-1)='$_POST[docid]' and substring_index(doc_name,'_',1)='$_POST[docid]')") or die('Error' . mysqli_error($db_con));
                    if ($updateMeta) {
                        //metadata update log
                        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'MetaData Value $fieldValue Assign in MetaData Field $rwgetMetaName[field_name] in $rwStrName[sl_name]','$date',null,'$host',null)");
                        echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Mtadta_Updted_sucsfly'] . '");</script>';
                    }
                }
            }

            $i++;
        }
    }
    $checkout = mysqli_query($db_con, "UPDATE tbl_document_master set checkin_checkout=1 WHERE doc_id='$docId'");
    if ($checkout && ($updateNew && $updateOld) || ($updateMeta)) {
        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'MetaData Value $fieldValue Assign in MetaData Field $rwgetMetaName[field_name] in $rwStrName[sl_name]','$date',null,'$host',null)") ;
        echo'<script>taskSuccess("storageFiles?id=' . base64_encode($meta_row['doc_name']) . '","' . $lang['Mtadta_Updted_sucsfly'] . '");</script>';
    }
    mysqli_close($db_con);
}
?>
