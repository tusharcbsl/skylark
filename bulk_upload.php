<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    require_once './application/pages/head.php';
    require_once './classes/ftp.php';


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
        #progress-div {border:#193860 1px solid;margin:30px 0px;border-radius:4px;text-align:center;}
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
                                    <li class="active"> <?php echo $lang['Bulk_Upload'] ?></li>
                                     <li> <a href="#" data-toggle="modal" data-target="#help-modal" id="helpview" data="16" title="<?= $lang['help']; ?>"><i class="fa fa-question-circle"></i> </a></li>
                                    <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                                </ol>
                            </div>
                        </div>
                        <div class="row">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h4 class="header-title col-md-6"><?php echo $lang['Required_fields_are_marked_with_a'] ?> (<span style="color:red;">*</span>)</h4>
                                    <a href="assets/images/bulkFileUpload.csv" download class="pull-right btn btn-primary"><i class="fa fa-download"></i> <?= $lang['downlod_sample_csv']; ?></a>
                                </div>
                                <div class="box-body">

                                    <div class="col-lg-12">
                                        <div class="card-box">
                                            <form data-parsley-validate novalidate method="post" action="uploadmutiplefiles.php"  id="uploadForm" enctype="multipart/form-data">
                                                <div class="form-group row">
                                                    <div class="col-md-2">
                                                        <label for="fup"><?php echo $lang['Sel_file_upload'] ?><span style="color:red;">*</span></label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="file" name="zip_upload[]" class="filestyle" parsley-trigger="change" required placeholder="<?php echo $lang['Entr_strg_nm'] ?>" class="form-control" id="flup" webkitdirectory mozdirectory msdirectory odirectory directory multiple>
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
                                                            $sllevel = mysqli_query($db_con, "select * from tbl_storage_level where sl_id in($slpermIdes) and delete_status=0 and readonly=0 order by sl_name asc");
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

                                                            $sql_child = "select * FROM tbl_storage_level WHERE sl_parent_id = '$sl_id'  and delete_status=0 and readonly=0 order by sl_name asc";

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
                                                                $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid'  and delete_status=0 and readonly=0") or die('Error' . mysqli_error($db_con));
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
                                                                    $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid' and delete_status=0 and readonly=0") or die('Error' . mysqli_error($db_con));
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
                                                    <a href="bulk_upload" class="btn btn-danger waves-effect waves-light m-l-5">
                                                        <?php echo $lang['Cancel']; ?>
                                                    </a>
                                                </div>
                                            </form>
                                            <br>
                                            <div class="row">
                                                <div class="col-md-12">
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
                        </div>



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
            $(document).ready(function () {
                $('#uploadForm').submit(function (e) {
                    if ($('#flup').val()) {
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
                            resetForm: true
                        });
                        return false;
                    }
                });
				
				
            });
			
			
			

            


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
                $("#flup").change(function () {
					setInterval(lastActive, 5000);
					
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
                });
				
				function lastActive(){
					
					$.post("application/ajax/lastActive.php", {lgt:2}, function(result, status){
						if(result=='2'){
							
							initSessionMonitor();
								
						}else{
							
						}
					}); 
				}

                // document.getElementById('flup').addEventListener('change', readMultipleFiles, false);
            </script>

    </body>
</html>
