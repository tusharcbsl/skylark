<?php
require_once '../../sessionstart.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout.php");
}
if (isset($_SESSION['lang'])) {
    $file = "../../" . $_SESSION['lang'] . ".json";
} else {
    $file = "../../English.json";
}
$data = file_get_contents($file);
$lang = json_decode($data, true);
require_once '../config/database.php';
$docId = $_POST['docId'];
$userId = $_POST['userId'];

$actions = mysqli_query($db_con, "select * from `tbl_document_subscriber` where subscriber_userid='$userId' and subscribe_docid='$docId'") or die('Error:' . mysqli_error($db_con));
$rwactions = mysqli_fetch_assoc($actions);
$actionIds = $rwactions['action_id'];
$actionId = explode(',', $actionIds);
//print_r($actionId);
?>
<link href="assets/plugins/multiselect/css/multi-select.css"  rel="stylesheet" type="text/css" />
<div class="form-group">
    <label for="privilege"><?php echo $lang['select_notification_when']; ?><span style="color:red;">*</span></label>
    <select class="select31 select2-multiple" data-live="true" name="editfileactions[]" multiple="multiple" multiple data-placeholder="<?php echo $lang['chos_actn']; ?>" required="">
        <option value="1" <?php
        if (in_array('1', $actionId)) {
            echo 'selected';
        } else {
            echo "";
        }
        ?>>Delete file</option>  
        <option value="2" <?php
        if (in_array('2', $actionId)) {
            echo 'selected';
        } else {
            echo "";
        }
        ?>>Modify keywords & add versioning file</option>  
        <option value="3" <?php
        if (in_array('3', $actionId)) {
            echo 'selected';
        } else {
            echo "";
        }
        ?>>Sent file in workflow</option>  
        <option value="4" <?php
        if (in_array('4', $actionId)) {
            echo 'selected';
        } else {
            echo "";
        }
        ?>>Sent file for review</option>  
        <option value="5" <?php
        if (in_array('5', $actionId)) {
            echo 'selected';
        } else {
            echo "";
        }
        ?>>Share file with other</option>  
        <option value="6" <?php
        if (in_array('6', $actionId)) {
            echo 'selected';
        } else {
            echo "";
        }
        ?>>Move file to other folder</option>  
        <option value="7" <?php
        if (in_array('7', $actionId)) {
            echo 'selected';
        } else {
            echo "";
        }
        ?>>Copy file to other folder</option>
        <option value="8" <?php
        if (in_array('8', $actionId)) {
            echo 'selected';
        } else {
            echo "";
        }
        ?>>E-mail file outside from DMS</option>
    </select>
</div>
<!--for multiselect-->
<script type="text/javascript" src="assets/plugins/multiselect/js/jquery.multi-select.js"></script>
<script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
<script>
    $(document).ready(function () {
        $('.select31').select2();
        $('form').parsley();

    });
</script>