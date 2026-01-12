<?php
require_once './application/pages/function.php';

//error_reporting(E_ALL);

if (isset($_POST['editMetaValue'], $_POST['token'])) {

    mysqli_set_charset($db_con, "utf8");

    $host = $host . '/' . $_SESSION['custom_ip'];
    //sk@120219:restrict up to two ip.
    $lip = $host;
    $ipos = strpos($lip, '/', strpos($lip, '/') + 1);
    $host = ($ipos ? substr($lip, 0, $ipos) : $lip);

    if (!empty($_FILES['fileName']['name'])) {

        $allowed = ALLOWED_EXTN;
        $allowext = implode(", ", $allowed);
        $ext = pathinfo($_FILES['fileName']['name'], PATHINFO_EXTENSION);
        if (!in_array(strtolower($ext), $allowed)) {

            echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '", "' . str_replace("ext", $allowext, $lang['document_allowed']) . '")</script>';
            exit();
        }

        $user_id = $_SESSION['cdes_user_id'];
        $doc_id = $_POST['docid'];
        $file_name = $_FILES['fileName']['name'];
        $file_size = $_FILES['fileName']['size'];
        $file_type = $_FILES['fileName']['type'];
        $file_tmp = $_FILES['fileName']['tmp_name'];
        $pageCount = $_POST['pageCount'];
        $extn = substr($file_name, strrpos($file_name, '.') + 1);
        $fname = substr($file_name, 0, strrpos($file_name, '.'));
        $fileExtn = substr($file_name, strrpos($file_name, ".") + 1);
        $getDocName = mysqli_query($db_con, "select * from tbl_document_master where doc_id = '$doc_id'"); // or die('Error:' . mysqli_error($db_con));
        $rwgetDocName = mysqli_fetch_assoc($getDocName);
        $docName = $rwgetDocName['doc_name'];
        //$docName = explode("_", $docName);
        $old_file_name = $rwgetDocName['old_doc_name'];
        $oldextn = substr($old_file_name, strrpos($old_file_name, '.') + 1); // old file extn
        $oldfname = substr($old_file_name, 0, strrpos($old_file_name, '.')); // old file name

        $updateDocName = $docName . '_' . $doc_id; //storage id followed by doc id
        $chekFileVersion = mysqli_query($db_con, "SELECT * FROM `tbl_document_master` WHERE find_in_set('$updateDocName', doc_name)"); // or die('Error:' . mysqli_error($db_con));
        $flVersion = mysqli_num_rows($chekFileVersion);
        $flVersion = $flVersion + 1;
        $nfilename = $oldfname . '_' . $flVersion;

        $strgName = mysqli_query($db_con, "select * from tbl_storage_level where sl_id = '$docName'"); // or die('Error:' . mysqli_error($db_con));
        $rwstrgName = mysqli_fetch_assoc($strgName);
        $storageName = $rwstrgName['sl_name'];
        $storageName = str_replace(" ", "", $storageName);
        $storageName = preg_replace('/[^A-Za-z0-9\-]/', '', $storageName);

        $updir = getStoragePath($db_con, $rwstrgName['sl_parent_id'], $rwstrgName['sl_depth_level']);
        if (!empty($updir)) {
            $updir = $updir . '/';
        } else {
            $updir = '';
        }


        $uploaddir = "extract-here/" . $updir . $storageName . '/';

        $folderpath = $uploaddir;

        if (!is_dir($uploaddir)) {
            mkdir($uploaddir, 0777, TRUE) or die(print_r(error_get_last()));
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
        $uploadInToFTP = true;
        $unlink_dir = false;

        // encypt file
        encrypt_my_file($uploaddir);
        if ($upload) {
            if (FTP_ENABLED) {
                $filepath = $updir . $storageName . '/' . $filenameEnct;
                $fileManager = new fileManager();
                $fileManager->conntFileServer();
                $uploadInToFTP = $fileManager->uploadFile($uploaddir, ROOT_FTP_FOLDER . '/' . $filepath, false);
            } else {
                $uploadInToFTP = TRUE;
            }
        }
        if ($uploadInToFTP) {

            //decrypt file
            // decrypt_my_file($uploaddir);

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
            // echo "INSERT INTO tbl_document_master($cols) select $cols from tbl_document_master where doc_id='$doc_id'";
            //  die;


            $createVrsn = mysqli_query($db_con, "INSERT INTO tbl_document_master($cols) select $cols from tbl_document_master where doc_id='$doc_id'"); // or die('Error:' . mysqli_error($db_con));
            $insertDocID = mysqli_insert_id($db_con);
            if (CREATE_THUMBNAIL) {
                $olddocname = base64_encode($insertDocID);
                //rename old thumbnail
                rename('thumbnail/' . base64_encode($doc_id) . '.jpg', 'thumbnail/' . $olddocname . '.jpg');
                //create thumbnail
                $newdocname = base64_encode($doc_id);
                if ($extn == 'jpg' || $extn == 'jpeg' || $extn == 'png') {
                    createThumbnail2($uploaddir, $newdocname);
                } elseif ($extn == 'pdf') {
                    changePdfToImage($uploaddir, $newdocname);
                }
            }
            /*  if($unlink_dir){
                unlink($uploaddir);
            } */

            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Versioning Document $file_name Added','$date',null,'$host',null)");
            if ($createVrsn) {
                $updateNew = mysqli_query($db_con, "update tbl_document_master set doc_name='$updateDocName', checkin_checkout='1' where doc_id='$insertDocID'");

                //////////////////////////////////////////////////// 
                $getMetaId = mysqli_query($db_con, "select * from tbl_document_master where doc_id = '" . $_POST['docid'] . "'"); // or die('Error:' . mysqli_error($db_con));
                //echo "select * from tbl_document_master where doc_id = '$_POST[docid]'";
                $meta_row = mysqli_fetch_assoc($getMetaId);
                $getMetaId = mysqli_query($db_con, "select * from tbl_metadata_to_storagelevel where sl_id = '$meta_row[doc_name]'"); // or die('Error:' . mysqli_error($db_con));
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
                        } else if ($rwgetMetaName['field_name'] == 'datetime') {
                            $fieldValue = $_POST['fieldName' . $i];
                            // echo $fieldValue = date("Y-m-d H:i:s", strtotime($fieldValue));
                            $updateMeta = mysqli_query($db_con, "update tbl_document_master set `$rwgetMetaName[field_name]` = '$fieldValue' where doc_id = '$_POST[docid]' or (substring_index(doc_name,'_',-1)='$_POST[docid]' and substring_index(doc_name,'_',1)='$_POST[docid]')"); // or die('Errordd' . mysqli_error($db_con));
                        } else {

                            $fieldValue = xss_clean($_POST['fieldName' . $i]);

                            //echo "update tbl_document_master set `$rwgetMetaName[field_name]` = '$fieldValue' where doc_id = '$_POST[metaId]' or (substring_index(doc_name,'_',-1)='$_POST[metaId]' and substring_index(doc_name,'_',1)='$_POST[metaId]')";
                            $updateMeta = mysqli_query($db_con, "update tbl_document_master set `$rwgetMetaName[field_name]` = '$fieldValue' where doc_id = '$_POST[docid]' or (substring_index(doc_name,'_',-1)='$_POST[docid]' and substring_index(doc_name,'_',1)='$_POST[docid]')"); // or die('Errordd' . mysqli_error($db_con));
                            //                    if ($updateMeta) {
                            //                        //metadata update log
                            //                        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'MetaData Value $fieldValue Assign in MetaData Field $rwgetMetaName[field_name] in $rwStrName[sl_name]','$date',null,'$host',null)");
                            //                        echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Mtadta_Updted_sucsfly'] . '");</script>';
                            //                    }
                        }
                    }

                    $i++;
                }

                $doc_path = $updir . $storageName . '/' . $filenameEnct;

                $updateOld = mysqli_query($db_con, "update tbl_document_master set old_doc_name='$file_name',filename='$fname', doc_extn='$extn', doc_path='$doc_path', uploaded_by='$user_id', doc_size='$file_size', noofpages='$pageCount', dateposted='$date', ftp_done='0' where doc_id='$doc_id'");

                if ($updateOld) {
                    $textdir = $folderpath . 'TXT/' . $doc_id . '.txt';
                    if (file_exists($textdir)) {
                        rename($textdir, $folderpath . 'TXT/' . $insertDocID . '.txt') or die(print_r(error_get_last()));
                    }
                }

                getData($doc_id,  $folderpath, $uploaddir);

                // echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Updtd_Sfly'] . '");</script>';
            }
        } else {
            echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['version_failed'] . '");</script>';
        }
    } else {

        $getMetaId = mysqli_query($db_con, "select * from tbl_document_master where doc_id = '$_POST[docid]'") or die('Error:' . mysqli_error($db_con));

        $meta_row = mysqli_fetch_assoc($getMetaId);

        if ($meta_row['doc_extn'] == 'ppt' || $meta_row['doc_extn'] == 'pptx' || $meta_row['doc_extn'] == 'xls' || $meta_row['doc_extn'] == 'xlsx' || $meta_row['doc_extn'] == 'doc' || $meta_row['doc_extn'] == 'docx') {
            require_once 'docs-editor-action.php';
        }


        $getMetaId = mysqli_query($db_con, "select * from tbl_document_master where doc_id = '$_POST[docid]'"); // or die('Error:' . mysqli_error($db_con));
        //echo "select * from tbl_document_master where doc_id = '$_POST[docid]'";
        $meta_row = mysqli_fetch_assoc($getMetaId);
        $getMetaId = mysqli_query($db_con, "select * from tbl_metadata_to_storagelevel where sl_id = '$meta_row[doc_name]'"); // or die('Error:' . mysqli_error($db_con));
        //echo "select * from tbl_metadata_to_storagelevel where sl_id = '$meta_row[doc_name]'";
        $i = 1;

        while ($rwgetMetaId = mysqli_fetch_assoc($getMetaId)) {

            $getMetaName = mysqli_query($db_con, "select * from tbl_metadata_master where id = '$rwgetMetaId[metadata_id]'"); // or die('Error:' . mysqli_error($db_con));
            $StorageNme = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id='$rwgetMetaId[sl_id]'");
            $rwStrName = mysqli_fetch_assoc($StorageNme);
            while ($rwgetMetaName = mysqli_fetch_assoc($getMetaName)) {
                $meta = mysqli_query($db_con, "select `$rwgetMetaName[field_name]` from tbl_document_master where doc_id='$meta_row[doc_id]'");
                $rwMeta = mysqli_fetch_array($meta);
                if ($rwgetMetaName['field_name'] == 'noofpages') {
                } else if ($rwgetMetaName['field_name'] == 'datetime') {
                    $fieldValue = $_POST['fieldName' . $i];
                    $fieldValue = date("Y-m-d H:i:s", strtotime($fieldValue));

                    $updateMeta = mysqli_query($db_con, "update tbl_document_master set `$rwgetMetaName[field_name]` = '$fieldValue' where doc_id = '$_POST[docid]' or (substring_index(doc_name,'_',-1)='$_POST[docid]' and substring_index(doc_name,'_',1)='$_POST[docid]')"); //or die('Errordd' . mysqli_error($db_con));
                } else {

                    $fieldValue = xss_clean(trim($_POST['fieldName' . $i]));
                    //echo "update tbl_document_master set `$rwgetMetaName[field_name]` = '$fieldValue' where doc_id = '$_POST[metaId]' or (substring_index(doc_name,'_',-1)='$_POST[metaId]' and substring_index(doc_name,'_',1)='$_POST[metaId]')";

                    $updateMeta = mysqli_query($db_con, "update tbl_document_master set `$rwgetMetaName[field_name]` = '$fieldValue' where doc_id = '$_POST[docid]' or (substring_index(doc_name,'_',-1)='$_POST[docid]' and substring_index(doc_name,'_',1)='$_POST[docid]')"); // or die('Errordd' . mysqli_error($db_con));
                    if ($updateMeta) {
                        //metadata update log
                        $sdate = date('Y-m-d H:i:s');
                        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'MetaData Value $fieldValue Assign in MetaData Field $rwgetMetaName[field_name] in $rwStrName[sl_name]','$sdate',null,'$host',null)"); // or die('error log: ' . mysqli_error($db_con));
                    }
                }
            }

            $i++;
        }
    }

    if (($updateNew && $updateOld) || ($updateMeta)) {

        if ($logversionDocument || $log) {

            $subdocId = $_POST['docid'];
            $userId = array();
            $checksubs = mysqli_query($db_con, "SELECT * FROM tbl_document_subscriber WHERE subscribe_docid='$subdocId' and find_in_set('2',action_id)");
            while ($rwcheckSubs = mysqli_fetch_assoc($checksubs)) {
                $userId[] = $rwcheckSubs['subscriber_userid'];
            }
            $userIds = implode(',', $userId);
            $mailto = array();
            $k = 1;
            $touser = mysqli_query($db_con, "SELECT user_email_id,first_name FROM tbl_user_master WHERE user_id in($userIds)");
            while ($rwtouser = mysqli_fetch_assoc($touser)) {
                $mailto[$k]['user_email_id'] = $rwtouser['user_email_id'];
                $mailto[$k]['first_name'] = $rwtouser['first_name'];

                $k++;
            }

            $getAction = mysqli_query($db_con, "SELECT remarks FROM tbl_ezeefile_logs WHERE id='$insertId'");
            $rwgetAction = mysqli_fetch_assoc($getAction);
            $fileaction = $rwgetAction['remarks'];
            if (!empty($file_name)) {
                $documentName = $old_file_name;
                $fileaction = "$old_file_name added as Version file of $file_name document in $storageName folder." . ' <br> ' . $fileaction;
            } else {
                $documentName = $old_file_name;
            }
            require_once './mail.php';

            foreach ($mailto as $to) {
                $email = $to['user_email_id'];
                $name = $to['first_name'];
                if (MAIL_BY_SOCKET) {
                    $paramsArray = array(
                        'email' => $email,
                        'filenamed' => $filenamed,
                        'action' => 'filesubscribe',
                        'projectName' => $projectName,
                        'fileaction' => $fileaction,
                        'name' => $name
                    );
                    mailBySocket($paramsArray);
                } else {

                    $mailsent = mailsenttoDocumentSubscribeUsers($email, $filenamed, $projectName, $fileaction, $name);
                }
            }
        }


        $checkout = mysqli_query($db_con, "UPDATE tbl_document_master set checkin_checkout=1 WHERE doc_id='$_POST[docid]'");
        echo '<script>taskSuccess("storageFiles?id=' . base64_encode($meta_row['doc_name']) . '","' . $lang['Mtadta_Updted_sucsfly'] . '");</script>';
    } else {

        $checkout = mysqli_query($db_con, "UPDATE tbl_document_master set checkin_checkout=1 WHERE doc_id='$_POST[docid]'");
        $sdate = date('Y-m-d H:i:s');
        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'You have no made any change during checkin checkout file','$sdate',null,'$host',null)"); // or die('error log: ' . mysqli_error($db_con));
        echo '<script>taskSuccess("storageFiles?id=' . base64_encode($meta_row['doc_name']) . '","' . $lang['Mtadta_Updted_sucsfly'] . '");</script>';
    }
    mysqli_close($db_con);

    apc_cache_clear();
}


function getData($docId, $outputDir, $inputDir)
{
    /**
     * 
     * @param String $url
     * @param Array $params 
     * done by M.U
     */
    $url = BASE_URL . 'ocr_bulk.php';
    $params = array('docId' => $docId, 'outputDir' => $outputDir, 'inputDir' => $inputDir);

    foreach ($params as $key => &$val) {
        if (is_array($val))
            $val = implode(',', $val);
        $post_params[] = $key . '=' . urlencode($val);
    }
    $post_string = implode('&', $post_params);
    $parts = parse_url($url); //print_r($parts);die();
    //echo 'presocket';
    $fp = fsockopen($parts['host'], isset($parts['port']) ? $parts['port'] : 80, $errno, $errstr, 360000);
    //$fp = fsockopen('ssl://' . $parts['host'], isset($parts['port']) ? $parts['port'] : 443, $errno, $errstr, 3600);
    //var_dump($fp);
    if (!$fp) {
        echo 'socket error';
    } else {
        $out = "POST " . $parts['path'] . " HTTP/1.1\r\n";
        $out .= "Host: " . $parts['host'] . "\r\n";
        $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $out .= "Content-Length: " . strlen($post_string) . "\r\n";
        $out .= "Connection: Close\r\n\r\n";
        if (isset($post_string))
            $out .= $post_string;

        fwrite($fp, $out);
    }
}
