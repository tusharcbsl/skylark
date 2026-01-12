<?php
require_once '../../sessionstart.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout");
}
if (isset($_SESSION['lang'])) {
     $file = "../../".$_SESSION['lang'].".json";
 } else {
     $file = "../../English.json";
 }
 $data = file_get_contents($file);
 $lang = json_decode($data, true);
require './../config/database.php';
//for user role

$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

$rwgetRole = mysqli_fetch_assoc($chekUsr);

// echo $rwgetRole['dashboard_mydms']; die;
if ($rwgetRole['view_recycle_bin'] != '1') {
    header('Location: ../../index');
}

$id = $_POST['ID'];
$RecyName = mysqli_query($db_con, "SELECT * FROM `tbl_document_master` where doc_id = '$id';") or die("Error in dd" . mysqli_error($db_con));
$rwRecyName = mysqli_fetch_assoc($RecyName) or die("Error in file fetch" . mysqli_error($db_con));
?>
<div class="row">
<p class="text-danger m-l-15 m-t-10"><?php echo $lang['r_u_Sure_Want_to_Restore_This_Document']; ?> </p>
<p class="text-primary m-l-15 m-t-10"><?php echo $lang['document_name']; ?> : <?php echo $rwRecyName['old_doc_name']; ?></p>
   <input type="hidden" name="DocId" value="<?php echo $rwRecyName['doc_id']; ?>">
</div> 

