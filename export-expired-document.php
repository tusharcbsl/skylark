<?php
require_once './loginvalidate.php';
require_once './application/config/database.php';

mysqli_set_charset($db_con, "utf8");

$slids = $_POST['slids'];

$where = "WHERE flag_multidelete='2' and doc_name in($slids)";

if (isset($_POST['expdoc']) && !empty($_POST['expdoc'])) {

    $deleteFile = trim($_POST['expdoc']);
    $deleteFile = xss_clean($deleteFile);
    $deleteFile  =  mysqli_real_escape_string($db_con, $deleteFile);
    
    $where .= "and old_doc_name like '%$deleteFile%'";
}

if (isset($_POST['exportUser'], $_POST['token'])) {

    $selectFormat = trim($_POST['select_Fm']);

    if ($selectFormat == "xlsx") {

        $exportData = mysqli_query($db_con, "SELECT old_doc_name, doc_expiry_period, doc_extn, doc_name, doc_size FROM `tbl_document_master` $where order by old_doc_name") or die('Error:' . mysqli_error($db_con));

        $header1 = 'Sr. No., File name, Expired Date, File Type, Name of Storage, File Size';
        $i=1;
        while ($row = mysqli_fetch_assoc($exportData)) {

            $line = $i.',';
             foreach ($row as $key => $value) {

                if ((!isset($value) ) || ( $value == "" ) || ($value == " ") || ($value == NULL)) {

                    $line .= '--,';

                 }else if($key=='doc_name'){


                    $getSlName = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id = '$value'") or die('Error in get name' . mysqli_error($db_con));
                    $rwgetSlName = mysqli_fetch_assoc($getSlName);

                    $line .= $rwgetSlName['sl_name'] . ',';

                }else if($key=='doc_size'){
                   $line .=  round($value / (1000 * 1000), 2).' MB,';
                }else{
                    $line .= $value . ',';
                }
                   
                    
            }

            $result1 .= trim($line) . "\n";

            $i++;
        }


 
        //$result1 = str_replace("\r", "", $result1);
        header("Content-Type: application/xls");
        header("Content-Disposition: attachment; filename=expdoc.xlsx");
        header("Pragma: no-cache");
        header("Expires: 0");
       // echo "\xEF\xBB\xBF";
        print "$header1\n$result1";

        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Exported Expired Document List','$date','$host','Exported Expired Document List.')") or die('error : ' . mysqli_error($db_con));
    }
   else if ($selectFormat == "csv") {
       
        $exportData = mysqli_query($db_con, "SELECT old_doc_name, doc_expiry_period, doc_extn, doc_name, doc_size FROM `tbl_document_master` $where order by old_doc_name") or die('Error:' . mysqli_error($db_con));

        $header1 = 'Sr. No., File name, Expired Date, File Type, Name of Storage, File Size';
        $i=1;
       while ($row = mysqli_fetch_assoc($exportData)) {

            $line = $i.',';

            foreach ($row as $key => $value) {

                if ((!isset($value) ) || ( $value == "" ) || ($value == " ") || ($value == NULL)) {

                    $line .= '--,';

                 }else if($key=='doc_name'){


                    $getSlName = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id = '$value'") or die('Error in get name' . mysqli_error($db_con));
                    $rwgetSlName = mysqli_fetch_assoc($getSlName);

                    $line .= $rwgetSlName['sl_name'] . ',';

                }else if($key=='doc_size'){
                   $line .=  round($value / (1000 * 1000), 2).' MB,';
                }else{
                    $line .= $value . ',';
                }
                   
                    
            }

            $result1 .= trim($line) . "\n";

            $i++;
        }


        //$result1 = str_replace("\r", "", $result1);
        header("Content-Type: application/xls");
        header("Content-Disposition: attachment; filename=expdoc.csv");
        header("Pragma: no-cache");
        header("Expires: 0");
        echo "\xEF\xBB\xBF";
        print "$header1\n$result1";

        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Exported Expired Document List','$date','$host','Exported Expired Document List.')") or die('error : ' . mysqli_error($db_con));
    } 
    elseif ($selectFormat == "pdf") {
        require('./wordwrap.php');

        $exportData = mysqli_query($db_con, "SELECT old_doc_name, doc_expiry_period, doc_extn, doc_name, doc_size FROM `tbl_document_master` $where order by old_doc_name") or die('Error:' . mysqli_error($db_con));

        $width = 0;
        $widthCell = array();
        $headers = array();
        $headers[] = 'Sr. No.';

        $width += 50;
        $headers[] = 'File Name';
        $widthCell[] = 50;
        $width += 50;
        $headers[] = "Expired Date";
        $widthCell[] = 50;
        $width += 50;
        $headers[] = "File Type";
        $widthCell[] = 50;
        $width += 50;
        $headers[] = "Name of Storage";
        $widthCell[] = 50;
        $width += 50;
        $headers[] = "File Size";
        $widthCell[] = 50;
        // while ($fields = mysqli_fetch_field($exptUsr)) {
        //     if ($fields->name != 'user_id') {
        //         $width += 50;
        //         $headers[] = $fields->name;
        //         $widthCell[] = 50;
        //     }
        // }

        $width += 50;
        $width += 50;
        
        $widthCell[] = 50;
        $widthCell[] = 50;
        $pdf = new PDF_MC_Table('L', 'mm', array($width + 5, $width));
        $pdf->SetMargins(3.5, 3.5, 5.5);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->AddPage();
        $pdf->SetFont('Times', 'B');

        $pdf->SetWidths($widthCell);
        $pdf->Row($headers);
        $i = 1;
        while ($row = mysqli_fetch_assoc($exportData)) {

            $getSlName = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id = '$row[doc_name]'") or die('Error in get name' . mysqli_error($db_con));
            $rwgetSlName = mysqli_fetch_assoc($getSlName);

            $data = array();
            $pdf->SetFont('Times', '', 11);
            $data[] = $i . '.';
            $data[] = $row['old_doc_name'];
            $data[] = $row['doc_expiry_period'];
            $data[] = $row['doc_extn'];
            $data[] = $rwgetSlName['sl_name'];
            $data[] = round($row['doc_size'] / (1000 * 1000), 2).' MB';

            $pdf->Row($data);
            $i++;
        }
        $pdf->Output();

        header("Content-Type: application/pdf charset=utf-8");
        header('Content-type: text/plain; charset=utf-8');
        header("Cache-Control: no-cache");
        header("Accept-Ranges: none");
        header("Content-Disposition: attachment; filename=\"expdoc.pdf\"");

       $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Exported Expired Document List','$date','$host','Exported Expired Document List.')") or die('error : ' . mysqli_error($db_con));

    } else if ($selectFormat == "word") {

         $exportData = mysqli_query($db_con, "SELECT old_doc_name, doc_expiry_period, doc_extn, doc_name, doc_size FROM `tbl_document_master` $where order by old_doc_name") or die('Error:' . mysqli_error($db_con));

        $html .= '<table width="100%" border="1" align="center"><thead><tr>';
        $html .= '<th>Sr No.</th>';
        $html .= '<th>File Name</th>';
        $html .= '<th>Expired Date</th>';
        $html .= '<th>File Type</th>';
        $html .= '<th>Name of Storage</th>';
        $html .= '<th>File Size</th>';
        $html .= '</tr></thead>';
        $i = 1;
        while ($row = mysqli_fetch_assoc($exportData)) {
            $html .= '<tbody><tr>';
            $html .= '<td>' . $i . '.' . '</td>';
            foreach ($row as $key => $value) {
               
                    if ((!isset($value) ) || ( $value == "" ) || ($value == " ") || ($value == NULL)) {

                        $html .= '<td>--</td>';

                    }else if($key=='doc_name'){


                        $getSlName = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id = '$value'") or die('Error in get name' . mysqli_error($db_con));
                        $rwgetSlName = mysqli_fetch_assoc($getSlName);

                        $html .= '<td>' . $rwgetSlName['sl_name'] . '</td>';

                    }else if($key=='doc_size'){
                        $html .= 'td'. round($row['doc_size'] / (1000 * 1000), 2).' MB</td>';
                    }else{
 
                        $value = str_replace('"', '""', $value);

                        $html .= '<td>' . $value . '</td>';
                    }
                
            }
            $html .= '</tr></tbody>';
            $i++;
        }
        $html .= '</table>';
        header("Content-type: application/vnd.ms-word");
        header("Content-Disposition: attachment;Filename=expdoc.doc");
        print "$html";
         $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Exported Expired Document List','$date','$host','Exported Expired Document List.')") or die('error : ' . mysqli_error($db_con));
    }

}