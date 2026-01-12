<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    //require_once './application/config/database.php';
    require_once './application/pages/head.php';

    if ($rwgetRole['folder_upload'] != '1') {
        header('Location: ./index');
    }
    ?>

    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />

    <style>
        .alert{
            padding: 8px !important;
        }
        .alert-dismissable, .alert-dismissible{
            padding-right: 35px !important;
        }
        .btn-default{
            color: #193860 !important;
        }




        .demoInputBox{padding:5px; border:#F0F0F0 1px solid; border-radius:4px; background-color:#FFF;}
        #progress-bar {background-color: #193860;height:20px;color: #FFFFFF;width:0%;-webkit-transition: width .3s;-moz-transition: width .3s;transition: width .3s;}
        .btnSubmit{background-color:#09f;border:0;padding:10px 40px;color:#FFF;border:#F0F0F0 1px solid; border-radius:4px;}
        #progress-div {border:#193860 1px solid;padding: 5px 0px;margin:30px 0px;border-radius:4px;text-align:center;}
        #targetLayer{width:100%;}
    </style>
    <body class="fixed-left">
        <!-- Begin page -->
        <div id="wrapper">
            <!-- Top Bar Start -->
            <?php require_once './application/pages/topBar.php'; ?>
            <!-- Top Bar End -->
            <!-- ========== Left Sidebar Start ========== 1001/10556/00959 12/12/2011 14:33:58-->
            <?php require_once './application/pages/sidebar.php'; ?>
            <!-- Left Sidebar End --> 
            <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->                      
            <div class="content-page">
                <!-- Start content -->
                <div class="content">
                    <div class="container">
                        <!-- Page-Title -->
                        <div class="row">
                            <ol class="breadcrumb">
                                <li><a href="#"><?php echo $lang['Upload_Import']; ?></a></li>
                                <li class="active"><?php echo $lang['Upload_multi_folder']; ?></li>
                                <li> <a href="#" data-toggle="modal" data-target="#help-modal" id="helpview" data="59" title="<?= $lang['help']; ?>"><i class="fa fa-question-circle"></i> </a></li>
                            </ol>
                        </div>
                        <div class="row">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h4 class="header-title col-md-6"><?php echo $lang['Required_fields_are_marked_with_a'] ?> (<span style="color:red;">*</span>)</h4>
                                    <a href="assets/bulkFileUpload.csv" download class="pull-right btn btn-primary"><i class="ti-import"></i> <?= $lang['downlod_sample_csv']; ?></a>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-8" id="card-box">
                                            <?php
                                            if (isset($Successmsg)) {
                                                foreach ($Successmsg as $message) {
                                                    ?>
                                                    <div class="alert alert-success fade in alert-dismissible">
                                                        <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
                                                        <strong><?= $lang['success']; ?></strong> <?php echo $message; ?>
                                                    </div>
                                                    <?php
                                                }
                                            }
                                            ?>
                                            <?php
                                            if (isset($Errormsg)) {
                                                foreach ($Errormsg as $error) {
                                                    ?>
                                                    <div class="alert alert-danger fade in alert-dismissible">
                                                        <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
                                                        <strong><?= $lang['error']; ?></strong> <?php echo $error; ?>
                                                    </div>

                                                    <?php
                                                }
                                            }
                                            ?>
                                        </div>

                                    </div>
                                    <div class="row" >
                                        <div class="col-md-12" id="movetostorage" >
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="card-box">
                                            <form   method="post" action="uploadmultiplefolder.php" enctype="multipart/form-data" id="uploadForm">
                                                <div class="row">
                                                    <div class="form-group ">
                                                        <div class="radio radio-inline radio-success">
                                                            <input type="radio" name="optradio" value="1" checked onclick="checkRadio();"><label><?php echo $lang['upload_folder']; ?></label>
                                                        </div>
                                                        <div class="radio radio-inline radio-success">
                                                            <input type="radio" name="optradio" value="2" onclick="checkRadio();" ><label><?php echo $lang['Upload_Metadata']; ?></label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group row" id="uploadfolder">
                                                    <div class="col-md-2">
                                                        <label for="fup"><?php echo $lang['Sel_file_upload'] ?><span style="color:red;">*</span></label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="file" name="files[]" parsley-trigger="change" required placeholder="Enter storage name" class="form-control filestyle" id="files" webkitdirectory mozdirectory msdirectory odirectory directory multiple>
                                                        <input type="hidden" id="pCount" name="pageCount">
                                                        <input type="hidden" name="paths" id="paths">
                                                    </div>
                                                </div> 
                                                <div class="form-group row" id="uploadmetadata" style="display:none;">
                                                    <div class="col-md-2">
                                                        <label for="fup"><?php echo $lang['Sel_file_upload'] ?><span style="color:red;">*</span></label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="file" name="csvfile" id="metadata" parsley-trigger="change" onchange='triggerValidation(this)' accept=".csv" class="form-control filestyle">
                                                    </div>
                                                </div> 
                                                <div class="row" id="output" style="display:none;">
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-2">
                                                        <label for="userName"><?php echo $lang['Storage_Name']; ?><span style="color:red;">*</span></label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <?php
                                                        $perm = mysqli_query($db_con, "select sl_id from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]' group by sl_id");
                                                        $slperms = array();
                                                        while ($rwPerm = mysqli_fetch_assoc($perm)) {
                                                            $slperms[] = $rwPerm['sl_id'];
                                                        }
                                                        $permcount = count($slperms);
                                                        $sl_perm = implode(',', $slperms);
                                                        mysqli_set_charset($db_con, "utf8");
                                                        $sllevel = mysqli_query($db_con, "select * from tbl_storage_level where sl_id in($sl_perm)  and delete_status=0 and readonly=0 order by sl_name asc");
                                                        ?>
                                                        <select class="form-control select2"  name="storage" id="storage" data-placeholder="<?= $lang['Select_Storage']; ?>" required>
                                                            <option disabled selected><?php echo $lang['Storage_Name']; ?></option>
                                                            <?php
                                                            while ($rwSllevel = mysqli_fetch_assoc($sllevel)) {
                                                                $level = $rwSllevel['sl_depth_level'];
                                                                $SlId = $rwSllevel['sl_id'];
                                                                findChild($SlId, $level, $SlId);
                                                            }
                                                            ?>
                                                        </select> 
                                                        <?php

                                                        function findChild($sl_id, $level, $slperm) {
                                                            global $db_con;
                                                            echo '<option value="' . $sl_id . '">';
                                                            parentLevel($sl_id, $db_con, $slperm, $level, '');
                                                            echo '</option>';
                                                            $sql_child = "SELECT * FROM tbl_storage_level WHERE sl_parent_id = '$sl_id'  and delete_status=0 and readonly=0";

                                                            $sql_child_run = mysqli_query($db_con, $sql_child) or die('Error:' . mysqli_error($db_con));

                                                            if (mysqli_num_rows($sql_child_run) > 0) {

                                                                while ($rwchild = mysqli_fetch_assoc($sql_child_run)) {

                                                                    $child = $rwchild['sl_id'];
                                                                    findChild($child, $level, $slperm);
                                                                }
                                                            }
                                                        }

                                                        function parentLevel($slid, $db_con, $slperm, $level, $value) {

                                                            if ($slperm == $slid) {
                                                                $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid'  and delete_status=0 and readonly=0") or die('Error' . mysqli_error($db_con));
                                                                $rwParent = mysqli_fetch_assoc($parent);

                                                                if ($level < $rwParent['sl_depth_level']) {
                                                                    parentLevel($rwParent['sl_parent_id'], $db_con, $slperm, $level, $rwParent['sl_name']);
                                                                }
                                                            } else {
                                                                $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid' and sl_parent_id='$slperm'  and delete_status=0") or die('Error' . mysqli_error($db_con));
                                                                if (mysqli_num_rows($parent) > 0) {

                                                                    $rwParent = mysqli_fetch_assoc($parent);
                                                                    if ($level < $rwParent['sl_depth_level']) {
                                                                        parentLevel($rwParent['sl_parent_id'], $db_con, $slperm, $level, $rwParent['sl_name']);
                                                                    }
                                                                } else {
                                                                    $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid'  and delete_status=0 and readonly=0") or die('Error' . mysqli_error($db_con));
                                                                    $rwParent = mysqli_fetch_assoc($parent);
                                                                    $getparnt = $rwParent['sl_parent_id'];
                                                                    if ($level <= $rwParent['sl_depth_level']) {
                                                                        parentLevel($getparnt, $db_con, $slperm, $level, $rwParent['sl_name']);
                                                                    } else {
                                                                        //header('Location: ./index.php');
                                                                        // header("Location: ./storage?id=".urlencode(base64_encode($slperm)));
                                                                    }
                                                                }
                                                            }

                                                            //echo $value;
                                                            if (!empty($value)) {
                                                                $value = $rwParent['sl_name'] . '<b> > </b>';
                                                            } else {
                                                                $value = $rwParent['sl_name'];
                                                            }
                                                            echo $value;
                                                        }
                                                        ?>

                                                    </div>
                                                </div>

                                                <div class="form-group  m-b-0">
                                                    <input type="hidden" name="uploadfolder" value="test">
                                                    <button class="btn btn-primary waves-effect waves-light" type="submit" name="uploadfolder" id="bulkUPLD"><?php echo $lang['Submit']; ?></button>
                                                    <a href="upload_folder" class="btn btn-danger waves-effect waves-light m-l-5"><?php echo $lang['Cancel']; ?></a>
                                                </div>

                                            </form>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div id="progress-div" style="display:none;"><div id="progress-bar"></div></div>
                                                    <div id="targetLayer" style="display:none;"><img src="assets/images/proceed.gif" alt="load"  style="width: 100px;" /></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>				
                        </div>

                    </div> <!-- container -->

                </div> <!-- content -->

                <?php require_once './application/pages/footer.php'; ?>

            </div>

            <div  style="display:none; text-align: center; color: #fff;  background: rgba(0,0,0,0.5); width: 100%; height: 100%; z-index: 2000; position: fixed; top:0;" id="wait">
                <img src="assets/images/uploading.gif" alt="load"  style="margin-top: 250px; width: 250px;" />
            </div>
        </div>
        <!-- END wrapper -->

        <?php require_once './application/pages/footerForjs.php'; ?>
        <script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>
        <script src="assets/js/uploadfolder.js"></script>
        <!--<script type="text/javascript" src="bulkupload.js"></script>-->
        <script type="text/javascript">
                                                            $(document).ready(function () {
                                                                $('#uploadForm').submit(function (e) {
                                                                    if ($('#files').val() || $('#metadata').val()) {
                                                                        $("#progress-div").show();
                                                                        $("#targetLayer").show();
                                                                        e.preventDefault();
                                                                        $('#loader-icon').show();
                                                                        $(this).ajaxSubmit({
                                                                            target: '#targetLayer',
                                                                            beforeSubmit: function () {
                                                                                $("#progress-bar").width('0%');
                                                                            },
                                                                            uploadProgress: function (event, position, total, percentComplete) {
                                                                                $("#progress-bar").width(percentComplete + '%');
                                                                                $("#progress-bar").html('<div id="progress-status">' + percentComplete + ' %</div>')
                                                                            },
                                                                            success: function () {
                                                                                getToken();
                                                                                $('#loader-icon').hide();
                                                                            },
                                                                            //resetForm: true
                                                                        });
                                                                        return false;
                                                                    }
                                                                });
																
																
																setInterval(lastActive, 5000);
																
                                                            });
															
															function lastActive(){
																
																$.post("application/ajax/lastActive.php", {lgt:2}, function(result, status){
																	if(result=='2'){
																		
																		initSessionMonitor();
																			
																	}else{
																		
																	}
																}); 
															}
															
															
                                                            $(document).ready(function () {
                                                                $('form').parsley();

                                                            });
                                                            $(".select2").select2();



                                                            var input = document.getElementById('files');
                                                            var output = document.getElementById('output');

                                                            input.onchange = function (e) {

                                                                var files = e.target.files; // FileList
                                                                var arr = [];
                                                                processArray(files);
//                                                                for (var i = 0, f; f = files[i]; ++i) {
//                                                                    //console.debug(files[i].webkitRelativePath);
//                                                                    output.innerText = output.innerText + files[i].webkitRelativePath + "\n";
//                                                                    arr[i] = files[i].webkitRelativePath;
//
//                                                                }
                                                                //arr = arr.join('//');
                                                                //$("#paths").val(arr);



                                                            }

                                                            function delay() {
                                                                return new Promise(resolve => setTimeout(resolve, 3));
                                                            }

                                                            async function delayedLog(item) {
                                                                // notice that we can await a function
                                                                // that returns a promise
                                                                await delay();
                                                                // console.log(item);
                                                            }
                                                            async function processArray(array) {
                                                                //$("#bulkUPLD").attr("disabled", true);
                                                                //var output = document.getElementById('output');
                                                                $("#bulkUPLD").text("Wait...");
                                                                var pathArray = [];
                                                                var sizes = 0;
                                                                for (var i = 0, f; f = array[i]; ++i) {

                                                                    $("#bulkUPLD").attr("disabled", true);

                                                                    //array.forEach(async (item) => {
                                                                    //console.log(array[i].webkitRelativePath);
                                                                    sizes += array[i].size;
                                                                    //output.innerText  = output.innerText + array[i].webkitRelativePath+"\n";
                                                                    pathArray[i] = array[i].webkitRelativePath;
                                                                    await delayedLog(array[i]);
                                                                    //})
                                                                }
                                                                pathArray = pathArray.join('//');
                                                                $("#paths").val(pathArray);

                                                                if (Math.round((sizes / 1024)) > 5000000) {
                                                                    alert("You can not upload more than 5 GB flies");
                                                                    $('#files').val('');
                                                                    $("#bulkUPLD").attr("disabled", "disabled");
                                                                    $("#bulkUPLD").removeAttr("name");
                                                                    $('#uploadForm').reset();
                                                                    $("#bulkUPLD").text("Submit");
                                                                } else {
                                                                    $("#bulkUPLD").attr("disabled", false);
                                                                    $("#bulkUPLD").text("Submit");
                                                                    validateClientMemory(sizes);
                                                                }
                                                                console.log('Done!');
                                                            }
                                                            function checkRadio() {
                                                                var check = $("input[name='optradio']:checked").val();
                                                                if (check == '1') {
                                                                    $("#uploadmetadata").hide();
                                                                    $("#uploadfolder").show();
                                                                    $("#files").attr('required', true);
                                                                    $("#metadata").attr('required', false);
                                                                } else {
                                                                    $("#uploadfolder").hide();
                                                                    $("#uploadmetadata").show();
                                                                    $("#metadata").attr('required', true);
                                                                    $("#files").attr('required', false);
                                                                }

                                                            }
        </script>

        <script type="text/javascript">
//            function getfolder(e) {
//                var files = e.target.files;
//                var path = files[0].webkitRelativePath;
//                var Folder = path.split("/");
//                //alert(Folder[0]);
//            }
            $("#bulkUPLD").click(function () {
                var validate = true;
                $('input:required').each(function () {
                    if ($(this).val().trim() === '') {
                        validate = false;
                    }
                });
                var validates = true;
                $('select:required').each(function () {
                    if ($(this).val().trim() === '') {
                        validates = false;
                    }
                });
                if (validate === true && validates === true) {
                    // $("#wait").show();

                }
            });
            function triggerValidation(el) {
                var regex = new RegExp("(.*?)\.(csv)$");
                if (!(regex.test(el.value.toLowerCase()))) {
                    el.value = '';
                    alert('Please select CSV file format for upload metadata.');
                }
            }

            function validateClientMemory(sizes) {
                $.post("application/ajax/valiadate_client_memory.php", {size: sizes}, function (result, status) {
                    if (status == 'success') {
                        //$("#stp").html(result);
                        var res = JSON.parse(result);
                        if (res.status == "true")
                        {
                            // $("#memoryres").html("<span style=color:green>" + res.msg + "</span>");
                            $.Notification.autoHideNotify('success', 'top center', 'Success', res.msg)
                            $("#bulkUPLD").removeAttr("disabled");
                            $("#bulkUPLD").attr("name", "uploadfolder");
                        } else {

                            $.Notification.autoHideNotify('warning', 'top center', 'Oops', res.msg)
                            $("#bulkUPLD").attr("disabled", "disabled");
                            $("#bulkUPLD").removeAttr("name");
                            //$("#memoryres").html("<span style=color:red>" + res.msg + "</span>");
                        }
                    }
                });


            }
        </script>


        <script src="viewer-pdf/build/pdf.js"></script>
        <script src="viewer-pdf/getpdftext.js"></script>   
    </body>
</html>
