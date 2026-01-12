<?php
require_once '../../sessionstart.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout");
}

require './../config/database.php';
//for user role

$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

$rwgetRole = mysqli_fetch_assoc($chekUsr);

//// echo $rwgetRole['dashboard_mydms']; die;
//if ($rwgetRole['view_recycle_bin'] != '1') {
//    header('Location: ../../index');
//}

$doc_ids = xss_clean($_POST['doc_ids']);
$sl_id = preg_replace("/[^0-9 ]/", "", $_POST['slid']);
$copySlName = mysqli_query($db_con, "SELECT * FROM `tbl_document_master` where doc_id in($doc_ids)") or die("Error in dd" . mysqli_error($db_con));
$rowcopysl_name = mysqli_fetch_assoc($copySlName) or die("Error in file fetch" . mysqli_error($db_con));
?>

