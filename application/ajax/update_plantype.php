<?php
require_once '../../sessionstart.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout");
}

require_once '../config/database.php';
//for user role
$id = $_POST['id'];
$qry = mysqli_query($db_con, "select * from tbl_plantype where plantype='$id'");
$row = mysqli_fetch_assoc($qry);
?>
<div class="row">
    <div class="form-group col-md-6">
        <label>Number Of User<span style="color:red;">*</span></label>
        <input type="text" name="nouser" required class="form-control" id="groupName" placeholder="Number Of User" value="<?= $row['no_users'] ?>" maxlength="10">
    </div>
    <div class="form-group col-md-6">
        <label>Total Memory<span style="color:red;">*</span></label>
        <input type="text" name="tmemory" required class="form-control" id="groupName" placeholder="Total Memory In GB"  value="<?= $row['memory_size'] ?>" maxlength="10">
    </div>
    <input type="hidden" value="<?= $id ?>" name="pid">
</div>