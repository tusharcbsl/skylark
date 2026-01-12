<!DOCTYPE html>
<html>
    <?php
    set_time_limit(0);
    require_once './loginvalidate.php';
    require_once './application/pages/head.php';
    require_once './classes/ftp.php';
    require_once './application/pages/function.php';
    $archivedDoc = urldecode(base64_decode(xss_clean($_GET['archived'])));
    if (($rwgetRole['metadata_search'] != '1' && $rwgetRole['dashboard_mydms'] != '1') || empty($slpermIdes)) {
        header('Location: ./index');
        exit();
    }

    $slid = urldecode(base64_decode($_GET['id']));
    $selectedStorage = preg_replace('/[^0-9]/', '', $slid);
    if (isset($selectedStorage) && !empty($selectedStorage)) {
        $folder = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$selectedStorage' and delete_status='0'");
        $rwFolder = mysqli_fetch_assoc($folder);
        $slid = $rwFolder['sl_id'];
        $parentid = $rwFolder['sl_name'];
    }


    //start checking storage permission
    $perm = mysqli_query($db_con, "select sl_id from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]' group by sl_id");
    $slperms = array();
    while ($rwPerm = mysqli_fetch_assoc($perm)) {
        $slperms[] = $rwPerm['sl_id'];
    }
    $sl_perm = implode(',', $slperms);
    $slids = findsubfolder($sl_perm, $db_con);

    $slids = implode(',', $slids);
    //end checking storage permission
    ?>
    <link href="assets/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" />
    <link href="assets/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" />   
    <!--for filter calnder-->
    <link href="assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
    <link href="assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet">
    <link href="assets/plugins/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">

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
                                        <a href="metasearch"><?php echo $lang['Search']; ?></a>
                                    </li>
                                    <li class="active">
                                        <?php echo $lang['MetaData_Search']; ?>
                                    </li>
                                    <li> <a href="#" data-toggle="modal" data-target="#help-modal" id="helpview" data="17" title="<?= $lang['help']; ?>"><i class="fa fa-question-circle"></i> </a></li>
                                    <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                                </ol>
                            </div>
                        </div>
                        <div class="box box-primary m-b-50">
                            <div class="box-header with-border">
                                <div class="col-sm-7">
                                    <h4 class="header-title"><?php echo $lang['Required_fields_are_marked_with_a']; ?>(<span style="color:red;">*</span>)</h4>
                                </div>
                                <div class="col-sm-5">
                                    <?php if ($rwgetRole['save_query'] == '1' && !empty($_GET['id'])) { ?>
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#savequery" id="svequery"><i class="fa fa-save" aria-hidden="true" title="<?php echo $lang['Sve_Qry']; ?>"></i> <?php echo $lang['Sve_Qry']; ?></button>
                                    <?php } ?>
                                    <a href="Frequently_queries" class="btn btn-primary"><i class="fa fa-eye"></i> <?php echo $lang['save_queries']; ?></a>
                                    <a href="metasearch" class="btn btn-warning"><?php echo $lang['Reset']; ?></a>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="container">
                                    <div class="row">
                                        <form method="get">
                                            <div class="form-group row">
                                                <div class="col-md-6">
                                                    <label><?php echo $lang['Select_Storage']; ?></label>
                                                    <select class="form-control parent select2" data-live-search="true" id="parent" name="id">
                                                        <option value=""><?php echo $lang['Select_Storage']; ?></option>
                                                        <?php
                                                        if (isset($selectedStorage) && !empty($selectedStorage)) {
                                                            $parentId = $selectedStorage;
                                                        }
                                                        if (!empty($slpermIdes)) {
                                                            $sllevel = mysqli_query($db_con, "select * from tbl_storage_level where sl_id in($slpermIdes) and delete_status='0' order by sl_name asc");
                                                            while ($rwSllevel = mysqli_fetch_assoc($sllevel)) {
                                                                $level = $rwSllevel['sl_depth_level'];
                                                                $slId = $rwSllevel['sl_id'];
                                                                $slperm = $rwSllevel['sl_id'];
                                                                findsubFolders($slId, $level, $slperm, $parentId);
                                                            }
                                                        }
                                                        ?>
                                                    </select> 
                                                    <?php

                                                    function findsubFolders($sl_id, $level, $slperm, $parentId) {

                                                        global $db_con;

                                                        if ($sl_id == $parentId) {
                                                            echo '<option value="' . base64_encode(urlencode($sl_id)) . '"  selected>';
                                                            parentLevel($sl_id, $db_con, $slperm, $level, '');
                                                            echo '</option>';
                                                        } else {
                                                            echo '<option value="' . base64_encode(urlencode($sl_id)) . '">';
                                                            parentLevel($sl_id, $db_con, $slperm, $level, '');
                                                            echo '</option>';
                                                        }

                                                        $sql_child = "select * FROM tbl_storage_level WHERE sl_parent_id = '$sl_id' and delete_status='0' order by sl_name asc";

                                                        $sql_child_run = mysqli_query($db_con, $sql_child) or die('Error:' . mysqli_error($db_con));

                                                        if (mysqli_num_rows($sql_child_run) > 0) {

                                                            while ($rwchild = mysqli_fetch_assoc($sql_child_run)) {

                                                                $child = $rwchild['sl_id'];
                                                                findsubFolders($child, $level, $slperm, $parentId);
                                                            }
                                                        }
                                                    }

                                                    function parentLevel($slid, $db_con, $slperm, $level, $value) {

                                                        if ($slperm == $slid) {
                                                            $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid' and delete_status='0'") or die('Error' . mysqli_error($db_con));
                                                            $rwParent = mysqli_fetch_assoc($parent);

                                                            if ($level < $rwParent['sl_depth_level']) {
                                                                parentLevel($rwParent['sl_parent_id'], $db_con, $slperm, $level, $rwParent['sl_name']);
                                                            }
                                                        } else {
                                                            $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid' and sl_parent_id='$slperm' and delete_status='0'") or die('Error' . mysqli_error($db_con));
                                                            if (mysqli_num_rows($parent) > 0) {

                                                                $rwParent = mysqli_fetch_assoc($parent);
                                                                if ($level < $rwParent['sl_depth_level']) {
                                                                    parentLevel($rwParent['sl_parent_id'], $db_con, $slperm, $level, $rwParent['sl_name']);
                                                                }
                                                            } else {
                                                                $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid' and delete_status='0'") or die('Error' . mysqli_error($db_con));
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

                                                <div class="col-sm-6">
                                                    <div class="btn-group pull-right">
                                                        <button type="button" class="btn btn-danger waves-effect btn-xs" data-placement="left" data-toggle="tooltip" title="Retention Document">Retention</button>
                                                        <button type="button" class="btn btn-default waves-effect btn-xs" data-toggle="tooltip" title="Primary Document"> Primary</button>
                                                        <button type="button" class="btn btn-success waves-effect btn-xs" data-toggle="tooltip" title="Checkout Document"> Checkout</button>
                                                        <button type="button" class="btn btn-warning waves-effect btn-xs" data-toggle="tooltip" title="Expired Document"> Expired</button>
                                                        <button type="button" class="btn btn-info waves-effect btn-xs" data-toggle="tooltip" data-placement="left" title="Expiry & Retention Document"> Both</button>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 m-t-10">
                                                    <div class="form-group">
                                                        <div class="checkbox checkbox-primary checkbox-single">
                                                            <input type="checkbox" value="Mg==" id="extdate" name="archived" <?php echo (($archivedDoc == '2') ? "checked" : ""); ?>>
                                                            <label for="extdate"><?= $lang['search_expired_document']; ?></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php if (isset($_GET['searchText'])) { ?>
                                                <div id="metadata_div">
                                                    <?php for ($j = 0; $j < count($_GET['searchText']); $j++) { ?>
                                                        <div class="form-group row numid-<?php echo $j; ?>" id="multiselect">
                                                            <div class="col-md-3" id="metajax">

                                                                <select class="form-control select2" onchange="metavaluechange(this, '<?php echo $j; ?>');" id="kk" data-live-search="true" name="metadata[]" required>
                                                                    <option selected  value=""><?php echo $lang['Select_Metadata']; ?></option>
                                                                    <option value="old_doc_name" <?php
                                                                    if ($_GET['metadata'][$j] == "old_doc_name") {
                                                                        echo'selected';
                                                                    }
                                                                    ?>><?php echo $lang['FileName']; ?></option>
                                                                    <option value="noofpages"<?php
                                                                    if ($_GET['metadata'][$j] == "noofpages") {
                                                                        echo'selected';
                                                                    }
                                                                    ?>><?php echo $lang['No_Of_Pages']; ?></option>
                                                                            <?php
                                                                            $metadatacount = 3;
                                                                            if (!empty($parentId)) {
                                                                                $metas = mysqli_query($db_con, "select * from tbl_metadata_to_storagelevel where sl_id='$parentId'");
                                                                            } else {
                                                                                $metas = mysqli_query($db_con, "select distinct metadata_id from tbl_metadata_to_storagelevel where sl_id in($slids)");
                                                                            }
                                                                            while ($metaval = mysqli_fetch_assoc($metas)) {
                                                                                $meta = mysqli_query($db_con, "select * from tbl_metadata_master WHERE id='$metaval[metadata_id]' order by field_name asc");
                                                                                $rwMeta = mysqli_fetch_assoc($meta);

                                                                                if ($rwMeta['field_name'] != 'filename') {
                                                                                    if ($_GET['metadata'][$j] == $rwMeta['field_name']) {
                                                                                        echo '<option selected>' . $rwMeta['field_name'] . '</option>';
                                                                                    } else {
                                                                                        echo '<option>' . $rwMeta['field_name'] . '</option>';
                                                                                    }
                                                                                    $metadatacount++;
                                                                                }
                                                                            }
                                                                            $metadatacount = $metadatacount - count($_GET['metadata']);
                                                                            ?>
                                                                </select>
                                                            </div>

                                                            <div class="col-md-3">
                                                                <select class="form-control select2" data-live-search="true"  name="cond[]" required>
                                                                    <option value="" selected><?php echo $lang['Slt_Condition']; ?></option>
                                                                    <option <?php
                                                                    if (isset($_GET['cond'][$j]) && !empty($_GET['cond'][$j]) && $_GET['cond'][$j] == 'Equal') {
                                                                        echo'selected';
                                                                    }
                                                                    ?> value="Equal"><?php echo $lang['Equal'] . ' ( = )'; ?></option>
                                                                    <option <?php
                                                                    if (isset($_GET['cond'][$j]) && !empty($_GET['cond'][$j]) && $_GET['cond'][$j] == 'Contains') {
                                                                        echo'selected';
                                                                    }
                                                                    ?> value="Contains"><?php echo $lang['Contains'] ?></option>
                                                                    <option <?php
                                                                    if (isset($_GET['cond'][$j]) && !empty($_GET['cond'][$j]) && $_GET['cond'][$j] == 'Like') {
                                                                        echo'selected';
                                                                    }
                                                                    ?> value="Like"><?php echo $lang['Like']; ?></option>
                                                                    <option <?php
                                                                    if (isset($_GET['cond'][$j]) && !empty($_GET['cond'][$j]) && $_GET['cond'][$j] == 'Not Like') {
                                                                        echo'selected';
                                                                    }
                                                                    ?> value="Not Like"><?php echo $lang['Not_Like'] . ' ( != )'; ?></option>
                                                                    <option <?php
                                                                    if (isset($_GET['cond'][$j]) && !empty($_GET['cond'][$j]) && $_GET['cond'][$j] == 'Between') {
                                                                        echo'selected';
                                                                    }
                                                                    ?> value="Between"><?php echo $lang['Between']; ?></option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div id="daterange<?= $j; ?>" class="input-group" <?php if (empty($_GET['startDate'][$j]) && empty($_GET['endDate'][$j])) { ?> style="display:none;" <?php } ?>>
                                                                    <div class="input-daterange input-group date-range">
                                                                        <input type="text" id="sdate<?= $j; ?>" class="form-control readonly" name="startDate[]" value="<?php echo xss_clean($_GET['startDate'][$j]); ?>" placeholder="<?= $lang['dd_mm_yyyy']; ?>" title="<?= $lang['dd_mm_yyyy']; ?>"  />
                                                                        <span class="input-group-addon bg-custom b-0 text-white"><?php echo $lang['to']; ?></span>
                                                                        <input type="text" id="edate<?= $j; ?>"  class="form-control readonly" name="endDate[]" value="<?php echo xss_clean($_GET['endDate'][$j]); ?>"   placeholder="<?= $lang['dd_mm_yyyy']; ?>" title="<?= $lang['dd_mm_yyyy']; ?>" />
                                                                    </div>
                                                                </div>
                                                                <input id="textsearch<?= $j; ?>" <?php if (!empty($_GET['startDate'][$j]) && !empty($_GET['endDate'][$j])) { ?> style="display:none;" <?php } else { ?> value="<?php echo xss_clean($_GET['searchText'][$j]); ?>" <?php } ?> type="text" class="form-control translatetext specialchaecterlock" name="searchText[]" autocomplete="off" placeholder="<?php echo $lang['entr_srch_txt_hr']; ?>" title="<?= $lang['Search'] ?>"> 

                                                            </div>
                                                            <?php
                                                            if ($j == 0) {
                                                                ?>  

                                                                <button type="submit" class="btn btn-primary" id="search" onclick="functionHide();"><i class="fa fa-search" title="<?= $lang['Search'] ?>"></i></button>
                                                                <a href="javascript:void(0)" class="btn btn-primary" id="addfields"><i class="fa fa-plus" title="<?= $lang['Add_more'] ?>"></i></a>
                                                            <?php } else { ?>

                                                                <div onclick="incrementCount()"> <a href="javascript:void(0)" class="btn btn-primary " id="<?= $j; ?>" onclick="invisible(this.id)" title="<?= $lang['Remove'] ?>"><i class='fa fa-minus-circle' aria-hidden='true'></i></a></div>

                                                            <?php } ?>
                                                        </div>
                                                    <?php }
                                                    ?>
                                                </div>
                                                <div class="contents col-lg-12"></div>
                                            <?php } else { ?>
                                                <div id="metadata_div">
                                                    <div class="form-group row" id="multiselect">

                                                        <div class="col-md-3">
                                                            <div id="metajax">
                                                                <select  class="form-control select2" data-live-search="true" onchange="metavaluechange(this, '<?php echo $j; ?>');" id="kk" name="metadata[]" required>
                                                                    <option selected value=""><?php echo $lang['Select_Metadata']; ?> </option>
                                                                    <option value="old_doc_name"><?php echo $lang['FileName']; ?></option>
                                                                    <option value="noofpages"><?php echo $lang['No_Of_Pages']; ?></option>
                                                                    <?php
                                                                    $metas = mysqli_query($db_con, "select distinct metadata_id from tbl_metadata_to_storagelevel where sl_id in($slids)");
                                                                    while ($metaval = mysqli_fetch_assoc($metas)) {
                                                                        $meta = mysqli_query($db_con, "select * from tbl_metadata_master WHERE id='$metaval[metadata_id]' order by field_name asc");
                                                                        $rwMeta = mysqli_fetch_assoc($meta);

                                                                        if ($rwMeta['field_name'] != 'filename') {
                                                                            echo '<option>' . $rwMeta['field_name'] . '</option>';
                                                                        }
                                                                    }
                                                                    ?>
                                                                    <?php
                                                                    if (isset($_GET['metadata']) && !empty($_GET['metadata'])) {

                                                                        echo '<option value="' . $rwMeta['field_name'] . '" selected>' . $_GET['metadata'] . '</option>';
                                                                    }
                                                                    $metadatacount = 3;
//                                                            
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <select class="form-control select2" data-live-search="true" name="cond[]" id="conditionadd" required="">
                                                                <option value="" selected><?php echo $lang['Slt_Condition']; ?></option>
                                                                <option value="Equal"><?php echo $lang['Equal']; ?></option>
                                                                <option value="Contains"><?php echo $lang['Contains']; ?></option>
                                                                <option value="Like"><?php echo $lang['Like']; ?></option>
                                                                <option value="Not Like"><?php echo $lang['Not_Like']; ?></option>
                                                                <option value="Between"><?php echo $lang['Between']; ?></option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <div class="input-group" id="mdaterange" style="display:none;">
                                                                <div class="input-daterange input-group date-range">
                                                                    <input type="text" class="form-control readonly" name="startDate[]" placeholder="<?= $lang['dd_mm_yyyy']; ?>" title="<?= $lang['dd_mm_yyyy']; ?>"/>
                                                                    <span class="input-group-addon bg-custom b-0 text-white"><?php echo $lang['to']; ?></span>
                                                                    <input type="text" class="form-control readonly" name="endDate[]" placeholder="<?= $lang['dd_mm_yyyy']; ?>" title="<?= $lang['dd_mm_yyyy']; ?>" />
                                                                </div>
                                                            </div>
                                                            <input type="text" id="metaser" autocomplete="off" class="form-control translatetext specialchaecterlock" name="searchText[]" value="<?php echo $_GET['searchText'][$i] ?>" placeholder="<?php echo $lang['entr_srch_txt_hr'] ?>">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <a href="javascript:void(0)" class="btn btn-primary" id="addfields" title="<?= $lang['Add_more'] ?>"><i class="fa fa-plus"></i></a>
                                                        </div>
                                                        <div class="col-md-12">

                                                            <div class="contents col-lg-12"></div>
                                                            <div class="col-md-2 pull-right msearch">
                                                                <button type="submit" class="btn btn-primary" id="search" title="<?= $lang['Search'] ?>"><i class="fa fa-search"></i></button>
                                                            </div>

                                                        </div>

                                                    </div>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </form>
                                    </div>
                                </div>

                                <?php
                                if ($_GET['searchText']) {
                                    $query = basename($_SERVER['REQUEST_URI']);
                                    ?>
                                    <?php
                                }
                                ?>
                            </div>
                            <?php
                            if (isset($_GET['searchText'])) {
                                $metadata = $_GET['metadata'];
                                $cond = $_GET['cond'];
                                $searchText = $_GET['searchText'];
                                $slid = urldecode(base64_decode($_GET['id']));
                                $selectedStorage = preg_replace('/[^0-9]/', '', $slid);
                                $limit = $_GET['limit'];
                                $start = $_GET['start'];
                                $queryString = $_SERVER["QUERY_STRING"];
                                $sharefeatureenable = $rwdocshare['docshare_enable_disable'];
                                $searchText = xss_clean($searchText);
                                $res = searchAllDB($searchText, $cond, $metadata, $selectedStorage, $db_con, $rwgetRole, $limit, $start, $queryString, $lang, $slids, $sharefeatureenable, $expiryEnableDisable, $retentioEnableDisable, $archivedDoc);
                            }
                            ?>

                        </div>
                    </div>
                    <!-- end: page -->

                </div> <!-- end Panel -->

            </div> <!-- container -->

        </div> <!-- content -->


        <div id="savequery" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog "> 
                <div class="panel panel-color panel-danger"> 
                    <div class="panel-heading"> 
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                        <h2 class="panel-title"><?php echo $lang['r_u_wnt_to_sv_qry']; ?></h2> 
                    </div>
                    <form method="post">
                        <div class="panel-body" >
                            <?php
                            if (isset($_GET['searchText'])) {
                                for ($i = 0; $i < count($_GET['searchText']); $i++) {
                                    $text = mysqli_real_escape_string($db_con, $_GET['searchText'][$i]);
                                    $cond = mysqli_real_escape_string($db_con, $_GET['cond'][$i]);
                                    $metadata = mysqli_real_escape_string($db_con, $_GET['metadata'][$i]);
                                    ?>
                                    <input type="hidden" name="metadata[]" id="metadata" value="<?php echo $metadata; ?>">
                                    <input type="hidden" name="cond[]" id="cond" value="<?php echo $cond; ?>">
                                    <input type="hidden" name="query[]" id="query" value="<?php echo $text; ?>">
                                    <?php
                                }
                            }
                            ?>
                            <label><?php echo $lang['Query']; ?><span class="text-danger">*</span></label>
                            <input type="text" id="qry_name" name="qry_name" class="form-control specialchaecterlock" value="" placeholder="<?= $lang['eyqh'] ?>" required>
                            <input type="hidden" name="url" id="url" value="<?php echo $query; ?>">
                        </div> 
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                            <input type="submit" class="btn btn-primary " name="savqry" value="<?php echo $lang['Sve_Qry']; ?>">

                        </div>
                    </form>

                </div> 
            </div>
        </div>
        <?php require_once './application/pages/footer.php'; ?>

        <?php require_once './application/pages/footerForjs.php'; ?>

        <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>
        <!--for filter calender-->
        <script src="assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>
        <script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
        <script src="assets/plugins/moment/moment.js"></script>
        <script src="assets/plugins/timepicker/bootstrap-timepicker.js"></script>
        <script src="assets/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
        <?php require_once 'file-action-js.php'; ?>
        <?php //require_once 'file-action-html.php';  ?>
        <script>
                                                                $(document).ready(function () {
                                                                    $(".readonly").keydown(function (e) {
                                                                        e.preventDefault();
                                                                    });
                                                                    jQuery('.date-range').datepicker({
                                                                        toggleActive: true
                                                                    });
                                                                });
                                                                function invisible(myid)
                                                                {
                                                                    $(".numid-" + myid).remove();
                                                                    $("#addfields").show();
                                                                    x--;
                                                                }
                                                                function incrementCount()
                                                                {
                                                                    var max_fields = $('#kk').find("option").length;
                                                                    //alert(max_fields);
                                                                    max_fields = max_fields + 1;
                                                                    //alert(max_fields);
                                                                }
                                                                var x = <?php echo (isset($_GET['searchText']) && !empty($_GET['searchText']) ? count($_GET['searchText']) : 1) ?>; //initlal text box count
                                                                $(document).ready(function () {

                                                                    var wrapper = $(".contents"); //Fields wrapper
                                                                    var add_button = $("#addfields"); //Add button ID


                                                                    $(add_button).click(function (e) { //on add input button click
                                                                        //var max_fields = $('#kk option').length;
                                                                        var max_fields = $('#kk').find("option").length;
                                                                        //alert($('#kk').find("option").length);  
                                                                        max_fields = parseInt(max_fields) - 1;
                                                                        //alert(max_fields);
                                                                        var id = $("#parent").val();
                                                                        e.preventDefault();
                                                                        var arr = $('input[name="searchText[]"]').map(function () {
                                                                            return $(this).val()
                                                                        }).get();
                                                                        var hasAnyEmptyElement = arr.includes("");
                                                                        //alert(id);

                                                                        // if (id) {
                                                                        //if (!hasAnyEmptyElement) {
                                                                        //alert(max_fields);
                                                                        //alert(x);
                                                                        if (x < max_fields) { //max input box allowed

                                                                            //text box increment
                                                                            var count = x++;
                                                                            $.ajax({url: "application/ajax/addMultipleMeataDtaSearch?id=" + atob(id) + "&slid=<?php echo $slids; ?>" + "&ad=" + count, success: function (result) {
                                                                                    $(wrapper).append("<div class='col-md-12' style='margin-bottom:17px'>" + result + "<button class='remove_field btn btn-primary'><i class='fa fa-minus-circle' aria-hidden='true'></i></a>" + "</div>"); //add input box

                                                                                }});

                                                                        } else
                                                                        {
                                                                            alert("No more metadata available.");
                                                                            $("#addfields").hide();
                                                                        }
                                                                        // } else {
                                                                        //   alert("Please fill all empty fields.");
                                                                        // }
//                                                                } else {
//                                                                    alert("Please select storage.");
//                                                                }

                                                                    });

                                                                    $(wrapper).on("click", ".remove_field", function (e) { //user click on remove text
                                                                        e.preventDefault();
                                                                        $(this).parent('div').remove();
                                                                        x--;
                                                                        $("#addfields").show();
                                                                    });
                                                                });
        </script>

        <script type="text/javascript">

            $("#parent").change(function () {
                var slId = $(this).val();

                $.post("application/ajax/childListWithMetaData.php", {sl_id: atob(slId), permstorage: '<?php echo $slids; ?>'}, function (result, status) {
                    if (status == 'success') {
                        //$("#multiselect").html(result);
                        x = 1;
                        //alert(x);
                        $("#metajax").html(result);
                        $("#metadata_div .form-group:nth-child(1)").nextAll(".form-group").remove();
                        $(".contents ").html('');
                        $("#addfields").show();

                    }
                });
            });


            //for delete version document
            $("#con-close-modal-history").delegate("a#deleteVersionDocument", "click", function () {
                var id = $(this).attr("data");
                //alert(id);
                $("#docidversion").val(id);
            });
            $('form').parsley();
        </script>
        <script type="text/javascript">
            $('#downloadcheckedfile').on('click', function (e) {

                var file = [];
                $(".emp_checkbox:checked").each(function () {
                    file.push($(this).data('doc-id'));
                });
                if (file.length <= 0) {
                    $("#unselectfile").show();
                    $("#filedownload").hide();
                    $("#download1").show();
                    $("#download2").hide();
                } else {
                    $("#unselectfile").hide();
                    $("#filedownload").show();
                    $("#download1").hide();
                    $("#download2").show();

                    var selected_values = file.join(",");
                    $('#totaldocId').val(selected_values);


                }
            });


            function metavaluechange(metaval, autoInc) {
                //alert(autoInc);
                var rsd = metaval.value;
                $.post("application/ajax/checKeywordDatatype.php", {FVAL: rsd, FID: autoInc}, function (result, status) {
                    if (status == 'success') {
                        var resp = result.split('~');
                        if (resp[1] != '') {
                            //alert(resp[1]);
                            if (resp[1] == 'a') {
                                $("#mdaterange").css("display", "block");
                                $("#sdate").prop('required', true);
                                $("#edate").prop('required', true);
                                $("#metaser").css("display", "none");
                                $("#metaser").removeAttr('required');
                                $("#metaser").val("");
                                //AFTER SEARCH
                                $("#daterange" + autoInc).css("display", "block");
                                $("#sdate" + autoInc).prop('required', true);
                                $("#edate" + autoInc).prop('required', true);
                                $("#textsearch" + autoInc).css("display", "none");
                                $("#textsearch" + autoInc).removeAttr('required');
                                $("#textsearch" + autoInc).val("");
                            } else if (resp[1] == 'b') {
                                $("#mdaterange").css("display", "none");
                                $("#sdate").removeAttr('required');
                                $("#edate").removeAttr('required');
                                $("#sdate").val('');
                                $("#edate").val('');
                                $("#metaser").css("display", "block");
                                $("#metaser").prop('required', true);
                                //AFTER SEARCH
                                $("#daterange" + autoInc).css("display", "none");
                                $("#sdate" + autoInc).removeAttr('required');
                                $("#edate" + autoInc).removeAttr('required');
                                $("#sdate" + autoInc).val('');
                                $("#edate" + autoInc).val('');
                                $("#textsearch" + autoInc).css("display", "block");
                                $("#textsearch" + autoInc).prop('required', true);
                            }
                        }

                        if (resp[2] == '') {
                            jQuery('#date-range').datepicker({
                                toggleActive: true
                            });
                        } else {
                            jQuery('#date-range' + resp[2]).datepicker({
                                toggleActive: true
                            });
                        }
                    }
                });

            }

        </script>


        <?php

        function searchAllDB($search, $cond, $metadata, $selectedStorage, $db_con, $rwgetRole, $limit, $start, $queryString, $lang, $slids, $sharefeatureenable, $expiryEnableDisable, $retentioEnableDisable, $archivedDoc) {
            if (isset($archivedDoc) && !empty($archivedDoc)) {
                $archivedDoc = " or flag_multidelete='$archivedDoc'";
            }
            $table = "tbl_document_master";
            mysqli_set_charset($db_con, 'utf8');
            if (!empty($selectedStorage)) {
                $sql_search = "select * from " . $table . " where doc_name='$selectedStorage' and (flag_multidelete=1" . $archivedDoc . ")";
            } else {
                $sql_search = "select * from " . $table . " where doc_name in($slids) and (flag_multidelete=1" . $archivedDoc . ")";
            }
            $sql_search_fields = Array();
            for ($i = 0; $i < count($_GET['searchText']); $i++) {
                $searchcontent = $_GET['searchText'][$i];
                $search = explode(",", $searchcontent);
                $textsearch = array();
                foreach ($search as $searchvalue) {
                    $textsearch[] = trim($searchvalue);
                }
                $likearray = [];
                if (preg_replace("/[^A-Za-z0-9._ ]/", "", $_GET['cond'][$i]) == 'Like' || preg_replace("/[^A-Za-z0-9._ ]/", "", $_GET['cond'][$i]) == 'Contains') {
                    foreach ($textsearch as $searchcontent) {
                        $searchcontent = str_replace(' ', '%', $searchcontent);
                        $likearray[] = 'CONVERT(`' . preg_replace("/[^A-Za-z0-9_ ]/", "", $_GET['metadata'][$i]) . "` USING utf8) like('%" . $searchcontent . "%')";
                    }
                    $sql_search_fields[] = '(' . implode(" OR ", $likearray) . ')';
                    // $sql_search_fields[] = 'CONVERT(`' . preg_replace("/[^A-Za-z0-9._ ]/", "", $_GET['metadata'][$i]) . "` USING utf8) like('%" . preg_replace("/[^A-Za-z0-9._ ]/", "", $_GET['searchText'][$i]) . "%')";
                } else if (preg_replace("/[^A-Za-z0-9_ ]/", "", $_GET['cond'][$i]) == 'Not Like') {
                    foreach ($textsearch as $searchcontent) {
                        $likearray[] = 'CONVERT(`' . preg_replace("/[^A-Za-z0-9_ ]/", "", $_GET['metadata'][$i]) . "` USING utf8) NOT LIKE ('%" . $searchcontent . "%')";
                    }
                    $sql_search_fields[] = '(' . implode(" and ", $likearray) . ')';
                    //$sql_search_fields[] = 'CONVERT(`' . preg_replace("/[^A-Za-z0-9_ ]/", "", $_GET['metadata'][$i]) . "` USING utf8) not like('%" . preg_replace("/[^A-Za-z0-9_. ]/", "", $_GET['searchText'][$i]) . "%')";
                } else if (preg_replace("/[^A-Za-z0-9_ ]/", "", $_GET['cond'][$i]) == 'Equal') {
                    $searchdata = $_GET['searchText'][$i];
                    $searchdata = explode(",", $searchdata);
                    $searcharray = array();
                    foreach ($searchdata as $searchText) {
                        $searcharray[] = trim($searchText);
                    }
                    $searchdata = "'" . implode("', '", $searcharray) . "'";
                    $sql_search_fields[] = "`" . preg_replace("/[^A-Za-z0-9,_.()!@#$&%-]/", "", $_GET['metadata'][$i]) . "` IN(" . $searchdata . ")";
                } else if (preg_replace("/[^A-Za-z0-9_ ]/", "", $_GET['cond'][$i]) == 'Between') {
                    $startdate = date('d-m-Y', strtotime($_GET['startDate'][$i]));
                    $enddate = date('d-m-Y', strtotime($_GET['endDate'][$i]));
                    $sql_search_fields[] = "`" . xss_clean(trim($_GET['metadata'][$i])) . "` BETWEEN '" . xss_clean(trim($startdate)) . "' AND '" . xss_clean(trim($enddate)) . "'";
                }
            }

            $sql_search .= ' and (';
            $sql_search .= implode(" and ", $sql_search_fields);
            $sql_search .= ')';
            $totalrowSql = $sql_search;
            $foundnumQuery = mysqli_query($db_con, $totalrowSql);
            $foundnum = mysqli_num_rows($foundnumQuery);
            if ($foundnum > 0) {
                if (is_numeric($limit)) {
                    $per_page = $limit;
                } else {
                    $per_page = 10;
                    $limit = 10;
                }
                $start = isset($start) ? ($start > 0) ? $start : 0 : 0;
                $max_pages = ceil($foundnum / $per_page);
                if (!$start) {
                    $start = 0;
                }

                $sql_search .= " limit $start,$per_page";
                $rs3 = mysqli_query($db_con, $sql_search);
                while ($row = mysqli_fetch_array($rs3, MYSQLI_ASSOC)) {
                    $storeArray[] = $row;
                }

                //ab@100421 alphanumeric sorting
                $doc_column = array_column($storeArray, 'old_doc_name');
                array_multisort($doc_column, SORT_NATURAL, $storeArray);
                if ($_GET['stype'] == '2') {
                    $storeArray = array_reverse($storeArray);
                }
                ?>

                <div class="container">
                    <div class="pull-right record">
                        <label><?php echo $start + 1 ?> <?php echo $lang['To'] ?> <?php
                            if (($start + $per_page) > $foundnum) {
                                echo $foundnum;
                            } else {
                                echo ($start + $per_page);
                            }
                            ?> <span><?php echo $lang['ttl_recrds']; ?> : <?php echo $foundnum; ?></span></label>
                    </div>
                    <div class="box-body limit">

                        <?php echo $lang['Show']; ?>
                        <select id="limit" class="input-sm">
                            <option value="10" <?php
                            if ($_GET['limit'] == 10) {
                                echo 'selected';
                            }
                            ?>>10</option>
                            <option value="25" <?php
                            if ($_GET['limit'] == 25) {
                                echo 'selected';
                            }
                            ?>>25</option>
                            <option value="50" <?php
                            if ($_GET['limit'] == 50) {
                                echo 'selected';
                            }
                            ?>>50</option>
                            <option value="250" <?php
                            if ($_GET['limit'] == 250) {
                                echo 'selected';
                            }
                            ?>>250</option>
                            <option value="500" <?php
                            if ($_GET['limit'] == 500) {
                                echo 'selected';
                            }
                            ?>>500</option>
                        </select> <?php echo $lang['Documents']; ?>
                    </div>
                    <table class="table table-striped table-bordered m-b-50">
                        <thead>
                            <tr>
                                <th><div class="checkbox checkbox-primary"><input  type="checkbox" class="checkbox-primary" id="select_all"> <label for="checkbox6"> <strong><?php echo $lang['All']; ?></strong></label></div>  </th>
                                <th class="js-sort-none" id="name_col"><?php echo $lang['File_Name']; ?>  <i <?php if ($_GET['stype'] == '2') { ?> class="fa fa-sort" <?php } else { ?>  class="fa fa-sort" <?php } ?> id="nmae_car"></i></th>
                                <th><?php echo $lang['File_Size']; ?></th>
                                <th><?php echo $lang['No_of_Pages']; ?></th>
                                <?php if ($rwgetRole['add_loc'] == 1 || $rwgetRole['edit_loc'] == 1 || $rwgetRole['view_loc'] == 1) { ?> <th><?php echo $lang['File_Location']; ?></th>  <?php } ?>
                                <th><?php echo $lang['Actions']; ?></th>
                            </tr></thead>
                        <?php
                        $rs3 = mysqli_query($db_con, $sql_search);
                        if (mysqli_num_rows($rs3) > 0) {
                            echo'<tbody>';
                            $j = $start + 1;
                            //while ($rw = mysqli_fetch_assoc($rs3)) {
                            foreach ($storeArray as $rw) {
                                if ($rwgetRole['doc_weeding_out'] == '1' && $retentioEnableDisable == '1') {
                                    if (isset($rw['retention_period']) && !empty($rw['retention_period'])) {
                                        $wedDate = $rw['retention_period'];
                                        $weedDate = strtotime($wedDate);
                                        $todate = strtotime(date("Y-m-d H:i:s"));
                                        if ($todate >= ($weedDate - 30 * 24 * 60 * 60)) {
                                            //if ($weedDate <= ($todate)) {
                                            $weed = '#FFAAAA';
                                            $weedTile = $lang['retention_time_msg'] . ' : ' . date('d-m-Y H:i', $weedDate);
                                        }
                                    } else {
                                        $weed = '';
                                        $weedTile = '';
                                    }
                                }
                                $expiryTitle = '';
                                if ($rwgetRole['doc_expiry_time'] == '1' && $expiryEnableDisable == '1') {
                                    if (isset($rw['doc_expiry_period']) && !empty($rw['doc_expiry_period'])) {
                                        $docexpDate = $rw['doc_expiry_period'];
                                        $docexpDate = strtotime($docexpDate);
                                        $todaydate = strtotime(date("Y-m-d H:i:s"));
                                        if ($todaydate >= ($docexpDate - 30 * 24 * 60 * 60)) {
                                            //if ($weedDate <= ($todate)) {
                                            $docexpcolor = '#f5ca7f';
                                            $expiryTitle = $lang['expiry_time_msg'] . ' : ' . date('d-m-Y H:i', $docexpDate);
                                        }
                                    } else {
                                        $docexpcolor = '';
                                        $expiryTitle = '';
                                    }
                                }
                                if ($rw['checkin_checkout'] == 0) {
                                    $checkoutcolor = '#b7f1a3';
                                    $checkoutTitle = 'File is checkout!';
                                } else {
                                    $checkoutcolor = '';
                                    $checkoutTitle = '';
                                }
                                $docExpRetentionPeriod = "#a6ecf7";
                                $docExpRetentionPrdtitle = $expiryTitle . ' ' . $lang['and'] . ' ' . $weedTile;
                                $shareDid = mysqli_query($db_con, "select doc_ids from tbl_document_share where doc_ids= '$rw[doc_id]'") or die("Error: " . mysqli_error($db_con));
                                $shreCount = mysqli_num_rows($shareDid);
                                $subscribeid = mysqli_query($db_con, "select id from tbl_document_subscriber where subscribe_docid= '$rw[doc_id]'") or die("Error: " . mysqli_error($db_con));
                                $subsCountId = mysqli_num_rows($subscribeid);
                                $isLinkFile = mysqli_query($db_con, "select doc_id from tbl_document_master where parent_doc_id='$rw[doc_id]'");
                                ?>
                                <?php if (!empty($checkoutcolor) && !empty($checkoutTitle)) { ?>
                                    <tr class="gradeX" style="background-color: <?php echo $checkoutcolor; ?> !important; color:#000;" data-toggle="tooltip" title="<?php echo $checkoutTitle; ?>">         
                                    <?php } else if ((!empty($weed) && !empty($weedTile)) && (!empty($docexpcolor) && !empty($expiryTitle))) { ?>
                                    <tr class="gradeX" style="background-color: <?php echo $docExpRetentionPeriod; ?> !important; color:#000;" data-toggle="tooltip" title="<?php echo $docExpRetentionPrdtitle; ?>">
                                    <?php } else if (!empty($docexpcolor) && !empty($expiryTitle)) { ?>
                                    <tr class="gradeX" style="background-color: <?php echo $docexpcolor; ?> !important; color:#000;" data-toggle="tooltip" title="<?php echo $expiryTitle; ?>">         
                                    <?php } else if (!empty($weed) && !empty($weedTile)) { ?>
                                    <tr class="gradeX" style="background-color: <?php echo $weed; ?> !important; color:#000;" data-toggle="tooltip" title="<?php echo $weedTile; ?>">         
                                    <?php } else { ?>

                                    <tr class="gradeX">           
                                    <?php } ?>
                                    <td> 
                                        <div class="checkbox checkbox-primary m-r-15"> <input  type="checkbox" class="checkbox-primary emp_checkbox" data-doc-id="<?php echo $rw['doc_id']; ?>" id="shreId"> <label for="checkbox6"> <?= $j . '.'; ?> </label></div>
                                        <?php
                                        if ($shreCount > 0) {
                                            ?>
                                            <span class="md md-share" style="font-size: 15px; color: #193860;" title="Shared document"></span>
                                        <?php } ?>
                                        <?php
                                        if ($subsCountId > 0) {
                                            ?>
                                            <span class="fa fa-bell-o" style="font-size: 15px; color: #193860;" title="Subscribe document"></span>
                                        <?php } ?>
                                    </td>

                                    <td>
                                        <?php if (file_exists('thumbnail/' . base64_encode($rw['doc_id']) . '.jpg')) { ?>
                                            <div> <img class="thumb-image" src="thumbnail/<?= base64_encode($rw['doc_id']) ?>.jpg"> </div>
                                            <?php
                                        } echo $rw['old_doc_name'];
                                        if (mysqli_num_rows($isLinkFile) > 0) {
                                            ?>
                                            <br>
                                            <a href="javascript:void(0)"  data-toggle="modal" data-target="#linkedfiles" onclick="return getLinkedFiles(<?php echo $rw['doc_id'] ?>, <?php echo $slid; ?>);" title="Linked Document"><i class="fa fa-link"></i></a> 
                                        <?php }
                                        ?>
                                    </td>

                                    <td><?php echo formatSizeUnits($rw['doc_size']); ?></td>
                                    <td><?= $rw['noofpages']; ?></td>
                                    <?php if ($rwgetRole['add_loc'] == 1 || $rwgetRole['edit_loc'] == 1 || $rwgetRole['view_loc'] == 1) { ?> 
                                        <td>
                                            <?php
                                            $loc = mysqli_query($db_con, "select * from tbl_digital_library where doc_id='" . $rw['doc_id'] . "'");
                                            $rwLoc = mysqli_num_rows($loc);
                                            if ($rwLoc == 0) {
                                                if ($rwgetRole['add_loc'] == 1) {
                                                    echo '<a href="javascript:void(0);" data-toggle="modal" data-target="#addLoc" id="addlocbtn" data="' . $rw['doc_id'] . '" title="' . $lang['add_file_location'] . '"> <i class="fa fa-plus-square"></i></a>';
                                                }
                                            } else if ($rwLoc == 1) {
                                                if ($rwgetRole['edit_loc'] == 1) {
                                                    echo '<a href="javascript:void(0);" data-toggle="modal" data-target="#editLoc" id="editlocbtn" data="' . $rw['doc_id'] . '" title="' . $lang['edit_file_location'] . '"> <i class="fa fa-edit"></i></a>';
                                                }
                                                if ($rwgetRole['view_loc'] == 1) {
                                                    echo '<a href="library?ID=' . $rw['doc_id'] . '"  id="viewlocbtn" title = "' . $lang['View_File_Location_Library'] . '" target = "_blank"  > <i class="fa fa-eye"></i></a>';
                                                }
                                            }
                                            ?>

                                        </td>
                                    <?php } ?>
                                    <td>
                                <li class="dropdown top-menu-item-xs">
                                    <?php
                                    $checkfileLockqry = mysqli_query($db_con, "SELECT * FROM `tbl_locked_file_master` WHERE doc_id='$rw[doc_id]' and is_active='1'");
                                    if (mysqli_num_rows($checkfileLockqry) > 0) {
                                        $checkfileLock = mysqli_query($db_con, "SELECT * FROM `tbl_locked_file_master` WHERE doc_id='$rw[doc_id]' and is_locked='1' and user_id='$_SESSION[cdes_user_id]'");
                                        if (mysqli_num_rows($checkfileLock) > 0) {
                                            ?>
                                            <a href="" class="dropdown-toggle profile waves-effect waves-light" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-gear"></i></a>
                                            <ul class="dropdown-menu pdf gearbody">
                                                <li> 
                                                    <?php
                                                    if ($rw['checkin_checkout'] == 1) {
                                                        //@sk(221118): include view handler to handle different file formats
                                                        $file_row = $rw;
                                                        require 'view-handler.php';
                                                        ?>
                                                    </li>

                                                    <li>
                                                        <?php
                                                        /* ------Lock file code----- */
                                                        if ($rwgetRole['lock_file'] == '1') {
                                                            $checkfileLock = mysqli_query($db_con, "SELECT * FROM `tbl_locked_file_master` WHERE doc_id='$file_row[doc_id]' and user_id='$_SESSION[cdes_user_id]' and is_active='1'");
                                                            if (mysqli_num_rows($checkfileLock) > 0) {
                                                                $fetchdatalock = mysqli_fetch_assoc($checkfileLock);
                                                                if ($fetchdatalock['is_locked'] == "1") {
                                                                    ?>
                                                                    <a href="javascript:void(0)" class="unlock_file" data="<?php echo $file_row['doc_id']; ?>"> <i class="fa fa-unlock"  title="<?php echo $lang['unlock_file']; ?>"></i> <?php echo $lang['unlock_file']; ?></a>   
                                                                    <?php
                                                                }
                                                            } else {
                                                                ?>
                                                                <a href="javascript:void(0)" class="lock_file" data="<?php echo $file_row['doc_id'] ?>"> <i class="fa fa-lock"  title="<?php echo $lang['lock_file']; ?>"></i> <?php echo $lang['lock_file']; ?></a>   
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                    </li>
                                                    <?php if ($rwgetRole['link_document'] == '1') { ?>

                                                        <li class="isprotected"> <a href="javascript:void(0)"  data-toggle="modal" data-target="#linkedDocument" onclick="return setLinkFileDetails('<?php echo $file_row['doc_id'] ?>', '<?php echo $file_row['old_doc_name'] ?>')" ><i class="fa fa-external-link"></i> <?php echo $lang['link_document']; ?></a></li>

                                                    <?php }if ($rwgetRole['view_metadata'] == '1') { ?>
                                                        <li class="isprotected"> <a href="javascript:void(0)" data-toggle="modal" data-target="#filemeta-modal" data="metaData<?php echo $n; ?>" id="viewMeta" onclick="getFileMetaData(<?php echo $file_row['doc_id'] ?>,<?php echo $file_row['doc_name'] ?>);" ><i class="fa fa-eye"></i> <?php echo $lang['View_MetaData']; ?></a></li>
                                                        <li> <a href="javascript:void(0)"  data-toggle="modal" data-target="#con-close-modal-history" onclick="return getFileHistory(<?php echo $file_row['doc_id'] ?>,<?php echo $file_row['doc_name']; ?>)"><i class="fa fa-history"></i> <?php echo $lang['history']; ?></a></li>

                                                    <?php } if ($rwgetRole['file_review'] == '1') { ?>
                                                        <?php if ((strtolower($file_row['doc_extn']) == 'pdf' || strtolower($file_row['doc_extn']) == 'doc' || strtolower($file_row['doc_extn']) == 'docx')) { ?>
                                                            <li> <a href="reviewers?i=<?php echo urlencode(base64_encode($_SESSION['cdes_user_id'])) ?>&doc_Id=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>" id="moveTorw" data="<?php echo $file_row['doc_id']; ?>"> <i class="fa fa fa-send"></i> <?php echo $lang['Review']; ?></a></li><?php } ?>

                                                    <?php } if ($rwgetRole['workflow_initiate_file'] == '1' || $rwgetRole['initiate_file'] == '1') { ?>
                                                        <li> <a href="javascript:void(0)" data-toggle="modal" data-target="#assign-workflow" id="moveToWf" data="<?php echo $file_row['doc_id']; ?>"> <i class="fa fa-plus"></i> <?php echo $lang['Workflow']; ?></a></li>
                                                    <?php } if (strtolower($file_row['doc_extn']) == 'pdf' && $rwgetRole['pdf_annotation']) { ?>
                                                        <li> <a href="anott/add-delete-page?id1=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>&pn=1"  target="_blank" > <i class="fa fa-plus"></i> <?php echo $lang['add_delete_pages']; ?></a></li>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['checkin_checkout'] == '1') { ?>
                                                        <li><a href="javascript:void(0)" id="checkout" data="<?php echo $file_row['doc_id']; ?>"><i class="fa fa-sign-out"></i> <?php echo $lang['Chk_Out']; ?></a></li>
                                                        <?php
                                                    }if ($rwgetRole['file_delete'] == '1') {
                                                        ?>
                                                        <li><a href="javascript:void(0)" data-toggle="modal" data-target="#con-close-modal2" id="removeRow" data="<?php echo $file_row['doc_id']; ?>"><i class="fa fa-trash"></i> <?php echo $lang['Delete']; ?> </a></li>
                                                        <?php
                                                    }
                                                } else {
                                                    $file_row = $rw;
                                                    require 'checkout-action.php';
                                                }
                                                ?>
                                            </ul>
                                        <?php } else {
                                            ?>
                                            <a href="javascript:void(0)" id="" data="<?php echo $rw['doc_id'] ?>" class="dropdown-toggle profile waves-effect waves-light send_lock_request" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-lock" title="<?php echo $lang['lock_file']; ?>"></i></a>

                                            <?php
                                        }
                                    } else {
                                        ?>
                                        <a href="" class="dropdown-toggle profile waves-effect waves-light" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-gear"></i></a>
                                        <ul class="dropdown-menu pdf gearbody">
                                            <li> 
                                                <?php
                                                if ($rw['checkin_checkout'] == 1) {
                                                    //@sk(221118): include view handler to handle different file formats
                                                    $file_row = $rw;
                                                    require 'view-handler.php';
                                                    ?>
                                                </li>

                                                <li class="isprotected">
                                                    <?php
                                                    /* ------Lock file code----- */
                                                    if ($rwgetRole['lock_file'] == '1') {
                                                        $checkfileLock = mysqli_query($db_con, "SELECT * FROM `tbl_locked_file_master` WHERE doc_id='$file_row[doc_id]' and user_id='$_SESSION[cdes_user_id]' and is_active='1'");
                                                        if (mysqli_num_rows($checkfileLock) > 0) {
                                                            $fetchdatalock = mysqli_fetch_assoc($checkfileLock);
                                                            if ($fetchdatalock['is_locked'] == "1") {
                                                                ?>
                                                                <a href="javascript:void(0)" class ="unlock_file" data="<?php echo $file_row['doc_id']; ?>"> <i class="fa fa-unlock"  title="<?php echo $lang['unlock_file']; ?>"></i> <?php echo $lang['unlock_file']; ?></a>   
                                                                <?php
                                                            }
                                                        } else {
                                                            ?>
                                                            <a href="javascript:void(0)" class ="lock_file" data="<?php echo $file_row['doc_id'] ?>"> <i class="fa fa-lock"  title="<?php echo $lang['lock_file']; ?>"></i> <?php echo $lang['lock_file']; ?></a>   
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </li>
                                                <?php if ($rwgetRole['link_document'] == '1') { ?>

                                                    <li> <a href="javascript:void(0)"  data-toggle="modal" data-target="#linkedDocument" onclick="return setLinkFileDetails('<?php echo $file_row['doc_id'] ?>', '<?php echo $file_row['old_doc_name']; ?>')" ><i class="fa fa-external-link"></i> <?php echo $lang['link_document']; ?></a></li>

                                                <?php }if ($rwgetRole['view_metadata'] == '1') { ?>
                                                    <li class="isprotected"> <a href="javascript:void(0)" data-toggle="modal" data-target="#filemeta-modal" data="metaData<?php echo $n; ?>" id="viewMeta" onclick="getFileMetaData(<?php echo $file_row['doc_id'] ?>,<?php echo $file_row['doc_name']; ?>);" ><i class="fa fa-eye"></i> <?php echo $lang['View_MetaData']; ?></a></li>
                                                    <li class="isprotected"> <a href="javascript:void(0)"  data-toggle="modal" data-target="#con-close-modal-history" onclick="return getFileHistory(<?php echo $file_row['doc_id'] ?>,<?php echo $file_row['doc_name']; ?>)"><i class="fa fa-history"></i> <?php echo $lang['history']; ?></a></li>

                                                <?php } if ($rwgetRole['file_review'] == '1') { ?>
                                                    <?php if ((strtolower($file_row['doc_extn']) == 'pdf' || strtolower($file_row['doc_extn']) == 'doc' || strtolower($file_row['doc_extn']) == 'docx')) { ?>
                                                        <li class="isprotected"> <a href="reviewers?i=<?php echo urlencode(base64_encode($_SESSION['cdes_user_id'])) ?>&doc_Id=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>" id="moveTorw" data="<?php echo $file_row['doc_id']; ?>"> <i class="fa fa fa-send"></i> <?php echo $lang['Review']; ?></a></li><?php } ?>

                                                <?php } if ($rwgetRole['workflow_initiate_file'] == '1' || $rwgetRole['initiate_file'] == '1') { ?>
                                                    <li class="isprotected"> <a href="javascript:void(0)" data-toggle="modal" data-target="#assign-workflow" id="moveToWf" data="<?php echo $file_row['doc_id']; ?>"> <i class="fa fa-plus"></i> <?php echo $lang['Workflow']; ?></a></li>
                                                <?php } if (strtolower($file_row['doc_extn']) == 'pdf' && $rwgetRole['pdf_annotation']) { ?>
                                                    <li class="isprotected"> <a href="anott/add-delete-page?id1=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>&pn=1"  target="_blank" > <i class="fa fa-plus"></i> <?php echo $lang['add_delete_pages']; ?></a></li>
                                                <?php } ?>
                                                <?php if ($rwgetRole['checkin_checkout'] == '1') { ?>
                                                    <li class="isprotected"><a href="javascript:void(0)" id="checkout" data="<?php echo $file_row['doc_id']; ?>"><i class="fa fa-sign-out"></i> <?php echo $lang['Chk_Out']; ?></a></li>
                                                    <?php
                                                }
                                                if ($rwgetRole['subscribe_document'] == '1') {
                                                    ?>
                                                    <li class="isprotected"><a href="javascript:void(0)" id="singlesubscribe" data-toggle="modal" data-target="#subscribe" data="<?php echo $file_row['doc_id']; ?>"><i class="fa fa-bell-o"></i> <?php echo $lang['subscribe']; ?></a></li>
                                                    <?php
                                                }if ($rwgetRole['file_delete'] == '1') {
                                                    ?>
                                                    <li class="isprotected"><a href="javascript:void(0)" data-toggle="modal" data-target="#con-close-modal2" id="removeRow" data="<?php echo $file_row['doc_id']; ?>"><i class="fa fa-trash"></i> <?php echo $lang['Delete']; ?> </a></li>
                                                    <?php
                                                }
                                            } else {
                                                $file_row = $rw;
                                                require 'checkout-action.php';
                                            }
                                            ?>
                                        </ul>
                                    <?php } ?>
                                </li>
                                </td>
                                <tr>
                                    <td colspan="6">
                                        <div id="metaData<?php echo $j; ?>"  class="metadata">
                                            <?php
                                            $getMetaId = mysqli_query($db_con, "select * from tbl_metadata_to_storagelevel where sl_id = '$file_row[doc_name]'") or die('Error:gg' . mysqli_error($db_con));

                                            while ($rwgetMetaId = mysqli_fetch_assoc($getMetaId)) {

                                                $getMetaName = mysqli_query($db_con, "select * from tbl_metadata_master where id = '$rwgetMetaId[metadata_id]'") or die('Error:' . mysqli_error($db_con));

                                                while ($rwgetMetaName = mysqli_fetch_assoc($getMetaName)) {
                                                    $meta = mysqli_query($db_con, "select `$rwgetMetaName[field_name]` from tbl_document_master where doc_id='$file_row[doc_id]'");
                                                    $rwMeta = mysqli_fetch_assoc($meta);

                                                    if (!empty($rwgetMetaName['field_name'])) {
                                                        if ($rwgetMetaName['field_name'] == 'noofpages' || $rwgetMetaName['field_name'] == 'filename') {
                                                            
                                                        } else {
                                                            echo "<label>" . $rwgetMetaName['field_name'] . "</label> : ";
                                                            if ($rwgetMetaName['data_type'] == 'datetime') {
                                                                if (!empty($rwMeta[$rwgetMetaName['field_name']])) {
                                                                    echo date('d-m-Y H:i', strtotime($rwMeta[$rwgetMetaName['field_name']]));
                                                                }
                                                            } else {
                                                                echo $rwMeta[$rwgetMetaName['field_name']];
                                                            }
                                                            echo " | ";
                                                        }
                                                    }
                                                }
                                            }
                                            ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                                echo'</tr>';
                                $j++;
                            }
                            echo '</tbody>';
                            mysqli_close($rs3);
                        }
                        ?>
                        <tr>

                            <td colspan="6" id="isprotected">
                                <?php if ($rwFolder['is_protected'] == 0 || $_SESSION['pass'] == $rwFolder['password']) { ?>
                                    <ul class="delete_export">
                                        <input type="hidden" name="slid" id="slid" value="<?php echo $selectedStorage; ?>">
                                        <input type="hidden" name="sty" id="sty" value="<?php echo $selectedStorage; ?>">
                                        <?php if ($rwgetRole['file_delete'] == '1') { ?>
                                            <li><button id="del_file" class="rows_selected btn btn-danger btn-sm" data-toggle="modal"  data-target="#del_send_to_recycle"><i data-toggle="tooltip" title="<?php echo $lang['Delete_files'] ?>" class="fa fa-trash-o"></i></button></li>
                                        <?php } if (($rwgetRole['export_csv'] == '1') && !empty($selectedStorage)) { ?>
                                            <li><button class="btn btn-primary btn-sm" id="export4"  data-toggle="modal"  data-target="#multi-csv-export-model"><i data-toggle="tooltip" title="<?php echo $lang['Export_Data'] ?>" class="fa fa-download"></i></button></li>
                                        <?php } if (($rwgetRole['move_file'] == '1') && !empty($selectedStorage)) { ?>
                                            <li><button id="move_multi" class="rows_selected btn btn-primary btn-sm" data-toggle="modal" data-target="#move-selected-files" > <i data-toggle="tooltip" title="<?php echo $lang['Mve_fles'] ?>" class="fa fa-share-square"></i></button></li>
                                        <?php } if (($rwgetRole['copy_file'] == '1') && !empty($selectedStorage)) { ?>
                                            <li><button class="rows_selected btn btn-primary btn-sm" id="copyFiles" data-toggle="modal" data-target="#copy-selected-files" ><i data-toggle="tooltip" title="<?php echo $lang['Copy_files'] ?>" class="fa fa-copy"></i></button></li>
                                        <?php } if ($rwgetRole['share_file'] == '1' && $sharefeatureenable == '1') { ?>
                                            <li><button class="rows_selected btn btn-primary btn-sm" id="shareFiles" data-toggle="modal" data-target="#share-selected-files"><i data-toggle="tooltip" title="<?php echo $lang['Share_files']; ?>" class="fa fa-share-alt"></i></button></li>
                                        <?php } if ($rwgetRole['mail_files'] == '1') { ?>
                                            <li><button class="rows_selected btn btn-primary btn-sm" id="mailFiles" data-toggle="modal" data-target="#mail-selected-files"><i data-toggle="tooltip" title="<?php echo $lang['mail_files']; ?>" class="fa fa-envelope-o"></i></button></li>
                                        <?php } if ($rwgetRole['pdf_download'] == '1') { ?>
                                            <li><button class="rows_selected btn btn-primary btn-sm" id="downloadcheckedfile" data-toggle="modal" data-target="#downloadfile"><i class="ti-import" data-toggle="tooltip" title="<?php echo $lang['download_selected_file']; ?>"></i></button></li>
                                        <?php } if ($rwgetRole['subscribe_document'] == '1') { ?>
                                            <li><button class="rows_selected btn btn-primary btn-sm" id="subscribecheckedfile" data-toggle="modal" data-target="#subscribefile"><i class="fa fa-bell-o" data-toggle="tooltip" title="<?php echo $lang['subscribe_selected_file']; ?>"></i></button></li>
                                        <?php } ?>
                                    </ul>
                                <?php } ?>
                            </td>
                        </tr>

                    </table>
                    <?php
                    //echo $subString="&limit=$limit";

                    if ($limit && $start) {
                        $subString = "&start=$start&limit=$limit";
                        $queryString = str_replace($subString, "", $queryString);
                    } elseif ($limit) {
                        $subString = "&limit=$limit";
                        $queryString = str_replace($subString, "", $queryString);
                    }
                    echo "<center>";

                    $prev = $start - $per_page;
                    $next = $start + $per_page;

                    $adjacents = 3;
                    $last = $max_pages - 1;
                    if ($max_pages > 1) {
                        if (isset($_GET['stype']) and $_GET['stype'] != '') {
                            $stype = "&stype=" . $_GET['stype'];
                        } else {
                            $stype = '';
                        }
                        ?>
                        <ul class='pagination'>
                            <?php
                            //previous buttons
                            if (!($start <= 0))
                                echo " <li><a href='?$queryString&start=$prev&limit=$limit" . $stype . "'>$lang[Prev]</a> </li>";
                            else
                                echo " <li class='disabled'><a href='javascript:(0)'>$lang[Prev]</a> </li>";
                            //pages 
                            if ($max_pages < 7 + ($adjacents * 2)) {   //not enough pages to bother breaking it up
                                $i = 0;
                                for ($counter = 1; $counter <= $max_pages; $counter++) {
                                    if ($i == $start) {
                                        echo " <li class='active'><a href='?$queryString&start=$i&limit=$limit" . $stype . "'><b>$counter</b></a> </li>";
                                    } else {
                                        echo "<li><a href='?$queryString&start=$i&limit=$limit" . $stype . "'>$counter</a></li> ";
                                    }
                                    $i = $i + $per_page;
                                }
                            } elseif ($max_pages > 5 + ($adjacents * 2)) {    //enough pages to hide some
                                //close to beginning; only hide later pages
                                if (($start / $per_page) < 1 + ($adjacents * 2)) {
                                    $i = 0;
                                    for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                                        if ($i == $start) {
                                            echo " <li class='active'><a href='?$queryString&start=$i&limit=$limit" . $stype . "'><b>$counter</b></a></li> ";
                                        } else {
                                            echo "<li> <a href='?$queryString&start=$i&limit=$limit" . $stype . "'>$counter</a> </li>";
                                        }
                                        $i = $i + $per_page;
                                    }
                                }
                                //in middle; hide some front and some back
                                elseif ($max_pages - ($adjacents * 2) > ($start / $per_page) && ($start / $per_page) > ($adjacents * 2)) {
                                    echo " <li><a href='?$queryString&start=0&limit=$limit" . $stype . "'>1</a></li> ";
                                    echo "<li><a href='?$queryString&start=$per_page&limit=$limit" . $stype . "'>2</a></li>";
                                    echo "<li><a href='javascript:(0)'>...</a></li>";

                                    $i = $start;
                                    for ($counter = ($start / $per_page) + 1; $counter < ($start / $per_page) + $adjacents + 2; $counter++) {
                                        if ($i == $start) {
                                            echo " <li class='active'><a href='?$queryString&start=$i&limit=$limit" . $stype . "'><b>$counter</b></a></li> ";
                                        } else {
                                            echo " <li><a href='?$queryString&start=$i&limit=$limit" . $stype . "'>$counter</a> </li>";
                                        }
                                        $i = $i + $per_page;
                                    }
                                }
                                //close to end; only hide early pages
                                else {
                                    echo "<li> <a href='?$queryString&start=0&limit=$limit" . $stype . "'>1</a> </li>";
                                    echo "<li><a href='?$queryString&start=$per_page" . $stype . "'>2</a></li>";
                                    echo "<li><a href='javascript:(0)'>...</a></li>";

                                    $i = $start;
                                    for ($counter = ($start / $per_page) + 1; $counter <= $max_pages; $counter++) {
                                        if ($i == $start) {
                                            echo " <li class='active'><a href='?$queryString&start=$i&limit=$limit" . $stype . "'><b>$counter</b></a></li> ";
                                        } else {
                                            echo "<li> <a href='?$queryString&start=$i&limit=$limit" . $stype . "'>$counter</a></li> ";
                                        }
                                        $i = $i + $per_page;
                                    }
                                }
                            }
                            //next button
                            if (!($start >= $foundnum - $per_page))
                                echo "<li><a href='?$queryString&start=$next&limit=$limit" . $stype . "'>$lang[Next]</a></li>";
                            else
                                echo "<li class='disabled'><a href='javascript:(0)'>$lang[Next]</a></li>";
                            ?>
                        </ul>
                        <?php
                    }
                    echo "</center>";
                    ?>
                </div>

            <?php } else {
                ?>
                <div class="container">
                    <table class="table table-striped table-bordered m-b-50">
                        <thead>
                            <tr>
                                <th><?php echo $lang['Sr_No']; ?></th>
                                <th><?php echo $lang['File_Name']; ?></th>
                                <th><?php echo $lang['File_Size']; ?></th>
                                <th><?php echo $lang['No_of_Pages']; ?></th>
                                <?php if ($rwgetRole['add_loc'] == 1 || $rwgetRole['edit_loc'] == 1 || $rwgetRole['view_loc'] == 1) { ?> <th><?php echo $lang['File_Location']; ?></th>  <?php } ?>
                                <th><?php echo $lang['Actions']; ?></th>
                            </tr></thead>
                        <tr>
                            <td class="text-center" colspan="6">
                                <strong class="text-danger"><i class="ti-face-sad text-pink"></i> <?php echo $lang['Who0ps!_No_Records_Found']; ?></strong>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
            </div>
            <?php
        }
        ?>


</html>
<script src="assets/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>

<script type="text/javascript" src="assets/multi_function_script.js"></script>
<?php //require 'file-action-html.php';  ?>
<?php require 'file-movement.php'; ?>
<?php require 'file-action-php.php'; ?>
<script type="text/javascript">
                            $(".select2").selectpicker();
</script>
<?php
if (isset($_POST['savqry'], $_POST['token'])) {

    $url = mysqli_real_escape_string($db_con, $_POST['url']);
// $query_name = mysqli_real_escape_string($db_con, $_POST['qry_name']);
    $query_name = trim($_POST['qry_name']);
    $cond = implode(',', $_POST['cond']);
    $metadata = implode(',', $_POST['metadata']);
    $text = implode(',', $_POST['query']);
    mysqli_set_charset($db_con, "utf8");
    $chkquery = mysqli_query($db_con, "SELECT * FROM query WHERE url='$url'");
    if (mysqli_num_rows($chkquery) < 1) {
        mysqli_set_charset($db_con, "utf8");

        $uri_query = mysqli_query($db_con, "INSERT INTO query SET url='$url',query='$text',metadata='$metadata',cond='$cond',query_name='$query_name', sl_id='" . $selectedStorage . "', user_id='" . $_SESSION['cdes_user_id'] . "'");

        if ($uri_query) {
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Query Saved','$date','$host','Keyword search query $query_name saved.')") or die('error : ' . mysqli_error($db_con));
            echo'<script>taskSuccess("' . basename($_SERVER[REQUEST_URI]) . '","' . $lang['Query_Successfully_Saved'] . '");</script>';
        }
    } else {
        echo'<script>taskFailed("' . basename($_SERVER[REQUEST_URI]) . '","' . $lang['query_exist'] . '");</script>';
    }
}
?>
<script >
    $('document').ready(function () {

        var is_protected = "<?php echo $rwFolder['is_protected']; ?>";
        $("#selected_lock_folder").prop('disabled', true);
        $("#selected_unlock_folder").prop('disabled', true);
        $("#selected_update_fol_pass").prop('disabled', true);
        if (is_protected == 1) {
            $("#unlock_fol").show();
            $("#isprotected").hide();
            $("#update_fol_pass").show();
            $("#copy_fol").hide();
            $("#move_fol").hide();
            $("#share_fol").hide();
            $(".isprotected").hide();
            $(".isprotectedcheckout").show();
//                $("#move_multi").hide();
//                $("#copyFiles").hide();
//                $("#shareFiles").hide();
//                $("#export4").hide();
//                $("#downloadcheckedfile").hide();
//                $("#mailFiles").hide();
//                $("#del_file").hide();
        } else {
            $("#unlock_fol").hide();
            $("#lock_fol").show();
            $("#update_fol_pass").hide();
            $("#copy_fol").show();
            $("#move_fol").show();
            $("#share_fol").show();
            $(".isprotectedcheckout").hide();

        }
    });
</script>
