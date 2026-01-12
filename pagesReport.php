<?php

require_once './loginvalidate.php';

if (isset($_POST['export'])) {
    $selectFormat = trim($_POST['select_Fm']);
    $slIds = $_POST['both'];
    if ($selectFormat == "EXCEL") {
        $parent = mysqli_query($db_con, "SELECT sl_id,sl_name FROM `tbl_storage_level` WHERE sl_id in($slIds) order by sl_name asc");
        //$fields = mysqli_num_fields ( $exportData );
        $header1 = "sr. No.\t Folder Name  \t Number of Files\t Number of Pages \t\n";
        $i = 1;
        $totalfiles = 0;
        $totalPages = 0;
        while ($reportRow = mysqli_fetch_assoc($parent)) {

            $dparent = mysqli_query($db_con, "SELECT count(doc_id) as count, sum(noofpages) as numPage FROM `tbl_document_master` WHERE doc_name='" . $reportRow['sl_id'] . "' and flag_multidelete='1'");
            $ssparent = mysqli_fetch_assoc($dparent);
            $totalfiles += $ssparent['count'];
            $totalPages += $ssparent['numPage'];
            $totalfiles += $contdoc['files'];
            $totalPages += $contdoc['numPages'];
            $totalf = (empty($ssparent['count']) ? "0" : $ssparent['count']);
            $totalnumPage = (empty($ssparent['numPage']) ? "0" : $ssparent['numPage']);
            $line = '';
            $result1 .= $i . "\t" . $reportRow['sl_name'] . "\t" . $totalf . "\t" . $totalnumPage . "\t\n";
            $i++;
        }
        $result1 .= "Total Files \t" . $totalfiles . "\t Total Pages \t" . $totalPages . "\t\n";
        $result1 = str_replace("\r", "", $result1);

        if ($result1 == "") {
            //$result1 = "\nNo Record(s) Found!\n";                        
        }
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=export.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        print "$header1\n$result1";
    } elseif ($selectFormat == "CSV") {


        $reportQuery = mysqli_query($db_con, "SELECT sl_parent_id,sl_id,sl_name FROM `tbl_storage_level` WHERE sl_depth_level=2");
        //$fields = mysqli_num_fields ( $exportData );
        $header1 = "sr. No. , Storage Name  , Department , Number of Files , Number of Pages\n";
        $i = 1;
        $totalfiles = 0;
        $totalPages = 0;
        while ($reportRow = mysqli_fetch_assoc($reportQuery)) {
            $numFile = 0;
            $totalFSize = 0;
            $totalFolder = 0;
            $noofpages = 0;
            $storageName = mysqli_query($db_con, "SELECT sl_name FROM `tbl_storage_level` WHERE sl_id='$reportRow[sl_parent_id]'");
            $srgRow = mysqli_fetch_array($storageName);
            $sl_id = $reportRow['sl_id'];
            //$contdoc = findTotalFile($sl_id);
            $agr = mysqli_query($db_con, "select no_of_file as files, no_of_pages as numPages, file_size from tbl_agr_doc_upload where sl_id='$sl_id'");
            $agrRow = mysqli_fetch_array($agr);
            $totalfiles += $agrRow['files'];
            $totalPages += $agrRow['numPages'];
            $line = '';
            $result1 .= $i . "," . $srgRow['sl_name'] . "," . $reportRow['sl_name'] . "," . $agrRow['files'] . "," . $agrRow['numPages'] . "\n";
            $i++;
        }
        $result1 .= ", Total Files ," . $totalfiles . ", Total Pages ," . $totalPages . "\n";
        $result1 = str_replace("\r", "", $result1);

        if ($result1 == "") {
            //$result1 = "\nNo Record(s) Found!\n";                        
        }
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=export.csv");
        header("Pragma: no-cache");
        header("Expires: 0");
        print "$header1\n$result1";
    } elseif ($selectFormat == "PDF") {
        if ($_POST['both']) {
            $sl_id = implode(',', $_POST['both']);
            $departMentName = 'Corporate Office & RBG / SBG /ZONE';
        } elseif ($_POST['corporate']) {
            $sl_id = $_POST['corporate'];
            $departMentName = 'Corporate Office';
        } elseif ($_POST['rbgzone']) {
            $sl_id = $_POST['rbgzone'];
            $departMentName = 'RBG / SBG /ZONE';
        }
        $today = date("d-m-Y");
        $text = "Digitized  Record as on $today";
        require_once('./wordwrap.php');

        $reportQuery = mysqli_query($db_con, "SELECT sl_id,sl_name FROM `tbl_storage_level` WHERE sl_depth_level=1 AND sl_id in($sl_id) order by sl_name asc");
        $width = 0;
        $widthCell = array();
        $headers = array();

        $width += 15;
        $headers[] = 'S.No.';
        $widthCell[] = 15;

        $width += 50;
        $headers[] = 'Department Name';
        $widthCell[] = 50;
        $width += 50;
        $headers[] = 'No. of Files';
        $widthCell[] = 50;
        $width += 44.2;
        $headers[] = 'No. of Pages';
        $widthCell[] = 44.2;
        $pdf = new PDF_MC_Table('P', 'mm', array(297, 210));
        $pdf->setDept($departMentName);
        $pdf->setText($text);
        $pdf->SetMargins(25.4, 10, 25.4);
        $pdf->SetAutoPageBreak(TRUE, 12.7);
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetWidths($widthCell);
        $pdf->Row($headers);

        $i = 1;
        $totalfiles = 0;
        $totalPages = 0;
        while ($row = mysqli_fetch_assoc($reportQuery)) {

            $storageName = mysqli_query($db_con, "SELECT sl_name,sl_id FROM `tbl_storage_level` WHERE sl_parent_id='$row[sl_id]' order by sl_name asc");
            while ($srgRow = mysqli_fetch_array($storageName)) {
                $sl_id = $srgRow['sl_id'];

                $numFile = 0;
                $totalFSize = 0;
                $totalFolder = 0;
                $noofpages = 0;
                $contdoc = findTotalFile($sl_id);
                $totalfiles += $contdoc['files'];
                $totalPages += $contdoc['numPages'];
                if ($contdoc['files'] > 0) {
                    $files = $contdoc['files'];
                } else {
                    $files = 0;
                }
                if ($contdoc['numPages'] > 0) {
                    $numPages = $contdoc['numPages'];
                } else {
                    $numPages = 0;
                }
                $data = array();
                $pdf->SetFont('Arial', '', 9);
                $data[] = $i;

                $data[] = $srgRow['sl_name'];
                $data[] = $files;
                $data[] = $numPages;





                $pdf->Row($data);
                $i++;
            }
        }
        $pdf->SetFont('Arial', 'B', 9);
        $data = array();
        $data[] = '';
        $data[] = 'Total';
        $data[] = $totalfiles;
        $data[] = $totalPages;

        $pdf->Row($data);

        $pdf->Output();
        header("Content-Type: application/pdf");
        header("Cache-Control: no-cache");
        header("Accept-Ranges: none");
        header("Content-Disposition: attachment; filename=\"export.pdf\"");
    }
}
?>