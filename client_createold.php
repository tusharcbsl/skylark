<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    require_once './application/config/database.php';
    require_once './application/pages/head.php';

    if ($rwgetRole['create_client'] != '1') {
        header('Location: ./index');
    }
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
                            <p id="error"></p>
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
                                        
                                    </div>
                                </div>
                                <form method="post">
                                    <div class="row setup-content" id="step-1">
                                        <div class="col-xs-12 mrt well">
                                            <div class="col-md-6 m-t-20">
                                                <div class="form-group">
                                                    <label for="userName">First Name<span style="color:red;">*</span></label>
                                                    <input type="text" name="firstname" parsley-trigger="change"  placeholder="Enter First Name" class="form-control" id="userName" required data-parsley-pattern="^[A-Za-z ]*$" >
                                                </div>
                                            </div>
                                            <div class="col-md-6 m-t-20">
                                                <div class="form-group">
                                                    <label for="lastName">Last Name</label>
                                                    <input type="text" name="lastname" parsley-trigger="change" placeholder="Enter Last Name" class="form-control" id="lastName" data-parsley-pattern="^[A-Za-z ]*$" >
                                                </div>
                                            </div>
                                            <div class="col-md-6 m-t-20">
                                                <div class="form-group">
                                                    <label for="phone">Phone<span style="color:red;">*</span></label>
                                                    <input type="text" name="phone" parsley-trigger="change" data-parsley-type="number"   data-parsley-minlength="10" data-parsley-minlength-message="This value is too short. It should have 10 digits only" placeholder="Enter Phone Number" class="form-control"  id="phone" maxlength="10" required>
                                                </div>
                                            </div>

                                            <div class="col-md-6 m-t-20">
                                                <div class="form-group">
                                                    <label for="designation">Company Name<span style="color:red;">*</span></label>
                                                    <span id="comerror" style="color: red;"></span>

                                                    <input type="text" name="cname" parsley-trigger="change" required placeholder="Enter Company Name" class="form-control" id="cname" onkeyup="checkCompanyName();" >
                                                </div>
                                            </div>
                                            <div class="col-md-6 m-t-20">
                                                <div class="form-group">
                                                    
                                                    <label for="designation">Enter Subdomain<span style="color:red;">*</span></label>
                                                    <span id="domainerror" style="color: red;"></span>
                                                    <input type="text" name="subd" parsley-trigger="change" required placeholder=" Enter Subdomain" class="form-control" id="subd" autocomplete="off" data-parsley-pattern="^[a-zA-Z0-9 ]+$" data-parsley-pattern-message="Domain name should only contain alphates" onkeyup="checkSubDomain();" >
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
                                                    
                                                     <span id="mailerror" style="color: red;"></span>

                                                    <input type="email" name="email" parsley-trigger="change" parsley-type="email"  placeholder="Enter Email ID" class="form-control" id="emailAddress" required onkeyup="checkEmailId();" >
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
                                                <div class="form-group col-md-6">
                                                    <label for="pass1">Total User<span style="color:red;">*</span></label>
                                                    <input  name="nouser" type="number"  placeholder="Total User" required class="form-control" >
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="pass1">Concurrent User<span style="color:red;">*</span></label>
                                                    <input  name="con_user" type="number"  placeholder="Concurrent User" required class="form-control" >
                                                </div>
                                            </div>
                                            <div class="col-md-6 m-t-20">
                                                <div class="form-group">
                                                    <label for="pass1">Total Memory<span style="color:red;">*</span></label>
                                                    <input  name="tomemory" type="number"  placeholder="Total Memory" required class="form-control" >
                                                </div>
                                            </div>
                                            <div class="col-md-12 m-t-20">
                                                <label for="privilege">Select Validity<span style="color:red;">*</span></label>
                                                <div class="form-group">

                                                    <div class="col-md-6">
                                                        <select class="select2" name="validupto_month"  data-placeholder="Select Month"  parsley-trigger="change" id="month" required="required">

                                                            <option value="">Select Month</option>  
                                                            <option value="7 day">7 Days</option>   
															<option value="15 day">15 Days</option>   
                                                            <option value="1 month">1 Month</option>  
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

                                                            <option value="">Select Year</option> 
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
                                                         //sk@210219 : static array for mapping role with domain.
                                                        $plan_domain_map=array('ezeefile'=>2,'ezeeprocess'=>3,'ezeeoffice'=>4);
                                                        $role = mysqli_query($db_con, "select user_role,role_id from  tbl_user_roles where role_id IN($roleids[roleids])")or die(mysqli_error($db_con));
                                                        while ($rows = mysqli_fetch_assoc($role)) {
                                                            ?>
                                                            <option <?php echo ($plan_domain_map[$doname[0]]==$rows['role_id'] ? 'selected':'')?> value="<?= $rows['role_id'] ?>"><?= $rows['user_role'] ?></option>   
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
            <script src="assets/jsCustom/wizard.js"></script>


            <script type="text/javascript">

                $(document).ready(function () {
                    $form = $('form').parsley();
                    $("#verify-comp").click(function () {
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

                jQuery(document).ready(function ($) {
                    $('#subd').bind("keypress change", function (e) {
                        var regex = new RegExp("^[0-9a-zA-Z_\]+$");
                        var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
                        if (regex.test(str)) {
                            return true;
                        }
                        e.preventDefault();
                        return false;
                    });
                });

                function checkSubDomain(){

                    var subd = $("#subd").val();

                    $.post("application/ajax/common.php", {subd: subd, 'action':'checkSubDomain'}, function (result, status) {

                        if (status == 'success') {

                            var rt =  result.split("^");

                            if(parseInt(rt[1])=="1"){

                                $("#domainerror").html("Subdomain already exist");
                                $("#verify-comp").attr("disabled", true);
                            }else{

                                $("#domainerror").html("");
                                $("#verify-comp").attr("disabled", false);
                            }


                           // $("#stp").html(result);


                        }
                    });

                }

                function checkCompanyName(){

                    var cname = $("input[name='cname']").val();

                    $.post("application/ajax/common.php", {cname: cname, 'action':'checkCompanyName'}, function (result, status) {

                        if (status == 'success') {

                            var rt =  result.split("^");

                            if(parseInt(rt[1])=="1"){

                                $("#comerror").html("Company name already exist");
                                $("#verify-comp").attr("disabled", true);
                            }else{

                                $("#comerror").html("");
                                $("#verify-comp").attr("disabled", false);
                            }


                           // $("#stp").html(result);


                        }
                    });

                }

                function checkEmailId(){

                    var email = $("input[name='email']").val();

                    $.post("application/ajax/common.php", {email: email, 'action':'checkEmailId'}, function (result, status) {

                        if (status == 'success') {

                            var rt =  result.split("^");

                            if(parseInt(rt[1])=="1"){

                                $("#mailerror").html("Email address already exist");
                                $("button[name='createuser']").attr("disabled", true);
                            }else{

                                $("#mailerror").html("");
                                $("button[name='createuser']").attr("disabled", false);
                            }


                           // $("#stp").html(result);


                        }
                    });

                }

				 $("#subd").on('keyup', function(e) {
					 
					  var value = $(this).val().toLowerCase();
					  $(this).val(value);
				});

            </script>


            <!-------------->
    </body>
</html>
<?php

if (isset($_POST['createuser'])) {
    echo "R1";
    define('domain', $domain_name);
    // Set autocommit to off
    //mysqli_autocommit($db_con,FALSE);  
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
    // $company = preg_replace("/[^0-9A-Za-z]/", "", $company); //filter phone
    $company = mysqli_real_escape_string($db_con, $company);
    $password = filter_input(INPUT_POST, "password");
    $password = mysqli_real_escape_string($db_con, $password);
    $validupto = filter_input(INPUT_POST, "validupto_month");
    $validupto = mysqli_real_escape_string($db_con, $validupto);
    $validupto_year = filter_input(INPUT_POST, "validupto_year");
    $validupto = strtotime(date("Y-m-d", strtotime("+" . $validupto . " " . $validupto_year))); //end of validity in time stamp
    $plantype = filter_input(INPUT_POST, "plantype");
    $product_type = filter_input(INPUT_POST, "product_type");
    $product_type = mysqli_real_escape_string($db_con, $product_type);

    $total_user = $_POST['nouser'];
    $concurrent_user = $_POST['con_user'];
    
    $total_memory = $_POST['tomemory'];

    $subDomain = filter_input(INPUT_POST, "subd");
    $subDomain = preg_replace("/[^a-zA-Z0-9_ ]/", "", strtolower($subDomain)); //filter name

    $FullSubDomain = $subDomain . "." . domain; //new subdomain
    $subDomain = mysqli_real_escape_string($db_con, $subDomain);
    $image = addslashes(file_get_contents($_FILES['image']['tmp_name']));

    
    $chkDuplicateCompany = mysqli_query($db_con, "select * from  `tbl_client_master` where company='$company' or email='$email' or subdomain='$FullSubDomain'");
    if (mysqli_num_rows($chkDuplicateCompany) > 0) {
        
        $fetchValidation = mysqli_fetch_assoc($chkDuplicateCompany);
        if ($fetchValidation['company'] == $company) {
            echo '<script>alert("Company Already Exist!")</script>';
        } elseif ($fetchValidation['email'] == $email) {
            echo '<script>alert("Email Already Exist!")</script>';
        } elseif ($fetchValidation['subdomain'] == $FullSubDomain) {
            echo '<script>alert("Sub Domain Already Exist!")</script>';
        } else {
            echo '<script>alert("User Already Registerd Using this Email Id Or Company Already Exist or Domain Name!")</script>';
        }
    } else {
        echo "R3";

//        $url = 'http://192.168.2.55/workspace/ezeefile_km/trunk/Api/addcustomer.php';
//        $params = array(
//            'firstname' => $firstname,
//            'lastname' => $lastname,
//            'email' => $email,
//            'phone' => $phone,
//            'company_name' => $company,
//            'noofusers' => $total_user,
//            'valid_upto' => $validupto,
//            'tomemory' => $total_memory,
//            'product_id' => $product_type,
//            'password' => $password
//        );
//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); //ssl verify of razor
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
//        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout after 30 seconds
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//        curl_setopt($ch, CURLOPT_POST, true);
//        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
//
//        $result = curl_exec($ch);
//        $response = json_decode($result, true);
//        if (curl_errno($ch) !== 0) {
//            error_log('cURL error when connecting to ' . $url . ': ' . curl_error($ch));
//        }
//        curl_close($ch);
//        if ($response['response'] == 1) {
//            if (!empty($response['customerid'])) {
//                $coustomer_id = $response['customerid'];
        $coustomer_id = 0;
        $create_client = mysqli_query($db_con, "insert into `tbl_client_master`(`fname`,`lname`,`email`,`company`,`password`,`profile`,`plan_type`,`valid_upto`,`product_type`,`total_memory`,`total_user`,`concurrent_user`,`subdomain`)values('$firstname','$lastname','$email','$company',sha1('$password'),'$image','$plantype','$validupto','$product_type','$total_memory','$total_user','$concurrent_user','$FullSubDomain')")or die(mysqli_error($db_con));
        $lastinsertid = mysqli_insert_id($db_con);

        
        
        if ($create_client) {
            //echo "R4";
            $client_status = createNewDB($company, $lastinsertid, $email, $password, $db_con, $date, $firstname, $lastname, $phone, $image, $dbHost, $dbUser, $dbPwd, $product_type, $projectName, $coustomer_id, $mainDirectorySrc, $subDomain, $FullSubDomain,$crtinfo,$doname);
            //print_r($client_status);
            if ($client_status['status']) {
                if (array_key_exists("connect", $client_status)) {
                    $connection = $client_status['connect'];
                    if (!empty($client_status[db_name])) {
                        $qry_remove_db = mysqli_query($connection, "DROP DATABASE $client_status[db_name]");
                        $qry_remove_client_id = mysqli_query($db_con, "Delete From `tbl_client_master` where client_id='$lastinsertid'");
                    }
                } else {

                    $qry_remove_client_id = mysqli_query($db_con, "Delete From `tbl_client_master` where client_id='$lastinsertid'");
                }
                //mysqli_rollback($db_con);
                echo '<script>taskFailed("client_create", "Company Creation Failed!")</script>';
            } else {
                $qry_update_crm = mysqli_query($db_con, "update tbl_client_master set crm_cid='$coustomer_id' where client_id='$lastinsertid'");
                //mysqli_commit($db_con);
                echo'<script> taskSuccess("client_create","Company created successfully"); </script>';
            }
//             
        } else {
            echo '<script>taskFailed("client_create", "Company Create Failed!")</script>';
        }
//            }
//        } else {
//            echo '<script>taskFailed("client_create", "' . $response['message'] . '!")</script>';
//        }
    }
}

function generateLicenseKey($clientdb, $clientId) {

    $key = '987654123';
    $plaintext = $clientdb . '%' . $clientId;
    $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
    $iv = openssl_random_pseudo_bytes($ivlen);
    $ciphertext_raw = openssl_encrypt($plaintext, $cipher, $key, $options = OPENSSL_RAW_DATA, $iv);
    $ciphertext = base64_encode($iv . /* $hmac. */$ciphertext_raw);

    return $ciphertext;
}

function decryptLicenseKey($licenseKey) {
    $key = '987654123';
    $c = base64_decode($licenseKey);
    $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
    $iv = substr($c, 0, $ivlen);
//$hmac = substr($c, $ivlen, $sha2len=32);
    $ciphertext_raw = substr($c, $ivlen/* +$sha2len */);
    $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, $options = OPENSSL_RAW_DATA, $iv);
    return $original_plaintext;
}

function createNewDB($company, $lastinsertid, $email, $password, $db_con, $date, $firstname, $lastname, $phone, $image, $dbHost, $dbUser, $dbPwd, $product_type, $projectName, $coustomer_id, $mainDirectorySrc, $subDomain, $FullSubDomain,$crtinfo,$doname) {
    $fetchMainColQry = mysqli_query($db_con, "show tables");
    if (mysqli_num_rows($fetchMainColQry) > 0) {
        $mainDbTables = array();
        while ($fetchAllColsMain = mysqli_fetch_array($fetchMainColQry)) {
            array_push($mainDbTables, $fetchAllColsMain['0']);
        }
        /*
         * Unset unneccessary tables values
         */
        array_splice($mainDbTables, array_search("tbl_aggregate_user_master", $mainDbTables), 1);
        array_splice($mainDbTables, array_search("tbl_client_master", $mainDbTables), 1);
        array_splice($mainDbTables, array_search("tbl_plantype", $mainDbTables), 1);

        /*
         * End
         */
       

        $conn = mysqli_connect($dbHost, $dbUser, $dbPwd);
        if ($conn) {
            $company = trim($company);
            // Create database
            $db_company = preg_replace("/[^A-Za-z]/", "_", $company); //filter phone
            $ddb_name = "DMS_" . $db_company . "_" . strtotime($date); //name of database
            //$licenseKey=NULL;
            /*
             * this line generate error in linux server
             */
            $licenseKey = generateLicenseKey($ddb_name, $lastinsertid);
            $sql = "CREATE DATABASE $ddb_name";
            $result = mysqli_query($conn, $sql); //create new database for particular client
            if ($result) {
                $conn = new mysqli($dbHost, $dbUser, $dbPwd, $ddb_name); // connection with dynamic database
                for ($index = 0; $index < count($mainDbTables); $index++) {
                    $newTableName = $mainDbTables[$index];
                    $fetchTbaleExist = "SHOW CREATE TABLE $newTableName";
                    $fetchTableQry = mysqli_query($db_con, $fetchTbaleExist);
                    if ($fetchTableQry) {
                        $fetchA = mysqli_fetch_assoc($fetchTableQry);
                        $query = $fetchA['Create Table'];
                        $tbl_qry = mysqli_query($conn, $query);
                        if ($tbl_qry) {
                            
                        } else {
                            return array("status" => True, "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "qry_error:" . $query);
                        }
                    } else {
                        return array("status" => True, "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "qry_error:" . $fetchTbaleExist);
                    }
                }
                /*
                 * admin info insert new database
                 */
                $insertAdminUserMaster = mysqli_query($db_con, "select * from `tbl_user_master` where user_id='1'");
                $res = mysqli_fetch_all($insertAdminUserMaster, MYSQLI_ASSOC);
                $userdataAdmin = "'" . implode("','", $res[0]) . "'";
                //echo "insert into `tbl_user_master` values($userdataAdmin)";
                $insertSuperUser = mysqli_query($conn, "insert into `tbl_user_master` values($userdataAdmin)");
                if ($insertSuperUser) {
                    $insertAdminRoleMaster = mysqli_query($db_con, "select * from `tbl_user_roles` where role_id='1'");
                    $roleAdmin = mysqli_fetch_all($insertAdminRoleMaster, MYSQLI_ASSOC);
                    $adminRoleInsert = "'" . implode("','", $roleAdmin[0]) . "'";
                    $insertSuperUserRole = mysqli_query($conn, "insert into `tbl_user_roles` values($adminRoleInsert)");
                    if ($insertSuperUserRole) {
                        $grp_to_su = mysqli_query($conn, "INSERT INTO `tbl_bridge_role_to_um` (`role_id`,`user_ids`) VALUES ('1','1')");
                        if ($grp_to_su) {
                            $storage_permissionSU = mysqli_query($conn, "insert into `tbl_storagelevel_to_permission`(`user_id`,`sl_id`) values('1','113')");
                            if ($storage_permissionSU) {
                                $SUgroup = mysqli_query($conn, "insert into `tbl_group_master`(`group_id`,`group_name`) values('1','SUPER ADMIN')");
                                if ($SUgroup) {
                                    $SUgrouprole = mysqli_query($conn, "insert into `tbl_bridge_grp_to_um`(`id`,`group_id`,`user_ids`,`roleids`) values('1','1','1','1')");
                                    if ($SUgrouprole) {
                                        
                                        $insertLang = "INSERT INTO `tbl_language` (`lang_name`,`lang_code`,`lang_img`,`default_language`) VALUES ('Hindi','hi','assets/images/indian.png',0),('English','en','assets/images/usa.png' ,1);";
                                        if(mysqli_query($conn, $insertLang)){
                                            $insertPassPolicy = "INSERT INTO `tbl_pass_policy`(`minlen`, `maxlen`, `lowercase`, `uppercase`, `numbers`, `s_char`, `edate`, `pass_reuse`, `admin_reset`, `user_chn_pass`, `feature_enable_disable`) VALUES ('8','16','1','1','1','1','0','0','0','0','0')";
                                            if(mysqli_query($conn, $insertPassPolicy)){
                                                
                                                if(insertFileExtension($conn, $dbUser, $dbPwd, $ddb_name)){
                                                    
                                                $deleteTrigger ='CREATE  TRIGGER `delete_doc` AFTER DELETE ON `tbl_document_master` FOR EACH ROW update tbl_agr_doc_upload set no_of_file=no_of_file-1,no_of_pages=no_of_pages-old.noofpages,file_size=file_size-old.doc_size where tbl_agr_doc_upload.sl_id=old.doc_name';
                                                if(mysqli_query($conn, $deleteTrigger)){
                                                   $insertTrigger = 'CREATE  TRIGGER `insertDoc` AFTER INSERT ON `tbl_document_master` FOR EACH ROW BEGIN DECLARE found_it INT; SELECT COUNT(1) INTO found_it FROM tbl_agr_doc_upload   WHERE sl_id = NEW.doc_name;
                                                                    IF found_it = 0 THEN
                                                                        INSERT INTO tbl_agr_doc_upload (sl_id, no_of_file, no_of_pages, file_size, dateposted) VALUES (NEW.doc_name, 1,new.noofpages,new.doc_size,new.dateposted);
                                                                    END IF;

                                                                    IF found_it>0 then
                                                                    IF new.doc_size >0 THEN 
                                                                    UPDATE tbl_agr_doc_upload SET no_of_file = no_of_file +1,
                                                                    no_of_pages = no_of_pages + new.noofpages,
                                                                    file_size = file_size + new.doc_size WHERE sl_id = new.doc_name;
                                                                    END IF;
                                                                    IF new.doc_size <=0 THEN 
                                                                    UPDATE tbl_agr_doc_upload SET no_of_file = no_of_file +1,
                                                                    no_of_pages = no_of_pages + new.noofpages WHERE sl_id = new.doc_name;
                                                                    END IF;
                                                                    end if;
                                                                    END';
                                                    if(mysqli_query($conn, $insertTrigger)){
                                                        $updateTrigger = 'CREATE  TRIGGER `updateDoc` AFTER UPDATE ON `tbl_document_master` FOR EACH ROW BEGIN 
                                                            IF new.doc_size >0 THEN 
                                                            UPDATE tbl_agr_doc_upload SET
                                                            no_of_pages = no_of_pages + new.noofpages-old.noofpages,
                                                            file_size = file_size + new.doc_size - old.doc_size WHERE sl_id = new.doc_name;
                                                            END IF;
                                                            IF new.doc_size <=0 THEN 
                                                            UPDATE tbl_agr_doc_upload SET no_of_pages = no_of_pages + new.noofpages - old.noofpages WHERE sl_id = new.doc_name;
                                                            END IF;
                                                            END';
                                                        if(mysqli_query($conn, $updateTrigger)){
                                                            
                                                        }else{
                                                             return array("status" => True, "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "Client database create update document trigger problem" . mysqli_error($conn)); 
                                                        }

                                                    }else{
                                                        return array("status" => True, "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "Client database create insert document trigger problem" . mysqli_error($conn)); 
                                                    }
                                                    
                                                }else{
                                                    return array("status" => True, "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "Client database create delete document trigger problem" . mysqli_error($conn)); 
                                                }
                                                
                                                
                                                
                                            }else{
                                                return array("status" => True, "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "Client database insert default file exten problem or file server crendentails or mail server credentials" . mysqli_error($conn)); 
                                            }
                                            
                                            }else{
                                                return array("status" => True, "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "Client database insert default pass policy problem" . mysqli_error($conn)); 
                                            }
                                            
                                        }else{
                                           return array("status" => True, "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "Client database insert default lang settings problem" . mysqli_error($conn)); 
                                        }
                                        
                                        
                                    } else {
                                        return array("status" => True, "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "Client database Bridge group to user Problem" . mysqli_error($conn));
                                    }
                                } else {
                                    return array("status" => True, "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "Client database Group Add Problem" . mysqli_error($conn));
                                }
                            } else {
                                return array("status" => True, "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "Client database Storage Permission Problem" . mysqli_error($conn));
                            }
                        } else {
                            return array("status" => True, "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "Client database User Master Problem" . mysqli_error($conn));
                        }
                    } else {
                        return array("status" => True, "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "Client database admin role Problem" . mysqli_error($conn));
                    }
                } else {
                    return array("status" => True, "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "Client database Admin add Problem" . mysqli_error($conn));
                }

                /*
                 * Admin end
                 * 
                 * FIrst Client info add start
                 */
                $update_dbname_clientqry = mysqli_query($db_con, "update `tbl_client_master` SET db_name='$ddb_name', license_key='$licenseKey' where client_id='$lastinsertid'");
                if ($update_dbname_clientqry) {
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
                    //echo  "insert into `tbl_user_roles`($data_cols_role)values($new_imploded_data)";
                    $Insert_New_User = mysqli_query($conn, "insert into `tbl_user_roles`(role_id,$data_cols_role)values('2',$new_imploded_data)");
                    // mysqli_error($conn);
                    $new_user_role = mysqli_insert_id($conn);
                    if ($Insert_New_User) {
                        $create = mysqli_query($conn, "insert into tbl_user_master (`user_email_id`, `first_name`, `last_name`, `password`, `designation`, `phone_no`, `profile_picture`, `superior_name`, `superior_email`, `user_created_date`, `emp_id`) values('$email','$firstname','$lastname',sha1('$password'),'null','$phone','$image','null','null','$date', 'null')");
                        $user_id = mysqli_insert_id($conn);
                        $user_idBridge = "1," . $user_id;
                        if ($create) {

                            $grp_to_um = mysqli_query($conn, "INSERT INTO `tbl_bridge_role_to_um` (`role_id`,`user_ids`) VALUES ('$new_user_role','$user_id')");
                            if ($grp_to_um) {
                                $create_Root_Strg = mysqli_query($conn, "insert into `tbl_storage_level`(`sl_id`,`sl_name`,`sl_parent_id`,`sl_depth_level`) values('113','$company','0',0)");
                                if ($create_Root_Strg) {
                                    $storage_permission = mysqli_query($conn, "insert into `tbl_storagelevel_to_permission`(`user_id`,`sl_id`) values('$user_id','113')");
                                    if ($storage_permission) {
                                        $Firstgroupqry = mysqli_query($conn, "insert into `tbl_group_master`(`group_id`,`group_name`) values('2','ADMIN')");
                                        if ($Firstgroupqry) {
                                            $firstgrouproleqry = mysqli_query($conn, "insert into `tbl_bridge_grp_to_um`(`id`,`group_id`,`user_ids`,`roleids`) values('2','2','$user_idBridge','$new_user_role')");
                                            if ($firstgrouproleqry) {
                                                $newstring=explode("/",$_SERVER[DOCUMENT_ROOT]);
                                                $newstr="/".$newstring[1]."/".$newstring[2];
                                                
                                                $rootdir = $newstr."/ezeefile_saas_client/";
                                                $newClientDirectory = $newstr."/ezeefile_saas_client/" . $ddb_name . "/";
                                                if (!is_dir($rootdir)) {
                                                    mkdir($rootdir, 0777, TRUE);
                                                }
                                                if (!is_dir($newClientDirectory)) {
                                                    mkdir($newClientDirectory, 0777, TRUE);
                                                }
                                                exec("cp -r $mainDirectorySrc" . "*" . " ./ $newClientDirectory", $shell); //copy whole directory

                                                chmod($newClientDirectory, 0777);

                                                if ($shell[0] != 0) {

                                                    return array("status" => True, "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "Directory Creation Failed");
                                                }
                                                exec("cp $mainDirectorySrc.htaccess $dest"); //copy htaccess file
                                                $path_to_file = $newClientDirectory . '/application/config/conf.php';
                                                $file_contents = file_get_contents($path_to_file);
                                                $file_contents = preg_replace('/\bezeefile_saas\b/u', $ddb_name, $file_contents);
                                                $file_contents = preg_replace('/\bdummymaindb\b/u', "ezeefile_saas", $file_contents);
                                                $file_contents = str_replace("iXejqbRUFYEYvBW6Qa9s4hyIgPeOAQK31pPm3vmC8Ss=", $licenseKey, $file_contents);
                                                // $confUpdates='$mainDbName="ezeefile_saas";'.'$clientKey='.'"'.$licenseKey.'";';
                                                file_put_contents($path_to_file, $file_contents);
                                                $subdomainName = $subDomain;
                                                $file = fopen("$subdomainName" . $doname[0].".conf", "w");
                                                $content = "<VirtualHost *:80>

                                                            ServerName $FullSubDomain
                                                            ServerAlias www.$FullSubDomain
                                                            Redirect permanent /  https://$FullSubDomain
                                                            #ServerAdmin ezeefileadmin@cbsl-india.com
                                                            #DocumentRoot $newClientDirectory
                                                            ErrorLog ${APACHE_LOG_DIR}/error.log
                                                            CustomLog ${APACHE_LOG_DIR}/access.log combined
                                                    </VirtualHost>
                                                    <IfModule mod_ssl.c>
                                                            <VirtualHost *:443>
                                                            ServerName $FullSubDomain
                                                            ServerAlias www.$FullSubDomain
                                                            ServerAdmin ezeefileadmin@cbsl-india.com
                                                            DocumentRoot $newClientDirectory

                                                            #   SSL Engine Switch:
                                                            #   Enable/Disable SSL for this virtual host.
                                                            SSLEngine on

                                                            #   A self-signed (snakeoil) certificate can be created by installing
                                                            #   the ssl-cert package. See
                                                            #   /usr/share/doc/apache2.2-common/README.Debian.gz for more info.
                                                            #   If both key and certificate are stored in the same file, only the
                                                            #   SSLCertificateFile directive is needed.
                                                            ##SSLCertificateFile /etc/apache2/ssl/ezeepea/45b90ab1c8461a7e.crt
                                                            ##SSLCertificateKeyFile /etc/apache2/ssl/ezeepeain.key
                                                            $crtinfo        
                                                    </VirtualHost>
                                                    </IfModule>
                                                    # vim: syntax=apache ts=4 sw=4 sts=4 sr noet";

                                                fwrite($file, $content);
                                                fclose($file);
                                                chmod("$subdomainName" . $doname[0].".conf", 0755);
                                                exec("mv $subdomainName" . $doname[0].".conf /etc/apache2/sites-available", $output);
                                                if ($output[0] == 0) {
                                                    exec("a2ensite $subdomainName" . $doname[0].".conf", $output1);
                                                    // var_dump($output1);
                                                    if ($output1[0] == 0) {
                                                        //exec("/etc/init.d/apache2 restart 2>&1",$output2);
                                                        //var_dump($output2);
                                                        exec("sudo /etc/init.d/apache2 reload 2>&1", $output2);
                                                        require 'mail.php';
                                                        $mail = mailClientCreate($email, $password, $projectName, $coustomer_id,$FullSubDomain);
                                                        if ($mail) {
                                                            
                                                        } else {

                                                            return array("status" => True, "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "Mail Not Sent");
                                                        }
                                                    } else {

                                                        return array("status" => True, "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "failed client site url enabled" . $output1);
                                                    }
                                                } else {

                                                    return array("status" => True, "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "failed client site conf enable move" . $output);
                                                }
                                                //var_dump($output);*/
                                                //exec("/usr/sbin/apache2 reload 2>&1",$output2);
                                            } else {
                                                return array("status" => True, "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "Client database Bridge group to user first client  Problem" . mysqli_error($conn));
                                            }
                                        } else {
                                            return array("status" => True, "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "Client database Group First client Add Problem" . mysqli_error($conn));
                                        }


//                                   
                                    } else {
                                        return array("status" => True, "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "Client database Storage Level Permission Problem-" . mysqli_error($conn));
                                    }
                                } else {
                                    return array("status" => True, "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "Client database Storage Level Problem" . mysqli_error($conn));
                                }
                            } else {
                                return array("status" => True, "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "Client database Group To User  Master Problem" . mysqli_error($conn));
                            }
                        } else {
                            return array("status" => True, "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "Client database User Master Problem" . mysqli_error($conn));
                        }
                    } else {
                        return array("status" => True, "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "Client User Role Creation Table" . mysqli_error($conn));
                    }
                } else {
                    return array("status" => True, "msg" => "Error creating Company", "connect" => $conn, "db_name" => $ddb_name, "dev_msg" => "Client master table failed");
                }
                mysqli_close($conn);
                //return TRUE;
            } else {
                return array("status" => True, "msg" => "Error creating Company", "dev_msg" => "Database Creation Failed");
            }
        } else {
            return array("status" => True, "msg" => "Error creating Connection", "dev_msg" => "Connection Failed");
        }
    } else {
        return array("status" => True, "msg" => "Error Fetch Table", "dev_msg" => "Master Database Table fetch Error");
    }
}


function insertFileExtension ($conn, $dbUser, $dbPwd, $ddb_name){
	
	 $fileCredentials= mysqli_query($conn, "INSERT INTO `tbl_file_server_details` (select * from ezeefile_saas.tbl_file_server_details)");
	
	$mailCredentails =mysqli_query($conn, "INSERT INTO `tbl_email_configuration_credential` (select * from ezeefile_saas.tbl_email_configuration_credential)");
    
    mysqli_query($conn, "ALTER TABLE `tbl_file_extensions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1");
  
  $exten = "INSERT INTO `tbl_file_extensions` ( `name`) VALUES('jpg'),('jpeg'),('png'),('pdf'),('mp4'),('mp3'),('html'),('rtf'),('gif'),('zip'),('docx'),('xlsx'),('xls'),('doc'),('csv'),('txt'),('bmp'),('mkv'),('rar'),('odt')";


	$command = 'mysql -u'.$dbUser.' -p'.$dbPwd.' -h'.$dbHost.' '.$ddb_name.' < '. __DIR__ .'/db_file/ezeefile_saas_upgraded_routines.sql';
	exec($command, $output);
  
    if(mysqli_query($conn, $exten) && $fileCredentials && $mailCredentails){
        return true;
    }else{
        return 0;
    }
}