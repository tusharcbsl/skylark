<!DOCTYPE html>
<?php
session_start();
require_once '../sessionstart.php';
require_once '../application/config/database.php';

$pgn=intval($_GET['pn']);
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout.php");
}
//  require_once '../application/pages/head.php';
//for user role
$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

$rwgetRole = mysqli_fetch_assoc($chekUsr);

$uid = base64_decode(urldecode($_GET['id']));
if ($uid != $_SESSION['cdes_user_id']) {
    // header('Location:../index');
}
if ($rwgetRole['pdf_annotation'] != '1') {
    header('Location: ../index');
}
$id1 = base64_decode(urldecode($_GET['id1'])); //doc_id
 
$id = base64_decode(urldecode($_GET['id'])); //doc asign id
