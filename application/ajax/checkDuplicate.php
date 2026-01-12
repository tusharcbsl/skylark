<?php
require_once '../../sessionstart.php';
if(!isset($_SESSION['cdes_user_id'])){
    header("location:../../logout.php");
}
require_once '../config/database.php';

     //for user role

$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:'. mysqli_error($db_con));
   
   $rwgetRole = mysqli_fetch_assoc($chekUsr);
  
  // echo $rwgetRole['dashboard_mydms']; die;
   if($rwgetRole['move_storage_level'] != '1'){
   header('Location: ../../index');
   }


$parentId = preg_replace("/[^0-9 ]/", "", $_POST['parentId']); 

$level=preg_replace("/[^0-9 ]/", "", $_POST['levelDepth'])+1; 
$folder=$_POST['folder']; 
$checkDupl = mysqli_query($db_con,"select * from tbl_storage_level where sl_parent_id='$parentId' AND sl_depth_level='$level' and sl_name='$folder'") or die('Error in parent id:'.mysqli_error($db_con));
if(mysqli_num_rows($checkDupl)>0){
    echo '1';
}else{
    echo '0';
}
?>

