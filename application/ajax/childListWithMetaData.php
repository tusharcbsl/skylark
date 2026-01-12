<?php
require_once '../../sessionstart.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout.php");
}
require_once '../config/database.php';

//for user role
if (isset($_SESSION['lang'])) {
    $file = "../../" . $_SESSION['lang'] . ".json";
} else {
    $file = "../../English.json";
}
//for user role
$data = file_get_contents($file);
$lang = json_decode($data, true);
$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

$rwgetRole = mysqli_fetch_assoc($chekUsr);

// echo $rwgetRole['dashboard_mydms']; die;
if ($rwgetRole['metadata_search'] != '1') {
    header('Location: ./index');
}


$slID = preg_replace("/[^0-9 ]/", "", $_POST['sl_id']);
?>


 <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />  

 <select  class="select2" data-style="btn-white" id="kk" name="metadata[]" required>
    <option disabled selected><?php echo $lang['Select_Metadata'] ?></option>
    <option value="old_doc_name"><?php echo $lang['FileName']; ?></option>
    <option value="noofpages"><?php echo $lang['No_Of_Pages']; ?></option>
    
    <?php
   $arrarMeta = array();
    $metas = mysqli_query($db_con, "select * from tbl_metadata_to_storagelevel where sl_id='$slID'");
    while ($metaval = mysqli_fetch_assoc($metas)) {
        //array_push($arrarMeta, $metaval['metadata_id']);
        //echo "select * from tbl_metadata_master WHERE id='$metaval[metadata_id]' order by field_name asc";
        $meta = mysqli_query($db_con, "select * from tbl_metadata_master WHERE id='$metaval[metadata_id]' order by field_name asc");
        $rwMeta = mysqli_fetch_assoc($meta);

        if ($rwMeta['field_name'] != 'filename') {
            if ($_GET['metadata'][$j] == $rwMeta['field_name']) {
                echo '<option selected>' . $rwMeta['field_name'] . '</option>';
            } else {
                echo '<option>' . $rwMeta['field_name'] . '</option>';
            }
            $metadatacount++;
        }
    }
    ?>
</select>
<script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
<script>
    jQuery(document).ready(function () {
        $('.select2').select2();

    });

</script>





