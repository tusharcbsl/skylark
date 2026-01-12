<?php
require_once '../../sessionstart.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout");
}
require './../config/database.php';
require './../pages/function.php';

$slpermIds = array();
$perm = mysqli_query($db_con, "select * from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]' group by sl_id");
while ($rwPerm = mysqli_fetch_assoc($perm)) {
    $slpermIds[] = $rwPerm['sl_id'];
}
$slpermIdes = implode(',', $slpermIds);

$slid = $_GET['slid'];

$sllevelTree = mysqli_query($db_con, "select * from tbl_storage_level where sl_id in($slpermIdes) and delete_status=0 order by sl_name asc");

//$sllevelTree = mysqli_query($db_con, "select * from tbl_storage_level where sl_id in($slpermIdes) and delete_status=0 and (sl_parent_id is NULL or sl_parent_id=0) order by sl_name asc");

$storageArray = [];
$i=0;
while($row = mysqli_fetch_assoc($sllevelTree)){
	
	$isChild  = mysqli_query($db_con, "select sl_id from tbl_storage_level where sl_parent_id='".$row['sl_id']."' and delete_status=0");
	
	$childExist = mysqli_num_rows($isChild);
	
	if($childExist){
		
		$url ='storage?id=' . urlencode(base64_encode($row['sl_id'])) . '';
	}else{
		$url ='storageFiles?id=' . urlencode(base64_encode($row['sl_id'])) . '';
	}
	$storageArray[$i]['id'] = $row['sl_id'];
	$storageArray[$i]['parent'] = "#";
	$storageArray[$i]['text'] = $row['sl_name']; 
	
	if($row['sl_id']==$slid){
		$storageArray[$i]['state']['opened'] = true;
		$storageArray[$i]['state']['selected'] = true; 
		
	}else{
		$storageArray[$i]['state']['opened'] = true;
	}
	
	$storageArray[$i]['children'] = (mysqli_num_rows($isChild)>0)?true:false; 
	$storageArray[$i]['a_attr']['href'] = $url; 
	$i++;
}

header('Content-Type: application/json');
echo json_encode($storageArray);

?>