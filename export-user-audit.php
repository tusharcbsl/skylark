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
$where = "";
if($searchText!=""){
    $where .= " and user_name='$searchText'";
}

if ((isset($_POST['sdate']) && !empty($_POST['sdate'])) && (isset($_POST['edate']) && !empty($_POST['edate']))) {
    $startdate = date('Y-m-d', strtotime($_POST['sdate']));
    $enddate = date('Y-m-d', strtotime($_POST['edate']));

    $where .= " and date(start_date) BETWEEN '" . xss_clean(trim($startdate)) . "' AND '" . xss_clean(trim($enddate)) . "'";
}

if (isset($_POST['exportUser'], $_POST['token'])) {

    $selectFormat = trim($_POST['select_Fm']);

    if ($selectFormat == "xlsx") {

        $exportData = mysqli_query($db_con, "select user_name, action_name, start_date, end_date, system_ip from tbl_ezeefile_logs where user_id in($sameGroupIDs) and action_name='Login/Logout'  $where order by start_date desc");

        
        $header1 .= 'User Name, Action Performed, Action Start Date, Action End Date, System IP';

        while ($row = mysqli_fetch_assoc($exportData)) {

            $line = '';
            foreach ($row as $key => $value) {
                if ((!isset($value) ) || ( $value == "" ) || ($value == " ") || ($value == NULL)) {
                    $value = "--";
                }

                        $line .= $value . ',';
                    
                }

            $result1 .= trim($line) . "\n";
        }


        //$result1 = str_replace("\r", "", $result1);
        header("Content-Type: application/xls");
        header("Content-Disposition: attachment; filename=useraudit.xlsx");
        header("Pragma: no-cache");
        header("Expires: 0");
        echo "\xEF\xBB\xBF";
        print "$header1\n$result1";

        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Login/Logout History Exported','$date','$host','User login/logout history exported in Excel.')") or die('error : ' . mysqli_error($db_con));
    }
   else if ($selectFormat == "csv") {
       
        $exportData = mysqli_query($db_con, "select user_name, action_name, start_date, end_date, system_ip from tbl_ezeefile_logs where user_id in($sameGroupIDs) and action_name='Login/Logout'  $where order by start_date desc");

        
        $header1 .= 'User Name, Action Performed, Action Start Date, Action End Date, System IP';

        while ($row = mysqli_fetch_assoc($exportData)) {

            $line = '';
            foreach ($row as $key => $value) {
                if ((!isset($value) ) || ( $value == "" ) || ($value == " ") || ($value == NULL)) {
                    $value = "--";
                }

                        $line .= $value . ',';
                    
                }

            $result1 .= trim($line) . "\n";
        }


        //$result1 = str_replace("\r", "", $result1);
        header("Content-Type: application/xls");
        header("Content-Disposition: attachment; filename=useraudit.csv");
        header("Pragma: no-cache");
        header("Expires: 0");
        echo "\xEF\xBB\xBF";
        print "$header1\n$result1";

        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Login/Logout History Exported','$date','$host','User login/logout history exported in Csv.')") or die('error : ' . mysqli_error($db_con));
    } 
    elseif ($selectFormat == "pdf") {
        require('./wordwrap.php');

        $exportData = mysqli_query($db_con, "select user_name, action_name, start_date, end_date, system_ip from tbl_ezeefile_logs where user_id in($sameGroupIDs) and action_name='Login/Logout'  $where order by start_date desc");

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
        $headers[] = "Action Start Date";
        $widthCell[] = 50;
        $width += 50;
        $headers[] = "Action End Date";
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
            $data[] = $row['start_date'];
            $data[] = $row['end_date'];
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

        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Login/Logout History Exported','$date','$host','User login/logout history exported in Pdf.')") or die('error : ' . mysqli_error($db_con));

    } else if ($selectFormat == "word") {

        $exportData = mysqli_query($db_con, "select user_name, action_name, start_date, end_date, system_ip from tbl_ezeefile_logs where user_id in($sameGroupIDs) and action_name='Login/Logout'  $where order by start_date desc");

        $html .= '<table width="100%" border="1" align="center"><thead><tr>';
        $html .= '<th>S.No.</th>';
        while ($fields = mysqli_fetch_field($exportData)) {

                if ($fields->name == 'user_name') {
                    $html .= '<th>User Name</th>';
                } else if ($fields->name == "action_name") {
                    $html .= '<th>Action Performed</th>';
                } else if ($fields->name == "start_date") {
                    $html .= '<th>Action Start Date</th>';
                } else if ($fields->name == "end_date") {
                    $html .= '<th>Action End Date</th>';
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
            $html .= '</tr></tbody>';
            $i++;
        }
        $html .= '</table>';
        header("Content-type: application/vnd.ms-word");
        header("Content-Disposition: attachment;Filename=useraudit.doc");
        print "$html";
         $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Login/Logout History Exported','$date','$host','User login/logout history exported in Word.')") or die('error : ' . mysqli_error($db_con));
    }

}