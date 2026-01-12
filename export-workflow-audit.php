<?php
require_once './loginvalidate.php';
require_once './application/config/database.php';
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

$searchText = trim($_POST['userLog']);
if ($_SESSION['cdes_user_id'] == '1') {
    $where = "where user_id in($sameGroupIDs)";
} else {
    $where = "where user_id in($sameGroupIDs) and user_id!='1'";
}
if($searchText!=""){
    $where .= " and user_name='$searchText'";
}

if ((isset($_POST['sdate']) && !empty($_POST['sdate'])) && (isset($_POST['edate']) && !empty($_POST['edate']))) {
    $startdate = date('Y-m-d', strtotime($_POST['sdate']));
    $enddate = date('Y-m-d', strtotime($_POST['edate']));

    $where .= " and date(start_date) BETWEEN '" . xss_clean(trim($startdate)) . "' AND '" . xss_clean(trim($enddate)) . "'";
}

if (isset($_POST['action']) && !empty($_POST['action'])) {
    if ($_SESSION['cdes_user_id'] == '1') {
        $where .= " and action_name ='" . $_POST['action'] . "'";
    } else {
        $where .= "and action_name ='" . $_POST['action'] . "' and user_id!='1'";
    }
}

if (isset($_POST['exportUser'], $_POST['token'])) {

    $selectFormat = trim($_POST['select_Fm']);

    if ($selectFormat == "xlsx") {

        $exportData = mysqli_query($db_con, "select user_id,user_name,action_name,start_date,system_ip,remarks from tbl_ezeefile_logs_wf $where order by id desc");
        while ($fields = mysqli_fetch_field($exportData)) {
            if ($fields->name != 'user_id')
                if ($fields->name == 'user_name') {
                    $header1 .= 'User Name' . "\t";
                } else if ($fields->name == "action_name") {
                    $header1 .= 'Action Performed' . "\t";
                } else if ($fields->name == "start_date") {
                    $header1 .= 'Action Date Time' . "\t";
                } else if ($fields->name == "system_ip") {
                    $header1 .= 'System IP' . "\t";
                } else if ($fields->name == "remarks") {
                    $header1 .= 'Remarks' . "\t";
                } else {
                    $header1 .= $fields->name . "\t";
                }
        }
        while ($row = mysqli_fetch_assoc($exportData)) {
            $line = '';
            foreach ($row as $key => $value) {
                if ($key != 'user_id') {
                    if ((!isset($value) ) || ( $value == "" ) || ($value == " ") || ($value == NULL)) {
                        $value = "--\t";
                    } else {

                        if ($key == 'start_date') {
                            $value = str_replace('"', '""', $value);
                            $value = '"' . date('d-m-Y H:i', strtotime($value)) . '"' . "\t";
                        } else {
                            $value = str_replace('"', '""', $value);
                            $value = '"' . $value . '"' . "\t";
                        }
                    }
                    $line .= $value;
                }
            }
            $result1 .= trim($line) . "\n";
        }
        $result1 = str_replace("\r", "", $result1);

        if ($result1 == "") {
            //$result1 = "\nNo Record(s) Found!\n";                        
        }
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=Workflow-audit-history.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        print "$header1\n$result1";
        if ($result1) {
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`user_id`, `user_name`,`action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Storage Audit Exported','$date','$host','Storage audit history exported in xlsx.')") or die('error : ' . mysqli_error($db_con));
        }
    }
   else if ($selectFormat == "csv") {
       
    $exportData = mysqli_query($db_con, "select user_id,user_name,action_name,start_date,system_ip,remarks from tbl_ezeefile_logs_wf $where order by id desc");

    while ($fields = mysqli_fetch_field($exportData)) {
        if ($fields->name != 'user_id')
            if ($fields->name == 'user_name') {
                $header1 .= 'User Name' . ",";
            } else if ($fields->name == "action_name") {
                $header1 .= 'Action Performed' . ",";
            } else if ($fields->name == "start_date") {
                $header1 .= 'Action Date Time' . ",";
            } else if ($fields->name == "system_ip") {
                $header1 .= 'System IP' . ",";
            } else if ($fields->name == "remarks") {
                $header1 .= 'Remarks' . ",";
            } else {
                $header1 .= $fields->name . ",";
            }
    }
    while ($row = mysqli_fetch_assoc($exportData)) {
        $line = '';
        foreach ($row as $key => $value) {
            if ($key != 'user_id') {
                if ((!isset($value) ) || ( $value == "" ) || ($value == " ") || ($value == NULL)) {
                    $value = "-- ,";
                } else {

                    if ($key == 'start_date') {
                        $value = str_replace('"', '""', $value);
                        $value = '"' . date('d-m-Y H:i', strtotime($value)) . '"' . ",";
                    } else {
                        $value = str_replace('"', '""', $value);
                        $value = '"' . $value . '"' . ",";
                    }
                }
                $line .= $value;
            }
        }
        $result1 .= trim($line) . "\n";
    }
    $result1 = str_replace("\r", "", $result1);
    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=Workflow-audit-history.csv");
    header("Pragma: no-cache");
    header("Expires: 0");
    print "$header1\n$result1";
    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`user_id`, `user_name`,`action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Storage Audit Exported','$date','$host','Storage audit history exported in CSV.')") or die('error : ' . mysqli_error($db_con));
    } 
    elseif ($selectFormat == "pdf") {
        
        require('./wordwrap.php');

        $exportData = mysqli_query($db_con, "select user_name, action_name, start_date, remarks, system_ip from tbl_ezeefile_logs_wf $where order by start_date desc");

        $width = 0;
        $widthCell = array();
        $headers = array();
        $headers[] = 'Sr. No.';

        $width += 50;
        $headers[] = 'User Name';
        $widthCell[] = 50;
        $width += 50;
        $headers[] = "Action Performed";
        $widthCell[] = 50;
        $width += 50;
        $headers[] = "Action Date Time";
        $widthCell[] = 50;
        $width += 50;
        $headers[] = "Remarks";
        $widthCell[] = 50;
        $width += 50;
        $headers[] = "System IP";
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

            $data = array();
            $pdf->SetFont('Times', '', 11);
            $data[] = $i . '.';
            $data[] = $row['user_name'];
            $data[] = $row['action_name'];
            $data[] = date('d-m-Y H:i:s A', strtotime($row['start_date']));
            $data[] = $row['remarks'];
            $data[] = $row['system_ip'];

            $pdf->Row($data);
            $i++;
        }
        $pdf->Output();

        header("Content-Type: application/pdf charset=utf-8");
        header('Content-type: text/plain; charset=utf-8');
        header("Cache-Control: no-cache");
        header("Accept-Ranges: none");
        header("Content-Disposition: attachment; filename=\"useraudit.pdf\"");

        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`user_id`, `user_name`,`action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Workflow Logs Exported','$date','$host','Workflow logs exported in pdf.')") or die('error : ' . mysqli_error($db_con));
    
     } else if ($selectFormat == "word") {

        $exportData = mysqli_query($db_con, "select user_name, action_name, start_date, remarks, system_ip from tbl_ezeefile_logs_wf $where order by start_date desc");
        
        $html .= '<table width="100%" border="1" align="center"><thead><tr>';
        $html .= '<th>S.No.</th>';
        while ($fields = mysqli_fetch_field($exportData)) {

            
                if ($fields->name == 'user_name') {
                    $html .= '<th>User Name</th>';
                } else if ($fields->name == "action_name") {
                    $html .= '<th>Action Performed</th>';
                } else if ($fields->name == "start_date") {
                    $html .= '<th>Action Date Time</th>';
                } else if ($fields->name == "remarks") {
                    $html .= '<th>Remarks</th>';
                } else if ($fields->name == "system_ip") {
                    $html .= '<th>System IP</th>';
                }
        }
        $html .= '</tr></thead>';
        $i = 1;
        while ($row = mysqli_fetch_assoc($exportData)) {
            $html .= '<tbody><tr>';
            $html .= '<td>' . $i . '.' . '</td>';
            foreach ($row as $key => $value) {
                if ($key != 'user_id') {
                    if ((!isset($value) ) || ( $value == "" ) || ($value == " ") || ($value == NULL)) {
                        $html .= '<td>--</td>';
                    } else {
                        if ($key == 'start_date' || $key == 'end_date') {
                            $value = str_replace('"', '""', $value);
                            $html .= '<td>' . date('d-m-Y H:i', strtotime($value)) . '</td>';
                        } else {
                            $value = str_replace('"', '""', $value);
                            $html .= '<td>' . $value . '</td>';
                        }
                    }
                }
            }
            $html .= '</tr></tbody>';
            $i++;
        }
        $html .= '</table>';
        header("Content-type: application/vnd.ms-word");
        header("Content-Disposition: attachment;Filename=workflowaudit.doc");
        print "$html";
        
        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`user_id`, `user_name`,`action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Workflow Logs Exported','$date','$host','Workflow logs exported in Word.')") or die('error : ' . mysqli_error($db_con));
    }
}
?>