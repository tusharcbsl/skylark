<!DOCTYPE html>
<html>
<?php
require_once './loginvalidate.php';
require_once './application/config/database.php';
require_once './application/pages/head.php';
$sameGroupIDs = array();
$group = mysqli_query($db_con, "select * from tbl_bridge_grp_to_um where find_in_set('$_SESSION[cdes_user_id]',user_ids)") or die('Error' . mysqli_error($db_con));
while ($rwGroup = mysqli_fetch_assoc($group)) {
    $sameGroupIDs[] = $rwGroup['roleids'];
}

// $sameGroupIDs[1];
$sameGroupIDs = implode(',', $sameGroupIDs);
$sameGroupIDs = explode(",", $sameGroupIDs);
$sameGroupIDs = array_unique($sameGroupIDs);
$sameGroupIDs = array_filter($sameGroupIDs, function ($value) {
    return $value !== '';
});
sort($sameGroupIDs);
$sameGroupIDs = implode(',', $sameGroupIDs);
//for user role
$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

$rwgetRole = mysqli_fetch_assoc($chekUsr);

if ($rwgetRole['role_view'] != '1') {
    header('Location: ./index');
}
?>
<link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />

<!-- Plugin Css-->
<link rel="stylesheet" href="assets/plugins/magnific-popup/css/magnific-popup.css" />
<link rel="stylesheet" href="assets/plugins/jquery-datatables-editable/datatables.css" />

<body class="fixed-left">
    <!-- Begin page -->
    <div id="wrapper">
        <!-- Top Bar Start -->
        <?php require_once './application/pages/topBar.php'; ?>
        <!-- Top Bar End -->
        <!-- ========== Left Sidebar Start ========== -->
        <?php require_once './application/pages/sidebar.php'; ?>
        <!-- Left Sidebar End -->

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="content-page">
            <!-- Start content -->
            <div class="content">
                <div class="container">

                    <!-- Page-Title -->
                    <div class="row">
                        <div class="col-sm-12">
                            <ol class="breadcrumb">
                                <li>
                                    <a href="#"><?php echo $lang['Masters']; ?></a>
                                </li>
                                <li>
                                    <a href="userRole"><?php echo $lang['User_Profile']; ?></a>
                                </li>
                                <li class="active">
                                    <?php echo $lang['User_Profile_List']; ?>

                                </li>
                                <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                            </ol>
                        </div>
                    </div>

                    <div class="panel">
                        <div class="box box-primary">
                            <div class="panel-body">

                                <div class="row">
                                    <div class="col-sm-12">
                                        <?php if ($rwgetRole['role_add'] == '1') { ?>
                                            <div class="col-sm-4">
                                                <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-left" data-toggle="modal" data-target="#role-add"><?php echo $lang['Add_New_Profile']; ?> <i class="fa fa-plus"></i></a>
                                            </div>
                                        <?php } ?>
                                        <form method="get">
                                            <div class="col-sm-3">
                                                <input type="text" class="form-control" name="rolename" value="<?php echo xss_clean($_GET['rolename']); ?>" parsley-trigger="change" data-parsley-required-message="Enter Role Name for Search" placeholder="<?php echo $lang['User_Profile'] . " " . $lang['seerchsingle']; ?>" />
                                            </div>
                                            <div class="col-sm-3">
                                                <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> <?php echo $lang['Search']; ?></button>
                                                <a href="userRole" class="btn btn-warning"><i class="fa fa-refresh"></i> <?php echo $lang['Reset']; ?></a>
                                            </div>

                                        </form>
                                    </div>
                                </div>

                                <div class="">
                                    <?php
                                    $roleName = "";
                                    $where = "WHERE role_id in($sameGroupIDs) and role_id!='$userRole' and role_id!='1' ";
                                    if (isset($_GET['rolename']) && !empty($_GET['rolename'])) {

                                        $roleName = xss_clean(trim($_GET['rolename']));
                                        $roleName = mysqli_real_escape_string($db_con, $roleName);
                                        //$roleName = preg_replace("/[^A-Za-z0-9 ]/", "", $roleName);
                                        $where .= "and user_role like '%$roleName%'";
                                    }
                                    if (!empty($sameGroupIDs)) {
                                        mysqli_set_charset($db_con, "utf8");
                                        $sql = "SELECT * FROM  tbl_user_roles $where";

                                        $retval = mysqli_query($db_con, $sql) or die('Could not get data: ' . mysqli_error($db_con));
                                        $foundnum = mysqli_num_rows($retval);
                                        if ($foundnum > 0) {
                                            if (is_numeric($_GET['limit'])) {
                                                $per_page = preg_replace("/[^0-9]/", "", $_GET['limit']);
                                            } else {
                                                $per_page = 10;
                                            }
                                            $start = preg_replace("/[^0-9]/", "", $_GET['start']);
                                            $start = isset($start) ? ($start > 0) ? $start : 0 : 0;
                                            $max_pages = ceil($foundnum / $per_page);
                                            if (!$start) {
                                                $start = 0;
                                            }
                                            $limit = preg_replace("/[^0-9]/", "", $_GET['limit']);
                                    ?>
                                            <div class="container">

                                                <div class="box-body">
                                                    <label><?php echo $lang['show_lst']; ?></label>
                                                    <select id="limit" class="input-sm m-t-10 m-b-10">
                                                        <option value="10" <?php
                                                                            if ($limit == 10) {
                                                                                echo 'selected';
                                                                            }
                                                                            ?>>10</option>
                                                        <option value="25" <?php
                                                                            if ($limit == 25) {
                                                                                echo 'selected';
                                                                            }
                                                                            ?>>25</option>
                                                        <option value="50" <?php
                                                                            if ($limit == 50) {
                                                                                echo 'selected';
                                                                            }
                                                                            ?>>50</option>
                                                        <option value="250" <?php
                                                                            if ($limit == 250) {
                                                                                echo 'selected';
                                                                            }
                                                                            ?>>250</option>
                                                        <option value="500" <?php
                                                                            if ($limit == 500) {
                                                                                echo 'selected';
                                                                            }
                                                                            ?>>500</option>
                                                    </select>
                                                    <label><?php echo $lang['User_Profile']; ?></label>
                                                    <div class="pull-right record m-t-10">
                                                        <?php echo $start + 1 ?> <?php echo $lang['To'] ?> <?php
                                                                                                            if ($start + $per_page > $foundnum) {
                                                                                                                echo $foundnum;
                                                                                                            } else {
                                                                                                                echo ($start + $per_page);
                                                                                                            }
                                                                                                            ?> <span><?php echo $lang['ttl_recrds']; ?> : <?php echo $foundnum; ?></span>
                                                    </div>
                                                    <?php
                                                    $users = mysqli_query($db_con, "SELECT * FROM tbl_user_roles $where ORDER BY user_role ASC LIMIT $start, $per_page") or die('Error:' . mysqli_error($db_con));

                                                    showData($users, $rwgetRole, $db_con, $start);
                                                    ?>
                                                    <?php
                                                    echo "<center>";
                                                    $prev = $start - $per_page;
                                                    $next = $start + $per_page;

                                                    $adjacents = 3;
                                                    $last = $max_pages - 1;
                                                    if ($max_pages > 1) {
                                                    ?>

                                                        <ul class='pagination strgePage'>
                                                            <?php
                                                            //previous button
                                                            if (!($start <= 0))
                                                                echo " <li><a href='?start=$prev&rolename='" . $roleName . "'&limit=$per_page'>$lang[Prev]</a> </li>";
                                                            else
                                                                echo " <li class='disabled'><a href='javascript:void(0)'>$lang[Prev]</a> </li>";
                                                            //pages 
                                                            if ($max_pages < 7 + ($adjacents * 2)) {   //not enough pages to bother breaking it up
                                                                $i = 0;
                                                                for ($counter = 1; $counter <= $max_pages; $counter++) {
                                                                    if ($i == $start) {
                                                                        echo "<li class='active'><a href='?start=$i&limit=$per_page&rolename=" . $roleName . "'><b>$counter</b></a> </li>";
                                                                    } else {
                                                                        echo "<li><a href='?start=$i&limit=$per_page&rolename=" . $roleName . "'>$counter</a></li> ";
                                                                    }
                                                                    $i = $i + $per_page;
                                                                }
                                                            } elseif ($max_pages > 5 + ($adjacents * 2)) {    //enough pages to hide some
                                                                //close to beginning; only hide later pages
                                                                if (($start / $per_page) < 1 + ($adjacents * 2)) {
                                                                    $i = 0;
                                                                    for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                                                                        if ($i == $start) {
                                                                            echo " <li class='active'><a href='?start=$i&limit=$per_page&rolename=" . $roleName . "'><b>$counter</b></a></li> ";
                                                                        } else {
                                                                            echo "<li> <a href='?start=$i&limit=$per_page&rolename=" . $roleName . "'>$counter</a> </li>";
                                                                        }
                                                                        $i = $i + $per_page;
                                                                    }
                                                                }
                                                                //in middle; hide some front and some back
                                                                elseif ($max_pages - ($adjacents * 2) > ($start / $per_page) && ($start / $per_page) > ($adjacents * 2)) {
                                                                    echo " <li><a href='?start=0&limit=$per_page&rolename=$_GET[rolename]'>1</a></li> ";
                                                                    echo "<li><a href='?start=$per_page&limit=$per_page&rolename='" . $roleName . "''>2</a></li>";
                                                                    echo "<li><a href='javascript:void(0)'>...</a></li>";

                                                                    $i = $start;
                                                                    for ($counter = ($start / $per_page) + 1; $counter < ($start / $per_page) + $adjacents + 2; $counter++) {
                                                                        if ($i == $start) {
                                                                            echo " <li class='active'><a href='?start=$i&limit=$per_page&rolename=" . $roleName . "'><b>$counter</b></a></li> ";
                                                                        } else {
                                                                            echo " <li><a href='?start=$i&limit=$per_page&rolename=" . $roleName . "'>$counter</a> </li>";
                                                                        }
                                                                        $i = $i + $per_page;
                                                                    }
                                                                }
                                                                //close to end; only hide early pages
                                                                else {
                                                                    echo "<li> <a href='?start=0&limit=$per_page&rolename=" . $roleName . "'>1</a> </li>";
                                                                    echo "<li><a href='?start=$per_page&limit=$per_page&rolename='" . $roleName . "'>2</a></li>";
                                                                    echo "<li><a href='javascript:void(0)'>...</a></li>";

                                                                    $i = $start;
                                                                    for ($counter = ($start / $per_page) + 1; $counter <= $max_pages; $counter++) {
                                                                        if ($i == $start) {
                                                                            echo " <li class='active'><a href='?start=$i&limit=$per_page&rolename=" . $roleName . "'><b>$counter</b></a></li> ";
                                                                        } else {
                                                                            echo "<li> <a href='?start=$i&limit=$per_page&rolename=" . $roleName . "'>$counter</a></li> ";
                                                                        }
                                                                        $i = $i + $per_page;
                                                                    }
                                                                }
                                                            }
                                                            //next button
                                                            if (!($start >= $foundnum - $per_page))
                                                                echo "<li><a href='?start=$next&rolename='" . $roleName . "'&limit=$per_page'>$lang[Next]</a></li>";
                                                            else
                                                                echo "<li class='disabled'><a href='javascript:void(0)'>$lang[Next]</a></li>";
                                                            ?>
                                                        </ul>
                                                    <?php
                                                    }
                                                    echo "</center>";
                                                } else {
                                                    ?>
                                                    <div class="form-group form-group no-records-found"><label><strong class="text-danger"><i class="ti-face-sad text-pink"></i> <?php echo $lang['Who0ps!_No_Records_Found']; ?></strong></label></div>
                                            <?php
                                                }
                                            } else {
                                                echo '<div class="form-group text-center"><label class="m-t-15 m-b-15"><strong class="text-danger">' . $lang['no_access_to_view_role'] . '</strong></label></div>';
                                            }
                                            ?>
                                                </div>
                                            </div>
                                            <!-- end: page -->
                                </div>
                            </div> <!-- end Panel -->
                        </div> <!-- container -->

                    </div> <!-- content -->

                    <?php require_once './application/pages/footer.php'; ?>
                </div>
                <!-- ============================================================== -->
                <!-- End Right content here -->
                <!-- ============================================================== -->
                <!-- Right Sidebar -->
                <?php //require_once './application/pages/rightSidebar.php'; 
                ?>
                <!-- /Right-bar -->
                <!-- MODAL -->
                <div id="dialog" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog">
                        <div class="panel panel-color panel-danger">
                            <div class="panel-heading">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <label>
                                    <h2 class="panel-title"><?php echo $lang['Are_u_confirm']; ?></h2>
                                </label>
                            </div>

                            <form method="post">
                                <div class="panel-body">
                                    <p style="color: red;"><?php echo $lang['Are_you_sure_that_you_want_to_delete_this_Profile']; ?></p>
                                </div>
                                <div class="modal-footer">
                                    <div class="col-md-12 text-right">
                                        <input type="hidden" id="uid" name="uid">
                                        <button type="submit" name="delete" id="dialogConfirm" class="btn btn-danger waves-effect waves-light"> <?php echo $lang['confirm']; ?></button>
                                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- end Modal -->

                <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <form method="post">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                    <h4 class="modal-title"><?php echo $lang['Update_User_Role']; ?></h4>
                                </div>

                                <div class="modal-body" id="modalModify">
                                    <img src="assets/images/load.gif" alt="load" class="img-responsive center-block" width="50px" />
                                </div>
                                <div class="modal-footer">

                                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                                    <button type="submit" name="editRole" class="btn btn-primary"><?php echo $lang['Save']; ?> </button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div><!-- /.modal -->
                <div id="role-add" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <form method="post">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                    <h4 class="modal-title"><?php echo $lang['Add_New_Profile']; ?></h4>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="form-group txt">
                                            <label><?php echo $lang['Profile_Name']; ?> <span style="color:red;">*</span></label>
                                            <input type="text" name="roleName" required class="form-control respecialchar" id="groupName" required placeholder="<?php echo $lang['Profile_Name']; ?> ">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group txt">
                                            <div class="form-group">
                                                <label for="privilege"><?php echo $lang['Select_Group']; ?><span style="color:red;">*</span></label>
                                                <select class="select2 select2-multiple" name="groups[]" multiple="multiple" multiple data-placeholder="<?php echo $lang['Select_Group']; ?>" parsley-trigger="change" id="group" required>
                                                    <?php
                                                    $group_permission = mysqli_query($db_con, "SELECT tbl_bridge_grp_to_um.group_id,user_ids FROM `tbl_bridge_grp_to_um` left join tbl_group_master tg on tbl_bridge_grp_to_um.group_id=tg.group_id order by tg.group_name asc");
                                                    while ($allGroupRow = mysqli_fetch_array($group_permission)) {
                                                        echo $_SESSION['cdes_user_id'];
                                                        $user_ids = explode(',', $allGroupRow['user_ids']);
                                                        if (in_array($_SESSION['cdes_user_id'], $user_ids)) {

                                                            $grp = mysqli_query($db_con, "select group_id,group_name from tbl_group_master WHERE group_id='$allGroupRow[group_id]' order by group_name asc") or die('error' . mysqli_error($db_con));
                                                            while ($rwGrp = mysqli_fetch_assoc($grp)) {
                                                                echo '<option value="' . $rwGrp['group_id'] . '">' . $rwGrp['group_name'] . '</option>';
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group txt">
                                            <label><?php echo $lang['Profls_Permissions']; ?></label>
                                        </div>
                                        <table style="width:100%;">
                                            <tr>
                                                <div class="checkbox checkbox-success">
                                                    <input type="checkbox" id="selectall" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass  col_0">
                                                    <label for="myCheck"><strong><?php echo $lang['Select_all']; ?></strong></label>
                                                </div>
                                            <tr>
                                                <td>
                                                    <div class="form-group"></div>
                                                </td>
                                            </tr>
                                            </tr>
                                            <?php if ($rwgetRole['dashboard_mydms'] == '1' || $rwgetRole['dashboard_mytask'] == '1' || $rwgetRole['dashboard_edit_profile'] == '1' || $rwgetRole['dashboard_query'] == '1' || $rwgetRole['num_of_folder'] == '1' || $rwgetRole['num_of_file'] == '1' || $rwgetRole['memory_used'] == '1' || $rwgetRole['status_wf'] == '1' || $rwgetRole['priority_wf'] == '1' || $rwgetRole['calendar'] == '1') {
                                            ?>
                                                <tr>
                                                    <td>
                                                        <div class="checkbox checkbox-success txt">
                                                            <input type="checkbox" id="select_row_0" />
                                                            <label for="myCheck"><?php echo $lang['Das']; ?></label>
                                                        </div>

                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php if ($rwgetRole['dashboard_mydms'] == '1' || $rwgetRole['dashboard_mytask'] == '1' || $rwgetRole['dashboard_edit_profile'] == '1' || $rwgetRole['dashboard_query'] == '1') {
                                            ?>
                                                <tr>
                                                    <?php if ($rwgetRole['dashboard_mydms'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck1" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_0 col_0" name="mydms" value="1">
                                                                <label for="myCheck"><?php echo $lang['MY_DMS']; ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['dashboard_mytask'] == '1') { ?>
                                                        <td>

                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck2" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_0 col_1" name="mytsk" value="1">
                                                                <label for="myCheck"><?php echo $lang['My_tasks']; ?></label>
                                                            </div>

                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['dashboard_edit_profile'] == '1') { ?>
                                                        <td>

                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck3" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_0 col_2" name="dashEditPro" value="1">
                                                                <label for="myCheck"><?php echo $lang['EDIT_PROFILE']; ?></label>
                                                            </div>

                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['dashboard_query'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck4" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_0 col_3" name="dashQury" value="1">
                                                                <label for="myCheck"><?php echo $lang['Queries']; ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php if ($rwgetRole['num_of_folder'] == '1' || $rwgetRole['num_of_file'] == '1' || $rwgetRole['memory_used'] == '1' || $rwgetRole['status_wf'] == '1' || $rwgetRole['priority_wf'] == '1' || $rwgetRole['calendar_wf'] == '1') {
                                            ?>
                                                <tr>
                                                    <?php if ($rwgetRole['num_of_folder'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck47" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_0 col_4" name="num_folders" value="1">
                                                                <label for="myCheck"><?php echo $lang['NO_OF_FOLDER']; ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['num_of_file'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck48" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_0 col_5" name="num_files" value="1">
                                                                <label for="myCheck"><?php echo $lang['NO_OF_FILE']; ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['memory_used'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck49" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_0 col_6" name="memory_use" value="1">
                                                                <label for="myCheck"><?php echo $lang['MEMORY_USED']; ?></label>
                                                            </div>

                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['status_wf'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck49" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_0 col_7" name="status" value="1">
                                                                <label for="myCheck"><?php echo $lang['wf_status']; ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php if ($rwgetRole['priority_wf'] == '1' || $rwgetRole['calendar_wf'] == '1' || $rwgetRole['user_graph'] == '1') { ?>
                                                <tr>
                                                    <?php if ($rwgetRole['priority_wf'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck49" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_0 col_8" name="priority" value="1">
                                                                <label for="myCheck"><?php echo $lang['wf_priority']; ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['calendar_wf'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck49" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_0 col_9" name="calendar" value="1">
                                                                <label for="myCheck"><?php echo $lang['calendar']; ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['user_graph'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck150" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_0 col_10" name="user_graph" value="1">
                                                                <label for="myCheck"><?php echo $lang['user_graph']; ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php
                                            if ($rwgetRole['create_user'] == '1' || $rwgetRole['modify_userlist'] == '1' || $rwgetRole['delete_userlist'] == '1' || $rwgetRole['view_userlist'] == '1') {
                                            ?>
                                                <tr>
                                                    <td>
                                                        <div class="checkbox checkbox-success txt">
                                                            <input type="checkbox" id="select_row_1" />
                                                            <label for="myCheck"><?php echo $lang['User_manager']; ?></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <?php if ($rwgetRole['create_user'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck5" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_1 col_0" name="usrAdd" value="1">
                                                                <label for="myCheck"><?php echo $lang['Add'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['modify_userlist'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck6" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_1 col_1" name="usrmodi" value="1">
                                                                <label for="myCheck"><?php echo $lang['Edit'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['delete_userlist'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck7" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_1 col_2" name="usrDelete" value="1">
                                                                <label for="myCheck"><?php echo $lang['Delete'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['view_userlist'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck8" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_1 col_3" name="usrView" value="1">
                                                                <label for="myCheck"><?php echo $lang['view'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php if ($rwgetRole['export_user'] == '1' || $rwgetRole['import_user'] == '1' || $rwgetRole['user_activate_deactivate'] == '1') { ?><tr>
                                                    <?php if ($rwgetRole['export_user'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck189" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_1 col_4" name="export_user" value="1">
                                                                <label for="myCheck"><?php echo $lang['Export_Users'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['import_user'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck190" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_1 col_5" name="import_user" value="1">
                                                                <label for="myCheck"><?php echo $lang['Import_Users'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['user_activate_deactivate'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck191" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_1 col_6" name="user_activate_deactivate" value="1">
                                                                <label for="myCheck"><?php echo $lang['user_activate_deactivate'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php if (($rwgetRole['storage_auth_plcy'] == 1) || ($rwgetRole['online_user'] == '1') || ($rwgetRole['email_config'] == '1')) { ?>
                                                <tr>
                                                    <td>
                                                        <div class="checkbox checkbox-success txt">
                                                            <input type="checkbox" id="select_row_2" />
                                                            <label for="myCheck"><?php echo $lang['Auth'] ?></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <?php if ($rwgetRole['storage_auth_plcy'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck9" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_2 col_0" name="strgAuth" value="1">
                                                                <label for="myCheck"><?php echo $lang['Storage_Auth'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['online_user'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck91" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_2 col_1" name="onlineUser" value="1">
                                                                <label for="myCheck"><?php echo $lang['Online_User'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['email_config'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck92" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_2 col_2" name="mailconfigYes" value="1">
                                                                <label for="myCheck"><?php echo $lang['Email_Confg'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['mail_lists'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck921" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_2 col_3" name="mailList" value="1">
                                                                <label for="myCheck"><?php echo $lang['Mail_Lists'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php if ($rwgetRole['add_group'] == '1' || $rwgetRole['delete_group'] == '1' || $rwgetRole['modify_group'] == '1' || $rwgetRole['view_user_list'] == '1') { ?>
                                                <tr>
                                                    <td>
                                                        <div class="checkbox checkbox-success txt">
                                                            <input type="checkbox" id="select_row_3" />
                                                            <label for="myCheck"><?php echo $lang['Group_Manager'] ?></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php if ($rwgetRole['add_group'] == '1' || $rwgetRole['delete_group'] == '1' || $rwgetRole['modify_group'] == '1' || $rwgetRole['view_user_list'] == '1') { ?>
                                                <tr>
                                                    <?php if ($rwgetRole['add_group'] == '1') { ?>
                                                        <td>

                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck10" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_3 col_0" name="grpAdd" value="1">
                                                                <label for="myCheck"><?php echo $lang['Add'] ?></label>
                                                            </div>

                                                        </td>
                                                    <?php } ?>
                                                    <td>
                                                        <?php if ($rwgetRole['delete_group'] == '1') { ?>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck11" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_3 col_1" name="grpDelete" value="1">
                                                                <label for="myCheck"><?php echo $lang['Delete'] ?></label>
                                                            </div>
                                                        <?php } ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($rwgetRole['modify_group'] == '1') { ?>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck12" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_3 col_2" name="grpModi" value="1">
                                                                <label for="myCheck"><?php echo $lang['Edit'] ?></label>
                                                            </div>
                                                        <?php } ?>
                                                    </td>

                                                    <td>
                                                        <?php if ($rwgetRole['view_group_list'] == '1') { ?>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck13" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_3 col_3" name="grpView" value="1">
                                                                <label for="myCheck"><?php echo $lang['view'] ?></label>
                                                            </div>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php if ($rwgetRole['role_add'] == '1' || $rwgetRole['role_delete'] == '1' || $rwgetRole['role_modi'] == '1' || $rwgetRole['role_view'] == '1') { ?>
                                                <tr>
                                                    <td colspan="4">
                                                        <div class="checkbox checkbox-success txt">
                                                            <input type="checkbox" id="select_row_4" />
                                                            <label for="myCheck"><?php echo $lang['User_Profile_Manager'] ?></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <?php if ($rwgetRole['role_add'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck14" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_4 col_0" name="usrRoleAdd" value="1">
                                                                <label for="myCheck"><?php echo $lang['Add'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['role_delete'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck15" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_4 col_1" name="usrRoleDel" value="1">
                                                                <label for="myCheck"><?php echo $lang['Delete'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['role_modi'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck16" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_4 col_2" name="usrRoleModi" value="1">
                                                                <label for="myCheck"><?php echo $lang['Edit'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['role_view'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck17" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_4 col_3" name="usrRoleView" value="1">
                                                                <label for="myCheck"><?php echo $lang['view'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php if ($rwgetRole['bulk_upload'] == '1') { ?>
                                                <tr>
                                                    <td>
                                                        <div class="checkbox checkbox-success txt">
                                                            <input type="checkbox" id="select_row_5" />
                                                            <label for="myCheck"><?php echo $lang['Upload_Import'] ?></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <?php if ($rwgetRole['bulk_upload'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck18" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_5 col_0" name="bulkUpld" value="1">
                                                                <label for="myCheck"><?php echo $lang['Bulk_Upload'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>

                                                    <?php if ($rwgetRole['folder_upload'] == '1') { ?>
                                                        <td colspan="3">
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck1s8" type="checkbox" class="checkBoxClass row_5 col_1" name="bulkUpldfolder" value="1">
                                                                <label for="myCheck"><?php echo $lang['Upload_multi_folder']; ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php if ($rwgetRole['add_metadata'] == '1' || $rwgetRole['view_metadata'] == '1' || $rwgetRole['edit_metadata'] == '1' || $rwgetRole['delete_metadata'] == '1' || $rwgetRole['assign_metadata'] == '1') { ?>
                                                <tr>
                                                    <td>
                                                        <div class="checkbox checkbox-success txt">
                                                            <input type="checkbox" id="select_row_6" />
                                                            <label for="myCheck"><?php echo $lang['MetaData_Registry'] ?></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php if ($rwgetRole['add_metadata'] == '1' || $rwgetRole['view_metadata'] == '1' || $rwgetRole['edit_metadata'] == '1' || $rwgetRole['assign_metadata'] == '1' || $rwgetRole['save_query'] == '1') { ?>
                                                <tr>
                                                    <?php if ($rwgetRole['add_metadata'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck19" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_6 col_0" name="metaDataAdd" value="1">
                                                                <label for="myCheck"><?php echo $lang['Add'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['view_metadata'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck20" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_6 col_1" name="meteDataView" value="1">
                                                                <label for="myCheck"><?php echo $lang['view'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['assign_metadata'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck21" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_6 col_2" name="meteDataAsin" value="1">
                                                                <label for="myCheck"><?php echo $lang['Assign'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['edit_metadata'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck21" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_6 col_3" name="meteDataedit" value="1">
                                                                <label for="myCheck"><?php echo $lang['Edit'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php if ($rwgetRole['delete_metadata'] == '1' || $rwgetRole['save_query'] == '1') { ?>
                                                <tr>
                                                    <?php if ($rwgetRole['delete_metadata'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck21" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_6 col_4" name="meteDatadelete" value="1">
                                                                <label for="myCheck"><?php echo $lang['delete_metadata'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['save_query'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck21" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_6 col_5" name="savequery" value="1">
                                                                <label for="myCheck"><?php echo $lang['Sve_Qry'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php if ($rwgetRole['create_storage'] == '1' || $rwgetRole['create_child_storage'] == '1' || $rwgetRole['upload_doc_storage'] == '1' || $rwgetRole['modify_storage_level'] == '1' || $rwgetRole['delete_storage_level'] == '1' || $rwgetRole['move_storage_level'] == '1' || $rwgetRole['copy_storage_level'] == '1') { ?>
                                                <tr>
                                                    <td>
                                                        <div class="checkbox checkbox-success txt">
                                                            <input type="checkbox" id="select_row_7" />
                                                            <label for="myCheck"><?php echo $lang['Storage_Manager'] ?></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php if ($rwgetRole['create_storage'] == '1' || $rwgetRole['create_child_storage'] == '1' || $rwgetRole['upload_doc_storage'] == '1' || $rwgetRole['modify_storage_level'] == '1') { ?>
                                                <tr>
                                                    <?php if ($rwgetRole['create_storage'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck39" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_7 col_0" name="strgCreate" value="1">
                                                                <label for="myCheck"><?php echo $lang['Crt_Strg'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['create_child_storage'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck22" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_7 col_0" name="strgAddChild" value="1">
                                                                <label for="myCheck"><?php echo $lang['Add_Nw_Chld'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['upload_doc_storage'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck23" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_7 col_1" name="strgUpldDoc" value="1">
                                                                <label for="myCheck"><?php echo $lang['Upload_Documents'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['modify_storage_level'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck24" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_7 col_2" name="strgModi" value="1">
                                                                <label for="myCheck"><?php echo $lang['Edit'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php if ($rwgetRole['delete_storage_level'] == '1' || $rwgetRole['move_storage_level'] == '1' || $rwgetRole['copy_storage_level'] == '1') { ?>

                                                <tr>
                                                    <?php if ($rwgetRole['delete_storage_level'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck25" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_7 col_3" name="strgDelete" value="1">
                                                                <label for="myCheck"><?php echo $lang['Delete'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['move_storage_level'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck26" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_7 col_4" name="strgMove" value="1">
                                                                <label for="myCheck"><?php echo $lang['move'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['copy_storage_level'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck27" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_7 col_5" name="strgCopy" value="1">
                                                                <label for="myCheck"><?php echo $lang['Copy'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                            <?php } ?>

                                            <?php if ($rwgetRole['view_user_audit'] == '1' || $rwgetRole['view_storage_audit'] == '1' || $rwgetRole['workflow_audit'] == '1' || $rwgetRole['delete_user_log'] == '1' || $rwgetRole['delete_storage_log'] == '1' || $rwgetRole['delete_wf_log'] == '1') { ?>
                                                <tr>
                                                    <td>
                                                        <div class="checkbox checkbox-success txt">
                                                            <input type="checkbox" id="select_row_8" />
                                                            <label for="myCheck"><?php echo $lang['Audit_Trail'] ?></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php if ($rwgetRole['view_user_audit'] == '1' || $rwgetRole['view_storage_audit'] == '1' || $rwgetRole['workflow_audit'] == '1' || $rwgetRole['delete_user_log'] == '1' || $rwgetRole['upload_logs'] == '1') { ?>
                                                <tr>
                                                    <?php if ($rwgetRole['view_user_audit'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck28" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_8 col_0" name="auditTrlUsr" value="1">
                                                                <label for="myCheck"><?php echo $lang['User_Wise'] ?></label>
                                                            </div>

                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['view_storage_audit'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck29" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_8 col_1" name="auditTrlStrg" value="1">
                                                                <label for="myCheck"><?php echo $lang['Storage_Wise'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['workflow_audit'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck291" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_8 col_2" name="auditwf" value="1">
                                                                <label for="myCheck"><?php echo $lang['WorkFlow_Wise'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['delete_user_log'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck291" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_8 col_3" name="delusrlog" value="1">
                                                                <label for="myCheck"><?php echo $lang['del_user_log'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php if ($rwgetRole['delete_storage_log'] == '1' || $rwgetRole['delete_wf_log'] == '1' || $rwgetRole['upload_logs'] == '1') { ?>
                                                <tr>
                                                    <?php if ($rwgetRole['delete_storage_log'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck291" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_8 col_4" name="delstrglog" value="1">
                                                                <label for="myCheck"><?php echo $lang['del_strg_log'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['delete_wf_log'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck291" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_8 col_5" name="delwflog" value="1">
                                                                <label for="myCheck"><?php echo $lang['del_wf_log'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['upload_logs'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="uplogs" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_8 col_6" name="uploadlog" value="1">
                                                                <label for="uplog"><?php echo $lang['upload_logs'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php if ($rwgetRole['create_workflow'] == '1' || $rwgetRole['view_workflow_list'] == '1' || $rwgetRole['edit_workflow'] == '1' || $rwgetRole['delete_workflow'] == '1') { ?>
                                                <tr>
                                                    <td colspan="20">
                                                        <div class="checkbox checkbox-success txt">
                                                            <input type="checkbox" id="select_row_9" />
                                                            <label for="myCheck"><?php echo $lang['Workflow_management'] ?></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <?php if ($rwgetRole['create_workflow'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck30" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_9 col_0" name="wrkflwCreate" value="1">
                                                                <label for="myCheck"><?php echo $lang['Create_Work_Flow'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['view_workflow_list'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck31" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_9 col_1" name="wrkflwView" value="1">
                                                                <label for="myCheck"><?php echo $lang['view'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['edit_workflow'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck32" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_9 col_2" name="wrkflwEdit" value="1">
                                                                <label for="myCheck"><?php echo $lang['Edit'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['delete_workflow'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck33" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_9 col_3" name="wrkflwDel" value="1">
                                                                <label for="myCheck"><?php echo $lang['Delete'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php if ($rwgetRole['workflow_step'] == '1' || $rwgetRole['workflow_initiate_file'] == '1' || $rwRole['initiate_file'] == '1') { ?>
                                                <tr>
                                                    <?php if ($rwgetRole['workflow_step'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck34" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_9 col_4" name="wrkflwStep" value="1">
                                                                <label for="myCheck"><?php echo $lang['Workflow_Step'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['workflow_initiate_file'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck35" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_9 col_5" name="wrkflwIniFile" value="1">
                                                                <label for="myCheck"><?php echo $lang['Initiate_WorkFlow'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['initiate_file'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck46" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_9 col_6" name="InitiateFile" value="1">
                                                                <label for="myCheck"><?php echo $lang['Initiate_File'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php if ($rwgetRole['involve_workflow'] == '1' || $rwgetRole['run_workflow'] == '1' || $rwgetRole['add_report'] == '1' || $rwgetRole['view_report'] == '1' || $rwgetRole['update_report'] == '1' || $rwgetRole['delete_report'] == '1') { ?>
                                                <td>
                                                    <div class="checkbox checkbox-success txt">
                                                        <input type="checkbox" id="select_row_18" />
                                                        <label for="myCheck"><?php echo $lang['Workflow_Reports'] ?></label>
                                                    </div>
                                                </td>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <?php if ($rwgetRole['involve_workflow'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck341" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_18 col_0" name="wrkflwInvl" value="1">
                                                                <label for="myCheck"><?php echo $lang['Involved_WorkFlow'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['run_workflow'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck351" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_18 col_1" name="wrkflwRun" value="1">
                                                                <label for="myCheck"><?php echo $lang['running_wf'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>


                                                    <?php if ($rwgetRole['view_report'] == '1') { ?>

                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck351" type="checkbox" class="checkBoxClass row_18 col_5" name="viewreport" value="1">
                                                                <label for="myCheck"><?php echo $lang['view_report']; ?></label>
                                                            </div>
                                                        </td>

                                                    <?php } ?>

                                                    <?php if ($rwgetRole['add_report'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck351" type="checkbox" class="checkBoxClass row_18 col_2" name="addreport" value="1">
                                                                <label for="myCheck"><?php echo $lang['add_report']; ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['update_report'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck351" type="checkbox" class="checkBoxClass row_18 col_3" name="editreport" value="1">
                                                                <label for="myCheck"><?php echo $lang['edit_report']; ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>

                                                </tr>
                                                <?php if ($rwgetRole['delete_report'] == '1') { ?>
                                                    <tr>
                                                        <td>
                                                            <div class="form-group"></div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <?php if ($rwgetRole['delete_report'] == '1') { ?>
                                                            <td colspan="4">
                                                                <div class="checkbox checkbox-success">
                                                                    <input id="myCheck351" type="checkbox" class="checkBoxClass row_18 col_4" name="deletereport" value="1">
                                                                    <label for="myCheck"><?php echo $lang['delete_report']; ?></label>
                                                                </div>
                                                            </td>
                                                        <?php } ?>
                                                    </tr>
                                                <?php
                                                }
                                                ?>

                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php if ($rwgetRole['workflow_task_track'] == '1') { ?>
                                                <tr>
                                                    <td>
                                                        <div class="checkbox checkbox-success txt">
                                                            <input type="checkbox" id="select_row_10" />
                                                            <label for="myCheck"><?php echo $lang['Task_Track_Status'] ?></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <?php if ($rwgetRole['workflow_task_track'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck36" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_10 col_0" name="tsktrk" value="1">
                                                                <label for="myCheck"><?php echo $lang['Task_Track'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php if ($rwgetRole['metadata_search'] == '1' || $rwgetRole['metadata_quick_search'] == '1') { ?>
                                                <tr>
                                                    <td>
                                                        <div class="checkbox checkbox-success txt">
                                                            <input type="checkbox" id="select_row_11" />
                                                            <label><?php echo $lang['MetaData_Search'] ?></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <?php if ($rwgetRole['metadata_search'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck37" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_11 col_0" name="metadataSerach" value="1">
                                                                <label for="myCheck"><?php echo $lang['MetaData_Search'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['metadata_quick_search'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck38" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_11 col_1" name="metaDataQsearch" value="1">
                                                                <label for="myCheck"><?php echo $lang['quich_search'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php
                                            if ($rwgetRole['file_edit'] == '1' || $rwgetRole['file_delete'] == '1' || $rwgetRole['file_anot'] == '1' || $rwgetRole['file_coment'] == '1' || $rwgetRole['file_anot_delete'] == '1' || $rwgetRole['initiate_file'] == '1' || $rwgetRole['pdf_file'] == '1' || $rwgetRole['doc_file'] == '1' || $rwgetRole['excel_file'] == '1' || $rwgetRole['image_file'] == '1' || $rwgetRole['pdf_annotation'] == '1' || $rwgetRole['file_version'] == '1' || $rwgetRole['delete_version'] == '1' || $rwgetRole['update_file'] == '1' || $rwgetRole['video_file'] == '1' || $rwgetRole['audio_file'] == '1' || $rwgetRole['tif_file'] == '1') {
                                            ?>
                                                <tr>
                                                    <td colspan="4">
                                                        <div class="checkbox checkbox-success txt">
                                                            <input type="checkbox" id="select_row_12" />
                                                            <label><?php echo $lang['file_View_Permissions'] ?></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                            <?php }
                                            if ($rwgetRole['file_edit'] == '1' || $rwgetRole['file_delete'] == '1' || $rwgetRole['file_anot'] == '1' || $rwgetRole['file_anot_delete'] == '1') { ?>
                                                <tr>
                                                    <?php if ($rwgetRole['file_edit'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck41" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_0" name="fileEdit" value="1">
                                                                <label for="myCheck"><?php echo $lang['View_MetaData_file'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['file_delete'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck42" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_1" name="fileDelete" value="1">
                                                                <label for="myCheck"><?php echo $lang['Delete'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['file_anot'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck43" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_2" name="fileAnot" value="1">
                                                                <label for="myCheck"><?php echo $lang['Annotation'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['file_anot_delete'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck45" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_3" name="fileAnotDelete" value="1">
                                                                <label for="myCheck"><?php echo $lang['Annotation_Delete'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php if ($rwgetRole['file_coment'] == '1' || $rwgetRole['tif_file'] == '1' || $rwgetRole['pdf_file'] == '1' || $rwgetRole['doc_file'] == '1') { ?>
                                                <tr>
                                                    <?php if ($rwgetRole['file_coment'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck44" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_4" name="fileComent" value="1">
                                                                <label for="myCheck"><?php echo $lang['Comment'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['tif_file'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck40" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_5" name="tiffile" value="1">
                                                                <label for="myCheck"><?php echo $lang['Tiff_File'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['pdf_file'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck46" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_6" name="pdffile" value="1">
                                                                <label for="myCheck"><?php echo $lang['pdf_file'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['doc_file'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck47" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_7" name="docfile" value="1">
                                                                <label for="myCheck"><?php echo $lang['doc_file']; ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php if ($rwgetRole['excel_file'] == '1' || $rwgetRole['image_file'] == '1' || $rwgetRole['video_file'] == '1' || $rwgetRole['audio_file'] == '1') { ?>
                                                <tr>
                                                    <?php if ($rwgetRole['excel_file'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck48" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_8" name="excelfile" value="1">
                                                                <label for="myCheck"><?php echo $lang['excel_file']; ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['audio_file'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck49" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_9" name="audiofile" value="1">
                                                                <label for="myCheck"><?php echo $lang['Audio_file']; ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['video_file'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck50" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_10" name="videofile" value="1">
                                                                <label for="myCheck"><?php echo $lang['Video_file']; ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['image_file'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck51" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_11" name="imagefile" value="1">
                                                                <label for="myCheck"><?php echo $lang['image_file']; ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php if ($rwgetRole['pdf_annotation'] == '1' || $rwgetRole['file_version'] == '1' || $rwgetRole['delete_version'] == '1' || $rwgetRole['update_file'] == '1') { ?>
                                                <tr>
                                                    <?php if ($rwgetRole['pdf_annotation'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck512" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_12" name="annotedpdf" value="1">
                                                                <label for="myCheck"><?php echo $lang['Annotated_Pdf']; ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['file_version'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck513" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_13" name="fileversion" value="1">
                                                                <label for="myCheck"><?php echo $lang['View_File_Version']; ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['delete_version'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck514" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_14" name="delfilevrsn" value="1">
                                                                <label for="myCheck"> <?php echo $lang['Del_File_Version']; ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['update_file'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck515" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_15" name="updatefile" value="1">
                                                                <label for="myCheck"><?php echo $lang['Update_File']; ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php if ($rwgetRole['export_csv'] == '1' || $rwgetRole['move_file'] == '1' || $rwgetRole['copy_file'] == '1' || $rwgetRole['share_file'] == '1') { ?>
                                                <tr>
                                                    <?php if ($rwgetRole['export_csv'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck516" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_16" name="csv" value="1">
                                                                <label for="myCheck"><?php echo $lang['Export_Csv']; ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['move_file'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck517" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_17" name="movefile" value="1">
                                                                <label for="myCheck"><?php echo $lang['Move_Files']; ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['copy_file'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck518" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_18" name="copyfile" value="1">
                                                                <label for="myCheck"><?php echo $lang['Copy_Files']; ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['share_file'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck519" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_19" name="sharefile" value="1">
                                                                <label for="myCheck"><?php echo $lang['Shared_Files']; ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php if ($rwgetRole['checkin_checkout'] == '1' || $rwgetRole['bulk_download'] == '1' || $rwgetRole['xls_download'] == '1' || $rwgetRole['xls_print'] == '1') { ?>
                                                <tr>
                                                    <?php if ($rwgetRole['checkin_checkout'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck520" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_20" name="CheckinOut" value="1">
                                                                <label for="myCheck"><?php echo $lang['Checkin_Checkout']; ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['bulk_download'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck521" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_21" name="bulkDwnld" value="1">
                                                                <label for="myCheck"><?php echo $lang['Bulk_Download']; ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['xls_download'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck522" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_21" name="xlsdownload" value="1">
                                                                <label for="myCheck"><?php echo $lang['Excel_Download']; ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['xls_print'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck523" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_21" name="xlsprint" value="1">
                                                                <label for="myCheck"><?php echo $lang['Excel_Print']; ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                            <?php } ?>

                                            <?php if ($rwgetRole['word_edit'] == '1' || $rwgetRole['view_psd'] == '1' || $rwgetRole['view_cdr'] == '1' || $rwgetRole['delete_page'] == '1') { ?>
                                                <tr>
                                                    <?php if ($rwgetRole['word_edit'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck524" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_22" name="word_edit" value="1">
                                                                <label for="myCheck"><?php echo $lang['word_edit']; ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['view_psd'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck525" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_23" name="view_psd" value="1">
                                                                <label for="myCheck"><?php echo $lang['view_psd']; ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['view_cdr'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck526" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_24" name="view_cdr" value="1">
                                                                <label for="myCheck"><?php echo $lang['view_cdr']; ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['delete_page'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck527" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_25" name="delete_page" value="1">
                                                                <label for="myCheck"><?php echo $lang['delete_page']; ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php if ($rwgetRole['mail_files'] == '1' || $rwgetRole['view_odt'] == '1' || $rwgetRole['view_rtf'] == '1' || $rwgetRole['file_review'] == '1') { ?>
                                                <tr>
                                                    <?php if ($rwgetRole['mail_files'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck528" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_26" name="mail_files" value="1">
                                                                <label for="myCheck"><?php echo $lang['mail_files']; ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>

                                                    <?php if ($rwgetRole['view_odt'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck528" type="checkbox" class="checkBoxClass row_12 col_27" name="odtfile" value="1">
                                                                <label for="myCheck"><?php echo $lang['odt_file']; ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['view_rtf'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck528" type="checkbox" class="checkBoxClass row_12 col_28" name="rtffile" value="1">
                                                                <label for="myCheck"><?php echo $lang['rtf_file']; ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['file_review'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck528" type="checkbox" class="checkBoxClass row_12 col_29" name="filereview" value="1">
                                                                <label for="myCheck"><?php echo $lang['sent_file_review']; ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                            <?php } ?>

                                            <?php if ($rwgetRole['doc_expiry_time'] == '1' || $rwgetRole['lock_file'] == '1' || $rwgetRole['doc_weeding_out'] == '1' || $rwgetRole['doc_share_time'] == '1' || $rwgetRole['rename_document'] == '1') { ?>
                                                <tr>
                                                    <?php if ($rwgetRole['lock_folder'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck41" type="checkbox" class="checkBoxClass row_12 col_30" name="lock_folder" value="1">
                                                                <label for="myCheck"><?php echo $lang['lock_folder'] ?></label>
                                                            </div>
                                                        </td>

                                                    <?php } ?>

                                                    <?php if ($rwgetRole['lock_file'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck41" type="checkbox" class="checkBoxClass row_12 col_34" name="lock_file" value="1">
                                                                <label for="myCheck"><?php echo $lang['lock_file'] ?></label>
                                                            </div>
                                                        </td>

                                                    <?php } ?>

                                                    <?php if ($rwgetRole['doc_weeding_out'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck41" type="checkbox" class="checkBoxClass row_12 col_31" name="weedingouttime" value="1">
                                                                <label for="myCheck"><?php echo $lang['weed_out_time'] ?></label>
                                                            </div>
                                                        </td>

                                                    <?php }
                                                    if ($rwgetRole['doc_share_time'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck41" type="checkbox" class="checkBoxClass row_12 col_32" name="docsharetime" value="1">
                                                                <label for="myCheck"><?php echo $lang['share_document_with_time'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php }
                                                    if ($rwgetRole['doc_expiry_time'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck41" type="checkbox" class="checkBoxClass row_12 col_33" name="expdocument" value="1">
                                                                <label for="myCheck"><?php echo $lang['expired_doc_list'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>

                                                </tr>
                                                <tr>
                                                    <!-- ankit 02 june -->
                                                    <?php if ($rwgetRole['rename_document'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck41" <?php echo ($rwRole['rename_document'] == '1') ? "checked" : ""; ?> type="checkbox" class="checkBoxClass row_12 col_33" name="renmdocument" value="1">
                                                                <label for="myCheck">Rename Document</label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <!-- ankit 02 june -->



                                                </tr>

                                            <?php } ?>


                                            <?php if ($rwgetRole['pdf_download'] == '1' || $rwgetRole['pdf_print'] == '1') { ?>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="checkbox checkbox-success txt">
                                                            <input type="checkbox" id="select_row_14" />
                                                            <label><?php echo $lang['For_pdf_Viewer']; ?></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <?php if ($rwgetRole['pdf_print'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck52" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_14 col_1" name="pdfprint" value="1">
                                                                <label for="myCheck"><?php echo $lang['Pdf_Print']; ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['pdf_download'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck53" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_14 col_2" name="pdfdownload" value="1">
                                                                <label for="myCheck"><?php echo $lang['Pdf_Download']; ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php if ($rwgetRole['view_faq'] == '1' || $rwgetRole['add_faq'] == '1' || $rwgetRole['edit_faq'] == '1' || $rwgetRole['del_faq'] == '1') { ?>
                                                <tr>
                                                    <td colspan="4">
                                                        <div class="checkbox checkbox-success txt">
                                                            <input type="checkbox" id="select_row_15" />
                                                            <label><?php echo $lang['FAQ_Help']; ?></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <?php if ($rwgetRole['view_faq'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck541" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_15 col_0" name="viewfaq" value="1">
                                                                <label for="myCheck"><?php echo $lang['View_Faq']; ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['add_faq'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck54" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_15 col_1" name="addfaq" value="1">
                                                                <label for="myCheck"><?php echo $lang['Add_Faq']; ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['edit_faq'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck55" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_15 col_2" name="editfaq" value="1">
                                                                <label for="myCheck"><?php echo $lang['Edit_Faq']; ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['del_faq'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck56" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_15 col_3" name="delfaq" value="1">
                                                                <label for="myCheck"><?php echo $lang['Delete_Faq']; ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php if ($rwgetRole['view_recycle_bin'] == '1' || $rwgetRole['restore_file'] == '1' || $rwgetRole['permanent_del'] == '1' || $rwgetRole['rename_file'] == '1') { ?>
                                                <tr>
                                                    <td>
                                                        <div class="checkbox checkbox-success txt">
                                                            <input type="checkbox" id="select_row_16" />
                                                            <label><?php echo $lang['Recycle_Bin']; ?></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <?php if ($rwgetRole['view_recycle_bin'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck571" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_16 col_0" name="viewrecycle" value="1">
                                                                <label for="myCheck"><?php echo $lang['View_Recycle_Bin']; ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['restore_file'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck57" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_16 col_1" name="restore" value="1">
                                                                <label for="myCheck"><?php echo $lang['Restore_Files']; ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['permanent_del'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck58" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_16 col_2" name="permntDel" value="1">
                                                                <label for="myCheck"><?php echo $lang['per_dlt']; ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['rename_file'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck58" type="checkbox" class="checkBoxClass row_16 col_3" name="renamefile" value="1">
                                                                <label for="myCheck"><?php echo $lang['rename_file']; ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php if ($rwgetRole['shared_file'] == '1' || $rwgetRole['share_with_me'] == '1' || $rwgetRole['feedback_msg'] == '1') { ?>
                                                <tr>
                                                    <td colspan="20">
                                                        <div class="checkbox checkbox-success txt">
                                                            <input type="checkbox" id="select_row_17" />
                                                            <label> <?php echo $lang['Shared_nd_share_with_me']; ?></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <?php if ($rwgetRole['shared_file'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck59" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_17 col_0" name="sharedFile" value="1">
                                                                <label for="myCheck"><?php echo $lang['View_shared_Files'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['share_with_me'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck60" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_17 col_1" name="shareWithme" value="1">
                                                                <label for="myCheck"><?php echo $lang['View_Share_With_Me'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['feedback_msg'] == '1') { ?>
                                                        <td colspan="2">
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck60" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_17 col_1" name="fbckmsg" value="1">
                                                                <label for="myCheck"><?php echo $lang['Feedback_Message'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php if ($rwgetRole['wf_log'] == '1' || $rwgetRole['review_log'] == '1') { ?>
                                                <tr>
                                                    <td colspan="20">
                                                        <div class="checkbox checkbox-success txt">
                                                            <input type="checkbox" id="select_row_19" />
                                                            <label> <?php echo $lang['log']; ?></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <?php if ($rwgetRole['wf_log'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck161" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_19 col_0" name="wf_log" value="1">
                                                                <label for="myCheck"><?php echo $lang['activity_log'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['review_log'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck162" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_19 col_1" name="review_log" value="1">
                                                                <label for="myCheck"><?php echo $lang['review_log'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php if ($rwgetRole['review_intray'] == '1' || $rwgetRole['review_track'] == '1') { ?>
                                                <tr>
                                                    <td colspan="20">
                                                        <div class="checkbox checkbox-success txt">
                                                            <input type="checkbox" id="select_row_13" />
                                                            <label> <?php echo $lang['reviewer']; ?></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <?php if ($rwgetRole['review_intray'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck61" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_13 col_0" name="review_intray" value="1">
                                                                <label for="myCheck"><?php echo $lang['reviewintray'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['review_track'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck62" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_13 col_1" name="review_track" value="1">
                                                                <label for="myCheck"><?php echo $lang['sentreview'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php if ($rwgetRole['todo_add'] == '1' || $rwgetRole['todo_edit'] == '1' || $rwgetRole['todo_archive'] == '1' || $rwgetRole['todo_view'] == '1') { ?>
                                                <tr>
                                                    <td colspan="20">
                                                        <div class="checkbox checkbox-success txt">
                                                            <input type="checkbox" id="select_row_23" />
                                                            <label> <?php echo $lang['to_do']; ?></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <?php if ($rwgetRole['todo_add'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck63" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_23 col_0" name="todo_add" value="1">
                                                                <label for="myCheck"><?php echo $lang['Add'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['todo_edit'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck64" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_23 col_1" name="todo_edit" value="1">
                                                                <label for="myCheck"><?php echo $lang['Edit'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['todo_archive'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck65" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_23 col_2" name="todo_archive" value="1">
                                                                <label for="myCheck"><?php echo $lang['archive'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['todo_view'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck66" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_23 col_3" name="todo_view" value="1">
                                                                <label for="myCheck"><?php echo $lang['view'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php if ($rwgetRole['appoint_add'] == '1' || $rwgetRole['appoint_edit'] == '1' || $rwgetRole['appoint_archive'] == '1' || $rwgetRole['appoint_view'] == '1') { ?>
                                                <tr>
                                                    <td colspan="20">
                                                        <div class="checkbox checkbox-success txt">
                                                            <input type="checkbox" id="select_row_22" />
                                                            <label> <?php echo $lang['appointments']; ?></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <?php if ($rwgetRole['appoint_add'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck67" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_22 col_0" name="appoint_add" value="1">
                                                                <label for="myCheck"><?php echo $lang['Add'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['appoint_edit'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck68" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_22 col_1" name="appoint_edit" value="1">
                                                                <label for="myCheck"><?php echo $lang['Edit'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['appoint_archive'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck69" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_22 col_2" name="appoint_archive" value="1">
                                                                <label for="myCheck"><?php echo $lang['archive'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['appoint_view'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck70" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_22 col_3" name="appoint_view" value="1">
                                                                <label for="myCheck"><?php echo $lang['view'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php if ($rwgetRole['hindi'] == '1' || $rwgetRole['english'] == '1' || $rwgetRole['app_default'] == '1' || $rwgetRole['customize_label'] == '1') { ?>
                                                <tr>
                                                    <td colspan="20">
                                                        <div class="checkbox checkbox-success txt">
                                                            <input type="checkbox" id="select_row_21" />
                                                            <label> <?php echo $lang['lang']; ?></label>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php if ($rwgetRole['hindi'] == '1' || $rwgetRole['english'] == '1' || $rwgetRole['app_default'] == '1' || $rwgetRole['customize_label'] == '1') { ?>
                                                <tr>
                                                    <?php if ($rwgetRole['hindi'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck1618" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_21 col_0" name="hindi" value="1">
                                                                <label for="myCheck"><?php echo $lang['Hindi'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['english'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck691" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_21 col_1" name="english" value="1">
                                                                <label for="myCheck"><?php echo $lang['English'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['app_default'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck617" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_21 col_2" name="appd" value="1">
                                                                <label for="myCheck"><?php echo $lang['App_default'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['customize_label'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck67" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_21 col_3" name="clabel" value="1">
                                                                <label for="myCheck"><?php echo $lang['edit_label'] ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php if ($rwgetRole['add_holiday'] == '1' || $rwgetRole['edit_holiday'] == '1' || $rwgetRole['view_holiday'] == '1' || $rwgetRole['delete_holiday'] == '1' || $rwgetRole['holiday_calender'] == '1') { ?>
                                                <tr>
                                                    <td colspan="20">

                                                        <div class="checkbox checkbox-success txt">
                                                            <input type="checkbox" id="select_row_20" />
                                                            <label><?= $lang['holiday_manager'] ?> </label>
                                                        </div>

                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php if ($rwgetRole['add_holiday'] == '1' || $rwgetRole['edit_holiday'] == '1' || $rwgetRole['view_holiday'] == '1' || $rwgetRole['delete_holiday'] == '1' || $rwgetRole['holiday_calender'] == '1') { ?>
                                                <tr>
                                                    <?php if ($rwgetRole['add_holiday'] == '1') { ?>
                                                        <td>

                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck159" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_20 col_0" name="addholiday" value="1">
                                                                <label for="myCheck"><?= $lang['add_holiday'] ?></label>
                                                            </div>
                                                        <?php } ?>
                                                        </td>
                                                        <td>
                                                            <?php if ($rwgetRole['edit_holiday'] == '1') { ?>
                                                                <div class="checkbox checkbox-success">
                                                                    <input id="myCheck600" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_20 col_1" name="editholiday" value="1">
                                                                    <label for="myCheck"><?= $lang['edit_holiday'] ?></label>
                                                                </div>
                                                            <?php } ?>
                                                        </td>
                                                        <td>
                                                            <?php if ($rwgetRole['view_holiday'] == '1') { ?>
                                                                <div class="checkbox checkbox-success">
                                                                    <input id="myCheck610" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_20 col_2" name="viewholiday" value="1">
                                                                    <label for="myCheck"><?= $lang['view_holiday'] ?></label>
                                                                </div>
                                                            <?php } ?>
                                                        </td>
                                                        <td>
                                                            <?php if ($rwgetRole['delete_holiday'] == '1') { ?>
                                                                <div class="checkbox checkbox-success">
                                                                    <input id="myCheck601u" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_20 col_3" name="delholiday" value="1">
                                                                    <label for="myCheck"><?= $lang['delete_holiday'] ?></label>
                                                                </div>
                                                            <?php } ?>
                                                        </td>
                                                        <td>
                                                            <?php if ($rwgetRole['holiday_calender'] == '1') { ?>
                                                                <div class="checkbox checkbox-success">
                                                                    <input id="myCheck601k" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_20 col_4" name="holidaycal" value="1">
                                                                    <label for="myCheck"><?= $lang['holiday_view'] ?></label>
                                                                </div>
                                                            <?php } ?>
                                                        </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                            <?php } ?>



                                            <?php if ($rwgetRole['password_policy'] == '1' || $rwgetRole['default_lang_setting'] == '1' || $rwgetRole['doc_exp_setting'] == '1' || $rwgetRole['doc_retention_setting'] == '1' || $rwgetRole['doc_share_setting'] == '1' || $rwgetRole['login_otp'] == '1') { ?>
                                                <tr>
                                                    <td colspan="4">

                                                        <div class="checkbox checkbox-success txt">
                                                            <input type="checkbox" id="select_row_24" />
                                                            <label><?= $lang['Administrative_tool'] . ' ' . $lang['and'] . ' ' . $lang['set_default_lang'] ?> </label>
                                                        </div>
                                                    </td>
                                                </tr>

                                            <?php }
                                            if ($rwgetRole['password_policy'] == '1' || $rwgetRole['default_lang_setting'] == '1' || $rwgetRole['doc_exp_setting'] == '1' || $rwgetRole['doc_retention_setting'] == '1' || $rwgetRole['doc_share_setting'] == '1') { ?>

                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <?php if ($rwgetRole['password_policy'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myChecfek159" type="checkbox" class="checkBoxClass row_24 col_0" name="passpolicy" value="1">
                                                                <label for="myCheck"><?= $lang['Set_Password_Policy'] ?></label>
                                                            </div>
                                                        </td>

                                                    <?php }
                                                    if ($rwgetRole['default_lang_setting'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myChefck159" type="checkbox" class="checkBoxClass row_24 col_1" name="langsetting" value="1">
                                                                <label for="myCheck"><?php echo $lang['set_default_lang']; ?></label>
                                                            </div>
                                                        </td>

                                                    <?php }
                                                    if ($rwgetRole['doc_exp_setting'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheeck159" type="checkbox" class="checkBoxClass row_24 col_2" name="docexpsetting" value="1">
                                                                <label for="myCheck"><?= $lang['expiry_document']; ?></label>
                                                            </div>
                                                        </td>

                                                    <?php }
                                                    if ($rwgetRole['doc_retention_setting'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="mydCheck159" type="checkbox" class="checkBoxClass row_24 col_3" name="docretention" value="1">
                                                                <label for="myCheck"><?= $lang['Retention_document']; ?></label>
                                                            </div>
                                                        </td>

                                                    <?php } ?>
                                                </tr>
                                                <?php if ($rwgetRole['doc_share_setting'] == '1' || $rwgetRole['login_otp'] == '1' || $rwgetRole['login_captcha'] == '1') { ?>
                                                    <tr>
                                                        <td>
                                                            <div class="form-group"></div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <?php if ($rwgetRole['doc_share_setting'] == '1') { ?>
                                                            <td>
                                                                <div class="checkbox checkbox-success">
                                                                    <input id="myChfeck159" type="checkbox" class="checkBoxClass row_24 col_4" name="docsharesetting" value="1">
                                                                    <label for="myCheck"><?= $lang['Share_docs_with_time']; ?></label>
                                                                </div>
                                                            </td>
                                                        <?php } ?>
                                                        <?php if ($rwgetRole['login_otp'] == '1') { ?>
                                                            <td colspan="2">
                                                                <div class="checkbox checkbox-success">
                                                                    <input id="myChfeck15d9" type="checkbox" class="checkBoxClass row_24 col_5" name="emailotp" value="1">
                                                                    <label for="myCheck"><?= $lang['login_with_otp']; ?></label>
                                                                </div>
                                                            </td>
                                                        <?php } ?>

                                                        <?php if ($rwgetRole['login_captcha'] == '1') { ?>
                                                            <td>
                                                                <div class="checkbox checkbox-success">
                                                                    <input id="myChfeck15d9" type="checkbox" class="checkBoxClass row_24 col_6" name="login_captcha" value="1">
                                                                    <label for="myCheck"><?= $lang['login_with_captcha']; ?></label>
                                                                </div>
                                                            </td>
                                                        <?php } ?>

                                                    </tr>

                                            <?php
                                                }
                                            }
                                            ?>


                                            <?php if ($rwgetRole['view_exten'] == '1' || $rwgetRole['add_exten'] == '1' || $rwgetRole['enable_exten'] == '1' || $rwgetRole['delete_exten'] == '1') { ?>

                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4">

                                                        <div class="checkbox checkbox-success txt">
                                                            <input type="checkbox" id="select_row_25" />
                                                            <label><?= $lang['managefile_exten']; ?> </label>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <?php if ($rwgetRole['view_exten'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="mydCheck160" type="checkbox" class="checkBoxClass row_25 col_1" name="view_exten" value="1">
                                                                <label for="myCheck"><?= $lang['view_exten']; ?></label>
                                                            </div>
                                                        </td>
                                                    <?php } ?>

                                                    <?php if ($rwgetRole['add_exten'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myChecfek159" type="checkbox" class="checkBoxClass row_25 col_2" name="add_exten" value="1">
                                                                <label for="myCheck"><?= $lang['add_exten'] ?></label>
                                                            </div>
                                                        </td>

                                                    <?php }
                                                    if ($rwgetRole['enable_exten'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myChefck159" type="checkbox" class="checkBoxClass row_25 col_3" name="enable_exten" value="1">
                                                                <label for="myCheck"><?php echo $lang['enable_exten']; ?></label>
                                                            </div>
                                                        </td>

                                                    <?php }
                                                    if ($rwgetRole['delete_exten'] == '1') { ?>
                                                        <td>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheeck159" type="checkbox" class="checkBoxClass row_25 col_4" name="delete_exten" value="1">
                                                                <label for="myCheck"><?= $lang['delete_exten']; ?></label>
                                                            </div>
                                                        </td>

                                                    <?php } ?>
                                                </tr>

                                            <?php } ?>

                                            <?php if ($rwgetRole['create_client'] == '1') { ?>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                                <tr>

                                                    <td colspan="20">

                                                        <div class="checkbox checkbox-success txt">
                                                            <input type="checkbox" id="select_row_177" />
                                                            <label><?php echo $lang['c_create']; ?></label>
                                                        </div>

                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <?php if ($rwgetRole['create_client'] == '1') { ?>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck79" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_177 col_0" name="ccreate" value="1">
                                                                <label for="myCheck"><?php echo $lang['c_create']; ?></label>
                                                            </div>
                                                        <?php } ?>
                                                    </td>


                                                </tr>
                                            <?php } ?>
                                            <?php if ($rwgetRole['ezeescan'] == '1') { ?>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>

                                                        <div class="checkbox checkbox-success txt">
                                                            <input type="checkbox" id="select_row_523" />
                                                            <label for="myCheck"><?php echo $lang['ezeescan']; ?></label>
                                                        </div>

                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <?php if ($rwgetRole['ezeescan'] == '1') { ?>
                                                            <div class="checkbox checkbox-success">
                                                                <input id="myCheck80" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_523 col_0" name="ezeescan" value="1">
                                                                <label for="myCheck"><?php echo $lang['ezeescan']; ?></label>
                                                            </div>
                                                        <?php } ?>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <div class="form-group"></div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </table>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal"><?php echo $lang['Close'] ?></button>
                                    <button type="submit" name="addRole" class="btn btn-primary"><?php echo $lang['Save'] ?></button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div><!-- /.modal -->
            </div>
        </div>
        <!-- END wrapper -->

        <?php require_once './application/pages/footerForjs.php'; ?>

        <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>

        <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                $('form').parsley();
                addTranslationClass();
            });
            $(".select2").select2();
        </script>
        <script>
            $("a#editRow").click(function() {
                var $id = $(this).attr('data');
                var $row = $(this).closest('tr');
                var name = '';
                var values = [];
                values = $row.find('td:nth-child(2)').map(function() {
                    var $this = $(this);
                    if ($this.hasClass('actions')) {

                    } else {
                        name = $.trim($this.text());
                        //$.trim( $this.text());
                    }
                    var token = $("input[name='token']").val();
                    $("#con-close-modal .modal-title").text("" + name + " <?php echo $lang['Update_Profile']; ?>");
                    $.post("application/ajax/updateRole.php", {
                        ID: $id,
                        token: token
                    }, function(result, status) {
                        if (status == 'success') {
                            $("#modalModify").html(result);
                            getToken();

                        }
                    });
                });
            });

            $("a#removeRow").click(function() {
                var id = $(this).attr('data');
                $("#uid").val(id);
            });
        </script>
        <script>
            function getRegexMatches(regex, string) {
                if (!(regex instanceof RegExp)) {
                    return "ERROR";
                } else {
                    if (!regex.global) {
                        // If global flag not set, create new one.
                        var flags = "g";
                        if (regex.ignoreCase)
                            flags += "i";
                        if (regex.multiline)
                            flags += "m";
                        if (regex.sticky)
                            flags += "y";
                        regex = RegExp(regex.source, flags);
                    }
                }
                var matches = [];
                var match = regex.exec(string);
                while (match) {
                    if (match.length > 2) {
                        var group_matches = [];
                        for (var i = 1; i < match.length; i++) {
                            group_matches.push(match[i]);
                        }
                        matches.push(group_matches);
                    } else {
                        matches.push(match[1]);
                    }
                    match = regex.exec(string);
                }
                return matches;
            }

            /**
             * get the select_row or select_col checkboxes dependening on the selectType row/col
             */
            function getSelectCheckboxes(selectType) {
                var regex = new RegExp("select_" + selectType + "_");
                var result = $('input').filter(function() {
                    return this.id.match(regex);
                });
                return result;
            }

            /**
             * matrix selection logic 
             * the goal is to provide select all / select row x / select col x
             * checkboxes that will allow to 
             *   select all: select all grid elements 
             *   select row: select the grid elements in the given row
             *   select col: select the grid elements in the given col
             *
             *   There is a naming convention for the ids and css style classes of the the selectors and elements:
             *   select all -> id: selectall
             *   select row -> id: select_row_row e.g. select_row_2
             *   select col -> id: select_col_col e.g. select_col_3 
             *   grid element -> class checkBoxClass col_col row_row e.g. checkBoxClass row_2 col_3
             */
            $(document).ready(function() {
                // handle click event for Select all check box
                $("#selectall").click(function() {
                    // set the checked property of all grid elements to be the same as
                    // the state of the SelectAll check box
                    var state = $("#selectall").prop('checked');
                    $(".checkBoxClass").prop('checked', state);
                    getSelectCheckboxes('row').prop('checked', state);
                    getSelectCheckboxes('col').prop('checked', state);
                });

                // handle clicks within the grid
                $(".checkBoxClass").on("click", function() {
                    // get the list of grid checkbox elements
                    // all checkboxes
                    var all = $('.checkBoxClass');
                    // all select row check boxes
                    var rows = getSelectCheckboxes('row');
                    // all select columnn check boxes
                    var cols = getSelectCheckboxes('col');
                    // console.log("rows: "+rows.length+", cols:"+cols.length+" total: "+all.length);
                    // get the total number of checkboxes in the grid
                    var allLen = all.length;
                    // get the number of checkboxes in the checked state
                    var filterLen = all.filter(':checked').length;
                    // console.log(allLen+"-"+filterLen);
                    // if all checkboxes are in the checked state  
                    // set the state of the selectAll checkbox to checked to be able
                    // to deselect all at once, otherwise set it to unchecked to be able to select all at once
                    if (allLen == filterLen) {
                        $("#selectall").prop("checked", true);
                    } else {
                        $("#selectall").prop("checked", false);
                    }

                    // now check the completeness of the rows
                    for (var row = 0; row < rows.length; row++) {
                        var rowall = $('.row_' + row);
                        console.log(row);
                        var rowchecked = rowall.filter(':checked');
                        console.log(rowall.length + ',' + rowchecked.length);
                        if (rowall.length == rowchecked.length) {
                            $("#select_row_" + row).prop("checked", true);
                        } else {
                            $("#select_row_" + row).prop("checked", false);
                        }
                    }
                });

                $('input')
                    .filter(function() {
                        return this.id.match(/select_row_|select_col_/);
                    }).on("click", function() {
                        var matchRowColArr = getRegexMatches(/select_(row|col)_([0-9]+)/, this.id);
                        var matchRowCol = matchRowColArr[0];
                        // console.log(matchRowCol);
                        if (matchRowCol.length == 2) {
                            var selectType = matchRowCol[0]; // e.g. row
                            var selectIndex = matchRowCol[1]; // e.g. 2
                            console.log(this.id + " clicked to select " + selectType + " " + selectIndex);
                            $("." + selectType + "_" + selectIndex).prop('checked', $("#select_" + selectType + "_" + selectIndex).prop('checked'));
                        }
                    });
            });

            //limit filter
            var url = window.location.href + "?";

            function removeParam(key, sourceURL) {
                sourceURL = String(sourceURL).replace("#/", "");
                var rtn = sourceURL.split("?")[0],
                    param,
                    params_arr = [],
                    queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";
                if (queryString !== "") {
                    params_arr = queryString.split("&");
                    for (var i = params_arr.length - 1; i >= 0; i -= 1) {
                        param = params_arr[i].split("=")[0];
                        if (param === key) {
                            params_arr.splice(i, 1);
                        }
                    }
                    rtn = rtn + "?" + params_arr.join("&");
                } else {
                    rtn = rtn + '?';
                }
                return rtn;
            }
            jQuery(document).ready(function($) {
                $("#limit").change(function() {
                    lval = $(this).val();
                    url = removeParam("limit", url);
                    url = removeParam("token", url);
                    url = url + "&limit=" + lval;
                    console.log(url);
                    window.open(url, "_parent");
                });
            });
        </script>
        <!-- end select all or none-->

        <?php
        if (isset($_POST['addRole'], $_POST['token'])) {

            $roleName = xss_clean(trim($_POST['roleName']));
            $roleName = preg_replace('/[^\w$\x{0080}-\x{FFFF}@#$&!%()_ <>]+/u', "", $roleName);
            $roleName = mysqli_real_escape_string($db_con, $roleName);

            //Dashboard
            if (!empty($_POST['mydms'])) {
                $mydms = 1;
            } else {
                $mydms = 0;
            }
            if (!empty($_POST['mytsk'])) {
                $mytsk = 1;
            } else {
                $mytsk = 0;
            }
            if (!empty($_POST['dashEditPro'])) {
                $dashEditPro = 1;
            } else {
                $dashEditPro = 0;
            }
            if (!empty($_POST['dashQury'])) {
                $dashQury = 1;
            } else {
                $dashQury = 0;
            }
            if (!empty($_POST['num_folders'])) {
                $num_Folders = 1;
            } else {
                $num_Folders = 0;
            }
            if (!empty($_POST['num_files'])) {
                $num_Files = 1;
            } else {
                $num_Files = 0;
            }
            if (!empty($_POST['memory_use'])) {
                $memory_Use = 1;
            } else {
                $memory_Use = 0;
            }
            //User Manager
            if (!empty($_POST['usrAdd'])) {
                $usrAdd = 1;
            } else {
                $usrAdd = 0;
            }
            if (!empty($_POST['usrmodi'])) {
                $usrmodi = 1;
            } else {
                $usrmodi = 0;
            }

            if (!empty($_POST['usrDelete'])) {
                $usrDelete = 1;
            } else {
                $usrDelete = 0;
            }
            if (!empty($_POST['usrView'])) {
                $usrView = 1;
            } else {
                $usrView = 0;
            }

            //Authorization
            if (!empty($_POST['strgAuth'])) {
                $strgAuth = 1;
            } else {
                $strgAuth = 0;
            }

            //Group Manager
            if (!empty($_POST['grpAdd'])) {
                $grpAdd = 1;
            } else {
                $grpAdd = 0;
            }
            if (!empty($_POST['grpDelete'])) {
                $grpDelete = 1;
            } else {
                $grpDelete = 0;
            }
            if (!empty($_POST['grpModi'])) {
                $grpModi = 1;
            } else {
                $grpModi = 0;
            }
            if (!empty($_POST['grpView'])) {
                $grpView = 1;
            } else {
                $grpView = 0;
            }

            //User Role Manager
            if (!empty($_POST['usrRoleAdd'])) {
                $usrRoleAdd = 1;
            } else {
                $usrRoleAdd = 0;
            }
            if (!empty($_POST['usrRoleDel'])) {
                $usrRoleDel = 1;
            } else {
                $usrRoleDel = 0;
            }
            if (!empty($_POST['usrRoleModi'])) {
                $usrRoleModi = 1;
            } else {
                $usrRoleModi = 0;
            }
            if (!empty($_POST['usrRoleView'])) {
                $usrRoleView = 1;
            } else {
                $usrRoleView = 0;
            }

            //Upload/Import
            if (!empty($_POST['bulkUpld'])) {
                $bulkUpld = 1;
            } else {
                $bulkUpld = 0;
            }

            //MetaData Registry
            if (!empty($_POST['metaDataAdd'])) {
                $metaDataAdd = 1;
            } else {
                $metaDataAdd = 0;
            }

            if (!empty($_POST['meteDataView'])) {
                $meteDataView = 1;
            } else {
                $meteDataView = 0;
            }
            if (!empty($_POST['meteDataedit'])) {
                $meteDataedit = 1;
            } else {
                $meteDataedit = 0;
            }
            if (!empty($_POST['meteDatadelete'])) {
                $meteDatadelete = 1;
            } else {
                $meteDatadelete = 0;
            }
            if (!empty($_POST['meteDataAsin'])) {
                $meteDataAsin = 1;
            } else {
                $meteDataAsin = 0;
            }

            //Storage Manager
            if (!empty($_POST['strgCreate'])) {
                $strgCreate = 1;
            } else {
                $strgCreate = 0;
            }
            if (!empty($_POST['strgAddChild'])) {
                $strgAddChild = 1;
            } else {
                $strgAddChild = 0;
            }
            if (!empty($_POST['strgUpldDoc'])) {
                $strgUpldDoc = 1;
            } else {
                $strgUpldDoc = 0;
            }
            if (!empty($_POST['strgModi'])) {
                $strgModi = 1;
            } else {
                $strgModi = 0;
            }
            if (!empty($_POST['strgDelete'])) {
                $strgDelete = 1;
            } else {
                $strgDelete = 0;
            }
            if (!empty($_POST['strgMove'])) {
                $strgMove = 1;
            } else {
                $strgMove = 0;
            }
            if (!empty($_POST['strgCopy'])) {
                $strgCopy = 1;
            } else {
                $strgCopy = 0;
            }

            //Audit Trail
            if (!empty($_POST['auditTrlUsr'])) {
                $auditTrlUsr = 1;
            } else {
                $auditTrlUsr = 0;
            }
            if (!empty($_POST['auditTrlStrg'])) {
                $auditTrlStrg = 1;
            } else {
                $auditTrlStrg = 0;
            }

            //Workflow Management
            if (!empty($_POST['wrkflwCreate'])) {
                $wrkflwCreate = 1;
            } else {
                $wrkflwCreate = 0;
            }
            if (!empty($_POST['wrkflwView'])) {
                $wrkflwView = 1;
            } else {
                $wrkflwView = 0;
            }
            if (!empty($_POST['wrkflwEdit'])) {
                $wrkflwEdit = 1;
            } else {
                $wrkflwEdit = 0;
            }
            if (!empty($_POST['wrkflwDel'])) {
                $wrkflwDel = 1;
            } else {
                $wrkflwDel = 0;
            }

            if (!empty($_POST['wrkflwStep'])) {
                $wrkflwStep = 1;
            } else {
                $wrkflwStep = 0;
            }
            if (!empty($_POST['wrkflwIniFile'])) {
                $wrkflwIniFile = 1;
            } else {
                $wrkflwIniFile = 0;
            }

            if (!empty($_POST['InitiateFile'])) {
                $initiateFile = 1;
            } else {
                $initiateFile = 0;
            }

            //Task Track Status:
            if (!empty($_POST['tsktrk'])) {
                $tsktrk = 1;
            } else {
                $tsktrk = 0;
            }
            //MetaData Search
            if (!empty($_POST['metadataSerach'])) {
                $metadataSerach = 1;
            } else {
                $metadataSerach = 0;
            }

            if (!empty($_POST['metaDataQsearch'])) {
                $metaDataQsearch = 1;
            } else {
                $metaDataQsearch = 0;
            }

            //file

            if (!empty($_POST['fileEdit'])) {
                $fileEdit = 1;
            } else {
                $fileEdit = 0;
            }

            if (!empty($_POST['fileDelete'])) {
                $fileDelete = 1;
            } else {
                $fileDelete = 0;
            }

            if (!empty($_POST['fileAnot'])) {
                $fileAnot = 1;
            } else {
                $fileAnot = 0;
            }

            if (!empty($_POST['fileComent'])) {
                $fileComent = 1;
            } else {
                $fileComent = 0;
            }

            if (!empty($_POST['fileAnotDelete'])) {
                $fileAnotDelete = 1;
            } else {
                $fileAnotDelete = 0;
            }

            if (!empty($_POST['pdffile'])) {
                $pdffile = 1;
            } else {
                $pdffile = 0;
            }
            if (!empty($_POST['docfile'])) {
                $docfile = 1;
            } else {
                $docfile = 0;
            }
            if (!empty($_POST['excelfile'])) {
                $excelfile = 1;
            } else {
                $excelfile = 0;
            }
            if (!empty($_POST['audiofile'])) {
                $audiofile = 1;
            } else {
                $audiofile = 0;
            }
            if (!empty($_POST['videofile'])) {
                $videofile = 1;
            } else {
                $videofile = 0;
            }
            if (!empty($_POST['imagefile'])) {
                $imagefile = 1;
            } else {
                $imagefile = 0;
            }
            if (!empty($_POST['pdfprint'])) {
                $pdfprint = 1;
            } else {
                $pdfprint = 0;
            }
            if (!empty($_POST['pdfdownload'])) {
                $pdfdownload = 1;
            } else {
                $pdfdownload = 0;
            }
            if (!empty($_POST['annotedpdf'])) {
                $annotedpdf = 1;
            } else {
                $annotedpdf = 0;
            }
            if (!empty($_POST['fileversion'])) {
                $fileversion = 1;
            } else {
                $fileversion = 0;
            }
            if (!empty($_POST['delfilevrsn'])) {
                $delfilevrsn = 1;
            } else {
                $delfilevrsn = 0;
            }
            if (!empty($_POST['updatefile'])) {
                $updatefile = 1;
            } else {
                $updatefile = 0;
            }
            if (!empty($_POST['auditwf'])) {
                $Auditwf = 1;
            } else {
                $Auditwf = 0;
            }

            if (!empty($_POST['mailconfigYes'])) {
                $MailconfigYes = 1;
            } else {
                $MailconfigYes = 0;
            }

            if (!empty($_POST['onlineUser'])) {
                $onlineUser = 1;
            } else {
                $onlineUser = 0;
            }
            if (!empty($_POST['tiffile'])) {
                $tiffile = 1;
            } else {
                $tiffile = 0;
            }
            //for faq
            if (!empty($_POST['viewfaq'])) {
                $viewfaq = 1;
            } else {
                $viewfaq = 0;
            }
            if (!empty($_POST['addfaq'])) {
                $addfaq = 1;
            } else {
                $addfaq = 0;
            }
            if (!empty($_POST['editfaq'])) {
                $editfaq = 1;
            } else {
                $editfaq = 0;
            }
            if (!empty($_POST['delfaq'])) {
                $delfaq = 1;
            } else {
                $delfaq = 0;
            }
            //Restore files
            if (!empty($_POST['viewrecycle'])) {
                $viewrecycle = 1;
            } else {
                $viewrecycle = 0;
            }
            if (!empty($_POST['restore'])) {
                $restoreFile = 1;
            } else {
                $restoreFile = 0;
            }
            if (!empty($_POST['permntDel'])) {
                $permanentDel = 1;
            } else {
                $permanentDel = 0;
            }

            if (!empty($_POST['renamefile'])) {
                $renamefile = 1;
            } else {
                $renamefile = 0;
            }

            //for shared/share with me files
            if (!empty($_POST['sharedFile'])) {
                $sharedFile = 1;
            } else {
                $sharedFile = 0;
            }
            if (!empty($_POST['shareWithme'])) {
                $shareWithme = 1;
            } else {
                $shareWithme = 0;
            }
            if (!empty($_POST['csv'])) {
                $exportcsv = 1;
            } else {
                $exportcsv = 0;
            }
            if (!empty($_POST['movefile'])) {
                $movefile = 1;
            } else {
                $movefile = 0;
            }
            if (!empty($_POST['copyfile'])) {
                $copyfile = 1;
            } else {
                $copyfile = 0;
            }
            if (!empty($_POST['sharefile'])) {
                $sharefile = 1;
            } else {
                $sharefile = 0;
            }
            if (!empty($_POST['CheckinOut'])) {
                $CheckinOut = 1;
            } else {
                $CheckinOut = 0;
            }
            if (!empty($_POST['bulkDwnld'])) {
                $bulkDwnld = 1;
            } else {
                $bulkDwnld = 0;
            }

            // Workflow Report
            if (!empty($_POST['wrkflwInvl'])) {
                $wrkflwInvl = 1;
            } else {
                $wrkflwInvl = 0;
            }
            if (!empty($_POST['wrkflwRun'])) {
                $wrkflwRun = 1;
            } else {
                $wrkflwRun = 0;
            }

            $viewreport = ((!empty($_POST['viewreport'])) ? 1 : 0);
            $addreport = ((!empty($_POST['addreport'])) ? 1 : 0);
            $editreport = ((!empty($_POST['editreport'])) ? 1 : 0);
            $deletereport = ((!empty($_POST['deletereport'])) ? 1 : 0);

            if (!empty($_POST['fbckmsg'])) {
                $fbckmsg = 1;
            } else {
                $fbckmsg = 0;
            }
            if (!empty($_POST['mailList'])) {
                $mailList = 1;
            } else {
                $mailList = 0;
            }
            if (!empty($_POST['xlsdownload'])) {
                $xlsdownload = 1;
            } else {
                $xlsdownload = 0;
            }
            if (!empty($_POST['xlsprint'])) {
                $xlsprint = 1;
            } else {
                $xlsprint = 0;
            }
            if (!empty($_POST['delete_page'])) {
                $delete_page = 1;
            } else {
                $delete_page = 0;
            }
            if (!empty($_POST['wf_log'])) {
                $wf_log = 1;
            } else {
                $wf_log = 0;
            }
            if (!empty($_POST['review_log'])) {
                $review_log = 1;
            } else {
                $review_log = 0;
            }
            if (!empty($_POST['review_intray'])) {
                $review_intray = 1;
            } else {
                $review_intray = 0;
            }
            if (!empty($_POST['review_track'])) {
                $review_track = 1;
            } else {
                $review_track = 0;
            }

            if (!empty($_POST['todo_add'])) {
                $todo_add = 1;
            } else {
                $todo_add = 0;
            }
            if (!empty($_POST['todo_edit'])) {
                $todo_edit = 1;
            } else {
                $todo_edit = 0;
            }
            if (!empty($_POST['todo_archive'])) {
                $todo_archive = 1;
            } else {
                $todo_archive = 0;
            }
            if (!empty($_POST['todo_view'])) {
                $todo_view = 1;
            } else {
                $todo_view = 0;
            }

            if (!empty($_POST['appoint_add'])) {
                $appoint_add = 1;
            } else {
                $appoint_add = 0;
            }
            if (!empty($_POST['appoint_edit'])) {
                $appoint_edit = 1;
            } else {
                $appoint_edit = 0;
            }
            if (!empty($_POST['appoint_archive'])) {
                $appoint_archive = 1;
            } else {
                $appoint_archive = 0;
            }
            if (!empty($_POST['appoint_view'])) {
                $appoint_view = 1;
            } else {
                $appoint_view = 0;
            }
            if (!empty($_POST['add_page_inbtwn'])) {
                $add_page_inbtwn = 1;
            } else {
                $add_page_inbtwn = 0;
            }
            if (!empty($_POST['add_page_no'])) {
                $add_page_no = 1;
            } else {
                $add_page_no = 0;
            }
            if (!empty($_POST['word_edit'])) {
                $word_edit = 1;
            } else {
                $word_edit = 0;
            }
            if (!empty($_POST['view_psd'])) {
                $view_psd = 1;
            } else {
                $view_psd = 0;
            }
            if (!empty($_POST['view_cdr'])) {
                $view_cdr = 1;
            } else {
                $view_cdr = 0;
            }
            //language
            if (!empty($_POST['hindi'])) {
                $hindi = 1;
            } else {
                $hindi = 0;
            }
            if (!empty($_POST['english'])) {
                $english = 1;
            } else {
                $english = 0;
            }
            if (!empty($_POST['clabel'])) {
                $clabel = 1;
            } else {
                $clabel = 0;
            }
            if (!empty($_POST['appd'])) {
                $appd = 1;
            } else {
                $appd = 0;
            }
            if (!empty($_POST['status'])) {
                $status = 1;
            } else {
                $status = 0;
            }
            if (!empty($_POST['priority'])) {
                $priority = 1;
            } else {
                $priority = 0;
            }
            if (!empty($_POST['calendar'])) {
                $calendar = 1;
            } else {
                $calendar = 0;
            }
            if (!empty($_POST['delstrglog'])) {
                $delstrglog = 1;
            } else {
                $delstrglog = 0;
            }
            if (!empty($_POST['delusrlog'])) {
                $deluserlog = 1;
            } else {
                $deluserlog = 0;
            }
            if (!empty($_POST['delwflog'])) {
                $delwflog = 1;
            } else {
                $delwflog = 0;
            }
            if (!empty($_POST['user_graph'])) {
                $user_graph = 1;
            } else {
                $user_graph = 0;
            }
            if (!empty($_POST['export_user'])) {
                $export_user = 1;
            } else {
                $export_user = 0;
            }
            if (!empty($_POST['import_user'])) {
                $import_user = 1;
            } else {
                $import_user = 0;
            }
            if (!empty($_POST['user_activate_deactivate'])) {
                $user_activate_deactivate = 1;
            } else {
                $user_activate_deactivate = 0;
            }
            //add holiday
            if (!empty($_POST['addholiday'])) {
                $addholiday = $_POST['addholiday'];
            } else {
                $addholiday = 0;
            }
            //edit holiday
            if (!empty($_POST['editholiday'])) {
                $editholiday = $_POST['editholiday'];
            } else {
                $editholiday = 0;
            }
            // view holiday
            if (!empty($_POST['viewholiday'])) {
                $viewholiday = $_POST['viewholiday'];
            } else {
                $viewholiday = 0;
            }
            //delete holiday
            if (!empty($_POST['delholiday'])) {
                $delholiday = $_POST['delholiday'];
            } else {
                $delholiday = 0;
            }
            //view  holiday calender
            if (!empty($_POST['holidaycal'])) {
                $holidaycal = $_POST['holidaycal'];
            } else {
                $holidaycal = 0;
            }
            //mail file from storage.
            if (!empty($_POST['mail_files'])) {
                $mail_files = $_POST['mail_files'];
            } else {
                $mail_files = 0;
            }
            //for frequently save query
            $savequery = ((!empty($_POST['savequery'])) ? 1 : 0);
            $uploadlog = ((!empty($_POST['uploadlog'])) ? 1 : 0);

            //For Client Create.
            if (!empty($_POST['ccreate'])) {
                $ccreate = $_POST['ccreate'];
            } else {
                $ccreate = 0;
            }
            //For Ezeescan
            if (!empty($_POST['ezeescan'])) {
                $ezeescan = $_POST['ezeescan'];
            } else {
                $ezeescan = 0;
            }

            $passpolicy = ((!empty($_POST['passpolicy'])) ? 1 : 0);

            //feature control role
            $langsetting = ((!empty($_POST['langsetting'])) ? 1 : 0);
            $docexpsetting = ((!empty($_POST['docexpsetting'])) ? 1 : 0);
            $docretentionsetting = ((!empty($_POST['docretention'])) ? 1 : 0);
            $docsharesetting = ((!empty($_POST['docsharesetting'])) ? 1 : 0);

            // storage New featues
            $view_odt = ((!empty($_POST['odtfile'])) ? 1 : 0);
            $view_rtf = ((!empty($_POST['rtffile'])) ? 1 : 0);
            $file_review = ((!empty($_POST['filereview'])) ? 1 : 0);
            $doc_weeding_out = ((!empty($_POST['weedingouttime'])) ? 1 : 0);
            $lock_folder = ((!empty($_POST['lock_folder'])) ? 1 : 0);
            $lock_file = ((!empty($_POST['lock_file'])) ? 1 : 0);
            $doc_share_time = ((!empty($_POST['docsharetime'])) ? 1 : 0);
            $doc_expiry_time = ((!empty($_POST['expdocument'])) ? 1 : 0);
            $folder_upload = ((!empty($_POST['bulkUpldfolder'])) ? 1 : 0);
            //for rename document files
            $File_Rename = ((!empty($_POST['renmdocument'])) ? 1 : 0);
            //email otp @dv 02-apr-20
            $emailotp = ((!empty($_POST['emailotp'])) ? 1 : 0);


            $view_exten = ((!empty($_POST['view_exten'])) ? 1 : 0);
            $add_exten = ((!empty($_POST['add_exten'])) ? 1 : 0);
            $enable_exten = ((!empty($_POST['enable_exten'])) ? 1 : 0);
            $delete_exten = ((!empty($_POST['delete_exten'])) ? 1 : 0);
            $login_captcha = ((!empty($_POST['login_captcha'])) ? 1 : 0);

            $rolevalidate = mysqli_query($db_con, "select * from tbl_user_roles where user_role='$roleName'") or die('Error: dd' . mysqli_error($db_con));
            if (mysqli_num_rows($rolevalidate) >= 1) {
                echo '<script>taskFailed("userRole","' . $lang['Role_Already_Exist'] . '");</script>';
            } else {
                mysqli_set_charset($db_con, "utf8");
                $insertRole = mysqli_query($db_con, "insert into tbl_user_roles (user_role, dashboard_mydms, dashboard_mytask,"
                    . " dashboard_edit_profile, dashboard_query, create_user, modify_userlist, delete_userlist, view_userlist,"
                    . " storage_auth_plcy, add_group, delete_group, modify_group, view_group_list, role_add, role_delete, role_modi, role_view, bulk_upload,"
                    . " add_metadata, view_metadata, assign_metadata, create_storage, create_child_storage,"
                    . " upload_doc_storage, modify_storage_level, delete_storage_level, move_storage_level,"
                    . " copy_storage_level, view_user_audit, view_storage_audit, create_workflow, view_workflow_list,"
                    . " edit_workflow, delete_workflow, workflow_step, workflow_initiate_file, workflow_task_track,"
                    . " metadata_search, metadata_quick_search, file_edit, file_delete, file_anot, file_coment, file_anot_delete, initiate_file,"
                    . "num_of_folder, num_of_file, memory_used, pdf_file, doc_file, excel_file, image_file, audio_file, video_file, pdf_print, pdf_download,"
                    . "pdf_annotation, file_version, delete_version, update_file, workflow_audit, email_config, online_user, tif_file,"
                    . " view_faq, add_faq, edit_faq, del_faq, view_recycle_bin, restore_file, permanent_del, shared_file, share_with_me, export_csv,"
                    . "move_file, copy_file, share_file, checkin_checkout, bulk_download, involve_workflow, run_workflow, feedback_msg, mail_lists,"
                    . "xls_download,xls_print,delete_page,wf_log,review_log,review_intray,review_track,todo_add,todo_edit,todo_archive,todo_view,"
                    . "appoint_add,appoint_edit,appoint_archive,appoint_view,add_page_inbtwn,add_page_no, edit_metadata, delete_metadata,word_edit,"
                    . "view_psd,view_cdr, hindi,english,app_default,customize_label, delete_user_log, delete_storage_log,delete_wf_log,status_wf,"
                    . "priority_wf,calendar_wf,user_graph,export_user,import_user,user_activate_deactivate,"
                    . "add_holiday,view_holiday,edit_holiday,delete_holiday,holiday_calender,mail_files,save_query, upload_logs,create_client,ezeescan,"
                    . "view_report,add_report,update_report,delete_report, password_policy, default_lang_setting,"
                    . "doc_exp_setting,doc_retention_setting,doc_share_setting, view_odt, view_rtf, file_review, lock_file, lock_folder, doc_weeding_out, "
                    . "doc_share_time, doc_expiry_time, folder_upload, rename_file,login_otp, view_exten, add_exten, enable_exten, delete_exten, login_captcha,rename_document) values ('$roleName','$mydms','$mytsk','$dashEditPro','$dashQury','$usrAdd',"
                    . "'$usrmodi','$usrDelete','$usrView','$strgAuth','$grpAdd','$grpDelete','$grpModi','$grpView','$usrRoleAdd','$usrRoleDel',"
                    . "'$usrRoleModi','$usrRoleView','$bulkUpld','$metaDataAdd','$meteDataView','$meteDataAsin','$strgCreate','$strgAddChild','$strgUpldDoc',"
                    . "'$strgModi','$strgDelete','$strgMove','$strgCopy','$auditTrlUsr','$auditTrlStrg','$wrkflwCreate','$wrkflwView','$wrkflwEdit',"
                    . "'$wrkflwDel','$wrkflwStep','$wrkflwIniFile','$tsktrk','$metadataSerach','$metaDataQsearch','$fileEdit','$fileDelete','$fileAnot','$fileComent','$fileAnotDelete','$initiateFile',"
                    . "'$num_Folders', '$num_Files', '$memory_Use', '$pdffile', '$docfile', '$excelfile', '$imagefile', '$audiofile', '$videofile', '$pdfprint', '$pdfdownload', '$annotedpdf', '$fileversion', "
                    . "'$delfilevrsn', '$updatefile', '$Auditwf', '$MailconfigYes', '$onlineUser','$tiffile', '$viewfaq', '$addfaq', '$editfaq', '$delfaq', '$viewrecycle', '$restoreFile', '$permanentDel', "
                    . "'$sharedFile', '$shareWithme', '$exportcsv', '$movefile', '$copyfile', '$sharefile', '$CheckinOut', '$bulkDwnld', '$wrkflwInvl', '$wrkflwRun', '$fbckmsg', '$mailList','$xlsdownload',"
                    . "'$xlsprint','$delete_page','$wf_log','$review_log','$review_intray','$review_track','$todo_add','$todo_edit','$todo_archive','$todo_view','$appoint_add','$appoint_edit',"
                    . "'$appoint_archive','$appoint_view','$add_page_inbtwn','$add_page_no', '$meteDataedit','$meteDatadelete','$word_edit','$view_psd','$view_cdr', '$hindi', '$english','$appd',"
                    . "'$clabel','$deluserlog', '$delstrglog', '$delwflog','$status', '$priority', '$calendar','$user_graph','$export_user',"
                    . "'$import_user','$user_activate_deactivate', '$addholiday', '$viewholiday', '$editholiday','$delholiday','$holidaycal','$mail_files','$savequery', '$uploadlog','$ccreate','$ezeescan',"
                    . " '$viewreport', '$addreport', '$editreport', '$deletereport', '$passpolicy','$langsetting', '$docexpsetting', '$docretentionsetting', '$docsharesetting', '$view_odt', '$view_rtf', '$file_review', "
                    . "'$lock_file', '$lock_folder', '$doc_weeding_out', '$doc_share_time', '$doc_expiry_time', '$folder_upload', '$renamefile', '$emailotp', '$view_exten','$add_exten','$enable_exten','$delete_exten', '$login_captcha','$File_Rename')") or die('Error: dd' . mysqli_error($db_con));

                if ($insertRole) {
                    $rolid = mysqli_insert_id($db_con);
                    for ($j = 0; $j < count($_POST['groups']); $j++) {
                        $groupid = $_POST['groups'][$j];
                        $checkgroup = mysqli_query($db_con, "select group_id,roleids from tbl_bridge_grp_to_um where group_id='$groupid'") or die('Error: dd' . mysqli_error($db_con));;
                        if (mysqli_num_rows($checkgroup) >= 1) {
                            $rwGroup = mysqli_fetch_assoc($checkgroup);
                            if (!empty($rwGroup['roleids'])) {
                                $rolids = $rwGroup['roleids'] . ',' . $rolid;
                            } else {
                                $rolids = $rolid;
                            }
                            $updateqry = mysqli_query($db_con, "update tbl_bridge_grp_to_um set roleids='$rolids' where group_id='$groupid'") or die('Error: dd' . mysqli_error($db_con));;
                        } else {
                            $qry = mysqli_query($db_con, "insert into  tbl_bridge_grp_to_um(`group_id`,`roleids`) values ('$groupid','$rolid')") or die('Error: dd' . mysqli_error($db_con));
                        }
                        //$qry= mysqli_query($db_con, "insert into  tbl_bridge_grp_to_um(`group_id`,`roleids`) values ('$groupid','$rolid')") or die('Error: dd' . mysqli_error($db_con));
                    }
                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'User Role $roleName Created','$date', null,'$host','')") or die('error :' . mysqli_error($db_con));
                    echo '<script>taskSuccess("userRole","' . $lang['Rl_Crtd_Scesfly'] . '");</script>';
                } else {
                    echo '<script>taskFailed("userRole","' . $lang['Rl_nt_Cretn_Fld'] . '");</script>';
                }
            }
            mysqli_close($db_con);
        }
        //edit user profile
        if (isset($_POST['editRole'], $_POST['token'])) {

            $rid = filter_input(INPUT_POST, "rid");

            $roleName = xss_clean(trim($_POST['roleName']));

            $roleName = mysqli_real_escape_string($db_con, $roleName);

            //Dashboard
            if (!empty($_POST['mydms'])) {
                $mydms = 1;
            } else {
                $mydms = 0;
            }
            if (!empty($_POST['mytsk'])) {
                $mytsk = 1;
            } else {
                $mytsk = 0;
            }
            if (!empty($_POST['dashEditPro'])) {
                $dashEditPro = 1;
            } else {
                $dashEditPro = 0;
            }
            if (!empty($_POST['dashQury'])) {
                $dashQury = 1;
            } else {
                $dashQury = 0;
            }
            if (!empty($_POST['num_folders'])) {
                $num_Folders = 1;
            } else {
                $num_Folders = 0;
            }
            if (!empty($_POST['num_files'])) {
                $num_Files = 1;
            } else {
                $num_Files = 0;
            }
            if (!empty($_POST['memory_use'])) {
                $memory_Use = 1;
            } else {
                $memory_Use = 0;
            }
            //User Manager
            if (!empty($_POST['usrAdd'])) {
                $usrAdd = 1;
            } else {
                $usrAdd = 0;
            }
            if (!empty($_POST['usrmodi'])) {
                $usrmodi = 1;
            } else {
                $usrmodi = 0;
            }

            if (!empty($_POST['usrDelete'])) {
                $usrDelete = 1;
            } else {
                $usrDelete = 0;
            }
            if (!empty($_POST['usrView'])) {
                $usrView = 1;
            } else {
                $usrView = 0;
            }

            //Authorization
            if (!empty($_POST['strgAuth'])) {
                $strgAuth = 1;
            } else {
                $strgAuth = 0;
            }

            //Group Manager
            if (!empty($_POST['grpAdd'])) {
                $grpAdd = 1;
            } else {
                $grpAdd = 0;
            }
            if (!empty($_POST['grpDelete'])) {
                $grpDelete = 1;
            } else {
                $grpDelete = 0;
            }
            if (!empty($_POST['grpModi'])) {
                $grpModi = 1;
            } else {
                $grpModi = 0;
            }
            if (!empty($_POST['grpView'])) {
                $grpView = 1;
            } else {
                $grpView = 0;
            }

            //User Role Manager
            if (!empty($_POST['usrRoleAdd'])) {
                $usrRoleAdd = 1;
            } else {
                $usrRoleAdd = 0;
            }
            if (!empty($_POST['usrRoleDel'])) {
                $usrRoleDel = 1;
            } else {
                $usrRoleDel = 0;
            }
            if (!empty($_POST['usrRoleModi'])) {
                $usrRoleModi = 1;
            } else {
                $usrRoleModi = 0;
            }
            if (!empty($_POST['usrRoleView'])) {
                $usrRoleView = 1;
            } else {
                $usrRoleView = 0;
            }

            //Upload/Import
            if (!empty($_POST['bulkUpld'])) {
                $bulkUpld = 1;
            } else {
                $bulkUpld = 0;
            }

            //MetaData Registry
            if (!empty($_POST['metaDataAdd'])) {
                $metaDataAdd = 1;
            } else {
                $metaDataAdd = 0;
            }

            if (!empty($_POST['meteDataView'])) {
                $meteDataView = 1;
            } else {
                $meteDataView = 0;
            }
            if (!empty($_POST['meteDataAsin'])) {
                $meteDataAsin = 1;
            } else {
                $meteDataAsin = 0;
            }

            //Storage Manager
            if (!empty($_POST['strgCreate'])) {
                $strgCreate = 1;
            } else {
                $strgCreate = 0;
            }
            if (!empty($_POST['strgAddChild'])) {
                $strgAddChild = 1;
            } else {
                $strgAddChild = 0;
            }
            if (!empty($_POST['strgUpldDoc'])) {
                $strgUpldDoc = 1;
            } else {
                $strgUpldDoc = 0;
            }
            if (!empty($_POST['strgModi'])) {
                $strgModi = 1;
            } else {
                $strgModi = 0;
            }
            if (!empty($_POST['strgDelete'])) {
                $strgDelete = 1;
            } else {
                $strgDelete = 0;
            }
            if (!empty($_POST['strgMove'])) {
                $strgMove = 1;
            } else {
                $strgMove = 0;
            }
            if (!empty($_POST['strgCopy'])) {
                $strgCopy = 1;
            } else {
                $strgCopy = 0;
            }

            //Audit Trail
            if (!empty($_POST['auditTrlUsr'])) {
                $auditTrlUsr = 1;
            } else {
                $auditTrlUsr = 0;
            }
            if (!empty($_POST['auditTrlStrg'])) {
                $auditTrlStrg = 1;
            } else {
                $auditTrlStrg = 0;
            }

            //Workflow Management
            if (!empty($_POST['wrkflwCreate'])) {
                $wrkflwCreate = 1;
            } else {
                $wrkflwCreate = 0;
            }
            if (!empty($_POST['wrkflwView'])) {
                $wrkflwView = 1;
            } else {
                $wrkflwView = 0;
            }
            if (!empty($_POST['wrkflwEdit'])) {
                $wrkflwEdit = 1;
            } else {
                $wrkflwEdit = 0;
            }
            if (!empty($_POST['wrkflwDel'])) {
                $wrkflwDel = 1;
            } else {
                $wrkflwDel = 0;
            }

            if (!empty($_POST['wrkflwStep'])) {
                $wrkflwStep = 1;
            } else {
                $wrkflwStep = 0;
            }
            if (!empty($_POST['wrkflwIniFile'])) {
                $wrkflwIniFile = 1;
            } else {
                $wrkflwIniFile = 0;
            }

            if (!empty($_POST['InitiateFile'])) {
                $initiateFile = 1;
            } else {
                $initiateFile = 0;
            }

            //Task Track Status:
            if (!empty($_POST['tsktrk'])) {
                $tsktrk = 1;
            } else {
                $tsktrk = 0;
            }
            //MetaData Search
            if (!empty($_POST['metadataSerach'])) {
                $metadataSerach = 1;
            } else {
                $metadataSerach = 0;
            }

            if (!empty($_POST['metaDataQsearch'])) {
                $metaDataQsearch = 1;
            } else {
                $metaDataQsearch = 0;
            }

            //file

            if (!empty($_POST['fileEdit'])) {
                $fileEdit = 1;
            } else {
                $fileEdit = 0;
            }

            if (!empty($_POST['fileDelete'])) {
                $fileDelete = 1;
            } else {
                $fileDelete = 0;
            }

            if (!empty($_POST['fileAnot'])) {
                $fileAnot = 1;
            } else {
                $fileAnot = 0;
            }

            if (!empty($_POST['fileComent'])) {
                $fileComent = 1;
            } else {
                $fileComent = 0;
            }

            if (!empty($_POST['fileAnotDelete'])) {
                $fileAnotDelete = 1;
            } else {
                $fileAnotDelete = 0;
            }

            if (!empty($_POST['pdffile'])) {
                $pdffile = 1;
            } else {
                $pdffile = 0;
            }
            if (!empty($_POST['docfile'])) {
                $docfile = 1;
            } else {
                $docfile = 0;
            }
            if (!empty($_POST['excelfile'])) {
                $excelfile = 1;
            } else {
                $excelfile = 0;
            }
            if (!empty($_POST['audiofile'])) {
                $audiofile = 1;
            } else {
                $audiofile = 0;
            }
            if (!empty($_POST['videofile'])) {
                $videofile = 1;
            } else {
                $videofile = 0;
            }
            if (!empty($_POST['imagefile'])) {
                $imagefile = 1;
            } else {
                $imagefile = 0;
            }
            if (!empty($_POST['pdfprint'])) {
                $pdfprint = 1;
            } else {
                $pdfprint = 0;
            }
            if (!empty($_POST['pdfdownload'])) {
                $pdfdownload = 1;
            } else {
                $pdfdownload = 0;
            }
            if (!empty($_POST['annotedpdf'])) {
                $annotedpdf = 1;
            } else {
                $annotedpdf = 0;
            }
            if (!empty($_POST['fileversion'])) {
                $fileversion = 1;
            } else {
                $fileversion = 0;
            }
            if (!empty($_POST['delfilevrsn'])) {
                $delfilevrsn = 1;
            } else {
                $delfilevrsn = 0;
            }
            if (!empty($_POST['updatefile'])) {
                $updatefile = 1;
            } else {
                $updatefile = 0;
            }
            if (!empty($_POST['auditwf'])) {
                $Auditwf = 1;
            } else {
                $Auditwf = 0;
            }

            if (!empty($_POST['mailconfigYes'])) {
                $MailconfigYes = 1;
            } else {
                $MailconfigYes = 0;
            }

            if (!empty($_POST['onlineUser'])) {
                $onlineUser = 1;
            } else {
                $onlineUser = 0;
            }
            if (!empty($_POST['tiffile'])) {
                $tiffile = 1;
            } else {
                $tiffile = 0;
            }
            //for faq
            if (!empty($_POST['viewfaq'])) {
                $viewfaq = 1;
            } else {
                $viewfaq = 0;
            }
            if (!empty($_POST['addfaq'])) {
                $addfaq = 1;
            } else {
                $addfaq = 0;
            }
            if (!empty($_POST['editfaq'])) {
                $editfaq = 1;
            } else {
                $editfaq = 0;
            }
            if (!empty($_POST['delfaq'])) {
                $delfaq = 1;
            } else {
                $delfaq = 0;
            }
            //Restore files
            if (!empty($_POST['viewrecycle'])) {
                $viewrecycle = 1;
            } else {
                $viewrecycle = 0;
            }
            if (!empty($_POST['restore'])) {
                $restoreFile = 1;
            } else {
                $restoreFile = 0;
            }
            if (!empty($_POST['permntDel'])) {
                $permanentDel = 1;
            } else {
                $permanentDel = 0;
            }

            if (!empty($_POST['renamefile'])) {
                $renamefile = 1;
            } else {
                $renamefile = 0;
            }
            //for shared/share with me files
            if (!empty($_POST['sharedFile'])) {
                $sharedFile = 1;
            } else {
                $sharedFile = 0;
            }
            if (!empty($_POST['shareWithme'])) {
                $shareWithme = 1;
            } else {
                $shareWithme = 0;
            }
            if (!empty($_POST['csv'])) {
                $exportcsv = 1;
            } else {
                $exportcsv = 0;
            }
            if (!empty($_POST['movefile'])) {
                $movefile = 1;
            } else {
                $movefile = 0;
            }
            if (!empty($_POST['copyfile'])) {
                $copyfile = 1;
            } else {
                $copyfile = 0;
            }
            if (!empty($_POST['sharefile'])) {
                $sharefile = 1;
            } else {
                $sharefile = 0;
            }
            if (!empty($_POST['CheckinOut'])) {
                $CheckinOut = 1;
            } else {
                $CheckinOut = 0;
            }
            if (!empty($_POST['bulkDwnld'])) {
                $bulkDwnld = 1;
            } else {
                $bulkDwnld = 0;
            }
            // Workflow Report
            if (!empty($_POST['wrkflwInvl'])) {
                $wrkflwInvl = 1;
            } else {
                $wrkflwInvl = 0;
            }
            if (!empty($_POST['wrkflwRun'])) {
                $wrkflwRun = 1;
            } else {
                $wrkflwRun = 0;
            }
            $viewreport = ((!empty($_POST['viewreport'])) ? 1 : 0);
            $addreport = ((!empty($_POST['addreport'])) ? 1 : 0);
            $editreport = ((!empty($_POST['editreport'])) ? 1 : 0);
            $deletereport = ((!empty($_POST['deletereport'])) ? 1 : 0);

            if (!empty($_POST['fbckmsg'])) {
                $fbckmsg = 1;
            } else {
                $fbckmsg = 0;
            }
            if (!empty($_POST['mailList'])) {
                $mailList = 1;
            } else {
                $mailList = 0;
            }
            if (!empty($_POST['xlsdownload'])) {
                $xlsdownload = 1;
            } else {
                $xlsdownload = 0;
            }
            if (!empty($_POST['xlsprint'])) {
                $xlsprint = 1;
            } else {
                $xlsprint = 0;
            }
            if (!empty($_POST['xlsprint'])) {
                $xlsprint = 1;
            } else {
                $xlsprint = 0;
            }
            if (!empty($_POST['delete_page'])) {
                $delete_page = 1;
            } else {
                $delete_page = 0;
            }
            if (!empty($_POST['wf_log'])) {
                $wf_log = 1;
            } else {
                $wf_log = 0;
            }
            if (!empty($_POST['review_log'])) {
                $review_log = 1;
            } else {
                $review_log = 0;
            }
            if (!empty($_POST['review_intray'])) {
                $review_intray = 1;
            } else {
                $review_intray = 0;
            }
            if (!empty($_POST['review_track'])) {
                $review_track = 1;
            } else {
                $review_track = 0;
            }

            if (!empty($_POST['todo_add'])) {
                $todo_add = 1;
            } else {
                $todo_add = 0;
            }
            if (!empty($_POST['todo_edit'])) {
                $todo_edit = 1;
            } else {
                $todo_edit = 0;
            }
            if (!empty($_POST['todo_archive'])) {
                $todo_archive = 1;
            } else {
                $todo_archive = 0;
            }
            if (!empty($_POST['todo_view'])) {
                $todo_view = 1;
            } else {
                $todo_view = 0;
            }

            if (!empty($_POST['appoint_add'])) {
                $appoint_add = 1;
            } else {
                $appoint_add = 0;
            }
            if (!empty($_POST['appoint_edit'])) {
                $appoint_edit = 1;
            } else {
                $appoint_edit = 0;
            }
            if (!empty($_POST['appoint_archive'])) {
                $appoint_archive = 1;
            } else {
                $appoint_archive = 0;
            }
            if (!empty($_POST['appoint_view'])) {
                $appoint_view = 1;
            } else {
                $appoint_view = 0;
            }
            if (!empty($_POST['add_page_inbtwn'])) {
                $add_page_inbtwn = 1;
            } else {
                $add_page_inbtwn = 0;
            }
            if (!empty($_POST['add_page_no'])) {
                $add_page_no = 1;
            } else {
                $add_page_no = 0;
            }
            if (!empty($_POST['meteDataedit'])) {
                $meteDataedit = 1;
            } else {
                $meteDataedit = 0;
            }
            if (!empty($_POST['meteDatadelete'])) {
                $meteDatadelete = 1;
            } else {
                $meteDatadelete = 0;
            }
            if (!empty($_POST['word_edit'])) {
                $word_edit = 1;
            } else {
                $word_edit = 0;
            }
            if (!empty($_POST['view_psd'])) {
                $view_psd = 1;
            } else {
                $view_psd = 0;
            }
            if (!empty($_POST['view_cdr'])) {
                $view_cdr = 1;
            } else {
                $view_cdr = 0;
            }
            //language
            if (!empty($_POST['hindi'])) {
                $hindi = 1;
            } else {
                $hindi = 0;
            }
            if (!empty($_POST['english'])) {
                $english = 1;
            } else {
                $english = 0;
            }
            if (!empty($_POST['clabel'])) {
                $clabel = 1;
            } else {
                $clabel = 0;
            }
            if (!empty($_POST['appd'])) {
                $appd = 1;
            } else {
                $appd = 0;
            }
            if (!empty($_POST['status'])) {
                $status = 1;
            } else {
                $status = 0;
            }
            if (!empty($_POST['priority'])) {
                $priority = 1;
            } else {
                $priority = 0;
            }
            if (!empty($_POST['calendar'])) {
                $calendar = 1;
            } else {
                $calendar = 0;
            }
            if (!empty($_POST['delstrglog'])) {
                $delstrglog = 1;
            } else {
                $delstrglog = 0;
            }
            if (!empty($_POST['delusrlog'])) {
                $deluserlog = 1;
            } else {
                $deluserlog = 0;
            }
            if (!empty($_POST['delwflog'])) {
                $delwflog = 1;
            } else {
                $delwflog = 0;
            }
            if (!empty($_POST['user_graph'])) {
                $user_graph = 1;
            } else {
                $user_graph = 0;
            }
            if (!empty($_POST['export_user'])) {
                $export_user = 1;
            } else {
                $export_user = 0;
            }
            if (!empty($_POST['import_user'])) {
                $import_user = 1;
            } else {
                $import_user = 0;
            }
            if (!empty($_POST['user_activate_deactivate'])) {
                $user_activate_deactivate = 1;
            } else {
                $user_activate_deactivate = 0;
            }
            //add holiday
            if (!empty($_POST['addholiday'])) {
                $addholiday = $_POST['addholiday'];
            } else {
                $addholiday = 0;
            }
            //edit holiday
            if (!empty($_POST['editholiday'])) {
                $editholiday = $_POST['editholiday'];
            } else {
                $editholiday = 0;
            }
            // view holiday
            if (!empty($_POST['viewholiday'])) {
                $viewholiday = $_POST['viewholiday'];
            } else {
                $viewholiday = 0;
            }
            //delete holiday
            if (!empty($_POST['delholiday'])) {
                $delholiday = $_POST['delholiday'];
            } else {
                $delholiday = 0;
            }
            //view  holiday calender
            if (!empty($_POST['holidaycal'])) {
                $holidaycal = $_POST['holidaycal'];
            } else {
                $holidaycal = 0;
            }
            // Mail files from Storage.
            if (!empty($_POST['mail_files'])) {
                $mail_files = $_POST['mail_files'];
            } else {
                $mail_files = 0;
            }
            // for client creation
            if (!empty($_POST['ccreate'])) {
                $ccreate = $_POST['ccreate'];
            } else {
                $ccreate = 0;
            }
            // for client creation
            if (!empty($_POST['ezeescan'])) {
                $ezeescan = $_POST['ezeescan'];
            } else {
                $ezeescan = 0;
            }

            //save query
            $savequery = ((!empty($_POST['savequery'])) ? 1 : 0);
            $uploadlog = ((!empty($_POST['uploadlog'])) ? 1 : 0);

            $passpolicy = ((!empty($_POST['passpolicy'])) ? 1 : 0);

            //feature control role
            $langsetting = ((!empty($_POST['langsetting'])) ? 1 : 0);
            $docexpsetting = ((!empty($_POST['docexpsetting'])) ? 1 : 0);
            $docretentionsetting = ((!empty($_POST['docretention'])) ? 1 : 0);
            $docsharesetting = ((!empty($_POST['docsharesetting'])) ? 1 : 0);

            // storage New featues
            $view_odt = ((!empty($_POST['odtfile'])) ? 1 : 0);
            $view_rtf = ((!empty($_POST['rtffile'])) ? 1 : 0);
            $file_review = ((!empty($_POST['filereview'])) ? 1 : 0);
            $doc_weeding_out = ((!empty($_POST['weedingouttime'])) ? 1 : 0);
            $lock_folder = ((!empty($_POST['lock_folder'])) ? 1 : 0);
            $lock_file = ((!empty($_POST['lock_file'])) ? 1 : 0);
            $doc_share_time = ((!empty($_POST['docsharetime'])) ? 1 : 0);
            $doc_expiry_time = ((!empty($_POST['expdocument'])) ? 1 : 0);
            $folder_upload = ((!empty($_POST['bulkUpldfolder'])) ? 1 : 0);
              //for rename document files
              $File_Rename = ((!empty($_POST['renmdocument'])) ? 1 : 0);
            //email otp @dv 02-apr-20
            $emailotp = ((!empty($_POST['emailotp'])) ? 1 : 0);

            $view_exten = ((!empty($_POST['view_exten'])) ? 1 : 0);
            $add_exten = ((!empty($_POST['add_exten'])) ? 1 : 0);
            $enable_exten = ((!empty($_POST['enable_exten'])) ? 1 : 0);
            $delete_exten = ((!empty($_POST['delete_exten'])) ? 1 : 0);
            $login_captcha = ((!empty($_POST['login_captcha'])) ? 1 : 0);

            $_SESSION['cdes_user_id'];
            $rolevalidate = mysqli_query($db_con, "select * from tbl_user_roles where user_role='$roleName' and  role_id!='$rid'") or die('Error: dd' . mysqli_error($db_con));
            if (mysqli_num_rows($rolevalidate) >= 1) {
                echo '<script>taskFailed("userRole","' . $lang['Role_Already_Exist'] . '");</script>';
            } else {
                $role = mysqli_query($db_con, "select user_role from tbl_user_roles where user_role='$roleName' ");
                $rwgetRole = mysqli_fetch_assoc($role);
                $rwRolename = $rwRole['user_role'];
                mysqli_set_charset($db_con, "utf8");
                $edit = mysqli_query($db_con, "update tbl_user_roles set user_role='$roleName', dashboard_mydms='$mydms',dashboard_mytask='$mytsk',"
                    . "dashboard_edit_profile='$dashEditPro',dashboard_query='$dashQury',create_user='$usrAdd',modify_userlist='$usrmodi',delete_userlist='$usrDelete',view_userlist='$usrView',storage_auth_plcy='$strgAuth',"
                    . "add_group='$grpAdd',delete_group='$grpDelete',modify_group='$grpModi',view_group_list='$grpView',role_add='$usrRoleAdd',role_delete='$usrRoleDel',role_modi='$usrRoleModi',role_view='$usrRoleView',bulk_upload='$bulkUpld',"
                    . "add_metadata='$metaDataAdd',view_metadata='$meteDataView',assign_metadata='$meteDataAsin', create_storage='$strgCreate',create_child_storage='$strgAddChild',upload_doc_storage='$strgUpldDoc',modify_storage_level='$strgModi',"
                    . "delete_storage_level='$strgDelete',move_storage_level='$strgMove',copy_storage_level='$strgCopy',view_user_audit='$auditTrlUsr',view_storage_audit='$auditTrlStrg',create_workflow='$wrkflwCreate',"
                    . "view_workflow_list='$wrkflwView',edit_workflow='$wrkflwEdit',delete_workflow='$wrkflwDel',workflow_step='$wrkflwStep',workflow_initiate_file='$wrkflwIniFile',workflow_task_track='$tsktrk',"
                    . "metadata_search='$metadataSerach',metadata_quick_search='$metaDataQsearch', file_edit='$fileEdit', file_delete='$fileDelete', file_anot='$fileAnot', file_coment='$fileComent', file_anot_delete='$fileAnotDelete', "
                    . "initiate_file='$initiateFile', num_of_folder='$num_Folders', num_of_file='$num_Files', memory_used='$memory_Use', pdf_file='$pdffile', doc_file='$docfile', excel_file='$excelfile', image_file='$imagefile', audio_file='$audiofile', "
                    . "video_file='$videofile', pdf_print='$pdfprint', pdf_download='$pdfdownload', pdf_annotation='$annotedpdf', file_version='$fileversion', delete_version='$delfilevrsn', update_file='$updatefile', workflow_audit='$Auditwf', "
                    . "email_config='$MailconfigYes', online_user='$onlineUser', tif_file='$tiffile', view_faq='$viewfaq', add_faq='$addfaq', edit_faq='$editfaq', del_faq='$delfaq', view_recycle_bin='$viewrecycle', restore_file='$restoreFile', "
                    . "permanent_del='$permanentDel', shared_file='$sharedFile', share_with_me='$shareWithme', "
                    . "export_csv='$exportcsv', move_file='$movefile', copy_file='$copyfile', share_file='$sharefile', checkin_checkout='$CheckinOut', bulk_download='$bulkDwnld', involve_workflow='$wrkflwInvl', run_workflow='$wrkflwRun', "
                    . "feedback_msg ='$fbckmsg', mail_lists='$mailList',xls_download='$xlsdownload',xls_print='$xlsprint',delete_page='$delete_page',wf_log='$wf_log',review_log='$review_log',review_intray='$review_intray',review_track='$review_track',"
                    . "todo_add='$todo_add',todo_edit='$todo_edit',todo_archive='$todo_archive',todo_view='$todo_view',appoint_add='$appoint_add',appoint_edit='$appoint_edit',appoint_archive='$appoint_archive',appoint_view='$appoint_view',"
                    . "add_page_inbtwn='$add_page_inbtwn',add_page_no='$add_page_no', edit_metadata='$meteDataedit', delete_metadata='$meteDatadelete', word_edit='$word_edit', view_psd='$view_psd', view_cdr='$view_cdr', hindi='$hindi', "
                    . "english='$english', customize_label='$clabel', app_default='$appd',status_wf='$status', priority_wf='$priority', calendar_wf='$calendar', delete_user_log='$deluserlog',delete_storage_log='$delstrglog',delete_wf_log='$delwflog',"
                    . "user_graph='$user_graph',"
                    . "export_user='$export_user',import_user='$import_user',user_activate_deactivate='$user_activate_deactivate', add_holiday='$addholiday', view_holiday='$viewholiday', edit_holiday='$editholiday',delete_holiday='$delholiday',holiday_calender='$holidaycal',"
                    . "mail_files='$mail_files',save_query='$savequery',upload_logs='$uploadlog',create_client='$ccreate',ezeescan='$ezeescan', view_report='$viewreport', add_report='$addreport', update_report='$editreport', delete_report='$deletereport', password_policy='$passpolicy', "
                    . "default_lang_setting='$langsetting',doc_exp_setting='$docexpsetting',doc_retention_setting='$docretentionsetting',doc_share_setting='$docsharesetting', view_odt='$view_odt', view_rtf='$view_rtf', file_review='$file_review', lock_file='$lock_file', lock_folder='$lock_folder',"
                    . "doc_weeding_out='$doc_weeding_out', doc_share_time='$doc_share_time', doc_expiry_time='$doc_expiry_time', folder_upload='$folder_upload', rename_file='$renamefile', login_otp='$emailotp', view_exten='$view_exten', add_exten='$add_exten', enable_exten='$enable_exten', delete_exten='$delete_exten', login_captcha='$login_captcha',rename_document=' $File_Rename' where role_id='$rid'"); //or die('Error : ' . mysqli_error($db_con));
                if ($edit) {
                    $id = $rid;
                    $flag = 0;
                    $groups = $_POST['groups'];
                    //reset group user
                    $userGroup = mysqli_query($db_con, "select * from tbl_bridge_grp_to_um where find_in_set('$id',roleids)") or die('Error' . mysqli_error($db_con));
                    while ($rwUsergroup = mysqli_fetch_assoc($userGroup)) {
                        $users = $rwUsergroup['roleids'];
                        $users = explode(",", $users);
                        $users = array_diff($users, array($id));
                        $users = implode(",", $users);
                        $resetBridgegrpusr = mysqli_query($db_con, "update tbl_bridge_grp_to_um set roleids='$users' where group_id='$rwUsergroup[group_id]'");
                    }

                    //update user in groups
                    foreach ($groups as $groupid) {
                        $check = mysqli_query($db_con, "select * from tbl_bridge_grp_to_um where group_id='$groupid'");

                        if (mysqli_num_rows($check) <= 0) {
                            $grpmap = mysqli_query($db_con, "insert into tbl_bridge_grp_to_um(group_id,roleids) values('$groupid','$user_id')") or die('Error 1' . mysqli_error($db_con));
                            if ($grpmap) {
                                $flag = 1;
                            }
                        } else {
                            $rwCheck = mysqli_fetch_assoc($check);
                            $userids = $rwCheck['roleids'];
                            if (!empty($userids)) {
                                $userids = explode(",", $userids);
                                if (!in_array($id, $userids)) {
                                    array_push($userids, $id);
                                }
                                $userids = implode(",", $userids);
                            } else {
                                $userids = $id;
                            }
                            $grpmap = mysqli_query($db_con, "update tbl_bridge_grp_to_um set roleids ='$userids' where group_id='$groupid'") or die('Error' . mysqli_error($db_con));
                            if ($grpmap) {
                                $flag = 1;
                            }
                        }
                    }
                    //end update user in group
                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'User Role $rwRolename to $roleName Updated','$date', null,'$host','')") or die('error :' . mysqli_error($db_con));
                    echo '<script>taskSuccess("userRole","' . $lang['Rl_Updtd_Scesfly'] . '");</script>';
                } else {
                    echo '<script>taskFailed("userRole","' . $lang['Rl_nt_Updtd'] . '");</script>';
                }
            }
            mysqli_close($db_con);
        }
        if (isset($_POST['delete'], $_POST['token'])) {
            $id = $_POST['uid'];
            $delName = mysqli_query($db_con, "select user_role from tbl_user_roles where role_id='$id'");
            $rwdelName = mysqli_fetch_assoc($delName);
            $deletedName = $rwdelName['user_role'];
            $del = mysqli_query($db_con, "delete from tbl_user_roles where role_id='$id'") or die('Error:' . mysqli_error($db_con));
            if ($del) {

                $delrole = mysqli_query($db_con, "select * from tbl_bridge_grp_to_um where find_in_set('$id',roleids)") or die('Error' . mysqli_error($db_con));
                while ($rwdel = mysqli_fetch_assoc($delrole)) {
                    $roleids = $rwdel['roleids'];
                    $roleids = explode(",", $roleids);
                    $roleids = array_diff($roleids, array($id));
                    $roleids = implode(",", $roleids);
                    $resetBridgegrpusr = mysqli_query($db_con, "update tbl_bridge_grp_to_um set roleids='$roleids' where group_id='$rwdel[group_id]'");
                }
                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'User Role $deletedName Deleted','$date', null,'$host','')") or die('error :' . mysqli_error($db_con));
                echo '<script>taskSuccess("userRole","' . $lang['Rl_Dltd_Sucsfly'] . '");</script>';
            } else {
                echo '<script>taskFailed("userRole","' . $lang['Rl_nt_Dltd'] . '");</script>';
            }
            mysqli_close($db_con);
        }
        ?>

        <?php

        function showData($user, $rwgetRole, $db_con, $start)
        {
            if (isset($_SESSION['lang'])) {
                $file = $_SESSION['lang'] . ".json";
            } else {
                $file = "English.json";
            }
            $data = file_get_contents($file);
            $lang = json_decode($data, true);
        ?>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th><?php echo $lang['Sr_No']; ?></th>
                        <th><?php echo $lang['Profile_Name']; ?></th>
                        <th><?php echo $lang['Actions']; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    $i += $start;
                    while ($rwUser = mysqli_fetch_assoc($user)) {
                        if ($rwUser['role_id'] != 1) {
                    ?>
                            <tr class="gradeX">
                                <td><?php echo $i . '.'; ?></td>
                                <td><?php echo $rwUser['user_role']; ?></td>
                                <td class="actions">
                                    <?php if ($rwgetRole['role_modi'] == '1') { ?>
                                        <a href="#" class="on-default edit-row btn btn-primary" data-toggle="modal" data-target="#con-close-modal" id="editRow" data="<?php echo $rwUser['role_id']; ?>" title="<?php echo $lang['Modify_column']; ?>"><i class="fa fa-edit"></i> <?php echo $lang['Modify_column']; ?></a>

                                    <?php } ?>
                                    <?php if ($rwgetRole['role_delete'] == '1') { ?>
                                        <a href="#" class="on-default remove-row btn btn-danger" data-toggle="modal" data-target="#dialog" id="removeRow" data="<?php echo $rwUser['role_id']; ?>" title="<?php echo $lang['Delete']; ?>"><i class="fa fa-trash-o"></i> <?php echo $lang['Delete']; ?></a>
                                    <?php } ?>

                                </td>

                            </tr>
                    <?php
                            $i++;
                        }
                    }
                    ?>
                </tbody>
            </table>
        <?php
        }
        ?>
</body>

</html>