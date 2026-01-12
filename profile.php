<?php
require_once('loginvalidate.php');
require_once('./application/config/database.php');
require_once('./application/config/validate_client_db.php');
require_once './application/pages/function.php';

// echo $rwgetRole['dashboard_mydms']; die;
if ($rwgetRole['dashboard_edit_profile'] != '1') {
    header('Location: ./index');
}
?>

<?php
mysqli_set_charset($db_con, "utf8");

if (isset($_GET['i'])) {
    $id = base64_decode(urldecode($_GET['i']));
    $user = mysqli_query($db_con, "select * from tbl_user_master where user_id='$id'");
} else {
    $id = $_SESSION['cdes_user_id'];
    $user = mysqli_query($db_con, "select * from tbl_user_master where user_id='$id'") or die('error');
}
$rwUser = mysqli_fetch_assoc($user);
$pre_sign_path = $rwUser['user_sign'];

$rwpwdPolicy = getPasswordPolicy($db_con);
?>
<!DOCTYPE html>
<html>
    <?php require_once './application/pages/head.php'; ?>
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
            <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
            <div class="content-page">
                <!-- Start content -->
                <div class="content">
                    <div class="wraper container">

                        <!-- Page-Title -->
                        <div class="row">
                            <div class="col-sm-12">
                                <ol class="breadcrumb">
                                    <li><a href="index"><?php echo $lang['Das']; ?></a></li>
                                    <li class="active"><?php echo $lang['Profile']; ?></li>
                                    <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                                </ol>

                                <div class="btn-group pull-right m-b-10">

                                    <button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false"><?php echo $lang['Settings']; ?><span class="m-l-5"><i class="fa fa-cog"></i></span></button>
                                    <ul class="dropdown-menu drop-menu-right" role="menu">
                                        <li> <a href="#" data-toggle="modal" data-target="#con-close-modal"><i class="fa fa-edit" ></i> <?php echo $lang['EDIT_PROFILE'] ?></a></li>
                                        <?php if ($rwgetRole['email_config'] == '1') { ?> 
                                            <li> <a href="#" data-toggle="modal" data-target="#MailConfig"><i class="fa fa-envelope-o" ></i> <?php echo $lang['Cfig_Mail'] ?></a></li>
                                        <?php } ?>
                                        <li> <a href="#" data-toggle="modal" data-target="#password-change-modal"><i class="fa fa-pencil" ></i><?php echo $lang['Chge_Pwd'] ?></a></li>

                                    </ul>
                                </div>


                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 col-lg-3">
                                <div class="box box-primary">
                                    <div class="profile-detail card-box">
                                        <div>
                                            <?php if (!empty($rwUser['profile_picture'])) { ?>
                                                <img src="data:image/jpeg;base64,<?php echo base64_encode($rwUser['profile_picture']); ?>" alt="user-img" class="img-circle"> 
                                            <?php } else { ?>

                                                <img src="./assets/images/avatar.png" alt="Image" class="img-circle">
                                            <?php } ?>


                                            <ul class="list-inline status-list m-t-20">
                                                <li>
                                                    <button type="button" class="btn btn-primary btn-custom btn-rounded waves-effect waves-light" data-toggle="modal" data-target="#custom-width-modal"><?php echo $lang['Change_Photo']; ?></button>
                                                </li>
                                            </ul>
                                            <hr>
                                            <h4 class="text-uppercase font-600"><?php echo $lang['AboutMe']; ?></h4>
                                            <p class="text-muted font-13 m-b-30">
                                                <?php echo $rwUser['UserInfo']; ?>
                                            </p>
                                            <div class="text-left">
                                                <p class="text-muted font-13"><strong><?php echo $lang['Fl_Nm']; ?> :</strong> <span><?php echo $rwUser['first_name'] . ' ' . $rwUser['last_name']; ?></span></p>

                                                <p class="text-muted font-13"><strong><?php echo $lang['Mob']; ?>  :</strong><span><?php echo $rwUser['phone_no']; ?></span></p>

                                                <p class="text-muted font-13"><strong><?php echo $lang['Email']; ?> :</strong> <span><?php echo $rwUser['user_email_id']; ?></span></p>

                                                <p class="text-muted font-13"><strong><?php echo $lang['Designation']; ?> :</strong> <span><?php echo $rwUser['designation']; ?></span></p>
                                                <?php
                                                if (!empty($rwUser['user_sign'])) {
                                                    ?>
                                                    <p class="text-muted font-13"><strong><?php echo $lang['My_Sign']; ?> :<?php echo $lang['AboutMe']; ?></strong></p> 
                                                    <p><a href="#"data-toggle="modal" data-target="#custom-width-modal1" class="btn btn-primary btn-custom btn-rounded text-center"><?php echo $lang['Change_Sign']; ?></a></p>
                                                    <p><img src="<?php echo $rwUser['user_sign']; ?>"></p>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <a href="#"data-toggle="modal" data-target="#custom-width-modal1" class="btn btn-primary btn-custom btn-rounded text-center"><?php echo $lang['Add_Sign']; ?></a>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                            <div class="button-list m-t-20">
                                                <?php if (!empty($rwUser['UserfaceBook'])) { ?>
                                                    <a href="<?php echo $rwUser['UserfaceBook']; ?>" target="blank" class="btn btn-facebook waves-effect waves-light">
                                                        <i class="fa fa-facebook"></i>
                                                    </a>
                                                <?php } if (!empty($rwUser['UserTwitter'])) { ?>
                                                    <a href="<?php echo $rwUser['UserTwitter']; ?>" target="blank" class="btn btn-twitter waves-effect waves-light">
                                                        <i class="fa fa-twitter"></i>
                                                    </a>
                                                <?php } if (!empty($rwUser['UserLinkedIN'])) { ?>
                                                    <a href="<?php echo $rwUser['UserLinkedIN']; ?>" target="blank" class="btn btn-linkedin waves-effect waves-light">
                                                        <i class="fa fa-linkedin"></i>
                                                    </a>
                                                <?php } ?>

                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-9 col-md-9">
                                <?php if ($rwgetRole['hindi'] || $rwgetRole['english']) { ?>
                                    <div class="box box-primary">
                                        <div class="card-box">
                                            <div class="row">
                                                <div class="col-md-3 form-group">
                                                    <label><?= $lang['Default_Language']; ?></label>
                                                    <select  name="lang"  id="lang" class="form-control select2">
                                                        <option disabled><?php echo $lang['Ch_lang']; ?></option>
                                                        <?php if ($rwgetRole['english']) { ?>
                                                            <option <?php
                                                            if ($_SESSION['lang'] == "English") {
                                                                echo 'Selected';
                                                            }
                                                            ?> value="English"> <?php echo $lang['English']; ?> </option>
                                                            <?php } if ($rwgetRole['hindi']) { ?>
                                                            <option <?php
                                                            if ($_SESSION['lang'] == "Hindi") {
                                                                echo 'Selected';
                                                            }
                                                            ?> value="Hindi"><?php echo $lang['Hindi']; ?></option>

                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="box box-primary">
                                    <div class="card-box">
                                        <strong><?php echo $lang['Designation']; ?></strong> -: <?php
                                        echo $rwUser['designation'] . '<br>';
                                        ?>
                                        <h5 class="font-600"><?php echo $lang['My_Privileges']; ?></h5>
                                        <?php
                                        $userRole = mysqli_fetch_assoc(mysqli_query($db_con, "select * from tbl_bridge_role_to_um where find_in_set('$rwUser[user_id]',user_ids)")) or die('error' . mysqli_error($db_con));
                                        $roleid = $userRole['role_id'];

                                        $priv = mysqli_query($db_con, "select * from tbl_user_roles where role_id='$roleid'");
                                        $rwPriv = mysqli_fetch_assoc($priv);

                                        echo '<strong>' . $lang['Rol'] . '</strong> - : ' . $rwPriv['user_role'] . '<br>';
//                                    $cols = mysqli_query($db_con, "show columns FROM `tbl_user_roles`");
//                                    $i = 0;
//                                    while ($rwCols = mysqli_fetch_array($cols)) {
//                                        if ($i > 1) {
//                                            if ($rwPriv[$rwCols[0]]) {
//                                                echo $rwCols[0] . ', ';
//                                            }
//                                        }
//                                        $i++;
//                                        //print_r($rwCols); 
//                                    }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog modal-lg"> 
                                <div class="modal-content"> 
                                    <form method="post" >
                                        <div class="modal-header"> 
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                                            <h4 class="modal-title"><?php echo $lang['Update_Your_Profile']; ?></h4> 
                                        </div>

                                        <div class="modal-body">

                                            <div class="row"> 
                                                <div class="col-md-6"> 
                                                    <div class="form-group"> 
                                                        <label for="usernName" class="control-label"><?php echo $lang['First_Name']; ?> <span style="color:red;">*</span></label> 
                                                        <input type="text" class="form-control specialchaecterlock translatetext" parsley-trigger="change" name="firstname" id="userName" placeholder="First Name" value="<?php echo $rwUser['first_name']; ?>" required> 
                                                    </div> 
                                                </div> 
                                                <div class="col-md-6"> 
                                                    <div class="form-group"> 
                                                        <label for="lastName" class="control-label"><?php echo $lang['Last_Name']; ?></label> 
                                                        <input type="text" class="form-control specialchaecterlock translatetext" parsley-trigger="change" name="lastname" id="lastName" placeholder="Last Name" value="<?php echo $rwUser['last_name']; ?>"> 
                                                    </div> 
                                                </div>
                                                <div class="col-md-6"> 
                                                    <?php
//$chekAdmin = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) and tur.user_role='Admin'") or die('Error:' . mysqli_error($db_con));
                                                    ?>
                                                    <div class="form-group"> 
                                                        <label for="inputEmail3" class="control-label"><?php echo $lang['Email_ID']; ?> <span style="color:red;">*</span></label> 
                                                        <input readonly type="email" class="form-control" parsley-trigger="change" required parsley-type="email"  name="email" id="inputEmail3" placeholder="<?php echo $lang['Email_ID']; ?>" value="<?php echo $rwUser['user_email_id']; ?>" <?php
                                                        //if (mysqli_num_rows($chekAdmin) == 0) {
                                                        //  echo 'readonly';
                                                        // }
                                                        ?>> 
                                                    </div> 
                                                </div>
                                                <div class="col-md-6"> 
                                                    <div class="form-group"> 
                                                        <label for="phone" class="control-label"><?php echo $lang['Phone']; ?> <span style="color:red;">*</span></label> 
                                                        <input type="text" data-parsley-type="digits" parsley-trigger="change" required class="form-control" name="phone" id="phone" placeholder="Phone" value="<?php echo $rwUser['phone_no']; ?>" maxlength="10" minlength="10" data-parsley-minlength-message="Phone number should be in 10 digit"> 
                                                    </div> 
                                                </div> 
                                            </div> 
                                            <div class="row"> 
                                                <div class="col-md-12"> 
                                                    <div class="form-group"> 
                                                        <label for="designation" class="control-label"><?php echo $lang['Designation']; ?> <span style="color:red;">*</span></label> 
                                                        <input type="text" class="form-control  specialchaecterlock translatetext" parsley-trigger="change" id="designation" name="designation" placeholder="<?php echo $lang['Designation']; ?>" required value="<?php echo $rwUser['designation']; ?>"> 
                                                    </div> 
                                                </div>

                                            </div> 



                                        </div> 
                                        <div class="modal-footer">
                                            <input type="hidden" name="uid" value="<?php echo $rwUser['user_id']; ?>">
                                            <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                                            <button type="submit" name="editProfile" class="btn btn-primary waves-effect waves-light"><?php echo $lang['Save_changes']; ?></button> 
                                        </div>
                                    </form>

                                </div> 
                            </div>
                        </div><!-- /.modal -->
                        <div id="MailConfig" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog modal-lg"> 
                                <div class="modal-content"> 
                                    <form method="post" id="mailConfigForm" >
                                        <div class="modal-header"> 
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                                            <h4 class="modal-title"><?php echo $lang['Ml_Cfig_Sting']; ?></h4> 
                                        </div>
                                        <?php
                                        $mailConfig = mysqli_query($db_con, "select * from tbl_email_config where user_id='$_SESSION[cdes_user_id]'");
                                        $rwMail = mysqli_fetch_assoc($mailConfig);
                                        if (mysqli_num_rows($mailConfig) < 1) {
                                            $filter = $rwMail['filters'];
                                            $filter = explode(",", $filter);
                                            ?>
                                            <div class="modal-body">

                                                <div class="row"> 
                                                    <div class="col-md-6"> 
                                                        <div class="form-group"> 
                                                            <label for="mailServer" class="control-label"><?php echo $lang['Ml_Cfig_Sting']; ?></label> 
                                                            <input type="text" class="form-control emaillock" required parsley-trigger="change" name="mailServer" id="mailServer" placeholder="<?php echo $lang['mail_server']; ?>" value="<?php echo $rwUser['']; ?>"> 
                                                        </div> 
                                                    </div> 
                                                    <div class="col-md-6"> 
                                                        <div class="form-group"> 
                                                            <label for="emlid" class="control-label"><?php echo $lang['Email_ID']; ?></label> 
                                                            <input type="text" class="form-control emaillock" parsley-trigger="change" required name="emailid" id="emlid" placeholder="<?php echo $lang['Email_ID']; ?>" value="<?php echo $rwUser['']; ?>"> 
                                                        </div> 
                                                    </div>
                                                    <div class="col-md-6"> 

                                                        <div class="form-group"> 
                                                            <label for="pwd" class="control-label"><?php echo $lang['Password']; ?></label> 
                                                            <input type="password" class="form-control" parsley-trigger="change"   name="password" id="pwd" placeholder="<?php echo $lang['Password']; ?>" value="<?php echo $rwUser['']; ?>" > 
                                                        </div> 
                                                    </div>
                                                    <div class="col-md-6"> 
                                                        <div class="form-group"> 
                                                            <label for="port" class="control-label"><?php echo $lang['Port']; ?></label> 
                                                            <input type="number" data-parsley-type="digits" parsley-trigger="change" required class="form-control" name="port" id="port" placeholder="Port" value="<?php echo $rwUser['']; ?>"> 
                                                        </div> 
                                                    </div> 
                                                </div> 
                                                <div class="row"> 
                                                    <div class="col-md-6">
                                                        <label  class="control-label"><?php echo $lang['SSL']; ?></label><br />
                                                        <div class="radio radio-success radio-inline">
                                                            <input type="radio"  parsley-trigger="change" id="ssl" name="ssl" required value="1">
                                                            <label for="ssl"><?php echo $lang['Yes']; ?></label>

                                                        </div>
                                                        <div class="radio radio-danger radio-inline">
                                                            <input type="radio"  parsley-trigger="change" id="ssl1" name="ssl" required value="0">
                                                            <label for="ssl1"><?php echo $lang['No']; ?></label>

                                                        </div>

                                                    </div>
                                                    <div class="col-md-6"> 
                                                        <div class="form-group"> 
                                                            <label  class="control-label"><?php echo $lang['Validate']; ?></label> <br>
                                                            <div class="radio radio-success radio-inline">
                                                                <input type="radio"  parsley-trigger="change" id="valid" name="valid" required value="1">
                                                                <label for="valid"><?php echo $lang['Yes']; ?></label>
                                                            </div>
                                                            <div class="radio radio-danger radio-inline">
                                                                <input type="radio"  parsley-trigger="change" id="valid1" name="valid" required value="0">
                                                                <label for="valid1"><?php echo $lang['No']; ?></label>
                                                            </div>
                                                        </div> 
                                                    </div>
                                                    <div class="col-md-12"> 
                                                        <div class="form-group">
                                                            <label><?php echo $lang['Filters']; ?></label>  
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label><?php echo $lang['Subject']; ?> :</label>
                                                                <input type="text" name="fltr_sub" class="form-control specialchaecterlock">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label><?php echo $lang['Body']; ?> :</label>
                                                                <input type="text" name="fltr_body" class="form-control specialchaecterlock">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label><?php echo $lang['From']; ?></label>
                                                                <input type="text" name="fltr_from" class="form-control emaillock">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label> <?php echo $lang['to']; ?>:</label>
                                                                <input type="text" name="fltr_to" class="form-control emaillock">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label>Since : <?php echo $lang['AboutMe']; ?></label>
                                                                <input type="text" name="fltr_date" class="form-control" placeholder="mm/dd/yyyy" id="datepicker" >
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> 
                                                <p id="message" style="display: none;"></p>
                                            </div> 
                                            <div class="modal-footer">
                                                <input type="hidden" name="uid" value="<?php echo $rwUser['user_id']; ?>">
                                                <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                                                <button type="button" id="testMailConn" class="btn btn-facebook"><?php echo $lang['Tst_Cnect']; ?></button>
                                                <button type="submit" name="mailConfig" class="btn btn-primary waves-effect waves-light"><?php echo $lang['Save']; ?></button> 

                                            </div>
                                            <?php
                                        } else {
                                            ?>
                                            <div class="modal-body">

                                                <div class="row"> 
                                                    <div class="col-md-6"> 
                                                        <div class="form-group"> 
                                                            <label for="mailServer" class="control-label"><?php echo $lang['Incoming_Mail_Server']; ?></label> 
                                                            <input type="text" class="form-control emaillock" required parsley-trigger="change" name="mailServer" id="mailServer" placeholder="Mail Server" value="<?php echo $rwMail['mailServer']; ?>"> 
                                                        </div> 
                                                    </div> 
                                                    <div class="col-md-6"> 
                                                        <div class="form-group"> 
                                                            <label for="emlid" class="control-label"><?php echo $lang['Email_ID']; ?></label> 
                                                            <input type="text" class="form-control emaillock" parsley-trigger="change" required name="emailid" id="emlid" placeholder="Email ID" value="<?php echo $rwMail['user_email']; ?>"> 
                                                        </div> 
                                                    </div>
                                                    <div class="col-md-6"> 

                                                        <div class="form-group"> 
                                                            <label for="pwd" class="control-label"><?php echo $lang['Password']; ?> </label> 
                                                            <input type="password" class="form-control" parsley-trigger="change"   name="password" id="pwd" placeholder="Password" > 
                                                        </div> 
                                                    </div>
                                                    <div class="col-md-6"> 
                                                        <div class="form-group"> 
                                                            <label for="port" class="control-label"><?php echo $lang['Port']; ?></label> 
                                                            <input type="number" data-parsley-type="digits" parsley-trigger="change" required class="form-control" name="port" id="port" placeholder="<?php echo $lang['Port']; ?>" value="<?php echo $rwMail['port']; ?>"> 
                                                        </div> 
                                                    </div> 
                                                </div> 
                                                <div class="row"> 
                                                    <div class="col-md-6"> 
                                                        <div class="form-group"> 
                                                            <label  class="control-label"><?php echo $lang['SSL']; ?></label> <br>
                                                            <input type="radio"  parsley-trigger="change" id="ssl" name="ssl" required value="1" <?php
                                                            if ($rwMail['ssl'] == 1) {
                                                                echo'checked';
                                                            }
                                                            ?>>
                                                            <label for="ssl"><?php echo $lang['Yes']; ?></label>
                                                            <input type="radio"  parsley-trigger="change" id="ssl1" name="ssl" required value="0" <?php
                                                            if ($rwMail['ssl'] == 0) {
                                                                echo'checked';
                                                            }
                                                            ?>>
                                                            <label for="ssl1"><?php echo $lang['No']; ?></label>
                                                        </div> 
                                                    </div>
                                                    <div class="col-md-6"> 
                                                        <div class="form-group"> 
                                                            <label  class="control-label"><?php echo $lang['Validate']; ?></label> <br>
                                                            <input type="radio"  parsley-trigger="change" id="valid" name="valid" required value="1" <?php
                                                            if ($rwMail['validate'] == 1) {
                                                                echo'checked';
                                                            }
                                                            ?>>
                                                            <label for="valid"><?php echo $lang['Yes']; ?></label>
                                                            <input type="radio"  parsley-trigger="change" id="valid1" name="valid" required value="0" <?php
                                                            if ($rwMail['validate'] == 0) {
                                                                echo'checked';
                                                            }
                                                            ?>>
                                                            <label for="valid1"><?php echo $lang['No']; ?></label>
                                                        </div> 
                                                    </div>
                                                    <div class="col-md-12"> 
                                                        <div class="form-group">
                                                            <label><?php echo $lang['Filters']; ?></label>  
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label><?php echo $lang['Subject']; ?>:</label>
                                                                <input type="text" name="fltr_sub" class="form-control specialchaecterlock">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label><?php echo $lang['Body']; ?>:</label>
                                                                <input type="text" name="fltr_body" class="form-control specialchaecterlock">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label><?php echo $lang['From']; ?>:</label>
                                                                <input type="text" name="fltr_from" class="form-control emaillock">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label> <?php echo $lang['to']; ?>:</label>
                                                                <input type="text" name="fltr_to" class="form-control emaillock">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label> <?php echo $lang['Since']; ?>:</label>
                                                                <input type="text" name="fltr_date" class="form-control" placeholder="mm/dd/yyyy" id="datepicker" >
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> 
                                                <p id="message" style="display: none;"></p>
                                            </div> 
                                            <div class="modal-footer">
                                                <input type="hidden" name="uid" value="<?php echo $rwMail['user_id']; ?>">
                                                <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                                                <button type="button" id="testMailConn" class="btn btn-facebook"><?php echo $lang['Tst_Cnect']; ?></button>
                                                <button type="submit" name="editmailConfig" class="btn btn-primary waves-effect waves-light"><?php echo $lang['Save_changes']; ?></button> 

                                            </div>
                                        <?php }
                                        ?>
                                    </form>

                                </div> 
                            </div>
                        </div><!-- /.modal --> 
                        <div id="custom-width-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="custom-width-modalLabel" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="post" enctype="multipart/form-data">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title" id="custom-width-modalLabel"><?php echo $lang['Change_Image']; ?></h4>
                                        </div>
                                        <div class="modal-body">
                                            <h4><?php echo $lang['Upld_Prfile_Image']; ?></h4>
                                            <div class="form-group">
                                                <p><input type="file" name="image" class="filestyle" accept="image/*" required ></p>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <input type="hidden" name="id" value="<?php echo $rwUser['user_id']; ?>">
                                            <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                                            <button type="submit" name="change-img" class="btn btn-primary waves-effect waves-light imageclass"><?php echo $lang['Save_changes']; ?></button>
                                        </div>
                                    </form>

                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- /.modal -->
                        <div id="custom-width-modal1" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="custom-width-modalLabel" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="post" enctype="multipart/form-data">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title" id="custom-width-modalLabel"><?php echo $lang['Add_Sign']; ?></h4>
                                        </div>
                                        <div class="modal-body">
                                            <!--<h4>Upload Sign</h4>-->
                                            <div class="form-group">
                                                <p><input type="file" name="sign" class="filestyle" accept="image/*" id="profileimg" required></p>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <input type="hidden" name="id" value="<?php echo $rwUser['user_id']; ?>">
                                            <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                                            <button type="submit" name="addSign" class="btn btn-primary waves-effect waves-light"><?php echo $lang['Save_changes']; ?></button>
                                        </div>
                                    </form>

                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- /.modal -->
                        <div id="password-change-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="custom-width-modalLabel" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="post" id="chngpass">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title" id="custom-width-modalLabel"><?php echo $lang['Chge_Pwd']; ?></h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label><?php echo $lang['old_Pwd']; ?> <span style="color:red;">*</span></label>

                                                        <input type="password" parsley-trigger="change" name="oldpwd" class="form-control" required placeholder="<?php echo $lang['old_Pwd']; ?>" />
                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label><?php echo $lang['Nw_Pwd']; ?> <span style="color:red;">*</span></label>
                                                        <input type="password" id="pass2" name="password" class="form-control" data-parsley-minlength="<?= (!empty($rwpwdPolicy['minlen']) ? $rwpwdPolicy['minlen'] : '8'); ?>" data-parsley-maxlength="<?= (!empty($rwpwdPolicy['maxlen']) ? $rwpwdPolicy['maxlen'] : '8'); ?>" data-parsley-uppercase="<?= $rwpwdPolicy['uppercase']; ?>" data-parsley-lowercase="<?= $rwpwdPolicy['lowercase']; ?>" data-parsley-number="<?= $rwpwdPolicy['numbers']; ?>" data-parsley-special="<?= $rwpwdPolicy['s_char']; ?>" data-parsley-errors-container=".errorspannewpassinput"  placeholder="<?php echo $lang['enp']; ?>" required>
                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label><?php echo $lang['Confirm_Password']; ?> <span style="color:red;">*</span></label>
                                                        <input type="password" parsley-trigger="change keyup" class="form-control" required data-parsley-equalto="#pass2"  placeholder="<?php echo $lang['RTpe_Pwd']; ?>" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <input type="hidden" name="id" value="<?php echo $rwUser['user_id']; ?>">
                                            <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                                            <button type="submit" name="change-pwd" class="btn btn-primary waves-effect waves-light"><?php echo $lang['Save_changes']; ?></button>

                                        </div>
                                    </form>

                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- /.modal -->
                    </div> <!-- container -->

                </div> <!-- content -->

                <?php require_once './application/pages/footer.php'; ?>

            </div>
            <!-- ============================================================== -->
            <!-- End Right content here -->
            <!-- ============================================================== -->


            <!-- Right Sidebar -->
            <?php require_once './application/pages/rightSidebar.php'; ?>
            <!-- /Right-bar -->

            <!--display wait gif image after submit-->
            <div  style=" display: none; background: rgba(0,0,0,0.5); width: 100%; height: 100%; z-index: 2000; position: fixed; top:0;" id="wait">;

                <img src="assets/images/proceed.gif" alt="load"  style=" margin-left: 48%; margin-top: 250px; width: 100px; height:100px; "/>
            </div>  
        </div>
        <!-- END wrapper -->
        <?php require_once './application/pages/footerForjs.php'; ?>

        <script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <!-- Modal-Effect -->
        <script src="assets/plugins/custombox/js/custombox.min.js"></script>
        <script src="assets/plugins/custombox/js/legacy.min.js"></script>
        <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
        <link href="assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet">
        <script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
        <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>


        <script type="text/javascript">
                                        $(document).ready(function () {
                                            $('form').parsley();
                                        });
                                        // jQuery('#datepicker').datepicker();
                                        jQuery('#datepicker').datepicker({
                                            autoclose: true,
                                            todayHighlight: true
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

                                        $('#testMailConn').click(function () {

                                            $('#wait').show();
                                            $(':button').prop('disabled', true);
                                            $(':input').prop('disabled', true);
                                            //values
                                            var mailServer = $("#mailServer").val();
                                            var userName = $("#emlid").val();
                                            var password = $("#pwd").val();
                                            var port = $("#port").val();
                                            var ssl = $('input[name=ssl]:checked').val();
                                            var valid = $('input[name=valid]:checked').val();

                                            var data = {mail: mailServer, email: userName, port: port, pwd: password, ssl: ssl, valid: valid};

                                            $.post("application/ajax/mailConnection.php", data, function (result, status) {

                                                if (status == 'success') {
                                                    if (result == 1) {
                                                        
                                                        $("#message").addClass("text-success");
                                                        $("#message").html("<?php echo $lang['connection_successfully']; ?> !");
                                                        
                                                        $("input[name='mailConfig']").show();

                                                    } else {
                                                        $("#message").addClass("text-danger");
                                                        $("#message").html("<?php echo $lang['unable_to_configure_email']; ?>");

                                                    }
                                                } else {
                                                    $("#message").addClass("text-danger");
                                                    $("#message").html("<?php echo $lang['unable_to_configure_email']; ?>");

                                                }
                                                $('#wait').hide();
                                                $('#message').show();
                                                $(':button').prop('disabled', false);
                                                $(':input').prop('disabled', false);

                                            });

                                        });
                                        $(".select2").select2({
                                            "language": {
                                                "noResults": function () {
                                                    return "<?= $lang['No_Rcrds_Fnd'] ?>";
                                                }
                                            }
                                        });

                                        $("#profileimg").change(function () {
                                            var i;
                                            var file = document.getElementById("profileimg").files[0];
                                            var type = document.getElementById("profileimg").files[0].name;
                                            var exten = type.split(".");

                                            if (exten[1] === "jpg" || exten[1] === "jpeg" || exten[1] === "png")
                                            {
                                                if (file.size > 921600)
                                                {
                                                    var fSExt = new Array('Bytes', 'KB', 'MB', 'GB');
                                                    var fSize = file.size;
                                                    i = 0;
                                                    while (fSize > 900) {
                                                        fSize /= 1024;
                                                        i++;
                                                    }
                                                    $("#fmsg").html("<p style='color:red'>File Size Should Be Less Then 900Kb</p>");
                                                    $(".imageclass").attr("disabled", "disabled");
                                                } else {
                                                    $("#fmsg").empty();
                                                    $(".imageclass").removeAttr("disabled", "disabled");
                                                }
                                            } else {
                                                $("#fmsg").html("<p style='color:red'>Invalid File Extension</p>");
                                                $(".imageclass").attr("disabled", "disabled");
                                            }
                                        })


                                        $('.emaillock').bind("keyup change", function ()
                                        {
                                            var GrpNme = $(this).val();
                                            re = /[`~!#$%^&*()_|+\=?;:'",<>\{\}\[\]\\\/]/gi;
                                            var isSplChar = re.test(GrpNme);
                                            if (isSplChar)
                                            {
                                                var no_spl_char = GrpNme.replace(/[`~!#$%^&*()_|+\=?;:'",<>\{\}\[\]\\\/]/gi, '');
                                                $(this).val(no_spl_char);
                                            }
                                        });
        </script>
        <?php
        //for change pasword
        if (isset($_POST['change-pwd'])) {
            if (!empty($_POST['password'])) {
                $ip = $_POST['ip'];
                $pwd = $_POST['password'];
                $pwd = mysqli_real_escape_string($db_con, $pwd);
                $id = $_POST['id'];
                $id = mysqli_real_escape_string($db_con, $id);
                $oldpwd = mysqli_real_escape_string($db_con, $_POST['oldpwd']);
                $check = mysqli_query($db_con, "select * from tbl_user_master where user_id='$id' and password=sha1('$oldpwd')");
                if (mysqli_num_rows($check) > 0) {
                    if (!empty($_SESSION[clientid]) && isset($_SESSION[clientid])) {
                        $update_aggregate = mysqli_query($db_valid_con, "update tbl_aggregate_user_master set password=sha1('$pwd') where tbl_ag_id='$_SESSION[client_user_id]' and password=sha1('$oldpwd')");
                    }
                    $update = mysqli_query($db_con, "update tbl_user_master set password=sha1('$pwd') where user_id='$id' and password=sha1('$oldpwd')");
                    if ($update) {

                        include 'mail.php';
                        mailResetPass($_SESSION['data']['user_email_id'], $pwd, $projectName, $_SESSION['admin_user_name']);
                        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'password change','$date',null,'$host',null)"); //or die('error : ' . mysqli_error($db_con));
                        echo'<script>taskSuccess("profile","' . $lang['password_updated_success'] . '!");</script>';
                    }
                } else {
                    echo'<script>taskFailed("profile","' . $lang['please_enter_valid_pass'] . '!");</script>';
                }
            }
            mysqli_close($db_con);
        }

//change profile pic
        if (isset($_POST['change-img'])) {
            $id = $_POST['id'];
            $filename = $_FILES['image']['name'];
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            $allowed = array('jpg', 'png', 'jpeg');
            if (in_array($ext, $allowed)) {

                $image = addslashes(file_get_contents($_FILES['image']['tmp_name']));
                $update = mysqli_query($db_con, "update tbl_user_master set profile_picture='$image' where user_id='$id'") or die('Error : ' . mysqli_error($db_con));
                if ($update) {
                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'profile image updated','$date',null,'$host','')"); //or die('error : ' . mysqli_error($db_con));
                    echo'<script>taskSuccess("profile","' . $lang['profile_img_update'] . ' !");</script>';
                }
                mysqli_close($db_con);
            } else {
                echo'<script>taskFailed("profile","' . $lang['photo_must_be_in_jpg_png'] . ' !");</script>';
            }
        }
        ?>
        <?php
        if (isset($_POST['addSign'])) {
            $id = $_POST['id'];
            $destination = 'userSign';
            if (!dir($destination)) {
                mkdir($destination, 0777, TRUE);
            }
            $filename = $_FILES['sign']['name'];
            $allowedSize = $_FILES['sign']['size']; // image size in byte
            //list($width, $height) = getimagesize($_FILES['image']['name']);
            //$width=imagesx($filename);
            //$height=imagesy($filename);

            $extn = substr($filename, strrpos($filename, '.') + 1);
            $extn = strtolower($extn);
            $destination = $destination . '/' . time() . '.' . $extn;
            $allowedExtn = array('jpg', 'png', 'jpeg');
            if (in_array($extn, $allowedExtn)) {
                move_uploaded_file($_FILES['sign']['tmp_name'], $destination) or die('Error');
                $update = mysqli_query($db_con, "update tbl_user_master set user_sign='$destination' where user_id='$id'") or die('Error : ' . mysqli_error($db_con));
                if ($update) {
                    unlink($pre_sign_path);
                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`, `action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Sign updated','$date','$host','You have changed your sign.')"); //or die('Log Error : ' . mysqli_error($db_con));
                    echo'<script>taskSuccess("profile","' . $lang['sign_updated_successfully'] . '");</script>';
                }
                mysqli_close($db_con);
            } else {
                echo'<script>taskFailed("profile","' . $lang['sign_must_be_in_jpg_png'] . '");</script>';
            }
        }
		
        if (isset($_POST['editProfile'])) {
            $id = filter_input(INPUT_POST, "uid");
            $fname = filter_input(INPUT_POST, "firstname");
            $fname = mysqli_real_escape_string($db_con, $fname);
            $lname = filter_input(INPUT_POST, "lastname");
            $lname = mysqli_real_escape_string($db_con, $lname);
            $phone = filter_input(INPUT_POST, "phone");
            $phone = mysqli_real_escape_string($db_con, $phone);
            $email = filter_input(INPUT_POST, "email");
            $email = mysqli_real_escape_string($db_con, $email);
            $designation = filter_input(INPUT_POST, "designation");
            $edit = mysqli_query($db_con, "update tbl_user_master set `first_name`='$fname', `last_name`='$lname', `phone_no`='$phone', designation='$designation' where user_id='$id'"); //or die('Error : ' . mysqli_error($dbc));
            if ($edit) {
                $_SESSION['admin_user_name'] = $fname;
                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'update his profile','$date',null,'$host','')"); //or die('error : ' . mysqli_error($db_con));
                echo'<script>taskSuccess("profile","' . $lang['user_profile_updated_success'] . ' !");</script>';
                //  header('location:'.$_SERVER['HTTP_REFERER']);
            } else {
                echo'<script>taskFailed("profile","' . $lang['user_profile_not_updated'] . ' !");</script>';
            }
            mysqli_close($db_con);
        }
		
        if (isset($_POST['mailConfig'])) {
            $filters = "";
            $fltr_sub = $_POST['fltr_sub'];
            if (!empty($fltr_sub)) {
                if (empty($filters)) {
                    $filters .= 'SUBJECT "' . $fltr_sub . '"';
                } else {
                    $filters .= ',SUBJECT "' . $fltr_sub . '"';
                }
            }
            $fltr_body = $_POST['fltr_body'];
            if (!empty($fltr_body)) {
                if (empty($filters)) {
                    $filters .= 'BODY "' . $fltr_body . '"';
                } else {
                    $filters .= ',BODY "' . $fltr_body . '"';
                }
            }
            $fltr_from = $_POST['fltr_from'];
            if (!empty($fltr_from)) {
                if (empty($filters)) {
                    $filters .= 'FROM "' . $fltr_from . '"';
                } else {
                    $filters .= ',FROM "' . $fltr_from . '"';
                }
            }
            $fltr_to = $_POST['fltr_to'];
            if (!empty($fltr_to)) {
                if (empty($filters)) {
                    $filters .= 'TO "' . $fltr_to . '"';
                } else {
                    $filters .= ',TO "' . $fltr_to . '"';
                }
            }
            $fltr_date = $_POST['fltr_date'];
            if (!empty($fltr_date)) {
                if (empty($filters)) {
                    $filters .= 'SINCE "' . date('d-M-Y', strtotime($fltr_date)) . '"';
                } else {
                    $filters .= ',SINCE "' . date('d-M-Y', strtotime($fltr_date)) . '"';
                }
            }

            $userid = $_POST['uid'];
            $mailServer = $_POST['mailServer'];
            $username = $_POST['emailid'];
            $password = $_POST['password'];
            $port = $_POST['port'];
            $ssl1 = $_POST['ssl'];
            if ($ssl1 == 1) {
                $ssl = "ssl";
            } else {
                $ssl = "";
            }
            $valid = $_POST['valid'];
            if ($valid == 0) {
                $validate = "novalidate-cert";
            } else {
                $validate = "";
            }

            require_once './mailServerInt.php';
            $conEmail = connectionCheck($mailServer, $port, $ssl, $validate, $username, $password);
            if ($conEmail) {
                $password = urlencode(base64_encode($password));
                $filters = mysqli_real_escape_string($db_con, $filters);
                $check = mysqli_query($db_con, "select * from tbl_email_config where user_email='$username'");
                if (mysqli_num_rows($check) <= 0) {
                    $mail = mysqli_query($db_con, "insert into tbl_email_config(`id`,`user_id`,`user_email`,`password`,`mailServer`,`port`,`ssl`,`validate`,`active`,`filters`) "
                            . "values(null,'$userid','$username','$password','$mailServer','$port','$ssl1','$valid','1','$filters')"); //or die('Error' . mysqli_error($db_con));
                    if ($mail) {
                        echo'<script>taskSuccess("profile","' . $lang['mail_configured_success'] . ' !");</script>';
                    }
                } else {
                    echo'<script>taskFailed("profile","' . $lang['already_configured'] . ' !");</script>';
                }
            } else {
                echo'<script>taskFailed("profile","' . $lang['unable_to_configure_email'] . ' !");</script>';
            }
            mysqli_close($db_con);
        }
		
        if (isset($_POST['editmailConfig'])) {
            $filters = "";
            $fltr_sub = $_POST['fltr_sub'];
            if (!empty($fltr_sub)) {
                if (empty($filters)) {
                    $filters .= 'SUBJECT "' . $fltr_sub . '"';
                } else {
                    $filters .= ',SUBJECT "' . $fltr_sub . '"';
                }
            }
            $fltr_body = $_POST['fltr_body'];
            if (!empty($fltr_body)) {
                if (empty($filters)) {
                    $filters .= 'BODY "' . $fltr_body . '"';
                } else {
                    $filters .= ',BODY "' . $fltr_body . '"';
                }
            }
            $fltr_from = $_POST['fltr_from'];
            if (!empty($fltr_from)) {
                if (empty($filters)) {
                    $filters .= 'FROM "' . $fltr_from . '"';
                } else {
                    $filters .= ',FROM "' . $fltr_from . '"';
                }
            }
            $fltr_to = $_POST['fltr_to'];
            if (!empty($fltr_to)) {
                if (empty($filters)) {
                    $filters .= 'TO "' . $fltr_to . '"';
                } else {
                    $filters .= ',TO "' . $fltr_to . '"';
                }
            }
            $fltr_date = $_POST['fltr_date'];
            if (!empty($fltr_date)) {
                if (empty($filters)) {
                    $filters .= 'SINCE "' . date('d-M-Y', strtotime($fltr_date)) . '"';
                } else {
                    $filters .= ',SINCE "' . date('d-M-Y', strtotime($fltr_date)) . '"';
                }
            }

            $userid = $_POST['uid'];
            $mailServer = $_POST['mailServer'];
            $username = $_POST['emailid'];
            $password = $_POST['password'];
            $port = $_POST['port'];
            $ssl1 = $_POST['ssl'];
            if ($ssl1 == 1) {
                $ssl = "ssl";
            } else {
                $ssl = "";
            }
            $valid = $_POST['valid'];
            if ($valid == 0) {
                $validate = "novalidate-cert";
            } else {
                $validate = "";
            }
            require_once './mailServerInt.php';
            $conEmail = connectionCheck($mailServer, $port, $ssl, $validate, $username, $password);
            if ($conEmail = 1) {
                $password = urlencode(base64_encode($password));
                $filters = mysqli_real_escape_string($db_con, $filters);
                $mail = mysqli_query($db_con, "update tbl_email_config set `user_email`='$username',`password`='$password',"
                        . "`mailServer`='$mailServer',`port`='$port',`ssl`='$ssl1',`validate`='$valid',`active`='1', `filters`='$filters' where user_id='$userid'"); //or die('Error' . mysqli_error($db_con));
                if ($mail) {
                    echo'<script>taskSuccess("profile","' . $lang['mail_configured_success'] . ' !");</script>';
                }
            } else {
                echo'<script>taskFailed("profile","' . $lang['unable_to_configure_email'] . ' !");</script>';
            }
            mysqli_close($db_con);
        }
        ?>
    </body>
</html>