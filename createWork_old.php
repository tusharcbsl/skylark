<!DOCTYPE html>
<html>
<?php
require_once './loginvalidate.php';
require_once './application/pages/head.php';
require_once './application/pages/function.php';
require_once './classes/fileManager.php';

//error_reporting(E_ALL);
$user_id = $_SESSION['cdes_user_id'];
//for user role
$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0"); // or die('Error:' . mysqli_error($db_con));

$rwgetRole = mysqli_fetch_assoc($chekUsr);
$wid = base64_decode(urldecode($_GET['wid']));
$workfid = preg_replace("/[^0-9 ]/", "", $wid);
if ($rwgetRole['workflow_initiate_file'] != '1' || !intval($workfid)) {
    header('Location: ./index');
}
?>
<link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<!--for searchable select-->
<link href="assets/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" />
<!-- Plugin Css-->
<link rel="stylesheet" href="assets/plugins/magnific-popup/css/magnific-popup.css" />
<link rel="stylesheet" href="assets/plugins/jquery-datatables-editable/datatables.css" />
<style>
    .tox-notifications-container {
        display: none !important;
    }
</style>
<?php
$folder = mysqli_query($db_con, "select * from tbl_storage_level where sl_depth_level='0'");
$rwFolder = mysqli_fetch_assoc($folder);
$slid = $rwFolder['sl_id'];
$parentid = $rwFolder['sl_parent_id'];
?>

<body class="fixed-left">
    <!-- Begin page -->
    <div id="wrapper">
        <!-- Top Bar Start -->
        <?php require_once './application/pages/topBar.php'; ?>
        <!-- Top Bar End -->
        <!-- ========== Left Sidebar Start ========== -->
        <?php
        require_once './application/pages/sidebar.php';
        ?>
        <!-- Left Sidebar End -->
        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="content-page">
            <!-- Start content -->
            <div class="content">
                <div class="container">
                    <?php
                    $getWorkflwDs = mysqli_query($db_con, "select * from tbl_workflow_master where workflow_id ='$workfid'"); // or die('Error in getWorkflw upload:' . mysqli_error($db_con));
                    $rwgetWorkflwIdDs = mysqli_fetch_assoc($getWorkflwDs);
                    ?>
                    <!-- Page-Title -->
                    <div class="row">
                        <div class="col-sm-12">
                            <ol class="breadcrumb">
                                <li>
                                    <a href="createWork"><?php echo $lang['Initiate_WorkFlow']; ?> / <?php echo $rwgetWorkflwIdDs['workflow_name']; ?></a>
                                </li>
                                <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                            </ol>
                        </div>
                    </div>
                    <style>
                        h3 {
                            text-align: center;
                        }
                    </style>
                    <div class="box box-primary">
                        <div class="panel">

                            <div class="panel-body">
                                <form method="post" enctype="multipart/form-data" id="wfform">

                                    <div class="row" id="descp">

                                        <div class="col-md-12 form-group">
                                            <?php
                                            $getWorkflwDs = mysqli_query($db_con, "select * from tbl_workflow_master where workflow_id ='$workfid'"); // or die('Error in getWorkflw upload:' . mysqli_error($db_con));
                                            $rwgetWorkflwIdDs = mysqli_fetch_assoc($getWorkflwDs);
                                            $tblnamedata = $rwgetWorkflwIdDs['form_tbl_name'];
                                            $getformid = mysqli_query($db_con, "select * from tbl_bridge_workflow_to_form where workflow_id='$workfid'"); //or die('Error in getWorkflw upload:' . mysqli_error($db_con));
                                            $form_id = mysqli_fetch_assoc($getformid);
                                            $formid = $form_id['form_id'];
                                            $formRequired = $rwgetWorkflwIdDs['form_req'];
                                            ?>
                                            <?php
                                            if ($rwgetWorkflwIdDs['form_req'] == 1 && mysqli_num_rows($getformid) > 0) {
                                                $formqry = mysqli_query($db_con, "select * from tbl_form_attribute where fid='$formid' order by aid asc"); // or die('Form:' . mysqli_error($db_con));
                                                $sqlqry = mysqli_query($db_con, "select * from  tbl_form_master where fid='$formid'"); //or die('Form:' . mysqli_error($db_con));

                                                $formname = mysqli_fetch_assoc($sqlqry);
                                                $i = 0;
                                                while ($row = mysqli_fetch_assoc($formqry)) {
                                                    $i++;
                                            ?>

                                                    <?php
                                                    if ($row['subtype'] == "h1" || $row['subtype'] == "h2" || $row['subtype'] == "h3") {
                                                    ?>
                                                        <<?= $row['subtype'] ?>><?= $row['label'] ?></<?= $row['subtype'] ?>>
                                                        <?php
                                                    }
                                                    if ($row['type'] == "radio-group") {
                                                        if ($i == 1) {
                                                        ?>

                                                            <div class="col-md-6 m-t-5">
                                                            <?php } ?>
                                                            <?php if (empty($row['dependency_id'])) { ?>
                                                                <label style="margin-top: 5px;"><?= $row['label']; ?></label><br>
                                                            <?php } ?>
                                                            <?php
                                                            $cid = $row['aid'];
                                                            $radioqry = mysqli_query($db_con, "select * from tbl_form_attribute where fid='$formid' and dependency_id='$cid' ");
                                                            while ($chkdata = mysqli_fetch_assoc($radioqry)) {
                                                            ?>

                                                                <label class="" style="margin-right: 10px;"> <input type="radio" value="<?= $chkdata['value'] ?>" name="<?= $row['name'] ?>"> <?= $chkdata['label'] ?></label>

                                                            <?php
                                                            }
                                                            if ($i == 1) {
                                                            ?>
                                                            </div>
                                                    <?php
                                                            }
                                                        }
                                                    ?>
                                                    <?php
                                                    if ($row['type'] == "text" || $row['type'] == "hidden" || $row['type'] == "file" || $row['type'] == "number" || $row['type'] == "email" || $row['type'] == "password") {
                                                    ?>
                                                        <div class="col-md-6 m-t-5">
                                                            <div class="text">
                                                                <label><?= $row['label'] ?></label> <input type="<?= $row['type'] ?>" value="<?= $row['value'] ?>" class="<?= $row['class'] ?>" placeholder="<?= $row['placeholder'] ?>" name="<?= $row['name'] ?>" maxlength="<?= $row['maxlength'] == 0 ? '255' : $row['maxlength'] ?>" <?= $row['required'] == 1 ? 'required' : '' ?>>
                                                            </div>
                                                        </div>
                                                    <?php } ?>

                                                    <?php
                                                    if ($row['type'] == "date") {
                                                    ?>
                                                        <div class="col-md-6 m-t-5">
                                                            <label><?= $row['label'] ?></label>
                                                            <div class="input-group">
                                                                <input type="text" value="<?= $row['value'] ?>" class="form-control datepicker" placeholder="<?= $row['placeholder'] ?>" name="<?= $row['name'] ?>" maxlength="<?= $row['maxlength'] == 0 ? '255' : $row['maxlength'] ?>" <?= $row['required'] == 1 ? 'required' : '' ?>>
                                                                <span class="input-group-addon bg-custom b-0 text-white"><i class="icon-calender"></i></span>
                                                            </div>
                                                        </div>

                                                        <div id="res"></div>
                                                    <?php } ?>
                                                    <?php
                                                    if ($row['type'] == "select") {
                                                    ?>
                                                        <div class="col-md-6 m-t-5">

                                                            <label><?= $row['label']; ?></label>
                                                            <select name="<?= $row['name']; ?>" class="<?= $row['class']; ?>" <?= $row['required'] == 1 ? 'required' : '' ?> onchange="leavetype(this.value)">
                                                                <?php
                                                                $selectid = $row['aid'];
                                                                $selectqry = mysqli_query($db_con, "select * from tbl_form_attribute where fid='$formid' and dependency_id='$selectid'");
                                                                while ($chkdata = mysqli_fetch_assoc($selectqry)) {
                                                                ?>
                                                                    <<?= $chkdata['type'] ?>><?= $chkdata['label'] ?> <<?= $chkdata['type'] ?>>
                                                                        <?php } ?>
                                                            </select>

                                                        </div>
                                                        <div id="co" class=""></div>
                                                    <?php } ?>
                                                    <?php
                                                    if ($row['type'] == "checkbox-group") {
                                                    ?>
                                                        <div class="col-md-6 m-t-5">

                                                            <?php if (empty($row['dependency_id'])) { ?>
                                                                <label style="margin-top:10px;"><?= $row['label']; ?></label><br>
                                                            <?php } ?>

                                                            <?php
                                                            $chkid = $row['aid'];
                                                            $chkqry = mysqli_query($db_con, "select * from tbl_form_attribute where fid='$formid' and dependency_id='$chkid'");
                                                            while ($chkdata = mysqli_fetch_assoc($chkqry)) {
                                                            ?>
                                                                <label class="<?= $row['inline'] == 1 ? 'checkbox-inline' : '' ?>" style="margin-right: 10px;"> <input type="checkbox" name="<?= $chkdata['name'] ?>" value="<?= $chkdata['value'] ?>"><?= $chkdata['label'] ?></label>
                                                            <?php } ?>
                                                        </div>
                                                    <?php } ?>
                                                    <?php
                                                    if ($row['subtype'] == "tinymce") {
                                                    ?>
                                                        <div class="col-md-12 m-t-5">
                                                            <div class="">
                                                                <label><?= $row['label']; ?></label>
                                                                <textarea class="<?= $row['class'] ?>" rows="5" name="<?= $row['name'] ?>" id="editor"><?= $row['placeholder']; ?><?= isset($row['value']) ? $row['value'] : '' ?></textarea>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                    <?php
                                                    if ($row['subtype'] == "p") {
                                                    ?>
                                                        <div class="col-md-12 m-t-5">
                                                            <div class="form-group">
                                                                <label><?= $row['label']; ?></label>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                    <?php
                                                    if ($row['subtype'] == "textarea") {
                                                    ?>
                                                        <div class="col-md-12 m-t-5">
                                                            <div class="form-group">
                                                                <label><?= $row['label']; ?></label>
                                                                <textarea class="<?= $row['class'] ?>" rows="5" name="<?= $row['name'] ?>"><?= $row['placeholder']; ?></textarea>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                <?php
                                                }
                                            }
                                            if ($rwgetWorkflwIdDs['form_req'] == 0 && $rwgetWorkflwIdDs['form_type'] == 0) {
                                                ?>
                                                <textarea class="form-control" rows="5" name="taskRemark" id="editor"></textarea>
                                            <?php } ?>
                                            <?php
                                            $getworkflowformid = mysqli_query($db_con, "select * from tbl_bridge_workflow_to_form where workflow_id='$workfid'"); //or die('Error in getWorkflw upload:' . mysqli_error($db_con));
                                            if (mysqli_num_rows($getworkflowformid) == 0 && $rwgetWorkflwIdDs['form_req'] == 1) {
                                                $getworkdesp = mysqli_query($db_con, "select * from tbl_workflow_master where workflow_id='$workfid'"); //or die(mysqli_error($db_con));
                                                $resultdesp = mysqli_fetch_assoc($getworkdesp);
                                            ?>
                                                <textarea class="form-control" rows="5" name="taskRemark" id="editor"><?= $resultdesp['workflow_description'] ?></textarea>
                                            <?php }
                                            ?>
                                        </div>
                                    </div>

                                    <?php if ($rwgetWorkflwIdDs['form_type'] == 1 || $rwgetWorkflwIdDs['form_type'] == 2) { ?>
                                        <!-- <form method="post" action="rfi_form.php" enctype="multipart/form-data"> -->
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th class="col-md-2" rowspan="2" style="vertical-align:middle; text-align:center;">
                                                        <img src="assets/images/rail.JPEG" class="south_railway" alt="Italian Trulli">
                                                    </th>
                                                    <th class="col-md-8" style="text-align:center; font-size:large; vertical-align:middle;">
                                                        <u>REQUEST FOR INSPECTION (RFI)</u>
                                                    </th>
                                                    <?php if ($rwgetWorkflwIdDs['form_type'] == 1) { ?>
                                                        <th class="col-md-2" rowspan="2" style="vertical-align:middle; text-align:center;">
                                                            <img src="assets/images/pra1.JPEG" class="south_railway" alt="Italian Trulli">
                                                        </th>
                                                    <?php } else { ?>
                                                        <th class="col-md-2" rowspan="2" style="vertical-align:middle; text-align:center;">
                                                            <img src="assets/images/raipur.jpg" class="south_railway" alt="Italian Trulli">
                                                        </th>
                                                    <?php } ?>
                                                </tr>
                                                <tr>
                                                    <?php if ($rwgetWorkflwIdDs['form_type'] == 1) { ?>
                                                        <th class="col-md-8" style="vertical-align:middle; text-align:center;">
                                                            Project Doubling of Railway Project comprising the section commencing from(--) Road station (End CH 967.055)
                                                            to Surajpur Road Station (End CH : 1006.44) (KM-39.385 KM) beside existing single 84 line in the state of chhattisgarh in the
                                                            south East centeral Railway Zone Agt No: SECR/SECRC/CMI/2024/0008/ dt 14-Mar-2024.
                                                        </th>
                                                    <?php } else { ?>
                                                        <th class="col-md-8" style="vertical-align:middle; text-align:center;">
                                                            Major Up-Gradation of Raipur Railway Station of SECR on EPC Mode
                                                        </th>
                                                    <?php } ?>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                        </table>

                                        <table class="table table-bordered upper">
                                            <thead>
                                                <?php if ($rwgetWorkflwIdDs['form_type'] == 1) { ?>
                                                    <tr>
                                                        <th class="col-md-6" colspan="3" style="text-align:left; vertical-align:middle;">Client : South East Central Railway</th>
                                                        <th class="col-md-6" colspan="3" style="text-align:center; vertical-align:middle;">Contractor : Barbrik Project Limited</th>
                                                    </tr>
                                                <?php } else { ?>
                                                    <th class="col-md-6" colspan="3" style="text-align:left; vertical-align:middle;">Client : South East Central Railway</th>

                                                    <th class="col-md-6" colspan="3" style="text-align:center; vertical-align:middle;">
                                                        Contractor : RPP-SATHYAMOORTHY JV<br>
                                                        Sub-Contractor : PRA INDIA PVT LTD
                                                    </th>
                                                <?php } ?>
                                                <tr>
                                                    <th class="col-md-2 top-center" style="text-align:center; vertical-align:middle;">RFI No</th>
                                                    <th class="col-md-2 top-center" style="text-align:center; vertical-align:middle;">RFI Date</th>
                                                    <th class="col-md-2 top-center" style="text-align:center; vertical-align:middle;">Type <br> (Regular/Spot)</th>
                                                    <th class="col-md-2 top-center" style="text-align:center; vertical-align:middle;">Name Of the Contractor's Engineer offering the work for inspection</th>
                                                    <th class="col-md-2 top-center" style="text-align:center; vertical-align:middle;">Item No as per contract <br> (for payment)</th>
                                                    <th class="col-md-2 top-center" style="text-align:center; vertical-align:middle;">Inspection Required On</th>
                                                </tr>
                                            </thead>
                                        </table>

                                        <table class="table table-bordered upper">
                                            <tr>
                                                <th class="col-md-2">
                                                    <?php

                                                    $rfi_query = "SELECT id,rfi_no FROM tbl_railway_master where railway_type=" . $rwgetWorkflwIdDs['form_type'] . " ORDER BY id DESC LIMIT 1";
                                                    $result = mysqli_query($db_con, $rfi_query);

                                                    // Initialize variable to store the RFI ID
                                                    if ($rwgetWorkflwIdDs['form_type'] == '1') {
                                                        $rfi_id = 3399;
                                                    }
                                                    if ($rwgetWorkflwIdDs['form_type'] == '2') {
                                                        $rfi_id = 10;
                                                    }


                                                    // Check if the query returned a result
                                                    if ($result && $result->num_rows > 0) {
                                                        // Fetch the row and store the ID in the variable
                                                        $row = $result->fetch_assoc();
                                                        $rfi_id = $row['rfi_no'];
                                                    }

                                                    $rfi_id = $rfi_id + 1;
                                                    $num = 1;
                                                    ?>

                                                    <input type="text" class="form-control" name="rfi_no" value="<?php echo $rfi_id; ?>" readonly>
                                                    <?php $num++; ?>
                                                </th>
                                                <th class="col-md-2">
                                                    <input type="date" id="date1" class="form-control" name="rfi_date" placeholder="Start" required />
                                                </th>
                                                <th class="col-md-2">
                                                    <select id="selectType" class="form-control" name="type_regular">
                                                        <option value="Regular">Regular</option>
                                                        <option value="Spot">Spot</option>
                                                    </select>
                                                </th>
                                                <th class="col-md-2">
                                                    <input type="text" class="form-control" placeholder="Name of Contractor" name="name_of_the_contractor">
                                                </th>
                                                <th class="col-md-2">
                                                    <?php
                                                    $sql = "SELECT * FROM tbl_railway_item_no";
                                                    $query = mysqli_query($db_con, $sql);
                                                    ?>

                                                    <select class="form-control" name="item_no_as_per">
                                                        <option value="">Select Item No</option>
                                                        <?php
                                                        if (mysqli_num_rows($query) > 0) {
                                                            while ($data = mysqli_fetch_assoc($query)) {
                                                                echo "<option value='" . $data['railway_item_no'] . "'>" . $data['railway_item_no'] . "</option>";
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </th>
                                                <th class="col-md-2">
                                                    <input type="date" id="date2" class="form-control" placeholder="End" name="inspection_required_date" />
                                                </th>
                                            </tr>
                                        </table>

                                        <table class="table table-bordered upper">
                                            <tr>
                                                <th class="col-md-2" rowspan="2" colspan="2">Location / Chainage</th>
                                                <th class="col-md-4" rowspan="2">
                                                    <div class="col-md-6">
                                                        <input type="number" id="fromValue" class="form-control" placeholder="From (967.055)" step="0.001" name="location_from">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input type="number" id="toValue" class="form-control" placeholder="To (1006.44)" step="0.001" name="location_to">
                                                    </div>
                                                    <div class="col-md-12">
                                                        <p id="errorMessage" style="color: red; display: none;">Value must be between 967.055 and 1006.44</p>
                                                    </div>
                                                </th>
                                                <th class="col-md-4" colspan="1" style="text-align:left;" readonly>Name Of the Inspecting Engineer</th>
                                                <th class="col-md-2" readonly>Inspected On</th>
                                            </tr>
                                            <tr>
                                                <th style="height: 40px;">
                                                    <input type="text" class="form-control" placeholder="Name of Inspecting Engineer" name="name_of_the_inspecting_engineer" readonly>
                                                </th>
                                                <th style="height: 40px;">
                                                    <input type="date" class="form-control" placeholder="Start" name="inspected_on_date" readonly />
                                                </th>
                                            </tr>
                                        </table>

                                        <table class="table table-bordered upper">
                                            <tr>
                                                <th class="col-md-12" colspan="6">
                                                    Request for Inspection of the following works, Which are /will be ready for inspection
                                                </th>
                                            </tr>
                                            <tr>
                                                <th class="col-md-3">1. C&G</th>
                                                <th class="col-md-3"><input type="checkbox" class="form-check-input" id="C&G" name="c_and_g"></th>
                                                <th class="col-md-3">8. Concreting</th>
                                                <th class="col-md-3"><input type="checkbox" class="form-check-input" id="concreting" name="concreting"></th>
                                            </tr>
                                            <tr>
                                                <th class="col-md-3">2. Earthwork</th>
                                                <th class="col-md-3"><input type="checkbox" class="form-check-input" id="earthwork" name="earthwork"></th>
                                                <th class="col-md-3">9. Drain/Retaining Wall</th>
                                                <th class="col-md-3"><input type="checkbox" class="form-check-input" id="drain_retaining_wall" name="drain_retaining_wall"></th>
                                            </tr>
                                            <tr>
                                                <th class="col-md-3">3. Blanketing</th>
                                                <th class="col-md-3"><input type="checkbox" class="form-check-input" id="blanketing" name="blanketing"></th>
                                                <th class="col-md-3">10. Roads</th>
                                                <th class="col-md-3"><input type="checkbox" class="form-check-input" id="roads" name="roads"></th>
                                            </tr>
                                            <tr>
                                                <th class="col-md-3">4. Survey</th>
                                                <th class="col-md-3"><input type="checkbox" class="form-check-input" id="survey" name="survey"></th>
                                                <th class="col-md-3">11. Utilities</th>
                                                <th class="col-md-3"><input type="checkbox" class="form-check-input" id="utilities" name="utilities"></th>
                                            </tr>
                                            <tr>
                                                <th class="col-md-3">5. Safety</th>
                                                <th class="col-md-3"><input type="checkbox" class="form-check-input" id="safety" name="safety"></th>
                                                <th class="col-md-3">12. Dismantling of Pway</th>
                                                <th class="col-md-3"><input type="checkbox" class="form-check-input" id="dismantling_of_Pway" name="dismantling_of_Pway"></th>
                                            </tr>
                                            <tr>
                                                <th class="col-md-3">6. QC/Material</th>
                                                <th class="col-md-3"><input type="checkbox" class="form-check-input" id="qc_material" name="qc_material"></th>
                                                <th class="col-md-3">13. Bridge Work</th>
                                                <th class="col-md-3"><input type="checkbox" class="form-check-input" id="bridge_work" name="Bridge_Work"></th>
                                            </tr>
                                            <tr>
                                                <th class="col-md-3">7. Shuttering/Reinforcement</th>
                                                <th class="col-md-3"><input type="checkbox" class="form-check-input" id="shuttering_reinforcement" name="shuttering_reinforcement"></th>
                                                <th class="col-md-3">14. Other</th>
                                                <th class="col-md-3"><input type="checkbox" class="form-check-input" id="other" name="other"></th>
                                            </tr>

                                            <tr>
                                                <th colspan="" style="text-align:left; vertical-align:top; height: 200px">Description of Work offered for Inspection</th>
                                                <th colspan="3" style="text-align:left; vertical-align:top;">
                                                    <input type="text" style="height: 50px;" class="form-control" placeholder="Description of Work" name="description_of_work">
                                                </th>
                                            </tr>

                                            <tr>
                                                <th class="col-md-3" colspan="1" style="text-align:left; vertical-align:top; height: 100px">Enclosures attached with RFI</th>
                                                <th class="col-md-9" colspan="3" style="text-align:center; vertical-align:middle; height: 100px"></th>
                                            </tr>
                                            <tr>
                                                <th class="col-md-6" colspan="2" style="text-align:left; vertical-align:top; height: 100px">Signature of the Contractor's Representative requesting for Inspection</th>
                                                <th class="col-md-6" colspan="3">
                                                    <textarea class="form-control" style="text-align:left; vertical-align:top;" name="signature_of_the_contractor" readonly><?php echo $_SESSION['admin_user_name'] . ' ' . $_SESSION['admin_user_last'] ?></textarea>
                                                </th>
                                            </tr>
                                            <tr>
                                                <th class="col-md-3" colspan="4" style="text-align:left; vertical-align:top; height: 200px">Remarks of the Inspection Engineer (Representative of Authority Engineer)</th>
                                            </tr>

                                            <tr>
                                                <th colspan="2">Signature of the Inspection Engineer Representative of Authority Engineer</th>
                                                <th colspan="3"></th>
                                            </tr>
                                            <tr>
                                                <th colspan="2" style="text-align:right;">Date :</th>
                                                <th colspan="3"><span name="date"></span></th>
                                            </tr>
                                            <tr>
                                                <th colspan="2" style="text-align:right;">Name :</th>
                                                <th colspan="3"><span name="name"></span></th>
                                            </tr>
                                            <tr>
                                                <th colspan="2" style="text-align:right;">Designation :</th>
                                                <th colspan="3"><span name="designation"></span></th>
                                            </tr>
                                        </table>

                                        <div class="col-md-14">
                                            <div class="container">
                                                <div class="card-box">
                                                    <div id="dynamicForm">
                                                        <div class="row" id="formRows">
                                                            <div class="col-md-1"></div>
                                                            <div class="col-md-1">
                                                                <div class="mb-3">
                                                                    <a href="javascript:void(0)" class="btn btn-primary add-btn true_false" id="addRow">
                                                                        <i class="fa fa-plus" aria-hidden="true"></i>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="mobile">Remark:</label>
                                                                    <input type="text" name="remark[]" class="form-control" placeholder="Enter Remark" required>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="">Upload Attachment</label>
                                                                    <input type="file" name="file[]" class="form-control">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- <div class="col-md-12 mt-6" style="margin-bottom: 25px;">
                                                    <input type="submit" class="btn btn-primary" name="submit" value="Submit">
                                                </div> -->
                                                <!-- </form> -->
                                            <?php } ?>



                                            <div class="row">
                                                <!-- <div class="col-md-6 form-group m-t-10">
                                                    <label style="color: olivedrab"> <?php echo $lang['Ch_fl_op']; ?> :- </label>
                                                    <input class="filestyle" id="myImage" multiple name="fileName[]" data-buttonname="btn-primary" id="filestyle-4" style="position: absolute; clip: rect(0px, 0px, 0px, 0px);" tabindex="-1" type="file">
                                                    <input type="hidden" id="pCount" name="pageCount">
                                                </div> -->
                                                <div class="col-md-6 m-t-10">
                                                    <label for="name">Task Remark</label>
                                                    <input type="text" class="form-control" placeholder="Enter Remark" name="taskRemark" id="" />
                                                </div>

                                                <div class="col-md-6 form-group m-t-40">

                                                </div>
                                            </div>
                                            <!-- <div class="row">
                                                <div style="display: none" id="hidden_div">
                                                    <label><?php echo $lang['Select_Storage']; ?>:-</label>
                                                    <div class="row">

                                                        <div class="col-md-3 form-group">
                                                            <select class="form-control select2" name="moveToParentId" id="parentMoveLevel">
                                                                <option selected disabled style="background: #808080; color: #121213;"><?php echo $lang['Select_Storage']; ?></option>
                                                                <?php
                                                                $storeID = mysqli_query($db_con, "select * from tbl_storagelevel_to_permission where user_id= '$user_id'") or die('Error: ' . mysqli_error($db_con));
                                                                while ($rwstoreID = mysqli_fetch_assoc($storeID)) {
                                                                    $sl_Pid = $rwstoreID['sl_id'];
                                                                    $storeName = mysqli_query($db_con, "select * from tbl_storage_level where sl_id in($sl_Pid)  and delete_status=0") or die('Error: ' . mysqli_error($db_con));
                                                                    $rwstoreName = mysqli_fetch_assoc($storeName);
                                                                ?>
                                                                    <option value="<?php echo $rwstoreName['sl_id']; ?>"><?php echo $rwstoreName['sl_name']; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>

                                                        <div class="" id="child">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> -->
                                            <br><br>
                                            <div class="clearfix"></div>
                                            <div class="row">
                                                <input type="hidden" name="wfid" value="<?= $workfid ?>">
                                                <input type="hidden" name="railway_type" value="<?php echo $rwgetWorkflwIdDs['form_type']; ?>">
                                                <input type="hidden" name="form_req" id="form_req" value="<?php echo $rwgetWorkflwIdDs['form_req']; ?>">
                                                <input type="hidden" name="pdf_req" id="pdf_req" value="<?php echo $rwgetWorkflwIdDs['pdf_req']; ?>">
                                                <input type="hidden" name="uploaddWfd">

                                                <div class="col-sm-12">
                                                    <button class="btn btn-primary" id="subb"><?php echo $lang['Submit']; ?></button>
                                                    <button class="btn btn-danger pull-right m-r-5" type="reset" onclick="fun_hid()"><?php echo $lang['Reset']; ?></button>
                                                </div>
                                            </div>
                                </form>

                                <script>
                                    document.getElementById('selectType').addEventListener('change', function() {
                                        const date1 = document.getElementById('date1');
                                        const date2 = document.getElementById('date2');
                                        const selectedType = this.value;

                                        if (selectedType === 'Spot') {
                                            // Automatically fill date2 with date1's value
                                            date2.value = date1.value;
                                            date2.setAttribute('readonly', true); // Make date2 read-only
                                        } else if (selectedType === 'Regular') {
                                            date2.removeAttribute('readonly');
                                            date2.value = ''; // Clear date2 for new input
                                        }
                                    });

                                    document.getElementById('date1').addEventListener('change', function() {
                                        const date2 = document.getElementById('date2');
                                        const selectType = document.getElementById('selectType').value;

                                        if (selectType === 'Spot') {
                                            // For Spot, ensure date2 is same as date1
                                            date2.value = this.value;
                                        } else if (selectType === 'Regular') {
                                            // For Regular, ensure date2 is after date1
                                            if (date2.value && date2.value < this.value) {
                                                Swal.fire({
                                                    title: 'Error!',
                                                    text: 'End date must be after the start date.',
                                                    icon: 'error'
                                                });
                                                date2.value = ''; // Clear date2 if it's not valid
                                            }
                                        }
                                    });

                                    document.getElementById('date2').addEventListener('change', function() {
                                        const date1 = document.getElementById('date1').value;

                                        if (this.value < date1) {
                                            Swal.fire({
                                                title: 'Error!',
                                                text: 'Inspection Required On Date is not Select Grater than RFI Date  when select Regular.',
                                                icon: 'error'
                                            });
                                            this.value = ''; // Clear invalid date2
                                        }
                                    });
                                </script>


                                <!-- range fix -->

                                <script>
                                    document.getElementById('fromValue').addEventListener('input', function() {
                                        validateRange(this.value, 967.055, 1006.44);
                                    });

                                    document.getElementById('toValue').addEventListener('input', function() {
                                        validateRange(this.value, 967.055, 1006.44);
                                    });

                                    function validateRange(value, min, max) {
                                        const errorMessage = document.getElementById('errorMessage');
                                        if (value < min || value > max) {
                                            errorMessage.style.display = 'block'; // Show error message
                                        } else {
                                            errorMessage.style.display = 'none'; // Hide error message
                                        }
                                    }
                                </script>


                                <script>
                                    document.addEventListener("DOMContentLoaded", function() {
                                        const formRows = document.getElementById('dynamicForm');

                                        document.getElementById('addRow').addEventListener('click', function() {
                                            const newRow = document.createElement('div');
                                            newRow.classList.add('row', 'dynamic-row');
                                            newRow.innerHTML = `
                                                <div class="col-md-1"></div>
                                                <div class="col-md-1">
                                                    <div class="mb-3">
                                                        <a href="javascript:void(0)" class="btn btn-danger remove-btn">
                                                            <i class="fa fa-minus" aria-hidden="true"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="mobile">Remark:</label>
                                                        <input type="text" name="remark[]" class="form-control" placeholder="Enter Remark" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="">Upload Attachment</label>
                                                        <input type="file" name="file[]" class="form-control">
                                                    </div>
                                                </div>
                                            `;

                                            formRows.appendChild(newRow);

                                            newRow.querySelector('.remove-btn').addEventListener('click', function() {
                                                formRows.removeChild(newRow);
                                            });
                                        });
                                    });
                                </script>

                            </div>
                        </div>
                    </div>
                    <!-- end: page -->

                </div> <!-- end Panel -->
            </div> <!-- container -->

        </div> <!-- content -->
        <div id="preview" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-full">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

                        <h4><?php echo $lang['Preview_Form']; ?></h4>
                    </div>
                    <div class="modal-body" style="width:100%; margin: auto; text-align: center; vertical-align: middle;">
                        <div id="viewpreview">
                        </div>
                        <div id="formpreview" style="display:none;width:100%; margin: auto; text-align:center; vertical-align: middle; padding:5px;">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="uploaddWfd">

                        <button class="btn btn-primary nextBtn" id="subb"><?php echo $lang['Submit']; ?></button>
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?> </button>
                    </div>
                </div>
            </div>
        </div>

        <?php require_once './application/pages/footer.php'; ?>
    </div>
    <?php require_once './application/pages/footerForjs.php'; ?>
    <script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
    <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>
    <!--for searchable select -->
    <script type="text/javascript" src="assets/plugins/jquery-quicksearch/jquery.quicksearch.js"></script>
    <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
    <script src="assets/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
    <script src="assets/plugins/tinymce/tinymce.min.js"></script>
    <link href="assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet">
    <script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            if ($("#editor").length > 0) {
                tinymce.init({
                    selector: "textarea#editor",
                    //theme: "modern",
                    height: 200,
                    plugins: [
                        "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
                        "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                        "save table contextmenu directionality emoticons template paste textcolor"
                    ],
                    toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | forecolor backcolor emoticons",
                    style_formats: [{
                            title: 'Bold text',
                            inline: 'b'
                        },
                        {
                            title: 'Red text',
                            inline: 'span',
                            styles: {
                                color: '#ff0000'
                            }
                        },
                        {
                            title: 'Red header',
                            block: 'h1',
                            styles: {
                                color: '#ff0000'
                            }
                        },
                    ]
                });
            }
        });
        $(document).ready(function() {
            var d1 = new Date();
            d1 = d1.setDate(d1.getDate() - 30);
            var d = new Date(d1);
            var month = d.getMonth() + 1;
            var day = d.getDate();
            var output = d.getFullYear() + '-' +
                (('' + month).length < 2 ? '0' : '') + month + '-' +
                (('' + day).length < 2 ? '0' : '') + day;
            //alert(output);
            $('.datepicker').datepicker({
                format: "yyyy-mm-dd",
                startDate: output,
                autoclose: true
            });
        });

        function leavetype(res) {
            var result = res.split("(");

            var co = result[0];
            var co1 = result[1];
            if (co == "CO" || co1 == "CO)") {


                //alert(lbl);
                $.post("application/ajax/formFieldDynamic.php", {}, function(result, status) {
                    if (status == 'success') {
                        $("#co").html(result);

                    }
                });

            } else {
                $("#co").html("");
            }

        }
    </script>

    <!--show wait gif-->
    <div style=" display: none; background: rgba(0,0,0,0.5); width: 100%; z-index: 2000; position: fixed; top:0;" id="wait">
        <img src="assets/images/proceed.gif" alt="load" style=" margin-left: 48%; margin-top: 250px; width: 100px; height:100px; position: fixed;" />
    </div>
    <script>
        //for wait gif display after submit
        var heiht = $(document).height();
        //alert(heiht);
        $('#wait').css('height', heiht);
        $('#subb').click(function() {

            $('#wait').show();
            //$('#wait').css('height',heiht);

            $('#afterClickHide').hide();
            document.getElementById("wfform").submit();
            return true;
        });
    </script>

    <script type="text/javascript">
        $(document).ready(function() {
            $('form').parsley();

        });
        $(".select2").select2();
        //firstname last name 
        $("input#groupName").keypress(function(e) {
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
        $("#parentMoveLevel").change(function() {
            var lbl = $(this).val();
            //alert(lbl);
            $.post("application/ajax/uploadWorkFlow.php", {
                parentId: lbl,
                levelDepth: 0,
                sl_id: <?php echo $slid; ?>
            }, function(result, status) {
                if (status == 'success') {
                    $("#child").html(result);
                    //alert(result);
                }
            });
        });

        $("#wfid").change(function() {
            var wfId = $(this).val();

            //alert(lbl);
            $("#subb").show();

        });
        $('input[type=file]').change(function() {
            $("#hidden_div").show();
        });

        function fun_hid() {
            $("#hidden_div").hide();

        }

        //image detail              
        $('#myImage').bind('change', function() {
            //this.files[0].size gets the size of your file.
            if (this.files[0].type == 'application/pdf') {
                var reader = new FileReader();
                reader.readAsBinaryString(this.files[0]);
                reader.onloadend = function() {
                    var count = reader.result.match(/\/Type[\s]*\/Page[^s]/g).length;
                    // alert(count);
                    $("#pageCount").html(count);
                    $("#pCount").val(count);
                    // console.log('Number of Pages:',count );
                }
            } else {
                // alert("count");
                $("#pageCount").html('1');
                $("#pCount").val('1');
            }

        });
        //file button validation
        $("#myImage").change(function() {
            var size = document.getElementById("myImage").files[0].size;
            // alert(size);

            //alert(lbl);
            $.post("application/ajax/valiadate_client_memory.php", {
                size: size
            }, function(result, status) {
                if (status == 'success') {
                    //$("#stp").html(result);
                    var res = JSON.parse(result);
                    if (res.status == "true") {
                        // $("#memoryres").html("<span style=color:green>" + res.msg + "</span>");
                        $.Notification.autoHideNotify('success', 'top center', 'Success', res.msg)
                        $("#dataprev").attr('data-target');
                        $("#dataprev").removeAttr('disabled', 'disabled');
                        $("#hidden_div").show();
                    } else {
                        $.Notification.autoHideNotify('warning', 'top center', 'Oops', res.msg)
                        $("#dataprev").attr('disabled', 'disabled');
                        $("#dataprev").removeAttr('data-target');
                        $("#hidden_div").hide();
                        //$("#memoryres").html("<span style=color:red>" + res.msg + "</span>");
                    }

                }
            });
        });
    </script>

    <!--form validation init-->
    <script>
        $("#wfid").change(function() {
            var wfId = $(this).val();
            //alert(lbl);
            $.post("application/ajax/RequireForm.php", {
                wid: wfId
            }, function(result, status) {
                if (status == 'success') {
                    $("#descp").html(result);
                    $("#descp").show();
                }
            });
        });
    </script>
    <script>
        //convert number to words
        //CRC Form Code Starts
        function convertNumberToWords(amount) {
            var words = new Array();
            words[0] = '';
            words[1] = 'One';
            words[2] = 'Two';
            words[3] = 'Three';
            words[4] = 'Four';
            words[5] = 'Five';
            words[6] = 'Six';
            words[7] = 'Seven';
            words[8] = 'Eight';
            words[9] = 'Nine';
            words[10] = 'Ten';
            words[11] = 'Eleven';
            words[12] = 'Twelve';
            words[13] = 'Thirteen';
            words[14] = 'Fourteen';
            words[15] = 'Fifteen';
            words[16] = 'Sixteen';
            words[17] = 'Seventeen';
            words[18] = 'Eighteen';
            words[19] = 'Nineteen';
            words[20] = 'Twenty';
            words[30] = 'Thirty';
            words[40] = 'Forty';
            words[50] = 'Fifty';
            words[60] = 'Sixty';
            words[70] = 'Seventy';
            words[80] = 'Eighty';
            words[90] = 'Ninety';
            amount = amount.toString();
            var atemp = amount.split(".");
            var number = atemp[0].split(",").join("");
            var n_length = number.length;
            var words_string = "";
            if (n_length <= 9) {
                var n_array = new Array(0, 0, 0, 0, 0, 0, 0, 0, 0);
                var received_n_array = new Array();
                for (var i = 0; i < n_length; i++) {
                    received_n_array[i] = number.substr(i, 1);
                }
                for (var i = 9 - n_length, j = 0; i < 9; i++, j++) {
                    n_array[i] = received_n_array[j];
                }
                for (var i = 0, j = 1; i < 9; i++, j++) {
                    if (i == 0 || i == 2 || i == 4 || i == 7) {
                        if (n_array[i] == 1) {
                            n_array[j] = 10 + parseInt(n_array[j]);
                            n_array[i] = 0;
                        }
                    }
                }
                value = "";
                for (var i = 0; i < 9; i++) {
                    if (i == 0 || i == 2 || i == 4 || i == 7) {
                        value = n_array[i] * 10;
                    } else {
                        value = n_array[i];
                    }
                    if (value != 0) {
                        words_string += words[value] + " ";
                    }
                    if ((i == 1 && value != 0) || (i == 0 && value != 0 && n_array[i + 1] == 0)) {
                        words_string += "Crores ";
                    }
                    if ((i == 3 && value != 0) || (i == 2 && value != 0 && n_array[i + 1] == 0)) {
                        words_string += "Lakhs ";
                    }
                    if ((i == 5 && value != 0) || (i == 4 && value != 0 && n_array[i + 1] == 0)) {
                        words_string += "Thousand ";
                    }
                    if (i == 6 && value != 0 && (n_array[i + 1] != 0 && n_array[i + 2] != 0)) {
                        words_string += "Hundred and ";
                    } else if (i == 6 && value != 0) {
                        words_string += "Hundred ";
                    }
                }
                words_string = words_string.split("  ").join(" ");
            }
            return words_string;
        }

        function checkCODate(date) {
            $.post("application/ajax/formFieldAjax.php", {
                userid: <?= $user_id; ?>,
                date: date
            }, function(result, status) {
                if (status == 'success') {
                    var data = JSON.parse(result);

                    if (data.Status == "true") {
                        $("#result").html("<p style='color:red'>Already Applied For CO Date</p>");
                        $("#subb").attr("disabled", "disabled");
                    }
                    if (data.Status == "False") {

                        $("#subb").removeAttr("disabled");
                        $("#result").html("");
                    }

                }
            });
        }
        //dynamic cash voucher code
        var k = 0;
        $(".ccenter").click(function() {

            if (k == 0) {

                $.post("application/ajax/ajaxCostCenter.php", {
                    class: "ccenter"
                }, function(result, status) {
                    if (status == 'success') {
                        $(".ccenter").html(result);
                        //$("#descp").show();

                        $.post("application/ajax/ajaxCostCenter.php", {
                            class: "whouse"
                        }, function(result, status) {
                            if (status == 'success') {
                                $(".whouse").html(result);
                                $('#descp input[name="wf_debitac"]').attr("id", "debitac");
                                $('#descp input[name="wf_no"]').attr("id", "number");
                                $("#number").attr('readonly', 'readonly');

                                //                       var value= $(" input[type=text]:nth-child(1)").val();
                                //                        console.log("this is run"+value);
                                //$("#descp").show();

                            }
                        });
                        $.post("application/ajax/ajaxCostCenter.php", {
                            class: "result"
                        }, function(result, status) {
                            if (status == 'success') {
                                $("#res").html(result);
                                //$("#descp").show();

                            }
                        });
                        k++;
                    }
                });
            }


        });
        $(".whouse").change(function() {
            var wfid = $(this).val();
            $.post("application/ajax/ajaxCostCenter.php", {
                whid: wfid
            }, function(result, status) {
                if (status == 'success') {
                    var jsonResult = JSON.parse(result);
                    if (jsonResult.status == "true") {
                        $("#debitac").parent("div").html(jsonResult.res);
                    }
                    if (jsonResult.status == "false") {
                        $("#debitac").val(jsonResult.res);
                    }

                    // $("#debitac").val(result);
                    //$("#descp").show();
                    //alert(result);


                }
            });
            $.post("application/ajax/ajaxCostCenter.php", {
                tblname: "<?= $tblnamedata ?>"
            }, function(result, status) {
                if (status == 'success') {
                    $("#number").val(result);
                    //$("#descp").show();


                }
            });
        });
        var ttl = 0;

        function totalsum(valu) {
            var arr = $('input[name="amt[]"]').map(function() {
                return $(this).val()
            }).get();
            var hasAnyEmptyElement = arr.includes("");
            //console.log(hasAnyEmptyElement);
            if (hasAnyEmptyElement) {

                alert("Enter Valid Amount");
            } else {
                var total = 0;
                for (var i in arr) {
                    total += parseInt(arr[i]);
                }
                $("#trupee").val(total);
                if (total != 0) {
                    document.getElementById("totalamt").value = convertNumberToWords(total);
                }
            }
        }
        $(document).ready(function() {

            $('#dataprev').click(function() {

                var form_req = $('#form_req').val();
                var form_data = $('#wfform').serialize();
                var tinyeditor = "";
                var name = $("#editor").attr("name");
                is_tinyMCE_active = false;
                if (typeof(tinyMCE) != "undefined") {
                    if (tinyMCE.activeEditor != null) {
                        tinyeditor = tinymce.get("editor").getContent();
                        if (tinyeditor != '') {
                            form_data = form_data + "&" + name + "=" + escape(tinymce.get("editor").getContent());
                        }
                    }
                }
                //  var content = tinymce.get("editor").getContent();
                var count = 0;

                function fn() {
                    count++;
                    return ' ';
                }
                if (tinyeditor != "") {
                    var x = tinyeditor.trim();
                    // console.log(x);
                    x = x.replace(/[\s]+/ig, fn);
                }
                //x is now filtered out of extra spaces too !
                var words = count + 1;
                count = 0;
                console.log(words);
                // alert(form_data);
                //var form_data=
                //                    if (words < 50)
                //                    {
                $.ajax({
                    url: 'application/ajax/preview.php',
                    type: 'POST',

                    data: form_data,
                    success: function(response) {

                        $('#formpreview').html(response);
                        $('#formpreview').show();
                    }
                });

                //}
                //                 <?php if ($formRequired != '1') { ?>  
                $('#viewpreview').html(tinyeditor);
                //                     
                $('#viewpreview').show();
            <?php } ?>


            });
        });
    </script>
</body>

</html>

<?php
if (isset($_POST['uploaddWfd'], $_POST['token'])) {
    /* turn autocommit off */
    //    mysqli_autoCommit($db_con,false);
    if (empty($_POST['rfi_date']) || empty($_POST['name_of_the_contractor']) || empty($_POST['item_no_as_per']) || empty($_POST['inspection_required_date']) || empty($_POST['location_from']) || empty($_POST['location_to']) || empty($_POST['description_of_work']) || empty($_POST['remark'])) {
        echo '<script>alert("Check All Required Fields")</script>';
        exit;
    }
    if ($_POST['location_from'] < 967.055 || $_POST['location_to'] > 1006.44) {
        echo '<script>alert("Check Location Distance")</script>';
        exit;
    }
    $wfid = $workfid;
    if (intval($wfid)) {

        $files = $_FILES['fileName']['name'];

        if (!empty($files)) {

            echo count($files);

            for ($k = 0; $k < count($files); $k++) {

                $file_name = $_FILES['fileName']['name'][$k];
                if ($file_name) {

                    $allowed = ALLOWED_EXTN;

                    $allowext = implode(", ", $allowed);
                    $ext = pathinfo($file_name, PATHINFO_EXTENSION);
                    if (!in_array(strtolower($ext), $allowed)) {

                        echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '", "' . str_replace("ext", $allowext, $lang['document_allowed']) . '")</script>';
                        exit();
                    }
                }
            }
        }

        mysqli_autocommit($db_con, true);
        $formExistQry = mysqli_query($db_con, "select * from tbl_bridge_workflow_to_form where workflow_id='$wfid'"); //or die("Error:" . mysqli_error($db_con));
        if (mysqli_num_rows($formExistQry) > 0) {
            $docId = '0';
            //$wfid = base64_decode(urldecode($_GET['wid']));
            $wfd = mysqli_query($db_con, "select * from tbl_workflow_master where workflow_id='$wfid'"); //or die('Error:' . mysqli_error($db_con));
            $rwWfd = mysqli_fetch_assoc($wfd);
            $workFlowName = $rwWfd['workflow_name'];
            $pdf_req = $rwWfd['pdf_req'];
            $user_id = $_SESSION['cdes_user_id'];

            $workFlowArray = explode(" ", $workFlowName);
            $ticket = '';
            for ($w = 0; $w < count($workFlowArray); $w++) {
                $name = $workFlowArray[$w];
                $ticket = $ticket . substr($name, 0, 1);
            }

            $ticket = $ticket . '_' . $user_id . '_' . strtotime($date);
            if ($rwWfd['form_req'] == 1 || $rwWfd['form_req'] == 2) {
                $taskRemark = "";
            } else {
                $taskRemark = mysqli_real_escape_string($db_con, $_POST['taskRemark']);
            }
            //if file uploaded then
            if (!empty($_POST['lastMoveId'])) {

                $chkrw = mysqli_query($db_con, "select * from tbl_task_master where workflow_id = '$wfid'"); //or die('Error:' . mysqli_error($db_con));

                if (mysqli_num_rows($chkrw) > 0) {
                    $sl_id = $_POST['lastMoveId'];
                    $id = $sl_id . '_' . $wfid;


                    //$docs_name =  $rwslname['sl_name'];
                    if ($rwWfd['form_req'] == 1 || $rwWfd['form_req'] == 2) {
                        $workFlowTblName = $rwWfd['form_tbl_name'];
                        $chkColExist = mysqli_query($db_con, "SELECT COLUMN_NAME FROM  information_schema.COLUMNS where  TABLE_NAME ='$workFlowTblName'  and COLUMN_NAME = 'co';"); //or die('Error:' . mysqli_error($db_con));

                        if (!empty($_POST['CO']) && mysqli_num_rows($chkColExist) > 0) {
                            $workFlowTblName = mysqli_escape_string($db_con, $workFlowTblName);
                            $dateofco = mysqli_escape_string($db_con, $_POST['CO']);
                            //echo "select tbl_id from '$workFlowTblName' where user_id='$user_id' and co='$dateofco'" ;
                            $qrycochk = mysqli_query($db_con, "select tbl_id from " . $workFlowTblName . " where user_id='$user_id' and co='$dateofco'"); //or die("Error:" . mysqli_error($db_con));
                            if (mysqli_num_rows($qrycochk) > 0) {
                                echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '", "' . $lang['Opps_Sbmsn_fld'] . '")</script>';
                            } else {

                                $formbrige = mysqli_query($db_con, "select form_id from tbl_bridge_workflow_to_form where workflow_id='$wfid'");
                                $formid = mysqli_fetch_assoc($formbrige);
                                $formnameqry = mysqli_query($db_con, "select name from tbl_form_attribute where fid='$formid[form_id]' and dependency_id is null");
                                $coloum .= "user_id,ticket_id";
                                $values .= "'" . $user_id . "'" . "," . "'" . $ticket . "'";


                                while ($rowdata = mysqli_fetch_assoc($formnameqry)) {
                                    $names = $rowdata['name'];

                                    if (!empty($names)) {
                                        $coloum .= "," . $names;
                                        $values .= ",'" . mysqli_real_escape_string($db_con, $_POST[$names]) . "'";
                                    }
                                    //array_push($formvalues, $_POST[$names]);
                                }
                                $sqlForm = "insert into " . $workFlowTblName . "($coloum) values ($values)";

                                $insertqry = mysqli_query($db_con, $sqlForm); // or die('Error:' . mysqli_error($db_con));
                                if ($insertqry) {
                                    $LastValuesId = mysqli_insert_id($db_con);
                                    if (!empty($_POST['CO'])) {

                                        $coDate = $_POST['CO'];

                                        $chkColExist = mysqli_query($db_con, "SELECT COLUMN_NAME FROM  information_schema.COLUMNS where  TABLE_NAME ='$workFlowTblName'  and COLUMN_NAME = 'co';"); //or die('Error:' . mysqli_error($db_con));
                                        if (mysqli_num_rows($chkColExist) > 0) {
                                            $updateco = mysqli_query($db_con, "update " . $workFlowTblName . " Set co='$coDate' where tbl_id='$LastValuesId'"); //or die('Error:' . mysqli_error($db_con));
                                        } else {
                                            $qry = mysqli_query($db_con, "ALTER TABLE " . $workFlowTblName . " ADD co varchar(255)"); //or die('Error:' . mysqli_error($db_con));
                                            if ($qry) {
                                                $updateco = mysqli_query($db_con, "update " . $workFlowTblName . " Set co='$coDate' where tbl_id='$LastValuesId'"); //or die('Error:' . mysqli_error($db_con));
                                            }
                                        }
                                    }

                                    $form_id = $formid['form_id'];

                                    $formbuild .= "<table class='table' border='1' cellspacing='7' width='100%' style='border-collapse: collapse; font-size:12px;'>";
                                    $qry = mysqli_query($db_con, "select * from tbl_form_attribute where fid='$form_id' and dependency_id is null"); //or die(mysqli_error($db_con));

                                    $colname = mysqli_query($db_con, "select * from $workFlowTblName where tbl_id='$LastValuesId'"); //or die("Error:" . mysqli_error($db_con));
                                    $fetch = mysqli_fetch_fields($colname);
                                    //print_r($fetch);
                                    $userdata = mysqli_query($db_con, "select first_name,last_name,designation from tbl_user_master where user_id='$user_id'"); //or die("Error:" . mysqli_errno($db_con));
                                    $userresult = mysqli_fetch_assoc($userdata);
                                    $fetchdata = mysqli_fetch_all($colname, MYSQLI_ASSOC);
                                    $i = 1;
                                    while ($row = mysqli_fetch_assoc($qry)) {

                                        if ($row['type'] == "header") {
                                            $formbuild .= "<tr>";
                                            $formbuild .= "<td colspan='2' align='center'>" . "<b>" . $row['label'] . "</b>" . "</td>";
                                            $formbuild .= "</tr>";
                                        }
                                        if ($i == 1) {
                                            $formbuild .= "<tr>";
                                            $formbuild .= "<td><b>" . Name . "</b></td>";
                                            $formbuild .= "<td>" . $userresult['first_name'] . " " . $userresult['last_name'] . "</td>";
                                            $formbuild .= "</tr>";
                                            $formbuild .= "<tr>";
                                            $formbuild .= "<td><b>" . Designation . "</b></td>";
                                            $formbuild .= "<td>" . $userresult['designation'] . "</td>";
                                            $formbuild .= "</tr>";
                                        }

                                        $formbuild .= "<tr>";
                                        if ($row['type'] != "header") {
                                            $formbuild .= "<td><b>" . $row['label'] . "</b></td>";
                                        }
                                        foreach ($fetch as $val) {

                                            if ($row['name'] == $val->name) {

                                                $name = $val->name;
                                                foreach ($fetchdata as $values) {
                                                    $formbuild .= "<td>" . $values[$name] . (($values[$name] == 'CO(Compensatory off)') ? ' - ' . $values['co'] : '') . "</td>";
                                                }
                                            }
                                        }
                                        $formbuild .= "</tr>";

                                        $i++;
                                    }

                                    $formbuild .= "</table>";
                                }
                            }
                        } elseif (!empty($_POST['cashvocher'])) {
                            $formbrige = mysqli_query($db_con, "select form_id from tbl_bridge_workflow_to_form where workflow_id='$wfid'");
                            $formid = mysqli_fetch_assoc($formbrige);
                            $formnameqry = mysqli_query($db_con, "select name from tbl_form_attribute where fid='$formid[form_id]' and dependency_id is null");
                            $coloum = "user_id,ticket_id";
                            $values = "'" . $user_id . "'" . "," . "'" . $ticket . "'";


                            while ($rowdata = mysqli_fetch_assoc($formnameqry)) {
                                $names = $rowdata['name'];
                                //echo$_POST["$names"];die();

                                if (!empty($names)) {
                                    $coloum .= "," . $names;
                                    $values .= ",'" . mysqli_real_escape_string($db_con, xss_clean($_POST["$names"])) . "'";
                                }
                                //                    array_push($formvalues, $_POST[$names]);
                            }
                            //                $purpose= implode(",", $_POST['cashvocher']);
                            //                $wf_amt= implode(",", $_POST['amt']);
                            //                $namt= implode(",", $_POST['namt']);
                            //                $rupee=$_POST['rupee'];
                            //                $famount=$_POST['amount'];
                            //                $coloum .=",wf_purpose,wf_amt,wf_netamt,wf_rupee,wf_amount";
                            //                $values.=",'".$purpose."','".$wf_amt."','".$namt."','".$rupee."','".$famount."'";
                            //echo $values;
                            // mysqli_autocommit($db_con, FALSE);

                            $sqlForm = "insert into " . $workFlowTblName . "($coloum) values ($values)";
                            // echo $sqlForm;

                            $insertqry = mysqli_query($db_con, $sqlForm); // or die('Error:' . mysqli_error($db_con));
                            if ($insertqry) {
                                $LastValuesId = mysqli_insert_id($db_con);
                                if (!empty($_POST['CO'])) {
                                    $coDate = $_POST['CO'];

                                    $chkColExist = mysqli_query($db_con, "SELECT COLUMN_NAME FROM  information_schema.COLUMNS where  TABLE_NAME ='$workFlowTblName'  and COLUMN_NAME = 'co';"); //or die('Error:' . mysqli_error($db_con));
                                    if (mysqli_num_rows($chkColExist) > 0) {
                                        $updateco = mysqli_query($db_con, "update `" . $workFlowTblName . "` Set co='$coDate' where tbl_id='$LastValuesId'"); //or die('Error:' . mysqli_error($db_con));
                                    } else {
                                        $qry = mysqli_query($db_con, "ALTER TABLE `" . $workFlowTblName . "` ADD co varchar(255)"); //or die('Error:' . mysqli_error($db_con));
                                        if ($qry) {
                                            $updateco = mysqli_query($db_con, "update `" . $workFlowTblName . "` Set co='$coDate' where tbl_id='$LastValuesId'"); //or die('Error:' . mysqli_error($db_con));
                                        }
                                    }
                                }
                                if (!empty($_POST['cashvocher']) || !empty($_POST['amt']) || !empty($_POST['namt']) || !empty($_POST['rupee']) || !empty($_POST['amount'])) {
                                    $purpose = implode(",", preg_replace("/[^a-zA-Z0-9_@]/", "", $_POST['cashvocher']));
                                    $wf_amt = implode(",", $_POST['amt']);
                                    $namt = implode(",", $_POST['namt']);
                                    $rupee = $_POST['rupee'];
                                    $famount = $_POST['amount'];

                                    //                        $chkColExist= mysqli_query($db_con,"SELECT COLUMN_NAME FROM  information_schema.COLUMNS where  TABLE_NAME ='$workFlowTblName'  and COLUMN_NAME = 'wf_purpose' and COLUMN_NAME = 'wf_amt' and COLUMN_NAME = 'wf_netamt' and COLUMN_NAME = 'wf_rupee' and COLUMN_NAME = 'wf_amount';")or die('Error26:' . mysqli_error($db_con));
                                    //                        if(mysqli_num_rows($chkColExist)>0)
                                    //                        {
                                    //                             $updateco= mysqli_query($db_con, "update `".$workFlowTblName."` Set wf_purpose='$purpose',wf_amt='$wf_amt',wf_netamt='$namt',wf_rupee='$rupee',wf_amount='$famount' where tbl_id='$LastValuesId'")or die('Error27:' . mysqli_error($db_con));
                                    //                        }
                                    //                      else {
                                    //$static_col=array();
                                    $qry = mysqli_query($db_con, "ALTER TABLE `" . $workFlowTblName . "` ADD wf_purpose Text,wf_amt Text,wf_netamt Text,wf_rupee int(11),wf_amount varchar(50)");
                                    if ($qry) {
                                        $updateco = mysqli_query($db_con, "update `" . $workFlowTblName . "` Set wf_purpose='$purpose',wf_amt='$wf_amt',wf_netamt='$namt',wf_rupee='$rupee',wf_amount='$famount' where tbl_id='$LastValuesId'"); //or die('Error:30' . mysqli_error($db_con));
                                    } else {
                                        $updateco = mysqli_query($db_con, "update `" . $workFlowTblName . "` Set wf_purpose='$purpose',wf_amt='$wf_amt',wf_netamt='$namt',wf_rupee='$rupee',wf_amount='$famount' where tbl_id='$LastValuesId'"); //or die('Error27:' . mysqli_error($db_con));
                                        //                     
                                    }
                                    //                        }
                                }

                                $form_id = $formid['form_id'];

                                $formbuild = "<table class='table' border='1' cellspacing='7' width='100%' style='border-collapse: collapse; font-size:12px;'>";
                                $qry = mysqli_query($db_con, "select * from tbl_form_attribute where fid='$form_id' and dependency_id is null"); //or die(mysqli_error($db_con));
                                $rowdata = mysqli_fetch_assoc($qry);
                                $colname = mysqli_query($db_con, "select * from $workFlowTblName where tbl_id='$LastValuesId'"); // or die("Error:" . mysqli_error($db_con));
                                $fetch = mysqli_fetch_assoc($colname);

                                $userdata = mysqli_query($db_con, "select first_name,last_name,designation from tbl_user_master where user_id='$user_id'"); //or die("Error:" . mysqli_errno($db_con));
                                $userresult = mysqli_fetch_assoc($userdata);
                                $fetchdata = mysqli_fetch_all($colname, MYSQLI_ASSOC);

                                //echo'table'. $fetchdata['wf_materialrequired'];
                                //echo $fetchdata['ticket_id'];
                                $i = 1;
                                //                    while ($row = mysqli_fetch_assoc($qry)) {
                                //                        echo $row['name'];

                                if ($rowdate['type'] == "header") {
                                    $formbuild .= "<tr>";
                                    $formbuild .= "<td colspan='4' align='center'>" . "<b>" . "<img src='assets/images/crc.png'  height='100px'>" . "</b>" . "</td>";
                                    $formbuild .= "</tr>";
                                    $formbuild .= "<tr>";
                                    $formbuild .= "<td colspan='4' align='center'>" . "<b>" . $rowdate['label'] . "</b>" . "</td>";
                                    $formbuild .= "</tr>";
                                    $formbuild .= "<tr>";
                                    $formbuild .= "<td colspan='4' align='center'>" . "<b>" . "CASH VOUCHER" . "</b>" . "</td>";
                                    $formbuild .= "</tr>";
                                }
                                if ($i == 1) {
                                    $formbuild .= "<tr>";
                                    $formbuild .= "<td><b>" . Name . "</b></td>";
                                    $formbuild .= "<td>" . $userresult['first_name'] . " " . $userresult['last_name'] . "</td>";
                                    $formbuild .= "</tr>";
                                    $formbuild .= "<tr>";
                                    $formbuild .= "<td><b>" . Designation . "</b></td>";
                                    $formbuild .= "<td>" . $userresult['designation'] . "</td>";
                                    $formbuild .= "</tr>";
                                }

                                $formbuild .= "<tr>";
                                //                            $formbuild .= "<td><b>" . $row['label'] . "</b></td>";
                                //                            foreach ($fetch as $val) {
                                //                                
                                //                                if ($row['name'] == $val->name) {
                                //                                    
                                //                                    echo $name = $val->name;
                                //                                    foreach ($fetchdata as $values) {
                                //                                        $formbuild .= "<td>" . $values[$name].(($values[$name]=='CO(Compensatory off)')?' - '.$values['co']:'') . "</td>";
                                //                                      
                                //                                        
                                //                                    }
                                //                                }
                                //                           
                                //                                
                                //                            }
                                $ccenter = mysqli_escape_string($db_con, $fetch['wf_ccenter']);
                                $whouse = mysqli_escape_string($db_con, $fetch['wf_whouse']);
                                $qry2 = mysqli_query($db_con, "select * from `tbl_cost_center` where cc_id='$ccenter'"); //or die(mysqli_error($db_con));
                                $result = mysqli_fetch_assoc($qry2);
                                $qry1 = mysqli_query($db_con, "select * from `tbl_whouse_master` where wh_id='$whouse'"); //or die(mysqli_error($db_con));
                                $result1 = mysqli_fetch_assoc($qry1);
                                $formbuild .= "<td><b>Cost Center</b></td><td>" . $result['cc_name'] . "</td>";
                                $formbuild .= "<td><b>Warehouse</b></td><td>" . $result1['wh_name'] . "</td>";
                                $formbuild .= "</tr>";
                                $formbuild .= "<tr>";
                                $formbuild .= "<td><b>No.</b></td><td>" . $fetch['wf_no'] . "</td>";
                                $formbuild .= "<td><b>Debit Account</b></td><td>" . $fetch['wf_debitac'] . "</td>";
                                $formbuild .= "</tr>";
                                $formbuild .= "<tr>";
                                $formbuild .= "<td><b>Date</b></td><td>" . $fetch['wf_dateselect'] . "</td>";

                                $formbuild .= "</tr>";
                                $formbuild .= "<tr>";
                                $formbuild .= "<td colspan='2'><b>Purpose</b></td>";
                                $formbuild .= "<td><b>Rupees:</b></td>";
                                $formbuild .= "<td><b>Paisa:</b></td>";

                                $formbuild .= "</tr>";


                                $pupose = explode(",", $fetch['wf_purpose']);
                                $amount = explode(",", $fetch['wf_amt']);
                                $netamont = explode(",", $fetch['wf_netamt']);
                                for ($i = 0; $i < count($pupose); $i++) {
                                    $formbuild .= "<tr>";
                                    $formbuild .= "<td colspan='2'>" . $pupose[$i] . "</td>";
                                    $formbuild .= "<td>" . $amount[$i] . "</td>";
                                    $formbuild .= "<td>" . $netamont[$i] . "</td>";
                                    $formbuild .= "</tr>";
                                }
                                //                              for($i=0;$i<count($amount);$i++)
                                //                             {
                                //                                    $formbuild.="<tr>";
                                //                          $formbuild.="<td>".$amount[$i]."</td>";
                                //                            $formbuild.="</tr>";
                                //                             }
                                //                              for($i=0;$i<count($netamont);$i++)
                                //                             {
                                //                                    $formbuild.="<tr>";
                                //                          $formbuild.="<td>".$netamont[$i]."</td>";
                                //                           $formbuild.="</tr>";
                                //                             }
                                //                        $i++;
                                //                    }
                                //                            $formbuild .= "<tr>";
                                //                            $formbuild .= "<td><b></b></td><td><b></b></td>";
                                //                            $formbuild .= "</tr>";
                                $formbuild .= "<tr>";
                                $formbuild .= "<td colspan='4' align='center'><b>Received from Capital Record Centre Pvt. Ltd. </b></td>";
                                $formbuild .= "</tr>";
                                $formbuild .= "<tr>";
                                $formbuild .= "<td><b>Total Rupees:</b></td>";
                                $formbuild .= "<td>" . $fetch['wf_rupee'] . "</td>";
                                $formbuild .= "</tr>";
                                $formbuild .= "<tr>";
                                $formbuild .= "<td><b>Total Amount:</b></td>";
                                $formbuild .= "<td>" . $fetch['wf_amount'] . "</td>";
                                $formbuild .= "<td><b>Receiver Signature</b></td><td><b>Manager</b></td>";
                                $formbuild .= "</tr>";
                                $formbuild .= "</table>";
                            }
                        } else {
                            $formbrige = mysqli_query($db_con, "select form_id from tbl_bridge_workflow_to_form where workflow_id='$wfid'"); //or die('Error:' . mysqli_error($db_con));
                            $formid = mysqli_fetch_assoc($formbrige);
                            $userdata = mysqli_query($db_con, "select first_name,last_name,designation from tbl_user_master where user_id='$user_id'"); //or die("Error:" . mysqli_errno($db_con));
                            $userresult = mysqli_fetch_assoc($userdata);
                            $formnameqry = mysqli_query($db_con, "select name from tbl_form_attribute where fid='$formid[form_id]' and dependency_id is null"); //or die('Error:' . mysqli_error($db_con));
                            $coloum .= "user_id,ticket_id";
                            $values .= "'" . $user_id . "'" . "," . "'" . $ticket . "'";


                            while ($rowdata = mysqli_fetch_assoc($formnameqry)) {
                                $names = $rowdata['name'];
                                if (!empty($names)) {
                                    $coloum .= "," . $names;
                                    $values .= ",'" . mysqli_real_escape_string($db_con, $_POST[$names]) . "'";
                                }
                                //                    array_push($formvalues, $_POST[$names]);
                            }
                            $sqlForm = "insert into " . $workFlowTblName . "($coloum) values ($values)";

                            $insertqry = mysqli_query($db_con, $sqlForm); //or die('Error:' . mysqli_error($db_con));
                            if ($insertqry) {
                                $LastValuesId = mysqli_insert_id($db_con);
                                if (!empty($_POST['CO'])) {
                                    $coDate = $_POST['CO'];

                                    $chkColExist = mysqli_query($db_con, "SELECT COLUMN_NAME FROM  information_schema.COLUMNS where  TABLE_NAME ='$workFlowTblName'  and COLUMN_NAME = 'co';"); //or die('Error:' . mysqli_error($db_con));
                                    if (mysqli_num_rows($chkColExist) > 0) {
                                        $updateco = mysqli_query($db_con, "update " . $workFlowTblName . " Set co='$coDate' where tbl_id='$LastValuesId'"); //or die('Error:' . mysqli_error($db_con));
                                    } else {
                                        $qry = mysqli_query($db_con, "ALTER TABLE " . $workFlowTblName . " ADD co varchar(255)"); //or die('Error:' . mysqli_error($db_con));
                                        if ($qry) {
                                            $updateco = mysqli_query($db_con, "update " . $workFlowTblName . " Set co='$coDate' where tbl_id='$LastValuesId'"); //or die('Error:' . mysqli_error($db_con));
                                        }
                                    }
                                }

                                $form_id = $formid['form_id'];

                                $formbuild .= "<table border='1' class='table' border='1' cellspacing='7' width='100%' style='border-collapse: collapse; font-size:12px;'>";
                                $qry = mysqli_query($db_con, "select * from tbl_form_attribute where fid='$form_id' and dependency_id is null"); //or die(mysqli_error($db_con));

                                $colname = mysqli_query($db_con, "select * from $workFlowTblName where tbl_id='$LastValuesId'"); // or die("Error:" . mysqli_error($db_con));
                                $fetch = mysqli_fetch_fields($colname);

                                $fetchdata = mysqli_fetch_all($colname, MYSQLI_ASSOC);
                                $i = 1;
                                while ($row = mysqli_fetch_assoc($qry)) {

                                    if ($row['type'] == "header") {
                                        $formbuild .= "<tr>";
                                        $formbuild .= "<td colspan='2' align='center'>" . "<b>" . $row['label'] . "</b>" . "</td>";
                                        $formbuild .= "</tr>";
                                    }
                                    if ($i == 1) {
                                        $formbuild .= "<tr>";
                                        $formbuild .= "<td><b>" . Name . "</b></td>";
                                        $formbuild .= "<td>" . $userresult['first_name'] . " " . $userresult['last_name'] . "</td>";
                                        $formbuild .= "</tr>";
                                        $formbuild .= "<tr>";
                                        $formbuild .= "<td><b>" . Designation . "</b></td>";
                                        $formbuild .= "<td>" . $userresult['designation'] . "</td>";
                                        $formbuild .= "</tr>";
                                    }

                                    $formbuild .= "<tr>";
                                    if ($row['type'] != "header") {
                                        $formbuild .= "<td><b>" . $row['label'] . "</b></td>";
                                    }
                                    foreach ($fetch as $val) {

                                        if ($row['name'] == $val->name) {
                                            $name = $val->name;
                                            foreach ($fetchdata as $values) {
                                                $formbuild .= "<td>" . $values[$name] . (($values[$name] == 'CO(Compensatory off)') ? ' - ' . $values['co'] : '') . "</td>";
                                            }
                                        }
                                    }
                                    $formbuild .= "</tr>";

                                    $i++;
                                }

                                $formbuild .= "</table>";
                            }
                        }

                        if ($pdf_req == 1 || !empty($_FILES['fileName']['name'])) {

                            $docName = mysqli_query($db_con, "select * from tbl_storage_level where sl_id = '$sl_id'") or die('Eror:' . mysqli_error($db_con));
                            $rwdocName = mysqli_fetch_assoc($docName);
                            $updir = getStoragePath($db_con, $rwdocName['sl_parent_id'], $rwdocName['sl_depth_level']);
                            if (!empty($updir)) {
                                $updir = $updir . '/';
                            } else {
                                $updir = '';
                            }

                            $folderName = $updir . str_replace(" ", "", $rwdocName['sl_name']);
                        }

                        if ($pdf_req == 1) {
                            include 'exportpdf.php';

                            //if ((isset($_POST['taskRemark'])) && (!empty(trim($_POST['taskRemark'])))) { //if content of CKEditor ISN'T empty
                            $posted_editor = $formbuild; //get content of CKEditor

                            $pdfName = trim($workFlowName) . "_" . mktime() . ".pdf"; //specify the file save location and the file name
                            $path = 'extract-here/' . $folderName;
                            if (!is_dir($path)) {
                                mkdir($path, 0777, true);
                            }
                            $path = $path . '/' . $pdfName;
                            exportPDF($posted_editor, $path);
                            $wrkflowFsize = filesize($path);

                            $wrkflowFsize = round(($wrkflowFsize / 1024), 2);
                            $doc_name = $sl_id . '_' . $wfid;
                            $pagecount = count_pages($path);
                            $wrkflowDoc = mysqli_query($db_con, "INSERT INTO tbl_document_master (doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages, dateposted, workflow_id) VALUES ('$doc_name', '$pdfName', 'pdf', '$folderName/$pdfName', '$user_id', '$wrkflowFsize', '$pagecount', '$date', '$wfid')") or die('Eror:' . mysqli_error($db_con));
                            $docId = mysqli_insert_id($db_con);
                            $newdocname = base64_encode($docId);

                            //create thumbnail
                            $uploadedfilename = $path;

                            //changePdfToImage($uploadedfilename,$newdocname);

                            $id = $sl_id . '_' . $docId . '_' . $wfid;

                            $destinationPath = $folderName . '/' . $pdfName;
                            $sourcePath = $path;
                            uploadFileInFtpServer($destinationPath, $sourcePath);
                            //}
                        } else {
                            $taskRemark = $formbuild;
                        }
                    }

                    $files = $_FILES['fileName']['name'];
                    //if (strlen($files) < 50) {
                    if (!empty($files)) {

                        for ($i = 0; $i < count($files); $i++) {
                            $file_name = $_FILES['fileName']['name'][$i];
                            $file_size = $_FILES['fileName']['size'][$i];
                            $file_type = $_FILES['fileName']['type'][$i];
                            $file_tmp = $_FILES['fileName']['tmp_name'][$i];
                            if (!empty($file_name)) {
                                $pageCount = $_POST['pageCount'];
                                // echo"<script>alert('two$pageCount')</script>";
                                $fname = substr($file_name, 0, strrpos($file_name, '.'));
                                $encryptName = urlencode(base64_encode($fname));
                                $fileExtn = substr($file_name, strrpos($file_name, '.') + 1);
                                $folder = $folderName;
                                $image_path = 'extract-here/' . $folder . '/';

                                if (!dir($image_path)) {
                                    mkdir($image_path, 0777, TRUE); // or die("Error local folder:". print_r(error_get_last()));
                                }
                                $file_name = time() . '_' . $file_name;
                                $image_path = $image_path . $file_name;

                                $upload = move_uploaded_file($file_tmp, $image_path); // or die(print_r(error_get_last()));

                                if ($upload) {

                                    $destinationPath = $folder . '/' . $file_name;

                                    $sourcePath = $image_path;
                                    uploadFileInFtpServer($destinationPath, $sourcePath);

                                    $query = "INSERT INTO tbl_document_master(doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages, dateposted, workflow_id) VALUES ('$id', '$fname', '$fileExtn', '$folder/$file_name', '$user_id', '$file_size', '$pageCount', '$date', '$wfid')";
                                    $exe = mysqli_query($db_con, $query);  //or die('Error query failed' . mysqli_error($db_con));

                                    $docId2 = mysqli_insert_id($db_con);

                                    // Decrypt file
                                    decrypt_my_file($image_path);

                                    $newdocname = base64_encode($docId2);

                                    //create thumbnail
                                    $uploadedfilename = $image_path;

                                    if ($fileExtn == 'jpg' || $fileExtn == 'jpeg' || $fileExtn == 'png') {
                                        //createThumbnail2($uploadedfilename, $newdocname);
                                    } elseif ($fileExtn == 'pdf') {
                                        //changePdfToImage($uploadedfilename,$newdocname);
                                    }

                                    if (empty($docId)) {

                                        $docId = $docId2;
                                        $id = $sl_id . '_' . $docId2 . '_' . $wfid;
                                    }
                                }
                            }
                        }
                    }
                    //} else{
                    //    echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '", "' . $lang['file_name _too_long'] . '")</script>';
                    //}
                    $getStep = mysqli_query($db_con, "select * from tbl_step_master where workflow_id = '$wfid' ORDER BY step_order ASC LIMIT 1"); // or die('Error:' . mysqli_error($db_con));
                    $getStpId = mysqli_fetch_assoc($getStep);
                    $stpId = $getStpId['step_id'];

                    $getTask = mysqli_query($db_con, "select * from tbl_task_master where step_id = '$stpId' ORDER BY task_order ASC LIMIT 1"); // or die('Error:' . mysqli_error($db_con));
                    $getTaskId = mysqli_fetch_assoc($getTask);
                    // echo 'ok';
                    $tskId = $getTaskId['task_id'];

                    $getTaskDl = mysqli_query($db_con, "select * from tbl_task_master where task_id='$tskId'"); // or die('Error:' . mysqli_error($db_con));
                    $rwgetTaskDl = mysqli_fetch_assoc($getTaskDl);


                    if ($rwgetTaskDl['deadline_type'] == 'Date' || $rwgetTaskDl['deadline_type'] == 'Hrs') {

                        $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 60));
                    }
                    if ($rwgetTaskDl['deadline_type'] == 'Days') {

                        $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 24 * 60 * 60));
                    }
                    $taskRemark = mysqli_real_escape_string($db_con, $taskRemark);
                    $insertInTask = mysqli_query($db_con, "INSERT INTO tbl_doc_assigned_wf(task_id, doc_id, start_date, end_date, task_status, assign_by, task_remarks,ticket_id) VALUES ('$tskId', '$docId', '$date', '$endDate', 'Pending', '$user_id','$taskRemark','$ticket')") or die('Error:' . mysqli_error($db_con));
                    $idins = mysqli_insert_id($db_con);

                    $getTask = mysqli_query($db_con, "select * from tbl_task_master where task_id = '$tskId'"); // or die('Error:' . mysqli_error($db_con));
                    $rwgetTask = mysqli_fetch_assoc($getTask);
                    $TskStpId = $rwgetTask['step_id'];
                    $TskWfId = $rwgetTask['workflow_id'];
                    $TskOrd = $rwgetTask['task_order'];
                    $TskAsinToId = $rwgetTask['assign_user'];
                    $nextTaskOrd = $TskOrd + 1;

                    nextTaskAsin($nextTaskOrd, $TskWfId, $TskStpId, $docId, $date, $user_id, $db_con, $taskRemark, $ticket);
                    if ($insertInTask) {
                        //echo '<script> alert("ok")</script>';
                        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `doc_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,null,'Task Created','$date',null,'$host',null)"); // or die('error : ' . mysqli_error($db_con));
                        require_once './mail.php';

                        if (MAIL_BY_SOCKET) {

                            $paramsArray = array(
                                'ticket' => $ticket,
                                'idins' => $idins,
                                'db_con' => $db_con,
                                'projectName' => $projectName,
                                'action' => 'assignTask'
                            );
                            mailBySocket($paramsArray);
                        } else {

                            $mail = assignTask($ticket, $idins, $db_con, $projectName);
                        }


                        //if ($mail) {
                        //send sms to mob who submit
                        //                                $getMobNum = mysqli_query($db_con, "select * from tbl_user_master where user_id = '$user_id'") or die('Error:' . mysqli_error($db_con));
                        //                                $rwgetMobNum = mysqli_fetch_assoc($getMobNum);
                        //                                $submtByMob = $rwgetMobNum['phone_no'];
                        //                                $msg = 'Your Ticket Id is: ' . $ticket . ' and Your Task is in Process.';
                        //                                $sendMsgToMbl = smsgatewaycenter_com_Send($submtByMob, $msg, $debug = false);
                        //

                        echo '<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '", "' . $lang['Sumitd_Sucsfly'] . '");</script>';
                        // } else {
                        //     echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '", "' . $lang['Ops_Ml_nt_snt'] . '")</script>';
                        // }
                    } else {
                        echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '", "' . $lang['Opps_Sbmsn_fld'] . '")</script>';
                    }
                } else {
                    echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '", "' . $lang['Tre_is_no_tsk_in_ts_wfw'] . '")</script>';
                }
            } else if (empty($_POST['lastMoveId'])) {

                $slperm = mysqli_query($db_con, "select * from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]'"); //or die('Error:' . mysqli_error($db_con));
                $rwSlperm = mysqli_fetch_assoc($slperm);
                $sl_id = $rwSlperm['sl_id'];
                $chkrw = mysqli_query($db_con, "select * from tbl_task_master where workflow_id = '$wfid'"); //or die('Error:' . mysqli_error($db_con));
                $id = $sl_id . '_' . $wfid;
                if (mysqli_num_rows($chkrw) > 0) {
                    $getStep = mysqli_query($db_con, "select * from tbl_step_master where workflow_id = '$wfid' ORDER BY step_order ASC LIMIT 1"); //or die('Error:' . mysqli_error($db_con));
                    $getStpId = mysqli_fetch_assoc($getStep);
                    $stpId = $getStpId['step_id'];

                    $getTask = mysqli_query($db_con, "select * from tbl_task_master where step_id = '$stpId' ORDER BY task_order ASC LIMIT 1"); //or die('Error:' . mysqli_error($db_con));
                    $getTaskId = mysqli_fetch_assoc($getTask);
                    $tskId = $getTaskId['task_id'];

                    $getTaskDl = mysqli_query($db_con, "select * from tbl_task_master where task_id='$tskId'"); //or die('Error:' . mysqli_error($db_con));
                    $rwgetTaskDl = mysqli_fetch_assoc($getTaskDl);


                    if ($rwgetTaskDl['deadline_type'] == 'Date' || $rwgetTaskDl['deadline_type'] == 'Hrs') {

                        $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 60));
                    }
                    if ($rwgetTaskDl['deadline_type'] == 'Days') {

                        $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 24 * 60 * 60));
                    }
                    //create pdf from form
                    if ($rwWfd['form_req'] == 1 || $rwWfd['form_req'] == 2) {

                        $workFlowTblName = $rwWfd['form_tbl_name'];
                        $chkColExist = mysqli_query($db_con, "SELECT COLUMN_NAME FROM  information_schema.COLUMNS where  TABLE_NAME ='$workFlowTblName'  and COLUMN_NAME = 'co';"); //or die('Error:' . mysqli_error($db_con));

                        if (!empty($_POST['CO']) && mysqli_num_rows($chkColExist) > 0) {


                            $workFlowTblName = mysqli_escape_string($db_con, $workFlowTblName);
                            $dateofco = mysqli_escape_string($db_con, $_POST['CO']);

                            $qrycochk = mysqli_query($db_con, "select tbl_id from " . $workFlowTblName . " where user_id='$user_id' and co='$dateofco'"); // or die("Error:" . mysqli_error($db_con));
                            if (mysqli_num_rows($qrycochk) > 0) {
                                echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '", "' . $lang['opp'] . '")</script>';
                            } else {
                                $formbrige = mysqli_query($db_con, "select form_id from tbl_bridge_workflow_to_form where workflow_id='$wfid'");
                                $formid = mysqli_fetch_assoc($formbrige);
                                $formnameqry = mysqli_query($db_con, "select name from tbl_form_attribute where fid='$formid[form_id]' and dependency_id is null");
                                $coloum .= "user_id,ticket_id";
                                $values .= "'" . $user_id . "'" . "," . "'" . $ticket . "'";


                                while ($rowdata = mysqli_fetch_assoc($formnameqry)) {
                                    $names = $rowdata['name'];

                                    if (!empty($names)) {
                                        $coloum .= "," . $names;
                                        $values .= ",'" . mysqli_real_escape_string($db_con, $_POST[$names]) . "'";
                                    }
                                    //array_push($formvalues, $_POST[$names]);
                                }
                                $sqlForm = "insert into " . $workFlowTblName . "($coloum) values ($values)";

                                $insertqry = mysqli_query($db_con, $sqlForm); // or die('Error:' . mysqli_error($db_con));
                                if ($insertqry) {
                                    $LastValuesId = mysqli_insert_id($db_con);
                                    if (!empty($_POST['CO'])) {
                                        $coDate = $_POST['CO'];

                                        $chkColExist = mysqli_query($db_con, "SELECT COLUMN_NAME FROM  information_schema.COLUMNS where  TABLE_NAME ='$workFlowTblName'  and COLUMN_NAME = 'co';"); //or die('Error:' . mysqli_error($db_con));
                                        if (mysqli_num_rows($chkColExist) > 0) {
                                            $updateco = mysqli_query($db_con, "update `" . $workFlowTblName . "` Set co='$coDate' where tbl_id='$LastValuesId'"); //or die('Error:' . mysqli_error($db_con));
                                        } else {
                                            $qry = mysqli_query($db_con, "ALTER TABLE `" . $workFlowTblName . "` ADD co varchar(255)"); //or die('Error:' . mysqli_error($db_con));
                                            if ($qry) {
                                                $updateco = mysqli_query($db_con, "update `" . $workFlowTblName . "` Set co='$coDate' where tbl_id='$LastValuesId'"); //or die('Error:' . mysqli_error($db_con));
                                            }
                                        }
                                    }

                                    $form_id = $formid['form_id'];

                                    $formbuild = "<table class='table' border='1' cellspacing='7' width='100%' style='border-collapse: collapse; font-size:12px;'>";
                                    $qry = mysqli_query($db_con, "select * from tbl_form_attribute where fid='$form_id' and dependency_id is null"); //or die(mysqli_error($db_con));

                                    $colname = mysqli_query($db_con, "select * from `$workFlowTblName` where tbl_id='$LastValuesId'"); // or die("Error:" . mysqli_error($db_con));
                                    $fetch = mysqli_fetch_fields($colname);
                                    //print_r($fetch);
                                    $userdata = mysqli_query($db_con, "select first_name,last_name,designation from tbl_user_master where user_id='$user_id'"); //or die("Error:" . mysqli_errno($db_con));
                                    $userresult = mysqli_fetch_assoc($userdata);
                                    $fetchdata = mysqli_fetch_all($colname, MYSQLI_ASSOC);
                                    // print_r($fetchdata);
                                    $i = 1;
                                    while ($row = mysqli_fetch_assoc($qry)) {


                                        if ($row['type'] == "header") {
                                            $formbuild .= "<tr>";
                                            $formbuild .= "<td colspan='2' align='center'>" . "<b>" . $row['label'] . "</b>" . "</td>";
                                            $formbuild .= "</tr>";
                                        }
                                        if ($i == 1) {
                                            $formbuild .= "<tr>";
                                            $formbuild .= "<td><b>" . Name . "</b></td>";
                                            $formbuild .= "<td>" . $userresult['first_name'] . " " . $userresult['last_name'] . "</td>";
                                            $formbuild .= "</tr>";
                                            $formbuild .= "<tr>";
                                            $formbuild .= "<td><b>" . Designation . "</b></td>";
                                            $formbuild .= "<td>" . $userresult['designation'] . "</td>";
                                            $formbuild .= "</tr>";
                                        }

                                        $formbuild .= "<tr>";
                                        if ($row['type'] != "header") {
                                            $formbuild .= "<td><b>" . $row['label'] . "</b></td>";
                                        }
                                        foreach ($fetch as $val) {

                                            if ($row['name'] == $val->name) {

                                                $name = $val->name;
                                                foreach ($fetchdata as $values) {
                                                    $formbuild .= "<td>" . $values[$name] . (($values[$name] == 'CO(Compensatory off)') ? ' - ' . $values['co'] : '') . "</td>";
                                                }
                                            }
                                        }
                                        $formbuild .= "</tr>";


                                        $i++;
                                    }

                                    $formbuild .= "</table>";
                                }
                            }
                        } elseif (!empty($_POST['cashvocher'])) {

                            $formbrige = mysqli_query($db_con, "select form_id from tbl_bridge_workflow_to_form where workflow_id='$wfid'");
                            $formid = mysqli_fetch_assoc($formbrige);
                            $formnameqry = mysqli_query($db_con, "select name from tbl_form_attribute where fid='$formid[form_id]' and dependency_id is null");
                            $coloum = "user_id,ticket_id";
                            $values = "'" . $user_id . "'" . "," . "'" . $ticket . "'";


                            while ($rowdata = mysqli_fetch_assoc($formnameqry)) {
                                $names = $rowdata['name'];
                                //echo$_POST["$names"];die();

                                if (!empty($names)) {
                                    $coloum .= "," . $names;
                                    $values .= ",'" . mysqli_real_escape_string($db_con, xss_clean($_POST["$names"])) . "'";
                                }
                                //                    array_push($formvalues, $_POST[$names]);
                            }
                            //                $purpose= implode(",", $_POST['cashvocher']);
                            //                $wf_amt= implode(",", $_POST['amt']);
                            //                $namt= implode(",", $_POST['namt']);
                            //                $rupee=$_POST['rupee'];
                            //                $famount=$_POST['amount'];
                            //                $coloum .=",wf_purpose,wf_amt,wf_netamt,wf_rupee,wf_amount";
                            //                $values.=",'".$purpose."','".$wf_amt."','".$namt."','".$rupee."','".$famount."'";
                            //echo $values;
                            // mysqli_autocommit($db_con, FALSE);
                            $sqlForm = "insert into " . $workFlowTblName . "($coloum) values ($values)";
                            // echo $sqlForm;

                            $insertqry = mysqli_query($db_con, $sqlForm); //or die('Error:' . mysqli_error($db_con));
                            if ($insertqry) {
                                $LastValuesId = mysqli_insert_id($db_con);

                                if (!empty($_POST['CO'])) {
                                    $coDate = $_POST['CO'];

                                    $chkColExist = mysqli_query($db_con, "SELECT COLUMN_NAME FROM  information_schema.COLUMNS where  TABLE_NAME ='$workFlowTblName'  and COLUMN_NAME = 'co';"); //or die('Error22:' . mysqli_error($db_con));
                                    if (mysqli_num_rows($chkColExist) > 0) {
                                        $updateco = mysqli_query($db_con, "update `" . $workFlowTblName . "` Set co='$coDate' where tbl_id='$LastValuesId'"); //or die('Error:' . mysqli_error($db_con));
                                    } else {
                                        $qry = mysqli_query($db_con, "ALTER TABLE `" . $workFlowTblName . "` ADD co varchar(255)"); //or die('Error:' . mysqli_error($db_con));
                                        if ($qry) {
                                            $updateco = mysqli_query($db_con, "update `" . $workFlowTblName . "` Set co='$coDate' where tbl_id='$LastValuesId'"); //or die('Error:' . mysqli_error($db_con));
                                        }
                                    }
                                }
                                if (!empty($_POST['cashvocher']) || !empty($_POST['amt']) || !empty($_POST['namt']) || !empty($_POST['rupee']) || !empty($_POST['amount'])) {
                                    $purpose = implode(",", preg_replace("/[^a-zA-Z0-9_@]/", " ", $_POST['cashvocher']));
                                    $wf_amt = implode(",", $_POST['amt']);
                                    $namt = implode(",", $_POST['namt']);
                                    $rupee = $_POST['rupee'];
                                    $famount = $_POST['amount'];

                                    //                        $chkvolExist= mysqli_query($db_con,"SELECT COLUMN_NAME FROM  information_schema.COLUMNS where  TABLE_NAME ='$workFlowTblName'  and COLUMN_NAME = 'wf_purpose' and  COLUMN_NAME = 'wf_amt' and COLUMN_NAME = 'wf_netamt' and COLUMN_NAME = 'wf_rupee' and COLUMN_NAME ='wf_amount'")or die('Error:22' . mysqli_error($db_con));
                                    //                       // echo "SELECT COLUMN_NAME FROM  information_schema.COLUMNS where  TABLE_NAME ='$workFlowTblName'  and COLUMN_NAME = 'wf_purpose' and  COLUMN_NAME = 'wf_amt' and COLUMN_NAME = 'wf_netamt' and COLUMN_NAME = 'wf_rupee' and COLUMN_NAME = 'wf_amount'";
                                    //                        if(mysqli_num_rows($chkvolExist)>0)
                                    //                        {
                                    //                             echo '<script>alert("run");</script>';
                                    //                             $updateco= mysqli_query($db_con, "update `".$workFlowTblName."` Set wf_purpose='$purpose',wf_amt='$wf_amt',wf_netamt='$namt',wf_rupee='$rupee',wf_amount='$famount' where tbl_id='$LastValuesId'")or die('Error31:' . mysqli_error($db_con));
                                    //                             
                                    //                        }
                                    //                      else {
                                    $qry = mysqli_query($db_con, "ALTER TABLE `" . $workFlowTblName . "` ADD COLUMN  `wf_purpose` Text,ADD COLUMN `wf_amt` Text,ADD COLUMN `wf_netamt` Text,ADD COLUMN `wf_rupee` int(11),ADD COLUMN `wf_amount` varchar(50)");
                                    if ($qry) {
                                        //                            echo '<script>alert("run");</script>';
                                        $updateco = mysqli_query($db_con, "update `" . $workFlowTblName . "` Set wf_purpose='$purpose',wf_amt='$wf_amt',wf_netamt='$namt',wf_rupee='$rupee',wf_amount='$famount' where tbl_id='$LastValuesId'"); //or die('Error32:' . mysqli_error($db_con));
                                    } else {
                                        $updateco = mysqli_query($db_con, "update `" . $workFlowTblName . "` Set wf_purpose='$purpose',wf_amt='$wf_amt',wf_netamt='$namt',wf_rupee='$rupee',wf_amount='$famount' where tbl_id='$LastValuesId'"); //or die('Error31:' . mysqli_error($db_con));
                                        //                             
                                    }
                                    //                        }
                                }
                                $form_id = $formid['form_id'];

                                $formbuild = "<table class='table' border='1' cellspacing='7' width='100%' style='border-collapse: collapse; font-size:12px;'>";
                                $qry = mysqli_query($db_con, "select * from tbl_form_attribute where fid='$form_id' and dependency_id is null"); //or die(mysqli_error($db_con));
                                $rowdate = mysqli_fetch_assoc($qry);
                                $colname = mysqli_query($db_con, "select * from $workFlowTblName where tbl_id='$LastValuesId'"); // or die("Error:" . mysqli_error($db_con));
                                $fetch = mysqli_fetch_assoc($colname);

                                $userdata = mysqli_query($db_con, "select first_name,last_name,designation from tbl_user_master where user_id='$user_id'"); //or die("Error:" . mysqli_errno($db_con));
                                $userresult = mysqli_fetch_assoc($userdata);
                                $fetchdata = mysqli_fetch_all($colname, MYSQLI_ASSOC);

                                //echo'table'. $fetchdata['wf_materialrequired'];
                                //echo $fetchdata['ticket_id'];
                                $i = 1;
                                //                    while ($row = mysqli_fetch_assoc($qry)) {
                                // echo $row['name'];

                                if ($rowdate['type'] == "header") {
                                    $formbuild .= "<tr>";
                                    $formbuild .= "<td colspan='4' align='center'>" . "<b>" . "<img src='assets/images/crc.png'  height='100px'>" . "</b>" . "</td>";
                                    $formbuild .= "</tr>";
                                    $formbuild .= "<tr>";
                                    $formbuild .= "<td colspan='4' align='center'>" . "<b>" . $rowdate['label'] . "</b>" . "</td>";
                                    $formbuild .= "</tr>";
                                    $formbuild .= "<tr>";
                                    $formbuild .= "<td colspan='4' align='center'>" . "<b>" . "CASH VOUCHER" . "</b>" . "</td>";
                                    $formbuild .= "</tr>";
                                }
                                if ($i == 1) {
                                    $formbuild .= "<tr>";
                                    $formbuild .= "<td><b>" . Name . "</b></td>";
                                    $formbuild .= "<td>" . $userresult['first_name'] . " " . $userresult['last_name'] . "</td>";
                                    $formbuild .= "</tr>";
                                    $formbuild .= "<tr>";
                                    $formbuild .= "<td><b>" . Designation . "</b></td>";
                                    $formbuild .= "<td>" . $userresult['designation'] . "</td>";
                                    $formbuild .= "</tr>";
                                }

                                $formbuild .= "<tr>";
                                //                            $formbuild .= "<td><b>" . $row['label'] . "</b></td>";
                                //                            foreach ($fetch as $val) {
                                //                                
                                //                                if ($row['name'] == $val->name) {
                                //                                    
                                //                                    echo $name = $val->name;
                                //                                    foreach ($fetchdata as $values) {
                                //                                        $formbuild .= "<td>" . $values[$name].(($values[$name]=='CO(Compensatory off)')?' - '.$values['co']:'') . "</td>";
                                //                                      
                                //                                        
                                //                                    }
                                //                                }
                                //                           
                                //                                
                                //                            }
                                $ccenter = mysqli_escape_string($db_con, $fetch['wf_ccenter']);
                                $whouse = mysqli_escape_string($db_con, $fetch['wf_whouse']);
                                $qrysql = "select * from `tbl_cost_center` where cc_id='$ccenter'";
                                $qrysql1 = "select * from `tbl_whouse_master` where wh_id='$whouse'";
                                $qry2 = mysqli_query($db_con, $qrysql); //or die(mysqli_error($db_con));
                                $result = mysqli_fetch_assoc($qry2);
                                $qry1 = mysqli_query($db_con, $qrysql1); //or die(mysqli_error($db_con));
                                $result1 = mysqli_fetch_assoc($qry1);
                                $formbuild .= "<td><b>Cost Center</b></td><td>" . $result['cc_name'] . "</td>";
                                $formbuild .= "<td><b>Warehouse</b></td><td>" . $result1['wh_name'] . "</td>";
                                $formbuild .= "</tr>";
                                $formbuild .= "<tr>";
                                $formbuild .= "<td><b>No.</b></td><td>" . $fetch['wf_no'] . "</td>";
                                $formbuild .= "<td><b>Debit Account</b></td><td>" . $fetch['wf_debitac'] . "</td>";
                                $formbuild .= "</tr>";
                                $formbuild .= "<tr>";
                                $formbuild .= "<td><b>Date</b></td><td>" . $fetch['wf_dateselect'] . "</td>";

                                $formbuild .= "</tr>";
                                $formbuild .= "<tr>";
                                $formbuild .= "<td colspan='2'><b>Purpose</b></td>";
                                $formbuild .= "<td><b>Rupees:</b></td>";
                                $formbuild .= "<td><b>Paisa:</b></td>";

                                $formbuild .= "</tr>";


                                $pupose = explode(",", $fetch['wf_purpose']);
                                $amount = explode(",", $fetch['wf_amt']);
                                $netamont = explode(",", $fetch['wf_netamt']);
                                for ($i = 0; $i < count($pupose); $i++) {
                                    $formbuild .= "<tr style='border-style: solid'>";
                                    $formbuild .= "<td colspan='2'>" . $pupose[$i] . "</td>";
                                    $formbuild .= "<td>" . $amount[$i] . "</td>";
                                    $formbuild .= "<td>" . $netamont[$i] . "</td>";
                                    $formbuild .= "</tr>";
                                }
                                //                              for($i=0;$i<count($amount);$i++)
                                //                             {
                                //                                    $formbuild.="<tr>";
                                //                          $formbuild.="<td>".$amount[$i]."</td>";
                                //                            $formbuild.="</tr>";
                                //                             }
                                //                              for($i=0;$i<count($netamont);$i++)
                                //                             {
                                //                                    $formbuild.="<tr>";
                                //                          $formbuild.="<td>".$netamont[$i]."</td>";
                                //                           $formbuild.="</tr>";
                                //                             }
                                //                        $i++;
                                //                    }
                                //                            $formbuild .= "<tr>";
                                //                            $formbuild .= "<td><b></b></td>";
                                //                            $formbuild .= "</tr>";
                                $formbuild .= "<tr>";
                                $formbuild .= "<td colspan='4' align='center'><b>Received from Capital Record Centre Pvt. Ltd. </b></td>";
                                $formbuild .= "</tr>";
                                $formbuild .= "<tr>";
                                $formbuild .= "<td><b>Total Rupees:</b></td>";
                                $formbuild .= "<td>" . $fetch['wf_rupee'] . "</td>";
                                $formbuild .= "</tr>";
                                $formbuild .= "<tr>";
                                $formbuild .= "<td><b>Total Amount:</b></td>";
                                $formbuild .= "<td>" . $fetch['wf_amount'] . "</td>";
                                $formbuild .= "<td><b> Receiver Signature</b></td><td><b>Manager</b></td>";
                                $formbuild .= "</tr>";
                                $formbuild .= "</table>";
                            }
                        } else {

                            $formbrige = mysqli_query($db_con, "select form_id from tbl_bridge_workflow_to_form where workflow_id='$wfid'");
                            $formid = mysqli_fetch_assoc($formbrige);
                            $formnameqry = mysqli_query($db_con, "select name from tbl_form_attribute where fid='$formid[form_id]' and dependency_id is null");
                            $coloum .= "user_id,ticket_id";
                            $values = "'" . $user_id . "'" . "," . "'" . $ticket . "'";


                            while ($rowdata = mysqli_fetch_assoc($formnameqry)) {
                                $names = $rowdata['name'];
                                //echo$_POST["$names"];die();

                                if (!empty($names)) {
                                    $coloum .= "," . $names;
                                    $values .= ",'" . mysqli_real_escape_string($db_con, xss_clean($_POST["$names"])) . "'";
                                }
                                //                    array_push($formvalues, $_POST[$names]);
                            }
                            //echo $values;
                            $sqlForm = "insert into " . $workFlowTblName . "($coloum) values ($values)";

                            $insertqry = mysqli_query($db_con, $sqlForm); // or die('Error:' . mysqli_error($db_con));
                            if ($insertqry) {
                                $LastValuesId = mysqli_insert_id($db_con);
                                if (!empty($_POST['CO'])) {
                                    $coDate = $_POST['CO'];

                                    $chkColExist = mysqli_query($db_con, "SELECT COLUMN_NAME FROM  information_schema.COLUMNS where  TABLE_NAME ='$workFlowTblName'  and COLUMN_NAME = 'co';"); //or die('Error:' . mysqli_error($db_con));
                                    if (mysqli_num_rows($chkColExist) > 0) {
                                        $updateco = mysqli_query($db_con, "update `" . $workFlowTblName . "` Set co='$coDate' where tbl_id='$LastValuesId'"); //or die('Error:' . mysqli_error($db_con));
                                    } else {
                                        $qry = mysqli_query($db_con, "ALTER TABLE `" . $workFlowTblName . "` ADD co varchar(255)"); //or die('Error:' . mysqli_error($db_con));
                                        if ($qry) {
                                            $updateco = mysqli_query($db_con, "update `" . $workFlowTblName . "` Set co='$coDate' where tbl_id='$LastValuesId'"); //or die('Error:' . mysqli_error($db_con));
                                        }
                                    }
                                }

                                $form_id = $formid['form_id'];

                                $formbuild = "<table class='table' border='1' cellspacing='7' width='100%' style='border-collapse: collapse; font-size:12px;'>";
                                $qry = mysqli_query($db_con, "select * from tbl_form_attribute where fid='$form_id' and dependency_id is null"); //or die(mysqli_error($db_con));

                                $colname = mysqli_query($db_con, "select * from $workFlowTblName where tbl_id='$LastValuesId'"); // or die("Error:" . mysqli_error($db_con));
                                $fetch = mysqli_fetch_fields($colname);

                                $userdata = mysqli_query($db_con, "select first_name,last_name,designation from tbl_user_master where user_id='$user_id'"); //or die("Error:" . mysqli_errno($db_con));
                                $userresult = mysqli_fetch_assoc($userdata);
                                $fetchdata = mysqli_fetch_all($colname, MYSQLI_ASSOC);

                                //echo'table'. $fetchdata['wf_materialrequired'];
                                //echo $fetchdata['ticket_id'];
                                $i = 1;
                                while ($row = mysqli_fetch_assoc($qry)) {
                                    $row['name'];

                                    if ($row['type'] == "header") {
                                        $formbuild .= "<tr>";
                                        $formbuild .= "<td colspan='2' align='center'>" . "<b>" . $row['label'] . "</b>" . "</td>";
                                        $formbuild .= "</tr>";
                                    }
                                    if ($i == 1) {
                                        $formbuild .= "<tr>";
                                        $formbuild .= "<td><b>" . Name . "</b></td>";
                                        $formbuild .= "<td>" . $userresult['first_name'] . " " . $userresult['last_name'] . "</td>";
                                        $formbuild .= "</tr>";
                                        $formbuild .= "<tr>";
                                        $formbuild .= "<td><b>" . Designation . "</b></td>";
                                        $formbuild .= "<td>" . $userresult['designation'] . "</td>";
                                        $formbuild .= "</tr>";
                                    }

                                    $formbuild .= "<tr>";
                                    if ($row['type'] != "header") {

                                        $formbuild .= "<td><b>" . $row['label'] . "</b></td>";
                                    }
                                    foreach ($fetch as $val) {

                                        if ($row['name'] == $val->name) {

                                            $name = $val->name;
                                            foreach ($fetchdata as $values) {
                                                $formbuild .= "<td>" . $values[$name] . (($values[$name] == 'CO(Compensatory off)') ? ' - ' . $values['co'] : '') . "</td>";
                                            }
                                        }
                                    }
                                    $formbuild .= "</tr>";

                                    $i++;
                                }

                                $formbuild .= "</table>";
                            }
                        }
                        if ($pdf_req == 1) {

                            include 'exportpdf.php';

                            //                if ((isset($_POST['taskRemark'])) && (!empty(trim($_POST['taskRemark'])))) { //if content of CKEditor ISN'T empty
                            $posted_editor = $formbuild; //get content of CKEditor
                            $slperm = mysqli_query($db_con, "select * from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]'");
                            $rwSlperm = mysqli_fetch_assoc($slperm);
                            $sl_id = $rwSlperm['sl_id'];
                            $docName = mysqli_query($db_con, "select sl_id,sl_name from tbl_storage_level where sl_id = '$sl_id'"); // or die('Eror:' . mysqli_error($db_con));
                            $rwdocName = mysqli_fetch_assoc($docName);
                            $folderName = str_replace(" ", "", $workFlowName);
                            $pdfName = trim($workFlowName) . "_" . mktime() . ".pdf"; //specify the file save location and the file name
                            $path = 'extract-here/' . str_replace(" ", "", $workFlowName);
                            if (!is_dir($path)) {
                                mkdir($path, 0777, true);
                            }
                            $path = $path . '/' . $pdfName;
                            exportPDF($posted_editor, $path);
                            $wrkflowFsize = filesize($path);
                            $wrkflowFsize = round(($wrkflowFsize / 1024), 2);
                            $doc_name = $sl_id . '_' . $wfid;
                            $pagecount = count_pages($path);
                            $wrkflowDoc = mysqli_query($db_con, "INSERT INTO tbl_document_master (doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages, dateposted, workflow_id) VALUES ('$doc_name', '$pdfName', 'pdf', '$folderName/$pdfName', '$user_id', '$wrkflowFsize', '$pagecount', '$date', '$wfid')"); // or die('Eror:' . mysqli_error($db_con));
                            $docId = mysqli_insert_id($db_con);
                            $newdocname = base64_encode($docId);

                            //create thumbnail
                            $uploadedfilename = $path;


                            //changePdfToImage($uploadedfilename,$newdocname);


                            $id = $sl_id . '_' . $docId . '_' . $wfid;

                            $destinationPath = str_replace(" ", "", $workFlowName) . '/' . $pdfName;
                            $sourcePath = $path;
                            uploadFileInFtpServer($destinationPath, $sourcePath);
                        } else {
                            $taskRemark = $formbuild;
                        }
                    }
                    //end create pdf
                    //upload files if any
                    $files = $_FILES['fileName']['name'];
                    //if (strlen($files) < 50) {
                    if (!empty($files)) {
                        for ($i = 0; $i < count($files); $i++) {
                            $file_name = $_FILES['fileName']['name'][$i];
                            $file_size = $_FILES['fileName']['size'][$i];
                            $file_type = $_FILES['fileName']['type'][$i];
                            $file_tmp = $_FILES['fileName']['tmp_name'][$i];
                            if (!empty($file_name)) {
                                $pageCount = $_POST['pageCount'];
                                //echo"<script>alert('$pageCount')</script>";
                                $fname = substr($file_name, 0, strrpos($file_name, '.'));
                                $encryptName = urlencode(base64_encode($fname));
                                $fileExtn = substr($file_name, strrpos($file_name, '.') + 1);
                                $folder = str_replace(" ", "", $workFlowName);
                                $image_path = 'extract-here/' . $folder . '/';

                                if (!dir($image_path)) {
                                    mkdir($image_path, 0777, TRUE); // or die("Error local folder:". print_r(error_get_last()));
                                }
                                $file_name = time() . '_' . $file_name;
                                $image_path = $image_path . $file_name;

                                $upload = move_uploaded_file($file_tmp, $image_path);

                                if ($upload) {

                                    $destinationPath = $folder . '/' . $file_name;
                                    $sourcePath = $image_path;
                                    uploadFileInFtpServer($destinationPath, $sourcePath);

                                    $query = "INSERT INTO tbl_document_master(doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages, dateposted, workflow_id) VALUES ('$id', '$fname', '$fileExtn', '$folder/$file_name', '$user_id', '$file_size', '$pageCount', '$date', '$wfid')";
                                    $exe = mysqli_query($db_con, $query); // or die('Error query failed' . mysqli_error($db_con));
                                    $docId2 = mysqli_insert_id($db_con);

                                    // Decrypt file
                                    decrypt_my_file($image_path);

                                    $newdocname = base64_encode($docId2);

                                    //create thumbnail
                                    $uploadedfilename = $image_path;

                                    if ($fileExtn == 'jpg' || $fileExtn == 'jpeg' || $fileExtn == 'png') {
                                        createThumbnail2($uploadedfilename, $newdocname);
                                    } elseif ($fileExtn == 'pdf') {
                                        changePdfToImage($uploadedfilename, $newdocname);
                                    }

                                    if (empty($docId)) {


                                        $docId = $docId2;
                                        $id = $sl_id . '_' . $docId2 . '_' . $wfid;
                                    }
                                }
                            }
                        }
                    }
                    //} else{
                    //    echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '", "' . $lang['file_name _too_long'] . '")</script>';
                    //}
                    //end upload file
                    // echo "INSERT INTO tbl_doc_assigned_wf(task_id, doc_id, start_date, end_date, task_status, assign_by, task_remarks,ticket_id) VALUES ('$tskId','$docId',  '$date', '$endDate', 'Pending', '$user_id','$taskRemark','$ticket')";
                    $taskRemark = mysqli_real_escape_string($db_con, $taskRemark);
                    $insertInTask = mysqli_query($db_con, "INSERT INTO tbl_doc_assigned_wf(task_id, doc_id, start_date, end_date, task_status, assign_by, task_remarks,ticket_id) VALUES ('$tskId','$docId',  '$date', '$endDate', 'Pending', '$user_id','$taskRemark','$ticket')"); // or die('Erorr: ' . mysqli_error($db_con));
                    $idins = mysqli_insert_id($db_con);

                    $getTask = mysqli_query($db_con, "select * from tbl_task_master where task_id = '$tskId'"); // or die('Error:' . mysqli_error($db_con));
                    $rwgetTask = mysqli_fetch_assoc($getTask);
                    $TskStpId = $rwgetTask['step_id'];
                    $TskWfId = $rwgetTask['workflow_id'];
                    $TskOrd = $rwgetTask['task_order'];
                    $TskAsinToId = $rwgetTask['assign_user'];
                    $nextTaskOrd = $TskOrd + 1;
                    //for export pdf

                    nextTaskAsin($nextTaskOrd, $TskWfId, $TskStpId, $docId, $date, $user_id, $db_con, $taskRemark, $ticket);

                    if ($insertInTask) {
                        //echo '<script>taskSuccess("'. basename($_SERVER['REQUEST_URI']).'", "Submitted Successfully!!");</script>';
                        require_once './mail.php';

                        if (MAIL_BY_SOCKET) {

                            $paramsArray = array(
                                'ticket' => $ticket,
                                'idins' => $idins,
                                'db_con' => $db_con,
                                'projectName' => $projectName,
                                'action' => 'assignTask'
                            );
                            mailBySocket($paramsArray);
                        } else {

                            $mail = assignTask($ticket, $idins, $db_con, $projectName);
                        }

                        //if ($mail) {
                        // mysqli_commit($db_con);
                        //send sms to mob
                        //                        $getMobNum = mysqli_query($db_con, "select * from tbl_user_master where user_id = '$user_id'") or die('Error:' . mysqli_error($db_con));
                        //                        $rwgetMobNum = mysqli_fetch_assoc($getMobNum);
                        //                        $submtByMob = $rwgetMobNum['phone_no'];
                        //                        $msg = 'Your Ticket Id is: ' . $ticket . ' and Your Task is in Process.';
                        //                        $sendMsgToMbl = smsgatewaycenter_com_Send($submtByMob, $msg, $debug = false);
                        //
                        //send sms to assign user
                        //                        $getTaskAsinToMob = mysqli_query($db_con, "select * from tbl_user_master where user_id='$TskAsinToId'") or die('Error:' . mysqli_error($db_con));
                        //                        $rwgetTaskAsinToMob = mysqli_fetch_assoc($getTaskAsinToMob);
                        //                        $asinToMob = $rwgetTaskAsinToMob['phone_no'];
                        //                        $msgAsinTo = 'New Task With Ticket Id : ' . $ticket . ' has been Assigned To You.';
                        //                        $sendMsgToMbl = smsgatewaycenter_com_Send($asinToMob, $msgAsinTo, $debug = false);
                        //



                        echo '<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '", "' . $lang['Sumitd_Sucsfly'] . '");</script>';
                        //                         } else {
                        // //echo'Opps!! Mail not sent!';
                        //                             echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '", "' . $lang['Ops_Ml_nt_snt'] . '")</script>';
                        //                         }
                    } else {
                        echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '", "' . $lang['Opps_Sbmsn_fld'];
                        '")</script>';
                    }
                } else {
                    echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '", "' . $lang['Tre_is_no_tsk_in_ts_wfw'] . '")</script>';
                }
            } else {
                echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '", "' . $lang['Task_Creation_Failed_Please_Select_storage'] . '")</script>';
            }
        } else {

            $docId = '0';
            //$wfid = base64_decode(urldecode($_GET['wid']));

            $wfd = mysqli_query($db_con, "select * from tbl_workflow_master where workflow_id='$wfid'");
            $rwWfd = mysqli_fetch_assoc($wfd);
            $workFlowName = $rwWfd['workflow_name'];

            $user_id = $_SESSION['cdes_user_id'];

            $workFlowArray = explode(" ", $workFlowName);
            $ticket = '';
            for ($w = 0; $w < count($workFlowArray); $w++) {
                $name = $workFlowArray[$w];
                $ticket = $ticket . substr($name, 0, 1);
            }

            $ticket = $ticket . '_' . $user_id . '_' . strtotime($date);
            if ($rwWfd['form_req'] == 1 || $rwWfd['form_req'] == 2) {
                $taskRemark = "";
            } else {
                $taskRemark = mysqli_real_escape_string($db_con, $_POST['taskRemark']);
            }

            //if file uploaded then
            if (!empty($_POST['lastMoveId'])) {

                $chkrw = mysqli_query($db_con, "select * from tbl_task_master where workflow_id = '$wfid'"); // or die('Error:' . mysqli_error($db_con));

                if (mysqli_num_rows($chkrw) > 0) {
                    $sl_id = $_POST['lastMoveId'];
                    $id = $sl_id . '_' . $wfid;


                    if ($pdf_req == 1 || !empty($_FILES['fileName']['name'])) {

                        $docName = mysqli_query($db_con, "select * from tbl_storage_level where sl_id = '$sl_id'") or die('Eror:' . mysqli_error($db_con));
                        $rwdocName = mysqli_fetch_assoc($docName);
                        $updir = getStoragePath($db_con, $rwdocName['sl_parent_id'], $rwdocName['sl_depth_level']);
                        if (!empty($updir)) {
                            $updir = $updir . '/';
                        } else {
                            $updir = '';
                        }

                        $folderName = $updir . str_replace(" ", "", $rwdocName['sl_name']);
                    }
                    if ($rwWfd['pdf_req'] == 1 || $rwWfd['form_req'] == 1 || $rwWfd['form_req'] == 2) {
                        include 'exportpdf.php';

                        if ((isset($_POST['taskRemark'])) && (!empty(trim($_POST['taskRemark'])))) { //if content of CKEditor ISN'T empty
                            $posted_editor = trim($_POST['taskRemark']); //get content of CKEditor

                            $pdfName = trim($workFlowName) . "_" . mktime() . ".pdf"; //specify the file save location and the file name
                            $path = 'extract-here/' . $folderName;
                            if (!is_dir($path)) {
                                mkdir($path, 0777, true);
                            }
                            $path = $path . '/' . $pdfName;
                            $wrkflowFsize = filesize($path);
                            $wrkflowFsize = round(($wrkflowFsize / 1024), 2);
                            $doc_name = $sl_id . '_' . $wfid;
                            $pagecount = count_pages($path);
                            $wrkflowDoc = mysqli_query($db_con, "INSERT INTO tbl_document_master (doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages, dateposted,generated_pdf_doc,ticket_id,documentnumber,doc_title) VALUES ('$doc_name', '$pdfName', 'pdf', '$folderName/$pdfName', '$user_id', '$wrkflowFsize', '$pagecount', '$date','1','$ticket','$doc_number','$doc_title')"); // or die('Eror:' . mysqli_error($db_con));
                            $docId = mysqli_insert_id($db_con);
                            exportPDF($posted_editor, $path);
                            $id = $sl_id . '_' . $docId . '_' . $wfid;


                            $docId = mysqli_insert_id($db_con);
                            $newdocname = base64_encode($docId);

                            //create thumbnail
                            $uploadedfilename = $path;
                            changePdfToImage($uploadedfilename, $newdocname);

                            $destinationPath = $folderName . '/' . $pdfName;
                            $sourcePath = $path;
                            uploadFileInFtpServer($destinationPath, $sourcePath);
                        }
                    }

                    $files = $_FILES['fileName']['name'];
                    //if (strlen($files) < 50) {
                    if (!empty($files)) {
                        //print_r($files);
                        for ($i = 0; $i < count($files); $i++) {
                            $file_name = $_FILES['fileName']['name'][$i];
                            $file_size = $_FILES['fileName']['size'][$i];
                            $file_type = $_FILES['fileName']['type'][$i];
                            $file_tmp = $_FILES['fileName']['tmp_name'][$i];
                            if (!empty($file_name)) {
                                $pageCount = $_POST['pageCount'];
                                $fname = substr($file_name, 0, strrpos($file_name, '.'));
                                $encryptName = urlencode(base64_encode($fname));
                                $fileExtn = substr($file_name, strrpos($file_name, '.') + 1);
                                $folder = $folderName;
                                $image_path = 'extract-here/' . $folder . '/';

                                if (!dir($image_path)) {
                                    mkdir($image_path, 0777, TRUE); // or die("Error local folder:". print_r(error_get_last()));
                                }
                                $file_name = time() . '_' . $file_name;
                                $image_path = $image_path . $file_name;

                                $upload = move_uploaded_file($file_tmp, $image_path) or die(print_r(error_get_last()));

                                if ($upload) {

                                    $destinationPath = $folder . '/' . $file_name;

                                    $sourcePath = $image_path;
                                    uploadFileInFtpServer($destinationPath, $sourcePath);

                                    $query = "INSERT INTO tbl_document_master(doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages, dateposted,ticket_id,documentnumber,doc_title) VALUES ('$id', '$fname', '$fileExtn', '$folder/$file_name', '$user_id', '$file_size', '$pageCount', '$date','$ticket','$doc_number','$doc_title')";
                                    $exe = mysqli_query($db_con, $query); // or die('Error query failed' . mysqli_error($db_con));
                                    $docId2 = mysqli_insert_id($db_con);

                                    // Decrypt file
                                    decrypt_my_file($image_path);

                                    $newdocname = base64_encode($docId2);

                                    //create thumbnail
                                    $uploadedfilename = $image_path;

                                    if ($fileExtn == 'jpg' || $fileExtn == 'jpeg' || $fileExtn == 'png') {
                                        createThumbnail2($uploadedfilename, $newdocname);
                                    } elseif ($fileExtn == 'pdf') {
                                        changePdfToImage($uploadedfilename, $newdocname);
                                    }

                                    if (empty($docId)) {

                                        $docId = $docId2;
                                        $id = $sl_id . '_' . $docId2 . '_' . $wfid;
                                    }
                                }
                            }
                        }
                    }
                    $getStep = mysqli_query($db_con, "select * from tbl_step_master where workflow_id = '$wfid' ORDER BY step_order ASC LIMIT 1"); // or die('Error:' . mysqli_error($db_con));
                    $getStpId = mysqli_fetch_assoc($getStep);
                    $stpId = $getStpId['step_id'];

                    $getTask = mysqli_query($db_con, "select * from tbl_task_master where step_id = '$stpId' ORDER BY task_order ASC LIMIT 1"); // or die('Error:' . mysqli_error($db_con));
                    $getTaskId = mysqli_fetch_assoc($getTask);
                    // echo 'ok';
                    $tskId = $getTaskId['task_id'];

                    $getTaskDl = mysqli_query($db_con, "select * from tbl_task_master where task_id='$tskId'"); // or die('Error:' . mysqli_error($db_con));
                    $rwgetTaskDl = mysqli_fetch_assoc($getTaskDl);


                    if ($rwgetTaskDl['deadline_type'] == 'Date' || $rwgetTaskDl['deadline_type'] == 'Hrs') {

                        $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 60));
                    }
                    if ($rwgetTaskDl['deadline_type'] == 'Days') {

                        $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 24 * 60 * 60));
                    }
                    $insertInTask = mysqli_query($db_con, "INSERT INTO tbl_doc_assigned_wf(task_id, doc_id, start_date, end_date, task_status, assign_by, task_remarks,ticket_id) VALUES ('$tskId', '$docId', '$date', '$endDate', 'Pending', '$user_id','$taskRemark','$ticket')"); // or die('Erorr: hh' . mysqli_error($db_con));
                    $idins = mysqli_insert_id($db_con);

                    mysqli_query($db_con, "INSERT INTO tbl_railway_master(
                    rfi_no, 
                    rfi_date, 
                    type_regular, 
                    name_of_the_contractor, 
                    item_no_as_per, 
                    inspection_required_date, 
                    location_from, 
                    location_to, 
                    name_of_the_inspecting_engineer, 
                    inspected_on_date, 
                    c_and_g, 
                    earthwork, 
                    blanketing, 
                    survey, 
                    safety, 
                    qc_material, 
                    shuttering_reinforcement, 
                    concreting, 
                    drain_retaining_wall, 
                    roads, 
                    utilities, 
                    dismantling_of_Pway, 
                    bridge_work, 
                    other, 
                    description_of_work, 
                    enclosures_attached, 
                    signature_of_the_contractor, 
                    remarks_of_the_inspection, 
                    approved, 
                    not_approved, 
                    signature_of_the_inspection, 
                    date, 
                    name, 
                    designation, 
                    ticket_id,
                    railway_type
                ) VALUES (
                    '" . $_POST['rfi_no'] . "', 
                    '" . $_POST['rfi_date'] . "', 
                    '" . $_POST['type_regular'] . "', 
                    '" . $_POST['name_of_the_contractor'] . "', 
                    '" . $_POST['item_no_as_per'] . "', 
                    '" . $_POST['inspection_required_date'] . "', 
                    '" . $_POST['location_from'] . "', 
                    '" . $_POST['location_to'] . "', 
                    '" . $_POST['name_of_the_inspecting_engineer'] . "', 
                    '" . $_POST['inspected_on_date'] . "', 
                    " . (isset($_POST['c_and_g']) ? 1 : 0) . ", 
                    " . (isset($_POST['earthwork']) ? 1 : 0) . ", 
                    " . (isset($_POST['blanketing']) ? 1 : 0) . ", 
                    " . (isset($_POST['survey']) ? 1 : 0) . ", 
                    " . (isset($_POST['safety']) ? 1 : 0) . ", 
                    " . (isset($_POST['qc_material']) ? 1 : 0) . ", 
                    " . (isset($_POST['shuttering_reinforcement']) ? 1 : 0) . ", 
                    " . (isset($_POST['concreting']) ? 1 : 0) . ", 
                    " . (isset($_POST['drain_retaining_wall']) ? 1 : 0) . ", 
                    " . (isset($_POST['roads']) ? 1 : 0) . ", 
                    " . (isset($_POST['utilities']) ? 1 : 0) . ", 
                    " . (isset($_POST['dismantling_of_Pway']) ? 1 : 0) . ", 
                    " . (isset($_POST['Bridge_Work']) ? 1 : 0) . ", 
                    " . (isset($_POST['other']) ? 1 : 0) . ", 
                    '" . $_POST['description_of_work'] . "', 
                    '" . $_POST['enclosures_attached'] . "', 
                    '" . $_POST['signature_of_the_contractor'] . "', 
                    '" . $_POST['remarks_of_the_inspection'] . "', 
                    " . (isset($_POST['approved']) ? 1 : 0) . ", 
                    " . (isset($_POST['not_approved']) ? 1 : 0) . ", 
                    '" . $_POST['signature_of_the_inspection'] . "', 
                    '" . $_POST['date'] . "', 
                    '" . $_POST['name'] . "', 
                    '" . $_POST['designation'] . "', 
                    '$ticket'
                    '" . $_POST['railway_type'] . "', 
                )");
                    $last_id_here = mysqli_insert_id($db_con);
                    $string_serial_number = stringSerialNumber($last_id_here);
                    mysqli_query($db_con, "update tbl_railway_master Set string_serial_number='$string_serial_number' where id='$last_id_here'");


                    $getTask = mysqli_query($db_con, "select * from tbl_task_master where task_id = '$tskId'"); // or die('Error:' . mysqli_error($db_con));
                    $rwgetTask = mysqli_fetch_assoc($getTask);
                    $TskStpId = $rwgetTask['step_id'];
                    $TskWfId = $rwgetTask['workflow_id'];
                    $TskOrd = $rwgetTask['task_order'];
                    $TskAsinToId = $rwgetTask['assign_user'];
                    $nextTaskOrd = $TskOrd + 1;

                    nextTaskAsin($nextTaskOrd, $TskWfId, $TskStpId, $docId, $date, $user_id, $db_con, $taskRemark, $ticket);
                    if ($insertInTask) {
                        //echo '<script> alert("ok")</script>';
                        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `doc_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,null,'Task Created','$date',null,'$host',null)"); // or die('error : ' . mysqli_error($db_con));
                        require_once './mail.php';


                        if (MAIL_BY_SOCKET) {

                            $paramsArray = array(
                                'ticket' => $ticket,
                                'idins' => $idins,
                                'db_con' => $db_con,
                                'projectName' => $projectName,
                                'action' => 'assignTask'
                            );
                            mailBySocket($paramsArray);
                        } else {

                            $mail = assignTask($ticket, $idins, $db_con, $projectName);
                        }


                        // if ($mail) {
                        //send sms to mob who submit
                        //                                $getMobNum = mysqli_query($db_con, "select * from tbl_user_master where user_id = '$user_id'") or die('Error:' . mysqli_error($db_con));
                        //                                $rwgetMobNum = mysqli_fetch_assoc($getMobNum);
                        //                                $submtByMob = $rwgetMobNum['phone_no'];
                        //                                $msg = 'Your Ticket Id is: ' . $ticket . ' and Your Task is in Process.';
                        //                                $sendMsgToMbl = smsgatewaycenter_com_Send($submtByMob, $msg, $debug = false);
                        //


                        echo '<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '", "' . $lang['Sumitd_Sucsfly'] . '");</script>';
                        // } else {
                        //     echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '", "' . $lang['Ops_Ml_nt_snt'] . '")</script>';
                        // }
                    } else {
                        echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '", "' . $lang['Opps_Sbmsn_fld'] . '")</script>';
                    }
                    //}else{
                    //    echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '", "' . $lang['file_name _too_long'] . '")</script>';
                    //}
                } else {
                    echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '", "' . $lang['Tre_is_no_tsk_in_ts_wfw'] . '")</script>';
                }
            } else if (empty($_POST['lastMoveId'])) {
                $slperm = mysqli_query($db_con, "select * from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]'");
                $rwSlperm = mysqli_fetch_assoc($slperm);
                $sl_id = $rwSlperm['sl_id'];
                $chkrw = mysqli_query($db_con, "select * from tbl_task_master where workflow_id = '$wfid'") or die('Error:' . mysqli_error($db_con));
                $id = $sl_id . '_' . $wfid;

                if (mysqli_num_rows($chkrw) > 0) {
                    $getStep = mysqli_query($db_con, "select * from tbl_step_master where workflow_id = '$wfid' ORDER BY step_order ASC LIMIT 1"); // or die('Error:' . mysqli_error($db_con));
                    $getStpId = mysqli_fetch_assoc($getStep);
                    $stpId = $getStpId['step_id'];

                    $getTask = mysqli_query($db_con, "select * from tbl_task_master where step_id = '$stpId' ORDER BY task_order ASC LIMIT 1"); // or die('Error:' . mysqli_error($db_con));
                    $getTaskId = mysqli_fetch_assoc($getTask);
                    $tskId = $getTaskId['task_id'];

                    $getTaskDl = mysqli_query($db_con, "select * from tbl_task_master where task_id='$tskId'"); // or die('Error:' . mysqli_error($db_con));
                    $rwgetTaskDl = mysqli_fetch_assoc($getTaskDl);


                    if ($rwgetTaskDl['deadline_type'] == 'Date' || $rwgetTaskDl['deadline_type'] == 'Hrs') {

                        $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 60));
                    }
                    if ($rwgetTaskDl['deadline_type'] == 'Days') {

                        $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 24 * 60 * 60));
                    }

                    if ($rwWfd['form_type'] == 1 || $rwWfd['form_type'] == 2) {
                        include 'exportpdf.php';
                        //if ((isset($_POST['taskRemark'])) && (!empty(trim($_POST['taskRemark']))))
                        // if ((isset($_POST['taskRemark']))) { //if content of CKEditor ISN'T empty
                        $posted_editor = trim($_POST['taskRemark']); //get content of CKEditor
                        $folderName = str_replace(" ", "", $workFlowName);
                        $pdfName = trim($workFlowName) . "_" . mktime() . ".pdf"; //specify the file save location and the file name
                        $path = 'extract-here/' . str_replace(" ", "", $workFlowName);
                        if (!is_dir($path)) {
                            mkdir($path, 0777, true);
                        }
                        $path = $path . '/' . $pdfName;
                        $wrkflowFsize = filesize($path);
                        $wrkflowFsize = round(($wrkflowFsize / 1024), 2);

                        if ($_POST['railway_type'] == 1) {
                            $doc_name = 114;
                        }
                        if ($_POST['railway_type'] == 2) {
                            $doc_name = 115;
                        }


                        // $doc_name = $sl_id . '_' . $wfid;


                        $noofPages = count_pages($target_path . $filenameEnct);
                        //print_r($path . $pagecount);
                        $wrkflowDoc = mysqli_query($db_con, "INSERT INTO tbl_document_master (doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages, dateposted,generated_pdf_doc,ticket_id) VALUES ('$doc_name', '$pdfName', 'pdf', '$folderName/$pdfName', '$user_id', '$wrkflowFsize', $noofPages, '$date','1','$ticket')") or die('Eror:' . mysqli_error($db_con));
                        $docId = mysqli_insert_id($db_con);

                        $htmlContent = '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request for Inspection</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .table th, .table td { border: 1px solid #000; padding: 8px; text-align: left; }
        .top-center { text-align: center; vertical-align: middle; }
        .upper { margin-top: 20px; }
        .south_railway { width: 100px; }
    </style>
</head>
<body>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th class="col-md-2" rowspan="2" style="vertical-align:middle; text-align:center;">
                    <img src="assets/images/rail.JPEG" class="south_railway" alt="Railway Logo">
                </th>
                <th class="col-md-8" style="text-align:center; font-size:large; vertical-align:middle;">
                    <u>REQUEST FOR INSPECTION (RFI)</u>
                </th>';

                        if (
                            $rwWfd['form_type'] == 1
                        ) {
                            $htmlContent .= '
                <th class="col-md-2" rowspan="2" style="vertical-align:middle; text-align:center;">
                    <img src="assets/images/pra1.JPEG" class="south_railway" alt="Contractor Logo">
                </th>';
                        } else {
                            $htmlContent .= '
                <th class="col-md-2" rowspan="2" style="vertical-align:middle; text-align:center;">
                    <img src="assets/images/raipur.jpg" class="south_railway" alt="Contractor Logo">
                </th>';
                        }

                        $htmlContent .= '
            </tr>
            <tr>';

                        if (
                            $rwWfd['form_type'] == 1
                        ) {
                            $htmlContent .= '
                <th class="col-md-8" style="text-align:center; vertical-align:middle;">
                    Project Doubling of Railway Project comprising the section commencing from(--) Road station (End CH 967.055)
                    to Surajpur Road Station (End CH : 1006.44) (KM-39.385 KM) beside existing single 84 line in the state of chhattisgarh in the
                    south East central Railway Zone Agt No: SECR/SECRC/CMI/2024/0008/ dt 14-Mar-2024.
                </th>';
                        } else {
                            $htmlContent .= '
                <th class="col-md-8" style="text-align:center; vertical-align:middle;">
                    Major Up-Gradation of Raipur Railway Station of SECR on EPC Mode
                </th>';
                        }

                        $htmlContent .= '
            </tr>
        </thead>
    </table>

    <table class="table table-bordered upper">
        <thead>';

                        if (
                            $rwWfd['form_type'] == 1
                        ) {
                            $htmlContent .= '
            <tr>
                <th class="col-md-6" colspan="3" style="text-align:left; vertical-align:middle;">Client : South East Central Railway</th>
                <th class="col-md-6" colspan="3" style="text-align:center; vertical-align:middle;">Contractor : Barbrik Project Limited</th>
            </tr>';
                        } else {
                            $htmlContent .= '
            <tr>
                                                    <th class="col-md-6" colspan="3" style="text-align:left; vertical-align:middle;">Client : South East Central Railway</th>

                                                    <th class="col-md-6" colspan="3" style="text-align:center; vertical-align:middle;">
                                                        Contractor : RPP-SATHYAMOORTHY JV<br>
                                                        Sub-Contractor : PRA INDIA PVT LTD
                                                    </th>
            </tr>';
                        }

                        $htmlContent .= '
            <tr>
                <th class="col-md-2 top-center">RFI No</th>
                <th class="col-md-2 top-center">RFI Date</th>
                <th class="col-md-2 top-center">Type <br> (Regular/Spot)</th>
                <th class="col-md-2 top-center">Name Of the Contractor\'s Engineer</th>
                <th class="col-md-2 top-center">Item No as per contract <br> (for payment)</th>
                <th class="col-md-2 top-center">Inspection Required On</th>
            </tr>
            <tr>
                <td class="col-md-2">' . htmlspecialchars($_POST['rfi_no']) . '</td>
                <td class="col-md-2">' . htmlspecialchars($_POST['rfi_date']) . '</td>
                <td class="col-md-2">' . htmlspecialchars($_POST['type_regular']) . '</td>
                <td class="col-md-2">' . htmlspecialchars($_POST['name_of_the_contractor']) . '</td>
                <td class="col-md-2">' . htmlspecialchars($_POST['item_no_as_per']) . '</td>
                <td class="col-md-2">' . htmlspecialchars($_POST['inspection_required_date']) . '</td>
            </tr>
        </thead>
    </table>

    <table class="table table-bordered upper">
        <tr>
            <th class="col-md-2" rowspan="2" colspan="2">Location / Chainage</th>
            <th class="col-md-4" rowspan="2">
                ' . htmlspecialchars($_POST['location_from']) . ' to ' . htmlspecialchars($_POST['location_to']) . '
            </th>
            <th class="col-md-4">Name Of the Inspecting Engineer</th>
            <th class="col-md-2">Inspected On</th>
        </tr>
        <tr>
            <th>' . htmlspecialchars($_POST['name_of_the_inspecting_engineer']) . '</th>
            <th>' . htmlspecialchars($_POST['inspected_on_date']) .
                            '</th>
        </tr>
    </table>

<table class="table table-bordered upper">
    <tr>
        <th class="col-md-12" colspan="2" style="text-align:left;">
            Request for Inspection of the following works, Which are /will be ready for inspection
        </th>
    </tr>';

                        // Checkbox items
                        $checkboxItems = [
                            ['C&G', $_POST['c_and_g']],
                            ['Concreting', $_POST['concreting']],
                            ['Earthwork', $_POST['earthwork']],
                            ['Blanketing', $_POST['blanketing']],
                            ['Survey', $_POST['survey']], // Added
                            ['Safety', $_POST['safety']], // Added
                            ['QC/Material', $_POST['qc_material']], // Added
                            ['Shuttering/Reinforcement', $_POST['shuttering_reinforcement']], // Added
                            ['Drain Retaining Wall', $_POST['drain_retaining_wall']], // Ensure you add corresponding checkboxes
                            ['Roads', $_POST['roads']],
                            ['utilities', $_POST['utilities']],
                            ['Dismantling of Pway', $_POST['dismantling_of_Pway']],
                            ['bridge_work', $_POST['Bridge_Work']],
                            ['Others', $_POST['other']],
                            
                        ];

                        foreach ($checkboxItems as $index => $item) {
                            if (isset($item[1]) && $item[1] == 'on') { // Only show items that are checked
                                $htmlContent .= '
        <tr>
            <th class="col-md-6" style="text-align:left;">' . htmlspecialchars($item[0]) . '</th>
            <th class="col-md-6" style="text-align:left;">Yes</th>
        </tr>';
                            }
                        }
                        $htmlContent .= '
</table>

    <table class="table table-bordered upper">
        <tr>
            <th colspan="2" style="text-align:left; vertical-align:top; height: 200px">Description of Work offered for Inspection</th>
            <th colspan="3" style="text-align:left; vertical-align:top;">
                ' . htmlspecialchars($_POST['description_of_work']) . '
            </th>
        </tr>
        <tr>
            <th colspan="2" style="text-align:left; vertical-align:top; height: 100px">Enclosures attached with RFI</th>
            <th colspan="3" style="text-align:left; vertical-align:top;">
                ' . htmlspecialchars($_POST['enclosures_attached']) . '
            </th>
        </tr>
        <tr>
            <th class="col-md-6" colspan="2" style="text-align:left; vertical-align:top; height: 100px">Signature of the Contractor\'s Representative requesting for Inspection</th>
            <th class="col-md-6" colspan="3">' . htmlspecialchars($_POST['signature_of_the_contractor']) . '</th>
        </tr>
        <tr>
            <th class="col-md-6" colspan="2" style="text-align:left; vertical-align:top; height: 100px">Remarks of the Inspection Engineer (Representative of Authority Engineer)</th>
            <th class="col-md-6" colspan="3">' . htmlspecialchars($_POST['remarks_of_the_inspection']) . '</th>
        </tr>
        <tr>
            <th colspan="2">Signature of the Inspection Engineer Representative of Authority Engineer</th>
            <th colspan="3">' . htmlspecialchars($_POST['signature_of_the_inspection']) . '</th>
        </tr>
        <tr>
            <th colspan="2" style="text-align:right;">Date :</th>
            <th colspan="3">' . htmlspecialchars($_POST['date']) . '</th>
        </tr>
        <tr>
            <th colspan="2" style="text-align:right;">Name :</th>
            <th colspan="3">' . htmlspecialchars($_POST['name']) . '</th>
        </tr>
        <tr>
            <th colspan="2" style="text-align:right;">Designation :</th>
            <th colspan="3">' . htmlspecialchars($_POST['designation']) . '</th>
        </tr>
    </table>

    <div class="col-md-14">
        <div class="container">
            <div class="card-box">
                <div id="dynamicForm">
                    <div class="row" id="formRows">';

                        $queryyy = "SELECT * FROM tbl_railway_attachment_master WHERE requested_id='" . $railway_details['id'] . "'";
                        $resultt = mysqli_query($db_con, $queryyy);

                        // Check if the query was successful
                        if ($resultt) {
                            while ($rowwe = mysqli_fetch_assoc($resultt)) {
                                $htmlContent .= '
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="mobile">Remark:</label>
                                ' . htmlspecialchars($rowwe['remark']) . '
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Upload Attachment</label>
                                <a href="uploads/' . htmlspecialchars($rowwe['attachment']) . '" download>
                                    ' . htmlspecialchars($rowwe['attachment']) . '
                                </a>
                            </div>
                        </div>
                    </div>';
                            }
                        }

                        $htmlContent .= '
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>';



                        // exportPDF($htmlContent, $path);
                        exportPDF($htmlContent, $path);
                        $id = $sl_id . '_' . $docId . '_' . $wfid;

                        $destinationPath = str_replace(" ", "", $workFlowName) . '/' . $pdfName;
                        $sourcePath = $path;
                        uploadFileInFtpServer($fileserver, $port, $ftpUser, $ftpPwd, $destinationPath, $sourcePath);
                        $newdocname = base64_encode($docId);
                        //create thumbnail
                        $uploadedfilename = $sourcePath;
                        if (CREATE_THUMBNAIL) {
                            changePdfToImage($uploadedfilename, $newdocname);
                        }
                        // }
                    }



                    //create pdf from form
                    if ($rwWfd['form_req'] == 1 || $rwWfd['form_req'] == 2) {
                        include 'exportpdf.php';
                        // print_r($rwSlperm);

                        if ((isset($_POST['taskRemark'])) && (!empty(trim($_POST['taskRemark'])))) { //if content of CKEditor ISN'T empty
                            $posted_editor = trim($_POST['taskRemark']); //get content of CKEditor

                            $docName = mysqli_query($db_con, "select sl_id,sl_name from tbl_storage_level where sl_id = '$sl_id'"); // or die('Eror:' . mysqli_error($db_con));
                            $rwdocName = mysqli_fetch_assoc($docName);
                            $folderName = str_replace(" ", "", $workFlowName);
                            $pdfName = trim($workFlowName) . "_" . mktime() . ".pdf"; //specify the file save location and the file name
                            $path = 'extract-here/' . str_replace(" ", "", $workFlowName);
                            if (!is_dir($path)) {
                                mkdir($path, 0777, true);
                            }
                            $path = $path . '/' . $pdfName;
                            $wrkflowFsize = filesize($path);
                            $wrkflowFsize = round(($wrkflowFsize / 1024), 2);
                            $doc_name = $sl_id . '_' . $wfid;
                            $pagecount = count_pages($path);
                            $wrkflowDoc = mysqli_query($db_con, "INSERT INTO tbl_document_master (doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages, dateposted, workflow_id) VALUES ('$doc_name', '$pdfName', 'pdf', '$folderName/$pdfName', '$user_id', '$wrkflowFsize', '$pagecount', '$date', 'wfid')"); // or die('Eror:' . mysqli_error($db_con));
                            $docId = mysqli_insert_id($db_con);
                            exportPDF($posted_editor, $path);
                            $id = $sl_id . '_' . $docId . '_' . $wfid;


                            $newdocname = base64_encode($docId);

                            //create thumbnail
                            $uploadedfilename = $path;

                            changePdfToImage($uploadedfilename, $newdocname);


                            $destinationPath = str_replace(" ", "", $workFlowName) . '/' . $pdfName;
                            $sourcePath = $path;
                            uploadFileInFtpServer($destinationPath, $sourcePath);
                        }
                    }
                    // print_r($rwSlperm);
                    // die('cde');

                    //end create pdf
                    //upload files if any
                    $files = $_FILES['fileName']['name'];
                    //if (strlen($files) < 50) {
                    if (!empty($files)) {
                        for ($i = 0; $i < count($files); $i++) {
                            $file_name = $_FILES['fileName']['name'][$i];
                            $file_size = $_FILES['fileName']['size'][$i];
                            $file_type = $_FILES['fileName']['type'][$i];
                            $file_tmp = $_FILES['fileName']['tmp_name'][$i];
                            if (!empty($file_name)) {
                                $pageCount = $_POST['pageCount'];
                                $fname = substr($file_name, 0, strrpos($file_name, '.'));
                                $encryptName = urlencode(base64_encode($fname));
                                $fileExtn = substr($file_name, strrpos($file_name, '.') + 1);
                                $folder = str_replace(" ", "", $workFlowName);
                                $image_path = 'extract-here/' . $folder . '/';

                                if (!dir($image_path)) {
                                    mkdir($image_path, 0777, TRUE); // or die("Error local folder:". print_r(error_get_last()));
                                }
                                $file_name = time() . '_' . $file_name;
                                $image_path = $image_path . $file_name;

                                $upload = move_uploaded_file($file_tmp, $image_path) or die(print_r(error_get_last()));

                                if ($upload) {

                                    $destinationPath = $folder . '/' . $file_name;

                                    $sourcePath = $image_path;
                                    uploadFileInFtpServer($destinationPath, $sourcePath);

                                    $query = "INSERT INTO tbl_document_master(doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages, dateposted, workflow_id) VALUES ('$id', '$fname', '$fileExtn', '$folder/$file_name', '$user_id', '$file_size', '$pageCount', '$date', '$wfid')";
                                    $exe = mysqli_query($db_con, $query); // or die('Error query failed' . mysqli_error($db_con));

                                    $docId2 = mysqli_insert_id($db_con);

                                    // Decrypt file
                                    decrypt_my_file($image_path);

                                    $newdocname = base64_encode($docId2);

                                    //create thumbnail
                                    $uploadedfilename = $image_path;

                                    if ($fileExtn == 'jpg' || $fileExtn == 'jpeg' || $fileExtn == 'png') {
                                        createThumbnail2($uploadedfilename, $newdocname);
                                    } elseif ($fileExtn == 'pdf') {
                                        changePdfToImage($uploadedfilename, $newdocname);
                                    }

                                    if (empty($docId)) {


                                        $docId = $docId2;
                                        $id = $sl_id . '_' . $docId2 . '_' . $wfid;
                                    }
                                }
                            }
                        }
                    }

                    function stringSerialNumber($last_id)
                    {

                        if ($last_id > 1 && $last_id < 9) {
                            $serial_numbers = "CON000" . $last_id;
                        }
                        if ($last_id > 10 && $last_id < 99) {
                            $serial_numbers = "CON00" . $last_id;
                        }
                        if ($last_id > 100 && $last_id < 999) {
                            $serial_numbers = "CON0" . $last_id;
                        }
                        if ($last_id > 1000 && $last_id < 9999) {
                            $serial_numbers = "CON" . $last_id;
                        }

                        return $serial_numbers;
                    }
                    //end upload file

                    $insertInTask = mysqli_query($db_con, "INSERT INTO tbl_doc_assigned_wf(task_id, doc_id, start_date, end_date, task_status, assign_by, task_remarks,ticket_id) VALUES ('$tskId','$docId',  '$date', '$endDate', 'Pending', '$user_id', '$taskRemark','$ticket')"); // or die('Erorr: hh1' . mysqli_error($db_con));
                    $idins = mysqli_insert_id($db_con);
                    $created_at = date('Y-m-d H:i:s');

                    mysqli_query($db_con, "INSERT INTO tbl_railway_master(
                    rfi_no, 
                    rfi_date, 
                    type_regular, 
                    name_of_the_contractor, 
                    item_no_as_per, 
                    inspection_required_date, 
                    location_from, 
                    location_to, 
                    name_of_the_inspecting_engineer, 
                    inspected_on_date, 
                    c_and_g, 
                    earthwork, 
                    blanketing, 
                    survey, 
                    safety, 
                    qc_material, 
                    shuttering_reinforcement, 
                    concreting, 
                    drain_retaining_wall, 
                    roads, 
                    utilities, 
                    dismantling_of_Pway, 
                    bridge_work, 
                    other, 
                    description_of_work, 
                    enclosures_attached, 
                    signature_of_the_contractor, 
                    remarks_of_the_inspection, 
                    approved, 
                    not_approved, 
                    signature_of_the_inspection, 
                    date, 
                    name, 
                    designation, 
                    ticket_id,
                    task_remark,
                    created_by,
                    railway_type
                ) VALUES (
                    '" . $_POST['rfi_no'] . "', 
                    '" . $_POST['rfi_date'] . "', 
                    '" . $_POST['type_regular'] . "', 
                    '" . $_POST['name_of_the_contractor'] . "', 
                    '" . $_POST['item_no_as_per'] . "', 
                    '" . $_POST['inspection_required_date'] . "', 
                    '" . $_POST['location_from'] . "', 
                    '" . $_POST['location_to'] . "', 
                    '" . $_POST['name_of_the_inspecting_engineer'] . "', 
                    '" . $_POST['inspected_on_date'] . "', 
                    " . (isset($_POST['c_and_g']) ? 1 : 0) . ", 
                    " . (isset($_POST['earthwork']) ? 1 : 0) . ", 
                    " . (isset($_POST['blanketing']) ? 1 : 0) . ", 
                    " . (isset($_POST['survey']) ? 1 : 0) . ", 
                    " . (isset($_POST['safety']) ? 1 : 0) . ", 
                    " . (isset($_POST['qc_material']) ? 1 : 0) . ", 
                    " . (isset($_POST['shuttering_reinforcement']) ? 1 : 0) . ", 
                    " . (isset($_POST['concreting']) ? 1 : 0) . ", 
                    " . (isset($_POST['drain_retaining_wall']) ? 1 : 0) . ", 
                    " . (isset($_POST['roads']) ? 1 : 0) . ", 
                    " . (isset($_POST['utilities']) ? 1 : 0) . ", 
                    " . (isset($_POST['dismantling_of_Pway']) ? 1 : 0) . ", 
                    " . (isset($_POST['Bridge_Work']) ? 1 : 0) . ", 
                    " . (isset($_POST['other']) ? 1 : 0) . ", 
                    '" . $_POST['description_of_work'] . "', 
                    '" . $_POST['enclosures_attached'] . "', 
                    '" . $_POST['signature_of_the_contractor'] . "', 
                    '" . $_POST['remarks_of_the_inspection'] . "', 
                    " . (isset($_POST['approved']) ? 1 : 0) . ", 
                    " . (isset($_POST['not_approved']) ? 1 : 0) . ", 
                    '" . $_POST['signature_of_the_inspection'] . "', 
                    '" . $_POST['date'] . "', 
                    '" . $_POST['name'] . "', 
                    '" . $_POST['designation'] . "', 
                    '$ticket','" . $_POST['taskRemark'] . "','$user_id',
                    '" . $_POST['railway_type'] . "'
                )");
                    $last_id_here = mysqli_insert_id($db_con);
                    $string_serial_number = stringSerialNumber($last_id_here);
                    mysqli_query($db_con, "update tbl_railway_master Set string_serial_number='$string_serial_numwhere id='$last_id_here'");


                    if (isset($_FILES['file'])) {
                        $files = $_FILES['file'];
                        $remarks = $_POST['remark'];

                        for ($i = 0; $i < count($files['name']); $i++) {
                            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                                $tmpName = $files['tmp_name'][$i];
                                $name = basename($files['name'][$i]);
                                $uploadDir = 'uploads/'; // Ensure this directory exists and is writable
                                $uploadFile = $uploadDir . $name;

                                if (move_uploaded_file($tmpName, $uploadFile)) {
                                    // Insert into tbl_railway_attachment_master
                                    mysqli_query($db_con, "INSERT INTO tbl_railway_attachment_master (requested_id, remark, attachment, created_at, created_by, ticket_id) VALUES ('$last_id_here', '" . mysqli_real_escape_string($db_con, $remarks[$i] ?? '') . "', '" . mysqli_real_escape_string($db_con, $name) . "', '$created_at', '$user_id', '$ticket')") or die(mysqli_error($db_con));
                                }
                            }
                        }
                    }


                    $getTask = mysqli_query($db_con, "select * from tbl_task_master where task_id = '$tskId'"); // or die('Error:' . mysqli_error($db_con));
                    $rwgetTask = mysqli_fetch_assoc($getTask);
                    $TskStpId = $rwgetTask['step_id'];
                    $TskWfId = $rwgetTask['workflow_id'];
                    $TskOrd = $rwgetTask['task_order'];
                    $TskAsinToId = $rwgetTask['assign_user'];
                    $nextTaskOrd = $TskOrd + 1;
                    //for export pdf

                    nextTaskAsin($nextTaskOrd, $TskWfId, $TskStpId, $docId, $date, $user_id, $db_con, $taskRemark, $ticket);

                    if ($insertInTask) {

                        require_once './mail.php';

                        if (MAIL_BY_SOCKET) {

                            $paramsArray = array(
                                'ticket' => $ticket,
                                'idins' => $idins,
                                'db_con' => $db_con,
                                'projectName' => $projectName,
                                'action' => 'assignTask'
                            );
                            mailBySocket($paramsArray);
                        } else {

                            $mail = assignTask($ticket, $idins, $db_con, $projectName);
                        }

                        // if ($mail) {
                        mysqli_autocommit($db_con, true);

                        //send sms to mob
                        //                        $getMobNum = mysqli_query($db_con, "select * from tbl_user_master where user_id = '$user_id'") or die('Error:' . mysqli_error($db_con));
                        //                        $rwgetMobNum = mysqli_fetch_assoc($getMobNum);
                        //                        $submtByMob = $rwgetMobNum['phone_no'];
                        //                        $msg = 'Your Ticket Id is: ' . $ticket . ' and Your Task is in Process.';
                        //                        $sendMsgToMbl = smsgatewaycenter_com_Send($submtByMob, $msg, $debug = false);
                        //
                        //send sms to assign user
                        //                        $getTaskAsinToMob = mysqli_query($db_con, "select * from tbl_user_master where user_id='$TskAsinToId'") or die('Error:' . mysqli_error($db_con));
                        //                        $rwgetTaskAsinToMob = mysqli_fetch_assoc($getTaskAsinToMob);
                        //                        $asinToMob = $rwgetTaskAsinToMob['phone_no'];
                        //                        $msgAsinTo = 'New Task With Ticket Id : ' . $ticket . ' has been Assigned To You.';
                        //                        $sendMsgToMbl = smsgatewaycenter_com_Send($asinToMob, $msgAsinTo, $debug = false);
                        //

                        echo '<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '", "' . $lang['Sumitd_Sucsfly'] . '");</script>';
                        //                         } else {
                        // //echo'Opps!! Mail not sent!';
                        //                             echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '", "' . $lang['Sumitd_Sucsfly'] . '")</script>';
                        //                         }
                    } else {
                        echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '", "' . $lang['Opps_Sbmsn_fld'] . '")</script>';
                    }
                    //}else {
                    //echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '", "' . $lang['file_name _too_long'] . '")</script>';
                    //}
                } else {
                    echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '", "' . $lang['Tre_is_no_tsk_in_ts_wfw'] . '")</script>';
                }
            } else {
                echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '", "' . $lang['Task_Creation_Failed_Please_Select_storage'] . '")</script>';
            }
        }
        mysqli_close($db_con);
    } else {

        echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '", "Something went wrong")</script>';
    }
}

// function count_pages($pdfname)
// {

//     $pdftext = file_get_contents($pdfname);

//     $num = preg_match_all("/\/Page\W/", $pdftext, $dummy);

//     return $num;
// }

function count_pages($pdfname)
{

    // $pdftext = file_get_contents($pdfname);

    // $num = preg_match_all("/\/Page\W/", $pdftext, $dummy);

    // return $num;

    $cmd = "pdfinfo.exe";  // Windows
    // Parse entire output
    // Surround with double quotes if file name has spaces

    exec("$cmd \"$pdfname\"", $output);
    // Iterate through lines

    $pagecount = 0;
    foreach ($output as $op) {
        // Extract the number
        if (preg_match("/Pages:\s*(\d+)/i", $op, $matches) === 1) {
            $pagecount = intval($matches[1]);
            break;
        }
    }
    return $pagecount;
}


function uploadFileInFtpServer($destinationPath, $sourcePath)
{

    /*  encrypt_my_file($sourcePath);

    $fileManager = new fileManager();
	// Connect to file server
	$fileManager->conntFileServer();
	if($fileManager->uploadFile($sourcePath, ROOT_FTP_FOLDER . '/' . $destinationPath)){
		 return true;
	}else{
		 return false;
	} */
}
?>