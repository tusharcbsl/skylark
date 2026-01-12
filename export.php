<?php
require_once './application/config/database.php';
mysqli_set_charset($db_con, "utf8");
if (isset($_POST['startExport'])) {
    $rad = $_POST['radExp'];
    $slid = mysqli_real_escape_string($db_con, $_POST['slid']);
    $metaName = '';
    $header1 = '';
    if ($rad == 'all') {
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
        //print_r($metaName);
        //$metaName= implode(",", $metaName);
        mysqli_set_charset($db_con, "utf8");
        $exportData = mysqli_query($db_con, "select old_doc_name as filename,doc_extn as Extension $metaName,noofpages,uploaded_by,dateposted, ticket_id from tbl_document_master where doc_name='$slid' and flag_multidelete='1'");
        //$fields = mysqli_num_fields ( $exportData );
        mysqli_set_charset($db_con, "utf8");
        while ($fields = mysqli_fetch_field($exportData)) {
            if ($fields->name != 'ticket_id') { // skip ticket_id
                $header1 .= $fields->name . "\t";
            }
        }
        $header1 .= "Description of Work\t";

        $result1 = '';
        while ($row = mysqli_fetch_assoc($exportData)) {

            $railway_description = "";
            if (!empty($row['ticket_id'])) {
                $queryyy = "SELECT * FROM tbl_railway_master WHERE ticket_id='" . $row['ticket_id'] . "'";
                $railway_result = mysqli_query($db_con, $queryyy);

                if ($railway_result && mysqli_num_rows($railway_result) > 0) {
                    $railway_row = mysqli_fetch_assoc($railway_result);
                    $railway_description =  ucfirst($railway_row['description_of_work']);
                }
            }
            $line = '';
            foreach ($row as $key => $value) {
                if ($key == 'ticket_id') continue;
                if (!isset($value) || trim($value) == "" || $value == NULL) {
                    $value = "--\t";
                } else {
                    if ($key == 'uploaded_by') {
                        mysqli_set_charset($db_con, "utf8");
                        $dataOwner = mysqli_fetch_assoc(mysqli_query($db_con, "SELECT first_name, last_name FROM tbl_user_master WHERE user_id='$value'"));
                        $name = trim($dataOwner['first_name'] . ' ' . $dataOwner['last_name']);
                        $value = ($name != "") ? '"' . str_replace('"', '""', $name) . '"' . "\t" : "--\t";
                    } else {
                        $value = '"' . str_replace('"', '""', $value) . '"' . "\t";
                    }
                }

                $line .= $value;
            }
            $line .= '"' . str_replace('"', '""', $railway_description) . '"' . "\t";

            $result1 .= trim($line) . "\n";
        }
        $result1 = str_replace("\r", "", $result1);

        if ($result1 == "") {
            $result1 = "\nNo Record(s) Found!\n";
        }


        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=export.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        print "$header1\n$result1";
    }
    mysqli_close($db_con);
}
