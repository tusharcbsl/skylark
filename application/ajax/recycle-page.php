<?php
require_once '../../sessionstart.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout");
}
require './../config/database.php';
require '../../anott/fpdf-function.php';

if (isset($_SESSION['lang'])) {
     $file = "../../".$_SESSION['lang'].".json";
 } else {
     $file = "../../English.json";
 }
 $data = file_get_contents($file);
 $lang = json_decode($data, true);

//for user role

$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

$rwgetRole = mysqli_fetch_assoc($chekUsr);

// echo $rwgetRole['dashboard_mydms']; die;
if ($rwgetRole['view_recycle_bin'] != '1') {
    header('Location: ../../index');
}
mysqli_set_charset($db_con,"utf8");	
$id = $_POST['ID'];
$parentDocId = $_POST['parentDocId'];
$RecyName = mysqli_query($db_con, "SELECT old_doc_name,doc_id FROM `tbl_document_master` where doc_id = '$id';") or die("Error in dd" . mysqli_error($db_con));
$rwRecyName = mysqli_fetch_assoc($RecyName) or die("Error in file fetch" . mysqli_error($db_con));

 $resultp =mysqli_query($db_con, "SELECT noofpages FROM `tbl_document_master` where doc_id = '$parentDocId';") or die("Error in dd" . mysqli_error($db_con));
$row = mysqli_fetch_assoc($resultp) or die("Error in file fetch" . mysqli_error($db_con));

?>
<div class="row">
<p class="text-primary"><?php echo $lang['document_name']; ?> : <?php echo $rwRecyName['old_doc_name']; ?></p>
   <input type="hidden" name="DocId" value="<?php echo $rwRecyName['doc_id']; ?>">
</div> 

<label>Select Page No. :</label>
<select name="fpnum" id="fpnum" class="input-sm">
	<?php
	$tfp = $row['noofpages'];
	for ($i = 1; $i <= $tfp; $i++) {
		?>
		<option <?= ($i == intval($_GET['pn']) ? 'selected="selected"' : '') ?> value="<?= $i ?>"><?= $i ?></option>
	<?php } ?>    
</select>
<label>Position : </label>
<select name="fpos" id="fpos" class="input-sm">
	<option value="a">After</option>
	<option value="b">Before</option>

</select><br>

