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

if ($rwgetRole['edit_email_credential'] != '1') {
    header('Location: ../../index');
}
?>
<?php
if (isset($_SESSION['lang'])) {
    $file = "../../" . $_SESSION['lang'] . ".json";
} else {
    $file = "../../English.json";
}
//for user role
$data = file_get_contents($file);
$lang = json_decode($data, true);

$id = $_POST['ID'];
$retval = mysqli_query($db_con, "SELECT * FROM `tbl_email_configuration_credential` WHERE id='$id'") or die('Could not get data: ' . mysqli_error($db_con));
$rwgetdata = mysqli_fetch_assoc($retval);
?>

<div class="row">
    <div class="col-sm-12">

        <div class="form-group">
            <label class="text-weight"> <?= $lang['host_name']; ?><span class="text-alert">*</span></label>
            <input type="text" class="form-control" value="<?php echo $rwgetdata['host_name']; ?>" placeholder="<?= $lang['host_name']; ?>" name="hostname" maxlength="30" required="" />
        </div>
        <div class="form-group">
            <label class="text-weight"> <?= $lang['port_number']; ?><span class="text-alert">*</span></label>
            <input type="text" class="form-control" value="<?php echo $rwgetdata['port_number']; ?>" placeholder="<?= $lang['port_number']; ?>" name="portnumber" maxlength="10" required="" />
        </div>
        <div class="form-group">
            <label class="text-weight"> <?= $lang['username']; ?><span class="text-alert">*</span></label>
            <input type="text" class="form-control" value="<?php echo base64_decode($rwgetdata['username']); ?>" placeholder="<?= $lang['username']; ?>" name="username" maxlength="50" required="" />
        </div>
        <div class="form-group">
            <label class="text-weight"> <?= $lang['Password']; ?><span class="text-alert">*</span></label>
            <input type="text" class="form-control" value="<?php echo base64_decode($rwgetdata['password']); ?>" placeholder="<?= $lang['Password']; ?>" name="pwd" maxlength="40" required=""/>
        </div>
        <div class="form-group">
            <label class="text-weight"> <?= $lang['setFrom']; ?><span class="text-alert">*</span></label>
            <input type="text" class="form-control" value="<?php echo $rwgetdata['setfrom']; ?>" placeholder="<?= $lang['setFrom']; ?>" name="setform" required="" maxlength="30" />
        </div>
    </div>
</div> 

<input type="hidden" name="id" value="<?php echo $rwgetdata['id']; ?>">

<script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('form').parsley();
        $('#groupName').on("cut copy paste", function (e) {
            e.preventDefault();
        });
    });
    $(".select3").selectpicker();
</script>
<script type="text/javascript">

    $('.specialchaecterlock').keyup(function ()
    {
        var groupName = $(this).val();
        re = /[`1234567890~!@#$%^&*()|+\=?;:'",.<>\{\}\[\]\\\/]/gi;
        var isSplChar = re.test(groupName);
        if (isSplChar)
        {
            var no_spl_char = groupName.replace(/[`~!@#$%^&*()|+\=?;:'",.<>\{\}\[\]\\\/]/gi, '');
            $(this).val(no_spl_char);
        }
    });
</script> 