<!DOCTYPE html>
<html>
<?php
// error_reporting(0);
require_once './loginvalidate.php';
require_once './application/config/database.php';
require_once './application/pages/head.php';
require_once './application/pages/function.php';
require_once './classes/fileManager.php';


if ($rwgetRole['dashboard_mytask'] != '1') {
    header('Location: ./index');
}
$roleids = array();
$grp_by_rl_ids = mysqli_query($db_con, "SELECT group_id,user_ids FROM `tbl_bridge_grp_to_um` where find_in_set($_SESSION[cdes_user_id],user_ids)");
while ($rwGrp = mysqli_fetch_array($grp_by_rl_ids)) {
    if (!empty($rwGrp['user_ids'])) {
        $user_ids[] = $rwGrp['user_ids'];
    }
}
$user_ids = implode(',', $user_ids);
$user_ids = explode(',', $user_ids);
$user_ids = array_unique($user_ids);
$user_ids = implode(',', $user_ids);
//workflow id
$wfid = base64_decode(urldecode($_GET['wfid']));
$workfid = preg_replace("/[^0-9 ]/", "", $wfid);

?>
<link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<!--for filter calnder-->
<link href="assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">

<link href="assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet">
<link href="assets/plugins/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
<!-- Plugin Css-->
<link rel="stylesheet" href="assets/plugins/magnific-popup/css/magnific-popup.css" />

<style>
    .disabled-task {
        pointer-events: none;
        cursor: default;
        opacity: 0.6;
    }

    #taskRemrk .table {
        border: none !important;
    }

    .error {
        color: #fff !important;
    }
</style>

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
                                    <a href="myTask"><?php echo $lang['Workflow']; ?></a>
                                </li>
                                <li class="active"> <?php echo $lang['IN_TRAY']; ?></li>
                                <a href="javascript:void(0)"
                                    class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9"
                                    onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i
                                        class="fa fa-arrow-circle-left"></i></a>
                            </ol>
                        </div>
                    </div>
                    <div class="box box-primary">
                        <div class="panel">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <form method="get">
                                            <div class="col-sm-2">
                                                <div class="form-group">
                                                    <select class="form-control select2" id="statusTask"
                                                        name="taskStats">
                                                        <option selected value=""><?php echo $lang['Sl_Tsk_Sts']; ?>
                                                        </option>
                                                        <option <?php
                                                                if ($_GET['taskStats'] == 'Pending') {
                                                                    echo 'selected';
                                                                }
                                                                ?>><?php echo $lang['Pending']; ?></option>
                                                        <option <?php
                                                                if ($_GET['taskStats'] == 'Processed') {
                                                                    echo 'selected';
                                                                }
                                                                ?>><?php echo $lang['Processed']; ?></option>
                                                        <option <?php
                                                                if ($_GET['taskStats'] == 'Approved') {
                                                                    echo 'selected';
                                                                }
                                                                ?>><?php echo ucwords($lang['approved']); ?>
                                                        </option>
                                                        <option <?php
                                                                if ($_GET['taskStats'] == 'Rejected') {
                                                                    echo 'selected';
                                                                }
                                                                ?>><?php echo $lang['Rejected']; ?></option>
                                                        <option <?php
                                                                if ($_GET['taskStats'] == 'Aborted') {
                                                                    echo 'selected';
                                                                }
                                                                ?>><?php echo $lang['Aborted']; ?></option>
                                                        <option <?php
                                                                if ($_GET['taskStats'] == 'Complete') {
                                                                    echo 'selected';
                                                                }
                                                                ?>><?php echo $lang['Complete']; ?></option>
                                                        <option <?php
                                                                if ($_GET['taskStats'] == 'Done') {
                                                                    echo 'selected';
                                                                }
                                                                ?>><?php echo $lang['Done']; ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <div class="form-group">
                                                    <select class="form-control select2" id="" name="taskPrioty">
                                                        <option selected value=""><?php echo $lang['Slct_Prty']; ?>
                                                        </option>
                                                        <option value="3" <?php
                                                                            if ($_GET['taskPrioty'] == 3) {
                                                                                echo 'selected';
                                                                            }
                                                                            ?>><?php echo $lang['Normal']; ?></option>
                                                        <option value="2" <?php
                                                                            if ($_GET['taskPrioty'] == 2) {
                                                                                echo 'selected';
                                                                            }
                                                                            ?>><?php echo $lang['Medium']; ?></option>
                                                        <option value="1" <?php
                                                                            if ($_GET['taskPrioty'] == 1) {
                                                                                echo 'selected';
                                                                            }
                                                                            ?>><?php echo $lang['Urgent']; ?></option>

                                                    </select>

                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <div class="input-daterange input-group" id="date-range">
                                                            <input type="text" class="form-control" name="startDate"
                                                                value="<?php echo $_GET['startDate']; ?>"
                                                                placeholder="<?= $lang['dd_mm_yyyy']; ?>"
                                                                title="<?= $lang['dd_mm_yyyy']; ?>" />
                                                            <span
                                                                class="input-group-addon bg-custom b-0 text-white"><?php echo $lang['to']; ?></span>
                                                            <input type="text" class="form-control" name="endDate"
                                                                value="<?php echo $_GET['endDate']; ?>"
                                                                placeholder="<?= $lang['dd_mm_yyyy']; ?>"
                                                                title="<?= $lang['dd_mm_yyyy']; ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <select class="form-control select2" id="" name="asinBy">
                                                        <option selected value="">
                                                            <?php echo $lang['Select_Assign_By']; ?></option>
                                                        <?php
                                                        $assign_by = array();
                                                        mysqli_set_charset($db_con, "utf8");
                                                        $users = mysqli_query($db_con, "select assign_by from tbl_task_master inner join tbl_doc_assigned_wf on tbl_task_master.task_id=tbl_doc_assigned_wf.task_id where assign_user='$_SESSION[cdes_user_id]' or alternate_user='$_SESSION[cdes_user_id]' or supervisor='$_SESSION[cdes_user_id]'");
                                                        while ($rwUsers = mysqli_fetch_assoc($users)) {
                                                            $assign_by[] = $rwUsers['assign_by'];
                                                        }
                                                        $assign_by = implode(',', $assign_by);
                                                        if ($_SESSION['cdes_user_id'] == 1) {
                                                            $userAsinBy = "SELECT * from tbl_user_master order by first_name, last_name asc";
                                                        } else {
                                                            $userAsinBy = "SELECT * from tbl_user_master where user_id in($assign_by) order by first_name, last_name asc";
                                                        }
                                                        if (!empty($assign_by) || $_SESSION['cdes_user_id'] == 1) {
                                                            $userAsinBy_query = mysqli_query($db_con, $userAsinBy) or die("Error: " . mysqli_error($db_con));
                                                            while ($userAsinBy_row = mysqli_fetch_assoc($userAsinBy_query)) {
                                                                if ($userAsinBy_row['user_id'] != 1 && $userAsinBy_row['user_id'] != $_SESSION['cdes_user_id']) {
                                                        ?>

                                                                    <option value="<?php echo $userAsinBy_row['user_id']; ?>" <?php
                                                                                                                                if ($_GET['asinBy'] == $userAsinBy_row['user_id']) {
                                                                                                                                    echo 'selected';
                                                                                                                                }
                                                                                                                                ?>>
                                                                        <?php echo $userAsinBy_row['first_name'] . ' ' . $userAsinBy_row['last_name']; ?>
                                                                    </option>

                                                        <?php
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <select class="select2" id="wfid" name="wfid">
                                                    <option selected value=""><?php echo $lang['Slt_Wrkflw']; ?>
                                                    </option>
                                                    <?php
                                                    $sameGroupIDs = array();
                                                    $group = mysqli_query($db_con, "select group_id from tbl_bridge_grp_to_um where find_in_set('$_SESSION[cdes_user_id]',user_ids)") or die('Error' . mysqli_error($db_con));
                                                    while ($rwGroup = mysqli_fetch_assoc($group)) {
                                                        $sameGroupIDs[] = $rwGroup['group_id'];
                                                    }
                                                    $sameGroupIDs = array_unique($sameGroupIDs);
                                                    sort($sameGroupIDs);
                                                    $getWfID = mysqli_query($db_con, "select workflow_id,group_id from tbl_workflow_to_group") or die("Error " . mysqli_error($db_con));
                                                    while ($RwgetWfID = mysqli_fetch_assoc($getWfID)) {
                                                        $WFId = $RwgetWfID['workflow_id'];
                                                        $group_ids = explode(',', $RwgetWfID["group_id"]);
                                                        if (array_intersect($sameGroupIDs, $group_ids)) {
                                                            $fetchWorkflow = mysqli_query($db_con, "select * from tbl_workflow_master where workflow_id='$WFId' order by workflow_name asc") or die('Error in fetchworkflow:' . mysqli_error($db_con));
                                                            if (mysqli_num_rows($fetchWorkflow) > 0) {
                                                                $rwfetchWorkflow = mysqli_fetch_assoc($fetchWorkflow);
                                                                if ($workfid == $rwfetchWorkflow['workflow_id']) {
                                                    ?>
                                                                    <option
                                                                        value="<?php echo urlencode(base64_encode($rwfetchWorkflow['workflow_id'])); ?>"
                                                                        selected=""><?php echo $rwfetchWorkflow['workflow_name']; ?>
                                                                    </option>
                                                                <?php } else {
                                                                ?>
                                                                    <option
                                                                        value="<?php echo urlencode(base64_encode($rwfetchWorkflow['workflow_id'])); ?>">
                                                                        <?php echo $rwfetchWorkflow['workflow_name']; ?></option>
                                                    <?php
                                                                }
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <button class="btn btn-primary waves-effect waves-light" type="submit"
                                                name="fltr"><i class="fa fa-filter"></i>
                                                <?php echo $lang['Apply_Filter']; ?>
                                            </button>
                                            <input type="button" class="btn btn-warning" id="reset"
                                                value="<?php echo $lang['Reset'] ?>">
                                        </div>
                                    </div>
                                    </form>
                                </div>
                                <div class="box-body">
                                    <?php
                                    $where = "";
                                    require_once 'application/pages/where.php';
                                    if ($_SESSION['cdes_user_id'] == 1) {
                                        $constructs = "SELECT count(id) as count FROM tbl_task_master tsm inner join tbl_doc_assigned_wf tdawf on tsm.task_id=tdawf.task_id $where";
                                    } else {
                                        $constructs = "SELECT count(id) as count FROM tbl_task_master tsm inner join tbl_doc_assigned_wf tdawf on tsm.task_id=tdawf.task_id $where";
                                    }
                                    mysqli_set_charset($db_con, "utf8");
                                    $run = mysqli_query($db_con, $constructs); //or die('Error 1' . mysqli_error($db_con));
                                    $rwRun = mysqli_fetch_assoc($run);
                                    $foundnum = $rwRun['count'];

                                    if ($foundnum > 0) {

                                        if (isset($_GET['limit'])) {
                                            if (!empty($_GET['limit'])) {
                                                $per_page = $_GET['limit'];
                                            } else {
                                                $per_page = 10;
                                            }
                                        } else {
                                            $per_page = 10;
                                        }

                                        $start = isset($_GET['start']) ? ($_GET['start'] > 0) ? $_GET['start'] : 0 : 0;
                                        $max_pages = ceil($foundnum / $per_page);
                                        if (!$start) {
                                            $start = 0;
                                        }
                                        $limit = $_GET['limit'];
                                        if ($_SESSION['cdes_user_id'] == 1) {
                                            $allot = "SELECT tdawf.id,tsm.task_name,tsm.task_order,tdawf.doc_id,tdawf.task_status,tdawf.ticket_id,tdawf.task_remarks,tdawf.start_date,tsm.deadline,tsm.workflow_id,tsm.deadline_type,tsm.priority_id,tdawf.assign_by,tdawf.NextTask FROM tbl_task_master tsm inner join tbl_doc_assigned_wf tdawf on tsm.task_id=tdawf.task_id $where order by tdawf.id desc  LIMIT $start, $per_page";
                                        } else {
                                            $allot = "SELECT tdawf.id,tdawf.ticket_id,tsm.task_name,tsm.task_order,tdawf.doc_id,tdawf.task_status,tdawf.ticket_id,tdawf.task_remarks,tdawf.start_date,tsm.deadline,tsm.workflow_id,tsm.deadline_type,tsm.priority_id,tdawf.assign_by,tdawf.NextTask FROM tbl_task_master tsm inner join tbl_doc_assigned_wf tdawf on tsm.task_id=tdawf.task_id $where order by tdawf.id desc LIMIT $start, $per_page";
                                        }

                                        $allot_query = mysqli_query($db_con, $allot) or die("Error: " . mysqli_error($db_con));
                                    ?>
                                        <div class="row">
                                            <div class="col-md-10">
                                                <?php echo $lang['Show'] ?>
                                                <select id="limit1">
                                                    <option value="10" <?php
                                                                        if ($_GET['limit'] == 10) {
                                                                            echo 'selected';
                                                                        }
                                                                        ?>>10</option>
                                                    <option value="25" <?php
                                                                        if ($_GET['limit'] == 25) {
                                                                            echo 'selected';
                                                                        }
                                                                        ?>>25</option>
                                                    <option value="50" <?php
                                                                        if ($_GET['limit'] == 50) {
                                                                            echo 'selected';
                                                                        }
                                                                        ?>>50</option>
                                                    <option value="100" <?php
                                                                        if ($_GET['limit'] == 100) {
                                                                            echo 'selected';
                                                                        }
                                                                        ?>>100</option>
                                                    <option value="200" <?php
                                                                        if ($_GET['limit'] == 200) {
                                                                            echo 'selected';
                                                                        }
                                                                        ?>>200</option>
                                                </select><?php echo $lang['Tasks'] ?>
                                            </div>
                                            <div class="col-sm-2 record m-b-10">
                                                <?php echo $start + 1 ?> <?php echo $lang['To']; ?> <?php
                                                                                                    if ($start + $per_page > $foundnum) {
                                                                                                        echo $foundnum;
                                                                                                    } else {
                                                                                                        echo ($start + $per_page);
                                                                                                    }
                                                                                                    ?>
                                                <span><?php echo $lang['ttl_recrds']; ?> : <?php echo $foundnum; ?></span>
                                            </div>
                                        </div>
                                        <div class="row" style="overflow-x:scroll;">
                                            <div class="col-sm-12">
                                                <table class="table table-striped table-bordered js-sort-table">
                                                    <thead>
                                                        <tr>
                                                            <th class="sort-js-none"><?php echo $lang['Sr_No']; ?></th>
                                                            <th class="sort-js-none"><?php echo $lang['Action']; ?></th>
                                                            <th><?php echo $lang['Workflow_Name']; ?></th>
                                                            <th><?php echo $lang['FileName']; ?></th>
                                                            <th><?php echo $lang['initiated_by']; ?></th>
                                                            <th class="sort-js-date"><?php echo $lang['initiated_date']; ?>
                                                            </th>
                                                            <th><?php echo $lang['sent_by']; ?></th>
                                                            <th class="sort-js-date"><?php echo $lang['sent_date']; ?></th>
                                                            <th class="sort-js-number"><?php echo $lang['Deadline']; ?></th>
                                                            <th><?php echo $lang['Priority']; ?></th>
                                                            <th><?php echo $lang['status']; ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $n = $start + 1;
                                                        while ($allot_row = mysqli_fetch_assoc($allot_query)) {
                                                            //workflow name
                                                            $Workflw = mysqli_query($db_con, "select workflow_name from tbl_workflow_master where workflow_id='" . $allot_row['workflow_id'] . "'"); // or die('Error in getWorkflw upload:' . mysqli_error($db_con));
                                                            $rwWorkflw = mysqli_fetch_assoc($Workflw);

                                                            

                                                            if($_SESSION['cdes_user_id'] !=1){
                                                                $task_masterflw = mysqli_query(
                                                                    $db_con,
                                                                    "SELECT enable_edit_btn 
                                                                        FROM tbl_task_master 
                                                                        WHERE workflow_id='" . $allot_row['workflow_id'] . "' 
                                                                        AND assign_user='" . $_SESSION['cdes_user_id'] . "'"
                                                                );

                                                            }
                                                            $rwtask_masterflw = mysqli_fetch_assoc($task_masterflw);


                                                            $anc_attr = '';
                                                            $railway_master = "SELECT * FROM tbl_railway_master WHERE ticket_id='" . $allot_row['ticket_id'] . "';";
                                                            $railway_query = mysqli_query($db_con, $railway_master);
                                                            while ($allot_roww = mysqli_fetch_assoc($railway_query)) {
                                                                $railway_details = $allot_roww;
                                                            }

                                                          $docAssignSql = "SELECT * FROM tbl_doc_assigned_wf 
                                                                WHERE ticket_id='" . $allot_row['ticket_id'] . "'";

                                                            $docAssignQuery = mysqli_query($db_con, $docAssignSql);

                                                            while ($docAssignRow = mysqli_fetch_assoc($docAssignQuery)) {
                                                                $docAssignDetails = $docAssignRow;
                                                            }
                                                        ?>
                                                            <tr class="gradeX" style="vertical-align: middle;">
                                                                <td style="width:60px"><?php echo $n; ?></td>
                                                                <!--<td>
                                                                        <a href="<?php
                                                                                    if ($allot_row['NextTask'] == 2 || (($allot_row['task_status'] == 'Approved' || $allot_row['task_status'] == 'Processed' || $allot_row['task_status'] == 'Done') && ($allot_row['NextTask'] == 1)) || $allot_row['task_status'] == 'Rejected' || $allot_row['task_status'] == 'Complete') {
                                                                                        echo 'javascript:void(0)';
                                                                                    } else {
                                                                                        echo 'process_task?id=' . urlencode(base64_encode($allot_row['id'])) . '&filter=' . urlencode(base64_encode($filter));
                                                                                    }
                                                                                    ?>" title="Process Task"> <i class="fa fa-check-circle"></i></a>

                                                                    </td>-->

                                                                    <td>
                                                                        <?php 
                                                                        $letterType = $docAssignDetails['letter_type'] ?? '';

                                                                        if (
                                                                            $allot_row['NextTask'] == 2 || 
                                                                            (
                                                                                ($allot_row['task_status'] == 'Approved' || 
                                                                                $allot_row['task_status'] == 'Processed' || 
                                                                                $allot_row['task_status'] == 'Done') 
                                                                                && ($allot_row['NextTask'] == 1)
                                                                            ) || 
                                                                            $allot_row['task_status'] == 'Rejected' || 
                                                                            $allot_row['task_status'] == 'Complete' || 
                                                                            $allot_row['task_status'] == 'Aborted'
                                                                            || 
                                                                            ($letterType != '' && $letterType != 'Approval')
                                                                        ) { 
                                                                        ?>
                                                                            <a href="javascript:void(0)" class="disabled-task" title="<?= $lang['Process_Task']; ?>">
                                                                                <i class="glyphicon glyphicon-new-window task"></i>
                                                                            </a>
                                                                        <?php 
                                                                        } else { 
                                                                        ?>
                                                                            <a href="javascript:void(0)" 
                                                                            data-toggle="modal" 
                                                                            data-target="#con-close-modal-act" 
                                                                            class="taskbtn" 
                                                                            data-id="<?= $allot_row['id']; ?>" 
                                                                            title="<?= $lang['Process_Task']; ?>">
                                                                                <i class="glyphicon glyphicon-new-window task"></i>
                                                                            </a>
                                                                        <?php } ?>
                                                                        </td>


                                                                
                                                                <td><label
                                                                        class="label label-primary"><?php echo $rwWorkflw['workflow_name']; ?></label>
                                                                </td>
                                                                <td>

                                                                    <?php if (file_exists('thumbnail/' . base64_encode($allot_row['doc_id']) . '.jpg')) { ?>
                                                                        <div> <img class="thumb-image"
                                                                                src="thumbnail/<?= base64_encode($allot_row['doc_id']) ?>.jpg">
                                                                        </div>
                                                                    <?php } ?>

                                                                    <?php
                                                                    if (!empty($allot_row['doc_id'])) {
                                                                        mysqli_set_charset($db_con, "utf8");
                                                                        $doc = mysqli_query($db_con, "select * from tbl_document_master where doc_id='$allot_row[doc_id]' and flag_multidelete='1'") or die('Error' . mysqli_error($db_con));
                                                                        $rwDoc = mysqli_fetch_assoc($doc);
                                                                    ?>
                                                                        <?php if (strtolower($rwDoc['doc_extn']) == 'pdf' && $rwgetRole['pdf_file'] == '1') {
                                                                        ?>

                                                                            <a href="viewer?id=<?php echo urlencode(base64_encode($_SESSION['cdes_user_id'])) ?>&i=<?php echo urlencode(base64_encode($rwDoc['doc_id'])); ?>&pn=1"
                                                                                class="pdfview" target="_blank"
                                                                                title="<?php echo $rwDoc['old_doc_name']; ?>">
                                                                                <?php echo $rwDoc['old_doc_name']; ?>
                                                                                <i class="fa fa fa-file-text-o"
                                                                                    title="<?= $lang['view_copy_print']; ?>"></i></a>

                                                                            <?php
                                                                            if ($allot_row['NextTask'] == 2 || (($allot_row['task_status'] == 'Approved' || $allot_row['task_status'] == 'Processed' || $allot_row['task_status'] == 'Done') && ($allot_row['NextTask'] == 1)) || $allot_row['task_status'] == 'Rejected' || $allot_row['task_status'] == 'Complete') {
                                                                            } else {
                                                                                echo '<a href="anott/index?id=' . urlencode(base64_encode($_SESSION['cdes_user_id'])) . '&id1=' . urlencode(base64_encode($rwDoc['doc_id'])) . '&pn=1&tid=' . urlencode(base64_encode($allot_row['id'])) . '" class="pdfview" target="blank" ><i class="ti-marker-alt" title="' . $lang['edit_File'] . '"></i></a>';
                                                                            }
                                                                            ?>

                                                                        <?php
                                                                        } else if ((strtolower($rwDoc['doc_extn']) == 'jpg' || strtolower($rwDoc['doc_extn']) == 'jpeg' || strtolower($rwDoc['doc_extn']) == 'png' || strtolower($rwDoc['doc_extn']) == 'gif' || strtolower($rwDoc['doc_extn']) == 'bmp') && $rwgetRole['image_file'] == '1') {
                                                                        ?>
                                                                            <a href="imageviewer?uid=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&i=<?php echo urlencode(base64_encode($rwDoc['doc_id'])); ?>"
                                                                                target="_blank"
                                                                                title="<?php echo $rwDoc['old_doc_name']; ?>">
                                                                                <?php echo $rwDoc['old_doc_name']; ?> <i
                                                                                    class="fa fa-file-image-o"></i></a>

                                                                            <?php if ($allot_row['NextTask'] == 2 || (($allot_row['task_status'] == 'Approved' || $allot_row['task_status'] == 'Processed' || $allot_row['task_status'] == 'Done') && ($allot_row['NextTask'] == 1)) || $allot_row['task_status'] == 'Rejected' || $allot_row['task_status'] == 'Complete') { ?>
                                                                            <?php } else { ?>
                                                                                <!-- <a href="imageAnnotation?uid=<?php //echo urlencode(base64_encode($_SESSION[cdes_user_id])); 
                                                                                                                    ?>&i=<?php echo urlencode(base64_encode($rwDoc['doc_id'])); ?>&tid=<?php echo urlencode(base64_encode($allot_row['id'])); ?>"
                                                                                        target="_blank"> <i class="ti-marker-alt"//
                                                                                            data-toggle="tooltip"
                                                                                            title="<?php //echo $lang['image_file']; 
                                                                                                    ?>"></i></a> -->


                                                                            <?php }
                                                                        } else if ((strtolower($rwDoc['doc_extn']) == 'tif' || strtolower($rwDoc['doc_extn']) == 'tiff') && $rwgetRole['tif_file'] == '1') { ?>
                                                                            <a href="tiff-viewer?id=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&i=<?php echo urlencode(base64_encode($rwDoc['doc_id'])); ?>"
                                                                                target="_blank"
                                                                                title="<?php echo $rwDoc['old_doc_name']; ?>">
                                                                                <?php echo $rwDoc['old_doc_name']; ?> <i
                                                                                    class="fa fa-picture-o"></i> </a>
                                                                        <?php } else if (strtolower($rwDoc['doc_extn']) == 'xlsx' && $rwgetRole['excel_file'] == '1') {
                                                                        ?>
                                                                            <a href="xlsx?uid=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&file=<?php echo urlencode(base64_encode($rwDoc['doc_id'])); ?>"
                                                                                target="_blank"
                                                                                title="<?php echo $rwDoc['old_doc_name']; ?>">
                                                                                <?php echo $rwDoc['old_doc_name']; ?> <i
                                                                                    class="fa fa-file-excel-o"></i></a>
                                                                        <?php } else if (strtolower($rwDoc['doc_extn']) == 'xls' && $rwgetRole['excel_file'] == '1') {
                                                                        ?>
                                                                            <a href="xls?uid=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&file=<?php echo urlencode(base64_encode($rwDoc['doc_id'])); ?>"
                                                                                target="_blank"
                                                                                title="<?php echo $rwDoc['old_doc_name']; ?>">
                                                                                <?php echo $rwDoc['old_doc_name']; ?> <i
                                                                                    class="fa fa-file-excel-o"></i></a>
                                                                        <?php } else if ((strtolower($rwDoc['doc_extn']) == 'doc' || strtolower($rwDoc['doc_extn']) == 'docx') && $rwgetRole['doc_file'] == '1') { ?>
                                                                            <a href="viewword?i=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&id=<?php echo urlencode(base64_encode($rwDoc['doc_id'])); ?>"
                                                                                target="_blank"
                                                                                title="<?php echo $rwDoc['old_doc_name']; ?>">
                                                                                <?php echo $rwDoc['old_doc_name']; ?> <i
                                                                                    class="fa fa-file-word-o"></i></a>
                                                                            <?php if ($rwgetRole['word_edit'] == '1') { ?>
                                                                                <!-- <a href="editword?i=<?php //echo urlencode(base64_encode($_SESSION[cdes_user_id])); 
                                                                                                            ?>&id=<?php echo urlencode(base64_encode($rwDoc['doc_id'])); ?>"
                                                                                                        target="_blank"> <i class="ti-marker-alt"
                                                                                                            title="<?php //echo $lang['edit_File']; 
                                                                                                                    ?>"></i></a> -->
                                                                            <?php } ?>

                                                                        <?php } else if (strtolower($rwDoc['doc_extn']) == 'psd' && $rwgetRole['view_psd'] == '1') { ?>
                                                                            <a href="viewword?i=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&id=<?php echo urlencode(base64_encode($rwDoc['doc_id'])); ?>"
                                                                                target="_blank"
                                                                                title="<?php echo $rwDoc['old_doc_name']; ?>"><?php echo $rwDoc['old_doc_name']; ?><img
                                                                                    src="<?= BASE_URL ?>assets/images/psd.png"></a>

                                                                        <?php } else if (strtolower($rwDoc['doc_extn']) == 'cdr' && $rwgetRole['view_cdr'] == '1') { ?>
                                                                            <a href="viewword?i=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&id=<?php echo urlencode(base64_encode($rwDoc['doc_id'])); ?>"
                                                                                target="_blank"
                                                                                title="<?php echo $rwDoc['old_doc_name']; ?>"><?php echo $rwDoc['old_doc_name']; ?><img
                                                                                    src="<?= BASE_URL ?>assets/images/cdr.png"></a>
                                                                        <?php } else if (strtolower($rwDoc['doc_extn']) == 'rtf' && $rwgetRole['view_rtf'] == '1') { ?>
                                                                            <a href="viewword?i=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&id=<?php echo urlencode(base64_encode($rwDoc['doc_id'])); ?>"
                                                                                target="_blank"
                                                                                title="<?php echo $rwDoc['old_doc_name']; ?>"><?php echo $rwDoc['old_doc_name']; ?>
                                                                                <i class="fa fa-file"></i></a>
                                                                        <?php } else if (strtolower($rwDoc['doc_extn']) == 'odt' && $rwgetRole['view_odt'] == '1') { ?>
                                                                            <a href="viewword?i=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&id=<?php echo urlencode(base64_encode($rwDoc['doc_id'])); ?>"
                                                                                target="_blank"
                                                                                title="<?php echo $rwDoc['old_doc_name']; ?>"><?php echo $rwDoc['old_doc_name']; ?>
                                                                                <i class="fa fa-file-word-o"></i></a>
                                                                        <?php } else if ((strtolower($rwDoc['doc_extn']) == 'mp3' || strtolower($rwDoc['doc_extn']) == 'wav') && $rwgetRole['audio_file'] == '1') { ?>
                                                                            <a href="audioplayer?i=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&id=<?php echo urlencode(base64_encode($rwDoc['doc_id'])); ?>"
                                                                                target="_blank">
                                                                                <i class="fa fa-music"
                                                                                    title="<?php echo $lang['Audio_file']; ?>"></i></a>
                                                                        <?php } else if ((strtolower($rwDoc['doc_extn']) == 'mp4' || strtolower($rwDoc['doc_extn']) == '3gp') && $rwgetRole['video_file'] == '1') { ?>
                                                                            <a href="video-player?i=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&id=<?php echo urlencode(base64_encode($rwDoc['doc_id'])); ?>"
                                                                                target="_blank">
                                                                                <i class="fa fa-video-camera"
                                                                                    title="<?php echo $lang['Video_file']; ?>"></i></a>
                                                                        <?php } else if (!empty($rwDoc['doc_id'])) {
                                                                        ?>
                                                                            <a href="downloaddoc?file=<?php echo urlencode(base64_encode($rwDoc['doc_id'])) ?>"
                                                                                id="fancybox-inner" target="_blank">
                                                                                <?php echo $rwDoc['old_doc_name']; ?> <i
                                                                                    class="fa fa-download"></i>
                                                                            </a>
                                                                        <?php
                                                                        } else {
                                                                            echo '<span class="label label-primary">' . $lang['notask_nodescription'] . '</span>';
                                                                        }
                                                                        $docName = $rwDoc['doc_name'];
                                                                        $docName = explode("_", $docName);
                                                                        $updateDocName = $docName[0] . '_' . $rwDoc['doc_id'] . ((!empty($docName[1])) ? '_' . $docName[1] : '');
                                                                        $fileVersion = mysqli_query($db_con, "SELECT * FROM `tbl_document_master` WHERE doc_name='$updateDocName' and flag_multidelete='1'") or die('Error:' . mysqli_error($db_con));
                                                                        while ($rwfileVersion = mysqli_fetch_assoc($fileVersion)) {
                                                                        ?>
                                                                            <div>

                                                                                <?php if (file_exists('thumbnail/' . base64_encode($rwfileVersion['doc_id']) . '.jpg')) { ?>
                                                                                    <div> <img class="thumb-image"
                                                                                            src="thumbnail/<?= base64_encode($rwfileVersion['doc_id']) ?>.jpg">
                                                                                    </div>
                                                                                <?php } ?>
                                                                                <?php
                                                                                //versioning view start here
                                                                                if (strtolower($rwfileVersion['doc_extn']) == 'pdf' && $rwgetRole['pdf_file'] == '1') {
                                                                                ?>
                                                                                    <?php
                                                                                    //@sk-11118:stop processed and complete from demo.
                                                                                    if ($allot_row['NextTask'] == 2 || (($allot_row['task_status'] == 'Approved' || $allot_row['task_status'] == 'Processed' || $allot_row['task_status'] == 'Done') && ($allot_row['NextTask'] == 1)) || $allot_row['task_status'] == 'Rejected' || $allot_row['task_status'] == 'Complete') {
                                                                                    ?>
                                                                                        <a href="viewer?id=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])) ?>&i=<?php echo urlencode(base64_encode($rwfileVersion['doc_id'])); ?>&pn=1"
                                                                                            class="pdfview"
                                                                                            target="_blank"><?php echo $rwfileVersion['old_doc_name']; ?>
                                                                                            <i class="fa fa fa-file-text-o"
                                                                                                title="<?= $lang['view_copy_print']; ?>"></i></a>
                                                                                    <?php
                                                                                    } else {
                                                                                    ?>
                                                                                        <a href="viewer?id=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])) ?>&i=<?php echo urlencode(base64_encode($rwfileVersion['doc_id'])); ?>&pn=1"
                                                                                            class="pdfview"
                                                                                            target="_blank"><?php echo $rwfileVersion['old_doc_name']; ?>
                                                                                            <i class="fa fa fa-file-text-o"
                                                                                                title="<?= $lang['view_copy_print']; ?>"></i></a>

                                                                                    <?php
                                                                                        //echo '<a href="anott/index?id=' . urlencode(base64_encode($_SESSION[cdes_user_id])) . '&id1=' . urlencode(base64_encode($rwfileVersion['doc_id'])) . '&pn=1&tid=' . urlencode(base64_encode($allot_row[id])) . '" class="pdfview" target="blank">' . $rwfileVersion['old_doc_name'] . ' <i class="ti-marker-alt" title="' . $lang['edit_File'] . '"></i></a>';
                                                                                    }
                                                                                    ?>
                                                                                <?php } else if ((strtolower($rwfileVersion['doc_extn']) == 'gif' || strtolower($rwfileVersion['doc_extn']) == 'jpeg' || strtolower($rwfileVersion['doc_extn']) == 'jpg' || strtolower($rwfileVersion['doc_extn']) == 'png' || strtolower($rwfileVersion['doc_extn']) == 'bmp') && $rwgetRole['image_file'] == '1') {
                                                                                ?>
                                                                                    <a href="imageviewer?uid=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&i=<?php echo urlencode(base64_encode($rwfileVersion['doc_id'])); ?>"
                                                                                        target="_blank"
                                                                                        title="<?php echo substr($rwfileVersion['old_doc_name']); ?> ">
                                                                                        <?php echo $rwfileVersion['old_doc_name']; ?>
                                                                                        <i class="fa fa-file-image-o"></i></a>
                                                                                    <!--viewer for version tiff start-->
                                                                                <?php } else if ((strtolower($rwfileVersion['doc_extn']) == 'tif' || strtolower($rwfileVersion['doc_extn']) == 'tiff') && $rwgetRole['tif_file'] == '1') { ?>
                                                                                    <a href="tiff-viewer?id=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&i=<?php echo urlencode(base64_encode($rwfileVersion['doc_id'])); ?>"
                                                                                        target="_blank"
                                                                                        title="<?php echo $rwfileVersion['old_doc_name']; ?>">
                                                                                        <?php echo $rwfileVersion['old_doc_name']; ?> <i
                                                                                            class="fa fa-picture-o"></i></a>
                                                                                    <!--viewer for excel versioning-->
                                                                                <?php } else if (strtolower($rwfileVersion['doc_extn']) == 'xlsx' && $rwgetRole['excel_file'] == '1') {
                                                                                ?>
                                                                                    <a href="xlsx?uid=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&file=<?php echo urlencode(base64_encode($rwfileVersion['doc_id'])); ?>"
                                                                                        target="_blank"
                                                                                        title="<?php echo $rwfileVersion['old_doc_name']; ?>">
                                                                                        <?php echo $rwfileVersion['old_doc_name']; ?> <i
                                                                                            class="fa fa-file-excel-o"></i></a>
                                                                                <?php } else if (strtolower($rwfileVersion['doc_extn']) == 'xls' && $rwgetRole['excel_file'] == '1') { ?>
                                                                                    <a href="xls?uid=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&file=<?php echo urlencode(base64_encode($rwfileVersion['doc_id'])); ?>"
                                                                                        target="_blank"
                                                                                        title="<?php echo $rwfileVersion['old_doc_name']; ?>">
                                                                                        <?php echo ($rwfileVersion['old_doc_name']); ?> <i
                                                                                            class="fa fa-file-excel-o"></i></a>
                                                                                    <!--viewer for excel versioning ends -->
                                                                                    <!-- doc version viewer-->
                                                                                <?php } else if ((strtolower($rwfileVersion['doc_extn']) == 'doc' || strtolower($rwfileVersion['doc_extn']) == 'docx') && $rwgetRole['doc_file'] == '1') { ?>
                                                                                    <a href="viewword?i=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&id=<?php echo urlencode(base64_encode($rwfileVersion['doc_id'])); ?>"
                                                                                        target="_blank"
                                                                                        title="<?php echo $rwfileVersion['old_doc_name']; ?>">
                                                                                        <?php echo $rwfileVersion['old_doc_name']; ?> <i
                                                                                            class="fa fa-file-word-o"></i></a>
                                                                                    <?php if ($rwgetRole['word_edit'] == '1') { ?>
                                                                                        <!-- <a href="editword?i=<?php //echo urlencode(base64_encode($_SESSION[cdes_user_id])); 
                                                                                                                    ?>&id=<?php echo urlencode(base64_encode($rwfileVersion['doc_id'])); ?>"
                                                                                                                target="_blank"
                                                                                                                title="<?php //echo $rwfileVersion['old_doc_name']; 
                                                                                                                        ?>">
                                                                                                                <i class="ti-marker-alt"
                                                                                                                    title="<?php //echo $lang['edit_File']; 
                                                                                                                            ?>"></i></a> -->
                                                                                    <?php } ?>
                                                                                    <!--for PSD viewer version-->
                                                                                <?php } else if (strtolower($rwfileVersion['doc_extn']) == 'psd' && $rwgetRole['view_psd'] == '1') { ?>
                                                                                    <a href="viewword?i=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&id=<?php echo urlencode(base64_encode($rwfileVersion['doc_id'])); ?>"
                                                                                        target="_blank"
                                                                                        title="<?php echo $rwfileVersion['old_doc_name']; ?>">
                                                                                        <?php echo $rwfileVersion['old_doc_name']; ?><img
                                                                                            src="<?= BASE_URL ?>assets/images/psd.png"></a>

                                                                                    <!--for CDR viewer version-->
                                                                                <?php } else if (strtolower($rwfileVersion['doc_extn']) == 'cdr' && $rwgetRole['view_cdr'] == '1') { ?>
                                                                                    <a href="viewword?i=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&id=<?php echo urlencode(base64_encode($rwfileVersion['doc_id'])); ?>"
                                                                                        target="_blank"
                                                                                        title="<?php echo $rwfileVersion['old_doc_name']; ?>">
                                                                                        <?php echo $rwfileVersion['old_doc_name']; ?> <img
                                                                                            src="<?= BASE_URL ?>assets/images/cdr.png"> </a>
                                                                                    <!--for audio/video viewer version-->
                                                                                <?php } else if (strtolower($rwfileVersion['doc_extn']) == 'odt' && $rwgetRole['view_odt'] == '1') { ?>
                                                                                    <a href="viewword?i=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&id=<?php echo urlencode(base64_encode($rwfileVersion['doc_id'])); ?>"
                                                                                        target="_blank"
                                                                                        title="<?php echo $rwfileVersion['old_doc_name']; ?>">
                                                                                        <?php echo $rwfileVersion['old_doc_name']; ?> <i
                                                                                            class="fa fa-file-word-o"></i></a>
                                                                                    <!--for audio/video viewer version-->
                                                                                <?php } else if (strtolower($rwfileVersion['doc_extn']) == 'rtf' && $rwgetRole['view_rtf'] == '1') { ?>
                                                                                    <a href="viewword?i=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&id=<?php echo urlencode(base64_encode($rwfileVersion['doc_id'])); ?>"
                                                                                        target="_blank"
                                                                                        title="<?php echo $rwfileVersion['old_doc_name']; ?>">
                                                                                        <?php echo $rwfileVersion['old_doc_name']; ?> <i
                                                                                            class="fa fa-file"></i></a>
                                                                                    <!--for audio/video viewer version-->
                                                                                <?php } else if (strtolower($rwfileVersion['doc_extn']) == 'mp3' && $rwgetRole['audio_file'] == '1') { ?>
                                                                                    <a href="audioplayer?i=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&id=<?php echo urlencode(base64_encode($rwfileVersion['doc_id'])); ?>"
                                                                                        target="_blank">
                                                                                        <?php echo $rwfileVersion['old_doc_name']; ?> <i
                                                                                            class="fa fa-music"
                                                                                            title="<?php echo $lang['Audio_file']; ?>"></i></a>
                                                                                <?php } else if (strtolower($rwfileVersion['doc_extn']) == 'mp4' && $rwgetRole['video_file'] == '1') { ?>
                                                                                    <a href="video-player?i=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&id=<?php echo urlencode(base64_encode($rwfileVersion['doc_id'])); ?>"
                                                                                        target="_blank">
                                                                                        <?php echo $rwfileVersion['old_doc_name']; ?> <i
                                                                                            class="fa fa-video-camera"
                                                                                            title="<?php echo $lang['Video_file']; ?>"></i></a>

                                                                                <?php } else {
                                                                                ?>
                                                                                    <a href="downloaddoc?file=<?php echo urlencode(base64_encode($rwfileVersion['doc_id'])) ?>"
                                                                                        id="fancybox-inner" target="_blank">
                                                                                        <?php echo $rwfileVersion['old_doc_name']; ?> <i
                                                                                            class="fa fa-download"></i>
                                                                                    </a>
                                                                            <?php
                                                                                }
                                                                            }
                                                                            ?>
                                                                            </div>
                                                                        <?php } ?>
                                                                        <?php if ($railway_details['status'] == 0) {
                                                                            if ($_SESSION['cdes_user_id'] == 1 || $rwtask_masterflw['enable_edit_btn'] == '1') {
                                                                        ?>
                                                                        

                                                                            <!-- <a
                                                                                href="request_form?id=<?= base64_encode($allot_row['ticket_id']) ?>&form_type=<?= base64_encode($railway_details['railway_type']) ?>"><i
                                                                                    style="font-size:24px"
                                                                                    class="fa">&#xf040;</i></a> -->
                                                                        <?php } ?>
                                                                                    
                                                                                    <?php } ?><?php

                                                                        if (!empty($allot_row['task_remarks'])) {
                                                                        ?>

                                                                            <a href="#" data-toggle="modal"
                                                                                data-target="#taskdescription" id="ViewTsk"
                                                                                data="<?php echo $allot_row['id']; ?>"
                                                                                title="<?php echo $lang['view_Task_Description']; ?>"><i
                                                                                    class="fa fa-eye"></i></a>
                                                                            <div style="display: none"
                                                                                id="<?php echo $allot_row['id']; ?>">
                                                                                <?php echo $allot_row['task_remarks']; ?></div>
                                                                        <?php
                                                                        } else {
                                                                            echo '<span class="label label-primary">' . $lang['notask_nodescription'] . '</span>';
                                                                        }
                                                                        ?>
                                                                        <?php


                                                                        $queryyy = "SELECT * FROM tbl_railway_attachment_master WHERE ticket_id='" . $allot_row['ticket_id'] . "'";
                                                                        // print_r($queryyy);
                                                                        $resultt = mysqli_query($db_con, $queryyy);
                                                                        while ($myattachment = mysqli_fetch_assoc($resultt)) { ?>
                                                                            <a href="uploads/<?php echo htmlspecialchars($myattachment['attachment']); ?>"
                                                                                target="_blank">
                                                                                <?php echo htmlspecialchars($myattachment['attachment']); ?>
                                                                            </a>
                                                                        <?php
                                                                        } ?>
                                                                </td>
                                                                <td><?php
                                                                    $user = mysqli_query($db_con, "select first_name,last_name from tbl_user_master where user_id='$allot_row[assign_by]'");
                                                                    $rwUser = mysqli_fetch_assoc($user);
                                                                    if (!empty($rwUser['first_name'])) {
                                                                    ?>
                                                                        <label
                                                                            class="label label-success"><?php echo $rwUser['first_name'] . ' ' . $rwUser['last_name']; ?></label>
                                                                    <?php } ?>
                                                                </td>

                                                                <td><label
                                                                        class="label label-info"><?php echo date("d-m-Y H:i:s", strtotime($allot_row['start_date'])); ?></label>
                                                                </td>

                                                                <td><?php
                                                                    $tsk_order = $allot_row['task_order'];
                                                                    $taskorderNo = $tsk_order - 1;
                                                                    $user_sent = mysqli_query($db_con, "select u.first_name,u.last_name, wa.action_time from tbl_doc_assigned_wf as wa
                                                                           
                                                                        left join tbl_user_master as u on wa.action_by=u.user_id
                                                                        left join tbl_task_master as tm on wa.task_id=tm.task_id
                                                                        where wa.ticket_id='$allot_row[ticket_id]' and tm.task_order='$taskorderNo'");
                                                                    $rwUserSent = mysqli_fetch_assoc($user_sent);
                                                                    if ($tsk_order == 1 || $rwUserSent['first_name'] == NULL) {
                                                                        echo '<label class="label label-primary">' . $rwUser['first_name'] . ' ' . $rwUser['last_name'] . '</label>';
                                                                    } else {
                                                                        if (!empty($rwUserSent['first_name'])) {
                                                                            echo '<label class="label label-primary">' . $rwUserSent['first_name'] . ' ' . $rwUserSent['last_name'] . '</label>';
                                                                        }
                                                                    }
                                                                    ?>
                                                                </td>
                                                                <td><?php if ($tsk_order == '1') { ?>
                                                                        <label
                                                                            class="label label-info"><?php echo date("d-m-Y H:i:s", strtotime($allot_row['start_date'])); ?></label>
                                                                    <?php
                                                                    } else {
                                                                        if ($rwUserSent['action_time'] != NULL) {
                                                                            $actiondate = $rwUserSent['action_time'];
                                                                        } else {
                                                                            $actiondate = $allot_row['start_date'];
                                                                        }
                                                                        echo '<label class="label label-info">' . date("d-m-Y H:i:s", strtotime($actiondate)) . '<label>';
                                                                    }
                                                                    ?>

                                                                </td>

                                                                <td><?php
                                                                    if ($allot_row['deadline_type'] == 'Date') {

                                                                        $deadDate = strtotime($allot_row['start_date']) + ($allot_row['deadline'] * 60 * 60); // convert in sec
                                                                        $remainTime = $deadDate - (strtotime($date));

                                                                        // echo intdiv($remainTime, 60) . ':' . ($remainTime % 60) . ' Hrs';
                                                                        if ($remainTime > 0) {
                                                                            echo '<label class="succes label label-success">' . humanTiming($remainTime) . '</label>';
                                                                        } else {
                                                                            echo '<label class="label label-danger">0 Seconds</label>';
                                                                        }
                                                                    } else if ($allot_row['deadline_type'] == 'Days') {
                                                                        $deadDate = strtotime($allot_row['start_date']) + ($allot_row['deadline'] * 24 * 60 * 60); // convert in sec
                                                                        $remainTime = $deadDate - (strtotime($date));
                                                                        //echo round($remainTime/(24*60*60)) . ' '. $allot_row['deadline_type'];
                                                                        if ($remainTime > 0) {
                                                                            echo '<label class="succes label label-success">' . humanTiming($remainTime) . '</label>';
                                                                        } else {
                                                                            echo '<label class="label label-danger">0 Seconds</label>';
                                                                        }
                                                                    } else {
                                                                        $deadDate = strtotime($allot_row['start_date']) + ($allot_row['deadline'] * 60); //  convert in sec
                                                                        $remainTime = $deadDate - (strtotime($date));
                                                                        //echo round($remainTime/(60*60)) . ' '. $allot_row['deadline_type'];
                                                                        if ($remainTime > 0) {
                                                                            echo '<label class="succes label label-success">' . humanTiming($remainTime) . '</label>';
                                                                        } else {
                                                                            echo '<label class="error label label-danger">0 Seconds</label>';
                                                                        }
                                                                    }
                                                                    ?>
                                                                </td>
                                                                <td>
                                                                    <?php
                                                                    if ($allot_row['priority_id'] == 1) {

                                                                        echo '<span class="label label-danger">' . $urgent = 'Urgent' . '</span>';
                                                                    } else if ($allot_row['priority_id'] == 2) {
                                                                        echo '<span class="label label-warning">' . $medium = 'Medium' . '</span>';
                                                                    } else if ($allot_row['priority_id'] == 3) {
                                                                        echo '<span class="label label-primary">' . $normal = 'Normal' . '</span>';
                                                                    }
                                                                    ?>
                                                                </td>
                                                                <td><?php
                                                                    if ($allot_row['task_status'] == 'Pending') {
                                                                        echo '<span class="label label-warning">' . $allot_row['task_status'] . '</span>';
                                                                    } else if ($allot_row['task_status'] == 'Approved' || $allot_row['task_status'] == 'Complete' || $allot_row['task_status'] == 'Processed' || $allot_row['task_status'] == 'Done') {
                                                                        echo '<span class="label label-success">' . $allot_row['task_status'] . '</span>';
                                                                        if ($allot_row['NextTask'] == 0) {
                                                                            echo '<br/><span class="text-danger"> Rejected from Ahead Task <i class="fa fa-question-circle" title="Please view the comments to know the rejection reason"></i></span>';
                                                                        }
                                                                    } else if ($allot_row['task_status'] == 'Rejected') {
                                                                        echo '<span class="label label-danger">' . $allot_row['task_status'] . '</span>';
                                                                    } else if ($allot_row['task_status'] == 'Aborted') {
                                                                        echo '<span class="label label-danger">' . $allot_row['task_status'] . '</span>';
                                                                    }
                                                                    ?>
                                                                </td>
                                                            </tr>
                                                        <?php
                                                            $n++;
                                                        }
                                                        ?>
                                                    </tbody>
                                                </table>
                                                <?php
                                                echo "<center>";

                                                $prev = $start - $per_page;
                                                $next = $start + $per_page;

                                                $adjacents = 3;
                                                $last = $max_pages - 1;
                                                if ($max_pages > 1) {
                                                ?>
                                                    <ul class='pagination'>
                                                        <?php
                                                        //previous button
                                                        if (!($start <= 0))
                                                            if (isset($_GET[taskStats]) || isset($_GET[taskPrioty]) || isset($_GET[asinBy]) || isset($_GET[startDate]) || isset($_GET[endDate])) {
                                                                echo " <li><a href='?start=$prev&limit=$per_page&taskStats=$_GET[taskStats]&taskPrioty=$_GET[taskPrioty]&asinBy=$_GET[asinBy]&startDate=$_GET[startDate]&endDate=$_GET[endDate]'>" . $lang['Prev'] . "</a> </li>";
                                                            } else {
                                                                echo " <li><a href='?start=$prev&limit=$per_page'>" . $lang['Prev'] . "</a> </li>";
                                                            }
                                                        else
                                                            echo " <li class='disabled'><a href='javascript:void(0)'>" . $lang['Prev'] . "</a> </li>";
                                                        //pages
                                                        if ($max_pages < 7 + ($adjacents * 2)) {   //not enough pages to bother breaking it up
                                                            $i = 0;
                                                            for ($counter = 1; $counter <= $max_pages; $counter++) {
                                                                if ($i == $start) {
                                                                    if (isset($_GET[taskStats]) || isset($_GET[taskPrioty]) || isset($_GET[asinBy]) || isset($_GET[startDate]) || isset($_GET[endDate])) {
                                                                        echo " <li class='active'><a href='?start=$i&limit=$per_page&taskStats=$_GET[taskStats]&taskPrioty=$_GET[taskPrioty]&asinBy=$_GET[asinBy]&startDate=$_GET[startDate]&endDate=$_GET[endDate]'>$counter</a> </li>";
                                                                    } else {
                                                                        echo " <li><a href='?start=$i&limit=$per_page'>$counter</a> </li>";
                                                                    }
                                                                } else {
                                                                    if (isset($_GET[taskStats]) || isset($_GET[taskPrioty]) || isset($_GET[asinBy]) || isset($_GET[startDate]) || isset($_GET[endDate])) {
                                                                        echo " <li><a href='?start=$i&limit=$per_page&taskStats=$_GET[taskStats]&taskPrioty=$_GET[taskPrioty]&asinBy=$_GET[asinBy]&startDate=$_GET[startDate]&endDate=$_GET[endDate]'>$counter</a> </li>";
                                                                    } else {
                                                                        echo " <li><a href='?start=$i&limit=$per_page'>$counter</a> </li>";
                                                                    }
                                                                }
                                                                $i = $i + $per_page;
                                                            }
                                                        } elseif ($max_pages > 5 + ($adjacents * 2)) {    //enough pages to hide some
                                                            //close to beginning; only hide later pages
                                                            if (($start / $per_page) < 1 + ($adjacents * 2)) {
                                                                $i = 0;
                                                                for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                                                                    if ($i == $start) {
                                                                        if (isset($_GET[taskStats]) || isset($_GET[taskPrioty]) || isset($_GET[asinBy]) || isset($_GET[startDate]) || isset($_GET[endDate])) {
                                                                            echo " <li class='active'><a href='?start=$i&limit=$per_page&taskStats=$_GET[taskStats]&taskPrioty=$_GET[taskPrioty]&asinBy=$_GET[asinBy]&startDate=$_GET[startDate]&endDate=$_GET[endDate]'>$counter</a> </li>";
                                                                        } else {
                                                                            echo " <li><a href='?start=$i&limit=$per_page'>$counter</a> </li>";
                                                                        }
                                                                    } else {
                                                                        if (isset($_GET[taskStats]) || isset($_GET[taskPrioty]) || isset($_GET[asinBy]) || isset($_GET[startDate]) || isset($_GET[endDate])) {
                                                                            echo " <li><a href='?start=$i&limit=$per_page&taskStats=$_GET[taskStats]&taskPrioty=$_GET[taskPrioty]&asinBy=$_GET[asinBy]&startDate=$_GET[startDate]&endDate=$_GET[endDate]'>$counter</a> </li>";
                                                                        } else {
                                                                            echo " <li><a href='?start=$i&limit=$per_page'>$counter</a> </li>";
                                                                        }
                                                                    }
                                                                    $i = $i + $per_page;
                                                                }
                                                            }
                                                            //in middle; hide some front and some back
                                                            elseif ($max_pages - ($adjacents * 2) > ($start / $per_page) && ($start / $per_page) > ($adjacents * 2)) {
                                                                if (isset($_GET[taskStats]) || isset($_GET[taskPrioty]) || isset($_GET[asinBy]) || isset($_GET[startDate]) || isset($_GET[endDate])) {
                                                                    echo " <li class='active'><a href='?start=0&limit=$per_page&taskStats=$_GET[taskStats]&taskPrioty=$_GET[taskPrioty]&asinBy=$_GET[asinBy]&startDate=$_GET[startDate]&endDate=$_GET[endDate]'>1</a></li> ";
                                                                    echo "<li><a href='?start=$per_page&limit=$per_page&taskStats=$_GET[taskStats]&taskPrioty=$_GET[taskPrioty]&asinBy=$_GET[asinBy]&startDate=$_GET[startDate]&endDate=$_GET[endDate]'>2</a></li>";
                                                                } else {
                                                                    echo " <li><a href='?start=0&limit=$per_page'>1</a></li> ";
                                                                    echo "<li><a href='?start=$per_page&limit=$per_page'>2</a></li>";
                                                                }
                                                                echo "<li><a href='javascript:void(0)'>...</a></li>";

                                                                $i = $start;
                                                                for ($counter = ($start / $per_page) + 1; $counter < ($start / $per_page) + $adjacents + 2; $counter++) {
                                                                    if ($i == $start) {
                                                                        if (isset($_GET[taskStats]) || isset($_GET[taskPrioty]) || isset($_GET[asinBy]) || isset($_GET[startDate]) || isset($_GET[endDate])) {
                                                                            echo " <li class='active'><a href='?start=$i&limit=$per_page&taskStats=$_GET[taskStats]&taskPrioty=$_GET[taskPrioty]&asinBy=$_GET[asinBy]&startDate=$_GET[startDate]&endDate=$_GET[endDate]'>$counter</a> </li>";
                                                                        } else {
                                                                            echo " <li><a href='?start=$i&limit=$per_page'>$counter</a> </li>";
                                                                        }
                                                                    } else {
                                                                        if (isset($_GET[taskStats]) || isset($_GET[taskPrioty]) || isset($_GET[asinBy]) || isset($_GET[startDate]) || isset($_GET[endDate])) {
                                                                            echo " <li><a href='?start=$i&limit=$per_page&taskStats=$_GET[taskStats]&taskPrioty=$_GET[taskPrioty]&asinBy=$_GET[asinBy]&startDate=$_GET[startDate]&endDate=$_GET[endDate]'>$counter</a> </li>";
                                                                        } else {
                                                                            echo " <li><a href='?start=$i&limit=$per_page'>$counter</a> </li>";
                                                                        }
                                                                    }
                                                                    $i = $i + $per_page;
                                                                }
                                                            }
                                                            //close to end; only hide early pages
                                                            else {
                                                                if (isset($_GET[taskStats]) || isset($_GET[taskPrioty]) || isset($_GET[asinBy]) || isset($_GET[startDate]) || isset($_GET[endDate])) {
                                                                    echo "<li class='active'> <a href='?start=0&limit=$per_page&taskStats=$_GET[taskStats]&taskPrioty=$_GET[taskPrioty]&asinBy=$_GET[asinBy]&startDate=$_GET[startDate]&endDate=$_GET[endDate]'>1</a> </li>";
                                                                    echo "<li><a href='?start=$per_page&limit=$per_page&taskStats=$_GET[taskStats]&taskPrioty=$_GET[taskPrioty]&asinBy=$_GET[asinBy]&startDate=$_GET[startDate]&endDate=$_GET[endDate]'>2</a></li>";
                                                                } else {
                                                                    echo "<li> <a href='?start=0&limit=$per_page'>1</a> </li>";
                                                                    echo "<li><a href='?start=$per_page&limit=$per_page'>2</a></li>";
                                                                }
                                                                echo "<li><a href='javascript:void(0)'>...</a></li>";

                                                                $i = $start;
                                                                for ($counter = ($start / $per_page) + 1; $counter <= $max_pages; $counter++) {
                                                                    if ($i == $start) {
                                                                        if (isset($_GET[taskStats]) || isset($_GET[taskPrioty]) || isset($_GET[asinBy]) || isset($_GET[startDate]) || isset($_GET[endDate])) {
                                                                            echo " <li><a href='?start=$i&limit=$per_page&taskStats=$_GET[taskStats]&taskPrioty=$_GET[taskPrioty]&asinBy=$_GET[asinBy]&startDate=$_GET[startDate]&endDate=$_GET[endDate]'>$counter</a> </li>";
                                                                        } else {
                                                                            echo " <li><a href='?start=$i&limit=$per_page'>$counter</a> </li>";
                                                                        }
                                                                    } else {
                                                                        if (isset($_GET[taskStats]) || isset($_GET[taskPrioty]) || isset($_GET[asinBy]) || isset($_GET[startDate]) || isset($_GET[endDate])) {
                                                                            echo " <li><a href='?start=$i&limit=$per_page&taskStats=$_GET[taskStats]&taskPrioty=$_GET[taskPrioty]&asinBy=$_GET[asinBy]&startDate=$_GET[startDate]&endDate=$_GET[endDate]'>$counter</a> </li>";
                                                                        } else {
                                                                            echo " <li><a href='?start=$i&limit=$per_page'>$counter</a> </li>";
                                                                        }
                                                                    }
                                                                    $i = $i + $per_page;
                                                                }
                                                            }
                                                        }
                                                        //next button
                                                        if (!($start >= $foundnum - $per_page))
                                                            if (isset($_GET[taskStats]) || isset($_GET[taskPrioty]) || isset($_GET[asinBy]) || isset($_GET[startDate]) || isset($_GET[endDate])) {
                                                                echo "<li><a href='?start=$next&limit=$per_page&taskStats=$_GET[taskStats]&taskPrioty=$_GET[taskPrioty]&asinBy=$_GET[asinBy]&startDate=$_GET[startDate]&endDate=$_GET[endDate]'>" . $lang['Next'] . "</a></li>";
                                                            } else {
                                                                echo "<li><a href='?start=$next&limit=$per_page'>" . $lang['Next'] . "</a></li>";
                                                            }
                                                        else
                                                            echo "<li class='disabled'><a href='javascript:void(0)'>" . $lang['Next'] . "</a></li>";
                                                        ?>
                                                    </ul>
                                                <?php
                                                }
                                                echo "</center>";
                                                ?>

                                            </div>
                                        <?php } else {
                                        ?>
                                            <table class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th><?php echo $lang['Sr_No']; ?></th>
                                                        <th><?php echo $lang['Action']; ?></th>
                                                        <th><?php echo $lang['subject_line']; ?></th>
                                                        <th><?php echo $lang['FileName']; ?></th>
                                                        <th><?php echo $lang['initiated_by']; ?></th>
                                                        <th><?php echo $lang['initiated_date']; ?></th>
                                                        <th><?php echo $lang['sent_by']; ?></th>
                                                        <th><?php echo $lang['sent_date']; ?></th>
                                                        <th><?php echo $lang['Deadline']; ?></th>
                                                        <th><?php echo $lang['Priority']; ?></th>
                                                        <th><?php echo $lang['status']; ?></th>
                                                    </tr>
                                                </thead>
                                                <tr>
                                                    <td colspan="11">
                                                        <center><strong class="text-danger text-center"> <i
                                                                    class="ti-face-sad text-pink"></i>
                                                                <?php echo $lang['u_Hav_No_Tsk']; ?></strong></center>
                                                    </td>
                                                </tr>
                                            </table>
                                        <?php }
                                        ?>
                                        </div>
                                </div>
                                <!-- end: page -->

                            </div> <!-- end Panel -->
                        </div>
                    </div> <!-- container -->

                </div> <!-- content -->

                <div id="con-close-modal-act" class="modal fade" role="dialog" aria-labelledby="myModalLabel"
                    aria-hidden="true" style="display: none;">
                    <input type="hidden" name="tid" value="" id="tid">
                    <div class="modal-dialog modal-full" id="afterSubmt">

                        <div id="task-modal-content">

                        </div>
                    </div>
                </div><!-- /.modal -->
                <?php require_once './application/pages/footer.php'; ?>

            </div>
        </div>
        <!-- END wrapper -->
        <?php require_once './application/pages/footerForjs.php'; ?>
        <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
        <!--for filter calender-->
        <script src="assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>
        <script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>

        <script src="assets/plugins/moment/moment.js"></script>
        <script src="assets/plugins/timepicker/bootstrap-timepicker.js"></script>
        <script src="assets/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
        <script src="assets/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>


        <div id="taskdescription" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
            aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

                        <h4 class="modal-title"><?php echo $lang['Task_Description']; ?></h4>
                    </div>
                    <div class="modal-body" id="taskRemrk">
                        <img src="assets/images/load1.gif" alt="load" class="img-responsive center-block" />

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect"
                            data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                    </div>
                </div>
            </div>
        </div><!-- /.modal -->

        <script type="text/javascript">
            $(document).ready(function(e) {
                $(".taskbtn").click(function(e) {
                    $("#tid").val($(this).data('id'));
                    $("#task-modal-content").html('');
                });
            })

            $('#con-close-modal-act').on('show.bs.modal', function(e) {
                var tid = $("#tid").val();
                //alert(tid);
                var token = $("input[name='token']").val();
                $.post("<?= BASE_URL ?>application/ajax/task-process.php", {
                    tid: tid,
                    token: token
                }, function(result, status) {
                    if (status == 'success') {
                        $("#task-modal-content").html(result);
                        getToken();
                    }
                });

            });

            jQuery('#date-range').datepicker({
                toggleActive: true
            });
            $(".select2").select2();
            //firstname last name

            $("a#video").click(function() {
                var id = $(this).attr('data');
                $.post("application/ajax/videoformat.php", {
                    vid: id
                }, function(result, status) {
                    if (status == 'success') {
                        $("#videofor").html(result);
                        //alert(result);

                    }
                });
            });
            $("a#audio").click(function() {
                var id = $(this).attr('data');
                $.post("application/ajax/audioformat.php", {
                    aid: id
                }, function(result, status) {
                    if (status == 'success') {
                        $("#foraudio").html(result);
                        //alert(result);

                    }
                });
            });

            //for viewing task Description
            $("a#ViewTsk").click(function() {
                var id = $(this).attr('data');
                var result = $("#" + id).html();
                $("#taskRemrk").html(result);

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
            jQuery(document).ready(function($) {
                $("#limit1").change(function() {
                    lval = $(this).val();
                    url = removeParam("limit", url);
                    url = url + "&limit=" + lval;
                    window.open(url, "_parent");
                });
            });

            $("#reset").click(function() {
                url = removeParam("taskStats", url);
                url = removeParam("taskPrioty", url);
                url = removeParam("startDate", url);
                url = removeParam("endDate", url);
                url = removeParam("asinBy", url);
                url = removeParam("fltr", url);
                window.open(url, "_parent");
            });
        </script>

        <script>
            $(document).ready(function(e) {
                //file button validation
                $("#con-close-modal-act").delegate("#myImage1", "change", function(e) {
                    var size = document.getElementById("myImage1").files[0].size;
                    // alert(size);
                    var name = document.getElementById("myImage1").files[0].name;
                    //alert(lbl);
                    if (name.length < 100) {
                        $.post("application/ajax/valiadate_client_memory.php", {
                            size: size
                        }, function(result, status) {
                            if (status == 'success') {
                                //$("#stp").html(result);
                                var res = JSON.parse(result);
                                if (res.status == "true") {
                                    // $("#memoryres").html("<span style=color:green>" + res.msg + "</span>");
                                    // $.Notification.autoHideNotify('success', 'top center', 'Success', res.msg)
                                    $("#mem_msg").fadeIn().addClass("mem_msg_success").html(res.msg);
                                } else {
                                    $("#mem_msg").fadeIn().addClass("mem_msg_fail").html(res.msg);
                                    $("#hideOnClick").prop('disabled', true)
                                    //$.Notification.autoHideNotify('warning', 'top center', 'Oops', res.msg)
                                    //$("#memoryres").html("<span style=color:red>" + res.msg + "</span>");
                                }

                            }
                        });
                    } else {
                        var input = $("#myImage1");
                        var fileName = input.val();

                        if (fileName) { // returns true if the string is not empty
                            input.val('');
                        }
                        $.Notification.autoHideNotify('error', 'top center', 'Error', "File Name Too Long");
                    }

                });
            })
        </script>
</body>

</html>
<?php
if (isset($_POST['approveTask'], $_POST['token'])) {


    $docID = '0';
    $id = intval($_POST['tid']);
    // use fo task id
    $rwTask = mysqli_fetch_assoc(mysqli_query($db_con, "select * from tbl_doc_assigned_wf where id='$id' and (task_status='Pending' or task_status='Approved') "));

    if ($_SESSION['cdes_user_id'] != '1') {
        $work = mysqli_query($db_con, "select * from tbl_task_master where task_id='$rwTask[task_id]' and (assign_user = '$_SESSION[cdes_user_id]' or alternate_user='$_SESSION[cdes_user_id]' or supervisor='$_SESSION[cdes_user_id]')");
        if (mysqli_num_rows($work) > 0) {
            $rwWork = mysqli_fetch_assoc($work);
            $ltaskName = $rwWork['task_name'];
        } else {
            header("Location:index");
        }
    } else {
        $rwTask[task_id];
        $work = mysqli_query($db_con, "select * from tbl_task_master where task_id='$rwTask[task_id]'");
        if (mysqli_num_rows($work) > 0) {
            $rwWork = mysqli_fetch_assoc($work);
        } else {
            header("Location:index");
        }
    }
    $assignBy = $rwTask['assign_by'];
    $docID = $rwTask['doc_id'];
    $ctaskID = $rwWork['task_id'];
    $ctaskOrder = $rwWork['task_order'];
    $stepId = $rwWork['step_id'];
    $wfid = $rwWork['workflow_id'];
    $ticket = $rwTask['ticket_id'];
    $taskRemark = mysqli_real_escape_string($db_con, $rwTask['task_remarks']); // die();
    // define required variable for further use
    $getOwnTask = mysqli_query($db_con, "select * from tbl_task_master where task_id='$rwTask[task_id]'") or die('Error:' . mysqli_error($db_con));
    $rwgetOwnTask = mysqli_fetch_assoc($getOwnTask);
    $TskStpId = $rwgetOwnTask['step_id'];
    $TskWfId = $rwgetOwnTask['workflow_id'];
    $TskOrd = $rwgetOwnTask['task_order'];
    $TskAsinToId = $rwgetOwnTask['assign_user'];
    $cTaskid = $rwgetOwnTask['task_id'];
    $cTaskOrd = $TskOrd;
    $nextTskId = nextTaskToUpdate($cTaskOrd, $TskWfId, $TskStpId, $db_con);
    $getNxtTask = mysqli_query($db_con, "select * from tbl_task_master where task_id='$nextTskId'") or die('Error:' . mysqli_error($db_con));
    $rwgetNextTask = mysqli_fetch_assoc($getNxtTask);
    $rwgetNextTask['task_order'];
    $comment = mysqli_real_escape_string($db_con, $_POST['comment']);

    $tktId = $_POST['tktId'];

    $user_id = $_SESSION['cdes_user_id'];
    $taskId = $rwTask['task_id'];

    if ($_FILES['fileName']['name']) {
        // echo '<pre>'; print_r($_POST); die();

        $file_name = $_FILES['fileName']['name'];

        $allowed = ALLOWED_EXTN;
        $allowext = implode(", ", $allowed);
        $ext = pathinfo($file_name, PATHINFO_EXTENSION);
        if (!in_array(strtolower($ext), $allowed)) {

            echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '", "' . str_replace("ext", $allowext, $lang['document_allowed']) . '")</script>';
            exit();
        }

        if (strlen($file_name) < 50) {
            $file_size = $_FILES['fileName']['size'];
            $file_type = $_FILES['fileName']['type'];
            $file_tmp = $_FILES['fileName']['tmp_name'];
            $pageCount = $_POST['pageCount'];

            $extn = substr($file_name, strrpos($file_name, '.') + 1);
            $fname = substr($file_name, 0, strrpos($file_name, '.'));

            $fileExtn = substr($file_name, strrpos($file_name, ".") + 1);

            $getDocId = mysqli_query($db_con, "select * from tbl_doc_assigned_wf where id = '$id'") or die('Error:' . mysqli_error($db_con));
            $rwgetDocId = mysqli_fetch_assoc($getDocId);
            $doc_id = $rwgetDocId['doc_id'];
            $dcPath = "extract-here/" . $rwgetDocId['doc_path'];
            $getDocName = mysqli_query($db_con, "select * from tbl_document_master where doc_id = '$doc_id'") or die('Error:' . mysqli_error($db_con));
            $rwgetDocName = mysqli_fetch_assoc($getDocName);
            $docName = $rwgetDocName['doc_name'];
            $docName = explode("_", $docName);

            $updateDocName = $docName[0] . '_' . $doc_id . '_' . $docName[1];
            $chekFileVersion = mysqli_query($db_con, "SELECT * FROM `tbl_document_master` WHERE find_in_set('$updateDocName', doc_name)") or die('Error:' . mysqli_error($db_con));
            $flVersion = mysqli_num_rows($chekFileVersion);
            $flVersion = $flVersion + 1;
            // $file_name = $tktId . '_' . $flVersion . '.' . $fileExtn;


            $strgName = mysqli_query($db_con, "select * from tbl_storage_level where sl_id = '$docName[0]'") or die('Error:' . mysqli_error($db_con));
            $rwstrgName = mysqli_fetch_assoc($strgName);
            $storageName = $rwstrgName['sl_name'];
            $storageName = str_replace(" ", "", $storageName);
            $storageName = preg_replace('/[^A-Za-z0-9\-]/', '', $storageName);

            $updir = getStoragePath($db_con, $rwstrgName['sl_parent_id'], $rwstrgName['sl_depth_level']);
            if (!empty($updir)) {
                $updir = $updir . '/';
            } else {
                $updir = '';
            }
            // $uploaddir = "extract-here/" . $updir . $storageName . '/';
            $uploaddir = "uploads/";
            // print_r($uploaddir); die();
            if (!is_dir($uploaddir)) {
                mkdir($uploaddir, 0777, TRUE) or die(print_r(error_get_last()));
            }

            // $fname = preg_replace('/[^A-Za-z0-9_\-]/', '', $fname);
            // // $filenameEnct=$fname.'.'.$extn;// urlencode(base64_encode($fname)).'.'.$extn;
            // $filenameEnct = urlencode(base64_encode($fname));
            // $filenameEnct = preg_replace('/[^A-Za-z0-9_\-]/', '', $filenameEnct);
            // $filenameEnct = $filenameEnct . '.' . $extn;
            // $filenameEnct = time() . $filenameEnct;

            //  $image_path = "images/" . $file_name;
            $uploaddir = $uploaddir . $file_name;
            $upload = move_uploaded_file($file_tmp, $uploaddir) or die(print_r(error_get_last()));
            //                $logTaskName = mysqli_query($db_conn, "select task_name from tbl_task_master where task_id = '$taskId'") or die('Erorr getting Name:' . mysqli_error($db_conn));
            //                $rwlogTaskName = mysqli_fetch_assoc($logTaskName);
            //                $ltaskName = $rwlogTaskName['task_name'];

            // encypt file
            // encrypt_my_file($uploaddir);


            // $fileManager = new fileManager();
            // // Connect to file server
            // $fileManager->conntFileServer();
            // $uploadOnFtp = $fileManager->uploadFile($uploaddir, ROOT_FTP_FOLDER . '/' . $updir . $storageName . '/' . $filenameEnct);

            if ($upload) {
                $cols = '';
                $columns = mysqli_query($db_con, "SHOW COLUMNS FROM tbl_document_master");
                while ($rwCols = mysqli_fetch_array($columns)) {
                    if ($rwCols['Field'] != 'doc_id') {
                        if (empty($cols)) {
                            $cols = '`' . $rwCols['Field'] . '`';
                        } else {
                            $cols = $cols . ',`' . $rwCols['Field'] . '`';
                        }
                    }
                }

                //decrypt file
                // decrypt_my_file($uploaddir);

                //"INSERT INTO tbl_document_master($cols) select $cols from tbl_document_master where doc_id='$doc_id'";
                // $createVrsn = mysqli_query($db_con, "INSERT INTO tbl_document_master($cols) select $cols from tbl_document_master where doc_id='$doc_id'") or die('Error insert:' . mysqli_error($db_con));
                $tktId = mysqli_real_escape_string($db_con, $_POST['tktId']);

                // Fetch railway data from the database
                $railway_list = mysqli_query($db_con, "SELECT * FROM tbl_railway_master WHERE ticket_id = '$tktId'") or die('Error: ' . mysqli_error($db_con));
                $railways = mysqli_fetch_assoc($railway_list);
                $railway_id = $railways['id'];
                // echo '<pre>'; print_r($railways); die();



                // Get the current timestamp for 'created_at'
                $created_at = date('Y-m-d H:i:s');  // You can adjust the format as needed

                // Get the file name safely
                $file_name = mysqli_real_escape_string($db_con, $file_name); // Assuming the file name comes from POST

                // Get user ID from session or elsewhere
                // $user_id = mysqli_real_escape_string($db_con, $_POST['user_id']); // Assuming user ID is passed via POST

                // Insert data into tbl_railway_attachment_master
                $createVrsn = mysqli_query($db_con, "
                    INSERT INTO tbl_railway_attachment_master (requested_id, remark, attachment, created_at, created_by, ticket_id) 
                    VALUES ('$railway_id', '', '$file_name', '$created_at', '$user_id', '$tktId')
                ") or die(mysqli_error($db_con));

                // Get the inserted document ID
                $insertDocID = mysqli_insert_id($db_con);

                //***********/ create new pdf **************
                

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
                
                    .info-table{
                        border-collapse: collapse;
                        width:100%;
                    }
                    .info-table th,
                    .info-table td{
                        border:1px solid #000;
                        padding:6px;
                        font-size:12px;
                    }
                    .info-table th{
                        font-weight:bold;
                    }
                    .title{
                        text-align:center;
                        font-weight:bold;
                    }
                    </style>
                </head>
                <body>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="col-md-2" rowspan="2" style="vertical-align:middle; text-align:center;">
                                    <img src="assets/images/ecr.png" class="south_railway" alt="ECR Logo">
                                </th>
                                <th class="col-md-8" style="text-align:center; font-size:large; vertical-align:middle;">
                                    <u>REQUEST FOR INSPECTION (RFI)</u>
                                </th>';

                        if (
                            $form_type == 1
                        ) {
                            $htmlContent .= '
                                <th class="col-md-2" rowspan="2" style="vertical-align:middle; text-align:center;">
                                    <img src="assets/images/skylark_logo.jpeg" class="south_railway" alt="Contractor Logo">
                                </th>';
                        } else {
                            $htmlContent .= '
                                <th class="col-md-2" rowspan="2" style="vertical-align:middle; text-align:center;">
                                    <img src="assets/images/skylark_logo.jpeg" class="south_railway" alt="Contractor Logo">
                                </th>';
                        }

                        $htmlContent .= '
                            </tr>
                            <tr>';

                        if (
                            $form_type == 1
                        ) {
                            $htmlContent .= '
                                <th class="col-md-8" style="text-align:center; vertical-align:middle;">
                                    Major Upgradation / Redevelopment of Darbhanga Junction Railway Station in Samastipur Division, East Central Railway
                                </th>';
                        } else {
                            $htmlContent .= '
                                <th class="col-md-8" style="text-align:center; vertical-align:middle;">
                                    Major Upgradation / Redevelopment of Darbhanga Junction Railway Station in Samastipur Division, East Central Railway
                                </th>';
                        }

                        $htmlContent .= '
                            </tr>
                        </thead>
                    </table>

                    <table class="table table-bordered upper">
                        <thead>';

                        if (
                            $form_type == 1
                        ) {
                            $htmlContent .= '
                            <tr>
                                <th class="col-md-6" colspan="3" style="text-align:left; vertical-align:middle;">Client : East Central Railway</th>
                                <th class="col-md-6" colspan="3" style="text-align:center; vertical-align:middle;">Contractor : SIEPL - ALTIS (JV)</th>
                            </tr>';
                        } else {
                            $htmlContent .= '
                            <tr>
                                                                    <th class="col-md-6" colspan="3" style="text-align:left; vertical-align:middle;">Client : East Central Railway</th>

                                                                    <th class="col-md-6" colspan="3" style="text-align:center; vertical-align:middle;">
                                                                        Contractor : SIEPL - ALTIS (JV)
                                                                    </th>
                            </tr>';
                        }

                        $htmlContent .= '
                            <tr>
                                <th class="col-md-2 top-center">RFI No</th>
                                <th class="col-md-2 top-center">Structure ID</th>
                                <th class="col-md-2 top-center">Location</th>
                                <th class="col-md-2 top-center">Date</th>
                                <th class="col-md-2 top-center">Request of Inspection</th>
                                <th class="col-md-2 top-center">Inspection Required On</th>
                            </tr>
                            <tr>
                                <td class="col-md-2">' . htmlspecialchars($railways['rfi_no']) . '</td>
                                <td class="col-md-2">' . htmlspecialchars($railways['structure_id']) . '</td>
                                <td class="col-md-2">' . htmlspecialchars($railways['location']) . '</td>
                                <td class="col-md-2">' . (!empty($railways['inspection_required_date']) ? date("d-m-Y", strtotime($railways['inspection_required_date'])) : '') . '</td>
                                <td class="col-md-2">' . htmlspecialchars($railways['name_of_the_contractor']) . '</td>
                                <td class="col-md-2">' . (!empty($railways['inspected_on_date']) ? htmlspecialchars(date('d-m-Y h:i A', strtotime($railways['inspected_on_date']))) : '') . '</td>
                            </tr>
                        </thead>
                    </table>

                    <table class="table table-bordered upper">
                        <tr>
                            
                            <th class="col-md-4">Activity</th>
                            
                        </tr>
                        <tr>
                            <th>' . htmlspecialchars($railways['description_of_work']) . '</th>
                        
                        </tr>
                    </table>

                    <table width="100%" cellspacing="10">
                <tr>

                <!-- LEFT -->
                <td width="50%" valign="top">
                <table class="info-table">
                <tr>
                    <th colspan="2" class="title">Requested by</th>
                </tr>
                <tr>
                    <th width="35%" align="right">Name :</th>
                    <td width="65%">' . htmlspecialchars($railways['requested_name']) . '</td>
                </tr>
                <tr>
                    <th align="right">Agency :</th>
                    <td>' . htmlspecialchars($railways['requested_agency']) . '</td>
                </tr>
                <tr>
                    <th align="right">Date :</th>
                    <td>' . (!empty($railways['requested_date']) 
                        ? date('d-m-Y h:i A', strtotime($railways['requested_date'])) 
                        : '' ) . '</td>
                </tr>
                </table>
                </td>

                </tr>
                </table>';

                    $htmlContent .= '<div class="inspection-box mt-3" style="padding:15px; font-family:serif;">

                        <h5 style="text-decoration:underline;"><b>INSPECTION RESULTS:</b></h5>

                        <p><b>Mark to Indicate</b></p>

                        <div style="margin-left:40px;">
                            <span>Approval for Commencement of work.</span><br>

                            <span>Remedial works required as below but no further approval required.</span><br>

                            <span>Remedial works required as below but re-inspection and approval required.</span><br>
                        </div>

                        <br>

                        <label>Comments if any :</label>
                        ' . htmlspecialchars($railways['inspection_comment']) . '

                    </div>

                    <table class="table table-bordered mt-3">
                        <tr>
                            <th rowspan="2">Signature</th>
                            <th>Agency</th>
                            <th>PMC</th>
                            <th>Railway</th>
                        </tr>

                        <tr>
                            <td>' . htmlspecialchars($railways['agency_sign']) . '</td>
                            <td>' . htmlspecialchars($railways['pmc_sign']) . '</td>
                            <td>' . htmlspecialchars($railways['railway_sign']) . '</td>
                            
                        </tr>

                        <tr>
                            <th>Name</th>
                            <td>' . htmlspecialchars($railways['agency_name']) . '</td>
                            <td>' . htmlspecialchars($railways['pmc_name']) . '</td>
                            <td>' . htmlspecialchars($railways['railway_name']) . '</td>
                        </tr>

                        <tr>
                            <th>Designation</th>
                            
                                <td>' . htmlspecialchars($railways['agency_desig']) . '</td>
                            <td>' . htmlspecialchars($railways['pmc_desig']) . '</td>
                            <td>' . htmlspecialchars($railways['railway_desig']) . '</td>
                        </tr>

                        <tr>
                            <th>Date</th>
                    
                            <td>' . (!empty($railways['agency_date']) ? htmlspecialchars(date('d-m-Y', strtotime($railways['agency_date']))) : '') . '</td>
                            <td>' . (!empty($railways['pmc_date']) ? htmlspecialchars(date('d-m-Y', strtotime($railways['pmc_date']))) : '') . '</td>
                            <td>' . (!empty($railways['railway_date']) ? htmlspecialchars(date('d-m-Y', strtotime($railways['railway_date']))) : '') . '</td>
                        </tr>
                    </table>';
                $htmlContent .= '   

                    <div class="col-md-14">
                        <div class="container">
                            <div class="card-box">
                                <div id="dynamicForm">
                                    <div class="row" id="formRows">';

                        $queryyy = "SELECT * FROM tbl_railway_attachment_master WHERE requested_id='" . $railways['id'] . "'";
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

                $document_master = "SELECT * FROM tbl_document_master where ticket_id='$tktId'";
                $document_query = mysqli_query($db_con, $document_master);
                while ($document_row = mysqli_fetch_assoc($document_query)) {
                    $document_details = $document_row;
                }

                include 'exportpdf.php';
                $path = 'extract-here/' . $document_details['doc_path'];
                exportPDF($htmlContent, $path);

                //************** end this code**************

                $olddocname = base64_encode($insertDocID);
                //rename old thumbnail
                rename('thumbnail/' . base64_encode($doc_id) . '.jpg', 'thumbnail/' . $olddocname . '.jpg');
                //create thumbnail
                $newdocname = base64_encode($doc_id);
                if ($extn == 'jpg' || $extn == 'jpeg' || $extn == 'png') {
                    // createThumbnail2($uploaddir,$newdocname);
                } elseif ($extn == 'pdf') {
                    // changePdfToImage($uploaddir,$newdocname);
                }

                // if ($uploadOnFtp) {
                //     unlink($uploaddir);
                // }

                //$createVrsn = mysqli_query($db_con, "INSERT INTO tbl_document_master(doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages, dateposted) VALUES ('$updateDocName', '$file_name', '$fileExtn', 'images/$storageName/$filenameEnct', '$user_id', '$file_size', '$pageCount', '$date')") or die('Error:' . mysqli_error($db_con));
                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `doc_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null, '$doc_id','Versioning Document $file_name Added in task $ltaskName','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                // if ($createVrsn) {
                //     $docpath = $updir . $storageName . '/' . $filenameEnct;

                //     $updateNew = mysqli_query($db_con, "update tbl_document_master set doc_name='$updateDocName', workflow_id='$wfid' where doc_id='$insertDocID'");
                //     $updateOld = mysqli_query($db_con, "update tbl_document_master set old_doc_name='$file_name', workflow_id='$wfid', filename='$fname', doc_extn='$extn', doc_path='$docpath', uploaded_by='$user_id', doc_size='$file_size', noofpages='$pageCount', dateposted='$date', ftp_done='1' where doc_id='$doc_id'");
                // } else {
                //     echo '<script>taskSuccess("' . basename($_SERVER[REQUEST_URI]) . '","' . $lang['version_failed'] . '");</script>';
                // }
            } else {
                echo '<script>taskSuccess("' . basename($_SERVER[REQUEST_URI]) . '","' . $lang['Updtd_Sfly'] . '");</script>';
            }
        } else {
            echo '<script>taskFailed("' . basename($_SERVER[REQUEST_URI]) . '", "' . $lang['file_name _too_long'] . '")</script>';
        }
    }

    if (!empty($_POST['app'])) {
        $app = $_POST['app'];

        if (!empty($comment)) {
            //$user_id = $_SESSION['cdes_user_id'];
            $cmttask = "INSERT INTO tbl_task_comment (`id`, `tickt_id`, `user_id`, `comment`, task_status, `comment_time`, task_id) VALUES (null,'$tktId', '$user_id','$comment', '$app', '$date', '$taskId')";
            $run = mysqli_query($db_con, $cmttask) or die('Error query failed' . mysqli_error($db_con));
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,' Comment $comment Added in task $ltaskName','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
        }



        if ($app == 'Approved' || $app == 'Processed' || $app == 'Done') {


            $run = mysqli_query($db_con, "UPDATE tbl_doc_assigned_wf SET task_status = '$app', action_by = '$user_id', action_time = '$date' where id='$id'") or die('Error query failed' . mysqli_error($db_con));
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'$ltaskName task $app ','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
            $assignBy = $rwTask['assign_by'];

            if (!empty($rwTask['doc_id'])) {
                $docID = $rwTask['doc_id'];
            }
            $ctaskID = $rwWork['task_id'];
            $ctaskOrder = $rwWork['task_order'];
            $stepId = $rwWork['step_id'];
            $wfid = $rwWork['workflow_id'];
            $ticket = $rwTask['ticket_id'];

            $taskRemark = mysqli_real_escape_string($db_con, $rwTask['task_remarks']);

            // Call approvalWorker.php in background with cURL

            if (!isset($rwTask['letter_type']) || $rwTask['letter_type'] === '') {
                $backgroundData = array(
                    'ticket' => $ticket,
                    'ctaskOrder' => $ctaskOrder,
                    'docID' => $docID,
                    'assignBy' => $assignBy,
                    'ctaskID' => $ctaskID,
                    'stepId' => $stepId,
                    'wfid' => $wfid
                );
                
                // Make background request using cURL
                if (function_exists('curl_init')) {
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, BASE_URL . 'approvalWorker.php');
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($backgroundData));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID=' . session_id());
                    
                    $response = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    $curlError = curl_error($ch);
                    
                    if ($curlError) {
                        error_log('approvalWorker.php cURL Error: ' . $curlError);
                    }
                    if ($httpCode != 200) {
                        error_log('approvalWorker.php HTTP Error Code: ' . $httpCode . ', Response: ' . $response);
                    }
                    
                    curl_close($ch);
                }
            }

            //$tskAsinTOUsrId = $rwWork['assign_user'];

            $getTskName = mysqli_query($db_con, "select * from tbl_task_master where task_id = '$ctaskID' ") or die('Error' . mysqli_error($db_con));
            $rwgetTskName = mysqli_fetch_assoc($getTskName);

            //send sms to mob
            //                    $getMobNum = mysqli_query($db_con, "select * from tbl_user_master where user_id = '$assignBy'") or die('Error:' . mysqli_error($db_con));
            //                    $rwgetMobNum = mysqli_fetch_assoc($getMobNum);
            //                    $submtByMob = $rwgetMobNum['phone_no'];
            //                    $msg = 'Your Ticket Id ' . $ticket . ' is Approved in Task ' . $rwgetTskName['task_name'] . '.';
            //                    $sendMsgToMbl = smsgatewaycenter_com_Send($submtByMob, $msg, $debug = false);
            //
            // $tt = taskAssignToUser($db_con, $stepId, $ctaskOrder, $docID, $ctaskID, $assignBy, $id, $wfid, $ticket, $taskRemark);
            //upadte own Created user and order
            //if (!empty($_POST['asiusr']) && !empty($_POST['altrUsr']) && !empty($_POST['supvsr'])) {

            if (!empty($_POST['asiusr'])) {
                $taskOrder = $_POST['taskOrder'];
                $assiUsers = $_POST['asiusr'];
                $altrusr = $_POST['altrUsr'];
                $supvsr = $_POST['supvsr'];
                $updOwnTask = mysqli_query($db_con, "update tbl_task_master set assign_user='$assiUsers', alternate_user='$altrusr', supervisor='$supvsr', task_order='$taskOrder' where task_id = '$nextTskId'") or die('Error hhh' . mysqli_error($db_con));
                //$updOwnTask = mysqli_query($db_con, "update tbl_task_master set assign_user='$assiUsers', alternate_user='$altrusr', deadline='$deadLine', deadline_type='$deadlineType', supervisor='$supvsr', task_order='$taskOrder', task_created_date='$date' where task_id = '$taskId") or die('Error' . mysqli_error($db_con));
                //$log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Assign User order updated in $ltaskName','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
            }
            //Add new user to asign task
            //if (!empty($_POST['assignUsrAdd']) && !empty($_POST['altrUsrAdd']) && !empty($_POST['supvsrAdd']) && !empty($_POST['radio'])) {
            if (!empty($_POST['assignUsrAdd'])) {
                $assiUsersAdd = $_POST['assignUsrAdd'];
                $altrusrAdd = $_POST['altrUsrAdd'];
                $supvsrAdd = $_POST['supvsrAdd'];
                $deadlineType = $_POST['radio'];

                if ($deadlineType == 'Date') {

                    $daterange = $_POST['daterangeAdd'];

                    $daterangee = explode("To", $daterange);

                    $startDate = date('Y-m-d H:i:s', strtotime($daterangee[0]));

                    $endDate = date('Y-m-d H:i:s', strtotime($daterangee[1]));

                    $date1 = new DateTime($startDate);
                    $date2 = new DateTime($endDate);
                    //print_r($date1);
                    // print_r($date2);
                    $diff = $date1->diff($date2);

                    $deadLineAdd = $diff->h * 60 + $diff->days * 24 * 60 + $diff->i;  //convert in minute
                    //echo $deadLine=$deadLine.'.'.$diff->i;
                    //echo   $deadLine=round($deadLine/60*60,1);
                    // die('ok');
                    //echo $deadLine;
                } else if ($deadlineType == 'Days') {
                    $deadLinee = $_POST['daysAdd'];
                    $deadLineAdd = $deadLinee;
                } else if ($deadlineType == 'Hrs') {

                    $deadLinee = $_POST['hrsAdd'];
                    $deadLineAdd = $deadLinee * 60;
                }

                // echo 'ok1';
                $cTskOrd = $TskOrd;
                $cTskId = $rwTask['task_id'];
                //echo $TskWfId;
                //$TskStpId = $rwgetOwnTask['step_id'];
                //$TskWfId = $rwgetOwnTask['workflow_id'];
                //$TskOrd = $rwgetOwnTask['task_order'];
                // echo 'dedline: ';
                // echo $deadLineAdd;

                $addUsr = addNewTskUsr($cTskId, $TskWfId, $TskStpId, $cTskOrd, $assiUsersAdd, $altrusrAdd, $supvsrAdd, $deadLineAdd, $deadlineType, $date, $db_con);


                $getTask = mysqli_query($db_con, "select * from tbl_task_master where task_id = '$cTskId'") or die('Error:' . mysqli_error($db_con));
                $rwgetTask = mysqli_fetch_assoc($getTask);
                $TskStpId = $rwgetTask['step_id'];
                $TskWfId = $rwgetTask['workflow_id'];
                $TskOrd = $rwgetTask['task_order'];
                $TskAsinToId = $rwgetTask['assign_user'];
                $nextTaskOrd = $TskOrd + 1;


                nextTaskAsin($nextTaskOrd, $TskWfId, $TskStpId, $docID, $date, $user_id, $db_con, $taskRemark, $ticket);
            }





            taskAssignToUser($db_con, $stepId, $ctaskOrder, $docID, $ctaskID, $assignBy, $id, $wfid, $ticket, $taskRemark, $date);



            echo '<script>taskSuccess("myTask","Task Completed successfully !");</script>';
        } else if ($app == 'Rejected') {
            if (!empty($comment)) {
                //$ticket_query= mysqli_query($db_con, "SELECT NextTask,ticket_id FROM tbl_doc_assigned_wf where id='$id' ") or die('Error query failed pp:' . mysqli_error($db_con));
                //$row_ticket_id=mysqli_fetch_array($ticket_query);
                $run = mysqli_query($db_con, "UPDATE tbl_doc_assigned_wf SET task_status = '$app', action_by = '$user_id',action_time = '$date' where id='$id' ") or die('Error query failed pp:' . mysqli_error($db_con));

                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'$ltaskName task $app ','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                $getTskName = mysqli_query($db_con, "select * from tbl_task_master where task_id = '$ctaskID' ") or die('Error' . mysqli_error($db_con));
                $rwgetTskName = mysqli_fetch_assoc($getTskName);

                backToPrevTsk($db_con, $stepId, $ctaskOrder, $docID, $ctaskID, $assignBy, $id, $wfid, $tktId, $taskRemark, $date, $projectName);

                require_once './mail.php';
                //echo 'mail send id = '.$id; die;
                //$mail = rejectTask($id, $ctaskID, $tktId, $db_con, $projectName, $comment, $doc_id);

                if (MAIL_BY_SOCKET) {

                    $paramsArray = array(
                        'id' => $id,
                        'ctaskID' => $ctaskID,
                        'tktId' => $tktId,
                        'projectName' => $projectName,
                        'docID' => $docID,
                        'comment' => $comment,
                        'action' => 'rejectTask',
                        'approvedByIds' => $approvedByIds
                    );

                    mailBySocket($paramsArray);
                } else {

                    $mail = rejectTask($id, $ctaskID, $tktId, $db_con, $projectName, $comment, $doc_id);
                }
                //$delete = mysqli_query($db_con, "DELETE FROM tbl_doc_assigned_wf WHERE ticket_id='$row_ticket_id[ticket_id]' AND NextTask=2") or die('Error query failed pp:' . mysqli_error($db_con));
                //if ($mail) {



                //send sms to mob
                //                        $getMobNum = mysqli_query($db_con, "select * from tbl_user_master where user_id = '$assignBy'") or die('Error:' . mysqli_error($db_con));
                //                        $rwgetMobNum = mysqli_fetch_assoc($getMobNum);
                //                        $submtByMob = $rwgetMobNum['phone_no'];
                //                        $msg = 'Your Ticket Id ' . $ticket . ' is Rejected in Task ' . $rwgetTskName['task_name'] . '.';
                //                        $sendMsgToMbl = smsgatewaycenter_com_Send($submtByMob, $msg, $debug = false);
                //

                echo '<script>taskSuccess("myTask", "Task has been rejected !");</script>';
                // } else {
                //     echo '<script>taskFailed("myTask", "Opps!! Task is not rejected !")</script>';
                // }
            } else {
                echo '<script>taskFailed("process_task", "Reason is mandatory in comment")</script>';
            }
            exit();
        } else if ($app == 'Aborted') {

            $getTskName = mysqli_query($db_con, "select * from tbl_task_master where task_id = '$ctaskID' ") or die('Error' . mysqli_error($db_con));
            $rwgetTskName = mysqli_fetch_assoc($getTskName);

            $update = mysqli_query($db_con, "update tbl_doc_assigned_wf set task_status='$app', action_by='$user_id',action_time='$date',NextTask='5' where id='$id'");
            $delete = mysqli_query($db_con, "DELETE FROM tbl_doc_assigned_wf WHERE ticket_id='$tktId' AND NextTask=2") or die('Error query failed pp:' . mysqli_error($db_con));
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'$ltaskName task $app','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
            if ($update) {
                require_once './mail.php';

                if (MAIL_BY_SOCKET) {

                    $paramsArray = array(
                        'ticket' => $ticket,
                        'id' => $id,
                        'wfid' => $wfid,
                        'db_con' => $db_con,
                        'projectName' => $projectName,
                        'action' => 'abortTask'
                    );

                    mailBySocket($paramsArray);
                } else {

                    $mailSent = abortTask($ticket, $id, $wfid, $db_con, $projectName);
                }


                //if ($mailSent) {

                //send sms to mob
                //                            $getMobNum = mysqli_query($db_con, "select * from tbl_user_master where user_id = '$assignBy'") or die('Error:' . mysqli_error($db_con));
                //                            $rwgetMobNum = mysqli_fetch_assoc($getMobNum);
                //                            $submtByMob = $rwgetMobNum['phone_no'];
                //                            $msg = 'Your Ticket Id ' . $ticket . ' is Aborted in Task ' . $rwgetTskName['task_name'] . '.';
                //                            $sendMsgToMbl = smsgatewaycenter_com_Send($submtByMob, $msg, $debug = false);
                //


                echo '<script>taskSuccess("myTask", "Task has been aborted !");</script>';
                // } else {
                //     echo '<script>taskFailed("myTask", "Opps!! Task is not aborted !")</script>';
                // }
            } else {
                echo '<script>taskFailed("myTask", "Opps!! Task is not aborted !")</script>';
            }
        } else if ($app == 'Complete') {
            $run = mysqli_query($db_con, "UPDATE tbl_doc_assigned_wf SET task_status = '$app', action_by = '$user_id', action_time = '$date', NextTask='1' where id='$id'") or die('Error query failed' . mysqli_error($db_con));
            $ticket = mysqli_query($db_con, "SELECT NextTask,ticket_id FROM tbl_doc_assigned_wf where id='$id' ") or die('Error query failed pp:' . mysqli_error($db_con));
            $row_ticket_id = mysqli_fetch_array($ticket);
            $delete = mysqli_query($db_con, "DELETE FROM tbl_doc_assigned_wf WHERE ticket_id='$row_ticket_id[ticket_id]' AND NextTask=2") or die('Error query failed pp:' . mysqli_error($db_con));
            if ($delete) {
                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'$ltaskName task $app ','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                $assignBy = $rwTask['assign_by'];

                if (!empty($rwTask['doc_id'])) {
                    $docID = $rwTask['doc_id'];
                }
                $ctaskID = $rwWork['task_id'];
                $ctaskOrder = $rwWork['task_order'];
                $stepId = $rwWork['step_id'];
                $wfid = $rwWork['workflow_id'];
                $ticket = $rwTask['ticket_id'];

                $taskRemark = mysqli_real_escape_string($db_con, $rwTask['task_remarks']);

                //$tskAsinTOUsrId = $rwWork['assign_user'];
                if (!empty($docID)) {
                    $updateDocMaster = mysqli_query($db_con, "update tbl_document_master set doc_name=replace(doc_name,'_$wfid','') where doc_id='$docID'");
                    //$update = mysqli_query($db_con, "update tbl_document_master set doc_name=replace(doc_name,'_$wfid','') where substring_index(doc_name,'_',-2)=$docID");
                    $update = mysqli_query($db_con, "update tbl_document_master set doc_name=CONCAT(TRIM(TRAILING '_$wfid' FROM doc_name), '') where substring_index(doc_name,'_',-2)=$docID");
                    //view version in storage after workflow complete
                }


                $getTskName = mysqli_query($db_con, "select * from tbl_task_master where task_id = '$ctaskID' ") or die('Error' . mysqli_error($db_con));
                $rwgetTskName = mysqli_fetch_assoc($getTskName);
                require_once './mail.php';


                if (MAIL_BY_SOCKET) {

                    $paramsArray = array(
                        'ticket' => $ticket,
                        'id' => $id,
                        'wfid' => $wfid,
                        'db_con' => $db_con,
                        'projectName' => $projectName,
                        'action' => 'completeTask',
                        'user_id' => $_SESSION['cdes_user_id']
                    );

                    mailBySocket($paramsArray);
                } else {

                    $mailSent = completeTask($ticket, $id, $wfid, $db_con, $projectName);
                }

                //if ($mailSent) {
                echo '<script>taskSuccess("myTask","Task Completed successfully !");</script>';
                // } else {
                //     echo '<script>taskFailed("myTask","Task Completion Failed !");</script>';
                // }

                //taskAssignToUser($db_con, $stepId, $ctaskOrder, $docID, $ctaskID, $assignBy, $id, $wfid, $ticket, $taskRemark, $date);
            } else {
                echo '<script>taskFailed("myTask","Next Task Deletion Failed !");</script>';
            }
        }
    }


    mysqli_close($db_con);
}

//end own user created and order

function taskAssignToUser($db_con, $stepId, $ctaskOrder, $docID, $ctaskID, $assignBy, $id, $wfid, $ticket, $taskRemark, $date)
{

    $nextTaskIds = array();
    require_once './application/pages/sendSms.php';
    require_once './mail.php';
    //echo "stepId :";
    // echo $stepId;
    $checkTaskNext = mysqli_query($db_con, "select * from tbl_task_master where step_id='$stepId' ORDER BY task_order");
    $k = 0;
    while ($rwCheckTask = mysqli_fetch_assoc($checkTaskNext)) {
        if ($rwCheckTask['task_order'] > $ctaskOrder) {
            array_push($nextTaskIds, $rwCheckTask['task_id']);
            $k++;
        }
        if ($k > 1) {
            break;
        }
    }


    //print_r($nextTaskIds);
    if (!empty($nextTaskIds)) {

        $i = 0;
        foreach ($nextTaskIds as $nextTaskId) {
            //echo "next task id: ";
            //echo $nextTaskId;
            $nxtTaskDetail = mysqli_query($db_con, "select * from tbl_task_master where task_id='$nextTaskId'");

            if (mysqli_num_rows($nxtTaskDetail) > 0) {
                $rwNxtTaskDeatil = mysqli_fetch_assoc($nxtTaskDetail);

                if ($rwNxtTaskDeatil['deadline_type'] == 'Days') {
                    $endDate = date('Y-m-d H:i:s', (strtotime($date) + ($rwNxtTaskDeatil['deadline'] * 24 * 60 * 60)));
                } else if ($rwTaskn['deadline_type'] == 'Date') {
                    $endDate = date('Y-m-d H:i:s', (strtotime($date) + ($rwTaskn['deadline'] * 60)));
                } else {
                    $endDate = date('Y-m-d H:i:s', (strtotime($date) + ($rwTaskn['deadline'] * 60 * 60)));
                }

                $taskCheck = mysqli_query($db_con, "select * from tbl_doc_assigned_wf where task_id='$nextTaskId' and doc_id='$docID' and ticket_id = '$ticket'");

                if (mysqli_num_rows($taskCheck) < 1) {
                    //echo $nextTaskId; die();
                    if ($i == 0) { //insert to next task
                        if (!empty($docID) && $docID != 0) {
                            $assignToNextWf = mysqli_query($db_con, "insert into tbl_doc_assigned_wf(`id`, `task_id`, `doc_id`, `start_date`, `end_date`, `task_status`, `assign_by`,`NextTask`,`ticket_id`,`task_remarks`)"
                                . "values(null,'$nextTaskId','$docID','$date','$endDate','Pending','$assignBy','0','$ticket','$taskRemark')") or die('Error to move next 1' . mysqli_error($db_con));
                        } else {
                            $assignToNextWf = mysqli_query($db_con, "insert into tbl_doc_assigned_wf(`id`, `task_id`, `start_date`, `end_date`, `task_status`, `assign_by`,`NextTask`,`ticket_id`,`task_remarks`)"
                                . "values(null,'$nextTaskId','$date','$endDate','Pending','$assignBy','0','$ticket','$taskRemark')") or die('Error to move next 2' . mysqli_error($db_con));
                        }
                    } else if ($i == 1) {
                        if (!empty($docID) && $docID != 0) {
                            $assignToNextWf = mysqli_query($db_con, "insert into tbl_doc_assigned_wf(`id`, `task_id`, `doc_id`, `start_date`, `end_date`, `task_status`, `assign_by`,`NextTask`,`ticket_id`,`task_remarks`)"
                                . "values(null,'$nextTaskId','$docID','$date','$endDate','Pending','$assignBy','2','$ticket','$taskRemark')") or die('Error to move next3' . mysqli_error($db_con));
                        } else {
                            $assignToNextWf = mysqli_query($db_con, "insert into tbl_doc_assigned_wf(`id`, `task_id`, `start_date`, `end_date`, `task_status`, `assign_by`,`NextTask`,`ticket_id`,`task_remarks`)"
                                . "values(null,'$nextTaskId','$date','$endDate','Pending','$assignBy','2','$ticket','$taskRemark')") or die('Error to move next4' . mysqli_error($db_con));
                        }
                    }
                    $idnxt = mysqli_insert_id($db_con);
                    if ($assignToNextWf) {
                        //update current task flag and completion time
                        $update = mysqli_query($db_con, "update tbl_doc_assigned_wf set NextTask='1' , end_date='$endDate' where id='$id'") or die('Error to update old' . mysqli_error($db_con));
                        echo "test1";

                        // assignTask($ticket, $idnxt, $db_con, $projectName);

                        if (MAIL_BY_SOCKET) {

                            $paramsArray = array(
                                'ticket' => $ticket,
                                'idins' => $idnxt,
                                'db_con' => $db_con,
                                'projectName' => $projectName,
                                'action' => 'assignTask'
                            );
                            mailBySocket($paramsArray);
                        } else {

                            $mail = assignTask($ticket, $idnxt, $db_con, $projectName);
                        }

                        //send sms to mob task asin to user

                        $getNextTaskId = mysqli_query($db_con, "select * from tbl_task_master where task_id = '$nextTaskId'") or die('Error:' . mysqli_error($db_con));
                        $rwgetNextTaskId = mysqli_fetch_assoc($getNextTaskId);
                        $taskName = $rwgetNextTaskId['task_name'];

                        //                                $getMobNumAsinTo = mysqli_query($db_con, "select * from tbl_user_master where user_id = '$rwgetNextTaskId[assign_user]'") or die('Error:' . mysqli_error($db_con));
                        //                                $rwgetMobNumAsinTo = mysqli_fetch_assoc($getMobNumAsinTo);
                        //                                $AsinToMob = $rwgetMobNumAsinTo['phone_no'];
                        //                                $msgAsinTo = 'New Task With Ticket Id ' . $ticket . ' and Task Name ' . $taskName . ' has been assingned to you.';
                        //                                $sendMsgToMbl = smsgatewaycenter_com_Send($AsinToMob, $msgAsinTo, $debug = false);
                        //
                    }
                } else {
                    echo "test2";
                    if ($i == 0) {

                        $assignToNextWf = mysqli_query($db_con, "update tbl_doc_assigned_wf set NextTask='0' , task_status='Pending' where task_id='$nextTaskId' and doc_id='$docID' and ticket_id = '$ticket'") or die('Error to move next update' . mysqli_error($db_con));
                    } else {
                        $assignToNextWf = mysqli_query($db_con, "update tbl_doc_assigned_wf set NextTask='2' , task_status='Pending' where task_id='$nextTaskId' and doc_id='$docID' and ticket_id = '$ticket'") or die('Error to move next update' . mysqli_error($db_con));
                    }
                    $rwtaskCheck = mysqli_fetch_assoc($taskCheck);
                    $idnxt = $rwtaskCheck['id'];

                    if ($assignToNextWf) {
                        $update = mysqli_query($db_con, "update tbl_doc_assigned_wf set NextTask='1' , end_date='$endDate' where id='$id'");
                        //assignTask($ticket, $idnxt, $db_con, $projectName);

                        if (MAIL_BY_SOCKET) {

                            $paramsArray = array(
                                'ticket' => $ticket,
                                'idins' => $idnxt,
                                'db_con' => $db_con,
                                'projectName' => $projectName,
                                'action' => 'assignTask'
                            );
                            mailBySocket($paramsArray);
                        } else {

                            $mail = assignTask($ticket, $idnxt, $db_con, $projectName);
                        }

                        //send sms to mob task asin to user

                        $getNextTaskId = mysqli_query($db_con, "select * from tbl_task_master where task_id = '$nextTaskId'") or die('Error:' . mysqli_error($db_con));
                        $rwgetNextTaskId = mysqli_fetch_assoc($getNextTaskId);
                        $taskName = $rwgetNextTaskId['task_name'];


                        //                                $getMobNumAsinTo = mysqli_query($db_con, "select * from tbl_user_master where user_id = '$rwgetNextTaskId[assign_user]'") or die('Error:' . mysqli_error($db_con));
                        //                                $rwgetMobNumAsinTo = mysqli_fetch_assoc($getMobNumAsinTo);
                        //                                $AsinToMob = $rwgetMobNumAsinTo['phone_no'];
                        //                                $msgAsinTo = 'New Task With Ticket Id ' . $ticket . ' and Task Name ' . $taskName . ' has been assingned to you.';
                        //                                $sendMsgToMbl = smsgatewaycenter_com_Send($AsinToMob, $msgAsinTo, $debug = false);
                        //
                    }
                }
            }
            $i++;
            //echo 'kkk'.$nextTaskId.$docID; die();
        }
    } else {

        $nextStepIds = array();
        $stepo = mysqli_query($db_con, "select * from tbl_step_master where step_id='$stepId'");
        $rwStepo = mysqli_fetch_assoc($stepo);
        $step = mysqli_query($db_con, "select * from tbl_step_master where workflow_id='$wfid'");
        $s = 0;
        while ($rwStep = mysqli_fetch_assoc($step)) {
            //echo $rwStep['step_id'].'/'.$rwStep['step_order'].'<br>';
            //echo $rwStepo['step_id'].'/'.$rwStepo['step_order'];
            if ($rwStep['step_order'] > $rwStepo['step_order']) {
                array_push($nextStepIds, $rwStep['step_id']);
                $s++;
            }
            if ($s > 1) {
                break;
            }
        }

        //print_r($nextStepIds);

        if (!empty($nextStepIds)) {

            $i = 0;
            foreach ($nextStepIds as $nextStepId) {
                $taskn = mysqli_query($db_con, "select * from tbl_task_master where step_id='$nextStepId' order by task_order asc limit 2");

                if (mysqli_num_rows($taskn) > 0) {

                    while ($rwTaskn = mysqli_fetch_assoc($taskn)) {

                        echo $nextTaskId = $rwTaskn['task_id'];

                        if ($rwTaskn['deadline_type'] == 'Days') {
                            $endDate = date('Y-m-d H:i:s', (strtotime($date) + ($rwTaskn['deadline'] * 24 * 60 * 60)));
                        } else if ($rwTaskn['deadline_type'] == 'Date') {
                            $endDate = date('Y-m-d H:i:s', (strtotime($date) + ($rwTaskn['deadline'] * 60)));
                        } else {
                            $endDate = date('Y-m-d H:i:s', (strtotime($date) + ($rwTaskn['deadline'] * 60 * 60)));
                        }

                        $taskCheck = mysqli_query($db_con, "select * from tbl_doc_assigned_wf where task_id='$nextTaskId' and doc_id='$docID' and ticket_id = '$ticket'") or die('Error:' . mysqli_error($db_con));
                        //echo 'helo';
                        // mysqli_num_rows($taskCheck);

                        if (mysqli_num_rows($taskCheck) < 1) {
                            echo 'ok ' . $i . ' ' . $docID;
                            if ($i == 0) {
                                if (!empty($docID) && $docID != 0) { //echo $endDate;
                                    $assignToNextWf = mysqli_query($db_con, "insert into tbl_doc_assigned_wf(`id`, `task_id`, `doc_id`, `start_date`, `end_date`, `task_status`, `assign_by`,`NextTask`,`ticket_id`,`task_remarks`)"
                                        . "values(null,'$nextTaskId','$docID','$date','$endDate','Pending','$assignBy','0','$ticket','$taskRemark')") or die('Error to move next5' . mysqli_error($db_con));
                                } else {
                                    $assignToNextWf = mysqli_query($db_con, "insert into tbl_doc_assigned_wf(`id`, `task_id`, `start_date`, `end_date`, `task_status`, `assign_by`,`NextTask`,`ticket_id`,`task_remarks`)"
                                        . "values(null,'$nextTaskId','$date','$endDate','Pending','$assignBy','0','$ticket','$taskRemark')") or die('Error to move next6' . mysqli_error($db_con));
                                }
                            } else if ($i == 1) {
                                if (!empty($docID) && $docID != 0) {
                                    $assignToNextWf = mysqli_query($db_con, "insert into tbl_doc_assigned_wf(`id`, `task_id`, `doc_id`, `start_date`, `end_date`, `task_status`, `assign_by`,`NextTask`,`ticket_id`,`task_remarks`)"
                                        . "values(null,'$nextTaskId','$docID','$date','$endDate','Pending','$assignBy','2','$ticket','$taskRemark')") or die('Error to move next7' . mysqli_error($db_con));
                                } else {
                                    $assignToNextWf = mysqli_query($db_con, "insert into tbl_doc_assigned_wf(`id`, `task_id`, `start_date`, `end_date`, `task_status`, `assign_by`,`NextTask`,`ticket_id`,`task_remarks`)"
                                        . "values(null,'$nextTaskId','$date','$endDate','Pending','$assignBy','0','$ticket','$taskRemark')") or die('Error to move next 8' . mysqli_error($db_con));
                                }
                            }
                            $idnxt = mysqli_insert_id($db_con);
                            if ($assignToNextWf) {
                                $update = mysqli_query($db_con, "update tbl_doc_assigned_wf set NextTask='1' , end_date='$endDate' where id='$id'");
                                //assignTask($ticket, $idnxt, $db_con, $projectName);


                                if (MAIL_BY_SOCKET) {

                                    $paramsArray = array(
                                        'ticket' => $ticket,
                                        'idins' => $idnxt,
                                        'db_con' => $db_con,
                                        'projectName' => $projectName,
                                        'action' => 'assignTask'
                                    );
                                    mailBySocket($paramsArray);
                                } else {

                                    $mail = assignTask($ticket, $idnxt, $db_con, $projectName);
                                }

                                //send sms to mob task asin to user

                                $getNextTaskId = mysqli_query($db_con, "select * from tbl_task_master where task_id = '$nextTaskId'") or die('Error:' . mysqli_error($db_con));
                                $rwgetNextTaskId = mysqli_fetch_assoc($getNextTaskId);
                                $taskName = $rwgetNextTaskId['task_name'];

                                //                                        $getMobNumAsinTo = mysqli_query($db_con, "select * from tbl_user_master where user_id = '$rwgetNextTaskId[assign_user]'") or die('Error:' . mysqli_error($db_con));
                                //                                        $rwgetMobNumAsinTo = mysqli_fetch_assoc($getMobNumAsinTo);
                                //                                        $AsinToMob = $rwgetMobNumAsinTo['phone_no'];
                                //                                        $msgAsinTo = 'New Task With Ticket Id ' . $ticket . ' and Task Name ' . $taskName . ' has been assingned to you.';
                                //                                        $sendMsgToMbl = smsgatewaycenter_com_Send($AsinToMob, $msgAsinTo, $debug = false);
                                //
                            }
                        } else {

                            if ($i == 0) {

                                $assignToNextWf = mysqli_query($db_con, "update tbl_doc_assigned_wf set NextTask='0', task_status='Pending' where task_id='$nextTaskId' and doc_id='$docID' and ticket_id = '$ticket'");
                            } else {

                                $assignToNextWf = mysqli_query($db_con, "update tbl_doc_assigned_wf set NextTask='2' where task_id='$nextTaskId' and doc_id='$docID' and ticket_id = '$ticket'");
                            }
                            $rwtaskCheck = mysqli_fetch_assoc($taskCheck);
                            $idnxt = $rwtaskCheck['id'];
                            if ($assignToNextWf) {
                                $update = mysqli_query($db_con, "update tbl_doc_assigned_wf set NextTask='1' , end_date='$endDate' where id='$id'");
                                //assignTask($ticket, $idnxt, $db_con, $projectName);

                                if (MAIL_BY_SOCKET) {

                                    $paramsArray = array(
                                        'ticket' => $ticket,
                                        'idins' => $idnxt,
                                        'db_con' => $db_con,
                                        'projectName' => $projectName,
                                        'action' => 'assignTask'
                                    );
                                    mailBySocket($paramsArray);
                                } else {

                                    $mail = assignTask($ticket, $idnxt, $db_con, $projectName);
                                }


                                //send sms to mob task asin to user
                                //                                        $getNextTaskId = mysqli_query($db_con, "select * from tbl_task_master where task_id = '$nextTaskId'") or die('Error:' . mysqli_error($db_con));
                                //                                        $rwgetNextTaskId = mysqli_fetch_assoc($getNextTaskId);
                                //                                        $taskName = $rwgetNextTaskId['task_name'];
                                //
                                //                                        $getMobNumAsinTo = mysqli_query($db_con, "select * from tbl_user_master where user_id = '$rwgetNextTaskId[assign_user]'") or die('Error:' . mysqli_error($db_con));
                                //                                        $rwgetMobNumAsinTo = mysqli_fetch_assoc($getMobNumAsinTo);
                                //                                        $AsinToMob = $rwgetMobNumAsinTo['phone_no'];
                                //
                                //                                        $msgAsinTo = 'New Task With Ticket Id ' . $ticket . ' and Task Name ' . $taskName . ' has been assingned to you.';
                                //                                        $sendMsgToMbl = smsgatewaycenter_com_Send($AsinToMob, $msgAsinTo, $debug = false);
                                //
                            }
                        }
                    }
                }
                if (mysqli_num_rows($taskn) > 1) {
                    break;
                }
                $i++;
            }
        } else {
            $assignToNextWf = mysqli_query($db_con, "update tbl_doc_assigned_wf set NextTask='1' where id='$id'");
            if ($assignToNextWf) {
                'doc id' . $docID;
                if (!empty($docID)) {
                    $updateDocMaster = mysqli_query($db_con, "update tbl_document_master set doc_name=replace(doc_name,'_$wfid','') where doc_id='$docID'");
                    //$update = mysqli_query($db_con, "update tbl_document_master set doc_name=replace(doc_name,'_$wfid','') where substring_index(doc_name,'_',-2)=$docID");
                    $update = mysqli_query($db_con, "update tbl_document_master set doc_name=CONCAT(TRIM(TRAILING '_$wfid' FROM doc_name), '') where substring_index(doc_name,'_',-2)=$docID");
                    //view version in storage after workflow complete
                }
                //echo "run123";


                if (MAIL_BY_SOCKET) {

                    $paramsArray = array(
                        'ticket' => $ticket,
                        'id' => $id,
                        'wfid' => $wfid,
                        'db_con' => $db_con,
                        'projectName' => $projectName,
                        'action' => 'completeTask',
                        'user_id' => $_SESSION['cdes_user_id']
                    );

                    mailBySocket($paramsArray);
                } else {

                    $mailSent = completeTask($ticket, $id, $wfid, $db_con, $projectName);
                }
                //completeTask($ticket, $id, $wfid, $db_con, $projectName);
                //send sms to mob
                //                        $getMobNum = mysqli_query($db_con, "select * from tbl_user_master where user_id = '$assignBy'") or die('Error:' . mysqli_error($db_con));
                //                        $rwgetMobNum = mysqli_fetch_assoc($getMobNum);
                //                        $submtByMob = $rwgetMobNum['phone_no'];
                //                        $msg = 'Your Ticket Id ' . $ticket . ' is Approved Successfully.';
                //                        $sendMsgToMbl = smsgatewaycenter_com_Send($submtByMob, $msg, $debug = false);
                //
                return TRUE;
            }
        }
    }
}

//back to prev task when reject
function backToPrevTsk($db_con, $stepId, $ctaskOrder, $docID, $ctaskID, $assignBy, $id, $wfid, $ticket, $taskRemark, $date, $projectName)
{
    $nextTaskIds = array();

    require_once './mail.php';
    $checkTaskNext = mysqli_query($db_con, "select * from tbl_task_master where step_id='$stepId' order by task_order desc");
    $k = 0;
    while ($rwCheckTask = mysqli_fetch_assoc($checkTaskNext)) {
        if ($rwCheckTask['task_order'] < $ctaskOrder) {
            array_push($nextTaskIds, $rwCheckTask['task_id']);
            $k++;
        }
        if ($k > 0) {
            break;
        }
    }

    if (!empty($nextTaskIds)) {
        foreach ($nextTaskIds as $nextTaskId) {

            echo '1->' . $nextTaskId;

            $setflg = mysqli_query($db_con, "update tbl_doc_assigned_wf set NextTask='2' where id = '$id'") or die('Error:' . mysqli_error($db_con));
            $updateTaskPrev = mysqli_query($db_con, "UPDATE tbl_doc_assigned_wf SET task_status = 'Approved', action_by = '$_SESSION[cdes_user_id]', action_time = '$date', NextTask = '0' where task_id='$nextTaskId' and ticket_id = '$ticket' ") or die('Error query failed 1 ' . mysqli_error($db_con));
        }
    } else {
        $nextStepIds = array();
        $stepo = mysqli_query($db_con, "select * from tbl_step_master where step_id='$stepId'");
        $rwStepo = mysqli_fetch_assoc($stepo);
        $step = mysqli_query($db_con, "select * from tbl_step_master where workflow_id='$wfid' order by step_order desc");
        $s = 0;
        while ($rwStep = mysqli_fetch_assoc($step)) {
            if ($rwStep['step_order'] < $rwStepo['step_order']) {
                array_push($nextStepIds, $rwStep['step_id']);
                $s++;
            }
            if ($s > 1) {
                break;
            }
        }

        //print_r($nextStepIds);

        if (!empty($nextStepIds)) {


            foreach ($nextStepIds as $nextStepId) {

                $taskn = mysqli_query($db_con, "select * from tbl_task_master where step_id='$nextStepId' order by task_order desc limit 1");

                if (mysqli_num_rows($taskn) > 0) {

                    $getPrevTskId = mysqli_fetch_assoc($taskn);
                    $setflg = mysqli_query($db_con, "update tbl_doc_assigned_wf set NextTask='2' where id = '$id'") or die('Error to move next update' . mysqli_error($db_con));
                    $updateTaskPrev = mysqli_query($db_con, "UPDATE tbl_doc_assigned_wf SET task_status = 'Approved', action_by = '$_SESSION[cdes_user_id]', action_time = '$date', NextTask = '0' where task_id='$getPrevTskId[task_id]' and ticket_id = '$ticket' ") or die('Error query failed2' . $_SESSION[cdes_user_id] . mysqli_error($db_con));
                }
            }
        } else {
            $setflg = mysqli_query($db_con, "update tbl_doc_assigned_wf set NextTask='2' where id = '$id'") or die('Error to move next update' . mysqli_error($db_con));
        }
    }
}


?>