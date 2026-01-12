<?php
require_once './application/config/database.php';
mysqli_set_charset($db_con, "utf8");

if (isset($_POST['exportData'])) {
    $docIds = $_POST['export_doc_ids'];
    $selectFormat = trim($_POST['select_Fm']);
    $slId = $_POST['id'];

    // Common metadata collection
    $metaName = '';
    $meta = mysqli_query($db_con, "SELECT * FROM tbl_metadata_to_storagelevel WHERE sl_id='$slId'");
    while ($rwMeta = mysqli_fetch_assoc($meta)) {
        $metan = mysqli_query($db_con, "SELECT field_name FROM tbl_metadata_master WHERE id='$rwMeta[metadata_id]'");
        $rwMetan = mysqli_fetch_assoc($metan);
        $metaName .= ',`' . $rwMetan['field_name'] . '`';
    }

    // ✅ ticket_id is used internally only, not displayed
    $queryBase = "SELECT old_doc_name $metaName, noofpages, uploaded_by, dateposted, ticket_id 
                  FROM tbl_document_master 
                  WHERE doc_id IN($docIds) AND doc_name='$slId' 
                  ORDER BY old_doc_name";

    if ($selectFormat == "excel") {
        $exportData = mysqli_query($db_con, $queryBase);

        // Build headers
        $header1 = '';
        while ($fields = mysqli_fetch_field($exportData)) {
            if ($fields->name == "old_doc_name") {
                $header1 .= "File Name\t";
            } elseif ($fields->name != "ticket_id") { // ❌ skip ticket_id
                $header1 .= $fields->name . "\t";
            }
        }
        $header1 .= "Description of Work\t";

        $result1 = '';
        while ($row = mysqli_fetch_assoc($exportData)) {
            $railway_description = "";
            if (!empty($row['ticket_id'])) {
                $qry = "SELECT description_of_work FROM tbl_railway_master WHERE ticket_id='" . $row['ticket_id'] . "'";
                $railway_result = mysqli_query($db_con, $qry);
                if ($railway_result && mysqli_num_rows($railway_result) > 0) {
                    $railway_row = mysqli_fetch_assoc($railway_result);
                    $railway_description = ucfirst($railway_row['description_of_work']);
                }
            }

            $line = '';
            foreach ($row as $key => $value) {
                if ($key == "ticket_id") continue; // ✅ skip this column
                
                if (empty(trim($value))) {
                    $value = "--\t";
                } else {
                    if ($key == 'uploaded_by') {
                        $dataOwner = mysqli_fetch_assoc(mysqli_query($db_con, "SELECT first_name,last_name FROM tbl_user_master WHERE user_id='$value'"));
                        $name = trim($dataOwner['first_name'] . ' ' . $dataOwner['last_name']);
                        $value = '"' . $name . '"' . "\t";
                    } elseif ($key == 'dateposted') {
                        $value = '"' . date('d-m-Y h:i:s', strtotime($value)) . '"' . "\t";
                    } else {
                        $value = '"' . str_replace('"', '""', $value) . '"' . "\t";
                    }
                }
                $line .= $value;
            }

            $line .= '"' . str_replace('"', '""', $railway_description) . '"' . "\t";
            $result1 .= trim($line) . "\n";
        }

        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=export.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        print "$header1\n$result1";
    } 
    
    elseif ($selectFormat == "csv") {
        $exportData = mysqli_query($db_con, $queryBase);

        $header1 = '';
        while ($fields = mysqli_fetch_field($exportData)) {
            if ($fields->name == "old_doc_name") {
                $header1 .= "File Name,";
            } elseif ($fields->name != "ticket_id") {
                $header1 .= $fields->name . ",";
            }
        }
        $header1 .= "Description of Work";

        $result1 = '';
        while ($row = mysqli_fetch_assoc($exportData)) {
            $railway_description = "";
            if (!empty($row['ticket_id'])) {
                $qry = "SELECT description_of_work FROM tbl_railway_master WHERE ticket_id='" . $row['ticket_id'] . "'";
                $railway_result = mysqli_query($db_con, $qry);
                if ($railway_result && mysqli_num_rows($railway_result) > 0) {
                    $railway_row = mysqli_fetch_assoc($railway_result);
                    $railway_description = ucfirst($railway_row['description_of_work']);
                }
            }

            $line = '';
            foreach ($row as $key => $value) {
                if ($key == "ticket_id") continue; // ✅ skip this column

                if (empty(trim($value))) {
                    $value = "--,";
                } else {
                    if ($key == 'uploaded_by') {
                        $dataOwner = mysqli_fetch_assoc(mysqli_query($db_con, "SELECT first_name,last_name FROM tbl_user_master WHERE user_id='$value'"));
                        $name = trim($dataOwner['first_name'] . ' ' . $dataOwner['last_name']);
                        $value = '"' . $name . '"' . ",";
                    } elseif ($key == 'dateposted') {
                        $value = '"' . date('d-m-Y h:i:s', strtotime($value)) . '"' . ",";
                    } else {
                        $value = '"' . str_replace('"', '""', $value) . '"' . ",";
                    }
                }
                $line .= $value;
            }
            $line .= '"' . str_replace('"', '""', $railway_description) . '"' . "\n";
            $result1 .= trim($line) . "\n";
        }

        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=export.csv");
        header("Pragma: no-cache");
        header("Expires: 0");
        print "$header1\n$result1";
    } 
    
    elseif ($selectFormat == "pdf") {
        require('./wordwrap.php');
        $exportData = mysqli_query($db_con, $queryBase);

        $widthCell = [];
        $headers = [];

        while ($fields = mysqli_fetch_field($exportData)) {
            if ($fields->name == "ticket_id") continue; // skip
            if ($fields->name == "old_doc_name") {
                $headers[] = "File Name";
            } else {
                $headers[] = $fields->name;
            }
            $widthCell[] = 50;
        }
        $headers[] = "Description of Work";
        $widthCell[] = 50;

        $pdf = new PDF_MC_Table('L', 'mm', array(297, 210));
        $pdf->SetMargins(5, 5, 5);
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetWidths($widthCell);
        $pdf->Row($headers);

        while ($row = mysqli_fetch_assoc($exportData)) {
            $data = [];
            foreach ($row as $key => $value) {
                if ($key == "ticket_id") continue;
                if ($key == 'uploaded_by') {
                    $user = mysqli_fetch_assoc(mysqli_query($db_con, "SELECT first_name,last_name FROM tbl_user_master WHERE user_id='$value'"));
                    $value = $user['first_name'] . ' ' . $user['last_name'];
                } elseif ($key == 'dateposted') {
                    $value = date('d-m-Y h:i:s', strtotime($value));
                }
                $data[] = $value;
            }

            $desc = "";
            if (!empty($row['ticket_id'])) {
                $res = mysqli_query($db_con, "SELECT description_of_work FROM tbl_railway_master WHERE ticket_id='" . $row['ticket_id'] . "'");
                if ($res && mysqli_num_rows($res) > 0) {
                    $rw = mysqli_fetch_assoc($res);
                    $desc = ucfirst($rw['description_of_work']);
                }
            }
            $data[] = $desc;
            $pdf->Row($data);
        }

        $pdf->Output("D", "export.pdf");
    } 
    
    elseif ($selectFormat == "word") {
        $exportData = mysqli_query($db_con, $queryBase);

        $html = '<table width="100%" border="1" align="center"><thead><tr><th>S.No.</th>';
        while ($fields = mysqli_fetch_field($exportData)) {
            if ($fields->name == "ticket_id") continue;
            $html .= '<th>' . ($fields->name == "old_doc_name" ? "File Name" : $fields->name) . '</th>';
        }
        $html .= '<th>Description of Work</th></tr></thead>';

        $i = 1;
        while ($row = mysqli_fetch_assoc($exportData)) {
            $html .= '<tr><td>' . $i++ . '</td>';
            foreach ($row as $key => $value) {
                if ($key == "ticket_id") continue;

                if (empty(trim($value))) {
                    $html .= '<td>--</td>';
                } elseif ($key == 'uploaded_by') {
                    $user = mysqli_fetch_assoc(mysqli_query($db_con, "SELECT first_name,last_name FROM tbl_user_master WHERE user_id='$value'"));
                    $html .= '<td>' . $user['first_name'] . ' ' . $user['last_name'] . '</td>';
                } elseif ($key == 'dateposted') {
                    $html .= '<td>' . date('d-m-Y h:i:s', strtotime($value)) . '</td>';
                } else {
                    $html .= '<td>' . $value . '</td>';
                }
            }

            // Description of Work
            $desc = "";
            if (!empty($row['ticket_id'])) {
                $res = mysqli_query($db_con, "SELECT description_of_work FROM tbl_railway_master WHERE ticket_id='" . $row['ticket_id'] . "'");
                if ($res && mysqli_num_rows($res) > 0) {
                    $rw = mysqli_fetch_assoc($res);
                    $desc = ucfirst($rw['description_of_work']);
                }
            }
            $html .= '<td>' . $desc . '</td></tr>';
        }

        $html .= '</table>';
        header("Content-type: application/vnd.ms-word");
        header("Content-Disposition: attachment;Filename=export.doc");
        echo $html;
    }
}
?>
