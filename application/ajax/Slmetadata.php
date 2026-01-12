<?php
require_once '../../sessionstart.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout.php");
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
         //for user role

$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:'. mysqli_error($db_con));
   
   $rwgetRole = mysqli_fetch_assoc($chekUsr);
  
  // echo $rwgetRole['dashboard_mydms']; die;
   if($rwgetRole['metadata_search'] != '1'){
   header('Location: ./index');
   }
 

$slID = $_POST['sl_id']; 
if (intval($slID)) {
?>


 <link href="assets/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" />   

<select  class="selectpicker" data-live-search="true" data-style="btn-white" name="metadata" required>
<option disabled selected><?php echo $lang['Select_Metadata'];?></option>
<?php
$arrarMeta = array();
$metas = mysqli_query($db_con, "select * from tbl_metadata_to_storagelevel where sl_id='$slID'");
while ($metaval = mysqli_fetch_assoc($metas)) {
    array_push($arrarMeta, $metaval['metadata_id']);
}
$meta = mysqli_query($db_con, "select * from tbl_metadata_master order by field_name asc");
while ($rwMeta = mysqli_fetch_assoc($meta)) {
    if (in_array($rwMeta['id'], $arrarMeta)) {
         echo '<option>' . $rwMeta['field_name'] . '</option>';
        } 
}
?>
        </select>


    <script src="assets/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
<script>
   jQuery(document).ready(function () {
        $('.selectpicker').selectpicker();

    });
 
</script>

<?php } ?>     
    
       
                      
              