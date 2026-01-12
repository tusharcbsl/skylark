<?php
require_once '../../sessionstart.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout.php");
}
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout.php");
}
if (isset($_SESSION['lang'])) {
    $file = "../../" . $_SESSION['lang'] . ".json";
} else {
    $file = "../../English.json";
}
$data = file_get_contents($file);
$lang = json_decode($data, true);
require_once '../config/database.php';
require_once '../pages/function.php';
	$slid=$_POST['slid'];
	
	$slidsArray = findsubfolder($slid, $db_con);
	$slids = implode(',', $slidsArray);
	
	$sql = "select sum(doc_size) as total, count(doc_name) as count,sum(noofpages) as numpages from tbl_document_master where  substring_index(doc_name,'_',1) in($slids) and flag_multidelete=1";
					 
	$contFiles = mysqli_query($db_con, $sql) or die('Error :' . mysqli_error($db_con));
	
	$totalFiles = array();
	$totalFiles["totalFolder"] = count($slidsArray);
	$totalFiles["files"]=0;
	$totalFiles["fileSize"]=0;
	$totalFiles["numPages"]=0;
	
	if(mysqli_num_rows($contFiles)>0){
		$rwcontFile = mysqli_fetch_assoc($contFiles);
		$totalFSize1 = $rwcontFile['total'];
		$totalFSize = round($totalFSize1 / (1024 * 1024), 2);
		$totalFiles["files"] = $rwcontFile['count'];
		$totalFiles["fileSize"] = $totalFSize;
		$totalFiles["numPages"] = $rwcontFile['numpages'];
	}

	echo $lang['folders'] . ' = ' . ($totalFiles["totalFolder"] - 1) . ',' . $lang['files'] . '= ' . $totalFiles["files"] . ', ' . $lang['Total_size'] . '= ' . (($totalFiles['fileSize'] > 999) ? round($totalFiles['fileSize'] / 1024, 2) : $totalFiles['fileSize']) . (($totalFiles['fileSize'] > 999) ? $lang['GB'] : $lang['MB']) . ' & ' . $lang['pages'] . '=' . $totalFiles["numPages"]; 
?>
                                        