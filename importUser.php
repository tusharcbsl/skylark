<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    require_once './application/config/database.php';
    require_once './application/pages/head.php';
    require_once './application/config/validate_client_db.php';
    require_once './application/pages/function.php';

    error_reporting(0);
    
    mysqli_set_charset($db_con, "utf8");
    if ($rwgetRole['create_user'] != '1') {
        header('Location: ./index');
    }
    
    mysqli_set_charset($db_con, "utf8");
    $chkdefaultLang = mysqli_query($db_con, "SELECT lang_name FROM tbl_language WHERE default_language='1'");
    $rwchkdefault = mysqli_fetch_assoc($chkdefaultLang);
    $language = ((!empty($rwchkdefault['lang_name']) ? $rwchkdefault['lang_name'] : "English"));


    if (isset($_POST["ImportUser"], $_POST['token'])) {
        // $decKey = decryptLicenseKey($clientKey);
        // $decKey = explode("%", $decKey);
        // if (!empty($decKey[0]) && !empty($decKey[1]) && !empty($_SESSION['clientid'])) {
            if(true){
            /*
             * validate right user at right time
             */
            // if ($_SESSION['clientid'] != $decKey[1]) {
            //     header('Location: ./index');
            //     exit();
            // }
            /*
             * End of validation
             */
            $ip = $_POST['ip'];
            $filename = $_FILES["file"]["name"];
            $ftype = explode(".", $filename);
            if (($ftype[1] == "csv" || $ftype[1] == "CSV")) {
                $filename = $_FILES["file"]["tmp_name"];

                if ($_FILES["file"]["size"] > 0) {
                    $file = fopen($filename, "r");
                    $i = 0;

                    while (($getData = fgetcsv($file, 0, ",")) !== FALSE) {
                        $user_role = $_POST['userRole'];
                        $groups = $_POST['groups'];
                        $slparentNameid = $_POST['slparentName'];
                        //$fname = preg_replace('/[^A-Za-z0-9\- ]/', '', $getData[1]);
                        $fname = trim($getData[1]);
                        $fname = preg_replace('/[^\w$\x{0080}-\x{FFFF}@#$&!%()_ <>]+/u', "", $fname);
                        $fname = mysqli_real_escape_string($db_con, $fname);
                        
                        //$lname = preg_replace('/[^A-Za-z0-9\- ]/', '', $getData[2]);
                        $lname = trim($getData[2]);
                        $lname = preg_replace('/[^\w$\x{0080}-\x{FFFF}@#$&!%()_ <>]+/u', "", $lname);
                        $lname = mysqli_real_escape_string($db_con, $lname);
                        
                        $des = trim($getData[4]);
                        $des = preg_replace('/[^\w$\x{0080}-\x{FFFF}@#$&!%()_ <>]+/u', "", $des);
                        $des = mysqli_real_escape_string($db_con, $des);
                        
                        
                        $phone = preg_replace('/[^0-9\-]/', '', $getData[5]);
                        $phone = mysqli_real_escape_string($db_con, $phone);
                        //$email = $getData[0];
                        $email = $getData[0];
                        $pass = $getData[3];
                        $superiorName = preg_replace('/[^A-Za-z0-9\- ]/', '', $getData[6]);
                        $superiorName = mysqli_real_escape_string($db_con, $superiorName);
                        $superiorEmail = $getData[7];

                        
                    if (filter_var($superiorEmail, FILTER_VALIDATE_EMAIL)) {
                        
                        $resultSuperior = mysqli_query($db_con, "select user_id, first_name, last_name from  tbl_user_master where user_email_id='$superiorEmail'"); //Query get validity of particular company user
                        if(mysqli_num_rows($resultSuperior)>0){
                            $rowU  = mysqli_fetch_assoc($resultSuperior);

                            $superiorEmail = $superiorEmail;
                            $superiorName = $rowU['user_id'];

                        }
                    }
                    
                    if ($i == 0) {
                            
                        } else {
                            $email = $getData[0];
                            
                            $check_validity_qry = mysqli_query($db_valid_con, "select * from  tbl_client_master where client_id='$decKey[1]'"); //Query get validity of particular company user
                            $validity_date = mysqli_fetch_assoc($check_validity_qry); //fetch validity timestamp from client table
//                                            $plantype_qry = mysqli_query($db_valid_con, "select * from tbl_plantype where plantype='$validity_date[plan_type]'");
//                                            $total_user_allot = mysqli_fetch_assoc($plantype_qry);
                            $t_user = preg_replace("/[^0-9]/", "", $validity_date['total_user']); //total user allow  //total user allow 
                            $t_user += 1; //extra client super user
                            mysqli_set_charset($db_con, "utf8");
                            $validate_num_user = mysqli_query($db_con, "select count(user_email_id) as total_user from tbl_user_master ") or die('Error:' . mysqli_error($db_valid_con));
                            $total_user = mysqli_fetch_assoc($validate_num_user);
                            // print_r($total_user);
                            // if ($total_user['total_user'] >= $t_user) {
                                if(false){
                                //echo '<script>taskFailed("createUser", "Cannot Create User,User Limit Exceeded!")</script>';
                                $message[] = "Cannot Create User,User Limit Exceeded!";
                            } else {
                                mysqli_set_charset($db_con, "utf8");
                                $chkUserMail = mysqli_query($db_con, "select * from tbl_user_master where user_email_id='$email'") or die('Error:' . mysqli_error($db_valid_con));

                                if (mysqli_num_rows($chkUserMail) < 1) {
                                    if (isset($email) && !empty($email)) {
                                        // check if e-mail address is well-formed
                                        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                            //if (filter_var($superiorEmail, FILTER_VALIDATE_EMAIL)) {
                                                if (isset($fname) && !empty($fname)) {
                                                    if (isset($des) && !empty($des)) {
                                                        if (isset($pass) && !empty($pass)) {
                                                            if (isset($phone) && !empty($phone)) {
                                                                if (isset($phone) && strlen($phone) == '10') {
                                                                    $sql = "INSERT into tbl_user_master (user_email_id,first_name,last_name,password,designation,phone_no,superior_name,superior_email,user_created_date, lang) 
                                                   values ('" . $email . "','" . $fname . "','" . $lname . "','" . sha1($pass) . "','" . $des . "','" . $phone . "','" . $superiorName . "','" . $superiorEmail . "','" . date("Y-m-d h:i:s") . "', '$language')";

                                                                    $result = mysqli_query($db_con, $sql);
                                                                    $user_id = mysqli_insert_id($db_con);
                                                                    if ($result) {
                                                                        mysqli_set_charset($db_con, "utf8");
                                                                        if (!empty($slparentNameid)) {
                                                                            $insertPerm = mysqli_query($db_con, "insert into tbl_storagelevel_to_permission (user_id,sl_id) values('$user_id','$slparentNameid')") or die('Error: sl permission' . mysqli_error($db_con));
                                                                        }
                                                                        $checkRole = mysqli_query($db_con, "select * from tbl_bridge_role_to_um where role_id='$user_role'");

                                                                        if (mysqli_num_rows($checkRole) <= 0) {
                                                                            $roleAsin = mysqli_query($db_con, "insert into tbl_bridge_role_to_um(role_id,user_ids) values('$user_role','$user_id')") or die('Error' . mysqli_error($db_con));
                                                                        } else {
                                                                            $rwCheckRole = mysqli_fetch_assoc($checkRole);
                                                                            $useridsRole = $rwCheckRole['user_ids'];
                                                                            if (!empty($useridsRole)) {
                                                                                $useridsRole = $useridsRole . ',' . $user_id;
                                                                            } else {
                                                                                $useridsRole = $user_id;
                                                                            }
                                                                            $roleAsin = mysqli_query($db_con, "update tbl_bridge_role_to_um set user_ids ='$useridsRole' where role_id='$user_role'") or die('Error' . mysqli_error($db_con));
                                                                        }

                                                                        $groups = array_filter($groups, function($value) {
                                                                            return $value !== '';
                                                                        });
                                                                        if (!empty($groups)) {
                                                                            $flag = 0;
                                                                            foreach ($groups as $groupid) {
                                                                                $check = mysqli_query($db_con, "select * from tbl_bridge_grp_to_um where group_id='$groupid'");

                                                                                if (mysqli_num_rows($check) <= 0) {
                                                                                    $grpmap = mysqli_query($db_con, "insert into tbl_bridge_grp_to_um(group_id,user_ids) values('$groupid','$user_id')") or die('Error' . mysqli_error($db_con));
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
                                                                                    $grpmap = mysqli_query($db_con, "update tbl_bridge_grp_to_um set user_ids ='$userids' where group_id='$groupid'") or die('Error' . mysqli_error($db_con));
                                                                                    if ($grpmap) {
                                                                                        $flag = 1;
                                                                                    }
                                                                                }
                                                                            }
                                                                            if ($flag == 1) {
                                                                                mysqli_set_charset($db_con, "utf8");
                                                                                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'user $getData[1]  $getData[2]  created.','$date',null,'$host','')") or die('error : ' . mysqli_error($db_con));
                                                                                $Cuser = $_SESSION['admin_user_name'] . ' ' . $_SESSION['admin_user_last'];
                                                                                require_once './mail.php';
                                                                                $subject = 'New user created in ' . $projectName;
                                                                                $mail = mailUserCreate($getData[7], 'SU', $email, $getData[1] . ' ' . $getData[2], $user_id, $getData[3], $subject, $db_con, $projectName, $Cuser);
                                                                                if ($log || $mail) {
                                                                                    $message1[] = "$lang[User] (" . $email . ") $lang[created_successfully]";
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                } else {
                                                                    $message[] = "$lang[Phone] (" . $phone . ") $lang[must_ten_digits]";
                                                                }
                                                            } else {
                                                                $message[] = "$lang[phone_empty]";
                                                            }
                                                        } else {
                                                            $message[] = "$lang[Password_empty]";
                                                        }
                                                    } else {
                                                        $message[] = "$lang[designation_empty]";
                                                    }
                                                } else {
                                                    $message[] = "$lang[first_not_empty]";
                                                }
//                                            } else {
//                                                $message[] = "$lang[email_validate_super]";
//                                            }
                                        } else {
                                            $message[] = "$lang[email_validate_user]";
                                        }
                                    } else {
                                        $message[] = "$lang[email_not_empty]";
                                    }
                                } else {
                                    $message[] = "$lang[this_email_id] (" . $email . ") $lang[email_already_exist]";
                                }
                            }
                        }$i++;
                    }
                    if ($i == 1) {
                        // echo 'okkkkk';
                        $message[] = "$lang[Mandatory_empty]";
                    }

                    fclose($file);
                }
            } else {
                echo '<script>alert("Opps!! Wrong File Format")</script>';
            }
        } else {
            echo '<script>alert("Opps!! Invalid Client")</script>';
        }
    }
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
                                <li class="active"><?php echo $lang['Import_Users']; ?></li>
                                <li> <a href="#" data-toggle="modal" data-target="#help-modal" id="helpview" data="53" title="<?= $lang['help']; ?>"><i class="fa fa-question-circle"></i> </a></li>
                                <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                            </ol>
                        </div>
                        <div class="row" id="afterClickHide">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h4 class="header-title col-lg-6"> <?php echo $lang['Required_fields_are_marked_with_a']; ?> (<span style="color:red;">*</span>)</h4>
                                    <a href="assets/images/User.csv" download class="pull-right btn btn-primary"><i class="fa fa-download"></i> <?= $lang['downlod_sample_csv']; ?></a>
                                </div>
                                <div class="box-body">
                                    <div class="col-lg-6">
                                        <div class="card-box" style="background-color: #ebeff2;">
                                            <?php
                                            if (isset($message)) {
                                                foreach ($message as $messages) {
                                                    ?>
                                                    <span class="label label-danger text-font"><?php echo $messages; ?></span><br/>
                                                    <?php
                                                }
                                            }
                                            ?>
                                            <?php
                                            if (isset($message1)) {
                                                foreach ($message1 as $messages1) {
                                                    ?>
                                                    <span class="label label-success text-font"><?php echo $messages1; ?></span><br/>
                                                    <?php
                                                }
                                            }
                                            ?>
                                            <form method="post" enctype="multipart/form-data" id="form">
                                                <div class="form-group">
                                                    <label for="privilege"><?php echo $lang['Select_Group'] ?><span style="color:red;">*</span></label>
                                                    <select class="select2 select2-multiple" name="groups[]" multiple="multiple" multiple data-placeholder="<?php echo $lang['Select_Group'] ?>"  parsley-trigger="change" id="group" required="required">
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
                                                </div>
                                                <div class="form-group">
                                                    <label for="privilege"><?php echo $lang["Select_User's_Privilege"]; ?><span style="color:red;">*</span></label>
                                                    <select class="select2" name="userRole" parsley-trigger="change" id="privelege" required>
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
                                                            $rol = mysqli_query($db_con, "select role_id,user_role from tbl_user_roles where role_id in($roleids)order by user_role asc") or die('Error' . mysqli_error($db_con));
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
                                                    <label for="userName"><?php echo $lang["Select_Storage"]; ?></label> 
                                                    <select class="select2" name="slparentName"> 
                                                        <option disabled selected><?php echo $lang["Select_Storage"]; ?></option>
                                                        <?php
                                                        $perm = mysqli_query($db_con, "select sl_id from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]' group by sl_id");
                                                        while ($rwPerm = mysqli_fetch_assoc($perm)) {
                                                            $slperm = $rwPerm['sl_id'];
                                                            $sllevel = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slperm'");
                                                            $rwSllevel = mysqli_fetch_assoc($sllevel);
                                                            $level = $rwSllevel['sl_depth_level'];
                                                            findChild($slperm, $level, $slperm);
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="picture"><?php echo $lang["Select_Csv_File"]; ?><span style="color:red;">*</span></label>
                                                    <input type="file" name="file" class="filestyle" accept="" required>
                                                </div>
                                                <div class="form-group text-right m-b-0">
                                                    <button class="btn btn-primary waves-effect waves-light" type="submit" name="ImportUser" id="submitbuton">
                                                        <?php echo $lang["Submit"]; ?>
                                                    </button>
                                                    <a href="importUser" class="btn btn-danger waves-effect waves-light m-l-5">
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

            });
            $(".select2").select2();
        </script>

    </body>
</html>