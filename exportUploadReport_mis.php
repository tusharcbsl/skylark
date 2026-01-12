<?php

require_once './loginvalidate.php';
 require_once './application/pages/function.php';

//@dv 08-03-2019 function for view all storage permissions and theier subfolders.


$perm = mysqli_query($db_con, "select * from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]' group by sl_id");
$slperms = array();
while ($rwPerm = mysqli_fetch_assoc($perm)) {
$slpermIds[] = $rwPerm['sl_id'];
}
$slpermIdes = implode(',', $slpermIds);

$slids = findsubfolder($slpermIdes, $db_con);

$slids = implode(',', $slids);

/* function findTotalFile($slperm) {
global $list;
$list = array();
global $db_con;
global $numFile;
global $totalFSize;
global $totalFolder;
global $noofpages;
$contFile = mysqli_query($db_con, "select sum(doc_size) as total, count(doc_id) as count,sum(noofpages) as numPage from tbl_document_master where substring_index(doc_name,'_',1)='$slperm' && flag_multidelete = 1") or die('Error :' . mysqli_error($db_con));
//$contFile = mysqli_query($db_con, "select sum(doc_size) as total, count(doc_name) as count,sum(noofpages) as numPage from tbl_document_master where FIND_IN_SET('$slperm',doc_name) && flag_multidelete = 1") or die('Error :' . mysqli_error($db_con));
//echo "select no_of_file as count, no_of_pages as numPage, file_size as total from tbl_agr_doc_upload where substring_index(sl_id,'_',1) in($slperm)";
//$contFile = mysqli_query($db_con, "select no_of_file as count, no_of_pages as numPage, file_size as total from tbl_agr_doc_upload where substring_index(sl_id,'_',1) ='$slperm'");
$rwcontFile = mysqli_fetch_assoc($contFile);
$totalFSize1 = $rwcontFile['total'];
$totalFSize += round($totalFSize1 / (1024 * 1024), 2);
$numFile += $rwcontFile['count'];
$list["files"] = $numFile;
$list["fileSize"] = $totalFSize;
if (!empty($slperm)) {
$totalFolder += 1;
}
$list["totalFolder"] = $totalFolder;
$noofpages += $rwcontFile['numPage'];
$list['numPages'] = $noofpages;
$sql_child = "select * FROM tbl_storage_level WHERE sl_parent_id in($slperm)";
$sql_child_run = mysqli_query($db_con, $sql_child) or die('Error: ' . mysqli_error($db_con));
if (mysqli_num_rows($sql_child_run) > 0) {

while ($rwchild = mysqli_fetch_assoc($sql_child_run)) {

$child = $rwchild['sl_id'];
$clagain = findTotalFile($child);
}
}
return $list;
}

if ($slpermIdes) {
$slpermIdes = explode(",", $slpermIdes);
$folders = 0;
$files = 0;
$numPages = 0;
$fileSize = 0;
foreach ($slpermIdes as $slperm) {
$totalFolder = 0;
$noofpages = 0;
$numFile = 0;
$totalFSize = 0;
$total = findTotalFile($slperm);
$folders += $total['totalFolder'];
$files += $total['files'];
$numPages += $total['numPages'];
$fileSize += $total['fileSize'];
}
} */
?>
<?php

if (isset($_POST['export'])) {
	
    if (!isset($_POST['token'])) {
        header('location:access-denied.html');
        exit();
    }
    $selectFormat = trim($_POST['select_Fm']);
	
	$where ="";
	if(isset($_POST['sname']) && !empty($_POST['sname'])){
		$slname = xss_clean($_POST['sname']);
		$where = " and sl_name like '%$slname%'";
	}
	
	if((isset($_POST['fromDate']) && !empty($_POST['fromDate'])) && (isset($_POST['toDate']) && !empty($_POST['toDate']))){
		$fromDate = xss_clean($_POST['fromDate']);
		$toDate = xss_clean($_POST['toDate']);
		
		$fromDate = date('Y-m-d', strtotime($fromDate));
		$toDate = date('Y-m-d', strtotime($toDate));
		$where .= " and date(dateposted)>='$fromDate' and date(dateposted)<='$toDate'";
	}

	
$uploadrept = mysqli_query($db_con, "SELECT sl_name,count(doc_id) as no_of_file,doc_name as sl_id,sum(noofpages) as no_of_pages,dateposted,sum(doc_size) as file_size FROM tbl_document_master as tdm  join tbl_storage_level as tsl on tsl.sl_id=tdm.doc_name where tsl.sl_id in($slids) and flag_multidelete=1 $where group by YEAR(dateposted),month(dateposted), day(dateposted),doc_name order by dateposted desc") or die("ERROR" . mysqli_error($db_con));


if ($selectFormat == "EXCEL") {

$header1 = "Sr. No.\t Storage  \t No of Files \t No of Pages\t Storage Size \tUploaded On\t\n";
$i = 1;
$totalFiles = 0;
$totalPages = 0;
$totalfiles = 0;
$totalPages = 0;
while ($rwuploadrpt = mysqli_fetch_assoc($uploadrept)) {
	
$totalFiles += $rwuploadrpt['no_of_file'];
$totalPages += $rwuploadrpt['no_of_pages'];
$totaldocSize += $rwuploadrpt['file_size'];
$slId = $rwuploadrpt['sl_id'];
$upldStrgNm = mysqli_query($db_con, "SELECT sl_name FROM tbl_storage_level WHERE sl_id='$slId'"); // or die("ERROR" . mysqli_error($db_con));
$rwSlname = mysqli_fetch_assoc($upldStrgNm);
$result1 .= $i . "\t" . $rwSlname['sl_name'] . "\t" . $rwuploadrpt['no_of_file'] . "\t" . $rwuploadrpt['no_of_pages'] . "\t" . formatSizeUnits($rwuploadrpt['file_size']). "\t" . Date('d-m-Y', strtotime($rwuploadrpt['dateposted'])) . "\t\n";
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
$totaldocSize = round($totaldocSize, 2) . 'TB';
}
} else {
$totaldocSize = round($totaldocSize, 2) . 'GB';
}
} else {
$totaldocSize = round($totaldocSize, 2) . 'MB';
}
} else {
$totaldocSize = round($totaldocSize, 2) . 'KB';
}
$result1 .= "\t" . "\t Total Files = " . $totalFiles . "\t Total Pages = " . $totalPages . "\t Total Size = " . $totaldocSize . "\t\n";
$result1 = str_replace("\r", "", $result1);

if ($result1 == "") {
//$result1 = "\nNo Record(s) Found!\n";                        
}
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=exported_upload_report.xls");
header("Pragma: no-cache");
header("Expires: 0");
print "$header1\n$result1";
}
if ($selectFormat == "CSV") {

$header1 = "Sr. No., Storage, No of Files, No of Pages, Storage Size,Uploaded On,\n";
$i = 1;
$totalFiles = 0;
$totalPages = 0;
$totalfiles = 0;
$totalPages = 0;
while ($rwuploadrpt = mysqli_fetch_assoc($uploadrept)) {
$totalFiles += $rwuploadrpt['no_of_file'];
$totalPages += $rwuploadrpt['no_of_pages'];
$totaldocSize += $rwuploadrpt['file_size'];
$slId = $rwuploadrpt['sl_id'];
$upldStrgNm = mysqli_query($db_con, "SELECT sl_name FROM tbl_storage_level WHERE sl_id='$slId'"); // or die("ERROR" . mysqli_error($db_con));
$rwSlname = mysqli_fetch_assoc($upldStrgNm);
//$line = '';
$result1 .= $i . "," . $rwSlname['sl_name'] . "," . $rwuploadrpt['no_of_file'] . "," . $rwuploadrpt['no_of_pages'] . "," . formatSizeUnits($rwuploadrpt['file_size']). "," . Date('d-m-Y', strtotime($rwuploadrpt['dateposted'])) . ",\n";
$i++;
}
// die;
$totaldocSize = $totaldocSize / 1024;
if ($totaldocSize >= 1024) {
$totaldocSize = $totaldocSize / 1024;
if ($totaldocSize >= 1024) {
$totaldocSize = $totaldocSize / 1024;
if ($totaldocSize >= 1024) {
$totaldocSize = $totaldocSize / 1024;
if ($totaldocSize >= 1024) {

} else {
$totaldocSize = round($totaldocSize, 2) . 'TB';
}
} else {
$totaldocSize = round($totaldocSize, 2) . 'GB';
}
} else {
$totaldocSize = round($totaldocSize, 2) . 'MB';
}
} else {
$totaldocSize = round($totaldocSize, 2) . 'KB';
}
// die();
$result1 .= "," . ", Total Files = " . $totalFiles . ", Total Pages = " . $totalPages . ", Total Size = " . $totaldocSize . ",\n";
$result1 = str_replace("\r", "", $result1);

if ($result1 == "") {
//$result1 = "\nNo Record(s) Found!\n";                        
}
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=exported_upload_report.csv");
header("Pragma: no-cache");
header("Expires: 0");
print "$header1\n$result1";
}
if ($selectFormat == "PDF") {
$today = date("d-m-Y");
require('./wordwrap.php');

$width = 0;
$widthCell = array();
$headers = array();
$width += 12;
$headers[] = 'S. No.';
$widthCell[] = 12;
$width += 50;
$headers[] = 'Storage';
$widthCell[] = 50;
$width += 28;
$headers[] = 'No. of Files';
$widthCell[] = 28;
$width += 28;
$headers[] = 'No. of Pages';
$widthCell[] = 28;
$width += 19.2;
$headers[] = 'Storage Size(MB)';
$widthCell[] = 19.2;
$width += 22;
$headers[] = 'Uploaded On';
$widthCell[] = 22;

$pdf = new PDF_MC_Table('P', 'mm', array(297, 210));

$pdf->SetMargins(25.4, 10, 25.4);
$pdf->SetAutoPageBreak(TRUE, 12.7);
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 9);

$pdf->SetWidths($widthCell);
$pdf->Row($headers);
$i = 1;
$totalFiles = 0;
$totalPages = 0;
$totalfiles = 0;
$totalPages = 0;
while ($rwuploadrpt = mysqli_fetch_assoc($uploadrept)) {
$totalFiles += $rwuploadrpt['no_of_file'];
$totalPages += $rwuploadrpt['no_of_pages'];
$totaldocSize += $rwuploadrpt['file_size'];
$data = array();
$pdf->SetFont('Arial', '', 9);
$data[] = $i;
$data[] = $rwuploadrpt['sl_name'];
$data[] = $rwuploadrpt['no_of_file'];
$data[] = $rwuploadrpt['no_of_pages'];

$data[] = formatSizeUnits($rwuploadrpt['file_size']);
$data[] = (!empty($rwuploadrpt['dateposted'])) ? date("d-m-Y", strtotime($rwuploadrpt['dateposted'])) : '';
$pdf->Row($data);
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
$totaldocSize = round($totaldocSize, 2) . 'TB';
}
} else {
$totaldocSize = round($totaldocSize, 2) . 'GB';
}
} else {
$totaldocSize = round($totaldocSize, 2) . 'MB';
}
} else {
$totaldocSize = round($totaldocSize, 2) . 'KB';
}
$footers = array();
$width += 50;
$footers[] = ' ';
$widthCell[] = 50;
$width += 50;
$footers[] = 'Total';
$widthCell[] = 50;
$width += 50;
$footers[] = $totalFiles;
$widthCell[] = 50;
$width += 50;
$footers[] = $totalPages;
$widthCell[] = 50;
$width += 50;
$footers[] = $totaldocSize;
$widthCell[] = 50;
$width += 50;
$footers[] = '';
$widthCell[] = 50;
$pdf->SetFont('Arial', 'B', 9);

$pdf->SetWidths($widthCell);
$pdf->Row($footers);

$pdf->Output();

header("Content-Type: application/pdf");
header("Cache-Control: no-cache");
header("Accept-Ranges: none");
header("Content-Disposition: attachment; filename=\"export.pdf\"");
}
}


function findAllSubfolder($db_con, $SlIds) {
global $folderperms;
$sllevel = mysqli_query($db_con, "select * from tbl_storage_level where sl_id in($SlIds) order by sl_name asc");
while ($rwfolderperm = mysqli_fetch_assoc($sllevel)) {
$folderperms[] = $rwfolderperm['sl_id'];

$sllevel1 = mysqli_query($db_con, "select * from tbl_storage_level where sl_parent_id='" . $rwfolderperm['sl_id'] . "' order by sl_name asc");

if (mysqli_num_rows($sllevel1) > 0) {
$childarray = array();
while ($rowCh = mysqli_fetch_assoc($sllevel1)) {
$childarray[] = $rowCh['sl_id'];
}
$childIds = implode(",", $childarray);
findAllSubfolder($db_con, $childIds);
}
}

return $folderperms;
}
?>