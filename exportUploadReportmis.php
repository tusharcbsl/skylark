<?php

    require_once './loginvalidate.php';
require_once'./application/config/database.php';
mysqli_set_charset($db_con, "utf8");
if ($_POST['report_type'] == "memoryusedreport") {
if (!empty($_POST['cname'])) {
	$company = $_POST['cname'];
	$condition .= "and (concat(fname, ' ', lname) LIKE '%$company%' OR concat(lname, ' ', fname) LIKE '%$company%' OR  company like '%$company%')";
}
$currentsdate=strtotime(date("Y-m-d"));
$sql = "SELECT * FROM  tbl_client_master where valid_upto>='$currentsdate' $condition";
$retval = mysqli_query($db_con, $sql) or die('Could not get data: ' . mysqli_error($db_con));
$foundnum = mysqli_num_rows($retval);
if ($foundnum > 0) {
	if (is_numeric($_POST['limit'])) {
		$per_page = preg_replace("/[^0-9]/", "", $_POST['limit']);
	} else {
		$per_page = 10;
	}
	$start = isset($_POST['start']) ? $_POST['start'] : '';
	$max_pages = ceil($foundnum / $per_page);
	if (!$start) {
		$start = 0;
	}
	$where = '';
	
	$users = mysqli_query($db_con, "select * from tbl_client_master where valid_upto>='$currentsdate'  $condition order by company asc LIMIT $start, $per_page") or die('Error:' . mysqli_error($db_con));
                                        
                $xlsoutput = '';
                 $xlsoutput .= '<table class="table table-striped table-bordered">';
        $xlsoutput .= '<thead>
				<tr>
                <th>S.No</th>
                <th>Contact Name</th>
                <th>Company Name</th>
                <th>Uploaded Memory</th>
                <th>Downloaded Memory</th>
            </tr>
        </thead>
        <tbody>';
           $i = 1;
            $i += $_POST['start'];
            while ($rwUser = mysqli_fetch_assoc($users)) {
                $dbname = $rwUser['db_name'];
                //db con 
                $totaldocSize = 0;
                $newdb_con = @mysqli_connect($dbHost,$dbUser,$dbPwd, $dbname);
                if (!$newdb_con) {
                    continue;
                }
                $qry = mysqli_query($newdb_con, "SELECT sum(doc_size) as res FROM `tbl_document_master` WHERE flag_multidelete='1'");
                $rows = mysqli_fetch_assoc($qry);
                $uploadMemory = round($rows[res] / (1000 * 1000), 2);
                $dmemory = array();
                $download_qry = mysqli_query($newdb_con, "select doc_id from tbl_ezeefile_logs   WHERE  action_name LIKE '%printed%' OR action_name LIKE '%Downloaded%' OR action_name LIKE '%download%' OR action_name LIKE '%Download%' OR action_name LIKE '%view%'  OR action_name LIKE '%viewed%' and doc_id is NOT NULL and doc_id!='Array' and doc_id!=''");
                while ($rowdownload_qry = mysqli_fetch_assoc($download_qry)) {
                    $child = mysqli_query($newdb_con, "select sum(doc_size) as file_size FROM tbl_document_master where FIND_IN_SET(doc_id,'$rowdownload_qry[doc_id]')");
                    $rwuploadrpt = mysqli_fetch_assoc($child);
                    //print_r($rwuploadrpt);
                    $totaldocSize += $rwuploadrpt['file_size'];
                }
                $totaldocSize = $totaldocSize / 1024;
                if ($totaldocSize >= 1024) {
                    $totaldocSize = $totaldocSize / 1024;
                    if ($totaldocSize >= 1024) {
                        $totaldocSize = $totaldocSize / 1024;
                        if ($totaldocSize >= 1024) {
                            $totaldocSize = $totaldocSize / 1024;
                            if ($totaldocSize >= 1024) {
                                
                            } else {
                                $totaldocSize = round($totaldocSize, 2) . ' TB';
                            }
                        } else {
                            $totaldocSize = round($totaldocSize, 2) . ' GB';
                        }
                    } else {
                        $totaldocSize = round($totaldocSize, 2) . ' MB';
                    }
                } else {
                    $totaldocSize = round($totaldocSize, 2) . ' KB';
                }
                $xlsoutput .= '<tr class="gradeX">';
                    $xlsoutput .= '<td>'.$i.'</td>';
                    $xlsoutput .= '<td>'.$rwUser['fname'] . " " . $rwUser['lname'].'</td>';
                    $xlsoutput .= '<td>'.$rwUser['company'].' </td>';
                    $xlsoutput .= '<td> <a href="upload-report?db='.urlencode(base64_encode($dbname)).'&cid='.urlencode(base64_encode($rwUser['client_id'])).'" class="btn btn-primary btn-xs" title="Total Uploaded Memory"><i class="fa fa-cloud-upload"></i> '.(($uploadMemory > 999) ? round($uploadMemory / 1024, 2) : $uploadMemory) . (($uploadMemory > 999) ? ' GB' : ' MB').'</a> </td>';
                    $xlsoutput .= '<td><a href="download-report?db='.urlencode(base64_encode($dbname)).'&cid='.urlencode(base64_encode($rwUser['client_id'])).'" class="btn btn-primary btn-xs" title="Total Downloded Memory"> <i class="fa fa-cloud-download"></i> '.$totaldocSize.' </a> </td>';
                $xlsoutput .= '</tr>';
                $i++;
            }
        $xlsoutput .= '</tbody>';
    $xlsoutput .= '</table>';
            } else {
            echo 'No Records Found';
			}


$fileName =$_POST['report_type']. date('Y-m-d') . ".xls";
header("Content-Disposition: attachment; filename=\"$fileName\"");
echo $xlsoutput;
}
if ($_POST['report_type'] == "uploadreport") {
    $dbname = base64_decode(urldecode($_POST['db']));
    $clientId = base64_decode(urldecode($_POST['cid']));
    //db con 
    $newdbconn = @mysqli_connect($dbHost,$dbUser,$dbPwd, $dbname) OR die('could not connect:' . mysqli_connect_error());
 $xlsoutput = '';
                 $xlsoutput .= '<table class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>SNO</th>
                                                        <th>Storage</th>
                                                        <th>No Of Files</th>
                                                        <th>No of Pages</th>
                                                        <th>Storage Size(MB)</th>
                                                        <th>Uploaded Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>';
                                                    $date = date("Y-m-d");
                                                    $where = " where sl_name!='' and sl_name is not NULL";
                                                    if (isset($_POST['enddt']) && !empty($_POST['enddt']) && isset($_POST['startdt']) && !empty($_POST['startdt'])) {
                                                        $end_date = date('Y-m-d', strtotime($_POST['enddt']));
                                                        $start_date = date('Y-m-d', strtotime($_POST['startdt']));
                                                        $where = " and  DATE(dateposted) BETWEEN '$start_date' AND '$end_date'";
                                                    }
                                                    $uploadrept = mysqli_query($newdbconn, "SELECT sl_name,count(doc_id) as no_of_file,doc_name as sl_id,sum(noofpages) as no_of_pages,dateposted,sum(doc_size) as file_size FROM tbl_document_master as tdm  join tbl_storage_level as tsl on tsl.sl_id=tdm.doc_name $where group by YEAR(dateposted),month(dateposted), day(dateposted),doc_name order by sl_name asc, DATE(dateposted) desc") or die("ERROR" . mysqli_error($newdbconn));

                                                    $num = mysqli_num_rows($uploadrept);
                                                    $totalFiles = 0;
                                                    $totalPages = 0;
                                                    $totaldocSize = 0;
                                                    if (isset($start) && $start != 0) {
                                                        $i = $start + 1;
                                                    } else {
                                                        $i = 1;
                                                    }
                                                    if ($num > 0) {

                                                        while ($rwuploadrpt = mysqli_fetch_assoc($uploadrept)) {
                                                            $totalFiles += $rwuploadrpt['no_of_file'];
                                                            $totalPages += $rwuploadrpt['no_of_pages'];
                                                            $totaldocSize += $rwuploadrpt['file_size'];
                                                            $child = mysqli_query($newdbconn, "select sl_id from tbl_storage_level where sl_parent_id='$rwuploadrpt[sl_id]'");
                                                             $xlsoutput .= '<tr>';
                                                                 $xlsoutput .= '<td>'.$i.'</td>';
                                                                 $xlsoutput .= '<td>'.$rwuploadrpt['sl_name'].'</td>';
                                                                 $xlsoutput .= '<td>';
                                                                    $sslid = explode('_', $rwuploadrpt['sl_id']);

                                                                    if (mysqli_num_rows($child) > 0) {

                                                                        $xlsoutput .= $rwuploadrpt['no_of_file'];
                                                                    } else {
                                                                        $xlsoutput .= $rwuploadrpt['no_of_file'];
                                                                    }
                                                                  $xlsoutput .= '</td>';
                                                                 $xlsoutput .= '<td>'.$rwuploadrpt['no_of_pages'].'</td>';
                                                                 $xlsoutput .= '<td>'.round($rwuploadrpt['file_size'] / (1024 * 1024), 2).'</td>';
                                                                 $xlsoutput .= '<td>';
                                                                    if (!empty($rwuploadrpt['dateposted']))
																	{
                                                                        $xlsoutput .= date("d-m-Y", strtotime($rwuploadrpt['dateposted']));
                                                                    }
                                                                   $xlsoutput .= '</td>';
                                                            $xlsoutput .= ' </tr>';
                                                            $i++;
                                                        }
                                                        $totaldocSize = $totaldocSize / 1024;
                                                        if ($totaldocSize >= 1024) {
                                                            $totaldocSize = $totaldocSize / 1024;
                                                            if ($totaldocSize >= 1024) {
                                                                $totaldocSize = $totaldocSize / 1024;
                                                                if ($totaldocSize >= 1024) {
                                                                    $totaldocSize = $totaldocSize / 1024;
                                                                    if ($totaldocSize >= 1024) {
                                                                        
                                                                    } else {
                                                                        $totaldocSize = round($totaldocSize, 2) . ' TB';
                                                                    }
                                                                } else {
                                                                    $totaldocSize = round($totaldocSize, 2) . ' GB';
                                                                }
                                                            } else {
                                                                $totaldocSize = round($totaldocSize, 2) . ' MB';
                                                            }
                                                        } else {
                                                            $totaldocSize = round($totaldocSize, 2) . ' KB';
                                                        }
                                                     $xlsoutput .= '<tfoot>';
                                                         $xlsoutput .= '<tr>';
                                                           $xlsoutput .= '<th></th><th><strong>Total</strong></th><th>'.$totalFiles.'</th><th>'.$totalPages.'</th><th colspan="2">'.$totaldocSize.'</th>';
                                                         $xlsoutput .= '</tr>';
                                                     $xlsoutput .= '</tfoot>';
                                                } else {
                                                     $xlsoutput .= '<tfoot>';
                                                         $xlsoutput .= '<tr>';
                                                           $xlsoutput .= '  <th colspan="6" style="text-align: center;color: red">No File Uploaded!</th>';
                                                        $xlsoutput .= ' </tr>';
                                                     $xlsoutput .= '</tfoot>';
                                                }
                                                 $xlsoutput .= '</tbody>';
                                             $xlsoutput .= '</table>';
$fileName = $_POST['report_type']. date('Y-m-d') . ".xls";
header("Content-Disposition: attachment; filename=\"$fileName\"");


echo $xlsoutput;die;
}
if ($_POST['report_type'] == "downloadreport") {
    $dbname = base64_decode(urldecode($_POST['db']));
    $clientId = base64_decode(urldecode($_POST['cid']));
    //db con 
    $newdbconn = @mysqli_connect($dbHost,$dbUser,$dbPwd, $dbname) OR die('could not connect:' . mysqli_connect_error());
 $xlsoutput = '';
                 $xlsoutput .= '<table class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>SNO</th>
                                                        <th>Storage</th>
                                                        <th>No Of Files</th>
                                                        <th>No of Pages</th>
                                                        <th>Storage Size(MB)</th>
                                                        <th>Downloaded Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>';
                                                    $date = date("Y-m-d");
                                                    $where = "";
                                                    if (isset($_POST['enddt']) && !empty($_POST['enddt']) && isset($_POST['startdt']) && !empty($_POST['startdt'])) {
                                                        $end_date = date('Y-m-d', strtotime($_POST['enddt']));
                                                        $start_date = date('Y-m-d', strtotime($_POST['startdt']));
                                                        $where = " and  DATE(tel.start_date) BETWEEN '$start_date' AND '$end_date'";
                                                    }
                                                   $totalFiles = 0;
                                                    $totalPages = 0;
                                                    $totaldocSize = 0;
                                                    $dmemory = array();

                                                    $download_qry = mysqli_query($newdbconn, "SELECT tel.sl_id,tel.start_date,tel.doc_id, tsl.sl_id,tsl.sl_name FROM `tbl_ezeefile_logs` as tel inner join tbl_storage_level as tsl on tel.sl_id=tsl.sl_id where action_name LIKE '%printed%' OR action_name LIKE '%Downloaded%' OR action_name LIKE '%download%' OR action_name LIKE '%Download%' OR action_name LIKE '%view%' OR action_name LIKE '%viewed%' and tel.sl_id>0  and tsl.sl_name!=''  and tsl.sl_name is not NULL  $where order by DATE(tel.start_date) desc");
                                                    $num = mysqli_num_rows($download_qry);
                                                    if (isset($start) && $start != 0) {
                                                        $i = $start + 1;
                                                    } else {
                                                        $i = 1;
                                                    }
                                                    if ($num > 0) {

                                                        while ($rowdownload_qry = mysqli_fetch_assoc($download_qry)) {

                                                            $child = mysqli_query($newdbconn, "select sl_name,count(doc_id) as no_of_file,sum(noofpages) as no_of_pages,sum(doc_size) as file_size FROM tbl_document_master as tdm  join tbl_storage_level as tsl on tsl.sl_id=tdm.doc_name where FIND_IN_SET(doc_id,'$rowdownload_qry[doc_id]') and tdm.doc_name='" . $rowdownload_qry['sl_id'] . "'");
                                                            $rwuploadrpt = mysqli_fetch_assoc($child);
															$xlsoutput .= '<tr>';
                                                                 $xlsoutput .= '<td>'.$i.'</td>';
                                                                 $xlsoutput .= '<td>'.$rowdownload_qry['sl_name'].'</td>';
                                                                 $xlsoutput .= '<td>';
                                                                    $sslid = explode('_', $rwuploadrpt['sl_id']);

                                                                    if (mysqli_num_rows($child) > 0) {

                                                                        $xlsoutput .= $rwuploadrpt['no_of_file'];
                                                                    } else {
                                                                        $xlsoutput .= $rwuploadrpt['no_of_file'];
                                                                    }
                                                                  $xlsoutput .= '</td>';
                                                                 $xlsoutput .= '<td>'.$rwuploadrpt['no_of_pages'].'</td>';
                                                                 $xlsoutput .= '<td>'.round($rwuploadrpt['file_size'] / (1024 * 1024), 2).'</td>';
                                                                 $xlsoutput .= '<td>';
                                                                    if (!empty($rowdownload_qry['start_date']))
																	{
                                                                        $xlsoutput .= date("d-m-Y", strtotime($rowdownload_qry['start_date']));
                                                                    }
                                                                   $xlsoutput .= '</td>';
                                                            $xlsoutput .= ' </tr>';
															
                                                            $totalFiles += $rwuploadrpt['no_of_file'];
                                                            $totalPages += $rwuploadrpt['no_of_pages'];
                                                            $totaldocSize += $rwuploadrpt['file_size'];
                                                            $i++;
                                                        }
                                                        $totaldocSize = $totaldocSize / 1024;
                                                        if ($totaldocSize >= 1024) {
                                                            $totaldocSize = $totaldocSize / 1024;
                                                            if ($totaldocSize >= 1024) {
                                                                $totaldocSize = $totaldocSize / 1024;
                                                                if ($totaldocSize >= 1024) {
                                                                    $totaldocSize = $totaldocSize / 1024;
                                                                    if ($totaldocSize >= 1024) {
                                                                        
                                                                    } else {
                                                                        $totaldocSize = round($totaldocSize, 2) . ' TB';
                                                                    }
                                                                } else {
                                                                    $totaldocSize = round($totaldocSize, 2) . ' GB';
                                                                }
                                                            } else {
                                                                $totaldocSize = round($totaldocSize, 2) . ' MB';
                                                            }
                                                        } else {
                                                            $totaldocSize = round($totaldocSize, 2) . ' KB';
                                                        }
                                                     $xlsoutput .= '<tfoot>';
                                                         $xlsoutput .= '<tr>';
                                                           $xlsoutput .= '<th></th><th><strong>Total</strong></th><th>'.$totalFiles.'</th><th>'.$totalPages.'</th><th colspan="2">'.$totaldocSize.'</th>';
                                                         $xlsoutput .= '</tr>';
                                                     $xlsoutput .= '</tfoot>';
                                                } else {
                                                     $xlsoutput .= '<tfoot>';
                                                         $xlsoutput .= '<tr>';
                                                           $xlsoutput .= '  <th colspan="6" style="text-align: center;color: red">No File Uploaded!</th>';
                                                        $xlsoutput .= ' </tr>';
                                                     $xlsoutput .= '</tfoot>';
                                                }
                                                 $xlsoutput .= '</tbody>';
                                             $xlsoutput .= '</table>';
$fileName = $_POST['report_type']. date('Y-m-d') . ".xls";
header("Content-Disposition: attachment; filename=\"$fileName\"");


echo $xlsoutput;die;
}

?>
