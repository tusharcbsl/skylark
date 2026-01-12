<!DOCTYPE html>
<html>
<link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<link href="assets/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" />
<link href="assets/plugins/multiselect/css/multi-select.css" rel="stylesheet" type="text/css" />
<?php
set_time_limit(0);
require_once './loginvalidate.php';
require_once './application/config/database.php';
require_once './application/pages/head.php';
require_once './application/pages/function.php';

require_once './classes/fileManager.php';
require_once './classes/ftp.php';
$fileManager = new fileManager();


/* for file find in workflow and version of a same file
     * SELECT doc_name,substring_index(doc_name,'_',-1) FROM `ezeefiledms`.`tbl_document_master` where substring_index(doc_name,'_',-1)=7 and substring_index(doc_name,'_',1)=113;
      SELECT doc_id,doc_name,substring_index(doc_name,'_',1) FROM `ezeefiledms`.`tbl_document_master` where substring_index(doc_name,'_',1)=113;
     * 
     */


// echo $rwgetRole['dashboard_mydms']; die;
if ($rwgetRole['dashboard_mydms'] != '1' || empty($slpermIdes)) {
    header('Location: ./index');
    exit(0);
}
?>
<?php
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $slid = base64_decode(urldecode($_GET['id']));
    $folder = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid'");
} else {
    if (!empty($slpermIdes)) {
        $folder = mysqli_query($db_con, "select * from tbl_storage_level where sl_id in($slpermIdes) limit 1");
    } else {
        $folder = mysqli_query($db_con, "select * from tbl_storage_level where sl_depth_level='0'");
    }
}
$rwFolder = mysqli_fetch_assoc($folder);
$slid = $rwFolder['sl_id'];
$slName = $rwFolder['sl_name'];
$parentid = $rwFolder['sl_parent_id'];;
$sldepthlevel = $rwFolder['sl_depth_level'];

isFolderReadable($db_con, $slid);

$sllid = "select * from tbl_storage_level where sl_id = '$slid'";
$sllid_run = mysqli_query($db_con, $sllid); //or die("error:" . mysqli_errno($db_con));
$namesl = mysqli_fetch_assoc($sllid_run);

$result = "SELECT count(*) as total from tbl_document_master where doc_name = '$namesl[sl_name]'";
$sql_run = mysqli_query($db_con, $result); //or die("error:" . mysqli_errno($db_con));
$data = mysqli_fetch_assoc($sql_run);
$data['total'];
?>
<!--link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" /-->
<link href="assets/plugins/jstree/style.css" rel="stylesheet" type="text/css" />
<link href="assets/plugins/ladda-buttons/css/ladda-themeless.min.css" rel="stylesheet" type="text/css" />

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
                        $sllevel = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slperm' and delete_status=0");
                        $rwSllevel = mysqli_fetch_assoc($sllevel);
                        $level = $rwSllevel['sl_depth_level'];
                        ?>
                        <ol class="breadcrumb">
                            <li><a href="storage?id=<?php echo urlencode(base64_encode($slperm)); ?>"><?php echo $lang['Storage_Manager']; ?></a></li>

                            <?php
                            parentLevel($slid, $db_con, $slpermIdes, $level);

                            function parentLevel($slid, $db_con, $slperm, $level)
                            {
                                $flag = 0;
                                $slPermIds = explode(',', $slperm);
                                if (in_array($slid, $slperm)) {
                                    $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid' and delete_status=0"); //or die('Error' . mysqli_error($db_con));
                                    $rwParent = mysqli_fetch_assoc($parent);

                                    if ($level < $rwParent['sl_depth_level']) {
                                        parentLevel($rwParent['sl_parent_id'], $db_con, $slperm, $level);
                                    }
                                    $flag = 1;
                                } else {
                                    $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid' and sl_parent_id='$slperm' and delete_status=0"); //or die('Error' . mysqli_error($db_con));
                                    if (mysqli_num_rows($parent) > 0) {

                                        $rwParent = mysqli_fetch_assoc($parent);
                                        if ($level < $rwParent['sl_depth_level']) {
                                            parentLevel($rwParent['sl_parent_id'], $db_con, $slperm, $level);
                                        }
                                        $flag = 1;
                                        $flag = 1;
                                    } else {
                                        $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid' and delete_status=0"); //or die('Error' . mysqli_error($db_con));
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
                            <li> <a href="#" data-toggle="modal" data-target="#help-modal" id="helpview" data="44" title="<?= $lang['help']; ?>"><i class="fa fa-question-circle"></i> </a></li>
                            <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                        </ol>

                    </div>

                    <div class="row">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <div class="col-md-10 col-lg-10 col-sm-10 col-xs-10 m-t-10">
                                    <a href="javascript:void(0);" id="viewChange" title="Change View"><i class="fa fa-list"></i> <b><?php echo $lang['change_view']; ?></b></a>
                                </div>

                                <?php if (isFolderReadable($db_con, $slid)) { // check is this folder shared with read only or not 
                                ?>

                                    <div class="btn-group pull-right m-t-0 col-sm-2">

                                        <button type="button" class="btn btn-linkedin dropdown-toggle" data-toggle="dropdown"><?php echo $lang['chos_actn'] ?></button>
                                        <button type="button" class="btn btn-linkedin dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                                            <span class="caret"></span>

                                        </button>
                                        <ul class="dropdown-menu storage" role="menu">
                                            <?php if ($rwFolder['is_protected'] == 0 || $_SESSION['pass'] == $rwFolder['password']) { ?>

                                                <?php if ($rwgetRole['export_csv'] == '1') { ?>
                                                    <li><a href="javascript:(0)" data-toggle="modal" data-target="#export"><?php echo $lang['Export_Csv']; ?></a></li>
                                                <?php } ?>
                                                <?php if ($rwgetRole['bulk_download'] == '1') { ?>
                                                    <li><a href="javascript:void(0)" data-toggle="modal" data-target="#bulkdownload"><?php echo $lang['Blk_Dwnld_Files']; ?></a></li>
                                                <?php } ?>
                                                <?php if ($rwgetRole['upload_doc_storage'] == '1') { ?>
                                                    <li><a href="adddocument?id=<?php echo urlencode(base64_encode($slid)); ?>"><?php echo $lang['Upld_Docmnt']; ?> </a></li>
                                                <?php } ?>
                                                <?php if ($rwgetRole['create_child_storage'] == '1') { ?>
                                                    <li><a href="javascript:void(0)" data-toggle="modal" data-target="#con-close-modal1"><?php echo $lang['Crt_New_Cld']; ?></a></li>
                                                <?php } ?>
                                                <?php if ($rwgetRole['modify_storage_level'] == '1') {
                                                    if (($parentid != NULL || $parentid != '0') && $sldepthlevel != '0') {
                                                ?>
                                                        <li><a href="javascript:void(0)" data-toggle="modal" data-target="#con-close-modal"><?php echo $lang['Modify_Storage']; ?></a></li>
                                                <?php }
                                                } ?>
                                                <?php if ($rwgetRole['delete_storage_level'] == '1') {
                                                    if (($parentid != NULL || $parentid != '0') && $sldepthlevel != '0') {
                                                ?>
                                                        <li><a href="javascript:void(0)" data-toggle="modal" data-target="#con-close-modal2"><?php echo $lang['Dlt_Storage']; ?></a></li>
                                                <?php }
                                                } ?>
                                                <?php if ($rwgetRole['assign_metadata'] == '1') { ?>
                                                    <li><a href="javascript:void(0)" data-toggle="modal" data-target="#con-close-modal5"><?php echo $lang['Asgn_MetaData']; ?></a></li>
                                                <?php } ?>
                                                <?php if ($rwgetRole['move_storage_level'] == '1') {

                                                    if (($parentid != NULL || $parentid != '0') && $sldepthlevel != '0') {
                                                ?>
                                                        <li><a href="javascript:void(0)" data-toggle="modal" data-target="#con-close-modal4"><?php echo $lang['Move_Storage']; ?></a></li>
                                                <?php }
                                                } ?>

                                                <?php if ($rwgetRole['copy_storage_level'] == '1') {
                                                    if (($parentid != NULL || $parentid != '0') && $sldepthlevel != '0') {
                                                ?>
                                                        <li><a href="javascript:void(0)" data-toggle="modal" data-target="#con-close-modal6"><?php echo $lang['Cpy_Storage']; ?></a></li>
                                                    <?php
                                                    }
                                                }

                                                if ($rwgetRole['share_folder'] == '1') {
                                                    if (($parentid != NULL || $parentid != '0') && $sldepthlevel != '0') {
                                                    ?>
                                                        <li><a href="javascript:void(0)" data-toggle="modal" data-target="#con-close-modal7" id="share_fol"><?php echo $lang['share_folder']; ?></a></li>
                                            <?php }
                                                }
                                            }
                                            ?>

                                            <?php if ($rwgetRole['lock_folder'] == '1' && $rwFolder['is_protected'] == 0) { ?>
                                                <li><a href="javascript:void(0)" data-toggle="modal" id="lock_fol" data-target="#lock-folder"><?php echo $lang['lock_folder']; ?></a></li> <?php } ?>
                                            <?php if ($rwgetRole['lock_folder'] == '1' && $rwFolder['is_protected'] == 2) { ?>
                                                <li><a href="javascript:void(0)" data-toggle="modal" id="unlock_fol" data-target="#unlock-folder"><?php echo $lang['unlock_folder']; ?></a></li>
                                            <?php } ?>
                                            <?php if ($rwgetRole['lock_folder'] == '1' && $rwFolder['is_protected'] == 2) { ?>
                                                <li><a href="javascript:void(0)" data-toggle="modal" id="reset_password" data-target="#forgot-password"><?php echo $lang['forgot_pass']; ?></a></li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                <?php } ?>
                                <!--h4 id="event_result" class="header-title" style="display: inline-block;">Selected Folder : <strong><?php //echo $slName = $rwFolder['sl_name'];                 
                                                                                                                                        ?></strong></h4-->
                            </div>

                            <div class="box-body" style="">
                                <div class="col-md-3" style="overflow: auto;">
                                    <div class="card-box">
                                        <div id="basicTree">
                                            <!--ul>
                                                    <?php
                                                    /*  $sllevelTree = mysqli_query($db_con, "select * from tbl_storage_level where sl_id in($slpermIdes) and delete_status=0");
                                                    while ($rwSllevelTree = mysqli_fetch_assoc($sllevelTree)) {
                                                        $level = $rwSllevelTree['sl_depth_level'];
                                                        $permSlId = $rwSllevelTree['sl_id'];
                                                        $slParentId = $rwSllevelTree['sl_parent_id'];
                                                        if (isset($_GET['id']) && !empty($_GET['id'])) {
                                                            storageLevelS($level, $db_con, $slid, $slParentId, $permSlId);
                                                        } else {
                                                            storageLevelS($level, $db_con, $slid, $slParentId, $permSlId);
                                                        }
                                                    } */
                                                    ?>
                                                </ul-->
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-9" style="padding-left: 0;">
                                    <form action="searchdata">
                                        <div class="row" id="multiselect">
                                            <div class="col-md-3">

                                                <select class="form-control select2" id="my_multi_select1" name="metadata[]" required>
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
                                                    <option disabled selected style="background: #808080; color: #121213;"><?php echo $lang['Slt_Condition']; ?></option>
                                                    <option <?php
                                                            if (isset($_GET['cond']) && !empty($_GET['cond']) && $_GET['cond'] == 'Equal') {
                                                                echo 'selected';
                                                            }
                                                            ?> value="Equal"><?php echo $lang['Equal']; ?></option>
                                                    <option <?php
                                                            if (isset($_GET['cond']) && !empty($_GET['cond']) && $_GET['cond'] == 'Contains') {
                                                                echo 'selected';
                                                            }
                                                            ?> value="Contains"><?php echo $lang['Contains']; ?></option>
                                                    <option <?php
                                                            if (isset($_GET['cond']) && !empty($_GET['cond']) && $_GET['cond'] == 'Like') {
                                                                echo 'selected';
                                                            }
                                                            ?> value="Like"><?php echo $lang['Like']; ?></option>
                                                    <option <?php
                                                            if (isset($_GET['cond']) && !empty($_GET['cond']) && $_GET['cond'] == 'Not Like') {
                                                                echo 'selected';
                                                            }
                                                            ?> value="Not Like"><?php echo $lang['Not_Like']; ?></option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="text" class="form-control translatetext" name="searchText[]" required value="<?php echo $_GET['searchText'] ?>" placeholder="<?php echo $lang['entr_srch_txt_hr'] ?>">
                                            </div>
                                            <input type="hidden" value="<?php echo $_GET['id']; ?>" name="id" />
                                            <button type="submit" class="btn btn-primary " id="search" title="<?= $lang['Search']; ?>"><i class="fa fa-search"></i></button>
                                            <a href="javascript:void(0)" class="btn btn-primary" id="addfields" title="Add More"><i class="fa fa-plus"></i></a>
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
                                    /*
                                          $count = "SELECT count(*) as total from tbl_document_master where doc_name = '$namesl[sl_id]'";
                                          $count_run = mysqli_query($db_con, $count) or die("error:" . mysqli_errno($db_con));
                                          $count_data = mysqli_fetch_assoc( $count_run);

                                          $fileCount = mysqli_query($db_con, "select sum(doc_size) as total from tbl_document_master where doc_name = '$namesl[sl_id]'") or die('Error:' . mysqli_error($db_con));
                                          $rwfileCount = mysqli_fetch_assoc($fileCount);
                                          $totalSize = $rwfileCount['total'];
                                          $Size = round($totalSize / 1024, 2);
                                          $countFolder = mysqli_query( $db_con, "SELECT count(*) as total from tbl_storage_level where sl_parent_id = '$namesl[sl_id]'") or die('Error :' . mysqli_error($db_con));
                                          $rwcountFolder = mysqli_fetch_assoc($countFolder);
                                          $totalFolder = $rwcountFolder['total'];
                                         */

                                    //echo $marray = findTotalFile($slperm);
                                    /*function findTotalFile($slperm) {
                                            global $list;
                                            $list = array();
                                            global $db_con;
                                            global $numFile;
                                            global $totalFSize;
                                            global $totalFolder;
                                            global $noofpages;
                                            //$contFile = mysqli_query($db_con, "select sum(doc_size) as total, count(doc_name) as count,sum(noofpages) as numPage from tbl_document_master where FIND_IN_SET('$slperm',doc_name) && flag_multidelete = 1") or die('Error :' . mysqli_error($db_con));
                                            $contFile = mysqli_query($db_con, "select no_of_file as count, no_of_pages as numPage, file_size as total from tbl_agr_doc_upload where substring_index(sl_id,'_',1)='$slperm'");
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
                                            $noofpages += $rwcontFile['numPage'];
                                            $list['numPages'] = $noofpages;
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

                                        $numFile = 0;
                                        $totalFSize = 0;
                                        $totalFolder = 0;
                                        $noofpages = 0;
                                        $totalFiles = findTotalFile($rwFolder['sl_id']);*/
                                    ?>

                                    <div class="box box-primary sel" style="margin-bottom:90px;">
                                        <h4 id="event_result" class="header-title" style="display: inline-block;"><?php echo $lang['Slt_Folder']; ?> : <strong><?php echo $rwFolder['sl_name']; ?></strong><span id="totalFiles"></span>&nbsp;<button type="button" onclick="calculatefile()" class="btn btn-primary pull-right ladda-button" data-style="expand-left" style="margin-left: 200px;">Calculate Folder/Files</button></h4>

                                        <div class="box-body">

                                            <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12" style="padding: 0;" id="response">

                                                <!--div id="response"-->

                                                <!-- response(next page's data) will get appended here -->

                                                <!--we need to populate some initial data-->
                                                <?php

                                                $store = mysqli_query($db_con, "select * from tbl_storage_level where sl_parent_id='$slid' and delete_status=0 order by sl_name asc limit 0,50");
                                                while ($rwStore = mysqli_fetch_assoc($store)) {
                                                    $hasSub = mysqli_query($db_con, "select * from tbl_storage_level where sl_parent_id='$rwStore[sl_id]' and delete_status=0");
                                                    if (mysqli_num_rows($hasSub) > 0) {


                                                        $contFile = mysqli_query($db_con, "select sum(doc_size) as total, count(doc_name) as count from tbl_document_master where FIND_IN_SET('$rwStore[sl_id]',doc_name) and flag_multidelete='1'") or die('Error:' . mysqli_error($db_con));
                                                        $rwcontFile = mysqli_fetch_assoc($contFile);
                                                        $totalFSize = $rwcontFile['total'];
                                                        $totalFSize = round($totalFSize / (1024 * 1024), 2);   //convert in kb
                                                        $numFile = $rwcontFile['count'];

                                                        echo '<a class="dropdown-toggle waves-effect waves-light col-md-2 col-lg-2 col-sm-2 col-xs-2 view1" href="storage?id=' . urlencode(base64_encode($rwStore['sl_id'])) . '"title="' . $lang['no_of_file'] . ' = ' . $numFile . ' ' . $lang['total_size'] . ' = ' . $totalFSize . $lang['MB'] . '"><i class="fa fa-folder dv"></i><div style="overflow:hidden; height:25px;" title="' . $rwStore['sl_name'] . '">' . $rwStore['sl_name'] . '</div></a>';

                                                        echo '<a class="view2" href="storage?id=' . urlencode(base64_encode($rwStore['sl_id'])) . '"title="' . $lang['no_of_file'] . ' = ' . $numFile . ' ' . $lang['total_size'] . ' = ' . $totalFSize . $lang['MB'] . '" style="display: none;" ><i class="fa fa-folder"></i> ' . $rwStore['sl_name'] . '<span class="pull-right"> ' . $lang['no_of_file'] . ' = ' . $numFile . ' ' . $lang['total_size'] . ' = ' . $totalFSize . $lang['MB'] . '</span></a>';

                                                        $string = strip_tags($string);

                                                        if (strlen($string) > 500) {

                                                            // truncate string
                                                            $stringCut = substr($string, 0, 500);

                                                            // make sure it ends in a word so assassinate doesn't become ass...
                                                            $string = substr($stringCut, 0, strrpos($stringCut, ' ')) . '... <a href="/this/story">Read More</a>';
                                                        }
                                                        echo $string;
                                                        '</a></span>';
                                                    } else {
                                                        $file = mysqli_query($db_con, "SELECT doc_id as total from tbl_document_master where doc_name='$rwStore[sl_id]' and flag_multidelete='1'");
                                                        if (mysqli_num_rows($file) > 0) {


                                                            $contFile = mysqli_query($db_con, "select sum(doc_size) as total, count(doc_name) as count from tbl_document_master where substring_index(doc_name,'_',1) in($rwStore[sl_id]) and flag_multidelete='1'") or die('Error:' . mysqli_error($db_con));

                                                            $rwcontFile = mysqli_fetch_assoc($contFile);
                                                            $totalFSize = $rwcontFile['total'];
                                                            $totalFSize = round($totalFSize / (1024 * 1024), 2);
                                                            $numFile = $rwcontFile['count'];

                                                            echo '<a class="dropdown-toggle waves-effect waves-light col-md-2 col-lg-2 col-sm-2 col-xs-2 view1" href="storage?id=' . urlencode(base64_encode($rwStore['sl_id'])) . '"title="' . $lang['no_of_file'] . ' = ' . $numFile . ' ' . $lang['total_size'] . ' = ' . $totalFSize . $lang['MB'] . '" ><i class="fa fa-folder dv"></i><div style="overflow:hidden; height:25px;" title="' . $rwStore['sl_name'] . '">' . $rwStore['sl_name'] . '</div></a>';
                                                            echo '<a class="view2" href="storage?id=' . urlencode(base64_encode($rwStore['sl_id'])) . '"title="' . $lang['no_of_file'] . ' = ' . $numFile . ' ' . $lang['total_size'] . ' = ' . $totalFSize . $lang['MB'] . '" style="display: none;" ><i class="fa fa-folder"></i> ' . $rwStore['sl_name'] . '<span class="pull-right"> ' . $lang['no_of_file'] . ' = ' . $numFile . ' ' . $lang['total_size'] . ' = ' . $totalFSize . $lang['MB'] . '</span></a>';
                                                        } else {
                                                            $contFile = mysqli_query($db_con, "select sum(doc_size) as total, count(doc_name) as count from tbl_document_master where substring_index(doc_name,'_',1) in($rwStore[sl_id]) and flag_multidelete='1'") or die('Error:' . mysqli_error($db_con));

                                                            $rwcontFile = mysqli_fetch_assoc($contFile);
                                                            $totalFSize = $rwcontFile['total'];
                                                            $totalFSize = round($totalFSize / (1024 * 1024), 2);
                                                            $numFile = $rwcontFile['count'];
                                                            echo '<a class="dropdown-toggle waves-effect waves-light col-md-2 col-lg-2 col-sm-2 col-xs-2 view1" href="storage?id=' . urlencode(base64_encode($rwStore['sl_id'])) . '" title="' . $lang['no_of_file'] . ' = ' . $numFile . ' ' . $lang['total_size'] . ' = ' . $totalFSize . $lang['MB'] . '" ><i class="fa fa-folder-o dv"></i><div style="overflow:hidden; height:25px;" title="' . $rwStore['sl_name'] . '">' . $rwStore['sl_name'] . '</div></a>';


                                                            echo '<a class="view2" href="storage?id=' . urlencode(base64_encode($rwStore['sl_id'])) . '" title="' . $lang['no_of_file'] . ' = ' . $numFile . ' ' . $lang['total_size'] . ' = ' . $totalFSize . $lang['MB'] . '" style="display: none;" ><i class="fa fa-folder-o"></i> ' . $rwStore['sl_name'] . '<span class="pull-right"> ' . $lang['no_of_file'] . ' = ' . $numFile . ' ' . $lang['total_size'] . ' = ' . $totalFSize . $lang['MB'] . '</span></a>';
                                                        }
                                                    }
                                                }
                                                ?>
                                                <!--</div-->
                                                <input type="hidden" id="pageno" value="1">
                                                <img id="loader" src="assets/images/giphy.gif" style="width:50px;">

                                                <?php
                                                $user_id4 = $_SESSION['cdes_user_id'];
                                                $result = "SELECT count(doc_id) as total from tbl_document_master where doc_name = '$namesl[sl_id]' and flag_multidelete=1";

                                                $sql_run = mysqli_query($db_con, $result) or die("error sql:" . mysqli_error($db_con));
                                                $data = mysqli_fetch_assoc($sql_run);
                                                if ($data['total'] > 0) {
                                                ?>
                                                    <a href="storageFiles?id=<?php echo urlencode(base64_encode($slid)); ?>" data-target="_blank" class="dropdown-toggle waves-effect waves-light col-md-2 col-lg-2 col-sm-2 col-xs-2 view1">

                                                        <i class="md md-my-library-books dv"></i>
                                                        <div> <span><?php echo $data['total']; ?> <?php echo $lang['Files']; ?></span></div>
                                                    </a>


                                                    <a href="storageFiles?id=<?php echo urlencode(base64_encode($slid)); ?>" data-target="_blank" class="dropdown-toggle waves-effect waves-light col-md-2 col-lg-2 col-sm-2 col-xs-2 view2" style="display: none;">
                                                        <i class="md md-my-library-books"></i>
                                                        <span><?php echo $data['total']; ?> <?php echo $lang['Files']; ?></span>
                                                    </a>

                                                <?php }
                                                ?>


                                            </div>

                                            <div class="clearfix"></div>

                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div> <!-- container -->

                </div> <!-- content -->

                <!---assign meta-data model start ---->
                <div id="con-close-modal5" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h4 class="modal-title"><?php echo $rwFolder['sl_name']; ?> : <?php echo $lang['Asgn_MetaData_Fields_to']; ?> </h4>
                            </div>

                            <form method="post">
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-12 shiv metaa">
                                            <span><strong><?php echo $lang['Fld_Slt']; ?> </strong></span>
                                            <strong style="margin-left: 113px;"><?php echo $lang['Fld_Asnd']; ?> </strong>
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

                                </div>
                                <div class="modal-footer">
									<input type="radio" value="current" name="metadata_for" id="current_folder" checked/> <label for="current_folder">For Current Folder</label>
                                        <input type="radio" value="all" name="metadata_for" id="for_all"/> <label for="for_all"> For All Folder</label>
                                    <input type="hidden" value="<?php echo base64_decode(urldecode($_GET['id'])); ?>" name="id">
                                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                                    <button class="btn btn-primary waves-effect waves-light" type="submit" name="assignMeta"><?php echo $lang['Submit']; ?></button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div><!--ends assign-meta-data modal -->

                <div id="con-close-modal7" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h4 class="modal-title"><?php echo $lang['share_folder']; ?></h4>
                            </div>
                            <form method="post">
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label><?php echo $lang['Folder_Name']; ?></label>
                                        <input type="text" class="form-control specialchaecterlock" name="modify_slname" id="mstore1" value="<?php echo $rwFolder['sl_name']; ?>" readonly>
                                    </div>

                                    <div class="form-group">
                                        <label><?php echo $lang['share_with']; ?></label>
                                        <select name="sharewith[]" id="sharewith" class="form-control select2 multi-select" multiple data-placeholder="<?php echo $lang['Select_User']; ?>" required>
                                            <option value=""><?php echo $lang['Select_User']; ?></option>
                                            <?php
                                            $sameGroupIDs = array();
                                            $group = mysqli_query($db_con, "select * from tbl_bridge_grp_to_um where find_in_set('$_SESSION[cdes_user_id]',user_ids)") or die('Error' . mysqli_error($db_con));
                                            while ($rwGroup = mysqli_fetch_assoc($group)) {
                                                $sameGroupIDs[] = $rwGroup['user_ids'];
                                            }
                                            $sameGroupIDs = array_unique($sameGroupIDs);
                                            sort($sameGroupIDs);
                                            $sameGroupIDs = implode(',', $sameGroupIDs);
                                            $user = mysqli_query($db_con, "select * from tbl_user_master where user_id in($sameGroupIDs) order by first_name asc");
                                            while ($rwUser = mysqli_fetch_assoc($user)) {
                                                if ($rwUser['user_id'] != 1 && $rwUser['user_id'] != $_SESSION['cdes_user_id']) {
                                                    echo '<option value="' . $rwUser['user_id'] . '">' . $rwUser['first_name'] . ' ' . $rwUser['last_name'] . '</option>';
                                                } else {
                                                    // echo '<option value="' . $rwUser['user_id'] . '">' . $rwUser['first_name'] . ' ' . $rwUser['last_name'] . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>


                                    <div class="form-group">
                                        <div class="form-check">
                                            <input type="checkbox" name="readonly" class="form-check-input" value="1">
                                            <label class="form-check-label" for="exampleCheck1"> &nbsp; Read Only</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <input value="<?php echo $slid; ?>" name="slId" type="hidden">
                                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                                    <input type="submit" name="shareFolder" class="btn btn-primary" value="<?php echo $lang['Submit']; ?>">
                                </div>
                            </form>
                        </div>
                    </div>
                </div><!-- /.modal -->
                <?php
                if ($_SESSION['cdes_user_id'] == '1') {
                ?>
                    <button class="btn btn-primary" id="selectAllCheckbox">Select All</button>
                    <button class="btn btn-danger" id="selectAllButton" data-toggle="modal" data-target="#con-close-modal-muldel">Delete Storage</button>
                <?php
                }
                ?>
                <?php require_once './application/pages/footer.php'; ?>

            </div>
            <!-- Right Sidebar -->
            <?php require_once './application/pages/rightSidebar.php'; ?>
            <!-- /Right-bar -->
        </div>
        <!-- END wrapper -->

        <?php require_once './application/pages/footerForjs.php'; ?>

        <script src="assets/inview.js"></script>

        <script>
            $(document).ready(function() {

                $('#loader').on('inview', function(event, isInView) {

                    //alert();

                    if (isInView) {
                        var slid = '<?php echo $slid; ?>';
                        var nextPage = parseInt($('#pageno').val()) + 1;
                        $.ajax({
                            type: 'POST',
                            url: 'application/ajax/loadfolder.php',
                            data: {
                                pageno: nextPage,
                                slid: slid
                            },
                            success: function(data) {
                                //alert(data);
                                if (data != '') {
                                    $('#response').append(data);
                                    $('#pageno').val(nextPage);

                                    var viewname = getCookie("view");
                                    // alert(viewname);
                                    if (viewname == "view1") {
                                        $(".view2").hide();
                                        $(".view1").css("display", "block");
                                    } else {
                                        $(".view1").hide();
                                        $(".view2").css("display", "block");
                                    }
                                } else {
                                    $("#loader").hide();
                                }
                            }
                        });
                    }
                });
            });
        </script>

        <script src="assets/plugins/ladda-buttons/js/spin.min.js"></script>
        <script src="assets/plugins/ladda-buttons/js/ladda.min.js"></script>
        <script src="assets/plugins/ladda-buttons/js/ladda.jquery.min.js"></script>
        <script>
            function calculatefile() {
                var slid = '<?php echo $slid; ?>';
                $.ajax({
                    type: 'POST',
                    url: 'application/ajax/calculatefolder.php',
                    data: {
                        slid: slid
                    },
                    success: function(data) {
                        // alert(data);                      
                        $('#totalFiles').html('(' + data + ')');
                        $('.ladda-button').hide();
                    }
                });
            }
            $(document).ready(function() {



                // Bind normal buttons
                $('.ladda-button').ladda('bind', {
                    timeout: 40000
                });

                // Bind progress buttons and simulate loading progress
                Ladda.bind('.progress-demo .ladda-button', {
                    callback: function(instance) {
                        var progress = 0;
                        var interval = setInterval(function() {
                            progress = Math.min(progress + Math.random() * 0.1, 1);
                            instance.setProgress(progress);

                            if (progress === 1) {
                                instance.stop();
                                clearInterval(interval);
                            }
                        }, 200);
                    }
                });


            });
        </script>

        <!--for multiselect-->
        <script type="text/javascript" src="assets/plugins/multiselect/js/jquery.multi-select.js"></script>
        <script src="assets/js/jquery.core.js"></script>

        <script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>
        <script src="assets/plugins/jstree/jstree.min.js"></script>
        <script src="assets/pages/jquery.tree.js"></script>

        <script>
            /* $('#basicTree')
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
			$(".cheekced").parentsUntil("div").map(function () {
				var tags = $(this)[0].tagName;
				console.log(tags);
				if (tags == 'LI') {
					var tag = this.childNodes[1].style.background = '#beebff';//$(this).nth-child(2).prop('tagName');
					console.log(tag);
				}
				this.style.color = 'red';
			});
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
			}); */
            //
            // $('#basicTree').jstree({
            // 		'core' : {
            // 		  'data' :{
            // 			'url' : function (node) {
            // 			  return node.id === '#' ?
            // 				'application/ajax/rootStorage.php?slid='+<?php echo $slid; ?>:
            // 				'application/ajax/childStorage.php?slid='+<?php echo $slid; ?>;
            // 			},
            // 			'data' : function (node) {
            // 			  return { 'id' : node.id };
            // 			}
            // 		  } 
            // 		},
            // 		'types': {
            // 			'default': {
            // 				'icon': 'md md-folder'
            // 			},
            // 			'file': {
            // 				'icon': 'md md-my-library-books'
            // 			}
            // 		},
            // 		'plugins': ['types']

            // 	});	
            
            $('#basicTree').jstree({
                'core': {
                    'data': {
                        'url': function(node) {
                            return node.id === '#' ?
                                'application/ajax/rootStorage.php?slid=' + <?php echo $slid; ?> :
                                'application/ajax/childStorage.php?slid=' + <?php echo $slid; ?>;
                        },
                        'data': function(node) {
                            return {
                                'id': node.id
                            };
                        }
                    }
                },
                'types': {
                    'default': {
                        'icon': 'md md-folder'
                    },
                    'file': {
                        'icon': 'md md-my-library-books',
                        'checkbox': true // Child nodes have checkboxes
                    }
                },
                'checkbox': {
                    'three_state': false
                },
                'plugins': ['types', 'checkbox']

            });
//ankit 
            $('#selectAllCheckbox').on('click', function() {
                var selectedNodes = $('#basicTree').jstree('get_selected');
                //const checked = this.textContent === 'Select All';
                if (selectedNodes >= '0') {
                   // this.textContent = checked ? 'UnCheck All' : 'Check All';
                    $('#basicTree').jstree('deselect_all');
                } else {
                    //this.textContent = checked ? 'UnCheck All' : 'Check All';
                    $('#basicTree').jstree('select_all');
                }
            });

            $('#selectAllButton').on('click', function() {

                var selectedNodes = $('#basicTree').jstree('get_selected');
                //alert(selectedNodes);
                $("#delmultipleslid").val(selectedNodes);
                if (selectedNodes.length > 0) {

                    alert("Selected Storage IDs: " + selectedIDs.join(", "));
                } else {
                    alert("No storage selected.");
                }
            }); 
             //ankit end
            $('#basicTree').bind("select_node.jstree", function(e, data) {
                var href = data.node.a_attr.href;
                window.location.href = href;
            });
        </script>

        <script type="text/javascript">
            $(document).ready(function() {
                $('form').parsley();

            });

            //firstname last name 
            $("input#userName, input#lastName").keypress(function(e) {
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
            $("input#phone").keypress(function(e) {
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

            $(document).ready(function() {

                //Disable mouse right click
                $("body").on("contextmenu", function(e) {
                    // return false;
                });
                var viewname = getCookie("view");
                // alert(viewname);
                if (viewname == "view1") {
                    $(".view2").hide();
                    $(".view1").css("display", "block");
                } else {
                    $(".view1").hide();
                    $(".view2").css("display", "block");
                }
            });

            $("#viewChange").click(function() {

                var $visible = $(".view1").is(":visible");
                //alert($visible);
                if ($visible) {
                    $(".view1").hide();
                    $(".view2").css("display", "block");
                    setCookie("view", "view2", 365);
                } else {
                    $(".view2").hide();
                    $(".view1").css("display", "block");
                    setCookie("view", "view1", 365);
                }
            });

            function getCookie(cname) {
                var name = cname + "=";
                var decodedCookie = decodeURIComponent(document.cookie);
                var ca = decodedCookie.split(';');
                for (var i = 0; i < ca.length; i++) {
                    var c = ca[i];
                    while (c.charAt(0) == ' ') {
                        c = c.substring(1);
                    }
                    if (c.indexOf(name) == 0) {
                        return c.substring(name.length, c.length);
                    }
                }
                return "";

            }

            function setCookie(cname, cvalue, exdays) {
                var d = new Date();
                d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
                var expires = "expires=" + d.toUTCString();
                document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
            }
        </script>
        <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title"><?php echo $lang['Mdfy_Storage_Level']; ?></h4>
                    </div>
                    <form method="post">
                        <div class="modal-body">
                            <input class="form-control specialchaecterlock" name="modify_slname" value="<?php echo $rwFolder['sl_name']; ?>" required>
                        </div>
                        <div class="modal-footer">
                            <input value="<?php echo $rwFolder['sl_id']; ?>" name="modi" type="hidden">
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                            <input type="submit" name="update" class="btn btn-primary" value="<?php echo $lang['Save_changes']; ?>">
                        </div>

                    </form>

                </div>
            </div>
        </div><!-- /.modal -->
        <!---Create child model start ---->
        <div id="con-close-modal1" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title"><?php echo $rwFolder['sl_name']; ?> (<?php echo $lang['Ad_New_Chld_to']; ?>) </h4>
                    </div>
                    <form method="post">
                        <div class="modal-body">
                            <input class="form-control specialchaecterlock" name="create_child" placeholder="<?php echo $lang['Crt_New_Cld']; ?>" required>
                        </div>
                        <div class="modal-footer">
                            <input value="<?php echo $rwFolder['sl_id']; ?>" name="add_child" type="hidden">
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                            <input type="submit" name="add_storage" class="btn btn-primary" value="<?php echo $lang['CREATE_CHILD']; ?>">
                        </div>
                    </form>
                </div>
            </div>
        </div><!--ends Create child modal -->
        <!--start delete model-->
        <div id="con-close-modal2" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog">
                <div class="panel panel-color panel-danger">
                    <div class="panel-heading">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h2 class="panel-title"><?php echo $lang['Are_u_confirm']; ?></h2>
                    </div>
                    <form method="post">
                        <div class="panel-body">
                            <p class="text-alert">
                                <?php echo str_replace('folder_name', $rwFolder['sl_name'], $lang['del_strg_sure_alert']); ?>?
                            </p>
                        </div>
                        <div class="modal-footer">
                            <input value="<?php echo $rwFolder['sl_id']; ?>" name="delsl" type="hidden">
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close'] ?></button>

                            <?php if ($rwgetRole['role_id'] == 1) { ?>
                                <button type="submit" name="deleted" class="btn btn-danger" value="yes"><i class="fa fa-trash-o"></i> <?php echo $lang['Delete'] ?> </button>
                            <?php } ?>

                            <button type="submit" id="no" name="deleted" class="btn btn-danger"> <i class="fa fa-recycle"></i>
                                <?php
                                if ($rwgetRole['role_id'] == 1) {
                                    echo $lang['Recycle'];
                                } else {
                                    echo $lang['Delete'];
                                }
                                ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div><!--ends delete modal -->
        <!--for move level-->
        <div id="con-close-modal4" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title"><?php echo $lang['Move_Storage']; ?></h4>
                    </div>
                    <form method="post">
                        <div class="modal-body">
                            <div class="row">
                                <?php
                                $moveFolderName = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id = $slid") or die('Error in move folder name: ' . mysqli_error($db_con));
                                $rwmoveFolderName = mysqli_fetch_assoc($moveFolderName);
                                ?>

                                <label><?php echo $rwmoveFolderName['sl_name']; ?> : <?php echo $lang['Move_Fld_File']; ?> </label>
                                <br>
                                <div class="col-md-12">
                                    <label><?php echo $lang['Move_To']; ?></label>
                                    <select class="form-control select2" name="lastMoveId" required>
                                        <option selected disabled style="background: #808080; color: #fff;"><?php echo $lang['Sel_Strg_Lvl']; ?></option>
                                        <?php
                                         mysqli_set_charset($db_con, "utf8");
                                $perm = mysqli_query($db_con, "select sl_id from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]' group by sl_id");
                                $slperms = array();
                                while ($rwPerm = mysqli_fetch_assoc($perm)) {
                                    $slperms[] = $rwPerm['sl_id'];
                                }
                                $permcount = count($slperms);
                                $sl_perm = implode(',', $slperms);
                                        $sllevel = mysqli_query($db_con, "select * from tbl_storage_level where sl_id in($slpermIdes) AND delete_status='0' order by sl_name asc");
 $all_data = mysqli_query($db_con, "select * from tbl_storage_level where  delete_status='0' order by sl_name asc");
                                   $new_arr=mysqli_fetch_all($all_data,MYSQLI_ASSOC);
                                    $parent_id_arr=array();
                            $sl_id_arr=array();
                            foreach($new_arr as $v)
                            {
                                $parent_id_arr[$v['sl_parent_id']][]=$v;
                                $sl_id_arr[$v['sl_id']]=$v;
                                $sl_parent_id_slid[$v['sl_parent_id']][$v['sl_id']]=$v;

                            }
                                        while ($rwSllevel = mysqli_fetch_assoc($sllevel)) {
                                            $level = $rwSllevel['sl_depth_level'];
                                            $SlId = $rwSllevel['sl_id'];
                                            //findChild($SlId, $level, $SlId, $slid);
                                            findchild1($SlId, $level, $SlId, $slid,$parent_id_arr,$sl_id_arr,$sl_parent_id_slid);
                                        }

                                        ?>
                                    </select>
                                    <div class="row">
                                        <div class="col-md-12" id="child">

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input value="<?php echo $rwFolder['sl_id']; ?>" name="modi" type="hidden">
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                            <input type="submit" name="move" class="btn btn-primary" value="<?php echo $lang['Move_Storage']; ?>">
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!---export csv model strats---->
        <div id="export" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog">
                <div class="panel panel-color panel-primary">
                    <div class="panel-heading">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h2 class="panel-title"><?php echo $lang['Export_CSV']; ?></h2>
                    </div>
                    <form method="post" action="export_1.php">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="radio radio-success radio-inline">
                                        <input type="radio" checked name="radExp" id="inlineRadio1" value="all">
                                        <label for="inlineRadio1"><?php echo $lang['Al_Files_of_slt_fld_and_their_chld']; ?></label>
                                    </div>
                                </div>
                                <div class="col-sm-12 m-t-15">
                                    <div class="radio radio-success radio-inline">
                                        <input type="radio" name="radExp" id="inlineRadio2" value="s">
                                        <label for="inlineRadio2"><?php echo $lang['Al_Files_of_slt_fld_only']; ?></label>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <input value="<?php echo $slid; ?>" name="slid" type="hidden">
                            <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                            <button type="submit" name="startExport" class="btn btn-primary"><i class="fa fa-download"></i> <?php echo $lang['Strt_xprt']; ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- export csv model ends--->


        <!--startmultiple storage delete modal-->
        <div id="con-close-modal-muldel" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog">
                <div class="panel panel-color panel-danger">
                    <div class="panel-heading">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h2 class="panel-title"><?php echo $lang['Are_u_confirm']; ?></h2>
                    </div>
                    <form method="post">
                        <div class="panel-body">
                            <p class="text-alert">
                                <?php echo str_replace('folder_name', $rwFolder['sl_name'], $lang['del_strg_sure_alert']); ?>?
                            </p>
                        </div>
                        <div class="modal-footer">
                            <input value="" name="delmultplesl" id="delmultipleslid" type="hidden">
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close'] ?></button>

                            <?php if ($rwgetRole['role_id'] == 1) { ?>
                                <button type="submit" name="multideleted" class="btn btn-danger" value="yes"><i class="fa fa-trash-o"></i> <?php echo $lang['Delete'] ?> </button>
                            <?php } ?>

                            <button type="submit" id="ino" name="multideleted" class="btn btn-danger"> <i class="fa fa-recycle"></i>
                                <?php
                                if ($rwgetRole['role_id'] == 1) {
                                    echo $lang['Recycle'];
                                } else {
                                    echo $lang['Delete'];
                                }
                                ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div><!--ends multiple storage delete modal -->

        <script>
            $("#parentMoveLevel").change(function() {
                var lbl = $(this).val();
                //alert(lbl);
                $.post("application/ajax/parentMoveList.php", {
                    parentId: lbl,
                    levelDepth: 0,
                    sl_id: <?php echo $slid; ?>
                }, function(result, status) {
                    if (status == 'success') {
                        $("#child").html(result);
                        //alert(result);
                    }
                });
            });
        </script>

        <!-- for copy level-->
        <div id="con-close-modal6" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title"><?php echo $lang['Cpy_Strge_Lvl']; ?></h4>
                    </div>
                    <form method="post">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <label><?php echo $lang['Cpy_Fld']; ?></label>
                                    <input type="text" readonly id="tocopyfolder" name="toCopyFolder" class="form-control" value="<?php echo $tocpyFolder = $rwFolder['sl_name']; ?>">
                                </div>
                                <div class="col-md-12">
                                    <p class="text-danger" id="error"></p>
                                </div>
                                <div class="col-md-12">
                                    <label><?php echo $lang['Cpy_To']; ?>: &nbsp;</label>
                                    <select class="form-control select2" name="lastCopyToId" required>
                                        <option selected value="" style="background: #808080; color: #fff;"><?php echo $lang['Sel_Strg_Lvl']; ?></option>
                                        <?php
                                          mysqli_set_charset($db_con, "utf8");
                                $perm = mysqli_query($db_con, "select sl_id from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]' group by sl_id");
                                $slperms = array();
                                while ($rwPerm = mysqli_fetch_assoc($perm)) {
                                    $slperms[] = $rwPerm['sl_id'];
                                }
                                $permcount = count($slperms);
                                $sl_perm = implode(',', $slperms);
                                        $sllevel = mysqli_query($db_con, "select * from tbl_storage_level where sl_id in($slpermIdes) AND delete_status='0' order by sl_name asc");
 $all_data = mysqli_query($db_con, "select * from tbl_storage_level where  delete_status='0' order by sl_name asc");
                                   $new_arr=mysqli_fetch_all($all_data,MYSQLI_ASSOC);
                                    $parent_id_arr=array();
                            $sl_id_arr=array();
                            foreach($new_arr as $v)
                            {
                                $parent_id_arr[$v['sl_parent_id']][]=$v;
                                $sl_id_arr[$v['sl_id']]=$v;
                                $sl_parent_id_slid[$v['sl_parent_id']][$v['sl_id']]=$v;

                            }
                                        while ($rwSllevel = mysqli_fetch_assoc($sllevel)) {
                                            $level = $rwSllevel['sl_depth_level'];
                                            $SlId = $rwSllevel['sl_id'];
                                            findChild($SlId, $level, $SlId, $slid);
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-12" id="childCopy">
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <input value="<?php echo $rwFolder['sl_id']; ?>" name="modi" type="hidden">
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                            <input type="submit" name="copyLevel" class="btn btn-primary" value="<?php echo $lang['Cpy_Strge_Lvl']; ?>">
                        </div>

                    </form>
                </div>
            </div>
        </div>
        <?php
        $validate = mysqli_query($db_con, "select doc_path,old_doc_name,doc_extn,doc_id from tbl_document_master where doc_name='$slid' and flag_multidelete=1");
        if (mysqli_num_rows($validate) > 0) {
        ?>
            <div id="bulkdownload" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h4 class="modal-title"><?php echo $lang['Dwnld_All_Files_of_slt_fld_only']; ?></h4>
                        </div>
                        <form method="post" class="form-inline">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <textarea class="form-control translatetext specialchaecterlock" name="reason" cols="65" rows="5" placeholder="<?php echo $lang['Wte_Rson_fr_Dnldng_fles']; ?>" required></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <input value="<?php echo $slid; ?>" name="slid" type="hidden">
                                <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal"><?php echo $lang['Close'] ?></button>
                                <button type="submit" name="bulkDownload" id="bulkdowns" class="btn btn-primary"><i class="fa fa-download"></i> <?php echo $lang['Download'] ?></button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        <?php } else { ?>
            <div id="bulkdownload" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog">
                    <div class="panel panel-color panel-danger">
                        <div class="panel-heading">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h2 class="panel-title"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i><?php echo $lang['Hre_msge']; ?></h2>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="text-alert"><?php echo $lang['No_Files_Ext_in_Slt_Storage']; ?></label>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input value="<?php echo $slid; ?>" name="slid" type="hidden">
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>


        <?php require_once './lock_folder_html.php'; ?>
        <?php require_once './lock_folder_php.php'; ?>
        <script>
            $("#parentCopyLevel").change(function() {
                var lbl = $(this).val();
                var copyf = $("#tocopyfolder").val();
                var sfolder = $(this).find(":selected").text();

                //alert(lbl);
                $.post("application/ajax/parentCopyList.php", {
                    parentId: lbl,
                    levelDepth: 0,
                    sl_id: <?php echo $slid; ?>,
                    folder: copyf,
                    sfolder: sfolder
                }, function(result, status) {
                    if (status == 'success') {
                        $("#childCopy").html(result);
                        //alert(result);
                        $.post("application/ajax/checkDuplicate.php", {
                            parentId: lbl,
                            levelDepth: 0,
                            folder: copyf
                        }, function(result, status) {
                            if (status == 'success') {
                                if (result == 0) {
                                    //alert('zero');
                                    // alert(result);
                                    $("#error").html("");
                                    $("#tocopyfolder").attr("readonly", "readonly");
                                    //$("#tocopyfolder").attr("readonly");
                                } else {
                                    //alert('one');
                                    // alert(result);
                                    // alert(copyf);
                                    //alert(sfolder);
                                    $("#error").html(copyf + " is already exist in " + sfolder + ". Please rename storage name.");
                                    $("#tocopyfolder").removeAttr("readonly");
                                }
                            }
                        });
                    }
                });
            });
        </script>
    </div>
    <?php
    if (isset($_POST['assignMeta'], $_POST['token'])) {
        $childName = $_POST['id'];
        $childName = mysqli_real_escape_string($db_con, $childName);
        $fields = $_POST['my_multi_select1'];
        $flag = 0;
        if (!empty($childName)) {
            $reset = mysqli_query($db_con, "delete from tbl_metadata_to_storagelevel where sl_id='$childName'");
        }
        if (!empty($fields)) {
            $metaDataNames = array();
            foreach ($fields as $field) {
                if (!empty($childName)) {
                    //check meta data assigned or not
                    $match = mysqli_query($db_con, "select * from tbl_metadata_to_storagelevel where sl_id='$childName' and metadata_id='$field'"); //or die('Error:' . mysqli_error($db_con));
                    if (mysqli_num_rows($match) <= 0) {
                        //assign meta data
                        $create = mysqli_query($db_con, "insert into tbl_metadata_to_storagelevel (`metadata_id`, `sl_id`) values('$field','$childName')"); //or die('Error' . mysqli_error($db_con));
                        // find meta data details
                        $metan = mysqli_query($db_con, "select * from tbl_metadata_master where id='$field'");
                        $rwMetan = mysqli_fetch_assoc($metan);
                        $metaDataNames[] = $rwMetan['field_name'];
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
			
				$metadata_for = mysqli_real_escape_string($db_con, $_POST['metadata_for']);
				if ( $metadata_for === "all" ) {
					// print_r($fields);
					$storage_query = mysqli_query($db_con, "SELECT * FROM tbl_storage_level");
					while( $storages = mysqli_fetch_assoc($storage_query) ) {
						// print_r($storages['sl_id']);
						foreach($fields as $field_item) {
							// echo $storages['sl_id'].' '.$field_item.',';
							//echo "SELECT * FROM tbl_metadata_to_storagelevel WHERE metadata_id = '".$field_item."' AND sl_id = '".$storages['sl_id']."'";
							$is_exist = mysqli_query($db_con, "SELECT * FROM tbl_metadata_to_storagelevel WHERE metadata_id = '".$field_item."' AND sl_id = '".$storages['sl_id']."'");
							if(mysqli_num_rows($is_exist) == 0) {
								$mts_sql = "INSERT INTO tbl_metadata_to_storagelevel SET 
								metadata_id = '".$field_item."',
								sl_id = '".$storages['sl_id']."'";
								mysqli_query($db_con, $mts_sql);
							}
						}
					}
				}
			
                $metaDataNames = implode(",", $metaDataNames);
                $strgeName = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id = '$sl_id' and delete_status=0");
                $rwstrgeName = mysqli_fetch_assoc($strgeName);
                $storageName = $rwstrgeName['sl_name'];
                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null, '$sl_id','MetaData($metaDataNames)  Assigned on storage $storageName','$date',null,'$host', null)"); //or die('error : ' . mysqli_error($db_con));
                echo '<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Metadata_Assigned'] . '");</script>';
            } else {
                echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['ftam'] . '");</script>';
            }
        } else {
            echo '<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['nmdta'] . '");</script>';
        }
        mysqli_close($db_con);
    }
    ?>
    <!--move Storage-->
    <?php
    // if (isset($_POST['move'], $_POST['token'])) {

    //     //echo $_POST['moveToId']; die;

    //     if (!empty($_POST['lastMoveId'])) {

    //         $checkDublteStorage = mysqli_query($db_con, "Select * from tbl_storage_level where sl_id = '$slid' and delete_status=0"); //or die('Error in checkDublteStorage:' . mysqli_error($db_con));

    //         $rwcheckDublteStorage = mysqli_fetch_assoc($checkDublteStorage);

    //         $sql_child = "select * FROM tbl_storage_level WHERE sl_parent_id = '$_POST[lastMoveId]' AND sl_name = '$rwcheckDublteStorage[sl_name]'";

    //         $sql_child_run = mysqli_query($db_con, $sql_child); //or die('Error:' . mysqli_error($db_con));

    //         if (mysqli_num_rows($sql_child_run) > 0) {

    //             $moveToId = $_POST['lastMoveId'];
    //             $moveToName = mysqli_query($db_con, "Select * from tbl_storage_level where sl_id = '$moveToId'"); //or die('Error in checkDublteStorage:' . mysqli_error($db_con));
    //             $rwmoveToName = mysqli_fetch_assoc($moveToName);
    //             $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$moveToId','Storage $rwFolder[sl_name] already exist in $rwmoveToName[sl_name].','$date', null,'$host',null)"); //or die('error : ' . mysqli_error($db_con));
    //             echo '<script>taskFailed("storage","Storage Name Having Same Name Already Exist !");</script>';
    //         } else {



    //             $sql_child1 = "select sl_depth_level FROM tbl_storage_level WHERE sl_id = '$_POST[lastMoveId]'";

    //             $levelres = mysqli_query($db_con, $sql_child1); //or die('Error:' . mysqli_error($db_con));
    //             $levelrow = mysqli_fetch_assoc($levelres);

    //             $moveToId = $_POST['lastMoveId'];
    //             $lastMoveIdLevel = $levelrow['sl_depth_level'];
    //             $lastMoveIdLevel = $lastMoveIdLevel + 1;

    //             $moveStorage = "update tbl_storage_level set sl_parent_id = '$moveToId', sl_depth_level = '$lastMoveIdLevel' where sl_id = '$slid'";
    //             $moveStorage_run = mysqli_query($db_con, $moveStorage); //or die('Error in move Stroge : ' . mysqli_error($db_con));


    //             $moveToName = mysqli_query($db_con, "Select * from tbl_storage_level where sl_id = '$moveToId'"); //or die('Error in checkDublteStorage:' . mysqli_error($db_con));
    //             $rwmoveToName = mysqli_fetch_assoc($moveToName);


    //             $slname = str_replace(" ", "", $rwcheckDublteStorage['sl_name']);

    //             $updir = getStoragePath($db_con, $moveToId, $lastMoveIdLevel);

    //             if (!empty($updir)) {
    //                 $updir = $updir . '/';
    //             } else {
    //                 $updir = '';
    //             }
    //             $folderpath = $updir . $slname;

    //             if (!is_dir($folderpath)) {
    //                 mkdir($folderpath, 0777, TRUE) or die(print_r(error_get_last()));
    //             }

    //             // Connect to file server
    //             $fileManager->conntFileServer();

    //             moveStorageFiles($db_con, $rwcheckDublteStorage['sl_id'], $folderpath, $fileManager);

    //             $moveStorage = mysqli_query($db_con, "select * from tbl_storage_level where sl_parent_id = '$slid'"); //or die('Error in checkDublteStorage:' . mysqli_error($db_con));

    //             while ($rowm = mysqli_fetch_assoc($moveStorage)) {

    //                 moveStorage($db_con, $rowm['sl_id'], $lastMoveIdLevel, $fileManager);
    //             }

    //             $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$moveToId','Storage $rwFolder[sl_name] moved to $rwmoveToName[sl_name]','$date',null,'$host',null)"); //or die('error : ' . mysqli_error($db_con));
    //             echo '<script>taskSuccess("storage","' . $lang['Strge_Moved_Scesfly'] . '");</script>';
    //         }
    //     }
    //     mysqli_close($db_con);
    // }
    ?>
    <?php
        if (isset($_POST['move'], $_POST['token'])) {
            if (!empty($_POST['lastMoveId'])) {
                $checkDublteStorage = mysqli_query($db_con, "Select * from tbl_storage_level where sl_id = '$slid'") or die('Error in checkDublteStorage:' . mysqli_error($db_con));

                $rwcheckDublteStorage = mysqli_fetch_assoc($checkDublteStorage);
                $storageName_From = $rwcheckDublteStorage['sl_name'];
                $storageName_From = str_replace(" ", "", $storageName_From);
                $storageName_From = preg_replace('/[^A-Za-z0-9\-]/', '', $storageName_From);

                $lmoveid = preg_replace("/[^A-Za-z0-9]/", "", $_POST['lastMoveId']);
                $sql_child = "select * FROM tbl_storage_level WHERE sl_parent_id = '$lmoveid' AND sl_name = '$rwcheckDublteStorage[sl_name]'";

                $sql_child_run = mysqli_query($db_con, $sql_child) or die('Error1234:' . mysqli_error($db_con));

                if (mysqli_num_rows($sql_child_run)) {
                    $moveToId = preg_replace("/[^A-Za-z0-9 ]/", "", $_POST['lastMoveId']);
                    $moveToName = mysqli_query($db_con, "Select * from tbl_storage_level where sl_id = '$moveToId'") or die('Error in checkDublteStorage:' . mysqli_error($db_con));
                    $rwmoveToName = mysqli_fetch_assoc($moveToName);
                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$moveToId','Storage $rwFolder[sl_name] already exist in $rwmoveToName[sl_name].','$date', null,'$host','')") or die('error : ' . mysqli_error($db_con));
                    echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Strg_Nme_Having_Same_Name_Already_Exist'] . '");</script>';
                } else {
                    mysqli_set_charset($db_con, "utf8");
                    $moveToId = preg_replace("/[^A-Za-z0-9 ]/", "", $_POST['lastMoveId']);
                    $lastMoveIdLevel = preg_replace("/[^A-Za-z0-9 ]/", "", $_POST['lastMoveIdLevel']);
                    $lastMoveIdLevel = $lastMoveIdLevel + 1;

                    ////////////////////D/////////////////////
                    $moveToDetail = mysqli_query($db_con, "Select * from tbl_storage_level where sl_id = '$moveToId'");
                    $rwmoveToDetail = mysqli_fetch_assoc($moveToDetail);
                    $storageName = $rwmoveToDetail['sl_name'];
                    $storageName = str_replace(" ", "", $storageName);
                    $storageName = preg_replace('/[^A-Za-z0-9\-]/', '', $storageName);

                    $updir_from = getStoragePath($db_con, $rwcheckDublteStorage['sl_parent_id'], $rwcheckDublteStorage['sl_depth_level']);
                    $updir_to = getStoragePath($db_con, $rwmoveToDetail['sl_parent_id'], $rwmoveToDetail['sl_depth_level']);

                    if (!empty($updir_from)) {
                        $updir_from = $updir_from . '/';
                    } else {
                        $updir_from = '';
                    }
                    if (!empty($updir_to)) {
                        $updir_to = $updir_to . '/';
                    } else {
                        $updir_to = '';
                    }

                    $uploaddir_from = $updir_from . $storageName_From;
                    $dir_from = 'extract-here/' . $uploaddir_from;
                    $uploaddir_to = $updir_to . $storageName;
                    $dir_to = 'extract-here/' . $uploaddir_to . '/' . $storageName_From;

                    if (!is_dir(dirname($dir_to))) {
                        mkdir(dirname($dir_to), 0777, true);
                    }

                    rename($dir_from, $dir_to);

                    $uploadInToFTP = false;
                    if (FTP_ENABLED) {

                        $ftp = new ftp();
                        $ftp->conn("$fileserver", "$port", "$ftpUser", "$ftpPwd");
                        $getAllFile = mysqli_query($db_con, "Select * from tbl_document_master where doc_name = '$slid'");
                        while ($fetchAllFile = mysqli_fetch_assoc($getAllFile)) {
                            $doc_EncryptFile = explode('/', $fetchAllFile['doc_path']);
                            $doc_Encrypt_nm = end($doc_EncryptFile);

                            if ($ftp->get($dir_to . '/' . $doc_Encrypt_nm, 'DMS/' . ROOT_FTP_FOLDER . '/' . $uploaddir_from . '/' . $doc_Encrypt_nm)) {

                                $destinationPath = $uploaddir_to . '/' . $storageName_From . '/' . $doc_Encrypt_nm;
                                $sourcePath = $dir_to . '/' . $doc_Encrypt_nm;

                                $uploadfile = $ftp->put('DMS/' . ROOT_FTP_FOLDER . '/' . $destinationPath, $sourcePath);

                                if ($uploadfile) {
                                    $uploadInToFTP = true;
                                    $ftp->singleFileDelete('DMS/' . ROOT_FTP_FOLDER . '/' . $uploaddir_from . '/' . $doc_Encrypt_nm);
                                    unlink($sourcePath);

                                    $moveFile = "update tbl_document_master set doc_path = '$destinationPath' where doc_id = '" . $fetchAllFile['doc_id'] . "'";
                                    $moveFile_run = mysqli_query($db_con, $moveFile) or die('Error in move Stroge : ' . mysqli_error($db_con));
                                } else {
                                    $uploadInToFTP = false;
                                    echo '<h2>Error:</h2>' . implode('<br />', $arr['error']);
                                }
                                $arr = $ftp->getLogData();
                            }
                        }
                    }


                    ////////////////////D/////////////////////

                    $moveStorage = "update tbl_storage_level set sl_parent_id = '$moveToId', sl_depth_level = '$lastMoveIdLevel' where sl_id = '$slid'";
                    $moveStorage_run = mysqli_query($db_con, $moveStorage) or die('Error in move Stroge : ' . mysqli_error($db_con));

                    $moveToName = mysqli_query($db_con, "Select * from tbl_storage_level where sl_id = '$moveToId'") or die('Error in checkDublteStorage:' . mysqli_error($db_con));
                    $rwmoveToName = mysqli_fetch_assoc($moveToName);

                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`, `sl_id`, `action_name`, `start_date`,`system_ip`, `remarks`) values ('" . $_SESSION['cdes_user_id'] . "', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','$moveToId','Storage moved','$date','$host','Storage $rwFolder[sl_name] moved to $rwmoveToName[sl_name]')") or die('error : ' . mysqli_error($db_con));
                    echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Strge_Moved_Scesfly'] . '");</script>';
                }
            }
            mysqli_close($db_con);
        }
        ?>
    <!--copy storage-->
    <?php
    // if (isset($_POST['copyLevel'], $_POST['token'])) {

    //     if (isset($_POST['toCopyFolder']) && !empty($_POST['toCopyFolder'])) {

    //         $toCopyFolder = $_POST['toCopyFolder'];
    //         if (isset($_POST['lastCopyToId']) && !empty($_POST['lastCopyToId'])) {
    //             $lastCopyToId = $_POST['lastCopyToId'];

    //             // Connect to file server
    //             $fileManager->conntFileServer();
    //             copyStorage($slid, $lastCopyToId, $toCopyFolder, $date, $fileManager, $lang);
    //         }
    //     }
    //     mysqli_close($db_con);
    // }
    if (isset($_POST['copyLevel'], $_POST['token'])) {
    if (isset($_POST['toCopyFolder']) && !empty($_POST['toCopyFolder'])) {
        $toCopyFolder = trim($_POST['toCopyFolder']);
        if (isset($_POST['lastCopyToId']) && !empty($_POST['lastCopyToId'])) {
            $lastCopyToId = trim($_POST['lastCopyToId']);
            copyStorage($slid, $lastCopyToId, $toCopyFolder, $date, $host, $fileserver, $port, $ftpUser, $ftpPwd, $lang, $db_con);
        }
    }
    mysqli_close($db_con);
}

    ?>
    <!--Add Storage Level -->
    <?php
    if (isset($_POST['add_storage'], $_POST['token'])) {
        $sl_id = $_POST['add_child'];
        $create = preg_replace('/[^a-zA-Z0-9-_ ]/', '', mysqli_real_escape_string($db_con, $_POST['create_child']));

        $checkLvlName = mysqli_query($db_con, "select * from tbl_storage_level where sl_parent_id='$sl_id' AND sl_name = '$create'"); //or die('Error in checkLvlName:' . mysqli_error($db_con));

        if (mysqli_num_rows($checkLvlName) > 0) {

            //echo'<script>alert("Storage of Same Name Can not be Created  !");</script>';
            echo '<script>taskFailed("storage?id=' . urlencode(base64_encode($sl_id)) . '","' . $lang['Storage_Name_Already_Exist'] . '");</script>';
        } else {

            $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$sl_id'"); //or die('Error:' . mysqli_error($db_con));

            $rwParent = mysqli_fetch_assoc($parent);

            $level = $rwParent['sl_depth_level'] + 1;
            if (!empty($create)) {
                $sql = "insert into tbl_storage_level(sl_id, sl_name, sl_parent_id, sl_depth_level)VALUES (null, '$create', '$sl_id', '$level')";
                $sql_run = mysqli_query($db_con, $sql) or die("error:" . mysqli_error($db_con));
                $newChildId = mysqli_insert_id($db_con);
                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$newChildId','New Child $create Created.','$date', null,'$host',null)"); //or die('error :' . mysqli_error($db_con));
                echo '<script>taskSuccess("storage?id=' . urlencode(base64_encode($sl_id)) . '","' . $lang['Child_Created_Successfully'] . '");</script>';
            }
        }
        // }
        mysqli_close($db_con);
    }
    ?>
    <!--modify storage level starts-->

    <?php
    if (isset($_POST['update'], $_POST['token']) && intval($_POST['modi'])) {
        $sl_id = preg_replace("/[^0-9]/", "", $_POST['modi']);
        $parentid = preg_replace("/[^0-9]/", "", $_POST['modi_parentId']);
        $modify = preg_replace('/[^a-zA-Z0-9-_ ]/', '', mysqli_real_escape_string($db_con, $_POST['modify_slname']));
        $modify = trim($modify);
        $checkrootfolder = mysqli_query($db_con, "select * from tbl_storage_level where sl_id = '$sl_id' AND sl_depth_level = '0'") or die('Error in check root folder:' . mysqli_error($db_con));
        if (mysqli_num_rows($checkrootfolder) != '1') {
            $checkSlName = mysqli_query($db_con, "select * from tbl_storage_level where sl_parent_id='$parentid' AND sl_id != '$sl_id' AND sl_name='$modify'") or die('Error in check DublteStorage:' . mysqli_error($db_con));
            if (mysqli_num_rows($checkSlName) > 0) {
                echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Strg_of_Same_Nme_Already_Exst'] . '");</script>';
            } else {
                mysqli_set_charset($db_con, 'utf8');
                $modiStorage = mysqli_query($db_con, "Select * from tbl_storage_level where sl_id = '$sl_id'") or die('Error in get sl name:' . mysqli_error($db_con));
                $rwmodiStorage = mysqli_fetch_assoc($modiStorage);
                $updateToName = $rwmodiStorage['sl_name'];
                $sql = "update tbl_storage_level set sl_name = '$modify' WHERE sl_id = '$sl_id' ";
                mysqli_set_charset($db_con, 'utf8');
                $sql_run = mysqli_query($db_con, $sql) or die("error:" . mysqli_errno($db_con));
                if ($sql_run) {
                    mysqli_set_charset($db_con, 'utf8');
                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`sl_id`, `action_name`, `start_date`,  `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','$sl_id','Storage edited','$date', '$host','Storage $updateToName rename to $modify.')") or die('error : ' . mysqli_error($db_con));
                    echo '<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Strg_Updtd_Sucsfly'] . '");</script>';
                } else {
                    echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Strg_Updn_Fld'] . '");</script>';
                }
            }
        } else {
            echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['root_folder_cannot_renamae'] . '");</script>';
        }
        mysqli_close($db_con);
    }
    ?>
    <!---delete storage level start---->
    <?php


    // if (isset($_POST['deleted'], $_POST['token']) && intval($_POST['delsl'])) {

    //     $sl_id = $_POST['delsl'];
    //     $checkdelete = $_POST['deleted'];
    //     //$validateStorage = mysqli_query($db_con, "select * from tbl_storage_level where sl_parent_id='$sl_id'");
    //     //if (mysqli_num_rows($validateStorage) > 0) {
    //     // echo'<script>taskFailed("' . basename($_SERVER[REQUEST_URI]) . '","' . $lang['root_storage_cannot_deleted'] . '");</script>';
    //     //} else {
    //     $deleteStorage = mysqli_query($db_con, "Select * from tbl_storage_level where sl_id = '$sl_id'"); //or die('Error :' . mysqli_error($db_con));
    //     $rwdeleteStorage = mysqli_fetch_assoc($deleteStorage);
    //     $deletStorageName = $rwdeleteStorage['sl_name'];
    //     $dirPath = "extract-here/" . str_replace(" ", "", $$deletStorageName);
    //     $delStrg = mysqli_query($db_con, "Select sl_id from tbl_storagelevel_to_permission where user_id = '$_SESSION[cdes_user_id]'"); //or die('Error :' . mysqli_error($db_con));
    //     $rwdelStrg = mysqli_fetch_assoc($delStrg);
    //     //echo $rwdelStrg['sl_id']; die;
    //     if ($rwdelStrg['sl_id'] != $sl_id) {
    //         //delStrg($sl_id, $fileserver, $port, $ftpUser, $ftpPwd);
    //         //rmdir($dirPath);
    //         if ($checkdelete == 'yes') {

    //             // Connect to file server
    //             $fileManager->conntFileServer();

    //             mysqli_query($db_con, "DELETE FROM tbl_storage_level WHERE sl_id='$sl_id'"); //or die('Error:' . mysqli_error($db_con));

    //             deleteDocument($db_con, $sl_id, $dirPath, $fileManager);

    //             deleteSubFolders($db_con, $sl_id, $fileManager, $checkdelete);

    //             rmdir($dirPath);
    //         } else {

    //             mysqli_query($db_con, "UPDATE tbl_storage_level set delete_status=1 WHERE sl_id='$sl_id'");

    //             moveFilesInRecycleBin($db_con, $sl_id, 3);

    //             moveStorageInRecycleBin($db_con, $sl_id);
    //         }

    //         $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$sl_id','Storage deleted','$date', null,'$host','Storage Name $deletStorageName deleted.')"); //or die('error :' . mysqli_error($db_con));
    //         $delParentId = $rwdeleteStorage['sl_parent_id'];

    //         echo '<script>taskSuccess("' . basename($_SERVER['SCRIPT_NAME']) . '?id=' . urlencode(base64_encode($delParentId)) . '","' . $lang['Strg_Deleted_Successfully'] . '");</script>';
    //     } else {
    //         echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['root_storage_cannot_deleted'] . '");</script>';
    //     }
    //     //}
    //     mysqli_close($db_con);
    // }

    ?>
    <?php
            if (isset($_POST['deleted'], $_POST['token']) && intval($_POST['delsl'])) {
            $sl_id = $_POST['delsl'];
            $checkdelete = $_POST['deleted'];
            $deleteStorage = mysqli_query($db_con, "Select * from tbl_storage_level where sl_id = '$sl_id'"); //or die('Error :' . mysqli_error($db_con));
            $rwdeleteStorage = mysqli_fetch_assoc($deleteStorage);
            $deletStorageName = $rwdeleteStorage['sl_name'];
            $dirPath = "extract-here/" . str_replace(" ", "", $$deletStorageName);
            $delStrg = mysqli_query($db_con, "Select sl_id from tbl_storagelevel_to_permission where user_id = '$_SESSION[cdes_user_id]'"); //or die('Error :' . mysqli_error($db_con));
            $rwdelStrg = mysqli_fetch_assoc($delStrg);
            if ($rwdelStrg['sl_id'] != $sl_id) {
                if ($checkdelete == 'yes') {
                    mysqli_query($db_con, "DELETE FROM tbl_storage_level WHERE sl_id='$sl_id' and (sl_parent_id!='0' and sl_depth_level!='0')"); //or die('Error:' . mysqli_error($db_con));
                    deleteDocument($db_con, $sl_id, $dirPath, $fileserver, $port, $ftpUser, $ftpPwd);
                    deleteSubFolders($db_con, $sl_id, $fileserver, $port, $ftpUser, $ftpPwds, $checkdelete);
                    rmdir($dirPath);
                } else {
                    mysqli_query($db_con, "UPDATE tbl_storage_level set delete_status=1 WHERE sl_id='$sl_id'");
                    moveFilesInRecycleBin($db_con, $sl_id, 3);
                    moveStorageInRecycleBin($db_con, $sl_id);
                }

                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`, `sl_id`, `action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','$sl_id','Subfolder deleted','$date','$host','Storage Name $deletStorageName deleted.')") or die('error :' . mysqli_error($db_con));
                $delParentId = $rwdeleteStorage['sl_parent_id'];
                echo'<script>taskSuccess("' . basename($_SERVER['SCRIPT_NAME']) . '?id=' . urlencode(base64_encode($delParentId)) . '","' . $lang['Strg_Deleted_Successfully'] . '");</script>';
            } else {
                echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['root_storage_cannot_deleted'] . '");</script>';
            }
            mysqli_close($db_con);
        }
        ?>
    <!---delete multiple storage level start---->
    <?php

    if (isset($_POST['multideleted'], $_POST['token'])) {
        // echo "rohitttt";

        // $sl_ids = $_POST['delmultplesl'];
        // echo "$sl_ids";
        // $sl_ids = explode(',', $sl_ids);
        // $sl_id_array = array_unique($sl_ids);
        // sort($sl_id_array);
        // print_r($sl_id_array);
        $sl_ids = $_POST['delmultplesl'];
        // echo "$sl_ids";

        // Debugging
        //var_dump($sl_ids);

        $sl_ids = explode(',', $sl_ids);
        // Debugging
        //var_dump($sl_ids);
        // echo "Array Length: " . count($sl_ids);
        $sl_id_array = array_unique($sl_ids);
        // echo "Array Length: " . count( $sl_id_array);
        // Debugging
        //var_dump($sl_id_array);

        // sort($sl_id_array);
        // print_r($sl_id_array);

        // $sl_id_array = explode(',', $sl_ids);
        $checkdelete = $_POST['multideleted'];
        //echo "ankitttt";


        //die("okayyy");
        // $sl_id = preg_replace("/[^A-Za-z0-9 ]/", "", $_POST['delsl']);
        //$sl_id = mysqli_real_escape_string($db_con, $sl_id);
        foreach ($sl_id_array as $sl_id) {
            if($sl_id != '113')
{
            $deleteStorage = mysqli_query($db_con, "Select * from tbl_storage_level where sl_id = '$sl_id'"); //or die('Error :' . mysqli_error($db_con));

            $rwdeleteStorage = mysqli_fetch_assoc($deleteStorage);
            $slname = str_replace(" ", "", $rwdeleteStorage['sl_name']);
            $updir = getStoragePath($db_con, $rwdeleteStorage['sl_parent_id'], $rwdeleteStorage['sl_depth_level']);

            if (!empty($updir)) {
                $updir = $updir . '/';
            } else {
                $updir = '';
            }

            $deletStorageName = $rwdeleteStorage['sl_name'];

            $dirPath = "extract-here/" . $updir . $slname;
            $delStrg = mysqli_query($db_con, "Select sl_id from tbl_storagelevel_to_permission where user_id = '$_SESSION[cdes_user_id]'"); //or die('Error :' . mysqli_error($db_con));
            $rwdelStrg = mysqli_fetch_assoc($delStrg);


            if ($rwdelStrg['sl_id'] != $sl_id && $sl_id != '113') {

                mysqli_set_charset($db_con, "utf8");

                if ($checkdelete == 'yes') {
                    // Connect to file server
                    $fileManager->conntFileServer();

                    mysqli_query($db_con, "DELETE FROM tbl_storage_level WHERE sl_id='$sl_id'"); //or die('Error:' . mysqli_error($db_con));

                    deleteDocument($db_con, $sl_id, $dirPath, $fileManager);

                    deleteSubFolders($db_con, $sl_id, $fileManager, $checkdelete);

                    //rmdir($dirPath);

                } else {

                    mysqli_query($db_con, "UPDATE tbl_storage_level set delete_status=1 WHERE sl_id='$sl_id'");

                    moveFilesInRecycleBin($db_con, $sl_id, 3);

                    moveStorageInRecycleBin($db_con, $sl_id);
                }

                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$sl_id','Storage Name $deletStorageName deleted.','$date', null,'$host','')") or die('error :' . mysqli_error($db_con));
                $delParentId = $rwdeleteStorage['sl_parent_id'];
                // echo '<script>taskSuccess("storage?id=' . urlencode(base64_encode($delParentId)) . '","' . $lang['Strg_Deleted_Successfully'] . '");</script>';
            } else {
                //echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['root_storage_cannot_deleted'] . '");</script>';
            }
        }
        }
        echo '<script>taskSuccess("storage?id=' . urlencode(base64_encode($delParentId)) . '","' . $lang['Strg_Deleted_Successfully'] . '");</script>';
        mysqli_close($db_con);
    }

    ?>
    <!---delete multiple storage level ends---->
    <?php
    if (isset($_POST['bulkDownload'], $_POST['token'])) {

        $rad = preg_replace("/[^A-Za-z0-9, ]/", "", $_POST['raddwn']);
        $rad = mysqli_real_escape_string($db_con, $rad);
        $slid = preg_replace("/[^A-Za-z0-9, ]/", "", $_POST['slid']);
        $slid = mysqli_real_escape_string($db_con, $slid);
        //$reason = preg_replace("/[^A-Za-z0-9, ]/", "", $_POST['reason']);
        $reason = xss_clean($_POST['reason']);
        $reason = mysqli_real_escape_string($db_con, $reason);
        $archive_file_name = $slName . '.zip';

        $download = mysqli_query($db_con, "select doc_path,old_doc_name,doc_extn,doc_id from tbl_document_master where doc_name='$slid' and flag_multidelete=1"); // or die('Error'.mysqli_error($db_con));
        $zip = new ZipArchive();
        //create the file and throw the error if unsuccessful
        if ($zip->open($archive_file_name, ZIPARCHIVE::CREATE) !== TRUE) {
            exit("cannot open <$archive_file_name>\n");
        }
        $zippedFilePath = array();
        // Connect to file server
        $fileManager->conntFileServer();
        while ($row = mysqli_fetch_assoc($download)) {
            $docPath = $row['doc_path'];
            $file_path = 'extract-here/' . substr($docPath, 0, strrpos($docPath, "/") + 1);
            $files = substr($docPath, strrpos($docPath, "/") + 1);
            $comp_folder = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id = '$slid'"); //or die('Error :' . mysqli_error($db_con));
            $rwfolder = mysqli_fetch_assoc($comp_folder);
            $file1 = $row['old_doc_name'];
            //$file1 = $row['old_doc_name'] . '.' . $row['doc_extn'];

            if (!is_dir($file_path)) {
                mkdir($file_path, 0777, TRUE) or die(print_r(error_get_last()));
            }

            if (!file_exists('extract-here/' . $docPath)) {
                if ($fileManager->downloadFile( 'DMS/' . ROOT_FTP_FOLDER . '/' . $docPath,  'extract-here/' . $docPath)) {
                    decrypt_my_file('extract-here/' . $docPath);
                    if ($zip->addFile($file_path . $files, $file1)) {
                        $zippedFilePath[] = 'extract-here/' . $docPath;
                    }
                }
            } else {
                decrypt_my_file('extract-here/' . $docPath);
                if ($zip->addFile($file_path . $files, $file1)) {
                    $zippedFilePath[] = 'extract-here/' . $docPath;
                }
            }
        }
        if ($zip->close()) {
            /* if (FTP_ENABLED) {
                    foreach ($zippedFilePath as $key => $value) {

                        unlink($zippedFilePath[$key]);
                    }
                } */
        }
        //then send the headers to foce download the zip file
        header("Content-type: application/zip");
        header("Content-Disposition: attachment; filename=$archive_file_name");
        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `doc_id`,`action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$slid', '$row[doc_id]','Storage document $old_doc_name compress to Storage $rwfolder[sl_name] with $row[old_doc_name].','$date',null,'$host','$reason')"); //or die('error : ' . mysqli_error($db_con));
        header("Pragma: no-cache");
        header("Expires: 0");
        readfile("$archive_file_name");
        unlink($archive_file_name);
        exit;
        mysqli_close($db_con);
    }


    if (isset($_POST['shareFolder'], $_POST['token'])) {
        // print_r($_POST);
        $sharewithUsers = $_POST['sharewith'];
        $readonly = ((!empty($_POST['readonly'])) ? 1 : 0);

        $sharewith = implode(',', $sharewithUsers);
        $slId = $_POST['slId'];
        $folder = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid'");
        $rwFolder = mysqli_fetch_assoc($folder);
        $shareby = $_SESSION['cdes_user_id'];
        for ($k = 0; $k < count($sharewithUsers); $k++) {
            $check = mysqli_query($db_con, "select * from tbl_folder_share where share_with='$sharewithUsers[$k]' and slId='$slId'"); //or die('Error :' . mysqli_error($db_con));
            if (mysqli_num_rows($check) > 0) {
                echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['folder_already_share'] . '");</script>';
            } else {
                $sql = mysqli_query($db_con, "INSERT INTO tbl_folder_share (slId, share_with, share_by) values('$slId', '$sharewithUsers[$k]', '$shareby')"); //or die('Error :' . mysqli_error($db_con));
                if ($sql) {
                    $username = mysqli_query($db_con, "select first_name,last_name from tbl_user_master where user_id='$sharewithUsers[$k]'");
                    $rwusername = mysqli_fetch_assoc($username);
                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `doc_id`,`action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$slid', NULL,'$rwFolder[sl_name] storage Shared with $rwusername[first_name] $rwusername[last_name]','$date',null,'$host',NULL)"); //or die('error : ' . mysqli_error($db_con));
                    if (checkFolderPermission($db_con, $sharewithUsers[$k], $slId)) {

                        $slins = mysqli_query($db_con, "insert into tbl_storagelevel_to_permission(user_id,sl_id,shared,readonly) values('$sharewithUsers[$k]','$slId', '1', '$readonly')");
                        if ($readonly) {
                            $slins = mysqli_query($db_con, "update tbl_storage_level set readonly='$readonly' where sl_id='$slId'");
                        }
                    } else {
                    }

                    echo '<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Folder_Share_success'] . '");</script>';
                }
            }
        }
    }

    function checkFolderPermission($db_con, $userId, $slId)
    {
        $result1 = mysqli_query($db_con, "select * from tbl_storagelevel_to_permission where user_id='$userId' and sl_id='$slId'");
        if (mysqli_num_rows($result1) > 0) {
            return false;
        } else {
            $result = mysqli_query($db_con, "select * from tbl_storagelevel_to_permission where user_id='$userId'");
            $slArray = array();
            while ($rowP = mysqli_fetch_assoc($result)) {
                $checkPermission = mysqli_query($db_con, "select sl_parent_id from tbl_storage_level where sl_parent_id ='" . $rowP['sl_id'] . "' and sl_id='$slId'");
                if (mysqli_num_rows($checkPermission) > 0) {
                    $slArray[] = $rowP['sl_id'];
                } else {
                }
            }
            if (count($slArray) > 0) {
                return false;
            } else {
                return true;
            }
        }
    }



    //        if (isset($_POST['update_folder_pass'], $_POST['token'])) {
    //            $lockslId = $_POST['lockslId'];
    //            $strgChlid = findChildss($lockslId);
    //            $allChilds = implode(',', $strgChlid);
    //
    //            $old_pass = $_POST['old_pass'];
    //            $password = $abs['password'];
    //            $new_pass = $_POST['new_pass'];
    //
    //            $fpass = SHA1($old_pass);
    //
    //            if ($password == $fpass) {
    //
    //                $unlock = mysqli_query($db_con, "UPDATE `tbl_storage_level` set password=sha1('$new_pass') where sl_id IN ($allChilds)")or die(mysqli_error($db_con));
    //
    //                echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['folder_password_updated'] . '");</script>';
    //            } else {
    //                echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['failed_update_pass'] . '");</script>';
    //            }
    //        }
    ?>

    <script>
        $(document).ready(function() {
            var max_fields = <?= $metadatacount; ?>; //maximum input boxes allowed
            var wrapper = $(".contents"); //Fields wrapper
            var add_button = $("#addfields"); //Add button ID
            var id = <?= $slid ?>;

            var x = 1; //initlal text box count
            $(add_button).click(function(e) { //on add input button click
                e.preventDefault();

                if (x < max_fields) { //max input box allowed
                    x++;
                    //text box increment
                    $.ajax({
                        url: "application/ajax/addmultimetadataStoregefile?id=" + id,
                        success: function(result) {
                            $(wrapper).append("<div class='col-lg-12' style='margin-bottom:17px'>" + result + "<button class='remove_field btn btn-primary' title='Remove'><i class='fa fa-minus-circle' aria-hidden='true'></i></a>" + "</div>"); //add input box


                        }
                    });

                } else {
                    alert("<?php echo $lang['No_Mor_mta_dat_avlbl']; ?>");
                    $("#addfields").hide();
                }
            });

            $(wrapper).on("click", ".remove_field", function(e) { //user click on remove text
                e.preventDefault();
                $(this).parent('div').remove();
                x--;
                $("#addfields").show();
            })
        });

        $(".select2").select2();

        $("#bulkdowns").click(function() {
            setTimeout(function() {
                location.reload();
            }, 2000);

        });
    </script>
    <!---end add and search metadata-->


</body>

</html>