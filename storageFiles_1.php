<?php
error_reporting(0);
ini_set('display_errors', '1');
require_once './loginvalidate.php';
require_once './application/config/database.php';
require_once './classes/ftp.php';
$perm = mysqli_query($db_con, "select sl_id from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]' group by sl_id");
$rwPerm = mysqli_fetch_assoc($perm);
$slperm = $rwPerm['sl_id'];
?>
<!DOCTYPE html>
<html>
    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" />
    <link href="assets/plugins/multiselect/css/multi-select.css"  rel="stylesheet" type="text/css" />
    <?php
    require_once './application/pages/head.php';
    require_once './application/pages/function.php';
    //for user role
    $chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));
    $rwgetRole = mysqli_fetch_assoc($chekUsr);

    if ($rwgetRole['dashboard_mydms'] != '1') {
        header('Location: ./index');
    }
    ?>

    <?php
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $slid = base64_decode(urldecode($_GET['id']));
        $folder = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid'");
    } else {
        $folder = mysqli_query($db_con, "select * from tbl_storage_level where sl_depth_level='0'");
    }
    $rwFolder = mysqli_fetch_assoc($folder);
    $slid = $rwFolder['sl_id'];
    $parentid = $rwFolder['sl_parent_id'];
    ?>
    <link href="assets/plugins/jstree/style.css" rel="stylesheet" type="text/css" />
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
                            <?php
                            $perm = mysqli_query($db_con, "select * from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]' group by sl_id");
                            $rwPerm = mysqli_fetch_assoc($perm);
                            $slperm = $rwPerm['sl_id'];
                            $sllevel = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slperm'");
                            $rwSllevel = mysqli_fetch_assoc($sllevel);
                            $level = $rwSllevel['sl_depth_level'];
                            ?>
                            <ol class="breadcrumb">
                                <?php
                                parentLevel($slid, $db_con, $slpermIdes, $level);

                                function parentLevel($slid, $db_con, $slperm, $level) {
                                    $flag = 0;
                                    $slPermIds = explode(',', $slperm);
                                    if (in_array($slid, $slperm)) {
                                        $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid'") or die('Error' . mysqli_error($db_con));
                                        $rwParent = mysqli_fetch_assoc($parent);

                                        if ($level < $rwParent['sl_depth_level']) {
                                            parentLevel($rwParent['sl_parent_id'], $db_con, $slperm, $level);
                                        }
                                        $flag = 1;
                                    } else {
                                        $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid' and sl_parent_id='$slperm'") or die('Error' . mysqli_error($db_con));
                                        if (mysqli_num_rows($parent) > 0) {

                                            $rwParent = mysqli_fetch_assoc($parent);
                                            if ($level < $rwParent['sl_depth_level']) {
                                                parentLevel($rwParent['sl_parent_id'], $db_con, $slperm, $level);
                                            } $flag = 1;
                                            $flag = 1;
                                        } else {
                                            $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid'") or die('Error' . mysqli_error($db_con));
                                            $rwParent = mysqli_fetch_assoc($parent);
                                            $getparnt = $rwParent['sl_parent_id'];
                                            if ($level <= $rwParent['sl_depth_level']) {
                                                parentLevel($getparnt, $db_con, $slperm, $level);
                                                $flag = 1;
                                            } else {
                                                $flag = 0;
                                                //header("Location: ./storage_test?id=" . urlencode(base64_encode($slperm)));
                                            }
                                        }
                                    }
                                    if ($flag == 1) {
                                        echo '<li class="active"><a href="storage?id=' . urlencode(base64_encode($rwParent['sl_id'])) . '">' . $rwParent['sl_name'] . '</a></li>';
                                    }
                                }
                                ?>
                                <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                            </ol>
                        </div>
                        <div class="row">
                            <div class="box box-primary">
                                <div class="box-body">
                                    <div class="col-sm-3" style="overflow: auto;">
                                        <div class="card-box">
                                            <div id="basicTree">
                                                <ul>
                                                    <?php
                                                    $slpermid = $slpermIdes;
                                                    $sllevelTree = mysqli_query($db_con, "select * from tbl_storage_level where sl_id in($slpermid)");
                                                    while ($rwSllevelTree = mysqli_fetch_assoc($sllevelTree)) {
                                                        $level = $rwSllevelTree['sl_depth_level'];
                                                        $slperm = $rwSllevelTree['sl_id'];
                                                        $parentid = $rwSllevelTree['sl_parent_id'];

                                                        storageLevelS($level, $db_con, $slid, $parentid, $slperm);
                                                    }
                                                    //storageLevelS($level, $db_con, $slid, $parentid, $slperm);
                                                    ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-9">
                                        <div class="box-header with-border">
                                            <div class="col-sm-10">
                                                <form action="allstoragesearch" method="get">
                                                    <div class="col-sm-6" style="margin-left: 35%;">
                                                        <input type="text" name="searchText" placeholder="<?php echo $lang['entr_srch_txt_hr']; ?>" class="form-control" required="required" />
                                                        <input type="hidden" value="<?php echo $_GET['id']; ?>" name="id">
                                                    </div>  
                                                    <div class="col-md-1">
                                                        <a href="#" onclick="$(this).closest('form').submit()" class="btn btn-primary" title="<?php echo $lang['Search'] ?>"><i class="fa fa-search"></i></a>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="btn-group pull-right col-sm-3" style="margin-right: -29px; margin-top: -38px;">

                                                <button type="button" class="btn btn-linkedin dropdown-toggle"  data-toggle="dropdown" ><?php echo $lang['Chse_Action']; ?></button>
                                                <button type="button" class="btn btn-linkedin dropdown-toggle" data-toggle="dropdown" aria-expanded="true"><span class="caret"></span> </button>
                                                <ul class="dropdown-menu storage" role="menu">

                                                    <?php if ($rwgetRole['bulk_download'] == '1') { ?>
                                                        <li><a href="javascript:void(0)" data-toggle="modal" data-target="#bulkdownload"><?php echo $lang['Blk_Dwnld_Files']; ?></a></li>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['upload_doc_storage'] == '1') { ?>
                                                        <li><a href="adddocument?id=<?php echo urlencode(base64_encode($slid)); ?>"><?php echo $lang['Upld_Docmnt']; ?></a></li>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['create_child_storage'] == '1') { ?>
                                                        <li><a href="javascript:void(0)" data-toggle="modal" data-target="#con-close-modal1"><?php echo $lang['Crt_New_Cld']; ?></a></li>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['assign_metadata'] == '1') { ?>
                                                        <li><a href="javascript:void(0)" data-toggle="modal" data-target="#con-close-modal5"><?php echo $lang['Asgn_MetaData']; ?></a></li>
                                                    <?php } ?>

                                                    <?php if ($rwgetRole['modify_storage_level'] == '1') { ?>
                                                        <li><a href="javascript:void(0)" data-toggle="modal" data-target="#con-close-modal-modify"><?php echo $lang['Modify_Storage']; ?></a></li>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['delete_storage_level'] == '1') { ?>
                                                        <li><a href="javascript:void(0)" data-toggle="modal" data-target="#con-close-modal-del"><?php echo $lang['Dlt_Storage']; ?></a></li>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['move_storage_level'] == '1') { ?>
                                                        <li><a href="javascript:void(0)" data-toggle="modal" data-target="#con-close-modal4"><?php echo $lang['Move_Storage']; ?></a></li>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['copy_storage_level'] == '1') { ?>
                                                        <li><a href="javascript:void(0)" data-toggle="modal" data-target="#con-close-modal6"><?php echo $lang['Cpy_Storage']; ?></a></li>
                                                    <?php } ?>
                                                    <li class="divider"></li>
                                                    <?php if ($rwgetRole['export_csv'] == '1') { ?>
                                                        <li><a href="javascript:void(0)" data-toggle="modal" data-target="#export"><?php echo $lang['Export_Csv']; ?></a></li>
                                                    <?php } ?>
                                                </ul>
                                            </div> 

                                        </div>
                                        <div class="col-lg-12 m-t-10" style="padding-left: 0;">
                                            <form action="searchdata">
                                                <div class="row" id="multiselect">
                                                    <div class="col-md-3">

                                                        <select  class="form-control select2" id="my_multi_select1" name="metadata[]" required>
                                                            <option disabled selected><?php echo $lang['Select_Metadata']; ?></option>
                                                            <option value="old_doc_name"><?php echo $lang['FileName']; ?></option>
                                                            <option value="noofpages"><?php echo $lang['No_Of_Pages']; ?></option>
                                                            <?php
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
                                                    <div class="col-md-3">
                                                        <select class="form-control select2" name="cond[]" required>
                                                            <option disabled selected style="background: #808080; color: #fff;"><?php echo $lang['Slt_Condition']; ?></option>
                                                            <option <?php
                                                            if (isset($_GET['cond']) && !empty($_GET['cond']) && $_GET['cond'] == 'Equal') {
                                                                echo'selected';
                                                            }
                                                            ?> value="Equal"><?php echo $lang['Equal']; ?></option>
                                                            <option <?php
                                                            if (isset($_GET['cond']) && !empty($_GET['cond']) && $_GET['cond'] == 'Contains') {
                                                                echo'selected';
                                                            }
                                                            ?> value="Contains"><?php echo $lang['Contains']; ?></option>
                                                            <option <?php
                                                            if (isset($_GET['cond']) && !empty($_GET['cond']) && $_GET['cond'] == 'Like') {
                                                                echo'selected';
                                                            }
                                                            ?> value="Like"><?php echo $lang['Like']; ?></option>
                                                            <option <?php
                                                            if (isset($_GET['cond']) && !empty($_GET['cond']) && $_GET['cond'] == 'Not Like') {
                                                                echo'selected';
                                                            }
                                                            ?> value="Not Like"><?php echo $lang['Not_Like']; ?></option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="text" class="form-control" name="searchText[]" required value="<?php echo $_GET['searchText'] ?>" placeholder="<?php echo $lang['entr_srch_txt_hr']; ?>">
                                                    </div>
                                                    <input type="hidden" value="<?php echo $_GET['id']; ?>" name="id" />
                                                    <button type="submit" class="btn btn-primary " id="search" title="<?php echo $lang['Search'] ?>"><i class="fa fa-search"></i></button>
                                                    <a href="javascript:void(0)" class="btn btn-primary" id="addfields" title="<?php echo $lang['Add_more'] ?>"><i class="fa fa-plus"></i></a>
                                                </div>
                                                <div class="row">
                                                    <div class="contents col-lg-12"></div>
                                                </div> 
                                            </form>
                                        </div>
                                        <?php

                                        function findTotalFile($slperm) {
                                            global $list;
                                            $list = array();
                                            global $db_con;
                                            global $numFile;
                                            global $totalFSize;
                                            global $totalFolder;

                                            $contFile = mysqli_query($db_con, "select sum(doc_size) as total, count(doc_name) as count from tbl_document_master where FIND_IN_SET('$slperm',doc_name)") or die('Error :' . mysqli_error($db_con));
                                            $rwcontFile = mysqli_fetch_assoc($contFile);
                                            $totalFSize1 = $rwcontFile['total'];
                                            $totalFSize += round($totalFSize1 / (1024 * 1024), 2);
                                            $numFile += $rwcontFile['count'];
                                            $list["files"] = $numFile;
                                            $list["fileSize"] = $totalFSize;
                                            if (!empty($slperm)) {
                                                $totalFolder += 1;
                                            }
                                            $list["totalFolder"] = $totalFolder;

                                            $sql_child = "select * FROM tbl_storage_level WHERE sl_parent_id = '$slperm' ";
                                            $sql_child_run = mysqli_query($db_con, $sql_child) or die('Error: ' . mysqli_error($db_con));
                                            if (mysqli_num_rows($sql_child_run) > 0) {

                                                while ($rwchild = mysqli_fetch_assoc($sql_child_run)) {

                                                    $child = $rwchild['sl_id'];
                                                    $clagain = findTotalFile($child);
                                                }
                                            }
                                            return $list;
                                        }

                                        $totalFiles = findTotalFile($namesl['sl_id']);
                                        ?>

                                        <div class="col-md-12" style="overflow: auto">
                                            <?php
                                            $where = '';
                                            if (isset($_GET['quicksearch']) && !empty($_GET['quicksearch'])) {
                                                $user_id1 = $_SESSION[cdes_user_id];
                                                $chekUsr1 = mysqli_query($db_con, "select * from tbl_bridge_role_to_um where FIND_IN_SET('$user_id1', user_ids) > 0") or die('Error:' . mysqli_error($db_con));
                                                $rwcheckUser1 = mysqli_fetch_assoc($chekUsr1);
                                                if ($rwcheckUser1['role_id'] == 1) {
                                                    $where = "where old_doc_name LIKE '%$_GET[quicksearch]%' and doc_name = '$rwFolder[sl_id]'";
                                                } else {
                                                    $where = "where old_doc_name LIKE '%$_GET[quicksearch]%' and doc_name = '$rwFolder[sl_id]' and flag_multidelete=1";
                                                }
                                            } else {
                                                $user_id1 = $_SESSION[cdes_user_id];
                                                $chekUsr1 = mysqli_query($db_con, "select * from tbl_bridge_role_to_um where FIND_IN_SET('$user_id1', user_ids) > 0") or die('Error:' . mysqli_error($db_con));
                                                $rwcheckUser1 = mysqli_fetch_assoc($chekUsr1);
                                                if ($rwcheckUser1['role_id'] == 1) {
                                                    $where = "where doc_name = '$rwFolder[sl_id]' and flag_multidelete=1";
                                                } else {
                                                    $where = "where doc_name = '$rwFolder[sl_id]' and flag_multidelete=1";
                                                }
                                            }
                                            $constructs = "SELECT doc_id,flag_multidelete FROM tbl_document_master $where";
                                            $run = mysqli_query($db_con, $constructs) or die('Error' . mysqli_error($db_con));

                                            $foundnum = mysqli_num_rows($run);
                                            if ($foundnum > 0) {
                                                if (is_numeric($_GET['limit'])) {
                                                    $per_page = $_GET['limit'];
                                                } else {
                                                    $per_page = 10;
                                                }
                                                $start = isset($_GET['start']) ? ($_GET['start'] > 0) ? $_GET['start'] : 0 : 0;
                                                $max_pages = ceil($foundnum / $per_page);
                                                if (!$start) {
                                                    $start = 0;
                                                }

                                                $getTpages = "SELECT SUM(noofpages) as totalPages FROM(SELECT noofpages FROM tbl_document_master $where ORDER BY old_doc_name asc LIMIT $start, $per_page) tbl_document_master";

                                                $totalp = mysqli_query($db_con, $getTpages) or die("Error: " . mysqli_error($db_con));

                                                $rowT = mysqli_fetch_assoc($totalp);
                                                $rowT['totalPages'];

                                                // echo "select * from tbl_document_master $where order by old_doc_name LIMIT $start, $per_page";
                                                //die;

                                                $allot = "select * from tbl_document_master $where order by old_doc_name LIMIT $start, $per_page";

                                                $allot_query = mysqli_query($db_con, $allot) or die("Error: " . mysqli_error($db_con));
                                                ?>
                                                <div class="box box-primary">
                                                    <h4 id="event_result" class="header-title" style="display: inline-block;"><?php echo $lang['Slt_Folder']; ?> : <strong><?php echo $rwFolder['sl_name']; ?></strong></h4>

                                                    <div class="box-body">

                                                        <label><?php echo $lang['Show']; ?></label> <select id="limit" class="input-sm">
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
                                                        </select> <label><?php echo ' ' . $lang['Documents']; ?></label>
                                                        <div class="pull-right record">
                                                            <?php echo $start + 1 ?> <?php echo $lang['To']; ?> <?php
                                                            if (($start + $per_page) > $foundnum) {
                                                                echo $foundnum;
                                                            } else {
                                                                echo ($start + $per_page);
                                                            }
                                                            ?>  <span> <?php echo $lang['Ttal_Rcrds']; ?> : <?php echo $foundnum; ?> |</span>

                                                            <?php echo $lang['Total_Pages']; ?> : <?php echo $rowT['totalPages']; ?>

                                                        </div>
                                                    </div>
                                                    <table class="table table-striped table-bordered" id="table_demo_icons">
                                                        <thead>
                                                            <tr>
                                                                <th><div class="checkbox checkbox-primary"><input  type="checkbox" class="checkbox-primary" id="select_all"> <label for="checkbox6"> <strong><?php echo $lang['All']; ?></strong></label></div>  </th>
                                                                <th><?php echo $lang['File_Name']; ?></th>
                                                                <th><?php echo $lang['File_Size']; ?></th>
                                                                <th><?php echo $lang['No_of_Pages']; ?></th>
                                                                <th><?php echo $lang['Upld_By']; ?></th>
                                                                <th><?php echo $lang['Upld_Date']; ?></th>
                                                                <th><?php echo $lang['Actions']; ?></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $n = $start + 1;
                                                            while ($file_row = mysqli_fetch_assoc($allot_query)) {
                                                                $shareDid = mysqli_query($db_con, "select doc_ids from tbl_document_share where doc_ids= '$file_row[doc_id]'") or die("Error: " . mysqli_error($db_con));
                                                                $shreCount = mysqli_num_rows($shareDid);
                                                                ?>
                                                                <tr class="gradeX">
                                                                    <td> 
                                                                        <div class="checkbox checkbox-primary m-r-15"> <input  type="checkbox" class="checkbox-primary emp_checkbox" data-doc-id="<?php echo $file_row['doc_id']; ?>" id="shreId"> <label for="checkbox6"> <?php echo $n . '.'; ?> </label></div>

                                                                        <?php
                                                                        if ($shreCount > 0) {
                                                                            ?>
                                                                            <span class="fa fa-share-square-o" style="font-size: 15px; color: #193860;" title="Shared Document"></span>
                                                                        <?php } ?>
                                                                    </td>
                                                                    <td> <div style="overflow: hidden; max-width:200px;" title="<?php echo $file_row['old_doc_name']; ?>"><?php echo $file_row['old_doc_name']; ?></div></td>
                                                                    <td ><?php
                                                                        $size = round($file_row['doc_size'] / 1000 / 1000, 2);
                                                                        if ($size <= 0) {
                                                                            echo $file_row['doc_size'] / 1000;
                                                                        } else {
                                                                            echo $size;
                                                                        }
                                                                        ?> MB</td>
                                                                    <td><?php echo $file_row['noofpages']; ?></td>
                                                                    <?php
                                                                    $userName = "SELECT first_name,last_name FROM tbl_user_master WHERE user_id = '$file_row[uploaded_by]'";
                                                                    $userName_run = mysqli_query($db_con, $userName) or die("Error: " . mysqli_error($db_con));

                                                                    $rwuserName = mysqli_fetch_assoc($userName_run)
                                                                    ?>
                                                                    <td><?php echo $rwuserName['first_name'] . " " . $rwuserName['last_name']; ?></td>
                                                                    <td><?php echo $file_row['dateposted']; ?></td>
                                                                    <td>
                                                            <li class="dropdown top-menu-item-xs">
                                                                <a href="" class="dropdown-toggle profile waves-effect waves-light" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-gear" title="<?php echo $lang['view_actions']; ?>"></i></a>
                                                                <ul class="dropdown-menu pdf gearbody">
                                                                    <li> 
                                                                        <?php
                                                                        if ($file_row['checkin_checkout'] == 1) {
                                                                            require 'view-handler.php';
                                                                            ?>   
                                                                        </li>

                                                                        <?php if ($rwgetRole['file_edit'] == '1') { ?>
                                                                            <li> <a href="javascript:void(0)" data="metaData<?php echo $n; ?>" id="viewMeta"><i class="fa fa-eye"></i> <?php echo $lang['View_MetaData']; ?></a></li>
                                                                        <?php } if ($rwgetRole['file_delete'] == '1') { ?>
                                                                            <li><a href="javascript:void(0)" data-toggle="modal" data-target="#con-close-modal2" id="removeRow" data="<?php echo $file_row['doc_id']; ?>"><i class="fa fa-trash-o"></i> <?php echo $lang['Delete']; ?> </a></li>
                                                                        <?php } ?>
                                                                        <?php if ((strtolower($file_row['doc_extn']) == 'pdf' || strtolower($file_row['doc_extn']) == 'doc' || strtolower($file_row['doc_extn']) == 'docx')) { ?>
                                                                            <li> <a href="reviewers.php?i=<?php echo urlencode(base64_encode($_SESSION['cdes_user_id'])) ?>&doc_Id=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>" id="moveTorw" data="<?php echo $file_row['doc_id']; ?>"> <i class="fa fa fa-eye"></i> <?php echo $lang['Review']; ?></a></li><?php } ?>

                                                                        <?php if ($rwgetRole['workflow_initiate_file'] == '1' || $rwgetRole['initiate_file'] == '1') { ?>
                                                                            <li> <a href="javascript:void(0)" data-toggle="modal" data-target="#assign-workflow" id="moveToWf" data="<?php echo $file_row['doc_id']; ?>"> <i class="fa fa-plus"></i> <?php echo $lang['Workflow']; ?></a></li>
                                                                        <?php } ?>
                                                                        <?php if ($rwgetRole['checkin_checkout'] == '1') { ?>
                                                                            <li><a href="javascript:void(0)" id="checkout" data="<?php echo $file_row['doc_id']; ?>"><i class="fa fa-sign-out"></i> <?php echo $lang['Chk_Out']; ?></a></li>
                                                                            <?php
                                                                        }
                                                                    } else {
                                                                        ?>
                                                                        <?php if ($rwgetRole['checkin_checkout'] == '1' && $rwgetRole['pdf_file'] == '1' && $file_row['doc_extn'] == 'pdf') { ?>
                                                                            <li> <a href="viewer.php?id=<?php echo urlencode(base64_encode($_SESSION['cdes_user_id'])); ?>&i=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>" id="fancybox-inner" class="pdfview" target="_blank">
                                                                                    <i class="fa fa-sign-in"></i> <?php echo $lang['Chk_In']; ?></a>
                                                                            </li>

                                                                        <?php } else if ($rwgetRole['checkin_checkout'] == '1' && $rwgetRole['image_file'] == '1' && strtolower($file_row['doc_extn']) == 'jpg' || strtolower($file_row['doc_extn']) == 'png' || strtolower($file_row['doc_extn']) == 'gif') { ?>
                                                                            <li> <a href="imageviewer?uid=<?php echo urlencode(base64_encode($_SESSION['cdes_user_id'])); ?>&i=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>"  target="_blank">
                                                                                    <i class="fa fa-sign-in"></i> <?php echo $lang['Chk_In']; ?></a>
                                                                            </li>
                                                                        <?php } else if ($rwgetRole['checkin_checkout'] == '1' && $rwgetRole['excel_file'] == '1' && strtolower($file_row['doc_extn']) == 'xls') { ?>
                                                                            <li> 
                                                                                <a href="xls?uid=<?php echo urlencode(base64_encode($_SESSION['cdes_user_id'])); ?>&file=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>"  target="_blank">
                                                                                    <i class="fa fa-sign-in"></i> <?php echo $lang['Chk_In']; ?></a>
                                                                            </li>
                                                                        <?php } else if ($rwgetRole['checkin_checkout'] == '1' && $rwgetRole['excel_file'] == '1' && strtolower($file_row['doc_extn']) == 'xlsx') { ?>
                                                                            <li> <a href="xlsx?uid=<?php echo urlencode(base64_encode($_SESSION['cdes_user_id'])); ?>&file=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>"  target="_blank">
                                                                                    <i class="fa fa-sign-in"></i> <?php echo $lang['Chk_In']; ?></a>
                                                                            </li>
                                                                        <?php } else if ($rwgetRole['checkin_checkout'] == '1' && $rwgetRole['tif_file'] == '1' && (strtolower($file_row['doc_extn']) == 'tiff' || strtolower($file_row['doc_extn']) == 'tif')) { ?>
                                                                            <li> <a href="file?uid=<?php echo urlencode(base64_encode($_SESSION['cdes_user_id'])); ?>&file=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>"  target="_blank">
                                                                                    <i class="fa fa-sign-in"></i> <?php echo $lang['Chk_In']; ?></a>
                                                                            </li>


                                                                        <?php } else if ($rwgetRole['checkin_checkout'] == '1' && $rwgetRole['excel_file'] == '1' && (strtolower($file_row['doc_extn']) == 'doc' || strtolower($file_row['doc_extn']) == 'docx')) { ?>
                                                                            <li>  <a href="viewword?i=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&id=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>" target="_blank" class="pdfview" title="View Word File">
                                                                                    <i class="fa fa-sign-in"></i> <?php echo $lang['Chk_In']; ?></a>
                                                                            </li>

                                                                        <?php } else { ?>
                                                                            <li> <a href="javascript:void(0)" data-toggle="modal" data-target="#editmetadata" id="editMdata" data="<?php echo $file_row['doc_id']; ?>"><i class="fa fa-sign-in"></i> <?php echo $lang['Chk_In']; ?></a></li>
                                                                            <?php
                                                                        }
                                                                    }
                                                                    ?>
                                                                </ul>
                                                            </li>
                                                            </td>
                                                            </tr>
                                                            <tr>

                                                                <td colspan="20">
                                                                    <div id="metaData<?php echo $n; ?>"  class="metadata">
                                                                        <?php
                                                                        $versionView = mysqli_query($db_con, "SELECT * FROM tbl_document_master where substring_index(doc_name,'_',-1)='$file_row[doc_id]' and substring_index(doc_name,'_',1)='$slid' ") or die("Error: test" . mysqli_error($db_con));
                                                                        if (mysqli_num_rows($versionView) > 0) {

                                                                            $i = 1.0;
                                                                            while ($rwView = mysqli_fetch_assoc($versionView)) {
                                                                                if ($rwgetRole['file_version'] == '1') {
                                                                                    if ($i > 0) {

                                                                                        echo 'Version ' . $i . '-';
                                                                                    }
                                                                                    //@sk(221118): include view handler to handle different file formats
                                                                                    echo $rwView['old_doc_name'];
                                                                                    $file_row = $rwView;
                                                                                    require 'view-handler.php';
                                                                                }
                                                                                if ($rwgetRole['delete_version'] == '1') {
                                                                                    ?>
                                                                                    <a href="javascript:void(0)" data="<?php echo $rwView['doc_id']; ?>" data-toggle="modal" data-target="#deleteVersion" id="deleteVersionDoc"><i class="fa fa-trash"></i></a>
                                                                                    <?php
                                                                                }
                                                                                $i = $i + 0.1;
                                                                            }
                                                                        }
                                                                        ?>
                                                                        <?php
                                                                        $getMetaId = mysqli_query($db_con, "select * from tbl_metadata_to_storagelevel where sl_id = '$file_row[doc_name]'") or die('Error:gg' . mysqli_error($db_con));

                                                                        while ($rwgetMetaId = mysqli_fetch_assoc($getMetaId)) {

                                                                            $getMetaName = mysqli_query($db_con, "select * from tbl_metadata_master where id = '$rwgetMetaId[metadata_id]'") or die('Error:' . mysqli_error($db_con));

                                                                            while ($rwgetMetaName = mysqli_fetch_assoc($getMetaName)) {
                                                                                $meta = mysqli_query($db_con, "select `$rwgetMetaName[field_name]` from tbl_document_master where doc_id='$file_row[doc_id]'");
                                                                                $rwMeta = mysqli_fetch_assoc($meta);

                                                                                if (!empty($rwMeta[$rwgetMetaName['field_name']])) {
                                                                                    if ($rwgetMetaName['field_name'] == 'noofpages' || $rwgetMetaName['field_name'] == 'filename') {
                                                                                        
                                                                                    } else {
                                                                                        echo "<label>" . $rwgetMetaName['field_name'] . "</label> : ";
                                                                                        if ($rwMeta[$rwgetMetaName['field_name']] != '0000-00-00 00:00:00') {

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
                                                            $n++;
                                                        }
                                                        ?>
                                                        <tr>
                                                            <td colspan="7">
                                                                <ul class="delete_export">
                                                                    <input type="hidden" name="slid" id="slid" value="<?php echo $slid; ?>">
                                                                    <input type="hidden" name="sty" id="sty" value="<?php echo $_GET['id']; ?>">
                                                                    <?php if ($rwgetRole['file_delete'] == '1') { ?>
                                                                        <li><button id="del_file" class="rows_selected btn btn-danger fa fa-trash-o" data-toggle="modal" data-target="#del_send_to_recycle" title="<?php echo $lang['Delete_files'] ?>"></button></li>
                                                                    <?php } if ($rwgetRole['export_csv'] == '1') { ?>
                                                                        <li><button class="btn btn-primary fa fa-download" id="export4"  data-toggle="modal"  data-target="#multi-csv-export-model" title="<?php echo $lang['Export_Data'] ?>"></button></li>
                                                                    <?php } if ($rwgetRole['move_file'] == '1') { ?>
                                                                        <li><button id="move_multi" class="rows_selected btn btn-primary fa fa-share-square" data-toggle="modal" data-target="#move-selected-files" title="<?php echo $lang['Mve_fles'] ?>"> </button></li>
                                                                    <?php } if ($rwgetRole['copy_file'] == '1') { ?>
                                                                        <li><button class="rows_selected btn btn-primary fa fa-copy" id="copyFiles" data-toggle="modal" data-target="#copy-selected-files" title="<?php echo $lang['Copy_files'] ?>"></button></li>
                                                                    <?php } if ($rwgetRole['share_file'] == '1') { ?>
                                                                        <li><button class="rows_selected btn btn-primary fa fa-share-alt" id="shareFiles" data-toggle="modal" data-target="#share-selected-files" title="<?php echo $lang['Share_files']; ?>"></button></li>
                                                                    <?php } if ($rwgetRole['mail_files'] == '1') { ?>
                                                                        <li><button class="rows_selected btn btn-primary fa fa-envelope-o" id="mailFiles" data-toggle="modal" data-target="#mail-selected-files" title="<?php echo $lang['mail_files']; ?>"></button></li>
                                                                    <?php } ?>
                                                                </ul>
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                    <?php
                                                    echo "<center>";
                                                    $prev = $start - $per_page;
                                                    $next = $start + $per_page;
                                                    $adjacents = 3;
                                                    $last = $max_pages - 1;
                                                    if ($max_pages > 1) {
                                                        ?>

                                                        <ul class='pagination strgePage'>
                                                            <?php
                                                            //previous button
                                                            if (!($start <= 0))
                                                                echo " <li><a href='?id=$_GET[id]&start=$prev&limit=$per_page'>" . $lang['Prev'] . "</a> </li>";
                                                            else
                                                                echo " <li class='disabled'><a href='javascript:void(0)'>" . $lang['Prev'] . "</a> </li>";
                                                            //pages 
                                                            if ($max_pages < 7 + ($adjacents * 2)) {   //not enough pages to bother breaking it up
                                                                $i = 0;
                                                                for ($counter = 1; $counter <= $max_pages; $counter++) {
                                                                    if ($i == $start) {
                                                                        echo " <li class='active'><a href='?id=$_GET[id]&start=$i&limit=$per_page'><b>$counter</b></a> </li>";
                                                                    } else {
                                                                        echo "<li><a href='?id=$_GET[id]&start=$i&limit=$per_page'>$counter</a></li> ";
                                                                    }
                                                                    $i = $i + $per_page;
                                                                }
                                                            } elseif ($max_pages > 5 + ($adjacents * 2)) {    //enough pages to hide some
                                                                //close to beginning; only hide later pages
                                                                if (($start / $per_page) < 1 + ($adjacents * 2)) {
                                                                    $i = 0;
                                                                    for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                                                                        if ($i == $start) {
                                                                            echo " <li class='active'><a href='?id=$_GET[id]&start=$i&limit=$per_page'><b>$counter</b></a></li> ";
                                                                        } else {
                                                                            echo "<li> <a href='?id=$_GET[id]&start=$i&limit=$per_page'>$counter</a> </li>";
                                                                        }
                                                                        $i = $i + $per_page;
                                                                    }
                                                                }
                                                                //in middle; hide some front and some back
                                                                elseif ($max_pages - ($adjacents * 2) > ($start / $per_page) && ($start / $per_page) > ($adjacents * 2)) {
                                                                    echo " <li class='active'><a href='?id=$_GET[id]&start=0'>1</a></li> ";
                                                                    echo "<li><a href='?id=$_GET[id]&start=$per_page&limit=$per_page'>2</a></li>";
                                                                    echo "<li><a href='javascript:void(0)'>...</a></li>";

                                                                    $i = $start;
                                                                    for ($counter = ($start / $per_page) + 1; $counter < ($start / $per_page) + $adjacents + 2; $counter++) {
                                                                        if ($i == $start) {
                                                                            echo " <li class='active'><a href='?id=$_GET[id]&start=$i&limit=$per_page'><b>$counter</b></a></li> ";
                                                                        } else {
                                                                            echo " <li><a href='?id=$_GET[id]&start=$i&limit=$per_page'>$counter</a> </li>";
                                                                        }
                                                                        $i = $i + $per_page;
                                                                    }
                                                                }
                                                                //close to end; only hide early pages
                                                                else {
                                                                    echo "<li> <a href='?id=$_GET[id]&start=0'>1</a> </li>";
                                                                    echo "<li><a href='?id=$_GET[id]&start=$per_page&limit=$per_page'>2</a></li>";
                                                                    echo "<li><a href='javascript:void(0)'>...</a></li>";

                                                                    $i = $start;
                                                                    for ($counter = ($start / $per_page) + 1; $counter <= $max_pages; $counter++) {
                                                                        if ($i == $start) {
                                                                            echo " <li class='active'><a href='?id=$_GET[id]&start=$i&limit=$per_page'><b>$counter</b></a></li> ";
                                                                        } else {
                                                                            echo "<li> <a href='?id=$_GET[id]&start=$i&limit=$per_page'>$counter</a></li> ";
                                                                        }
                                                                        $i = $i + $per_page;
                                                                    }
                                                                }
                                                            }
                                                            //next button
                                                            if (!($start >= $foundnum - $per_page))
                                                                echo "<li><a href='?id=$_GET[id]&start=$next&limit=$per_page'>" . $lang['Next'] . "</a></li>";
                                                            else
                                                                echo "<li class='disabled'><a href='javascript:void(0)'>" . $lang['Next'] . "</a></li>";
                                                            ?>
                                                        </ul>
                                                        <?php
                                                    }
                                                    echo "</center>";
                                                    ?>

                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }else {

                                        echo '<div class="form-group no-records-found no-recordfiles m-b-90"><label><strong class="text-danger"><i class="ti-face-sad text-pink"></i> ' . $lang['Who0ps!_No_Records_Found'] . '</strong></label></div>';
                                    }
                                    ?>

                                </div>
                            </div>
                        </div>				
                    </div>
                </div> <!-- container -->
            </div> <!-- content -->
        </div>  
        <!-- /.modal -->
        <div id="del_send_to_recycle" class="modal fade"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog"> 
                <div class="panel panel-color panel-danger"> 
                    <div class="panel-heading"> 
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h2 class="panel-title" style="display:none;" id="hid"> <i class="fa fa-exclamation-triangle" aria-hidden="true"></i><?php echo $lang['Hre_msge']; ?></h2>
                        <h2 class="panel-title" id="confirm"><?php echo $lang['Are_u_confirm']; ?></h2> 
                    </div>
                    <form method="post">
                        <div class="panel-body">
                            <span id="errmessage" style="display:none;"> <h5 class="text-alert"><?php echo $lang['Pls_slt_fles_for_Del']; ?></h5></span>
                            <label class="text-danger" id="hide"><?php echo $lang['r_u_sue_wnt_to_Del_tis_Docs'] ?> <?php if ($rwgetRole['role_id'] == 1) { ?> <?php
                                    echo $lang['Perm'];
                                }
                                ?></label>
                        </div> 
                        <div class="modal-footer">
                            <input type="hidden" id="sl_id1" name="sl_id1">
                            <input type="hidden" id="reDel" name="DelFile">
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?= $lang['Close']; ?></button> 
                            <?php
                            if ($rwgetRole['role_id'] == 1) {
                                ?>
                                <button type="submit" id="yes" name="Delmultiple" class="btn btn-danger" value="Yes"> <i class="fa fa-trash-o"></i> <?php echo $lang['Yes']; ?></button>
                                <?php
                            }
                            ?>
                            <button type="submit" id="no" name="Delmultiple" class="btn btn-danger"> <i class="fa fa-recycle"></i>
                                <?php
                                if ($rwgetRole['role_id'] == 1) {
                                    echo $lang['Recycle'];
                                } else {
                                    echo $lang['Delete'];
                                }
                                ?>

                            </button> 
                        </div>
                    </form>

                </div> 
            </div>
        </div>

        <!-- /.modal --> 
        <!--share files with users-->
        <div id="share-selected-files" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog"> 
                <div class="panel panel-color panel-danger"> 
                    <div class="panel-heading"> 
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                        <h2 class="panel-title" id="shr"> <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <?php echo $lang['Hre_msge']; ?></h2>
                        <h2 class="panel-title" style="display:none;" id="stitle"> <?php echo $lang['Shre_Docs_Wth']; ?></h2> 
                    </div>
                    <div id="unseshare">
                        <div class="panel-body">
                            <p class="text-alert"><?php echo $lang['Pls_slct_Fles_for_Sre']; ?></p>
                        </div>
                        <div class="modal-footer"> 
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                        </div>
                    </div>
                    <div id="selected2">
                        <form method="post">
                            <div class="panel-body" >
                                <div class="row">
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

                                </button> 
                            </div>
                        </form>
                    </div>
                </div> 
            </div>
        </div><!-- /.modal -->

        <!--share files with users-->
        <div id="mail-selected-files" class="modal fade"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog"> 
                <div class="panel panel-color panel-danger"> 
                    <div class="panel-heading"> 
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                        <h2 class="panel-title" id="mailf"> <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <?php echo $lang['Hre_msge']; ?></h2>
                        <h2 class="panel-title" style="display:none;" id="mtitle"> <?php echo $lang['mail_document']; ?></h2> 
                    </div>
                    <div id="unmail">
                        <div class="panel-body">
                            <h5 class="text-alert"><?php echo $lang['Pls_slct_Fles_for_mail']; ?></h5>
                        </div>
                        <div class="modal-footer"> 
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                        </div>
                    </div>
                    <div id="selected3">
                        <form method="post" >
                            <div class="panel-body" >
                                <div class="row">
                                    <div class="form-group">
                                        <label><?php echo $lang['Email']; ?></label>
                                        <input type="email" name="mailto" id="mailto" parsley-type="email" class="form-control" required="" placeholder="<?php echo $lang['Enter_Email_Id']; ?>">
                                        <!--<select class="select2 select2-multiple" multiple data-placeholder="Select Users" name="userid[]" required>
                                        <?php
//                                                $sameGroupIDs = array();
//                                                $group = mysqli_query($db_con, "select * from tbl_bridge_grp_to_um where find_in_set('$_SESSION[cdes_user_id]',user_ids)") or die('Error' . mysqli_error($db_con));
//                                                while ($rwGroup = mysqli_fetch_assoc($group)) {
//                                                    $sameGroupIDs[] = $rwGroup['user_ids'];
//                                                }
//                                                $sameGroupIDs = array_unique($sameGroupIDs);
//                                                sort($sameGroupIDs);
//                                                $sameGroupIDs = implode(',', $sameGroupIDs);
//
//                                                $user = mysqli_query($db_con, "select * from tbl_user_master where user_id in($sameGroupIDs)");
//                                                while ($rwUser = mysqli_fetch_assoc($user)) {
//                                                    if ($rwUser['user_id'] != 1 && $rwUser['user_id'] != $_SESSION['cdes_user_id']) {
//                                                        echo '<option value="' . $rwUser['user_id'] . '">' . $rwUser['first_name'] . ' ' . $rwUser['last_name'] . '</option>';
//                                                    }
//                                                }
                                        ?>
                                        </select>-->
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group">
                                        <label><?php echo $lang['subject']; ?></label>
                                        <input type="text" name="subject" id="subject" class="form-control" placeholder="<?php echo $lang['enter_subject']; ?>" required>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="form-group">
                                        <label><?php echo $lang['description']; ?></label>
                                        <textarea name="mailbody" id="mailbody" class="form-control" placeholder="<?php echo $lang['enter_description']; ?>" required ></textarea>
                                    </div>

                                </div>
                            </div> 
                            <div class="modal-footer">
                                <input type="hidden" id="mail_docids" name="mailFile">
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close'] ?></button>

                                <button type="submit" name="mailFiles" class="btn btn-primary"><i class="fa fa-send-o"></i> <?php echo $lang['Send'] ?></button>

                                </button> 
                            </div>
                        </form>
                    </div>
                </div> 
            </div>
        </div><!-- /.modal -->
        <!---assign meta-data model start ---->
        <div id="con-close-modal5" class="modal fade"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog"> 
                <div class="modal-content"> 
                    <div class="modal-header"> 
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                        <h4 class="modal-title"><?php echo $rwFolder['sl_name']; ?> : <?php echo $lang['Asgn_MetaData_Fields_to']; ?></h4> 
                    </div> 

                    <form method="post">
                        <div class="modal-body">

                            <div class="row shiv metaa">
                                <span><strong><?php echo $lang['Fld_Slt']; ?></strong></span>
                                <strong style="margin-left: 113px;"><?php echo $lang['Fld_Asnd']; ?></strong>
                                <select multiple="multiple" class="multi-select" id="my_multi_select1" name="my_multi_select1[]" data-plugin="multiselect">
                                    <?php
                                    $arrarMeta = array();
                                    $metas = mysqli_query($db_con, "select * from tbl_metadata_to_storagelevel where sl_id = '$slid'") or die('Error: metadata' . mysqli_error($db_con));
                                    while ($metaval = mysqli_fetch_assoc($metas)) {
                                        array_push($arrarMeta, $metaval['metadata_id']);
                                    }
                                    $meta = mysqli_query($db_con, "select * from tbl_metadata_master order by field_name asc");
                                    while ($rwMeta = mysqli_fetch_assoc($meta)) {
                                        if (in_array($rwMeta['id'], $arrarMeta)) {
                                            echo '<option value="' . $rwMeta['id'] . '" selected>' . $rwMeta['field_name'] . '</option>';
                                        } else {
                                            echo '<option value="' . $rwMeta['id'] . '">' . $rwMeta['field_name'] . '</option>';
                                        }
                                    }
                                    ?>
                                </select>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" value="<?php echo base64_decode(urldecode($_GET['id'])); ?>" name="id">
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                            <button class="btn btn-primary waves-effect waves-light" type="submit" name="assignMeta"><?php echo $lang['Submit']; ?></button>
                        </div>
                    </form>

                </div> 
            </div>
        </div>
        <div id="multi-csv-export-model" class="modal fade"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog"> 
                <div class="panel panel-color panel-danger"> 
                    <div class="panel-heading"> 
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                        <h2 class="panel-title" id="unexport"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <?php echo $lang['Hre_msge']; ?></h2>
                        <h2 class="panel-title" id="export_title"><?php echo $lang['xprt_Slt_Dta']; ?></h2>
                    </div>
                    <form action="multi_data_export" data-parsley-validate novalidate method="post" enctype="multipart/form-data">
                        <div class="panel-body">
                            <div class="row">
                                <label id="export_unselected" style="display:none;"><h5 class="text-alert"> <?php echo $lang['Pls_slt_Files_for_xpt_dta']; ?></h5></label>
                                <div id="export_selected">
                                    <label><?php echo $lang['Slct_Fles_fr_xpt_Frmt']; ?></label>
                                    <select class="form-control select2" name="select_Fm">
                                        <option value="csv"><?php echo $lang['Csv']; ?></option>
                                        <option value="excel"><?php echo $lang['Excel']; ?></option>
                                        <option value="pdf"><?php echo $lang['Pdf']; ?></option> 
                                        <option value="word"><?php echo $lang['Word']; ?></option>
                                    </select>
                                </div>
                            </div>
                            <input type="hidden" name="export_doc_ids" id="export_doc_ids" value="">
                        </div>

                        <div class="modal-footer"> 
                            <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                            <input type="hidden" value="<?php echo base64_decode(urldecode($_GET['id'])); ?>" name="id">
                            <button class="btn btn-primary waves-effect waves-light" type="submit" name="exportData" id="hidexp"> <i class="fa fa-download"></i> <?php echo $lang['Export']; ?></button>
                        </div>
                    </form>

                </div> 
            </div>
        </div>
        <!--ends assign-meta-data modal --> 
        <?php require_once './application/pages/footer.php'; ?>
        <!-- Right Sidebar -->
        <?php //require_once './application/pages/rightSidebar.php';     ?>
        <!-- /Right-bar -->

        <!-- END wrapper -->
        <?php require_once './application/pages/footerForjs.php'; ?>
        <!--for multiselect-->
        <script type="text/javascript" src="assets/plugins/multiselect/js/jquery.multi-select.js"></script>
        <script src="assets/js/jquery.core.js"></script>

        <script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>

        <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>
        <script src="assets/plugins/jstree/jstree.min.js"></script>
        <script src="assets/pages/jquery.tree.js"></script>

        <script type="text/javascript" src="assets/multi_function_script.js"></script>
        <script src="assets/js/gs_sortable.js"></script>

        <!-- for searchable select-->
        <script type="text/javascript">
                                                            var TSort_Data = new Array('table_demo_icons', '', 's', 'i', 'i', 's', 'd');
                                                            var TSort_Icons = new Array('<span><i class="fa fa-caret-up"></i></span>', '<span><i class="fa fa-caret-down"></i></span>');
                                                            tsRegister();

        </script>

        <!--edit metadata-->
        <script>
            $("a#editMdata").click(function () {
                var $id = $(this).attr('data');
                var $row = $(this).closest('tr');
                var name = '';
                var values = [];
                values = $row.find('td:nth-child(2)').map(function () {
                    var $this = $(this);
                    if ($this.hasClass('actions')) {

                    } else {
                        name = $.trim($this.text());
                    }

                    $("#editmetadata .modal-title").html("<?php echo $lang['Updt_Mta_Data_of_File']; ?>: <strong>" + name + "</strong>");
                    $.post("application/ajax/editMdataValue.php", {ID: $id}, function (result, status) {
                        if (status == 'success') {
                            $("#modalModifyMvalue").html(result);
                        }
                    });
                });
            });
        </script>                                 
        <script type="text/javascript">

            $(document).ready(function () {
                //$('form').parsley();
            });

            //firstname last name 
            $("input#userName, input#lastName").keypress(function (e) {
                //if the letter is not digit then display error and don't type anything
                if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57) && e.which != 46) {
                    //display error message
                    return true;
                } else {
                    return false;
                }
                str = $(this).val();
                str = str.split(".").length - 1;
                if (str > 0 && e.which == 46) {
                    return false;
                }
            });
            $("input#phone").keypress(function (e) {
                //if the letter is not digit then display error and don't type anything
                if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57) && e.which != 46) {
                    //display error message
                    return false;
                }
                str = $(this).val();
                str = str.split(".").length - 1;
                if (str > 0 && e.which == 46) {
                    return false;
                }
            });
            $('#basicTree')
                    // listen for event
                    .on('changed.jstree', function (e, data) {
                        if (data.node) {
                            //debugger;
                            var nodeID = data.node.id + '_anchor';
                            var href = $("#" + nodeID).attr('href');
                            //history.pushState(null, null, href);
                            window.location.href = href;
                        }
                        var i, j, r = [];
                        for (i = 0, j = data.selected.length; i < j; i++) {
                            r.push(data.instance.get_node(data.selected[i]).text);
                        }
                        //$('#event_result').html('Selected : <strong>' + r.join(', ') + '</strong>');

                    })
                    // create the instance
                    .jstree({
                        'core': {
                            'themes': {
                                'responsive': false
                            }
                        },
                        'types': {
                            'default': {
                                'icon': 'md md-folder'
                            },
                            'file': {
                                'icon': 'md md-my-library-books'
                            }
                        },
                        'plugins': ['types']
                    });
            $(document).ready(function () {

                //Disable mouse right click
                $("body").on("contextmenu", function (e) {
                    // return false;
                });
            });
        </script>
        <script>

            $("a#viewMeta").click(function () {

                if ($(this).find('i').hasClass('fa-eye')) {
                    $(".metadata").css('display', 'none');
                    $("a#viewMeta").find('i').removeClass('fa-eye');
                    $("a#viewMeta").find('i').addClass('fa-eye');
                    var mid = $(this).attr("data");
                    $("#" + mid).css('display', 'block');
                    $(this).find('i').removeClass('fa-eye');
                    $(this).find('i').addClass('fa-eye')
                } else {
                    $(".metadata").css('display', 'none');
                    $("a#viewMeta").find('i').removeClass('fa-eye');
                    $("a#viewMeta").find('i').addClass('fa-eye');
                }
            });
            $("input:checkbox").click(function () {
                var column = "table ." + $(this).attr("name");
                $(column).toggle();
            });
        </script>

        <!--for audio model-->
        <div id="modal-audio" class="modal fade"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
        <div id="modal-video" class="modal fade"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
        <!--modify starts-->
        <div id="con-close-modal" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-lg"> 
                <div class="modal-content"> 
                    <form method="post" >
                        <div class="modal-header"> 
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                            <h4 class="modal-title"><?php echo $lang['Update_Your_file']; ?></h4> 
                        </div>
                        <div class="modal-body" id="modalModify">

                        </div> 
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                            <button type="submit" name="editFileName" class="btn btn-primary waves-effect waves-light"><?php echo $lang['Save_changes']; ?></button> 
                        </div>
                    </form>

                </div> 
            </div>
        </div><!-- /.modal -->
        <div id="con-close-modal-modify" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog"> 
                <div class="modal-content"> 
                    <div class="modal-header"> 
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                        <h4 class="modal-title"><?php echo $lang['Mdfy_Storage_Level']; ?></h4> 
                    </div> 
                    <form method="post">
                        <div class="modal-body">
                            <input class="form-control" name="modify_slname" value="<?php echo $rwFolder['sl_name']; ?>" required>
                        </div>
                        <div class="modal-footer"> 
                            <input value="<?php echo $rwFolder['sl_id']; ?>" name="modi" type="hidden" >
                            <input value="<?php echo $rwFolder['sl_parent_id']; ?>" name="modi_parentId" type="hidden" >
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                            <input type="submit" name="update" class="btn btn-primary" value="<?php echo $lang['Save_changes']; ?>">
                        </div>
                    </form>
                </div> 
            </div>
        </div><!-- /.modal -->  
        <!--start delete model-->
        <div id="con-close-modal2" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog"> 
                <div class="panel panel-color panel-danger"> 
                    <div class="panel-heading"> 
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                        <h2 class="panel-title"><?php echo $lang['Dlt_Docment']; ?></h2> 
                    </div> 
                    <form method="post">
                        <div class="panel-body">
                            <p class="text-alert"><?php echo $lang['r_u_sr_tht_u_wnt_to_dl_ts_Dc']; ?></p>
                        </div>
                        <div class="modal-footer"> 
                            <input type="hidden" id="uid" name="uid">
                            <?php
                            if ($rwgetRole['role_id'] == 1) {
                                ?>
                                <button type="submit" id="yes" name="deleteDoc" class="btn btn-danger" value="Yes"> <i class="fa fa-trash-o"></i> <?php echo $lang['Delete']; ?></button>
                                <?php
                            }
                            ?>
                            <button type="submit" id="no" name="deleteDoc" class="btn btn-info"> <i class="fa fa-recycle"></i>
                                <?php
                                if ($rwgetRole['role_id'] == 1) {
                                    echo $lang['Recycle'];
                                } else {
                                    echo $lang['Delete'];
                                }
                                ?>

                            </button> 
                        </div>
                    </form>
                </div> 
            </div>
        </div><!--ends delete modal -->
        <!--start delete model-->
        <div id="con-close-modal21" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog"> 
                <div class="modal-content"> 
                    <div class="modal-header"> 
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                        <h4 class="modal-title"><?php echo $lang['Dlt_Docment']; ?></h4> 
                    </div> 
                    <form method="post">
                        <div class="modal-body">
                            <p style="color: red;"><?php echo $lang['r_u_sr_tht_u_wnt_to_dl_ts_Dc']; ?></p>
                        </div>
                        <div class="modal-footer"> 
                            <input type="hidden" id="uid" name="uid">
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['close']; ?></button>
                            <input type="submit" name="deleteDoc" class="btn btn-danger" value="<?php echo $lang['Delete']; ?>">
                        </div>
                    </form>
                </div> 
            </div>
        </div><!--ends delete modal -->
        <!--start delete Version of Document model-->
        <div id="deleteVersion" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog"> 
                <div class="panel panel-color panel-danger"> 
                    <div class="panel-heading"> 
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                        <h2 class="panel-title"><?php echo $lang['Dlt_Vrsn_of_Docment']; ?></h2> 
                    </div> 
                    <form method="post">
                        <div class="panel-body">
                            <p style="color: red;"><?php echo $lang['r_u_sr_tht_u_wt_to_dl_ts_vsn_of_Dc_th_dc_wl_b_dlt_pnt']; ?></p>
                        </div>
                        <div class="modal-footer"> 
                            <input type="hidden" id="docid" name="docid">
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                            <input type="submit" name="deleteVersionDoc" class="btn btn-danger" value="<?php echo $lang['Delete']; ?>">
                        </div>
                    </form>
                </div> 
            </div>
        </div><!--ends delete modal -->
        <!---assign workflow---->
        <div id="assign-workflow" class="modal fade"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title"><?php echo $lang['Asgn_in_Wrk_flow']; ?></h4> 
                    </div>
                    <form method="post" class="form-inline" id="wfasign">
                        <div class="modal-body">
                            <label><?php echo $lang['Assign_To']; ?></label>
                            <select class="form-control select2" id="wfid" name="wfid">
                                <option selected disabled style="background: #808080; color: #fff;"><?php echo $lang['Slt_Wrkflw']; ?></option>
                                <?php
                                $WorkflwGet = mysqli_query($db_con, "select * from tbl_workflow_master") or die('Error in getWorkflw Assign:' . mysqli_error($db_con));
                                while ($rwWorkflwGet = mysqli_fetch_assoc($WorkflwGet)) {
                                    ?> 
                                    <option value="<?php echo $rwWorkflwGet['workflow_id']; ?>" name="wrkname"><?php echo $rwWorkflwGet['workflow_name']; ?></option>
                                <?php } ?>
                            </select>

                        </div>
                        <div class="modal-footer"> 
                            <input type="hidden" id="mTowf" name="mTowf">
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                            <input type="submit" name="assignTo" class="btn btn-primary" value="<?php echo $lang['Submit'] ?>" >
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--display wait gif image after submit-->
        <div style="display: none; background: rgba(0,0,0,0.5); width: 100%; z-index: 2000; position: fixed; top:0;" id="wait">;

            <img src="assets/images/proceed.gif" alt="load"  style=" margin-left: 48%; margin-top: 250px; width: 100px; height:100px; position: fixed; "/>
        </div>  
        <script>
            //for wait gif display after submit
            var heiht = $(document).height();
            //alert(heiht);
            $('#wait').css('height', heiht);
            $('#wfasign').submit(function () {
                if ($.trim($("#wfid").val()) != "") {
                    $('#wait').show();
                    //$('#wait').css('height',heiht);
                    $('#assign-workflow').hide();
                    return true;
                }
            });
        </script>
        <!--Edit metadata-->
        <div id="editmetadata" class="modal fade bs-example-modal-lg"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog"> 
                <div class="modal-content"> 
                    <form method="post" enctype="multipart/form-data">
                        <div class="modal-header"> 
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                            <h4 class="modal-title"><?php echo $lang['Edit_MetaData']; ?></h4> 
                        </div>
                        <div class="modal-body" id="modalModifyMvalue">
                            <img src="assets/images/load.gif" alt="load" class="img-responsive center-block" width="50px"/>
                        </div> 
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                            <button type="submit" name="editMetaValue" class="btn btn-primary"><?php echo $lang['Save_checkout']; ?></button> 
                        </div>
                    </form>

                </div> 
            </div>
        </div>

        <!---Create child model start ---->
        <div id="con-close-modal1" class="modal fade"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog"> 
                <div class="modal-content"> 
                    <div class="modal-header"> 
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                        <h4 class="modal-title"><?php echo $rwFolder['sl_name']; ?> (<?php echo $lang['Ad_New_Chld_to']; ?>)</h4> 
                    </div> 
                    <form method="post">
                        <div class="modal-body">
                            <label class="text-primary"><?php echo $lang['Crt_New_Cld']; ?><span class="text-alert">*</span></label>
                            <input class="form-control" name="create_child" placeholder="<?php echo $lang['Crt_New_Cld']; ?>" required="">
                        </div>
                        <div class="modal-footer"> 
                            <input value="<?php echo $rwFolder['sl_id']; ?>" name="add_child" type="hidden" >
                            <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                            <button type="submit" name="add_storage" class="btn btn-primary"><i class="fa fa-plus"></i> <?php echo $lang['Add']; ?></button>
                        </div>
                    </form>
                </div> 
            </div>
        </div>
        <!--ends Create child modal --> 
        <!--start delete model-->
        <div id="con-close-modal-del" class="modal fade"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog"> 
                <div class="panel panel-color panel-danger"> 
                    <div class="panel-heading"> 
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                        <h2 class="panel-title"><?php echo $lang['Are_u_confirm']; ?></h2> 
                    </div> 
                    <form method="post">
                        <div class="panel-body">
                            <p class="text-alert"><strong><?php echo $rwFolder['sl_name']; ?></strong> <?php echo $lang['Folder_and_their_Sub_folder']; ?></p>
                        </div>
                        <div class="modal-footer"> 
                            <input value="<?php echo $rwFolder['sl_id']; ?>" name="delsl" type="hidden" >
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close'] ?></button>
                            <button type="submit" name="deleted" class="btn btn-danger"><i class="fa fa-trash-o"></i> <?php echo $lang['Delete'] ?> </button>
                        </div>
                    </form>
                </div> 
            </div>
        </div><!--ends delete modal --> 
        <script>
            $("a#checkout").click(function () {
                var path = $(this).attr('data');
                // alert(id);

                $.post("application/ajax/checkout.php", {CHECKOUT: path}, function (result, status) {
                    window.location.href = "<?php echo basename($_SERVER['PHP_SELF']) . '?' . $_SERVER['QUERY_STRING']; ?>";

                });
            });
            $("a#editMdata").click(function () {
                var id = $(this).attr('data');
                // alert(id);

                $.post("application/ajax/checkin.php", {CHECKIN: id}, function (result, status) {

                });
            });
            $("a#editRow").click(function () {
                var id = $(this).attr('data');
                // alert(id);

                $.post("application/ajax/updateDocument.php", {ID: id}, function (result, status) {
                    if (status == 'success') {
                        $("#modalModify").html(result);
                        //alert(result);
                    }
                });
            });
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
            $("a#removeRow").click(function () {
                var id = $(this).attr('data');
                // alert(id);
                $("#uid").val(id);
            });
            $("a#deleteVersionDoc").click(function () {
                var id = $(this).attr("data");
                $("#docid").val(id);
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
            $("a#moveToWf").click(function () {
                var id = $(this).attr('data');
                // alert(id);
                $("#mTowf").val(id);
            });
        </script>
        <!-- MODAL for addworkflow -->
        <script>

            $("#wfid").change(function () {
                var wfId = $(this).val();
                //alert(lbl);
                $.post("application/ajax/workFlstp.php", {wid: wfId}, function (result, status) {
                    if (status == 'success') {
                        $("#stp").html(result);
                    }
                });
            });
            $("#ufw,#verify-comp").click(function (event) {
                if ($("input#myCheck").is(":checked")) {
                    alert('ok');
                } else {
                    document.querySelector('#inufw').click();
                }
            });
        </script>
        <!-- for move level-->
        <div id="con-close-modal4" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog"> 
                <div class="modal-content"> 
                    <div class="modal-header"> 
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                        <h4 class="modal-title"><?php echo $lang['Move_Storage_Level'] ?></h4> 
                    </div> 
                    <form method="post" class="form-inline">
                        <div class="modal-body">
                            <div class="row">
                                <?php
                                $moveFolderName = mysqli_query($db_con, "select * from tbl_storage_level where sl_id = $slid") or die('Error in move folder name: ' . mysqli_error($db_con));
                                $rwmoveFolderName = mysqli_fetch_assoc($moveFolderName);
                                ?>     
                                <label><?php echo $rwmoveFolderName['sl_name']; ?> : <?php echo $lang['Move_Fld_File'] ?></label>
                                <br><br>
                                <div class="col-md-12">
                                    <label><?php echo $lang['Move_To'] ?></label>
                                    <select class="form-control select2" name="moveToParentId" id="parentMoveLevel">

                                        <option selected disabled style="background: #808080; color: #fff;"><?php echo $lang['Sel_Strg_Lvl'] ?></option>

                                        <?php
                                        $storeName = mysqli_query($db_con, "select * from tbl_storage_level where sl_depth_level= '0' AND sl_id != '$slid'") or die('Error in move store: ' . mysqli_error($db_con));

                                        while ($rwstoreName = mysqli_fetch_assoc($storeName)) {
                                            ?>
                                            <option value="<?php echo $rwstoreName['sl_id']; ?>"><?php echo $rwstoreName['sl_name']; ?></option>
                                        <?php } ?>
                                    </select>
                                    <br>            
                                    <div class="row">
                                        <div class="col-md-12" id="child">

                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer"> 
                            <input value="<?php echo $rwFolder['sl_id']; ?>" name="modi" type="hidden" >
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close'] ?></button> 
                            <input type="submit" name="move" class="btn btn-primary" value="<?php echo $lang['Move_Storage'] ?>">
                        </div>

                    </form>

                </div> 
            </div>
        </div>
        <script>

            $("#parentMoveLevel").change(function () {
                var lbl = $(this).val();
                //alert(lbl);
                $.post("application/ajax/parentMoveList.php", {parentId: lbl, levelDepth: 0, sl_id:<?php echo $slid; ?>}, function (result, status) {
                    if (status == 'success') {
                        $("#child").html(result);
                        //alert(result);
                    }
                });
            });
            //filter limit
            var url = window.location.href + "?";
            function removeParam(key, sourceURL) {
                sourceURL = String(sourceURL).replace("#/", "");
                var rtn = sourceURL.split("?")[0],
                        param,
                        params_arr = [],
                        queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";
                if (queryString !== "") {
                    params_arr = queryString.split("&");
                    for (var i = params_arr.length - 1; i >= 0; i -= 1) {
                        param = params_arr[i].split("=")[0];
                        if (param === key) {
                            params_arr.splice(i, 1);
                        }
                    }
                    rtn = rtn + "?" + params_arr.join("&");
                } else {
                    rtn = rtn + '?';
                }
                return rtn;
            }
            jQuery(document).ready(function ($) {
                $("#limit").change(function () {
                    lval = $(this).val();
                    url = removeParam("limit", url);
                    url = url + "&limit=" + lval;
                    window.open(url, "_parent");
                });
            });
        </script>

        <!-- for copy level-->
        <!-- for copy level-->
        <div id="con-close-modal6" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog"> 

                <div class="modal-content"> 

                    <div class="modal-header"> 
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                        <h4 class="modal-title"><?php echo $lang['Cpy_Storage']; ?></h4> 
                    </div> 
                    <form method="post" class="form-inline">
                        <div class="modal-body">

                            <div class="row">
                                <div class="col-md-12">

                                    <div class="col-md-12">
                                        <label><?php echo $lang['Cpy_Fld']; ?>:</label>
                                        /<input type="text" readonly id="tocopyfolder" name="toCopyFolder" class="form-control" value="<?php echo $tocpyFolder = $rwFolder['sl_name']; ?>">
                                    </div>
                                    <div class="col-md-12">
                                        <p class="text-danger" id="error"></p>
                                    </div>
                                    <div class="clearfix"></div>

                                    <div class="col-md-12">
                                        <label> <?php echo $lang['Cpy_To']; ?></label>
                                        <select class="form-control select2" name="moveToParentId" id="parentCopyLevel">

                                            <option selected style="background: #808080; color: #121213;"><?php echo $lang['Sel_Strg_Lvl']; ?></option>

                                            <?php
                                            $storeName = mysqli_query($db_con, "select * from tbl_storage_level where sl_depth_level= '0' AND sl_id != '$slid'") or die('Error in move store: ' . mysqli_error($db_con));

                                            $rwstoreName = mysqli_fetch_assoc($storeName)
                                            ?>
                                            <option value="<?php echo $rwstoreName['sl_id']; ?>"><?php echo $rwstoreName['sl_name']; ?></option>
                                        </select>
                                    </div>
                                    <div class="clearfix"></div>

                                    <div class="col-md-12">
                                        <span class="" id="FilesCopy">
                                        </span>
                                    </div>

                                </div>
                            </div>      
                            <div class="clearfix"></div>
                        </div>
                        <div class="modal-footer"> 
                            <input value="<?php echo $rwFolder['sl_id']; ?>" name="modi" type="hidden" >
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button> 
                            <input type="submit" name="copyLevel" class="btn btn-primary" value="Copy Storage">
                        </div>

                    </form>
                </div>
            </div> 
        </div>

        <div id="export" class="modal fade"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog"> 
                <div class="panel panel-color panel-primary"> 
                    <div class="panel-heading"> 
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                        <h2 class="panel-title"><?php echo $lang['Export_CSV']; ?></h2>
                    </div> 
                    <form method="post" action="export.php">
                        <div class="panel-body">
                            <div class="col-md-12">
                                <div class="radio radio-success radio-inline"> 
                                    <input type="radio" name="radExp" id="inlineRadio1" value="all" required>
                                    <label for="inlineRadio1"><?php echo $lang['Al_Files_in_slt_fld']; ?></label>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer"> 
                            <input value="<?php echo $slid; ?>" name="slid" type="hidden" >
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                            <button type="submit" name="startExport" class="btn btn-primary"><i class="fa fa-download"></i> <?php echo $lang['Strt_xprt']; ?></button>
                        </div>

                    </form>
                </div>
            </div> 
        </div>
        <!-- for bulk downloads files-->
        <?php
        $validate = mysqli_query($db_con, "select doc_path,old_doc_name,doc_extn,doc_id from tbl_document_master where doc_name='$slid' and flag_multidelete=1");
        if (mysqli_num_rows($validate) > 0) {
            ?>
            <div id="bulkdownload" class="modal fade"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog"> 
                    <div class="modal-content"> 
                        <div class="modal-header"> 
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                            <h4 class="modal-title"><?php echo $lang['Dwnld_All_Files_of_slt_fld_only']; ?></h4>
                        </div> 

                        <form method="post">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <textarea class="form-control" name="reason" cols="65" rows="5" placeholder="<?php echo $lang['Wte_Rson_fr_Dnldng_fles']; ?>" required></textarea>
                                        </div>
                                    </div>
                                </div> 
                            </div>
                            <div class="modal-footer"> 
                                <input value="<?php echo $slid; ?>" name="slid" type="hidden" >
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close'] ?></button> 
                                <input type="submit" name="bulkDownload" class="btn btn-primary" value="<?php echo $lang['Download'] ?>">
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        <?php } else { ?>
            <div id="bulkdownload" class="modal fade"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog"> 
                    <div class="modal-content"> 
                        <div class="modal-header"> 
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                            <h4 class="modal-title"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i><?php echo $lang['Hre_msge']; ?></h4>
                        </div> 
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <label style="color:red;"><?php echo $lang['No_Files_Ext_in_Slt_Storage']; ?></label>                       
                                </div>
                            </div> 
                        </div>
                        <div class="modal-footer"> 
                            <input value="<?php echo $slid; ?>" name="slid" type="hidden" >
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>  
        <script>
            $("#parentCopyLevel").change(function () {
                var lbl = $(this).val();

                var copyf = $("#tocopyfolder").val();
                var sfolder = $(this).find(":selected").text();
                //alert(lbl);
                $.post("application/ajax/parentCopyList.php", {parentId: lbl, levelDepth: 0, sl_id:<?php echo $slid; ?>, folder: copyf, sfolder: sfolder}, function (result, status) {
                    if (status == 'success') {
                        $("#FilesCopy").html(result);
                        //alert(result);
                        $.post("application/ajax/checkDuplicate.php", {parentId: lbl, levelDepth: 0, folder: copyf}, function (result, status) {
                            if (status == 'success') {
                                if (result == 0) {
                                    $("#tocopyfolder").attr("readonly", "readonly");
                                    $("#tocopyfolder").attr("readonly");
                                } else {
                                    $("#error").html(copyf + " is already exist in " + sfolder + ".Please rename storage name.");
                                    $("#tocopyfolder").removeAttr("readonly");
                                }
                            }
                        });
                    }
                });
            });
        </script>
        <!-- move selected files---->
        <div id="move-selected-files" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog">
                <div class="panel panel-color panel-danger" > 
                    <div class="panel-heading"> 
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h2 class="panel-title" id="unseMove"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i><?php echo $lang['Hre_msge']; ?></h2>
                        <h2 class="panel-title" style="display:none;" id="mov"><?php echo $lang['Move_Slt_Files']; ?></h2> 
                    </div>
                    <div id="unselected" style="display:none;">
                        <div class="panel-body">
                            <h5 class="text-alert"><?php echo $lang['Pls_slct_Fles_fr_mve']; ?></h5>
                        </div>

                        <div class="modal-footer"> 
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                        </div>
                    </div>
                    <div id="selected">
                        <?php
                        $moveFolderName = mysqli_query($db_con, "select * from tbl_storage_level where sl_id = $slid") or die('Error in move folder name: ' . mysqli_error($db_con));
                        $rwmoveFolderName = mysqli_fetch_assoc($moveFolderName);
                        ?>   
                        <form method="post">
                            <div class="panel-body">
                                <input type="hidden" name="doc_id_smove_multi" id="doc_id_smove_multi" value="">
                                <input type="hidden" name="sl_id_move_multi" id="sl_id_move_multi" value="<?php echo $slid; ?>">
                                <div class="row">
                                    <div class="form-group col-sm-12">
                                        <label><?php echo $lang['Move_Fld_File']; ?> <?php echo '(' . $rwmoveFolderName['sl_name'] . ') ' . $lang['folders']; ?></label>
                                    </div>
                                    <div class="col-md-12">
                                        <label> <?php echo $lang['Move_To']; ?></label>
                                        <select class="form-control select2" name="moveToParentId" id="moveToParentId">

                                            <option selected disabled style="background: #808080; color: #fff;"><?php echo $lang['Sel_Strg_Lvl']; ?></option>

                                            <?php
                                            $storeName = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slperm'") or die('Error in move store: ' . mysqli_error($db_con));
                                            while ($rwstoreName = mysqli_fetch_assoc($storeName)) {
                                                ?>
                                                <option value="<?php echo $rwstoreName['sl_id']; ?>"><?php echo $rwstoreName['sl_name']; ?></option>
                                            <?php }
                                            ?>
                                        </select>
                                        <br>            
                                        <div class="row">
                                            <div class="col-md-12" id="child1">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer"> 
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close'] ?></button> 
                                <input type="submit" name="movemulti" class="btn btn-primary" value="<?php echo $lang['Mve_fles'] ?>">
                            </div>

                        </form>
                    </div>
                </div> 
            </div>
        </div>
        <script>

            $("#moveToParentId").change(function () {
                var lbl = $(this).val();
                //alert(lbl);
                $.post("application/ajax/parentMoveList_1.php", {parentId: lbl, levelDepth: 0, sl_id:<?php echo $slid; ?>}, function (result, status) {
                    if (status == 'success') {
                        $("#child1").html(result);
                        //alert(result);
                    }
                });
            });
            //filter limit

            var url = window.location.href + "?";
            function removeParam(key, sourceURL) {
                sourceURL = String(sourceURL).replace("#/", "");
                var rtn = sourceURL.split("?")[0],
                        param,
                        params_arr = [],
                        queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";
                if (queryString !== "") {
                    params_arr = queryString.split("&");
                    for (var i = params_arr.length - 1; i >= 0; i -= 1) {
                        param = params_arr[i].split("=")[0];
                        if (param === key) {
                            params_arr.splice(i, 1);
                        }
                    }
                    rtn = rtn + "?" + params_arr.join("&");
                } else {
                    rtn = rtn + '?';
                }
                return rtn;
            }
            jQuery(document).ready(function ($) {
                $("#limit").change(function () {
                    lval = $(this).val();
                    url = removeParam("limit", url);
                    url = url + "&limit=" + lval;
                    window.open(url, "_parent");
                });
            });
        </script>
        <!--copy selected files--->
        <div id="copy-selected-files" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog"> 
                <div class="panel panel-color panel-danger"> 
                    <div class="panel-heading"> 
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                        <h2 class="panel-title" id="cop"> <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <?php echo $lang['Hre_msge']; ?></h2> 
                        <h2 class="panel-title" style="display:none;" id="ctitle"><?php echo $lang['Cpy_Slt_Files_in_Storage']; ?></h2> 
                    </div> 
                    <div id="unselected1" style="display:none;">
                        <div class="panel-body">
                            <h5 class="text-alert"><?php echo $lang['Pls_slct_Fles_fr_Cpy']; ?></h5>
                        </div>
                        <div class="modal-footer"> 
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                        </div>
                    </div>
                    <div id="selected1">
                        <form method="post">
                            <div class="panel-body" id="csf">
                                <div class="row">
                                    <label><?php echo $lang['Copy_files']; ?> </label>
                                    <input type="text" readonly id="tocopyfolder" name="toCopyFolder" class="form-control" value="<?php echo $tocpyFolder = $rwFolder['sl_name']; ?>">
                                    <div class="col-md-12">
                                        <p class="text-danger" id="error"></p>
                                    </div>

                                    <input type="hidden" name="doc_ids" id="doc_ids" values="">
                                    <input type="hidden" name="sl_id4" id="sl_id4" values="">

                                    <label> <?php echo $lang['Cpy_To']; ?></label>
                                    <select class="form-control select2" name="copyToParentId" id="copyToParentId">

                                        <option selected style="background: #808080; color: #fff;"><?php echo $lang['Sel_Strg_Lvl']; ?></option>
                                        <?php
                                        $storeName = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slperm'") or die('Error in move store: ' . mysqli_error($db_con));
                                        while ($rwstoreName = mysqli_fetch_assoc($storeName)) {
                                            ?>
                                            <option value="<?php echo $rwstoreName['sl_id']; ?>"><?php echo $rwstoreName['sl_name']; ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                    <div class="col-md-12" id="child2">

                                    </div>
                                </div>   
                            </div>
                            <div class="modal-footer"> 
                                <input value="<?php echo $rwFolder['sl_id']; ?>" name="modi" type="hidden" >
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close'] ?></button> 
                                <input type="submit" name="copyFiles" class="btn btn-primary" value="<?php echo $lang['Copy_files'] ?>">
                            </div>
                        </form>
                    </div>
                </div>
            </div> 
        </div>
        <script>

            $("#copyToParentId").change(function () {
                var lbl = $(this).val();
                //alert(lbl);
                $.post("application/ajax/parentMoveList_2.php", {parentId: lbl, levelDepth: 0, sl_id:<?php echo $slid; ?>}, function (result, status) {
                    if (status == 'success') {
                        $("#child2").html(result);
                        //alert(result);
                    }
                });
            });
            //filter limit

            var url = window.location.href + "?";
            function removeParam(key, sourceURL) {
                sourceURL = String(sourceURL).replace("#/", "");
                var rtn = sourceURL.split("?")[0],
                        param,
                        params_arr = [],
                        queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";
                if (queryString !== "") {
                    params_arr = queryString.split("&");
                    for (var i = params_arr.length - 1; i >= 0; i -= 1) {
                        param = params_arr[i].split("=")[0];
                        if (param === key) {
                            params_arr.splice(i, 1);
                        }
                    }
                    rtn = rtn + "?" + params_arr.join("&");
                } else {
                    rtn = rtn + '?';
                }
                return rtn;
            }
            jQuery(document).ready(function ($) {
                $("#limit").change(function () {
                    lval = $(this).val();
                    url = removeParam("limit", url);
                    url = url + "&limit=" + lval;
                    window.open(url, "_parent");
                });
            });
        </script>
        <!-- SHARE SELECTED FILES--->
        <?php
        if (isset($_POST['assignMeta'])) {

            $childName = preg_replace("/[^A-Za-z0-9 ]/", "", $_POST['id']);
            $childName = mysqli_real_escape_string($db_con, $childName);
            $fields = $_POST['my_multi_select1'];
            $flag = 0;
            if (!empty($childName)) {
                $reset = mysqli_query($db_con, "delete from tbl_metadata_to_storagelevel where sl_id='$childName'");
            }
            if (!empty($fields)) {
                $metaNames = array();
                foreach ($fields as $field) {
                    if (!empty($childName)) {
                        //check meta data assigned or not
                        $match = mysqli_query($db_con, "select * from tbl_metadata_to_storagelevel where sl_id='$childName' and metadata_id='$field'") or die('Error:' . mysqli_error($db_con));
                        if (mysqli_num_rows($match) <= 0) {
                            //assign meta data
                            $create = mysqli_query($db_con, "insert into tbl_metadata_to_storagelevel (`metadata_id`, `sl_id`) values('$field','$childName')") or die('Error' . mysqli_error($db_con));
                            // find meta data details
                            $metan = mysqli_query($db_con, "select * from tbl_metadata_master where id='$field'");
                            $rwMetan = mysqli_fetch_assoc($metan);
                            $metaNames[] = $rwMetan['field_name'];
                            //check meta data in table tbl_document_master
                            $checkDoc = mysqli_query($db_con, "SHOW COLUMNS FROM tbl_document_master LIKE '$rwMetan[field_name]'");
                            if (mysqli_num_rows($checkDoc) <= 0) { //if not
                                $metaCreateDoc = mysqli_query($db_con, "ALTER TABLE tbl_document_master ADD `$rwMetan[field_name]` $rwMetan[data_type]($rwMetan[length_data])  null");
                            }
                            $flag = 1;
                            $sl_id = $childName;
                        } else {
                            $sl_id = $childName;
                        }
                    }
                }
                if ($flag == 1) {

                    $metaNames = implode(",", $metaNames);
                    $strgeName = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id = '$sl_id'");
                    $rwstrgeName = mysqli_fetch_assoc($strgeName);
                    $storageName = $rwstrgeName['sl_name'];
                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null, '$sl_id','MetaData($metaNames)  Assigned on storage $storageName','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                    echo '<script>taskSuccess("' . basename($_SERVER[REQUEST_URI]) . '","' . $lang['Metadata_Assigned'] . '");</script>';
                } else {
                    echo '<script>taskFailed("' . basename($_SERVER[REQUEST_URI]) . '","' . $lang['Metadata_failed'] . '");</script>';
                }
            } else {
                echo '<script>taskSuccess("' . basename($_SERVER[REQUEST_URI]) . '","' . $lang['nmdta'] . '");</script>';
            }
            mysqli_close($db_con);
        }
        ?>
        <!--move Storage-->
        <?php
        if (isset($_POST['move'])) {
            //echo $_POST['moveToId']; die;
            if (!empty($_POST['lastMoveId'])) {
                $checkDublteStorage = mysqli_query($db_con, "Select * from tbl_storage_level where sl_id = '$slid'") or die('Error in checkDublteStorage:' . mysqli_error($db_con));

                $rwcheckDublteStorage = mysqli_fetch_assoc($checkDublteStorage);
                $lmoveid = preg_replace("/[^A-Za-z0-9 ]/", "", $_POST[lastMoveId]);
                $sql_child = "select * FROM tbl_storage_level WHERE sl_parent_id = '$lmoveid' AND sl_name = '$rwcheckDublteStorage[sl_name]'";

                $sql_child_run = mysqli_query($db_con, $sql_child) or die('Error:' . mysqli_error($db_con));

                if (mysqli_num_rows($sql_child_run)) {
                    $moveToId = preg_replace("/[^A-Za-z0-9 ]/", "", $_POST['lastMoveId']);
                    $moveToName = mysqli_query($db_con, "Select * from tbl_storage_level where sl_id = '$moveToId'") or die('Error in checkDublteStorage:' . mysqli_error($db_con));
                    $rwmoveToName = mysqli_fetch_assoc($moveToName);
                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$moveToId','Storage $rwFolder[sl_name] already exist in $rwmoveToName[sl_name].','$date', null,'$host','')") or die('error : ' . mysqli_error($db_con));
                    echo'<script>taskFailed("storage","' . $lang['Strg_Nme_Having_Same_Name_Already_Exist'] . '");</script>';
                } else {
                    $moveToId = preg_replace("/[^A-Za-z0-9 ]/", "", $_POST['lastMoveId']);
                    $lastMoveIdLevel = preg_replace("/[^A-Za-z0-9 ]/", "", $_POST['lastMoveIdLevel']);
                    $lastMoveIdLevel = $lastMoveIdLevel + 1;

                    $moveStorage = "update tbl_storage_level set sl_parent_id = '$moveToId', sl_depth_level = '$lastMoveIdLevel' where sl_id = '$slid'";
                    $moveStorage_run = mysqli_query($db_con, $moveStorage) or die('Error in move Stroge : ' . mysqli_error($db_con));

                    $moveToName = mysqli_query($db_con, "Select * from tbl_storage_level where sl_id = '$moveToId'") or die('Error in checkDublteStorage:' . mysqli_error($db_con));
                    $rwmoveToName = mysqli_fetch_assoc($moveToName);

                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$moveToId','Storage $rwFolder[sl_name] moved to $rwmoveToName[sl_name]','$date',null,'$host','')") or die('error : ' . mysqli_error($db_con));
                    echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Strge_Moved_Scesfly'] . '");</script>';
                }
            }
            mysqli_close($db_con);
        }
        ?>
        <!--copy storage-->
        <?php
        if (isset($_POST['copyLevel'])) {
            if (isset($_POST['toCopyFolder']) && !empty($_POST['toCopyFolder'])) {
                $toCopyFolder = preg_replace("/[^A-Za-z0-9 ]/", "", $_POST['toCopyFolder']);
                if (isset($_POST['lastCopyToId']) && !empty($_POST['lastCopyToId'])) {
                    $lastCopyToId = preg_replace("/[^A-Za-z0-9 ]/", "", $_POST['lastCopyToId']);
                    copyStorage($slid, $lastCopyToId, $toCopyFolder, $date, $host, $fileserver, $port, $ftpUser, $ftpPwd, $lang);
                }
            }
            mysqli_close($db_con);
        }
        ?>

        <!--modify storage level starts-->
        <?php
        if (isset($_POST['update'])) {
            $sl_id = preg_replace("/[^A-Za-z0-9 ]/", "", $_POST['modi']);
            $modify = preg_replace("/[^A-Za-z0-9 ]/", "", $_POST['modify_slname']);
            $parentid = preg_replace("/[^A-Za-z0-9 ]/", "", $_POST['modi_parentId']);

            $modify = preg_replace('/[^a-zA-Z0-9_]/', '', mysqli_real_escape_string($db_con, $_POST['modify_slname']));
            //$slname = "select * from tbl_storage_level where sl_parent_id = '$parentid' AND sl_id != '$sl_id' AND sl_name = '$modify'";
            $checkSlName = mysqli_query($db_con, "select * from tbl_storage_level where sl_parent_id = '$parentid' AND sl_id != '$sl_id' AND sl_name = '$modify'") or die('Error in check DublteStorage:' . mysqli_error($db_con));
            if (mysqli_num_rows($checkSlName) > 0) {
                echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Strg_of_Same_Nme_Already_Exst'] . '");</script>';
            } else {
                $modiStorage = mysqli_query($db_con, "Select * from tbl_storage_level where sl_id = '$sl_id'") or die('Error in get sl name:' . mysqli_error($db_con));
                $rwmodiStorage = mysqli_fetch_assoc($modiStorage);
                $updateToName = $rwmodiStorage['sl_name'];
                $sql = "update tbl_storage_level set sl_name = '$modify' WHERE sl_id = '$sl_id' ";
                $sql_run = mysqli_query($db_con, $sql) or die("error:" . mysqli_errno($db_con));
                if ($sql_run) {
                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$sl_id','Storage $updateToName rename to $modify.','$date', null,'$host','')") or die('error : ' . mysqli_error($db_con));
                    echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Strg_Updtd_Sucsfly'] . '");</script>';
                } else {
                    echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Strg_Updn_Fld'] . '");</script>';
                }
            }
            mysqli_close($db_con);
        }
        ?>
        <!---delete storage level start---->
        <?php
        if (isset($_POST['deleted'])) {
            $sl_id = preg_replace("/[^A-Za-z0-9 ]/", "", $_POST['delsl']);
            $sl_id = mysqli_real_escape_string($db_con, $sl_id);
            $deleteStorage = mysqli_query($db_con, "Select * from tbl_storage_level where sl_id = '$sl_id'") or die('Error :' . mysqli_error($db_con));
            $rwdeleteStorage = mysqli_fetch_assoc($deleteStorage);
            $deletStorageName = $rwdeleteStorage['sl_name'];
            $dirPath = "extract-here/" . $deletStorageName;
            $delStrg = mysqli_query($db_con, "Select sl_id from tbl_storagelevel_to_permission where user_id = '$_SESSION[cdes_user_id]'") or die('Error :' . mysqli_error($db_con));
            $rwdelStrg = mysqli_fetch_assoc($delStrg);
            if ($rwdelStrg['sl_id'] != $sl_id) {
                delStrg($sl_id, $fileserver, $port, $ftpUser, $ftpPwd);

                rmdir($dirPath);
                mysqli_query($db_con, "DELETE FROM tbl_storage_level WHERE sl_id='$sl_id'") or die('Error:' . mysqli_error($db_con));
                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$sl_id','Storage Name $deletStorageName deleted.','$date', null,'$host','')") or die('error :' . mysqli_error($db_con));
                $delParentId = $rwdeleteStorage['sl_parent_id'];
                echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Strg_Deleted_Successfully'] . '");</script>';
            } else {
                echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['root_storage_cannot_deleted'] . '");</script>';
            }
            mysqli_close($db_con);
        }
        ?>
        <!--Add Storage Level -->
        <?php
        if (isset($_POST['add_storage'])) {
            $sl_id = preg_replace("/[^A-Za-z0-9 ]/", "", $_POST['add_child']);
            $sl_id = mysqli_real_escape_string($db_con, $sl_id);
            $create = mysqli_real_escape_string($db_con, $create);
            $create = preg_replace('/[^a-zA-Z0-9_]/', '', mysqli_real_escape_string($db_con, $_POST['create_child']));
            $checkLvlName = mysqli_query($db_con, "select * from tbl_storage_level where sl_parent_id='$sl_id' AND sl_name = '$create'") or die('Error in checkLvlName:' . mysqli_error($db_con));
            if (mysqli_num_rows($checkLvlName) > 0) {

                echo'<script>taskFailed("' . basename($_SERVER[REQUEST_URI]) . '","' . $lang['Storage_Name_Already_Exist'] . '");</script>';
            } else {

                $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$sl_id'") or die('Error:' . mysqli_error($db_con));

                $rwParent = mysqli_fetch_assoc($parent);

                $level = $rwParent['sl_depth_level'] + 1;
                if (!empty($create)) {
                    $sql = "insert into tbl_storage_level(sl_id, sl_name, sl_parent_id, sl_depth_level)VALUES (null, '$create', '$sl_id', '$level')";
                    $sql_run = mysqli_query($db_con, $sql) or die("error:" . mysqli_error($db_con));
                    $newChildId = mysqli_insert_id($db_con);
                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$newChildId','New Sub-folder $create Created.','$date', null,'$host','')") or die('error :' . mysqli_error($db_con));
                    echo'<script>taskSuccess("' . basename($_SERVER[REQUEST_URI]) . '","' . $lang['Child_Created_Successfully'] . '");</script>';
                }
            }
            mysqli_close($db_con);
        }
        ?>
        <?php
        //asign doc to workflow
        if (isset($_POST['assignTo'])) {
            echo 'workflow id: ' . $wfid = preg_replace("/[^A-Za-z0-9 ]/", "", $_POST['wfid']);
            echo 'doc id: ' . $dcId = preg_replace("/[^A-Za-z0-9 ]/", "", $_POST['mTowf']);

            $wfd = mysqli_query($db_con, "select * from tbl_workflow_master where workflow_id='$wfid'");
            $rwWfd = mysqli_fetch_assoc($wfd);
            $workFlowName = $rwWfd['workflow_name'];
            $workFlowArray = explode(" ", $workFlowName);
            $ticket = '';
            for ($w = 0; $w < count($workFlowArray); $w++) {
                $name = $workFlowArray[$w];
                $ticket = $ticket . substr($name, 0, 1);
            }
            $user_id = $_SESSION['cdes_user_id'];
            $ticket = $ticket . '_' . $user_id . '_' . strtotime($date);
            $id = preg_replace("/[^A-Za-z0-9 ]/", "", base64_decode(urldecode(@$_GET['id'])));  //get docId from url
            $id = $id . '_' . $wfid;
            if (!empty($wfid)) {

                $chkrw = mysqli_query($db_con, "select * from tbl_task_master where workflow_id = '$wfid'") or die('Error:' . mysqli_error($db_con));

                if (mysqli_num_rows($chkrw) > 0) {

                    $uptDocName = mysqli_query($db_con, "UPDATE tbl_document_master SET doc_name = '$id' where doc_id = '$dcId'") or die('error update:' . mysqli_error($db_con));

                    $getStep = mysqli_query($db_con, "select * from tbl_step_master where workflow_id = '$wfid' ORDER BY step_order ASC LIMIT 1") or die('Error:' . mysqli_error($db_con));
                    $getStpId = mysqli_fetch_assoc($getStep);
                    $stpId = $getStpId['step_id'];

                    $getTask = mysqli_query($db_con, "select * from tbl_task_master where step_id = '$stpId' ORDER BY task_order ASC LIMIT 1") or die('Error:' . mysqli_error($db_con));
                    $getTaskId = mysqli_fetch_assoc($getTask);
                    $tskId = $getTaskId['task_id'];

                    $getTaskDl = mysqli_query($db_con, "select * from tbl_task_master where task_id='$tskId'") or die('Error:' . mysqli_error($db_con));
                    $rwgetTaskDl = mysqli_fetch_assoc($getTaskDl);

                    if ($rwgetTaskDl['deadline_type'] == 'Date' || $rwgetTaskDl['deadline_type'] == 'Hrs') {

                        $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 60 * 60));
                    }
                    if ($rwgetTaskDl['deadline_type'] == 'Days') {

                        $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 24 * 60 * 60));
                    }
                    $insertInTask = mysqli_query($db_con, "INSERT INTO tbl_doc_assigned_wf(task_id, doc_id, start_date, end_date, task_status, assign_by, task_remarks,ticket_id) VALUES ('$tskId', '$dcId', '$date', '$endDate', 'Pending', '$user_id', '$taskRemark','$ticket')") or die('Erorr: hh' . mysqli_error($db_con));
                    $idins = mysqli_insert_id($db_con);

                    $getTask = mysqli_query($db_con, "select * from tbl_task_master where task_id = '$tskId'") or die('Error:' . mysqli_error($db_con));
                    $rwgetTask = mysqli_fetch_assoc($getTask);
                    $TskStpId = $rwgetTask['step_id'];
                    $TskWfId = $rwgetTask['workflow_id'];
                    $TskOrd = $rwgetTask['task_order'];
                    $nextTaskOrd = $TskOrd + 1;
                    nextTaskAsin($nextTaskOrd, $TskWfId, $TskStpId, $dcId, $date, $user_id, $db_con, $taskRemark, $ticket);
                    if ($insertInTask) {
                        require_once './mail.php';

                        $mail = assignTask($ticket, $idins, $db_con, $projectName);
                        if ($mail) {


                            echo '<script>uploadSuccess("' . basename($_SERVER[REQUEST_URI]) . '", "' . $lang['Sumitd_in_wf_Sucsfly'] . '");</script>';
                        } else {

                            echo '<script>taskFailed("' . basename($_SERVER[REQUEST_URI]) . '", "' . $lang['Ops_Ml_nt_snt'] . '")</script>';
                        }
                    } else {
                        echo '<script>taskFailed("' . basename($_SERVER[REQUEST_URI]) . '", "' . $lang['Opps_Sbmsn_fld'] . '")</script>';
                    }
                } else {
                    echo '<script>taskFailed("' . basename($_SERVER[REQUEST_URI]) . '", "' . $lang['Tre_is_no_tsk_in_ts_wfw'] . '")</script>';
                }
            } else {
                echo'<script>taskFailed("' . basename($_SERVER[REQUEST_URI]) . '","' . $lang['Pls_Slt_WF'] . '");</script>';
            }
            mysqli_close($db_con);
        }
        ?>
        <!--delete doc-->  
        <?php
        if (isset($_POST['Delmultiple'])) {

            $filePath = array();
            $pathtxt = array();
            $filename = array();
            $permission = trim($_POST['Delmultiple']);
            $del_sl_id = explode($_POST['sl_id1']);
            $docDelete = trim($_POST['DelFile']);
            $user_id4 = $_SESSION['cdes_user_id'];
            $chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um where FIND_IN_SET('$user_id4', user_ids) > 0") or die('Error:' . mysqli_error($db_con));
            $rwcheckUser = mysqli_fetch_assoc($chekUsr);
            $getDocPath = mysqli_query($db_con, "select doc_path,old_doc_name,doc_name from tbl_document_master where doc_id in($docDelete)") or die('Error:' . mysqli_error($db_con));
            while ($rwgetDocPath = mysqli_fetch_assoc($getDocPath)) {
                $filePath[] = $rwgetDocPath['doc_path'];
                $path = substr($rwgetDocPath['doc_path'], 0, strrpos($rwgetDocPath['doc_path'], '/') + 1);
                $pathtxt[] = 'extract-here/' . $path;
                $filename[] = $rwgetDocPath['old_doc_name'];
                $storgId = $rwgetDocPath['doc_name'];
            }
            if ($rwcheckUser['role_id'] == 1 && $permission == "Yes") {

                $del = mysqli_query($db_con, "DELETE FROM tbl_document_master WHERE doc_id in($docDelete)") or die('Error:' . mysqli_error($db_con));
                $delshareDoc = mysqli_query($db_con, "DELETE FROM tbl_document_share WHERE doc_ids in($docDelete)") or die('Error:' . mysqli_error($db_con));
                foreach ($filePath as $filePaths) {
                    $path = 'extract-here/' . $filePaths;
                    $ftppath = explode('/', $filePaths);
                    if (FTP_ENABLED) {
                        $ftp = new ftp();
                        $ftp->conn("$fileserver", "$port", "$ftpUser", "$ftpPwd");
                        $ftp->singleFileDelete(ROOT_FTP_FOLDER . '/' . $filePaths);
                        $arr = $ftp->getLogData();
                        if ($arr['error'] != "") {

                            echo '<h2>Error:</h2>' . implode('<br />', $arr['error']);
                        }
                    } else {

                        unlink($path);
                    }
                }
                if ($del) {
                    foreach ($filename as $filenames) {
                        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Storage Document $filenames Deleted','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                    }

                    echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Doc_Dltd_Sucesfly'] . '");</script>';
                } else {
                    echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Doc_Nt_Dltd'] . '");</script>';
                }
            } elseif ($rwcheckUser['role_id'] == 1 && $permission == "No") {
                $deletefilename1 = mysqli_query($db_con, "UPDATE tbl_document_master SET flag_multidelete=0 WHERE doc_id in($docDelete)") or die('Error:' . mysqli_error($db_con));
                if ($deletefilename1) {
                    foreach ($filename as $filenames) {
                        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Storage Document $filenames Deleted','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                    }

                    echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Doc_Dltd_Sucesfly'] . '");</script>';
                } else {
                    echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Doc_Nt_Dltd'] . '");</script>';
                }
            } else {
                $deletefilename1 = mysqli_query($db_con, "UPDATE tbl_document_master SET flag_multidelete=0 WHERE doc_id in($docDelete)") or die('Error:' . mysqli_error($db_con));
                if ($deletefilename1) {
                    foreach ($filename as $filenames) {
                        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Storage Document $filenames Deleted','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                    }
                    echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Doc_Dltd_Sucesfly'] . '");</script>';
                } else {
                    echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Doc_Nt_Dltd'] . '");</script>';
                }
            }
            mysqli_close($db_con);
        }
        if (isset($_POST['deleteDoc'])) {
            $id = preg_replace("/[^A-Za-z0-9 ]/", "", $_POST['uid']);
            $id = mysqli_real_escape_string($db_con, $id);
            $permission = preg_replace("/[^A-Za-z0-9 ]/", "", trim($_POST['deleteDoc']));
            $permission = mysqli_real_escape_string($db_con, $permission);
            $getDocPath = mysqli_query($db_con, "select * from tbl_document_master where doc_id='$id'") or die('Error:' . mysqli_error($db_con));
            $rwgetDocPath = mysqli_fetch_assoc($getDocPath);
            $filePath = $rwgetDocPath['doc_path'];
            $delfilename = $rwgetDocPath['old_doc_name'];
            $deldocId = $rwgetDocPath['doc_id'];
            $storgId = $rwgetDocPath['doc_name'];
            if ($rwcheckUser['role_id'] == 1 && $permission == "Yes") {
                $path = substr($rwgetDocPath['doc_path'], 0, strrpos($rwgetDocPath['doc_path'], '/') + 1);
                $pathtxt = 'extract-here/' . $path . 'TXT/' . $id . '.txt';

                $del = mysqli_query($db_con, "DELETE FROM tbl_document_master WHERE doc_id ='$id'") or die('Error:' . mysqli_error($db_con));
                $delDocShare = mysqli_query($db_con, "DELETE FROM tbl_document_share WHERE doc_ids ='$id'") or die('Error:' . mysqli_error($db_con));
                if ($del) {
                    if (FTP_ENABLED) {
                        $ftp = new ftp();
                        $ftp->conn("$fileserver", "$port", "$ftpUser", "$ftpPwd");
                        $ftp->singleFileDelete(ROOT_FTP_FOLDER . '/' . $filePath);
                        $arr = $ftp->getLogData();
                        if ($arr['error'] != "") {

                            echo '<h2>Error:</h2>' . implode('<br />', $arr['error']);
                        }
                    } else {
                        unlink('extract-here/' . $filePath);
                    }


                    unlink($pathtxt);
                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `doc_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null, '$deldocId', 'Storage Document $delfilename Deleted','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                    echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Doc_Dltd_Sucesfly'] . '");</script>';
                } else {
                    echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Doc_Nt_Dltd'] . '");</script>';
                }
            } elseif ($rwcheckUser['role_id'] == 1 && $permission == "No") {
                $deletefilename = mysqli_query($db_con, "UPDATE tbl_document_master SET flag_multidelete=0 WHERE doc_id='$id'") or die('Error:' . mysqli_error($db_con));
                if ($deletefilename) {
                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `doc_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null, '$deldocId', 'Storage Document $delfilename Deleted','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                    echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Strg_Deleted_Successfully'] . '");</script>';
                } else {
                    echo '<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Storage_Not_Deleted'] . '")</script>';
                }
            } else {
                $deletefilename = mysqli_query($db_con, "UPDATE tbl_document_master SET flag_multidelete=0 WHERE doc_id='$id'") or die('Error:' . mysqli_error($db_con));
                if ($deletefilename) {
                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `doc_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null, '$deldocId', 'Storage Document $delfilename Deleted','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                    echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Strg_Deleted_Successfully'] . '");</script>';
                } else {
                    echo '<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Storage_Not_Deleted'] . '")</script>';
                }
            }
            mysqli_close($db_con);
        }

        if (isset($_POST['deleteVersionDoc'])) {
            $id = preg_replace("/[^A-Za-z0-9 ]/", "", $_POST['docid']);
            $id = mysqli_real_escape_string($db_con, $id);
            $getDocPath = mysqli_query($db_con, "select * from tbl_document_master where doc_id='$id'") or die('Error:' . mysqli_error($db_con));
            $rwgetDocPath = mysqli_fetch_assoc($getDocPath);
            $filePath = $rwgetDocPath['doc_path'];
            $delvrsnfile = $rwgetDocPath['old_doc_name'];
            $del = mysqli_query($db_con, "delete from tbl_document_master where doc_id='$id'") or die('Error:' . mysqli_error($db_con));
            unlink('extract-here/' . $filePath);
            if ($del) {
                if (FTP_ENABLED) {

                    $ftp = new ftp();
                    $ftp->conn("$fileserver", "$port", "$ftpUser", "$ftpPwd");
                    $ftp->singleFileDelete(ROOT_FTP_FOLDER . '/' . $filePath);
                    $arr = $ftp->getLogData();
                    if ($arr['error'] != "") {

                        echo '<h2>Error:</h2>' . implode('<br />', $arr['error']);
                    }
                }
                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Storage Document $delvrsnfile Deleted','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));

                $docName = explode("_", $rwgetDocPath['doc_name']);
                $storgId = $docName[0];
                echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['file_version_delt_success'] . '");</script>';
                //echo'<script>taskSuccess("storageFiles","Document Deleted Successfully !");</script>';
            } else {
                echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['file_version_not_delt'] . '")</script>';
            }
            mysqli_close($db_con);
        }
        ?>               
        <!--rename doc-->
        <?php
        if (isset($_POST['editFileName'])) {

            $renameid = preg_replace("/[^A-Za-z0-9 ]/", "", filter_input(INPUT_POST, "docId"));
            $renameName = preg_replace("/[^A-Za-z0-9 ]/", "", filter_input(INPUT_POST, "renameName"));

            $updateDoc = mysqli_query($db_con, "update tbl_document_master set old_doc_name = '$renameName' where doc_id = '$renameid'") or die('Error: ' . mysqli_error($db_con));
        }
        ?>

        <!--update metadata value-->
        <?php
        if (isset($_POST['editMetaValue'])) {
            if (!empty($_FILES['fileName']['name'])) {
                $user_id = $_SESSION['cdes_user_id'];
                $doc_id = preg_replace("/[^A-Za-z0-9 ]/", "", $_POST['docid']);
                $doc_id = mysqli_real_escape_string($db_con, $doc_id);
                $file_name = $_FILES['fileName']['name'];
                $file_size = $_FILES['fileName']['size'];
                $file_type = $_FILES['fileName']['type'];
                $file_tmp = $_FILES['fileName']['tmp_name'];
                $pageCount = $_POST['pageCount'];
                if ($pageCount <= 0) {
                    $pageCount = 1;
                }
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
                $uploaddir = "extract-here/" . $storageName . '/';
                if (!is_dir($uploaddir)) {
                    mkdir($uploaddir, 777, TRUE) or die(print_r(error_get_last()));
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
                        //$ftp->get(ROOT_FTP_FOLDER.'/'.$doc_Path_copy_to,$doc_path); 

                        $filepath = $storageName . '/' . $filenameEnct;
                        $uploadfile = $ftp->put(ROOT_FTP_FOLDER . '/' . $filepath, $uploaddir);
                        $arr = $ftp->getLogData();
                        if ($uploadfile) {

                            $uploadInToFTP = true;
                            unlink($uploaddir);
                        } else {
                            echo '<h2>Error:</h2>' . implode('<br />', $arr['error']);
                            $uploadInToFTP = false;
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
                    $getMetaId = mysqli_query($db_con, "select * from tbl_document_master where doc_id = '$_POST[docid]'") or die('Error:' . mysqli_error($db_con));
                    //echo "select * from tbl_document_master where doc_id = '$_POST[docid]'";
                    $meta_row = mysqli_fetch_assoc($getMetaId);
                    $getMetaId = mysqli_query($db_con, "select * from tbl_metadata_to_storagelevel where sl_id = '$meta_row[doc_name]'") or die('Error:' . mysqli_error($db_con));
                    $i = 1;
                    while ($rwgetMetaId = mysqli_fetch_assoc($getMetaId)) {
                        $getMetaName = mysqli_query($db_con, "select * from tbl_metadata_master where id = '$rwgetMetaId[metadata_id]'") or die('Error:' . mysqli_error($db_con));
                        $StorageNme = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id='$rwgetMetaId[sl_id]'");
                        $rwStrName = mysqli_fetch_assoc($StorageNme);
                        while ($rwgetMetaName = mysqli_fetch_assoc($getMetaName)) {
                            $meta = mysqli_query($db_con, "select `$rwgetMetaName[field_name]` from tbl_document_master where doc_id='$meta_row[doc_id]'");
                            $rwMeta = mysqli_fetch_array($meta);
                            if ($rwgetMetaName['field_name'] == 'noofpages') {
                                
                            } else {
                                $fieldValue = $_POST['fieldName' . $i];
                                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Versioning Document $file_name Added','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                                if ($createVrsn) {
                                    //echo "update tbl_document_master set `$rwgetMetaName[field_name]` = '$fieldValue', doc_name='$updateDocName' where doc_id='$insertDocID'";
                                    //echo "update tbl_document_master set old_doc_name='$file_name',filename='$fname', doc_extn='$extn', doc_path='$storageName/$filenameEnct', uploaded_by='$user_id', doc_size='$file_size', noofpages='$pageCount', dateposted='$date' where doc_id='$_POST[docid]'";
                                    //die;
                                    $updateNew = mysqli_query($db_con, "update tbl_document_master set `$rwgetMetaName[field_name]` = '$fieldValue', doc_name='$updateDocName' where doc_id='$insertDocID'");
                                    $updateOld = mysqli_query($db_con, "update tbl_document_master set `$rwgetMetaName[field_name]` = '$fieldValue', old_doc_name='$file_name',filename='$fname', doc_extn='$extn', doc_path='$storageName/$filenameEnct', uploaded_by='$user_id', doc_size='$file_size', noofpages='$pageCount', dateposted='$date' where doc_id='$_POST[docid]'");
                                    if ($updateNew && $updateOld) {
                                        echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Updtd_Sfly'] . '");</script>';
                                    }
                                }
                            }
                        }
                    }
                } else {
                    echo '<script>taskFailed("' . basename($_SERVER[REQUEST_URI]) . '", "' . $lang['Op_Fle_upld_fld'] . '")</script>';
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
                                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'MetaData Value $fieldValue Assign in MetaData Field $rwgetMetaName[field_name] in $rwStrName[sl_name]','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                                echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Mtadta_Updted_sucsfly'] . '");</script>';
                            }
                        }
                    }

                    $i++;
                }
                mysqli_close($db_con);
            }
        }

        //for move multi files
        if (isset($_POST['movemulti'])) {
            $to = $_POST['lastMoveId'];
            $level = $_POST['lastMoveIdLevel'];
            $mutiId = $_POST['doc_id_smove_multi'];
            //$doc_id_smove_multi = explode(',', $mutiId);
            $moveToParentId = $_POST['moveToParentId'];
            $fromSlid = $_POST['sl_id_move_multi'];
            $checkDupDoc = mysqli_query($db_con, "select old_doc_name, doc_id, doc_name from tbl_document_master where doc_id in($mutiId) and doc_name='$fromSlid'") or die('Error' . mysqli_error($db_con));
            $successFlag = array();
            while ($rwcheckDupDoc = mysqli_fetch_assoc($checkDupDoc)) {
                $docdupname = $rwcheckDupDoc['old_doc_name'];
                $doc_id = $rwcheckDupDoc['doc_id'];
                $duplicate = mysqli_query($db_con, "select old_doc_name from tbl_document_master where doc_name='$to' and old_doc_name='$docdupname'") or die('Errorasds' . mysqli_error($db_con));
                if (mysqli_num_rows($duplicate) <= 0) {
                    $from_moveDocNm = mysqli_query($db_con, "select old_doc_name,doc_path from tbl_document_master where doc_id='$doc_id'") or die('Error' . mysqli_error($db_con));
                    $from_rwMoveNm = mysqli_fetch_assoc($from_moveDocNm);
                    $fromDocPath = "extract-here/" . $from_rwMoveNm['doc_path'];
                    $updateMoveDoc = mysqli_query($db_con, "update tbl_document_master set doc_name = '$to' where doc_id='$doc_id'") or die('Error' . mysqli_error($db_con));
                    $movestrgeNm = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id ='$to'") or die('Error' . mysqli_error($db_con));
                    $rwmovestrgeNm = mysqli_fetch_assoc($movestrgeNm);
                    $doc_EncryptFile = explode('/', $fromDocPath);
                    $doc_Encrypt_nm = end($doc_EncryptFile);
                    $dir_to = "extract-here/" . $rwmovestrgeNm['sl_name'];
                    if (!is_dir($dir_to)) {
                        mkdir($dir_to);
                    }
                    $dir = "extract-here/" . $rwmovestrgeNm['sl_name'];
                    $doc_Path_copy_to = $dir . "/" . $doc_Encrypt_nm;
                    $pathArray = explode('/', $doc_Path_copy_to);
                    array_shift($pathArray);
                    $db_copy_Path_to = implode('/', $pathArray);
                    copy($fromDocPath, $doc_Path_copy_to);
                    $destinationPath = $rwmovestrgeNm['sl_name'] . '/' . $doc_Encrypt_nm;
                    $sourcePath = $fromDocPath;

                    $uploadInToFTP = false;
                    if (FTP_ENABLED) {

                        $ftp = new ftp();

                        $ftp->conn("$fileserver", "$port", "$ftpUser", "$ftpPwd");

                        if ($ftp->get($sourcePath, ROOT_FTP_FOLDER . '/' . $from_rwMoveNm['doc_path'])) {

                            $uploadfile = $ftp->put(ROOT_FTP_FOLDER . '/' . $destinationPath, $sourcePath);
                            $arr = $ftp->getLogData();
                            if ($uploadfile) {
                                $uploadInToFTP = true;
                                $ftp->singleFileDelete(ROOT_FTP_FOLDER . '/' . $from_rwMoveNm['doc_path']);
                                unlink($fromDocPath);
                            } else {
                                $uploadInToFTP = false;
                                echo '<h2>Error:</h2>' . implode('<br />', $arr['error']);
                            }
                        }
                    } else {
                        $uploadInToFTP = true;
                        unlink($fromDocPath);
                    }
                    $updateDocPath = mysqli_query($db_con, "update tbl_document_master set doc_path = '$db_copy_Path_to' where doc_id='$doc_id'") or die('Error' . mysqli_error($db_con));
                    if ($updateDocPath) {
                        $successFlag[] = "success";
                    }
                } else {
                    $message = 2;
                }
            }
            if ($uploadInToFTP) {
                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`,`doc_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$to', '$doc_id','$rwFolder[sl_name] storage document $from_rwMoveNm[old_doc_name] moved to storage $rwmovestrgeNm[sl_name]','$date',null,'$host','')") or die('error : ' . mysqli_error($db_con));
                if ($log) {
                    $message = 1;
                }
            }
            if (count($successFlag)> 0) {
                echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Fls_mvd_Scsfly'] . '");</script>';
            } else if ($message == 2) {
                echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['uploaded_already'] . '");</script>';
            } else {
                echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Fld_to_mv_Fls'] . '");</script>';
            }
        }
        ?>

        <script type="text/javascript">
            $(document).ready(function () {
                $("#select_all").change(function () {
                    $(".emp_checkbox").prop("checked", $(this).prop("checked"));
                });
            });

            //Extraxt CSV 

            $(document).ready(function () {

                function exportTableToCSV($table, filename) {

                    var $rows = $table.find('tr:has(td),tr:has(th)'),
                            //var $rows = $table.filter('tr:has(:checkbox:checked)').find('tr:has(td),tr:has(th)'),

                            tmpColDelim = String.fromCharCode(11),
                            tmpRowDelim = String.fromCharCode(0),
                            colDelim = '","',
                            rowDelim = '"\r\n"',
                            csv = '"' + $rows.map(function (i, row) {
                                var $row = $(row), $cols = $row.find('td,th');

                                return $cols.map(function (j, col) {
                                    var $col = $(col), text = $col.text();

                                    return text.replace(/"/g, '""');
                                }).get().join(tmpColDelim);

                            }).get().join(tmpRowDelim)
                            .split(tmpRowDelim).join(rowDelim)
                            .split(tmpColDelim).join(colDelim) + '"',
                            csvData = 'data:application/csv;charset=utf-8,' + encodeURIComponent(csv);

                    console.log(csv);

                    if (window.navigator.msSaveBlob) {
                        window.navigator.msSaveOrOpenBlob(new Blob([csv], {type: "text/plain;charset=utf-8;"}), "csvname.csv")
                    } else {
                        $(this).attr({'download': filename, 'href': csvData, 'target': '_blank'});
                    }
                }

                $("#down").on('click', function (event) {

                    exportTableToCSV.apply(this, [$('#home-table'), 'data.csv']);

                });
            });
        </script>

        <?php
        if (isset($_POST['shareFiles'])) {
            $fromUser = $_SESSION[cdes_user_id];
            $ToUser = $_POST['userid'];
            $date = date('Y-m-d H:i:s');
            $ToUser = implode(",", $ToUser);
            $ToUser = preg_replace("/[^A-Za-z0-9, ]/", "", $ToUser);
            $shareDocIds = $_POST['shareFile'];
            $shareDocIds = explode(',', $shareDocIds);
            $myuser = explode(',', $ToUser);

            foreach ($shareDocIds as $shareId) {
                foreach ($myuser as $myuserid) {

                    $chkDocId = mysqli_query($db_con, "select * from tbl_document_share where doc_ids='$shareId' and to_ids ='$myuserid'") or die('Error in check' . mysqli_error($db_con));


                    if (mysqli_num_rows($chkDocId) > 0) {
                        echo'<script>taskFailed("storageFiles?id=' . $_GET[id] . '","' . $lang['Doc_Alrdy_Shared'] . '");</script>';
                    } else {

                        $shareFiles = mysqli_query($db_con, "INSERT INTO `tbl_document_share`(`from_id`, `to_ids`, `doc_ids`, `share_date`) VALUES ('$fromUser','$myuserid','$shareId', '$date')") or die('Error in insert share document' . mysqli_error($db_con));



                        $shareDocNm = mysqli_query($db_con, "select old_doc_name from tbl_document_master where doc_id = '$shareId'") or die('Error :' . mysqli_error($db_con));
                        while ($rwshareDocNm = mysqli_fetch_assoc($shareDocNm)) {

                            if ($shareFiles) {
                                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `doc_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null, '$shareDocIds', 'Storage Document $rwshareDocNm[old_doc_name] Shared','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                                if ($log) {
                                    $message = "Y";
                                }
                            }
                        }
                        if ($message == "Y") {
                            echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Doc_shared_Sfly'] . '");</script>';
                        } else {
                            echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Doc_nt_shared'] . '");</script>';
                        }
                    }
                }
            }
            mysqli_close($db_con);
        }

        if (isset($_POST['copyFiles'])) {
            $to = preg_replace("/[^A-Za-z0-9, ]/", "", $_POST['lastMoveId']);
            $to = mysqli_real_escape_string($db_con, $to);
            $level = preg_replace("/[^A-Za-z0-9, ]/", "", $_POST['lastMoveIdLevel']);
            $level = mysqli_real_escape_string($db_con, $level);
            $doc_ids = preg_replace("/[^A-Za-z0-9, ]/", "", $_POST['doc_ids']);
            $doc_ids = mysqli_real_escape_string($db_con, $doc_ids);
            $copyToParentId = preg_replace("/[^A-Za-z0-9, ]/", "", $_POST['copyToParentId']);
            $copyToParentId = mysqli_real_escape_string($db_con, $copyToParentId);
            $sl_id4 = preg_replace("/[^A-Za-z0-9, ]/", "", $_POST['sl_id4']);
            $sl_id4 = mysqli_real_escape_string($db_con, $sl_id4);
            $meta = mysqli_query($db_con, "select * from tbl_metadata_to_storagelevel where sl_id='$sl_id4'"); //?
            //echo "select * from tbl_metadata_to_storagelevel where sl_id='$sl_id4'";
            $fetchresult = mysqli_query($db_con, "select * from tbl_document_master where doc_id in($doc_ids) and doc_name='$sl_id4'");
            $copyLaststrg = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id = '$to'") or die('Error :' . mysqli_error($db_con));
            $rwcopyLaststrg = mysqli_fetch_assoc($copyLaststrg);
            $rowcount = mysqli_num_rows($fetchresult);

            $rowmultifield = mysqli_fetch_field($fetchresult);

            while ($rowmulticopy = mysqli_fetch_array($fetchresult)) {
                $doc_extn = $rowmulticopy['doc_extn'];
                $old_doc_name = $rowmulticopy['old_doc_name'];
                $doc_path = "extract-here/" . $rowmulticopy['doc_path'];
                $uploaded_by = $rowmulticopy['uploaded_by'];
                $doc_size = $rowmulticopy['doc_size'];

                $doc_EncryptFile = explode('/', $doc_path);
                $doc_Encrypt_nm = end($doc_EncryptFile);
                $dir_to = "extract-here/" . $rwcopyLaststrg['sl_name'];

                if (!is_dir($dir_to)) {
                    mkdir($dir_to);
                }
                $dir = "extract-here/" . $rwcopyLaststrg['sl_name'];

                $doc_Path_copy_to = $dir . "/" . $doc_Encrypt_nm;
                $pathArray = explode('/', $doc_Path_copy_to);

                array_shift($pathArray);

                $db_copy_Path_to = implode('/', $pathArray);

                copy($doc_path, $doc_Path_copy_to);
                $uploadInToFTP = false;
                if (FTP_ENABLED) {

                    $ftp = new ftp();
                    $ftp->conn("$fileserver", "$port", "$ftpUser", "$ftpPwd");
                    if ($ftp->get($doc_path, ROOT_FTP_FOLDER . '/' . $rowmulticopy['doc_path'])) {
                        $filepath = $rwcopyLaststrg['sl_name'] . '/' . $doc_Encrypt_nm;
                        $uploadfile = $ftp->put(ROOT_FTP_FOLDER . '/' . $filepath, $doc_path);
                        $arr = $ftp->getLogData();
                        if ($uploadfile) {
                            $uploadInToFTP = true;
                            unlink($doc_path);
                        } else {
                            $uploadInToFTP = false;
                            if ($arr['error'] != "") {
                                echo '<h2>Error:</h2>' . implode('<br />', $arr['error']);
                            }
                        }
                    } else {
                        $uploadInToFTP = false;
                        if ($arr['error'] != "") {
                            echo '<h2>Error:</h2>' . implode('<br />', $arr['error']);
                        }
                    }
                } else {
                    $uploadInToFTP = true;
                }
                if ($uploadInToFTP) {
                    $checkdubDocument = mysqli_query($db_con, "select old_doc_name from tbl_document_master where doc_name='$to' and old_doc_name='$old_doc_name'") or die('Error : ' . mysqli_error($db_con));
                    if (mysqli_num_rows($checkdubDocument) < 1) {

                        $sql2 = "INSERT INTO tbl_document_master SET";
                        $sql2 .= " doc_name='$to',old_doc_name='$old_doc_name',doc_extn='$doc_extn',doc_path='$db_copy_Path_to',uploaded_by='$uploaded_by',doc_size='$doc_size',dateposted='$rowmulticopy[dateposted]',noofpages='$rowmulticopy[noofpages]'";
                        while ($rwMeta = mysqli_fetch_assoc($meta)) {
                            $metan = mysqli_query($db_con, "select field_name from tbl_metadata_master where id='$rwMeta[metadata_id]'");
                            $rwMetan = mysqli_fetch_assoc($metan);

                            $field = $rwMetan['field_name'];
                            $value = $rowmulticopy[$field];
                            $sql2 .= ",`$field`='$value'";
                        }
                        $multicopyinsert = mysqli_query($db_con, $sql2)or die("Error copy" . mysqli_error($db_Con));
                        if ($multicopyinsert) {
                            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `doc_id`,`action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$to', '$rowmulticopy[doc_id]','Storage document $old_doc_name copy to Storage $rwcopyLaststrg[sl_name].','$date',null,'$host','')") or die('Error DBB: ' . mysqli_error($db_con));
                            if ($log) {
                                $message = "yes";
                            }
                        }
                    } else {
                        echo'<script>taskFailed("' . basename($_SERVER[REQUEST_URI]) . '","' . $lang['uploaded_already'] . '");</script>';
                    }
                } else {
                    $message = "no";
                }
            }
            if ($message == "yes") {
                echo'<script>taskSuccess("' . basename($_SERVER[REQUEST_URI]) . '","' . $lang['Doc_Cpy_Sfly'] . '");</script>';
            } else {
                echo'<script>taskFail("' . basename($_SERVER[REQUEST_URI]) . '","' . $lang['Document_not_copied'] . '");</script>';
            }

            mysqli_close($db_con);
        }
//Bulk Download
        if (isset($_POST['bulkDownload'])) {

            $rad = preg_replace("/[^A-Za-z0-9, ]/", "", $_POST['raddwn']);
            $rad = mysqli_real_escape_string($db_con, $rad);
            $slid = preg_replace("/[^A-Za-z0-9, ]/", "", $_POST['slid']);
            $slid = mysqli_real_escape_string($db_con, $slid);
            $reason = preg_replace("/[^A-Za-z0-9, ]/", "", $_POST['reason']);
            $reason = mysqli_real_escape_string($db_con, $reason);
            $archive_file_name = $slName . '.zip';

            $download = mysqli_query($db_con, "select doc_path,old_doc_name,doc_extn,doc_id from tbl_document_master where doc_name='$slid' and flag_multidelete=1"); // or die('Error'.mysqli_error($db_con));
            $zip = new ZipArchive();
            //create the file and throw the error if unsuccessful
            if ($zip->open($archive_file_name, ZIPARCHIVE::CREATE) !== TRUE) {
                exit("cannot open <$archive_file_name>\n");
            }
            $zippedFilePath = array();
            while ($row = mysqli_fetch_assoc($download)) {
                $docPath = $row['doc_path'];
                $file_path = 'extract-here/' . substr($docPath, 0, strrpos($docPath, "/") + 1);
                $files = substr($docPath, strrpos($docPath, "/") + 1);
                $comp_folder = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id = '$slid'") or die('Error :' . mysqli_error($db_con));
                $rwfolder = mysqli_fetch_assoc($comp_folder);
                $file1 = $row['old_doc_name'];
                //$file1 = $row['old_doc_name'] . '.' . $row['doc_extn'];
                if (FTP_ENABLED) {
                    $ftp = new ftp();
                    $ftp->conn("$fileserver", "$port", "$ftpUser", "$ftpPwd");

                    if ($ftp->get('extract-here/' . $docPath, ROOT_FTP_FOLDER . '/' . $docPath)) {

                        if ($zip->addFile($file_path . $files, $file1)) {
                            //unlink('extract-here/' .$docPath);

                            $zippedFilePath[] = 'extract-here/' . $docPath;
                        }
                    } else {
                        $arr = $ftp->getLogData();
                    }
                } else {
                    $zip->addFile($file_path . $files, $file1);
                }
            }
            if ($zip->close()) {
                if (FTP_ENABLED) {
                    foreach ($zippedFilePath as $key => $value) {

                        unlink($zippedFilePath[$key]);
                    }
                }
            }
            //then send the headers to foce download the zip file
            header("Content-type: application/zip");
            header("Content-Disposition: attachment; filename=$archive_file_name");
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `doc_id`,`action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$slid', '$row[doc_id]','Storage document $old_doc_name compress to Storage $rwfolder[sl_name] with $row[old_doc_name].','$date',null,'$host','$reason')") or die('error : ' . mysqli_error($db_con));
            header("Pragma: no-cache");
            header("Expires: 0");
            readfile("$archive_file_name");
            unlink($archive_file_name);
            exit;
            mysqli_close($db_con);
        }

        if (isset($_POST['mailFiles'])) {

            $mailto = $_POST['mailto'];
            $subject = $_POST['subject'];
            $mailbody = $_POST['mailbody'];
            $doc_ids = $_POST['mailFile'];
            $slid = base64_decode(urldecode($_GET['id']));
            $doc_path = array();
            $docIds = explode(',', $doc_ids);
            //$docdetails = mysqli_query($db_con, "select * from tbl_document_master where doc_id in($doc_ids)");
            // while ($docRow = mysqli_fetch_assoc($docdetails)) {
            // $docIds[] = $docRow['doc_id'];
//                    if (FTP_ENABLED) {
//
//                        $ftp = new ftp();
//                        $ftp->conn("$fileserver", "$port", "$ftpUser", "$ftpPwd");
//
//                        if ($ftp->get("extract-here/" . $docRow['doc_path'], ROOT_FTP_FOLDER . '/' . $docRow['doc_path'])) {
//                            $doc_path[] = "extract-here/" . $docRow['doc_path'];
//                        } else {
//                            $arr = $ftp->getLogData();
//                            if ($arr['error'] != "") {
//                                echo '<h2>Error:</h2>' . implode('<br />', $arr['error']);
//                            }
//                        }
//                    } else {
//                        $doc_path[] = "extract-here/" . $docRow['doc_path'];
//                    }
            //}
            // foreach ($userids as $key => $value) {
            // $user_id = $userids[$key];
            //$userdetails = mysqli_query($db_con, "SELECT first_name, last_name, user_email_id from tbl_user_master where user_id='$user_id'") or die('error : ' . mysqli_error($db_con));
            //$row = mysqli_fetch_assoc($userdetails);
            $username = 'User';
            //$email = $row['user_email_id'];
            require_once './mail.php';
            if (mailDocuments($projectName, $subject, $mailbody, $username, $mailto, $doc_path, $docIds)) {
                foreach ($docIds as $docId) {
                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `doc_id`,`action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$slid', '$docId','Share file with $mailto .','$date',null,'$host',NULL)") or die('error : ' . mysqli_error($db_con));
                }
                echo'<script>taskSuccess("' . $_SERVER['RESQUEST_URI'] . '","' . $lang['document_send'] . '");</script>';
            } else {
                echo'<script>taskFailed("' . $_SERVER['RESQUEST_URI'] . '","' . $lang['error_occured_mail_doc'] . '");</script>';
            }
            //}
        }
        ?>
        <!-- for add and search metaData---> 
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
                        $.ajax({url: "application/ajax/addmultimetadataStoregefile?id=" + id, success: function (result) {
                                $(wrapper).append("<div class='col-lg-12' style='margin-bottom:17px'>" + result + "<button class='remove_field btn btn-primary' title='<?= $lang['Remove'] ?>'><i class='fa fa-minus-circle' aria-hidden='true'></i></a>" + "</div>"); //add input box


                            }});

                    } else
                    {
                        alert("<?php echo $lang['No._Mor_mta_dat_avlbl']; ?>");
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
            $(".select2").select2();
            $('form').parsley();
        </script>
        <!---end add and search metadata-->
    </body>
</html>
