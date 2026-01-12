<?php

require_once('loginvalidate.php');
require_once './application/pages/function.php';
mysqli_set_charset($db_con, "utf8");
if (isset($_POST['startExport'], $_POST['token'])) {
    $rad = $_POST['radExp'];
    $slid = mysqli_real_escape_string($db_con, $_POST['slid']);

    $metaName = '';
    $header1 = '';
    if ($rad == 'all') {
        $slIdesI = findsubfolder($slid, $db_con);
        $slid = implode(',', $slIdesI);
        csvExportAll($slid, $date, $host);
    } else {
        csvExport($slid, $date, $host);
    }
}

function csvExportAll($slid, $date, $host) {
    global $db_con;

    $meta = mysqli_query($db_con, "select DISTINCT metadata_id from tbl_metadata_to_storagelevel where sl_id in($slid)");
    while ($rwMeta = mysqli_fetch_assoc($meta)) {
        $metan = mysqli_query($db_con, "select field_name from tbl_metadata_master where id='$rwMeta[metadata_id]'");
        $rwMetan = mysqli_fetch_assoc($metan);
        $metaName .= ',`' . $rwMetan['field_name'] . '`';
    }

    //$exportData= mysqli_query($db_con, "select old_doc_name as filename,$metaName,uploaded_by,dateposted from tbl_document_master where doc_name='$slid'");
    $exportData = mysqli_query($db_con, "select doc_name as FolderName, old_doc_name as filename,noofpages,doc_extn as Extension $metaName,uploaded_by,dateposted from tbl_document_master where doc_name in($slid) and flag_multidelete='1'");
    //$fields = mysqli_num_fields ( $exportData );
    if (mysqli_num_rows($exportData) > 0) {
        //$header1  = 'Storage Name' . "\t";  
        while ($fields = mysqli_fetch_field($exportData)) {
            $header1 .= $fields->name . "\t";
        }
        while ($row = mysqli_fetch_assoc($exportData)) {
            $strg = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id='" . $row['FolderName'] . "'");
            $rwstrg = mysqli_fetch_assoc($strg);
            $rwstrgname = $rwstrg['sl_name'];
            $line = '';
            foreach ($row as $key => $value) {
                if ((!isset($value) ) || ( $value == "" ) || ($value == NULL)) {
                    $value = "--\t";
                } else {
                    if ($key == 'uploaded_by') {
                        mysqli_set_charset($db_con, "utf8");
                        $dataOwner = mysqli_fetch_assoc(mysqli_query($db_con, "select first_name,last_name from tbl_user_master where user_id='$value'"));
                        $name = $dataOwner['first_name'] . ' ' . $dataOwner['last_name'];
                        if ((!isset($name) ) || ( $name == "" )) {
                            $value = "\t";
                        } else {
                            $value = str_replace('"', '""', $name);
                            $value = '"' . $value . '"' . "\t";
                        }
                    } else if ($key == 'dateposted') {
                        $value = str_replace('"', '""', $value);
                        $value = '"' . date('d-m-Y H:i:s', strtotime($value)) . '"' . "\t";
                    } else if ($key == 'FolderName') {
                        $value = str_replace('"', '""', $value);
                        $value = '"' . $rwstrgname . '"' . "\t";
                    } else {
                        $value = str_replace('"', '""', $value);
                        $value = '"' . $value . '"' . "\t";
                    }
                }

                $line .= $value;
            }
            $result1 .= trim($line) . "\n";
        }

        $result1 = str_replace("\r", "", $result1);
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=export.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        print "$header1\n$result1\n";
        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Keyword Exported','$date','$host','File Keyword of $storgName folder and their sub-folders exported in csv')") or die('error : ' . mysqli_error($db_con));
        exit();
    } else {
        $result1 = "No Record(s) Found!\n";
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=export.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        print "$result1\n";
        exit();
    }
    $sql_child = "select * FROM tbl_storage_level WHERE sl_parent_id = '$slid' ";
    $sql_child_run = mysqli_query($db_con, $sql_child) or die('Error:' . mysqli_error($db_con));
    if (mysqli_num_rows($sql_child_run) > 0) {

        while ($rwchild = mysqli_fetch_assoc($sql_child_run)) {

            $child = $rwchild['sl_id'];
            csvExportAll($child);
        }
    }
}

function csvExport($slid, $date, $host) {
    global $db_con;
    $strgName = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id='$slid'");
    $rwstrgName = mysqli_fetch_assoc($strgName);
    $storgName = $rwstrgName['sl_name'];
    $meta = mysqli_query($db_con, "select * from tbl_metadata_to_storagelevel where sl_id='$slid'");
    while ($rwMeta = mysqli_fetch_assoc($meta)) {
        $metan = mysqli_query($db_con, "select field_name from tbl_metadata_master where id='$rwMeta[metadata_id]'");
        $rwMetan = mysqli_fetch_assoc($metan);
        if (empty($metaName)) {
            $metaName = ',`' . $rwMetan['field_name'] . '`';
        } else {
            $metaName .= ',`' . $rwMetan['field_name'] . '`';
        }
    }

    $exportData = mysqli_query($db_con, "select doc_name as FolderName, old_doc_name as filename,doc_extn as Extension $metaName,uploaded_by,dateposted from tbl_document_master where doc_name='$slid' and flag_multidelete='1'") or die('error' . mysqli_error($db_con));
    //$fields = mysqli_num_fields ( $exportData );
    if (mysqli_num_rows($exportData) > 0) {
        //$header1  = 'Storage Name' . "\t";  
        while ($fields = mysqli_fetch_field($exportData)) {
            $header1 .= $fields->name . "\t";
        }
        while ($row = mysqli_fetch_assoc($exportData)) {
            $strg = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id='" . $row['FolderName'] . "'");
            $rwstrg = mysqli_fetch_assoc($strg);
            $rwstrgname = $rwstrg['sl_name'];
            $line = '';
            foreach ($row as $key => $value) {
                if ((!isset($value) ) || ( $value == "" ) || ($value == NULL)) {
                    $value = "--\t";
                } else {
                    if ($key == 'uploaded_by') {
                        $dataOwner = mysqli_fetch_assoc(mysqli_query($db_con, "select first_name,last_name from tbl_user_master where user_id='$value'"));
                        $name = $dataOwner['first_name'] . ' ' . $dataOwner['last_name'];
                        if ((!isset($name) ) || ( $name == "" )) {
                            $value = "\t";
                        } else {
                            $value = str_replace('"', '""', $name);
                            $value = '"' . $value . '"' . "\t";
                        }
                    } else if ($key == 'dateposted') {
                        $value = str_replace('"', '""', $value);
                        $value = '"' . date('d-m-Y H:i:s', strtotime($value)) . '"' . "\t";
                    } else if ($key == 'FolderName') {
                        $value = str_replace('"', '""', $value);
                        $value = '"' . $rwstrgname . '"' . "\t";
                    } else {
                        $value = str_replace('"', '""', $value);
                        $value = '"' . $value . '"' . "\t";
                    }
                }

                $line .= $value;
            }
            $result1 .= trim($line) . "\n";
        }
        $result1 = str_replace("\r", "", $result1);

        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=export.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        print "$header1\n$result1\n";
        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`sl_id`, `action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','$slid','Keyword Exported','$date','$host','File Keyword of $storgName folder exported in csv')") or die('error : ' . mysqli_error($db_con));
        exit();
    } else {
        $result1 = "No Record(s) Found!\n";
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=export.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        print "$result1\n";
        exit();
    }
}

?>