<!DOCTYPE html>
<html>

    <?php
    require_once './loginvalidate.php';
    require_once './application/config/database.php';
    require_once './application/pages/head.php';

    //for user role

    $chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

    $rwgetRole = mysqli_fetch_assoc($chekUsr);

    // echo $rwgetRole['dashboard_mydms']; die;
    if ($rwgetRole['storage_auth_plcy'] != '1') {
        header('Location: ./index');
    }
    ?>
    <!-- for searchable select-->
    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <body class="fixed-left">
        <!-- Begin page -->
        <div id="wrapper">
            <?php require_once './application/pages/topBar.php'; ?>
            <?php require_once './application/pages/sidebar.php'; ?>
            <div class="content-page">
                <!-- Start content -->
                <div class="content">
                    <div class="container">
                        <!-- Page-Title -->
                        <div class="row">
                            <div class="col-sm-12">
                                <ol class="breadcrumb">
                                    <li>
                                        <a href="slpermission"><?php echo $lang['Masters']; ?></a>
                                    </li>
                                    <li class="active">
                                        <?php echo $lang['Storage_privileges']; ?>
                                    </li>
                                    <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                                </ol>
                            </div>
                        </div>
                        <section class="content">
                            <div class="box box-primary">
                                <div class="panel">
                                    <div class="panel-body">

                                        <!--form here start-->  
                                        <form action="slpermission" method="post">
                                            <div class="row form-group">
                                                <div class="form-group">
                                                    <div class="col-md-2">
                                                        <label for="privilege"><?php echo $lang['Select_User']; ?><span style="color:red;">*</span></label>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <select class="select2 select2-multiple selectUser" name="user[]" multiple="multiple" multiple data-placeholder="<?php echo $lang['Select_User']; ?>"  parsley-trigger="change" >
                                                            <?php
                                                            $sameGroupIDs = array();
                                                            $group = mysqli_query($db_con, "select * from tbl_bridge_grp_to_um where find_in_set('$_SESSION[cdes_user_id]',user_ids)") or die('Error' . mysqli_error($db_con));
                                                            while ($rwGroup = mysqli_fetch_assoc($group)) {
                                                                $sameGroupIDs[] = $rwGroup['user_ids'];
                                                            }
                                                            $sameGroupIDs = array_unique($sameGroupIDs);
                                                            sort($sameGroupIDs);
                                                            $sameGroupIDs = implode(',', $sameGroupIDs);
                                                            $user = "select * from tbl_user_master where user_id in($sameGroupIDs) order by first_name,last_name asc" or die("Error get grp" . mysqli_error($db_con));

                                                            $user_run = mysqli_query($db_con, $user);
                                                            $i = 1;
                                                            while ($rwUser = mysqli_fetch_assoc($user_run)) {
                                                                if ($rwUser['user_id'] != 1 && $_SESSION['cdes_user_id'] != $rwUser['user_id']) {
                                                                    echo'<option value="' . $rwUser['user_id'] . '">' . $rwUser['first_name'] . ' ' . $rwUser['last_name'] . '</option>';
                                                                }
                                                            }
                                                            ?>    

                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-2">
                                                    <label for="userName"><?php echo $lang['Select_Storage']; ?><span style="color:red;">*</span></label>
                                                </div>
                                                <div class="col-md-6">
                                                    <?php
                                                    $perm = mysqli_query($db_con, "select sl_id from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]' group by sl_id");
                                                    $rwPerm = mysqli_fetch_assoc($perm);
                                                    $slperm = $rwPerm['sl_id'];
                                                    $sllevel = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slperm'");
                                                    $rwSllevel = mysqli_fetch_assoc($sllevel);
                                                    $level = $rwSllevel['sl_depth_level'];
                                                    ?>
                                                    <select class="form-control select2"  name="slparentName" data-placeholder="<?php echo $lang['Select_Storage']; ?>" required>
                                                        <option disabled selected><?php echo $lang['Select_Storage']; ?></option>
                                                        <?php
                                                        findChild($slperm, $level, $slperm);
                                                        ?>
                                                    </select> 
                                                        <?php

                                                        function findChild($sl_id, $level, $slperm) {

                                                            global $db_con;
                                                            echo '<option value="' . $sl_id . '">';
                                                            parentLevel($sl_id, $db_con, $slperm, $level, '');
                                                            echo '</option>';
                                                            $sql_child = "select * FROM tbl_storage_level WHERE sl_parent_id = '$sl_id' ";

                                                            $sql_child_run = mysqli_query($db_con, $sql_child) or die('Error:' . mysqli_error($db_con));

                                                            if (mysqli_num_rows($sql_child_run) > 0) {

                                                                while ($rwchild = mysqli_fetch_assoc($sql_child_run)) {

                                                                    $child = $rwchild['sl_id'];
                                                                    findChild($child, $level, $slperm);
                                                                }
                                                            }
                                                        }

                                                        function parentLevel($slid, $db_con, $slperm, $level, $value) {

                                                            if ($slperm == $slid) {
                                                                $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid' ") or die('Error' . mysqli_error($db_con));
                                                                $rwParent = mysqli_fetch_assoc($parent);

                                                                if ($level < $rwParent['sl_depth_level']) {
                                                                    parentLevel($rwParent['sl_parent_id'], $db_con, $slperm, $level, $rwParent['sl_name']);
                                                                }
                                                            } else {
                                                                $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid' and sl_parent_id='$slperm'") or die('Error' . mysqli_error($db_con));
                                                                if (mysqli_num_rows($parent) > 0) {

                                                                    $rwParent = mysqli_fetch_assoc($parent);
                                                                    if ($level < $rwParent['sl_depth_level']) {
                                                                        parentLevel($rwParent['sl_parent_id'], $db_con, $slperm, $level, $rwParent['sl_name']);
                                                                    }
                                                                } else {
                                                                    $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid'") or die('Error' . mysqli_error($db_con));
                                                                    $rwParent = mysqli_fetch_assoc($parent);
                                                                    $getparnt = $rwParent['sl_parent_id'];
                                                                    if ($level <= $rwParent['sl_depth_level']) {
                                                                        parentLevel($getparnt, $db_con, $slperm, $level, $rwParent['sl_name']);
                                                                    } else {
                                                                        //header('Location: ./index.php');
                                                                        // header("Location: ./storage?id=".urlencode(base64_encode($slperm)));
                                                                    }
                                                                }
                                                            }

                                                            //echo $value;
                                                            if (!empty($value)) {
                                                                $value = $rwParent['sl_name'] . '<b> > </b>';
                                                            } else {
                                                                $value = $rwParent['sl_name'];
                                                            }
                                                            echo $value;
                                                        }
                                                        ?>

                                                </div>
                                            </div>

                                            <div class="box-body">
                                                <div class="col-sm-2 m-t-20" style="margin-left: -20px;">
                                                   <label><?php echo $lang['User_Current_Storage']; ?></label>
                                                </div>
                                                <div class="col-sm-9">
                                                    <div name="" class="well show-permsn">
                                                     <span id="selgroup"></span><br>
                                                    </div>
                                                </div>
                                                <div class="col-sm-8 sl-perm">
                                                <input type="submit" name="submit" value="<?php echo $lang['Save']; ?>" class="btn btn-primary" />
                                                <a href="slpermission" class="btn btn-warning"><?php echo $lang['Reset']; ?></a>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
        <!--form here end-->
<?php require_once './application/pages/footer.php'; ?>
        <!-- Right Sidebar -->
        <?php require_once './application/pages/rightSidebar.php'; ?>
        <!-- /Right-bar -->
        <?php require_once './application/pages/footerForjs.php'; ?>
        <!-- for searchable select-->
        <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>

        <!-- for searchable select-->
        <script type="text/javascript">
                                        $(document).ready(function () {

                                            $(".select2").select2();
                                        });

        </script>
        <script>
            /* $('.selectUser').click(function () {
             
             $('input.selectUser:checked').each(function () {
             $.ajax({
             
             url: './application/ajax/getData.php?id=' + $(this).attr("id"), // the id gets passed here
             
             success: function (data) {
             //alert(data); 
             
             //$('#selgroup').html("Selected Group:-" + " " + data);
             $('#selgroup').html("Selected Storage:-" + " " + data);
             }
             
             });
             });
             });
             */
            $('.selectUser').change(function () {
                var optionSelected = $(this).find("option:selected");
                var valueSelected = optionSelected.val();
                //alert(valueSelected);

                $.post("application/ajax/getData.php", {id: valueSelected}, function (result, status) {
                    if (status == 'success') {
                        // alert(result); 
                        $('#selgroup').html(result);
                    }

                });
            });
        </script>

    </body>
</html>


<?php
/*
  if (isset($_POST['submit'])) {

  if (!empty($_POST['slparentName'])) {
  $slparentNameid = $_POST['slparentName'];
  } else {
  $slparentNameid = $_POST['slchildName'];
  }

  $user = $_POST['user'];

  $group = $_POST['group'];

  $permission = $_POST['permission'];

  if (!empty($slparentNameid)) {


  if (!empty($_POST['slparentName'])) {
  if (!empty($_POST['slchildName'])) {
  $slparentNameid = $_POST['slchildName'];
  } else {

  $slparentNameid = $_POST['slparentName'];
  }
  }

  for ($i = 0; $i < count($user); $i++) {

  $outputUser = $user[$i];

  $user_id = "select * from tbl_user_master where first_name = '$outputUser'";

  $user_id_run = mysqli_query($db_con, $user_id) or die('Error' . mysqli_error($db_con));

  $rwuser_id = mysqli_fetch_assoc($user_id_run);

  $checkUserId = "select * from tbl_storagelevel_to_permission where user_id = '$rwuser_id[user_id]'";
  $checkUserId_run = mysqli_query($db_con, $checkUserId) or die('Error' . mysqli_error($db_con));

  $checkUserId_rw = mysqli_num_rows($checkUserId_run);

  if ($checkUserId_rw) {


  $userIdDelete = "DELETE FROM tbl_storagelevel_to_permission WHERE user_id = '$rwuser_id[user_id]'";
  $userIdDelete_run = mysqli_query($db_con, $userIdDelete) or die('Error' . mysqli_error($db_con));


  for ($g = 0; $g < count($group); $g++) {

  $outputGroup = $group[$g];

  $group_id = "select group_id from tbl_group_master where group_name = '$outputGroup'";

  $group_id_run = mysqli_query($db_con, $group_id) or die('Error' . mysqli_error($db_con));

  $rwgroup_id = mysqli_fetch_assoc($group_id_run);


  for ($p = 0; $p < count($permission); $p++) {


  $outputPermission = $permission[$p];

  $permission_id = "select permission_id from tbl_permission_master where permission_name = '$outputPermission'";

  $permission_id_run = mysqli_query($db_con, $permission_id) or die('Error' . mysqli_error($db_con));

  $rwpermission_id = mysqli_fetch_assoc($permission_id_run);

  $insertStp = "insert into tbl_storagelevel_to_permission (user_id, group_id, permission_id, sl_id) values('$rwuser_id[user_id]','$rwgroup_id[group_id]', '$rwpermission_id[permission_id]', '$slparentNameid')";

  $insertStp_run = mysqli_query($db_con, $insertStp) or die('Error:' . mysqli_error($db_con));

  $log = "insert into tbl_ezeefile_logs (user_id, user_name, group_id, sl_id, action_name, start_date, end_date, system_ip, remarks) values('$rwuser_id[user_id]', '$rwuser_id[first_name]','$rwgroup_id[group_id]', '$slparentNameid', 'Update Storage Level permission', '$date',null, '$host', '')";
  $log_run = mysqli_query($db_con, $log) or die('Error:' . mysqli_error($db_con));

  echo'<script>taskSuccess("slpermission","Privileges Updated Successfully.");</script>';
  }
  }
  } else {
  for ($g = 0; $g < count($group); $g++) {

  $outputGroup = $group[$g];

  $group_id = "select group_id from tbl_group_master where group_name = '$outputGroup'";

  $group_id_run = mysqli_query($db_con, $group_id) or die('Error' . mysqli_error($db_con));

  $rwgroup_id = mysqli_fetch_assoc($group_id_run);


  for ($p = 0; $p < count($permission); $p++) {


  $outputPermission = $permission[$p];

  $permission_id = "select permission_id from tbl_permission_master where permission_name = '$outputPermission'";

  $permission_id_run = mysqli_query($db_con, $permission_id) or die('Error' . mysqli_error($db_con));

  $rwpermission_id = mysqli_fetch_assoc($permission_id_run);

  $insertStp = "insert into tbl_storagelevel_to_permission (user_id, group_id, permission_id, sl_id) values('$rwuser_id[user_id]','$rwgroup_id[group_id]', '$rwpermission_id[permission_id]', '$slparentNameid')";

  $insertStp_run = mysqli_query($db_con, $insertStp) or die('Error:' . mysqli_error($db_con));

  $log = "insert into tbl_ezeefile_logs (user_id, user_name, group_id, sl_id, action_name, start_date, end_date, system_ip, remarks) values('$rwuser_id[user_id]', '$rwuser_id[first_name]','$rwgroup_id[group_id]', '$slparentNameid', 'Storage Level permission', now(), '', '$host', '')";

  $log_run = mysqli_query($db_con, $log) or die('Error:' . mysqli_error($db_con));
  echo'<script>taskSuccess("slpermission","Privileges Inserted Successfully.");</script>';
  }
  }
  }
  }
  } else {
  echo'<script>taskFailed("slpermission","please assign priviledges");</script>';
  }

  }
 */
?>

<?php
if (isset($_POST['submit'])) {
    if (!empty($_POST['slparentName'])) {
        $slparentNameid = $_POST['slparentName'];
        $slparentNameid = preg_replace("/[^0-9,]/", "", $slparentNameid); //filter slid
        $slparentNameid = mysqli_real_escape_string($db_con, $slparentNameid);
    }
    $user = $_POST['user'];
    //$user = mysqli_real_escape_string($db_con, $user);
    if (!empty($slparentNameid)) {
        if (!empty($user) && count($user) > 0) {

            // if (!empty($group)) { 
            for ($i = 0; $i < count($user); $i++) {

                $outputUser = $user[$i];
                $outputUser = preg_replace("/[^0-9,]/", "", $outputUser); //filter slid
                //$user_id = "select * from tbl_user_master where first_name = '$outputUser'";
                $user_id = "select * from tbl_user_master where user_id = '$outputUser'";

                $user_id_run = mysqli_query($db_con, $user_id) or die('Error' . mysqli_error($db_con));

                $rwuser_id = mysqli_fetch_assoc($user_id_run);

                $checkUserId = "select * from tbl_storagelevel_to_permission where user_id = '$rwuser_id[user_id]'";
                $checkUserId_run = mysqli_query($db_con, $checkUserId) or die('Error :' . mysqli_error($db_con));

                $checkUserId_rw = mysqli_num_rows($checkUserId_run);

                if ($checkUserId_rw) {


                    $userIdDelete = "DELETE FROM tbl_storagelevel_to_permission WHERE user_id = '$rwuser_id[user_id]'";
                    $userIdDelete_run = mysqli_query($db_con, $userIdDelete) or die('Error' . mysqli_error($db_con));


                    //for ($g = 0; $g < count($group); $g++) {
                    //$outputGroup = $group[$g];
                    // $group_id = "select group_id from tbl_group_master where group_name = '$outputGroup'";
                    // $group_id_run = mysqli_query($db_con, $group_id) or die('Error' . mysqli_error($db_con));
                    //$numGrp = mysqli_num_rows($group_id_run);
                    //if($numGrp){
                    // $rwgroup_id = mysqli_fetch_assoc($group_id_run);
                    $insertStp = "insert into tbl_storagelevel_to_permission (user_id, sl_id) values('$rwuser_id[user_id]','$slparentNameid')";
                    // } else {
                    // $insertStp = "insert into tbl_storagelevel_to_permission (user_id, permission_id, sl_id) values('$rwuser_id[user_id]',null, '$slparentNameid')";
                    // }
                    $insertStp_run = mysqli_query($db_con, $insertStp) or die('Error:' . mysqli_error($db_con));
                    $strgName = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id = '$slparentNameid'") or die('Error:' . mysqli_error($db_con));
                    $rwslName = mysqli_fetch_assoc($strgName);
                    //$log = "insert into tbl_ezeefile_logs (user_id, user_name, group_id, sl_id, action_name, start_date, end_date, system_ip, remarks) values('$rwuser_id[user_id]', '$rwuser_id[first_name]',null, '$slparentNameid', 'Storage permission $rwslName[sl_name] Alloted', '$date',null, '$host', null)";
                    //$log_run = mysqli_query($db_con, $log) or die('Error:' . mysqli_error($db_con));
                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `doc_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$slparentNameid',null,'Storage permission $rwslName[sl_name] Alloted to $rwuser_id[first_name] $rwuser_id[last_name]','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                    echo'<script>taskSuccess("slpermission","' . $lang[Privileges_Updated_Successfully] . '.");</script>';

                    // }
                } else {
                    //for ($g = 0; $g < count($group); $g++) {
                    //$outputGroup = $group[$g];
                    //$group_id = "select group_id from tbl_group_master where group_name = '$outputGroup'";
                    //$group_id_run = mysqli_query($db_con, $group_id) or die('Error' . mysqli_error($db_con));
                    //$rwgroup_id = mysqli_fetch_assoc($group_id_run);
                    $insertStp = "insert into tbl_storagelevel_to_permission (user_id,sl_id) values('$rwuser_id[user_id]','$slparentNameid')";

                    $insertStp_run = mysqli_query($db_con, $insertStp) or die('Error:' . mysqli_error($db_con));
                    $strgName = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id = '$slparentNameid'") or die('Error:' . mysqli_error($db_con));
                    $rwslName = mysqli_fetch_assoc($strgName);
                    //$log = "insert into tbl_ezeefile_logs (user_id, user_name, group_id, sl_id, action_name, start_date, end_date, system_ip, remarks) values('$rwuser_id[user_id]', '$rwuser_id[first_name]',null, '$slparentNameid', 'Storage permission', now(),null, '$host', null)";
                    //$log_run = mysqli_query($db_con, $log) or die('Error:' . mysqli_error($db_con));
                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `doc_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$slparentNameid',null,'Storage permission $rwslName[sl_name] Alloted to $rwuser_id[first_name] $rwuser_id[last_name]','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                    echo'<script>taskSuccess("slpermission","' . $lang['Privileges_Alloted_Successfully'] . '");</script>';

                    //}
                }
            }
            //} 
            // else {
            // echo'<script>taskFailed("slpermission","please Select Group !");</script>';
            // }
        } else {
            echo'<script>taskFailed("slpermission","' . $lang['please_Select_User'] . ' !");</script>';
        }
    } else {
        echo'<script>taskFailed("slpermission","' . $lang['Select_Storage'] . ' !");</script>';
    }
    mysqli_close($db_con);
}
?>