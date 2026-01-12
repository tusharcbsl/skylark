<?php
require_once '../../sessionstart.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout");
}
if (isset($_SESSION['lang'])) {
    $file = "../../" . $_SESSION['lang'] . ".json";
} else {
    $file = "../../English.json";
}
$data = file_get_contents($file);
$lang = json_decode($data, true);
require_once '../config/database.php';
?>
<link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<?php
//for user role
$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

$rwgetRole = mysqli_fetch_assoc($chekUsr);

// echo $rwgetRole['dashboard_mydms']; die;
if ($rwgetRole['workflow_initiate_file'] != '1') {
    header('Location: ../../index');
}

$parentId = preg_replace("/[^0-9 ]/", "", $_POST['parentId']);
$level = preg_replace("/[^0-9 ]/", "", $_POST['levelDepth']);

$sl_id = preg_replace("/[^0-9 ]/", "", $_POST['sl_id']);

echo "<input type='hidden' value='$parentId' name='lastMoveId' />";
echo "<input type='hidden' value='$level' name='lastMoveIdLevel' />";

$childName = mysqli_query($db_con, "select * from tbl_storage_level where sl_parent_id='$parentId' AND sl_id != '$sl_id' and delete_status=0 order by sl_name asc") or die('Error in parent id:' . mysqli_error($db_con));

if (mysqli_num_rows($childName) == 0) {
    
} else {
    ?>
    <div class="col-md-3 form-group">
        <select class="form-control select3" name="moveToChildId<?= $level; ?>" id="childMoveLevel<?= $level; ?>" style="width:100%">
            <option selected disabled> <?= $lang['Select_Child']; ?></option>
            <?php
            while ($rwchildName = mysqli_fetch_assoc($childName)) {
                echo '<option value="' . $rwchildName['sl_id'] . '">' . $rwchildName['sl_name'] . '</option>';
            }
            ?>
        </select></div>

    <div id="subChild<?php echo $level; ?>">

    </div>


<?php } ?>
<script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
<script>

    $("#childMoveLevel<?php echo $level; ?>").change(function () {
        var lbl = $(this).val();
        //alert(lbl);
        $.post("application/ajax/uploadWorkFlow.php", {parentId: lbl, levelDepth:<?php echo $level + 1; ?>, sl_id:<?php echo $sl_id; ?>}, function (result, status) {
            if (status == 'success') {
                $("#subChild<?php echo $level; ?>").html(result);
                // alert($level);

            }
        });
    });
    $(".select3").select2();
</script>

