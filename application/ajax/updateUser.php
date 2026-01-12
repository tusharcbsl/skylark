<?php
require_once '../../sessionstart.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout");
}
if (isset($_SESSION['lang'])) {
    $file = "../../" . $_SESSION['lang'] . ".json";
} else {
    $file = "../../English.json";
}
$data = file_get_contents($file);
$lang = json_decode($data, true);
require './../config/database.php';

require_once '../pages/function.php';
mysqli_set_charset($db_con, "utf8");

$langDetail = mysqli_query($db_con, "select * from tbl_language where lang_name='" . $_SESSION['lang'] . "'") or die('Error : ' . mysqli_error($db_con));
$langDetail = mysqli_fetch_assoc($langDetail);

$sameGroupIDs = array();
$group = mysqli_query($db_con, "select group_id from tbl_bridge_grp_to_um where find_in_set('$_SESSION[cdes_user_id]',user_ids)") or die('Error' . mysqli_error($db_con));
while ($rwGroup = mysqli_fetch_assoc($group)) {
    $sameGroupIDs[] = $rwGroup['group_id'];
}
$sameGroupIDs = array_unique($sameGroupIDs);
sort($sameGroupIDs);
$sameGroupIDs = implode(',', $sameGroupIDs);

//for user role
$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

$rwgetRole = mysqli_fetch_assoc($chekUsr);
if ($rwgetRole['modify_userlist'] != '1') {
    header('Location:../../index');
}
if (intval($_POST['ID'])) {
    $id = preg_replace("/[^0-9 ]/", "", $_POST['ID']);
    mysqli_set_charset($db_con, "utf8");
    $user = mysqli_query($db_con, "select * from tbl_user_master where user_id='$id'");
    $rwUser = mysqli_fetch_assoc($user);

    $rwpwdPolicy = getPasswordPolicy($db_con);
    ?>
    <link href="./assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" /> 

    <div class="row"> 
        <div class="col-md-6"> 
            <div class="form-group"> 
                <label for="userName" class="control-label"><?php echo $lang['First_Name']; ?><span style="color:red;">*</span></label> 
                <input type="text" class="form-control translatetext" name="firstname" id="userName" placeholder="<?php echo $lang['Enter_First_Name']; ?>" value="<?php echo $rwUser['first_name']; ?>" required> 
            </div> 
        </div> 
        <div class="col-md-6"> 
            <div class="form-group"> 
                <label for="lastName" class="control-label"><?php echo $lang['Last_Name']; ?></label> 
                <input type="text" class="form-control translatetext" name="lastname" id="lastName" placeholder="<?php echo $lang['Enter_Last_Name']; ?>" value="<?php echo $rwUser['last_name']; ?>"> 
            </div> 
        </div>
        <div class="col-md-6"> 
            <?php
            //$chekAdmin = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) and tur.user_role='Admin'") or die('Error:' . mysqli_error($db_con));
            ?>
            <div class="form-group"> 
                <label for="field-2" class="control-label"><?php echo $lang['Email_Address']; ?><span style="color:red;">*</span></label> 
                <input type="email" class="form-control" required parsley-type="email"  name="email" id="inputEmail3" placeholder="<?php echo $lang['Enter_Email_Id']; ?>" value="<?php echo $rwUser['user_email_id']; ?>" <?php
                if ($_SESSION['cdes_user_id'] != 1) {
                    echo 'readonly';
                }
                ?> required> 
            </div> 
        </div>
        <div class="col-md-6"> 
            <div class="form-group">
                <label for="emailAddress"><?php echo $lang['Employee_ID']; ?></label>
                <input type="text" name="empId" placeholder="<?php echo $lang['Employee_ID']; ?>" class="form-control" id="empId" value="<?php echo $rwUser['emp_id']; ?>">
            </div>
        </div>
        <div class="col-md-6"> 
            <div class="form-group"> 
                <label for="phone" class="control-label"><?php echo $lang['Phone']; ?><span style="color:red;">*</span></label> 
                <input type="text" data-parsley-type="digits" required class="form-control" name="phone" id="phone" placeholder="<?php echo $lang['Phone']; ?>" value="<?php echo $rwUser['phone_no']; ?>" required  parsley-trigger="change" data-parsley-type="number"   maxlength="10" minlength="10" data-parsley-minlength-message="This value is too short. It should have 10 digits only"> 
            </div> 
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="pass1"><?php echo $lang['Password']; ?><!--<span style="color:red;">*</span>--></label>
                <input id="pass1" name="password" type="password" placeholder="<?php echo $lang['Password']; ?>"  class="form-control" data-parsley-errors-container=".errorspannewpassinput"    data-parsley-minlength="<?= (!empty($rwpwdPolicy['minlen']) ? $rwpwdPolicy['minlen'] : '8'); ?>" data-parsley-maxlength="<?= (!empty($rwpwdPolicy['maxlen']) ? $rwpwdPolicy['maxlen'] : '8'); ?>" data-parsley-uppercase="<?= $rwpwdPolicy['uppercase']; ?>" data-parsley-lowercase="<?= $rwpwdPolicy['lowercase']; ?>" data-parsley-number="<?= $rwpwdPolicy['numbers']; ?>" data-parsley-required-message="Please enter your password."  >
                <!--<input id="pass1" type="password" id="pass1" name="password" class="form-control" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" data-parsley-errors-container=".errorspannewpassinput" data-parsley-required-message="Please enter your password."    data-parsley-uppercase="1"    data-parsley-lowercase="1"    data-parsley-number="1"    data-parsley-special="1" placeholder="<?php echo $lang['Password']; ?>" >-->
    <!-- <input type="password" id="pass1" parsley-trigger="change keyup" name="password" class="form-control"  required placeholder="Password" data-parsley-minlength="8"    data-parsley-errors-container=".errorspannewpassinput"    data-parsley-required-message="Please enter your password."    data-parsley-uppercase="1"    data-parsley-lowercase="1"    data-parsley-number="1"    data-parsley-special="1" />-->
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="passWord2"><?php echo $lang['Confirm_Password']; ?> <!--<span style="color:red;">*</span>--></label>
                <input data-parsley-equalto="#pass1" type="password" placeholder="<?php echo $lang['Confirm_Password']; ?>" class="form-control" id="passWord2" >
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="designation"><?php echo $lang['Designation']; ?> <span style="color:red;">*</span></label>
                <input type="text" name="designation" parsley-trigger="change" value="<?php echo $rwUser['designation']; ?>" required placeholder="<?php echo $lang['Enter_Designation']; ?>" class="form-control translatetext" id="designation" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="privilege"><?php echo $lang['Select_Group']; ?> <span style="color:red;">*</span></label>

                <?php
                $userGroupMap = array();
                $grpbrg = mysqli_query($db_con, "select group_id from tbl_bridge_grp_to_um where user_ids like '%$rwUser[user_id]%'") or die('Error : ' . mysqli_error($db_con));
                while ($rwBdgrp = mysqli_fetch_assoc($grpbrg)) {
                    array_push($userGroupMap, $rwBdgrp['group_id']);
                }
                //$userGroupMap= explode(",", $userGroupMap);
                ?>
                <select class="form-control select15 select2-multiple" name="groups[]" multiple="multiple" multiple data-placeholder="<?php echo $lang['Select_Group']; ?>" required parsley-trigger="change" required>
                    <?php
                    $grp = mysqli_query($db_con, "select * from tbl_group_master where group_id in($sameGroupIDs)") or die('error' . mysqli_error($db_con));
                    while ($rwGrp = mysqli_fetch_assoc($grp)) {
                        if (in_array($rwGrp['group_id'], $userGroupMap)) {
                            echo '<option value="' . $rwGrp['group_id'] . '" selected>' . $rwGrp['group_name'] . '</option>';
                        } else {
                            echo '<option value="' . $rwGrp['group_id'] . '" >' . $rwGrp['group_name'] . '</option>';
                        }
                    }
                    ?>    


                </select>
            </div>
        </div>    
                      
        <div class="col-md-6">
            <div class="form-group">
                <?php
                $EditUserrole = mysqli_query($db_con, "select * from tbl_bridge_role_to_um where find_in_set($id,user_ids)");
                $rwRoleId = mysqli_fetch_assoc($EditUserrole);
                ?>
                <label for="privilege"><?php echo $lang["User_Role"]; ?> <span style="color:red;">*</span></label>
                <input name="previousProfile" type="hidden" value="<?php echo $rwRoleId['role_id']; ?>" />
                <select class="form-control selectpicker" data-live-search="true"  name="userRole" required>
                    <?php
                    $roleids = array();
                    $grp_by_rl_ids = mysqli_query($db_con, "SELECT group_id,user_ids,roleids FROM `tbl_bridge_grp_to_um` where find_in_set($_SESSION[cdes_user_id],user_ids)");
                    while ($rwGrp = mysqli_fetch_array($grp_by_rl_ids)) {

                        if (!empty($rwGrp['roleids'])) {
                            $roleids[] = $rwGrp['roleids'];
                        }
                    }
                    $roleids = implode(',', $roleids);
                    $roleids = explode(',', $roleids);
                    $roleids = array_unique($roleids);
                    $roleids = implode(',', $roleids);
                    if (!empty($roleids)) {
                        $role = mysqli_query($db_con, "select * from tbl_bridge_role_to_um where find_in_set($id,user_ids)");
                        $rwRolid = mysqli_fetch_assoc($role);

                        $rol = mysqli_query($db_con, "select role_id,user_role from tbl_user_roles where role_id in($roleids)order by user_role asc") or die('Error' . mysqli_error($db_con));
                        while ($rwRole = mysqli_fetch_assoc($rol)) {
                            if ($rwRole['role_id'] != 1) {
                                if ($rwRole['role_id'] == $rwRolid['role_id']) {
                                    echo'<option value="' . $rwRole['role_id'] . '" selected>' . $rwRole['user_role'] . '</option>';
                                } else {
                                    echo'<option value="' . $rwRole['role_id'] . '">' . $rwRole['user_role'] . '</option>';
                                }
                            }
                        }
                    }
                    ?>    
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="reporting"><?php echo $lang["select_department"]; ?></label>
                <select class="form-control select15 select2-multiple" data-live-search="true" name="dept_id[]"multiple="multiple" multiple data-placeholder="<?php echo $lang['select_department']; ?>">
                    <option value=""><?php echo $lang["select_department"]; ?></option>
                    <?php
                        $dept_data = mysqli_query($db_con, "SELECT * FROM tbl_department");
                        $selected_dept_ids = explode(',', $rwUser['dept_id']);
                        while ($row = mysqli_fetch_assoc($dept_data)) {
                            $selected = in_array($row['id'], $selected_dept_ids) ? 'selected' : '';
                            echo '<option value="' . $row['id'] . '" ' . $selected . '>' . $row['department_name'] . '</option>';
                        }
                    ?>
                </select>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="reporting"><?php echo $lang["Select_Reporting_To"]; ?></label>
                <select class="form-control select15" data-live-search="true" name="superiorName" parsley-trigger="change" >
                    <option selected disabled><?php echo $lang["Select_Reporting_To"]; ?></option>

                    <?php
                    mysqli_set_charset($db_con, "utf8");
                    $sameUserGroupIDs = array();
                    $samegroup = mysqli_query($db_con, "select * from tbl_bridge_grp_to_um where find_in_set($id,user_ids)") or die('Error' . mysqli_error($db_con));
                    while ($rwGroupUser = mysqli_fetch_assoc($samegroup)) {
                        $sameUserGroupIDs[] = $rwGroupUser['user_ids'];
                    }
                    $sameUserGroupIDs = array_unique($sameUserGroupIDs);
                    sort($sameUserGroupIDs);
                    $sameUserGroupIDs = implode(',', $sameUserGroupIDs);
                    $repotingUser = mysqli_query($db_con, "select * from tbl_user_master where user_id in($sameUserGroupIDs)");
                    //$sameUserGroupIDs = array_unique($sameUserGroupIDs);
                    $sameUsersGroupIDs = explode(',', $sameUserGroupIDs);
                    //  print_r($sameUsersGroupIDs);

                    while ($rwreportingUser = mysqli_fetch_assoc($repotingUser)) {
                        if ($rwreportingUser['user_id'] != 1) {
                            $repotingmgr = mysqli_query($db_con, "select superior_name from tbl_user_master where user_id='$id'");
                            $rwreportingmgr = mysqli_fetch_assoc($repotingmgr);
                            if ($rwreportingmgr['superior_name'] == $rwreportingUser['user_id']) {
                                echo '<option value="' . $rwreportingUser['user_id'] . '" selected>' . $rwreportingUser['first_name'] . ' ' . $rwreportingUser['last_name'] . '</option>';
                            } else {
                                echo '<option value="' . $rwreportingUser['user_id'] . '">' . $rwreportingUser['first_name'] . ' ' . $rwreportingUser['last_name'] . '</option>';
                            }
                        }
                    }
                    ?>   
                </select>

            </div>
        </div>
      
        <div class="col-md-6">
            <div class="form-group">
                <label for="picture"><?php echo $lang["Profile_Picture"]; ?></label>
                <input type="file" name="image"  class="filestyle" accept="image/*">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="picture"><?php echo $lang["Current_Profile_Picture"]; ?></label>
                <?php if (!empty($rwUser['profile_picture'])) { ?>
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($rwUser['profile_picture']); ?>" height="50px">
                <?php } else { ?>
                    <img src="./assets/images/avatar.png" height="50px">
                <?php } ?>
            </div>
        </div>
        

    </div> 
    <input type="hidden" name="uid" value="<?php echo $rwUser['user_id']; ?>">

    <script src="./assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>

    <script src="./assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
    <script src="./assets/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>
    <script src="assets/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>

    <!-- for searchable select-->
    <script type="text/javascript">
        $(document).ready(function () {
            //$('form').parsley();
            $(".select15").select2();
        });
        jQuery(document).ready(function () {
            $('.selectpicker').selectpicker();

        });
    </script>
    <script type="text/javascript">
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
        //for avoid special charecter //firstname last name 
        $("input#userName, input#lastName, input#designation, input#phone, input#empId").keyup(function ()
        {
            var GrpNme = $(this).val();
            re = /[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi;
            var isSplChar = re.test(GrpNme);
            if (isSplChar)
            {
                var no_spl_char = GrpNme.replace(/[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi, '');
                $(this).val(no_spl_char);
            }
        });
        $("input#userName, input#lastName, input#designation, input#phone, input#empId").bind(function () {
            $(this).val($(this).val().replace(/[<>]/g, ""))
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
        $("#pass1").keyup(function () {
            var pass1 = $("#pass1").val();
            if (pass1 == "")
            {
                $("#passWord2").removeAttr("required", "required");
            } else {
                $("#passWord2").attr("required", "required");
            }
        })
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
        $.getScript('assets/js/test.js', function () {
            // Call custom function defined in script
            onLoad();
        });
        google.setOnLoadCallback(onLoad);
    </script>



    <?php
}


?>


