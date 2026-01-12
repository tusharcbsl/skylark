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

//for user role

$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

$rwgetRole = mysqli_fetch_assoc($chekUsr);

// echo $rwgetRole['dashboard_mydms']; die;
if ($rwgetRole['workflow_initiate_file'] != '1') {
    header('Location: ../../index');
}

$sid = preg_replace("/[^0-9 ]/", "", $_POST['wsid']);
?>
<link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<link href="assets/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" />  
<label class="text-weight"><?php echo $lang['Slt_Tsk']; ?> </label>     
<select class="select2" data-live-search="true" data-style="btn-white" name="wtsk">
    <option selected disabled><?php echo $lang['Slt_Tsks']; ?></option>
<?php
mysqli_set_charset($db_con,"utf8");
$selTsk = mysqli_query($db_con, "select * from tbl_task_master where step_id='$sid' ORDER BY task_order ASC") or die('Error:' . mysqli_error($db_con));
while ($rwselTsk = mysqli_fetch_assoc($selTsk)) {
    ?> 
        <option value="<?php echo $rwselTsk['task_id']; ?>"><?php echo $rwselTsk['task_name']; ?></option>
        <?php
    }
    ?>
</select>

<script src="assets/plugins/moment/moment.js"></script>
<script src="assets/plugins/timepicker/bootstrap-timepicker.js"></script>
<script src="assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>
<script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script src="assets/plugins/clockpicker/js/bootstrap-clockpicker.min.js"></script>
<script src="assets/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
<script type="text/javascript" src="assets/plugins/jquery-quicksearch/jquery.quicksearch.js"></script>
<script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
<script src="assets/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
<script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>

<script src="assets/js/jquery.core.js"></script>
<script src="assets/js/jquery.app.js"></script>

<script src="assets/pages/jquery.form-pickers.init.js"></script>
<script type="text/javascript">
    jQuery(document).ready(function () {
        $('.select2').selectpicker();
        $(":file").filestyle({input: false});
    });


</script>
