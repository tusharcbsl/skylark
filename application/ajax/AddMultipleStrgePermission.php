<?php
require_once '../../sessionstart.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout");
}
require './../config/database.php';

//for user role
if (isset($_SESSION['lang'])){
     $file = "../../".$_SESSION['lang'].".json";
 } else {
     $file = "../../English.json";
 }
 //for user role
$data = file_get_contents($file);
 $lang = json_decode($data, true);
mysqli_set_charset($db_con,"utf8");	  
$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

$rwgetRole = mysqli_fetch_assoc($chekUsr);
?>
<?php
$slid = $_REQUEST['slid'];
//$sl_ids=explode(',',$slid);
//$page = $_REQUEST['page'];
$perm = mysqli_query($db_con, "select sl_id from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]' group by sl_id");
$slperm = array();
while ($rwPerm = mysqli_fetch_assoc($perm)) {
    $slperm[] = $rwPerm['sl_id'];
}
$sl_perm = implode(',', $slperm);
//echo "SELECT * FROM tbl_storage_level WHERE sl_id IN($sl_perm) AND sl_id NOT IN($slid)";
$sllevel = mysqli_query($db_con, "SELECT * FROM tbl_storage_level WHERE sl_id IN($sl_perm) and delete_status=0 order by sl_name asc");
if (mysqli_num_rows($sllevel) > 0) {
    ?>
    <div class="col-md-2 col-lg-2">
        <label for="userName"><?php echo $lang['Select_Storage']; ?><span style="color:red;">*</span></label>
    </div>
    <div class="col-md-6 col-lg-6 col-sm-6 m-t-10">
        <select class="form-control strg_id select2 select2-multiple" data-live-search="true" multiple  name="slparentName[]"  data-placeholder="<?php echo $lang['Select_Storage']; ?>" required>

            <?php
            while ($rwSllevel = mysqli_fetch_assoc($sllevel)) {
                $level = $rwSllevel['sl_depth_level'];
                $SlId = $rwSllevel['sl_id'];
                findChild($SlId, $level, $SlId, $slid);
            }
            ?>
        </select> 

    </div>


    <?php
} else {
    echo '<script>alert("You don\'nt have any storage permission.")</script>';
}
?>
<?php

function findChild($sl_id, $level, $slperm, $sl_ids) {

    global $db_con;
    $sl_ids = explode(",", $sl_ids);
    if (in_array($sl_id, $sl_ids)) {
        echo '<option selected value="' . $sl_id . '">';
        parentLevel($sl_id, $db_con, $slperm, $level, '');
        echo '</option>';
    } else {
        echo '<option value="' . $sl_id . '">';
        parentLevel($sl_id, $db_con, $slperm, $level, '');
        echo '</option>';
    }
    if (!in_array($sl_id, $sl_ids)) {
        $sl_ids = implode(",", $sl_ids);
        $sql_child = "select * FROM tbl_storage_level WHERE sl_parent_id = '$sl_id'  and delete_status=0  order by sl_name asc";

        $sql_child_run = mysqli_query($db_con, $sql_child) or die('Error:' . mysqli_error($db_con));

        if (mysqli_num_rows($sql_child_run) > 0) {
            //$sl_ids=explode(",",$sl_ids);
            while ($rwchild = mysqli_fetch_assoc($sql_child_run)) {
                //if(!in_array($rwchild['sl_id'],$sl_ids)){
                $child = $rwchild['sl_id'];

                findChild($child, $level, $slperm, $sl_ids);
                //}
            }
        }
    }
}

function parentLevel($slid, $db_con, $slperm, $level, $value) {

    if ($slperm == $slid) {
        $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid'  and delete_status=0  ") or die('Error' . mysqli_error($db_con));
        $rwParent = mysqli_fetch_assoc($parent);

        if ($level < $rwParent['sl_depth_level']) {
            parentLevel($rwParent['sl_parent_id'], $db_con, $slperm, $level, $rwParent['sl_name']);
        }
    } else {
        $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid' and sl_parent_id='$slperm'  and delete_status=0 ") or die('Error' . mysqli_error($db_con));
        if (mysqli_num_rows($parent) > 0) {

            $rwParent = mysqli_fetch_assoc($parent);
            if ($level < $rwParent['sl_depth_level']) {
                parentLevel($rwParent['sl_parent_id'], $db_con, $slperm, $level, $rwParent['sl_name']);
            }
        } else {
            $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid' and delete_status=0") or die('Error' . mysqli_error($db_con));
            $rwParent = mysqli_fetch_assoc($parent);
            $getparnt = $rwParent['sl_parent_id'];
            if ($level <= $rwParent['sl_depth_level']) {
                parentLevel($getparnt, $db_con, $slperm, $level, $rwParent['sl_name']);
            } else {
                
            }
        }
    }


    if (!empty($value)) {
        $value = $rwParent['sl_name'] . '<b> > </b>';
    } else {
        $value = $rwParent['sl_name'];
    }
    echo $value;
}
?>
<link href="assets/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" />  
<script src="assets/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
<script>
    $(".select2").selectpicker();
    $("select.strg_id").change(function () {
        //alert();
        var id = $("select[name='slparentName[]']").map(function () {
            return $(this).val();
        }).get();
        id = id.toString();
        //console.log(id);
        $.post("application/ajax/AddMultipleStrgePermission.php", {slid: id}, function (result, status) {
            if (status == 'success') {
                // debugger;
                $('#selgroup').html(result);

            }
        });
    });
</script>
