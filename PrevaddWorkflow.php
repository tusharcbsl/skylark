<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    require_once './application/config/database.php';
    require_once './application/pages/head.php';

    //for user role
    $chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

    $rwgetRole = mysqli_fetch_assoc($chekUsr);

    if ($rwgetRole['view_workflow_list'] != '1') {
        header('Location: ./index');
    }
    ?>
    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />

    <!-- Plugin Css-->
    <link rel="stylesheet" href="assets/plugins/magnific-popup/css/magnific-popup.css" />
    <link rel="stylesheet" href="assets/plugins/jquery-datatables-editable/datatables.css" />

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
                                        <a href="./addWorkflow"><?php echo $lang['WORKFLOW_MANAGEMENT'];?></a>
                                    </li>
                                    <li class="active">
                                        <?php echo $lang['Workflow_List'];?>
                                    </li>
                                </ol>
                            </div>
                        </div>
                        <div class="box box-primary">
                            <div class="panel">
                                <div class="box-header with-border">
                                    <h4 class="header-title"><?php echo $lang['WORKFLOW_MANAGEMENT'];?></h4>

                                </div>
                                <div class="panel-body">

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="box box-solid box-primary">
                                                <div class="panel panel-color panel-primary">
                                                    <div class="panel-heading" style="background-color: #3c8dbc;">
                                                        <h3 class="panel-title" style="text-align: center;"><?php echo $lang['Adm_Tls'];?></h3>
                                                    </div>
                                                    <div class="panel-body">
                                                        <?php if ($rwgetRole['create_workflow'] == '1') { ?>
                                                            <a class="btn btn-primary btn-block" href="createWorkflow"><?php echo $lang['Nw_Wrkflow'];?></a>
                                                        <?php } ?>
                                                        <?php if ($rwgetRole['view_workflow_list'] == '1') { ?>
                                                            <a class="btn btn-primary btn-block" style="background: #286090 !important; border-color: #204d74 !important;"><?php echo $lang['Wf_Lst'];?></a>
                                                        <?php } ?>
                                                        <?php if ($rwgetRole['workflow_step'] == '1') { ?>
                                                            <a class="btn btn-primary btn-block" href="javascript:void(0)" id="wf_steps"><?php echo $lang['Wf_Step'];?></a>
                                                        <?php } ?>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="box box-solid box-primary" style="padding:2px;">
                                                <label><h4 style="margin-left: 38px;"><?php echo $lang['Wf_Lst'];?></h4></label>
                                                 <div class="form-group col-md-3">

                                                        <select class="select2" name="group"  id="segroup">
                                                            <option><?php echo $lang['Slt_Grp'];?></option>
                                                            <?php
                                                            $group_permission = mysqli_query($db_con, "SELECT group_id,user_ids FROM `tbl_bridge_grp_to_um`");
                                                            while ($allGroupRow = mysqli_fetch_array($group_permission)) {
                                                                $user_ids = explode(',', $allGroupRow['user_ids']);
                                                                if (in_array($_SESSION['cdes_user_id'], $user_ids)) {
                                                                    $grp = mysqli_query($db_con, "select group_id,group_name from tbl_group_master WHERE group_id='$allGroupRow[group_id]' order by group_name asc") or die('Error' . mysqli_error($db_con));
                                                                    while ($rwGrp = mysqli_fetch_assoc($grp)) {
                                                                        echo'<option value="' . $rwGrp['group_id'] . '">' . $rwGrp['group_name'] . '</option>';
                                                                    }
                                                                }
                                                            }
                                                            ?>    


                                                        </select>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                    <table class="table table-striped" id="datatable">
                                                        <thead>
                                                            <tr>
                                                                <th><?php echo $lang['Workflow_Name'];?></th>
                                                                <th><?php echo $lang['Actions'];?></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $i = 0;
                                                            $sameGroupIDs = array();
                                                            $group = mysqli_query($db_con, "select group_id from tbl_bridge_grp_to_um where find_in_set('$_SESSION[cdes_user_id]',user_ids)") or die('Error' . mysqli_error($db_con));
                                                            while ($rwGroup = mysqli_fetch_assoc($group)) {
                                                                $sameGroupIDs[] = $rwGroup['group_id'];
                                                            }
                                                            $sameGroupIDs = array_unique($sameGroupIDs);
                                                            sort($sameGroupIDs);

                                                            //$sameGroupIDs = implode(',', $sameGroupIDs);
                                                            $group_id=base64_decode($_GET['group_id']);
                                                            if ($group_id) {
                                                                $getWfID = mysqli_query($db_con, "select workflow_id,group_id from tbl_workflow_to_group WHERE find_in_set('$group_id',group_id)") or die("Error " . mysqli_error($db_con));
                                                            } else {
                                                                $getWfID = mysqli_query($db_con, "select workflow_id,group_id from tbl_workflow_to_group") or die("Error " . mysqli_error($db_con));
                                                            }
                                                            while ($RwgetWfID = mysqli_fetch_assoc($getWfID)) {
                                                                $WFId = $RwgetWfID['workflow_id'];
                                                                $group_ids = explode(',', $RwgetWfID["group_id"]);

                                                                if (array_intersect($sameGroupIDs, $group_ids)) {
                                                                     //echo "select * from tbl_workflow_master where workflow_id='$WFId'";
                                                                    $fetchWorkflow = mysqli_query($db_con, "select * from tbl_workflow_master where workflow_id='$WFId'") or die('Error in fetchworkflow:' . mysqli_error($db_con));
                                                                    if(mysqli_num_rows($fetchWorkflow)>0){
                                                                    $rwfetchWorkflow = mysqli_fetch_array($fetchWorkflow); 
                                                                        ?>

                                                                        <tr>

                                                                            <td>

                                                                                <div class="checkbox checkbox-primary">
                                                                                    <input type="checkbox" name="fetchWork" id="wfid<?php echo $i; ?>" value="<?php echo urlencode(base64_encode($rwfetchWorkflow['workflow_id'])); ?>" />
                                                                                    <label for="wfid<?php echo $i; ?>"><?php echo $rwfetchWorkflow['workflow_name']; ?></label>
                                                                                </div>
                                                                            </td>

                                                                            <td class="actions">
                                                                                <?php if ($rwgetRole['edit_workflow'] == '1') { ?>
                                                                                    <a href="javascript:void(0)" class="on-default edit-row btn btn-primary btn-xs" data-toggle="modal" data-target="#con-close-modal" id="editRow" data="<?php echo $rwfetchWorkflow['workflow_id']; ?>"><i class="fa fa-pencil"></i></a>
                                                                                <?php } ?>
                                                                                <?php if ($rwgetRole['delete_workflow'] == '1') { ?>
                                                                                    <a href="javascript:void(0)" id="removeRow" data-toggle="modal" data-target="#dialog" data="<?php echo $rwfetchWorkflow['workflow_id']; ?>" class="on-default remove-row btn btn-danger btn-xs" ><i class="fa fa-trash-o"></i> </a>
                                                                                <?php } ?>
                                                                            </td>
                                                                        </tr>

                                                                        <?php
                                                                    }
                                                                        $i++;
                                                                    
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
                        <!-- end: page -->

                    </div> <!-- end Panel -->
                </div> <!-- container -->

            </div> <!-- content -->

            <?php require_once './application/pages/footer.php'; ?>

        </div>
        <!-- MODAL -->
        <div id="dialog" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog"> 
                <div class="modal-content"> 
                    <div class="modal-header"> 
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                        <label><h2 class="panel-title"><?php echo $lang['Are_u_confirm'];?></h2></label> 
                    </div> 
                    <form method="post">
                        <div class="modal-body">
                            <p style="color:red;"><?php echo $lang['r_u_sure_that_u_wnt_to_Del_tis_Wf'];?></p>
                        </div>
                        <div class="modal-footer">
                            <div class="col-md-12 text-right">
                                <input type="hidden" id="wid" name="wid">
                                <button type="submit" name="workflowdel" id="dialogConfirm" class="btn btn-primary waves-effect waves-light"><?php echo $lang['Confirm'];?></button>
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close'];?></button> 
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
                            <h4 class="modal-title"><?php echo $lang['Update_WF'];?></h4> 
                        </div>

                        <div class="modal-body" id="modalModify">
                            <img src="assets/images/load.gif" alt="load" class="img-responsive center-block"/>
                        </div> 
                        <div class="modal-footer">

                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close'];?></button> 
                            <button type="submit" name="editWrkFlow" class="btn btn-primary waves-effect waves-light"><?php echo $lang['Save_changes'];?></button> 
                        </div>
                    </form>

                </div> 
            </div>
        </div><!-- /.modal -->
        <!-- END wrapper -->

        <?php require_once './application/pages/footerForjs.php'; ?>

        <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>

        <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function(){
                 
                $("#segroup").change(function(){
                  var group_id = $(this).val();
                    //alert(group_id);
                    // window.btoa(
                    window.location.href = "?group_id=" + btoa(encodeURI(group_id));
                });
            });
            $(document).ready(function () {
                $('form').parsley();
                $('#datatable').dataTable();
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

                    $("#con-close-modal .modal-title").text("Update Workflow " + name + "");
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

                    window.location.href = "workFlowStep?idwork=" +idw;
                    //window.location.href = "workFlowStep?idwork=" + btoa(encodeURI(idw));
                } else {
                    alert("Please Select at least one Workflow to perform this operation.");
                    return false;
                }
            });
            //$.noConflict();
            
            
        </script>
        <?php
        if (isset($_POST['editWrkFlow'])) {
            $workflowName = mysqli_real_escape_string($db_con,$_POST['workflowName']);
            $workDes = mysqli_real_escape_string($db_con, $_POST['area']);
            $wid = mysqli_real_escape_string($db_con,$_POST['wid']);
            $workflowgroups =mysqli_real_escape_string($db_con,$_POST['groupswf']);
            if (!empty($_POST['formRequire'])) {
                $formRequire = $_POST['formRequire'];
                ;
            } else {
                $formRequire = 0;
            }
            //$formRequire = $_POST['formRequire'];
            $workflowgroups = implode(",", $workflowgroups);
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
                echo '<script>taskSuccess("addWorkflow","'.$lang['Wf_Updted_Scesfly'].'");</script>';
            } else {
                echo '<script>taskFailed("addWorkflow","'.$lang['Fld_to_Updt_Wf'].'");</script>';
            }
            mysqli_close($db_con);
        }


        //delete workflow
        if (isset($_POST['workflowdel'])) {

            $workflowdel = mysqli_real_escape_string($db_con,$_POST['wid']);
            $getWrkFlname = mysqli_query($db_con, "select * from tbl_workflow_master where workflow_id = '$workflowdel'") or die('Error in getWrkFlName:' . mysqli_error($db_con));
            $rwgetWrkFlname = mysqli_fetch_assoc($getWrkFlname);
            $delworkflowName = $rwgetWrkFlname['workflow_name'];

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
                $getDocPath = mysqli_query($db_con, "select * from tbl_document_master where doc_name like '$workflowdel'");

                $numrow = mysqli_num_rows($getDocPath);

                $del = mysqli_query($db_con, "delete from tbl_workflow_master where workflow_id='$workflowdel'");
                $del = mysqli_query($db_con, "delete from tbl_step_master where workflow_id='$workflowdel'");
                $delFrTask = mysqli_query($db_con, "delete from tbl_task_master where workflow_id='$workflowdel'");

                if ($delFrTask) {

                    $getWrkFl = mysqli_query($db_con, "select * from tbl_workflow_master") or die('Error in getWrkFl:' . mysqli_error($db_con));

                    if (mysqli_num_rows($getWrkFl) > 0) {
                        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'WorkFlow $delworkflowName Deleted','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                        echo '<script>taskSuccess("addWorkflow","'.$lang['Wf_Dltd_Sucsfly'].'");</script>';
                    } //else {
                    //echo '<script>taskSuccess("createWorkflow","Workflow Deleted Successfully !");</script>';
                    // }
                } else {
                    echo '<script>taskFailed("addWorkflow","'.$lang['Fld_to_Dltd_Wf'].'");</script>';
                }
            } else {
                echo '<script>taskFailed("addWorkflow","'.$lang['Wf_is_n_Use_nd_cn_t_b_Dltd'].'");</script>';
            }
            mysqli_close($db_con);
        }
        ?>
    </body>
</html>
