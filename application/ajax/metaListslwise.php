<?php
require_once '../../sessionstart.php';
if(!isset($_SESSION['cdes_user_id'])){
    header("location:../../logout.php");
}
if (isset($_SESSION['lang'])){
     $file = "../../".$_SESSION['lang'].".json";
 } else {
     $file = "../../English.json";
 }
 $data = file_get_contents($file);
 $lang = json_decode($data, true);
require_once '../config/database.php';

         //for user role

$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:'. mysqli_error($db_con));
   
   $rwgetRole = mysqli_fetch_assoc($chekUsr);
  
  // echo $rwgetRole['dashboard_mydms']; die;
   if($rwgetRole['metadata_search'] != '1'){
   header('Location: ./index');
   }


$slID=$_POST['sl_id'];
$cval=$_POST['cval'];

?>
  
<div class="col-md-2">
    <label for="userName">Select Metadata</label>
</div>
<div class="col-md-4">
    
 <select class="form-control" id="my_multi_select1" name="metadata" required>
     <option value="" disabled selected>select metadata</option>
                                                 <?php
                                                 $arrarMeta=array();
                                                 $metas=mysqli_query($db_con,"select * from tbl_metadata_to_storagelevel where sl_id='$slID'");
                                                 while($metaval= mysqli_fetch_assoc($metas)){
                                                     array_push($arrarMeta, $metaval['metadata_id']);
                                                 }
                                                 $meta= mysqli_query($db_con, "select * from tbl_metadata_master");
                                                 while($rwMeta= mysqli_fetch_assoc($meta)){
                                                     if(in_array($rwMeta['id'], $arrarMeta)){
                                                         if($cval==$rwMeta['field_name']){
                                                             echo '<option value="'.$rwMeta['id'].'" selected>'.$rwMeta['field_name'].'</option>';
                                                         }else{
                                                             echo '<option value="'.$rwMeta['id'].'" >'.$rwMeta['field_name'].'</option>';
                                                         }
                                                         
                                                     }
                                                 }
                                                 ?>
                                                 </select>
                                                     </div>

 
        
