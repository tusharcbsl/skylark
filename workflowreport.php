<!DOCTYPE html>
<html>
    <?php
    error_reporting(0);
    //$path = $_SERVER['REQUEST_URI'];
    // $uri = $path;

    require_once './loginvalidate.php';
    require_once './sessionstart.php';
    require_once './application/config/database.php';
    require_once './application/pages/head.php';
    require_once './application/pages/function.php';

    // echo $rwgetRole['dashboard_mydms']; die;
    if ($rwgetRole['view_report'] != '1') {
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
    ?>
    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <script src="https://www.google.com/jsapi" type="text/javascript">
    </script>  
    <script type="text/javascript">

        // Load the Google Transliterate API
        google.load("elements", "1", {
            packages: "transliteration"
        });

        function onLoad() {
            var langcode = '<?php echo $rwgetRole['langCode']; ?>';
            var options = {
                sourceLanguage: 'en',
                destinationLanguage: [langcode],
                shortcutKey: 'ctrl+g',
                transliterationEnabled: true
            };
            // Create an instance on TransliterationControl with the required
            // options.
            var control =
                    new google.elements.transliteration.TransliterationControl(options);

            // Enable transliteration in the text fields with the given ids.
            var ids = ["sear", "ser1"];
            control.makeTransliteratable(ids);


            // Show the transliteration control which can be used to toggle between
            // English and Hindi and also choose other destination language.
            // control.showControl('translControl');

        }
        google.setOnLoadCallback(onLoad);

    </script> 	
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
                                <?php
                                $rid = base64_decode(urldecode($_GET['rid']));
                                $wfid = base64_decode(urldecode($_GET['wfid']));
                                $wftblqry = mysqli_query($db_con, "select * from  tbl_workflow_master where workflow_id='$wfid'");
                                $rows = mysqli_fetch_assoc($wftblqry);
                                $qryform = mysqli_query($db_con, "select * from tbl_bridge_workflow_to_form where workflow_id='$wfid'");
                                $res = mysqli_fetch_assoc($qryform);
                                $formid = $res["form_id"];
                                ?>
                                <ol class="breadcrumb">
                                    <li>
                                        <a href="workflowreport"><?php echo $lang['WORKFLOW_REPORT']; ?></a>
                                    </li>
                                    <li class="active"><?= $rows['workflow_name'] ?></li>
                                    
                                    <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                                </ol>
                            </div>
                        </div>
                        <div class="box box-primary">
                            <div class="panel">
                                <div class="panel-body">
                                    <div class="box-body">
                                        <?php
                                        $wftblqry = mysqli_query($db_con, "select * from  tbl_workflow_master where workflow_id='$wfid'") or die(mysqli_error($db_con));
                                        if (mysqli_num_rows($wftblqry) > 0) {
                                            $dataContent = mysqli_fetch_assoc($wftblqry);
                                            $tblname = $dataContent['form_tbl_name'];
                                            $poFlag = $dataContent['po_flag'];
                                            $qry = mysqli_query($db_con, "select * from tbl_wf_reports where rp_id='$rid' and wf_id='$wfid'")or die(mysqli_error($db_con));
                                            $rowdata = mysqli_fetch_assoc($qry);
                                            $recol = $rowdata['coloums'];
                                            $recol = explode(",", $recol);
                                            $metaCount = count($recol);
                                            $coloums = $rowdata['coloums'];

                                            $newcoloums = $coloums . "," . "tbl_id";
                                            $cashVoucher = false;

                                            // print_r($recol);
                                            if (in_array('wf_devision', $recol)) {

                                                $cashVoucher = true;
                                            }
                                            if (!empty($coloums)) { 
                                                if (mysqli_num_rows($qry) > 0) {
                                                    
                                                   

                                                    if (!empty($_GET['colname']) && !empty($_GET['search'])) {
                                                         

                                                        $where = " where";

                                                        for ($k = 0; $k < count($_GET['colname']); $k++) {

                                                            if ($k == 0) {
                                                                if ($_GET['colname'][$k] == "wf_devision") {
                                                                    $where .= " d.division_name LIKE " . "'%" . $_GET['search'][$k] . "%'";
                                                                } else if ($_GET['colname'][$k] == "wf_project") {
                                                                    $where .= " p.project_name LIKE " . "'%" . $_GET['search'][$k] . "%'";
                                                                } else if ($_GET['colname'][$k] == "action_by") {
                                                                    $where .= " CONCAT(um.first_name, ' ', um.last_name) LIKE" . "'%" . $_GET['search'][$k] . "%'";
                                                                } else if ($_GET['colname'][$k] == "assign_by") {
                                                                    $assign_by = " where CONCAT(first_name, ' ', last_name) LIKE " . "'%" . $_GET['search'][$k] . "%'";
                                                                    $user = mysqli_query($db_con, "select GROUP_CONCAT(user_id) as assgnbyid from tbl_user_master  $assign_by");
                                                                    $rwUser = mysqli_fetch_assoc($user);
                                                                    $where .= " FIND_IN_SET(tbl_doc_assigned_wf.assign_by,'" . $rwUser['assgnbyid'] . "')";
                                                                } else if ($_GET['colname'][$k] == "first_name") {
                                                                    $where .= " um.first_name LIKE" . "'%" . $_GET['search'][$k] . "%' ";
                                                                } else {
                                                                    $where .= " " . $_GET['colname'][$k] . " LIKE " . "'%" . $_GET['search'][$k] . "%'";
                                                                }
                                                            } else {
                                                                if ($_GET['colname'][$k] == "wf_devision") {
                                                                    $where .= " AND d.division_name LIKE " . "'%" . $_GET['search'][$k] . "%'";
                                                                } else if ($_GET['colname'][$k] == "wf_project") {
                                                                    $where .= " AND p.project_name LIKE " . "'%" . $_GET['search'][$k] . "%'";
                                                                } else if ($_GET['colname'][$k] == "action_by") {
                                                                    $where .= " AND CONCAT(um.first_name, ' ', um.last_name) LIKE " . "'%" . $_GET['search'][$k] . "%'";
                                                                } else if ($_GET['colname'][$k] == "assign_by") {
                                                                    $assign_by = " where CONCAT(first_name, ' ', last_name) LIKE " . "'%" . $_GET['search'][$k] . "%'";
                                                                    $user = mysqli_query($db_con, "select GROUP_CONCAT(user_id) as assgnbyid from tbl_user_master  $assign_by");
                                                                    $rwUser = mysqli_fetch_assoc($user);
                                                                    $where .= " AND FIND_IN_SET(tbl_doc_assigned_wf.assign_by,'" . $rwUser['assgnbyid'] . "')";
                                                                } else if ($_GET['colname'][$k] == "first_name") {
                                                                    $where .= " um.first_name LIKE" . "'%" . $_GET['search'][$k] . "%' ";
                                                                } else {
                                                                    $where .= " AND " . $_GET['colname'][$k] . " LIKE " . "'%" . $_GET['search'][$k] . "%'";
                                                                }
                                                            }
                                                        }

//                                                        if (in_array("emp_id", $recol)) {
//                                                            $allot = "SELECT " . $newcoloums . " FROM " . $tblname . " INNER JOIN tbl_doc_assigned_wf ON " . $tblname . ".ticket_id = tbl_doc_assigned_wf.ticket_id INNER JOIN  tbl_user_master on tbl_user_master.user_id=tbl_doc_assigned_wf.assign_by $where";
//                                                        } else {
                                                        //$allot = "SELECT " . $newcoloums . " FROM " . $tblname . " INNER JOIN tbl_doc_assigned_wf ON " . $tblname . ".ticket_id = tbl_doc_assigned_wf.ticket_id INNER JOIN  tbl_user_master on tbl_user_master.user_id=tbl_doc_assigned_wf.assign_by $where";
                                                        //  $allot = "SELECT " . $newcoloums . " FROM " . $tblname . " INNER JOIN tbl_doc_assigned_wf ON " . $tblname . ".ticket_id = tbl_doc_assigned_wf.ticket_id $where";

                                                        if ($cashVoucher) {
                                                            $allot = "SELECT " . $newcoloums . ", d.division_name,p.project_name  FROM " . $tblname . " INNER JOIN tbl_doc_assigned_wf ON " . $tblname . ".ticket_id = tbl_doc_assigned_wf.ticket_id INNER JOIN tbl_division as d on " . $tblname . ".wf_devision=d.Id INNER JOIN tbl_project as p on " . $tblname . ".wf_project=p.Id $where";
                                                        } else {

                                                            $allot = "SELECT " . $newcoloums . " FROM " . $tblname . " INNER JOIN tbl_doc_assigned_wf ON " . $tblname . ".ticket_id = tbl_doc_assigned_wf.ticket_id INNER JOIN  tbl_user_master on tbl_user_master.user_id=tbl_doc_assigned_wf.assign_by left join tbl_user_master as um on tbl_doc_assigned_wf.action_by=um.user_id  $where";
                                                        }
//                                                        }
                                                    } else {
                                                        
                                                        
                                                       
                                                        if ($cashVoucher) {
                                                            $allot = "SELECT " . $newcoloums . ", d.division_name,p.project_name  FROM " . $tblname . " INNER JOIN tbl_doc_assigned_wf ON " . $tblname . ".ticket_id = tbl_doc_assigned_wf.ticket_id INNER JOIN tbl_division as d on " . $tblname . ".wf_devision=d.Id INNER JOIN tbl_project as p on " . $tblname . ".wf_project=p.Id";
                                                        } else {

                                                            $allot = "SELECT " . $newcoloums . " FROM " . $tblname . " INNER JOIN tbl_doc_assigned_wf ON " . $tblname . ".ticket_id = tbl_doc_assigned_wf.ticket_id INNER JOIN  tbl_user_master on tbl_user_master.user_id=tbl_doc_assigned_wf.assign_by left join tbl_user_master as um on tbl_doc_assigned_wf.action_by=um.user_id";
                                                        }
                                                    }

                                                    $coloums = explode(",", $coloums);
                                                    $coloums = implode("','", $coloums);
                                                    mysqli_set_charset($db_con, "utf8");
                                                    $allot_query = mysqli_query($db_con, $allot) or die("Error dgdfg: " . mysqli_error($db_con));
                                                    $foundnum = mysqli_num_rows($allot_query);


                                                    if ($foundnum > 0) {
                                                        
                                                        
                                                        if (is_numeric($_GET['limit'])) {
                                                            $per_page = $_GET['limit'];
                                                        } else {
                                                            $per_page = 10;
                                                        }
                                                        $start = isset($_GET['start']) ? ($_GET['start'] > 0) ? $_GET['start'] : 0 : 0;
                                                        $max_pages = ceil($foundnum / $per_page);
                                                        if (!$start) {
                                                            $start = 0;
                                                        }
                                                        if (!empty($_GET['colname']) && !empty($_GET['search'])) {
                                                            $where = "where ";
                                                            for ($k = 0; $k < count($_GET['colname']); $k++) {
                                                                if ($k == 0) {
                                                                    if ($_GET['colname'][$k] == "wf_devision") {
                                                                        $where .= " d.division_name LIKE " . "'%" . $_GET['search'][$k] . "%'";
                                                                    } else if ($_GET['colname'][$k] == "wf_project") {
                                                                        $where .= "  p.project_name LIKE " . "'%" . $_GET['search'][$k] . "%'";
                                                                    } else if ($_GET['colname'][$k] == "action_by") {
                                                                        $where .= " CONCAT(um.first_name, ' ', um.last_name) LIKE " . "'%" . $_GET['search'][$k] . "%'";
                                                                    } else if ($_GET['colname'][$k] == "assign_by") {
                                                                        $assign_by = " where CONCAT(first_name, ' ', last_name) LIKE " . "'%" . $_GET['search'][$k] . "%'";
                                                                        $user = mysqli_query($db_con, "select GROUP_CONCAT(user_id) as assgnbyid from tbl_user_master  $assign_by");
                                                                        $rwUser = mysqli_fetch_assoc($user);
                                                                        $where .= " FIND_IN_SET(tbl_doc_assigned_wf.assign_by,'" . $rwUser['assgnbyid'] . "')";
                                                                    } else if ($_GET['colname'][$k] == "first_name") {
                                                                        $where .= " um.first_name LIKE" . "'%" . $_GET['search'][$k] . "%' ";
                                                                    } else {
                                                                        $where .= " " . $_GET['colname'][$k] . " LIKE " . "'%" . $_GET['search'][$k] . "%'";
                                                                    }
                                                                } else {
                                                                    if ($_GET['colname'][$k] == "wf_devision") {
                                                                        $where .= " AND d.division_name LIKE " . "'%" . $_GET['search'][$k] . "%'";
                                                                    } else if ($_GET['colname'][$k] == "wf_project") {
                                                                        $where .= " AND  p.project_name LIKE " . "'%" . $_GET['search'][$k] . "%'";
                                                                    } else if ($_GET['colname'][$k] == "action_by") {
                                                                        $where .= " AND CONCAT(um.first_name, ' ', um.last_name) LIKE " . "'%" . $_GET['search'][$k] . "%'";
                                                                    } else if ($_GET['colname'][$k] == "assign_by") {
                                                                        $assign_by = " where CONCAT(first_name, ' ', last_name) LIKE " . "'%" . $_GET['search'][$k] . "%'";
                                                                        $user = mysqli_query($db_con, "select GROUP_CONCAT(user_id) as assgnbyid from tbl_user_master  $assign_by");
                                                                        $rwUser = mysqli_fetch_assoc($user);
                                                                        $where .= " AND FIND_IN_SET(tbl_doc_assigned_wf.assign_by,'" . $rwUser['assgnbyid'] . "')";
                                                                    } else if ($_GET['colname'][$k] == "first_name") {
                                                                        $where .= " um.first_name LIKE" . "'%" . $_GET['search'][$k] . "%' ";
                                                                    } else {
                                                                        $where .= " AND " . $_GET['colname'][$k] . " LIKE " . "'%" . $_GET['search'][$k] . "%'";
                                                                    }
                                                                }
                                                            }

                                                            if ($cashVoucher) {
                                                                $allote = "SELECT " . $newcoloums . ", d.division_name,p.project_name  FROM " . $tblname . " INNER JOIN tbl_doc_assigned_wf ON " . $tblname . ".ticket_id = tbl_doc_assigned_wf.ticket_id INNER JOIN tbl_division as d on " . $tblname . ".wf_devision=d.Id INNER JOIN tbl_project as p on " . $tblname . ".wf_project=p.Id  $where LIMIT $start, $per_page";
                                                            } else {
                                                                mysqli_set_charset($db_con, "utf8");
                                                                $allote = "SELECT " . $newcoloums . " FROM " . $tblname . " INNER JOIN tbl_doc_assigned_wf ON " . $tblname . ".ticket_id = tbl_doc_assigned_wf.ticket_id INNER JOIN  tbl_user_master on tbl_user_master.user_id=tbl_doc_assigned_wf.assign_by left join tbl_user_master as um on tbl_doc_assigned_wf.action_by=um.user_id $where  LIMIT $start, $per_page";
                                                                $allote_query = mysqli_query($db_con, $allote) or die("ERROR:" . mysqli_error($db_con));
                                                            }
                                                        } else {

                                                            if ($cashVoucher) {
                                                                $allote = "SELECT " . $newcoloums . ", d.division_name,p.project_name  FROM " . $tblname . " INNER JOIN tbl_doc_assigned_wf ON " . $tblname . ".ticket_id = tbl_doc_assigned_wf.ticket_id INNER JOIN tbl_division as d on " . $tblname . ".wf_devision=d.Id INNER JOIN tbl_project as p on " . $tblname . ".wf_project=p.Id LIMIT $start, $per_page";
                                                            } else {
                                                                mysqli_set_charset($db_con, "utf8");
                                                                $allote = "SELECT " . $newcoloums . " FROM " . $tblname . " INNER JOIN tbl_doc_assigned_wf ON " . $tblname . ".ticket_id = tbl_doc_assigned_wf.ticket_id INNER JOIN  tbl_user_master on tbl_user_master.user_id=tbl_doc_assigned_wf.assign_by left join tbl_user_master as um on tbl_doc_assigned_wf.action_by=um.user_id LIMIT $start, $per_page";
                                                                $allote_query = mysqli_query($db_con, $allote) or die("ERROR11:" . mysqli_error($db_con));
                                                            }
                                                        }
                                                        ?>
                                                        <div class="row">
                                                            <div class="col-md-6"></div>
                                                            <div class="col-md-6">
                                                                <?php
                                                                $urlnew = "&";
                                                                for ($l = 0; $l < count($_GET['search']); $l++) {
                                                                    if ($l == 0) {
                                                                        $urlnew .= "colname[]=" . $_GET['colname'][$l] . "&search[]=" . $_GET['search'][$l];
                                                                    } else {
                                                                        $urlnew .= "&colname[]=" . $_GET['colname'][$l] . "&search[]=" . $_GET['search'][$l];
                                                                    }
                                                                }
                                                                ?>

                                                            </div>
                                                        </div>
                                                        <form method="get">
                                                            <div class="row">
                                                                <?php
                                                                if (!empty($_GET['colname']) && !empty($_GET['search'])) {
                                                                    for ($k = 0; $k < count($_GET['colname']); $k++) {
                                                                        ?>
                                                                        <div id="remove<?= $k; ?>" class="textfieldcount">
                                                                            <div class="col-md-4 m-b-5 m-r-5">
                                                                                <select class="form-control select2" name="colname[]" >

                                                                                    <?php
                                                                                    $labelnameqry = mysqli_query($db_con, "SELECT label,name FROM tbl_form_attribute WHERE name in('$coloums') and dependency_id IS NUll and  fid='$formid'") or die("Label Error:" . mysqli_error($db_con));
                                                                                    while ($rowdataFetch = mysqli_fetch_assoc($labelnameqry)) {
                                                                                        // print_r($rowdataFetch);
                                                                                        if ($rowdataFetch['name'] == "wf_ccenter" || $rowdataFetch['name'] == "wf_whouse") {
                                                                                            ?>
                                                                                        <?php } else { ?>
                                                                                            <option value="<?= $rowdataFetch['name'] ?>" <?= $rowdataFetch['name'] == $_GET['colname'][$k] ? 'selected' : '' ?>><?= $rowdataFetch['label'] ?></option>
                                                                                            <?php
                                                                                        }
                                                                                    }
                                                                                    ?>
                                                                                    <?php if (in_array("task_status", $recol)) { ?>
                                                                                        <option value="task_status" <?= "task_status" == $_GET['colname'][$k] ? 'selected' : '' ?>><?= $lang['Task_Status']; ?></option>
                                                                                    <?php } if (in_array("action_by", $recol)) { ?>
                                                                                        <option value="action_by" <?= "action_by" == $_GET['colname'][$k] ? 'selected' : '' ?>><?= $lang['approved_by']; ?></option>
                                                                                    <?php } if (in_array("assign_by", $recol)) { ?>
                                                                                        <option value="assign_by" <?= "assign_by" == $_GET['colname'][$k] ? 'selected' : '' ?>><?= $lang['Assigned_By']; ?></option>
                                                                                    <?php } if (in_array("start_date", $recol)) { ?>
                                                                                        <option value="start_date" <?= "start_date" == $_GET['colname'][$k] ? 'selected' : '' ?>><?= $lang['submitted_date']; ?></option>
                                                                                    <?php } ?>

                                                                                </select>
                                                                            </div>
                                                                            <div class="col-md-4 m-b-5">
                                                                                <input type="text" id="ser1" name="search[]" placeholder="<?php echo $lang['Search']; ?>..." class="form-control" value='<?= $_GET[search][$k] ?>' required="">
                                                                            </div>

                                                                            <input type="hidden" value="<?= $_GET['rid'] ?>" name="rid">
                                                                            <input type="hidden" value="<?= $_GET['wfid'] ?>" name="wfid">
                                                                            <?php if ($k == 0) { ?>
                                                                                <div class="col-md-4 pull-right" style="margin-right: -5px;"> 
                                                                                    <button type="submit" name="submit" class="btn btn-primary" ><?php echo $lang['Submit']; ?></button>
                                                                                    <a class="btn btn-primary" id="addfields"><i class="fa fa-plus-square"></i></a>
                                                                                    <a  href="workflowreport?rid=<?php echo $_GET['rid']; ?>&wfid=<?php echo $_GET['wfid']; ?>" class="btn btn-warning"><i class="fa fa-refresh" title="<?php echo $lang['Reset']; ?>"></i></a>

                                                                                    <a href="exportwfreport?<?php echo "wfid=" . $wfid . "&rid=" . $rid . $urlnew ?>" class="btn btn-primary"><i class="fa fa-download"></i> <?php echo $lang['export_report']; ?></a>
                                                                                </div>
                                                                            <?php } else { ?>
                                                                                <div class="col-md-2 m-t-5">
                                                                                    <a class="btn btn-primary" id="remove<?= $k ?>" onclick="remove(this.id)"><i class="fa fa-minus-square"></i></a>
                                                                                </div>
                                                                            </div>
                                                                            <?php
                                                                        }
                                                                    }
                                                                } else {
                                                                    ?>

                                                                    <div class="col-md-4 m-b-5">
                                                                        <select class="form-control select2" name="colname[]" >

                                                                            <?php
                                                                            $labelnameqry = mysqli_query($db_con, "SELECT label,name FROM tbl_form_attribute WHERE name in('$coloums') and dependency_id IS NUll and  fid='$formid'") or die("Label Error:" . mysqli_error($db_con));
                                                                            while ($rowdataFetch = mysqli_fetch_assoc($labelnameqry)) {
                                                                                if ($rowdataFetch['name'] == "wf_ccenter") {
                                                                                    echo "<option value=cc_name>" . $rowdataFetch['label'] . "</option>";
                                                                                } elseif ($rowdataFetch['name'] == "wf_whouse") {
                                                                                    echo "<option value=wh_name>" . $rowdataFetch['label'] . "</option>";
                                                                                } else {
                                                                                    ?>

                                                                                    <option value="<?= $rowdataFetch['name'] ?>"><?= $rowdataFetch['label'] ?></option>
                                                                                    <?php
                                                                                }
                                                                            }
                                                                            ?>
                                                                            <?php if (in_array("task_status", $recol)) { ?>
                                                                                <option value="task_status"><?= $lang['Task_Status']; ?></option>
                                                                            <?php } if (in_array("action_by", $recol)) { ?>
                                                                                <option value="action_by"><?= $lang['approved_by']; ?></option>
                                                                            <?php } if (in_array("assign_by", $recol)) { ?>
                                                                                <option value="assign_by"><?= $lang['Assigned_By']; ?></option>
                                                                            <?php } if (in_array("start_date", $recol)) { ?>
                                                                                <option value="start_date"><?= $lang['submitted_date']; ?></option>
                                                                            <?php } ?>

                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-4 m-b-5">
                                                                        <input type="text" id="sear" name="search[]" placeholder="<?= $lang['Search']; ?>" class="form-control" required="">
                                                                    </div>
                                                                    <input type="hidden" value="<?= $_GET['rid'] ?>" name="rid">
                                                                    <input type="hidden" value="<?= $_GET['wfid'] ?>" name="wfid">

                                                                    <div class="col-md-4 pull-right">
                                                                        <button type="submit" name="submit" class="btn btn-primary" ><?php echo $lang['Submit']; ?></button>
                                                                        <a class="btn btn-primary" id="addfields"><i class="fa fa-plus-square"></i></a>
                                                                        <a  href="workflowreport?rid=<?php echo $_GET['rid']; ?>&wfid=<?php echo $_GET['wfid']; ?>" class="btn btn-warning"><i class="fa fa-refresh" title="<?php echo $lang['Reset']; ?>"></i></a>

                                                                        <a href="exportwfreport?<?php echo "wfid=" . $wfid . "&rid=" . $rid . $urlnew ?>" class="btn btn-primary"><i class="fa fa-download"></i> <?php echo $lang['export_report']; ?></a>
                                                                    </div>
                                                                <?php } ?>
                                                            </div>
                                                            <div class="row">
                                                                <div class="contents">

                                                                </div>
                                                            </div>
                                                        </form>
                                                        <div class="row">
                                                            <div class="col-sm-9">
                                                                <div class="box-body">

                                                                    <label><?php echo $lang['show_lst']; ?> </label>
                                                                    <select id="limit" class="input-sm">
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
                                                                        <option value="250" <?php
                                                                        if ($_GET['limit'] == 250) {
                                                                            echo 'selected';
                                                                        }
                                                                        ?>>250</option>
                                                                        <option value="500" <?php
                                                                        if ($_GET['limit'] == 500) {
                                                                            echo 'selected';
                                                                        }
                                                                        ?>>500</option>
                                                                    </select> 
                                                                    <label><?php echo $lang['WORKFLOW_REPORT']; ?></label>
                                                                </div>
                                                            </div>
                                                            <div class="record col-sm-3 pull-right m-t-10">
                                                                <label><?php echo $start + 1 ?> <?php echo $lang['To']; ?> <?php
                                                                    if ($start + $per_page > $foundnum) {
                                                                        echo $foundnum;
                                                                    } else {
                                                                        echo ($start + $per_page);
                                                                    }
                                                                    ?> <span><?php echo $lang['ttl_recrds']; ?>: <?php echo $foundnum; ?></span></label>
                                                            </div>
                                                        </div>
                                                        <table  class="table table-striped table-bordered" >
                                                            <thead>
                                                                <tr>
                                                                    <th><?= $lang['SNO']; ?></th>
                                                                    <?php
                                                                    $dFormColoums = array();
                                                                    $labelnameqry = mysqli_query($db_con, "SELECT label,name FROM tbl_form_attribute WHERE name in('$coloums')  and dependency_id IS NULL and  fid='$formid'") or die("Label Error:" . mysqli_error($db_con));
                                                                    while ($rowdataFetch = mysqli_fetch_assoc($labelnameqry)) {

                                                                        if ($rowdataFetch['name'] == 'wf_devision') {
                                                                            array_push($dFormColoums, 'division_name');
                                                                        } else if ($rowdataFetch['name'] == 'wf_project') {
                                                                            array_push($dFormColoums, 'project_name');
                                                                        } else {
                                                                            array_push($dFormColoums, $rowdataFetch['name']);
                                                                        }
                                                                        ?>
                                                                        <th><?= $rowdataFetch['label'] ?></th>
                                                                    <?php } ?>
                                                                    <?php if (in_array("task_status", $recol)) { ?>
                                                                        <th><?= $lang['Task_Status']; ?></th>
                                                                    <?php } if (in_array("action_by", $recol)) { ?>
                                                                        <th><?= $lang['approved_by']; ?></th>
                                                                    <?php } if (in_array("assign_by", $recol)) { ?>
                                                                        <th><?= $lang['Assigned_By']; ?></th>
                                                                    <?php } if (in_array("start_date", $recol)) { ?>
                                                                        <th><?= $lang['submitted_date']; ?></th>
                                                                    <?php } ?>

                                                                </tr>
                                                                <?php //print_r($dFormColoums); ?>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $n = $start + 1;
                                                                //print_r($recol);
                                                                while ($allot_row = mysqli_fetch_assoc($allote_query)) {
                                                                    ?>
                                                                    <tr class="gradeX" style="vertical-align: middle;">
                                                                        <td style="width:60px"><?php echo $n; ?></td>

                                                                        <?php foreach ($dFormColoums as $key => $value) { ?>
                                                                            <td><?php echo $allot_row[$value]; ?> </td>

                                                                        <?php } if (in_array("task_status", $recol)) { ?>
                                                                            <td><?= $allot_row['task_status']; ?></td>
                                                                        <?php } ?>

                                                                        <?php if (in_array("action_by", $recol)) { ?>
                                                                            <td><?php
                                                                                if (!empty($allot_row['action_by'])) {
                                                                                    $user = mysqli_query($db_con, "select first_name,last_name from tbl_user_master where user_id='$allot_row[action_by]'");
                                                                                    $rwUser = mysqli_fetch_assoc($user);
                                                                                    echo $rwUser['first_name'] . ' ' . $rwUser['last_name'];
                                                                                } else {
                                                                                    echo $lang['pending_no_action'];
                                                                                }
                                                                                ?></td>
                                                                        <?php } ?>

                                                                        <?php if (in_array("assign_by", $recol)) { ?>
                                                                            <td><?php
                                                                                $user = mysqli_query($db_con, "select first_name,last_name from tbl_user_master where user_id='$allot_row[assign_by]'");
                                                                                $rwUser = mysqli_fetch_assoc($user);
                                                                                echo '<label class="label label label-success">' . $rwUser['first_name'] . ' ' . $rwUser['last_name'] . '</label>';
                                                                                ?></td>
                                                                        <?php } ?>
                                                                        <?php if (in_array("start_date", $recol)) { ?>
                                                                            <td><?php echo '<label class="label label label-info">' . $allot_row['start_date'] . '<label>'; ?></td>
                                                                        <?php } ?>


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
                                                            $url = "&";
                                                            for ($j = 0; $j < count($_GET['search']); $j++) {
                                                                if ($j == 0) {
                                                                    $url .= "colname[]=" . $_GET['colname'][$j] . "&search[]=" . $_GET['search'][$j];
                                                                } else {
                                                                    $url .= "&colname[]=" . $_GET['colname'][$j] . "&search[]=" . $_GET['search'][$j];
                                                                }
                                                            }
                                                            ?>

                                                            <ul class='pagination strgePage'>
                                                                <?php
                                                                //previous button
                                                                if (!($start <= 0))
                                                                    echo " <li><a href='?rid=$_GET[rid]&wfid=$_GET[wfid]&start=$prev $url'>$lang[Prev]</a> </li>";
                                                                else
                                                                    echo " <li class='disabled'><a href='javascript:void(0)'>$lang[Prev]</a> </li>";
                                                                //pages 
                                                                if ($max_pages < 7 + ($adjacents * 2)) {   //not enough pages to bother breaking it up
                                                                    $i = 0;
                                                                    for ($counter = 1; $counter <= $max_pages; $counter++) {
                                                                        if ($i == $start) {
                                                                            echo " <li class='active'><a href='?rid=$_GET[rid]&wfid=$_GET[wfid]&start=$i $url'><b>$counter</b></a> </li>";
                                                                        } else {
                                                                            echo "<li><a href='?rid=$_GET[rid]&wfid=$_GET[wfid]&start=$i $url'>$counter</a></li> ";
                                                                        }
                                                                        $i = $i + $per_page;
                                                                    }
                                                                } elseif ($max_pages > 5 + ($adjacents * 2)) {    //enough pages to hide some
                                                                    //close to beginning; only hide later pages
                                                                    if (($start / $per_page) < 1 + ($adjacents * 2)) {
                                                                        $i = 0;
                                                                        for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                                                                            if ($i == $start) {
                                                                                echo " <li class='active'><a href='?rid=$_GET[rid]&wfid=$_GET[wfid]&start=$i $url'><b>$counter</b></a></li> ";
                                                                            } else {
                                                                                echo "<li> <a href='?rid=$_GET[rid]&wfid=$_GET[wfid]&start=$i $url'>$counter</a> </li>";
                                                                            }
                                                                            $i = $i + $per_page;
                                                                        }
                                                                    }
                                                                    //in middle; hide some front and some back
                                                                    elseif ($max_pages - ($adjacents * 2) > ($start / $per_page) && ($start / $per_page) > ($adjacents * 2)) {
                                                                        echo " <li><a href='?rid=$_GET[rid]&wfid=$_GET[wfid]&start=0 $url'>1</a></li> ";
                                                                        echo "<li><a href='?rid=$_GET[rid]&wfid=$_GET[wfid]&start=$per_page $url' >2</a></li>";
                                                                        echo "<li><a href='javascript:void(0)'>...</a></li>";

                                                                        $i = $start;
                                                                        for ($counter = ($start / $per_page) + 1; $counter < ($start / $per_page) + $adjacents + 2; $counter++) {
                                                                            if ($i == $start) {
                                                                                echo " <li><a href='?rid=$_GET[rid]&wfid=$_GET[wfid]&start=$i $url'><b>$counter</b></a></li> ";
                                                                            } else {
                                                                                echo " <li><a href='?rid=$_GET[rid]&wfid=$_GET[wfid]&start=$i $url'>$counter</a> </li>";
                                                                            }
                                                                            $i = $i + $per_page;
                                                                        }
                                                                    }
                                                                    //close to end; only hide early pages
                                                                    else {
                                                                        echo "<li> <a href='?rid=$_GET[rid]&wfid=$_GET[wfid]&start=0 $url'>1</a> </li>";
                                                                        echo "<li><a href='?rid=$_GET[rid]&wfid=$_GET[wfid]&start=$per_page $url'>2</a></li>";
                                                                        echo "<li><a href='javascript:void(0)'>...</a></li>";

                                                                        $i = $start;
                                                                        for ($counter = ($start / $per_page) + 1; $counter <= $max_pages; $counter++) {
                                                                            if ($i == $start) {
                                                                                echo " <li class='active'><a href='?rid=$_GET[rid]&wfid=$_GET[wfid]&start=$i $url'><b>$counter</b></a></li> ";
                                                                            } else {
                                                                                echo "<li> <a href='?rid=$_GET[rid]&wfid=$_GET[wfid]&start=$i $url'>$counter</a></li> ";
                                                                            }
                                                                            $i = $i + $per_page;
                                                                        }
                                                                    }
                                                                }
                                                                //next button
                                                                if (!($start >= $foundnum - $per_page))
                                                                    echo "<li><a href='?rid=$_GET[rid]&wfid=$_GET[wfid]&start=$next $url'>$lang[Next]</a></li>";
                                                                else
                                                                    echo "<li class='disabled'><a href='javascript:void(0)'>$lang[Next]</a></li>";
                                                                ?>
                                                            </ul>
                                                            <?php
                                                        }
                                                        echo "</center>";
                                                    } else {
                                                        ?>
                                                        <div class="form-group form-group no-records-found"><label><strong class="text-danger"><i class="ti-face-sad text-pink"></i> <?php echo $lang['Who0ps!_No_Records_Found'] ?></strong></label></div>
                                                                <?php }
                                                                ?>
                                                </div>   
                                                <?php
                                            }
                                        } else {
                                            echo $lang['No_Rcrds_Fnd'];
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                            <!-- end: page -->
                        </div> <!-- end Panel -->
                    </div>
                </div> <!-- container -->

            </div> <!-- content -->

            <?php require_once './application/pages/footer.php'; ?>

        </div>
    </div>
    <!-- END wrapper -->
    <?php require_once './application/pages/footerForjs.php'; ?>
    <script type="text/javascript" src="assets/plugins/multiselect/js/jquery.multi-select.js"></script>
    <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
    <script src="assets/js/jquery.core.js"></script>

    <script type="text/javascript">

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
                                                            jQuery(document).ready(function ($) {
                                                                $("#limit").change(function () {
                                                                    lval = $(this).val();
                                                                    url = removeParam("limit", url);
                                                                    url = url + "&limit=" + lval;
                                                                    window.open(url, "_parent");
                                                                });
                                                            });


    </script>
    <script>

        $(document).ready(function () {
            var textfieldcount = $(".textfieldcount").length;
            if (textfieldcount < <?php echo $metaCount; ?>) {
                //alert("gbbbb");
                var max_fields = <?php echo $metaCount; ?>; //maximum input boxes allowed
                var wrapper = $(".contents"); //Fields wrapper
                var add_button = $("#addfields"); //Add button ID
                var rid =<?= $rid ?>;
                var wfid =<?= $wfid ?>;

                var x = 1; //initlal text box count
                $(add_button).click(function (e) { //on add input button click
                    e.preventDefault();

                    if (x < max_fields) { //max input box allowed
                        x++;
                        //text box increment
                        $.ajax({url: "application/ajax/reportCopy?id=" + x + "&rid=" + rid + "&wfid=" + wfid, success: function (result) {
                                $(wrapper).append("<div class='col-lg-12' style='margin-bottom:0px'>" + result + "<button class='remove_field btn btn-primary' style='margin-left:15px; margin-top:10px;'><i class='fa fa-minus-square' aria-hidden='true' title='<?php echo $lang['Remove']; ?>'></i></a>" + "</div>"); //add input box


                            }});

                    } else
                    {
                        alert("<?= $lang['No_Available']; ?>");
                        $("#addfields").hide();
                    }
                });

                $(wrapper).on("click", ".remove_field", function (e) { //user click on remove text
                    e.preventDefault();
                    $(this).parent('div').remove();
                    x--;
                    $("#addfields").show();
                })
            } else {
                //alert("<?= $lang['No_Available']; ?>");
                $("#addfields").hide();
            }
        });
        function remove(id) {

            $("#" + id).remove();
        }
        $(".select2").select2();
    </script>
</body>
</html>

