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
require_once './classes/ftp.php';
require_once './application/pages/function.php';


/* for file find in workflow and version of a same file
     * SELECT doc_name,substring_index(doc_name,'_',-1) FROM `ezeefiledms`.`tbl_document_master` where substring_index(doc_name,'_',-1)=7 and substring_index(doc_name,'_',1)=113;
      SELECT doc_id,doc_name,substring_index(doc_name,'_',1) FROM `ezeefiledms`.`tbl_document_master` where substring_index(doc_name,'_',1)=113;
     * 
     */
//for user role
$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));
$rwgetRole = mysqli_fetch_assoc($chekUsr);

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
    $folder = mysqli_query($db_con, "select * from tbl_storage_level where sl_depth_level='0'");
}
$rwFolder = mysqli_fetch_assoc($folder);
$slid = $rwFolder['sl_id'];
$slName = $rwFolder['sl_name'];
$parentid = $rwFolder['sl_parent_id'];
?>


<?php
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
                            <li><a href="storage?id=<?php echo urlencode(base64_encode($slperm)); ?>"><?php echo $lang['Storage_Manager']; ?></a></li>

                            <?php
                            parentLevel($slid, $db_con, $slpermIdes, $level);

                            function parentLevel($slid, $db_con, $slperm, $level)
                            {
                                $flag = 0;
                                $slPermIds = explode(',', $slperm);
                                if (in_array($slid, $slperm)) {
                                    $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid'"); //or die('Error' . mysqli_error($db_con));
                                    $rwParent = mysqli_fetch_assoc($parent);

                                    if ($level < $rwParent['sl_depth_level']) {
                                        parentLevel($rwParent['sl_parent_id'], $db_con, $slperm, $level);
                                    }
                                    $flag = 1;
                                } else {
                                    $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid' and sl_parent_id='$slperm'"); //or die('Error' . mysqli_error($db_con));
                                    if (mysqli_num_rows($parent) > 0) {

                                        $rwParent = mysqli_fetch_assoc($parent);
                                        if ($level < $rwParent['sl_depth_level']) {
                                            parentLevel($rwParent['sl_parent_id'], $db_con, $slperm, $level);
                                        }
                                        $flag = 1;
                                        $flag = 1;
                                    } else {
                                        $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid'"); //or die('Error' . mysqli_error($db_con));
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
                            <div class="box-header with-border">
                                <div class="col-md-10 col-lg-10 col-sm-10 col-xs-10 m-t-10">
                                    <a href="javascript:void(0);" id="viewChange" title="Change View"><i class="fa fa-list"></i> <b><?php echo $lang['change_view']; ?></b></a>
                                </div>
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



                                            <!-- Ankit -->

                                            <?php if ($rwgetRole['bulkfol_download'] == '1') { ?>
                                                <li><a href="javascript:void(0)" data-toggle="modal" data-target="#bulkfoldownload"><?php echo $lang['Blk_Dwnld_Folder']; ?></a></li>
                                            <?php } ?>
                                            <!-- may 15 Ankit -->
                                            <?php if ($rwgetRole['upload_doc_storage'] == '1') { ?>
                                                <li><a href="adddocument?id=<?php echo urlencode(base64_encode($slid)); ?>"><?php echo $lang['Upld_Docmnt']; ?> </a></li>
                                            <?php } ?>
                                            <?php if ($rwgetRole['create_child_storage'] == '1') { ?>
                                                <li><a href="javascript:void(0)" data-toggle="modal" data-target="#con-close-modal1"><?php echo $lang['Crt_New_Cld']; ?></a></li>
                                            <?php } ?>
                                            <?php if ($rwgetRole['modify_storage_level'] == '1') { ?>
                                                <li><a href="javascript:void(0)" data-toggle="modal" data-target="#con-close-modal"><?php echo $lang['Modify_Storage']; ?></a></li>
                                            <?php } ?>
                                            <?php if ($rwgetRole['delete_storage_level'] == '1') { ?>
                                                <li><a href="javascript:void(0)" data-toggle="modal" data-target="#con-close-modal2"><?php echo $lang['Dlt_Storage']; ?></a></li>
                                            <?php } ?>
                                            <?php if ($rwgetRole['assign_metadata'] == '1') { ?>
                                                <li><a href="javascript:void(0)" data-toggle="modal" data-target="#con-close-modal5"><?php echo $lang['Asgn_MetaData']; ?></a></li>
                                            <?php } ?>
                                            <?php if ($rwgetRole['move_storage_level'] == '1') { ?>
                                                <li><a href="javascript:void(0)" data-toggle="modal" data-target="#con-close-modal4"><?php echo $lang['Move_Storage']; ?></a></li>
                                            <?php } ?>

                                            <?php if ($rwgetRole['copy_storage_level'] == '1') { ?>
                                                <li><a href="javascript:void(0)" data-toggle="modal" data-target="#con-close-modal6"><?php echo $lang['Cpy_Storage']; ?></a></li>
                                        <?php
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
                                <!--h4 id="event_result" class="header-title" style="display: inline-block;">Selected Folder : <strong><?php //echo $slName = $rwFolder['sl_name'];                
                                                                                                                                        ?></strong></h4-->
                            </div>

                            <div class="box-body" style="">
                                <div class="col-md-3" style="overflow: auto;">
                                    <div class="card-box">
                                        <div id="basicTree">
                                            <ul>
                                                <?php
                                                $sllevelTree = mysqli_query($db_con, "select * from tbl_storage_level where sl_id in($slpermIdes)");
                                                while ($rwSllevelTree = mysqli_fetch_assoc($sllevelTree)) {
                                                    $level = $rwSllevelTree['sl_depth_level'];
                                                    $permSlId = $rwSllevelTree['sl_id'];
                                                    $slParentId = $rwSllevelTree['sl_parent_id'];
                                                    if (isset($_GET['id']) && !empty($_GET['id'])) {
                                                        storageLevelS($level, $db_con, $slid, $slParentId, $permSlId);
                                                    } else {
                                                        storageLevelS($level, $db_con, $slid, $slParentId, $permSlId);
                                                    }
                                                }
                                                ?>
                                            </ul>
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
                                                    $metadatacount = 0;
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

                                    <?php ?>
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
                                    function findTotalFile($slperm)
                                    {
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
                                    $totalFiles = findTotalFile($rwFolder['sl_id']);

                                    ?>

                                    <div class="box box-primary sel" style="margin-bottom:90px;">
                                        <h4 id="event_result" class="header-title" style="display: inline-block;"><?php echo $lang['Slt_Folder']; ?> : <strong><?php echo $rwFolder['sl_name']; ?></strong>(<?php echo $lang['folders'] . ' = ' . (((!empty($totalFiles["totalFolder"]) ? $totalFiles["totalFolder"] : "0"))) . ',' . $lang['files'] . '= ' . $totalFiles["files"] . ', ' . $lang['Total_size'] . '= ' . (($totalFiles['fileSize'] > 999) ? round($totalFiles['fileSize'] / 1024, 2) : $totalFiles['fileSize']) . (($totalFiles['fileSize'] > 999) ? $lang['GB'] : $lang['MB']) . ' & ' . $lang['pages'] . '=' . $totalFiles["numPages"]; ?>)</h4>

                                        <div class="box-body">

                                            <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12" style="padding: 0;">

                                                <?php
                                                if (empty($slperm)) {
                                                    echo '<script>alert("' . $lang['Op_u_r_nt_athrsed_to_acs_ts_strg'] . '"); window.open("index","_parent");</script>';
                                                }

                                                storageLevelName($db_con, $slid, $lang);
                                                ?>
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

                                                    <a href="storageFiles?id=<?php echo urlencode(base64_encode($slid)); ?>" data-target="_blank" style="display: none;" class="dropdown-toggle waves-effect waves-light col-md-2 col-lg-2 col-sm-2 col-xs-2 view2">

                                                        <i class="md md-my-library-books"></i>
                                                        <span><?php echo $data['total']; ?> <?php echo $lang['Files']; ?></span> <span class="pull-right"> </span>
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
                                    <input type="hidden" value="<?php echo base64_decode(urldecode($_GET['id'])); ?>" name="id">
                                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                                    <button class="btn btn-primary waves-effect waves-light" type="submit" name="assignMeta"><?php echo $lang['Submit']; ?></button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div><!--ends assign-meta-data modal -->



                <?php require_once './application/pages/footer.php'; ?>

            </div>
            <!-- Right Sidebar -->
            <?php require_once './application/pages/rightSidebar.php'; ?>
            <!-- /Right-bar -->
        </div>
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
        <script>
            $('#basicTree')
                // listen for event
                .on('changed.jstree', function(e, data) {
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
                    $(".cheekced").parentsUntil("div").map(function() {
                        var tags = $(this)[0].tagName;
                        console.log(tags);
                        if (tags == 'LI') {
                            var tag = this.childNodes[1].style.background = '#beebff'; //$(this).nth-child(2).prop('tagName');
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
                            <button type="submit" name="delete" class="btn btn-danger"><i class="fa fa-trash-o"></i> <?php echo $lang['Delete'] ?> </button>
                        </div>
                    </form>
                </div>
            </div>
        </div><!--ends delete modal -->
        <!--for move level-->
        <div id="con-close-modal4" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
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
                                $moveFolderName = mysqli_query($db_con, "select * from tbl_storage_level where sl_id = $slid") or die('Error in move folder name: ' . mysqli_error($db_con));
                                $rwmoveFolderName = mysqli_fetch_assoc($moveFolderName);
                                ?>

                                <label><?php echo $rwmoveFolderName['sl_name']; ?> : <?php echo $lang['Move_Fld_File']; ?> </label>
                                <br>
                                <div class="col-md-12">
                                    <label><?php echo $lang['Move_To']; ?></label>
                                    <select class="form-control select2" name="moveToParentId" id="parentMoveLevel">
                                        <option selected disabled style="background: #808080; color: #fff;"><?php echo $lang['Sel_Strg_Lvl']; ?></option>
                                        <?php
                                        $storeName = mysqli_query($db_con, "select * from tbl_storage_level where sl_depth_level= '0' AND sl_id != '$slid'") or die('Error in move store: ' . mysqli_error($db_con));

                                        while ($rwstoreName = mysqli_fetch_assoc($storeName)) {
                                        ?>
                                            <option value="<?php echo $rwstoreName['sl_id']; ?>"><?php echo $rwstoreName['sl_name']; ?></option>
                                        <?php } ?>
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
        <div id="con-close-modal6" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
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
                                    <select class="form-control select2" name="copyToParentId" id="parentCopyLevel">
                                        <option selected style="background: #808080; color: #fff;"><?php echo $lang['Sel_Strg_Lvl']; ?></option>
                                        <?php
                                        $storeName = mysqli_query($db_con, "select * from tbl_storage_level where sl_depth_level= '0' AND sl_id != '$slid'") or die('Error in move store: ' . mysqli_error($db_con));

                                        $rwstoreName = mysqli_fetch_assoc($storeName)
                                        ?>
                                        <option value="<?php echo $rwstoreName['sl_id']; ?>"><?php echo $rwstoreName['sl_name']; ?></option>
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
        <!-- ankit 15may -->

        <?php
        //ankit 10/05
        $idchild = findsubfolder($slid, $db_con);
        $idchild = implode(',', $idchild);

        // //end ankit 10/05
        $validate = mysqli_query($db_con, "select doc_path,old_doc_name,doc_extn,doc_id from tbl_document_master where doc_name in ($idchild) and flag_multidelete=1");
        if (mysqli_num_rows($validate) > 0) {
        ?>
            <div id="bulkfoldownload" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h4 class="modal-title">Downloads All Files of Selected Folder( <?php echo $slName; ?>) and their Sub Folder</h4>
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
                                <button type="submit" name="bulkfoldownl" id="bulkdowfol" class="btn btn-primary"><i class="fa fa-download"></i> <?php echo $lang['Download'] ?></button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        <?php } else { ?>
            <div id="bulkfoldownload" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog">
                    <div class="panel panel-color panel-danger">
                        <div class="panel-heading">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h2 class="panel-title"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i><?php echo $lang['Hre_msge']; ?></h2>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="text-alert"><?php echo $lang['No_Files_Ext_in_Slt_Storage_fld_sub_folder']; ?></label>
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
        <!-- ankit 15m may end -->
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
                $metaDataNames = implode(",", $metaDataNames);
                $strgeName = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id = '$sl_id'");
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
    if (isset($_POST['move'], $_POST['token'])) {

        //echo $_POST['moveToId']; die;

        if (!empty($_POST['lastMoveId'])) {

            $checkDublteStorage = mysqli_query($db_con, "Select * from tbl_storage_level where sl_id = '$slid'"); //or die('Error in checkDublteStorage:' . mysqli_error($db_con));

            $rwcheckDublteStorage = mysqli_fetch_assoc($checkDublteStorage);

            $sql_child = "select * FROM tbl_storage_level WHERE sl_parent_id = '$_POST[lastMoveId]' AND sl_name = '$rwcheckDublteStorage[sl_name]'";

            $sql_child_run = mysqli_query($db_con, $sql_child); //or die('Error:' . mysqli_error($db_con));

            if (mysqli_num_rows($sql_child_run)) {
                $moveToId = $_POST['lastMoveId'];
                $moveToName = mysqli_query($db_con, "Select * from tbl_storage_level where sl_id = '$moveToId'"); //or die('Error in checkDublteStorage:' . mysqli_error($db_con));
                $rwmoveToName = mysqli_fetch_assoc($moveToName);
                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$moveToId','Storage $rwFolder[sl_name] already exist in $rwmoveToName[sl_name].','$date', null,'$host',null)"); //or die('error : ' . mysqli_error($db_con));
                echo '<script>taskFailed("storage","Storage Name Having Same Name Already Exist !");</script>';
            } else {
                $moveToId = $_POST['lastMoveId'];
                $lastMoveIdLevel = $_POST['lastMoveIdLevel'];
                $lastMoveIdLevel = $lastMoveIdLevel + 1;

                $moveStorage = "update tbl_storage_level set sl_parent_id = '$moveToId', sl_depth_level = '$lastMoveIdLevel' where sl_id = '$slid'";
                $moveStorage_run = mysqli_query($db_con, $moveStorage); //or die('Error in move Stroge : ' . mysqli_error($db_con));

                $moveToName = mysqli_query($db_con, "Select * from tbl_storage_level where sl_id = '$moveToId'"); //or die('Error in checkDublteStorage:' . mysqli_error($db_con));
                $rwmoveToName = mysqli_fetch_assoc($moveToName);

                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$moveToId','Storage $rwFolder[sl_name] moved to $rwmoveToName[sl_name]','$date',null,'$host',null)"); //or die('error : ' . mysqli_error($db_con));
                echo '<script>taskSuccess("storage","' . $lang['Strge_Moved_Scesfly'] . '");</script>';
            }
        }
        mysqli_close($db_con);
    }
    ?>
    <!--copy storage-->
    <?php
    if (isset($_POST['copyLevel'], $_POST['token'])) {

        if (isset($_POST['toCopyFolder']) && !empty($_POST['toCopyFolder'])) {

            $toCopyFolder = $_POST['toCopyFolder'];
            if (isset($_POST['lastCopyToId']) && !empty($_POST['lastCopyToId'])) {
                $lastCopyToId = $_POST['lastCopyToId'];

                copyStorage($slid, $lastCopyToId, $toCopyFolder, $date, $host, $fileserver, $port, $ftpUser, $ftpPwd, $lang);
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
        $sl_id = mysqli_real_escape_string($db_con, $_POST['modi']);
        $modify = mysqli_real_escape_string($db_con, $_POST['modify_slname']);
        $modiStorage = mysqli_query($db_con, "Select * from tbl_storage_level where sl_id = '$sl_id'"); //or die('Error in checkDublteStorage:' . mysqli_error($db_con));
        $rwmodiStorage = mysqli_fetch_assoc($modiStorage);
        $updateToName = $rwmodiStorage['sl_name'];

        $sql = "update tbl_storage_level set sl_name = '$modify' WHERE sl_id = '$sl_id' ";
        $sql_run = mysqli_query($db_con, $sql); //or die("error:" . mysqli_errno($db_con));
        if ($sql_run) {
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$sl_id','Storage Name $updateToName rename to $modify.','$date', null,'$host','')"); //or die('error : ' . mysqli_error($db_con));
            echo '<script>taskSuccess("storage?id=' . urlencode(base64_encode($sl_id)) . '","' . $lang['Strg_Updtd_Sucsfly'] . '");</script>';
        } else {
            echo '<script>taskFailed("storage?id=' . urlencode(base64_encode($sl_id)) . '","' . $lang['Strg_Updn_Fld'] . '");</script>';
        }
        mysqli_close($db_con);
    }
    ?>

    <!---delete storage level start---->
    <?php
    if (isset($_POST['delete'], $_POST['token']) && intval($_POST['delsl'])) {
        $sl_id = $_POST['delsl'];
        //$validateStorage = mysqli_query($db_con, "select * from tbl_storage_level where sl_parent_id='$sl_id'");
        //if (mysqli_num_rows($validateStorage) > 0) {
        // echo'<script>taskFailed("' . basename($_SERVER[REQUEST_URI]) . '","' . $lang['root_storage_cannot_deleted'] . '");</script>';
        //} else {
        $deleteStorage = mysqli_query($db_con, "Select * from tbl_storage_level where sl_id = '$sl_id'"); //or die('Error :' . mysqli_error($db_con));
        $rwdeleteStorage = mysqli_fetch_assoc($deleteStorage);
        $deletStorageName = $rwdeleteStorage['sl_name'];
        $dirPath = "extract-here/" . ROOT_FTP_FOLDER . "/" . $deletStorageName;
        $delStrg = mysqli_query($db_con, "Select sl_id from tbl_storagelevel_to_permission where user_id = '$_SESSION[cdes_user_id]'"); //or die('Error :' . mysqli_error($db_con));
        $rwdelStrg = mysqli_fetch_assoc($delStrg);
        //echo $rwdelStrg['sl_id']; die;
        if ($rwdelStrg['sl_id'] != $sl_id) {
            delStrg($sl_id, $fileserver, $port, $ftpUser, $ftpPwd);
            rmdir($dirPath);
            mysqli_query($db_con, "DELETE FROM tbl_storage_level WHERE sl_id='$sl_id' and (sl_parent_id!='0' and sl_depth_level!='0')"); //or die('Error:' . mysqli_error($db_con));
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$sl_id','Storage Name $deletStorageName deleted.','$date', null,'$host','')"); //or die('error :' . mysqli_error($db_con));
            $delParentId = $rwdeleteStorage['sl_parent_id'];
            echo '<script>taskSuccess("' . basename($_SERVER['SCRIPT_NAME']) . '?id=' . urlencode(base64_encode($delParentId)) . '","' . $lang['Strg_Deleted_Successfully'] . '");</script>';
        } else {
            echo '<script>taskFailed("' . basename($_SERVER[REQUEST_URI]) . '","' . $lang['root_storage_cannot_deleted'] . '");</script>';
        }
        //}
        mysqli_close($db_con);
    }
    ?>

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
        while ($row = mysqli_fetch_assoc($download)) {
            $docPath = $row['doc_path'];
            $file_path = 'extract-here/' . substr($docPath, 0, strrpos($docPath, "/") + 1);
            $files = substr($docPath, strrpos($docPath, "/") + 1);
            $comp_folder = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id = '$slid'"); //or die('Error :' . mysqli_error($db_con));
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
        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `doc_id`,`action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$slid', '$row[doc_id]','Storage document $old_doc_name compress to Storage $rwfolder[sl_name] with $row[old_doc_name].','$date',null,'$host','$reason')"); //or die('error : ' . mysqli_error($db_con));
        header("Pragma: no-cache");
        header("Expires: 0");
        readfile("$archive_file_name");
        unlink($archive_file_name);
        exit;
        mysqli_close($db_con);
    }


    // Ankit may 15 2023
    if (isset($_POST['bulkfoldownl'], $_POST['token'])) {
        $rad = preg_replace("/[^A-Za-z0-9, ]/", "", $_POST['raddwn']);
        $rad = mysqli_real_escape_string($db_con, $rad);
        $slid = preg_replace("/[^A-Za-z0-9, ]/", "", $_POST['slid']);
        $slid = mysqli_real_escape_string($db_con, $slid);
        //$reason = preg_replace("/[^A-Za-z0-9, ]/", "", $_POST['reason']);
        $reason = xss_clean($_POST['reason']);


        $reason = mysqli_real_escape_string($db_con, $reason);
        $archive_file_name = $slName . '.zip';
        //ankit 10/05
        $idchild = findsubfolder($slid, $db_con);
        $idchild = implode(',', $idchild);

        // //end ankit 10/05

        $download = mysqli_query($db_con, "select doc_name,doc_path,old_doc_name,doc_extn,doc_id from tbl_document_master where doc_name in ($idchild) and flag_multidelete=1 order by doc_name asc") or die('Error' . mysqli_error($db_con));

        //echo "select doc_name,doc_path,old_doc_name,doc_extn,doc_id from tbl_document_master where doc_name in ($idchild) and flag_multidelete=1 order by doc_name asc ";

        $zip = new ZipArchive();
        //create the file and throw the error if unsuccessful
        if ($zip->open($archive_file_name, ZIPARCHIVE::CREATE) !== TRUE) {
            exit("cannot open <$archive_file_name>\n");
        }
        $zippedFilePath = array();

        while ($row = mysqli_fetch_assoc($download)) {
            $folder_name = array(); // reset the folder name variable for each iteration
            $docPath = $row['doc_path'];
            $sldocid = $row['doc_name'];
            $file_path = 'extract-here/' . substr($docPath, 0, strrpos($docPath, "/") + 1);
            $files = substr($docPath, strrpos($docPath, "/") + 1);
            $filecheck = $row['old_doc_name'];
            $extension = pathinfo($filecheck, PATHINFO_EXTENSION);

            if ($extension === '') {
                //echo $extension.'5555';

                // The file has a .pdf extension
                // Your code here for handling PDF files
                $file1 = $filecheck . '.' . $row['doc_extn'];
                
                
            } else {
                //ssssecho $extension.'4444';

                // The file does not have a .pdf extension
                // Your code here for handling other file types
                $file1 = $filecheck;
            }



            findparentpath($slid, $sldocid, $db_con, $folder_name);
            $folder_name = implode(',', $folder_name);
            $folder_name = explode(',', $folder_name);
            $folder_name = array_reverse($folder_name);
            $folder_name = implode('/', $folder_name);



            if (FTP_ENABLED) {
                $ftp = new ftp();
                $ftp->conn("$fileserver", "$port", "$ftpUser", "$ftpPwd");

                if ($ftp->get('extract-here/' . $docPath, ROOT_FTP_FOLDER . '/' . $docPath)) {

                    $zip->addEmptyDir("$folder_name");
                    if ($zip->addFile($file_path . $files, "$folder_name/" . $file1)) {


                        //unlink('extract-here/' .$docPath);

                        $zippedFilePath[] = 'extract-here/' . $docPath;
                    }
                } else {
                    $arr = $ftp->getLogData();
                }
            } else {

                $zip->addEmptyDir("$folder_name");
                $zip->addFile($file_path . $files, "$folder_name/" . $file1);
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


    // ankit end 15 may


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

        // $("#bulkdowns").click(function() {
        //     setTimeout(function() {
        //         location.reload();
        //     }, 2000);

        // });

        // $("#bulkdowfol").click(function() {
        //     setTimeout(function() {
        //         location.reload();
        //     }, 2000);

        // });
    </script>
    <!---end add and search metadata-->


</body>

</html>