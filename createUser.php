<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    require_once './application/config/database.php';
    require_once './application/pages/head.php';
	
    require_once './application/pages/function.php';

    // echo $rwgetRole['dashboard_mydms']; die;
    if ($rwgetRole['create_user'] != '1') {
        header('Location: ./index');
    }

    $rwpwdPolicy = getPasswordPolicy($db_con);

    ?>
    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />

    <body class="fixed-left">
        <!-- Begin page -->
        <div id="wrapper">
            <!-- Top Bar Start -->
            <?php require_once './application/pages/topBar.php'; ?>
            <!-- Top Bar End -->
            <?php require_once './application/pages/sidebar.php'; ?>
            <!-- Left Sidebar End --> 
            <div class="content-page">
                <!-- Start content -->
                <div class="content">
                    <div class="container">
                        <!-- Page-Title -->
                        <div class="row">
                            <ol class="breadcrumb">
                                <li><a href="createUser"><?php echo $lang['Masters']; ?></a></li>
                                <li class="active"><?php echo $lang['Create_user']; ?></li>
                                 <li> <a href="#" data-toggle="modal" data-target="#help-modal" id="helpview" data="3" title="<?= $lang['help']; ?>"><i class="fa fa-question-circle"></i> </a></li>
                            </ol>
                        </div>
                        <div class="row" id="afterClickHide">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h4 class="header-title col-lg-6"> <?php echo $lang['Required_fields_are_marked_with_a']; ?>(<span style="color:red;">*</span>)</h4>
                                    
                                    <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right" onclick="goPrevious();"><i class="fa fa-arrow-alt-left"></i> <?php echo $lang['go_back']; ?> </a>
                                </div>
                                <div class="box-body">
                                    <div class="col-lg-6">
                                        <div class="card-box">
                                            <form action="#" data-parsley-validate novalidate method="post" enctype="multipart/form-data" id="form">
                                                <div class="form-group">
                                                    <label for="userName"> <?php echo $lang['First_Name']; ?><span style="color:red;">*</span></label>
                                                    <input type="text" name="firstname" parsley-trigger="change"  placeholder="<?php echo $lang['Enter_First_Name']; ?>" class="form-control translatetext" id="userName" required data-parsley-pattern-message="First name should only contain alphates">
                                                </div>
                                                <div class="form-group">
                                                    <label for="lastName"><?php echo $lang['Last_Name']; ?></label>
                                                    <input type="text" name="lastname" parsley-trigger="change" placeholder="<?php echo $lang['Enter_Last_Name']; ?>" class="form-control translatetext" id="lastName"  data-parsley-pattern-message="Last name should only contain alphates">
                                                </div>
                                                <div class="form-group">
                                                    <label for="emailAddress"><?php echo $lang['Email_Address']; ?><span style="color:red;">*</span></label>
                                                    <input type="email" name="email" parsley-trigger="change" parsley-type="email"  placeholder="<?php echo $lang['Enter_Email_Id']; ?>" class="form-control" id="emailAddress" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="emailAddress"><?php echo $lang['Employee_ID']; ?></label>
                                                    <input type="text" name="empId" parsley-trigger="change" parsley-type="email"  placeholder="<?php echo $lang['Enter_Employee_ID']; ?>" class="form-control specialchaecterlock" id="empId" >
                                                </div>
                                                <div class="form-group">
                                                    <label for="phone"><?php echo $lang['Phone']; ?><span style="color:red;">*</span></label>
                                                    <input type="text" name="phone" parsley-trigger="change" data-parsley-type="number"   data-parsley-minlength="10" data-parsley-maxlength="10" data-parsley-minlength-message="This value is too short. It should have 10 digits only" placeholder="<?php echo $lang['Enter_Phone_Number']; ?>" class="form-control"  id="phone" maxlength="10" required>
                                                </div>
                                                <!--    <div class="form-group">
                                                        <button class="btn btn-primary waves-effect waves-light" type="button" id="veriMob" onclick="sendOtp()" name="verifyMob">
                                                            Verify Mobile No.</button> &nbsp;&nbsp;<span id="sentMsg" style="color: green;"></span><span id="sentMsg2" style="color: red;"></span>
                                                        <img src="assets/images/load.gif" width="50px"  alt="load" class="img-responsive center-block" style="display: none" id="lod" />
                                                        <div id="entrOpt"></div>
                                                    </div> -->
                                                <script>
                                                    function sendOtp() {
                                                        var mob = $("#phone").val();
                                                        //alert('helo');
                                                        if (mob.length == 10) {
                                                            // do something

                                                            //$(this).attr('src',image.src);
                                                            $("#lod").css('display', 'block');


                                                            //$("#lod").css('display', 'block');
                                                            $.post("application/ajax/otpToMob.php", {mobno: mob}, function (result, status) {

                                                                if (status == 'success') {
                                                                    $("#entrOpt").html(result);
                                                                    // alert('otp sent');
                                                                    $("#sentMsg").html('OTP Sent in Your Above Mobile Number');
                                                                    $("#sentMsg2").css('display', 'none');
                                                                    $("#lod").css('display', 'none');
                                                                }
                                                            });
                                                        } else {
                                                            $("#sentMsg2").html('Please Enter Valid Mobile No.');
                                                            $("#lod").css('display', 'none');
                                                            //$("#sentMsg").css('display', 'none');
                                                        }
                                                    }

                                                </script>

                                                <div class="form-group">
                                                    <label for="pass1"><?php echo $lang['Password']; ?><span style="color:red;">*</span></label>
                                                    <input id="pass1" name="password" type="password"  placeholder="<?php echo $lang['Password']; ?>" required class="form-control" data-parsley-minlength="<?= (!empty($rwpwdPolicy['minlen']) ? $rwpwdPolicy['minlen'] : '8'); ?>" data-parsley-maxlength="<?= (!empty($rwpwdPolicy['maxlen']) ? $rwpwdPolicy['maxlen'] : '8'); ?>" data-parsley-uppercase="<?= $rwpwdPolicy['uppercase']; ?>" data-parsley-lowercase="<?= $rwpwdPolicy['lowercase']; ?>" data-parsley-number="<?= $rwpwdPolicy['numbers']; ?>" data-parsley-special="<?= $rwpwdPolicy['s_char']; ?>" data-parsley-errors-container=".errorspannewpassinput" data-parsley-required-message="Please enter your password.">
                                                </div>
                                                <div class="form-group">
                                                    <label for="passWord2"><?php echo $lang['Confirm_Password']; ?><span style="color:red;">*</span></label>
                                                    <input data-parsley-equalto="#pass1" type="password" name="confirm_pass" required placeholder="<?php echo $lang['Confirm_Password']; ?>" class="form-control" id="passWord2">
                                                </div>
                                                <div class="form-group">
                                                    <label for="designation"><?php echo $lang['Designation']; ?><span style="color:red;">*</span></label>
                                                    <input type="text" name="designation" parsley-trigger="change" required placeholder="<?php echo $lang['Enter_Designation']; ?>" class="form-control translatetext" id="designation">
                                                </div>
                                                <div class="form-group">
                                                    <label for="privilege"><?php echo $lang['Select_Group']; ?><span style="color:red;">*</span></label>
                                                    <select class="select2 select2-multiple" name="groups[]" multiple="multiple" multiple data-placeholder="<?php echo $lang['Select_Group']; ?>"   id="group" required="" >
                                                        <?php
                                                        $group_permission = mysqli_query($db_con, "SELECT group_id,user_ids FROM `tbl_bridge_grp_to_um`");
                                                        while ($allGroupRow = mysqli_fetch_array($group_permission)) {
                                                            $user_ids = explode(',', $allGroupRow['user_ids']);
                                                            if (in_array($_SESSION['cdes_user_id'], $user_ids)) {
                                                                $grp = mysqli_query($db_con, "select group_id,group_name from tbl_group_master WHERE group_id='$allGroupRow[group_id]' order by group_name asc") or die('Error' . mysqli_error($db_con));
                                                                while ($rwGrp = mysqli_fetch_assoc($grp)) {
                                                                    echo'<option value="' . $rwGrp['group_id'] . '">' . $rwGrp['group_name'] . '</option>';
                                                                }
                                                            }
                                                        }
                                                        ?>    
                                                    </select>
                                                    <span style="color:red" id="grp"></span>
                                                </div>
                                                <div class="form-group">
                                                    <label for="privilege"><?php echo $lang["Select_User's_Privilege"]; ?><span style="color:red;">*</span></label>
                                                    <select class="select2 translatetext" name="userRole" id="privelege" required>
                                                        <option selected disabled><?php echo $lang["Select_Privilege"]; ?></option>
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
                                                            $rol = mysqli_query($db_con, "select role_id,user_role from tbl_user_roles where role_id in($roleids)order by user_role asc"); // or die('Error' . mysqli_error($db_con));
                                                            while ($rwRole = mysqli_fetch_assoc($rol)) {
                                                                if ($rwRole['role_id'] != 1) {
                                                                    echo'<option value="' . $rwRole['role_id'] . '">' . $rwRole['user_role'] . '</option>';
                                                                }
                                                            }
                                                        }
                                                        ?>    
                                                    </select>
                                                </div>
                                                 <div class="form-group">
                                                    <label for="reporting"><?php echo $lang["select_department"]; ?></label>
                                                    <select class="form-control select2 select2-multiple" name="dept_id[]" multiple="multiple" multiple data-placeholder="<?php echo $lang['select_department']; ?>" id="dept_id">
                                                        <option><?php echo $lang["select_department"]; ?></option>
                                                        <?php
                                                            $dept_data = mysqli_query($db_con, "SELECT * FROM tbl_department");
                                                            while ($row = mysqli_fetch_assoc($dept_data)) {
                                                                echo '<option value="' . $row['id'] . '">' . $row['department_name'] . '</option>';
                                                            }
                                                        ?>
                                                    </select>

                                                </div>
                                                <div class="form-group">
                                                  <label for="userName"><?php echo $lang["Select_Storage"]; ?> <!--<span style="color:red;">*</span>--></label> 
                                                    <select class="select2"  name="slparentName" data-placeholder="<?php echo $lang['Select_Storage']; ?>" > 
                                                        <option disabled selected><?php echo $lang["Select_Storage"]; ?></option>
                                                        <?php
                                                        $perm = mysqli_query($db_con, "select sl_id from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]' group by sl_id");
                                                        while ($rwPerm = mysqli_fetch_assoc($perm)) {
                                                            $slperm = $rwPerm['sl_id'];
                                                            $sllevel = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slperm' and delete_status=0");
                                                            $rwSllevel = mysqli_fetch_assoc($sllevel);
                                                            $level = $rwSllevel['sl_depth_level'];
                                                            findChild($slperm, $level, $slperm);
                                                        }
                                                        ?>
                                                    </select>

                                                </div>

                                                <div class="form-group">
                                                    <label for="reporting"><?php echo $lang["Select_Reporting_To"]; ?></label>
                                                    <select class="form-control select2" name="superiorName"   parsley-trigger="change" id="reportto">
                                                        <option selected disabled><?php echo $lang["Select_Reporting_To"]; ?></option>
                                                        <?php
                                                        $reportTo = mysqli_query($db_con, "select * from tbl_user_master order by first_name asc"); // or die('error' . mysqli_error($db_con));
                                                        while ($rwreportTo = mysqli_fetch_assoc($reportTo)) {
                                                            if ($rwreportTo['user_id'] != 1 && $id != $rwreportTo['user_id']) {
                                                                ?>
                                                                <option value="<?php echo $rwreportTo['first_name'] . ' ' . $rwreportTo['last_name'] . '(' . $rwreportTo['user_email_id']; ?>" <?php echo (($rwUser['superior_name'] == $rwreportTo['first_name'] . ' ' . $rwreportTo['last_name']) ? 'selected' : ' '); ?>> <?php echo $rwreportTo['first_name'] . ' ' . $rwreportTo['last_name'] . ' (' . $rwreportTo['user_email_id'] . ')'; ?></option>

                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                        ?>
                                                    </select>

                                                </div>
                                                <!--<div class="form-group">
                                                    <label for="reporting">Superior Name<span style="color:red;">*</span></label> 
                                                     
                                                    <input type="text" name="superiorName" parsley-trigger="change" required placeholder="Enter Reporting to" class="form-control" id="reporting">
                                                </div>-->
                                                <!--   <div class="form-group">
                                                       <label for="supemail">Superior Email ID<span style="color:red;">*</span></label>
                                                       <input type="email" name="superiorEmail" parsley-type="email" parsley-trigger="change" required placeholder="Enter Superior Email ID" class="form-control" id="supemail">
                                                   </div> -->
                                                <div class="form-group">
                                                    <label for="picture"><?php echo $lang["Profile_Picture"]; ?><!--<span style="color:red;">*</span>--></label>
                                                    <input type="file" name="image" class="filestyle" accept="image/*">
                                                </div>
                                                <div class="form-group text-right m-b-0">

                                                    <button class="btn btn-primary waves-effect waves-light" type="submit" name="createuser" id="submitbuton">
                                                        <?php echo $lang["Submit"]; ?>
                                                    </button>
                                                    <a href="createUser" class="btn btn-default waves-effect waves-light m-l-5">
                                                        <?php echo $lang["Cancel"]; ?>
                                                    </a>
                                                </div>

                                            </form>

                                        </div>
                                    </div>
                                </div>
                            </div>				
                        </div>

                    </div> <!-- container -->

                </div> <!-- content -->

                <?php require_once './application/pages/footer.php'; ?>
            </div>          
        </div>
        <!-- END wrapper -->
        <?php require_once './application/pages/footerForjs.php'; ?>
        <script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>

        <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>
        <!--show wait gif-->
        <div style=" display: none; background: rgba(0,0,0,0.5); width: 100%; z-index: 2000; position: fixed; top:0;" id="wait">;
            <img src="assets/images/proceed.gif" alt="load"  style=" margin-left: 48%; margin-top: 250px; width: 100px; height:100px; position: fixed; "/>
        </div> 
        <script>
                                                    //for wait gif display after submit
                                                    var heiht = $(document).height();

                                                    $('#wait').css('height', heiht);

        </script>
        <script type="text/javascript">
            $(document).ready(function () {
                $form = $('form').parsley();
                $("#submitbuton").click(function () {
                    if ($('form').parsley() == true) {
                        $('#wait').show();
                        $('#afterClickHide').hide();
                    }
                });

                //addTranslationClass();

            });
            $(".select2").select2();

            //for avoid special charecter //firstname last name 
            $("input#userName, input#lastName, input#designation, input#phone").keyup(function ()
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
            $("input#userName, input#lastName, input#designation, input#phone").bind(function () {
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
        </script>
        <?php
        if (isset($_POST['createuser'], $_POST['token'])) {

            //$firstname = filter_input(INPUT_POST, "firstname");
            //$firstname = preg_replace("/[^a-zA-Z ]/", "", $firstname); //filter name
            $firstname = trim($_POST['firstname']);
            $firstname = preg_replace('/[^\w$\x{0080}-\x{FFFF}@#$&!%()_ <>]+/u', "", $firstname);
            $firstname = mysqli_real_escape_string($db_con, $firstname);


            $lastname = trim($_POST['lastname']);
            $lastname = preg_replace('/[^\w$\x{0080}-\x{FFFF}@#$&!%()_ <>]+/u', "", $lastname);
            $lastname = mysqli_real_escape_string($db_con, $lastname);

            $email = filter_input(INPUT_POST, "email");
            $email = preg_replace("/[^a-zA-Z0-9_@.-]/", "", $email); //filter email
            $email = mysqli_real_escape_string($db_con, $email);
            $empId = filter_input(INPUT_POST, "empId");
            //$empId = preg_replace("/[^0-9_]/", "", $empId); //filter empid
            $empId = mysqli_real_escape_string($db_con, $empId);
            $phone = filter_input(INPUT_POST, "phone");
            $phone = preg_replace("/[^0-9]/", "", $phone); //filter phone
            $phone = mysqli_real_escape_string($db_con, $phone);

            // $otp= filter_input(INPUT_POST, "OTP");
            //$otp= mysqli_real_escape_string($db_con,$otp);

            $password = filter_input(INPUT_POST, "password");
            $password = mysqli_real_escape_string($db_con, $password);
            $confirm_pass = filter_input(INPUT_POST, "confirm_pass");
            $confirm_pass = mysqli_real_escape_string($db_con, $confirm_pass);
//            $designation = filter_input(INPUT_POST, "designation");
//            $designation = preg_replace("/[^a-zA-Z ]/", "", $designation); //filter name
//            $designation = mysqli_real_escape_string($db_con, $designation);
//            
            $designation = trim($_POST['designation']);
            $designation = preg_replace('/[^\w$\x{0080}-\x{FFFF}@#$&!%()_ <>]+/u', "", $designation);
            $designation = mysqli_real_escape_string($db_con, $designation);
            $dept_id = $_POST['dept_id'];
            $dept_ids = implode(',', $dept_id);
            
            
            $ip = $_POST["ip"];

            $groups = $_POST["groups"];
            $userRole = $_POST["userRole"];
            $userRole = mysqli_real_escape_string($db_con, $userRole);
            $slparentNameid = $_POST['slparentName'];
            $slparentNameid = mysqli_real_escape_string($db_con, $slparentNameid);
            $superiorName = filter_input(INPUT_POST, "superiorName");
            //order is should be like this 
            $superiorEmail = substr($superiorName, strrpos($superiorName, "(") + 1);
            $superiorName = substr($superiorName, 0, strrpos($superiorName, "("));
            // $superiorName = mysqli_real_escape_string($db_con, $superiorName);
            //$superiorEmail = filter_input(INPUT_POST, "superiorEmail");
            //$superiorEmail = mysqli_real_escape_string($db_con, $superiorEmail);

            if ($password === $confirm_pass) {

                if (isset($_FILES['image']['name']) && !empty($_FILES['image']['name'])) {

                    $allowed = array('png', 'jpg', 'jpeg');
                    $filename = $_FILES['image']['name'];
                    $ext = pathinfo($filename, PATHINFO_EXTENSION);
                    if (!in_array($ext, $allowed)) {
                        echo '<script>taskFailed("createUser", "' . $lang['profile_image_allowed'] . '")</script>';

                        exit();
                    }
                }



                $image = addslashes(file_get_contents($_FILES['image']['tmp_name']));

                $decKey = decryptLicenseKey($clientKey);
                $decKey = explode("%", $decKey);
                if (!empty($decKey[0]) && !empty($decKey[1]) && !empty($_SESSION['clientid'])) {
                    /*
                     * validate right user at right time
                     */
                    if ($_SESSION['clientid'] != $decKey[1]) {
                        header('Location: ./index');
                        exit();
                    }
                    /*
                     * End of validation
                     */
                    require_once './application/config/validate_client_db.php';
                    $check_validity_qry = mysqli_query($db_valid_con, "select * from  tbl_client_master where client_id='$decKey[1]'"); //Query get validity of particular company user
                    $validity_date = mysqli_fetch_assoc($check_validity_qry); //fetch validity timestamp from client table
                    //echo "select * from tbl_plantype where plantype='$validity_date[plan_type]'";
//            $plantype_qry= mysqli_query($db_valid_con, "select * from tbl_plantype where plantype='$validity_date[plan_type]'");
//            $total_user_allot= mysqli_fetch_assoc($plantype_qry);
                    $t_user = preg_replace("/[^0-9]/", "", $validity_date['total_user']); //total user allow 
                    $t_user += 1; //1 extra user for super user
                    $validate_num_user = mysqli_query($db_con, "select count(user_email_id) as total_user from tbl_user_master where active_inactive_users=1"); //or die('Error:' . mysqli_error($db_valid_con));
                    $total_user = mysqli_fetch_assoc($validate_num_user);
                    // print_r($total_user);

                    $chkdefaultLang = mysqli_query($db_con, "SELECT lang_name FROM tbl_language WHERE default_language='1'");
                    $rwchkdefault = mysqli_fetch_assoc($chkdefaultLang);
                    $language = ((!empty($rwchkdefault['lang_name'])) ? ucfirst(strtolower($rwchkdefault['lang_name'])) : "English");


                    if ($total_user['total_user'] >= $t_user) {
                        echo '<script>taskFailed("createUser", "Cannot Create User,User Limit Exceeded!")</script>';
                    } else {
                        mysqli_set_charset($db_con, "utf8");
                        $chkUserMail = mysqli_query($db_con, "select * from tbl_user_master where user_email_id='$email'"); //or die('Error:' . mysqli_error($db_con));
//            $chkUserMail = mysqli_query($db_con, "select * from tbl_user_master where user_email_id='$email'") or die('Error:' . mysqli_error($db_con));
                        if (mysqli_num_rows($chkUserMail) > 0) {
                            echo '<script>taskFailed("createUser", "User Already Registerd Using this Email Id !")</script>';
                        } else {
                           
                            mysqli_set_charset($db_con, "utf8");
                            $create = mysqli_query($db_con, "insert into tbl_user_master (`user_id`, `user_email_id`, `first_name`, `last_name`, `password`, `designation`, `phone_no`, `profile_picture`, `superior_name`, `superior_email`, `user_created_date`, `emp_id`, `lang`, `dept_id`) values(null,'$email','$firstname','$lastname',sha1('$password'),'$designation','$phone','$image','$superiorName','$superiorEmail','$date', '$empId', '$language', '$dept_ids')"); //or die('Error' . mysqli_error($db_con));

                            if ($create) {
                                $user_id = mysqli_insert_id($db_con);
                                if (!empty($slparentNameid)) {
                                    mysqli_set_charset($db_con, "utf8");
                                    $insertPerm = mysqli_query($db_con, "insert into tbl_storagelevel_to_permission (user_id,sl_id) values('$user_id','$slparentNameid')"); //or die('Error: sl permission' . mysqli_error($db_con));
                                    //$insertPerm_run = mysqli_query($db_con, $insertStp) or die('Error:' . mysqli_error($db_con));
                                }
                                $checkRole = mysqli_query($db_con, "select * from tbl_bridge_role_to_um where role_id='$userRole'");

                                if (mysqli_num_rows($checkRole) <= 0) {
                                    mysqli_set_charset($db_con, "utf8");
                                    $roleAsin = mysqli_query($db_con, "insert into tbl_bridge_role_to_um(role_id,user_ids) values('$userRole','$user_id')"); //or die('Error' . mysqli_error($db_con));
                                } else {
                                    $rwCheckRole = mysqli_fetch_assoc($checkRole);
                                    $useridsRole = $rwCheckRole['user_ids'];
                                    if (!empty($useridsRole)) {
                                        $useridsRole = $useridsRole . ',' . $user_id;
                                    } else {
                                        $useridsRole = $user_id;
                                    }
                                    mysqli_set_charset($db_con, "utf8");
                                    $roleAsin = mysqli_query($db_con, "update tbl_bridge_role_to_um set user_ids ='$useridsRole' where role_id='$userRole'"); //or die('Error' . mysqli_error($db_con));
                                }

                                $groups = array_filter($groups, function($value) {
                                    return $value !== '';
                                });
                                if (!empty($groups)) {
                                    $flag = 0;
                                    foreach ($groups as $groupid) {
                                        $check = mysqli_query($db_con, "select * from tbl_bridge_grp_to_um where group_id='$groupid'");

                                        if (mysqli_num_rows($check) <= 0) {
                                            $grpmap = mysqli_query($db_con, "insert into tbl_bridge_grp_to_um(group_id,user_ids) values('$groupid','$user_id')"); //or die('Error' . mysqli_error($db_con));
                                            if ($grpmap) {
                                                $flag = 1;
                                            }
                                        } else {
                                            $rwCheck = mysqli_fetch_assoc($check);
                                            $userids = $rwCheck['user_ids'];
                                            if (!empty($userids)) {
                                                $userids = $userids . ',' . $user_id;
                                            } else {
                                                $userids = $user_id;
                                            }
                                            $grpmap = mysqli_query($db_con, "update tbl_bridge_grp_to_um set user_ids ='$userids' where group_id='$groupid'"); //or die('Error' . mysqli_error($db_con));
                                            if ($grpmap) {
                                                $flag = 1;
                                            }
                                        }
                                    }
                                    if ($flag == 1) {
                                        mysqli_set_charset($db_con, "utf8");
                                        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'user $firstname $lastname created.','$date',null,'$host/$ip','')"); //or die('error : ' . mysqli_error($db_con));
                                        $logid = mysqli_insert_id($db_con);
                                        $Cuser = $_SESSION['admin_user_name'] . ' ' . $_SESSION['admin_user_last'];
                                        require_once './mail.php';
                                        $subject = 'New user created';
                                        $mail = mailUserCreate($superiorEmail, $superiorName, $email, $firstname . ' ' . $lastname, $user_id, $password, $subject, $db_con, $projectName, $Cuser);
                                        if ($mail) {
                                            echo'<script> taskSuccess("createUser","' . $lang['User_created_sucesfuly'] . '"); </script>';
                                        }
                                    } else {
                                        $user = mysqli_query($db_con, "delete from tbl_user_master where user_id='$user_id'");
                                        $logDel = mysqli_query($db_con, "delete from tbl_ezeefile_logs where id='$logid'");
                                        echo'<script> taskFailed("createUser","' . $lang['Error_occurred_while_sending_mail'] . '"); </script>';
                                    }
                                }
                            }
                        }
                        mysqli_close($db_con);
                    }
                } else {
                    $chkUserMail = mysqli_query($db_con, "select * from tbl_user_master where user_email_id='$email'"); //or die('Error:' . mysqli_error($db_con));
                    if (mysqli_num_rows($chkUserMail) > 0) {
                        echo '<script>taskFailed("createUser", "User Already Registerd Using this Email Id !")</script>';
                    } else {

                        $create = mysqli_query($db_con, "insert into tbl_user_master (`user_email_id`, `first_name`, `last_name`, `password`, `designation`, `phone_no`, `profile_picture`, `superior_name`, `superior_email`, `user_created_date`, `emp_id`, `lang`, `dept_id`) values('$email','$firstname','$lastname',sha1('$password'),'$designation','$phone','$image','$superiorName','$superiorEmail','$date', '$empId', '$language', '$dept_ids')"); //or die('Error' . mysqli_error($db_con));
                        if ($create) {
                            $user_id = mysqli_insert_id($db_con);
                            if (!empty($slparentNameid)) {
                                $insertPerm = mysqli_query($db_con, "insert into tbl_storagelevel_to_permission (user_id,sl_id) values('$user_id','$slparentNameid')"); //or die('Error: sl permission' . mysqli_error($db_con));
                                //$insertPerm_run = mysqli_query($db_con, $insertStp) or die('Error:' . mysqli_error($db_con));
                            }
                            $checkRole = mysqli_query($db_con, "select * from tbl_bridge_role_to_um where role_id='$userRole'");

                            if (mysqli_num_rows($checkRole) <= 0) {
                                $roleAsin = mysqli_query($db_con, "insert into tbl_bridge_role_to_um(role_id,user_ids) values('$userRole','$user_id')"); //or die('Error' . mysqli_error($db_con));
                            } else {
                                $rwCheckRole = mysqli_fetch_assoc($checkRole);
                                $useridsRole = $rwCheckRole['user_ids'];
                                if (!empty($useridsRole)) {
                                    $useridsRole = $useridsRole . ',' . $user_id;
                                } else {
                                    $useridsRole = $user_id;
                                }
                                $roleAsin = mysqli_query($db_con, "update tbl_bridge_role_to_um set user_ids ='$useridsRole' where role_id='$userRole'"); //or die('Error' . mysqli_error($db_con));
                            }

                            $groups = array_filter($groups, function($value) {
                                return $value !== '';
                            });
                            if (!empty($groups)) {
                                $flag = 0;
                                foreach ($groups as $groupid) {
                                    $check = mysqli_query($db_con, "select * from tbl_bridge_grp_to_um where group_id='$groupid'");

                                    if (mysqli_num_rows($check) <= 0) {
                                        $grpmap = mysqli_query($db_con, "insert into tbl_bridge_grp_to_um(group_id,user_ids) values('$groupid','$user_id')"); //or die('Error' . mysqli_error($db_con));
                                        if ($grpmap) {
                                            $flag = 1;
                                        }
                                    } else {
                                        $rwCheck = mysqli_fetch_assoc($check);
                                        $userids = $rwCheck['user_ids'];
                                        if (!empty($userids)) {
                                            $userids = $userids . ',' . $user_id;
                                        } else {
                                            $userids = $user_id;
                                        }
                                        $grpmap = mysqli_query($db_con, "update tbl_bridge_grp_to_um set user_ids ='$userids' where group_id='$groupid'"); //or die('Error' . mysqli_error($db_con));
                                        if ($grpmap) {
                                            $flag = 1;
                                        }
                                    }
                                }
                                if ($flag == 1) {
                                    mysqli_set_charset($db_con, "utf8");
                                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'user $firstname $lastname created.','$date',null,'$host','')"); //or die('error : ' . mysqli_error($db_con));
                                    $logid = mysqli_insert_id($db_con);
                                    $Cuser = $_SESSION['admin_user_name'] . ' ' . $_SESSION['admin_user_last'];
                                    require_once './mail.php';
                                    $subject = 'New user created';
                                    mysqli_set_charset($db_con, "utf8");
                                    $userres = mysqli_query($db_con, "select first_name, last_name from tbl_user_master where user_id='$user_id'");
                                    $rowu = mysqli_fetch_assoc($userres);
                                    $mail = mailUserCreate($superiorEmail, $superiorName, $email, $rowu['first_name'] . ' ' . $rowu['last_name'], $user_id, $password, $subject, $db_con, $projectName, $Cuser);
                                    if ($mail ||$create) {
                                        echo'<script> taskSuccess("createUser","' . $lang['User_created_sucesfuly'] . '"); </script>';
                                    }
                                } else {
                                    $user = mysqli_query($db_con, "delete from tbl_user_master where user_id='$user_id'");
                                    $logDel = mysqli_query($db_con, "delete from tbl_ezeefile_logs where id='$logid'");
                                    echo'<script> taskFailed("createUser","' . $lang['Error_occurred_while_sending_mail'] . '"); </script>';
                                }
                            }
                        }
                    }
                    mysqli_close($db_con);
                }
            } else {
                echo'<script> taskFailed("createUser","Password & confirm password does not match"); </script>';
            }
        }
        ?>


    </body>
</html>