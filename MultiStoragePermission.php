<!DOCTYPE html>
<html>
<?php
require_once './loginvalidate.php';
require_once './application/pages/head.php';
if ($rwgetRole['storage_auth_plcy'] != '1') {
    header('Location: ./index');
}
mysqli_set_charset($db_con, "utf8");

function findChildss($slid)
{
    global $db_con;
    global $slChild;
    $sql_child = "select * FROM tbl_storage_level WHERE sl_parent_id = '$slid' AND delete_status='0' order by sl_name asc";
    $sql_child_run = mysqli_query($db_con, $sql_child) or die('Error:' . mysqli_error($db_con));
    if (mysqli_num_rows($sql_child_run) > 0) {
        while ($rwchild = mysqli_fetch_assoc($sql_child_run)) {
            $child = $rwchild['sl_id'];

            findChildss($child);
        }
    }
    $slChild[] = $slid;

    return $slChild;
}
?>
<!-- for searchable select-->
<link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<style>
    .content-page .content:first-of-type {
        min-height: 410px !important;
    }
</style>

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
                        <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                            <ol class="breadcrumb">
                                <li>
                                    <a href="#"><?php echo $lang['Masters']; ?></a>
                                </li>
                                <li class="active">
                                    <a href="MultiStoragePermission"><?php echo $lang['Storage_Policies']; ?></a>
                                </li>
                                <li> <a href="#" data-toggle="modal" data-target="#help-modal" id="helpview" data="6" title="<?= $lang['help']; ?>"><i class="fa fa-question-circle"></i> </a></li>
                                <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                            </ol>
                        </div>
                    </div>
                    <section class="content">
                        <div class="box box-primary">
                            <div class="panel">
                                <div class="panel-body">

                                    <!--form here start-->
                                    <form action="MultiStoragePermission" method="post">
                                        <div class="row form-group">
                                            <div class="col-md-2 col-lg-2 col-sm-2 col-xs-2">
                                                <label for="privilege"><?php echo $lang['Select_User']; ?><span style="color:red;">*</span></label>
                                            </div>
                                            <div class="col-md-6 col-lg-6 col-sm-6 col-xs-6">
                                                <select class="form-control select2  selectUser" name="user" data-placeholder="Select User" required>
                                                    <option><?php echo $lang['Select_User']; ?></option>
                                                    <?php
                                                    mysqli_set_charset($db_con, "utf8");
                                                    $sameGroupIDs = array();
                                                    $group = mysqli_query($db_con, "select * from tbl_bridge_grp_to_um where find_in_set('$_SESSION[cdes_user_id]',user_ids)") or die('Error' . mysqli_error($db_con));
                                                    while ($rwGroup = mysqli_fetch_assoc($group)) {
                                                        $sameGroupIDs[] = $rwGroup['user_ids'];
                                                    }
                                                    $sameGroupIDs = array_unique($sameGroupIDs);
                                                    sort($sameGroupIDs);
                                                    $sameGroupIDs = implode(',', $sameGroupIDs);
                                                    $user = "select * from tbl_user_master where user_id in($sameGroupIDs) order by first_name, last_name" or die("Error get grp" . mysqli_error($db_con));

                                                    $user_run = mysqli_query($db_con, $user);
                                                    $i = 1;
                                                    while ($rwUser = mysqli_fetch_assoc($user_run)) {
                                                        if ($rwUser['user_id'] != 1 && $_SESSION['cdes_user_id'] != $rwUser['user_id']) {
                                                            echo '<option value="' . $rwUser['user_id'] . '">' . $rwUser['first_name'] . ' ' . $rwUser['last_name'] . ' (' . $rwUser['user_email_id'] . ') ' . '</option>';
                                                        }
                                                    }
                                                    ?>

                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row " id="selgroup">
                                            <div class="col-sm-2">
                                                <label for="userName"><?php echo $lang['Select_Storage']; ?><span style="color:red;">*</span></label>
                                            </div>

                                            <div class="col-md-6 col-lg-6 col-sm-6 col-xs-6 form-group">
                                                <?php
                                                mysqli_set_charset($db_con, "utf8");
                                                $perm = mysqli_query($db_con, "select sl_id from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]' group by sl_id");
                                                $slperms = array();
                                                while ($rwPerm = mysqli_fetch_assoc($perm)) {
                                                    $slperms[] = $rwPerm['sl_id'];
                                                }
                                                $permcount = count($slperms);
                                                $sl_perm = implode(',', $slperms);

                                                $sllevel = mysqli_query($db_con, "select * from tbl_storage_level where sl_id in($sl_perm) AND delete_status='0' order by sl_name asc");
                                                ?>
                                                <select class="form-control select2" multiple name="slparentName[]" e data-placeholder="<?php echo $lang['Select_Storage']; ?>" required>

                                                    <?php
                                                    while ($rwSllevel = mysqli_fetch_assoc($sllevel)) {
                                                        $level = $rwSllevel['sl_depth_level'];
                                                        $SlId = $rwSllevel['sl_id'];
                                                        findChild($SlId, $level, $SlId);
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                        </div>
                                        <div class="col-sm-8 m-l-m-10">
                                            <input type="submit" name="submit" value="<?php echo $lang['Save']; ?>" class="btn btn-primary" />
                                            <a href="MultiStoragePermission" class="btn btn-warning"><?php echo $lang['Reset']; ?></a>
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
    <?php

    function findChild($sl_id, $level, $slperm)
    {

        global $db_con;
        echo '<option value="' . $sl_id . '">';
        parentLevel($sl_id, $db_con, $slperm, $level, '');
        echo '</option>';
        $sql_child = "select * FROM tbl_storage_level WHERE sl_parent_id = '$sl_id' AND delete_status='0' order by sl_name asc";

        $sql_child_run = mysqli_query($db_con, $sql_child) or die('Error:' . mysqli_error($db_con));

        if (mysqli_num_rows($sql_child_run) > 0) {

            while ($rwchild = mysqli_fetch_assoc($sql_child_run)) {

                $child = $rwchild['sl_id'];
                findChild($child, $level, $slperm);
            }
        }
    }

    function parentLevel($slid, $db_con, $slperm, $level, $value)
    {

        if ($slperm == $slid) {
            $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid' AND delete_status='0'") or die('Error' . mysqli_error($db_con));
            $rwParent = mysqli_fetch_assoc($parent);

            if ($level < $rwParent['sl_depth_level']) {
                parentLevel($rwParent['sl_parent_id'], $db_con, $slperm, $level, $rwParent['sl_name']);
            }
        } else {
            $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid' and sl_parent_id='$slperm' AND delete_status='0'") or die('Error' . mysqli_error($db_con));
            if (mysqli_num_rows($parent) > 0) {

                $rwParent = mysqli_fetch_assoc($parent);
                if ($level < $rwParent['sl_depth_level']) {
                    parentLevel($rwParent['sl_parent_id'], $db_con, $slperm, $level, $rwParent['sl_name']);
                }
            } else {
                $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid' AND delete_status='0'") or die('Error' . mysqli_error($db_con));
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
    <div style="display:none; text-align: center; color: #fff;  background: rgba(0,0,0,0.5); width: 100%; height: 100%; z-index: 2000; position: fixed; top:0;" id="wait">
        <img src="assets/images/load1.gif" alt="load" style="margin-top: 250px; width: 100px; border-radius: 5px;" />
    </div>
    <!--form here end-->
    <?php require_once './application/pages/footer.php'; ?>
    <!-- Right Sidebar -->
    <?php //require_once './application/pages/rightSidebar.php';  
    ?>
    <!-- /Right-bar -->
    <?php require_once './application/pages/footerForjs.php'; ?>
    <!-- for searchable select-->
    <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
    <script>
        function invisible(myid) {
            $(".numid-" + myid).remove();
            $("#addfields").show();
        }
        //var max_fields = <?= $permcount; ?>; //maximum input boxes allowed
        var max_fields = 100;

        function incrementCount() {
            max_fields = max_fields + 1;
            //alert(max_fields);
        };
        $(document).ready(function() {

            var wrapper = $(".contents"); //Fields wrapper
            var add_button = $("#addfields"); //Add button ID
            //var id =<?= $slid ?>;
            var x = 1; //initlal text box count
            $(add_button).click(function(e) { //on add input button click
                //alert('ok');
                //var id = $("select[name=slparentName]").val();
                var id = $("select[name='slparentName[]']").map(function() {
                    return $(this).val();
                }).get();
                e.preventDefault();
                console.log(id);
                var $id = id.toString();
                console.log($id);
                if (x < max_fields) { //max input box allowed
                    x++;
                    //text box increment
                    var token = $("input[name='token']").val();
                    //debugger;
                    $.post("application/ajax/AddMultipleStrgePermission.php", {
                        slid: $id,
                        token: token
                    }, function(result, status) {
                        if (status == 'success') {
                            $(wrapper).append(result);
                            getToken();
                        }
                    });
                } else {
                    alert("No. More meta data available");
                    $("#addfields").hide();
                }
            });

            $(wrapper).on("click", ".remove_field", function(e) { //user click on remove text
                e.preventDefault();
                $(this).parent().parent('div').remove();
                x--;
                $("#addfields").show();
            });
            $(".remove_field").click(function(e) { //user click on remove text
                e.preventDefault();
                $(this).parent().parent('div').remove();
                x--;
                $("#addfields").show();
            });
        });
        $("select.strg_id").change(function() {

            var $id = $("select[name='slparentName[]']").map(function() {
                return $(this).val();
            }).get();
            var id = $id.toString();
            //console.log(id);
            $.post("application/ajax/AddMultipleStrgePermission.php", {
                slid: id
            }, function(result, status) {
                if (status == 'success') {
                    // debugger;
                    $('#selgroup').html(result);
                }
            });
        });
    </script>
    <!-- for searchable select-->
    <script type="text/javascript">
        $(document).ready(function() {
            $(".select2").select2();
        });
    </script>
    <script>
        $('.selectUser').change(function() {
            var optionSelected = $(this).find("option:selected");
            var valueSelected = optionSelected.val();
            //alert(valueSelected);
            $("#wait").show();
            $.post("application/ajax/currentStorageView.php", {
                id: valueSelected
            }, function(result, status) {
                if (status == 'success') {
                    $('#selgroup').html(result);
                    $("#wait").hide();
                }
            });
        });
    </script>
</body>

</html>
<?php

function findChilds($slid)
{
    global $db_con;
    global $slChild;
    $sql_child = "select * FROM tbl_storage_level WHERE sl_parent_id = '$slid' AND delete_status='0' order by sl_name asc";
    $sql_child_run = mysqli_query($db_con, $sql_child) or die('Error:' . mysqli_error($db_con));
    if (mysqli_num_rows($sql_child_run) > 0) {
        while ($rwchild = mysqli_fetch_assoc($sql_child_run)) {
            $child = $rwchild['sl_id'];

            findChilds($child);
        }
    }
    $slChild[] = $slid;

    return $slChild;
}

if (isset($_POST['submit'], $_POST['token'])) {
    if (!empty($_POST['slparentName'])) {
        $slparentNameid = $_POST['slparentName'];
    }
    //$list=array();
    $user = trim($_POST['user']);
    //print_r($slparentNameid);
    //die;
    $aslids = array();
    $qassigned = mysqli_query($db_con, "SELECT * FROM `tbl_storagelevel_to_permission` where user_id='$user'");
    while ($rwAssigned = mysqli_fetch_assoc($qassigned)) {
        $aslids[] = $rwAssigned['sl_id'];
    }
    $slParentarray = array_unique($slparentNameid);
    foreach ($slids as $slid) {
        $childs = findChilds($slid);
        unset($childs[array_search($slid, $childs)]);
        if (empty($slchild)) {
            $slchild = $childs;
        } else {
            array_merge($slchild, $childs);
        }
    }
    $slchild = array_unique($slchild);
    foreach ($slchild as $child) {
        if (in_array($child, $slParentarray)) {
            unset($slParentarray[array_search($child, $slParentarray)]);
        }
    }
    $slparentNameid = implode(',', $slParentarray);
    $slparentNameid = explode(',', $slparentNameid);
    //print_r($slparentNameid);
    //die;
    if (!empty($slparentNameid)) {
        if (!empty($user) && is_numeric($user)) {

            $aslids = implode(",", $aslids);
            if (!empty($aslids)) {
                $rwstoragename = array();
                $oldstrgName = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id in($aslids) AND delete_status='0'") or die('Error:' . mysqli_error($db_con));
                while ($rwoldstrgName = mysqli_fetch_assoc($oldstrgName)) {
                    $rwstoragename[] = $rwoldstrgName['sl_name'];
                }

                $oldstrgName = implode(',', $rwstoragename);
                $userDelete = mysqli_query($db_con, "delete from tbl_storagelevel_to_permission where sl_id in($aslids) and user_id='$user'");
            }
            $oldstrgName = (!empty($oldstrgName) ? $oldstrgName : "Nothing");
            $user_id_run = mysqli_query($db_con, "select first_name,last_name from tbl_user_master where user_id ='$user'") or die('Error' . mysqli_error($db_con));
            $rwuser_id = mysqli_fetch_assoc($user_id_run);

            foreach ($slparentNameid as $slid) {

                $slins = mysqli_query($db_con, "insert into tbl_storagelevel_to_permission(user_id,sl_id) values('$user','$slid')");
                if ($slins) {
                    $strgName = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id = '$slid' AND delete_status='0'") or die('Error:' . mysqli_error($db_con));
                    $rwslName = mysqli_fetch_assoc($strgName);
                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`sl_id`,`action_name`, `start_date`,`system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','$slid','Storage permission changed','$date','$host','Storage permission $oldstrgName to $rwslName[sl_name] Alloted to $rwuser_id[first_name] $rwuser_id[last_name]')") or die('error : ' . mysqli_error($db_con));

                    $flag = 1;
                } else {
                    $flag = 0;
                }
            }
            if (count($slparentNameid) <= 0) {
                echo '<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","All Privileges Removed Successfully.");</script>';
            }
            if ($flag == 1) {
                echo '<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['storage_allocated'] . '");</script>';
            }
        } else {
            echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['please_Select_User'] . ' !");</script>';
        }
    } else {
        echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['please_Select_Storage'] . '!");</script>';
    }
    mysqli_close($db_con);
}
?>