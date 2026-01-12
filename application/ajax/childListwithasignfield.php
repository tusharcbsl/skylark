
<?php
require_once '../../sessionstart.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout.php");
}
require_once '../config/database.php';

     //for user role
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
   if($rwgetRole['assign_metadata'] != '1'){
   header('Location: ../../index');
   }

$slID = $_POST['sl_id'];
?>
<div class="form-group row" >
    <div class="col-md-2">
        <label for="userName"><?php echo $lang['Slt_Node'];?></label>
    </div>
    <div class="col-md-4">
   <select class="form-control" id="child_level" name="childName">
    <option selected disabled style="background: #808080; color: #121213;"><?php echo $lang['Select_Child'];?></option>
<?php
$depth = mysqli_query($db_con, "select sl_name,sl_id from tbl_storage_level where sl_parent_id='$slID'") or die('Error:' . mysqli_error($db_con));
//echo'<option selected disabled>--select child--</option>';
while ($rwDepth = mysqli_fetch_assoc($depth)) {
    echo '<option value="' . $rwDepth['sl_id'] . '">' . $rwDepth['sl_name'] . '</option>';
}
?>
        </select>
    </div>
</div>
<div class="form-group" id="multiselect">
    <div class="col-md-2">
        <label for="userName"><?php echo $lang['List_of_Fields'];?></label>
    </div>
    <div class="col-md-4" >
        <select multiple="multiple" class="multi-select" id="my_multi_select1" name="my_multi_select1[]" data-plugin="multiselect">
<?php
$arrarMeta = array();
$metas = mysqli_query($db_con, "select * from tbl_metadata_to_storagelevel where sl_id='$slID'");
while ($metaval = mysqli_fetch_assoc($metas)) {
    array_push($arrarMeta, $metaval['metadata_id']);
}
$meta = mysqli_query($db_con, "select * from tbl_metadata_master order by field_name asc");
while ($rwMeta = mysqli_fetch_assoc($meta)) {
    if (in_array($rwMeta['id'], $arrarMeta)) {
        echo '<option value="' . $rwMeta['id'] . '" selected>' . $rwMeta['field_name'] . '</option>';
    } else {
        echo '<option value="' . $rwMeta['id'] . '">' . $rwMeta['field_name'] . '</option>';
    }
}
?>
        </select>
    </div>
</div>
<script type="text/javascript" src="assets/plugins/multiselect/js/jquery.multi-select.js"></script>
<script src="assets/js/jquery.core.js"></script>
<script>
    $("#child_level").change(function () {
        var slId = $(this).val();
        //alert(slId);
        $.post("application/ajax/metaList.php", {sl_id: slId}, function (result, status) {
            if (status == 'success') {
                $("#multiselect").html(result);
            }
            //  alert(result);
        });
    });
</script>