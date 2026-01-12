<!DOCTYPE html>
<html>
<?php
// error_reporting(E_ALL);
require_once './loginvalidate.php';

require_once './application/pages/head.php';

require_once './application/pages/function.php';
// die("rrrrrrr");

require_once './application/pages/samegroupUserlist.php';


$_SESSION['cdes_user_id'] == $rwUser['user_id'];
$number_of_pages = 0;
if (isset($_GET['file']) && !empty($_GET['file'])) {
    $number_of_pages = getNumPagesPdf(base64_decode($_GET['file']));
}
// print_r($rwgetRole);
// die("rrr");
if ($rwgetRole['send_split_file'] != '1') {
    header('Location: ./index');
}

?>

<link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<link href="assets/css/responsive.css" rel="stylesheet" type="text/css" />
<script src="assets/js/jquery.min.js"></script>
<script type="text/javascript" src="assets/plugins/jquery-validation/js/jquery.validate.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<!-- Sweet-Alert  -->
<!--<script src="assets/plugins/sweetalert2/sweetalert2.min.js"></script>
<script src="assets/pages/jquery.sweet-alert.init.js"></script>-->
<script src="assets/plugins/sweetalert2/sweetalert2-new.js"></script>
<script src="https://cdn.polyfill.io/v2/polyfill.min.js"></script>
<script src="assets/plugins/sweetalert2/sweet-alert.init.js"></script>


<body class="fixed-left">
    <!-- Begin page -->
    <div id="wrapper">
        <!-- Top Bar Start -->
        <?php //require_once './application/pages/topBar.php'; 
        ?>
        <!-- Top Bar End -->
        <!-- ========== Left Sidebar Start ========== -->
        <?php // require_once './application/pages/sidebar.php'; 
        ?>
        <!-- Left Sidebar End -->
        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="content-page1">
            <!-- Start content -->
            <div class="content">
                <div class="container">
                    <!-- Page-Title -->


                    <div class="panel">
                        <div class="panel-body">
                            <div class="row ">
                                <div class="col-md-10">
                                    <object data="<?php echo base64_decode($_REQUEST['file']); ?>" width="100%" height="500">
                                    </object>
                                </div>
                                <div class="col-md-2 m-b-10">
                                    <div class="row">
                                        <form method="post" id="split_form">
                                            <h3>Select Pages</h3>

                                            <div class="col-md-12">
                                            <select class="select2" name="pages[]" id="pages" multiple data-placeholder="Select Page/Pages">
                                                <option value=""><?php echo "Select Page/Pages"; ?></option>
                                                <?php
                                                for ($i = 1; $i <= $number_of_pages; $i++) {
                                                ?>
                                                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                                <?php
                                                }

                                                ?>
                                            </select>
                                            </div>


                                          
                                    </div>
                                   
                                    <div class="row">
                                    <h3>OR</h3>
                                    <h3>Select Range</h3>
                                        <div class="col-md-12">

                                            <input type="hidden" name="file" value="<?php echo $_REQUEST['file']; ?>">
                                            <input type="hidden" name="id" value="<?php echo $_REQUEST['id']; ?>">
                                            <input type="hidden" name="split_pdf" value="split_pdf">


                                            <select class="select2" name="start_page" id="start_page" data-placeholder="Starting Page">
                                                <option value=""><?php echo "Starting Page"; ?></option>
                                                <?php
                                                for ($i = 1; $i <= $number_of_pages; $i++) {
                                                ?>
                                                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                                <?php
                                                }

                                                ?>
                                            </select>
                                            <span id="estart_page" style="color:red;"></span>
                                        </div>
                                       &nbsp;
                                    
                                        <div class="col-md-12">


                                            <select class="select2" name="end_page" id="end_page" data-placeholder="End Page">
                                                <option value=""><?php echo "End Page"; ?></option>
                                                <?php
                                                for ($i = 1; $i <= $number_of_pages; $i++) {
                                                ?>
                                                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                                <?php
                                                }

                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <span id="e_both_selected" style="color:red;"></span>
                                    <br>
                                    <br>
                                    <div class="row ">

                                        <div class="col-md-12">
                                            <input type="submit" name="split_pdf" id="split_pdf" class="btn btn-primary pull-right" value="<?php echo "Split PDF"; ?>">




                                        </div>
                                    </div>


                                </div>




                                <br>
                                <br>
                                <br>

                                <?php $slid = base64_decode($_REQUEST['id']);
                              
                                require_once './splited_list.php'; ?>
                            </div>







                        </div>
                        <!-- end: page -->

                    </div> <!-- end Panel -->






                </div> <!-- container -->

            </div> <!-- content -->

            <?php //require_once './application/pages/footer.php'; 
            ?>
        </div>

        <!-- END wrapper -->

        <?php //require_once './application/pages/footerForjs.php'; ?>
    </div>
    <!--share files with users-->
    <div id="share-selected-files11" class="modal fade" role="dialog" aria-labelledby="" aria-hidden="true" >
        <div class="modal-dialog">
            <div class="panel panel-color panel-danger">
                <div class="panel-heading">
                    <!-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> -->
                    <h2 class="panel-title" id="shr"> <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <?php echo $lang['Hre_msge']; ?></h2>
                    <h2 class="panel-title" id="stitle"> <?php echo $lang['Shre_Docs_Wth']; ?></h2>
                </div>
                <div id="unseshare">
                    <div class="panel-body">
                        <p class="text-danger"><?php echo "Please Choose Pages/page OR Range of Page"; ?></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                    </div>
                </div>
                <div id="selected2">
                    <form method="post">
                        <div class="panel-body">

                            <div class="form-group">
                                <label class="text-primary"><?php echo $lang['Select_User']; ?> <span class="text-alert">*</span></label>
                                <select class="select2 select2-multiple" multiple data-placeholder="<?php echo $lang['Select_User']; ?>" name="userid[]" required>
                                    <?php
                                    $sameGroupIDs = array();
                                    $group = mysqli_query($db_con, "select * from tbl_bridge_grp_to_um where find_in_set('$_SESSION[cdes_user_id]',user_ids)") or die('Error' . mysqli_error($db_con));
                                    while ($rwGroup = mysqli_fetch_assoc($group)) {
                                        $sameGroupIDs[] = $rwGroup['user_ids'];
                                    }
                                    $sameGroupIDs = array_unique($sameGroupIDs);
                                    sort($sameGroupIDs);
                                    $sameGroupIDs = implode(',', $sameGroupIDs);

                                    $user = mysqli_query($db_con, "select * from tbl_user_master where user_id in($sameGroupIDs) order by first_name,last_name asc");
                                    while ($rwUser = mysqli_fetch_assoc($user)) {
                                        if ($rwUser['user_id'] != 1 && $rwUser['user_id'] != $_SESSION['cdes_user_id']) {
                                            echo '<option value="' . $rwUser['user_id'] . '">' . $rwUser['first_name'] . ' ' . $rwUser['last_name'] . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                        </div>

                        <div class="modal-footer">
                            <input type="hidden" id="share_docids" name="shareFile">
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close'] ?></button>
                            <button type="submit" name="shareFiles" class="btn btn-primary"> <i class="fa fa-share-alt"></i> <?php echo $lang['Share'] ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div><!-- /.modal -->
    <!--share files with users-->
    <div id="mail-selected-files" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="panel panel-color panel-danger">
                <div class="panel-heading">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h2 class="panel-title" id="mailf"> <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <?php echo $lang['Hre_msge']; ?></h2>
                    <h2 class="panel-title" style="display:none;" id="mtitle"> <?php echo $lang['mail_document']; ?></h2>
                </div>
                <div id="unmail">
                    <div class="panel-body">
                        <h5 class="text-danger"><?php echo $lang['Pls_slct_Fles_for_mail']; ?></h5>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                    </div>
                </div>
                <div id="selected3">
                    <form method="post">
                        <div class="panel-body">
                            <div id="addemailbox">
                                <div class="row" id="emailremove1">
                                    <div class="form-group">
                                        <label><?php echo $lang['Email']; ?><span class="text-alert">*</span></label>
                                        <div class="input-group">
                                            <input type="email" name="mailto[]" id="mailto" parsley-type="email" class="form-control emaillock" required="" placeholder="<?php echo $lang['Enter_Email_Id']; ?>">
                                            <span class="input-group-btn add-on">
                                                <a class="btn btn-primary btn-md" href="javascript:void(0);" onclick="addMoreRows('1');" title="<?= $lang['Add_more']; ?>">
                                                    <i class="fa fa-plus"></i>
                                                </a>
                                            </span>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label><?php echo $lang['subject']; ?><span class="text-alert">*</span></label>
                                    <input type="text" name="subject" id="subject" class="form-control textarealock translatetext" placeholder="<?php echo $lang['enter_subject']; ?>" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label><?php echo $lang['description']; ?><span class="text-alert">*</span></label>
                                    <textarea name="mailbody" id="mailbody" class="form-control textarealock translatetext" placeholder="<?php echo $lang['enter_description']; ?>" required></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" id="mail_docids" name="mailFile">
                            <input type="hidden" value="<?php echo $slid; ?>" name="storagemailFile">
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close'] ?></button>
                            <button type="submit" name="mailFiles1" class="btn btn-primary"><i class="fa fa-send-o"></i> <?php echo $lang['Send'] ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div><!-- /.modal -->


    <!--for multiselect-->
    <script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
    <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>
    <link href="assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet">
    <script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
    <script type="text/javascript" src="assets/multi_function_script.js"></script>



    <!-- for searchable select-->
    <script type="text/javascript">
        $(function() {
            if ($("input[name='extenddate']:checked").val()) {
                $('#extend').show();
                $("#duration").prop('required', true);
            }
            $('#extdate').click(function() {
                if ($("input[name='extenddate']:checked").val()) {
                    $('#extend').show();
                    $("#duration").prop('required', true);
                } else {
                    $('#extend').hide();
                    $("#duration").prop('required', false);
                }
            });
        });
    </script>
    <script type="text/javascript">

        $(document).ready(function() {
            $('form').parsley();
        });
        $(".select2").select2();
        //for disabled previous date during set disabled login time
        $(document).ready(function() {
        
            var d = new Date();
            var month = d.getMonth() + 1;
            var day = d.getDate();
            var output = d.getFullYear() + '-' +
                (('' + month).length < 2 ? '0' : '') + month + '-' +
                (('' + day).length < 2 ? '0' : '') + day;
            var day = d.getDate();
            var output1 = d.getFullYear() + '-' +
                (('' + month).length < 2 ? '0' : '') + month + '-' +
                (('' + day).length < 2 ? '0' : '') + day;
            //alert(output);
            $('.datepicker').datepicker({
                format: "dd-mm-yyyy",
                startDate: "today",
                //endDate: output1,
                autoclose: true
            });
        });
        //firstname last name 
        $('#split_pdf').on('click', function(e) {
            e.preventDefault();
            var pages = [];
            debugger
            var start_page = $('#start_page').val();
            var end_page = $('#end_page').val();
            if ($('#pages').val() != null) {
                pages = $('#pages').val();
            }
            if (start_page == "" && end_page == "" && pages.length == 0) {
                $('#e_both_selected').html('Please Choose Either Range Or Pages');
                $('#estart_page').html('');
            } else if (start_page != "" && end_page != "" && pages.length != 0) {
                $('#e_both_selected').html('Please Choose Either Range Or Pages');
                $('#estart_page').html('');
            } else
            if (start_page != "" && end_page != "") {
                if (start_page > end_page) {
                    $('#e_both_selected').html('');
                    $('#estart_page').html('Please Choose Correct Range');
                } else {
                    var form = document.getElementById("split_form");
                    // alert(form);
                    form.submit();

                }
            } else
            if (pages.length != 0) {

                var form = document.getElementById("split_form");
                // alert(form);
                form.submit();

            }



        });

        $('#shareFiles1').on('click', function(e) {
    // debugger
            var file = [];
            $(".emp_checkbox:checked").each(function() {
                file.push($(this).data('doc-id'));
            });
            // alert(file.length);
            if (file.length <= 0) {
                $("#shr").show();
                $("#stitle").hide();
                $("#unseshare").show();
                $("#selected2").hide();
           
            } else {

                $("#unseshare").hide();
                $("#stitle").show();

                $("#shr").hide();
                $("#selected2").show();



                var selected_values = file.join(",");
                $('#share_docids').val(selected_values);


            }
        });
        $('#mailFiles1').on('click', function(e) {

            var file = [];
            $(".emp_checkbox:checked").each(function() {
                file.push($(this).data('doc-id'));
            });
            if (file.length <= 0) {
                $("#unmail").show();
                $("#selected3").hide();
                $("#mailf").show();
                $("#mtitle").hide();
            } else {
                $("#unmail").hide();
                $("#selected3").show();
                $("#mailf").hide();
                $("#mtitle").show();

                var selected_values = file.join(",");
                $('#mail_docids').val(selected_values);


            }
        });

        $("#select_all").change(function() {
            $(".emp_checkbox").prop("checked", $(this).prop("checked"));
        });
    </script>
    <script>
        var id = 1;

        function addMoreRows(Id) {
            id++;
            $("#addemailbox").append('<div class="row m-b-10" id="emailremove' + id + '"><div class="input-group"><input type="email" name="mailto[]" id="mailto" parsley-type="email" class="form-control emaillock" required="" placeholder="<?php echo $lang['Enter_Email_Id']; ?>"><span class="input-group-btn add-on"><a href="javascript:void(0);" class="btn btn-danger btn-md" onclick="removeLastRow(' + id + ');" title="<?= $lang['Remove']; ?>"><i class="fa fa-minus"></i></button></span></div></div>');
        }

        function removeLastRow(Id) {

            $('#emailremove' + Id).remove();
        }

        
        
        
       
    </script>


    <?php include 'fscan-js.php';
    // print_r(base64_decode($_REQUEST['file']));
    // die;

    // function getNumPagesPdf($filepath)
    // {
    //     $fp = @fopen(preg_replace("/\[(.*?)\]/i", "", $filepath), "r");
    //     $max = 0;
    //     if (!$fp) {
    //         return "Could not open file: $filepath";
    //     } else {
    //         while (!@feof($fp)) {
    //             $line = @fgets($fp);
    //             if (preg_match('/\/Count [0-9]+/', $line, $matches)) {
    //                 preg_match('/[0-9]+/', $matches[0], $matches2);
    //                 if ($max < $matches2[0]) {
    //                     $max = trim($matches2[0]);
    //                     break;
    //                 }
    //             }
    //         }
    //         @fclose($fp);
    //     }

    //     return $max;
    // }
    // die("rrrrr");
    // Path to the PDF file you want to count pages for
    function getNumPagesPdf($filepath)
    {
        $pdfFilePath = $filepath;
        // print_r($filepath);
        // die("rrrr");
        // Check if the file exists
        if (file_exists($pdfFilePath)) {
            // Use pdftk to get the page count
            $command = '"' . GHOST_SCRIPT . '" -q -dNODISPLAY -c "(' . $pdfFilePath . ') (r) file runpdfbegin pdfpagecount = quit"';

            // Execute the command and capture the output
            $output = shell_exec($command);

            // Extract the page count from the output
            $pageCount = intval(preg_replace('/[^0-9]/', '', $output));

            // Display the page count
            // echo "The PDF file has $pageCount pages.";
            return $pageCount;
        } else {
            return 0;
        }
    }
    function getPathForStoring($outputPdfFile, $id, $check)
    {
        $db_con = $GLOBALS['db_con'];
        $file_name = $outputPdfFile;
        $query_validate = mysqli_query($db_con, "select * from `tbl_document_master_for_split_pdf` where doc_name='$id' and old_doc_name='$file_name' and flag_multidelete='1'");
        if (mysqli_num_rows($query_validate) > 0) {
            $errorMsg = 'uploaded_already_exist';
            return array("success" => "False", "msg" => $errorMsg);
            // echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '", "' . $lang['uploaded_already_exist'] . '")</script>';
            //exit();
        }
        $strgName = mysqli_query($db_con, "select * from tbl_storage_level where sl_id = '$id'") or die('Error:' . mysqli_error($db_con));
        $rwstrgName = mysqli_fetch_assoc($strgName);
        $storageName = trim($rwstrgName['sl_name']);
        $storageName = preg_replace('/[^a-zA-Z0-9-_]/', '', $storageName);
        $updir = getStoragePath($db_con, $rwstrgName['sl_parent_id'], $rwstrgName['sl_depth_level']);
        if (!empty($updir)) {
            $updir = $updir . '/';
        } else {
            $updir = '';
        }
        $uploaddir = 'extract-here/' . $updir . $storageName . '/';

        if (!is_dir($uploaddir)) {
            mkdir($uploaddir, 0777, true) or die(print_r(error_get_last()));
        }

        $extn = substr($outputPdfFile, strrpos($outputPdfFile, '.') + 1);
        $fileExtn = $extn;

        $filenameEnct = urlencode(base64_encode($outputPdfFile));
        $filenameEnct = preg_replace('/[^A-Za-z0-9_\-]/', '', $filenameEnct) . '.' . $extn;
        $filePath = $updir . $storageName . '/' . $filenameEnct;

        if ($check == 1) {
            // return  $uploaddir . $filenameEnct;
            return array("success" => "True", "msg" => $uploaddir . $filenameEnct);
            //   $upload = move_uploaded_file($file_tmp, $uploaddir . $filenameEnct) or die('Error' . print_r(error_get_last()));
        } else if ($check == 2) {

            $upload = 1;
            $user_id = $_SESSION['cdes_user_id'];
            $date = date('Y-m-d H:i:s');
            //  echo FTP_ENABLED;
            //  die("rrrrrr");

            if ($fileExtn == "pdf") {
                // $pageCount = getNumPagesPdf($uploaddir . $filenameEnct);
                $pageCount = 0;
            } elseif ($fileExtn == "docx") {
                $pageCount = PageCount_DOCX($uploaddir . $filenameEnct);
            } else {
                $pageCount = 1;
            }
            // encypt file
            //encrypt_my_file($uploaddir . $filenameEnct);

            $uploadInToFTP = false;
            $parentDocId = "";

            if ($upload) {

                if (FTP_ENABLED) {
                    //echo $uploaddir . $filenameEnct;

                    // die("rrrrr");

                    require_once './classes/ftp.php';

                    $fileserver = $GLOBALS['fileserver'];
                    $port = $GLOBALS['port'];
                    $ftpUser = $GLOBALS['ftpUser'];
                    $ftpPwd = $GLOBALS['ftpPwd'];
                    $ftp = new ftp();
                    $ftp->conn("$fileserver", "$port", "$ftpUser", "$ftpPwd");
                    // print_r(ROOT_FTP_FOLDER . '/' . $filePath);
                    // print_r($uploaddir . $filenameEnct);
                    // die("rrrrrrrr");

                    $uploadfile = $ftp->put(ROOT_FTP_FOLDER . '/' . $filePath, $uploaddir . $filenameEnct);

                    $arr = $ftp->getLogData();

                    //echo ROOT_FTP_FOLDER . '/' . $storageName . '/' . $filenameEnct, $uploaddir . $filenameEnct;die("dfhdgb");
                    if ($uploadfile) {

                        $uploadInToFTP = true;
                    } else {

                        $uploadInToFTP = false;
                        echo '<h2>Error:</h2>' . implode('<br />', $arr['error']);
                    }
                } else {

                    //echo "--------------------------------------------------"; die("OOOK");
                    $uploadInToFTP = true;
                }
            }
            if ($uploadInToFTP) {

                // Decrypt file
                //decrypt_my_file($uploaddir . $filenameEnct);
                if (strpos($file_name, ".")) {
                    $fnamewithoutextn = preg_replace('/.[^.]*$/', '', $file_name);
                } else {
                    $fnamewithoutextn = $file_name;
                }

                if ($parentDocId) {
                    $exe = mysqli_query($db_con, "INSERT INTO tbl_document_master_for_split_pdf(doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages $columns , dateposted, filename, parent_doc_id) VALUES ('$id', '$file_name', '$fileExtn', '$filePath', '$user_id', '$file_size', '$pageCount' $metavals, '$date', '$fnamewithoutextn', '$parentDocId')") or die('error in upload: ' . mysqli_error($db_con));
                } else {

                    $exe = mysqli_query($db_con, "INSERT INTO tbl_document_master_for_split_pdf(doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages $columns , dateposted, filename) VALUES ('$id', '$file_name', '$fileExtn', '$filePath', '$user_id', '$file_size', '$pageCount' $metavals, '$date', '$fnamewithoutextn')") or die('error in upload: ' . mysqli_error($db_con));
                }
                $doc_id = mysqli_insert_id($db_con);
                if ($parentDocId == "") {
                    $parentDocId = $doc_id;
                }

                // if(CREATE_THUMBNAIL)
                // {

                //create thumbnail
                // echo 'fhiuweuif11';
                $newdocname = base64_encode($doc_id);
                $uploadedfilename = $uploaddir . $filenameEnct;
                // if ($extn == 'jpg' || $extn == 'jpeg' || $extn == 'png') {
                //     createThumbnail2($uploadedfilename, $newdocname);
                // } elseif (strtolower($extn) == 'pdf') {
                //     changePdfToImage($uploadedfilename, $newdocname);
                // }
                //}

                $txtpath = $uploaddir . '/TXT/';
                if (!is_dir($txtpath)) {
                    mkdir($txtpath, 0777, TRUE) or die(print_r(error_get_last()));
                }
                $extractHereDirfile = $uploaddir . $filenameEnct;
                if (strtolower($extn) == "doc") {
                    $docText = read_doc($extractHereDirfile);
                } elseif (strtolower($extn) == "docx") {
                    $docText = read_docx($extractHereDirfile);
                } elseif (strtolower($extn) == "xlsx") {
                    $docText = xlsx_to_text($extractHereDirfile);
                } elseif (strtolower($extn) == "xls") {
                    // $docText = xls_to_txt($extractHereDirfile, $fnamewithoutextn);
                } elseif (strtolower($extn) == "pptx" || strtolower($extn) == "ppt") {
                    $docText = pptx_to_text($extractHereDirfile);
                } elseif (strtolower($extn) == "txt") {
                    $docText = txt_to_text($extractHereDirfile);
                }
                if (!empty($docText)) {
                    $fp = fopen($txtpath . $doc_id . ".txt", "wb");
                    fwrite($fp, $docText);
                    fclose($fp);
                }
                if ($exe) {
                    if ($_POST['linkcheck'] == 1) {

                        if ($_POST['makeparent'] == 1) {
                            mysqli_query($db_con, "update tbl_document_master_for_split_pdf set parent_doc_id='$doc_id' where doc_id='$linkDocId'");
                        } else {
                            mysqli_query($db_con, "update tbl_document_master_for_split_pdf set parent_doc_id='$linkDocId' where doc_id='$doc_id'");
                        }
                    }
                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`sl_id`, `doc_id`, `action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','$id','$doc_id','Document Uploaded','$date','$host','Document $file_name Uploaded in $storageName.')") or die('error : ' . mysqli_error($db_con));
                    $img_array = array('jpg', 'jpeg', 'png', 'bmp', 'pnm', 'jfif', 'jpeg', 'tiff');
                    if (strtolower($extn) == 'pdf' || in_array(strtolower($extn), $img_array)) {
                        //   getData($doc_id, $uploaddir, $uploaddir . $filenameEnct, $ocrUrl);
                        //gettxtpdf($uploaddir . $filenameEnct, $uploaddir, $doc_id);
                        $successMsg = 'File Splitted Successfully';
                        // echo '<script>uploadSuccess("storageFiles?id=' . $_GET['id'] . '", "' . $lang['Fle_Uplded_Sucsfly'] . '");</script>';
                    } else {

                        if (FTP_ENABLED) {
                            unlink($uploaddir . $filenameEnct);
                        }

                        $successMsg = 'File Splitted Successfully';
                    }
                } else {
                    $errorMsg = 'Opps File upload failed';
                    //echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '", "' . $lang['Op_Fle_upld_fld'] . '")</script>';
                }
            } else {
                $errorMsg = 'Opps File upload failed';

                //echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '", "' . $lang['Op_Fle_upld_fld'] . '")</script>';
            }
        } else {
            $errorMsg = "Filename is too long";

            //echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '", "File name too long")</script>';
        }


        if ($errorMsg) {
            return array("success" => "False", "msg" => $errorMsg);
        } else {
            return array("success" => "True", "msg" => $successMsg);
        }
    }
    function documentSharenotificationtoUser($db_con, $shareDocId, $docvalidupto)
    {

        $getdocument = mysqli_query($db_con, "SELECT old_doc_name,uploaded_by,doc_extn FROM `tbl_document_master_for_split_pdf` WHERE  doc_id in ($shareDocId)");
        if (mysqli_num_rows($getdocument) > 0) {
            $html = '<table border="1" cellpacing="2" cellpadding="4" style="border-collapse : collapse;">';
            $html .= '<tr>';
            $html .= '<th>SNo.</th>';
            $html .= '<th>Document Name</th>';
            $html .= '<th>Uploaded By</th>';
            $html .= '<th>Document validity</th>';
            $html .= '<th>Shared Time</th>';
            $html .= '</tr>';
            $i = 1;
            while ($rwgetdocument = mysqli_fetch_assoc($getdocument)) {

                mysqli_set_charset($db_con, "utf8");
                $uploadedby = mysqli_query($db_con, "select first_name,last_name from tbl_user_master where user_id='" . $rwgetdocument['uploaded_by'] . "'");
                $rwuploadedby = mysqli_fetch_assoc($uploadedby);
                $fileName = preg_replace('/.[^.]*$/', '', $rwgetdocument['old_doc_name']);
                $html .= '<tr>';
                $html .= '<td>' . $i . '.' . '</td>';
                $html .= '<td>' . $fileName . '.' . $rwgetdocument['doc_extn'] . '</td>';
                $html .= '<td>' . $rwuploadedby['first_name'] . ' ' . $rwuploadedby['last_name'] . '</td>';
                if (!empty($docvalidupto)) {
                    $html .= '<td>' . date('d-m-Y H:i:s', strtotime($docvalidupto));
                    '</td>';
                } else {
                    $html .= '<td>---</td>';
                }
                $html .= '<td>' . date('d-m-Y H:i:s') . '</td>';

                $html .= '</tr>';
                $i++;
            }
            $html .= '</table>';

            return $html;
        } else {

            return false;
        }
    }

    function Splitfunction($file_path, $doc_name)
    {
        // $doc_name .= "_splited";
        require_once('anott/fpdf/fpdf.php');
        require_once('anott/fpdf/Fpdi/src/autoload.php');

        $version = changePdfVersion1($file_path);
        if ($version) {
            $inputPdfFile = $version['file'];
        }

        $flie_arr = explode("/", $file_path);
        $output_range_ = explode(".", end($flie_arr));

        if (!empty($_REQUEST['start_page']) && !empty($_REQUEST['end_page']) && !empty($_REQUEST['pages'])) {
            echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","Please Choose Either Pages or Range");</script>';
        }
        // Page ranges to split the PDF
        else if (!empty($_REQUEST['start_page']) && !empty($_REQUEST['end_page'])) {
            $pageRanges = [
                [$_REQUEST['start_page'], $_REQUEST['end_page']],   // Page range 1-3

            ];
        } else if (!empty($_REQUEST['pages'])) {
            if (sizeof($_REQUEST['pages']) == 1) {
                $pageRanges = [
                    [$_REQUEST['pages'][0], $_REQUEST['pages'][0]],   // Page range 1-3

                ];
            } else {
                $pageRanges = [
                    [min($_REQUEST['pages']), max($_REQUEST['pages'])],   // Page range 1-3

                ];
            }
        }


        foreach ($pageRanges as $range) {
            list($startPage, $endPage) = $range;


            $pdf = new setasign\Fpdi\Fpdi();

            // Add new pages for the range
            for ($pageNo = $startPage; $pageNo <= $endPage; $pageNo++) {
                // $pdf->AddPage();
                if (!empty($_REQUEST['pages'])) {
                    if (in_array($pageNo, $_REQUEST['pages'])) {
                        $pdf->setSourceFile($inputPdfFile);

                        // Import and use the page from the input PDF
                        $tplIdx = $pdf->importPage($pageNo);

                        //
                        $size = $pdf->getTemplateSize($tplIdx);

                        // Determine the orientation of the imported page
                        $orientation = ($size['width'] > $size['height']) ? 'L' : 'P'; // Landscape or Portrait

                        // Add a new page with the determined orientation
                        $pdf->AddPage($orientation);
                        //


                        $pdf->useTemplate($tplIdx);
                    }
                } else if (!empty($_REQUEST['start_page']) && !empty($_REQUEST['end_page'])) {
                    // Set the source PDF
                    $pdf->setSourceFile($inputPdfFile);

                    // Import and use the page from the input PDF
                    $tplIdx = $pdf->importPage($pageNo);

                    //
                    $size = $pdf->getTemplateSize($tplIdx);

                    // Determine the orientation of the imported page
                    $orientation = ($size['width'] > $size['height']) ? 'L' : 'P'; // Landscape or Portrait

                    // Add a new page with the determined orientation
                    $pdf->AddPage($orientation);
                    //


                    $pdf->useTemplate($tplIdx);
                }
            }
            if (!FTP_ENABLED) {
                $output_range_[0] = base64_decode(urldecode($output_range_[0]));
            }
            // Output the current range as a separate PDF
            $outputPdfFile = substr($output_range_[0], 0, -3) . "(" . $startPage . '-' . $endPage . ")" . '.pdf';
            $outputPdfFile1 = getPathForStoring($outputPdfFile, $doc_name, 1); // for getting path to store splited file.

            // print_r($outputPdfFile1);
            // die("rrrrr");
            if ($outputPdfFile1['msg'] != "uploaded_already_exist") {
                $pdf->Output($outputPdfFile1['msg'], 'F');
                if ($version['status'] == 'new') {
                    unlink($inputPdfFile);
                }
                $return_msg = getPathForStoring($outputPdfFile, $doc_name, 2); // for putting stored file on FTP.
            }
        }

        if ($return_msg['success'] == "True") {
            echo '<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $return_msg['msg'] . '");</script>';
        } else {
            echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","Pdf already Exists");</script>';
        }
    }
    if (isset($_REQUEST['split_pdf']) && !empty($_REQUEST['split_pdf'])) {


        Splitfunction(base64_decode($_REQUEST['file']), base64_decode($_REQUEST['id']));
    }

    if (isset($_POST['shareFiles'])) {
        $fromUser = $_SESSION['cdes_user_id'];
        $ToUser = preg_replace("/[^0-9]/", "", $_POST['userid']);
        $date = date('Y-m-d H:i:s');
        if (!empty($_POST['extenddate'])) {
            $docvalidupto = date('Y-m-d H:i:s', strtotime($_POST['docsharetime']));
        } else {
            $docvalidupto = NULL;
        }
        $ToUser = implode(",", $ToUser);
        mysqli_set_charset($db_con, "utf8");
        $sharedToUser = array();
        $userName_run = mysqli_query($db_con, "SELECT first_name,last_name FROM tbl_user_master WHERE user_id in($ToUser) order by first_name,last_name asc") or die("Error: " . mysqli_error($db_con));
        while ($rwuserName = mysqli_fetch_assoc($userName_run)) {
            $sharedToUser[] = $rwuserName['first_name'] . ' ' . $rwuserName['last_name'];
        }
        $sharedToUserName = implode(',', $sharedToUser);
        $shareDocId = preg_replace("/[^0-9,]/", "", $_POST['shareFile']);
        $shareDocIds = explode(',', $shareDocId);

        $myuser = explode(',', $ToUser);

        $doc_path = array();
        $filename = array();
        // $message='N';
        foreach ($shareDocIds as $shareId) {
            foreach ($myuser as $myuserid) {

                $chkDocId = mysqli_query($db_con, "select * from tbl_document_share_for_split_pdf where doc_ids='$shareId' and to_ids ='$myuserid'") or die('Error in check' . mysqli_error($db_con));
                // print_r($chkDocId);
                // die("rrrrr");

                if (mysqli_num_rows($chkDocId) > 0) {

                    echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Doc_Alrdy_Shared'] . '");</script>';
                } else {
                    $shareFiles = mysqli_query($db_con, "INSERT INTO `tbl_document_share_for_split_pdf`(`from_id`, `to_ids`, `doc_ids`, `share_date`,`doc_share_valid_upto`) VALUES ('$fromUser','$myuserid','$shareId', '$date','$docvalidupto')") or die('Error in insert share document' . mysqli_error($db_con));

                    if ($shareFiles) {
                        $message = "Y";
                    }
                }
            }
            $shareDocNm = mysqli_query($db_con, "select old_doc_name from tbl_document_master_for_split_pdf where doc_id='$shareId'") or die('Error :' . mysqli_error($db_con));
            while ($rwshareDocNm = mysqli_fetch_assoc($shareDocNm)) {
                $filename[] = $rwshareDocNm['old_doc_name'];
            }
        }
        if ($message == "Y") {
            // error_reporting(E_ALL);
            $filenamed = implode(',', $filename);
            $shareDocIds_str = implode(',', $shareDocIds);

            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`doc_id`, `action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','$shareDocIds_str', 'Document Shared','$date','$host','Storage Document $filenamed Shared with $sharedToUserName')") or die('error : ' . mysqli_error($db_con));
            $doclist = documentSharenotificationtoUser($db_con, $shareDocId, $docvalidupto);
            $subject = $projectName . " document shared alert.";
            require_once './mail.php';
            //mail for subscribe document
            $subdocId = $shareDocId;

            $userId = array();


            sharedDocumentsMail($projectName, $subject, $ToUser, $doclist, $db_con);


            echo '<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Doc_shared_Sfly'] . '");</script>';
        } else {
            echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Doc_nt_shared'] . '");</script>';
        }

        mysqli_close($db_con);
    }
    if (isset($_POST['mailFiles1'])) {

        $mailto = $_POST['mailto'];
        $tousers = implode(',', $mailto);
        $subject = $_POST['subject'];
        $mailbody = $_POST['mailbody'];
        $storagemailFile = $_POST['storagemailFile'];
        $doc_ids = preg_replace("/[^0-9,]/", "", $_POST['mailFile']);
        $docIds = explode(',', $doc_ids);
        $username = 'User';
        $subdocId = $doc_ids;
        $userId = array();
        $emailto = array();
        $k = 1;

        $maildocname = mysqli_query($db_con, "SELECT old_doc_name FROM tbl_document_master_for_split_pdf WHERE doc_id in($subdocId) and flag_multidelete='1'");
        require_once './mail.php';

        foreach ($mailto as $to) {

            $emailsent = mailDocuments_splt($projectName, $subject, $mailbody, $username, $to, $docIds);
        }
        if ($emailsent) {
            foreach ($mailto as $to) {
                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`, `doc_id`,`action_name`, `start_date`,`system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]', '$doc_ids','Document Mailed','$date','$host','Document mailed with ($to).')") or die('error : ' . mysqli_error($db_con));
                $flag = 1;
            }
        }
        if ($flag == '1') {
            echo '<script>taskSuccess("' . $_SERVER['RESQUEST_URI'] . '","' . $lang['document_send'] . '");</script>';
        } else {
            echo '<script>taskFailed("' . $_SERVER['RESQUEST_URI'] . '","' . $lang['error_occured_mail_doc'] . '");</script>';
        }
    }

    ?>

</body>

</html>