<?php
require_once '../../sessionstart.php';
if(!isset($_SESSION['cdes_user_id'])){
    header("location:../../logout");
}

require_once '../config/database.php';
if (isset($_SESSION['lang'])){
     $file = "../../".$_SESSION['lang'].".json";
 } else {
     $file = "../../English.json";
 }
 //for user role
$data = file_get_contents($file);
 $lang = json_decode($data, true);
$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:'. mysqli_error($db_con));
   
   $rwgetRole = mysqli_fetch_assoc($chekUsr);
  
  // echo $rwgetRole['dashboard_mydms']; die;
   if($rwgetRole['storage_auth_plcy'] != '1'){
   header('Location: ../../index');
   }

 $lbl=$_POST['level'];
$depth=mysqli_query($db_con,"select sl_name,sl_id from tbl_storage_level where sl_depth_level='$lbl' and delete_status=0") or die('Error:'.mysqli_error($db_con));
echo '<option selected disabled style="background: #808080; color: #121213;">'.$lang['Select_Parent'].'</option>';
while($rwDepth= mysqli_fetch_assoc($depth)){
    echo '<option value="'.$rwDepth['sl_id'].'">'.$rwDepth['sl_name'].'</option>';
}

?>

