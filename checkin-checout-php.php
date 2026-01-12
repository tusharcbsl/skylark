<?php
//if (isset($_POST['editMetaValue'])) {
//     $host=$host.'/'.$_SESSION['custom_ip'];
//    if (!empty($_FILES['fileName']['name'])) {
//        $user_id = $_SESSION['cdes_user_id'];
//        $doc_id = $_POST['docid'];
//        $file_name = $_FILES['fileName']['name'];
//        $file_size = $_FILES['fileName']['size'];
//        $file_type = $_FILES['fileName']['type'];
//        $file_tmp = $_FILES['fileName']['tmp_name'];
//        $pageCount = $_POST['pageCount'];
//        $extn = substr($file_name, strrpos($file_name, '.') + 1);
//        $fname = substr($file_name, 0, strrpos($file_name, '.'));
//        $fileExtn = substr($file_name, strrpos($file_name, ".") + 1);
//        $getDocName = mysqli_query($db_con, "select * from tbl_document_master where doc_id = '$doc_id'") or die('Error:' . mysqli_error($db_con));
//        $rwgetDocName = mysqli_fetch_assoc($getDocName);
//        $docName = $rwgetDocName['doc_name'];
//        //$docName = explode("_", $docName);
//        $old_file_name = $rwgetDocName['old_doc_name'];
//        $oldextn = substr($old_file_name, strrpos($old_file_name, '.') + 1); // old file extn
//        $oldfname = substr($old_file_name, 0, strrpos($old_file_name, '.')); // old file name
//
//        $updateDocName = $docName . '_' . $doc_id; //storage id followed by doc id
//        $chekFileVersion = mysqli_query($db_con, "SELECT * FROM `tbl_document_master` WHERE find_in_set('$updateDocName', doc_name)") or die('Error:' . mysqli_error($db_con));
//        $flVersion = mysqli_num_rows($chekFileVersion);
//        $flVersion = $flVersion + 1;
//        $nfilename = $oldfname . '_' . $flVersion;
//
//        $strgName = mysqli_query($db_con, "select * from tbl_storage_level where sl_id = '$docName'") or die('Error:' . mysqli_error($db_con));
//        $rwstrgName = mysqli_fetch_assoc($strgName);
//        $storageName = $rwstrgName['sl_name'];
//        $storageName = str_replace(" ", "", $storageName);
//        $storageName = preg_replace('/[^A-Za-z0-9\-]/', '', $storageName);
//        $uploaddir = "extract-here/" . ROOT_FTP_FOLDER . "/" . $storageName . '/';
//        if (!is_dir($uploaddir)) {
//            mkdir($uploaddir, 0777, TRUE) or die(print_r(error_get_last()));
//        }
//        $nfilename = preg_replace('/[^A-Za-z0-9_\-]/', '', $nfilename);
//        // $filenameEnct=$fname.'.'.$extn;// urlencode(base64_encode($fname)).'.'.$extn;
//        $filenameEnct = urlencode(base64_encode($nfilename));
//        $filenameEnct = preg_replace('/[^A-Za-z0-9_\-]/', '', $filenameEnct);
//        $filenameEnct = $filenameEnct . '.' . $extn;
//        $filenameEnct = time() . $filenameEnct;
//
//        //  $image_path = "images/" . $file_name;
//        $uploaddir = $uploaddir . $filenameEnct;
//        $upload = move_uploaded_file($file_tmp, $uploaddir) or die(print_r(error_get_last()));
//        $uploadInToFTP = false;
//        if ($upload) {
//            if (FTP_ENABLED) {
//
//                $ftp = new ftp();
//                $ftp->conn("$fileserver", "$port", "$ftpUser", "$ftpPwd");
//
//                $filepath = $storageName . '/' . $filenameEnct;
//                $uploadfile = $ftp->put(ROOT_FTP_FOLDER . '/' . $filepath, $uploaddir);
//                $arr = $ftp->getLogData();
//                if ($uploadfile) {
//                    $uploadInToFTP = true;
//                    unlink($uploaddir);
//                } else {
//                    $uploadInToFTP = false;
//                    if ($arr['error'] != "") {
//                        echo '<h2>Error:</h2>' . implode('<br />', $arr['error']);
//                    }
//                }
//            } else {
//                $uploadInToFTP = true;
//            }
//        }
//        if ($uploadInToFTP) {
//
//            $cols = '';
//            $columns = mysqli_query($db_con, "SHOW COLUMNS FROM tbl_document_master");
//            while ($rwCols = mysqli_fetch_array($columns)) {
//                if ($rwCols['Field'] != 'doc_id') {
//                    if (empty($cols)) {
//                        $cols = '`' . $rwCols['Field'] . '`';
//                    } else {
//                        $cols = $cols . ',`' . $rwCols['Field'] . '`';
//                    }
//                }
//            }
//            //echo "INSERT INTO tbl_document_master($cols) select $cols from tbl_document_master where doc_id='$doc_id'";
//            // die;
//            $createVrsn = mysqli_query($db_con, "INSERT INTO tbl_document_master($cols) select $cols from tbl_document_master where doc_id='$doc_id'") or die('Error:' . mysqli_error($db_con));
//            $insertDocID = mysqli_insert_id($db_con);
//            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Versioning Document $file_name Added','$date',null,'$host',null)");
//            if ($createVrsn) {
//                $updateNew = mysqli_query($db_con, "update tbl_document_master set doc_name='$updateDocName', checkin_checkout='1' where doc_id='$insertDocID'");
//
//                //////////////////////////////////////////////////// 
//                $getMetaId = mysqli_query($db_con, "select * from tbl_document_master where doc_id = '$_POST[docid]'") or die('Error:' . mysqli_error($db_con));
//                //echo "select * from tbl_document_master where doc_id = '$_POST[docid]'";
//                $meta_row = mysqli_fetch_assoc($getMetaId);
//                $getMetaId = mysqli_query($db_con, "select * from tbl_metadata_to_storagelevel where sl_id = '$meta_row[doc_name]'") or die('Error:' . mysqli_error($db_con));
//                //echo "select * from tbl_metadata_to_storagelevel where sl_id = '$meta_row[doc_name]'";
//                $i = 1;
//
//                while ($rwgetMetaId = mysqli_fetch_assoc($getMetaId)) {
//
//                    $getMetaName = mysqli_query($db_con, "select * from tbl_metadata_master where id = '$rwgetMetaId[metadata_id]'") or die('Error:' . mysqli_error($db_con));
//                    $StorageNme = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id='$rwgetMetaId[sl_id]'");
//                    $rwStrName = mysqli_fetch_assoc($StorageNme);
//                    while ($rwgetMetaName = mysqli_fetch_assoc($getMetaName)) {
//                        $meta = mysqli_query($db_con, "select `$rwgetMetaName[field_name]` from tbl_document_master where doc_id='$meta_row[doc_id]'");
//                        $rwMeta = mysqli_fetch_array($meta);
//                        //$metadatValue = $rwMeta[''];
//                        //echo $i; echo '-';
//                        if ($rwgetMetaName['field_name'] == 'noofpages') {
//                            
//                        } else if ($rwgetMetaName['field_name'] == 'datetime') {
//                            $fieldValue = $_POST['fieldName' . $i];
//                            $fieldValue = date("Y-m-d H:i:s", strtotime($fieldValue));
//                            $updateMeta = mysqli_query($db_con, "update tbl_document_master set `$rwgetMetaName[field_name]` = '$fieldValue' where doc_id = '$_POST[docid]' or (substring_index(doc_name,'_',-1)='$_POST[docid]' and substring_index(doc_name,'_',1)='$_POST[docid]')") or die('Errordd' . mysqli_error($db_con));
//                        } else {
//
//                            $fieldValue = $_POST['fieldName' . $i];
//
//                            //echo "update tbl_document_master set `$rwgetMetaName[field_name]` = '$fieldValue' where doc_id = '$_POST[metaId]' or (substring_index(doc_name,'_',-1)='$_POST[metaId]' and substring_index(doc_name,'_',1)='$_POST[metaId]')";
//                            $updateMeta = mysqli_query($db_con, "update tbl_document_master set `$rwgetMetaName[field_name]` = '$fieldValue' where doc_id = '$_POST[docid]' or (substring_index(doc_name,'_',-1)='$_POST[docid]' and substring_index(doc_name,'_',1)='$_POST[docid]')") or die('Errordd' . mysqli_error($db_con));
////                    if ($updateMeta) {
////                        //metadata update log
////                        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'MetaData Value $fieldValue Assign in MetaData Field $rwgetMetaName[field_name] in $rwStrName[sl_name]','$date',null,'$host',null)");
////                        echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Mtadta_Updted_sucsfly'] . '");</script>';
////                    }
//                        }
//                    }
//
//                    $i++;
//                }
//
//
//                $updateOld = mysqli_query($db_con, "update tbl_document_master set old_doc_name='$file_name',filename='$fname', doc_extn='$extn', doc_path='$storageName/$filenameEnct', uploaded_by='$user_id', doc_size='$file_size', noofpages='$pageCount', dateposted='$date' where doc_id='$doc_id'");
//                // echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Updtd_Sfly'] . '");</script>';
//            }
//        } else {
//            echo'<script>taskFailed("' . basename($_SERVER[REQUEST_URI]) . '","' . $lang['version_failed'] . '");</script>';
//        }
//    } else {
//
//        $getMetaId = mysqli_query($db_con, "select * from tbl_document_master where doc_id = '$_POST[docid]'") or die('Error:' . mysqli_error($db_con));
//        //echo "select * from tbl_document_master where doc_id = '$_POST[docid]'";
//        $meta_row = mysqli_fetch_assoc($getMetaId);
//        $getMetaId = mysqli_query($db_con, "select * from tbl_metadata_to_storagelevel where sl_id = '$meta_row[doc_name]'") or die('Error:' . mysqli_error($db_con));
//        //echo "select * from tbl_metadata_to_storagelevel where sl_id = '$meta_row[doc_name]'";
//        $i = 1;
//
//        while ($rwgetMetaId = mysqli_fetch_assoc($getMetaId)) {
//
//            $getMetaName = mysqli_query($db_con, "select * from tbl_metadata_master where id = '$rwgetMetaId[metadata_id]'") or die('Error:' . mysqli_error($db_con));
//            $StorageNme = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id='$rwgetMetaId[sl_id]'");
//            $rwStrName = mysqli_fetch_assoc($StorageNme);
//            while ($rwgetMetaName = mysqli_fetch_assoc($getMetaName)) {
//                $meta = mysqli_query($db_con, "select `$rwgetMetaName[field_name]` from tbl_document_master where doc_id='$meta_row[doc_id]'");
//                $rwMeta = mysqli_fetch_array($meta);
//                //$metadatValue = $rwMeta[''];
//                //echo $i; echo '-';
//                if ($rwgetMetaName['field_name'] == 'noofpages') {
//                    
//                } else if ($rwgetMetaName['field_name'] == 'datetime') {
//                    $fieldValue = $_POST['fieldName' . $i];
//                    echo $fieldValue = date("Y-m-d H:i:s", strtotime($fieldValue));
//                    $updateMeta = mysqli_query($db_con, "update tbl_document_master set `$rwgetMetaName[field_name]` = '$fieldValue' where doc_id = '$_POST[docid]' or (substring_index(doc_name,'_',-1)='$_POST[docid]' and substring_index(doc_name,'_',1)='$_POST[docid]')") or die('Errordd' . mysqli_error($db_con));
//                } else {
//
//                    $fieldValue = $_POST['fieldName' . $i];
//                    //echo "update tbl_document_master set `$rwgetMetaName[field_name]` = '$fieldValue' where doc_id = '$_POST[metaId]' or (substring_index(doc_name,'_',-1)='$_POST[metaId]' and substring_index(doc_name,'_',1)='$_POST[metaId]')";
//                    $updateMeta = mysqli_query($db_con, "update tbl_document_master set `$rwgetMetaName[field_name]` = '$fieldValue' where doc_id = '$_POST[docid]' or (substring_index(doc_name,'_',-1)='$_POST[docid]' and substring_index(doc_name,'_',1)='$_POST[docid]')") or die('Errordd' . mysqli_error($db_con));
////                    
//                   if ($updateMeta) {
//                      $sdate = date('Y-m-d H:i:s');
//                         $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'MetaData Value $fieldValue Assign in MetaData Field $rwgetMetaName[field_name] in $rwStrName[sl_name]','$sdate',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
//                    }
//                }
//            }
//
//            $i++;
//        }
//    }
//    $checkout = mysqli_query($db_con, "UPDATE tbl_document_master set checkin_checkout=1 WHERE doc_id='$docId'");
//    if ($checkout && ($updateNew && $updateOld) || ($updateMeta)) {
//        echo'<script>taskSuccess("storageFiles?id=' . base64_encode($meta_row['doc_name']) . '","' . $lang['Mtadta_Updted_sucsfly'] . '");</script>';
//    }
//    mysqli_close($db_con);
//}
?>