<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    require_once './application/config/database.php';
    require_once './application/pages/head.php';
   
    $sameGroupIDs = array();
    $group = mysqli_query($db_con, "select * from tbl_bridge_grp_to_um where find_in_set('$_SESSION[cdes_user_id]',user_ids)") or die('Error' . mysqli_error($db_con));
    while ($rwGroup = mysqli_fetch_assoc($group)) {
        $sameGroupIDs[] = $rwGroup['user_ids'];
    }
    $sameGroupIDs = implode(',', $sameGroupIDs);
    $sameGroupIDs = explode(',', $sameGroupIDs);
    $sameGroupIDs = array_unique($sameGroupIDs);
    sort($sameGroupIDs);
    $sameGroupIDs = implode(',', $sameGroupIDs);
    // print_r($sameGroupIDs);

    if ($rwgetRole['view_userlist'] != '1') {
        header('Location: ./index');
    }
    $_SESSION['cdes_user_id'] == $rwUser['user_id'];
    if (isset($_GET['GrpId']) && !empty($_GET['GrpId'])) {
        $group_id = base64_decode(urldecode($_GET['GrpId']));
        if (intval($group_id)) {
            mysqli_set_charset($db_con, "utf8");
            $getUserID = mysqli_query($db_con, "select user_ids,group_id from tbl_bridge_grp_to_um WHERE group_id='$group_id' "); //or die("Error " . mysqli_error($db_con));
            $RwgetUserID = mysqli_fetch_assoc($getUserID);
            $userIds_selected_group = $RwgetUserID['user_ids'];
        }
    }

    $pwdpolicy = mysqli_query($db_con, "SELECT * FROM `tbl_pass_policy`");
    $rwpwdpolicy = mysqli_fetch_assoc($pwdpolicy);
    //filter  for active inactive users
    $activeIn = urldecode(base64_decode($_GET['users']));
    $activeInusers = preg_replace("/[^0-9]/", "", $activeIn);
    ?>
    <!-- Plugin Css-->
    <link rel="stylesheet" href="assets/plugins/magnific-popup/css/magnific-popup.css" />
    <link rel="stylesheet" href="assets/plugins/jquery-datatables-editable/datatables.css" />
    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
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
                                        <a href="userList"><?php echo $lang['Masters']; ?></a>
                                    </li>
                                    <li class="active">
                                        <?php echo $lang['User_List']; ?>
                                    </li>
                                    <li> <a href="#" data-toggle="modal" data-target="#help-modal" id="helpview" data="4" title="<?= $lang['help']; ?>"><i class="fa fa-question-circle"></i> </a></li>
                                    <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                                    <?php if ($rwgetRole['import_user'] == '1') { ?>
                                        <div class="pull-right">
                                            <a href="importUser" class="btn btn-primary waves-effect waves-light" style="height:36px; margin-top:-9px;"><?php echo $lang['Import_Users']; ?> <i class="fa fa-user-plus"></i></a>
                                        </div>
                                    <?php } ?>
                                </ol>
                            </div>
                        </div>
                        <div class="box box-primary">
                            <div class="panel">
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <?php if ($rwgetRole['create_user'] == '1') { ?>
                                                <div class="col-sm-1">
                                                    <a href="createUser" class="btn btn-primary waves-effect waves-light" style="height:36px;"> <?php echo $lang['Add']; ?> <i class="fa fa-user-plus"></i></a>
                                                </div>
                                            <?php } ?>
                                            <div class="form-group col-md-3">
                                                <input type="text" name="searchtext" id="searchtext" class="form-control translatetext" placeholder="<?php echo strtolower($lang['search_uesr_name']) . ', ' . strtolower($lang['Email']) . ', ' . strtolower($lang['Phone']); ?>" value="<?php echo (($_GET['searchtxt'] != "") ? urldecode(base64_decode(xss_clean($_GET['searchtxt']))) : ""); ?>">
                                            </div>
                                            <div class="form-group col-md-3">
                                                <select class="select2" name="group" id="usergroup">
                                                    <option value=""><?php echo $lang['Select_Users_Group']; ?></option>
                                                    <?php
                                                    $group_id = base64_decode(urldecode($_GET['GrpId']));
                                                    mysqli_set_charset($db_con, "utf8");
                                                    $group_user = mysqli_query($db_con, "SELECT group_id,user_ids FROM `tbl_bridge_grp_to_um`");
                                                    while ($allGroups = mysqli_fetch_array($group_user)) {
                                                        $user_ids = explode(',', $allGroups['user_ids']);
                                                        if (in_array($_SESSION['cdes_user_id'], $user_ids)) {
                                                            $grp = mysqli_query($db_con, "select group_id,group_name from tbl_group_master WHERE group_id='$allGroups[group_id]' order by group_name asc"); //or die('Error' . mysqli_error($db_con));
                                                            while ($rwGrp = mysqli_fetch_assoc($grp)) {
                                                                if ($rwGrp['group_id'] == $group_id) {
                                                                    ?>
                                                                    <option value="<?php echo $rwGrp['group_id']; ?>" selected><?php echo $rwGrp['group_name']; ?></option>
                                                                <?php } else {
                                                                    ?>
                                                                    <option value="<?php echo $rwGrp['group_id']; ?>"><?php echo $rwGrp['group_name']; ?></option>
                                                                    <?php
                                                                }
                                                            }
                                                        }
                                                    }
                                                    ?>    
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <select class="select2" name="allusers"  id="allusers">
                                                    <option value="" selected=""><?php echo $lang['select_user']; ?></option>
                                                    <option value="2" <?php
                                                    if ($activeInusers == 2) {
                                                        echo "selected";
                                                    }
                                                    ?>><?php echo $lang['all_users']; ?></option>
                                                    <option value="1" <?php
                                                    if ($activeInusers == 1) {
                                                        echo "selected";
                                                    }
                                                    ?>><?php echo $lang['active_users']; ?></option>
                                                    <option value="3" <?php
                                                    if ($activeInusers == 3) {
                                                        echo "selected";
                                                    }
                                                    ?>><?php echo $lang['inactive_users']; ?></option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <input type="button" name="search" id="search" class="btn btn-primary" value="<?php echo $lang['Apply']; ?>" onclick="searchUser();" title="<?php echo $lang['Search']; ?>" >
                                                <a href="userList" class="btn btn-warning" title="<?php echo $lang['Reset']; ?>" ><?php echo $lang['Reset']; ?></a>
                                                <?php if ($rwgetRole['export_user'] == '1') { ?>
                                                    <button class="btn btn-primary" id="export4"  data-toggle="modal"  data-target="#multi-csv-export-model" title="<?php echo $lang['Export_Users']; ?>"><i class="fa fa-download"></i> <?php echo $lang['Export']; ?></button>
                                                <?php } ?>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="">
                                        <?php
                                        $where = "";
                                        $searchText = urldecode(base64_decode($_GET['searchtxt']));
                                        $searchText = preg_replace("/[^\w$\x{0080}-\x{FFFF}-@. ]+/u", "", $searchText);
                                        if (!empty($activeInusers)) {
                                            if ($activeInusers == '3') {
                                                $activeInusers = "0";
                                                $activeInactive = " and  active_inactive_users='$activeInusers'";
                                                $where .= " and  user_id in($sameGroupIDs) $activeInactive";
                                            } else if ($activeInusers == '2') {
                                                $activeInactive .= " and  user_id in($sameGroupIDs)";
                                                $where .= " $activeInactive";
                                            } else if ($activeInusers == '1') {
                                                $activeInactive .= " and  active_inactive_users='$activeInusers'";
                                                $where .= " and  user_id in($sameGroupIDs) $activeInactive";
                                            }
                                            if (isset($searchText) && !empty($searchText)) {
                                                $where .= " $activeInactive and (CONCAT(first_name,' ', last_name) like '%$searchText%'  or designation like '%$searchText%' or phone_no like '%$searchText%' or user_email_id like '%$searchText%' or emp_id like '%$searchText%')";
                                            }

                                            if (!empty($userIds_selected_group)) {
                                                //run if filter by group is use
                                                $where .= " and  user_id in($userIds_selected_group) $activeInactive";
                                            }
                                        } else {
                                            $where .= " and  active_inactive_users='1' and user_id in($sameGroupIDs)";
                                            if (isset($searchText) && !empty($searchText)) {
                                                $where .= " and (CONCAT(first_name,' ', last_name) like '%$searchText%'  or designation like '%$searchText%' or phone_no like '%$searchText%' or user_email_id like '%$searchText%' or emp_id like '%$searchText%')";
                                            }
                                            if (!empty($userIds_selected_group)) {
                                                //run if filter by group is use
                                                $where .= " and  user_id in($userIds_selected_group)";
                                            }
                                        }
                                        mysqli_set_charset($db_con, "utf8");
                                        if (!empty($where)) {
                                            $sql = "SELECT * FROM  tbl_user_master where user_id!=1 and user_id!='$_SESSION[cdes_user_id]' $where";
                                        } else {
                                            $sql = "SELECT * FROM  tbl_user_master where user_id!=1 and user_id!='$_SESSION[cdes_user_id]' and user_id in($sameGroupIDs) and active_inactive_users='1'";
                                        }
                                        $retval = mysqli_query($db_con, $sql); // or die('Could not get data: ' . mysqli_error($db_con));
                                        $foundnum = mysqli_num_rows($retval);
                                        if ($foundnum > 0) {
                                            $StartPoint = preg_replace("/[^0-9]/", "", $_GET['limit']); //filter limit from all special chars
                                            if (is_numeric($StartPoint)) {
                                                $per_page = $StartPoint;
                                            } else {
                                                $per_page = 10;
                                            }
                                            $start = preg_replace("/[^0-9]/", "", $_GET['start']); //filter start variable
                                            $start = isset($start) ? ($start > 0) ? $start : 0 : 0;
                                            $max_pages = ceil($foundnum / $per_page);
                                            if (!$start) {
                                                $start = 0;
                                            }
                                            $limit = preg_replace("/[^0-9]/", "", $_GET['limit']);
                                            ?>
                                            <div class="container">
                                                <label><?php echo $lang['show_lst']; ?> </label>
                                                <select id="limit" class="input-sm">
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
                                                <label><?php echo $lang['User_List']; ?></label>

                                                <div class="record pull-right">
                                                    <label> <?php echo $start + 1 ?> <?php echo $lang['To']; ?> <?php
                                                    if ($start + $per_page > $foundnum) {
                                                        echo $foundnum;
                                                    } else {
                                                        echo ($start + $per_page);
                                                    }
                                                    ?> <span><?php echo $lang['ttl_recrds']; ?>: <?php echo $foundnum; ?></span></label>
                                                </div>
                                            </div>
                                            <div class="box-body">
                                                <?php

                                                function showData($user, $rwgetRole, $db_con, $start, $lang) {
                                                    ?>
                                                    <table class="table table-striped table-bordered js-sort-table" id="table_demo_icons">
                                                        <thead>
                                                            <tr>
                                                                <th class="sort-js-none" ><div class="checkbox checkbox-primary"><input  type="checkbox" class="checkbox-primary" id="select_all"> <label for="select_all"> <strong><?php echo $lang['All']; ?></strong></label></div></th>
                                                                <th><?php echo $lang['User_Name']; ?></th>
                                                                <th><?php echo $lang['Designation']; ?></th>
                                                                <th><?php echo $lang['User_Email']; ?></th>
                                                                <th class="sort-js-number" ><?php echo $lang['Phone']; ?></th>
                                                                <th><?php echo $lang['User_Role']; ?></th>
                                                                <th><?php echo $lang['Superior_Name']; ?></th>
                                                                <th><?php echo $lang['department_name']; ?></th>
                                                                <th style="width: 160px;" class="sort-js-none" ><?php echo $lang['Actions']; ?></th>

                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $i = 1;
                                                            $i += $start;
                                                            while ($rwUser = mysqli_fetch_assoc($user)) {
                                                                ?>
                                                                <?php
                                                                if ($_SESSION['cdes_user_id'] == $rwUser['user_id']) {

                                                                    continue;
                                                                } else {
                                                                    ?>
                                                                    <?php if ($rwUser['user_id'] != 1) { ?>
                                                                        <tr class="gradeX"> 
                                                                            <td><div class="checkbox checkbox-primary"><input type="checkbox" class="checkbox-primary emp_checkbox" id="checkbox<?php echo $i; ?>" data-doc-id="<?php echo $rwUser['user_id']; ?>"><label for="checkbox<?php echo $i; ?>">  <?php echo $i . '.'; ?> </label> </div></td>
                                                                            <td>
                                                                                <!--for show who is online -->
                                                                                <?php if ($rwgetRole['create_user'] == '1' || $rwgetRole['modify_userlist'] == '1' || $rwgetRole['delete_userlist'] == '1' || $rwgetRole['view_userlist'] == '1') { ?>
                                                                                    <div class="radio radio-success radio-inline user-text">
                                                                                        <?php if ($rwUser['current_login_status'] == 1) { ?>
                                                                                            <input type="radio" class="login" name="radio<?php echo $i; ?>" id="inlineRadio<?php echo $i; ?>"  <?php
                                                                                            echo 'checked';
                                                                                            ?> title="<?= $lang['currently_login'] ?>" readonly>
                                                                                               <?php } else { ?>
                                                                                            <input type="radio" class="login" name="radio<?php echo $i; ?>" id="inlineRadio<?php echo $i; ?>" title="<?= $lang['currently_logout'] ?>" readonly>
                                                                                        <?php }
                                                                                        ?>

                                                                                        <label for="inlineRadio<?php echo $i; ?>"><?php echo $rwUser['first_name'] . ' '; ?>
                                                                                            <?php echo $rwUser['last_name']; ?></label>
                                                                                    </div>
                                                                                </td>
                                                                                <td><?php echo $rwUser['designation']; ?></td>
                                                                                <td><?php echo $rwUser['user_email_id']; ?></td>
                                                                                <td><?php echo $rwUser['phone_no']; ?></td>
                                                                                <td>
                                                                                    <?php
                                                                                    $chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um where FIND_IN_SET('$rwUser[user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));
                                                                                    $rwcheckUser = mysqli_fetch_assoc($chekUsr);
                                                                                    $rol = mysqli_query($db_con, "select * from tbl_user_roles where role_id='$rwcheckUser[role_id]'") or die('error in role' . mysqli_error($db_con));
                                                                                    $rwRole = mysqli_fetch_assoc($rol);
                                                                                    echo $rwRole['user_role'];
                                                                                    ?>
                                                                                </td>
                                                                                <td>
                                                                                    <?php
                                                                                    if (!empty($rwUser['superior_name'])) {
                                                                                        $reporterId = $rwUser['superior_name'];
                                                                                        $reporter = mysqli_query($db_con, "select first_name,last_name,user_email_id from tbl_user_master where user_id='$reporterId'") or die('Error : ' . mysqli_error($db_con));
                                                                                        $rwreporterId = mysqli_fetch_assoc($reporter);
                                                                                        if (!empty($rwreporterId['user_email_id'])) {
                                                                                            echo '<p data-toggle="tooltip" title="' . $rwreporterId['user_email_id'] . '">' . $rwreporterId['first_name'] . ' ' . $rwreporterId['last_name'] . '</p>';
                                                                                        } else {
                                                                                            echo $rwreporterId['first_name'] . ' ' . $rwreporterId['last_name'] . '<p>(' . $rwreporterId['user_email_id'] . ')</p>';
                                                                                        }
                                                                                    } else {
                                                                                        echo'';
                                                                                    }
                                                                                    ?>

                                                                                </td>
                                                                                <td>
                                                                                    <?php
                                                                                    if (!empty($rwUser['dept_id'])) {
                                                                                        $deptId = $rwUser['dept_id'];
                                                                                        $dept_data = mysqli_query($db_con, "select * from tbl_department where id IN ($deptId)") or die('Error : ' . mysqli_error($db_con));
                                                                                        $departmentNames = [];
                                                                                        while ($department = mysqli_fetch_assoc($dept_data)) {
                                                                                        
                                                                                            $departmentNames[] = $department['department_name'];
                                                                                        }
                                                                                        
                                                                                        if (!empty($departmentNames)) {
                                                                                            echo implode(', ', $departmentNames);    
                                                                                        }
                                                                                       
                                                                                    } else {
                                                                                        echo'';
                                                                                    }
                                                                                    ?>

                                                                                </td>
                                                                                <td class="actions">
                                                                                    <?php if ($rwgetRole['modify_userlist'] == '1') { ?>
                                                                                        <a href="#" class="on-default edit-row btn btn-primary" data-toggle="modal" data-target="#con-close-modal" id="editRow" data="<?php echo $rwUser['user_id']; ?>" title="<?= $lang['Modify_column']; ?>"><i class="fa fa-edit"></i></a>

                                                                                    <?php } ?>
                                                                                    <?php if ($rwgetRole['delete_userlist'] == '1') { ?>
                                                                                        <a href="#" class="on-default remove-row btn btn-danger" data-toggle="modal" data-target="#dialog" id="removeRow" data="<?php echo $rwUser['user_id']; ?>" title="<?= $lang['Delete']; ?>"><i class="fa fa-trash-o"></i></a>
                                                                                    <?php } if ($rwgetRole['user_activate_deactivate'] == 1) { ?>
                                                                                        <?php if ($rwUser['active_inactive_users'] == 1) { ?>
                                                                                            <a href="#" class="on-default edit-row btn btn-success" data-toggle="modal" id="dective" data-target="#activate" data="<?php echo $rwUser['user_id']; ?>" title="<?= $lang['active_user']; ?>"><i class="fa fa-toggle-on"></i></a>
                                                                                        <?php } else { ?>
                                                                                            <a href="#" class="on-default edit-row btn btn-danger" data-toggle="modal" data-target="#deactivate" id="active" data="<?php echo $rwUser['user_id']; ?>" title="<?= $lang['Deactivated_User']; ?>"><i class="fa fa-toggle-off"></i></a>
                                                                                            <?php
                                                                                        }
                                                                                    }
                                                                                    ?>  
                                                                                </td>

                                                                            </tr>
                                                                            <?php
                                                                            $i++;
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                            ?>

                                                        </tbody>
                                                    </table>
													<?php
													if ($rwgetRole['modify_userlist'] == '1') {
                                                                ?>
                                                                <tr>
                                                                    <td colspan="8"><a href="#" class="on-default edit-row btn btn-warning" data-toggle="modal" id="chngeuserIds" data-target="#changePassword"><i class="fa fa-key"></i> <?= $lang['change_multiple_user_password']; ?></a></td>
                                                                </tr>
                                                            <?php } ?>

                                                    <?php
                                                }
                                                ?>
                                                <?php
                                                $start = xss_clean(trim($start));
                                                $per_page = xss_clean(trim($per_page));
                                                if (!empty($where)) {

                                                    $users = mysqli_query($db_con, "select * from tbl_user_master where user_id!=1 and user_id!='$_SESSION[cdes_user_id]' $where order by first_name, last_name asc LIMIT $start, $per_page");
                                                } else {
                                                    $users = mysqli_query($db_con, "select * from tbl_user_master where user_id!=1 and user_id!='$_SESSION[cdes_user_id]' and user_id in($sameGroupIDs) and active_inactive_users='1' order by first_name, last_name asc LIMIT $start, $per_page");
                                                }
                                                showData($users, $rwgetRole, $db_con, $start, $lang);
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
                                                        $groupId = xss_clean(trim($_GET['GrpId']));
                                                        //previous button
                                                        if (!($start <= 0))
                                                            echo " <li><a href='?start=$prev&limit=$per_page&GrpId=" . $groupId . "'>$lang[Prev]</a> </li>";
                                                        else
                                                            echo " <li class='disabled'><a href='javascript:void(0)'>$lang[Prev]</a> </li>";
                                                        //pages 
                                                        if ($max_pages < 7 + ($adjacents * 2)) {   //not enough pages to bother breaking it up
                                                            $i = 0;
                                                            for ($counter = 1; $counter <= $max_pages; $counter++) {
                                                                if ($i == $start) {
                                                                    echo "<li class='active'><a href='?start=$i&limit=$per_page&GrpId=" . $groupId . "'><b>$counter</b></a> </li>";
                                                                } else {
                                                                    echo "<li><a href='?start=$i&limit=$per_page&GrpId=" . $groupId . "'>$counter</a></li> ";
                                                                }
                                                                $i = $i + $per_page;
                                                            }
                                                        } elseif ($max_pages > 5 + ($adjacents * 2)) {    //enough pages to hide some
                                                            //close to beginning; only hide later pages
                                                            if (($start / $per_page) < 1 + ($adjacents * 2)) {
                                                                $i = 0;
                                                                for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                                                                    if ($i == $start) {
                                                                        echo " <li class='active'><a href='?start=$i&limit=$per_page&GrpId=" . $groupId . "'><b>$counter</b></a></li> ";
                                                                    } else {
                                                                        echo "<li> <a href='?start=$i&limit=$per_page&GrpId=" . $groupId . "'>$counter</a> </li>";
                                                                    }
                                                                    $i = $i + $per_page;
                                                                }
                                                            }
                                                            //in middle; hide some front and some back
                                                            elseif ($max_pages - ($adjacents * 2) > ($start / $per_page) && ($start / $per_page) > ($adjacents * 2)) {
                                                                echo " <li><a href='?start=0'>1</a></li> ";
                                                                echo "<li><a href='?start=$per_page'>2</a></li>";
                                                                echo "<li><a href='javascript:void(0)'>...</a></li>";

                                                                $i = $start;
                                                                for ($counter = ($start / $per_page) + 1; $counter < ($start / $per_page) + $adjacents + 2; $counter++) {
                                                                    if ($i == $start) {
                                                                        echo " <li class='active'><a href='?start=$i&limit=$per_page&GrpId=" . $groupId . "'><b>$counter</b></a></li> ";
                                                                    } else {
                                                                        echo " <li><a href='?start=$i&limit=$per_page&GrpId=" . $groupId . "'>$counter</a> </li>";
                                                                    }
                                                                    $i = $i + $per_page;
                                                                }
                                                            }
                                                            //close to end; only hide early pages
                                                            else {
                                                                echo "<li> <a href='?start=0'>1</a> </li>";
                                                                echo "<li><a href='?start=$per_page&GrpId=" . $groupId . "'>2</a></li>";
                                                                echo "<li><a href='javascript:void(0)'>...</a></li>";

                                                                $i = $start;
                                                                for ($counter = ($start / $per_page) + 1; $counter <= $max_pages; $counter++) {
                                                                    if ($i == $start) {
                                                                        echo " <li class='active'><a href='?start=$i&limit=$per_page&GrpId=" . $groupId . "'><b>$counter</b></a></li> ";
                                                                    } else {
                                                                        echo "<li> <a href='?start=$i&limit=$per_page&GrpId=" . $groupId . "'>$counter</a></li> ";
                                                                    }
                                                                    $i = $i + $per_page;
                                                                }
                                                            }
                                                        }
                                                        //next button
                                                        if (!($start >= $foundnum - $per_page))
                                                            echo "<li><a href='?start=$next&limit=$per_page&GrpId=" . $groupId . "'>$lang[Next]</a></li>";
                                                        else
                                                            echo "<li class='disabled'><a href='javascript:void(0)'>$lang[Next]</a></li>";
                                                        ?>
                                                    </ul>
                                                    <?php
                                                }
                                                echo "</center>";
                                            } else {
                                                ?>
                                                <table class="table table-striped table-bordered" id="table_demo_icons">
                                                    <thead>
                                                        <tr>
                                                            <th><?php echo $lang['Sr_No']; ?></th>
                                                            <th><?php echo $lang['User_Name']; ?></th>
                                                            <th><?php echo $lang['Designation']; ?></th>
                                                            <th><?php echo $lang['User_Email']; ?></th>
                                                            <th><?php echo $lang['Phone']; ?></th>
                                                            <th><?php echo $lang['User_Role']; ?></th>
                                                            <th><?php echo $lang['Superior_Name']; ?></th>
                                                            <th style="width: 160px;"><?php echo $lang['Actions']; ?></th>

                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr class="text-center"><td colspan="8"><label><strong class="text-danger"><i class="ti-face-sad text-pink"></i> <?php echo $lang['Who0ps!_No_Records_Found']; ?></strong></label></td></tr>
                                                    </tbody>
                                                </table>
                                            <?php }
                                            ?>	
                                        </div>                                        	
                                    </div>
                                </div>
                                <!-- end: page -->

                            </div> <!-- end Panel -->
                        </div>

                        <!-- MODAL -->
                        <div id="dialog" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog"> 
                                <div class="panel panel-color panel-danger"> 
                                    <div class="panel-heading"> 
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                                        <label><h2 class="panel-title"><?php echo $lang['Are_you_sure_that_you_want_to_delete_this_User']; ?></h2></label> 
                                    </div> 
                                    <form method="post" >
                                        <div class="panel-body">
                                            <label>
                                                <p><?php echo $lang['Remarks']; ?></p> 
                                            </label>
                                            <textarea cols="75" rows="5" class="form-control translatetext" name="delreason" placeholder="<?php echo $lang['user_del_reason']; ?>" required></textarea>
                                        </div>

                                        <div class="modal-footer">
                                            <div class="col-md-12 text-right">
                                                <input type="hidden" id="uid" name="uid">
                                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                                                <button type="submit" name="delete" id="dialogConfirm" class="btn btn-danger waves-effect waves-light"><i class="fa fa-trash-o"></i> <?php echo $lang['Delete']; ?></button>

                                            </div>
                                        </div>
                                    </form>
                                </div> 
                            </div>
                        </div>
                        <!-- end Modal -->
                        <div id="multi-csv-export-model" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog"> 
                                <div class="panel panel-color panel-primary"> 
                                    <div class="panel-heading"> 
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                                        <label><h2 class="panel-title"><?php echo $lang['Export_Listed_User_Lists']; ?></h2></label> 
                                    </div> 
                                    <form action="multi-export-user-data"  method="post">
                                        <div class="panel-body">
                                            <div class="col-md-5  m-t-10">
                                                <strong class="text-primary"><?php echo $lang['Select_Files_for_Export_Format']; ?> : </strong>
                                            </div>
                                            <div class="col-md-3">
                                                <select class="select2 input-sm" id="my_multi_select1" name="select_Fm">

                                                    <option value="xlsx"><?php echo $lang['Excel']; ?></option>
                                                    <!--  <option value="excel">Excel</option>-->
                                                    <option value="pdf"><?php echo $lang['Pdf']; ?></option>
                                                </select>
                                            </div>

                                        </div>
                                        <div class="modal-footer">
                                            <input type="hidden" value="<?php echo $group_id; ?>" name="userIds">
                                            <input type="hidden" value="<?php echo urldecode(base64_decode($_GET['searchtxt'])); ?>" name="searchtext">
                                            <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                                            <button class="btn btn-primary waves-effect waves-light" type="submit" name="exportUser"><i class="fa fa-download"></i> <?php echo $lang['Export']; ?></button>
                                        </div>
                                    </form>

                                </div> 
                            </div>
                        </div>
                        <div id="con-close-modal" class="modal fade"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog modal-lg"> 
                                <div class="modal-content"> 
                                    <form method="post" enctype="multipart/form-data">
                                        <div class="modal-header"> 
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button> 

                                            <h4 class="modal-title"><?php echo $lang['Update_Profile']; ?></h4> 
                                        </div>

                                        <div class="modal-body" id="modalModify">
                                            <img src="assets/images/load.gif" alt="load" class="img-responsive center-block" />

                                        </div> 
                                        <div class="modal-footer">

                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                                            <button type="submit" name="editProfile" class="btn btn-primary waves-effect waves-light"><?php echo $lang['Save_changes']; ?></button> 
                                        </div>
                                    </form>

                                </div> 
                            </div>
                        </div><!-- /.modal -->
                        <div id="activate" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog"> 
                                <div class="panel panel-color panel-success"> 
                                    <div class="panel-heading"> 
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                                        <label><h2 class="panel-title"><?= $lang['Are_u_confirm'] ?></h2></label> 
                                    </div> 
                                    <form method="post">
                                        <div class="panel-body">
                                            <p class="text-danger"><?php echo $lang['Are_you_sure_that_you_want_to_deactivate_this_User'] ?> ? </p>
                                        </div>
                                        <div class="modal-footer">
                                            <div class="col-md-12 text-right">
                                                <input type="hidden" id="dectiveId" name="dacvtUsr">
                                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close'] ?></button> 
                                                <button type="submit" name="deactivate" id="dialogConfirm" class="btn btn-danger waves-effect waves-light"><i class="fa fa-toggle-off"></i> <?= $lang['Deactivate'] ?></button>

                                            </div>
                                        </div>
                                    </form>
                                </div> 
                            </div>
                        </div>

                        <div id="deactivate" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog"> 
                                <div class="panel panel-color panel-danger"> 
                                    <div class="panel-heading"> 
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                                        <label><h2 class="panel-title"><?= $lang['Are_u_confirm'] ?></h2></label> 
                                    </div> 
                                    <form method="post" >
                                        <div class="panel-body">
                                            <p class="text-danger"><?= $lang['Are_you_sure_that_you_want_to_activate_this_User'] ?> ? </p>
                                        </div>
                                        <div class="modal-footer">
                                            <div class="col-md-12 text-right">
                                                <input type="hidden" id="activateId" name="actUser" value="">
                                                <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal"><?= $lang['Close'] ?></button> 
                                                <button type="submit" name="activate" id="dialogConfirm" class="btn btn-success waves-effect waves-light"><i class="fa fa-toggle-on"></i> <?= $lang['Activate'] ?></button>

                                            </div>
                                        </div>
                                    </form>
                                </div> 
                            </div>
                        </div>
                        <div id="changePassword" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog"> 
                                <div class="panel panel-color panel-warning"> 
                                    <div class="panel-heading"> 
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                                        <h2 class="panel-title" id="warn"><?= $lang['change_password_selected_users']; ?></h2>
                                        <h2 class="panel-title" id="warning"><?= $lang['Hre_msge']; ?></h2>

                                    </div> 
                                    <form method="post">
                                        <div class="panel-body">
                                            <div class="form-group" id="unselectuser">
                                                <h5 class="text-danger"><?php echo $lang['select_user_one_more']; ?></h5>
                                            </div>
                                            <div class="row"  id="selectuser">
                                                <div class="form-group">
                                                    <label><?= $lang['pwd']; ?><span class="text-alert">*</span></label>
                                                    <input type="password" class="form-control" id="pwd2" name="changemultiuserpwd" required="required" data-parsley-minlength="<?= (!empty($rwpwdpolicy['minlen']) ? $rwpwdpolicy['minlen'] : '8'); ?>" data-parsley-maxlength="<?= (!empty($rwpwdpolicy['maxlen']) ? $rwpwdpolicy['maxlen'] : '8'); ?>" data-parsley-uppercase="<?= $rwpwdpolicy['uppercase']; ?>" data-parsley-lowercase="<?= $rwpwdpolicy['lowercase']; ?>" data-parsley-number="<?= $rwpwdpolicy['numbers']; ?>" data-parsley-special="<?= $rwpwdpolicy['s_char']; ?>" data-parsley-errors-container=".errorspannewpassinput" placeholder="<?= $lang['pwd']; ?>" />
                                                </div>
                                                <div class="form-group">
                                                    <label><?= $lang['Confirm_Password']; ?><span class="text-alert">*</span></label>
                                                    <input type="password" class="form-control" data-parsley-equalto="#pwd2" name="confirmuserspass" required="required" data-parsley-minlength="<?= (!empty($rwpwdpolicy['minlen']) ? $rwpwdpolicy['minlen'] : '8'); ?>" data-parsley-maxlength="<?= (!empty($rwpwdpolicy['maxlen']) ? $rwpwdpolicy['maxlen'] : '8'); ?>" data-parsley-uppercase="<?= $rwpwdpolicy['uppercase']; ?>" data-parsley-lowercase="<?= $rwpwdpolicy['lowercase']; ?>" data-parsley-number="<?= $rwpwdpolicy['numbers']; ?>" data-parsley-special="<?= $rwpwdpolicy['s_char']; ?>" data-parsley-errors-container=".errorspannewpassinput" placeholder="<?= $lang['Confirm_Password']; ?>" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <div class="col-md-12 text-right">
                                                <input type="hidden" id="selectuserIds" name="usersIds" value="">
                                                <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal"><?= $lang['Close'] ?></button> 
                                                <button type="submit" name="change-users-pwd" id="actionbtn" class="btn btn-warning waves-effect waves-light"><i class="fa fa-key"></i> <?= $lang['Chge_Pwd'] ?></button>

                                            </div>
                                        </div>
                                    </form>
                                </div> 
                            </div>
                        </div>
                    </div> <!-- container -->

                </div> <!-- content -->

                <?php require_once './application/pages/footer.php'; ?>
            </div>          
            <!-- END wrapper -->
            <?php require_once './application/pages/footerForjs.php'; ?>
        </div>
        <script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>

        <script src="assets/multi_function_script.js"></script>
        <script src="assets/js/gs_sortable.js"></script>
        <!-- for searchable select-->
        <script type="text/javascript">

                                                    var TSort_Data = new Array('table_demo_icons', '', 's', 's', 's', 's');
                                                    var TSort_Icons = new Array('<i class="fa fa-caret-up"></i>', '<i class="fa fa-caret-down"></i>');
                                                    tsRegister();

        </script>
        <script type="text/javascript">
            $(document).ready(function () {
                $('form').parsley();
            });
            $(".select2").select2();
            //export user in groupwise
            $(document).ready(function () {

                //                        $("#usergroup").change(function () {
                //                            var group_id = $(this).val();
                //                            //alert(group_id);
                //                            window.location.href = "?GrpId=" + btoa(encodeURI(group_id));
                //                        });
            });
            //firstname last name 
            $("input#userName, input#lastName").keypress(function (e) {
                //if the letter is not digit then display error and don't type anything
                if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57) && e.which != 46) {
                    //display error message
                    return true;
                } else {
                    return false;
                }
                str = $(this).val();
                str = str.split(".").length - 1;
                if (str > 0 && e.which == 46) {
                    return false;
                }
            });
            $("input#phone").keypress(function (e) {
                //if the letter is not digit then display error and don't type anything
                if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57) && e.which != 46) {
                    //display error message
                    return false;
                }
                str = $(this).val();
                str = str.split(".").length - 1;
                if (str > 0 && e.which == 46) {
                    return false;
                }
            });
        </script>
        <script>
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
            jQuery(document).ready(function ($) {
                $("#limit").change(function () {
                    lval = $(this).val();
                    url = removeParam("limit", url);
                    url = url + "&limit=" + lval;
                    window.open(url, "_parent");
                });
            });

            $("a#editRow").click(function () {
                var $id = $(this).attr('data');
                var token = $("input[name='token']").val();
                // $("#con-close-modal .modal-title").text("Update " +name+ "'s Profile");
                $.post("application/ajax/updateUser.php", {ID: $id, token: token}, function (result, status) {
                    if (status == 'success') {
                        $("#modalModify").html(result);
                        getToken();

                    }
                });

            });
            $("a#removeRow").click(function () {
                var id = $(this).attr('data');
                $("#uid").val(id);
            });

        </script>
        <script type="text/javascript">
            $("a#active").click(function () {
                var id = $(this).attr('data');
                $("#activateId").val(id);

            });
            $("a#dective").click(function () {
                var id = $(this).attr('data');
                $("#dectiveId").val(id);
            });
            //TableManageButtons.init();
            function searchUser() {
                var group_id = $("#usergroup").val();
                var searchtext = $("#searchtext").val();
                var allusers = $("#allusers").val();
                //alert(group_id);
                window.location.href = "?GrpId=" + btoa(encodeURI(group_id)) + "&searchtxt=" + btoa(encodeURI(searchtext)) + "&users=" + btoa(encodeURI(allusers));

            }
        </script>

    </body>
</html>
<?php
if (isset($_POST['change-users-pwd'], $_POST['token'])) {
    $userIds = $_POST['usersIds'];
    $count = explode(",", $userIds);
    $totalUsers = count($count);
    //for getting username.
    $userpasschange = array();
    $userepassword = mysqli_query($db_con, "SELECT first_name,last_name,user_email_id FROM tbl_user_master where user_id in($userIds)");
    while ($rwuserpasschange = mysqli_fetch_assoc($userepassword)) {
        $userpasschange[] = $rwuserpasschange['first_name'] . ' ' . $rwuserpasschange['last_name'] . '(' . $rwuserpasschange['user_email_id'] . ')';
    }
    $usernames = implode(', ', $userpasschange);

    $userspwd = mysqli_real_escape_string($db_con, $_POST['changemultiuserpwd']);
    $confirmuserspwd = mysqli_real_escape_string($db_con, $_POST['confirmuserspass']);
    if ($userspwd == $confirmuserspwd) {
        $pass = mysqli_query($db_con, "UPDATE tbl_user_master set password=sha1('$userspwd') WHERE user_id in($userIds)") or die("Error : pwd change" . mysqli_error($db_con));
        if ($pass) {
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Password edited','$date','$host','Password of $totalUsers users[$usernames] updated')") or die('error1 : ' . mysqli_error($db_con));
            $admin = $_SESSION['admin_user_name'] . ' ' . $_SESSION['admin_user_last'];
            $to = $_SESSION['adminMail'];
            require_once './mail.php';
            $subject = 'Password Changed';
            $mail = mailChangingMultipleUsersPassword($to, $userspwd, $projectName, $admin, $usernames, $totalUsers);
            if ($mail) {
                echo'<script> taskSuccess("' . basename($_SERVER[REQUEST_URI]) . '","' . $lang['password_updated_success'] . '"); </script>';
            } else {
                echo'<script> taskFailed("' . basename($_SERVER[REQUEST_URI]) . '","' . $lang['password_change_mailnotsent'] . '"); </script>';
            }
        } else {
            echo'<script> taskFailed("' . basename($_SERVER[REQUEST_URI]) . '", "' . $lang['ftc'] . '");</script>';
        }
    } else {
        echo'<script> taskFailed("' . basename($_SERVER[REQUEST_URI]) . '", "' . $lang['pwd_confirm_pwd_same'] . '");</script>';
    }
}
?>
<?php
if (isset($_POST['editProfile'], $_POST['token']) && !empty($_POST['uid'])) {
    $id = filter_input(INPUT_POST, "uid");
//    $fname = filter_input(INPUT_POST, "firstname");
//    $fname = preg_replace("/[^a-zA-Z ]/", "", $fname); //filter name

    $fname = trim($_POST['firstname']);
    $fname = preg_replace('/[^\w$\x{0080}-\x{FFFF}@#$&!%()_ <>]+/u', "", $fname);
    $fname = mysqli_real_escape_string($db_con, $fname);

    //$lname = filter_input(INPUT_POST, "lastname");
    //$lname = preg_replace("/[^a-zA-Z ]/", "", $lname); //filter name

    $lname = trim($_POST['lastname']);
    $lname = preg_replace('/[^\w$\x{0080}-\x{FFFF}@#$&!%()_ <>]+/u', "", $lname);
    $lname = mysqli_real_escape_string($db_con, $lname);

    $userto = $fname . ' ' . $lname;
    $phone = filter_input(INPUT_POST, "phone");
    $phone = preg_replace("/[^0-9]/", "", $phone); //filter phone
    $phone = mysqli_real_escape_string($db_con, $phone);
    $email = filter_input(INPUT_POST, "email");
    $email = preg_replace("/[^a-zA-Z0-9_@.-]/", "", $email); //filter email
    $email = mysqli_real_escape_string($db_con, $email);
    $empid = filter_input(INPUT_POST, "empId");
    $empid = preg_replace("/[^a-zA-Z0-9_]/", "", $empid); //filter empid
    $empid = mysqli_real_escape_string($db_con, $empid);
    $password = filter_input(INPUT_POST, "password");
    $password = mysqli_real_escape_string($db_con, $password);
    // $dept_id = filter_input(INPUT_POST, "dept_id");
    $dept_id = $_POST['dept_id'];
    $dept_ids = implode(',', $dept_id);


    //$designation = filter_input(INPUT_POST, "designation");
    //$designation = preg_replace("/[^a-zA-Z ]/", "", $designation); //filter name

    $designation = trim($_POST['designation']);
    $designation = preg_replace('/[^\w$\x{0080}-\x{FFFF}@#$&!%()_ <>]+/u', "", $designation);
    $designation = mysqli_real_escape_string($db_con, $designation);

    $groups = preg_replace("/[^a-zA-Z0-9,-_ ]/", "", $_POST['groups']);
    $userRole = preg_replace("/[^a-zA-Z0-9,-_ ]/", "", $_POST["userRole"]);

    if (isset($_FILES['image']['name']) && !empty($_FILES['image']['name'])) {

        $allowed = array('png', 'jpg', 'jpeg');
        $filename = $_FILES['image']['name'];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if (!in_array($ext, $allowed)) {
            echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '", "' . $lang['profile_image_allowed'] . '")</script>';

            exit();
        }
    }

    $image = addslashes(file_get_contents($_FILES['image']['tmp_name']));
    $slparentNameid = preg_replace("/[^a-zA-Z0-9,-_ ]/", "", $_POST['slparentName']);
    $slparentNameid = !empty($slparentNameid) ? $slparentNameid : 0;
    if (!empty($_POST['superiorName'])) {
        $superiorName = filter_input(INPUT_POST, "superiorName");
    }
    $previousRole = $_POST['previousProfile'];

    mysqli_set_charset($db_con, "utf8");
    $chkempid = mysqli_query($db_con, "select * from tbl_user_master where emp_id='$empid' and user_id!='$id' and emp_id!='' and emp_id IS NOT NULL"); // or die('Error:' . mysqli_error($db_con));
    $checkRoleInSame = mysqli_query($db_con, "select * from tbl_bridge_role_to_um where role_id='$userRole' and FIND_IN_SET('$id', user_ids) > 0"); //or die('Error:' . mysqli_error($db_con));
    $rl = mysqli_query($db_con, "SELECT * FROM tbl_user_roles WHERE role_id='$userRole'");
    $rl_row = mysqli_fetch_array($rl);

    if (mysqli_num_rows($chkempid) > 0) {
        echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '", "' . $lang['empid_alrdy_ext'] . '")</script>';
        exit(0);
    } else if (mysqli_num_rows($checkRoleInSame) == 0) {
        $checkRole = mysqli_query($db_con, "select * from tbl_bridge_role_to_um where role_id='$userRole'") or die('Error:' . mysqli_error($db_con));
        if (mysqli_num_rows($checkRole) > 0) {
            $rwCheckRole = mysqli_fetch_assoc($checkRole);
            $users = $rwCheckRole['user_ids'];
            $users = explode(",", $users);
            $users = implode(",", $users);
            if (!empty($users)) {
                $newUserIds = $users . ',' . $id;
            } else {
                $newUserIds = $id;
            }
            mysqli_set_charset($db_con, "utf8");
            $checkPreRole = mysqli_query($db_con, "select * from tbl_bridge_role_to_um where role_id='$previousRole'") or die('Error:' . mysqli_error($db_con));
            $preRole = mysqli_fetch_assoc($checkPreRole);
            $PreUserIs = $preRole['user_ids'];
            $PreUserUpdateIds = explode(",", $PreUserIs);
            $removeIndex = array_search($id, $PreUserUpdateIds);
            unset($PreUserUpdateIds[$removeIndex]);
            $PreUserUpdateIds = implode(",", $PreUserUpdateIds);
            $PreRoleupdate = mysqli_query($db_con, "update tbl_bridge_role_to_um set user_ids='$PreUserUpdateIds' where role_id='$previousRole'");
            if ($PreRoleupdate) {
                mysqli_set_charset($db_con, "utf8");
                $updateoldrole = mysqli_query($db_con, "update tbl_bridge_role_to_um set user_ids='$newUserIds' where role_id='$userRole'") or die('Error:' . mysqli_error($db_con));
                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'User profile of $rwUsername[first_name] $rwUsername[last_name] changed from $preProfile[user_role] to $rl_row[user_role]','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
            }
        } else {
            mysqli_set_charset($db_con, "utf8");
            $checkPreRole = mysqli_query($db_con, "select * from tbl_bridge_role_to_um where role_id='$previousRole'") or die('Error:' . mysqli_error($db_con));
            $preRole = mysqli_fetch_assoc($checkPreRole);
            $PreUserIs = $preRole['user_ids'];
            $PreUserUpdateIds = explode(",", $PreUserIs);
            $removeIndex = array_search($id, $PreUserUpdateIds);
            unset($PreUserUpdateIds[$removeIndex]);
            $PreUserUpdateIds = implode(",", $PreUserUpdateIds);
            $PreRoleupdate = mysqli_query($db_con, "update tbl_bridge_role_to_um set user_ids='$PreUserUpdateIds' where role_id='$previousRole'");
            if ($PreRoleupdate) {
                mysqli_set_charset($db_con, "utf8");
                $AddNewrow = mysqli_query($db_con, "insert into tbl_bridge_role_to_um(role_id, user_ids) values('$userRole', '$id')");
                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'User profile of $rwUsername[first_name] $rwUsername[last_name] changed from $preProfile[user_role] to $rl_row[user_role]','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
            }
        }
    }
    mysqli_set_charset($db_con, "utf8");
    $userPswrd = mysqli_query($db_con, "SELECT first_name,last_name FROM tbl_user_master where user_id = $id");
    $rwUserPwd = mysqli_fetch_assoc($userPswrd);
    $Ufname = $rwUserPwd['first_name'] . ' ' . $rwUserPwd['last_name'];
    if (!empty($password)) {

        $pwdupdate = mysqli_query($db_con, "update tbl_user_master set password=sha1('$password') where user_id='$id'");
        if ($pwdupdate) {
            mysqli_set_charset($db_con, "utf8");
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Password of $Ufname has been changed.','$date',null,'$host',null)"); //or die('error : ' . mysqli_error($db_con));
        }
    }
    if (!empty($image)) {
        mysqli_set_charset($db_con, "utf8");
        $edit = mysqli_query($db_con, "update tbl_user_master set `user_email_id`='$email', `first_name`='$fname', `last_name`='$lname', `phone_no`='$phone',`designation`='$designation', `superior_name`='$superiorName',`emp_id`='$empid',`superior_email`='$superiorEmail', `profile_picture`='$image' where user_id='$id'"); //or die('Error : ' . mysqli_error($db_con));
        //dv
        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Profile picture of $Ufname has been changed.','$date',null,'$host',null)"); //or die('error : ' . mysqli_error($db_con));
    } else {
        mysqli_set_charset($db_con, "utf8");
        $edit = mysqli_query($db_con, "update tbl_user_master set `user_email_id`='$email', `first_name`='$fname', `last_name`='$lname', `phone_no`='$phone',`designation`='$designation', `superior_name`='$superiorName',`emp_id`='$empid', `superior_email`='$superiorEmail', `dept_id`='$dept_ids' where user_id='$id'"); //or die('Error : ' . mysqli_error($db_con));
        //dv
        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Profile of $Ufname has been modified.','$date',null,'$host',null)"); //or die('error : ' . mysqli_error($db_con));
    }
    //MU
    if (!empty($slparentNameid)) {
        $upslperm = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id='$slparentNameid'");
        $rwUpslperm = mysqli_fetch_assoc($upslperm);
        $storage_validate = mysqli_query($db_con, "select * from tbl_storagelevel_to_permission where user_id='$id'"); //check User id exist or notif exist then update if not insert
        if (mysqli_num_rows($storage_validate) > 0) {
            $editPerm = mysqli_query($db_con, "update tbl_storagelevel_to_permission set `sl_id`='$slparentNameid' where user_id='$id' "); //or die('Error: sl_id update' . mysqli_error($db_con));
            //dv
            mysqli_set_charset($db_con, "utf8");
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,$slparentNameid,'Storage Permission $rwUpslperm[sl_name] Alloted to $Ufname.','$date',null,'$host',null)"); //or die('error : ' . mysqli_error($db_con));
        } else {
            $editPerm = mysqli_query($db_con, "insert into tbl_storagelevel_to_permission(`user_id`,`sl_id`) values('$id','$slparentNameid')"); //or die('Error: sl_id update' . mysqli_error($db_con));
            //dv
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,$slparentNameid,'Storage Permission $rwUpslperm[sl_name] Alloted to $Ufname.','$date',null,'$host',null)"); //or die('error : ' . mysqli_error($db_con));
        }
    }
    //MU
    if ($edit) {
        $groups = array_filter($groups, function($value) {
            return $value !== '';
        });

        // print_r($groups); die;
        $groupids = implode(",", $groups);
        $userGrpPerm = mysqli_query($db_con, "select * from tbl_group_master where group_id in($groupids)"); //or die('Error' . mysqli_error($db_con));
        while ($rwgrpPrm = mysqli_fetch_assoc($userGrpPerm)) {
            $grpNmae = $rwgrpPrm['group_name'];
            if (!empty($groups)) {
                $flag = 0;
                //reset group user
                $userGroup = mysqli_query($db_con, "select * from tbl_bridge_grp_to_um where find_in_set('$id',user_ids)"); //or die('Error' . mysqli_error($db_con));
                while ($rwUsergroup = mysqli_fetch_assoc($userGroup)) {
                    $users = $rwUsergroup['user_ids'];
                    $users = explode(",", $users);
                    $users = array_diff($users, array($id));
                    $users = implode(",", $users);
                    $resetBridgegrpusr = mysqli_query($db_con, "update tbl_bridge_grp_to_um set user_ids='$users' where group_id='$rwUsergroup[group_id]'");
                }

                //update user in groups
                foreach ($groups as $groupid) {
                    $check = mysqli_query($db_con, "select * from tbl_bridge_grp_to_um where group_id='$groupid'");
                    if (mysqli_num_rows($check) <= 0) {
                        $grpmap = mysqli_query($db_con, "insert into tbl_bridge_grp_to_um(group_id,user_ids) values('$groupid','$user_id')"); //or die('Error 1' . mysqli_error($db_con));
                        if ($grpmap) {
                            $flag = 1;
                        }
                    } else {
                        $rwCheck = mysqli_fetch_assoc($check);
                        $userids = $rwCheck['user_ids'];
                        if (!empty($userids)) {
                            $userids = explode(",", $userids);
                            if (!in_array($id, $userids)) {
                                array_push($userids, $id);
                            }
                            $userids = implode(",", $userids);
                        } else {
                            $userids = $id;
                        }
                        $grpmap = mysqli_query($db_con, "update tbl_bridge_grp_to_um set user_ids ='$userids' where group_id='$groupid'"); //or die('Error' . mysqli_error($db_con));
                        if ($grpmap) {
                            $flag = 1;
                        }
                    }
                }
                //end update user in group
                if ($flag == 1) {
                    //dv
                    mysqli_set_charset($db_con, "utf8");
                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'$Ufname become user of $grpNmae group.','$date',null,'$host','')"); //or die('error : ' . mysqli_error($db_con));
                }
            } else {
                header('location:' . $_SERVER['HTTP_REFERER']);
            }
        }
        echo '<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['User_profile_updated_successfully'] . '!");</script>';
    } else {
        echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Opps_Sbmsn_fld'] . '!");</script>';
    }
    mysqli_close($db_con);
}
if (isset($_POST['delete'], $_POST['token'])) {
    $id = xss_clean($_POST['uid']);
    $reason = xss_clean(trim($_POST['delreason']));
    mysqli_set_charset($db_con, "utf8");
    $userNme = mysqli_query($db_con, "SELECT * FROM tbl_user_master where user_id = '$id'");
    $rwUserNme = mysqli_fetch_assoc($userNme);
    $firstName = $rwUserNme['first_name'];
    $lastName = $rwUserNme['last_name'];
    $del = mysqli_query($db_con, "delete from tbl_user_master where user_id='$id'"); //or die('Error:' . mysqli_error($db_con));
    $deluser_fm_lvl = mysqli_query($db_con, "delete from tbl_storagelevel_to_permission where user_id='$id'"); //or die('Error:' . mysqli_error($db_con));
    $deluser_frm_comment = mysqli_query($db_con, "delete from tbl_task_comment where user_id='$id'"); //or die('Error: del user in comment' . mysqli_error($db_con));
    $deluser_frm_annotation = mysqli_query($db_con, "delete from tbl_anotation where anotation_by='$id'"); //or die('Error: del user in comment' . mysqli_error($db_con));
    // $deluser_frm_assigned_wf = mysqli_query($db_con, "delete from tbl_doc_assigned_wf where assign_by = '$id' or action_by='$id'") or die('Error: del user in comment' . mysqli_error($db_con));
    $removeuser = mysqli_query($db_con, "SELECT * FROM `tbl_bridge_role_to_um`"); //or die('Error: ff' . mysqli_error($db_con));
    $removeuser_brg_gp = mysqli_query($db_con, "SELECT * FROM `tbl_bridge_grp_to_um`");
    while ($row_remove = mysqli_fetch_array($removeuser)) {
        $ids3 = explode(',', $row_remove['user_ids']);

        if (($key = array_search($id, $ids3)) !== false) {
            unset($ids3[$key]);
            $user_ids = implode(',', $ids3);
            $briz_id = $row_remove['id'];
            break;
        }
    }
    while ($row_remove_brg_gp = mysqli_fetch_array($removeuser_brg_gp)) {
        $gp_user_ids = explode(',', $row_remove_brg_gp['user_ids']);

        if (($key = array_search($id, $gp_user_ids)) !== false) {
            unset($gp_user_ids[$key]);
            $gp_user_ids = implode(',', $gp_user_ids);
            $gp_briz_id = $row_remove_brg_gp['id'];

            break;
        }
    }
    $update_rol_ids = mysqli_query($db_con, "UPDATE `tbl_bridge_role_to_um` SET user_ids='$user_ids' WHERE id='$briz_id'"); //or die('Error:' . mysqli_error($db_con));
    $update_gp_ids = mysqli_query($db_con, "UPDATE `tbl_bridge_grp_to_um` SET user_ids='$gp_user_ids' WHERE id='$gp_briz_id'"); //or die('Error:' . mysqli_error($db_con));
    if ($update_rol_ids && $update_gp_ids) {
        mysqli_set_charset($db_con, "utf8");
        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,' User $firstName $lastName Deleted','$date',null,'$host','$reason')"); //or die('error : ' . mysqli_error($db_con));
        echo '<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['User_Deleted_Successfully'] . '");</script>';
    }
    mysqli_close($db_con);
}
?>
<?php
if (isset($_POST['activate'], $_POST['token'])) {
    $actUserId = mysqli_escape_string($db_con, $_POST['actUser']);
    $actUserId = preg_replace("/[^0-9]/", "", $actUserId);
    if (!empty($_POST['extenddate'])) {
        $ExtentLogin = mysqli_escape_string($db_con, $_POST['extendduration']);
        $ExtentLogin = date('Y-m-d', strtotime($ExtentLogin));
    } else {
        $ExtentLogin = NULL;
    }

    $UserActive = mysqli_query($db_con, "select first_name, last_name from tbl_user_master where user_id='$actUserId'");
    $rwUserActive = mysqli_fetch_assoc($UserActive);
    $acvtUser = mysqli_query($db_con, "update tbl_user_master set active_inactive_users ='1', failed_login_attempts='0', current_login_status='0', login_disabled_date='$ExtentLogin' where user_id = '$actUserId'") or die('Error:' . mysqli_error($db_con));
    if ($acvtUser) {
        mysqli_set_charset($db_con, "utf8");
        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`,`system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','User activated','$date','$host','User $rwUserActive[first_name] $rwUserActive[last_name] activated.')") or die('error : ' . mysqli_error($db_con));
        echo '<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['User_Activated_successfully'] . '");</script>';
    }
}
?>
<?php
if (isset($_POST['deactivate'], $_POST['token'])) {
    $DeactUserId = mysqli_escape_string($db_con, $_POST['dacvtUsr']);
    $DeactUserId = preg_replace("/[^0-9]/", "", $DeactUserId);
    $UserdeActive = mysqli_query($db_con, "select first_name, last_name from tbl_user_master where user_id='$DeactUserId'");
    $rwUserdeActive = mysqli_fetch_assoc($UserdeActive);
    $deacvtUser = mysqli_query($db_con, "update tbl_user_master set active_inactive_users ='0' where user_id = '$DeactUserId'") or die('Error:' . mysqli_error($db_con));
    if ($deacvtUser) {
        mysqli_set_charset($db_con, "utf8");
        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`,`system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','User deactivated','$date','$host','User $rwUserdeActive[first_name] $rwUserdeActive[last_name] Deactivated.')") or die('error : ' . mysqli_error($db_con));
        echo '<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['User_Deactivated_successfully'] . '");</script>';
    }
}
?>