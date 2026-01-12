<?php
require_once '../../sessionstart.php';
//require_once '../../loginvalidate.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout");
}
require './../config/database.php';

//for user role
$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));
$rwgetRole = mysqli_fetch_assoc($chekUsr);

if (isset($_SESSION['lang'])) {
    $file = "../../" . $_SESSION['lang'] . ".json";
} else {
    $file = "../../English.json";
}
//for user role
$data = file_get_contents($file);
$lang = json_decode($data, true);
if ($rwgetRole['view_faq'] != '1') {
    ?>
    <p class="text-danger"><?php echo $lang['you_donot_have_any_permission']; ?></p>
    <?php
} else {
    $id = $_POST['ID'];
    $ques = mysqli_query($db_con, "select * from `tbl_desc_answ` where id='$id'") or die('error in table' . mysqli_error($db_con));
    $rwques = mysqli_fetch_assoc($ques);

    if (mysqli_num_rows($ques) > 0) {
        ?>
        <h5><strong><?php echo $lang['ques']; ?> : </strong> <?php echo $rwques['question']; ?></h5>
        <p><label><strong><?php echo $lang['ans']; ?> : </strong></label> <?php echo $rwques['answer']; ?></p>
        <?php
    } else {
        ?>
        <label><strong class="text-danger"><?php echo $lang['Who0ps!_No_Records_Found']; ?></strong></label>
            <?php
        }
        ?>

<?php } ?>