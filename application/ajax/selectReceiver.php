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
//for user role
$data = file_get_contents($file);
$lang = json_decode($data, true);
require_once '../config/database.php';
$sameGroupIDs = array();
mysqli_set_charset($db_con, "utf8");
$group = mysqli_query($db_con, "select * from tbl_bridge_grp_to_um where find_in_set('$_SESSION[cdes_user_id]',user_ids)") or die('Error' . mysqli_error($db_con));
while ($rwGroup = mysqli_fetch_assoc($group)) {
    $sameGroupIDs[] = $rwGroup['user_ids'];
}
$sameGroupIDs = array_unique($sameGroupIDs);
sort($sameGroupIDs);
$sameGroupIDs = implode(',', $sameGroupIDs);
?>
<select class="form-control selectpicker" data-live-search="true" name="review[]" id="review" required>
    <option value=""><?php echo $lang['Select_User']; ?></option>
    <?php
    $user = mysqli_query($db_con, "select * from tbl_user_master where user_id in($sameGroupIDs) order by first_name, last_name asc");
    while ($rwUser = mysqli_fetch_assoc($user)) {
        if ($rwUser['user_id'] != 1 && $_SESSION['cdes_user_id'] != $rwUser['user_id']) {
            ?>
            <option value="<?php echo $rwUser['user_id']; ?>"><?php echo $rwUser['first_name'] . ' ' . $rwUser['last_name']; ?></option>

            <?php
        }
    }
    ?>
</select>
<script>
    $('.selectpicker').selectpicker();
</script>