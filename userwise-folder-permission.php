<!DOCTYPE html>
<html>
    <?php 
    require_once './loginvalidate.php';
    require_once './application/config/database.php';
    require_once './application/pages/head.php';
    
    $sameGroupIDs = array();
    $group = mysqli_query($db_con, "select * from tbl_bridge_grp_to_um where find_in_set('$_SESSION[cdes_user_id]',user_ids)") or die('Error' . mysqli_error($db_con));
    while ($rwGroup = mysqli_fetch_assoc($group)) {
        $sameGroupIDs[] = $rwGroup['user_ids'];
    }
    $sameGroupIDs = implode(',', $sameGroupIDs);
    $sameGroupIDs = explode(',', $sameGroupIDs);
    $sameGroupIDs = array_unique($sameGroupIDs);
    sort($sameGroupIDs);
    $sameGroupIDs = implode(',', $sameGroupIDs);
    // print_r($sameGroupIDs);

    if ($rwgetRole['role_view'] != '1') {
        header('Location: ./index');
    }

    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $slid = urldecode(base64_decode($_GET['id']));
        $parentId = preg_replace('/[^0-9]/', '', $slid);
    }
    ?>
    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
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
                                        <a href="userList"><?php echo $lang['Masters']; ?></a>
                                    </li>
                                    <li class="active">
                                        <?php echo $lang['folder_permission']; ?>
                                    </li>
                                    <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>

                                </ol>
                            </div>
                        </div>
                        <div class="box box-primary">
                            <div class="panel">
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <form method="get">
                                                <div class="form-group col-md-4">

                                                    <select class="select2 select2-multiple" multiple data-placeholder="<?php echo $lang['Select_permission']; ?>" name="perms[]">
                                                        <?php
                                                        $columnName = array();
                                                        $columnlevel = array();
                                                        $role = mysqli_query($db_con, "SELECT `dashboard_mydms`, `storage_auth_plcy`, `bulk_upload`, `create_storage`, `create_child_storage`, `upload_doc_storage`, `modify_storage_level`, `delete_storage_level`, `move_storage_level`, `copy_storage_level`, `view_storage_audit`, `metadata_search`, `metadata_quick_search`, `num_of_folder`, `num_of_file`, `memory_used`, `pdf_file`, `doc_file`, `excel_file`, `image_file`, `audio_file`, `video_file`, `pdf_print`, `pdf_download`,`file_version`, `delete_version`, `tif_file`, `view_recycle_bin`, `restore_file`, `permanent_del`, `shared_file`, `share_with_me`, `export_csv`, `move_file`, `copy_file`, `share_file`, `checkin_checkout`, `bulk_download`, `xls_download`, `xls_print`,`delete_metadata`, `edit_metadata`, `view_psd`, `view_cdr`,`save_query`, `mail_files`, `share_folder`, `shared_folder_with_me`, `upload_logs`, `view_rtf`, `view_odt`, `folder_upload`, `lock_folder`, `lock_file`, `doc_weeding_out`, `doc_share_time`, `doc_expiry_time`,`view_exten`, `add_exten`, `enable_exten`, `delete_exten`,`subscribe_document` FROM `tbl_user_roles`") or die('Error' . mysqli_error($db_con));
                                                        while ($rwrole = mysqli_fetch_field($role)) {
                                                            $columnName[] = $rwrole->name;
                                                            if ($rwrole->name == 'dashboard_mydms') {
                                                                $columnlevel[] = "MY DMS";
                                                            }
                                                            if ($rwrole->name == 'storage_auth_plcy') {
                                                                $columnlevel[] = $lang['Storage_Auth'];
                                                            }
                                                            if ($rwrole->name == 'bulk_upload') {
                                                                $columnlevel[] = $lang['Bulk_Upload'];
                                                            }
                                                            if ($rwrole->name == 'create_storage') {
                                                                $columnlevel[] = $lang['Crt_Strg'];
                                                            }
                                                            if ($rwrole->name == 'create_child_storage') {
                                                                $columnlevel[] = $lang['Add_Nw_Chld'];
                                                            }
                                                            if ($rwrole->name == 'upload_doc_storage') {
                                                                $columnlevel[] = $lang['Upload_Documents'];
                                                            }
                                                            if ($rwrole->name == 'modify_storage_level') {
                                                                $columnlevel[] = $lang['Edit'];
                                                            }
                                                            if ($rwrole->name == 'delete_storage_level') {
                                                                $columnlevel[] = $lang['Delete'];
                                                            }
                                                            if ($rwrole->name == 'move_storage_level') {
                                                                $columnlevel[] = $lang['move'];
                                                            }
                                                            if ($rwrole->name == 'copy_storage_level') {
                                                                $columnlevel[] = $lang['Copy'];
                                                            }
                                                            if ($rwrole->name == 'view_storage_audit') {
                                                                $columnlevel[] = $lang['Storage_Wise'];
                                                            }
                                                            if ($rwrole->name == 'upload_logs') {
                                                                $columnlevel[] = $lang['upload_logs'];
                                                            }
                                                            if ($rwrole->name == 'file_edit') {
                                                                $columnlevel[] = $lang['View_MetaData_file'];
                                                            }
                                                            if ($rwrole->name == 'tif_file') {
                                                                $columnlevel[] = $lang['Tiff_File'];
                                                            }
                                                            if ($rwrole->name == 'pdf_file') {
                                                                $columnlevel[] = $lang['pdf_file'];
                                                            }
                                                            if ($rwrole->name == 'doc_file') {
                                                                $columnlevel[] = $lang['doc_file'];
                                                            }
                                                            if ($rwrole->name == 'excel_file') {
                                                                $columnlevel[] = $lang['excel_file'];
                                                            }
                                                            if ($rwrole->name == 'audio_file') {
                                                                $columnlevel[] = $lang['Audio_file'];
                                                            }
                                                            if ($rwrole->name == 'video_file') {
                                                                $columnlevel[] = $lang['Video_file'];
                                                            }
                                                            if ($rwrole->name == 'image_file') {
                                                                $columnlevel[] = $lang['image_file'];
                                                            }
                                                            if ($rwrole->name == 'bulk_download') {
                                                                $columnlevel[] = $lang['Bulk_Download'];
                                                            }
                                                            if ($rwrole->name == 'xls_download') {
                                                                $columnlevel[] = $lang['Excel_Download'];
                                                            }
                                                            if ($rwrole->name == 'xls_print') {
                                                                $columnlevel[] = $lang['Excel_Print'];
                                                            }
                                                            if ($rwrole->name == 'view_psd') {
                                                                $columnlevel[] = $lang['view_psd'];
                                                            }
                                                            if ($rwrole->name == 'view_cdr') {
                                                                $columnlevel[] = $lang['view_cdr'];
                                                            }
                                                            if ($rwrole->name == 'view_odt') {
                                                                $columnlevel[] = $lang['odt_file'];
                                                            }
                                                            if ($rwrole->name == 'view_rtf') {
                                                                $columnlevel[] = $lang['rtf_file'];
                                                            }
                                                            if ($rwrole->name == 'pdf_print') {
                                                                $columnlevel[] = $lang['Pdf_Print'];
                                                            }
                                                            if ($rwrole->name == 'pdf_download') {
                                                                $columnlevel[] = $lang['Pdf_Download'];
                                                            }
                                                            if ($rwrole->name == 'file_version') {
                                                                $columnlevel[] = $lang['View_File_Version'];
                                                            }
                                                            if ($rwrole->name == 'delete_version') {
                                                                $columnlevel[] = $lang['Del_File_Version'];
                                                            }
                                                            if ($rwrole->name == 'export_csv') {
                                                                $columnlevel[] = $lang['Export_Csv'];
                                                            }
                                                            if ($rwrole->name == 'copy_file') {
                                                                $columnlevel[] = $lang['Copy_Files'];
                                                            }
                                                            if ($rwrole->name == 'share_file') {
                                                                $columnlevel[] = $lang['Shared_Files'];
                                                            }
                                                            if ($rwrole->name == 'checkin_checkout') {
                                                                $columnlevel[] = $lang['Checkin_Checkout'];
                                                            }
                                                            if ($rwrole->name == 'mail_files') {
                                                                $columnlevel[] = $lang['mail_files'];
                                                            }
                                                            if ($rwrole->name == 'lock_folder') {
                                                                $columnlevel[] = $lang['lock_folder'];
                                                            }
                                                            if ($rwrole->name == 'lock_file') {
                                                                $columnlevel[] = $lang['lock_file'];
                                                            }
                                                            if ($rwrole->name == 'doc_weeding_out') {
                                                                $columnlevel[] = $lang['weed_out_time'];
                                                            }
                                                            if ($rwrole->name == 'doc_share_time') {
                                                                $columnlevel[] = $lang['share_document_with_time'];
                                                            }
                                                            if ($rwrole->name == 'doc_expiry_time') {
                                                                $columnlevel[] = $lang['expired_doc_list'];
                                                            }
                                                            if ($rwrole->name == 'view_recycle_bin') {
                                                                $columnlevel[] = $lang['View_Recycle_Bin'];
                                                            }
                                                            if ($rwrole->name == 'restore_file') {
                                                                $columnlevel[] = $lang['Restore_Files'];
                                                            }
                                                            if ($rwrole->name == 'permanent_del') {
                                                                $columnlevel[] = $lang['per_dlt'];
                                                            }
                                                            if ($rwrole->name == 'rename_file') {
                                                                $columnlevel[] = $lang['rename_file'];
                                                            }
                                                            if ($rwrole->name == 'shared_file') {
                                                                $columnlevel[] = $lang['View_shared_Files'];
                                                            }
                                                            if ($rwrole->name == 'view_exten') {
                                                                $columnlevel[] = $lang['view_exten'];
                                                            }
                                                            if ($rwrole->name == 'add_exten') {
                                                                $columnlevel[] = $lang['add_exten'];
                                                            }
                                                            if ($rwrole->name == 'enable_exten') {
                                                                $columnlevel[] = $lang['enable_exten'];
                                                            }
                                                            if ($rwrole->name == 'delete_exten') {
                                                                $columnlevel[] = $lang['delete_exten'];
                                                            }
                                                            if ($rwrole->name == 'share_folder') {
                                                                $columnlevel[] = $lang['share_folder'];
                                                            }
                                                            if ($rwrole->name == 'shared_folder_with_me') {
                                                                $columnlevel[] = $lang['share_folder_with_me'];
                                                            }
                                                            if ($rwrole->name == 'folder_upload') {
                                                                $columnlevel[] = $lang['upload_folder'];
                                                            }
                                                            if ($rwrole->name == 'subscribe_document') {
                                                                $columnlevel[] = $lang['subscribe'];
                                                            }
                                                            if ($rwrole->name == 'metadata_search') {
                                                                $columnlevel[] = $lang['METADATA_SEARCH'];
                                                            }
                                                            if ($rwrole->name == 'metadata_quick_search') {
                                                                $columnlevel[] = $lang['quich_search'];
                                                            }
                                                            if ($rwrole->name == 'num_of_folder') {
                                                                $columnlevel[] = $lang['NO_OF_FOLDER'];
                                                            }
                                                            if ($rwrole->name == 'num_of_file') {
                                                                $columnlevel[] = $lang['NO_OF_FILE'];
                                                            }
                                                            if ($rwrole->name == 'memory_used') {
                                                                $columnlevel[] = $lang['MEMORY_USED'];
                                                            }
                                                            if ($rwrole->name == 'share_with_me') {
                                                                $columnlevel[] = $lang['View_Share_With_Me'];
                                                            }
                                                            if ($rwrole->name == 'move_file') {
                                                                $columnlevel[] = $lang['Move_Files'];
                                                            }
                                                            if ($rwrole->name == 'delete_metadata') {
                                                                $columnlevel[] = $lang['delete_metadata'];
                                                            }
                                                            if ($rwrole->name == 'edit_metadata') {
                                                                $columnlevel[] = $lang['Edit_Metadata'];
                                                            }
                                                            if ($rwrole->name == 'save_query') {
                                                                $columnlevel[] = $lang['Sve_Qry'];
                                                            }
                                                        }
                                                        //sort($columnlevel);

                                                        foreach ($columnName as $key => $column) {
                                                            if (in_array($columnName[$key], $_GET['perms'])) {
                                                                echo "<option value='$columnName[$key]' selected>" . ucfirst(strtolower($columnlevel[$key])) . "</option>";
                                                            } else {
                                                                echo "<option value='$columnName[$key]'>" . ucfirst(strtolower($columnlevel[$key])) . "</option>";
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>

                                                <div class="col-md-4">
                                                    <select class="form-control select2 parent" id="parent" name="id">
                                                        <option value="" selected><?php echo $lang['Select_Storage']; ?></option>
                                                        <?php
                                                        if (!empty($slpermIdes)) {
                                                            mysqli_set_charset($db_con, "utf8");
                                                            $sllevel = mysqli_query($db_con, "select * from tbl_storage_level where sl_id in($slpermIdes) order by sl_name asc");
                                                            while ($rwSllevel = mysqli_fetch_assoc($sllevel)) {
                                                                $level = $rwSllevel['sl_depth_level'];
                                                                $slId = $rwSllevel['sl_id'];
                                                                $slperm = $rwSllevel['sl_id'];
                                                                findChild($slId, $level, $slperm, $parentId);
                                                            }
                                                        }
                                                        ?>
                                                    </select> 
                                                    <?php

                                                    function findChild($sl_id, $level, $slperm, $parentId) {

                                                        global $db_con;

                                                        if ($sl_id == $parentId) {
                                                            echo '<option value="' . base64_encode(urlencode($sl_id)) . '"  selected>';
                                                            parentLevel($sl_id, $db_con, $slperm, $level, '');
                                                            echo '</option>';
                                                        } else {
                                                            echo '<option value="' . base64_encode(urlencode($sl_id)) . '" >';
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

                                                <div class="form-group col-md-1">
                                                    <input type="submit" name="search" class="btn btn-primary" value="<?php echo $lang['Apply']; ?>" title="<?php echo $lang['Search']; ?>" >
                                                </div>
                                            </form>
                                            <div class="form-group col-md-1">
                                                <a href="userwise-folder-permission" class="btn btn-warning" title="<?php echo $lang['Reset']; ?>" ><?php echo $lang['Reset']; ?></a>
                                            </div>
                                            <?php if ($rwgetRole['export_user_perm'] == '1') { ?>
                                                <div class="form-group col-md-2">
                                                    <button class="btn btn-primary" id="export4"  data-toggle="modal"  data-target="#multi-csv-export-model"> <i class="ti-import"></i> <?php echo $lang['Export_Users_perm']; ?></button>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="">
                                        <?php
                                        $where = " ";
                                        if (isset($_GET['perms']) && !empty($_GET['perms'])) {
                                            $permscolumn = $_GET['perms'];
                                            $sql_search_fields = Array();
                                            for ($i = 0; $i < count($permscolumn); $i++) {
                                                $sql_search_fields[] = "`" . xss_clean(trim($permscolumn[$i])) . "` ='1'";
                                            }
                                            $searchText = implode(' and ', $sql_search_fields);
                                            $where .= " where $searchText";
                                        }
                                        if (isset($_GET['id']) && !empty($_GET['id'])) {
                                            $slId = base64_decode(urldecode($_GET['id']));
                                            $where .= " and sl_id='$slId'";
                                        }

                                        mysqli_set_charset($db_con, "utf8");
                                        $sql = "SELECT tur.*, tbum.first_name,tbum.last_name, tbum.user_id,tbrum.user_ids,tspl.sl_id FROM tbl_bridge_role_to_um as tbrum INNER JOIN tbl_user_roles as tur ON tur.role_id = tbrum.role_id INNER JOIN tbl_user_master as tbum ON FIND_IN_SET(tbum.user_id, tbrum.user_ids) INNER JOIN tbl_storagelevel_to_permission as tspl ON tbum.user_id=tspl.user_id and tbum.user_id!='1' and tbum.user_id in($sameGroupIDs) $where group by tbum.user_id";
                                        $retval = mysqli_query($db_con, $sql) or die('Could not get data: ' . mysqli_error($db_con));
                                        $foundnum = mysqli_num_rows($retval);
                                        if ($foundnum > 0) {
                                            $StartPoint = preg_replace("/[^0-9]/", "", $_GET['limit']); //filter limit from all special chars
                                            if (is_numeric($StartPoint)) {
                                                $per_page = $StartPoint;
                                            } else {
                                                $per_page = 10;
                                            }
                                            //$start = preg_replace("/[^0-9]/", "", $_GET['start']); //filter start variable
                                            $start = isset($_GET['start']) ? ($_GET['start'] > 0) ? $_GET['start'] : 0 : 0;
                                            $max_pages = ceil($foundnum / $per_page);
                                            if (!$start) {
                                                $start = 0;
                                            }
                                            $limit = $_GET['limit'];
                                            ?>
                                            <div class="container">

                                                <label><?php echo $lang['show_lst']; ?> </label>
                                                <select id="limit" class="input-sm">
                                                    <option value="10" <?php
                                                    if ($limit == 10) {
                                                        echo 'selected';
                                                    }
                                                    ?>>10</option>
                                                    <option value="25" <?php
                                                    if ($limit == 25) {
                                                        echo 'selected';
                                                    }
                                                    ?>>25</option>
                                                    <option value="50" <?php
                                                    if ($limit == 50) {
                                                        echo 'selected';
                                                    }
                                                    ?>>50</option>
                                                    <option value="250" <?php
                                                    if ($limit == 250) {
                                                        echo 'selected';
                                                    }
                                                    ?>>250</option>
                                                    <option value="500" <?php
                                                    if ($limit == 500) {
                                                        echo 'selected';
                                                    }
                                                    ?>>500</option>
                                                </select> 
                                                <label><?php echo $lang['User_List']; ?></label>

                                                <div class="record pull-right">
                                                    <?php echo $start + 1 ?> <?php echo $lang['To']; ?> <?php
                                                    if ($start + $per_page > $foundnum) {
                                                        echo $foundnum;
                                                    } else {
                                                        echo ($start + $per_page);
                                                    }
                                                    ?> <span><?php echo $lang['ttl_recrds']; ?>: <?php echo $foundnum; ?></span>
                                                </div>
                                            </div>
                                            <div class="box-body">
                                                <?php

                                                function showData($user, $rwgetRole, $db_con, $start, $lang) {
                                                    ?>
                                                    <table class="table table-striped table-bordered" id="table_demo_icons">
                                                        <thead>
                                                            <tr>
                                                                <th><?php echo $lang['Sr_No']; ?></th>
                                                                <th><?php echo $lang['root_storage']; ?></th>
                                                                <th><?php echo $lang['User_Name']; ?></th>
                                                                <th><?php echo $lang['User_Role']; ?></th>
                                                                <th><?php echo $lang['folder_permission']; ?></th>

                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $j = 1;
                                                            $j += $start;
                                                            while ($rwUser = mysqli_fetch_assoc($user)) {
                                                              
                                                                ?>
                                                                <?php
                                                                if ($_SESSION['cdes_user_id'] == $rwUser['user_id']) {

                                                                    continue;
                                                                } else {
                                                                    ?>
                                                                    <?php if ($rwUser['user_id'] != 1) { ?>
                                                                        <tr class="gradeX"> 
                                                                            <td><?php echo $j . '.'; ?></td>
                                                                            <td><?php
                                                                                //storage name
                                                                                $storageNames = array();
                                                                                $userstorage = mysqli_query($db_con, "SELECT * FROM `tbl_storagelevel_to_permission` where user_id='" . $rwUser['user_id'] . "'") or die("Error: test" . mysqli_error($db_con));
                                                                                while ($storageperms = mysqli_fetch_assoc($userstorage)) {
                                                                                    $storagepermission = $storageperms['sl_id'];
                                                                                    $storageName = mysqli_query($db_con, "SELECT sl_name FROM `tbl_storage_level` where sl_id='" . $storagepermission . "'") or die("Error: test" . mysqli_error($db_con));
                                                                                    $rwstorageName = mysqli_fetch_assoc($storageName);
                                                                                    $storageNames[] = $rwstorageName['sl_name'];
                                                                                }
                                                                                echo implode(', ', $storageNames);
                                                                                ?></td>
                                                                            <td>
                                                                                <label><?php echo $rwUser['first_name'] . ' '; ?> <?php echo $rwUser['last_name']; ?></label>
                                                                            </td>
                                                                            <td>
                                                                                <?php
                                                                                echo $rwUser['user_role'];
                                                                                ?>
                                                                            </td>

                                                                            <td>
                                                                                <?php
                                                                                $permission = '';

                                                                                $permission .= (($rwUser['dashboard_mydms'] == '1') ? $lang['MY_DMS'] . ", " : "");
                                                                                $permission .= (($rwUser['num_of_folder'] == '1') ? $lang['NO_OF_FOLDER'] . ", " : "");
                                                                                $permission .= (($rwUser['num_of_file'] == '1') ? $lang['NO_OF_FILE'] . ", " : "");
                                                                                $permission .= (($rwUser['memory_used'] == '1') ? $lang['MEMORY_USED'] . ", " : "");
                                                                                $permission .= (($rwUser['storage_auth_plcy'] == '1') ? $lang['Storage_Auth'] . ", " : "");
                                                                                $permission .= (($rwUser['bulk_upload'] == '1') ? $lang['Bulk_Upload'] . ", " : "");
                                                                                $permission .= (($rwUser['folder_upload'] == '1') ? $lang['Upload_multi_folder'] . ", " : "");
                                                                                $permission .= (($rwUser['save_query'] == '1') ? $lang['Sve_Qry'] . ", " : "");
                                                                                $permission .= (($rwUser['subscribe_document'] == '1') ? $lang['subscribe'] . ", " : "");
                                                                                $permission .= (($rwUser['create_storage'] == '1') ? $lang['Crt_Strg'] . ", " : "");
                                                                                $permission .= (($rwUser['create_child_storage'] == '1') ? $lang['Add_Nw_Chld'] . ", " : "");
                                                                                $permission .= (($rwUser['upload_doc_storage'] == '1') ? $lang['Upload_Documents'] . ", " : "");
                                                                                $permission .= (($rwUser['modify_storage_level'] == '1') ? $lang['Edit'] . ", " : "");
                                                                                $permission .= (($rwUser['delete_storage_level'] == '1') ? $lang['Delete'] . ", " : "");
                                                                                $permission .= (($rwUser['move_storage_level'] == '1') ? $lang['move'] . ", " : "");
                                                                                $permission .= (($rwUser['copy_storage_level'] == '1') ? $lang['Copy'] . ", " : "");
                                                                                $permission .= (($rwUser['view_storage_audit'] == '1') ? $lang['Storage_Wise'] . ", " : "");
                                                                                $permission .= (($rwUser['upload_logs'] == '1') ? $lang['upload_logs'] . ", " : "");
                                                                                $permission .= (($rwUser['file_edit'] == '1') ? $lang['View_MetaData_file'] . ", " : "");
                                                                                $permission .= (($rwUser['tif_file'] == '1') ? $lang['Tiff_File'] . ", " : "");
                                                                                $permission .= (($rwUser['pdf_file'] == '1') ? $lang['pdf_file'] . ", " : "");
                                                                                $permission .= (($rwUser['doc_file'] == '1') ? $lang['doc_file'] . ", " : "");
                                                                                $permission .= (($rwUser['excel_file'] == '1') ? $lang['excel_file'] . ", " : "");
                                                                                $permission .= (($rwUser['audio_file'] == '1') ? $lang['Audio_file'] . ", " : "");
                                                                                $permission .= (($rwUser['video_file'] == '1') ? $lang['Video_file'] . ", " : "");
                                                                                $permission .= (($rwUser['image_file'] == '1') ? $lang['image_file'] . ", " : "");
                                                                                $permission .= (($rwUser['bulk_download'] == '1') ? $lang['Bulk_Download'] . ", " : "");
                                                                                $permission .= (($rwUser['xls_download'] == '1') ? $lang['Excel_Download'] . ", " : "");
                                                                                $permission .= (($rwUser['xls_print'] == '1') ? $lang['Excel_Print'] . ", " : "");
                                                                                $permission .= (($rwUser['view_psd'] == '1') ? $lang['view_psd'] . ", " : "");
                                                                                $permission .= (($rwUser['view_cdr'] == '1') ? $lang['view_cdr'] . ", " : "");
                                                                                $permission .= (($rwUser['view_odt'] == '1') ? $lang['odt_file'] . ", " : "");
                                                                                $permission .= (($rwUser['view_rtf'] == '1') ? $lang['rtf_file'] . ", " : "");
                                                                                $permission .= (($rwUser['pdf_print'] == '1') ? $lang['Pdf_Print'] . ", " : "");
                                                                                $permission .= (($rwUser['pdf_download'] == '1') ? $lang['Pdf_Download'] . ", " : "");
                                                                                $permission .= (($rwUser['file_version'] == '1') ? $lang['View_File_Version'] . ", " : "");
                                                                                $permission .= (($rwUser['delete_version'] == '1') ? $lang['Del_File_Version'] . ", " : "");
                                                                                $permission .= (($rwUser['update_file'] == '1') ? $lang['Update_File'] . ", " : "");
                                                                                $permission .= (($rwUser['export_csv'] == '1') ? $lang['Export_Csv'] . ", " : "");
                                                                                $permission .= (($rwUser['move_file'] == '1') ? $lang['Move_Files'] . ", " : "");
                                                                                $permission .= (($rwUser['copy_file'] == '1') ? $lang['Copy_Files'] . ", " : "");
                                                                                $permission .= (($rwUser['share_file'] == '1') ? $lang['Shared_Files'] . ", " : "");
                                                                                $permission .= (($rwUser['checkin_checkout'] == '1') ? $lang['Checkin_Checkout'] . ", " : "");
                                                                                $permission .= (($rwUser['mail_files'] == '1') ? $lang['mail_files'] . ", " : "");
                                                                                $permission .= (($rwUser['lock_folder'] == '1') ? $lang['lock_folder'] . ", " : "");
                                                                                $permission .= (($rwUser['lock_file'] == '1') ? $lang['lock_file'] . ", " : "");
                                                                                $permission .= (($rwUser['doc_weeding_out'] == '1') ? $lang['weed_out_time'] . ", " : "");
                                                                                $permission .= (($rwUser['doc_share_time'] == '1') ? $lang['share_document_with_time'] . ", " : "");
                                                                                $permission .= (($rwUser['doc_expiry_time'] == '1') ? $lang['expired_doc_list'] . ", " : "");
                                                                                $permission .= (($rwUser['view_recycle_bin'] == '1') ? $lang['View_Recycle_Bin'] . ", " : "");
                                                                                $permission .= (($rwUser['restore_file'] == '1') ? $lang['Restore_Files'] . ", " : "");
                                                                                $permission .= (($rwUser['permanent_del'] == '1') ? $lang['per_dlt'] . ", " : "");
                                                                                $permission .= (($rwUser['rename_file'] == '1') ? $lang['rename_file'] . ", " : "");
                                                                                $permission .= (($rwUser['shared_file'] == '1') ? $lang['View_shared_Files'] . ", " : "");
                                                                                $permission .= (($rwUser['share_with_me'] == '1') ? $lang['View_Share_With_Me'] . ", " : "");
                                                                                $permission .= (($rwUser['doc_exp_setting'] == '1') ? $lang['expiry_document'] . ", " : "");
                                                                                $permission .= (($rwUser['doc_retention_setting'] == '1') ? $lang['Retention_document'] . ", " : "");
                                                                                $permission .= (($rwUser['doc_share_setting'] == '1') ? $lang['Share_docs_with_time'] . ", " : "");
                                                                                $permission .= (($rwUser['view_exten'] == '1') ? $lang['view_exten'] . ", " : "");
                                                                                $permission .= (($rwUser['add_exten'] == '1') ? $lang['add_exten'] . ", " : "");
                                                                                $permission .= (($rwUser['enable_exten'] == '1') ? $lang['enable_exten'] . ", " : "");
                                                                                $permission .= (($rwUser['delete_exten'] == '1') ? $lang['delete_exten'] . ", " : "");
                                                                                $permission .= (($rwUser['shared_file'] == '1') ? $lang['View_shared_Files'] : "");
                                                                                // echo $permission;
                                                                                //$columnmatch[] = $permission;

                                                                               if (isset($_GET['perms']) && !empty($_GET['perms'])) {
                                                                                    $permscolumn = $_GET['perms'];
                                                                                    $columnlevel2 = Array();
                                                                                    for ($i = 0; $i < count($permscolumn); $i++) {
                                                                                        if ($permscolumn[$i] == 'dashboard_mydms') {
                                                                                            $columnlevel2[] = "MY DMS";
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'storage_auth_plcy') {
                                                                                            $columnlevel2[] = $lang['Storage_Auth'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'bulk_upload') {
                                                                                            $columnlevel2[] = $lang['Bulk_Upload'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'create_storage') {
                                                                                            $columnlevel2[] = $lang['Crt_Strg'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'create_child_storage') {
                                                                                            $columnlevel2[] = $lang['Add_Nw_Chld'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'upload_doc_storage') {
                                                                                            $columnlevel2[] = $lang['Upload_Documents'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'modify_storage_level') {
                                                                                            $columnlevel2[] = $lang['Edit'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'delete_storage_level') {
                                                                                            $columnlevel2[] = $lang['Delete'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'move_storage_level') {
                                                                                            $columnlevel2[] = $lang['move'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'copy_storage_level') {
                                                                                            $columnlevel2[] = $lang['Copy'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'view_storage_audit') {
                                                                                            $columnlevel2[] = $lang['Storage_Wise'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'upload_logs') {
                                                                                            $columnlevel2[] = $lang['upload_logs'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'file_edit') {
                                                                                            $columnlevel2[] = $lang['View_MetaData_file'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'tif_file') {
                                                                                            $columnlevel2[] = $lang['Tiff_File'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'pdf_file') {
                                                                                            $columnlevel2[] = $lang['pdf_file'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'doc_file') {
                                                                                            $columnlevel2[] = $lang['doc_file'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'excel_file') {
                                                                                            $columnlevel2[] = $lang['excel_file'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'audio_file') {
                                                                                            $columnlevel2[] = $lang['Audio_file'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'video_file') {
                                                                                            $columnlevel2[] = $lang['Video_file'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'image_file') {
                                                                                            $columnlevel2[] = $lang['image_file'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'bulk_download') {
                                                                                            $columnlevel2[] = $lang['Bulk_Download'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'xls_download') {
                                                                                            $columnlevel2[] = $lang['Excel_Download'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'xls_print') {
                                                                                            $columnlevel2[] = $lang['Excel_Print'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'view_psd') {
                                                                                            $columnlevel2[] = $lang['view_psd'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'view_cdr') {
                                                                                            $columnlevel2[] = $lang['view_cdr'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'view_odt') {
                                                                                            $columnlevel2[] = $lang['odt_file'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'view_rtf') {
                                                                                            $columnlevel2[] = $lang['rtf_file'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'pdf_print') {
                                                                                            $columnlevel2[] = $lang['Pdf_Print'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'pdf_download') {
                                                                                            $columnlevel2[] = $lang['Pdf_Download'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'file_version') {
                                                                                            $columnlevel2[] = $lang['View_File_Version'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'delete_version') {
                                                                                            $columnlevel2[] = $lang['Del_File_Version'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'export_csv') {
                                                                                            $columnlevel2[] = $lang['Export_Csv'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'copy_file') {
                                                                                            $columnlevel2[] = $lang['Copy_Files'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'share_file') {
                                                                                            $columnlevel2[] = $lang['Shared_Files'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'checkin_checkout') {
                                                                                            $columnlevel2[] = $lang['Checkin_Checkout'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'mail_files') {
                                                                                            $columnlevel2[] = $lang['mail_files'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'lock_folder') {
                                                                                            $columnlevel2[] = $lang['lock_folder'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'lock_file') {
                                                                                            $columnlevel2[] = $lang['lock_file'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'doc_weeding_out') {
                                                                                            $columnlevel2[] = $lang['weed_out_time'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'doc_share_time') {
                                                                                            $columnlevel2[] = $lang['share_document_with_time'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'doc_expiry_time') {
                                                                                            $columnlevel2[] = $lang['expired_doc_list'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'view_recycle_bin') {
                                                                                            $columnlevel2[] = $lang['View_Recycle_Bin'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'restore_file') {
                                                                                            $columnlevel2[] = $lang['Restore_Files'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'permanent_del') {
                                                                                            $columnlevel2[] = $lang['per_dlt'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'rename_file') {
                                                                                            $columnlevel2[] = $lang['rename_file'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'shared_file') {
                                                                                            $columnlevel2[] = $lang['View_shared_Files'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'view_exten') {
                                                                                            $columnlevel2[] = $lang['view_exten'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'add_exten') {
                                                                                            $columnlevel2[] = $lang['add_exten'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'enable_exten') {
                                                                                            $columnlevel2[] = $lang['enable_exten'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'delete_exten') {
                                                                                            $columnlevel2[] = $lang['delete_exten'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'share_folder') {
                                                                                            $columnlevel2[] = $lang['share_folder'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'shared_folder_with_me') {
                                                                                            $columnlevel2[] = $lang['share_folder_with_me'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'folder_upload') {
                                                                                            $columnlevel2[] = $lang['upload_folder'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'subscribe_document') {
                                                                                            $columnlevel2[] = $lang['subscribe'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'metadata_search') {
                                                                                            $columnlevel2[] = $lang['METADATA_SEARCH'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'metadata_quick_search') {
                                                                                            $columnlevel2[] = $lang['quich_search'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'num_of_folder') {
                                                                                            $columnlevel2[] = $lang['NO_OF_FOLDER'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'num_of_file') {
                                                                                            $columnlevel2[] = $lang['NO_OF_FILE'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'memory_used') {
                                                                                            $columnlevel2[] = $lang['MEMORY_USED'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'share_with_me') {
                                                                                            $columnlevel2[] = $lang['View_Share_With_Me'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'move_file') {
                                                                                            $columnlevel2[] = $lang['Move_Files'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'delete_metadata') {
                                                                                            $columnlevel2[] = $lang['delete_metadata'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'edit_metadata') {
                                                                                            $columnlevel2[] = $lang['Edit_Metadata'];
                                                                                        }
                                                                                        if ($permscolumn[$i] == 'save_query') {
                                                                                            $columnlevel2[] = $lang['Sve_Qry'];
                                                                                        }
                                                                                    }
                                                                                    sort($columnlevel2);
                                                                                    echo $searchText = implode(', ', $columnlevel2);
                                                                                } else {
                                                                                    echo $permission;
                                                                                }
                                                                                ?>
                                                                            </td>

                                                                        </tr>
                                                                        <?php
                                                                        $j++;
                                                                    }
                                                                }
                                                            }
                                                            ?>
                                                        </tbody>
                                                    </table>

                                                    <?php
                                                }
                                                ?>
                                                <?php
                                                $users = mysqli_query($db_con, "SELECT tur.*, tbum.first_name,tbum.last_name,tbum.user_id,tbrum.user_ids,tspl.sl_id FROM tbl_bridge_role_to_um as tbrum INNER JOIN tbl_user_roles as tur ON tur.role_id = tbrum.role_id INNER JOIN tbl_user_master as tbum ON FIND_IN_SET(tbum.user_id, tbrum.user_ids) INNER JOIN tbl_storagelevel_to_permission as tspl ON tbum.user_id=tspl.user_id and tbum.user_id in($sameGroupIDs) $where group by tbum.user_id ORDER BY tbum.first_name,tbum.last_name LIMIT $start, $per_page");
                                                showData($users, $rwgetRole, $db_con, $start, $lang);
                                                ?>
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
                                                            echo " <li><a href='?start=$prev&limit=$per_page&GrpId=" . $_GET['GrpId'] . "&username=" . $_GET['username'] . "'>$lang[Prev]</a> </li>";
                                                        else
                                                            echo " <li class='disabled'><a href='javascript:void(0)'>$lang[Prev]</a> </li>";
                                                        //pages 
                                                        if ($max_pages < 7 + ($adjacents * 2)) {   //not enough pages to bother breaking it up
                                                            $i = 0;
                                                            for ($counter = 1; $counter <= $max_pages; $counter++) {
                                                                if ($i == $start) {
                                                                    echo "<li class='active'><a href='?start=$i&limit=$per_page&GrpId=" . $_GET['GrpId'] . "&username=" . $_GET['username'] . "'><b>$counter</b></a> </li>";
                                                                } else {
                                                                    echo "<li><a href='?start=$i&limit=$per_page&GrpId=" . $_GET['GrpId'] . "&username=" . $_GET['username'] . "'>$counter</a></li> ";
                                                                }
                                                                $i = $i + $per_page;
                                                            }
                                                        } elseif ($max_pages > 5 + ($adjacents * 2)) {    //enough pages to hide some
                                                            //close to beginning; only hide later pages
                                                            if (($start / $per_page) < 1 + ($adjacents * 2)) {
                                                                $i = 0;
                                                                for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                                                                    if ($i == $start) {
                                                                        echo " <li class='active'><a href='?start=$i&limit=$per_page&GrpId=" . $_GET['GrpId'] . "&username=" . $_GET['username'] . "'><b>$counter</b></a></li> ";
                                                                    } else {
                                                                        echo "<li> <a href='?start=$i&limit=$per_page&GrpId=" . $_GET['GrpId'] . "&username=" . $_GET['username'] . "'>$counter</a> </li>";
                                                                    }
                                                                    $i = $i + $per_page;
                                                                }
                                                            }
                                                            //in middle; hide some front and some back
                                                            elseif ($max_pages - ($adjacents * 2) > ($start / $per_page) && ($start / $per_page) > ($adjacents * 2)) {
                                                                echo " <li><a href='?start=0'>1</a></li> ";
                                                                echo "<li><a href='?start=$per_page'>2</a></li>";
                                                                echo "<li><a href='javascript:void(0)'>...</a></li>";

                                                                $i = $start;
                                                                for ($counter = ($start / $per_page) + 1; $counter < ($start / $per_page) + $adjacents + 2; $counter++) {
                                                                    if ($i == $start) {
                                                                        echo " <li class='active'><a href='?start=$i&limit=$per_page&GrpId=" . $_GET['GrpId'] . "&username=" . $_GET['username'] . "'><b>$counter</b></a></li> ";
                                                                    } else {
                                                                        echo " <li><a href='?start=$i&limit=$per_page&GrpId=" . $_GET['GrpId'] . "&username=" . $_GET['username'] . "'>$counter</a> </li>";
                                                                    }
                                                                    $i = $i + $per_page;
                                                                }
                                                            }
                                                            //close to end; only hide early pages
                                                            else {
                                                                echo "<li> <a href='?start=0'>1</a> </li>";
                                                                echo "<li><a href='?start=$per_page&GrpId=" . $_GET['GrpId'] . "&username=" . $_GET['username'] . "'>2</a></li>";
                                                                echo "<li><a href='javascript:void(0)'>...</a></li>";

                                                                $i = $start;
                                                                for ($counter = ($start / $per_page) + 1; $counter <= $max_pages; $counter++) {
                                                                    if ($i == $start) {
                                                                        echo " <li class='active'><a href='?start=$i&limit=$per_page&GrpId=" . $_GET['GrpId'] . "&username=" . $_GET['username'] . "'><b>$counter</b></a></li> ";
                                                                    } else {
                                                                        echo "<li> <a href='?start=$i&limit=$per_page&GrpId=" . $_GET['GrpId'] . "&username=" . $_GET['username'] . "'>$counter</a></li> ";
                                                                    }
                                                                    $i = $i + $per_page;
                                                                }
                                                            }
                                                        }
                                                        //next button
                                                        if (!($start >= $foundnum - $per_page))
                                                            echo "<li><a href='?start=$next&limit=$per_page&GrpId=" . $_GET['GrpId'] . "&username=" . $_GET['username'] . "''>$lang[Next]</a></li>";
                                                        else
                                                            echo "<li class='disabled'><a href='javascript:void(0)'>$lang[Next]</a></li>";
                                                        ?>
                                                    </ul>
                                                    <?php
                                                }
                                                echo "</center>";
                                            } else {
                                                ?>
                                                <table class="table table-striped table-bordered" id="table_demo_icons">
                                                    <thead>
                                                        <tr>
                                                            <th><?php echo $lang['Sr_No']; ?></th>
                                                            <th><?php echo $lang['root_storage']; ?></th>
                                                            <th><?php echo $lang['User_Name']; ?></th>
                                                            <th><?php echo $lang['User_Role']; ?></th>
                                                            <th><?php echo $lang['folder_permission']; ?></th>

                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td colspan="5" class="text-center">
                                                                <label><strong class="text-danger text-center"><i class="ti-face-sad text-pink"></i> <?php echo $lang['Who0ps!_No_Records_Found']; ?></strong></label>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            <?php }
                                            ?>	
                                        </div>                                        	
                                    </div>
                                </div>
                                <!-- end: page -->

                            </div> <!-- end Panel -->
                        </div>

                        <!-- end Modal -->
                        <div id="multi-csv-export-model" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog"> 
                                <div class="panel panel-color panel-primary"> 
                                    <div class="panel-heading"> 
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                                        <label><h2 class="panel-title"><?php echo $lang['Export_Users_perm']; ?></h2></label> 
                                    </div> 
                                    <form action="export-user-permission"  method="post">
                                        <div class="panel-body">
                                            <div class="col-md-5  m-t-10">
                                                <strong class="text-primary"><?php echo $lang['Export_CSV']; ?> : </strong>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="hidden" value="csvexport" name="select_Fm" />
                                                <select class="select2 input-sm" id="my_multi_select1">
                                                    <option value=""><?php echo $lang['Csv']; ?></option>
                                                </select>
                                            </div>

                                        </div>
                                        <?php
                                        $permscolumn = implode(',', $_GET['perms']);
                                        ?>
                                        <div class="modal-footer">
                                            <input type="hidden" value="<?php echo $parentId; ?>" name="slid">
                                            <input type="hidden" value="<?php echo $permscolumn; ?>" name="searchperms">
                                            <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                                            <button class="btn btn-primary waves-effect waves-light" type="submit" name="exportUser"><i class="ti-import"></i> <?php echo $lang['Export_CSV']; ?></button>
                                        </div>
                                    </form>

                                </div> 
                            </div>
                        </div>
                        <div id="con-close-modal" class="modal fade"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog modal-lg"> 
                                <div class="modal-content"> 
                                    <form method="post" enctype="multipart/form-data">
                                        <div class="modal-header"> 
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                                            <h4 class="modal-title"><?php echo $lang['View_permission']; ?></h4> 
                                        </div>
                                        <div class="modal-body" id="modalModify">
                                            <img src="assets/images/load.gif" alt="load" class="img-responsive center-block" />
                                        </div> 
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                                        </div>
                                    </form>

                                </div> 
                            </div>
                        </div><!-- /.modal -->

                    </div> <!-- container -->

                </div> <!-- content -->

                <?php require_once './application/pages/footer.php'; ?>
            </div>          
            <!-- END wrapper -->
            <?php require_once './application/pages/footerForjs.php'; ?>
        </div>
        <script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>

        <script type="text/javascript">

                                        $(document).ready(function () {
                                            $('form').parsley();
                                        });
                                        $(".select2").select2();

        </script>
        <script>
            //limit filter
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

            $("a#editRow").click(function () {
                var $id = $(this).attr('data');
                $.post("application/ajax/viewFolderPermssion.php", {ID: $id}, function (result, status) {
                    if (status == 'success') {
                        $("#modalModify").html(result);
                        getToken();

                    }
                });

            });
        </script>
        <script type="text/javascript">
$('#my_multi_select1').prop('disabled',true);
//select option asc desc
            var options = $('select.column option');
            var arr = options.map(function (_, o) {
                return {
                    t: $(o).text(),
                    v: o.value
                };
            }).get();
            arr.sort(function (o1, o2) {
                return o1.t > o2.t ? 1 : o1.t < o2.t ? -1 : 0;
            });
            options.each(function (i, o) {
                console.log(i);
                o.value = arr[i].v;
                $(o).text(arr[i].t);
            });
//            //TableManageButtons.init();
//            function searchUser() {
//                var group_id = $("#usergroup").val();
//                var searchtext = $("#searchtext").val();
//                //alert(group_id);
//                window.location.href = "?GrpId=" + btoa(encodeURI(group_id)) + "&searchtxt=" + btoa(encodeURI(searchtext));
//
//            }
        </script>

    </body>
</html>
