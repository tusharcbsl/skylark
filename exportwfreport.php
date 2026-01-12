<?php

require_once('./sessionstart.php');
require_once'./application/config/database.php';
mysqli_set_charset($db_con, "utf8");
//if ($_REQUEST['format'] == "xls") {
$rid = $_REQUEST['rid'];
$wfid = $_REQUEST['wfid'];
$where = '';
if (isset($_REQUEST['startDate']) && !empty($_REQUEST['startDate']) && isset($_REQUEST['endDate']) && !empty($_REQUEST['endDate'])) {
    if (empty($where)) {
        $where = "where (start_date BETWEEN '" . date('Y-m-d', strtotime($_REQUEST['startDate'])) . "' AND '" . date('Y-m-d', strtotime($_REQUEST['endDate']) + 24 * 60 * 60) . "')";
    }
}

$wftblqry = mysqli_query($db_con, "select * from  tbl_workflow_master where workflow_id='$wfid'");
$rows = mysqli_fetch_assoc($wftblqry);
$qryform = mysqli_query($db_con, "select * from tbl_bridge_workflow_to_form where workflow_id='$wfid'");
$res = mysqli_fetch_assoc($qryform);
$formid = $res["form_id"];
$wftblqry = mysqli_query($db_con, "select * from  tbl_workflow_master where workflow_id='$wfid'") or die(mysqli_error($db_con));
if (mysqli_num_rows($wftblqry) > 0) {
    $dataContent = mysqli_fetch_assoc($wftblqry);
    $tblname = $dataContent['form_tbl_name'];
    $qry = mysqli_query($db_con, "select * from tbl_wf_reports where rp_id='$rid' and wf_id='$wfid'")or die(mysqli_error($db_con));
    $rowdata = mysqli_fetch_assoc($qry);
    $recol = $rowdata['coloums'];
    $recol = explode(",", $recol);
    $coloums = $rowdata['coloums'];
    $newcoloums = $coloums . "," . "tbl_id";

    if (in_array('wf_devision', $recol)) {

        $cashVoucher = true;
    }

    if (!empty($coloums)) {
        if (mysqli_num_rows($qry) > 0) {
            if (!empty($_REQUEST['colname']) && !empty($_REQUEST['search'])) {
                $where = " where";
                for ($k = 0; $k < count($_REQUEST['colname']); $k++) {

                    if ($k == 0) {
                        if ($_REQUEST['colname'][$k] == "wf_devision") {
                            $where .= " d.division_name LIKE " . "'%" . $_REQUEST['search'][$k] . "%'";
                        } else if ($_REQUEST['colname'][$k] == "wf_project") {
                            $where .= " p.project_name LIKE " . "'%" . $_REQUEST['search'][$k] . "%'";
                        } else if ($_GET['colname'][$k] == "action_by") {
                            $where .= " um.first_name LIKE " . "'%" . $_GET['search'][$k] . "%'";
                        } else if ($_GET['colname'][$k] == "first_name") {
                            $where .= " um.first_name LIKE " . "'%" . $_GET['search'][$k] . "%' or tbl_user_master.first_name LIKE" . "'%" . $_GET['search'][$k] . "%'";
                        } else {
                            $where .= " " . $_REQUEST['colname'][$k] . " LIKE " . "'%" . $_REQUEST['search'][$k] . "%'";
                        }
                    } else {
                        if ($_REQUEST['colname'][$k] == "wf_devision") {
                            $where .= " AND d.division_name LIKE " . "'%" . $_REQUEST['search'][$k] . "%'";
                        } else if ($_REQUEST['colname'][$k] == "wf_project") {
                            $where .= " AND p.project_name LIKE " . "'%" . $_REQUEST['search'][$k] . "%'";
                        } else if ($_GET['colname'][$k] == "action_by") {
                            $where .= " AND um.first_name LIKE " . "'%" . $_GET['search'][$k] . "%'";
                        } else if ($_GET['colname'][$k] == "first_name") {
                            $where .= " um.first_name LIKE " . "'%" . $_GET['search'][$k] . "%' or tbl_user_master.first_name LIKE" . "'%" . $_GET['search'][$k] . "%'";
                        } else {
                            $where .= " AND " . $_REQUEST['colname'][$k] . " LIKE " . "'%" . $_REQUEST['search'][$k] . "%'";
                        }
                    }
                }
                if ($cashVoucher) {
                    $allot = "SELECT " . $newcoloums . ", d.division_name, p.project_name  FROM " . $tblname . " INNER JOIN tbl_doc_assigned_wf ON " . $tblname . ".ticket_id = tbl_doc_assigned_wf.ticket_id INNER JOIN tbl_division as d on " . $tblname . ".wf_devision=d.Id INNER JOIN tbl_project as p on " . $tblname . ".wf_project=p.Id $where";
                } else {

                    $allot = "SELECT " . $newcoloums . " FROM " . $tblname . " INNER JOIN tbl_doc_assigned_wf ON " . $tblname . ".ticket_id = tbl_doc_assigned_wf.ticket_id INNER JOIN  tbl_user_master on tbl_user_master.user_id=tbl_doc_assigned_wf.assign_by left join tbl_user_master as um on tbl_doc_assigned_wf.action_by=um.user_id $where";
                }
            } else {
                if ($cashVoucher) {
                    $allot = "SELECT " . $newcoloums . ", d.division_name,p.project_name  FROM " . $tblname . " INNER JOIN tbl_doc_assigned_wf ON " . $tblname . ".ticket_id = tbl_doc_assigned_wf.ticket_id INNER JOIN tbl_division as d on " . $tblname . ".wf_devision=d.Id INNER JOIN tbl_project as p on " . $tblname . ".wf_project=p.Id";
                } else {

                    $allot = "SELECT " . $newcoloums . " FROM " . $tblname . " INNER JOIN tbl_doc_assigned_wf ON " . $tblname . ".ticket_id = tbl_doc_assigned_wf.ticket_id INNER JOIN  tbl_user_master on tbl_user_master.user_id=tbl_doc_assigned_wf.assign_by left join tbl_user_master as um on tbl_doc_assigned_wf.action_by=um.user_id";
                }
            }
            $coloums = explode(",", $coloums);
            $coloums = implode("','", $coloums);
            $allote_query = mysqli_query($db_con, $allot) or die("ERROR1222:" . mysqli_error($db_con));
            $foundnum = mysqli_num_rows($allote_query);
            if ($foundnum > 0) {
                if (is_numeric($_REQUEST['limit'])) {
                    $per_page = $_REQUEST['limit'];
                } else {
                    $per_page = 10;
                }
                $start = isset($_POST['start']) ? $_POST['start'] : '';
                $max_pages = ceil($foundnum / $per_page);
                if (!$start) {
                    $start = 0;
                }
                if (!empty($_REQUEST['colname']) && !empty($_REQUEST['search'])) {

                    if ($cashVoucher) {
                        $allote = "SELECT " . $newcoloums . ", d.division_name, p.project_name FROM " . $tblname . " INNER JOIN tbl_doc_assigned_wf ON " . $tblname . ".ticket_id = tbl_doc_assigned_wf.ticket_id INNER JOIN tbl_division as d on " . $tblname . ".wf_devision=d.Id INNER JOIN tbl_project as p on " . $tblname . ".wf_project=p.Id $where";
                    } else {
                        $allote = "SELECT " . $newcoloums . " FROM " . $tblname . " INNER JOIN tbl_doc_assigned_wf ON " . $tblname . ".ticket_id = tbl_doc_assigned_wf.ticket_id INNER JOIN  tbl_user_master on tbl_user_master.user_id=tbl_doc_assigned_wf.assign_by left join tbl_user_master as um on tbl_doc_assigned_wf.action_by=um.user_id $where";
                    }

                    $allote_query = mysqli_query($db_con, $allote) or die("ERROR1:" . mysqli_error($db_con));
                } else {
                    if ($cashVoucher) {
                        $allote = "SELECT " . $newcoloums . ", d.division_name,p.project_name  FROM " . $tblname . " INNER JOIN tbl_doc_assigned_wf ON " . $tblname . ".ticket_id = tbl_doc_assigned_wf.ticket_id INNER JOIN tbl_division as d on " . $tblname . ".wf_devision=d.Id INNER JOIN tbl_project as p on " . $tblname . ".wf_project=p.Id ";
                    } else {

                        $allote = "SELECT " . $newcoloums . " FROM " . $tblname . " INNER JOIN tbl_doc_assigned_wf ON " . $tblname . ".ticket_id = tbl_doc_assigned_wf.ticket_id INNER JOIN  tbl_user_master on tbl_user_master.user_id=tbl_doc_assigned_wf.assign_by left join tbl_user_master as um on tbl_doc_assigned_wf.action_by=um.user_id";
                    }
                    $allote_query = mysqli_query($db_con, $allote) or die("ERROR3:" . mysqli_error($db_con));
                }
                $xlsoutput = '';
                $xlsoutput .= '<table class="table" bordered="1">
                                   <thead>
                                    <tr>';
                $xlsoutput .= '<th>SNO</th>';
                $dFormColoums = array();
                $labelnameqry = mysqli_query($db_con, "SELECT label,name FROM tbl_form_attribute WHERE name in('$coloums')  and dependency_id IS NUll and  fid='$formid'") or die("Label Error:" . mysqli_error($db_con));
                while ($rowdataFetch = mysqli_fetch_assoc($labelnameqry)) {
                    if ($rowdataFetch['name'] == 'wf_devision') {
                        array_push($dFormColoums, 'division_name');
                    } else if ($rowdataFetch['name'] == 'wf_project') {
                        array_push($dFormColoums, 'project_name');
                    } else {
                        array_push($dFormColoums, $rowdataFetch['name']);
                    }
                    $xlsoutput .= '<th>' . $rowdataFetch[label] . '</th>';
                }

                if (in_array("task_status", $recol)) {
                    $xlsoutput .= '<th>Task Status</th>';
                }
                if (in_array("action_by", $recol)) {
                    $xlsoutput .= '<th>Approved By</th>';
                }
                if (in_array("action_time", $recol)) {
                    $xlsoutput .= '<th>Action  Date</th>';
                }
                if (in_array("assign_by", $recol)) {
                    $xlsoutput .= '<th> First Name</th>';
                }
                if (in_array("start_date", $recol)) {
                    $xlsoutput .= '<th> Submitted Date</th>';
                }
                if (in_array("emp_id", $recol)) {
                    $xlsoutput .= '<th>Employee ID</th>';
                }
                $xlsoutput .= '</tr>'
                        . '</thead>'
                        . '<tbody>';
                $n = $start + 1;
                while ($allot_row = mysqli_fetch_assoc($allote_query)) {

                    $xlsoutput .= '<tr>'
                            . '<td>' . $n . '</td>';

                    foreach ($dFormColoums as $key => $value) {
                        if ($allot_row[$value] == "CO(Compensatory off)") {
                            $tblid = $allot_row['tbl_id'];
                            $tblid = mysqli_escape_string($db_con, $tblid);
                            $tblname = mysqli_escape_string($db_con, $tblname);
                            $qry = mysqli_query($db_con, "select * from " . $tblname . " where tbl_id='$tblid'") or die(mysqli_error($db_con));
                            $fetch = mysqli_fetch_assoc($qry);
                            $xlsoutput .= '<td>' . $allot_row[$value] . "-" . $fetch['co'] . '</td>';
                        } else {
                            $xlsoutput .= '<td>' . $allot_row[$value] . '</td>';
                        }
                    }

                    if (in_array("task_status", $recol)) {
                        $xlsoutput .= '<td>' . $allot_row['task_status'] . '</td>';
                    }
                    if (in_array("action_by", $recol)) {

                        if (!empty($allot_row['action_by'])) {
                            $user = mysqli_query($db_con, "select first_name,last_name from tbl_user_master where user_id='$allot_row[action_by]'");
                            $rwUser = mysqli_fetch_assoc($user);
                            $xlsoutput .= '<td>' . $rwUser['first_name'] . ' ' . $rwUser['last_name'] . '</td>';
                        } else {
                            $xlsoutput .= '<td>' . 'No Action Performed' . '</td>';
                        }
                        $xlsoutput .= '</td>';
                    }
                    if (in_array("action_time", $recol)) {
                        $xlsoutput .= '<td>' . $allot_row['action_time'] . '</td>';
                    }
                    if (in_array("assign_by", $recol)) {
                        $user = mysqli_query($db_con, "select first_name,last_name from tbl_user_master where user_id='$allot_row[assign_by]'");
                        $rwUser = mysqli_fetch_assoc($user);
                        $xlsoutput .= '<td>' . $rwUser['first_name'] . ' ' . $rwUser['last_name'] . '</td>';
                    }
                    if (in_array("start_date", $recol)) {
                        $xlsoutput .= '<td>' . $allot_row['start_date'] . '</td>';
                    }
                    if (in_array("emp_id", $recol)) {
                        $xlsoutput .= '<td>' . $allot_row['emp_id'] . '</td>';
                    }
                    $xlsoutput .= '</tr>';
                    $n++;
                }
                $xlsoutput .= '</tbody>'
                        . ' </table>';
            }
        } else {
            echo 'No Records Found';
        }
    }
}


$fileName = "report_export_data_" . date('Y-m-d') . ".xls";
header("Content-Disposition: attachment; filename=\"$fileName\"");
echo $xlsoutput;

//} else if ($_REQUEST['format'] == "csv") {
//    $rid = $_REQUEST['rid'];
//    $wfid = $_REQUEST['wfid'];
//    $where = '';
//    if (isset($_REQUEST['startDate']) && !empty($_REQUEST['startDate']) && isset($_REQUEST['endDate']) && !empty($_REQUEST['endDate'])) {
//        if (empty($where)) {
//            $where = "where (start_date BETWEEN '" . date('Y-m-d', strtotime($_REQUEST['startDate'])) . "' AND '" . date('Y-m-d', strtotime($_REQUEST['endDate']) + 24 * 60 * 60) . "')";
//        }
//    }
//    $wftblqry = mysqli_query($db_con, "select * from  tbl_workflow_master where workflow_id='$wfid'");
//    $rows = mysqli_fetch_assoc($wftblqry);
//    $qryform = mysqli_query($db_con, "select * from tbl_bridge_workflow_to_form where workflow_id='$wfid'");
//    $res = mysqli_fetch_assoc($qryform);
//    $formid = $res["form_id"];
//    $wftblqry = mysqli_query($db_con, "select * from  tbl_workflow_master where workflow_id='$wfid'") or die(mysqli_error($db_con));
//    if (mysqli_num_rows($wftblqry) > 0) {
//        $dataContent = mysqli_fetch_assoc($wftblqry);
//        $tblname = $dataContent['form_tbl_name'];
//        $qry = mysqli_query($db_con, "select * from tbl_wf_reports where rp_id='$rid' and wf_id='$wfid'")or die(mysqli_error($db_con));
//        $rowdata = mysqli_fetch_assoc($qry);
//        $recol = $rowdata['coloums'];
//        $recol = explode(",", $recol);
//        $coloums = $rowdata['coloums'];
//        $newcoloums = $coloums . "," . "tbl_id";
//        if (!empty($coloums)) {
//            if (mysqli_num_rows($qry) > 0) {
//                if (!empty($_REQUEST['colname']) && !empty($_REQUEST['search'])) {
//                    
//                    for ($k = 0; $k < count($_REQUEST['colname']); $k++) {
//                       
//                        if ($k == 0) {
//                     
//                                if (empty($where)) {
//                                    $where .= " where " . $_REQUEST['colname'][$k] . " LIKE " . "'%" . $_REQUEST['search'][$k] . "%'";
//                                } else {
//                                    $where .= " and " . $_REQUEST['colname'][$k] . " LIKE " . "'%" . $_REQUEST['search'][$k] . "%'";
//                                }
//                            
//                        } else {
//                            
//                                if (empty($where)) {
//                                    $where .= " where " . $_REQUEST['colname'][$k] . " LIKE " . "'%" . $_REQUEST['search'][$k] . "%'";
//                                } else {
//                                    $where .= " AND " . $_REQUEST['colname'][$k] . " LIKE " . "'%" . $_REQUEST['search'][$k] . "%'";
//                                }
//                            
//                        }
//                    }
//
//                    
//
//                     $allot = "SELECT " . $newcoloums . " FROM " . $tblname . " INNER JOIN tbl_doc_assigned_wf ON " . $tblname . ".ticket_id = tbl_doc_assigned_wf.ticket_id INNER JOIN  tbl_user_master tusm on tusm.user_id=tbl_doc_assigned_wf.assign_by INNER JOIN  tbl_user_master tudsm on tudsm.user_id=tbl_doc_assigned_wf.action_by $where ";
//                 
//                } else {
//                    //this code run when search filter not used
//                    $allot = "SELECT " . $newcoloums . " FROM " . $tblname . " INNER JOIN tbl_doc_assigned_wf ON " . $tblname . ".ticket_id = tbl_doc_assigned_wf.ticket_id ";
//                }
//
//                $coloums = explode(",", $coloums);
//                $coloums = implode("','", $coloums);
//
//                $allot_query = mysqli_query($db_con, $allot) or die("Error21: " . mysqli_error($db_con));
//                $foundnum = mysqli_num_rows($allot_query);
//
//
//                if ($foundnum > 0) {
//                    if (is_numeric($_REQUEST['limit'])) {
//                        $per_page = $_REQUEST['limit'];
//                    } else {
//                        $per_page = 10;
//                    }
//                    $start = isset($_REQUEST['start']) ? $_REQUEST['start'] : '';
//                    $max_pages = ceil($foundnum / $per_page);
//                    if (!$start) {
//                        $start = 0;
//                    }
//                    if (!empty($_REQUEST['colname']) && !empty($_REQUEST['search'])) {
//
//// 
//                        $allote = "SELECT " . $newcoloums . " FROM " . $tblname . " INNER JOIN tbl_doc_assigned_wf ON " . $tblname . ".ticket_id = tbl_doc_assigned_wf.ticket_id INNER JOIN  tbl_user_master tusm on tusm.user_id=tbl_doc_assigned_wf.assign_by INNER JOIN  tbl_user_master tudsm on tudsm.user_id=tbl_doc_assigned_wf.action_by $where  LIMIT $start, $per_page";
//
//                        $allote_query = mysqli_query($db_con, $allote) or die("ERROR:" . mysqli_error($db_con));
////                                              
//                    } else {
//
//                        $allote = "SELECT " . $newcoloums . " FROM " . $tblname . " INNER JOIN tbl_doc_assigned_wf ON " . $tblname . ".ticket_id = tbl_doc_assigned_wf.ticket_id  LIMIT $start, $per_page";
//                        $allote_query = mysqli_query($db_con, $allote) or die("ERROR:" . mysqli_error($db_con));
////                                                            }
//                    }
//                    $dFormColoums = array();
//                    $labelnameqry = mysqli_query($db_con, "SELECT label,name FROM tbl_form_attribute WHERE name in('$coloums') and dependency_id IS NUll and  fid='$formid'") or die("Label Error:" . mysqli_error($db_con));
//                    $header = array();
//                    array_push($header, "S.NO");
//                    while ($rowdataFetch = mysqli_fetch_assoc($labelnameqry)) {
//                        array_push($dFormColoums, $rowdataFetch['name']);
//                        array_push($header, $rowdataFetch['label']);
//                        $rowdataFetch['label'];
//                    }
//                    if (in_array("wf_rupee", $recol)) {
//                        array_push($header, "Total Amount");
//                    }if (in_array("task_status", $recol)) {
//                        array_push($header, "Total Status");
//                    } if (in_array("action_by", $recol)) {
//                        array_push($header, "Action By");
//                    } if (in_array("action_time", $recol)) {
//                        array_push($header, "Action Date");
//                    }
//                    if (in_array("assign_by", $recol)) {
//                        array_push($header, "Initiated By");
//                    }
//                    if (in_array("start_date", $recol)) {
//                        array_push($header, "Initiated Date");
//                    }
//                    if (in_array("emp_id", $recol)) {
//                        array_push($header, "Employee ID");
//                    }
//                    $n = 1;
//                    $data = '';
//                    while ($allot_row = mysqli_fetch_assoc($allote_query)) {
//                        unset($data_values);
//                        $whpos = array_search('wf_whouse', $dFormColoums); //search warehoue in array and remove it
//                        unset($dFormColoums[$whpos]);
//                        $ccpos = array_search('wf_ccenter', $dFormColoums); //search warehoue in array and remove it
//                        unset($dFormColoums[$ccpos]);
//
//                        $data_values[] = $n;
//                        /* if (in_array("wf_ccenter", $recol)) {
//                            $qry = mysqli_query($db_con, "select * from tbl_cost_center where cc_id='$allot_row[wf_ccenter]'");
//                            $cc = mysqli_fetch_assoc($qry);
//                            $data_values[] = $cc['cc_name'];
//                        } if (in_array("wf_whouse", $recol)) {
//                            $whqry = mysqli_query($db_con, "select * from  tbl_whouse_master where wh_id='$allot_row[wf_whouse]'");
//                            $wh = mysqli_fetch_assoc($whqry);
//                            $data_values[] = $wh['wh_name'];
//                        } */
//
//
//                        foreach ($dFormColoums as $key => $value) {
//                            if ($allot_row[$value] == "CO(Compensatory off)") {
//                                $tblid = $allot_row['tbl_id'];
//                                $tblid = mysqli_escape_string($db_con, $tblid);
//                                $tblname = mysqli_escape_string($db_con, $tblname);
//                                $qry = mysqli_query($db_con, "select * from " . $tblname . " where tbl_id='$tblid'") or die(mysqli_error($db_con));
//                                $fetch = mysqli_fetch_assoc($qry);
//                                $data_values[] = $allot_row[$value] . "-" . $fetch['co'];
//                            } else {
//                                $data_values[] = $allot_row[$value];
//                            }
//                        }
////                                                                            }
//
//                        if (in_array("wf_rupee", $recol)) {
//                            $data_values[] = $allot_row['wf_rupee'];
//                        }
//
//
//                        if (in_array("task_status", $recol)) {
//                            $data_values[] = $allot_row['task_status'];
//                        }
//                        if (in_array("action_by", $recol)) {
//                            if (!empty($allot_row['action_by'])) {
//                                $user = mysqli_query($db_con, "select first_name,last_name from tbl_user_master where user_id='$allot_row[action_by]'");
//                                $rwUser = mysqli_fetch_assoc($user);
//                                $data_values[] = $rwUser['first_name'] . ' ' . $rwUser['last_name'];
//                            } else {
//                                $data_values[] = 'No Action Performed';
//                            }
//                        } if (in_array("action_time", $recol)) {
//                            $data_values[] = $allot_row['action_time'];
//                        }
//                        if (in_array("assign_by", $recol)) {
//                            $user = mysqli_query($db_con, "select first_name,last_name from tbl_user_master where user_id='$allot_row[assign_by]'");
//                            $rwUser = mysqli_fetch_assoc($user);
//                            $data_values[] = $rwUser['first_name'] . ' ' . $rwUser['last_name'];
//                        }
//                        if (in_array("start_date", $recol)) {
//                            $data_values[] = $allot_row['start_date'];
//                        }
//                        if (in_array("emp_id", $recol)) {
//                            $data_values[] = $allot_row['emp_id'];
//                        }
//                        $line = '';
//                        if (!empty($data_values)) {
//                            foreach ($data_values as $value) {
//
//                                if ((!isset($value) ) || ( $value == "" )) {
//                                    $value = "--";
//                                } else {
//                                    $value = str_replace('"', '""', $value);
//                                    //$value = '"' . $value . '"' . ",";
//
//                                    $value = $value . ',';
//                                }
//                                $line .= $value;
//                            }
//                            if (!empty($line)) {
//                                $line;
//
//                                $data .= trim($line) . "\n";
//                            }
//                        }
//
//
//                        $n++;
//                        // fputcsv($output, $data_values,",",chr(0));
//                    }
//                    $data = str_replace("\r", "", $data);
//                    $topheader = implode(",", $header);
//                }
//            }
//        }
//    }
//
//    header("Content-type: application/octet-stream");
//    header("Content-Disposition: attachment; filename=export.csv");
//    header("Pragma: no-cache");
//    header("Expires: 1");
//    echo"$topheader\n$data";
//} else if ($_REQUEST['format'] == "txt") {
//    $rid = $_REQUEST['rid'];
//    $wfid = $_REQUEST['wfid'];
//    $where = '';
//    if (isset($_REQUEST['startDate']) && !empty($_REQUEST['startDate']) && isset($_REQUEST['endDate']) && !empty($_REQUEST['endDate'])) {
//        if (empty($where)) {
//            $where = "where (start_date BETWEEN '" . date('Y-m-d', strtotime($_REQUEST['startDate'])) . "' AND '" . date('Y-m-d', strtotime($_REQUEST['endDate']) + 24 * 60 * 60) . "')";
//        }
//    }
//    $wftblqry = mysqli_query($db_con, "select * from  tbl_workflow_master where workflow_id='$wfid'");
//    $rows = mysqli_fetch_assoc($wftblqry);
//    $qryform = mysqli_query($db_con, "select * from tbl_bridge_workflow_to_form where workflow_id='$wfid'");
//    $res = mysqli_fetch_assoc($qryform);
//    $formid = $res["form_id"];
//    $wftblqry = mysqli_query($db_con, "select * from  tbl_workflow_master where workflow_id='$wfid'") or die(mysqli_error($db_con));
//    if (mysqli_num_rows($wftblqry) > 0) {
//        $dataContent = mysqli_fetch_assoc($wftblqry);
//        $tblname = $dataContent['form_tbl_name'];
//        $qry = mysqli_query($db_con, "select * from tbl_wf_reports where rp_id='$rid' and wf_id='$wfid'")or die(mysqli_error($db_con));
//        $rowdata = mysqli_fetch_assoc($qry);
//        $recol = $rowdata['coloums'];
//        $recol = explode(",", $recol);
//        $coloums = $rowdata['coloums'];
//        $newcoloums = $coloums . "," . "tbl_id";
//        if (!empty($coloums)) {
//            if (mysqli_num_rows($qry) > 0) {
//                if (!empty($_REQUEST['colname']) && !empty($_REQUEST['search'])) {
//             
//                    for ($k = 0; $k < count($_REQUEST['colname']); $k++) {
//                      
//                        if ($k == 0) {
//                        
//                                if (empty($where)) {
//                                    $where .= " where " . $_REQUEST['colname'][$k] . " LIKE " . "'%" . $_REQUEST['search'][$k] . "%'";
//                                } else {
//                                    $where .= " and " . $_REQUEST['colname'][$k] . " LIKE " . "'%" . $_REQUEST['search'][$k] . "%'";
//                                }
//                            
//                        } else {
//                           
//                                if (empty($where)) {
//                                    $where .= " where " . $_REQUEST['colname'][$k] . " LIKE " . "'%" . $_REQUEST['search'][$k] . "%'";
//                                } else {
//                                    $where .= " AND " . $_REQUEST['colname'][$k] . " LIKE " . "'%" . $_REQUEST['search'][$k] . "%'";
//                                }
//                            
//                        }
//                    }
//
//                    
//
//                    $allot = "SELECT " . $newcoloums . " FROM " . $tblname . " INNER JOIN tbl_doc_assigned_wf ON " . $tblname . ".ticket_id = tbl_doc_assigned_wf.ticket_id INNER JOIN  tbl_user_master tusm on tusm.user_id=tbl_doc_assigned_wf.assign_by INNER JOIN  tbl_user_master tudsm on tudsm.user_id=tbl_doc_assigned_wf.action_by $where ";
//                } else {
//                    //this code run when search filter not used
//                    $allot = "SELECT " . $newcoloums . " FROM " . $tblname . " INNER JOIN tbl_doc_assigned_wf ON " . $tblname . ".ticket_id = tbl_doc_assigned_wf.ticket_id ";
//                }
//
//                $coloums = explode(",", $coloums);
//                $coloums = implode("','", $coloums);
//
//                $allot_query = mysqli_query($db_con, $allot) or die("Error: " . mysqli_error($db_con));
//                $foundnum = mysqli_num_rows($allot_query);
//
//
//                if ($foundnum > 0) {
//                    if (is_numeric($_REQUEST['limit'])) {
//                        $per_page = $_REQUEST['limit'];
//                    } else {
//                        $per_page = 10;
//                    }
//                    $start = isset($_REQUEST['start']) ? $_REQUEST['start'] : '';
//                    $max_pages = ceil($foundnum / $per_page);
//                    if (!$start) {
//                        $start = 0;
//                    }
//                    if (!empty($_REQUEST['colname']) && !empty($_REQUEST['search'])) {
//
//
//                        $allote = "SELECT " . $newcoloums . " FROM " . $tblname . " INNER JOIN tbl_doc_assigned_wf ON " . $tblname . ".ticket_id = tbl_doc_assigned_wf.ticket_id INNER JOIN  tbl_user_master on tbl_user_master.user_id=tbl_doc_assigned_wf.assign_by $where  LIMIT $start, $per_page";
//                        $allote_query = mysqli_query($db_con, $allote) or die("ERROR:" . mysqli_error($db_con));
//                    } else {
//
//                        $allote = "SELECT " . $newcoloums . " FROM " . $tblname . " INNER JOIN tbl_doc_assigned_wf ON " . $tblname . ".ticket_id = tbl_doc_assigned_wf.ticket_id  LIMIT $start, $per_page";
//                        $allote_query = mysqli_query($db_con, $allote) or die("ERROR:" . mysqli_error($db_con));
////                                                            }
//                    }
//                    $dFormColoums = array();
//                    $labelnameqry = mysqli_query($db_con, "SELECT label,name FROM tbl_form_attribute WHERE name in('$coloums') and dependency_id IS NUll and  fid='$formid'") or die("Label Error:" . mysqli_error($db_con));
//                    $header = array();
//                    array_push($header, "S.NO");
//                    while ($rowdataFetch = mysqli_fetch_assoc($labelnameqry)) {
//                        array_push($dFormColoums, $rowdataFetch['name']);
//                        array_push($header, $rowdataFetch['label']);
//                        $rowdataFetch['label'];
//                    }
//                    if (in_array("wf_rupee", $recol)) {
//                        array_push($header, "Total Amount");
//                    }if (in_array("task_status", $recol)) {
//                        array_push($header, "Total Status");
//                    } if (in_array("action_by", $recol)) {
//                        array_push($header, "Action By");
//                    } if (in_array("action_time", $recol)) {
//                        array_push($header, "Action Date");
//                    }
//                    if (in_array("assign_by", $recol)) {
//                        array_push($header, "Initiated By");
//                    }
//                    if (in_array("start_date", $recol)) {
//                        array_push($header, "Initiated Date");
//                    }
//                    if (in_array("emp_id", $recol)) {
//                        array_push($header, "Employee ID");
//                    }
//                    $n = 1;
//                    $data = '';
//                    while ($allot_row = mysqli_fetch_assoc($allote_query)) {
//                        unset($data_values);
//                        $whpos = array_search('wf_whouse', $dFormColoums); //search warehoue in array and remove it
//                        unset($dFormColoums[$whpos]);
//                        $ccpos = array_search('wf_ccenter', $dFormColoums); //search warehoue in array and remove it
//                        unset($dFormColoums[$ccpos]);
//
//                        $data_values[] = $n;
//                        /* if (in_array("wf_ccenter", $recol)) {
//                            $qry = mysqli_query($db_con, "select * from tbl_cost_center where cc_id='$allot_row[wf_ccenter]'");
//                            $cc = mysqli_fetch_assoc($qry);
//                            $data_values[] = $cc['cc_name'];
//                        } if (in_array("wf_whouse", $recol)) {
//                            $whqry = mysqli_query($db_con, "select * from  tbl_whouse_master where wh_id='$allot_row[wf_whouse]'");
//                            $wh = mysqli_fetch_assoc($whqry);
//                            $data_values[] = $wh['wh_name'];
//                        } */
//
//
//                        foreach ($dFormColoums as $key => $value) {
//                            if ($allot_row[$value] == "CO(Compensatory off)") {
//                                $tblid = $allot_row['tbl_id'];
//                                $tblid = mysqli_escape_string($db_con, $tblid);
//                                $tblname = mysqli_escape_string($db_con, $tblname);
//                                $qry = mysqli_query($db_con, "select * from " . $tblname . " where tbl_id='$tblid'") or die(mysqli_error($db_con));
//                                $fetch = mysqli_fetch_assoc($qry);
//                                $data_values[] = $allot_row[$value] . "-" . $fetch['co'];
//                            } else {
//                                $data_values[] = $allot_row[$value];
//                            }
//                        }
////                                                                            }
//
//                        if (in_array("wf_rupee", $recol)) {
//                            $data_values[] = $allot_row['wf_rupee'];
//                        }
//
//
//                        if (in_array("task_status", $recol)) {
//                            $data_values[] = $allot_row['task_status'];
//                        }
//                        if (in_array("action_by", $recol)) {
//                            if (!empty($allot_row['action_by'])) {
//                                $user = mysqli_query($db_con, "select first_name,last_name from tbl_user_master where user_id='$allot_row[action_by]'");
//                                $rwUser = mysqli_fetch_assoc($user);
//                                $data_values[] = $rwUser['first_name'] . ' ' . $rwUser['last_name'];
//                            } else {
//                                $data_values[] = 'No Action Performed';
//                            }
//                        } if (in_array("action_time", $recol)) {
//                            $data_values[] = $allot_row['action_time'];
//                        }
//                        if (in_array("assign_by", $recol)) {
//                            $user = mysqli_query($db_con, "select first_name,last_name from tbl_user_master where user_id='$allot_row[assign_by]'");
//                            $rwUser = mysqli_fetch_assoc($user);
//                            $data_values[] = $rwUser['first_name'] . ' ' . $rwUser['last_name'];
//                        }
//                        if (in_array("start_date", $recol)) {
//                            $data_values[] = $allot_row['start_date'];
//                        }
//                        if (in_array("emp_id", $recol)) {
//                            $data_values[] = $allot_row['emp_id'];
//                        }
//                        $line = '';
//                        if (!empty($data_values)) {
//                            foreach ($data_values as $value) {
//
//                                if ((!isset($value) ) || ( $value == "" )) {
//                                    $value = "--";
//                                } else {
//                                    $value = str_replace('"', '""', $value);
//                                    //$value = '"' . $value . '"' . ",";
//
//                                    $value = $value . ',';
//                                }
//                                $line .= $value;
//                            }
//                            if (!empty($line)) {
//                                $line;
//
//                                $data .= trim($line) . "\n";
//                            }
//                        }
//
//
//                        $n++;
//                        // fputcsv($output, $data_values,",",chr(0));
//                    }
//                    $data = str_replace("\r", "", $data);
//                    $topheader = implode(",", $header);
//                }
//            }
//        }
//    }
//
//    header("Content-type: application/octet-stream");
//    header("Content-Disposition: attachment; filename=export.txt");
//    header("Pragma: no-cache");
//    header("Expires: 1");
//    echo"$topheader\n$data";
//}
?>
