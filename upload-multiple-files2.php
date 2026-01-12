<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    require_once './application/pages/head.php';
    require_once './classes/ftp.php';

    //for user role
    $chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

    $rwgetRole = mysqli_fetch_assoc($chekUsr);

    if ($rwgetRole['bulk_upload'] != '1') {
        header('Location: ./index');
    }
    ?>
    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />

    <!-- Plugin Css-->
    <link rel="stylesheet" href="assets/plugins/magnific-popup/css/magnific-popup.css" />
    <link rel="stylesheet" href="assets/plugins/jquery-datatables-editable/datatables.css" />
    <link rel="stylesheet" href="assets/css/global.css">
    <style>
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


            <!-- ========== Left Sidebar Start ========== -->
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
                            <div class="col-sm-12">
                                <ol class="breadcrumb">
                                    <li><a href="#"><?php echo $lang['Storage_Management'] ?></a></li>
                                    <li><a href="#"><?php echo $lang['Upload_Import'] ?></a></li>
                                    <li class="active"> <?php echo $lang['Upload_Files'] ?></li>
                                    <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                                </ol>
                            </div>
                        </div>
                        <div class="row">
                            <div class="box box-primary">
                                <!-- <div class="box-header with-border">
                                    <h4 class="header-title col-md-6"><?php echo $lang['Required_fields_are_marked_with_a'] ?> (<span style="color:red;">*</span>)</h4>
                                    <a href="assets/images/bulkFileUpload.csv" download class="pull-right btn btn-primary"><i class="fa fa-download"></i> <?= $lang['downlod_sample_csv']; ?></a>
                                </div> -->
                                <div class="box-body">

                                    <div class="col-lg-12">
                                        <div class="card-box">
                                            <form data-parsley-validate novalidate method="post" action="chunk-action.php"  id="uploadForm" enctype="multipart/form-data">
                                                <div class="form-group row">
                                                    <div class="col-md-2">
                                                        <label for="fup"><?php echo $lang['Sel_file_upload'] ?><span style="color:red;">*</span></label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <!-- <input type="file" name="zip_upload[]" class="filestyle" parsley-trigger="change" required placeholder="<?php echo $lang['Entr_strg_nm'] ?>" class="form-control" id="flup"  multiple> -->
                                                        <input type="file" name="myfile" id="flup" class="form-control">
                                                        <!-- <div id="flup">[Upload files]</div> -->
                                                        <!-- UPLOAD FILE LIST -->
                                                        <div id="filelist"></div>
                                                        <input type="hidden" id="pCount" name="pageCount">
                                                    </div>
                                                </div>      
                                                <div class="form-group row">
                                                    <div class="col-md-2">
                                                        <label for="userName"><?php echo $lang['Storage_Name']; ?><span style="color:red;">*</span></label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <?php
//                                                        $perm = mysqli_query($db_con, "select sl_id from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]' group by sl_id");
//                                                        $rwPerm = mysqli_fetch_assoc($perm);
//                                                        $slperm = $rwPerm['sl_id'];
//                                                        $sllevel = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slperm'");
//                                                        $rwSllevel = mysqli_fetch_assoc($sllevel);
//                                                        $level = $rwSllevel['sl_depth_level'];
                                                        ?>
                                                        <select class="form-control select2"  name="storage" data-placeholder="select Storage" id="storage" required>
                                                            <option value=""><?php echo $lang['Select_Storage']; ?></option>
                                                            <?php
                                                            if (isset($_GET['parentName']) && !empty($_GET['parentName'])) {
                                                                $parentId = $_GET['parentName'];
                                                            }
                                                            mysqli_set_charset($db_con, "utf8");
                                                            $sllevel = mysqli_query($db_con, "select * from tbl_storage_level where sl_id in($slpermIdes) order by sl_name asc");
                                                            while ($rwSllevel = mysqli_fetch_assoc($sllevel)) {
                                                                $level = $rwSllevel['sl_depth_level'];
                                                                $slId = $rwSllevel['sl_id'];
                                                                $slperm = $rwSllevel['sl_id'];
                                                                findChild($slId, $level, $slperm, $parentId);
                                                            }
                                                            ?>
                                                        </select> 
                                                        <?php

                                                        function findChild($sl_id, $level, $slperm, $parentId) {

                                                            global $db_con;

                                                            if ($sl_id == $parentId) {
                                                                echo '<option value="' . $sl_id . '"  selected>';
                                                                parentLevel($sl_id, $db_con, $slperm, $level, '');
                                                                echo '</option>';
                                                            } else {
                                                                echo '<option value="' . $sl_id . '" >';
                                                                parentLevel($sl_id, $db_con, $slperm, $level, '');
                                                                echo '</option>';
                                                            }

                                                            $sql_child = "select * FROM tbl_storage_level WHERE sl_parent_id = '$sl_id' order by sl_name asc";

                                                            $sql_child_run = mysqli_query($db_con, $sql_child) or die('Error:' . mysqli_error($db_con));

                                                            if (mysqli_num_rows($sql_child_run) > 0) {

                                                                while ($rwchild = mysqli_fetch_assoc($sql_child_run)) {

                                                                    $child = $rwchild['sl_id'];
                                                                    findChild($child, $level, $slperm, $parentId);
                                                                }
                                                            }
                                                        }

                                                        function parentLevel($slid, $db_con, $slperm, $level, $value) {
                                                            mysqli_set_charset($db_con, "utf8");
                                                            if ($slperm == $slid) {
                                                                $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid' ") or die('Error' . mysqli_error($db_con));
                                                                $rwParent = mysqli_fetch_assoc($parent);

                                                                if ($level < $rwParent['sl_depth_level']) {
                                                                    parentLevel($rwParent['sl_parent_id'], $db_con, $slperm, $level, $rwParent['sl_name']);
                                                                }
                                                            } else {
                                                                $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid' and sl_parent_id='$slperm'") or die('Error' . mysqli_error($db_con));
                                                                if (mysqli_num_rows($parent) > 0) {

                                                                    $rwParent = mysqli_fetch_assoc($parent);
                                                                    if ($level < $rwParent['sl_depth_level']) {
                                                                        parentLevel($rwParent['sl_parent_id'], $db_con, $slperm, $level, $rwParent['sl_name']);
                                                                    }
                                                                } else {
                                                                    $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid'") or die('Error' . mysqli_error($db_con));
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
                                                    <input type="hidden" name="bulkUpload" value="test">
                                                    <button class="btn btn-primary waves-effect waves-light" type="submit" name="bulkUpload" id="bulkUPLD" disabled="">
                                                        <?php echo $lang['Submit']; ?>
                                                    </button>
                                                    <a href="upload-multiple-files" class="btn btn-danger waves-effect waves-light m-l-5">
                                                        <?php echo $lang['Cancel']; ?>
                                                    </a>
                                                </div>
                                            </form>
                                            <br>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="col-md-12">
                                                        <div id="progress-div" style="display:none;"><div id="progress-bar"></div></div>
                                                        <div id="targetLayer"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>				
                        </div>
                        <!-- <div id="container">
                          <a class="button" id="uploadfiles" href="#">Upload</a>
                        </div> -->

                        


                    </div> <!-- content -->

                    <?php require_once './application/pages/footer.php'; ?>                
                </div>
                <!-- Right Sidebar -->
                <?php //require_once './application/pages/rightSidebar.php';   ?>
            </div>
            <div  style="display:none; text-align: center; color: #fff;  background: rgba(0,0,0,0.5); width: 100%; height: 100%; z-index: 2000; position: fixed; top:0;" id="wait">
                <img src="assets/images/uploading.gif" alt="load"  style="margin-top: 250px; width: 250px;" />
            </div>
            <!-- END wrapper -->
            <?php require_once './application/pages/footerForjs.php'; ?>
            <script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
            <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
            <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>
            <script src="assets/js/uploadfolder.js"></script>
            <script>
                /*$(document).ready(function() { 
                    $('#uploadForm').submit(function(e) {	
                        if($('#flup').val()) {
                            $("#progress-div").show();
                            e.preventDefault();
                            $('#loader-icon').show();
                            $(this).ajaxSubmit({ 
                                target:   '#targetLayer', 
                                beforeSubmit: function() {
                                  $("#progress-bar").width('0%');
                                },
                                uploadProgress: function (event, position, total, percentComplete){	
                                        $("#progress-bar").width(percentComplete + '%');
                                        $("#progress-bar").html('<div id="progress-status">' + percentComplete +' %</div>')
                                },
                                success:function (){
                                      getToken();
                                        $('#loader-icon').hide();
                                },
                                resetForm: true 
                            }); 
                            return false; 
                        }
                    });
                }); */
                                        

            </script>
            <script type="text/javascript">
                $(".select2").select2();
            </script>
            <script type="text/javascript">
                function getfolder(e) {
                    var files = e.target.files;
                    var path = files[0].webkitRelativePath;
                    var Folder = path.split("/");
                    //alert(Folder[0]);
                }
                $("#bulkUPLD").click(function () {
                    var validate = true;
                    $('input:required').each(function () {
                        if ($(this).val().trim() === '') {
                            validate = false;
                        }
                    });
                    var validate = true;
                    $('select:required').each(function () {
                        if ($(this).val().trim() === '') {
                            validate = false;
                        }
                    });
                    if (validate === true) {
                        // $("#wait").show();

                    }
                });

            </script>
            <script type="text/javascript">
                $("#flup").click(function () {
                    var inp = document.getElementById('flup');
                    var sizes = 0;
                    for (var i = 0; i < inp.files.length; ++i) {
                        sizes += inp.files.item(i).size;

                    }
                    $.post("application/ajax/valiadate_client_memory.php", {size: sizes}, function (result, status) {
                        if (status == 'success') {
                            //$("#stp").html(result);
                            var res = JSON.parse(result);
                            if (res.status == "true")
                            {
                                // $("#memoryres").html("<span style=color:green>" + res.msg + "</span>");
                                $.Notification.autoHideNotify('success', 'top center', 'Success', res.msg)
                                $("#bulkUPLD").removeAttr("disabled");
                                $("#bulkUPLD").attr("name", "bulkUpload");
                            } else {

                                $.Notification.autoHideNotify('warning', 'top center', 'Oops', res.msg)
                                $("#bulkUPLD").attr("disabled", "disabled");
                                $("#bulkUPLD").removeAttr("name");
                                //$("#memoryres").html("<span style=color:red>" + res.msg + "</span>");
                            }

                        }
                    });
                    //console.log(sizes/(1024*1024));   
                })

                $(document).ready(function () {
                    /*$('#uploadForm').submit(function (e) {
                        if ($('#flup').val()) {
                            $("#progress-div").show();
                            e.preventDefault();
                            $('#loader-icon').show();
                            $('#wait').show();
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
                                     $('#wait').hide();
                                },
                                resetForm: true
                            });
                            return false;
                        }
                    });*/

                    $("#storage").change(function(){
                        slid = $("#storage").val();
                        $.post("application/ajax/update_session.php", {slid: slid}, function (result, status) {
                            if (status == 'success') {
                                //alert(result);
                            }
                        });                        
                    });
                });

                // document.getElementById('flup').addEventListener('change', readMultipleFiles, false);
            </script>
            <script src="assets/js/plupload.full.min.js"></script>
            <script>
            
            window.addEventListener("load", function () {
                var allowed_extn = '<?php echo implode(',', ALLOWED_EXTN); ?>';                
                var uploader = new plupload.Uploader({
                runtimes: 'html5,html4',
                browse_button: 'flup',
                url: '../chunk-action.php',
                chunk_size: '4mb',
                
                filters: {
                  // max_file_size: '150mb',
                  //mime_types: [{title: "Image files", extensions: allowed_extn}]
                },
                
                init: {
                  PostInit: function () {
                    document.getElementById('filelist').innerHTML = '';
                  },
                  FilesAdded: function (up, files) {
                    plupload.each(files, function (file) {
                      document.getElementById('filelist').innerHTML += `<div id="${file.id}">${file.name} (${plupload.formatSize(file.size)}) <strong></strong></div>`;
                    });
                    $('#bulkUPLD').click(function(e) {
                        uploader.start();
                        e.preventDefault();
                    });
                    // uploader.start();
                  },
                  UploadProgress: function (up, file) {
                    document.querySelector(`#${file.id} strong`).innerHTML = `<span>${file.percent}%</span>`;
                  },
                  FileUploaded: function(up, file, info) {
                    // Called when file has finished uploading
                    var rsp = JSON.parse(info.response);
                    // document.getElementById('filelist').innerHTML += `<div id="${file.id}">${file.name} (${plupload.formatSize(rsp.info)}) <strong></strong></div>`;
                    console.log(rsp.info);
                  },
                  UploadComplete: function(up, files) {
                        // Called when all files are either uploaded or failed
                    // console.log(files);
                    // var rsp = JSON.parse(files.response);
                    // alert(rsp.info);
                    // alert(files);
                    var my_val = 'Bio94gy6xZbcG9jhxu1GtrnVSBGyMQHfnxoPf9HLuiM';
                    $.ajax({
                        url: "application/ajax/sendUploadEmail.php",
                        type: "post",
                        async: true,
                        data: {'my_val': my_val},
                        contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
                        success: function(result) {                   
                            if(result=='sent'){
                                uploadSuccess("", "<?=$lang['Fle_Uplded_Sucsfly']?>");
                            }               
                        }
                    });
                  },
                  Error: function (up, err) {
                    var rsp = JSON.parse(err.response);
                    alert(rsp.info);
                    console.log(err);
                  }
                }
              });
              uploader.init();
            });

            </script>
           
    </body>
</html>
