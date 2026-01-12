<?php
require_once '../../sessionstart.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout");
}
if (isset($_SESSION['lang'])) {
    $file = "../../" . $_SESSION['lang'] . ".json";
} else {
    $file = "../../English.json";
}
$data = file_get_contents($file);
$lang = json_decode($data, true);
require './../config/database.php';

require_once '../pages/function.php';
mysqli_set_charset($db_con, "utf8");

$langDetail = mysqli_query($db_con, "select * from tbl_language where lang_name='" . $_SESSION['lang'] . "'") or die('Error : ' . mysqli_error($db_con));
$langDetail = mysqli_fetch_assoc($langDetail);

if (intval($_POST['ID']))
{
    $id = preg_replace("/[^0-9 ]/", "", $_POST['ID']);
    mysqli_set_charset($db_con, "utf8");
    $userStorage = mysqli_query($db_con, "select first_name,last_name,storage_limit,view_file_limit,upload_file_limit FROM tbl_user_master where user_id = '$id'");
    $rwUserStorage = mysqli_fetch_assoc($userStorage);
    $result['storage_limit']=(($rwUserStorage['storage_limit']/1024)?$rwUserStorage['storage_limit']/1024:'NOT SET');
    $result['view_file_limit']=(($rwUserStorage['view_file_limit']/1024)?$rwUserStorage['view_file_limit']/1024:'NOT SET');
    $result['upload_file_limit']=(($rwUserStorage['upload_file_limit']/1024)?$rwUserStorage['upload_file_limit']/1024:'NOT SET');
    echo(json_encode($result));
}

?>
