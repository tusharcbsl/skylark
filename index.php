<!DOCTYPE html>
<html>
<!--Morris Chart CSS -->
<link rel="stylesheet" href="assets/plugins/chartist/css/chartist.min.css">
<?php
require_once './loginvalidate.php';
require_once './application/pages/head.php';
require_once './application/pages/function.php';
?>
<style>
    .h4,
    h4 {
        font-size: 15px;
    }

    .h2,
    h2 {
        font-size: 24px;
    }

    .btn {
        padding: 5px 9px;
    }
</style>

<!-- full Calendar -->
<link rel="stylesheet" href="assets/plugins/fullcalendar/css/fullcalendar.min.css">
<link rel="stylesheet" href="assets/plugins/fullcalendar/css/fullcalendar.print.min.css" media="print">

<body class="fixed-left">

    <!-- Begin page -->
    <div id="wrapper">

        <!-- Top Bar Start -->
        <?php require_once './application/pages/topBar.php'; ?>
        <!-- Top Bar End -->
        <!-- ========== Left Sidebar Start ========== -->
        <?php require_once './application/pages/sidebar.php'; ?>
        <!-- Left Side bar End -->
        <?php //require_once './application/pages/rightSidebar.php';  
        ?>
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

                            <div class="btn-group pull-right">
                                <button type="button" class="btn btn-default"><i class="fa fa-history"></i> <strong><?php echo $lang['Last_Login']; ?> </strong> <?php
                                                                                                                                                                    if (!empty($loginRw['last_active_logout'])) {
                                                                                                                                                                        echo '<strong>' . date('d-m-Y h : i : s A', strtotime($loginRw['last_active_logout'])) . '</strong>';
                                                                                                                                                                    } else {
                                                                                                                                                                        echo '00:00:00 00:00:00';
                                                                                                                                                                    }
                                                                                                                                                                    ?> </button>
                            </div>
                            <h4 class="page-title"><?php echo $lang['Das']; ?></h4>
                            <!-- <p class="text-muted page-title-alt"> <?php echo $projectName . ' ' . $lang['Wel_EzeeFile_Dash']; ?></p> -->
                            <p class="text-muted page-title-alt"><b> MAJOR UPGRADATION OF RAIPUR RAILWAY STATION OF RAIPUR DIVISION OF SECR ON (EPC) MODE</b></p>
                        </div>
                    </div>
                    <div class="row">
                        <?php
                        mysqli_set_charset($db_con, "utf8");

                        $permQuery = "SELECT GROUP_CONCAT(sl_id) AS sl_ids FROM tbl_storagelevel_to_permission WHERE user_id=" . $_SESSION['cdes_user_id'];
                        $permResult = mysqli_query($db_con, $permQuery) or die('Error: ' . mysqli_error($db_con));

                        $slids = "";

                        if (mysqli_num_rows($permResult) > 0) {
                            $rwPerm = mysqli_fetch_assoc($permResult);
                            $slids = $rwPerm['sl_ids'];
                        }

                        if ($slids) {
                            $folderperms = findsubfoldern($slids, $db_con);
                            $slidsUnique = array_unique($folderperms);
                            $slids = implode(',', $slidsUnique);
                            if (!empty($slids)) {
                                $sql = "SELECT SUM(doc_size) AS total, COUNT(doc_name) AS count, SUM(noofpages) AS numpages 
                FROM tbl_document_master 
                WHERE substring_index(doc_name, '_', 1) IN ($slids) AND flag_multidelete = 1";
                                $contFiles = mysqli_query($db_con, $sql) or die('Error: ' . mysqli_error($db_con));

                                $totalFiles = [
                                    "totalFolder" => count($slidsUnique),
                                    "files" => 0,
                                    "fileSize" => 0,
                                    "numPages" => 0
                                ];

                                if (mysqli_num_rows($contFiles) > 0) {
                                    $rwcontFile = mysqli_fetch_assoc($contFiles);
                                    $totalFSize1 = $rwcontFile['total'];
                                    $totalFSize = round($totalFSize1 / (1000 * 1000), 2);
                                    $totalFiles["files"] = $rwcontFile['count'];
                                    $totalFiles["fileSize"] = $totalFSize;
                                    $totalFiles["numPages"] = $rwcontFile['numpages'];
                                }
                            }
                        }

                        function findsubfoldern($SlIds, $db_con)
                        {
                            $sllevel = mysqli_query($db_con, "SELECT sl_id FROM tbl_storage_level WHERE sl_id IN ($SlIds) AND delete_status = '0'") or die('Error: ' . mysqli_error($db_con));
                            $folderperms = [];

                            while ($rwfolderperm = mysqli_fetch_assoc($sllevel)) {
                                $folderperms[] = $rwfolderperm['sl_id'];
                            }

                            $sllevel1 = mysqli_query($db_con, "SELECT sl_id FROM tbl_storage_level WHERE sl_parent_id IN ($SlIds)") or die('Error: ' . mysqli_error($db_con));

                            if (mysqli_num_rows($sllevel1) > 0) {
                                $childIds = [];

                                while ($rowCh = mysqli_fetch_assoc($sllevel1)) {
                                    $childIds[] = $rowCh['sl_id'];
                                }

                                $childIdsString = implode(",", $childIds);
                                $folderperms = array_merge($folderperms, findsubfoldern($childIdsString, $db_con));
                            }

                            return $folderperms;
                        }

                        ?>


                        <?php if (($rwgetRole['num_of_file'] == '1') || ($rwgetRole['num_of_folder'] == '1')) { ?>
                            <a href="<?php
                                        if (mysqli_num_rows($perm) > 0) {
                                            echo 'storage?id=' . urlencode(base64_encode($slperm));
                                        }
                                        ?>">
                                <div class="col-md-6 col-lg-4">
                                    <div class="box box-primary">
                                        <div class="card-box" style="height: 300px;">
                                            <h4 class="text-dark header-title m-t-0 m-b-10" style="text-align:center"><?php echo $lang['MY_DMS']; ?></h4>
                                            <div class="widget-chart text-center">
                                                <div id="sparkline3"></div>
                                                <ul class="list-inline m-t-15">
                                                    <?php if ($rwgetRole['num_of_folder'] == '1') { ?>
                                                        <li>
                                                            <h6 class="text-muted m-t-20"><button class="btn btn-primary"></button> <?php echo $lang['folders']; ?></h6>
                                                            <h4 class="m-b-0"><?php echo ((!empty($totalFiles["totalFolder"]) ? $totalFiles["totalFolder"] : "0")); ?></h4>
                                                        </li>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['num_of_file'] == '1') { ?>
                                                        <li>
                                                            <h6 class="text-muted m-t-20"><button class="btn btn-info"></button> <?php echo $lang['dFiles']; ?></h6>
                                                            <h4 class="m-b-0"><?php echo $totalFiles["files"]; ?></h4>
                                                        </li>
                                                        <li>
                                                            <h6 class="text-muted m-t-20"><button class="btn btn-warning"></button> <?php echo $lang['pages']; ?></h6>
                                                            <h4 class="m-b-0"><?php
                                                                                if ($totalFiles["numPages"]) {
                                                                                    echo $totalFiles["numPages"];
                                                                                } else {
                                                                                    echo '0';
                                                                                }
                                                                                ?></h4>
                                                        </li>
                                                    <?php } ?>

                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        <?php } ?>
                        <?php

                        function workflowStatus()
                        {
                            global $db_con;
                            if ($_SESSION['cdes_user_id'] == 1) {
                                $constructs = "call workflowStatusDashboard(0,'status')";
                            } else {
                                $constructs = "call workflowStatusDashboard(" . $_SESSION['cdes_user_id'] . ",'status')";
                            }
                            // print_r($constructs);
                            // die;
                            $run = mysqli_query($db_con, $constructs) or die('Error 1' . mysqli_error($db_con));
                            $rwRun = mysqli_fetch_assoc($run);
                            mysqli_next_result($db_con);
                            $pending = $rwRun['pending'];
                            $processed = $rwRun['processed'];
                            $completed = $rwRun['complete'];

                            $res_array = array('completed' => $completed, 'processed' => $processed, 'pending' => $pending, 'up' => $rwRun['urgent'], 'mp' => $rwRun['Mediuma'], 'np' => $rwRun['normal']);
                            return $res_array;
                        }


                        $wfst = workflowStatus();
                        ?>
                        <?php if ($rwgetRole['status_wf'] == '1') { ?>
                            <div class="col-md-6 col-lg-4">
                                <?php ///print_r($wfst);die;             
                                ?>
                                <div class="box box-primary">
                                    <div class="card-box" style="height: 300px;">
                                        <div class="box-header">
                                            <h4 class="text-dark header-title margin-t-9 m-b-10" style="text-align:center"><?php echo $lang['wf_status']; ?></h4>
                                            <div class="widget-chart text-center">
                                                <?php if ($wfst['completed'] != '0' || $wfst['processed'] != '0' || $wfst['pending'] != '0') { ?>
                                                    <div id="workflow-status" style="height: 175px;"></div>
                                                <?php } else { ?><div class="ezeeoffice status">
                                                        <li align="center" class="list-group-item"><span class="text-alert"> <?= $lang['no_status']; ?></span></li>
                                                    </div><?php } ?>

                                                <ul class="list-inline m-t-15">

                                                    <?php //if ($rwgetRole['num_of_folder'] == '1'){                
                                                    ?>
                                                    <li>
                                                        <h6 class="text-muted"><a href="myTask?taskStats=Pending" class="btn btn-danger"></a> <?php echo $lang['Pending']; ?></h6>
                                                        <h4 class="m-b-0"><?= $wfst['pending'] ?></h4>
                                                    </li>
                                                    <?php //}         
                                                    ?>
                                                    <?php //if ($rwgetRole['num_of_file'] == '1'){             
                                                    ?>
                                                    <li>
                                                        <h6 class="text-muted"><a href="myTask?taskStats=Processed" class="btn btn-warning"></a> <?php echo $lang['Processed']; ?></h6>
                                                        <h4 class="m-b-0"><?= $wfst['processed'] ?></h4>
                                                    </li>
                                                    <li>
                                                        <h6 class="text-muted"><a href="myTask?taskStats=Approved" class="btn btn-success"></a> <?php echo 'Approved'; ?></h6>
                                                        <h4 class="m-b-0"><?= $wfst['completed'] ?></h4>
                                                    </li>
                                                    <?php //}               
                                                    ?>

                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <?php if ($rwgetRole['priority_wf'] == '1') { ?>
                            <div class="col-md-6 col-lg-4">
                                <?php ///print_r($wfst);die;              
                                ?>
                                <div class="box box-primary">
                                    <div class="card-box" style="height: 300px;">

                                        <div class="box-header">
                                            <h4 class="text-dark header-title margin-t-9 m-b-10" style="text-align:center"><?php echo $lang['wf_priority']; ?></h4>
                                            <div class="widget-chart text-center">
                                                <?php if ($wfst['up'] != '0' || $wfst['mp'] != '0' || $wfst['np'] != '0') { ?>
                                                    <div id="workflow-priority" style="height: 175px;"></div>
                                                <?php } else { ?><div class="ezeeoffice status">
                                                        <li align="center" class="list-group-item"><span class="text-alert"> <?= $lang['no_wf_priority']; ?></span></li>
                                                    </div><?php } ?>
                                                <ul class="list-inline m-t-15">

                                                    <?php //if ($rwgetRole['num_of_folder'] == '1'){                
                                                    ?>
                                                    <li>
                                                        <h6 class="text-muted"><a href="myTask?taskPrioty=1" class="btn btn-danger"></a> <?= $lang['Urgent']; ?></h6>
                                                        <h4 class="m-b-0"><?= $wfst['up'] ?></h4>
                                                    </li>
                                                    <?php //}           
                                                    ?>
                                                    <?php //if ($rwgetRole['num_of_file'] == '1'){               
                                                    ?>
                                                    <li>
                                                        <h6 class="text-muted"><a href="myTask?taskPrioty=2" class="btn btn-warning"></a> <?= $lang['Medium']; ?></h6>
                                                        <h4 class="m-b-0"><?= $wfst['mp'] ?></h4>
                                                    </li>
                                                    <li>
                                                        <h6 class="text-muted"><a href="myTask?taskPrioty=3" class="btn btn-success"></a> <?= $lang['Normal']; ?></h6>
                                                        <h4 class="m-b-0"><?= $wfst['np'] ?></h4>
                                                    </li>
                                                    <?php //}                 
                                                    ?>

                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <?php if ($rwgetRole['dashboard_mytask'] == '1') { ?>
                            <a href="myTask">
                                <div class="col-md-6 col-lg-4">
                                    <div class="widget-bg-color-icon card-box">
                                        <div class="bg-icon bg-icon-custom pull-left">
                                            <i class="md md-storage text-custom"></i>
                                        </div>
                                        <?php ?>
                                        <div class="text-center">
                                            <h4 class="text-dark"><strong><?php echo $lang['IN_TRAY']; ?>(<?php echo $wfst['pending']; ?>)</strong></h4>
                                            <!-- <h4 class="text-dark"><strong><?php echo $lang['IN_TRAY']; ?>(<?php //echo ($wfst['pending'] + $wfst['processed'] + $wfst['completed']); 
                                                                                                                ?>)</strong></h4> -->
                                            <p class="text-muted"><?php echo $lang['view_your_tray']; ?></p>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </a>
                        <?php } ?>
                        <?php if ($rwgetRole['review_intray'] == '1') { ?>
                            <a href="reviewintray">
                                <div class="col-md-6 col-lg-4">
                                    <div class="widget-bg-color-icon card-box">
                                        <div class="bg-icon bg-icon-custom pull-left">
                                            <i class="fa fa-eye text-custom"></i>
                                        </div>
                                        <?php
                                        $where = "";
                                        if ($_SESSION['cdes_user_id'] != 1) {
                                            $where = "WHERE  action_by = '$_SESSION[cdes_user_id]' and review_status='0' and next_task='0'";
                                        }
                                        $sql = "SELECT distinct count(ticket_id)as num FROM  `tbl_doc_review`  $where ";
                                        $runw = mysqli_query($db_con, $sql) or die('Error' . mysqli_error($db_con));
                                        $runw_res = mysqli_fetch_assoc($runw);
                                        $fondnum11 = $runw_res['num'];
                                        ?>
                                        <div class="text-center">
                                            <h4 class="text-dark"><strong><?php echo $lang['reviewintray']; ?>(<?php echo $fondnum11; ?>)</strong></h4>
                                            <p class="text-muted"><?php echo $lang['view_your_tray']; ?></p>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </a>
                        <?php } ?>
                        <?php if ($rwgetRole['initiate_file'] == '1') { ?>
                            <a href="initiateFile">
                                <div class="col-md-6 col-lg-4">
                                    <div class="widget-bg-color-icon card-box">
                                        <div class="bg-icon bg-icon-custom pull-left">
                                            <i class="fa  fa fa-paper-plane text-custom"></i>
                                        </div>
                                        <div class="text-center">
                                            <h4 class="text-dark text-custom"><strong><?php echo $lang['Initiate_Files']; ?></strong></h4>
                                            <p class="text-muted"><?php echo $lang['Initiate_Files']; ?></p>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </a>
                        <?php } ?>
                        <?php if ($rwgetRole['appoint_view'] == '1') { ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="box box-primary">
                                    <h4 class="text-center header-title text-dark m-t-15"><?= $lang['appoints'] ?></h4>
                                    <div class="box-body ">
                                        <ul class="todo-list ezeeoffice nicescroll" style="height: 231px;">
                                            <?php
                                            $i = 1;
                                            $td_query = mysqli_query($db_con, "select * from appointments where user_id='$_SESSION[cdes_user_id]' and is_archived='0' order by app_date desc");
                                            $appoint_no = mysqli_num_rows($td_query);
                                            if ($appoint_no > 0) {
                                                while ($td_res = mysqli_fetch_assoc($td_query)) {
                                            ?>
                                                    <a href="manage-appointment?aid=<?= urlencode(base64_encode($td_res['id'])) ?>">
                                                        <li class="list-group-item"><?= $i . '. ' . $td_res['title']; ?></li>
                                                    </a>
                                                <?php
                                                    $i++;
                                                }
                                            } else {
                                                ?><li align="center" class="list-group-item"><span class="text-alert"> <?= $lang['no_appoint_found']; ?></span></li><?php } ?>
                                        </ul>
                                    </div>
                                    <!-- /.box-body-->
                                </div>
                            </div>
                        <?php } ?>

                        <?php if ($rwgetRole['todo_view'] == '1') { ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="box box-primary">
                                    <h4 class="text-dark header-title m-t-15 m-b-5" style="text-align:center"><?= $lang['to_do_list'] ?></h4>
                                    <div class="box-body ">
                                        <ul class="todo-list ezeeoffice nicescroll" style="height: 234px;">
                                            <?php
                                            $j = 1;
                                            mysqli_set_charset($db_con, "utf8");
                                            $td_query = mysqli_query($db_con, "select * from todo_list where find_in_set($_SESSION[cdes_user_id],emp_id) and is_archived='0' order by task_date desc");
                                            $todo_no = mysqli_num_rows($td_query);
                                            if ($todo_no > 0) {
                                                while ($td_res = mysqli_fetch_assoc($td_query)) {
                                            ?>
                                                    <a href="manage-todo?tdid=<?= urlencode(base64_encode($td_res['id'])) ?>">
                                                        <li class="list-group-item"><?= $j . '. ' . $td_res['task_name'] ?></li>
                                                    </a>
                                                <?php
                                                    $j++;
                                                }
                                            } else {
                                                ?><li align="center" class="list-group-item"><span class="text-alert"> <?= $lang['no_to_do']; ?></span></li><?php } ?>
                                        </ul>
                                    </div>
                                    <!-- /.box-body-->
                                </div>
                            </div>

                        <?php } ?>
                        <?php if ($rwgetRole['metadata_search'] == '1') { ?>
                            <a href="metasearch">
                                <div class="col-md-6 col-lg-4">
                                    <div class="box box-primary">
                                        <h4 class="text-dark header-title m-t-15 m-b-5" style="text-align:center">&nbsp;<?php //echo $lang['METADATA_SEARCH'];                              
                                                                                                                        ?></h4>
                                        <div class="box-body ">
                                            <ul class="todo-list ezeeoffice nicescroll" style="height: 234px;">
                                                <div class="widget-bg-color-icon">
                                                    <div class="bg-icon bg-icon-custom pull-left">
                                                        <i class="fa fa-search text-custom"></i>
                                                    </div>
                                                    <div class="text-center">
                                                        <h4 class="text-dark text-custom"><strong><?php echo $lang['METADATA_SEARCH']; ?></strong></h4>
                                                        <p class="text-muted text-uppercase"><?php echo $lang['seerchsingle'] . ' ' . $lang['dFiles']; ?></p>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                </div>
                                            </ul>
                                        </div>
                                        <!-- /.box-body-->
                                    </div>
                                </div>
                            </a>
                        <?php } ?>
                        <?php
                        if ($rwgetRole['mis_report'] == '1') {
                        ?>
                            <div class="col-md-4 col-lg-4 col-sm-4 col-xs-4">
                                <a href="misUploadReport">
                                    <div class="widget-bg-color-icon card-box">
                                        <div class="bg-icon bg-icon-custom pull-left">
                                            <i class="ti-bar-chart text-custom"></i>
                                        </div>
                                        <div class="text-center dsh">
                                            <h5 class="text-dark"><strong><?php echo $lang['MIS_upload_report']; ?></strong></h5>
                                            <p class="text-muted text-uppercase"><?php echo $lang['MIS_upload_report']; ?></p>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </a>
                            </div>
                        <?php }
                        if ($rwgetRole['view_ocr_list'] == '1') { ?>
                            <a href="ocrList">
                                <div class="col-md-4 col-lg-4">
                                    <div class="widget-bg-color-icon card-box">
                                        <div class="bg-icon bg-icon-custom pull-left">
                                            <i class="fa fa-file-text-o text-custom"></i>
                                        </div>
                                        <div class="text-center">
                                            <?php
                                            $ocrp = mysqli_query($db_con, "select count(doc_id) as totalocr from tbl_document_master where ocr='1' and flag_multidelete='1'") or die('error' . mysqli_error($db_con));
                                            $rwOcrp = mysqli_fetch_array($ocrp);
                                            $ocric = mysqli_query($db_con, "select count(doc_id) as totalpending from tbl_document_master where ocr='0' and flag_multidelete='1'") or die('error' . mysqli_error($db_con));
                                            $rwOcric = mysqli_fetch_array($ocric);
                                            ?>
                                            <h4 class="text-dark"><strong><?php echo $lang['ocr_done_file']; ?> (<span class="text-dark text-center"><?php echo $rwOcrp['totalocr']; ?></span>)</strong></h4>
                                            <h4 class="text-dark"><strong><?php echo $lang['ocr_pending_file']; ?> (<span class="text-dark text-center"><?php echo $rwOcric['totalpending']; ?></span>)</strong></h4>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </a>
                        <?php }
                        if ($rwgetRole['mis_report'] == '1') {
                        ?>
                            <div id="num_of_files" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <div class="row">
                                                <?php
                                                $today = date("d-m-Y");
                                                ?>
                                                <div class="col-md-8 col-lg-8 col-sm-8 col-xs-8 align-center">
                                                    <h4><span class="align-center pull-right align-center" id="digit">Digitized Records as on <?php echo $today; ?></span></h4>
                                                </div>
                                                <?php
                                                //require_once './application/pages/function.php';
                                                //$perm = mysqli_query($db_con, "select sl_id from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]' group by sl_id");
                                                //$slperms = array();
                                                //while ($rwPerm = mysqli_fetch_assoc($perm)) {
                                                //  $slperms[] = $rwPerm['sl_id'];
                                                //                                                    }
                                                //
                                                //                                                    $sl_perm = implode(',', $slperms);
                                                //                                                    $slids = findsubfolder($sl_perm, $db_con);
                                                //
                                                //                                                    $slids = implode(',', $slids);
                                                //$parent = mysqli_query($db_con, "SELECT sl_id,sl_name FROM `tbl_storage_level` WHERE sl_id in($slids) and delete_status='0' order by sl_name asc");
                                                ?>
                                                <form method="post" action="pagesReport">
                                                    <div class="exptPdf">
                                                        <button type="submit" name="export" value="Export" title="EXPORT PDF" class="btn btn-primary btn btn-xs" style="margin-left: 160px;"> <i class="fa fa-download"></i> Export</button>
                                                    </div>
                                                    <div class="col-md-2 col-lg-2 col-sm-2 col-xs-2 pull-right">
                                                        <input type="hidden" name="select_Fm" value="EXCEL">
                                                        <input type="hidden" name="both" value="<?php echo $slids; ?>">

                                                    </div>
                                                </form>

                                            </div>
                                        </div>
                                        <div class="modal-body" style="width:100%; margin: auto; text-align: center; vertical-align: middle;" id="misReport">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <a href="#" data-toggle="modal" data-target="#num_of_files" id="getmisReport">
                                <div class="col-md-4 col-lg-4 col-lg-4 col-sm-4 col-xs-4">
                                    <div class="widget-bg-color-icon card-box">
                                        <div class="bg-icon bg-icon-custom pull-left">
                                            <i class="fa fa-file text-custom"></i>
                                        </div>
                                        <div class="text-center dsh">
                                            <h5 class="text-dark"><strong><?php echo $lang['nofiles']; ?> (<span class="text-dark text-center"><?php echo $totalFiles["files"]; ?></span>) </strong></h5>
                                            <p class="text-dark"><strong><?php echo $lang['NoPages']; ?> (<span class="text-dark text-center"><?php echo $totalFiles["numPages"]; ?></span>)</strong></p>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </a>
                        <?php } ?>
                    </div>


                    <?php if ($rwgetRole['view_raipur_letter'] == '1') { ?>
                        <div class="col-md-4 col-lg-4 col-sm-4 col-xs-4">
                            <a href="raipur.php">
                                <div class="widget-bg-color-icon card-box">
                                    <div class="bg-icon bg-icon-custom pull-left">
                                    <i class="fa fa-bars" aria-hidden="true"></i>
                                    </div>
                                    <div class="text-center dsh">
                                        <h5 class="text-dark"><strong> Letter Submission Form </strong></h5>
                                        <p class="text-muted text-uppercase"> RPP Letter Submission </p>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </a>
                        </div>
                    <?php } ?>


                    <?php if ($rwgetRole['view_raipur_report'] == '1') { ?>
                        <div class="col-md-4 col-lg-4 col-sm-4 col-xs-4">
                            <a href="raipur_report.php">
                                <div class="widget-bg-color-icon card-box">
                                    <div class="bg-icon bg-icon-custom pull-left">
                                    <i class="fa fa-bars" aria-hidden="true"></i>
                                    </div>
                                    <div class="text-center dsh">
                                        <h5 class="text-dark"><strong> RPP SATHYAMOORTHY JV </strong></h5>
                                        <p class="text-muted text-uppercase"> RPP Report </p>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </a>
                        </div>
                    <?php } ?>

                    <div class="col-md-4 col-lg-4 col-sm-4 col-xs-4">
                        <a href="raipur_report.php?loc=sent">
                            <div class="widget-bg-color-icon card-box">
                                <div class="bg-icon bg-icon-custom pull-left">
                                    <i class="fa fa-bars" aria-hidden="true"></i>
                                </div>
                                <div class="text-center dsh">
                                    <h5 class="text-dark"><strong> Sent </strong></h5>
                                    <!-- <p class="text-muted text-uppercase"> RPP Letter Submission </p> -->
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>  
                    <div class="col-md-4 col-lg-4 col-sm-4 col-xs-4">
                        <a href="raipur_report.php?loc=inbox">
                            <div class="widget-bg-color-icon card-box">
                                <div class="bg-icon bg-icon-custom pull-left">
                                    <i class="fa fa-bars" aria-hidden="true"></i>
                                </div>
                                <div class="text-center dsh">
                                    <h5 class="text-dark"><strong> Inbox </strong></h5>
                                    <!-- <p class="text-muted text-uppercase"> RPP Letter Submission </p> -->
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>  

                </div> <!-- end row -->

            </div>
        </div> <!-- container -->

        <?php require_once './application/pages/footer.php'; ?>
    </div> <!-- content -->
    <!-- END wrapper -->
    <?php require_once './application/pages/footerForjs.php'; ?>
    <!--Chartist Chart-->
    <script src="assets/plugins/chartist/js/chartist.min.js"></script>
    <script src="assets/plugins/chartist/js/chartist-plugin-tooltip.min.js"></script>
    <!-- Chart JS -->
    <script src="assets/plugins/chart.js/chart.min.js"></script>
    <!-- Counterup  -->
    <script>
        <?php if ($rwgetRole['user_graph'] == '1') { ?>
            var ctx = document.getElementById("bar");
            var myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ["<?php echo $lang['january']; ?>", "<?php echo $lang['febuary']; ?> ", "<?php echo $lang['march']; ?>", "<?php echo $lang['april']; ?>", "<?php echo $lang['may']; ?>", "<?php echo $lang['june']; ?>", "<?php echo $lang['july']; ?>", "<?php echo $lang['august']; ?>", "<?php echo $lang['september']; ?>", "<?php echo $lang['october']; ?>", "<?php echo $lang['november']; ?>", "<?php echo $lang['december']; ?>"],
                    datasets: [{
                        label: '<?php echo $lang['active_user_analytics']; ?> <?= $year; ?>',
                        fill: false,
                        lineTension: 0.1,
                        borderCapStyle: 'butt',
                        borderDash: [],
                        borderDashOffset: 0.0,
                        borderJoinStyle: 'miter',
                        pointBorderColor: "#a1c8df",
                        pointBackgroundColor: "#fff",
                        pointBorderWidth: 1,
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: "#a1c8df",
                        pointHoverBorderColor: "#a1c8df",
                        pointHoverBorderWidth: 2,
                        pointRadius: 1,
                        pointHitRadius: 10,
                        data: [<?= $datas; ?>],
                        backgroundColor: [
                            '#34d3eb'

                        ],
                        borderColor: [
                            '#acacac'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {

                                stepSize: 5
                            },
                            scaleLabel: {
                                display: true,
                                labelString: '<?php echo $lang['no_of_user']; ?>'
                            }
                        }],
                        xAxes: [{
                            ticks: {

                                stepSize: 1
                            },
                            scaleLabel: {
                                display: true,
                                labelString: '<?php echo $lang['months']; ?>'
                            }
                        }]
                    }
                }
            });
        <?php } ?>
        //creating lineChart

        new Chartist.Line('#line-chart-tooltips', {
            labels: ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'],
            series: [{
                    name: 'Upload Memory In MB',
                    data: [<?= $memorys ?>]
                },
                {
                    name: 'Download Memory In MB',
                    data: [<?= $down_memory ?>]
                }
            ]
        }, {
            plugins: [
                Chartist.plugins.tooltip()
            ]
        });
        var $chart = $('#line-chart-tooltips');
        var $toolTip = $chart
            .append('<div class="tooltip"></div>')
            .find('.tooltip')
            .hide();
        $chart.on('mouseenter', '.ct-point', function() {
            var $point = $(this),
                value = $point.attr('ct:value'),
                seriesName = $point.parent().attr('ct:series-name');
            $toolTip.html(seriesName + '<br>' + value).show();
        });
        $chart.on('mouseleave', '.ct-point', function() {
            $toolTip.hide();
        });
        $chart.on('mousemove', function(event) {
            $toolTip.css({
                left: (event.offsetX || event.originalEvent.layerX) - $toolTip.width() / 2 - 10,
                top: (event.offsetY || event.originalEvent.layerY) - $toolTip.height() - 40
            });
        });
    </script>
    <script src="assets/plugins/morris/morris.min.js"></script>
    <script src="assets/plugins/raphael/raphael-min.js"></script>
    <script type="text/javascript">
        //@sk(20918) Donut chart for task status
        <?php if ($rwgetRole['status_wf'] == '1') { ?>
            Morris.Donut({
                element: 'workflow-status',
                data: [{
                        label: "Pending",
                        value: <?= $wfst['pending'] ?>
                    },
                    {
                        label: "Processed",
                        value: <?= $wfst['processed'] ?>
                    },
                    {
                        label: "Approved",
                        value: <?= $wfst['completed'] ?>
                    }
                ],
                resize: true, //defaulted to true
                colors: ['#f05050', '#ffbd4a', '#81c868'],
            }).on('click', function(r, e) {
                switch (e.label) {
                    case "Pending":
                        window.location = 'myTask?taskStats=Pending';
                        break;
                    case "Processed":
                        window.location = 'myTask?taskStats=Processed';
                        break;
                    case "Approved":
                        window.location = 'myTask?taskStats=Approved';
                        break;
                }
            });
        <?php } ?>
        <?php if ($rwgetRole['priority_wf'] == '1') { ?>
            // for Workflow task priority
            Morris.Donut({
                element: 'workflow-priority',
                data: [{
                        label: "Urgent",
                        value: <?= $wfst['up'] ?>
                    },
                    {
                        label: "Medium",
                        value: <?= $wfst['mp'] ?>
                    },
                    {
                        label: "Normal",
                        value: <?= $wfst['np'] ?>
                    }
                ],
                resize: true, //defaulted to true
                colors: ['#f05050', '#ffbd4a', '#81c868'],
            }).on('click', function(r, e) {
                switch (e.label) {
                    case "Urgent":
                        window.location = 'myTask?taskPrioty=1';
                        break;
                    case "Medium":
                        window.location = 'myTask?taskPrioty=2';
                        break;
                    case "Normal":
                        window.location = 'myTask?taskPrioty=3';
                        break;
                }
            });
        <?php } ?>
    </script>
    <script src="assets/pages/jquery.todo.js"></script>
    <script src="assets/plugins/jquery-sparkline/jquery.sparkline.min.js"></script>
    <!--<script src="assets/pages/jquery.dashboard_3.js"></script>-->
    <link href="assets/plugins/radial/radial.css" rel="stylesheet">
    <script src="assets/pages/jquery.chartjs.init.js"></script>
    <script>
        $('#sparkline3').sparkline([<?php echo $rwgetRole['num_of_folder'] == '1' ? $totalFiles["totalFolder"] : 0 ?>, <?php echo $rwgetRole['num_of_file'] == '1' ? $totalFiles["files"] : 0 ?>, <?php echo $rwgetRole['num_of_file'] == '1' ? $totalFiles["numPages"] : 0 ?>], {
            type: 'pie',
            width: '150',
            height: '150',
            sliceColors: ['#193860', '#34d3eb', '#FFBD4A'],
        });
        //Pie chart
    </script>
    <!-- full calendar -->
    <script src="assets/plugins/moment/moment.js"></script>
    <script src="assets/plugins/fullcalendar/js/fullcalendar.min.js"></script>
    <script type="text/javascript">
        var eventDetails = [];
        <?php
        try {
            /** All ToDo's */
            if ($rwgetRole['todo_view'] == '1') {
                $todoResponse = $todo->getAllTodo($db_con, $_SESSION['cdes_user_id']);

                for ($i = 0; $i < sizeof($todoResponse); $i++) {
                    $formatedDate = $todo->formatDate($todoResponse[$i][task_date], ',');
                    $formatedTime = $todo->formatTime24($todoResponse[$i][task_time], ',');
                    echo "var jsonList = {id: '" . $todoResponse[$i][id] . "',title :'" . $todoResponse[$i][task_name] . "', start : new Date('" . $formatedDate . "'),backgroundColor: '#f39c12', borderColor : '#f39c12', url : './manage-todo?tdid=" . urlencode(base64_encode($todoResponse[$i][id])) . "'};";
                    echo "eventDetails.push(jsonList);";
                }
            }

            if ($rwgetRole['appoint_view'] == '1') {
                mysqli_set_charset($db_con, "utf8");
                $appointResponse = $appoint->getAllAppointment($db_con, $_SESSION['cdes_user_id']);

                //print_r($appointResponse);
                // die;   
                for ($i = 0; $i < sizeof($appointResponse); $i++) {
                    $formatedDate = $appoint->formatDate($appointResponse[$i][app_date], ',');
                    $formatedTime = $appoint->formatTime24($appointResponse[$i][app_time], ',');
                    echo "var jsonList = {id: '" . $appointResponse[$i][id] . "',title :'" . $appointResponse[$i][title] . "', start : new Date('" . $formatedDate . "'),backgroundColor: '#228B22', borderColor : '#228B22', url : './manage-appointment?aid=" . urlencode(base64_encode($appointResponse[$i][id])) . "'};";
                    echo "eventDetails.push(jsonList);";
                }
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }
        ?>
        $('#calendar').fullCalendar({
            dayNamesShort: ['<?= $lang['sunday']; ?>', '<?= $lang['monday']; ?>', '<?= $lang['tuesday']; ?>', '<?= $lang['wednesday']; ?>', '<?= $lang['thursday']; ?>', '<?= $lang['friday']; ?>', '<?= $lang['saturday']; ?>'],
            header: {
                left: 'prev,next today',
                center: 'title',
                /* right: 'month,agendaWeek,agendaDay'*/
                right: 'month'
            },

            //titleFormat: '<?= $lang['january']; ?>',        
            buttonText: {
                today: '<?= $lang['today']; ?>',
                month: '<?= $lang['months']; ?>',
                week: 'week',
                day: 'day'
            },
            //Random default events
            events: eventDetails,
            displayEventTime: false,
            editable: false,
            droppable: false, // this allows things to be dropped onto the calendar !!!
            drop: function(date, allDay) { // this function is called when something is dropped
                // retrieve the dropped element's stored Event Object
                var originalEventObject = $(this).data('eventObject')

                // we need to copy it, so that multiple events don't have a reference to the same object
                var copiedEventObject = $.extend({}, originalEventObject)

                // assign it the date that was reported
                copiedEventObject.start = date
                copiedEventObject.allDay = allDay
                copiedEventObject.backgroundColor = $(this).css('background-color')
                copiedEventObject.borderColor = $(this).css('border-color')

                // render the event on the calendar
                // the last `true` argument determines if the event "sticks" (http://arshaw.com/fullcalendar/docs/event_rendering/renderEvent/)
                $('#calendar').fullCalendar('renderEvent', copiedEventObject, true)

                // is the "remove after drop" checkbox checked?
                if ($('#drop-remove').is(':checked')) {
                    // if so, remove the element from the "Draggable Events" list
                    $(this).remove()
                }

            },
            eventClick: function(calEvent, jsEvent, view) {
                window.localStorage.setItem('rowKey', calEvent.id);
            }
        })

        /* ADDING EVENTS */
        var currColor = '#3c8dbc' //Red by default
        //Color chooser button
        var colorChooser = $('#color-chooser-btn')
        $('#color-chooser > li > a').click(function(e) {
            e.preventDefault()
            //Save color
            currColor = $(this).css('color')
            //Add color effect to button
            $('#add-new-event').css({
                'background-color': currColor,
                'border-color': currColor
            })
        })
        $('#add-new-event').click(function(e) {
            e.preventDefault()
            //Get value and make sure it is not null
            var val = $('#new-event').val()
            if (val.length == 0) {
                return
            }

            //Create events
            var event = $('<div />')
            event.css({
                'background-color': currColor,
                'border-color': currColor,
                'color': '#fff'
            }).addClass('external-event')
            event.html(val)
            $('#external-events').prepend(event)

            //Add draggable funtionality
            init_events(event)

            //Remove event from text input
            $('#new-event').val('')
        })

        $("a#getmisReport").click(function() {
            slid = '<?php echo $slids; ?>';
            $.post("application/ajax/dashboardreports.php", {
                slids: slid,
                report: 'mistReport'
            }, function(result, status) {
                if (status == 'success') {
                    $("#misReport").html(result);
                }
            });
        });
    </script>
    <!-- /full calendar -->
</body>

</html>