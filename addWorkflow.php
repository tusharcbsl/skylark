<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    require_once './application/config/database.php';
    require_once './application/pages/head.php';

    //sk@241218 : Get Page Length for dataTable records
    $pageLength = intval($_GET['pgLen']); //get page length
    // Prepare Url by removing pglen from url if present.
    $query_string = str_replace('pgLen=' . $_GET['id1'], $id1, $_SERVER['QUERY_STRING']);
    $redirect_url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) . '?' . $query_string;

 

    if ($rwgetRole['view_workflow_list'] != '1') {
        header('Location: ./index');
    }
    ?>
    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <!-- Plugin Css-->
    <link rel="stylesheet" href="assets/plugins/magnific-popup/css/magnific-popup.css" />
    <link rel="stylesheet" href="assets/plugins/jquery-datatables-editable/datatables.css" />
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
                                        <a href="./addWorkflow"><?php echo $lang['WORKFLOW_MANAGEMENT']; ?></a>
                                    </li>
                                    <li class="active">
                                        <?php echo $lang['Workflow_List']; ?>
                                    </li>
                                    <li> <a href="#" data-toggle="modal" data-target="#help-modal" id="helpview" data="21" title="<?= $lang['help']; ?>"><i class="fa fa-question-circle"></i> </a></li>
                                    <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                                </ol>
                            </div>
                        </div>
                        <div class="box box-primary">
                            <div class="panel">
                                <div class="box-header with-border">
                                    <h4 class="header-title col-md-6"><?php echo $lang['WORKFLOW_MANAGEMENT']; ?></h4>
                                     <a href="createWorkflow" class="btn btn-primary pull-right m-r-5"><i class="fa fa-plus"></i> <?php echo $lang['Create_Workflow']; ?></a> 
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                       <div class="col-md-12">
                                            <div class="box box-solid box-primary">
                                                <div class="col-sm-4">
                                                    <label><h4><?php echo $lang['Wf_Lst']; ?></h4></label>
                                                </div>
                                                <div class="col-md-4 m-t-10">
                                                    <select class="select2" name="group"  id="segroup">
                                                        <option value=""><?php echo $lang['Select_Group']; ?></option>
                                                        <?php
                                                        //sk@241218 : get group id for showing filter value selected.
                                                        $grp_id = preg_replace("/[^0-9 ]/", "", $_GET['group_id']);
                                                        mysqli_set_charset($db_con, "utf8");
                                                        $group_permission = mysqli_query($db_con, "SELECT group_id,user_ids FROM `tbl_bridge_grp_to_um`");
                                                        while ($allGroupRow = mysqli_fetch_array($group_permission)) {
                                                            $user_ids = explode(',', $allGroupRow['user_ids']);
                                                            if (in_array($_SESSION['cdes_user_id'], $user_ids)) {
                                                                $grp = mysqli_query($db_con, "select group_id,group_name from tbl_group_master WHERE group_id='$allGroupRow[group_id]' order by group_name asc"); //or die('Error' . mysqli_error($db_con));
                                                                while ($rwGrp = mysqli_fetch_assoc($grp)) {
                                                                    echo'<option value="' . $rwGrp['group_id'] . '" ' . ($rwGrp['group_id'] == $grp_id ? "selected" : "") . '>' . $rwGrp['group_name'] . '</option>';
                                                                }
                                                            }
                                                        }
                                                        ?>    
                                                    </select>
                                                </div>
                                               
                                                <div class="container">
                                                    <table class="table table-striped table-bordered" id="datatable">
                                                        <thead>
                                                            <tr>
                                                                <th><?php echo $lang['Sr_No']; ?></th>
                                                                <th><?php echo $lang['Workflow_Name']; ?></th>
                                                                <th><?php echo $lang['Actions']; ?></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $k = 1;
                                                            $sameGroupIDs = array();
                                                            $group = mysqli_query($db_con, "select group_id from tbl_bridge_grp_to_um where find_in_set('$_SESSION[cdes_user_id]',user_ids)"); //or die('Error' . mysqli_error($db_con));
                                                            while ($rwGroup = mysqli_fetch_assoc($group)) {
                                                                $sameGroupIDs[] = $rwGroup['group_id'];
                                                            }
                                                            $sameGroupIDs = array_unique($sameGroupIDs);
                                                            sort($sameGroupIDs);
                                                            mysqli_set_charset($db_con, "utf8");
                                                            if ($_GET['group_id'] && intval($_GET['group_id'])) {
                                                                $getWfID = mysqli_query($db_con, "select workflow_id,group_id from tbl_workflow_to_group WHERE find_in_set('$_GET[group_id]',group_id)"); //or die("Error " . mysqli_error($db_con));
                                                            } else {
                                                                $getWfID = mysqli_query($db_con, "select workflow_id,group_id from tbl_workflow_to_group"); //or die("Error " . mysqli_error($db_con));
                                                            }
                                                            while ($RwgetWfID = mysqli_fetch_assoc($getWfID)) {
                                                                $WFId = $RwgetWfID['workflow_id'];
                                                                $group_ids = explode(',', $RwgetWfID["group_id"]);
                                                                if (array_intersect($sameGroupIDs, $group_ids)) {

                                                                    $fetchWorkflow = mysqli_query($db_con, "select * from tbl_workflow_master where workflow_id='$WFId' order by workflow_name asc"); //or die('Error in fetchworkflow:' . mysqli_error($db_con));
                                                                    if (mysqli_num_rows($fetchWorkflow) > 0) {
                                                                        $rwfetchWorkflow = mysqli_fetch_assoc($fetchWorkflow);
                                                                        ?>
                                                                        <tr>
                                                                            <td><?php echo $k . '.'; ?></td>
                                                                            <td>
                                                                                <label for="wfid<?php echo $k; ?>"><?php echo $rwfetchWorkflow['workflow_name']; ?></label>
                                                                            </td>
                                                                            <td class="actions">
                                                                                <?php
                                                                                $getformid = mysqli_query($db_con, "select * from tbl_bridge_workflow_to_form where workflow_id='$rwfetchWorkflow[workflow_id]'"); //or die('Error in getWorkflw upload:' . mysqli_error($db_con));
                                                                                if ($rwfetchWorkflow['form_req'] == 1 && mysqli_num_rows($getformid) > 0) {
                                                                                    ?> 
                                                                                    <?php if ($rwgetRole['edit_workflow'] == '1') { ?>
                                                                                        <a href="updateWorkflow?id=<?php echo urlencode(base64_encode($rwfetchWorkflow['workflow_id'])); ?>" class="on-default edit-row btn btn-primary btn-sm"  data=""><i class="fa fa-edit"></i> <?php echo $lang['Edit']; ?></a>
                                                                                    <?php } ?>
                                                                                <?php } else { ?>
                                                                                    <?php if ($rwgetRole['edit_workflow'] == '1') { ?>
                                                                                        <a href="javascript:void(0)" class="on-default edit-row btn btn-primary btn-sm" data-toggle="modal" data-target="#con-close-modal" id="editRow" data="<?php echo $rwfetchWorkflow['workflow_id']; ?>"><i class="fa fa-edit"></i> <?php echo $lang['Edit']; ?></a>
                                                                                        <?php
                                                                                    }
                                                                                }
                                                                                ?>
                                                                                <?php if ($rwgetRole['delete_workflow'] == '1') { ?>
                                                                                    <a href="javascript:void(0)" id="removeRow" data-toggle="modal" data-target="#dialog" data="<?php echo $rwfetchWorkflow['workflow_id']; ?>" class="on-default remove-row btn btn-danger btn-sm" > <i class="fa fa-trash-o"></i> <?php echo $lang['Delete']; ?> </a>
                                                                                <?php } ?>
                                                                                <?php if ($rwgetRole['workflow_step'] == '1') { ?>
                                                                                    <a class="on-default edit-row btn btn-primary btn-sm" href="workFlowStep?idwork=<?php echo urlencode(base64_encode($rwfetchWorkflow['workflow_id'])); ?>" ><i class="fa  fa-plus-circle m-r-5" aria-hidden="true"></i> <?php echo $lang['Workflow_Step']; ?></a>
                                                                                <?php } ?>
                                                                                <?php if ($rwgetRole['view_report'] == '1' || $rwgetRole['add_report'] == '1' || $rwgetRole['delete_report'] == '1' || $rwgetRole['update_report'] == '1') { ?>
                                                                                    <a href="reportList?wfid=<?php echo urlencode(base64_encode($rwfetchWorkflow['workflow_id'])); ?>"   class="on-default remove-row btn btn-info btn-sm" ><i class="fa fa-book rpot"></i> <?php echo $lang['Workflow_Reports']; ?></a>
                                                                                <?php } ?>
                                                                            </td>
                                                                        </tr>
                                                                        <?php
                                                                         $k++;
                                                                    }
                                                                   
                                                                }
                                                            }
                                                            ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end: page -->

                    </div> <!-- end Panel -->
                </div> <!-- container -->

            </div> <!-- content -->

            <?php require_once './application/pages/footer.php'; ?>

        </div>
        <!-- MODAL -->
        <div id="dialog" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog"> 
                <div class="panel panel-color panel-danger"> 
                    <div class="panel-heading"> 
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                        <label><h2 class="panel-title"><?php echo $lang['Are_u_confirm']; ?></h2></label> 
                    </div> 
                    <form method="post">
                        <div class="panel-body">
                            <p style="color:red;"><?php echo $lang['r_u_sure_that_u_wnt_to_Del_tis_Wf']; ?></p>
                        </div>
                        <div class="modal-footer">
                            <div class="col-md-12 text-right">
                                <input type="hidden" id="wid" name="wid">
                                <button type="submit" name="workflowdel" id="dialogConfirm" class="btn btn-danger waves-effect waves-light"><?php echo $lang['confirm']; ?></button>
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                            </div>
                        </div>
                    </form>
                </div> 
            </div>
        </div>
        <!-- end Modal -->
        <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-lg"> 
                <div class="modal-content"> 
                    <form method="post" >
                        <div class="modal-header"> 
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                            <h4 class="modal-title"><?php echo $lang['Update_WF']; ?></h4> 
                        </div>

                        <div class="modal-body" id="modalModify">
                            <img src="assets/images/load.gif" alt="load" class="img-responsive center-block"/>
                        </div> 
                        <div class="modal-footer">

                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                            <button type="submit" name="editWrkFlow" class="btn btn-primary waves-effect waves-light"><?php echo $lang['Save_changes']; ?></button> 
                        </div>
                    </form>

                </div> 
            </div>
        </div><!-- /.modal -->
        <?php
        $sameGroupIDs = array();
        $group = mysqli_query($db_con, "select group_id from tbl_bridge_grp_to_um where find_in_set('$_SESSION[cdes_user_id]',user_ids)") or die('Error' . mysqli_error($db_con));
        while ($rwGroup = mysqli_fetch_assoc($group)) {
            $sameGroupIDs[] = $rwGroup['group_id'];
        }
        $sameGroupIDs = array_unique($sameGroupIDs);
        sort($sameGroupIDs);

        //$sameGroupIDs = implode(',', $sameGroupIDs);
        if ($_GET['group_id'] && intval($_GET['group_id'])) {
            $getWfID = mysqli_query($db_con, "select workflow_id,group_id from tbl_workflow_to_group WHERE find_in_set('$_GET[group_id]',group_id)"); //or die("Error " . mysqli_error($db_con));
        } else {
            $getWfID = mysqli_query($db_con, "select workflow_id,group_id from tbl_workflow_to_group") or die("Error " . mysqli_error($db_con));
        }
        while ($RwgetWfID = mysqli_fetch_assoc($getWfID)) {
            $WFId = $RwgetWfID['workflow_id'];
            $workname = $RwgetWfID['workflow_name'];
            ?>

            <!---assign meta-data model start ---->
            <div id="con-close-modal5<?= $WFId ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog"> 
                    <div class="modal-content"> 
                        <div class="modal-header"> 
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                            <h4 class="modal-title"><?php echo $lang['assign_workflow_report_fields']; ?> <?= $workname; ?></h4> 

                        </div> 

                        <form action="#" data-parsley-validate novalidate method="post" enctype="multipart/form-data">
                            <div class="modal-body row">
                                <div class="col-md-12 shiv metaa">
                                    <span><strong>Report Name:</strong></span>
                                    <input name="rname" class="form-control" required>
                                </div>
                                <div class="col-md-12 shiv metaa" style="margin-top:10px;">
                                    <span><strong>Field Select:</strong></span>
                                    <strong style="margin-left: 113px;">Field Assigned:</strong>
                                    <select multiple="multiple" class="multi-select" id="my_multi_select1" name="my_multi_select1[]" data-plugin="multiselect">
                                        <option value="empid">Employee ID</option>
                                        <option value="action_by">Action By</option>
                                        <option value="task_status">Task Status</option>
                                        <option value="start_date">Submitted Day</option>
                                        <option value="assign_by">Name</option>
                                        <option value="co">CO Date</option>


                                        <?php
                                        $qry = mysqli_query($db_con, "select * from tbl_bridge_workflow_to_form where workflow_id='$WFId'") or die("Error bridge table:" . mysqli_error($db_con));
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
                            <div class="modal-footer">
                                <input type="hidden" value="<?= $WFId ?>" name="id">
                                <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                                <button class="btn btn-primary waves-effect waves-light" type="submit" name="assignReport"><?php echo $lang['Submit']; ?></button>
                            </div>
                        </form>

                    </div> 
                </div>
            </div><!--ends assign-meta-data modal -->  
        <?php } ?>
        <!-- END wrapper -->
        <div style="display: none; background: rgba(0, 0, 0, 0.8); width: 100%; z-index: 2000; position: fixed; top: 0px; height: 800px;" id="wait">;
            <img src="assets/images/proceed.gif" alt="load" style=" margin-left: 48%; margin-top: 250px; width: 100px; height:100px; position: fixed;">
        </div>

        <?php require_once './application/pages/footerForjs.php'; ?>


        <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>

        <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>

        <script type="text/javascript">
                                        $(document).ready(function () {

                                        $("#segroup").change(function () {
                                        var group_id = $(this).val();
                                        //alert(group_id);
                                        window.location.href = "?group_id=" + group_id;
                                        });
                                        });
                                        $(document).ready(function () {
                                        $('form').parsley();
                                        $('#datatable').dataTable({
<?= (!empty($pageLength) ? '"pageLength":' . $pageLength . ',' : '') ?>
                                        "language": {
                                        "paginate": {
                                        "previous": "<?= $lang['Prev'] ?>",
                                                "next": "<?= $lang['Next'] ?>",
                                        },
                                                "emptyTable": "<?= $lang['No_Rcrds_Fnd'] ?>",
                                                "sEmptyTable": "<?= $lang['No_Rcrds_Fnd'] ?>",
                                                "sInfo": "<?= $lang['sInfo'] ?>",
                                                "sInfoEmpty": "<?= $lang['sInfoEmpty'] ?>",
                                                "sSearch": "<?= $lang['Search'] ?>",
                                                "sLengthMenu": "<?= $lang['sLengthMenu'] ?>",
                                                "sInfoFiltered": "<?= $lang['sInfoFiltered'] ?>",
                                                "sZeroRecords": "<?= $lang['sZeroRecords'] ?>",
                                        }
                                        });
                                     
//                                        $('#datatable').on('length.dt', function (e, settings, len) {
//                                        window.location = "addWorkflow?pgLen=" + len;
//                                        //$("#wait").css({"opacity":"0","display":"block",}).show(200).animate({opacity:1});
//                                        //$('#wait').show();
//                                        });
                                        });
                                        $(".select2").select2();
                                        //firstname last name 
                                        $("input#groupName").keypress(function (e) {
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
        </script>
        <script>

//limit filter
            var url = window.location.href + "?";
            function removeParam(key, sourceURL) {
            sourceURL = String(sourceURL).replace("#/", "");
            var rtn = sourceURL.split("?")[0],
                    param,
                    params_arr = [],
                    queryString = (sourceURL.indexOf("?") !== - 1) ? sourceURL.split("?")[1] : "";
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
            $("a#editRow").click(function () {
            var $id = $(this).attr('data');
            var $row = $(this).closest('tr');
            var name = '';
            var values = [];
            values = $row.find('td:nth-child(2)').map(function () {
            var $this = $(this);
            if ($this.hasClass('actions')) {

            } else {
            name = $.trim($this.text());
            //$.trim( $this.text());
            }

            $("#con-close-modal .modal-title").text("Workflow Report" + name + "");
            $.post("application/ajax/updateWorkflow.php", {ID: $id}, function (result, status) {
            if (status == 'success') {
            $("#modalModify").html(result);
            }
            });
            });
            });
            $("a#removeRow").click(function () {

            var id = $(this).attr('data');
            $("#wid").val(id);
            });
            $("input:checkbox").click(function () {
            var box = $(this);
            if (box.is(":checked")) {
            $("input:checkbox").prop("checked", false);
            box.prop('checked', true);
            } else {
            box.prop('checked', false);
            }
            });
            $("#wf_steps").click(function () {
            if ($("input:checkbox").is(":checked")) {
            //alert($("input#wfid:checked").val());
            var idw = $("input:checkbox:checked").val();
            window.location.href = "workFlowStep?idwork=" + idw;
            } else {
            alert("Please Select at least one Workflow to perform this operation.");
            return false;
            }
            });
            //$.noConflict();


        </script>

        <?php
        if (isset($_POST['editWrkFlow'], $_POST['token'])) {
            $workflowName = $_POST['workflowName'];
            //echo $workflowName;
            $workflowName = preg_replace("/[^a-zA-Z0-9_ ]/ ", "", $workflowName); //filter workflow
            $workDes = mysqli_real_escape_string($db_con, str_replace("script", "", $_POST['area']));
            $wid = preg_replace("/[^a-zA-Z0-9_, ]/ ", "", $_POST['wid']);
            $wid = mysqli_real_escape_string($db_con, $wid);
            $workflowgroups = preg_replace("/[^a-zA-Z0-9_, ]/ ", "", $_POST['groupswf']);

            if (!empty($_POST['formRequire'])) {
                $formRequire = preg_replace("/[^a-zA-Z0-9_ ]/ ", "", $_POST['formRequire']);
                $formRequire = mysqli_real_escape_string($db_con, $formRequire);
            } else {
                $formRequire = 0;
            }
            //$formRequire = $_POST['formRequire'];
            $workflowgroups = implode(",", $workflowgroups);
            $workflowgroups = preg_replace("/[^a-zA-Z0-9,]/ ", "", $workflowgroups); //filter workflow
            $workflowgroups = mysqli_real_escape_string($db_con, $workflowgroups);
            $WorkflowNms = mysqli_query($db_con, "select workflow_name from tbl_workflow_master where workflow_id='$wid'");
            $rwWorkflowNms = mysqli_fetch_assoc($WorkflowNms);
            $Workfname = $rwWorkflowNms['workflow_name'];
            //echo $update = "update tbl_workflow_master set workflow_name='$workflowName' workflow_description = '$workDes', form_req = '$formRequire' where workflow_id='$wid'"; die;
            if ($formRequire == 1) {
                $update = mysqli_query($db_con, "update tbl_workflow_master set workflow_name='$workflowName', workflow_description = '$workDes', form_req = '$formRequire' where workflow_id='$wid'");
            } else {
                $update = mysqli_query($db_con, "update tbl_workflow_master set workflow_name='$workflowName', workflow_description = null, form_req = '$formRequire' where workflow_id='$wid'") or die('error in update' . mysqli_error($db_con));
            }
            if ($update) {
                $update = mysqli_query($db_con, "update tbl_workflow_to_group set group_id = '$workflowgroups' where workflow_id='$wid'");
                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'WorkFlow $Workfname to $workflowName  Changed','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                echo '<script>taskSuccess("addWorkflow","' . $lang['Wf_Updted_Scesfly'] . '");</script>';
            } else {
                echo '<script>taskFailed("addWorkflow","' . $lang['Fld_to_Updt_Wf'] . '");</script>';
            }
            mysqli_close($db_con);
        }

        //delete workflow
        if (isset($_POST['workflowdel'], $_POST['token'])) {

            $workflowdel = $_POST['wid'];
            $getWrkFlname = mysqli_query($db_con, "select * from tbl_workflow_master where workflow_id = '$workflowdel'") or die('Error in getWrkFlName:' . mysqli_error($db_con));
            $rwgetWrkFlname = mysqli_fetch_assoc($getWrkFlname);
            $delworkflowName = $rwgetWrkFlname['workflow_name'];
            $delworkflowTbleName = $rwgetWrkFlname['form_tbl_name'];


            //check workflow is in use or not
            $prsnt = 0;
            $getWrkFlTsk = mysqli_query($db_con, "select task_id from tbl_task_master where workflow_id = '$workflowdel'");
            while ($rwgetWrkFlTsk = mysqli_fetch_assoc($getWrkFlTsk)) {
                $getWrkFlTsk = mysqli_query($db_con, "select task_id from tbl_doc_assigned_wf where FIND_IN_SET('$rwgetWrkFlTsk[task_id]',task_id)");
                if (mysqli_num_rows($getWrkFlTsk) >= 1) {
                    $prsnt++;
                    break;
                }
            }

            if ($prsnt == 0) {
                $getDocPath = mysqli_query($db_con, "select * from tbl_document_master where doc_name like '$workflowdel'") or die("Error:" . mysqli_error($db_con));

                $numrow = mysqli_num_rows($getDocPath);
                $delWorkflowBridge = mysqli_query($db_con, "delete from tbl_workflow_to_group where workflow_id='$workflowdel'") or die("Error:" . mysqli_error($db_con));
                $del = mysqli_query($db_con, "delete from tbl_workflow_master where workflow_id='$workflowdel'") or die("Error:" . mysqli_error($db_con));
                $del = mysqli_query($db_con, "delete from tbl_step_master where workflow_id='$workflowdel'") or die("Error:" . mysqli_error($db_con));
                $delFrTask = mysqli_query($db_con, "delete from tbl_task_master where workflow_id='$workflowdel'") or die("Error:" . mysqli_error($db_con));
                if (!empty($delworkflowTbleName)) {
                    $tblBridgeWfForm = mysqli_query($db_con, "select form_id from  tbl_bridge_workflow_to_form where workflow_id='$workflowdel'") or die("Error:" . mysqli_error($db_con));
                    $formdata = mysqli_fetch_assoc($tblBridgeWfForm);
                    $fid = $formdata['form_id'];
                    $tblFormAttr = mysqli_query($db_con, "delete from  tbl_form_attribute where fid='$fid'") or die("Error:" . mysqli_error($db_con));
                    $tblFormMaster = mysqli_query($db_con, "delete from tbl_form_master where fid='$fid'") or die("Error:" . mysqli_error($db_con));
                    $dropWfTbl = mysqli_query($db_con, "Drop table " . $delworkflowTbleName . "") or die("Error:" . mysqli_error($db_con));
                    $tblBridgeWfFormqry = mysqli_query($db_con, "delete from  tbl_bridge_workflow_to_form where workflow_id='$workflowdel'") or die("Error:" . mysqli_error($db_con));
                }
                //echo '<script>taskSuccess("addWorkflow","Workflow Deleted Successfully !");</script>';
                if ($delFrTask) {

                    $getWrkFl = mysqli_query($db_con, "select * from tbl_workflow_master") or die('Error in getWrkFl:' . mysqli_error($db_con));

                    if (mysqli_num_rows($getWrkFl) > 0) {
                        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'WorkFlow $delworkflowName Deleted','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                        echo '<script>taskSuccess("addWorkflow","' . $lang['Wf_Dltd_Sucsfly'] . '");</script>';
                    } //else {
                    //echo '<script>taskSuccess("createWorkflow","Workflow Deleted Successfully !");</script>';
                    // }
                } else {
                    echo '<script>taskFailed("addWorkflow","' . $lang['Fld_to_Dltd_Wf'] . '");</script>';
                }
            } else {
                echo '<script>taskFailed("addWorkflow","' . $lang['Wf_is_n_Use_nd_cn_t_b_Dltd'] . '");</script>';
            }
            mysqli_close($db_con);
        }


        //metareport
        if (isset($_POST['assignReport'], $_POST['token'])) {
            $wfid = $_POST['id'];
            $rname = $_POST['rname'];
            $coloums = implode(",", $_POST['my_multi_select1']);


            $qry = mysqli_query($db_con, "insert into  tbl_wf_reports (`wf_id`,`coloums`,`report_name`) values ('$wfid','$coloums','$rname')") or die("Reports fields error:" . mysqli_error($db_con));
            if ($qry) {
                echo '<script>taskSuccess("addWorkflow","' . $lang['Fields_Added_Sucesfly'] . '");</script>';
            } else {
                echo '<script>taskFailed("addWorkflow","' . $lang['FieldsaddedFailed'] . '");</script>';
            }
        }
        ?>
    </body>
</html>
