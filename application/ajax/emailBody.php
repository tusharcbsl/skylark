<?php
require_once '../../sessionstart.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout");
}

if (isset($_SESSION['lang'])){
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
if ($rwgetRole['mail_lists'] != '1') {
    header('Location: ../../index');
}

$id = $_POST['ID'];
$ViewEmailB = mysqli_query($db_con, "SELECT body_email FROM `tbl_my_mails` where id = '$id';") or die("Error in body mail" . mysqli_error($db_con));
$rwViewEmailB = mysqli_fetch_assoc($ViewEmailB) or die("Error in edit" . mysqli_error($db_con));
?>
<div class="row">
    <div class="form-group">
        <div><?php echo $rwViewEmailB['body_email']; ?></div>
    </div>
</div> 
  