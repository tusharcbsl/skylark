<?php
require_once '../../sessionstart.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout");
}

require_once '../config/database.php';

//for user role

$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

$rwgetRole = mysqli_fetch_assoc($chekUsr);

// echo $rwgetRole['dashboard_mydms']; die;
if ($rwgetRole['storage_auth_plcy'] != '1') {
    header('Location: ../../index');
}


$slID = $_POST['sl_id'];

?>
<?php
if (isset($_SESSION['lang'])) {
     $file = "../../".$_SESSION['lang'].".json";
 } else {
     $file = "../../English.json";
 }
 $data = file_get_contents($file);
 $lang = json_decode($data, true);
?>

<div id="childall">
    <div class="col-md-4 no-padding2">
        <div class="form-group">
            <label><?php echo $lang['Slt_Child_Lvl'];?></label>
            <select class="form-control" id="child_level" name="slchildName" onchange="chidepth()">
                <option selected disabled><?php echo $lang['Select_Child'];?></option>
                <?php
                $depth = mysqli_query($db_con, "select sl_name,sl_id from tbl_storage_level where sl_parent_id='$slID' and delete_status=0") or die('Error:' . mysqli_error($db_con));
                //echo'<option selected disabled>--select child--</option>';
                while ($rwDepth = mysqli_fetch_assoc($depth)) {
                    echo '<option value="' . $rwDepth['sl_id'] . '">' . $rwDepth['sl_name'] . '</option>';
                }
                ?>
            </select>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label><?php echo $lang['Select_User'];?></label>
                <div id="" class="form-control" style="height:100px;width:100%;overflow:scroll;">

                    <table id="" style="width:100%;">

                        <?php
                        $user = "select * from tbl_user_master";

                        $user_run = mysqli_query($db_con, $user);
                        $i = 1;
                        while ($rwUser = mysqli_fetch_assoc($user_run)) {
                            ?>

                            <tr>
                                <td>
                                    <div class="checkbox checkbox-primary">
                                        <input class="selectUser" type="checkbox" name="user[]" name="" value="<?php echo $rwUser['first_name']; ?>"/><label for="myCheck" class="shiv"><?php echo $rwUser['first_name']; ?></label>
                                    </div>
                                </td>
                            </tr>
                            
                            <?php
                            $i++;
                        }
                        ?>

                    </table>
                </div> 

            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label><?php echo $lang['Select_Group'];?></label>
                <div id="" class="form-control" style="height:100px;width:96%;overflow:scroll;">

                    <table id="" style="width:100%;">
                        <?php
                        $group = "select * from tbl_group_master";

                        $group_run = mysqli_query($db_con, $group);
                        $i = 1;
                        while ($rwGroup = mysqli_fetch_assoc($group_run)) {
                            ?>
                        
                            <tr>
                                <td>
                                    <div class="checkbox checkbox-primary">
                                        <input class="selectGroup" type="checkbox" name="group[]" value="<?php echo $rwGroup['group_name']; ?>"/><label for="myCheck" class="shiv"><?php echo $rwGroup['group_name']; ?></label>
                                    </div>
                                </td>
                            </tr>
                            <?php
                            $i++;
                        }
                        ?>
                    </table>

                </div> 
            </div>
        </div>
      <!--  <div class="col-md-4">
            <div class="form-group">
                <label>Select Action</label>
                <div id="" class="form-control" style="height:100px;width:95%; margin-left: -15px; overflow:scroll;">

                    <table id="" style="width:100%;">

                        <?php
                        $permission = "select * from tbl_permission_master";

                        $permission_run = mysqli_query($db_con, $permission);
                        $i = 1;
                        while ($rwPermission = mysqli_fetch_assoc($permission_run)) {
                            ?>
                        
                        <tr>
                                <td>
                                    <div class="checkbox checkbox-primary">
                                        <input class="selectAction" type="checkbox"  name="permission[]" value="<?php echo $rwPermission['permission_name']; ?>" /><label for="myCheck" class="shiv"><?php echo $rwPermission['permission_name']; ?></label>
                                    </div>
                                </td>
                            </tr>
                            
                            <?php
                            $i++;
                        }
                        ?>
                    </table>


                </div> 
            </div>
        </div> -->
    </div>


    <div class="box-body">
        <div  name=""  class="well well-lg">



            <span id="showlevel"></span><span id="showparent"></span><span id="showchild"></span><br>
            <span id="seluser"></span><br>
            <span id="selgroup"></span><br>
            <span id="selaction"></span>
        </div>


    </div>
</div>
<script>
//get id of user
    /*
     $('.selectUser').click(function () {
     
     $('input.selectUser:checked').each(function () {
     $.ajax({
     type: 'GET',
     url: './application/ajax/getData.php?id=' + $(this).attr("id"), // the id gets passed here
     
     success: function(data) {
     // alert(data); 
     
     $('#selgroup').html("Selected Group:-"+ " " + data);
     }
     // $('#selgroup').text("Selected Group:-" + " " + data);
     });
     });
     });
     */
</script>
<script type="text/javascript" src="./assets/jsCustom/slpermission.js"></script>



<?php
//$checkPermission = "select * from tbl_storagelevel_to_permission where (sl_id = '$slID' or child_id = '$slID') and user_id = "
?>