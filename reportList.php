<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    require_once './application/config/database.php';
    require_once './application/pages/head.php';
    $sameGroupIDs = array();
    $group = mysqli_query($db_con, "select group_id from tbl_bridge_grp_to_um where find_in_set('$_SESSION[cdes_user_id]',user_ids)") or die('Error' . mysqli_error($db_con));
    while ($rwGroup = mysqli_fetch_assoc($group)) {
        $sameGroupIDs[] = $rwGroup['group_id'];
    }
    $sameGroupIDs = array_unique($sameGroupIDs);
    sort($sameGroupIDs);
    $sameGroupIDs = implode(',', $sameGroupIDs);
    //for user role
    $ses_val = $_SESSION;
    mysqli_set_charset($db_con, "utf8");
    $langDetail = mysqli_query($db_con, "select * from tbl_language where lang_name='$_SESSION[lang]'") or die('Error : ' . mysqli_error($db_con));
    $langDetail = mysqli_fetch_assoc($langDetail);
    $chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));
    $rwgetRole = mysqli_fetch_assoc($chekUsr);
    $upcount = mysqli_query($db_con, "select * from tbl_wf_reports");
    $row1 = mysqli_fetch_assoc($upcount);
    //print_r($upcount);die;	
    if ($rwgetRole['view_report'] != '1') {
        header('Location: ./index');
    }
    $id = base64_decode(urldecode($_GET['wfid']));
    ?>
    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />

    <link href="assets/plugins/multiselect/css/multi-select.css"  rel="stylesheet" type="text/css" />

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
                                        <a href="./addWorkflow"><?php echo $lang['Workflow_management']; ?></a>
                                    </li>
                                    <li class="active">
                                        <?php echo $lang['Wf_Lst']; ?> 
                                    </li>
                                    <li class="active">
                                        <?php echo $lang['Report_List']; ?> 
                                    </li>
                                   
                                    <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                                </ol>
                            </div>
                        </div>
                        <div class="box box-primary">
                            <div class="panel">

                                <div class="panel-body">

                                    <?php if ($rwgetRole['add_report'] == '1') { ?>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="m-b-30">
                                                     <li> <a href="#" data-toggle="modal" data-target="#help-modal" id="helpview" data="49" title="<?= $lang['help']; ?>"><i class="fa fa-question-circle"></i> </a></li>
                                                    <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right" data-toggle="modal" data-target="#con-close-modal5"><?php echo $lang['add_new_report']; ?> <i class="fa fa-plus"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <div class="">
                                        <?php
                                        $where = "";
                                        if (isset($_GET['report']) && !empty($_GET['report'])) {
                                            $reportName = xss_clean($_GET['report']);
                                            $where .= "and report_name like '%$reportName%'";
                                        }
                                        $report = "select * from  tbl_wf_reports where wf_id ='$id' ";
                                        mysqli_set_charset($db_con, "utf8");
                                        $run = mysqli_query($db_con, $report) or die('Error' . mysqli_error($db_con));

                                        $foundnum = mysqli_num_rows($run);
                                        if ($foundnum > 0) {
                                            if (is_numeric($_GET['limit'])) {
                                                $per_page = preg_replace("/[^0-9]/", "", $_GET['limit']);
                                            } else {
                                                $per_page = 10;
                                            }
                                            $start = isset($_GET['start']) ? ($_GET['start'] > 0) ? $_GET['start'] : 0 : 0;
                                            $max_pages = ceil($foundnum / $per_page);
                                            if (!$start) {
                                                $start = 0;
                                            }
                                            $limit = $_GET['limit'];
                                            ?>
                                            <div class="box-body">
                                                <label><?php echo $lang['show_lst']; ?></label>
                                                <select id="limit" class="input-sm m-t-10 m-b-10">
                                                    <option value="10" <?php
                                                    if ($limit == 10) {
                                                        echo 'selected';
                                                    }
                                                    ?>>10</option>
                                                    <option value="25" <?php
                                                    if ($limit == 25) {
                                                        echo 'selected';
                                                    }
                                                    ?>>25</option>
                                                    <option value="50" <?php
                                                    if ($limit == 50) {
                                                        echo 'selected';
                                                    }
                                                    ?>>50</option>
                                                    <option value="250" <?php
                                                    if ($limit == 250) {
                                                        echo 'selected';
                                                    }
                                                    ?>>250</option>
                                                    <option value="500" <?php
                                                    if ($limit == 500) {
                                                        echo 'selected';
                                                    }
                                                    ?>>500</option>
                                                </select> 
                                                <label> <?php echo $lang['Report_List']; ?> </label>
                                                <div class="pull-right record m-t-10">
                                                    <label><?php echo $start + 1 ?> <?php echo $lang['To'] ?> <?php
                                                        if ($start + $per_page > $foundnum) {
                                                            echo $foundnum;
                                                        } else {
                                                            echo ($start + $per_page);
                                                        }
                                                        ?> <span><?php echo $lang['ttl_recrds']; ?> : <?php echo $foundnum; ?></span></label>
                                                </div>
                                                <?php
                                                mysqli_set_charset($db_con, "utf8");
                                                $users = mysqli_query($db_con, "select * from  tbl_wf_reports where wf_id ='$id' ")or die('Error:' . mysqli_error($db_con));
                                                if (mysqli_num_rows($users) == 0) {
                                                    
                                                }

                                                showData($users, $rwgetRole, $db_con, $privilegeSession, $lang);
                                                ?>

                                                <?php
                                                echo "<center>";
                                                $prev = $start - $per_page;
                                                $next = $start + $per_page;

                                                $adjacents = 3;
                                                $last = $max_pages - 1;
                                                if ($max_pages > 1) {
                                                    ?>

                                                    <ul class='pagination strgePage'>
                                                        <?php
                                                        //previous button
                                                        if (!($start <= 0))
                                                            echo " <li><a href='?start=$prev&tktid=$_GET[tktid]&limit=$per_page'>$lang[Prev]</a> </li>";
                                                        else
                                                            echo " <li class='disabled'><a href='javascript:void(0)'>$lang[Prev]</a> </li>";
                                                        //pages 
                                                        if ($max_pages < 7 + ($adjacents * 2)) {   //not enough pages to bother breaking it up
                                                            $i = 0;
                                                            for ($counter = 1; $counter <= $max_pages; $counter++) {
                                                                if ($i == $start) {
                                                                    echo "<li class='active'><a href='?start=$i&limit=$per_page&tktid=" . $_GET['tktid'] . "'><b>$counter</b></a> </li>";
                                                                } else {
                                                                    echo "<li><a href='?start=$i&limit=$per_page&tktid=" . $_GET['tktid'] . "'>$counter</a></li> ";
                                                                }
                                                                $i = $i + $per_page;
                                                            }
                                                        } elseif ($max_pages > 5 + ($adjacents * 2)) {    //enough pages to hide some
                                                            //close to beginning; only hide later pages
                                                            if (($start / $per_page) < 1 + ($adjacents * 2)) {
                                                                $i = 0;
                                                                for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                                                                    if ($i == $start) {
                                                                        echo " <li class='active'><a href='?start=$i&limit=$per_page&tktid=" . $_GET['tktid'] . "'><b>$counter</b></a></li> ";
                                                                    } else {
                                                                        echo "<li> <a href='?start=$i&limit=$per_page&tktid=" . $_GET['tktid'] . "'>$counter</a> </li>";
                                                                    }
                                                                    $i = $i + $per_page;
                                                                }
                                                            }
                                                            //in middle; hide some front and some back
                                                            elseif ($max_pages - ($adjacents * 2) > ($start / $per_page) && ($start / $per_page) > ($adjacents * 2)) {
                                                                echo " <li><a href='?start=0&limit=$per_page&tktid=$_GET[tktid]'>1</a></li> ";
                                                                echo "<li><a href='?start=$per_page&limit=$per_page&tktid=$_GET[tktid]'>2</a></li>";
                                                                echo "<li><a href='javascript:void(0)'>...</a></li>";

                                                                $i = $start;
                                                                for ($counter = ($start / $per_page) + 1; $counter < ($start / $per_page) + $adjacents + 2; $counter++) {
                                                                    if ($i == $start) {
                                                                        echo " <li class='active'><a href='?start=$i&limit=$per_page&tktid=" . $_GET['tktid'] . "'><b>$counter</b></a></li> ";
                                                                    } else {
                                                                        echo " <li><a href='?start=$i&limit=$per_page&tktid=" . $_GET['tktid'] . "'>$counter</a> </li>";
                                                                    }
                                                                    $i = $i + $per_page;
                                                                }
                                                            }
                                                            //close to end; only hide early pages
                                                            else {
                                                                echo "<li> <a href='?start=0&limit=$per_page&tktid=$_GET[tktid]'>1</a> </li>";
                                                                echo "<li><a href='?start=$per_page&limit=$per_page&tktid=$_GET[tktid]'>2</a></li>";
                                                                echo "<li><a href='javascript:void(0)'>...</a></li>";

                                                                $i = $start;
                                                                for ($counter = ($start / $per_page) + 1; $counter <= $max_pages; $counter++) {
                                                                    if ($i == $start) {
                                                                        echo " <li class='active'><a href='?start=$i&limit=$per_page&tktid=" . $_GET['tktid'] . "'><b>$counter</b></a></li> ";
                                                                    } else {
                                                                        echo "<li> <a href='?start=$i&limit=$per_page&tktid=" . $_GET['tktid'] . "'>$counter</a></li> ";
                                                                    }
                                                                    $i = $i + $per_page;
                                                                }
                                                            }
                                                        }
                                                        //next button
                                                        if (!($start >= $foundnum - $per_page))
                                                            echo "<li><a href='?start=$next&tktid=$_GET[tktid]&limit=$per_page'>$lang[Next]</a></li>";
                                                        else
                                                            echo "<li class='disabled'><a href='javascript:void(0)'>$lang[Next]</a></li>";
                                                        ?>
                                                    </ul>
                                                    <?php
                                                }
                                                echo "</center>";
                                            } else {
                                                ?>
                                                <table class="table table-striped table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th><?php echo $lang['SNO']; ?></th>
                                                            <th><?php echo $lang['Report_Name']; ?></th>
                                                            <th><?php echo $lang['Actions']; ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tr>
                                                        <td colspan="3"><strong class="text-danger"><?php echo $lang['Who0ps!_No_Records_Found']; ?></strong></td>
                                                    </tr>
                                                </table>
                                                <?php
                                            }
                                            ?>
                                        </div>

                                    </div>
                                </div>
                                <!-- end: page -->
                            </div> <!-- end Panel -->
                        </div> <!-- container -->
                    </div>
                </div> <!-- content -->


                <!-- /Right-bar -->
                <!-- MODAL -->
                <div id="dialog" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog"> 
                        <div class="panel panel-danger panel-color"> 
                            <div class="panel-heading"> 
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                                <label><h2 class="panel-title"><?= $lang['Are_u_confirm'] ?></h2></label> 
                            </div> 
                            <form method="post">
                                <div class="panel-body">
                                    <p style="color: red;"><?= $lang['report_delete_msg'] ?></p>
                                </div>
                                <div class="modal-footer">
                                    <div class="col-md-12 text-right">
                                        <input type="hidden" id="uid" name="uid">
                                        <button type="submit" name="delete" id="dialogConfirm" class="btn btn-danger waves-effect waves-light"><i class="fa fa-trash-o"></i> <?php echo $lang['Delete']; ?></button>
                                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                                    </div>
                                </div>
                            </form>
                        </div> 
                    </div>
                </div>
                <!-- end Modal -->

                <?php
                $sameGroupIDs = array();
                $group = mysqli_query($db_con, "select group_id from tbl_bridge_grp_to_um where find_in_set('$_SESSION[cdes_user_id]',user_ids)") or die('Error' . mysqli_error($db_con));
                while ($rwGroup = mysqli_fetch_assoc($group)) {
                    $sameGroupIDs[] = $rwGroup['group_id'];
                }
                $sameGroupIDs = array_unique($sameGroupIDs);
                sort($sameGroupIDs);

                //$sameGroupIDs = implode(',', $sameGroupIDs);
                if ($_GET['group_id']) {
                    $getWfID = mysqli_query($db_con, "select workflow_id,group_id from tbl_workflow_to_group WHERE find_in_set('$_GET[group_id]',group_id)") or die("Error " . mysqli_error($db_con));
                } else {
                    $getWfID = mysqli_query($db_con, "select workflow_id,group_id from tbl_workflow_to_group") or die("Error " . mysqli_error($db_con));
                }
                while ($RwgetWfID = mysqli_fetch_assoc($getWfID)) {
                    $WFId = $RwgetWfID['workflow_id'];
                    $workname = $RwgetWfID['workflow_name'];
                    ?>

                    <!---assign meta-data model start ---->
                    <div id="con-close-modal5" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog"> 
                            <div class="modal-content"> 
                                <div class="modal-header"> 
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                                    <h4 class="modal-title"><?php echo $lang['assign_workflow_report_fields']; ?><?= $workname; ?></h4> 
                                </div> 
                                <form action="#"  method="post">
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-12 m-b-10">
                                                <label> <strong><?php echo $lang['wgroup']; ?> :<span class="text-alert"> *</span> </strong></label>
                                                <select class="select2 select2-multiple" name="groupswf[]" multiple="multiple" multiple data-placeholder="<?php echo $lang['wgroup']; ?>" required parsley-trigger="change" id="groups">
                                                    <?php
                                                    mysqli_set_charset($db_con, "utf8");
                                                    $group_permission = mysqli_query($db_con, "SELECT group_id,user_ids FROM `tbl_bridge_grp_to_um`");
                                                    while ($allGroupRow = mysqli_fetch_array($group_permission)) {
                                                        $user_ids = explode(',', $allGroupRow['user_ids']);
                                                        if (in_array($_SESSION['cdes_user_id'], $user_ids)) {
                                                            $grp = mysqli_query($db_con, "select group_id,group_name from tbl_group_master WHERE group_id='$allGroupRow[group_id]' order by group_name asc") or die('Error' . mysqli_error($db_con));
                                                            while ($rwGrp = mysqli_fetch_assoc($grp)) {
                                                                if (in_array($rwGrp['group_id'], explode(",", $_REQUEST['groups']))) {
                                                                    echo '<option value="' . $rwGrp['group_id'] . '" selected>' . $rwGrp['group_name'] . '</option>';
                                                                } else {
                                                                    echo'<option value="' . $rwGrp['group_id'] . '">' . $rwGrp['group_name'] . '</option>';
                                                                }
                                                            }
                                                        }
                                                    }
                                                    ?>    
                                                </select>
                                            </div>

                                            <div class="col-md-12 shiv metaa">
                                                <strong><?php echo $lang['Report_Name']; ?> : <span class="text-alert">* </span></strong>
                                                <input name="rname" id="rname" class="form-control specialchaecterlock translatetext" required placeholder="<?= $lang['Report_Name']; ?>">
                                            </div>

                                            <div class="col-md-12 shiv metaa" style="margin-top:10px;">
                                                <strong><?php echo $lang['Field_Slt']; ?> : <span class="text-alert">*</span></strong>
                                                <strong style="margin-left: 170px;"><?php echo $lang['Fld_Asnd']; ?></strong>
                                                <select multiple="multiple" class="multi-select" id="my_multi_select1" name="my_multi_select1[]" data-plugin="multiselect" required>
                                                    <option value="action_by"><?= $lang['approved_by']; ?></option>
                                                    <option value="task_status"><?= $lang['Task_Status']; ?></option>
                                                    <option value="start_date"><?= $lang['submitted_date']; ?></option>
                                                    <option value="assign_by"><?= $lang['Assigned_By']; ?></option>
                                                    <?php
                                                    $qry = mysqli_query($db_con, "select * from tbl_bridge_workflow_to_form where workflow_id='$id'") or die("Error bridge table:" . mysqli_error($db_con));
                                                    $resultFormId = mysqli_fetch_assoc($qry);
                                                    $fid = $resultFormId['form_id'];
                                                    $formField = mysqli_query($db_con, "select * from  tbl_form_attribute where fid='$fid' and dependency_Id is Null");
                                                    while ($rowdata = mysqli_fetch_assoc($formField)) {
                                                        if ($rowdata['type'] == "header") {
                                                            
                                                        } else {
                                                            ?>
                                                            <option value="<?= $rowdata['name']; ?>"><?= $rowdata['label']; ?></option>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </select>

                                            </div>
                                            <div id="modalModify"></div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <input type="hidden" value="<?= $id ?>" name="id">
                                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                                        <button class="btn btn-primary waves-effect waves-light" type="submit" name="assignReport"><?php echo $lang['Submit']; ?></button>
                                    </div>
                                </form>

                            </div> 
                        </div>
                    </div><!--ends assign-meta-data modal -->  
                <?php } ?>
                <?php
                $updateqry = mysqli_query($db_con, "select * from tbl_wf_reports");
                while ($row = mysqli_fetch_assoc($updateqry)) {
                    ?>

                    <!---assign meta-data model start ---->
                    <div id="con-close-modal<?= $row['rp_id'] ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog"> 
                            <div class="modal-content"> 
                                <div class="modal-header"> 
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                                    <h4 class="modal-title"><?php echo $lang['assign_workflow_report_fields']; ?> <?= $workname; ?></h4> 

                                </div> 
                                <form method="post">
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <?php
                                                $idrpo = $row['rp_id'];
                                                $workflGroupMap = array();
                                                $grpbrg = mysqli_query($db_con, "select group_id from tbl_bridge_grp_to_report where report_id='$idrpo'") or die('Error : ' . mysqli_error($db_con));
                                                $rwrkfltogrp = mysqli_fetch_assoc($grpbrg);
                                                $workflGroupMap = explode(",", $rwrkfltogrp['group_id']);
                                                ?>
                                                <label><strong><?php echo $lang['wgroup']; ?> :<span class="text-alert"> *</span></strong></label>
                                                <select class="select2 select2-multiple" name="groupswf[]" multiple="multiple" multiple data-placeholder="<?php echo $lang['wgroup']; ?>" required parsley-trigger="change" id="groups">
                                                    <?php
                                                    $group = mysqli_query($db_con, "select group_id from tbl_bridge_grp_to_um where find_in_set('$_SESSION[cdes_user_id]',user_ids)") or die('Error' . mysqli_error($db_con));

                                                    while ($rwGroup = mysqli_fetch_assoc($group)) {
                                                        $sameGroupIDs[] = $rwGroup['group_id'];
                                                    }
                                                    $sameGroupIDs = array_unique($sameGroupIDs);
                                                    sort($sameGroupIDs);
                                                    $grp = mysqli_query($db_con, "select * from tbl_group_master") or die('error' . mysqli_error($db_con));
                                                    while ($rwGrp = mysqli_fetch_assoc($grp)) {
                                                        if (in_array($rwGrp['group_id'], $workflGroupMap)) {
                                                            echo '<option value="' . $rwGrp['group_id'] . '" selected>' . $rwGrp['group_name'] . '</option>';
                                                        } else {
                                                            echo '<option value="' . $rwGrp['group_id'] . '" >' . $rwGrp['group_name'] . '</option>';
                                                        }
                                                    }
                                                    ?>    

                                                </select>
                                            </div>
                                            <div class="col-md-12">
                                                <label><strong><?php echo $lang['Report_Name']; ?> : <span class="text-alert">* </span></strong></label>
                                                <input name="rname" id="<?= $row['rp_id'] ?>" class="form-control specialchaecterlock translatetext" required value="<?= $row['report_name'] ?>" placeholder="<?php echo $lang['Report_Name']; ?>" >
                                            </div>
                                            <div class="col-md-12 shiv metaa" style="margin-top:10px;">
                                                <strong><?php echo $lang['Field_Slt']; ?> : <span class="text-alert">*</span></strong>
                                                <strong style="margin-left: 170px;"><?php echo $lang['Fld_Asnd']; ?></strong>
                                                <select multiple="multiple" class="multi-select" id="my_multi_select1" name="my_multi_select1[]" data-plugin="multiselect" required>
                                                    <?php
                                                    $resultCol = explode(",", $row['coloums']);
                                                    //static check for empid, action by,task status,submitted day, name,cod date
                                                    if (in_array("action_by", $resultCol)) {
                                                        echo "<option value='action_by' selected>" . $lang['approved_by'] . "</option>";
                                                    } else {
                                                        echo "<option value='action_by'>" . $lang['approved_by'] . "</option>";
                                                    }
                                                    if (in_array("task_status", $resultCol)) {
                                                        echo "<option value='task_status' selected>" . $lang['Task_Status'] . "</option>";
                                                    } else {
                                                        echo "<option value='task_status' >" . $lang['Task_Status'] . "</option>";
                                                    }
                                                    if (in_array("start_date", $resultCol)) {
                                                        echo "<option value='start_date' selected>" . $lang['submitted_date'] . "</option>";
                                                    } else {
                                                        echo "<option value='start_date' >" . $lang['submitted_date'] . "</option>";
                                                    }
                                                    if (in_array("assign_by", $resultCol)) {
                                                        echo "<option value='assign_by' selected>" . $lang['Assigned_By'] . "</option>";
                                                    } else {
                                                        echo "<option value='assign_by'>" . $lang['Assigned_By'] > "</option>";
                                                    }
                                                    //static check close
                                                    $qry = mysqli_query($db_con, "select * from tbl_bridge_workflow_to_form where workflow_id='$id'") or die("Error bridge table:" . mysqli_error($db_con));
                                                    $resultFormId = mysqli_fetch_assoc($qry);
                                                    $fid = $resultFormId['form_id'];
                                                    $formField = mysqli_query($db_con, "select * from  tbl_form_attribute where fid='$fid' and dependency_Id is Null");
                                                    while ($rowdata = mysqli_fetch_assoc($formField)) {
                                                        if ($rowdata['type'] == "header") {
                                                            
                                                        } else {
                                                            if (in_array($rowdata['name'], $resultCol)) {
                                                                ?>
                                                                <option value="<?= $rowdata['name']; ?>" selected><?= $rowdata['label']; ?></option>
                                                                <?php
                                                            } else {
                                                                ?>

                                                                <option value="<?= $rowdata['name']; ?>"><?= $rowdata['label']; ?></option>
                                                                <?php
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </select>

                                            </div>
                                            <div id="modalModify"></div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <input type="hidden" value="<?= $row['rp_id'] ?>" name="id">
                                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                                        <button class="btn btn-primary waves-effect waves-light" type="submit" name="Updatereport"><?php echo $lang['Submit']; ?></button>
                                    </div>
                                </form>

                            </div> 
                        </div>
                    </div><!--ends assign-meta-data modal -->  
                   
                <?php } ?>

                <!-- END wrapper -->

            </div>
            <!-- END wrapper -->
            <?php require_once './application/pages/footer.php'; ?>

        </div>
        <!-- ============================================================== -->
        <!-- End Right content here -->
        <!-- ============================================================== -->
        <!-- Right Sidebar -->
        <?php //require_once './application/pages/rightSidebar.php';  ?>
        <?php require_once './application/pages/footerForjs.php'; ?>
        <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>
        <script type="text/javascript">
                                        $(document).ready(function () {
                                            $('form').parsley();

                                        });
                                        $(".select2").select2();

        </script>
        <script>
            //for avoid special charecter
            $('#report').keyup(function ()
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
            $('#report').bind(function () {
                $(this).val($(this).val().replace(/[<>]/g, ""))
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
            jQuery(document).ready(function ($) {
                $("#limit").change(function () {
                    lval = $(this).val();
                    url = removeParam("limit", url);
                    url = url + "&limit=" + lval;
                    window.open(url, "_parent");
                });
            });

            $("a#removeRow").click(function () {
                var id = $(this).attr('data');
                $("#uid").val(id);
            });

        </script>

        <!--for multiselect-->
        <script type="text/javascript" src="assets/plugins/multiselect/js/jquery.multi-select.js"></script>
        <script src="assets/js/jquery.core.js"></script>
        <!-------------->
        <?php
        //metareport
        if (isset($_POST['assignReport'], $_POST['token'])) {
            $group = implode(",", $_POST['groupswf']);
            $wfid = $_POST['id'];
            $rname = $_POST['rname'];
            $coloums = implode(",", $_POST['my_multi_select1']);
            mysqli_set_charset($db_con, "utf8");
            $rNameQry = mysqli_query($db_con, "select * from tbl_wf_reports where report_name='$rname'") or die("Error:" . mysqli_error($db_con));
            if (mysqli_num_rows($rNameQry) > 0) {
                echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","Report Name Already Exist !");</script>';
            } else {
                $qry = mysqli_query($db_con, "insert into tbl_wf_reports (`wf_id`,`coloums`,`report_name`) values ('$wfid','$coloums','$rname')") or die("Reports fields error:" . mysqli_error($db_con));
                if ($qry) {
                    $reportid = mysqli_insert_id($db_con);
                    $record = mysqli_query($db_con, "insert into tbl_bridge_grp_to_report(`report_id`,`group_id`) values('$reportid','$group')")or die("Reports group error:" . mysqli_error($db_con));
                    ;
                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'WorkFlow Report $rname Added','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                    echo '<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['report_add'] . '");</script>';
                } else {
                    echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['report_add_failed'] . '");</script>';
                }
            }
            mysqli_close($db_con);
        }

        if (isset($_POST['Updatereport'], $_POST['token'])) {
            $group = implode(",", $_POST['groupswf']);
            $reportname = $_POST['rname'];
            $rid = $_POST['id'];
            //print_r( $_POST['my_multi_select1']);
            $rcoloum = implode(",", $_POST['my_multi_select1']);
            mysqli_set_charset($db_con, "utf8");
            $update = mysqli_query($db_con, "update tbl_wf_reports set report_name='$reportname',coloums='$rcoloum'  where rp_id='$rid'") or die("Update Problem:" . mysqli_error($db_con));
            if ($update) {
                $upgrp = mysqli_query($db_con, "update tbl_bridge_grp_to_report  set group_id='$group' where report_id='$rid'");
                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'WorkFlow Report $reportname Updated','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));

                echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['report_Updated_Successfully'] . '");</script>';
            } else {
                echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['ruf'] . '");</script>';
            }
        }
        if (isset($_POST['delete'], $_POST['token'])) {
            $id = $_POST['uid'];
            $id = mysqli_real_escape_string($db_con, $id);
            $delreportName = mysqli_query($db_con, "select * from tbl_wf_reports where rp_id='$id'") or die(mysqli_error(db_con));
            $reponame = mysqli_fetch_assoc($delreportName);
            $delNme = mysqli_query($db_con, "delete from tbl_wf_reports where rp_id='$id'") or die("Error Occurs Delete:" . mysqli_error($db_con));
            if ($delNme) {
                $wreportdel = "Report " . $reponame['report_name'] . " Deleted";
                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'$wreportdel','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Report_Deleted_Successfully'] . '");</script>';
            } else {
                echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Failed_to_delete_report'] . '");</script>';
            }
            mysqli_close($db_con);
        }
        ?>

        <?php

        function showData($user, $rwgetRole, $db_con, $privilegeSession, $lang) {
            ?>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th><?php echo $lang['SNO']; ?></th>
                        <th><?php echo $lang['Report_Name']; ?></th>
                        <th><?php echo $lang['Actions']; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    $i += $start;
                    while ($rwUser = mysqli_fetch_assoc($user)) {
                        ?>
                        <tr class="gradeX">
                            <td><?php echo $i; ?></td>
                            <td><?php echo $rwUser['report_name']; ?></td>
                            <td class="actions">
                                <?php if ($rwgetRole['update_report'] == '1') { ?>
                                    <a href="#" class="on-default edit-row btn btn-primary" data-toggle="modal" data-target="#con-close-modal<?php echo $rwUser['rp_id']; ?>"  data="<?php echo $rwUser['rp_id']; ?>"><i class="fa fa-edit"></i> <?php echo $lang['Modify_column']; ?></a>
                                <?php } ?>
                                <?php if ($rwgetRole['delete_report'] == '1') { ?>
                                    <a href="#" class="on-default remove-row btn btn-danger" data-toggle="modal" data-target="#dialog" id="removeRow" data="<?php echo $rwUser['rp_id']; ?>"><i class="fa fa-trash-o"></i> <?php echo $lang['Delete']; ?></a>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php
                        $i++;
                    }
                    ?>
                </tbody>
            </table>
            <?php
        }
        ?>

    </body>
</html>