<?php
require_once '../../sessionstart.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout.php");
}
require_once '../config/database.php';
if (isset($_SESSION['lang'])) {
    $file = "../../" . $_SESSION['lang'] . ".json";
} else {
    $file = "../../English.json";
}
//for user role
$data = file_get_contents($file);
$lang = json_decode($data, true);



$doc_id = preg_replace("/[^0-9 ]/", "", $_POST['doc_id']);
//$lockdoc_id = $_POST['lockdoc_id'];
$lock_req_docid = preg_replace("/[^0-9 ]/", "", $_POST['lock_req_docid']);
$useridsinfo = array();
mysqli_set_charset($db_con, "utf8");
$lockqry = mysqli_query($db_con, "SELECT * FROM `tbl_locked_file_master` WHERE doc_id='$doc_id' and is_active='1'");
while ($locdata = mysqli_fetch_assoc($lockqry)) {
    array_push($useridsinfo, $locdata['user_id']);
}
?>
<link href="./assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<link href="assets/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" />
<link href="assets/plugins/multiselect/css/multi-select.css"  rel="stylesheet" type="text/css" />
<script type="text/javascript" src="assets/plugins/multiselect/js/jquery.multi-select.js"></script>
<div class="row">
    <label class="text-primary"><?php echo $lang['sh_lock_file']; ?> </label>
    <select class="select2 select2-multiple" data-live-search="true" multiple data-placeholder="<?php echo $lang['sh_lock_file']; ?>" name="userid[]">
        <?php
        $user = mysqli_query($db_con, "select * from tbl_user_master  order by first_name,last_name asc");
        while ($rwUser = mysqli_fetch_assoc($user)) {
            if ($rwUser['user_id'] != 1 && $rwUser['user_id'] != $_SESSION['cdes_user_id']) {
                if (in_array($rwUser['user_id'], $useridsinfo)) {
                    echo '<option value="' . $rwUser['user_id'] . '" selected>' . $rwUser['first_name'] . ' ' . $rwUser['last_name'] . '</option>';
                } else {
                    echo '<option value="' . $rwUser['user_id'] . '" >' . $rwUser['first_name'] . ' ' . $rwUser['last_name'] . '</option>';
                }
            }
        }
        ?>
    </select>
    <input type="hidden" id="lock_docid" name="lockdoc_id" value="<?php echo $doc_id; ?>">
    <input type="hidden" id="lock_reqid" name="lock_req_docid" value="<?php echo $lock_req_docid; ?>">
</div>
<script type="text/javascript" src="assets/plugins/multiselect/js/jquery.multi-select.js"></script>
<script src="assets/js/jquery.core.js"></script>

<script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
<script src="assets/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
<script type="text/javascript">
    $(".select2").selectpicker();
</script>
