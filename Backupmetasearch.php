<!DOCTYPE html>
<html>

    <?php
    set_time_limit(0);
    require_once './loginvalidate.php';
    require_once './application/config/database.php';
    //require_once './application/config/db_sql.php';
    require_once './application/pages/head.php';


    //for user role

    $chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

    $rwgetRole = mysqli_fetch_assoc($chekUsr);

    // echo $rwgetRole['dashboard_mydms']; die;
    if ($rwgetRole['metadata_search'] != '1') {
        header('Location: ./index');
    }
    ?>
    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />

    <!-- Plugin Css-->
    <link rel="stylesheet" href="assets/plugins/magnific-popup/css/magnific-popup.css" />
    <link rel="stylesheet" href="assets/plugins/jquery-datatables-editable/datatables.css" />
    <link href="assets/plugins/datatables/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="assets/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" />   
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
                                    <li>
                                        <a href="metasearch"><?php echo $lang['Ezeefile_DMS']; ?></a>
                                    </li>
                                    <li class="active">
                                        <?php echo $lang['MetaData_Search']; ?>
                                    </li>
                                </ol>
                            </div>
                        </div>

                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <?php echo $lang['Required_fields_are_marked_with_a']; ?>(<span style="color:red;">*</span>)
                            </div>
                            <div class="box-body ">

                                <div class="card-box">
                                    <div class="row">
                                        <form >
                                            <!--  <div class="form-group row">
                                                  <div class="col-md-2">
                                                      <label for="userName">Select Depth Level<span style="color:red;">*</span></label>
                                                  </div>
                                                  <div class="col-md-4">
                                                      <select class="form-control select2" id="depth_level" name="depthLevel" required >
                                                          <option selected disabled style="background: #808080; color: #121213;">Select Depth</option>
                                            <?php /*
                                              $depth = mysqli_query($db_con, "select sl_depth_level from tbl_storage_level group by sl_depth_level");
                                              while ($rwDepth = mysqli_fetch_assoc($depth)) {
                                              //if ($_GET['depthLevel'] == $rwDepth['sl_depth_level']) {
                                              //echo'<option selected>' . $rwDepth['sl_depth_level'] . '</option>';
                                              //} else {
                                              echo '<option>' . $rwDepth['sl_depth_level'] . '</option>';
                                              // }
                                              } */
                                            ?>
                                                      </select>
                                                  </div>
                                              </div>
                                              <div class="form-group row">
                                                  <div class="col-md-2">
                                                      <label for="userName">Select Parent<span style="color:red;">*</span></label>
                                                  </div>
                                                  <div class="col-md-4">
                                                      <select class="form-control select2" id="parent" name="parentName" required>
                                                          <option selected disabled style="background: #808080; color: #121213;">Select Parent</option>
  
                                                      </select>
                                                  </div>
                                              </div>
                                              <div class="clearfix"></div> 
                                              <div id="childWithMeta">
                                                  <div class="form-group row">
                                                      <div class="col-md-2">
                                                          <label for="userName">Select Node</label>
                                                      </div>
                                                      <div class="col-md-4">
                                                          <select class="form-control select2" id="child_level" name="childName" >
                                                              <option selected disabled style="background: #808080; color: #121213;">Select Child</option>
                                                          </select>
                                                      </div>
                                                  </div>
                                                  <div class="form-group row" id="multiselect">
                                                      <div class="col-md-2">
                                                          <label for="userName">Select Metadata</label>
                                                      </div>
                                                      <div class="col-md-4">
  
                                                          <select  class="form-control select2" id="my_multi_select1" name="metadata" required>
                                                              <option selected disabled value="">select metadata</option>
                                            <?php /*
                                              $meta = mysqli_query($db_con, "select * from tbl_metadata_master");
                                              while ($rwMeta = mysqli_fetch_assoc($meta)) {
                                              if ($_GET['metadata'] == $rwMeta['field_name']) {
                                              echo '<option selected>' . $rwMeta['field_name'] . '</option>';
                                              } else {
                                              echo '<option>' . $rwMeta['field_name'] . '</option>';
                                              }
                                              } */
                                            ?>
                                                          </select>
                                                      </div>
                                                  </div>
                                              </div> -->

                                            <div class="form-group row">
                                                <div class="col-md-2">
                                                    <label for="userName"><?php echo $lang['Ezeefile_DMS']; ?><span style="color:red;">*</span></label>
                                                </div>
                                                <div class="col-md-4">
                                                    <?php
                                                    $perm = mysqli_query($db_con, "select sl_id from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]' group by sl_id");
                                                    $rwPerm = mysqli_fetch_assoc($perm);
                                                    $slperm = $rwPerm['sl_id'];
                                                    $sllevel = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slperm'");
                                                    $rwSllevel = mysqli_fetch_assoc($sllevel);
                                                    $level = $rwSllevel['sl_depth_level'];
                                                    ?>
                                                    <select class="form-control select2" id="parent" name="parentName" required>
                                                        <option disabled selected><?php echo $lang['Select']; ?></option>
                                                        <?php
                                                        if (isset($_GET['parentName']) && !empty($_GET['parentName'])) {
                                                            $parentId = $_GET['parentName'];
                                                        }
                                                        findChild($slperm, $level, $slperm, $parentId);
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

                                                        $sql_child = "select * FROM tbl_storage_level WHERE sl_parent_id = '$sl_id' ";

                                                        $sql_child_run = mysqli_query($db_con, $sql_child) or die('Error:' . mysqli_error($db_con));

                                                        if (mysqli_num_rows($sql_child_run) > 0) {

                                                            while ($rwchild = mysqli_fetch_assoc($sql_child_run)) {

                                                                $child = $rwchild['sl_id'];
                                                                findChild($child, $level, $slperm, $parentId);
                                                            }
                                                        }
                                                    }

                                                    function parentLevel($slid, $db_con, $slperm, $level, $value) {

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

                                            <div class="form-group row" id="multiselect">
                                                <div class="col-md-2">
                                                    <label for="userName"><?php echo $lang['Select_Metadata']; ?></label>
                                                </div>
                                                <div class="col-md-4">
                                                    <div id="metajax">
                                                        <select  class="selectpicker select2" data-live-search="true" data-style="btn-white" id="my_multi_select1" name="metadata[]" required>
                                                            <option selected disabled value=""><?php echo $lang['Select_Metadata']; ?></option>
                                                            <?php
                                                            if (isset($_GET['metadata']) && !empty($_GET['metadata'])) {

                                                                echo '<option selected>' . $_GET['metadata'] . '</option>';
                                                            }
                                                            $metadatacount = 2;
                                                            $arrarMeta = array();
                                                            $metas = mysqli_query($db_con, "select * from tbl_metadata_to_storagelevel where sl_id='$slid'");
                                                            while ($metaval = mysqli_fetch_assoc($metas)) {
                                                                array_push($arrarMeta, $metaval['metadata_id']);
                                                            }
                                                            $meta = mysqli_query($db_con, "select * from tbl_metadata_master order by field_name asc");
                                                            while ($rwMeta = mysqli_fetch_assoc($meta)) {
                                                                if (in_array($rwMeta['id'], $arrarMeta)) {
                                                                    if ($rwMeta['field_name'] != 'filename') {
                                                                        echo '<option>' . $rwMeta['field_name'] . '</option>';
                                                                        $metadatacount++;
                                                                    }
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-2">
                                                    <label>Select Condition</label>
                                                </div>
                                                <div class="col-md-2">
                                                    <select class="form-control" name="cond" required>
                                                        <option disabled selected style="background: #808080; color: #121213;"><?php echo $lang['Select']; ?></option>
                                                        <option <?php
                                                        if (isset($_GET['cond']) && !empty($_GET['cond']) && $_GET['cond'] == 'Equal') {
                                                            echo'selected';
                                                        }
                                                        ?> value="Equal"><?php echo $lang['Equal'] ?></option>
                                                        <option <?php
                                                        if (isset($_GET['cond']) && !empty($_GET['cond']) && $_GET['cond'] == 'Contains') {
                                                            echo'selected';
                                                        }
                                                        ?> value="Contains"><?php echo $lang['Contains'] ?></option>
                                                        <option <?php
                                                        if (isset($_GET['cond']) && !empty($_GET['cond']) && $_GET['cond'] == 'Like') {
                                                            echo'selected';
                                                        }
                                                        ?> value="Like"><?php echo $lang['Like'] ?></option>
                                                        <option <?php
                                                        if (isset($_GET['cond']) && !empty($_GET['cond']) && $_GET['cond'] == 'Not Like') {
                                                            echo'selected';
                                                        }
                                                        ?> value="Not Like"><?php echo $lang['Not_Like'] ?></option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="text" class="form-control" name="searchText" required value="<?php echo $_GET['searchText'] ?>" placeholder="<?php echo $lang['entr_srch_txt_hr'] ?>">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="contents col-lg-12"></div>
                                            </div> 
                                            <div class="col-md-12">


                                                <div class="col-md-2">
                                                    <button type="submit" class="btn btn-primary" id="search"><i class="fa fa-search"></i><?php echo $lang['Search']; ?></button>
                                                </div>
                                                <div class="col-md-2">
                                                    <a href="javascript:void(0)" class="btn btn-primary" id="addfields"><i class="fa fa-plus"></i></a>
                                                </div>
                                            </div>

                                        </form>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="">

                                    <?php
                                    if (isset($_GET['searchText'])) {
                                        $metadata = $_GET['metadata'];
                                        $cond = $_GET['cond'];
                                        $searchText = $_GET['searchText'];
                                        $parentNameID = $_GET['parentName'];

                                        $searchText = mysqli_real_escape_string($db_con, $searchText);
                                        $res = searchAllDB($searchText, $cond, $metadata, $parentNameID, $db_con);
                                    }
                                    ?>	
                                </div>
                            </div>
                            <!-- end: page -->

                        </div> <!-- end Panel -->

                    </div> <!-- container -->

                </div> <!-- content -->

                <?php require_once './application/pages/footer.php'; ?>

            </div>
            <!-- ============================================================== -->
            <!-- End Right content here -->
            <!-- ============================================================== -->


            <!-- Right Sidebar -->
            <?php require_once './application/pages/rightSidebar.php'; ?>
            <!-- /Right-bar -->
        </div>
        <!-- END wrapper -->

        <?php require_once './application/pages/footerForjs.php'; ?>

        <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>

        <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>
        <script src="assets/plugins/datatables/jquery.dataTables.min.js"></script>
        <script src="assets/plugins/datatables/dataTables.bootstrap.js"></script>

        <script>
            $("a#showPic").click(function () {
                var path = $(this).attr('data');
                // alert(id);

                $.post("application/ajax/displayImage.php", {PATH: path}, function (result, status) {
                    if (status == 'success') {
                        $("#Display").html(result);
                        //alert(result);
                    }
                });
            });

        </script>
        <script src="assets/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
        <script>
            jQuery(document).ready(function () {
                $('.selectpicker').selectpicker();

            });

        </script>
        <script type="text/javascript">

            $(document).ready(function () {
                $('form').parsley();
                $('#datatable').dataTable();

            });
            $(".select2").select2();
            //firstname last name 
            /*
             $("#depth_level").change(function () {
             var lbl = $(this).val();
             //alert(lbl);
             $.post("application/ajax/parentList.php", {level: lbl}, function (result, status) {
             if (status == 'success') {
             $("#parent").html(result);
             }
             });
             });*/
            /*  $("#parent").change(function () {
             var slId = $(this).val();
             // alert(slId);
             $.post("application/ajax/childListWithMetaData.php", {sl_id: slId}, function (result, status) {
             if (status == 'success') {
             $("#childWithMeta").html(result);
             }
             });
             });*/

            $("#parent").change(function () {
                var slId = $(this).val();
                $.post("application/ajax/childListWithMetaData.php", {sl_id: slId}, function (result, status) {
                    if (status == 'success') {
                        //$("#multiselect").html(result);
                        $("#metajax").html(result);
                        //  alert(result);
                    }
                });
            });
            $("a#video").click(function () {
                var id = $(this).attr('data');

                $.post("application/ajax/videoformat.php", {vid: id}, function (result, status) {
                    if (status == 'success') {
                        $("#videofor").html(result);
                        //alert(result);

                    }
                });
            });
            $("a#audio").click(function () {
                var id = $(this).attr('data');

                $.post("application/ajax/audioformat.php", {aid: id}, function (result, status) {
                    if (status == 'success') {
                        $("#foraudio").html(result);
                        //alert(result);

                    }
                });
            });
            /*
             $("#child_level").change(function () {
             var slId = $(this).val();
             //alert(slId);
             $.post("application/ajax/metaListslwise.php", {sl_id: slId}, function (result, status) {
             if (status == 'success') {
             $("#multiselect").html(result);
             }
             //  alert(result);
             });
             }); */
        </script>

        <div id="full-width-modal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myLargeModalLabel"><?php echo $lang['Image_viewer']; ?></h4>
                    </div>
                    <div class="modal-body">
                        <div id="Display"></div>
                    </div>
                    <div class="modal-footer">

                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                    </div>
                </div>
            </div>

        </div>

        <!-- for audio model-->
        <div id="modal-audio" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel"><?php echo $lang['Play_Audio']; ?></h4>
                    </div>
                    <div id="foraudio">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>

                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <!--for video model-->
        <div id="modal-video" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel"><?php echo $lang['Play_video']; ?></h4>
                    </div>
                    <div  id="videofor">


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>

                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <?php

        //print_r($res);
        function searchAllDB($search, $cond, $metadata, $parentNameID, $db_con) {
            if (isset($_SESSION['lang'])) {
                $file = $_SESSION['lang'] . ".json";
            } else {
                $file = "English.json";
            }
            $data = file_get_contents($file);
            $lang = json_decode($data, true);
            //  $out = "";
            ?>
            <table class="table table-striped table-bordered dataTable no-footer" id="datatable" role="grid" aria-describedby="datatable_info">
                <?php
                $table = "tbl_document_master";
                //$out .= $table.";";
                $sql_search = "select * from " . $table . " where doc_name=";
                $sql_search_fields = Array();

                echo '<thead><tr>';
                echo '<th>' . $lang['Sr_No'] . '</th>';
                echo '<th>' . $lang['File_Name'] . '</th>';
                echo '<th>' . $lang['File_Size'] . '</th>';
                echo '<th>' . $lang['No_of_Pages'] . '</th>';
                echo '<th>' . $lang['MetaData'] . '</th>';
                echo'</tr></thead>';
                if ($cond == 'Like') {
                    $sql_search_fields[] = 'CONVERT(`' . $metadata . "` USING utf8) like('%" . $search . "%')";
                } else if ($cond == 'Not Like') {
                    $sql_search_fields[] = 'CONVERT(`' . $metadata . "` USING utf8) not like('%" . $search . "%')";
                } else if ($cond == 'Contains') {
                    $sql_search_fields[] = 'CONVERT(`' . $metadata . "` USING utf8) contains('%" . $search . "%')";
                } else if ($cond == 'Equal') {
                    $sql_search_fields[] = "`" . $metadata . "` ='" . $search . "'";
                }

                $sql_search .= $parentNameID;
                $sql_search .= ' and (';
                $sql_search .= implode(" OR ", $sql_search_fields);
                $sql_search .= ')';

                //echo $sql_search;
                $rs3 = mysqli_query($db_con, $sql_search);
                //$out .= mysqli_num_rows($rs3)."\n ok";
                if (mysqli_num_rows($rs3) > 0) {
                    echo'<tbody>';
                    $i = 1;
                    while ($rw = mysqli_fetch_assoc($rs3)) {
                        //print_r($rw);
                        echo '<tr>';
                        echo '<td>' . $i . '</td>';
                        echo '<td>';
                        ?>
                        <!--for pdf files -->
                        <?php if ($rw['doc_extn'] == 'pdf') { ?>
                            <a href="flipflop-viewer?i=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])) ?>&i=<?php echo urlencode(base64_encode($rw['doc_id'])); ?>" id="fancybox-inner" class="pdfview" target="_blank"><?php echo $rw['old_doc_name']; ?>
                                <i class="ti-book" style="font-size: 18px;"></i></a>
                            <a href="viewer?id=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])) ?>&i=<?php echo urlencode(base64_encode($rw['doc_id'])); ?>" id="fancybox-inner" class="pdfview"  target="_blank">
                                <i class="fa fa-file-pdf-o"></i></a>
                            <!--for image viewer -->
                        <?php } else if ($rw['doc_extn'] == 'jpg' || $rw['doc_extn'] == 'png' || $rw['doc_extn'] == 'gif') { ?>
                            <a href="#" data-toggle="modal" data-target="#full-width-modal" id="showPic" data="<?php echo $rw['doc_path']; ?>"><?php echo substr($rw['old_doc_name'], stripos($rw['old_doc_name'], "_") + 0); ?> <i class="fa fa-picture-o"></i></a>
                            <!--for tiff files -->
                        <?php } else if ($rw['doc_extn'] == 'tiff' || $rw['doc_extn'] == 'tif') { ?>
                            <a href="file?uid=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&file=<?php echo urlencode(base64_encode($rw['doc_id'])); ?>" target="_blank"><?php echo substr($rw['old_doc_name'], stripos($rw['old_doc_name'], "_") + 0); ?> <i class="fa fa-picture-o"></i></a>
                            <!--for xlsx files -->
                        <?php } else if ($rw['doc_extn'] == 'xlsx') { ?>
                            <a href="excel?file=<?php echo urlencode(base64_encode($rw['doc_id'])); ?>" target="_blank"><?php echo substr($rw['old_doc_name'], stripos($rw['old_doc_name'], "_") + 0); ?> <i class="fa fa-file-excel-o"></i></a>
                            <!--for docx files -->
                        <?php } else if ($rw['doc_extn'] == 'docx' || $rw['doc_extn'] == 'doc') { ?>
                            <a href="docx?file=<?php echo urlencode(base64_encode($rw['doc_id'])); ?>" target="_blank"><?php echo substr($rw['old_doc_name'], stripos($rw['old_doc_name'], "_") + 0); ?> <i class="fa fa-file-word-o"></i></a>

                            <!--for audio player -->
                        <?php } else if ($rw['doc_extn'] == 'mp3') { ?>
                            <a href="javascript:void(0)" data-toggle="modal" data-target="#modal-audio" data="<?php echo $rw['doc_id']; ?>" id="audio">
                                <?php echo $rw['old_doc_name']; ?> <i class="fa fa-music"></i>
                            </a>
                            <!--for video player -->
                        <?php } else if ($rw['doc_extn'] == 'mp4') { ?>
                            <a href="javascript:void(0)" data-toggle="modal" data-target="#modal-video" data="<?php echo $rw['doc_id']; ?>" id="video"> <?php echo $rw['old_doc_name']; ?> <i class="fa fa-video-camera"></i></a>
                        <?php } else { ?>

                            <a href="extract-here/<?php echo $rw['doc_path']; ?>" id="fancybox-inner" target="_blank"><?php echo $rw['old_doc_name']; ?>
                            </a>
                        <?php } ?>
                        <?php
                        '</td>';
                        echo'<td>' . round($rw['doc_size'] / 1024) . 'KB</td>';
                        echo'<td>' . $rw['noofpages'] . '</td>';
                        ?>
                        <?php echo'<td>' ?>
                        <?php
                        $getMetaId = mysqli_query($db_con, "select * from tbl_metadata_to_storagelevel where sl_id = '$rw[doc_name]'") or die('Error:' . mysqli_error($db_con));

                        while ($rwgetMetaId = mysqli_fetch_assoc($getMetaId)) {

                            $getMetaName = mysqli_query($db_con, "select * from tbl_metadata_master where id = '$rwgetMetaId[metadata_id]'") or die('Error:' . mysqli_error($db_con));

                            while ($rwgetMetaName = mysqli_fetch_assoc($getMetaName)) {

                                echo "<strong>" . $rwgetMetaName['field_name'] . "</strong>: ";
                                $meta = mysqli_query($db_con, "select `$rwgetMetaName[field_name]` from tbl_document_master where doc_id='$rw[doc_id]'");
                                $rwMeta = mysqli_fetch_array($meta);
                                echo $rwMeta[$rwgetMetaName['field_name']];
                                echo " | ";
                            }
                        }

                        echo'</td>';
                        echo'</tr>';
                        $i++;
                    }
                    echo '</tbody>';
                    mysqli_close($rs3);
                }
                ?>
            </table>
            <?php
            //return $out;
        }
        ?>
</html>
<script>

    $(document).ready(function () {
        var max_fields = <?= $metadatacount; ?>; //maximum input boxes allowed
        var wrapper = $(".contents"); //Fields wrapper
        var add_button = $("#addfields"); //Add button ID
        var id =<?= $slid ?>;

        var x = 1; //initlal text box count
        $(add_button).click(function (e) { //on add input button click
            e.preventDefault();

            if (x < max_fields) { //max input box allowed
                x++;
                //text box increment
                $.ajax({url: "application/ajax/addMultipleMeataDtaSearch?id=" + id, success: function (result) {
                        $(wrapper).append("<div class='col-lg-12' style='margin-bottom:17px'>" + result + "<button class='remove_field btn btn-primary'><i class='fa fa-minus-circle' aria-hidden='true'></i></a>" + "</div>"); //add input box


                    }});

            } else
            {
                alert("No. More meta data available");
                $("#addfields").hide();
            }
        });

        $(wrapper).on("click", ".remove_field", function (e) { //user click on remove text
            e.preventDefault();
            $(this).parent('div').remove();
            x--;
            $("#addfields").show();
        })
    });


</script>