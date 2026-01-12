<!DOCTYPE html>
<html>
    <?php
    //$path = $_SERVER['REQUEST_URI'];
    // $uri = $path; 
    require_once './loginvalidate.php';
    require_once './application/config/database.php';
    require_once './application/pages/head.php';


    //for user role
    $chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

    $rwgetRole = mysqli_fetch_assoc($chekUsr);


//    if ($rwgetRole['create_client'] != '1') {
//        header('Location: ./index');
//    }
    $sameGroupIDs = array();
    $group = mysqli_query($db_con, "select * from tbl_bridge_grp_to_um where find_in_set('$_SESSION[cdes_user_id]',user_ids)") or die('Error' . mysqli_error($db_con));
    while ($rwGroup = mysqli_fetch_assoc($group)) {
        $sameGroupIDs[] = $rwGroup['user_ids'];
    }

    $sameGroupIDs = implode(',', $sameGroupIDs);
    $sameGroupIDs = explode(",", $sameGroupIDs);
    $sameGroupIDs = array_unique($sameGroupIDs);
    sort($sameGroupIDs);
    $sameGroupIDs = implode(',', $sameGroupIDs);
    ?>
    <!--Form Wizard-->
    <link rel="stylesheet" type="text/css" href="assets/plugins/jquery.steps/css/jquery.steps.css" />
    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <!-- Plugin Css-->
    <link rel="stylesheet" href="assets/plugins/magnific-popup/css/magnific-popup.css" />
    <link rel="stylesheet" href="assets/plugins/jquery-datatables-editable/datatables.css" />
    <!--Form Wizard-->
    <link rel="stylesheet" type="text/css" href="assets/plugins/jquery.steps/css/jquery.steps.css" />
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
                                        <a href="AuditTrail-workflow"> Client Creation </a>
                                    </li>
                                    <li class="active">
                                        Add Client
                                    </li>
                                </ol>
                            </div>
                        </div>
                        <div class="box box-primary">
                            <div class="card-box">

                                <div class="stepwizard">
                                    <div class="stepwizard-row setup-panel">
                                        <div class="stepwizard-step">
                                            <a href="#step-1" type="button" class="btn btn-primary btn-circle">1</a>
                                            <h4>Step 1</h4>
                                        </div>
                                        <div class="stepwizard-step">
                                            <a href="#step-2" type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
                                            <h4>Step 2</h4>
                                        </div>
                                        <!--                                        <div class="stepwizard-step">
                                                                                    <a href="#step-3" type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
                                                                                    <h4>VERIFY & COMPLETE</h4>
                                                                                </div>-->
                                        <?php if ($rwgetRole['workflow_initiate_file'] == '1' || $rwgetRole['initiate_file'] == '1') { ?>
                                            <!--                                            <div class="stepwizard-step">
                                                                                            <a href="#step-4" type="button" class="btn btn-default btn-circle" disabled="disabled">4</a>
                                                                                            <h4>Term and Conditions</h4>
                                                                                        </div>-->
                                        <?php } ?>
                                    </div>
                                </div>
                                <form method="post">


                                    <div class="row setup-content" id="step-1">
                                        <div class="col-xs-12 mrt well">
                                            <div class="col-md-6 m-t-20">
                                                <div class="form-group">
                                                    <label for="userName">First Name<span style="color:red;">*</span></label>
                                                    <input type="text" name="firstname" parsley-trigger="change"  placeholder="Enter First Name" class="form-control" id="userName" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6 m-t-20">
                                                <div class="form-group">
                                                    <label for="lastName">Last Name</label>
                                                    <input type="text" name="lastname" parsley-trigger="change" placeholder="Enter Last Name" class="form-control" id="lastName">
                                                </div>
                                            </div>
                                            <div class="col-md-6 m-t-20">
                                                <div class="form-group">
                                                    <label for="phone">Phone<span style="color:red;">*</span></label>
                                                    <input type="text" name="phone" parsley-trigger="change" data-parsley-type="number" data-parsley-minlength="10" data-parsley-maxlength="10"  placeholder="Enter Phone Number" class="form-control" id="phone" maxlength="10" required>
                                                </div>
                                            </div>

                                            <div class="col-md-6 m-t-20">
                                                <div class="form-group">
                                                    <label for="designation">Company Name<span style="color:red;">*</span></label>
                                                    <input type="text" name="cname" parsley-trigger="change" required placeholder="Enter Company Name" class="form-control" id="designation">
                                                </div>
                                            </div>
                                            <!--<div class="col-md-6 form-group m-t-20">
                                               <label style="font-weight: 600; font-size: 20px;">(pdf, jpg, png, gif, tif/tiff, mp3, mp4 )</label>
                                           </div>-->
                                        </div>
                                        <button class="btn btn-primary nextBtn pull-right" type="button" id="verify-comp">Next</button>
                                    </div>
                                    <div class="row setup-content" id="step-2">
                                        <div class="col-xs-12 mrt well">
                                            <div class="col-md-6 m-t-20">
                                                <div class="form-group">
                                                    <label for="emailAddress">Email address<span style="color:red;">*</span></label>
                                                    <input type="email" name="email" parsley-trigger="change" parsley-type="email"  placeholder="Enter Email ID" class="form-control" id="emailAddress" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6 m-t-20">
                                                <div class="form-group">
                                                    <label for="pass1">Password<span style="color:red;">*</span></label>
                                                    <input id="pass1" name="password" type="password"  placeholder="Password" required class="form-control" data-parsley-minlength="8" data-parsley-minlength="8"    data-parsley-errors-container=".errorspannewpassinput"    data-parsley-required-message="Please enter your password."    data-parsley-uppercase="1"    data-parsley-lowercase="1"    data-parsley-number="1"    data-parsley-special="1">
                                                </div>
                                            </div>
                                            <div class="col-md-6 m-t-20">
                                                <div class="form-group">
                                                    <label for="passWord2">Confirm Password<span style="color:red;">*</span></label>
                                                    <input data-parsley-equalto="#pass1" type="password" required placeholder="Confirm Password" class="form-control" id="passWord2">
                                                </div>
                                            </div>

                                            <div class="col-md-6 m-t-20">
                                                <div class="form-group">
                                                    <label for="privilege">Select Plan Type<span style="color:red;">*</span></label>
                                                    <select class="select2 " name="plantype"  data-placeholder="Select Plan Type"  parsley-trigger="change" id="group" required="required">
                                                        <option value="">Select Plan Type</option>  
                                                        <option value="5 User-5 GB">5 User-5 GB</option>  
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6 m-t-20">
                                                <label for="privilege">Select Validity<span style="color:red;">*</span></label>
                                                <div class="form-group">

                                                    <div class="col-md-6">
                                                        <select class="select2 select2-multiple " name="validupto_month"  data-placeholder="Select Month"  parsley-trigger="change" id="month" required="required">

                                                            <option value="0 Month">Select Month</option>  
                                                            <option value="1 day">1 Month</option>  
                                                            <option value="2 month">2 Month</option>  
                                                            <option value="3 month">3 Month</option>  
                                                            <option value="4 month">4 Month</option>  
                                                            <option value="5 month">5 Month</option>  
                                                            <option value="6 month">6 Month</option> 
                                                            <option value="7 month">7 Month</option> 
                                                            <option value="8 month">8 Month</option> 
                                                            <option value="9 month">9 Month</option> 
                                                            <option value="10 month">10 Month</option>
                                                            <option value="11 month">11 Month</option> 
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <select class="select2 select2-multiple " name="validupto_year"  data-placeholder="Select Year"  parsley-trigger="change" id="year" required="required">

                                                            <option value="0 Year">Select Year</option> 
                                                            <option value="1 Year">1 Year</option>  
                                                            <option value="2 Year">2 Year</option>  
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 m-t-20">
                                                <div class="form-group">
                                                    <label for="privilege">Select Product Type<span style="color:red;">*</span></label>
                                                    <select class="select2" name="product_type"  data-placeholder="Select Product Plan"  parsley-trigger="change" id="group" required="required">
                                                        <option value="">Select Product Type</option>   
                                                        <?php
                                                        $qry = mysqli_query($db_con, "select roleids  from tbl_bridge_grp_to_um where group_id='1'") or die(mysqli_error($db_con));
                                                        $roleids = mysqli_fetch_assoc($qry);

                                                        $role = mysqli_query($db_con, "select user_role,role_id from  tbl_user_roles where role_id IN($roleids[roleids])")or die(mysqli_error($db_con));
                                                        while ($rows = mysqli_fetch_assoc($role)) {
                                                            ?>
                                                            <option value="<?= $rows['role_id'] ?>"><?= $rows['user_role'] ?></option>   
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6 m-t-20">
                                                <div class="form-group">
                                                 <label for="picture">Profile Picture<!--<span style="color:red;">*</span>--></label>
                                                    <input type="file" name="image" class="filestyle" accept="image/*">
                                                </div>
                                            </div>
                                            <!--<div class="col-md-6 form-group m-t-20">
                                               <label style="font-weight: 600; font-size: 20px;">(pdf, jpg, png, gif, tif/tiff, mp3, mp4 )</label>
                                           </div>-->
                                        </div>
                                        <button type="submit" class="btn btn-primary nextBtn pull-right" type="button"  name="createuser">Submit</button>
                                    </div>

                                </form>
                            </div>
                        </div>

                        <!-- End row -->


                    </div> <!-- content -->
                    <?php require_once './application/pages/footer.php'; ?>
                </div>
            </div>
            <!-- END wrapper -->
            <?php require_once './application/pages/footerForjs.php'; ?>
            <script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
            <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
            <script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
            <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>

            <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>
            <!--Form Wizard-->
            <script src="assets/plugins/jquery.steps/js/jquery.steps.min.js" type="text/javascript"></script>
            <script src="assets/jscustom/wizard.js"></script>


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
                $("#month").change(function () {
                    var valu = $(this).val();
                    if (valu != "")
                    {
                        $("#year").removeAttr("required");
                    } else {
                        $("#year").attr("required", "required");
                    }
                })
                $("#year").change(function () {
                    var valu = $(this).val();
                    if (valu != "")
                    {
                        $("#month").removeAttr("required");
                    } else {
                        $("#month").attr("required", "required");
                    }
                })
            </script>


            <!-------------->
    </body>
</html>

<?php
if (isset($_POST['createuser'])) {
    // Set autocommit to off
    mysqli_autocommit($db_con, FALSE);

    $firstname = filter_input(INPUT_POST, "firstname");
    $firstname = preg_replace("/[^a-zA-Z ]/", "", $firstname); //filter name
    $firstname = mysqli_real_escape_string($db_con, $firstname);

    $lastname = filter_input(INPUT_POST, "lastname");
    $lastname = preg_replace("/[^a-zA-Z ]/", "", $lastname); //filter name
    $lastname = mysqli_real_escape_string($db_con, $lastname);
    $email = filter_input(INPUT_POST, "email");
    $email = preg_replace("/[^a-zA-Z0-9_@.-]/", "", $email); //filter email
    $email = mysqli_real_escape_string($db_con, $email);

    $phone = filter_input(INPUT_POST, "phone");
    $phone = preg_replace("/[^0-9]/", "", $phone); //filter phone
    $phone = mysqli_real_escape_string($db_con, $phone);

    $company = filter_input(INPUT_POST, "cname");
    $company = preg_replace("/[^0-9A-Za-z]/", "", $company); //filter phone
    $company = mysqli_real_escape_string($db_con, $company);
    $password = filter_input(INPUT_POST, "password");
    $password = mysqli_real_escape_string($db_con, $password);
    $validupto = filter_input(INPUT_POST, "validupto_month");
    $validupto = mysqli_real_escape_string($db_con, $validupto);
    $validupto_year = filter_input(INPUT_POST, "validupto_year");
    $validupto = strtotime(date("Y-m-d", strtotime("+" . $validupto . " " . $validupto_year))); //end of validity in time stamp
    $plantype = filter_input(INPUT_POST, "plantype");
    $plantype = mysqli_real_escape_string($db_con, $plantype);
    $plantype = preg_replace("/[^0-9A-Za-z- ]/", "", $plantype); //filter phone
    $product_type = filter_input(INPUT_POST, "product_type");
    $product_type = mysqli_real_escape_string($db_con, $product_type);
    $image = addslashes(file_get_contents($_FILES['image']['tmp_name']));


    $chkUserMail = mysqli_query($db_con, "select * from  tbl_aggregate_user_master where email='$email'") or die('Error:' . mysqli_error($db_con));
    if (mysqli_num_rows($chkUserMail) > 0) {
        echo '<script>taskFailed("client_create", "User Already Registerd Using this Email Id !")</script>';
    } else {
        $create_client = mysqli_query($db_con, "insert into `tbl_client_master`(`fname`,`lname`,`email`,`company`,`password`,`profile`,`plan_type`,`valid_upto`,`product_type`)values('$firstname','$lastname','$email','$company',sha1('$password'),'$image','$plantype','$validupto','$product_type')")or die(mysqli_error($db_con));
        $lastinsertid = mysqli_insert_id($db_con);
        if ($create_client) {
            $client_status = createNewDB($company, $lastinsertid, $email, $password, $db_con, $date, $firstname, $lastname, $phone, $image, $dbHost, $dbUser, $dbPwd, $product_type, $projectName);
            if ($client_status['status']) {


                if (array_key_exists("aggrigate_id", $client_status)) {
                    $connection = $client_status['connect'];
                    if (!empty($client_status[db_name])) {
                        $qry_remove_db = mysqli_query($connection, "DROP DATABASE $client_status[db_name]");
                        $qry_remove_aggregate_id = mysqli_query($db_con, "Delete From `tbl_aggregate_user_master` where tbl_ag_id=$client_status[aggrigate_id]");
                        $qry_remove_client_id = mysqli_query($db_con, "Delete From `tbl_client_master` where client_id='$lastinsertid'");
                    }
                } elseif (array_key_exists("connect", $client_status)) {
                    $connection = $client_status['connect'];
                    if (!empty($client_status[db_name])) {
                        $qry_remove_db = mysqli_query($connection, "DROP DATABASE $client_status[db_name]");
                        $qry_remove_client_id = mysqli_query($db_con, "Delete From `tbl_client_master` where client_id='$lastinsertid'");
                    }
                } else {

                    $qry_remove_client_id = mysqli_query($db_con, "Delete From `tbl_client_master` where client_id='$lastinsertid'");
                }
                mysqli_rollback($db_con);
                echo '<script>taskFailed("client_create", "Company Creation Failed!")</script>';
            } else {
                mysqli_commit($db_con);
                echo'<script> taskSuccess("client_create","Company created successfully"); </script>';
            }
//             
        } else {
            echo '<script>taskFailed("client_create", "Company Create Failed!")</script>';
        }
//        }
    }
}

function createNewDB($company, $lastinsertid, $email, $password, $db_con, $date, $firstname, $lastname, $phone, $image, $dbHost, $dbUser, $dbPwd, $product_type, $projectName) {
    $conn = @mysqli_connect($dbHost, $dbUser, $dbPwd);
    if ($conn) {
        // Create database

        $ddb_name = "Ezee_" . $company . "_" . strtotime($date); //name of database


        $result = mysqli_query($conn, "CREATE DATABASE $ddb_name"); //create new database for particular client
        if ($result) {
            $conn = new mysqli($dbHost, $dbUser, $dbPwd, $ddb_name); // connection with dynamic database

            $query = '';
            $sqlScript = file('db_file/ezeefile_fresh.sql'); //fresh database file
            foreach ($sqlScript as $line) {

                $startWith = substr(trim($line), 0, 2);
                $endWith = substr(trim($line), -1, 1);

                if (empty($line) || $startWith == '--' || $startWith == '/*' || $startWith == '//') {
                    continue;
                }

                $query = $query . $line;
                if ($endWith == ';') {
                    $tbl_qry = mysqli_query($conn, $query);
                    $query = '';
                    if ($tbl_qry) {
                        
                    } else {
                        return array("status" => "False", "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "qry_error:" . $query);
                    }
                }
            }
            $qry = mysqli_query($db_con, "insert into `tbl_aggregate_user_master` (`email`,`password`,`client_id`,`db_name`)values('$email',sha1('$password'),'$lastinsertid','$ddb_name')"); //insert in aggregate table
            if ($qry) {
                $aggrigate_id = mysqli_insert_id($db_con);
                $role_fieldName = array();
                $data_role_col = mysqli_query($db_con, "show COLUMNS FROM `tbl_user_roles`")or die(mysqli_error($db_con));
                while ($row = mysqli_fetch_assoc($data_role_col)) {
                    $role_fieldName[] = $row['Field'];
                }
                unset($role_fieldName[0]); // remove role id becoz it is autoincrement
                $data_cols_role = implode(",", $role_fieldName);
                $result_cols = array();
                $selected_roles_data = mysqli_query($db_con, "select $data_cols_role from `tbl_user_roles` where role_id='$product_type'");
                $newdata = mysqli_fetch_all($selected_roles_data);
                $new_imploded_data = "'" . implode("'" . "," . "'", $newdata[0]) . "'";
                //  echo  "insert into `tbl_user_roles`($data_cols_role)values($new_imploded_data)";
                $Insert_New_User = mysqli_query($conn, "insert into `tbl_user_roles`($data_cols_role)values($new_imploded_data)");
                $new_user_role = mysqli_insert_id($conn);
                if ($Insert_New_User) {
                    $create = mysqli_query($conn, "insert into tbl_user_master (`user_email_id`, `first_name`, `last_name`, `password`, `designation`, `phone_no`, `profile_picture`, `superior_name`, `superior_email`, `user_created_date`, `emp_id`,`usr_acvt_dacvt`) values('$email','$firstname','$lastname',sha1('$password'),'null','$phone','$image','null','null','$date', 'null','1')");
                    $user_id = mysqli_insert_id($conn);
                    if ($create) {

                        $grp_to_um = mysqli_query($conn, "INSERT INTO `tbl_bridge_role_to_um` (`role_id`,`user_ids`) VALUES ('$new_user_role','$user_id')");
                        if ($grp_to_um) {
                            $create_Root_Strg = mysqli_query($conn, "insert into `tbl_storage_level`(`sl_id`,`sl_name`,`sl_parent_id`,`sl_depth_level`) values('113','$company','0',0)");
                            if ($create_Root_Strg) {
                                $storage_permission = mysqli_query($conn, "insert into `tbl_storagelevel_to_permission`(`user_id`,`sl_id`) values('$user_id','113')");
                                if ($storage_permission) {
                                    $company = preg_replace('/[^A-Za-z0-9\-]/', '', $company);
                                    $ftp_server = "192.168.2.112"; //connection to ftp
                                    $ftp_conn = ftp_connect($ftp_server);
                                    $login = ftp_login($ftp_conn, "Administrator", "yadav@1234"); //login to ftp server
                                    //check befor folder exist or not
                                    if (ftp_mkdir($ftp_conn, $company)) {
                                        // echo "Successfully created $company";
                                        require './mail.php';
                                        $mail = mailClientCreate($email, $password, $projectName, $company);
                                        if ($mail) {
                                            
                                        } else {
                                            return array("status" => "False", "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "Mail Not Sent", "aggrigate_id" => $aggrigate_id);
                                        }
                                    } else {
                                        return array("status" => "False", "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "Client database Storage Level Permission Problem(Could not connect to $ftp_server)", "aggrigate_id" => $aggrigate_id);
                                    }
                                    // then do something...
                                    // close connection
                                    ftp_close($ftp_conn);
                                } else {
                                    return array("status" => "False", "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "Client database Storage Level Permission Problem", "aggrigate_id" => $aggrigate_id);
                                }
                            } else {
                                return array("status" => "False", "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "Client database Storage Level Problem", "aggrigate_id" => $aggrigate_id);
                            }
                        } else {
                            return array("status" => "False", "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "Client database Group To User  Master Problem", "aggrigate_id" => $aggrigate_id);
                        }
                    } else {
                        return array("status" => "False", "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "Client database User Master Problem", "aggrigate_id" => $aggrigate_id);
                    }
                } else {
                    return array("status" => "False", "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "Client User Role Creation Table", "aggrigate_id" => $aggrigate_id);
                }
            } else {
                return array("status" => "False", "msg" => "Error creating Company", "connect" => $conn, "db_name" => $ddb_name, "aggrigate_id" => $aggrigate_id, "dev_msg" => "Aggregate Table Error");
            }
            mysqli_close($conn);
            //return TRUE;
        } else {
            return array("status" => "False", "msg" => "Error creating Company", "dev_msg" => "Database Creation Failed");
        }
    } else {
        return array("status" => "False", "msg" => "Error creating Connection", "dev_msg" => "Connection Failed");
    }
}
