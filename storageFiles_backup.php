<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once './loginvalidate.php';
require_once './application/config/database.php';
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
// echo $_GET['id']; die;
//for user role
    $chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));
    $rwgetRole = mysqli_fetch_assoc($chekUsr);
// echo $rwgetRole['dashboard_mydms']; die;
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
    <!--link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" /-->
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
                                <li><a href="storage?id=<?php echo urlencode(base64_encode($slperm)); ?>">Storage Management</a></li>

                                <?php
                                parentLevel($slid, $db_con, $slperm, $level);

                                function parentLevel($slid, $db_con, $slperm, $level) {

                                    if ($slperm == $slid) {
                                        $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid'") or die('Error' . mysqli_error($db_con));
                                        $rwParent = mysqli_fetch_assoc($parent);

                                        if ($level < $rwParent['sl_depth_level']) {
                                            parentLevel($rwParent['sl_parent_id'], $db_con, $slperm, $level);
                                        }
                                    } else {
                                        $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid' and sl_parent_id='$slperm'") or die('Error' . mysqli_error($db_con));
                                        if (mysqli_num_rows($parent) > 0) {

                                            $rwParent = mysqli_fetch_assoc($parent);
                                            if ($level < $rwParent['sl_depth_level']) {
                                                parentLevel($rwParent['sl_parent_id'], $db_con, $slperm, $level);
                                            }
                                        } else {
                                            $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid'") or die('Error' . mysqli_error($db_con));
                                            $rwParent = mysqli_fetch_assoc($parent);
                                            $getparnt = $rwParent['sl_parent_id'];
                                            if ($level <= $rwParent['sl_depth_level']) {
                                                parentLevel($getparnt, $db_con, $slperm, $level);
                                            } else {
                                                //header('Location: ./index.php');
                                                header("Location: ./storage?id=" . urlencode(base64_encode($slperm)));
                                            }
                                        }
                                    }
                                    echo '<li class="active"><a href="storage?id=' . urlencode(base64_encode($rwParent['sl_id'])) . '">' . $rwParent['sl_name'] . '</a></li>';
                                }
                                ?>
                            </ol>
                        </div>
                        <div class="row">
                            <div class="box box-primary">

                                <div class="box-body">
                                    <div class="col-md-3" style="overflow: auto;">
                                        <div class="card-box">
                                            <div id="basicTree">
                                                <ul>
                                                    <?php
                                                    $perm = mysqli_query($db_con, "select * from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]' group by sl_id");
                                                    $rwPerm = mysqli_fetch_assoc($perm);
                                                    $slperm = $rwPerm['sl_id'];
                                                    ?>
                                                    <?php
                                                    storageLevelS($level, $db_con, $slid, $parentid, $slperm);
                                                    ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-9" style="padding-left: 0;">

                                        <div class="box-header with-border">
                                            <div class="btn-group pull-right m-t-0">

                                                <button type="button" class="btn btn-linkedin">Choose Action</button>
                                                <button type="button" class="btn btn-linkedin dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                                                    <span class="caret"></span>
                                                    <span class="sr-only">Toggle Dropdown</span>
                                                </button>
                                                <ul class="dropdown-menu storage" role="menu">
                                                    <?php if ($rwgetRole['export_csv'] == '1') { ?>
                                                        <li><a href="javascript:void(0)" data-toggle="modal" data-target="#export">Export CSV</a></li>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['bulk_download'] == '1') { ?>
                                                        <li><a href="javascript:void(0)" data-toggle="modal" data-target="#bulkdownload">Bulk Download Files</a></li>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['upload_doc_storage'] == '1') { ?>
                                                        <li><a href="adddocument?id=<?php echo urlencode(base64_encode($slid)); ?>">Upload Document </a></li>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['create_child_storage'] == '1') { ?>
                                                        <li><a href="javascript:void(0)" data-toggle="modal" data-target="#con-close-modal1">Create New Child</a></li>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['modify_storage_level'] == '1') { ?>
                                                        <li><a href="javascript:void(0)" data-toggle="modal" data-target="#con-close-modal-modify">Modify Storage </a></li>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['delete_storage_level'] == '1') { ?>
                                                        <li><a href="javascript:void(0)" data-toggle="modal" data-target="#con-close-modal-del">Delete Storage </a></li>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['assign_metadata'] == '1') { ?>
                                                        <li><a href="javascript:void(0)" data-toggle="modal" data-target="#con-close-modal5">Assign MetaData</a></li>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['move_storage_level'] == '1') { ?>
                                                        <li><a href="javascript:void(0)" data-toggle="modal" data-target="#con-close-modal4">Move Storage</a></li>
                                                    <?php } ?>
                                                    <li class="divider"></li>
                                                    <?php if ($rwgetRole['copy_storage_level'] == '1') { ?>
                                                        <li><a href="javascript:void(0)" data-toggle="modal" data-target="#con-close-modal6">Copy Storage</a></li>
                                                    <?php } ?>

                                                </ul>
                                            </div>   
                                            <h4 id="event_result" class="header-title" style="display: inline-block;">Selected Folder : <strong><?php echo $slName = $rwFolder['sl_name']; ?></strong></h4>  
                                        </div>
                                        <div class="col-lg-12 m-t-10" style="padding-left: 0;">
                                            <form action="searchdata">
                                                <div class="row" id="multiselect">
                                                    <div class="col-md-3">

                                                        <select  class="form-control select2" id="my_multi_select1" name="metadata[]" required>
                                                            <option disabled selected>select metadata</option>
                                                            <option value="old_doc_name">FileName</option>
                                                            <option value="noofpages">No Of Pages</option>
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
                                                        <select class="form-control" name="cond[]" required>
                                                            <option disabled selected style="background: #808080; color: #121213;">Select Condition</option>
                                                            <option <?php
                                                            if (isset($_GET['cond']) && !empty($_GET['cond']) && $_GET['cond'] == 'Equal') {
                                                                echo'selected';
                                                            }
                                                            ?>>Equal</option>
                                                            <option <?php
                                                            if (isset($_GET['cond']) && !empty($_GET['cond']) && $_GET['cond'] == 'Contains') {
                                                                echo'selected';
                                                            }
                                                            ?>>Contains</option>
                                                            <option <?php
                                                            if (isset($_GET['cond']) && !empty($_GET['cond']) && $_GET['cond'] == 'Like') {
                                                                echo'selected';
                                                            }
                                                            ?>>Like</option>
                                                            <option <?php
                                                            if (isset($_GET['cond']) && !empty($_GET['cond']) && $_GET['cond'] == 'Not Like') {
                                                                echo'selected';
                                                            }
                                                            ?>>Not Like</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="text" class="form-control" name="searchText[]" required value="<?php echo $_GET['searchText'] ?>" placeholder="enter search text here">
                                                    </div>
                                                    <input type="hidden" value="<?php echo $_GET['id']; ?>" name="id" />
                                                    <button type="submit" class="btn btn-primary " id="search"><i class="fa fa-search"></i></button>
                                                    <a href="javascript:void(0)" class="btn btn-primary" id="addfields"><i class="fa fa-plus"></i></a>
                                                </div>
                                                <div class="row">
                                                    <div class="contents col-lg-12"></div>
                                                </div> 
                                            </form>

                                            <div class="">

                                                <?php /*
                                                  if (isset($_GET['searchText'])) {
                                                  $metadata = $_GET['metadata'];
                                                  $cond = $_GET['cond'];
                                                  $searchText = $_GET['searchText'];
                                                  $searchText = mysqli_real_escape_string($db_con, $searchText);
                                                  $res = searchAllDB($searchText, $cond, $metadata, $db_con);
                                                  } */
                                                ?>	
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
                                        </div>
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
                                                $start = isset($_GET['start']) ? $_GET['start'] : '';
                                                $max_pages = ceil($foundnum / $per_page);
                                                if (!$start) {
                                                    $start = 0;
                                                }

                                                $allot = "select * from tbl_document_master $where order by old_doc_name LIMIT $start, $per_page";

                                                $allot_query = mysqli_query($db_con, $allot) or die("Error: " . mysqli_error($db_con));
                                                ?>
                                                <div class="container" >
                                                    <div class="pull-right record">
                                                        <?php echo $start + 1 ?> to <?php
                                                        if (($start + 10) > $foundnum) {
                                                            echo $foundnum;
                                                        } else {
                                                            echo ($start + 10);
                                                        };
                                                        ?> Out Of <span>Total Records: <?php echo $foundnum; ?></span>
                                                    </div>
                                                    <div class="box-body limit">
                                                        <?php
                                                        $limit = trim($_GET['limit']);

                                                        if (isset($limit) and ! empty($limit) and $limit == '') {

                                                            $rec_limit = $limit;
                                                        } else {

                                                            $rec_limit = 10;
                                                        }
                                                        $user_id = $_SESSION[cdes_user_id];
                                                        $chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um where FIND_IN_SET('$user_id', user_ids) > 0") or die('Error:' . mysqli_error($db_con));
                                                        $rwcheckUser = mysqli_fetch_assoc($chekUsr);
                                                        if ($rwcheckUser['role_id'] == 1) {
                                                            $sql = "SELECT count(doc_id) FROM  tbl_document_master where doc_name = $slid and flag_multidelete=1";
                                                        } else {
                                                            $sql = "SELECT count(doc_id) FROM  tbl_document_master where doc_name = $slid and flag_multidelete=1";
                                                        }
                                                        $retval = mysqli_query($db_con, $sql) or die('Could not get data: ' . mysqli_error($db_con));
                                                        $row = mysqli_fetch_array($retval, MYSQLI_NUM);
                                                        $rec_count = $row[0];
                                                        $maxpage = $rec_count / $rec_limit;
                                                        $maxpage = ceil($maxpage);
                                                        if (isset($_GET{'page'})) {
                                                            $page = $_GET{'page'} + 1;
                                                            $offset = $rec_limit * $page;
                                                            $i = $_GET['index'];
                                                        } else {
                                                            $page = 0;
                                                            $offset = 0;
                                                        }
                                                        $left_rec = $rec_count - ($page * $rec_limit);
                                                        $bg = '#E3EDF0'; //variable used to store alternate row colors
                                                        ?>
                                                        Show <select id="limit">
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
                                                        </select> Documents
                                                    </div>
                                                    <table class="table table-striped" >
                                                        <thead>
                                                            <tr>
                                                                <th width="51px"><input  type="checkbox" class="checkbox-primary" id="select_all"> All </th>
                                                                <th>File Name </th>
                                                                <th>File Size</th>
                                                                <th>No.of Pages</th>
                                                                <th>Uploaded By</th>
                                                                <th>Uploaded Date</th>
                                                                <th>Actions</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $n = $start + 1;
                                                            while ($file_row = mysqli_fetch_assoc($allot_query)) {
                                                                ?>
                                                                <tr class="gradeX">
                                                                    <td> 

                                                                        <input  type="checkbox" class="checkbox-primary emp_checkbox" data-doc-id="<?php echo $file_row['doc_id']; ?>">
                                                                        <?php echo $n; ?>
                                                                    </td>
                                                                    <td> <div style="overflow: hidden; max-width:200px;" title="<?php echo $file_row['old_doc_name']; ?>"><?php echo $file_row['old_doc_name']; ?></div></td>
                                                                    <td ><?php
                                                                        $size = round($file_row['doc_size'] / 1024 / 1024, 2);
                                                                        if ($size <= 0) {
                                                                            echo $file_row['doc_size'] / 1024;
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
                                                                <a href="" class="dropdown-toggle profile waves-effect waves-light" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-gear"></i></a>
                                                                <ul class="dropdown-menu pdf gearbody">
                                                                    <li> 
                                                                        <?php
                                                                        if ($file_row['checkin_checkout'] == 1) {
                                                                            if (strtolower($file_row['doc_extn']) == 'pdf') {
                                                                                ?>
                                                                                <?php if ($rwgetRole['pdf_file'] == '1') { ?>
                                                                                    <a href="flipflop-viewer?i=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])) ?>&i=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>" id="fancybox-inner" class="pdfview" target="_blank">
                                                                                        <i class="ti-book" style="font-size: 18px;"></i></a>

                                                                                    <a href="viewer?id=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&i=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>" id="fancybox-inner" class="pdfview" target="_blank">
                                                                                        <i class="fa fa-file-pdf-o"></i></a>
                                                                                <?php } ?>
                                                                                <!--for tool tip on pdf-->   
                                                                                <?php if ($rwgetRole['pdf_annotation'] == '1') { ?>
                                                                                    <a href="anott/index?id=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&id1=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>&pn=1" class="pdfview" target="blank">  <i class="fa fa fa-file-text-o"></i></a>
                                                                                    <?php
                                                                                }
                                                                            } else if (strtolower($file_row['doc_extn']) == 'jpg' || strtolower($file_row['doc_extn']) == 'png' || strtolower($file_row['doc_extn']) == 'gif') {
                                                                                ?>
                                                                                <a href="#" data-toggle="modal" data-target="#full-width-modal" id="showPic" data="extract-here/<?php echo $file_row['doc_path']; ?>" >
                                                                                    <?php if ($rwgetRole['image_file'] == '1') { ?>
                                                                                        <i class="fa fa-file-image-o"></i> Image</a>
                                                                                <?php } ?>
                                                                            <?php } else if (strtolower($file_row['doc_extn']) == 'tif' || strtolower($file_row['doc_extn']) == 'tiff') { ?>
                                                                                <a href="file?uid=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&file=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>" target="_blank" >
                                                                                    <?php if ($rwgetRole['tif_file'] == '1') { ?>
                                                                                        <i class="fa fa-picture-o"></i> Tiff File
                                                                                    </a>
                                                                                <?php } ?>

                                                                            <?php } else if (strtolower($file_row['doc_extn']) == 'xlsx') {
                                                                                ?>
                                                                                <a href="xlsx?uid=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&file=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>" target="_blank">
                                                                                    <?php if ($rwgetRole['excel_file'] == '1') { ?>
                                                                                        <i class="fa fa-file-excel-o"></i> Execl file</a>
                                                                                <?php } ?>

                                                                            <?php } else if (strtolower($file_row['doc_extn']) == 'xls') {
                                                                                ?>
                                                                                <a href="xls?uid=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&file=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>" target="_blank">
                                                                                    <?php if ($rwgetRole['excel_file'] == '1') { ?>
                                                                                        <i class="fa fa-file-excel-o"></i> Execl file</a>
                                                                                <?php } ?>

                                                                            <?php } else if (strtolower($file_row['doc_extn']) == 'doc' || strtolower($file_row['doc_extn']) == 'docx') { ?>
                                                                                <a href="docx?uid=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&file=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>" target="_blank">
                                                                                    <?php if ($rwgetRole['doc_file'] == '1') { ?>
                                                                                        <i class="fa fa-file-word-o"></i> Word file</a>
                                                                                <?php } ?>

                                                                            <?php } else if (strtolower($file_row['doc_extn']) == 'mp3' || strtolower($file_row['doc_extn']) == 'wav') { ?>
                                                                                                                                                                                                                                                                                                                                                                                                                              <!--a class="" href="#modal-audio" data-uk-modal=""><i class="fa fa-music"></i> </a-->
                                                                                <a href="javascript:void(0)" data-toggle="modal" data-target="#modal-audio" data="<?php echo $file_row['doc_id']; ?>" id="audio">
                                                                                    <?php if ($rwgetRole['audio_file'] == '1') { ?>
                                                                                        <i class="fa fa-music"></i> Audio </a>
                                                                                <?php } ?>

                                                                            <?php } else if (strtolower($file_row['doc_extn']) == 'mp4' || strtolower($file_row['doc_extn']) == '3gp') { ?>
                                                                                <a href="javascript:void(0)" data-toggle="modal" data-target="#modal-video" data="<?php echo $file_row['doc_id']; ?>" id="video">
                                                                                    <?php if ($rwgetRole['video_file'] == '1') { ?>
                                                                                        <i class="fa fa-video-camera"></i> Video</a>
                                                                                <?php } ?>                                                                        
                                                                            <?php } else {
                                                                                ?>
                                                                                <a href="extract-here/<?php echo $file_row['doc_path']; ?>" id="fancybox-inner" target="_blank" download> <i class="fa fa-download"></i> <?php echo $file_row['old_doc_name']; ?>
                                                                                </a>
                                                                            <?php } ?>
                                                                        </li>

                                                                        <?php if ($rwgetRole['file_edit'] == '1') { ?>
                                                                            <li> <a href="javascript:void(0)" data="metaData<?php echo $n; ?>" id="viewMeta"><i class="fa fa-eye"></i> View MetaData</a></li>
                                                                        <?php } if ($rwgetRole['file_delete'] == '1') { ?>
                                                                            <li><a href="javascript:void(0)" data-toggle="modal" data-target="#con-close-modal2" id="removeRow" data="<?php echo $file_row['doc_id']; ?>"><i class="fa fa-trash-o"></i> Delete </a></li>
                                                                        <?php } ?>
                                                                        <?php if ($rwgetRole['workflow_initiate_file'] == '1' || $rwgetRole['initiate_file'] == '1') { ?>
                                                                            <li> <a href="javascript:void(0)" data-toggle="modal" data-target="#assign-workflow" id="moveToWf" data="<?php echo $file_row['doc_id']; ?>"><i class="fa fa-plus"></i> Workflow</a></li>
                                                                        <?php } ?>
                                                                        <?php if ($rwgetRole['checkin_checkout'] == '1') { ?>
                                                                            <li><a href="javascript:void(0)" id="checkout" data="<?php echo $file_row['doc_id']; ?>"><i class="fa fa-sign-out"></i> Check Out</a></li>
                                                                            <?php
                                                                        }
                                                                    } else {
                                                                        ?>
                                                                        <?php if ($rwgetRole['checkin_checkout'] == '1') { ?>
                                                                            <li> <a href="javascript:void(0)" data-toggle="modal" data-target="#editmetadata" id="editMdata" data="<?php echo $file_row['doc_id']; ?>"><i class="fa fa-sign-in"></i> Check IN</a></li>
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
                                                                                    ?>

                                                                                    <?php if (strtolower($rwView['doc_extn']) == 'pdf') { ?>

                                                                                        <a href="viewer?id=<?php echo $_SESSION['cdes_user_id']; ?>&i=<?php echo urlencode(base64_encode($rwView['doc_id'])); ?>" id="fancybox-inner" target="_blank">
                                                                                            <?php echo $rwView['old_doc_name']; ?>
                                                                                        </a>

                                                                                    <?php } else if (strtolower($rwView['doc_extn']) == 'jpg' || strtolower($rwView['doc_extn']) == 'png' || strtolower($rwView['doc_extn']) == 'gif') { ?>
                                                                                        <a href="#" data-toggle="modal" data-target="#full-width-modal" id="showPic" data="extract-here/<?php echo $rwView['doc_path']; ?>" >
                                                                                            <?php echo $rwView['old_doc_name']; ?>
                                                                                        </a>
                                                                                    <?php } else if (strtolower($rwView['doc_extn']) == 'tif' || strtolower($rwView['doc_extn']) == 'tiff') { ?>
                                                                                        <a href="file?uid=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&file=<?php echo urlencode(base64_encode($rwView['doc_id'])); ?>" target="_blank" >
                                                                                            <?php if ($rwgetRole['tif_file'] == '1') { ?>
                                                                                                <i class="fa fa-picture-o"></i>
                                                                                            </a>
                                                                                        <?php } ?>

                                                                                    <?php } else if (strtolower($rwView['doc_extn']) == 'xlsx') {
                                                                                        ?>
                                                                                        <a href="xlsx?uid=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&file=<?php echo urlencode(base64_encode($rwView['doc_id'])); ?>" target="_blank">
                                                                                            <?php if ($rwgetRole['excel_file'] == '1') { ?>
                                                                                                <i class="fa fa-file-excel-o"></i></a>
                                                                                        <?php } ?>

                                                                                    <?php } else if (strtolower($rwView['doc_extn']) == 'xls') {
                                                                                        ?>
                                                                                        <a href="xls?uid=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&file=<?php echo urlencode(base64_encode($rwView['doc_id'])); ?>" target="_blank">
                                                                                            <?php if ($rwgetRole['excel_file'] == '1') { ?>
                                                                                                <i class="fa fa-file-excel-o"></i></a>
                                                                                        <?php } ?>

                                                                                    <?php } else if (strtolower($rwView['doc_extn']) == 'doc' || strtolower($rwView['doc_extn']) == 'docx') { ?>
                                                                                        <a href="docx?uid=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&file=<?php echo urlencode(base64_encode($rwView['doc_id'])); ?>" target="_blank">
                                                                                            <?php if ($rwgetRole['doc_file'] == '1') { ?>
                                                                                                <i class="fa fa-file-word-o"></i></a>
                                                                                        <?php } ?>
                                                                                    <?php } else if (strtolower($rwView['doc_extn']) == 'mp3') { ?>
                                                                                        <a href="javascript:void(0)" data-toggle="modal" data-target="#modal-audio" data="<?php echo $rwView['doc_id']; ?>" id="audio">
                                                                                            <?php echo $rwView['old_doc_name']; ?>
                                                                                        </a>
                                                                                    <?php } else if (strtolower($rwView['doc_extn']) == 'mp4') { ?>
                                                                                        <a href="javascript:void(0)" data-toggle="modal" data-target="#modal-video" data="<?php echo $rwView['doc_id']; ?>" id="video">
                                                                                            <?php echo $rwView['old_doc_name']; ?>
                                                                                        </a>

                                                                                    <?php } else {
                                                                                        ?>
                                                                                        <a href="extract-here/<?php echo $rwView['doc_path']; ?>" id="fancybox-inner" target="_blank" download>
                                                                                            <?php echo $rwView['old_doc_name']; ?>
                                                                                        </a>
                                                                                        <?php
                                                                                    }
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
                                                                                $rwMeta = mysqli_fetch_array($meta);
                                                                                if (!empty($rwMeta[$rwgetMetaName['field_name']])) {
                                                                                    if ($rwgetMetaName['field_name'] == 'noofpages' || $rwgetMetaName['field_name'] == 'filename') {
                                                                                        
                                                                                    } else {
                                                                                        echo "<label>" . $rwgetMetaName['field_name'] . "</label> : ";

                                                                                        echo $rwMeta[$rwgetMetaName['field_name']];
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
                                                        </tbody>
                                                        <tr>
                                                            <td colspan="50">
                                                                <ul class="delete_export">
                                                                    <input type="hidden" name="slid" id="slid" value="<?php echo $slid; ?>">
                                                                    <input type="hidden" name="sty" id="sty" value="<?php echo $_GET['id']; ?>">
                                                                    <?php if ($rwgetRole['file_delete'] == '1') { ?>
                                                                        <li><button id="del_file" class="rows_selected btn btn-danger fa fa-trash-o" data-toggle="modal" data-target="#del_send_to_recycle" title="Delete Selected Files"></button></li>
                                                                    <?php } if ($rwgetRole['export_csv'] == '1') { ?>
                                                                        <li><button class="btn btn-primary fa fa-download" id="export4"  data-toggle="modal"  data-target="#multi-csv-export-model" title="Export MetaData of Selected Files"></button></li>
                                                                    <?php } if ($rwgetRole['move_file'] == '1') { ?>
                                                                        <li><button id="move_multi" class="rows_selected btn btn-primary fa fa-share-square" data-toggle="modal" data-target="#move-selected-files" title="Move Selected files to other Storage"></button></li>
                                                                    <?php } if ($rwgetRole['copy_file'] == '1') { ?>
                                                                        <li><button class="rows_selected btn btn-primary fa fa-copy" id="copyFiles" data-toggle="modal" data-target="#copy-selected-files" title="Copy Selected files to other Storage"> </button></li>
                                                                    <?php } if ($rwgetRole['share_file'] == '1') { ?>
                                                                        <li><button class="rows_selected btn btn-primary fa fa-share-alt" id="shareFiles" data-toggle="modal" data-target="#share-selected-files" title="Share Selected files"></button></li>
                                                                    <?php } ?>
                                                                </ul>
                                                            </td>
                                                        </tr>
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
                                                                echo " <li><a href='?id=$_GET[id]&start=$prev'>Prev</a> </li>";
                                                            else
                                                                echo " <li class='disabled'><a href='javascript:void(0)'>Prev</a> </li>";
                                                            //pages 
                                                            if ($max_pages < 7 + ($adjacents * 2)) {   //not enough pages to bother breaking it up
                                                                $i = 0;
                                                                for ($counter = 1; $counter <= $max_pages; $counter++) {
                                                                    if ($i == $start) {
                                                                        echo " <li><a href='?id=$_GET[id]&start=$i'><b>$counter</b></a> </li>";
                                                                    } else {
                                                                        echo "<li><a href='?id=$_GET[id]&start=$i'>$counter</a></li> ";
                                                                    }
                                                                    $i = $i + $per_page;
                                                                }
                                                            } elseif ($max_pages > 5 + ($adjacents * 2)) {    //enough pages to hide some
                                                                //close to beginning; only hide later pages
                                                                if (($start / $per_page) < 1 + ($adjacents * 2)) {
                                                                    $i = 0;
                                                                    for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                                                                        if ($i == $start) {
                                                                            echo " <li><a href='?id=$_GET[id]&start=$i'><b>$counter</b></a></li> ";
                                                                        } else {
                                                                            echo "<li> <a href='?id=$_GET[id]&start=$i'>$counter</a> </li>";
                                                                        }
                                                                        $i = $i + $per_page;
                                                                    }
                                                                }
                                                                //in middle; hide some front and some back
                                                                elseif ($max_pages - ($adjacents * 2) > ($start / $per_page) && ($start / $per_page) > ($adjacents * 2)) {
                                                                    echo " <li><a href='?id=$_GET[id]&start=0'>1</a></li> ";
                                                                    echo "<li><a href='?id=$_GET[id]&start=$per_page'>2</a></li>";
                                                                    echo "<li><a href='javascript:void(0)'>...</a></li>";

                                                                    $i = $start;
                                                                    for ($counter = ($start / $per_page) + 1; $counter < ($start / $per_page) + $adjacents + 2; $counter++) {
                                                                        if ($i == $start) {
                                                                            echo " <li><a href='?id=$_GET[id]&start=$i'><b>$counter</b></a></li> ";
                                                                        } else {
                                                                            echo " <li><a href='?id=$_GET[id]&start=$i'>$counter</a> </li>";
                                                                        }
                                                                        $i = $i + $per_page;
                                                                    }
                                                                }
                                                                //close to end; only hide early pages
                                                                else {
                                                                    echo "<li> <a href='?id=$_GET[id]&start=0'>1</a> </li>";
                                                                    echo "<li><a href='?id=$_GET[id]&start=$per_page'>2</a></li>";
                                                                    echo "<li><a href='javascript:void(0)'>...</a></li>";

                                                                    $i = $start;
                                                                    for ($counter = ($start / $per_page) + 1; $counter <= $max_pages; $counter++) {
                                                                        if ($i == $start) {
                                                                            echo " <li><a href='?id=$_GET[id]&start=$i'><b>$counter</b></a></li> ";
                                                                        } else {
                                                                            echo "<li> <a href='?id=$_GET[id]&start=$i'>$counter</a></li> ";
                                                                        }
                                                                        $i = $i + $per_page;
                                                                    }
                                                                }
                                                            }
                                                            //next button
                                                            if (!($start >= $foundnum - $per_page))
                                                                echo "<li><a href='?id=$_GET[id]&start=$next'>Next</a></li>";
                                                            else
                                                                echo "<li class='disabled'><a href='javascript:void(0)'>Next</a></li>";
                                                            ?>
                                                        </ul>
                                                        <?php
                                                    }
                                                    echo "</center>";
                                                    ?>
                                                </div>
                                            </div>
                                            <?php
                                        }else {

                                            echo'<div style="text-align:center;"><h4 style="color:red;">File Not found</h4></div>';
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
            <div id="del_send_to_recycle" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog"> 
                    <div class="modal-content"> 
                        <form method="post" >
                            <div class="modal-header"> 
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h4 class="modal-title" style="display:none;" id="hid"> <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Here's a message!</h4>
                                <h4 class="modal-title" id="confirm"> Are You Sure?</h4> 
                            </div>
                            <div class="modal-body">
                                <span id="errmessage" style="display:none;"> <h5 class="text-alert">Please select files for Delete.</h5></span>
                                <label class="text-danger" id="hide">Are you sure want to Delete this<?php if ($rwgetRole['role_id'] == 1) { ?>  <strong>Document Permanently.</strong><?php } ?>?</label>
                            </div> 
                            <div class="modal-footer">
                                <input type="hidden" id="sl_id1" name="sl_id1">
                                <input type="hidden" id="reDel" name="DelFile">
                                <!--  <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button> -->
                                <?php
                                if ($rwgetRole['role_id'] == 1) {
                                    ?>
                                    <button type="submit" id="yes" name="Delmultiple" class="btn btn-danger" value="Yes"> <i class="fa fa-trash-o"></i> Yes</button>
                                    <?php
                                }
                                ?>
                                <button type="submit" id="no" name="Delmultiple" class="btn btn-danger"> <i class="fa fa-recycle"></i>
                                    <?php
                                    if ($rwgetRole['role_id'] == 1) {
                                        echo 'Recycle';
                                    } else {
                                        echo "Delete";
                                    }
                                    ?>

                                </button> 
                            </div>
                        </form>

                    </div> 
                </div>
            </div>
            <div id="csv_export_model" class="modal">
                <div class="modal-dialog">
                    <div class="modal-content"> 
                        <div class="modal-header"> 
                            <h4 class="modal-title"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Here's a message!</h4> 
                        </div>
                        <div class="modal-body">
                            <h5 class="text-alert">Please select Files for Export CSV.</h5>
                        </div>
                        <div class="modal-footer"> 
                            <button onclick="document.getElementById('csv_export_model').style.display = 'none'" class="btn btn-default waves-effect">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- /.modal --> 
            <!--share files with users-->
            <div id="share-selected-files" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog"> 
                    <div class="modal-content"> 
                        <div class="modal-header"> 
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                            <h4 class="modal-title" id="shr"> <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Here's a message!</h4>
                            <h4 class="modal-title" style="display:none;" id="stitle"> Share Documents With</h4> 
                        </div>
                        <div id="unseshare">
                            <div class="modal-body">
                                <h5 class="text-alert">Please select Files for Share.</h5>
                            </div>
                            <div class="modal-footer"> 
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button> 
                            </div>
                        </div>
                        <div id="selected2">
                            <form method="post" >
                                <div class="modal-body" >
                                    <div class="form-group">
                                        <label>Select User</label>
                                        <select class="select2 select2-multiple" multiple data-placeholder="Select Users" name="userid[]" required>
                                            <?php
                                            $sameGroupIDs = array();
                                            $group = mysqli_query($db_con, "select * from tbl_bridge_grp_to_um where find_in_set('$_SESSION[cdes_user_id]',user_ids)") or die('Error' . mysqli_error($db_con));
                                            while ($rwGroup = mysqli_fetch_assoc($group)) {
                                                $sameGroupIDs[] = $rwGroup['user_ids'];
                                            }
                                            $sameGroupIDs = array_unique($sameGroupIDs);
                                            sort($sameGroupIDs);
                                            $sameGroupIDs = implode(',', $sameGroupIDs);
                                            echo "select * from tbl_user_master WHERE and user_id in($sameGroupIDs)";
                                            $user = mysqli_query($db_con, "select * from tbl_user_master where user_id in($sameGroupIDs)");
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
                                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>

                                    <button type="submit" name="shareFiles" class="btn btn-primary"> <i class="fa fa-share-alt"></i> Share</button>

                                    </button> 
                                </div>
                            </form>
                        </div>
                    </div> 
                </div>
            </div><!-- /.modal --> 
            <!---assign meta-data model start ---->
            <div id="con-close-modal5" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog"> 
                    <div class="modal-content"> 
                        <div class="modal-header"> 
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                            <h4 class="modal-title">Assign Meta-Data Fields to <strong><?php echo $rwFolder['sl_name']; ?></strong></h4> 
                        </div> 

                        <form action="#" data-parsley-validate novalidate method="post" enctype="multipart/form-data">
                            <div class="modal-body row">

                                <div class="col-md-12 shiv metaa">
                                    <span><strong>Field Select:</strong></span>
                                    <strong style="margin-left: 113px;">Field Assigned:</strong>
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
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button> 
                                <button class="btn btn-primary waves-effect waves-light" type="submit" name="assignMeta">Submit</button>
                            </div>
                        </form>

                    </div> 
                </div>
            </div>
            <!--ends assign-meta-data modal --> 
            <?php require_once './application/pages/footer.php'; ?>
            <!-- Right Sidebar -->
            <?php require_once './application/pages/rightSidebar.php'; ?>
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

            <!-- for searchable select-->
            <script type="text/javascript">
                                $(document).ready(function () {

                                    $(".select2").select2();
                                });
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

                        $("#editmetadata .modal-title").html("Update Meta Data of File: <strong>" + name + "</strong>");
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
                    $('form').parsley();
                });
                $(".select2").select2();

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
            <div id="multi-csv-export-model" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog"> 
                    <div class="modal-content"> 
                        <div class="modal-header"> 
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="close" style="display:none;">×</button>
                            <h4 class="modal-title" id="unexport"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Here's a message!</h4>
                            <!--<h4 class="modal-title" style="display:none;" id="export_title"> Export Selected Rows </h4>--> 
                        </div>
                        <div id="export_unselected" style="display:none;">
                            <div class="modal-body">
                                <h5 class="text-alert"> Please select Files for Export.</h5>
                            </div>

                            <div class="modal-footer"> 
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button> 
                            </div>
                        </div>
                        <div id="export_selected">
                            <div class="modal-header"> 
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                                <h4 class="modal-title">Export Selected Data</h4> 
                            </div> 

                            <form action="multi_data_export" data-parsley-validate novalidate method="post" enctype="multipart/form-data">
                                <div class="modal-body row">

                                    <div class="col-md-12 shiv metaa">
                                        <span><strong>Select Files for Export Format:</strong></span>

                                        <select  class="multi-select" id="my_multi_select1" name="select_Fm">
                                            <option value="csv">Csv</option>
                                            <option value="excel">Excel</option>
                                            <option value="pdf">Pdf</option> 
                                            <option value="word">Word</option>
                                        </select>

                                    </div>
                                    <input type="hidden" name="export_doc_ids" id="export_doc_ids" value="">
                                </div>
                                <div class="modal-footer">
                                    <input type="hidden" value="<?php echo base64_decode(urldecode($_GET['id'])); ?>" name="id">
                                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button> 
                                    <button class="btn btn-primary waves-effect waves-light fa fa-download" type="submit" name="exportData"> Export</button>
                                </div>
                            </form>
                        </div>
                    </div> 
                </div>
            </div>
            <!--for audio model-->
            <div id="modal-audio" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h4 class="modal-title" id="myModalLabel">Play/Download Audio</h4>
                        </div>
                        <div id="foraudio">

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>

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
                            <h4 class="modal-title" id="myModalLabel">Play/Download video</h4>
                        </div>
                        <div  id="videofor">


                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>

                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
            <!--modify starts-->
            <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog modal-lg"> 
                    <div class="modal-content"> 
                        <form method="post" >
                            <div class="modal-header"> 
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                                <h4 class="modal-title">Update Your file</h4> 
                            </div>
                            <div class="modal-body" id="modalModify">

                            </div> 
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button> 
                                <button type="submit" name="editFileName" class="btn btn-primary waves-effect waves-light">Save changes</button> 
                            </div>
                        </form>

                    </div> 
                </div>
            </div><!-- /.modal -->
            <div id="con-close-modal-modify" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog"> 
                    <div class="modal-content"> 
                        <div class="modal-header"> 
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                            <h4 class="modal-title">Modify Storage Level</h4> 
                        </div> 
                        <form method="post">
                            <div class="modal-body">
                                <input class="form-control" name="modify_slname" value="<?php echo $rwFolder['sl_name']; ?>" required>
                            </div>
                            <div class="modal-footer"> 
                                <input value="<?php echo $rwFolder['sl_id']; ?>" name="modi" type="hidden" >
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button> 
                                <input type="submit" name="update" class="btn btn-primary" value="Save Changes">
                            </div>
                        </form>
                    </div> 
                </div>
            </div><!-- /.modal -->  
            <!--start delete model-->
            <div id="con-close-modal2" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog"> 
                    <div class="modal-content"> 
                        <div class="modal-header"> 
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                            <h4 class="modal-title">Delete Document</h4> 
                        </div> 
                        <form method="post">
                            <div class="modal-body">
                                <p style="color: red;">Are you sure that you want to delete this <strong>Document</strong></p>
                            </div>
                            <div class="modal-footer"> 
                                <input type="hidden" id="uid" name="uid">
                                <?php
                                if ($rwgetRole['role_id'] == 1) {
                                    ?>
                                    <button type="submit" id="yes" name="deleteDoc" class="btn btn-danger" value="Yes"> <i class="fa fa-trash-o"></i> Delete</button>
                                    <?php
                                }
                                ?>
                                <button type="submit" id="no" name="deleteDoc" class="btn btn-danger"> <i class="fa fa-recycle"></i>
                                    <?php
                                    if ($rwgetRole['role_id'] == 1) {
                                        echo 'Recycle';
                                    } else {
                                        echo "Delete";
                                    }
                                    ?>

                                </button> 
                            </div>
                        </form>
                    </div> 
                </div>
            </div><!--ends delete modal -->
            <!--start delete model-->
            <div id="con-close-modal21" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog"> 
                    <div class="modal-content"> 
                        <div class="modal-header"> 
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                            <h4 class="modal-title">Delete Document</h4> 
                        </div> 
                        <form method="post">
                            <div class="modal-body">
                                <p style="color: red;">Are you sure that you want to delete this <strong>Document</strong></p>
                            </div>
                            <div class="modal-footer"> 
                                <input type="hidden" id="uid" name="uid">
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                <input type="submit" name="deleteDoc" class="btn btn-danger" value="Delete">
                            </div>
                        </form>
                    </div> 
                </div>
            </div><!--ends delete modal -->
            <!--start delete Version of Document model-->
            <div id="deleteVersion" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog"> 
                    <div class="modal-content"> 
                        <div class="modal-header"> 
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                            <h4 class="modal-title">Delete Version of Document</h4> 
                        </div> 
                        <form method="post">
                            <div class="modal-body">
                                <p style="color: red;">Are you sure that you want to delete this version of <strong>Document</strong>. this document will be deleted permanently.</p>
                            </div>
                            <div class="modal-footer"> 
                                <input type="hidden" id="docid" name="docid">
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                <input type="submit" name="deleteVersionDoc" class="btn btn-danger" value="Delete">
                            </div>
                        </form>
                    </div> 
                </div>
            </div><!--ends delete modal -->
            <div id="full-width-modal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h4 class="modal-title" id="myLargeModalLabel">Image Viewer</h4>
                        </div>
                        <div class="modal-body">
                            <div id="Display"></div>
                        </div>
                        <div class="modal-footer">

                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>

            </div>

            <!---assign workflow---->
            <div id="assign-workflow" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h4 class="modal-title">Assign in Work flow</h4> 
                        </div>
                        <form method="post" class="form-inline" id="wfasign">
                            <div class="modal-body">
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <label>Assign To:</label>
                                        <select class="form-control" class="selectpicker" data-live-search="true" id="wfid" data-style="btn-white" style="" name="wfid">
                                            <option selected disabled style="background: #808080; color: #121213;">Select Workflow</option>
                                            <?php
                                            $WorkflwGet = mysqli_query($db_con, "select * from tbl_workflow_master") or die('Error in getWorkflw Assign:' . mysqli_error($db_con));
                                            while ($rwWorkflwGet = mysqli_fetch_assoc($WorkflwGet)) {
                                                ?> 
                                                <option value="<?php echo $rwWorkflwGet['workflow_id']; ?>" name="wrkname"><?php echo $rwWorkflwGet['workflow_name']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer"> 
                                <input type="hidden" id="mTowf" name="mTowf">
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button> 
                                <input type="submit" name="assignTo" class="btn btn-primary" value="Submit" >
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
            <div id="editmetadata" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog modal-lg"> 
                    <div class="modal-content"> 
                        <form method="post" enctype="multipart/form-data">
                            <div class="modal-header"> 
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                                <h4 class="modal-title">Edit MetaData</h4> 
                            </div>
                            <div class="modal-body" id="modalModifyMvalue">
                                <img src="assets/images/load.gif" alt="load" class="img-responsive center-block" width="50px"/>
                            </div> 
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button> 
                                <button type="submit" name="editMetaValue" class="btn btn-primary">Save </button> 
                            </div>
                        </form>

                    </div> 
                </div>
            </div>

            <!---Create child model start ---->
            <div id="con-close-modal1" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog"> 
                    <div class="modal-content"> 
                        <div class="modal-header"> 
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                            <h4 class="modal-title">Add New Child to <b><?php echo $rwFolder['sl_name']; ?></b></h4> 
                        </div> 
                        <form method="post">
                            <div class="modal-body">
                                <input class="form-control" name="create_child" placeholder="Create New Child">
                            </div>
                            <div class="modal-footer"> 
                                <input value="<?php echo $rwFolder['sl_id']; ?>" name="add_child" type="hidden" >
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button> 
                                <input type="submit" name="add_storage" class="btn btn-primary" value="CREATE CHILD">
                            </div>
                        </form>
                    </div> 
                </div>
            </div>
            <!--ends Create child modal --> 
            <!--start delete model-->
            <div id="con-close-modal-del" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog"> 
                    <div class="modal-content"> 
                        <div class="modal-header"> 
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                            <h4 class="modal-title">Delete Storage</h4> 
                        </div> 
                        <form method="post">
                            <div class="modal-body">
                                <p style="color: red;">Are you sure that you want to delete <strong><?php echo $rwFolder['sl_name']; ?></strong> Folder and their Sub-folder?</p>
                            </div>
                            <div class="modal-footer"> 
                                <input value="<?php echo $rwFolder['sl_id']; ?>" name="delsl" type="hidden" >
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                <input type="submit" name="deleted" class="btn btn-danger" value="Delete">
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
            <div id="con-close-modal4" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog"> 
                    <div class="modal-content"> 
                        <div class="modal-header"> 
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                            <h4 class="modal-title">Move Storage Level</h4> 
                        </div> 
                        <form method="post" class="form-inline">
                            <div class="modal-body">
                                <div class="form-group">
                                    <?php
                                    $moveFolderName = mysqli_query($db_con, "select * from tbl_storage_level where sl_id = $slid") or die('Error in move folder name: ' . mysqli_error($db_con));
                                    $rwmoveFolderName = mysqli_fetch_assoc($moveFolderName);
                                    ?>     
                                    <label>Move Folder/File: </label>  <label> <?php echo $rwmoveFolderName['sl_name']; ?></label>
                                    <br><br>
                                    <div class="col-md-12">
                                        <label> Move To: &nbsp;</label>
                                        <select class="form-control" name="moveToParentId" id="parentMoveLevel">

                                            <option selected disabled style="background: #808080; color: #121213;">Select Storage Level</option>

                                            <?php
                                            $storeName = mysqli_query($db_con, "select * from tbl_storage_level where sl_depth_level= '0' AND sl_id != '$slid'") or die('Error in move store: ' . mysqli_error($db_con));

                                            while ($rwstoreName = mysqli_fetch_assoc($storeName)) {
                                                ?>
                                                <option value="<?php echo $rwstoreName['sl_id']; ?>"><?php echo $rwstoreName['sl_name']; ?></option>
                                            <?php } ?>
                                        </select>
                                        <br>            
                                        <div class="row">
                                            <div class="col-md-3"></div>

                                            <div class="col-md-9">
                                                <span class="" id="child">

                                                </span>

                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer"> 
                                <input value="<?php echo $rwFolder['sl_id']; ?>" name="modi" type="hidden" >
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button> 
                                <input type="submit" name="move" class="btn btn-primary" value="Move Storage">
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
            <div id="con-close-modal6" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog modal-lg"> 

                    <div class="modal-content"> 

                        <div class="modal-header"> 
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                            <h4 class="modal-title">Copy Storage</h4> 
                        </div> 
                        <script type="text/javascript" src="./assets/jsCustom/selectcheckbox.js"></script>
                        <form method="post" class="form-inline">
                            <div class="modal-body">

                                <div class="row">
                                    <div class="col-md-12">

                                        <div class="col-md-6 form-group">
                                            <label>Copy Folders:</label>
                                            /<input type="text" readonly id="tocopyfolder" name="toCopyFolder" class="form-control" value="<?php echo $tocpyFolder = $rwFolder['sl_name']; ?>">
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <p class="text-danger" id="error"></p>
                                        </div>
                                        <div class="clearfix"></div>

                                        <div class="col-md-6 form-group">
                                            <label> Copy To: &nbsp;</label>
                                            <select class="form-control" name="moveToParentId" id="parentCopyLevel" style="width: 100%">

                                                <option selected style="background: #808080; color: #121213;">Select Storage Level</option>

                                                <?php
                                                $storeName = mysqli_query($db_con, "select * from tbl_storage_level where sl_depth_level= '0' AND sl_id != '$slid'") or die('Error in move store: ' . mysqli_error($db_con));

                                                $rwstoreName = mysqli_fetch_assoc($storeName)
                                                ?>
                                                <option value="<?php echo $rwstoreName['sl_id']; ?>"><?php echo $rwstoreName['sl_name']; ?></option>
                                            </select>
                                        </div>
                                        <div class="clearfix"></div>

                                        <div class="col-md-6 form-group">
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

            <div id="export" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog modal-lg"> 
<!--                    <span id="errmessage" style="color:red;display:none;">Please select Files for Delete.</span>-->
                    <div class="modal-content"> 

                        <div class="modal-header"> 
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                            <h4 class="modal-title">Export CSV</h4>
                        </div> 
                        <script type="text/javascript" src="./assets/jsCustom/selectcheckbox.js"></script>
                        <form method="post" class="form-inline" action="export.php">
                            <div class="modal-body">

                                <div class="row">
                                    <div class="col-md-12">

                                        <div class="col-md-6 form-group">
                                            <label>All Files in selected folder</label>

                                        </div>
                                        <div class="col-md-6 form-group">

                                            <input type="radio" name="radExp" class="form-control radio" value="all" required>
                                        </div>

                                        <div class="clearfix"></div>

                                        <div class="col-md-6 form-group">
                                            <label></label>                                       
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>      
                                <div class="clearfix"></div>
                            </div>
                            <div class="modal-footer"> 
                                <input value="<?php echo $slid; ?>" name="slid" type="hidden" >
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button> 
                                <input type="submit" name="startExport" class="btn btn-primary" value="Start Export">
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
                <div id="bulkdownload" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog"> 
                        <div class="modal-content"> 
                            <div class="modal-header"> 
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                                <h4 class="modal-title">Downloads All Files of selected folder only</h4>
                            </div> 
                            <script type="text/javascript" src="./assets/jsCustom/selectcheckbox.js"></script>
                            <form method="post" class="form-inline">
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <textarea class="form-control" name="reason" cols="65" rows="5" placeholder="Write Reason for Downloding files..." required></textarea>
                                            </div>
                                        </div>
                                    </div> 
                                </div>
                                <div class="modal-footer"> 
                                    <input value="<?php echo $slid; ?>" name="slid" type="hidden" >
                                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button> 
                                    <input type="submit" name="bulkDownload" class="btn btn-primary" value="Download">
                                </div>

                            </form>

                        </div>
                    </div>
                </div>
            <?php } else { ?>
                <div id="bulkdownload" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog"> 
                        <div class="modal-content"> 
                            <div class="modal-header"> 
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                                <h4 class="modal-title"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Here's a message!</h4>
                            </div> 
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label style="color:red;">No Files Exist in Selected Storage</label>                       
                                    </div>
                                </div> 
                            </div>
                            <div class="modal-footer"> 
                                <input value="<?php echo $slid; ?>" name="slid" type="hidden" >
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button> 
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
                            $("#childCopy").html(result);
                            //alert(result);
                            $.post("application/ajax/checkDuplicate.php", {parentId: lbl, levelDepth: 0, folder: copyf}, function (result, status) {
                                if (status == 'success') {
                                    if (result == 0) {
                                        $("#tocopyfolder").attr("readonly", "readonly");
                                        $("#tocopyfolder").attr("readonly");
                                    } else {
                                        $("#error").html(copyf + " is already exist in " + sfolder + ". Please rename storage name.");
                                        $("#tocopyfolder").removeAttr("readonly");
                                    }
                                }
                            });
                        }
                    });
                });
            </script>
            <!-- move selected files---->
            <div id="move-selected-files" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog">
                    <div class="modal-content" > 
                        <div class="modal-header"> 
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h4 class="modal-title" id="unseMove"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Here's a message!</h4>
                            <h4 class="modal-title" style="display:none;" id="mov"> Move Selected Files </h4> 
                        </div>
                        <div id="unselected" style="display:none;">
                            <div class="modal-body">
                                <h5 class="text-alert"> Please select Files for move.</h5>
                            </div>

                            <div class="modal-footer"> 
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button> 
                            </div>
                        </div>
                        <div id="selected">
                            <form method="post" class="form-inline">
                                <?php
                                $moveFolderName = mysqli_query($db_con, "select * from tbl_storage_level where sl_id = $slid") or die('Error in move folder name: ' . mysqli_error($db_con));
                                $rwmoveFolderName = mysqli_fetch_assoc($moveFolderName);
                                ?>   
                                <div class="modal-body">
                                    <input type="hidden" name="doc_id_smove_multi" id="doc_id_smove_multi" value="">
                                    <input type="hidden" name="sl_id_move_multi" id="sl_id_move_multi" value="<?php echo $slid; ?>">
                                    <div class="form-group">

                                        <label>Move Folder/File: </label>  <label> <?php echo $rwmoveFolderName['sl_name']; ?></label>
                                        <br><br>
                                        <div class="col-md-12">
                                            <label> Move To: &nbsp;</label>
                                            <select class="form-control" name="moveToParentId" id="moveToParentId">

                                                <option selected disabled style="background: #808080; color: #121213;">Select Storage Level</option>

                                                <?php
                                                if ($slid == 113) {
                                                    $storeName = mysqli_query($db_con, "select * from tbl_storage_level where sl_parent_id='$slperm'") or die('Error in move store: ' . mysqli_error($db_con));
                                                } else {
                                                    $storeName = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slperm'") or die('Error in move store: ' . mysqli_error($db_con));
                                                }
                                                while ($rwstoreName = mysqli_fetch_assoc($storeName)) {
                                                    ?>
                                                    <option value="<?php echo $rwstoreName['sl_id']; ?>"><?php echo $rwstoreName['sl_name']; ?></option>
                                                <?php }
                                                ?>
                                            </select>
                                            <br>            
                                            <div class="row">
                                                <div class="col-md-3"></div>

                                                <div class="col-md-9">
                                                    <span class="" id="child1">

                                                    </span>

                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer"> 
                                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button> 
                                    <input type="submit" name="movemulti" class="btn btn-primary" value="Move files">
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
            <div id="copy-selected-files" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog"> 
                    <div class="modal-content"> 
                        <div class="modal-header"> 
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                            <h4 class="modal-title" id="cop"> <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Here's a message!</h4> 
                            <h4 class="modal-title" style="display:none;" id="ctitle">Copy Selected Files in Storage</h4> 
                        </div> 

                        <div id="unselected1" style="display:none;">
                            <div class="modal-body">
                                <h5 class="text-alert">Please select Files for Copy.</h5>
                            </div>
                            <div class="modal-footer"> 
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button> 
                            </div>
                        </div>
                        <div id="selected1">
                            <form method="post" class="form-inline">
                                <div class="modal-body" id="csf">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-6 form-group">
                                                <label>Copy Files:</label>
                                                <input type="text" readonly id="tocopyfolder" name="toCopyFolder" class="form-control" value="<?php echo $tocpyFolder = $rwFolder['sl_name']; ?>">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <p class="text-danger" id="error"></p>
                                            </div>
                                            <div class="clearfix"></div>

                                            <div class="col-md-6 form-group">
                                                <input type="hidden" name="doc_ids" id="doc_ids" values="">
                                                <input type="hidden" name="sl_id4" id="sl_id4" values="">

                                                <label> Copy To: &nbsp;</label>
                                                <select class="form-control" name="copyToParentId" id="copyToParentId" style="width: 100%">

                                                    <option selected style="background: #808080; color: #121213;">Select Storage Level</option>

                                                    <?php
                                                    //$storeLevel = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slperm'") or die('Error in move store: ' . mysqli_error($db_con));
                                                    //$rwstoreLevel = mysqli_fetch_assoc($storeLevel);


                                                    if ($slid == 113) {
                                                        $storeName = mysqli_query($db_con, "select * from tbl_storage_level where sl_parent_id='$slperm'") or die('Error in move store: ' . mysqli_error($db_con));
                                                    } else {
                                                        $storeName = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slperm'") or die('Error in move store: ' . mysqli_error($db_con));
                                                    }
                                                    while ($rwstoreName = mysqli_fetch_assoc($storeName)) {
                                                        ?>
                                                        <option value="<?php echo $rwstoreName['sl_id']; ?>"><?php echo $rwstoreName['sl_name']; ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="clearfix"></div>

                                            <div class="col-md-6 form-group">
                                                <span class="" id="child2">
                                                </span>
                                            </div>

                                        </div>
                                    </div>   


                                    <div class="clearfix"></div>
                                </div>
                                <div class="modal-footer"> 
                                    <input value="<?php echo $rwFolder['sl_id']; ?>" name="modi" type="hidden" >
                                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button> 
                                    <input type="submit" name="copyFiles" class="btn btn-primary" value="Copy Files">
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
                $childName = $_POST['id'];
                $childName = mysqli_real_escape_string($db_con, $childName);
                $fields = $_POST['my_multi_select1'];
                $flag = 0;
                if (!empty($childName)) {
                    $reset = mysqli_query($db_con, "delete from tbl_metadata_to_storagelevel where sl_id='$childName'");
                }
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
                    echo '<script>metasuccess("storageFiles?id=' . $_GET['id'] . '");</script>';
                } else {
                    echo '<script>metafailed("storageFiles?id=' . $_GET['id'] . '");</script>';
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

                    $sql_child = "select * FROM tbl_storage_level WHERE sl_parent_id = '$_POST[lastMoveId]' AND sl_name = '$rwcheckDublteStorage[sl_name]'";

                    $sql_child_run = mysqli_query($db_con, $sql_child) or die('Error:' . mysqli_error($db_con));

                    if (mysqli_num_rows($sql_child_run)) {
                        $moveToId = $_POST['lastMoveId'];
                        $moveToName = mysqli_query($db_con, "Select * from tbl_storage_level where sl_id = '$moveToId'") or die('Error in checkDublteStorage:' . mysqli_error($db_con));
                        $rwmoveToName = mysqli_fetch_assoc($moveToName);
                        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$moveToId','Storage $rwFolder[sl_name] already exist in $rwmoveToName[sl_name].','$date', null,'$host','')") or die('error : ' . mysqli_error($db_con));
                        echo'<script>taskFailed("storage","Storage Name Having Same Name Already Exist !");</script>';
                    } else {
                        $moveToId = $_POST['lastMoveId'];
                        $lastMoveIdLevel = $_POST['lastMoveIdLevel'];
                        $lastMoveIdLevel = $lastMoveIdLevel + 1;

                        $moveStorage = "update tbl_storage_level set sl_parent_id = '$moveToId', sl_depth_level = '$lastMoveIdLevel' where sl_id = '$slid'";
                        $moveStorage_run = mysqli_query($db_con, $moveStorage) or die('Error in move Stroge : ' . mysqli_error($db_con));

                        $moveToName = mysqli_query($db_con, "Select * from tbl_storage_level where sl_id = '$moveToId'") or die('Error in checkDublteStorage:' . mysqli_error($db_con));
                        $rwmoveToName = mysqli_fetch_assoc($moveToName);

                        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$moveToId','Storage $rwFolder[sl_name] moved to $rwmoveToName[sl_name]','$date',null,'$host','')") or die('error : ' . mysqli_error($db_con));
                        echo'<script>taskSuccess("storage","Storage Moved Successfully !");</script>';
                    }
                }
                mysqli_close($db_con);
            }
            ?>
            <!--copy storage-->
            <?php
            if (isset($_POST['copyLevel'])) {
                if (isset($_POST['toCopyFolder']) && !empty($_POST['toCopyFolder'])) {
                    $toCopyFolder = $_POST['toCopyFolder'];
                    if (isset($_POST['lastCopyToId']) && !empty($_POST['lastCopyToId'])) {
                        $lastCopyToId = $_POST['lastCopyToId'];
                        copyStorage($slid, $lastCopyToId, $toCopyFolder, $date, $host);
                    }
                }
                mysqli_close($db_con);
            }
            ?>
            <!--modify storage level starts-->
            <?php
            if (isset($_POST['update']) && $_SERVER['PHP_SELF']) {
                $sl_id = $_POST['modi'];
                $modify = $_POST['modify_slname'];
                $slname = "select * from tbl_storage_level where sl_parent_id = '$sl_id' AND sl_name = '$modify'";
                $checkSlName = mysqli_query($db_con, "select * from tbl_storage_level where sl_parent_id = '$sl_id' AND sl_name = '$modify'") or die('Error in check DublteStorage:' . mysqli_error($db_con));
                if (mysqli_num_rows($checkSlName) > 0) {
                    echo'<script>taskFailed("storageFiles?id=' . urlencode(base64_encode($sl_id)) . '","Storage Name Already Exists Successfully !");</script>';
                } else {
                    $modiStorage = mysqli_query($db_con, "Select * from tbl_storage_level where sl_id = '$sl_id'") or die('Error in get sl name:' . mysqli_error($db_con));
                    $rwmodiStorage = mysqli_fetch_assoc($modiStorage);
                    $updateToName = $rwmodiStorage['sl_name'];
                    $sql = "update tbl_storage_level set sl_name = '$modify' WHERE sl_id = '$sl_id' ";
                    $sql_run = mysqli_query($db_con, $sql) or die("error:" . mysqli_errno($db_con));
                }
                if ($sql_run) {
                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$sl_id','Storage $updateToName rename to $modify.','$date', null,'$host','')") or die('error : ' . mysqli_error($db_con));
                    echo'<script>taskSuccess("storageFiles?id=' . urlencode(base64_encode($sl_id)) . '","Storage Updated Successfully !");</script>';
                } else {
                    echo'<script>taskFailed("storageFiles?id=' . urlencode(base64_encode($sl_id)) . '","Storage Updation Failed !");</script>';
                }
            }
            ?>
            <!---delete storage level start---->
            <?php
            if (isset($_POST['deleted'])) {
                $sl_id = $_POST['delsl'];
                $sl_id = mysqli_real_escape_string($db_con, $sl_id);
                $deleteStorage = mysqli_query($db_con, "Select * from tbl_storage_level where sl_id = '$sl_id'") or die('Error :' . mysqli_error($db_con));
                $rwdeleteStorage = mysqli_fetch_assoc($deleteStorage);
                $deletStorageName = $rwdeleteStorage['sl_name'];
                $dirPath = "extract-here/" . $deletStorageName;
                delStrg($sl_id);
                rmdir($dirPath);
                mysqli_query($db_con, "DELETE FROM tbl_storage_level WHERE sl_id='$sl_id'") or die('Error:' . mysqli_error($db_con));
                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$sl_id','Storage Name $deletStorageName deleted.','$date', null,'$host','')") or die('error :' . mysqli_error($db_con));
                $delParentId = $rwdeleteStorage['sl_parent_id'];
                echo'<script>taskSuccess("storageFiles?id=' . urlencode(base64_encode($delParentId)) . '","Storage Deleted Successfully !");</script>';

                mysqli_close($db_con);
            }
            ?>
            <!--Add Storage Level -->
            <?php
            if (isset($_POST['add_storage'])) {
                $sl_id = $_POST['add_child'];
                $sl_id = mysqli_real_escape_string($db_con, $sl_id);
                $create = mysqli_real_escape_string($db_con, $create);
                $create = preg_replace('/[^a-zA-Z0-9_]/', '', mysqli_real_escape_string($db_con, $_POST['create_child']));
                $checkLvlName = mysqli_query($db_con, "select * from tbl_storage_level where sl_parent_id='$sl_id' AND sl_name = '$create'") or die('Error in checkLvlName:' . mysqli_error($db_con));
                if (mysqli_num_rows($checkLvlName) > 0) {

                    echo'<script>taskFailed("storage?id=' . urlencode(base64_encode($sl_id)) . '","Storage of Same Name Already Exist !");</script>';
                } else {

                    $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$sl_id'") or die('Error:' . mysqli_error($db_con));

                    $rwParent = mysqli_fetch_assoc($parent);

                    $level = $rwParent['sl_depth_level'] + 1;
                    if (!empty($create)) {
                        $sql = "insert into tbl_storage_level(sl_id, sl_name, sl_parent_id, sl_depth_level)VALUES (null, '$create', '$sl_id', '$level')";
                        $sql_run = mysqli_query($db_con, $sql) or die("error:" . mysqli_error($db_con));
                        $newChildId = mysqli_insert_id($db_con);
                        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$newChildId','New Child $create Created.','$date', null,'$host','')") or die('error :' . mysqli_error($db_con));
                        echo'<script>taskSuccess("storage?id=' . urlencode(base64_encode($sl_id)) . '","Child Created Successfully !");</script>';
                    }
                }
                mysqli_close($db_con);
            }
            ?>
            <?php
            //asign doc to workflow
            if (isset($_POST['assignTo'])) {
                echo 'workflow id: ' . $wfid = $_POST['wfid'];
                echo 'doc id: ' . $dcId = $_POST['mTowf'];

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
                $id = base64_decode(urldecode(@$_GET['id']));  //get docId from url
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
                            $mail = assignTask($ticket, $idins, $db_con);
                            if ($mail) {

                                echo '<script>uploadSuccess("index", "Submitted Successfully!!");</script>';
                            } else {

                                echo '<script>taskFailed("index", "Opps!! Mail not sent !")</script>';
                            }
                        } else {
                            echo '<script>taskFailed("index", "Opps!! Submission failed !")</script>';
                        }
                    } else {
                        echo '<script>taskFailed("index", "There is no task in this workflow !")</script>';
                    }
                } else {
                    echo'<script>taskFailed(" ","Please Select WorkFlow !");</script>';
                }
                mysqli_close($db_con);
            }
            ?>
            <!--delete doc-->  
            <?php
            if (isset($_POST['Delmultiple'])) {
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
                    foreach ($filePath as $filePaths) {
                        $path = 'extract-here/' . $filePaths;

                        unlink($path);
                    }
                    if ($del) {
                        foreach ($filename as $filenames) {
                            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Storage Document $filenames Deleted','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                        }

                        echo'<script>taskSuccess("storageFiles?id=' . urlencode(base64_encode($storgId)) . '","Document Deleted Successfully !");</script>';
                    } else {
                        echo'<script>taskFailed("storageFiles?id=' . urlencode(base64_encode($storgId)) . '","Document not Deleted  !");</script>';
                    }
                } elseif ($rwcheckUser['role_id'] == 1 && $permission == "No") {
                    $deletefilename1 = mysqli_query($db_con, "UPDATE tbl_document_master SET flag_multidelete=0 WHERE doc_id in($docDelete)") or die('Error:' . mysqli_error($db_con));
                    if ($deletefilename1) {
                        foreach ($filename as $filenames) {
                            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Storage Document $filenames Deleted','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                        }

                        echo'<script>taskSuccess("storageFiles?id=' . urlencode(base64_encode($storgId)) . '","Document Deleted Successfully !");</script>';
                    } else {
                        echo'<script>taskFailed("storageFiles?id=' . urlencode(base64_encode($storgId)) . '","Document not Deleted  !");</script>';
                    }
                } else {
                    $deletefilename1 = mysqli_query($db_con, "UPDATE tbl_document_master SET flag_multidelete=0 WHERE doc_id in($docDelete)") or die('Error:' . mysqli_error($db_con));
                    if ($deletefilename1) {
                        foreach ($filename as $filenames) {
                            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Storage Document $filenames Deleted','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                        }
                        echo'<script>taskSuccess("storageFiles?id=' . urlencode(base64_encode($storgId)) . '","Document Deleted Successfully !");</script>';
                    } else {
                        echo'<script>taskFailed("storageFiles?id=' . urlencode(base64_encode($storgId)) . '","Document not Deleted  !");</script>';
                    }
                }
                mysqli_close($db_con);
            }
            if (isset($_POST['deleteDoc'])) {
                $id = $_POST['uid'];
                $id = mysqli_real_escape_string($db_con, $id);
                $permission = trim($_POST['deleteDoc']);
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
                    if ($del) {
                        unlink('extract-here/' . $filePath);
                        unlink($pathtxt);
                        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `doc_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null, '$deldocId', 'Storage Document $delfilename Deleted','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                        echo'<script>taskSuccess("storageFiles?id=' . urlencode(base64_encode($storgId)) . '","Document Deleted Successfully !");</script>';
                    } else {
                        echo'<script>taskFailed("storageFiles?id=' . urlencode(base64_encode($storgId)) . '","Document not Deleted  !");</script>';
                    }
                } elseif ($rwcheckUser['role_id'] == 1 && $permission == "No") {
                    $deletefilename = mysqli_query($db_con, "UPDATE tbl_document_master SET flag_multidelete=0 WHERE doc_id='$id'") or die('Error:' . mysqli_error($db_con));
                    if ($deletefilename) {
                        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `doc_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null, '$deldocId', 'Storage Document $delfilename Deleted','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                        echo'<script>taskSuccess("storageFiles?id=' . urlencode(base64_encode($storgId)) . '","Storage Deleted Successfully !");</script>';
                    } else {
                        echo '<script>taskSuccess("storageFiles?id=' . urlencode(base64_encode($storgId)) . '","Document Not Deleted")</script>';
                    }
                } else {
                    $deletefilename = mysqli_query($db_con, "UPDATE tbl_document_master SET flag_multidelete=0 WHERE doc_id='$id'") or die('Error:' . mysqli_error($db_con));
                    if ($deletefilename) {
                        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `doc_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null, '$deldocId', 'Storage Document $delfilename Deleted','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                        echo'<script>taskSuccess("storageFiles?id=' . urlencode(base64_encode($storgId)) . '","Storage Deleted Successfully !");</script>';
                    } else {
                        echo '<script>taskSuccess("storageFiles?id=' . urlencode(base64_encode($storgId)) . '","Document Not Deleted")</script>';
                    }
                }
                mysqli_close($db_con);
            }
            if (isset($_POST['deleteVersionDoc'])) {
                $id = $_POST['docid'];
                $id = mysqli_real_escape_string($db_con, $id);
                $getDocPath = mysqli_query($db_con, "select * from tbl_document_master where doc_id='$id'") or die('Error:' . mysqli_error($db_con));
                $rwgetDocPath = mysqli_fetch_assoc($getDocPath);
                $filePath = $rwgetDocPath['doc_path'];
                $delvrsnfile = $rwgetDocPath['old_doc_name'];
                $del = mysqli_query($db_con, "delete from tbl_document_master where doc_id='$id'") or die('Error:' . mysqli_error($db_con));
                unlink('extract-here/' . $filePath);
                if ($del) {
                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Storage Document $delvrsnfile Deleted','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));

                    $docName = explode("_", $rwgetDocPath['doc_name']);
                    $storgId = $docName[0];
                    echo'<script>taskSuccess("storageFiles?id=' . urlencode(base64_encode($storgId)) . '","Storage Deleted Successfully !");</script>';
                    //echo'<script>taskSuccess("storageFiles","Document Deleted Successfully !");</script>';
                } else {
                    echo '<script>taskFailed("storageFiles","Document Not Deleted")</script>';
                }
                mysqli_close($db_con);
            }
            ?>               
            <!--rename doc-->
            <?php
            if (isset($_POST['editFileName'])) {

                $renameid = filter_input(INPUT_POST, "docId");
                $renameName = filter_input(INPUT_POST, "renameName");

                $updateDoc = mysqli_query($db_con, "update tbl_document_master set old_doc_name = '$renameName' where doc_id = '$renameid'") or die('Error: ' . mysqli_error($db_con));
            }
            ?>

            <!--update metadata value-->
            <?php
            if (isset($_POST['editMetaValue'])) {

                $getMetaId = mysqli_query($db_con, "select * from tbl_document_master where doc_id = '$_POST[metaId]'") or die('Error:' . mysqli_error($db_con));
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
                        //$metadatValue = $rwMeta[''];
                        //echo $i; echo '-';
                        if ($rwgetMetaName['field_name'] == 'noofpages') {
                            
                        } else {
                            $fieldValue = $_POST['fieldName' . $i];
                            $updateMeta = mysqli_query($db_con, "update tbl_document_master set `$rwgetMetaName[field_name]` = '$fieldValue' where doc_id = '$_POST[metaId]' or (substring_index(doc_name,'_',-1)='$_POST[metaId]' and substring_index(doc_name,'_',1)='$_POST[metaId]')") or die('Error' . mysqli_error($db_con));
                            if ($updateMeta) {
                                //metadata update log
                                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'MetaData Value $fieldValue Assign in MetaData Field $rwgetMetaName[field_name] in $rwStrName[sl_name]','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                                echo'<script>taskSuccess("storageFiles?id=' . $_GET[id] . ((isset($_GET[start])) ? ('&start=' . $_GET[start]) : '') . '","MetaData Updated Successfully !");</script>';
                            }
                        }
                    }

                    $i++;
                }
                mysqli_close($db_con);
            }
            if (isset($_POST['updateDoc'])) {
                $user_id = $_SESSION['cdes_user_id'];
                if (!empty($_FILES['fileName']['name'])) {
                    $doc_id = $_POST['docid'];
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

                    if ($upload) {
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
                        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Versioning Document $file_name Added','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                        if ($createVrsn) {
                            $updateNew = mysqli_query($db_con, "update tbl_document_master set doc_name='$updateDocName' where doc_id='$insertDocID'");
                            $updateOld = mysqli_query($db_con, "update tbl_document_master set old_doc_name='$file_name',filename='$fname', doc_extn='$extn', doc_path='$storageName/$filenameEnct', uploaded_by='$user_id', doc_size='$file_size', noofpages='$pageCount', dateposted='$date' where doc_id='$doc_id'");
                            echo'<script>taskSuccess("storageFiles?id=' . $_GET[id] . ((isset($_GET[start])) ? ('&start=' . $_GET[start]) : '') . '","Updated Successfully !");</script>';
                        }
                    }
                }
                mysqli_close($db_con);
            }

//for move multi files
            if (isset($_POST['movemulti'])) {
                $to = $_POST['lastMoveId'];
                $level = $_POST['lastMoveIdLevel'];
                $mutiId = $_POST['doc_id_smove_multi'];
                $doc_id_smove_multi = explode(',', $mutiId);
                $moveToParentId = $_POST['moveToParentId'];
                $sl_id_move = $_POST['sl_id_move_multi'];
                $length = count($doc_id_smove_multi);
                if (isset($moveToParentId) && isset($doc_id_smove_multi)) {
                    foreach ($doc_id_smove_multi as $doc_id_smove_multis) {
                        $from_moveDocNm = mysqli_query($db_con, "select old_doc_name,doc_path from tbl_document_master where doc_id in($mutiId)") or die('Error' . mysqli_error($db_con));
                        $from_rwMoveNm = mysqli_fetch_assoc($from_moveDocNm);
                        $fromDocPath = "extract-here/" . $from_rwMoveNm['doc_path'];
                        $updateMoveDoc = "update tbl_document_master set doc_name = '$to' where doc_id ='$doc_id_smove_multis'";
                        mysqli_query($db_con, $updateMoveDoc) or die('Error' . mysqli_error($db_con));
                        $moveDocNm = mysqli_query($db_con, "select old_doc_name from tbl_document_master where doc_id in($mutiId)") or die('Error' . mysqli_error($db_con));
                        $rwMoveNm = mysqli_fetch_assoc($moveDocNm);
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
                        unlink($fromDocPath);
                        mysqli_query($db_con, "update tbl_document_master set doc_path = '$db_copy_Path_to' where doc_id ='$doc_id_smove_multis'") or die('Error' . mysqli_error($db_con));
                        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`,`doc_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$to', '$mutiId','$rwFolder[sl_name] Storage Document $rwMoveNm[old_doc_name] moved to Storage $rwmovestrgeNm[sl_name]','$date',null,'$host','')") or die('error : ' . mysqli_error($db_con));
                        if ($log) {
                            $message = 1;
                        }
                    }
                    if ($message == 1) {
                        echo'<script>taskSuccess("storageFiles?id=' . $_GET[id] . ((isset($_GET[start])) ? ('&start=' . $_GET[start]) : '') . '","Files moved Successfully !");</script>';
                    } else {
                        echo'<script>taskFailed("storageFiles?id=' . $_GET[id] . ((isset($_GET[start])) ? ('&start=' . $_GET[start]) : '') . '","Failed to move Files !");</script>';
                    }
                }
                mysqli_close($db_con);
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
                $ToUser = mysqli_real_escape_string($db_con, $ToUser);
                $date = date('Y-m-d H:i:s');
                $ToUser = implode(",", $ToUser);
                $shareDocIds = $_POST['shareFile'];
                $shareDocIds = explode(',', $shareDocIds);
                $myuser = explode(',', $ToUser);
                foreach ($shareDocIds as $shareId) {
                    foreach ($myuser as $myuserid) {

                        $chkDocId = mysqli_query($db_con, "select * from tbl_document_share where doc_ids='$shareId' and to_ids ='$myuserid'") or die('Error in check' . mysqli_error($db_con));


                        if (mysqli_num_rows($chkDocId) > 0) {
                            echo'<script>taskFailed("storageFiles?id=' . $_GET[id] . '","Document Already Shared !");</script>';
                        } else {

                            $shareFiles = mysqli_query($db_con, "INSERT INTO `tbl_document_share`(`from_id`, `to_ids`, `doc_ids`, `dateShare`) VALUES ('$fromUser','$myuserid','$shareId', '$date')") or die('Error in insert share document' . mysqli_error($db_con));



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
                                echo'<script>taskSuccess("storageFiles?id=' . $_GET[id] . '","Document shared Successfully !");</script>';
                            } else {
                                echo'<script>taskFailed("storageFiles?id=' . $_GET[id] . '","Document not shared !");</script>';
                            }
                        }
                    }
                }
                mysqli_close($db_con);
            }

            // copy multiple files
            if (isset($_POST['copyFiles'])) {
                $to = $_POST['lastMoveId'];
                $to = mysqli_real_escape_string($db_con, $to);
                $level = $_POST['lastMoveIdLevel'];
                $level = mysqli_real_escape_string($db_con, $level);
                $doc_ids = $_POST['doc_ids'];
                $doc_ids = mysqli_real_escape_string($db_con, $doc_ids);
                $copyToParentId = $_POST['copyToParentId'];
                $copyToParentId = mysqli_real_escape_string($db_con, $copyToParentId);
                $sl_id4 = $_POST['sl_id4'];
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

                    $sql2 = "INSERT INTO tbl_document_master SET";
                    $sql2 .= " doc_name='$to',old_doc_name='$old_doc_name',doc_extn='$doc_extn',doc_path='$db_copy_Path_to',uploaded_by='$uploaded_by',doc_size='$doc_size',dateposted='$rowmulticopy[dateposted]',noofpages='$rowmulticopy[noofpages]'";
                    while ($rwMeta = mysqli_fetch_assoc($meta)) {
                        $metan = mysqli_query($db_con, "select field_name from tbl_metadata_master where id='$rwMeta[metadata_id]'");
                        $rwMetan = mysqli_fetch_assoc($metan);

                        $field = $rwMetan['field_name'];
                        $value = $rowmulticopy[$field];
                        $sql2 .= ",$field='$value'";
                    }

                    $multicopyinsert = mysqli_query($db_con, $sql2);
                    if ($multicopyinsert) {
                        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `doc_id`,`action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$to', '$rowmulticopy[doc_id]','Storage document $old_doc_name copy to Storage $rwcopyLaststrg[sl_name].','$date',null,'$host','')") or die('Error DB: ' . mysqli_error($db_con));
                        if ($log) {
                            $message = "yes";
                        }
                    }
                }

                if ($message == "yes") {
                    echo'<script>taskSuccess("storageFiles?id=' . $_GET[id] . '","Document Copy Successfully !");</script>';
                }
                mysqli_close($db_con);
            }
            //Bulk Download
            if (isset($_POST['bulkDownload'])) {
                $rad = $_POST['raddwn'];
                $rad = mysqli_real_escape_string($db_con, $rad);
                $slid = $_POST['slid'];
                $slid = mysqli_real_escape_string($db_con, $slid);
                $reason = $_POST['reason'];
                $reason = mysqli_real_escape_string($db_con, $reason);
                $archive_file_name = $slName . '.zip';
                $download = mysqli_query($db_con, "select doc_path,old_doc_name,doc_extn,doc_id from tbl_document_master where doc_name='$slid' and flag_multidelete=1"); // or die('Error'.mysqli_error($db_con));
                $zip = new ZipArchive();
                //create the file and throw the error if unsuccessful
                if ($zip->open($archive_file_name, ZIPARCHIVE::CREATE) !== TRUE) {
                    exit("cannot open <$archive_file_name>\n");
                }
                while ($row = mysqli_fetch_assoc($download)) {
                    $docPath = $row['doc_path'];
                    $file_path = 'extract-here/' . substr($docPath, 0, strrpos($docPath, "/") + 1);
                    $files = substr($docPath, strrpos($docPath, "/") + 1);
                    $comp_folder = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id = '$slid'") or die('Error :' . mysqli_error($db_con));
                    $rwfolder = mysqli_fetch_assoc($comp_folder);

                    $file1 = $row['old_doc_name'] . '.' . $row['doc_extn'];
                    $zip->addFile($file_path . $files, $file1);
                }
                $zip->close();
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
            <!---end add and search metadata-->
    </body>
</html>
