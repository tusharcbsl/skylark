<?php
require_once '../../sessionstart.php';
//require_once '../../loginvalidate.php';
if (isset($_SESSION['lang'])) {
    $file = "../../" . $_SESSION['lang'] . ".json";
} else {
    $file = "../../English.json";
}
$data = file_get_contents($file);
$lang = json_decode($data, true);
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout");
}

require './../config/database.php';
//for user role

mysqli_set_charset($db_con, "utf8");
$langDetail = mysqli_query($db_con, "select * from tbl_language where lang_name='$_SESSION[lang]'") or die('Error : ' . mysqli_error($db_con));
$langDetail = mysqli_fetch_assoc($langDetail);

$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

$rwgetRole = mysqli_fetch_assoc($chekUsr);

// echo $rwgetRole['dashboard_mydms']; die;
if ($rwgetRole['role_modi'] != '1') {
    header('Location: ../../index');
}
if (!isset($_POST['ID'], $_POST['token'])) {
    echo "Unauthorised access !";
    exit;
}
$id = preg_replace("/[^0-9 ]/", "", $_POST['ID']);
mysqli_set_charset($db_con, "utf8");
$role = mysqli_query($db_con, "select * from tbl_user_roles where role_id='$id'");
$rwRole = mysqli_fetch_assoc($role);
?>
<div class="row">
    <div class="form-group">
        <label for="privilege"><b><?php echo $lang['Profile_Name']; ?></b><span style="color:red;">*</span></label>
        <input type="text" name="roleName" required class="form-control translatetext respecialchar" id="groupName" value="<?php echo $rwRole['user_role']; ?>" required>
    </div>
</div>
<div class="row">
    <div class="form-group txt">
        <div class="form-group">
            <label for="privilege"><?php echo $lang['Select_Group']; ?><span style="color:red;">*</span></label>
            <select class="select3 select2-multiple" name="groups[]" multiple="multiple" multiple data-placeholder="<?php echo $lang['Select_Group']; ?>" parsley-trigger="change" id="group" required>
                <?php
                $group_permission = mysqli_query($db_con, "SELECT group_id,user_ids,roleids FROM `tbl_bridge_grp_to_um` ");
                while ($allGroupRow = mysqli_fetch_array($group_permission)) {
                    $user_ids = explode(',', $allGroupRow['user_ids']);
                    $roleids = explode(',', $allGroupRow['roleids']);
                    if (in_array($_SESSION['cdes_user_id'], $user_ids)) {
                        $grp = mysqli_query($db_con, "select group_id,group_name from tbl_group_master WHERE group_id='$allGroupRow[group_id]' order by group_name asc") or die('error' . mysqli_error($db_con));
                        $rwGrp = mysqli_fetch_assoc($grp);
                        if (in_array($id, $roleids)) {
                            echo '<option value="' . $rwGrp['group_id'] . '" selected>' . $rwGrp['group_name'] . '</option>';
                        } else {
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
            <td>
                <div class="checkbox checkbox-success">
                    <input type="checkbox" id="selectall" name="selectall" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass col_0">
                    <label for="myCheck"><strong><?php echo $lang['Select_all']; ?></strong></label>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="form-group"></div>
            </td>
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
                            <input id="myCheck1" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_0 col_0" name="mydms" value="1" <?php
                                                                                                                                                                                    if ($rwRole['dashboard_mydms'] == '1') {
                                                                                                                                                                                        echo 'checked';
                                                                                                                                                                                    }
                                                                                                                                                                                    ?>>
                            <label for="myCheck"><?php echo $lang['MY_DMS']; ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['dashboard_mytask'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck2" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_0 col_1" name="mytsk" value="1" <?php
                                                                                                                                                                                    if ($rwRole['dashboard_mytask'] == '1') {
                                                                                                                                                                                        echo 'checked';
                                                                                                                                                                                    }
                                                                                                                                                                                    ?>>
                            <label for="myCheck"><?php echo $lang['My_tasks']; ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['dashboard_edit_profile'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck3" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_0 col_2" name="dashEditPro" value="1" <?php
                                                                                                                                                                                            if ($rwRole['dashboard_edit_profile'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['EDIT_PROFILE']; ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['dashboard_query'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck4" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_0 col_3" name="dashQury" value="1" <?php
                                                                                                                                                                                        if ($rwRole['dashboard_query'] == '1') {
                                                                                                                                                                                            echo 'checked';
                                                                                                                                                                                        }
                                                                                                                                                                                        ?>>
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
                            <input id="myCheck47" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_0 col_4" name="num_folders" value="1" <?php
                                                                                                                                                                                            if ($rwRole['num_of_folder'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['NO_OF_FOLDER']; ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['num_of_file'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck48" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_0 col_5" name="num_files" value="1" <?php
                                                                                                                                                                                        if ($rwRole['num_of_file'] == '1') {
                                                                                                                                                                                            echo 'checked';
                                                                                                                                                                                        }
                                                                                                                                                                                        ?>>
                            <label for="myCheck"><?php echo $lang['NO_OF_FILE']; ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['memory_used'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck49" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_0 col_6" name="memory_use" value="1" <?php
                                                                                                                                                                                            if ($rwRole['memory_used'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['MEMORY_USED']; ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['status_wf'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck49" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_0 col_7" name="status" value="1" <?php
                                                                                                                                                                                        if ($rwRole['status_wf'] == '1') {
                                                                                                                                                                                            echo 'checked';
                                                                                                                                                                                        }
                                                                                                                                                                                        ?>>
                            <label for="myCheck"><?php echo $lang['wf_status']; ?></label>
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
        <?php if ($rwgetRole['priority_wf'] == '1' || $rwgetRole['calendar_wf'] == '1' || $rwgetRole['user_graph'] == '1' || $rwgetRole['mis_report'] == '1') { ?>
            <tr>
                <?php if ($rwgetRole['priority_wf'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck49" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_0 col_8" name="priority" value="1" <?php
                                                                                                                                                                                        if ($rwRole['priority_wf'] == '1') {
                                                                                                                                                                                            echo 'checked';
                                                                                                                                                                                        }
                                                                                                                                                                                        ?>>
                            <label for="myCheck"><?php echo $lang['wf_priority']; ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['calendar_wf'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck49" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_0 col_9" name="calendar" value="1" <?php
                                                                                                                                                                                        if ($rwRole['calendar_wf'] == '1') {
                                                                                                                                                                                            echo 'checked';
                                                                                                                                                                                        }
                                                                                                                                                                                        ?>>
                            <label for="myCheck"><?php echo $lang['calendar']; ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['user_graph'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck150" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_0 col_10" name="user_graph" value="1" <?php
                                                                                                                                                                                            if ($rwRole['user_graph'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['user_graph']; ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['mis_report'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck150" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_0 col_11" name="mis_report" value="1" <?php
                                                                                                                                                                                            if ($rwRole['mis_report'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck">Mis Report</label>
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
                            <input id="myCheck5" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_1 col_0" name="usrAdd" value="1" <?php
                                                                                                                                                                                    if ($rwRole['create_user'] == '1') {
                                                                                                                                                                                        echo 'checked';
                                                                                                                                                                                    }
                                                                                                                                                                                    ?>>
                            <label for="myCheck"><?php echo $lang['Add'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['modify_userlist'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck6" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_1 col_1" name="usrmodi" value="1" <?php
                                                                                                                                                                                        if ($rwRole['modify_userlist'] == '1') {
                                                                                                                                                                                            echo 'checked';
                                                                                                                                                                                        }
                                                                                                                                                                                        ?>>
                            <label for="myCheck"><?php echo $lang['Edit'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['delete_userlist'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck7" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_1 col_2" name="usrDelete" value="1" <?php
                                                                                                                                                                                        if ($rwRole['delete_userlist'] == '1') {
                                                                                                                                                                                            echo 'checked';
                                                                                                                                                                                        }
                                                                                                                                                                                        ?>>
                            <label for="myCheck"><?php echo $lang['Delete'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['view_userlist'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck8" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_1 col_3" name="usrView" value="1" <?php
                                                                                                                                                                                        if ($rwRole['view_userlist'] == '1') {
                                                                                                                                                                                            echo 'checked';
                                                                                                                                                                                        }
                                                                                                                                                                                        ?>>
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
                            <input id="myCheck189" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_1 col_3" name="export_user" value="1" <?php
                                                                                                                                                                                            if ($rwRole['export_user'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['Export_Users'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['import_user'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck190" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_1 col_3" name="import_user" value="1" <?php
                                                                                                                                                                                            if ($rwRole['import_user'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['Import_Users'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['user_activate_deactivate'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck191" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_1 col_3" name="user_activate_deactivate" value="1" <?php
                                                                                                                                                                                                        if ($rwRole['user_activate_deactivate'] == '1') {
                                                                                                                                                                                                            echo 'checked';
                                                                                                                                                                                                        }
                                                                                                                                                                                                        ?>>
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

        <?php if (($rwgetRole['storage_auth_plcy'] == '1') || $rwgetRole['online_user'] == '1' || $rwgetRole['email_config'] == '1') { ?>
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
                            <input id="myCheck9" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_2 col_0" name="strgAuth" value="1" <?php
                                                                                                                                                                                        if ($rwRole['storage_auth_plcy'] == '1') {
                                                                                                                                                                                            echo 'checked';
                                                                                                                                                                                        }
                                                                                                                                                                                        ?>>
                            <label for="myCheck"><?php echo $lang['Storage_Auth'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['online_user'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck91" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_2 col_1" name="onlineUser" value="1" <?php
                                                                                                                                                                                            if ($rwRole['online_user'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['Online_User'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['email_config'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck54" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_15 col_0" name="mailconfigYes" value="1" <?php
                                                                                                                                                                                                if ($rwRole['email_config'] == '1') {
                                                                                                                                                                                                    echo 'checked';
                                                                                                                                                                                                }
                                                                                                                                                                                                ?>>
                            <label for="myCheck"><?php echo $lang['Email_Confg'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['mail_lists'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck921" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_2 col_3" name="mailList" value="1" <?php
                                                                                                                                                                                        if ($rwRole['mail_lists'] == '1') {
                                                                                                                                                                                            echo 'checked';
                                                                                                                                                                                        }
                                                                                                                                                                                        ?>>
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
            <tr>
                <?php if ($rwgetRole['add_group'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck10" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_3 col_0" name="grpAdd" value="1" <?php
                                                                                                                                                                                        if ($rwRole['add_group'] == '1') {
                                                                                                                                                                                            echo 'checked';
                                                                                                                                                                                        }
                                                                                                                                                                                        ?>>
                            <label for="myCheck"><?php echo $lang['Add'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['delete_group'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck11" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_3 col_1" name="grpDelete" value="1" <?php
                                                                                                                                                                                        if ($rwRole['delete_group'] == '1') {
                                                                                                                                                                                            echo 'checked';
                                                                                                                                                                                        }
                                                                                                                                                                                        ?>>
                            <label for="myCheck"><?php echo $lang['Delete'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['modify_group'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck12" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_3 col_2" name="grpModi" value="1" <?php
                                                                                                                                                                                        if ($rwRole['modify_group'] == '1') {
                                                                                                                                                                                            echo 'checked';
                                                                                                                                                                                        }
                                                                                                                                                                                        ?>>
                            <label for="myCheck"><?php echo $lang['Edit'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['view_group_list'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck13" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_3 col_3" name="grpView" value="1" <?php
                                                                                                                                                                                        if ($rwRole['view_group_list'] == '1') {
                                                                                                                                                                                            echo 'checked';
                                                                                                                                                                                        }
                                                                                                                                                                                        ?>>
                            <label for="myCheck"><?php echo $lang['view'] ?></label>
                        </div>
                    </td>
                <?php } ?>
            </tr>
            <tr>
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
                            <input id="myCheck14" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_4 col_0" name="usrRoleAdd" value="1" <?php
                                                                                                                                                                                            if ($rwRole['role_add'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['Add'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['role_delete'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck15" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_4 col_1" name="usrRoleDel" value="1" <?php
                                                                                                                                                                                            if ($rwRole['role_delete'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['Delete'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['role_modi'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck16" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_4 col_2" name="usrRoleModi" value="1" <?php
                                                                                                                                                                                            if ($rwRole['role_modi'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['Edit'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['role_view'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck17" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_4 col_3" name="usrRoleView" value="1" <?php
                                                                                                                                                                                            if ($rwRole['role_view'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['view'] ?></label>
                        </div>
                    </td>
                <?php } ?>
            <tr>
                <td>
                    <div class="form-group"></div>
                </td>
            </tr>
        <?php } ?>
        <?php if ($rwgetRole['bulk_upload'] == '1' || $rwgetRole['folder_upload'] || $rwgetRole['upload_files'] == '1') { ?>
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
                            <input id="myCheck18" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_5 col_0" name="bulkUpld" value="1" <?php
                                                                                                                                                                                        if ($rwRole['bulk_upload'] == '1') {
                                                                                                                                                                                            echo 'checked';
                                                                                                                                                                                        }
                                                                                                                                                                                        ?>>
                            <label for="myCheck"><?php echo $lang['Bulk_Upload'] ?></label>
                        </div>
                    </td>
                <?php } ?>

                <?php if ($rwgetRole['folder_upload'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck1s8" type="checkbox" class="checkBoxClass row_5 col_1" name="bulkUpldfolder" value="1" <?php
                                                                                                                                        if ($rwRole['folder_upload'] == '1') {
                                                                                                                                            echo 'checked';
                                                                                                                                        }
                                                                                                                                        ?>>
                            <label for="myCheck"><?php echo $lang['Upload_multi_folder']; ?></label>
                        </div>
                    </td>
                <?php }
                if ($rwgetRole['upload_files'] == '1') { ?>
                    <td colspan="2">
                        <div class="checkbox checkbox-success">
                            <input id="myChecdk1s8" type="checkbox" class="checkBoxClass row_5 col_2" name="bulkUpldfiles" value="1" <?php
                                                                                                                                        if ($rwRole['folder_upload'] == '1') {
                                                                                                                                            echo 'checked';
                                                                                                                                        }
                                                                                                                                        ?>>
                            <label for="myCheck"><?php echo $lang['Upload_multi_files']; ?></label>
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
        <?php if ($rwgetRole['add_metadata'] == '1' || $rwgetRole['view_metadata'] == '1' || $rwgetRole['assign_metadata'] == '1' || $rwgetRole['edit_metadata'] == '1' || $rwgetRole['delete_metadata'] == '1') { ?>
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
        <?php if ($rwgetRole['add_metadata'] == '1' || $rwgetRole['view_metadata'] == '1' || $rwgetRole['edit_metadata'] == '1' || $rwgetRole['assign_metadata'] == '1') { ?>
            <tr>
                <?php if ($rwgetRole['add_metadata'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck19" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_6 col_0" name="metaDataAdd" value="1" <?php
                                                                                                                                                                                            if ($rwRole['add_metadata'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['Add'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['view_metadata'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck20" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_6 col_1" name="meteDataView" value="1" <?php
                                                                                                                                                                                            if ($rwRole['view_metadata'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['view'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['assign_metadata'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck21" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_6 col_2" name="meteDataAsin" value="1" <?php
                                                                                                                                                                                            if ($rwRole['assign_metadata'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['Assign'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['edit_metadata'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck21" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_6 col_3" name="meteDataedit" value="1" <?php
                                                                                                                                                                                            if ($rwRole['edit_metadata'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
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
        <?php if ($rwgetRole['delete_metadata'] == '1') { ?>
            <tr>
                <?php if ($rwgetRole['delete_metadata'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck21" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_6 col_4" name="meteDatadelete" value="1" <?php
                                                                                                                                                                                                if ($rwRole['delete_metadata'] == '1') {
                                                                                                                                                                                                    echo 'checked';
                                                                                                                                                                                                }
                                                                                                                                                                                                ?>>
                            <label for="myCheck"><?php echo $lang['delete_metadata'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['save_query'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck21" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_6 col_5" name="savequery" value="1" <?php
                                                                                                                                                                                        if ($rwRole['save_query'] == '1') {
                                                                                                                                                                                            echo 'checked';
                                                                                                                                                                                        }
                                                                                                                                                                                        ?>>
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
        <?php if ($rwgetRole['create_storage'] == '1' || $rwgetRole['create_child_storage'] == '1' || $rwgetRole['upload_doc_storage'] == '1' || $rwgetRole['modify_storage_level'] == '1') { ?>
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
            <tr>
                <?php if ($rwgetRole['create_storage'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck39" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_7 col_0" name="strgCreate" value="1" <?php
                                                                                                                                                                                            if ($rwRole['create_storage'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['Crt_Strg'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['create_child_storage'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck22" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_7 col_0" name="strgAddChild" value="1" <?php
                                                                                                                                                                                            if ($rwRole['create_child_storage'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['Add_Nw_Chld'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['upload_doc_storage'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck23" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_7 col_1" name="strgUpldDoc" value="1" <?php
                                                                                                                                                                                            if ($rwRole['upload_doc_storage'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['Upload_Documents'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['modify_storage_level'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck24" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_7 col_2" name="strgModi" value="1" <?php
                                                                                                                                                                                        if ($rwRole['modify_storage_level'] == '1') {
                                                                                                                                                                                            echo 'checked';
                                                                                                                                                                                        }
                                                                                                                                                                                        ?>>
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
                            <input id="myCheck25" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_7 col_3" name="strgDelete" value="1" <?php
                                                                                                                                                                                            if ($rwRole['delete_storage_level'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['Delete'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['move_storage_level'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck26" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_7 col_4" name="strgMove" value="1" <?php
                                                                                                                                                                                        if ($rwRole['move_storage_level'] == '1') {
                                                                                                                                                                                            echo 'checked';
                                                                                                                                                                                        }
                                                                                                                                                                                        ?>>
                            <label for="myCheck"><?php echo $lang['move'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['copy_storage_level'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck27" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_7 col_5" name="strgCopy" value="1" <?php
                                                                                                                                                                                        if ($rwRole['copy_storage_level'] == '1') {
                                                                                                                                                                                            echo 'checked';
                                                                                                                                                                                        }
                                                                                                                                                                                        ?>>
                            <label for="myCheck"><?php echo $lang['Copy'] ?></label>
                        </div>
                    </td>
                <?php } ?>

                <?php if ($rwgetRole['export_user_perm'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck24" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_7 col_7" name="export_user_perm" value="1" <?php
                                                                                                                                                                                                if ($rwRole['export_user_perm'] == '1') {
                                                                                                                                                                                                    echo 'checked';
                                                                                                                                                                                                }
                                                                                                                                                                                                ?>>
                            <label for="myCheck"><?php echo $lang['Export_Users_perm'] ?></label>
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
        <?php if ($rwgetRole['view_user_audit'] == '1' || $rwgetRole['view_storage_audit'] == '1' || $rwgetRole['workflow_audit'] == '1' || $rwgetRole['upload_logs'] == '1') { ?>
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
            <tr>
                <?php if ($rwgetRole['view_user_audit'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck28" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_8 col_0" name="auditTrlUsr" value="1" <?php
                                                                                                                                                                                            if ($rwRole['view_user_audit'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['User_Wise'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['view_storage_audit'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheckx29" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_8 col_1" name="auditTrlStrg" value="1" <?php
                                                                                                                                                                                            if ($rwRole['view_storage_audit'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['Storage_Wise'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['workflow_audit'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myChecdk291" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_8 col_2" name="auditwf" value="1" <?php
                                                                                                                                                                                        if ($rwRole['workflow_audit'] == '1') {
                                                                                                                                                                                            echo 'checked';
                                                                                                                                                                                        }
                                                                                                                                                                                        ?>>
                            <label for="myCheck"><?php echo $lang['WorkFlow_Wise'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['delete_user_log'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheckf291" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_8 col_3" name="delusrlog" value="1" <?php
                                                                                                                                                                                            if ($rwRole['delete_user_log'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
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
                            <input id="myCh1eck291" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_8 col_4" name="delstrglog" value="1" <?php
                                                                                                                                                                                            if ($rwRole['delete_storage_log'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['del_strg_log'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['delete_wf_log'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myChfeck291" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_8 col_5" name="delwflog" value="1" <?php
                                                                                                                                                                                            if ($rwRole['delete_wf_log'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['del_wf_log'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['upload_logs'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="uplogs" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_8 col_6" name="uploadlog" value="1" <?php
                                                                                                                                                                                        if ($rwRole['upload_logs'] == '1') {
                                                                                                                                                                                            echo 'checked';
                                                                                                                                                                                        }
                                                                                                                                                                                        ?>>
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
                            <input id="myCheck30" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_9 col_0" name="wrkflwCreate" value="1" <?php
                                                                                                                                                                                            if ($rwRole['create_workflow'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['Create_Work_Flow'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['view_workflow_list'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck31" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_9 col_1" name="wrkflwView" value="1" <?php
                                                                                                                                                                                            if ($rwRole['view_workflow_list'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['view'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['edit_workflow'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck32" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_9 col_2" name="wrkflwEdit" value="1" <?php
                                                                                                                                                                                            if ($rwRole['edit_workflow'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['Edit'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['delete_workflow'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck33" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_9 col_3" name="wrkflwDel" value="1" <?php
                                                                                                                                                                                        if ($rwRole['delete_workflow'] == '1') {
                                                                                                                                                                                            echo 'checked';
                                                                                                                                                                                        }
                                                                                                                                                                                        ?>>
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
        <?php if ($rwgetRole['workflow_step'] == '1' || $rwgetRole['workflow_initiate_file'] == '1' || $rwgetRole['initiate_file'] == '1') { ?>
            <tr>
                <?php if ($rwgetRole['workflow_step'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck34" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_9 col_4" name="wrkflwStep" value="1" <?php
                                                                                                                                                                                            if ($rwRole['workflow_step'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['Workflow_Step'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['workflow_initiate_file'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck35" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_9 col_5" name="wrkflwIniFile" value="1" <?php
                                                                                                                                                                                            if ($rwRole['workflow_initiate_file'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['Initiate_WorkFlow'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['initiate_file'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck35" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_9 col_6" name="InitiateFile" value="1" <?php
                                                                                                                                                                                            if ($rwRole['initiate_file'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
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
        <?php if ($rwgetRole['involve_workflow'] == '1' || $rwgetRole['run_workflow'] == '1') { ?>
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
                            <input id="myCheck341" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_18 col_0" name="wrkflwInvl" value="1" <?php
                                                                                                                                                                                            if ($rwRole['involve_workflow'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['Involved_WorkFlow'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['run_workflow'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck351" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_18 col_1" name="wrkflwRun" value="1" <?php
                                                                                                                                                                                            if ($rwRole['run_workflow'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['running_wf'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['view_report'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck351" type="checkbox" class="checkBoxClass row_18 col_5" name="viewreport" value="1" <?php echo ($rwRole['view_report'] == '1') ? "checked" : ""; ?>>
                            <label for="myCheck"><?php echo $lang['view_report']; ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['add_report'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck351" type="checkbox" class="checkBoxClass row_18 col_2" name="addreport" value="1" <?php echo ($rwRole['add_report'] == '1') ? "checked" : ""; ?>>
                            <label for="myCheck"><?php echo $lang['add_report']; ?></label>
                        </div>
                    </td>
                <?php } ?>


            </tr>

            <?php if ($rwgetRole['update_report'] == '1' || $rwgetRole['delete_report'] == '1') { ?>
                <tr>
                    <td>
                        <div class="form-group"></div>
                    </td>
                </tr>
                <tr>
                    <?php if ($rwgetRole['update_report'] == '1') { ?>
                        <td>
                            <div class="checkbox checkbox-success">
                                <input id="myCheck351" type="checkbox" class="checkBoxClass row_18 col_3" name="editreport" value="1" <?php echo ($rwRole['update_report'] == '1') ? "checked" : ""; ?>>
                                <label for="myCheck"><?php echo $lang['edit_report']; ?></label>
                            </div>
                        </td>
                    <?php } ?>

                    <?php if ($rwgetRole['delete_report'] == '1') { ?>
                        <td colspan="4">
                            <div class="checkbox checkbox-success">
                                <input id="myCheck351" type="checkbox" class="checkBoxClass row_18 col_4" name="deletereport" value="1" <?php echo ($rwRole['delete_report'] == '1') ? "checked" : ""; ?>>
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
                            <input id="myCheck36" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_10 col_0" name="tsktrk" value="1" <?php
                                                                                                                                                                                        if ($rwRole['workflow_task_track'] == '1') {
                                                                                                                                                                                            echo 'checked';
                                                                                                                                                                                        }
                                                                                                                                                                                        ?>>
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
        <?php if ($rwgetRole['metadata_search'] == '1' || $rwgetRole['metadata_quick_search'] == '1' || $rwgetRole['advance_search'] == '1' || $rwgetRole['view_ocr_list'] == '1') { ?>
            <tr>
                <td>
                    <div class="checkbox checkbox-success txt">
                        <input type="checkbox" id="select_row_11" />
                        <label for="myCheck"><?php echo $lang['MetaData_Search'] ?></label>
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
                            <input id="myCheck37" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_11 col_0" name="metadataSerach" value="1" <?php
                                                                                                                                                                                                if ($rwRole['metadata_search'] == '1') {
                                                                                                                                                                                                    echo 'checked';
                                                                                                                                                                                                }
                                                                                                                                                                                                ?>>
                            <label for="myCheck"><?php echo $lang['MetaData_Search'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['metadata_quick_search'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck38" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_11 col_1" name="metaDataQsearch" value="1" <?php
                                                                                                                                                                                                if ($rwRole['metadata_quick_search'] == '1') {
                                                                                                                                                                                                    echo 'checked';
                                                                                                                                                                                                }
                                                                                                                                                                                                ?>>
                            <label for="myCheck"><?php echo $lang['quich_search'] ?></label>
                        </div>
                    </td>
                <?php }
                if ($rwgetRole['advance_search'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myChecka38" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_11 col_2" name="advancesearch" value="1" <?= (($rwRole['advance_search'] == '1') ? "checked" : ""); ?>>
                            <label for="myCheck"><?php echo $lang['advance_keyword_search'] ?></label>
                        </div>
                    </td>

                <?php }
                if ($rwgetRole['view_ocr_list'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck3d8" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_11 col_3" name="viewocrlist" value="1" <?= (($rwRole['view_ocr_list'] == '1') ? "checked" : ""); ?>>
                            <label for="myCheck"><?php echo $lang['ocr_pending_list'] ?></label>
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
                            <input id="myCheck41" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_0" name="fileEdit" value="1" <?php
                                                                                                                                                                                        if ($rwRole['file_edit'] == '1') {
                                                                                                                                                                                            echo 'checked';
                                                                                                                                                                                        }
                                                                                                                                                                                        ?>>
                            <label for="myCheck"><?php echo $lang['View_MetaData_file'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['file_delete'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck42" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_1" name="fileDelete" value="1" <?php
                                                                                                                                                                                            if ($rwRole['file_delete'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['Delete'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['file_anot'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck43" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_2" name="fileAnot" value="1" <?php
                                                                                                                                                                                        if ($rwRole['file_anot'] == '1') {
                                                                                                                                                                                            echo 'checked';
                                                                                                                                                                                        }
                                                                                                                                                                                        ?>>
                            <label for="myCheck"><?php echo $lang['Annotation'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['file_anot_delete'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck45" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_3" name="fileAnotDelete" value="1" <?php
                                                                                                                                                                                                if ($rwRole['file_anot_delete'] == '1') {
                                                                                                                                                                                                    echo 'checked';
                                                                                                                                                                                                }
                                                                                                                                                                                                ?>>
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
                            <input id="myCheck44" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_4" name="fileComent" value="1" <?php
                                                                                                                                                                                            if ($rwRole['file_coment'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['Comment'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['tif_file'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck40" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_5" name="tiffile" value="1" <?php
                                                                                                                                                                                        if ($rwRole['tif_file'] == '1') {
                                                                                                                                                                                            echo 'checked';
                                                                                                                                                                                        }
                                                                                                                                                                                        ?>>
                            <label for="myCheck"><?php echo $lang['Tiff_File'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['pdf_file'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck46" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_6" name="pdffile" value="1" <?php
                                                                                                                                                                                        if ($rwRole['pdf_file'] == '1') {
                                                                                                                                                                                            echo 'checked';
                                                                                                                                                                                        }
                                                                                                                                                                                        ?>>
                            <label for="myCheck"><?php echo $lang['pdf_file'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['doc_file'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck47" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_7" name="docfile" value="1" <?php
                                                                                                                                                                                        if ($rwRole['doc_file'] == '1') {
                                                                                                                                                                                            echo 'checked';
                                                                                                                                                                                        }
                                                                                                                                                                                        ?>>
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
                            <input id="myCheck48" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_8" name="excelfile" value="1" <?php
                                                                                                                                                                                            if ($rwRole['excel_file'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['excel_file']; ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['audio_file'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck49" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_9" name="audiofile" value="1" <?php
                                                                                                                                                                                            if ($rwRole['audio_file'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['Audio_file']; ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['video_file'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck50" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_10" name="videofile" value="1" <?php
                                                                                                                                                                                            if ($rwRole['video_file'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['Video_file']; ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['image_file'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck51" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_11" name="imagefile" value="1" <?php
                                                                                                                                                                                            if ($rwRole['image_file'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
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
                            <input id="myCheck512" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_12" name="annotedpdf" value="1" <?php
                                                                                                                                                                                            if ($rwRole['pdf_annotation'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['Annotated_Pdf']; ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['file_version'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck513" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_13" name="fileversion" value="1" <?php
                                                                                                                                                                                                if ($rwRole['file_version'] == '1') {
                                                                                                                                                                                                    echo 'checked';
                                                                                                                                                                                                }
                                                                                                                                                                                                ?>>
                            <label for="myCheck"><?php echo $lang['View_File_Version']; ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['delete_version'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck514" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_14" name="delfilevrsn" value="1" <?php
                                                                                                                                                                                                if ($rwRole['delete_version'] == '1') {
                                                                                                                                                                                                    echo 'checked';
                                                                                                                                                                                                }
                                                                                                                                                                                                ?>>
                            <label for="myCheck"><?php echo $lang['Del_File_Version']; ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['update_file'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck515" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_15" name="updatefile" value="1" <?php
                                                                                                                                                                                            if ($rwRole['update_file'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
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
        <?php }
        if ($rwgetRole['export_csv'] == '1' || $rwgetRole['move_file'] == '1' || $rwgetRole['copy_file'] == '1' || $rwgetRole['share_file'] == '1') { ?>
            <tr>
                <?php if ($rwgetRole['export_csv'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck516" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_16" name="csv" value="1" <?php
                                                                                                                                                                                        if ($rwRole['export_csv'] == '1') {
                                                                                                                                                                                            echo 'checked';
                                                                                                                                                                                        }
                                                                                                                                                                                        ?>>
                            <label for="myCheck"><?php echo $lang['Export_Csv']; ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['move_file'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck517" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_17" name="movefile" value="1" <?php
                                                                                                                                                                                            if ($rwRole['move_file'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['Move_Files']; ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['copy_file'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck518" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_18" name="copyfile" value="1" <?php
                                                                                                                                                                                            if ($rwRole['copy_file'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['Copy_Files']; ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['share_file'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck519" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_19" name="sharefile" value="1" <?php
                                                                                                                                                                                            if ($rwRole['share_file'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
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
                            <input id="myCheck520" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_20" name="CheckinOut" value="1" <?php
                                                                                                                                                                                            if ($rwRole['checkin_checkout'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['Checkin_Checkout']; ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['bulk_download'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck521" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_21" name="bulkDwnld" value="1" <?php
                                                                                                                                                                                            if ($rwRole['bulk_download'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['Bulk_Download']; ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['xls_download'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck522" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_21" name="xlsdownload" value="1" <?php
                                                                                                                                                                                                if ($rwRole['xls_download'] == '1') {
                                                                                                                                                                                                    echo 'checked';
                                                                                                                                                                                                }
                                                                                                                                                                                                ?>>
                            <label for="myCheck"><?php echo $lang['Excel_Download']; ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['xls_print'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck523" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_21" name="xlsprint" value="1" <?php
                                                                                                                                                                                            if ($rwRole['xls_print'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
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
                            <input id="myCheck524" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_22" name="word_edit" value="1" <?php
                                                                                                                                                                                            if ($rwRole['word_edit'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['word_edit']; ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['view_psd'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck525" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_23" name="view_psd" value="1" <?php
                                                                                                                                                                                            if ($rwRole['view_psd'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['view_psd']; ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['view_cdr'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck526" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_24" name="view_cdr" value="1" <?php
                                                                                                                                                                                            if ($rwRole['view_cdr'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['view_cdr']; ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['delete_page'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck527" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_25" name="delete_page" value="1" <?php
                                                                                                                                                                                                if ($rwRole['delete_page'] == '1') {
                                                                                                                                                                                                    echo 'checked';
                                                                                                                                                                                                }
                                                                                                                                                                                                ?>>
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
        <?php if ($rwgetRole['mail_files'] == '1' || $rwgetRole['view_odt'] == '1' || $rwgetRole['view_rtf'] == '1' || $rwgetRole['file_review'] == '1' || $rwgetRole['share_folder'] == '1') { ?>
            <tr>
                <?php if ($rwgetRole['mail_files'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck528" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_26" name="mail_files" value="1" <?php
                                                                                                                                                                                            if ($rwRole['mail_files'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['mail_files']; ?></label>
                        </div>
                    </td>
                <?php } ?>


                <?php if ($rwgetRole['view_odt'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck528" <?php echo ($rwRole['view_odt'] == '1') ? "checked" : ""; ?> type="checkbox" class="checkBoxClass row_12 col_27" name="odtfile" value="1">

                            <label for="myCheck"><?php echo $lang['odt_file']; ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['view_rtf'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck528" type="checkbox" <?php echo ($rwRole['view_rtf'] == '1') ? "checked" : ""; ?> class="checkBoxClass row_12 col_28" name="rtffile" value="1">
                            <label for="myCheck"><?php echo $lang['rtf_file']; ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['file_review'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck528" type="checkbox" <?php echo ($rwRole['file_review'] == '1') ? "checked" : ""; ?> class="checkBoxClass row_12 col_29" name="filereview" value="1">
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


        <?php if ($rwgetRole['doc_expiry_time'] == '1' || $rwgetRole['lock_file'] == '1' || $rwgetRole['lock_folder'] == 1 || $rwgetRole['doc_weeding_out'] == '1' || $rwgetRole['doc_share_time'] == '1') { ?>
            <tr>
                <?php if ($rwgetRole['lock_folder'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck41" <?php echo ($rwRole['lock_folder'] == '1') ? "checked" : ""; ?> type="checkbox" class="checkBoxClass row_12 col_30" name="lock_folder" value="1">
                            <label for="myCheck"><?php echo $lang['lock_folder'] ?></label>
                        </div>
                    </td>

                <?php } ?>

                <?php if ($rwgetRole['lock_file'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck41" type="checkbox" <?php echo ($rwRole['lock_file'] == '1') ? "checked" : ""; ?> class="checkBoxClass row_12 col_34" name="lock_file" value="1">
                            <label for="myCheck"><?php echo $lang['lock_file'] ?></label>
                        </div>
                    </td>

                <?php } ?>

                <?php if ($rwgetRole['doc_weeding_out'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck41" <?php echo ($rwRole['doc_weeding_out'] == '1') ? "checked" : ""; ?> type="checkbox" class="checkBoxClass row_12 col_31" name="weedingouttime" value="1">
                            <label for="myCheck"><?php echo $lang['weed_out_time'] ?></label>
                        </div>
                    </td>

                <?php }
                if ($rwgetRole['doc_share_time'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck41" <?php echo ($rwRole['doc_share_time'] == '1') ? "checked" : ""; ?> type="checkbox" class="checkBoxClass row_12 col_32" name="docsharetime" value="1">
                            <label for="myCheck"><?php echo $lang['share_document_with_time'] ?></label>
                        </div>
                    </td>

                <?php } ?>


            </tr>
            <?php if ($rwgetRole['doc_expiry_time'] == '1' || $rwgetRole['view_ppt_pptx'] == '1' || $rwgetRole['view_csv'] == '1' || $rwgetRole['export_ocr'] == '1') { ?>

                <tr>
                    <td>
                        <div class="form-group"></div>
                    </td>
                </tr>

                <tr>
                    <?php if ($rwgetRole['doc_expiry_time'] == '1') { ?>
                        <td>
                            <div class="checkbox checkbox-success">
                                <input id="myCheck41" <?php echo ($rwRole['doc_expiry_time'] == '1') ? "checked" : ""; ?> type="checkbox" class="checkBoxClass row_12 col_33" name="expdocument" value="1">
                                <label for="myCheck"><?php echo $lang['expired_doc_list'] ?></label>
                            </div>
                        </td>
                    <?php } ?>

                    <?php if ($rwgetRole['view_ppt_pptx'] == '1') { ?>
                        <td>
                            <div class="checkbox checkbox-success">
                                <input id="myChecsak53" type="checkbox" class="checkBoxClass row_12 col_34" name="pptppx" value="1" <?php echo ($rwRole['view_ppt_pptx'] == '1') ? "checked" : ""; ?>>
                                <label for="myCheck"><?php echo $lang['ppt_file']; ?></label>
                            </div>
                        </td>
                    <?php } ?>

                    <?php if ($rwgetRole['view_csv'] == '1') { ?>
                        <td>
                            <div class="checkbox checkbox-success">
                                <input id="myChecsak53" type="checkbox" class="checkBoxClass row_12 col_35" name="view_csv" value="1" <?php echo ($rwRole['view_csv'] == '1') ? "checked" : ""; ?>>
                                <label for="myCheck"><?php echo $lang['csv_file']; ?></label>
                            </div>
                        </td>
                    <?php } ?>

                    <?php if ($rwgetRole['export_ocr'] == '1') { ?>
                        <td>
                            <div class="checkbox checkbox-success">
                                <input id="myChecsak53" type="checkbox" class="checkBoxClass row_12 col_36" name="export_ocr" value="1" <?php echo ($rwRole['export_ocr'] == '1') ? "checked" : ""; ?>>
                                <label for="myCheck"><?php echo $lang['exportocr']; ?></label>
                            </div>
                        </td>
                    <?php } ?>
                </tr>

                <?php if ($rwgetRole['share_folder'] == '1') { ?>
                    <tr>
                        <td>
                            <div class="form-group"></div>
                        </td>
                    </tr>
                    <tr>

                        <td>
                            <div class="checkbox checkbox-success">
                                <input id="myCheck61" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_12 col_37" name="share_folder" value="1" <?php echo ($rwRole['share_folder'] == '1') ? "checked" : ""; ?>>
                                <label for="myCheck"><?php echo $lang['share_folder'] ?></label>
                            </div>
                        </td>

                    </tr>
                <?php } ?>



            <?php } ?>

        <?php

        } ?>

        <?php if ($rwgetRole['pdf_print'] == '1' || $rwgetRole['pdf_download'] == '1' || $rwgetRole['splitpdf'] == '1') { ?>
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
                            <input id="myCheck52" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_14 col_1" name="pdfprint" value="1" <?php
                                                                                                                                                                                        if ($rwRole['pdf_print'] == '1') {
                                                                                                                                                                                            echo 'checked';
                                                                                                                                                                                        }
                                                                                                                                                                                        ?>>
                            <label for="myCheck"><?php echo $lang['Pdf_Print']; ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['pdf_download'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck53" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_14 col_2" name="pdfdownload" value="1" <?php
                                                                                                                                                                                            if ($rwRole['pdf_download'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['Pdf_Download']; ?></label>
                        </div>
                    </td>
                <?php } ?>

                <?php if ($rwgetRole['splitpdf'] == '1') { ?>
                    <td colspan="2">
                        <div class="checkbox checkbox-success">
                            <input id="myCheck53" type="checkbox" class="checkBoxClass row_12 col_34" name="splitpdf" value="1" <?php
                                                                                                                                if ($rwRole['splitpdf'] == '1') {
                                                                                                                                    echo 'checked';
                                                                                                                                }
                                                                                                                                ?>>
                            <label for="myCheck"><?php echo $lang['splitpdf']; ?></label>
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
        <!-- ankit 09 june -->
        <?php if ($rwgetRole['rename_document'] == '1' || $rwgetRole['Multi_rename_document'] == '1') { ?>
            <tr>
                <td>
                    <div class="form-group"></div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="checkbox checkbox-success txt">
                        <input type="checkbox" id="select_row_141" />
                        <label><?php echo $lang['For_Rename_Document']; ?></label>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="form-group"></div>
                </td>
            </tr>
            <tr>

                <!-- ankit 02 june -->
                <?php if ($rwgetRole['rename_document'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck534" <?php echo ($rwRole['rename_document'] == '1') ? "checked" : ""; ?> type="checkbox" class="checkBoxClass row_141 col_333" name="renmdocument" value="1">
                            <label for="myCheck">Rename Document</label>
                        </div>
                    </td>
                <?php } ?>
                <!-- ankit 02 june -->

                <!-- ankit 08 june -->
                <?php if ($rwgetRole['Multi_rename_document'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck535" <?php echo ($rwRole['Multi_rename_document'] == '1') ? "checked" : ""; ?> type="checkbox" class="checkBoxClass row_141 col_333" name="multirenmdocument" value="1">
                            <label for="myCheck">Rename multiple Document</label>
                        </div>
                    </td>
                <?php } ?>
                <!-- ankit 08 june -->


            </tr>
            <tr>
                <td>
                    <div class="form-group"></div>
                </td>
            </tr>
        <?php } ?>
        <!-- ankitend 09 june -->
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
                            <input id="myCheck541" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_15 col_0" name="viewfaq" value="1" <?php
                                                                                                                                                                                        if ($rwRole['view_faq'] == '1') {
                                                                                                                                                                                            echo 'checked';
                                                                                                                                                                                        }
                                                                                                                                                                                        ?>>
                            <label for="myCheck"><?php echo $lang['View_Faq']; ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['add_faq'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck54" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_15 col_1" name="addfaq" value="1" <?php
                                                                                                                                                                                        if ($rwRole['add_faq'] == '1') {
                                                                                                                                                                                            echo 'checked';
                                                                                                                                                                                        }
                                                                                                                                                                                        ?>>
                            <label for="myCheck"><?php echo $lang['Add_Faq']; ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['edit_faq'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck55" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_15 col_2" name="editfaq" value="1" <?php
                                                                                                                                                                                        if ($rwRole['edit_faq'] == '1') {
                                                                                                                                                                                            echo 'checked';
                                                                                                                                                                                        }
                                                                                                                                                                                        ?>>
                            <label for="myCheck"><?php echo $lang['Edit_Faq']; ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['del_faq'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck56" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_15 col_3" name="delfaq" value="1" <?php
                                                                                                                                                                                        if ($rwRole['del_faq'] == '1') {
                                                                                                                                                                                            echo 'checked';
                                                                                                                                                                                        }
                                                                                                                                                                                        ?>>
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
        <?php if ($rwgetRole['view_recycle_bin'] == '1' || $rwgetRole['restore_file'] == '1' || $rwgetRole['permanent_del'] == '1') { ?>
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
                            <input id="myCheck571" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_16 col_0" name="viewrecycle" value="1" <?php
                                                                                                                                                                                            if ($rwRole['view_recycle_bin'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['View_Recycle_Bin']; ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['restore_file'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck57" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_16 col_1" name="restore" value="1" <?php
                                                                                                                                                                                        if ($rwRole['restore_file'] == '1') {
                                                                                                                                                                                            echo 'checked';
                                                                                                                                                                                        }
                                                                                                                                                                                        ?>>
                            <label for="myCheck"><?php echo $lang['Restore_Files']; ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['permanent_del'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck58" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_16 col_2" name="permntDel" value="1" <?php
                                                                                                                                                                                            if ($rwRole['permanent_del'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['per_dlt']; ?></label>
                        </div>
                    </td>
                <?php } ?>

                <?php if ($rwgetRole['rename_file'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck58" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_16 col_3" name="renamefile" value="1" <?php
                                                                                                                                                                                            if ($rwRole['rename_file'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['rename_file']; ?></label>
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

        <?php if ($rwgetRole['view_recycle_storage'] == '1' || $rwgetRole['restore_storage'] == '1' || $rwgetRole['delete_storage'] == '1' || $rwgetRole['rename_storage'] == '1') { ?>

            <tr>
                <?php if ($rwgetRole['view_recycle_storage'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck571" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_16 col_4" name="view_recycle_storage" value="1" <?php
                                                                                                                                                                                                        if ($rwRole['view_recycle_storage'] == '1') {
                                                                                                                                                                                                            echo 'checked';
                                                                                                                                                                                                        }
                                                                                                                                                                                                        ?>>
                            <label for="myCheck"><?php echo $lang['view_recycle_storage']; ?></label>
                        </div>
                    </td>
                <?php } ?>

                <?php if ($rwgetRole['restore_storage'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck57" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_16 col_5" name="restore_storage" value="1" <?php
                                                                                                                                                                                                if ($rwRole['restore_storage'] == '1') {
                                                                                                                                                                                                    echo 'checked';
                                                                                                                                                                                                }
                                                                                                                                                                                                ?>>
                            <label for="myCheck"><?php echo $lang['restore_storage']; ?></label>
                        </div>
                    </td>
                <?php } ?>

                <?php if ($rwgetRole['delete_storage'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck58" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_16 col_6" name="delete_storage" value="1" <?php
                                                                                                                                                                                                if ($rwRole['delete_storage'] == '1') {
                                                                                                                                                                                                    echo 'checked';
                                                                                                                                                                                                }
                                                                                                                                                                                                ?>>
                            <label for="myCheck"><?php echo $lang['delete_storage']; ?></label>
                        </div>
                    </td>
                <?php } ?>

                <?php if ($rwgetRole['rename_storage'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck58" type="checkbox" class="checkBoxClass row_16 col_7" name="rename_storage" value="1" <?php
                                                                                                                                        if ($rwRole['rename_storage'] == '1') {
                                                                                                                                            echo 'checked';
                                                                                                                                        }
                                                                                                                                        ?>>
                            <label for="myCheck"><?php echo $lang['rename_storage']; ?></label>
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


        <?php if ($rwgetRole['shared_file'] == '1' || $rwgetRole['share_with_me'] == '1' || $rwgetRole['shared_folder'] == '1' || $rwgetRole['shared_folder_with_me'] == '1' || $rwgetRole['feedback_msg'] == '1') { ?>
            <tr>
                <td colspan="20">
                    <div class="checkbox checkbox-success txt">
                        <input type="checkbox" id="select_row_17" />
                        <label><?php echo $lang['Shared_nd_share_with_me']; ?> </label>
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
                            <input id="myCheck59" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_17 col_0" name="sharedFile" value="1" <?php
                                                                                                                                                                                            if ($rwRole['shared_file'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['View_shared_Files'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['share_with_me'] == '1') { ?>
                    <td colspan="">
                        <div class="checkbox checkbox-success">
                            <input id="myCheck60" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_17 col_1" name="shareWithme" value="1" <?php
                                                                                                                                                                                            if ($rwRole['share_with_me'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['View_Share_With_Me'] ?></label>
                        </div>
                    </td>
                <?php } ?>

                <?php if ($rwgetRole['shared_folder'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck60" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_17 col_2" name="shared_folder" value="1" <?= (($rwRole['shared_folder'] == '1') ? "checked" : ""); ?>>
                            <label for="myCheck"><?php echo $lang['shared_folder'] ?></label>
                        </div>
                    </td>
                <?php } ?>

                <?php if ($rwgetRole['shared_folder_with_me'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck60" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_17 col_3" name="shared_folder_with_me" value="1" <?= (($rwRole['shared_folder_with_me'] == '1') ? "checked" : ""); ?>>
                            <label for="myCheck"><?php echo $lang['share_folder_with_me'] ?></label>
                        </div>
                    </td>
                <?php } ?>
            </tr>
            <tr>
                <td>
                    <div class="form-group"></div>
                </td>
            </tr>
            <tr>
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
                        <label><?php echo $lang['log']; ?> </label>
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
                            <input id="myCheck161" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_19 col_0" name="wf_log" value="1" <?php
                                                                                                                                                                                        if ($rwRole['wf_log'] == '1') {
                                                                                                                                                                                            echo 'checked';
                                                                                                                                                                                        }
                                                                                                                                                                                        ?>>
                            <label for="myCheck"><?php echo $lang['activity_log'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['review_log'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck162" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_19 col_1" name="review_log" value="1" <?php
                                                                                                                                                                                            if ($rwRole['review_log'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
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
                            <input id="myCheck61" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_13 col_0" name="review_intray" value="1" <?php
                                                                                                                                                                                                if ($rwRole['review_intray'] == '1') {
                                                                                                                                                                                                    echo 'checked';
                                                                                                                                                                                                }
                                                                                                                                                                                                ?>>
                            <label for="myCheck"><?php echo $lang['reviewintray'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['review_track'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck62" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_13 col_1" name="review_track" value="1" <?php
                                                                                                                                                                                            if ($rwRole['review_track'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
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
                            <input id="myCheck63" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_23 col_0" name="todo_add" value="1" <?php
                                                                                                                                                                                        if ($rwRole['todo_add'] == '1') {
                                                                                                                                                                                            echo 'checked';
                                                                                                                                                                                        }
                                                                                                                                                                                        ?>>
                            <label for="myCheck"><?php echo $lang['Add'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['todo_edit'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck64" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_23 col_1" name="todo_edit" value="1" <?php
                                                                                                                                                                                            if ($rwRole['todo_edit'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['Edit'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['todo_archive'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck65" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_23 col_2" name="todo_archive" value="1" <?php
                                                                                                                                                                                            if ($rwRole['todo_archive'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['archive'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['todo_view'] == '1') { ?>
                    <td>

                        <div class="checkbox checkbox-success">
                            <input id="myCheck66" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_23 col_3" name="todo_view" value="1" <?php
                                                                                                                                                                                            if ($rwRole['todo_view'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
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
                            <input id="myCheck67" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_22 col_0" name="appoint_add" value="1" <?php
                                                                                                                                                                                            if ($rwRole['appoint_add'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['Add'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['appoint_edit'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck68" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_22 col_1" name="appoint_edit" value="1" <?php
                                                                                                                                                                                            if ($rwRole['appoint_edit'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?php echo $lang['Edit'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['appoint_archive'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck69" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_22 col_2" name="appoint_archive" value="1" <?php
                                                                                                                                                                                                if ($rwRole['appoint_archive'] == '1') {
                                                                                                                                                                                                    echo 'checked';
                                                                                                                                                                                                }
                                                                                                                                                                                                ?>>
                            <label for="myCheck"><?php echo $lang['archive'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['appoint_view'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck70" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_22 col_3" name="appoint_view" value="1" <?php
                                                                                                                                                                                            if ($rwRole['appoint_view'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
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
            <tr>
                <?php if ($rwgetRole['hindi'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck68" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_21 col_0" name="hindi" value="1" <?php
                                                                                                                                                                                        if ($rwRole['hindi'] == '1') {
                                                                                                                                                                                            echo 'checked';
                                                                                                                                                                                        }
                                                                                                                                                                                        ?>>
                            <label for="myCheck"><?php echo $lang['Hindi'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['english'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck69" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_21 col_1" name="english" value="1" <?php
                                                                                                                                                                                        if ($rwRole['english'] == '1') {
                                                                                                                                                                                            echo 'checked';
                                                                                                                                                                                        }
                                                                                                                                                                                        ?>>
                            <label for="myCheck"><?php echo $lang['English'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['app_default'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck67" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_21 col_2" name="appd" value="1" <?php
                                                                                                                                                                                    if ($rwRole['app_default'] == '1') {
                                                                                                                                                                                        echo 'checked';
                                                                                                                                                                                    }
                                                                                                                                                                                    ?>>
                            <label for="myCheck"><?php echo $lang['App_default'] ?></label>
                        </div>
                    </td>
                <?php } ?>
                <?php if ($rwgetRole['customize_label'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck67" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_21 col_3" name="clabel" value="1" <?php
                                                                                                                                                                                        if ($rwRole['customize_label'] == '1') {
                                                                                                                                                                                            echo 'checked';
                                                                                                                                                                                        }
                                                                                                                                                                                        ?>>
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
        <tr>
            <td colspan="20">
                <?php if ($rwgetRole['add_holiday'] == '1' || $rwgetRole['edit_holiday'] == '1' || $rwgetRole['view_holiday'] == '1' || $rwgetRole['delete_holiday'] == '1' || $rwgetRole['holiday_calender'] == '1') { ?>
                    <div class="checkbox checkbox-success txt">
                        <input type="checkbox" id="select_row_20" />
                        <label><?= $lang['holiday_manager'] ?> </label>
                    </div>
                <?php } ?>
            </td>
        </tr>
        <tr>
            <td>
                <div class="form-group"></div>
            </td>
        </tr>
        <?php if ($rwgetRole['add_holiday'] == '1' || $rwgetRole['edit_holiday'] == '1' || $rwgetRole['view_holiday'] == '1' || $rwgetRole['delete_holiday'] == '1' || $rwgetRole['holiday_calender'] == '1') { ?>
            <tr>
                <td>
                    <?php if ($rwgetRole['add_holiday'] == '1') { ?>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck159" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_20 col_0" name="addholiday" value="1" <?php
                                                                                                                                                                                            if ($rwRole['add_holiday'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?= $lang['add_holiday'] ?></label>
                        </div>
                    <?php } ?>
                </td>
                <td>
                    <?php if ($rwgetRole['edit_holiday'] == '1') { ?>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck600" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_20 col_1" name="editholiday" value="1" <?php
                                                                                                                                                                                            if ($rwRole['edit_holiday'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?= $lang['edit_holiday'] ?></label>
                        </div>
                    <?php } ?>
                </td>
                <td>
                    <?php if ($rwgetRole['view_holiday'] == '1') { ?>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck610" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_20 col_2" name="viewholiday" value="1" <?php
                                                                                                                                                                                            if ($rwRole['view_holiday'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?= $lang['view_holiday'] ?></label>
                        </div>
                    <?php } ?>
                </td>
                <td>
                    <?php if ($rwgetRole['delete_holiday'] == '1') { ?>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck601u" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_20 col_3" name="delholiday" value="1" <?php
                                                                                                                                                                                            if ($rwRole['delete_holiday'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
                            <label for="myCheck"><?= $lang['delete_holiday'] ?></label>
                        </div>
                    <?php } ?>
                </td>

            </tr>
            <?php if ($rwgetRole['holiday_calender'] == '1') { ?>
                <tr>
                    <td>
                        <div class="form-group"></div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <?php if ($rwgetRole['holiday_calender'] == '1') { ?>
                            <div class="checkbox checkbox-success">
                                <input id="myCheck601k" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_20 col_4" name="holidaycal" value="1" <?= (($rwRole['holiday_calender'] == '1') ? "checked" : ""); ?>>
                                <label for="myCheck"><?= $lang['holiday_view'] ?></label>
                            </div>
                        <?php } ?>
                    </td>
                </tr>
        <?php }
        } ?>

        <?php if ($rwgetRole['password_policy'] == '1' || $rwgetRole['default_lang_setting'] == '1' || $rwgetRole['doc_exp_setting'] == '1' || $rwgetRole['doc_retention_setting'] == '1' || $rwgetRole['doc_share_setting'] == '1' || $rwgetRole['login_otp'] == '1' || $rwgetRole['login_captcha'] == '1') { ?>
            <tr>
                <td>
                    <div class="form-group"></div>
                </td>
            </tr>

            <tr>
                <td colspan="4">
                    <div class="checkbox checkbox-primary txt">
                        <input type="checkbox" id="select_row_24" />
                        <label><?= $lang['Administrative_tool'] . ' ' . $lang['and'] . ' ' . $lang['set_default_lang'] ?> </label>
                    </div>
                </td>
            </tr>

        <?php } ?>
        <?php if ($rwgetRole['password_policy'] == '1' || $rwgetRole['default_lang_setting'] == '1' || $rwgetRole['doc_exp_setting'] == '1' || $rwgetRole['doc_retention_setting'] == '1' || $rwgetRole['doc_share_setting'] == '1') { ?>
            <tr>
                <td>
                    <div class="form-group"></div>
                </td>
            </tr>
            <tr>
                <?php if ($rwgetRole['password_policy'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myChecdk159" type="checkbox" class="checkBoxClass row_24 col_0" name="passpolicy" value="1" <?= (($rwRole['password_policy'] == '1') ? "checked" : ""); ?>>
                            <label for="myCheck"><?= $lang['Set_Password_Policy'] ?></label>
                        </div>
                    </td>
                <?php }
                if ($rwgetRole['default_lang_setting'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck159" type="checkbox" class="checkBoxClass row_24 col_1" name="langsetting" value="1" <?= (($rwRole['default_lang_setting'] == '1') ? "checked" : ""); ?>>
                            <label for="myCheck"><?php echo $lang['set_default_lang']; ?></label>
                        </div>
                    </td>

                <?php }
                if ($rwgetRole['doc_exp_setting'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myChecdk159" type="checkbox" class="checkBoxClass row_24 col_2" name="docexpsetting" value="1" <?= (($rwRole['doc_exp_setting'] == '1') ? "checked" : ""); ?>>
                            <label for="myCheck"><?= $lang['expiry_document']; ?></label>
                        </div>
                    </td>

                <?php }
                if ($rwgetRole['doc_retention_setting'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="msyCheck159" type="checkbox" class="checkBoxClass row_24 col_3" name="docretention" value="1" <?= (($rwRole['doc_retention_setting'] == '1') ? "checked" : ""); ?>>
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
                                <input id="myChheck159" type="checkbox" class="checkBoxClass row_24 col_4" name="docsharesetting" value="1" <?= (($rwRole['doc_share_setting'] == '1') ? "checked" : ""); ?>>
                                <label for="myCheck"><?= $lang['Share_docs_with_time']; ?></label>
                            </div>
                        </td>
                    <?php } ?>
                    <?php if ($rwgetRole['login_otp'] == '1') { ?>
                        <td>
                            <div class="checkbox checkbox-success">
                                <input id="myChfeck15d9" type="checkbox" class="checkBoxClass row_24 col_6" name="emailotp" value="1" <?= (($rwRole['login_otp'] == '1') ? "checked" : ""); ?>>
                                <label for="myCheck"><?= $lang['login_with_otp']; ?></label>
                            </div>
                        </td>
                    <?php } ?>
                    <?php if ($rwgetRole['login_captcha'] == '1') { ?>
                        <td>
                            <div class="checkbox checkbox-success">
                                <input id="myChfeck15d9" type="checkbox" class="checkBoxClass row_24 col_7" name="login_captcha" value="1" <?= (($rwRole['login_captcha'] == '1') ? "checked" : ""); ?>>
                                <label for="myCheck"><?= $lang['login_with_captcha']; ?></label>
                            </div>
                        </td>
                    <?php } ?>

                    <?php if ($rwgetRole['login_otp_mobile'] == '1') { ?>
                        <td>
                            <div class="checkbox checkbox-success">
                                <input id="myChfeccsk15d10" type="checkbox" class="checkBoxClass row_24 col_7" name="loginmobile" value="1" <?= (($rwRole['login_otp_mobile'] == '1') ? "checked" : ""); ?>>
                                <label for="myCheck"><?= $lang['login_otp_mobile']; ?></label>
                            </div>
                        </td>
                    <?php } ?>

                </tr>
                <?php if ($rwgetRole['set_watermark'] == '1') { ?>

                    <tr>
                        <td>
                            <div class="form-group"></div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="checkbox checkbox-success">
                                <input id="myChfeccsk15d11" type="checkbox" class="checkBoxClass row_24 col_8" name="setwatermark" value="1" <?= (($rwRole['set_watermark'] == '1') ? "checked" : ""); ?>>
                                <label for="myCheck"><?= $lang['set_watermark']; ?></label>
                            </div>
                        </td>
                    </tr>
        <?php }
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
                            <input id="mydCheck160" type="checkbox" class="checkBoxClass row_25 col_1" name="view_exten" value="1" <?= (($rwRole['view_exten'] == '1') ? "checked" : ""); ?>>
                            <label for="myCheck"><?= $lang['view_exten']; ?></label>
                        </div>
                    </td>
                <?php } ?>

                <?php if ($rwgetRole['add_exten'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myChecfek159" type="checkbox" class="checkBoxClass row_25 col_2" name="add_exten" value="1" <?= (($rwRole['add_exten'] == '1') ? "checked" : ""); ?>>
                            <label for="myCheck"><?= $lang['add_exten'] ?></label>
                        </div>
                    </td>

                <?php }
                if ($rwgetRole['enable_exten'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myChefck159" type="checkbox" class="checkBoxClass row_25 col_3" name="enable_exten" value="1" <?= (($rwRole['enable_exten'] == '1') ? "checked" : ""); ?>>
                            <label for="myCheck"><?php echo $lang['enable_exten']; ?></label>
                        </div>
                    </td>

                <?php }
                if ($rwgetRole['delete_exten'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myCheeck159" type="checkbox" class="checkBoxClass row_25 col_4" name="delete_exten" value="1" <?= (($rwRole['delete_exten'] == '1') ? "checked" : ""); ?>>
                            <label for="myCheck"><?= $lang['delete_exten']; ?></label>
                        </div>
                    </td>

                <?php } ?>
            </tr>


        <?php } ?>

        <?php if ($rwgetRole['view_apikey'] == '1' || $rwgetRole['add_apikey'] == '1' || $rwgetRole['regenerate_apikey'] == '1' || $rwgetRole['delete_apikey'] == '1') { ?>

            <tr>
                <td>
                    <div class="form-group"></div>
                </td>
            </tr>
            <tr>
                <td colspan="4">

                    <div class="checkbox checkbox-success txt">
                        <input type="checkbox" id="select_row_26" />
                        <label><?= $lang['manage_apikey']; ?> </label>
                    </div>
                </td>
            </tr>

            <tr>
                <td>
                    <div class="form-group"></div>
                </td>
            </tr>
            <tr>
                <?php if ($rwgetRole['view_apikey'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input <?= (($rwRole['view_apikey'] == '1') ? "checked" : ""); ?> id="mydCheck160" type="checkbox" class="checkBoxClass row_26 col_1" name="view_apikey" value="1">
                            <label for="myCheck"><?= $lang['view_apikey']; ?></label>
                        </div>
                    </td>
                <?php } ?>

                <?php if ($rwgetRole['add_apikey'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input <?= (($rwRole['add_apikey'] == '1') ? "checked" : ""); ?> id="myChecfek159" type="checkbox" class="checkBoxClass row_26 col_2" name="add_apikey" value="1">
                            <label for="myCheck"><?= $lang['add_apikey'] ?></label>
                        </div>
                    </td>

                <?php }
                if ($rwgetRole['regenerate_apikey'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input <?= (($rwRole['regenerate_apikey'] == '1') ? "checked" : ""); ?> id="myChefck159" type="checkbox" class="checkBoxClass row_26 col_3" name="regenerate_apikey" value="1">
                            <label for="myCheck"><?php echo $lang['regenerate_apikey']; ?></label>
                        </div>
                    </td>

                <?php }
                if ($rwgetRole['delete_apikey'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input <?= (($rwRole['delete_apikey'] == '1') ? "checked" : ""); ?> id="myCheeck159" type="checkbox" class="checkBoxClass row_26 col_4" name="delete_apikey" value="1">
                            <label for="myCheck"><?= $lang['delete_apikey']; ?></label>
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
                            <input id="myCheck79" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_177 col_0" name="ccreate" value="1" <?php
                                                                                                                                                                                        if ($rwRole['create_client'] == '1') {
                                                                                                                                                                                            echo 'checked';
                                                                                                                                                                                        }
                                                                                                                                                                                        ?>>
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
                            <input id="myCheck80" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_523 col_0" name="ezeescan" value="1" <?php
                                                                                                                                                                                            if ($rwRole['ezeescan'] == '1') {
                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>>
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
        <?php if ($rwgetRole['mis_upload_download_report'] == '1') { ?>
            <tr>
                <td>
                    <div class="form-group"></div>
                </td>
            </tr>
            <tr>
                <td colspan="2">

                    <div class="checkbox checkbox-success txt">
                        <input type="checkbox" id="select_row_524" />
                        <label for="myCheck">Client Upload & Download MIS Report</label>
                    </div>

                </td>
            </tr>
            <tr>
                <td>
                    <div class="form-group"></div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <?php if ($rwgetRole['mis_upload_download_report'] == '1') { ?>
                        <div class="checkbox checkbox-success">
                            <input id="myCheck80" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" class="checkBoxClass row_524 col_0" name="mis_upload_download_report" value="1" <?php
                                                                                                                                                                                                            if ($rwRole['mis_upload_download_report'] == '1') {
                                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                                            }
                                                                                                                                                                                                            ?>>
                            <label for="myCheck">Client Upload & Download MIS Report</label>
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

        <?php if ($rwgetRole['email_credential'] == '1' || $rwgetRole['add_email_credential'] == '1' || $rwgetRole['edit_email_credential'] == '1') { ?>

            <tr>
                <td colspan="4">
                    <div class="checkbox checkbox-success txt">
                        <input type="checkbox" id="select_row_29" />
                        <label><?= $lang['Manage_Email_Credential']; ?> </label>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="form-group"></div>
                </td>
            </tr>

        <?php }
        if ($rwgetRole['email_credential'] == '1' || $rwgetRole['add_email_credential'] == '1' || $rwgetRole['edit_email_credential'] == '1') { ?>
            <tr>
                <?php if ($rwgetRole['email_credential'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="mydCdhecck160" type="checkbox" class="checkBoxClass row_29 col_0" name="viewemailcre" value="1" <?= (($rwRole['email_credential'] == '1') ? "checked" : ""); ?>>
                            <label for="myCheck"><?= $lang['view_email_credential']; ?></label>
                        </div>
                    </td>
                <?php } ?>

                <?php if ($rwgetRole['add_email_credential'] == '1') { ?>
                    <td>
                        <div class="checkbox checkbox-success">
                            <input id="myChecfdxek159" type="checkbox" class="checkBoxClass row_29 col_1" name="addmailcre" value="1" <?= (($rwRole['add_email_credential'] == '1') ? "checked" : ""); ?>>
                            <label for="myCheck"><?= $lang['add_email_credential'] ?></label>
                        </div>
                    </td>

                <?php }
                if ($rwgetRole['edit_email_credential'] == '1') { ?>
                    <td colspan="2">
                        <div class="checkbox checkbox-success">
                            <input id="myxChedfck159" type="checkbox" class="checkBoxClass row_29 col_2" name="editemailcre" value="1" <?= (($rwRole['edit_email_credential'] == '1') ? "checked" : ""); ?>>
                            <label for="myCheck"><?php echo $lang['edit_email_credential']; ?></label>
                        </div>
                    </td>

                <?php } ?>
            </tr>

        <?php } ?>
        <tr>
            <td colspan="4"> 
                <div class="checkbox checkbox-primary txt">
                    <input type="checkbox" id="select_row_41"/>
                    <label><?= $lang['Shared_With_Me']; ?> </label>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="checkbox checkbox-primary">
                    <input id="mydCdhecck160" type="checkbox" class="checkBoxClass row_41 col_0" name="shared_with_me_splt" value="1" <?= (($rwRole['shared_with_me_splt'] == '1') ? "checked" : ""); ?>>
                    <label for="myCheck">splited file</label>
                </div>
            </td>
        </tr>

        <tr>
            <td colspan="4"> 
                <div class="checkbox checkbox-primary txt">
                    <input type="checkbox" id="select_row_42"/>
                    <label>Shared Files</label>
                </div>
            </td>
        </tr>

        <tr>
            <td>
                <div class="checkbox checkbox-primary">
                    <input id="mydCdhecck160" type="checkbox" class="checkBoxClass row_42 col_0" name="shared_files_splt" value="1" <?= (($rwRole['shared_files_splt'] == '1') ? "checked" : ""); ?>>
                    <label for="myCheck">split file</label>
                </div>
            </td>
            <td>
                <div class="checkbox checkbox-primary">
                    <input id="mydCdhecck160" type="checkbox" class="checkBoxClass row_42 col_0" name="send_split_file" value="1" <?= (($rwRole['send_split_file'] == '1') ? "checked" : ""); ?>>
                    <label for="myCheck">Send split file</label>
                </div>
            </td>
        </tr>
       

    </table>
    <input type="hidden" name="rid" value="<?php echo $rwRole['role_id']; ?>">
</div>
<!--for select all or none---->
<script type="text/javascript" src="./assets/js/jquery.min.js"></script>
<script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>

<script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('form').parsley();

        $('.respecialchar').keyup(function() {
            var groupName = $(this).val();
            re = /[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi;
            var isSplChar = re.test(groupName);
            if (isSplChar) {
                var no_spl_char = groupName.replace(/[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi, '');
                $(this).val(no_spl_char);
            }
        });

    });
    $(".select3").select2();
    //firstname last name 
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
            for (row = 0; row < rows.length; row++) {
                var rowall = $('.row_' + row);
                var rowchecked = rowall.filter(':checked');
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
                    // console.log(this.id+" clicked to select "+selectType+" "+selectIndex);
                    // e.g. .row_2
                    $("." + selectType + "_" + selectIndex)
                        .prop('checked', $("#select_" + selectType + "_" + selectIndex).prop('checked'));
                }
            });
    });
</script>
<script type="text/javascript">
    google.load("elements", "1", {
        packages: "transliteration"
    });

    function onLoad() {
        var langcode = '<?php echo $langDetail['lang_code']; ?>';
        var options = {
            sourceLanguage: 'en',
            destinationLanguage: [langcode],
            shortcutKey: 'ctrl+g',
            transliterationEnabled: true
        };

        var control =
            new google.elements.transliteration.TransliterationControl(options);
        //var ids = ["groupName12"];
        var elements = document.getElementsByClassName('translatetext');
        control.makeTransliteratable(elements);
    }
    $.getScript('assets/js/test.js', function() {
        // Call custom function defined in script
        onLoad();
    });
    google.setOnLoadCallback(onLoad);
</script>
<!---->