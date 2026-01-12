<?php

require_once '../../sessionstart.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout");
}

require './../config/database.php';
//for user role

if (isset($_SESSION['lang'])) {
    $file = "../../" . $_SESSION['lang'] . ".json";
} else {
    $file = "../../English.json";
}
//for user role
$data = file_get_contents($file);
$lang = json_decode($data, true);

$flname = $_POST['FVAL'];
$fid = $_POST['FID'];
$fid = (!empty($fid) ? $fid : "");
$getMetaName = mysqli_query($db_con, "select data_type from tbl_metadata_master where field_name='$flname'") or die('Error:' . mysqli_error($db_con));
$rwgetMetaName = mysqli_fetch_assoc($getMetaName);
//echo $rwgetMetaName['data_type']; die;
$datas = "";
if ($rwgetMetaName['data_type'] == 'date') {
    $datas = 'a';
} else {
    $datas = 'b';
}
echo "test~" . $datas."~".$fid;
?>
