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
    $where = "where action_name!='Login/Logout' and user_id in($sameGroupIDs)";
} else {
    $where = "where action_name!='Login/Logout' and user_id in($sameGroupIDs) and user_id!='1'";
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
     //echo "select user_id,user_name,action_name,start_date,system_ip,remarks from tbl_ezeefile_logs $where order by id desc"; die;
        $exportData = mysqli_query($db_con, "select user_id,user_name,action_name,start_date,system_ip,remarks from tbl_ezeefile_logs $where order by id desc");
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
        header("Content-Disposition: attachment; filename=Storage-audit-history.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        print "$header1\n$result1";
        if ($result1) {
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Storage Audit Exported','$date','$host','Storage audit history exported in xlsx.')") or die('error : ' . mysqli_error($db_con));
        }
    
    }
   else if ($selectFormat == "csv") {
       
    $exportData = mysqli_query($db_con, "select user_id,user_name,action_name,start_date,system_ip,remarks from tbl_ezeefile_logs $where order by id desc");

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
    header("Content-Disposition: attachment; filename=Storage-audit-history.csv");
    header("Pragma: no-cache");
    header("Expires: 0");
    print "$header1\n$result1";
    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Storage Audit Exported','$date','$host','Storage audit history exported in CSV.')") or die('error : ' . mysqli_error($db_con));
    } 
    elseif ($selectFormat == "pdf") {
        require('./wordwrap.php');
        $exportData = mysqli_query($db_con, "select user_id,user_name,action_name,start_date,system_ip,remarks from tbl_ezeefile_logs $where order by id desc");
        $width = 0;
        $widthCell = array();

        $headers = array();
        $width += 15;
        $headers[] = 'S.No.';
        $widthCell[] = 15;
        while ($fields = mysqli_fetch_field($exportData)) {
            $width += 50;
            if ($fields->name == 'user_name') {
                $headers[] .= 'User Name';
            } else if ($fields->name == "action_name") {
                $headers[] .= 'Action Performed';
            } else if ($fields->name == "start_date") {
                $headers[] .= 'Action Date Time';
            } else if ($fields->name == "system_ip") {
                $headers[] .= 'System IP';
            } else if ($fields->name == "remarks") {
                $headers[] .= 'Remarks';
            }
            $widthCell[] = 50;
        }

        $pdf = new PDF_MC_Table('L', 'mm', array($width + 6, $width));
        $pdf->SetMargins(25.4, 10, 25.4);
        $pdf->SetAutoPageBreak(TRUE, 12.7);
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetWidths($widthCell);
        $pdf->Row($headers);
        $i = 1;
        while ($row = mysqli_fetch_assoc($exportData)) {
            //print_r($row);
            $data = array();
            $pdf->SetFont('Arial', '', 8);
            $data[] = $i . '.';
            $data[] = $row['user_name'];
            $data[] = $row['action_name'];
            $data[] = date('d-m-Y H:i', strtotime($row['start_date']));
            $data[] = $row['system_ip'];
            $data[] = $row['remarks'];
            $pdf->Row($data);
            $i++;
        }
        $pdf->Output();
        header("Content-Type: application/pdf");
        header("Cache-Control: no-cache");
        header("Accept-Ranges: none");
        header("Content-Disposition: attachment; filename=\"Storage-audit-history.pdf\"");
        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Storage Audit Exported','$date','$host','Storage audit history exported in PDF.')") or die('error : ' . mysqli_error($db_con));
     } else if ($selectFormat == "word") {
        $exportData = mysqli_query($db_con, "select user_id,user_name,action_name,start_date,system_ip,remarks from tbl_ezeefile_logs $where order by id desc");
        $html .= '<table width="100%" border="1" align="center"><thead><tr>';
        $html .= '<th>S.No.</th>';
        while ($fields = mysqli_fetch_field($exportData)) {

            if ($fields->name != 'user_id')
                if ($fields->name == 'user_name') {
                    $html .= '<th>User Name</th>';
                } else if ($fields->name == "action_name") {
                    $html .= '<th>Action Performed</th>';
                } else if ($fields->name == "start_date") {
                    $html .= '<th>Action Date Time</th>';
                } else if ($fields->name == "system_ip") {
                    $html .= '<th>System IP</th>';
                } else if ($fields->name == "remarks") {
                    $html .= '<th>Remarks</th>';
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
                            if(!empty($value)){
                            $html .= '<td>' . date('d-m-Y H:i', strtotime($value)) . '</td>';
                            }else{
                              $html .= '<td>--</td>';   
                            }
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
        header("Content-Disposition: attachment;Filename=Storage-audit-history.doc");
        print "$html";
        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Storage Audit Exported','$date','$host','Storage audit history exported in Word.')") or die('error : ' . mysqli_error($db_con));
    }
}
?>