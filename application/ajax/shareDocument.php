<?php
require_once '../../sessionstart.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout");
}

require './../config/database.php';
//for user role

$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

$rwgetRole = mysqli_fetch_assoc($chekUsr);

// echo $rwgetRole['dashboard_mydms']; die;
if ($rwgetRole['share_file'] != '1') {
    header('Location: ../../index');
}

$ids = preg_replace("/[^0-9 ]/", "", $_POST['DOCIDS']);
$docId = mysqli_query($db_con, "select * from tbl_document_share where doc_ids='$ids'");
while ($rwdocId = mysqli_fetch_assoc($docId)) {
    
}
?>
<div class="row"> 
    <div class="col-md-12"> 
        <div class="form-group">
            <label>Select User</label>
            <select class="select2 select2-multiple" multiple data-placeholder="Select Users" name="userid[]" required>
                <?php
                $sameGroupIDs = array();
                $group = mysqli_query($db_con, "select * from tbl_bridge_grp_to_um where find_in_set('$_SESSION[cdes_user_id]',user_ids)") or die('Error' . mysqli_error($db_con));
                while ($rwGroup = mysqli_fetch_assoc($group)) {
                    $sameGroupIDs[] = $rwGroup['user_ids'];
                }
                $sameGroupIDs = array_unique($sameGroupIDs);
                sort($sameGroupIDs);
                $sameGroupIDs = implode(',', $sameGroupIDs);
                $user = mysqli_query($db_con, "select * from tbl_user_master where user_id in($sameGroupIDs)");
                while ($rwUser = mysqli_fetch_assoc($user)) {
                    if ($rwUser['user_id'] != 1 && $rwUser['user_id'] != $_SESSION['cdes_user_id']) {
                        echo '<option value="' . $rwUser['user_id'] . '">' . $rwUser['first_name'] . ' ' . $rwUser['last_name'] . '</option>';
                    } else {
                        echo '<option value="' . $rwUser['user_id'] . '">' . $rwUser['first_name'] . ' ' . $rwUser['last_name'] . '</option>';
                    }
                }
                ?>
            </select>
        </div>
    </div> 

</div>                                                   
<input type="hidden" name="gid" value="<?php echo $rwGroup['group_id']; ?>">

<script src="assets/js/jquery.min.js"></script>
<script type="text/javascript" src="assets/plugins/jquery-validation/js/jquery.validate.min.js"></script>
<script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
<script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>

<script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>

<script type="text/javascript">
    $(document).ready(function () {

        $(".select2").select2();
    });
</script>