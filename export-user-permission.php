<?php
 require_once './loginvalidate.php';
//require_once './application/config/database.php';
$sameGroupIDs = array();
mysqli_set_charset($db_con, "utf8");
$group = mysqli_query($db_con, "select * from tbl_bridge_grp_to_um where find_in_set('$_SESSION[cdes_user_id]',user_ids)") or die('Error' . mysqli_error($db_con));
while ($rwGroup = mysqli_fetch_assoc($group)) {
    $sameGroupIDs[] = $rwGroup['user_ids'];
}
$sameGroupIDs = implode(',', $sameGroupIDs);
$sameGroupIDs = explode(',', $sameGroupIDs);
$sameGroupIDs = array_unique($sameGroupIDs);
sort($sameGroupIDs);
$sameGroupIDs = implode(',', $sameGroupIDs);

if (isset($_POST['exportUser'], $_POST['token'])) {
	
    $selectFormat = trim($_POST['select_Fm']);
    if ($selectFormat == "csvexport"){

        $where = " ";
        if (isset($_POST['searchperms']) && !empty($_POST['searchperms'])) {
            $permscolumnstring = $_POST['searchperms'];
            $permscolumn = explode(',', $permscolumnstring);
            $sql_search_fields = Array();
            for ($i = 0; $i < count($permscolumn); $i++) {
                $sql_search_fields[] = "`" . xss_clean(trim($permscolumn[$i])) . "` ='1'";
            }
            $searchText = implode(' and ', $sql_search_fields);
            $where .= " where $searchText";
        }
        if (isset($_POST['slid']) && !empty($_POST['slid'])) {
            $slId = $_POST['slid'];
            $where .= " and sl_id='$slId'";
        }
        if (!empty($searchText) || !empty($slId)) {
            //echo "SELECT tur.*, tbum.first_name,tbum.last_name,tbum.user_id,tbrum.user_ids,tspl.sl_id FROM tbl_bridge_role_to_um as tbrum INNER JOIN tbl_user_roles as tur ON tur.role_id = tbrum.role_id INNER JOIN tbl_user_master as tbum ON tbum.user_id = tbrum.user_ids INNER JOIN tbl_storagelevel_to_permission as tspl ON tbum.user_id=tspl.user_id and tbum.user_id!='1' and tbum.user_id in($sameGroupIDs) $where";
            $exptUsr = mysqli_query($db_con, "SELECT tur.*, tbum.first_name,tbum.last_name,tbum.user_id,tbrum.user_ids,tspl.sl_id FROM tbl_bridge_role_to_um as tbrum INNER JOIN tbl_user_roles as tur ON tur.role_id = tbrum.role_id INNER JOIN tbl_user_master as tbum ON FIND_IN_SET(tbum.user_id, tbrum.user_ids) INNER JOIN tbl_storagelevel_to_permission as tspl ON tbum.user_id=tspl.user_id and tbum.user_id!='1' and tbum.user_id in($sameGroupIDs) $where GROUP BY tbum.user_id ORDER BY tbum.first_name,tbum.last_name");
        } else {
            $exptUsr = mysqli_query($db_con, "SELECT tur.*, tbum.first_name,tbum.last_name,tbum.user_id,tbrum.user_ids,tspl.sl_id FROM tbl_bridge_role_to_um as tbrum INNER JOIN tbl_user_roles as tur ON tur.role_id = tbrum.role_id INNER JOIN tbl_user_master as tbum ON FIND_IN_SET(tbum.user_id, tbrum.user_ids) INNER JOIN tbl_storagelevel_to_permission as tspl ON tbum.user_id=tspl.user_id and tbum.user_id!='1' and tbum.user_id in($sameGroupIDs) GROUP BY tbum.user_id ORDER BY tbum.first_name,tbum.last_name");
        }

        while ($users = mysqli_fetch_field($exptUsr)) {

            //if ($users->name != 'user_id')
            //$header1 .= $users->name . ",";
        }

        $header1 .= 'Sr No.' . ",";
        $header1 .= 'Root Storage' . ",";
        $header1 .= 'User Name' . ",";
        $header1 .= 'User Role' . ",";
        $header1 .= 'Folder & Files Permission List';
        $k = 1;
        while ($row = mysqli_fetch_assoc($exptUsr)) {
            $line = '';
            $line .= $k . ',';
            //storage name
            $storageNames = array();
            $userstorage = mysqli_query($db_con, "SELECT * FROM `tbl_storagelevel_to_permission` where user_id='" . $row['user_id'] . "'") or die("Error: test" . mysqli_error($db_con));
            while ($storageperms = mysqli_fetch_assoc($userstorage)) {
                $storagepermission = $storageperms['sl_id'];
                $storageName = mysqli_query($db_con, "SELECT sl_name FROM `tbl_storage_level` where sl_id='" . $storagepermission . "'") or die("Error: test" . mysqli_error($db_con));
                $rwstorageName = mysqli_fetch_assoc($storageName);
                $storageNames[] = $rwstorageName['sl_name'];
            }
            $line .= implode('|', $storageNames) . ',';
            foreach ($row as $key => $value) {
                if ((!isset($value) ) || ( $value == "" ) || ($value == " ") || ($value == NULL)) {
                    $value = "--";
                }
            }
            $line .= $row['first_name'] . ' ' . $row['last_name'] . ',';
            $line .= $row['user_role'] . ',';
            //$line .= $roleName . ',';
            //get userroles
            $permission = '';
            $userrolesView = mysqli_query($db_con, "SELECT * FROM `tbl_user_roles` where role_id='" . $userRid . "'") or die("Error: test" . mysqli_error($db_con));
            $perms = mysqli_fetch_assoc($userrolesView);
            $permission .= (($row['dashboard_mydms'] == '1') ? $lang['MY_DMS'] . " | " : "");
            $permission .= (($row['num_of_folder'] == '1') ? $lang['NO_OF_FOLDER'] . " | " : "");
            $permission .= (($row['num_of_file'] == '1') ? $lang['NO_OF_FILE'] . " | " : "");
            $permission .= (($row['memory_used'] == '1') ? $lang['MEMORY_USED'] . " | " : "");
            $permission .= (($row['storage_auth_plcy'] == '1') ? $lang['Storage_Auth'] . " | " : "");
            $permission .= (($row['bulk_upload'] == '1') ? $lang['Bulk_Upload'] . " | " : "");
            $permission .= (($row['folder_upload'] == '1') ? $lang['Upload_multi_folder'] . " | " : "");
            $permission .= (($row['save_query'] == '1') ? $lang['Sve_Qry'] . " | " : "");
            $permission .= (($row['subscribe_document'] == '1') ? $lang['subscribe'] . " | " : "");
            $permission .= (($row['create_storage'] == '1') ? $lang['Crt_Strg'] . " | " : "");
            $permission .= (($row['create_child_storage'] == '1') ? $lang['Add_Nw_Chld'] . " | " : "");
            $permission .= (($row['upload_doc_storage'] == '1') ? $lang['Upload_Documents'] . " | " : "");
            $permission .= (($row['modify_storage_level'] == '1') ? $lang['Edit'] . " | " : "");
            $permission .= (($row['delete_storage_level'] == '1') ? $lang['Delete'] . " | " : "");
            $permission .= (($row['move_storage_level'] == '1') ? $lang['move'] . " | " : "");
            $permission .= (($row['copy_storage_level'] == '1') ? $lang['Copy'] . " | " : "");
            $permission .= (($row['view_storage_audit'] == '1') ? $lang['Storage_Wise'] . " | " : "");
            $permission .= (($row['upload_logs'] == '1') ? $lang['upload_logs'] . " | " : "");
            $permission .= (($row['file_edit'] == '1') ? $lang['View_MetaData_file'] . " | " : "");
            $permission .= (($row['tif_file'] == '1') ? $lang['Tiff_File'] . " | " : "");
            $permission .= (($row['pdf_file'] == '1') ? $lang['pdf_file'] . " | " : "");
            $permission .= (($row['doc_file'] == '1') ? $lang['doc_file'] . " | " : "");
            $permission .= (($row['excel_file'] == '1') ? $lang['excel_file'] . " | " : "");
            $permission .= (($row['audio_file'] == '1') ? $lang['Audio_file'] . " | " : "");
            $permission .= (($row['video_file'] == '1') ? $lang['Video_file'] . " | " : "");
            $permission .= (($row['image_file'] == '1') ? $lang['image_file'] . " | " : "");
            $permission .= (($row['bulk_download'] == '1') ? $lang['Bulk_Download'] . " | " : "");
            $permission .= (($row['xls_download'] == '1') ? $lang['Excel_Download'] . " | " : "");
            $permission .= (($row['xls_print'] == '1') ? $lang['Excel_Print'] . " | " : "");
            $permission .= (($row['view_psd'] == '1') ? $lang['view_psd'] . " | " : "");
            $permission .= (($row['view_cdr'] == '1') ? $lang['view_cdr'] . " | " : "");
            $permission .= (($row['view_odt'] == '1') ? $lang['odt_file'] . " | " : "");
            $permission .= (($row['view_rtf'] == '1') ? $lang['rtf_file'] . " | " : "");
            $permission .= (($row['pdf_print'] == '1') ? $lang['Pdf_Print'] . " | " : "");
            $permission .= (($row['pdf_download'] == '1') ? $lang['Pdf_Download'] . " | " : "");
            $permission .= (($row['file_version'] == '1') ? $lang['View_File_Version'] . " | " : "");
            $permission .= (($row['delete_version'] == '1') ? $lang['Del_File_Version'] . " | " : "");
            $permission .= (($row['update_file'] == '1') ? $lang['Update_File'] . " | " : "");
            $permission .= (($row['export_csv'] == '1') ? $lang['Export_Csv'] . " | " : "");
            $permission .= (($row['move_file'] == '1') ? $lang['Move_Files'] . " | " : "");
            $permission .= (($row['copy_file'] == '1') ? $lang['Copy_Files'] . " | " : "");
            $permission .= (($row['share_file'] == '1') ? $lang['Shared_Files'] . " | " : "");
            $permission .= (($row['checkin_checkout'] == '1') ? $lang['Checkin_Checkout'] . " | " : "");
            $permission .= (($row['mail_files'] == '1') ? $lang['mail_files'] . " | " : "");
            $permission .= (($row['lock_folder'] == '1') ? $lang['lock_folder'] . " | " : "");
            $permission .= (($row['lock_file'] == '1') ? $lang['lock_file'] . " | " : "");
            $permission .= (($row['doc_weeding_out'] == '1') ? $lang['weed_out_time'] . " | " : "");
            $permission .= (($row['doc_share_time'] == '1') ? $lang['share_document_with_time'] . " | " : "");
            $permission .= (($row['doc_expiry_time'] == '1') ? $lang['expired_doc_list'] . " | " : "");
            $permission .= (($row['view_recycle_bin'] == '1') ? $lang['View_Recycle_Bin'] . " | " : "");
            $permission .= (($row['restore_file'] == '1') ? $lang['Restore_Files'] . " | " : "");
            $permission .= (($row['permanent_del'] == '1') ? $lang['per_dlt'] . " | " : "");
            $permission .= (($row['rename_file'] == '1') ? $lang['rename_file'] . " | " : "");
            $permission .= (($row['shared_file'] == '1') ? $lang['View_shared_Files'] . " | " : "");
            $permission .= (($row['share_with_me'] == '1') ? $lang['View_Share_With_Me'] . " | " : "");
            $permission .= (($row['doc_exp_setting'] == '1') ? $lang['expiry_document'] . " | " : "");
            $permission .= (($row['doc_retention_setting'] == '1') ? $lang['Retention_document'] . " | " : "");
            $permission .= (($row['doc_share_setting'] == '1') ? $lang['Share_docs_with_time'] . " | " : "");
            $permission .= (($row['view_exten'] == '1') ? $lang['view_exten'] . " | " : "");
            $permission .= (($row['add_exten'] == '1') ? $lang['add_exten'] . " | " : "");
            $permission .= (($row['enable_exten'] == '1') ? $lang['enable_exten'] . " | " : "");
            $permission .= (($row['delete_exten'] == '1') ? $lang['delete_exten'] . " | " : "");
            $permission .= (($row['shared_file'] == '1') ? $lang['View_shared_Files'] : "");

            // $line .= $permission;
            if (isset($_POST['searchperms']) && !empty($_POST['searchperms'])) {
                $permscolumns = $_POST['searchperms'];
                $permscolumn = explode(',', $permscolumns);
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
                $line .= implode('|', $columnlevel2);
            } else {
                $line .= $permission;
            }
            $result1 .= trim($line) . "\n";

            $k++;
        }

        //$result1 = str_replace("\r", "", $result1);
        header("Content-Type: application/csv");
        header("Content-Disposition: attachment; filename=User permission.csv");
        header("Pragma: no-cache");
        header("Expires: 0");
        echo "\xEF\xBB\xBF";
        print "$header1\n$result1";
    }
}
?>