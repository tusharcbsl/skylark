<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    //include('sessionstart.php');
    require_once './application/config/database.php';
    require_once './application/pages/head.php';
    require_once './application/pages/function.php';
    //require_once './application/pages/sendSms.php';
    require_once 'classes/ftp.php';
	require_once './classes/fileManager.php';


    $sameGroupIDs = array();
    $group = mysqli_query($db_con, "select * from tbl_bridge_grp_to_um where find_in_set('$_SESSION[cdes_user_id]',user_ids)") or die('Error' . mysqli_error($db_con));
    while ($rwGroup = mysqli_fetch_assoc($group)) {
        $sameGroupIDs[] = $rwGroup['user_ids'];
    }
    $sameGroupIDs = array_unique($sameGroupIDs);
    sort($sameGroupIDs);
    $sameGroupIDs = implode(',', $sameGroupIDs);
   

    if ($rwgetRole['initiate_file'] != '1') {
        header('Location: ./index');
    }

    //sk@121218 : check if  file is from storage.
    $rwdc_id = base64_decode(urldecode($_GET['did'])); // doc id;
    $rwres = mysqli_fetch_assoc(mysqli_query($db_con, "select * from tbl_document_reviewer where doc_id='$rwdc_id'"));
    ?>
    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />

    <!--for searchable select-->
    <link href="assets/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" />
    <link href="assets/plugins/jstree/style.css" rel="stylesheet" type="text/css" />

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
            <?php require_once './application/pages/sidebar.php'; ?>
            <!-- Left Sidebar End -->

            <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->
            <div class="content-page">
                <!-- Start content -->
                <div class="content">
                    <div class="container" id="afterSubmt"  style="">
                        <!-- Page-Title -->
                        <div class="row">
                            <div class="col-sm-12">
                                <ol class="breadcrumb">
                                    <li>
                                        <a href="initiateFile"><?php echo $lang['Initiate_File']; ?></a>
                                    </li>
                                    <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                                </ol>
                            </div>
                        </div>
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h4 class="header-title col-lg-6"> <?php echo $lang['Initiate_File']; ?></h4>
                            </div>
                            <div class="card-box" style="height: auto;">
                                <form method="post" enctype="multipart/form-data" id="initiate_form">
                                    <div class="row">
                                        <?php
                                        if (isset($_GET['did']) && !empty($_GET['did']) && isset($_GET['ticket_id']) && !empty($_GET['ticket_id'])) {
                                            ?>
                                            <div class="row" style="margin-left: -5px;">
                                                <div class="col-md-12">
                                                    <div class="form-group m-t-20">
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <label for="userName"><?php echo $lang['SELECT_EXISTING_WORK_FLOW']; ?> :-</label>
                                                                <select class="select27" id="wfid" data-style="btn-white" style="" name="wfid">
                                                                    <option  value="0"><?php echo $lang['SELECT_EXISTING_WORK_FLOW']; ?></option>
                                                                    <?php
                                                                    $privileges = array();
                                                                    mysqli_set_charset($db_con, "utf8");
                                                                    $priv = mysqli_query($db_con, "SELECT group_id FROM tbl_bridge_grp_to_um where find_in_set('$_SESSION[cdes_user_id]',user_ids)") or die('Error' . mysqli_error($db_con));
                                                                    while ($rwPriv = mysqli_fetch_assoc($priv)) {
                                                                        array_push($privileges, $rwPriv['group_id']);
                                                                    }
                                                                    $privileges = array_filter($privileges, function($value) {
                                                                        return $value !== '';
                                                                    });
                                                                    $groups = array_unique($privileges);
                                                                    //print_r($groups);
                                                                    $wfids = array();
                                                                    foreach ($groups as $group) {
                                                                        $work = mysqli_query($db_con, "SELECT * FROM `tbl_workflow_to_group` WHERE find_in_set('$group',group_id)");

                                                                        while ($rwWork = mysqli_fetch_assoc($work)) {
                                                                            array_push($wfids, $rwWork['workflow_id']);
                                                                        }
                                                                    }
                                                                    $wfids = array_filter($wfids, function($value) {
                                                                        return $value !== '';
                                                                    });

                                                                    // print_r($wfids);

                                                                    $wfids = implode("','", $wfids);
                                                                    mysqli_set_charset($db_con, "utf8");
                                                                    $getWorkflw = mysqli_query($db_con, "select DISTINCT twm.workflow_id, twm.workflow_name from tbl_workflow_master twm inner join tbl_task_master ttm on twm.workflow_id = ttm.workflow_id where twm.workflow_id in ('$wfids')") or die('Error in getWorkflw upload:' . mysqli_error($db_con));
                                                                    //$getWorkflw = mysqli_query($db_con, "select * from tbl_workflow_master where workflow_id in('$wfids')") or die('Error in getWorkflw upload:' . mysqli_error($db_con));
                                                                    while ($rwgetWorkflw = mysqli_fetch_assoc($getWorkflw)) {
                                                                        ?> 
                                                                        <option value="<?php echo $rwgetWorkflw['workflow_id']; ?>"><?php echo $rwgetWorkflw['workflow_name']; ?></option>
                                                                    <?php } ?>
                                                                </select>

                                                            </div>

                                                        </div>
                                                    </div>
                                                    <?php if (empty($rwres['storage_doc_id'])) { ?>
                                                        <div id="hideonselWf"> <!--hide this div on select existing wf-->
                                                            <label><?php echo $lang['Or']; ?></label>
                                                            <br /><br />
                                                            <label><?php echo $lang['CrT_USRS_FLOW']; ?> </label>
                                                            <div class="form-group col-xs-12 well">

                                                                <div class="row">
                                                                    <div class="col-sm-2">
                                                                        <label for="userName"><?php echo $lang['Order']; ?><span style="color: red;">*</span></label>
                                                                        <input type="number" class="form-control" name="taskOrder[]" min="1"  placeholder="<?php echo $lang['Order']; ?>">
                                                                    </div> 
                                                                    <div class="col-sm-2">
                                                                        <label for="userName"><?php echo $lang['Assign_User']; ?><span style="color: red;">*</span></label>
                                                                        <select class="selectpicker" data-live-search="true" name="assignUsr[]" data-style="btn-white">
                                                                            <option selected disabled ><?php echo $lang['Slt_Usrs']; ?></option>
                                                                            <?php
                                                                            $user = mysqli_query($db_con, "select * from tbl_user_master where user_id in($sameGroupIDs) and active_inactive_users='1' order by first_name, last_name asc");
                                                                            while ($rwUser = mysqli_fetch_assoc($user)) {
                                                                                if ($rwUser['user_id'] != 1 && $_SESSION['cdes_user_id'] != $rwUser['user_id']) {
                                                                                    ?>
                                                                                    <option value="<?php echo $rwUser['user_id']; ?>"><?php echo $rwUser['first_name'] . ' ' . $rwUser['last_name']; ?></option>
                                                                                    <?php
                                                                                }
                                                                            }
                                                                            ?>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-sm-2">
                                                                        <label for="userName"><?php echo $lang['Alternate_User']; ?><!--<span style="color: red;">*</span>--></label>
                                                                        <select class="selectpicker" data-live-search="true" name="altrUsr[]" data-style="btn-white">
                                                                            <option selected disabled ><?php echo $lang['Slt_Usrs']; ?></option>
                                                                            <?php
                                                                            $user = mysqli_query($db_con, "select * from tbl_user_master where user_id in($sameGroupIDs) and active_inactive_users='1' order by first_name, last_name asc");
                                                                            while ($rwUser = mysqli_fetch_assoc($user)) {
                                                                                if ($rwUser['user_id'] != 1 && $_SESSION['cdes_user_id'] != $rwUser['user_id']) {
                                                                                    ?>
                                                                                    <option value="<?php echo $rwUser['user_id']; ?>"><?php echo $rwUser['first_name'] . ' ' . $rwUser['last_name']; ?></option>

                                                                                    <?php
                                                                                }
                                                                            }
                                                                            ?>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-sm-2">
                                                                        <label for="userName"><?php echo $lang['Select_Supervisor']; ?><span style="color: red;">*</span></label>
                                                                        <select class="selectpicker" data-live-search="true" name="supvsr[]">
                                                                            <option selected disabled ><?php echo $lang['Select_Supervisor']; ?></option>
                                                                            <?php
                                                                            $user = mysqli_query($db_con, "select * from tbl_user_master where user_id in($sameGroupIDs) and active_inactive_users='1' order by first_name, last_name asc");
                                                                            while ($rwUser = mysqli_fetch_assoc($user)) {
                                                                                if ($rwUser['user_id'] != 1 && $_SESSION['cdes_user_id'] != $rwUser['user_id']) {
                                                                                    ?>
                                                                                    <option <?php
                                                                                    if ($rwgetTask['supervisor'] == $rwUser['user_id']) {
                                                                                        echo 'selected';
                                                                                    }
                                                                                    ?> value="<?php echo $rwUser['user_id']; ?>"><?php echo $rwUser['first_name'] . ' ' . $rwUser['last_name']; ?></option>

                                                                                    <?php
                                                                                }
                                                                            }
                                                                            ?>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-sm-3">
                                                                        <div class="form-group row">
                                                                            <label><?= $lang['Slct_Dadlne']; ?><span style="color: red;">*</span></label>
                                                                            <table>
                                                                                <tr>
                                                                                    <td>
                                                                                        <div class="radio radio-primary">
                                                                                            <input type="radio" name="radio0" id="radio1" value="Days" checked>
                                                                                            <label for="radio1">
                                                                                                <?php echo $lang['Days']; ?> &nbsp;
                                                                                            </label>
                                                                                        </div>
                                                                                    </td>
                                                                                    <td>
                                                                                        <div class="radio radio-primary">
                                                                                            <input type="radio" name="radio0" id="radio2" value="Hrs">
                                                                                            <label for="radio2"><?php echo $lang['Hrs']; ?> &nbsp;</label>
                                                                                        </div>
                                                                                    </td>
                                                                                </tr>
                                                                            </table>
                                                                            <input type="text" class="form-control days" name="days[]" value="" id="days" style="display: none; height:35px;" placeholder="<?php echo $lang['Days']; ?>"/>
                                                                            <input type="text" class="form-control days" name="hrs[]" value="" id="hrs" style="display: none; height:35px;" placeholder="<?php echo $lang['Hrs']; ?>"/>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                                <div id="createTaskFlowr">
                                                                    <div class="form-group">
                                                                        <a href="#" id="createOwnflowr" class="btn btn-primary" style="margin-top: -64px; float: right;" data=""><i class="fa fa-plus-circle"></i></a>
                                                                    </div>
                                                                </div>
                                                            </div> 
                                                        </div><!--hide this div on select existing wf-->
                                                    <?php } ?>
                                                    <button class="btn btn-primary pull-right" type="submit" name="iniFileSub" id="waitOnSubmit"><?php echo $lang['Submit']; ?></button>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </form>
                            </div>
                            <!-- end: page -->
                            </form>
                        </div> <!-- end Panel -->
                    </div> <!-- container -->
                </div> <!-- content -->
            </div>
        </div>
    </div>

    <?php require_once './application/pages/footer.php'; ?>
    <!-- Right Sidebar -->
    <?php //require_once './application/pages/rightSidebar.php'; ?>
    <?php require_once './application/pages/footerForjs.php'; ?>
    <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
    <script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>
    <!--for searchable select -->
    <script type="text/javascript" src="assets/plugins/jquery-quicksearch/jquery.quicksearch.js"></script>
    <script src="assets/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
    <script src="assets/jscustom/wizard.js"></script>
    <!---add new-->
    <script src="assets/plugins/moment/moment.js"></script>
    <script src="assets/plugins/timepicker/bootstrap-timepicker.js"></script>
    <script src="assets/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
    <script>
                                        $('#initiate_form').submit(function () {
                                            $('#wait').show();
                                            //$('#wait').css('height',heiht);
                                            $('#afterSubmt').hide();
                                            return true;
                                        });
    </script>
    <script>
        $(function () {

            $('#wfid').change(function () {
                if ($('#wfid').val() === '0') {
                    $('#hideonselWf').show();
                    $("#days").prop("required", true);
                    $("#hrs").prop("required", true);
                } else {
                    $('#hideonselWf').hide();
                    $("#days").removeAttr("required", true);
                    $("#hrs").removeAttr("required", true);
                }
            });
        });

    </script>

    <script type="text/javascript">
        jQuery(document).ready(function () {
            $('.selectpicker').selectpicker();
            //number only in text

            $("input.days").keypress(function (e) {
                //  alert();
                //if the letter is not digit then display error and don't type anything
                if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57) && e.which != 46) {
                    //display error message
                    return false;
                } else {
                    str = $(this).val();
                    str = str.split(".").length + 1;
                    if (str > 0 && e.which == 46) {
                        return false;
                    } else {
                        return true;
                    }
                }
            });

        });
        jQuery('#timepicker').timepicker({
            defaultTIme: false
        });
        jQuery('#timepicker2').timepicker({
            showMeridian: true
        });
        jQuery('#timepicker3').timepicker({
            minuteStep: 15
        });
        //Date range picker
        $('.input-daterange-datepicker').daterangepicker({
            buttonClasses: ['btn', 'btn-sm'],
            applyClass: 'btn-default',
            cancelClass: 'btn-white'
        });
        $('.input-daterange-timepicker').daterangepicker({
            timePicker: true,
            timePickerIncrement: 1,
            locale: {
                format: 'DD-MM-YYYY h:mm A'
            },
            buttonClasses: ['btn', 'btn-sm'],
            applyClass: 'btn-default',
            cancelClass: 'btn-white'
        });
        $('.input-limit-datepicker').daterangepicker({
            format: 'MM/DD/YYYY',
            minDate: '06/01/2015',
            maxDate: '06/30/2015',
            buttonClasses: ['btn', 'btn-sm'],
            applyClass: 'btn-default',
            cancelClass: 'btn-white',
            dateLimit: {
                days: 6
            }
        });

        $('#reportrange span').html(moment().subtract(29, 'days').format('MMMM D, YYYY') + ' - ' + moment().format('MMMM D, YYYY'));

        $('#reportrange').daterangepicker({
            format: 'MM/DD/YYYY',
            startDate: moment().subtract(29, 'days'),
            endDate: moment(),
            minDate: '01/01/2012',
            maxDate: '12/31/2015',
            dateLimit: {
                days: 60
            },
            showDropdowns: true,
            showWeekNumbers: true,
            timePicker: false,
            timePickerIncrement: 1,
            timePicker12Hour: true,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            opens: 'left',
            drops: 'down',
            buttonClasses: ['btn', 'btn-sm'],
            applyClass: 'btn-default',
            cancelClass: 'btn-white',
            separator: ' to ',
            locale: {
                applyLabel: 'Submit',
                cancelLabel: 'Cancel',
                fromLabel: 'From',
                toLabel: 'To',
                customRangeLabel: 'Custom',
                daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                firstDay: 1
            }
        }, function (start, end, label) {
            console.log(start.toISOString(), end.toISOString(), label);
            $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        });


    </script>
    <!-- jQuery  -->
    <script>
        $(document).ready(function () {
            $("#days").prop("required", true);
            $("#days").show();
        });

        $("input:radio[name='radio0']").click(function () {
            var val = $(this).val();

            if (val == 'Days') {
                $("#hrs").removeAttr("required", true);
                $("#days").prop("required", true);
                $("#days").css("display", "block");
                $("#hrs").css("display", "none");
                $("#hrs").val("");
            }
            if (val == 'Hrs') {
                //$("#dateRange").css("display", "none");
                $("#hrs").prop("required", true);
                $("#days").removeAttr("required", true);
                $("#days").css("display", "none");
                $("#hrs").css("display", "block");
                $("#days").val("");
            }
        });

    </script>
    <!--for  intitate file-->
    <script>
        $("a#createOwnflowr").click(function () {
            var createown = 0;
            // alert(id);

            $.post("application/ajax/createownReviewFlow.php", {ID: createown}, function (result, status) {
                if (status == 'success') {
                    $("#createTaskFlowr").html(result);
                    // alert(result);
                }
            });
        });
        $('.select27').select2();
    </script>
</body>
</html>
<?php

function count_pages($pdfname) {

    $pdftext = file_get_contents($pdfname);

    $num = preg_match_all("/\/Page\W/", $pdftext, $dummy);

    return $num;
}

/*
 * Existing Workflow Select
 * IN CASE OF FTP
 * DOWNLOAD FILE TO TEMP AND MOVE TO ASSIGN WORKFLOW
 */
if (isset($_POST['iniFileSub'], $_POST['token'])) {
    //print_r($_GET);die;
    $user_id = $_SESSION['cdes_user_id'];
    $workflId = $_POST['wfid'];
    $doc_id = base64_decode(urldecode($_GET['did']));
    $did = $doc_id;
    $docQry = mysqli_query($db_con, "select * from `tbl_document_reviewer` where doc_id='$doc_id'");
    $docInfoFetch = mysqli_fetch_assoc($docQry);
    $dname = $docInfoFetch['old_doc_name'];
    $docDesp = json_decode($docInfoFetch['doc_desc'], TRUE);
    $subject = $docDesp['subject'];
    $taskRemark = $docDesp['docdesp'];
    $height = $docDesp['height'];
    $width = $docDesp['width'];

    //mb_internal_decoding("UTF-8");
    $reviewTicketId = urldecode(base64_decode($_GET['ticket_id']));
    // print_r(urldecode(base64_decode($_GET['ticket_id'])));die;
    /*
     * FTP
     */
    // print_r($docInfoFetch);die;
    $storage = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id='$docName'");
    $rwStor = mysqli_fetch_assoc($storage);
    $folderName = "temp";
    if (!dir($folderName)) {
        mkdir($folderName, 0777, TRUE);
    }
    $folderName = $folderName . '/' . $_SESSION['cdes_user_id'];
    if (!dir($folderName)) {
        mkdir($folderName, 0777, TRUE);
    }
    $folderName = $folderName . '/' . preg_replace('/[^A-Za-z0-9\-_]/', '', $rwStor['sl_name']); //preg_replace('/[^A-Za-z0-9\-]/', '', $string);
    if (!dir($folderName)) {
        mkdir($folderName, 0777, TRUE);
    }
    $ftp = new ftp();
    /*
     * Message
     * IF WORKFLOW ALREADY EXIST
     */
    if (!empty($workflId) && $workflId != '0') {
        $reviewWF = addReviewDocWF($user_id, $workflId, $reviewTicketId, $date, $db_con, $height, $width, $folderName, $projectName, $did, $ftp, $lang);
        if ($reviewWF['status']) {
            // Commit transaction
            mysqli_commit($reviewWF['db_con']);

            // Close connection
            mysqli_close($reviewWF['db_con']);

            echo '<script>taskSuccess("sentreview", "' . $reviewWF['msg'] . '")</script>';
        } else {
            echo '<script>taskFailed("' . basename($_SERVER[REQUEST_URI]) . '", "' . $reviewWF['msg'] . '")</script>';
        }
    }
    if (empty($workflId)) {
        $reviewWF = wfNotExist($user_id, $subject, $reviewTicketId, $date, $db_con, $height, $width, $folderName, $projectName, $did, $ftp, $lang);
        if ($reviewWF['status']) {
            // Commit transaction
            mysqli_commit($reviewWF['db_con']);

            // Close connection
            mysqli_close($reviewWF['db_con']);
            echo '<script>taskSuccess("sentreview", "' . $reviewWF['msg'] . '")</script>';
        } else {
            echo '<script>taskFailed("' . basename($_SERVER[REQUEST_URI]) . '", "' . $reviewWF['msg'] . '")</script>';
        }
    }
}

/*
 * ADD DOC IF WORKFLOW IS ALREDAY EXIST
 */

function addReviewDocWF($user_id, $workflId, $reviewTicketId, $date, $db_con, $height, $width, $folderName, $projectName, $did, $ftp, $lang) {
	
	$fileManager = new fileManager();
	// Connect to file server
	$fileManager->conntFileServer();
	
    // Set autocommit to off because bulk queries run
    mysqli_autocommit($db_con, FALSE);
    $tempFileRemove = array();
    $exportStaticPath = "extract-here/";
    $pathWfFolder;

    try {
        $wrkFlwName = mysqli_query($db_con, "select * from tbl_workflow_master where workflow_id = '$workflId'");
        if (mysqli_num_rows($wrkFlwName) > 0) {
            $rwwrkFlwName = mysqli_fetch_assoc($wrkFlwName);
            $workflowName = $rwwrkFlwName['workflow_name'];
            /*
             * Fetch Main doc Id using ticket Id from tbl doc review
             */
            $fetchMainDocID = mysqli_query($db_con, "select * from tbl_doc_review where ticket_id='$reviewTicketId'");
            if (mysqli_num_rows($fetchMainDocID) > 0) {
                $fetchReviewData = mysqli_fetch_assoc($fetchMainDocID);
                $mainDocID = $fetchReviewData['doc_id'];
                /*
                 * Fetch The storage assaign to main doc id
                 * 
                 */

                $fetchMainDocStrg = mysqli_query($db_con, "select * from tbl_document_reviewer where doc_id='$mainDocID'");
                if (mysqli_num_rows($fetchMainDocStrg) > 0) {
                    $mainDocStrg = mysqli_fetch_assoc($fetchMainDocStrg);
                    $docName = $mainDocStrg['doc_name'];
                    $oldDOCNAME = $mainDocStrg['old_doc_name'];
                    $docExtn = $mainDocStrg['doc_extn'];
                    $docPath = $mainDocStrg['doc_path'];
                    $uploadBy = $mainDocStrg['uploaded_by'];
                    $docSize = $mainDocStrg['doc_size'];
                    $noPages = $mainDocStrg['noofpages'];
                    $datePosted = $mainDocStrg['dateposted'];
                    $fileNumber = $mainDocStrg['File_Number'];
                    $supportDoc = $docName . "_" . $mainDocID;

                    if (!empty($docName)) {
                        $id = $docName . '_' . $workflId;
                        $workFlowArray = explode(" ", $workflowName);
                        $ticket = '';
                        for ($w = 0; $w < count($workFlowArray); $w++) {
                            $name = $workFlowArray[$w];
                            $ticket = $ticket . substr($name, 0, 1);
                        }
                        $ticket = $ticket . '_' . $user_id . '_' . strtotime($date);
                        /*
                         * Convert It into pdf
                         */
                        if (FTP_ENABLED) {
                            if (empty($mainDocStrg['storage_doc_id'])) {
                                $localPath = $folderName . '/' . preg_replace('/[^A-Za-z0-9\-_]/', '', $oldDOCNAME) . '.' . "html";
                            } else {
                                $localPath = $folderName . '/' . preg_replace('/[^A-Za-z0-9\-_]/', '', $oldDOCNAME) . '.' . pathinfo($docPath, PATHINFO_EXTENSION);
                            }
							$server_path = ROOT_FTP_FOLDER . '/' . $docPath;
							$fileManager->downloadFile($server_path, $localPath);
							
							
                            /* $ftp->conn("$fileserver", "$port", "$ftpUser", "$ftpPwd");
                            
                            $ftp->get($localPath, $server_path);  */
                        } else {
                            $localPath = 'extract-here/' . $docPath;
                        }
                        
                        decrypt_my_file($localPath);
                        //@sk

                        if (empty($mainDocStrg['storage_doc_id'])) {
                            $nfilename = preg_replace('/[^A-Za-z0-9_\-]/', '', $oldDOCNAME);
                            // $filenameEnct=$fname.'.'.$extn;// urlencode(base64_encode($fname)).'.'.$extn;
                            $filenameEnct = urlencode(base64_encode($nfilename));
                            $filenameEnct = preg_replace('/[^A-Za-z0-9_\-]/', '', $filenameEnct);
                            $filenameEnct = time() . $filenameEnct;
                            $pdfName = $filenameEnct . ".pdf"; //specify the file save location and the file name
                            $pathWfFolder = str_replace(" ", "", $workflowName);
                            $exportedPath = $exportStaticPath . $pathWfFolder;
                            if (!is_dir($exportedPath)) {
                                mkdir($exportedPath, 0777, true);
                            }
                            include 'exportpdf.php';
                            $posted_editor = file_get_contents($localPath);
                            $exportedPath = $exportedPath . "/" . $pdfName; //export path of extract here
                            $dbPath = $pathWfFolder . "/" . $pdfName; // path goes to db
                            exportPDFSize($posted_editor, $exportedPath, $height, $width);
                            $wrkflowFsize = filesize($exportedPath);
                            $pagecount = count_pages($exportedPath);

                            if (uploadFileInFtpServer($dbPath, $exportedPath)) {
                                $tempFileRemove[] = $server_path; //$ftp->singleFileDelete($server_path);//delete file from old storage after upload in workflow
                            } else {
                                return array("status" => FALSE, "msg" => "Failed To Upload File", "dev_msg" => "");
                            }
                        } else {
                            $wrkflowFsize = filesize($localPath);
                            $pagecount = count_pages($localPath);
                        }

                        /*
                         * Insert First doc id with workflow id
                         */


                        $doc_name = $docName . '_' . $workflId;

                        //@sk241018: make filename standard for workflow;
                        $oldDOCNAME = explode('_', $oldDOCNAME);
                        $oldDOCNAME = $oldDOCNAME[0];

                        // check whether file is from storage or not.
                        if (!empty($mainDocStrg['storage_doc_id'])) {
                            $dc_ext = strtolower(pathinfo($localPath, PATHINFO_EXTENSION));
                            if ($dc_ext == 'html') {
                                $dc_ext = 'docx';
                                $docPathNew = substr($docPath, 0, strrpos($docPath, '.'));
                                $docPath = $docPathNew . '.' . $dc_ext;
                                $new_name = basename($localPath, ".html") . '.' . $dc_ext;
                                $newPath = substr($localPath, 0, strrpos($localPath, '.')) . '.' . $dc_ext;

                                var_dump(rename($localPath, substr($localPath, 0, strrpos($localPath, '.')) . '.' . $dc_ext));
                                //$lp=substr($localPath, 0, strrpos($localPath, '/'));
                                //copy($localPath,$lp.'/'.$new_name);
                                //echo $newPath;
                                //die;
                                if (FTP_ENABLED) {
                                    if (uploadFileInFtpServer($docPath, $newPath)) {
                                        $tempFileRemove[] = $server_path; //delete file from old storage after upload in workflow
                                    } else {
                                        return array("status" => FALSE, "msg" => "Failed To Upload File", "dev_msg" => "");
                                    }
                                }
                            }

                            $wrkflowDoc = mysqli_query($db_con, "INSERT INTO tbl_document_master (doc_id,doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages, dateposted,File_Number, workflow_id) VALUES ('$mainDocStrg[storage_doc_id]','$doc_name', '$mainDocStrg[storage_doc_name]', '$dc_ext', '$docPath', '$uploadBy', '$wrkflowFsize', '$pagecount', '$datePosted','$fileNumber', '$workflId')") or die(mysqli_error($db_con));
                            $newdocId = mysqli_insert_id($db_con);
                            $newdocname = base64_encode($newdocId);
                         
                            if($dc_ext=='jpg' || $dc_ext=='jpeg' || $dc_ext=='png'){
                                createThumbnail2($localPath,$newdocname);
                            }elseif($dc_ext=='pdf'){
                                changePdfToImage($localPath,$newdocname);
                            }
                            
                        } else {
                            
                            $wrkflowDoc = mysqli_query($db_con, "INSERT INTO tbl_document_master (doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages, dateposted,File_Number, workflow_id) VALUES ('$doc_name', '$oldDOCNAME', 'pdf', '$dbPath', '$uploadBy', '$wrkflowFsize', '$pagecount', '$datePosted','$fileNumber', '$workflId')") or die(mysqli_error($db_con));
                            $newdocId = mysqli_insert_id($db_con);
                            $newdocname = base64_encode($newdocId);
                            changePdfToImage($exportedPath,$newdocname);
                        }
                        if ($wrkflowDoc) {
                            $docId = mysqli_insert_id($db_con);
                            $host = $_SERVER['REMOTE_ADDR'] . '/' . $_SESSION['custom_ip'];
                            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`,`doc_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,$docName,$docId,'Document Upload in $workflowName workflow','$date','$date','$host',null)");
                            if ($log) {
                                /*
                                 * Fetch ALL REALATED DOCUMENT FROM DOC REVIEW MASTER
                                 */
                                $docAllReview = mysqli_query($db_con, "select * from `tbl_document_reviewer` where doc_name='$supportDoc'");
                                if (mysqli_num_rows($docAllReview) > 0) {
                                    while ($rows = mysqli_fetch_assoc($docAllReview)) {

                                        $oldDoc = explode("_", $rows['doc_name']);
                                        $sl_doc_id = $oldDoc[0] . '_' . $docId;
                                        $id = $oldDoc[0] . '_' . $docId . '_' . $workflId;
                                        $soldDOCNAME = $rows['old_doc_name'];
                                        $sdocExtn = $rows['doc_extn'];
                                        $sdocPath = $rows['doc_path'];
                                        $suploadBy = $rows['uploaded_by'];
                                        $sdocSize = $rows['doc_size'];
                                        $snoPages = $rows['noofpages'];
                                        $sdatePosted = $rows['dateposted'];
                                        $sfileNumber = $rows['File_Number'];
                                        $info = new SplFileInfo($sdocPath); //get file extension from file
                                        $fileExten = $info->getExtension(); //orginal extension of file
                                        /*
                                         * IF FILE EXTENSION IS HTML AND VERSION OF HTML FILE ONLY
                                         */


                                        if ($fileExten == "html") {
                                            /* if(FTP_ENABLED){
                                              $localPath = $folderName . '/' . preg_replace('/[^A-Za-z0-9\-_]/', '', $soldDOCNAME) . '.' . "html";
                                              $server_path = ROOT_FTP_FOLDER . '/' . $sdocPath;
                                              $ftp->get($localPath, $server_path); // download live "$server_path"  to local "$localpath"
                                              } else {
                                              $localPath = 'extract-here/'.$sdocPath;
                                              }
                                              $nfilename = preg_replace('/[^A-Za-z0-9_\-]/', '', $soldDOCNAME); */
                                            // $filenameEnct=$fname.'.'.$extn;// urlencode(base64_encode($fname)).'.'.$extn;
                                            /* $filenameEnct = urlencode(base64_encode($nfilename));
                                              $filenameEnct = preg_replace('/[^A-Za-z0-9_\-]/', '', $filenameEnct);
                                              $filenameEnct = time() . $filenameEnct;
                                              $pdfName = $filenameEnct . ".pdf"; //specify the file save location and the file name
                                              $posted_editor1 = file_get_contents($localPath);

                                              $exportedPath = $exportStaticPath . $pathWfFolder . "/" . $pdfName; //export path of extract here
                                              exportPDFSize($posted_editor1, $exportedPath, $height, $width); */
                                            /*
                                             * Remove Extract Here
                                             */
                                            /* $dbPath = $pathWfFolder . "/" . $pdfName; */

                                            /*
                                             * Remove complete
                                             */

                                            /*
                                             * Remove File From old Place after upload in workflow
                                             */

                                            /* $wrkflowFsize = filesize($exportedPath);
                                              $pagecount = count_pages($exportedPath); */
                                            //    $ftp->singleFileDelete($server_path);
                                            //$sDoc = mysqli_query($db_con, "INSERT INTO tbl_document_master (doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages, dateposted,File_Number) VALUES ('$id', '$soldDOCNAME', 'pdf', '$dbPath', '$suploadBy', '$wrkflowFsize', '$pagecount', '$sdatePosted','$sfileNumber')");
                                        } else {

                                            if (FTP_ENABLED) {
                                                $localPath = $folderName . '/' . end(explode("/", $sdocPath));
                                                $server_path = ROOT_FTP_FOLDER . '/' . $sdocPath;
												
												$fileManager->downloadFile($server_path, $localPath);
                                                //$ftp->get($localPath, $server_path); // download live "$server_path"  to local "$localpath"
                                            } else {
                                                $localPath = 'extract-here/' . $sdocPath;
                                            }
                                            
                                            
                                            if (empty($mainDocStrg['storage_doc_id'])) {
                                                $path = $exportStaticPath . str_replace(" ", "", $workflowName);
                                                //$copyExtn = $path . "/" . $soldDOCNAME . "." . $sdocExtn;
                                                $copyExtn = $path . "/" . end(explode("/", $sdocPath));

                                                if (!copy($localPath, $copyExtn)) {
                                                    return array("status" => FALSE, "msg" => "Failed To Move Document In Workflow", "dev_msg" => "");
                                                } else {
                                                    $dbPath = explode("/", $copyExtn);
                                                    unset($dbPath[0]);
                                                    $dbPath = implode("/", $dbPath);

                                                    if (uploadFileInFtpServer($dbPath, $copyExtn)) {
                                                        $tempFileRemove[] = $server_path; //delete file from old storage after upload in workflow
                                                    } else {
                                                        return array("status" => FALSE, "msg" => "Failed To Upload File", "dev_msg" => "");
                                                    }
                                                    $wrkflowFsize = filesize($copyExtn);
                                                    $pagecount = count_pages($copyExtn);
                                                    // $ftp->singleFileDelete($server_path);


                                                    $sDoc = mysqli_query($db_con, "INSERT INTO tbl_document_master (doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages, dateposted,File_Number, workflow_id) VALUES ('$id', '$soldDOCNAME', '$sdocExtn', '$dbPath', '$suploadBy', '$wrkflowFsize', '$pagecount', '$sdatePosted','$sfileNumber', '$workflId')");
                                                    $nDOcID = mysqli_insert_id($db_con);
                                                    if ($sDoc) {
                                                        
                                                        
                                                        $newdocname = base64_encode($nDOcID);
                                                        
                                                        if($sdocExtn=='jpg' || $sdocExtn=='jpeg' || $sdocExtn=='png'){
                                                            createThumbnail2($copyExtn,$newdocname);
                                                        }elseif($sdocExtn=='pdf'){
                                                            changePdfToImage($copyExtn,$newdocname);
                                                        }
                                                        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`,`doc_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'0','$nDOcID','Document Upload in $workflowName workflow','$date','$date','$host',null)")or die(mysqli_error($db_con));
                                                       
                                                        if ($log) {
                                                            
                                                        } else {
                                                            return array("status" => FALSE, "msg" => "Failed To Generate Log2", "dev_msg" => "");
                                                        }
                                                    } else {
                                                        return array("status" => FALSE, "msg" => "Failed To Upload Supporting Document In Workflow", "dev_msg" => "");
                                                    }
                                                }
                                            }
                                        }
                                    }

                                    $getFirstTask = mysqli_query($db_con, "select * from tbl_task_master where workflow_id = '$workflId' ORDER BY task_order ASC LIMIT 1");
                                   
                                    if (mysqli_num_rows($getFirstTask) > 0) {
                                        
                                        $rwgetTask = mysqli_fetch_assoc($getFirstTask);
                                        $wTaskId = $rwgetTask[task_id];

                                        $getTaskDl = mysqli_query($db_con, "select * from tbl_task_master where task_id='$wTaskId'");
                                        
                                        $rwgetTaskDl = mysqli_fetch_assoc($getTaskDl);

                                        if ($rwgetTaskDl['deadline_type'] == 'Date' || $rwgetTaskDl['deadline_type'] == 'Hrs') {

                                            $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 60));
                                        }
                                        if ($rwgetTaskDl['deadline_type'] == 'Days') {

                                            $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 24 * 60 * 60));
                                        }

                                        $insertInTask = mysqli_query($db_con, "INSERT INTO tbl_doc_assigned_wf(task_id, doc_id, start_date, end_date, task_remarks, task_status, assign_by, ticket_id) VALUES ('$wTaskId', '$docId', '$date', '$endDate', '$taskRemark', 'Pending', '$user_id', '$ticket')");
                                        
                                        if ($insertInTask) {
                                            
                                            $idins = mysqli_insert_id($db_con);
                                            
                                            $getTask = mysqli_query($db_con, "select * from tbl_task_master where task_id = '$wTaskId'");
                                            if (mysqli_num_rows($getTask) > 0) {
                                                $rwgetTask = mysqli_fetch_assoc($getTask);
                                                $TskStpId = $rwgetTask['step_id'];
                                                $TskWfId = $rwgetTask['workflow_id'];
                                                $TskOrd = $rwgetTask['task_order'];
                                                $TskAsinToId = $rwgetTask['assign_user'];
                                                $nextTaskOrd = $TskOrd + 1;

                                                nextTaskAsin($nextTaskOrd, $TskWfId, $TskStpId, $docId, $date, $user_id, $db_con, $taskRemark, $ticket);
                                                require_once './mail.php';
                                                $mail = assignTask($ticket, $idins, $db_con, $projectName);
                                                if ($mail) {
                                                    if ($dc_ext == 'pdf') {
                                                        $update_annot = mysqli_query($db_con, "update tbl_anotation set doc_id='$docId',is_inreview='0' where doc_id='$did' and is_inreview='1'");
                                                    }
                                                    $updateReview = mysqli_query($db_con, "update `tbl_reviews_log` set doc_id='$docId',in_review='1' where doc_id=$did");
                                                    if ($updateReview) {
                                                        $delDocReview = mysqli_query($db_con, "Delete from tbl_document_reviewer where doc_id='$did'");
                                                        if ($delDocReview) {
                                                            $delDocReviewSupport = mysqli_query($db_con, "Delete from `tbl_document_reviewer` where doc_name='$supportDoc'");
                                                            if ($delDocReviewSupport) {
                                                                $delTicketID = mysqli_query($db_con, "Delete from `tbl_doc_review` where ticket_id='$reviewTicketId'");
                                                                if ($delTicketID) {
                                                                    /*
                                                                     * REmove all temp files FTP server after move it to workflow folder
                                                                     */

                                                                    foreach ($tempFileRemove as $key => $serverValues) {
                                                                        if (!empty($serverValues)) {
																			
																			$fileManager->deleteFile($serverValues);
                                                                            //$ftp->singleFileDelete($serverValues);
                                                                        }
                                                                    }

                                                                    return array("status" => TRUE, "msg" => "$lang[Process_Send_Tray]", "dev_msg" => "", "db_con" => $db_con);
                                                                } else {
                                                                    return array("status" => FALSE, "msg" => "Failed", "dev_msg" => "$lang[support_from_doc_review]");
                                                                }
                                                            } else {
                                                                return array("status" => FALSE, "msg" => "Failed", "dev_msg" => "$lang[support_from_doc_review]");
                                                            }
                                                        } else {
                                                            return array("status" => FALSE, "msg" => "Failed", "dev_msg" => "$lang[failed_delete_document_review]");
                                                        }
                                                    } else {
                                                        return array("status" => FALSE, "msg" => "$lang[log_not_update]", "dev_msg" => "");
                                                    }
                                                } else {

                                                    return array("status" => FALSE, "msg" => "$lang[Ops_Ml_nt_snt]", "dev_msg" => "");
                                                }
                                            } else {
                                                return array("status" => FALSE, "msg" => "$lang[Failed_Next_Task_Assign]", "dev_msg" => "");
                                            }
                                        } else {
                                            return array("status" => FALSE, "msg" => "$lang[Failed_Assigned_Task]", "dev_msg" => "");
                                        }
                                    } else {
                                        return array("status" => FALSE, "msg" => "$lang[Tre_is_no_tsk_in_ts_wfw]", "dev_msg" => "");
                                    }
                                }
                            } else {
                                return array("status" => FALSE, "msg" => "$lang[Failed_Create_Log]", "dev_msg" => "");
                            }
                        } else {
                            return array("status" => FALSE, "msg" => "$lang[Failed_Upload_Document_Workflow]", "dev_msg" => "");
                        }
                    } else {
                        return array("status" => FALSE, "msg" => "$lang[Invalid_Storage_Select]");
                    }
                } else {
                    return array("status" => FALSE, "msg" => "$lang[Document_Not_Available]");
                }
            } else {
                return array("status" => FALSE, "msg" => "$lang[Invalid_Review_Ticket_ID]");
            }
        } else {
            return array("status" => FALSE, "msg" => "$lang[Invalid_Workflow]");
        }
    } catch (Exception $exc) {
        echo $exc->getTraceAsString();
    }
}

/*
 * Workflow not exist
 */

function wfNotExist($user_id, $subject, $reviewTicketId, $date, $db_con, $height, $width, $folderName, $projectName, $did, $ftp, $lang) {
    $fileManager = new fileManager();
	// Connect to file server
	$fileManager->conntFileServer();
	
	// Set autocommit to off because bulk queries run
    mysqli_autocommit($db_con, FALSE);
    $tempFileRemove = array();
    $exportStaticPath = "extract-here/";
    $pathWfFolder;

    try {
        if (!empty($subject)) {
            //$subject = preg_replace("/[^a-zA-Z0-9&_ ]/", "", $subject);
            // print_r($subject);die("OOk");
            $checkWrkFlwName = mysqli_query($db_con, "select workflow_name from tbl_workflow_master where FIND_IN_SET('$subject', workflow_name)") or die('Error: ' . mysqli_error($db_con));
            if (mysqli_num_rows($checkWrkFlwName) == 1) {
                //check duplicate name of workflow
                return array("status" => FALSE, "msg" => "Workflow of this Subject Already Exist !", "dev_msg" => "Workflow name is already exist");
            } else {
                /*
                 * Fetch Main doc Id using ticket Id from tbl doc review
                 */
                $fetchMainDocID = mysqli_query($db_con, "select * from tbl_doc_review where ticket_id='$reviewTicketId'");
                if (mysqli_num_rows($fetchMainDocID) > 0) {
                    $fetchReviewData = mysqli_fetch_assoc($fetchMainDocID);
                    $mainDocID = $fetchReviewData['doc_id'];

                    $taskOrder = $_POST['taskOrder'];
                    $assiUsers = $_POST['assignUsr'];
                    $altrusr = $_POST['altrUsr'];
                    $supvsr = $_POST['supvsr'];
                    if (!empty($taskOrder) && !empty($assiUsers) && !empty($supvsr)) {
                        if (count(array_unique($taskOrder)) < count($taskOrder)) {
                            return array("status" => FALSE, "msg" => "Order No. Can\'t be Same! ", "dev_msg" => "Duplicate order assign");
                        } else {
                            //create workflow
                            $workflowName = trim($subject);
                            // print_r($workflowName);die;
                            $insertWorkflow = mysqli_query($db_con, "insert into tbl_workflow_master (workflow_name) values ('$workflowName')");
                            if ($insertWorkflow) {

                                $workflId = mysqli_insert_id($db_con);
                                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'WorkFlow Name $workflowName Created','$date',null,'$host',null)");
                                if ($log) {

                                    //asign workflow to group
                                    if ($insertWorkflow) {
                                        $usrGrp = mysqli_query($db_con, "SELECT * FROM `tbl_bridge_grp_to_um` WHERE FIND_IN_SET('$user_id',user_ids)");

                                        if (mysqli_num_rows($usrGrp) > 0) {
                                            while ($rwusrGrp = mysqli_fetch_assoc($usrGrp)) {
                                                $arrayGrp[] = $rwusrGrp['group_id'];
                                            }
                                            $workflowgroups = implode(",", $arrayGrp);
                                            $insertworkflowgrp = mysqli_query($db_con, "insert into tbl_workflow_to_group(workflow_id,group_id) values ('$workflId','$workflowgroups')");

                                            if ($insertworkflowgrp) {
                                                //create step
                                                $workStepName = "Step";
                                                $workStepOrd = 1;
                                                $adStep = mysqli_query($db_con, "insert into tbl_step_master (step_name, workflow_id, step_order) values ('$workStepName', '$workflId', '$workStepOrd')");
                                                if ($adStep) {
                                                    $stepid = mysqli_insert_id($db_con);

                                                    for ($i = 0; $i < count($taskOrder); $i++) {

                                                        $ord = $taskOrder[$i];
                                                        $taskName = 'Task' . $ord;
                                                        $asUsr = $assiUsers[$i];
                                                        $altUsr = $altrusr[$i];
                                                        $supVsr = $supvsr[$i];
                                                        $deadlineType = $_POST['radio' . $i];

                                                        if ($deadlineType == 'Date') {

                                                            $daterange = $_POST['daterange'];

                                                            $daterangee = explode("To", $daterange[$i]);

                                                            $startDate = date('Y-m-d H:i:s', strtotime($daterangee[0]));

                                                            $endDate = date('Y-m-d H:i:s', strtotime($daterangee[1]));

                                                            $date1 = new DateTime($startDate);
                                                            $date2 = new DateTime($endDate);
                                                            //print_r($date1);
                                                            // print_r($date2);
                                                            $diff = $date1->diff($date2);

                                                            $deadLine = $diff->h * 60 + $diff->days * 24 * 60 + $diff->i;  //convert in minute
                                                            //echo $deadLine=$deadLine.'.'.$diff->i;
                                                            //echo   $deadLine=round($deadLine/60*60,1);
                                                            // die('ok');
                                                            //echo $deadLine;
                                                        } else if ($deadlineType == 'Days') {
                                                            $deadLinee = $_POST['days'];
                                                            $deadLine = $deadLinee[$i];
                                                        } else if ($deadlineType == 'Hrs') {

                                                            $deadLinee = $_POST['hrs'];
                                                            $deadLine = $deadLinee[$i] * 60;
                                                        }

                                                        $dedTyp = $deadlineType;
                                                        $dedLn = $deadLine;


                                                        $insertTask = mysqli_query($db_con, "insert into tbl_task_master (task_name, assign_user,alternate_user, supervisor, task_order, step_id, workflow_id, task_created_date, deadline, deadline_type) values('$taskName', '$asUsr','$altUsr', '$supVsr', '$ord', '$stepid', '$workflId', '$date', '$dedLn', '$dedTyp')");
                                                        if ($insertTask) {
                                                            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,' $taskName Task Added','$date',null,'$host',null)");
                                                        } else {
                                                            return array("status" => FALSE, "msg" => "Failed To Create Task", "dev_msg" => "Task QUERY Failed");
                                                        }
                                                        /*
                                                         * IF ALL TASK CREATED
                                                         */
                                                    }

                                                    /*
                                                     * Fetch The storage assaign to main doc id
                                                     * 
                                                     */
                                                    $fetchMainDocStrg = mysqli_query($db_con, "select * from tbl_document_reviewer where doc_id='$mainDocID'");
                                                    if (mysqli_num_rows($fetchMainDocStrg) > 0) {
                                                        $mainDocStrg = mysqli_fetch_assoc($fetchMainDocStrg);
                                                        $docName = $mainDocStrg['doc_name'];
                                                        $oldDOCNAME = $mainDocStrg['old_doc_name'];
                                                        $docExtn = $mainDocStrg['doc_extn'];
                                                        $docPath = $mainDocStrg['doc_path'];
                                                        $uploadBy = $mainDocStrg['uploaded_by'];
                                                        $docSize = $mainDocStrg['doc_size'];
                                                        $noPages = $mainDocStrg['noofpages'];
                                                        $datePosted = $mainDocStrg['dateposted'];
                                                        $fileNumber = $mainDocStrg['File_Number'];
                                                        $supportDoc = $docName . "_" . $mainDocID;
                                                        if (!empty($docName)) {
                                                            $id = $docName . '_' . $workflId;
                                                            $workFlowArray = explode(" ", $workflowName);
                                                            $ticket = '';
                                                            for ($w = 0; $w < count($workFlowArray); $w++) {
                                                                $name = $workFlowArray[$w];
                                                                mb_internal_encoding("UTF-8");
                                                                $ticket = $ticket . mb_substr($name, 0, 1);
                                                            }

                                                            $ticket = $ticket . '_' . $user_id . '_' . strtotime($date);
                                                            //echo $ticket;die;
                                                            /*
                                                             * Convert It into pdf
                                                             */
                                                            if (FTP_ENABLED) {
                                                                $localPath = $folderName . '/' . preg_replace('/[^A-Za-z0-9\-_]/', '', $oldDOCNAME) . '.' . "html";
                                                                
                                                                $server_path = ROOT_FTP_FOLDER . '/' . $docPath;
																
																$fileManager->downloadFile($server_path, $localPath);
                                                                //$ftp->get($localPath, $server_path); 
																
																
                                                                //$folderName = str_replace(" ", "", $workflowName);
                                                            } else {
                                                                $localPath = 'extract-here/' . $docPath;
                                                            }
                                                            
                                                            decrypt_my_file($localPath);

                                                            $nfilename = preg_replace('/[^A-Za-z0-9_\-]/', '', $oldDOCNAME);
                                                            // $filenameEnct=$fname.'.'.$extn;// urlencode(base64_encode($fname)).'.'.$extn;
                                                            $filenameEnct = urlencode(base64_encode($nfilename));
                                                            $filenameEnct = preg_replace('/[^A-Za-z0-9_\-]/', '', $filenameEnct);
                                                            $filenameEnct = time() . $filenameEnct;
                                                            $pdfName = $filenameEnct . ".pdf"; //specify the file save location and the file name
                                                            $pathWfFolder = str_replace(" ", "", $workflowName);
                                                            $exportedPath = $exportStaticPath . $pathWfFolder;
                                                            if (!is_dir($exportedPath)) {
                                                                mkdir($exportedPath, 0777, true);
                                                            }
                                                            include 'exportpdf.php';
                                                            $posted_editor = file_get_contents($localPath);
                                                            $exportedPath = $exportedPath . "/" . $pdfName; //export path of extract here
                                                            $dbPath = $pathWfFolder . "/" . $pdfName; // path goes to db
                                                            exportPDFSize($posted_editor, $exportedPath, $height, $width);
                                                            $wrkflowFsize = filesize($exportedPath);
                                                            $pagecount = count_pages($exportedPath);

                                                            if (uploadFileInFtpServer($dbPath, $exportedPath)) {
                                                                $tempFileRemove[] = $server_path; //$ftp->singleFileDelete($server_path);//delete file from old storage after upload in workflow
                                                            } else {
                                                                return array("status" => FALSE, "msg" => "Failed To Upload File", "dev_msg" => "");
                                                            }

                                                            /*
                                                             * Insert First doc id with workflow id
                                                             */
                                                            $doc_name = $docName . '_' . $workflId;
                                                            //@sk241018: make filename standard for workflow;
                                                            $oldDOCNAME = explode('_', $oldDOCNAME);
                                                            $oldDOCNAME = $oldDOCNAME[0];
                                                            if (!empty($mainDocStrg['storage_doc_id'])) {
                                                                $wrkflowDoc = mysqli_query($db_con, "INSERT INTO tbl_document_master (doc_id,doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages, dateposted,File_Number, workflow_id) VALUES ('$mainDocStrg[storage_doc_id]','$doc_name', '$oldDOCNAME', 'pdf', '$dbPath', '$uploadBy', '$wrkflowFsize', '$pagecount', '$datePosted','$fileNumber', '$workflId')");
                                                            } else {
                                                                $wrkflowDoc = mysqli_query($db_con, "INSERT INTO tbl_document_master (doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages, dateposted,File_Number, workflow_id) VALUES ('$doc_name', '$oldDOCNAME', 'pdf', '$dbPath', '$uploadBy', '$wrkflowFsize', '$pagecount', '$datePosted','$fileNumber',  '$workflId')");
                                                            }

                                                            if ($wrkflowDoc) {
                                                                $docId = mysqli_insert_id($db_con);
                                                                $newdocname = base64_encode($docId);
                                                                changePdfToImage($exportedPath,$newdocname);
                                                                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`,`doc_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,$docName,$docId,'Document Upload in $workflowName workflow','$date','$date','$host',null)");
                                                                if ($log) {
                                                                    
                                                                } else {
                                                                    return array("status" => FALSE, "msg" => "Failed To Generate Log", "dev_msg" => "");
                                                                }
                                                                /*
                                                                 * Fetch ALL REALATED DOCUMENT FROM  DOC REVIEW MASTER
                                                                 * move file from assign storage to workflow folder
                                                                 */
                                                                $docAllReview = mysqli_query($db_con, "select * from `tbl_document_reviewer` where doc_name='$supportDoc'");
                                                                if (mysqli_num_rows($docAllReview) > 0) {
                                                                    while ($rows = mysqli_fetch_assoc($docAllReview)) {

                                                                        $oldDoc = explode("_", $rows['doc_name']);
                                                                        $id = $oldDoc[0] . '_' . $docId . '_' . $workflId;
                                                                        $dlid = $oldDoc[0] . '_' . $docId;
                                                                        $soldDOCNAME = $rows['old_doc_name'];
                                                                        $sdocExtn = $rows['doc_extn'];
                                                                        $sdocPath = $rows['doc_path'];
                                                                        $suploadBy = $rows['uploaded_by'];
                                                                        $sdocSize = $rows['doc_size'];
                                                                        $snoPages = $rows['noofpages'];
                                                                        $sdatePosted = $rows['dateposted'];
                                                                        $sfileNumber = $rows['File_Number'];
                                                                        $info = new SplFileInfo($sdocPath); //get file extension from file
                                                                        $fileExten = $info->getExtension(); //orginal extension of file
                                                                        /*
                                                                         * IF FILE EXTENSION IS HTML AND VERSION OF HTMOL FILE OINLY
                                                                         */
                                                                        if ($fileExten == "html") {
                                                                            if (FTP_ENABLED) {
                                                                                $localPath = $folderName . '/' . preg_replace('/[^A-Za-z0-9\-_]/', '', $soldDOCNAME) . '.' . "html";
                                                                                $server_path = ROOT_FTP_FOLDER . '/' . $sdocPath;
																				
																				$fileManager->downloadFile($server_path, $localPath);
																				
                                                                                //$ftp->get($localPath, $server_path); 
                                                                            } else {
                                                                                $localPath = 'extract-here/' . $sdocPath;
                                                                            }

                                                                            $nfilename = preg_replace('/[^A-Za-z0-9_\-]/', '', $soldDOCNAME);
                                                                            // $filenameEnct=$fname.'.'.$extn;// urlencode(base64_encode($fname)).'.'.$extn;
                                                                            $filenameEnct = urlencode(base64_encode($nfilename));
                                                                            $filenameEnct = preg_replace('/[^A-Za-z0-9_\-]/', '', $filenameEnct);
                                                                            $filenameEnct = time() . $filenameEnct;
                                                                            $pdfName = $filenameEnct . ".pdf"; //specify the file save location and the file name

                                                                            $posted_editor1 = file_get_contents($localPath);
                                                                            $exportedPath = $exportStaticPath . $pathWfFolder . "/" . $pdfName; //export path of extract here
                                                                            exportPDFSize($posted_editor1, $exportedPath, $height, $width);

                                                                            /*
                                                                             * Remove Extract Here
                                                                             */
                                                                            $dbPath = $pathWfFolder . "/" . $pdfName;

                                                                            /*
                                                                             * Remove complete
                                                                             */
                                                                            /*
                                                                             * Remove File From old Place after upload in workflow
                                                                             */

                                                                            $wrkflowFsize = filesize($exportedPath);
                                                                            $pagecount = count_pages($exportedPath);
                                                                            //    $ftp->singleFileDelete($server_path);
                                                                            if (uploadFileInFtpServer($dbPath, $exportedPath)) {
                                                                                $tempFileRemove[] = $server_path; //$ftp->singleFileDelete($server_path);//delete file from old storage after upload in workflow
                                                                            } else {
                                                                                return array("status" => FALSE, "msg" => "Failed To Upload File", "dev_msg" => "");
                                                                            }



                                                                            // $sDoc = mysqli_query($db_con, "INSERT INTO tbl_document_master (doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages, dateposted,File_Number) VALUES ('$id', '$soldDOCNAME', 'pdf', '$dbPath', '$suploadBy', '$wrkflowFsize', '$pagecount', '$sdatePosted','$sfileNumber')");
                                                                        } else {
                                                                            if (FTP_ENABLED) {
                                                                                $localPath = $folderName . '/' . end(explode("/", $sdocPath));
                                                                                $server_path = ROOT_FTP_FOLDER . '/' . $sdocPath;
                                                                                //$ftp->get($localPath, $server_path); 
																				
																				$fileManager->downloadFile($server_path, $localPath);
																				
                                                                            } else {
                                                                                $localPath = 'extract-here/' . $sdocPath;
                                                                            }
                                                                            $path = $exportStaticPath . str_replace(" ", "", $workflowName);
                                                                            //$copyExtn = $path . "/" . $soldDOCNAME . "." . $sdocExtn;
                                                                            $copyExtn = $path . "/" . end(explode("/", $sdocPath));


                                                                            if (!copy($localPath, $copyExtn)) {
                                                                                return array("status" => FALSE, "msg" => "Failed To Move Document In Workflow", "dev_msg" => "");
                                                                            } else {
                                                                                $dbPath = explode("/", $copyExtn);
                                                                                unset($dbPath[0]);
                                                                                $dbPath = implode("/", $dbPath);

                                                                                if (uploadFileInFtpServer($dbPath, $copyExtn)) {
                                                                                    $tempFileRemove[] = $server_path; //delete file from old storage after upload in workflow
                                                                                } else {
                                                                                    return array("status" => FALSE, "msg" => "Failed To Upload File", "dev_msg" => "");
                                                                                }
                                                                                $wrkflowFsize = filesize($copyExtn);
                                                                                $pagecount = count_pages($copyExtn);
                                                                                // $ftp->singleFileDelete($server_path);
                                                                                $sDoc = mysqli_query($db_con, "INSERT INTO tbl_document_master (doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages, dateposted,File_Number, workflow_id) VALUES ('$id', '$soldDOCNAME', '$sdocExtn', '$dbPath', '$suploadBy', '$wrkflowFsize', '$pagecount', '$sdatePosted','$sfileNumber', '$workflId')");
                                                                            
                                                                                $newdocId = mysqli_insert_id($db_con);
                                                                                decrypt_my_file($copyExtn);
                                                                                if($sdocExtn=='jpg' || $sdocExtn=='jpeg' || $sdocExtn=='png'){
                                                                                    createThumbnail2($copyExtn,$newdocname);
                                                                                }elseif($sdocExtn=='pdf'){
                                                                                    changePdfToImage($copyExtn,$newdocname);
                                                                                }
                                                                                
                                                                            }
                                                                            if ($sDoc) {
                                                                                
                                                                               

//                                                                                 $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`,`doc_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$dlid',$docId,'Document Upload in $workflowName workflow','$date','$date','$host',null)");
//                                                                            if ($log) {
//                                                                                
//                                                                            } else {
//                                                                                return array("status" => FALSE, "msg" => "Failed To Generate Log", "dev_msg" => "");
//                                                                            }
                                                                            } else {
                                                                                return array("status" => FALSE, "msg" => "Failed To Upload Supporting Document In Workflow", "dev_msg" => "");
                                                                            }
                                                                        }
                                                                    }

                                                                    $getFirstTask = mysqli_query($db_con, "select * from tbl_task_master where workflow_id = '$workflId' ORDER BY task_order ASC LIMIT 1");
                                                                    if (mysqli_num_rows($getFirstTask) > 0) {
                                                                        $rwgetTask = mysqli_fetch_assoc($getFirstTask);
                                                                        $wTaskId = $rwgetTask[task_id];

                                                                        $getTaskDl = mysqli_query($db_con, "select * from tbl_task_master where task_id='$wTaskId'");
                                                                        $rwgetTaskDl = mysqli_fetch_assoc($getTaskDl);

                                                                        if ($rwgetTaskDl['deadline_type'] == 'Date' || $rwgetTaskDl['deadline_type'] == 'Hrs') {

                                                                            $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 60));
                                                                        }
                                                                        if ($rwgetTaskDl['deadline_type'] == 'Days') {

                                                                            $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 24 * 60 * 60));
                                                                        }
                                                                        mysqli_set_charset($db_con, "utf8");
                                                                        $insertInTask = mysqli_query($db_con, "INSERT INTO tbl_doc_assigned_wf(task_id, doc_id, start_date, end_date, task_remarks, task_status, assign_by, ticket_id) VALUES ('$wTaskId', '$docId', '$date', '$endDate', '$taskRemark', 'Pending', '$user_id', '$ticket')");
                                                                        //print_r("INSERT INTO tbl_doc_assigned_wf(task_id, doc_id, start_date, end_date, task_remarks, task_status, assign_by, ticket_id) VALUES ('$wTaskId', '$docId', '$date', '$endDate', '$taskRemark', 'Pending', '$user_id', '$ticket')");die("dhfnd");
                                                                        if ($insertInTask) {
                                                                            $idins = mysqli_insert_id($db_con);

                                                                            $getTask = mysqli_query($db_con, "select * from tbl_task_master where task_id = '$wTaskId'");
                                                                            if (mysqli_num_rows($getTask) > 0) {
                                                                                $rwgetTask = mysqli_fetch_assoc($getTask);
                                                                                $TskStpId = $rwgetTask['step_id'];
                                                                                $TskWfId = $rwgetTask['workflow_id'];
                                                                                $TskOrd = $rwgetTask['task_order'];
                                                                                $TskAsinToId = $rwgetTask['assign_user'];
                                                                                $nextTaskOrd = $TskOrd + 1;

                                                                                nextTaskAsin($nextTaskOrd, $TskWfId, $TskStpId, $docId, $date, $user_id, $db_con, $taskRemark, $ticket);
                                                                                require_once './mail.php';
                                                                                $mail = assignTask($ticket, $idins, $db_con, $projectName);
                                                                                if ($mail) {
                                                                                    $updateReview = mysqli_query($db_con, "update `tbl_reviews_log` set doc_id='$docId',in_review='1' where doc_id=$did");
                                                                                    if ($updateReview) {
                                                                                        $delDocReview = mysqli_query($db_con, "Delete from tbl_document_reviewer where doc_id='$did'");
                                                                                        if ($delDocReview) {
                                                                                            $delDocReviewSupport = mysqli_query($db_con, "Delete from `tbl_document_reviewer` where doc_name='$supportDoc'");
                                                                                            if ($delDocReviewSupport) {
                                                                                                $delTicketID = mysqli_query($db_con, "Delete from `tbl_doc_review` where ticket_id='$reviewTicketId'");
                                                                                                if ($delTicketID) {
                                                                                                    /*
                                                                                                     * REMOVE ALL TEMP FILES FROM SERVER after move files to workflow folder
                                                                                                     */

                                                                                                    foreach ($tempFileRemove as $key => $serverValues) {
                                                                                                        if (!empty($serverValues)) {
                                                                                                            $ftp->singleFileDelete($serverValues);
                                                                                                        }
                                                                                                    }
                                                                                                    return array("status" => TRUE, "msg" => "$lang[Process_Send_Tray]", "dev_msg" => "", "db_con" => $db_con);
                                                                                                } else {
                                                                                                    return array("status" => FALSE, "msg" => "$lang[support_from_doc_review]", "dev_msg" => "$lang[support_from_doc_review]");
                                                                                                }
                                                                                            } else {
                                                                                                return array("status" => FALSE, "msg" => "$lang[support_from_doc_review]", "dev_msg" => "$lang[support_from_doc_review]");
                                                                                            }
                                                                                        } else {
                                                                                            return array("status" => FALSE, "msg" => "$lang[failed_delete_document_review]", "dev_msg" => "$lang[failed_delete_document_review]");
                                                                                        }
                                                                                    } else {
                                                                                        return array("status" => FALSE, "msg" => "$lang[log_not_update]", "dev_msg" => "$lang[log_not_update]");
                                                                                    }

                                                                                    //return array("status" => TRUE, "msg" => "Process Send To In Tray", "dev_msg" => "","db_con"=>$db_con);
                                                                                } else {

                                                                                    return array("status" => FALSE, "msg" => "$lang[Ops_Ml_nt_snt]", "dev_msg" => "");
                                                                                }
                                                                            } else {
                                                                                return array("status" => FALSE, "msg" => "$lang[Failed_Next_Task_Assign]", "dev_msg" => "");
                                                                            }
                                                                        } else {
                                                                            return array("status" => FALSE, "msg" => "$lang[Failed_Assigned_Task]", "dev_msg" => "");
                                                                        }
                                                                    } else {
                                                                        return array("status" => FALSE, "msg" => "$lang[Tre_is_no_tsk_in_ts_wfw]", "dev_msg" => "");
                                                                    }
                                                                }
                                                            } else {
                                                                return array("status" => FALSE, "msg" => "$lang[Failed_Upload_Document_Workflow] ", "dev_msg" => "");
                                                            }
                                                        } else {
                                                            return array("status" => FALSE, "msg" => "$lang[Invalid_Storage_Select]");
                                                        }
                                                    } else {
                                                        return array("status" => FALSE, "msg" => "$lang[Document_Not_Available]");
                                                    }
                                                } else {
                                                    return array("status" => FALSE, "msg" => "$lang[Failed_Create_Step]", "dev_msg" => "Insert step failed");
                                                }
                                            } else {
                                                return array("status" => FALSE, "msg" => "$lang[Failed_Assign_Group_Workflow]", "dev_msg" => "$lang[Failed_Assign_Group_Workflow]");
                                            }
                                        } else {
                                            return array("status" => FALSE, "msg" => "$lang[Group_Assign_user]", "dev_msg" => "Group Query Not work");
                                        }
                                    }
                                } else {
                                    return array("status" => FALSE, "msg" => "$lang[log_not_update]", "dev_msg" => "Workflow Log Quesry not run");
                                }
                            } else {
                                return array("status" => FALSE, "msg" => "$lang[failed_delete_document_review]", "dev_msg" => "Workflow Insert Quesry not run");
                            }
                        }
                    } else {
                        return array("status" => FALSE, "msg" => $lang['pfarf'], "dev_msg" => "$lang[No_order_task_created]");
                    }
                } else {
                    return array("status" => FALSE, "msg" => "$lang[Invalid_Review_Ticket_ID]");
                }
            }
        } else {
            return array("status" => FALSE, "msg" => $lang['Subject_Name_or_Existing_Work_Flow_should_be_filled'], "dev_msg" => "subject is empty");
        }
    } catch (Exception $exc) {
        echo $exc->getTraceAsString();
    }
}

function uploadFileInFtpServer($destinationPath, $sourcePath) {
	
  $fileManager = new fileManager();
	// Connect to file server
	$fileManager->conntFileServer();
	if($fileManager->uploadFile($sourcePath, ROOT_FTP_FOLDER . '/' . $destinationPath)){
		 return true;
	}else{ 
		 return false;
	}
}
?>
