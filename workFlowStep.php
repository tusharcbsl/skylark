<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    require_once './application/config/database.php';
    require_once './application/pages/head.php';
    //echo urlencode($_GET['idwork']); die;

    $idwork = base64_decode(urldecode($_GET['idwork']));
    //for user role
    $chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

    $rwgetRole = mysqli_fetch_assoc($chekUsr);

    if (($rwgetRole['workflow_step'] != '1') || (!intval($idwork))) {
        header('Location: ./index');
    }


    $stepCond = mysqli_query($db_con, "select * from tbl_step_master where workflow_id = '$idwork'") or die('Error ' . mysqli_error($db_con));
    $taskCond = mysqli_query($db_con, "select * from tbl_task_master where workflow_id = '$idwork'") or die('Error ' . mysqli_error($db_con));
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
                                        <a href="./addWorkflow"><?php echo $lang['Workflow_management'] ?></a>
                                    </li>
                                    <li class="active"><?php echo $lang['Workflow_Step'] ?></li>
                                    <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                                </ol>
                            </div>
                        </div>
                        <div class="box box-primary">
                            <div class="panel">
                                <div class="box-header with-border">
                                    <div class="col-sm-12 ">
                                        <a class="btn btn-primary " href="javascript:void(0)" data-toggle="modal" data-target="#addStep" data="<?php echo $idwork; ?>"><i class="fa fa-plus"></i> <?php echo $lang['Add_Step']; ?></a>
                                        <?php if (mysqli_num_rows($stepCond) > 0) { ?>
                                            <a class="btn btn-primary " href="javascript:void(0)" id="ewfs_steps" data-toggle="modal" data-target="#editStep" data="<?php echo $idwork; ?>"><i class="fa fa-edit"></i> <?php echo $lang['Edt_Stp']; ?></a>
                                            <a class="btn btn-danger " href="javascript:void(0)" id="dwfs_steps" data-toggle="modal" data-target="#con-close-modal" ><i class="fa fa-trash-o"></i> <?php echo $lang['Del_Sp']; ?></a>
                                            <a class="btn btn-primary " href="javascript:void(0)" id="wfs_atask" data-toggle="modal" data-target="#addTask" data="<?php echo $idwork; ?>"><i class="fa fa-plus"></i> <?php echo $lang['Ad_Tsk']; ?></a>
                                        <?php } ?>
                                        <?php if (mysqli_num_rows($taskCond) > 0) { ?>
                                            <a class="btn btn-primary " href="javascript:void(0)" id="ewfs_task" data-toggle="modal" data-target="#editTask" data="<?php echo $idwork; ?>"><i class="fa fa-edit"></i> <?php echo $lang['Edt_Tsk']; ?></a>
                                            <a class="btn btn-danger " href="javascript:void(0)" id="dwfs_task" data-toggle="modal" data-target="#con-close-modal1" ><i class="fa fa-trash-o"></i> <?php echo $lang['Del_Tsk']; ?></a>
                                        <?php } ?>

                                    </div>
                                </div>
                                <div class="panel-body">
                                    <div class="row">

                                        <div class="col-md-12">
                                            <?php
                                            mysqli_set_charset($db_con, "utf8");
                                            $getWorkFlowName = mysqli_query($db_con, "select * from tbl_workflow_master where workflow_id = '$idwork'") or die('Error in fetworkflwname:' . mysqli_error($db_con));
                                            $rwgetWorkFlowName = mysqli_fetch_assoc($getWorkFlowName);


                                            if (mysqli_num_rows($stepCond) > 0) {
                                                ?>
                                                <div class="box box-solid box-primary p-10">
                                                    <h4><?php echo $lang['Workflow_Step']; ?><?php
//                                                        if (mysqli_num_rows($stepCond) > 1) {
//                                                            echo 's';
//                                                        }
                                                        ?> <?php
//                                                        if (mysqli_num_rows($taskCond) > 0) {
//                                                            echo '& ' . $lang['Task'];
//                                                        }
                                                        ?><?php
//                                                        if (mysqli_num_rows($taskCond) > 1) {
//                                                            echo 's';
//                                                        }
                                                        ?> : <b><?php echo $rwgetWorkFlowName['workflow_name']; ?></b></h4>

                                                    <table class="table table-striped table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th><?php echo $lang['task_order']; ?></th>
                                                                <th><?php echo $lang['Task_Name']; ?></th>
                                                                <th><?php echo $lang['Priority']; ?></th>
                                                                <th><?php echo $lang['Deadline']; ?></th>
                                                                <th><?php echo $lang['Ass_To']; ?></th>
                                                                <th><?php echo $lang['Alternate_User']; ?></th>
                                                                <th><?php echo $lang['Supervisor']; ?></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            //for step no. and name
                                                            mysqli_set_charset($db_con, "utf8");
                                                            $getStepName = mysqli_query($db_con, "select * from tbl_step_master where workflow_id = '$idwork' order by step_order asc") or die('Error in getStepName:' . mysqli_error($db_con));
                                                            $i = 0;
                                                            while ($rwgetStepName = mysqli_fetch_assoc($getStepName)) {
                                                                ?>
                                                                <tr>
                                                                    <td colspan="20">
                                                                        <div class="checkbox checkbox-primary">
                                                                            <input type="checkbox" name="fetchWork" class="stepchk" id="wfsid<?php echo $i; ?>" value="<?php echo $rwgetStepName['step_id']; ?>" />
                                                                            <label for="wfsid<?php echo $i; ?>"> <?php echo $lang['stp']; ?> <?php echo $rwgetStepName['step_order']; ?> : <span style="color:#3f51b5"> <?php echo $rwgetStepName['step_name']; ?></span></label>
                                                                        </div>
                                                                    </td>
                                                                </tr>

                                                                <?php
                                                                //for task name and more
                                                                mysqli_set_charset($db_con, "utf8");
                                                                $fetchTask = mysqli_query($db_con, "select * from tbl_task_master where step_id = '$rwgetStepName[step_id]' order by task_order asc") or die('Error in fetchTask:' . mysqli_error($db_con));
                                                                $k = 1;
                                                                while ($rwfetchTask = mysqli_fetch_assoc($fetchTask)) {
                                                                    ?>
                                                                    <tr>
                                                                        <td>
                                                                            <div class="checkbox checkbox-primary">

                                                                                <input type="checkbox" name="fetchWork" class="tskchk" id="wftsk<?php echo $rwfetchTask['task_id']; ?>" value="<?php echo $rwfetchTask['task_id']; ?>" />
                                                                                <label for="wftsk<?php echo $rwfetchTask['task_id']; ?>"><span><?php echo $lang['Task']; ?> <?php echo $rwfetchTask['task_order']; ?></span> 
                                                                                </label>
                                                                            </div>  
                                                                        </td>
                                                                        <td><span style="color: #3f51b5"><?php echo $rwfetchTask['task_name']; ?></span></td>
                                                                        <td>
                                                                            <?php
                                                                            if ($rwfetchTask['priority_id'] == 1) {
                                                                                echo 'Urgent';
                                                                            } elseif ($rwfetchTask['priority_id'] == 2) {
                                                                                echo 'Medium';
                                                                            } else { {
                                                                                    echo 'Normal';
                                                                                }
                                                                            };
                                                                            ?>
                                                                        </td>

                                                                        <td>
                                                                            <?php
                                                                            if ($rwfetchTask['deadline_type'] == 'Date') {

                                                                                echo intdiv($rwfetchTask['deadline'], 60) . ':' . ($rwfetchTask['deadline'] % 60) . ' Hrs';
                                                                            } else if ($rwfetchTask['deadline_type'] == 'Days') {
                                                                                echo $rwfetchTask['deadline'] . ' ' . $rwfetchTask['deadline_type'];
                                                                            } else {
                                                                                echo $rwfetchTask['deadline'] / 60 . ' ' . $rwfetchTask['deadline_type'];
                                                                            }
                                                                            ?>
                                                                        </td>

                                                                        <td>
                                                                            <?php
                                                                            mysqli_set_charset($db_con, "utf8");
                                                                            $fetchUser = mysqli_query($db_con, "select * from tbl_user_master where user_id = '$rwfetchTask[assign_user]'") or die('Error in fetchUser:' . mysqli_error($db_con));
                                                                            $rwfetchUser = mysqli_fetch_assoc($fetchUser);
                                                                            echo $rwfetchUser['first_name'] . ' ' . $rwfetchUser['last_name'];
                                                                            ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php
                                                                            mysqli_set_charset($db_con, "utf8");
                                                                            $fetchUser = mysqli_query($db_con, "select * from tbl_user_master where user_id = '$rwfetchTask[alternate_user]'") or die('Error in fetchUser:' . mysqli_error($db_con));
                                                                            $rwfetchUser = mysqli_fetch_assoc($fetchUser);
                                                                            echo $rwfetchUser['first_name'] . ' ' . $rwfetchUser['last_name'];
                                                                            ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php
                                                                            mysqli_set_charset($db_con, "utf8");
                                                                            $user = mysqli_query($db_con, "select * from tbl_user_master where user_id='$rwfetchTask[supervisor]'") or die('Error:' . mysqli_error($db_con));
                                                                            $rwUser = mysqli_fetch_assoc($user);
                                                                            echo $rwUser['first_name'] . ' ' . $rwUser['last_name']
                                                                            ?>
                                                                        </td> 

                                                                    </tr>

                                                                    <tr>
                                                                        <td colspan="20"><label><?php echo $lang['Tsk_Instruct']; ?> : </label> <span style="color: #2196F3;"><?php echo $rwfetchTask['task_instructions']; ?></span> </td> 
                                                                    </tr>

                                                                    <?php
                                                                    $k++;
                                                                }

                                                                $i++;
                                                            }
                                                            ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            <?php } else {
                                                ?>
                                                <div class="box box-solid box-primary step">
                                                    <label><strong><?= $rwgetWorkFlowName['workflow_name']; ?></strong> <?= $lang['create_steps_and_tasks_workflow']; ?></label>
                                                </div>
                                            <?php }
                                            ?>
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

        <!-- Right Sidebar -->
        <?php require_once './application/pages/rightSidebar.php'; ?>
        <!-- /Right-bar -->

        <!-- MODAL -->
        <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog"> 
                <div class="panel panel-color panel-danger"> 
                    <div class="panel-heading"> 
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                        <label><h2 class="panel-title"><?php echo $lang['Cnfrm']; ?></h2></label> 
                    </div> 
                    <form method="post">
                        <div class="panel-body">
                            <p style="color: red;"><?php echo $lang['r_y_s_stp']; ?></p>
                        </div>
                        <div class="modal-footer">
                            <div class="col-md-12 text-right">
                                <input type="hidden" id="sid" name="sid">
                                <button type="submit" name="stepdelete" id="dialogConfirm" class="btn btn-danger waves-effect waves-light"><?php echo $lang['confirm']; ?></button>
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                            </div>
                        </div>
                    </form>
                </div> 
            </div>
        </div>
        <div id="addStep" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content"> 
                    <header class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                        <label><h2 class="panel-title"><?php echo $lang['ad_stp_wf']; ?></h2></label>
                    </header>
                    <form method="post">
                        <div class="modal-body">
                            <div class="modal-wrapper">
                                <div class="form-group row">
                                    <div class="col-md-3">
                                        <label for=""><?php echo $lang['Stp_Nme']; ?> <span style="color: red;">*</span></label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control translatetext specialchaecterlock" name="workflowStep" placeholder="<?php echo $lang['Entr_Stp_Nam']; ?>" maxlength="40" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-3">
                                        <label for="userName"><?php echo $lang['Stp_Ordr']; ?><span style="color: red;">*</span></label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control specialchaecterlock" name="workStepOrd" placeholder="<?php echo $lang['Entr_only_intgr_vaul']; ?>"  required id="number"/>                                          
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-3">
                                        <label for="userName"><?php echo $lang['Des']; ?></label>
                                    </div>
                                    <div class="col-md-9">
                                        <textarea class="form-control translatetext specialchaecterlock" rows="5" name="workStepDesc"></textarea>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="col-md-12 text-right">
                                <input type="hidden" id="sid" name="sid">
                                <button type="submit" name="addSteps" class="btn btn-primary waves-effect waves-light"><?php echo $lang['Add']; ?></button>
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div id="editStep" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog">
                <form method="post">
                    <div class="modal-content"> 
                        <header class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                            <label><h2 class="panel-title"><?php echo $lang['Edt_stp_n_wf']; ?></h2></label>
                        </header>

                        <div class="modal-body">
                            <div class="modal-wrapper" id="modalModify">
                                <img src="assets/images/load.gif" alt="load" class="img-responsive center-block" />
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="col-md-12 text-right">
                                <input type="hidden" id="sid" name="sid">
                                <button type="submit" name="editSteps" class="btn btn-primary waves-effect waves-light"><?php echo $lang['Update']; ?></button>
                                <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                            </div>
                        </div>

                    </div>

                </form>
            </div>
        </div>
        <!-- end Modal -->

        <div id="addTask" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content"> 
                    <header class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                        <label><h2 class="panel-title"><?php echo $lang['AD_TSK_PLS_E_TE_FW_INFN_TE_FS_MKD_WTH_A']; ?></h2></label>
                    </header>
                    <form method="post">
                        <div class="modal-body">
                            <div class="modal-wrapper" id="modalAddTask">
                                <img src="assets/images/load.gif" alt="load" class="img-responsive center-block" width="50px"/>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="col-md-12 text-right">
                                <input type="hidden" name="wfid" value="<?php echo $idwork; ?>" /> 
                                <input type="hidden" id="sid" name="sid">
                                <button type="submit" name="addTask" class="btn btn-primary waves-effect waves-light"><?php echo $lang['Ad_Tsk']; ?></button>
                                <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- END wrapper -->
        <div id="editTask" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-lg">
                <form method="post">
                    <div class="modal-content"> 
                        <header class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                            <label><h2 class="panel-title"><?php echo $lang['Edt_Tsk']; ?></h2></label>
                        </header>

                        <div class="modal-body">
                            <div class="modal-wrapper" id="modalTModify">
                                <img src="assets/images/load.gif" alt="load" class="img-responsive center-block" />
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="col-md-12 text-right">
                                <input type="hidden" name="wfid" value="<?php echo $idwork; ?>" /> 
                                <input type="hidden" id="sid" name="sid">
                                <button type="submit" name="updateTask" class="btn btn-primary waves-effect waves-light"><?php echo $lang['Updte_Tsk']; ?></button>
                                <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                            </div>
                        </div>

                    </div>

                </form>
            </div>
        </div>
        <!----task delete modal-->
        <div id="con-close-modal1" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog"> 
                <div class="panel panel-color panel-danger"> 
                    <div class="panel-heading"> 
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                        <label><h2 class="panel-title"><?php echo $lang['Cnfrm']; ?></h2></label> 
                    </div> 
                    <form method="post">
                        <div class="panel-body">
                            <p class="text-danger"><?php echo $lang['r_u_sure_tat_u_wnt_to_del_ths_Tsk']; ?></p>
                        </div>
                        <div class="modal-footer">
                            <div class="col-md-12 text-right">
                                <input type="hidden" id="tid" name="tid">
                                <button type="submit" name="taskdelete" id="dialogConfirm" class="btn btn-danger waves-effect waves-light"><?php echo $lang['confirm']; ?></button>
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                            </div>
                        </div>
                    </form>
                </div> 
            </div>
        </div>
        <?php require_once './application/pages/footerForjs.php'; ?>
        <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>

        <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>

        <script type="text/javascript">
                                        $(document).ready(function () {
                                            //Disable cut copy paste
                                            $('body').bind('cut copy paste', function (e) {
                                                e.preventDefault();
                                            });

                                            //Disable mouse right click
                                            $("body").on("contextmenu", function (e) {
                                                return false;
                                            });
                                        });
        </script>
        <script type="text/javascript">
            $(document).ready(function () {
                $('form').parsley();

            });
            // $(".select2").select2();
            //number only in text
            $("input#number,input#days").keypress(function (e) {
                //  alert();
                //if the letter is not digit then display error and don't type anything
                if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57) && e.which != 46) {
                    //display error message
                    return false;
                } else if (event.ctrlKey == true && (event.which == '118' || event.which == '86')) {
                    event.preventDefault();
                } else {
                    str = $(this).val();
                    str = str.split(".").length - 1;
                    if (str > 0 && e.which == 46) {
                        return false;
                    } else {
                        return true;
                    }
                }
            });

        </script>

        <script>

            $("#wfs_steps").click(function () {
                if ($("input.stepchk").is(":checked")) {
                    //alert($("input#wfsid:checked").val());
                    window.location.href = "addTask?idwork=<?php echo urlencode($_GET[idwork]); ?>&idstp=" + $("input.stepchk:checked").val();
                } else {
                    alert("<?php echo $lang['Pls_slt_chckbx_to_prfrm_ths_opratn']; ?>.");
                    return false;
                }
            });

            $("#ewfs_steps").click(function () {
                if ($("input.stepchk").is(":checked")) {
                    var sid = $("input.stepchk:checked").val();
                    var token = $("input[name='token']").val();
                    $.post("application/ajax/editSteps.php", {ID: sid, token:token}, function (result, status) {
                        if (status == 'success') {
                            $("#modalModify").html(result);
                            getToken();
                        }
                    });
                } else {
                    alert("<?php echo $lang['Pls_slt_stp_chckbx_to_prfrm_ths_oprtn']; ?>.");
                    return false;
                }
            });


            $("#dwfs_steps").click(function () {
                if ($("input.stepchk").is(":checked")) {
                    var sid = $("input.stepchk:checked").val();
                    $("#sid").val(sid);
                    // alert($("input#wfsid:checked").val());
                } else {
                    alert("<?php echo $lang['Pls_slt_stp_chckbx_to_prfrm_ths_oprtn']; ?>.");
                    return false;
                }
            });

            $("#wfs_atask").click(function () {
                if ($("input.stepchk").is(":checked")) {
                    var sid = $("input.stepchk:checked").val();
                    var token = $("input[name='token']").val();
                    $.post("application/ajax/addTask.php", {ID: sid, token:token}, function (result, status) {
                        if (status == 'success') {
                            $("#modalAddTask").html(result);
                            getToken();
                        }
                    });
                } else {
                    alert("<?php echo $lang['Pls_slt_stp_chckbx_to_prfrm_ths_oprtn']; ?>.");
                    return false;
                }
            });
            $("#ewfs_task").click(function () {
                if ($("input.tskchk").is(":checked")) {
                    var tid = $("input.tskchk:checked").val();
                      var token = $("input[name='token']").val();
                    $.post("application/ajax/editTask.php", {ID: tid, token:token}, function (result, status) {
                        if (status == 'success') {
                            $("#modalTModify").html(result);
                            getToken();
                        }
                    });
                } else {
                    alert("<?php echo $lang['Pls_slt_tsk_chckbx_to_prfrm_ths_oprtn']; ?>.");
                    return false;
                }
            });
            $("#dwfs_task").click(function () {
                if ($("input.tskchk").is(":checked")) {
                    var tid = $("input.tskchk:checked").val();
                    $("#tid").val(tid);
                    // alert($("input#wfsid:checked").val());
                } else {
                    alert("<?php echo $lang['Pls_slt_tsk_chckbx_to_prfrm_ths_oprtn']; ?>.");
                    return false;
                }
            });

            $("input.stepchk").click(function () {
                var box = $(this);
                if (box.is(":checked")) {
                    $("input.stepchk").prop("checked", false);
                    box.prop('checked', true);
                } else {
                    box.prop('checked', false);
                }
            });


            $("input.tskchk").click(function () {
                var box = $(this);
                if (box.is(":checked")) {
                    $("input.tskchk").prop("checked", false);
                    box.prop('checked', true);
                } else {
                    box.prop('checked', false);
                }
            });


        </script>
        <?php
        if (isset($_POST['addSteps'], $_POST['token']) && !empty($idwork)) {
            $workFlowId = $idwork;
            $workStepName = xss_clean(trim($_POST['workflowStep']));
            $workStepName = mysqli_real_escape_string($db_con, $workStepName);
            $workStepOrd = $_POST['workStepOrd'];
            $workStepDesc = xss_clean(trim($_POST['workStepDesc']));
            $workStepDesc = mysqli_real_escape_string($db_con, $workStepDesc);
            
            mysqli_set_charset($db_con, "utf8");
            $chkStep = mysqli_query($db_con, "select * from tbl_step_master where step_order = '$workStepOrd' and workflow_id = '$workFlowId'"); //or die('Error in chkStep:' . mysqli_error($db_con));
            if (mysqli_num_rows($chkStep) > 0) {
                echo'<script>taskFailed("workFlowStep?idwork=' . urlencode($_GET[idwork]) . '","Step already Exist !");</script>';
            } else {
                mysqli_set_charset($db_con, "utf8");
                $adStep = mysqli_query($db_con, "insert into tbl_step_master (step_name, step_description, workflow_id, step_order) values ('$workStepName', '$workStepDesc', '$workFlowId', '$workStepOrd')") or die('Error in workflow:' . mysqli_error($db_con));
                $stepName = mysqli_query($db_con, "select step_name from tbl_step_master where workflow_id = '$workFlowId'"); //or die('Error in getting step name:' . mysqli_error($db_con));
                $rwstepName = mysqli_fetch_assoc($stepName);
                $nameStep = $rwstepName['step_name'];
                // $Steporder = $rwstepName['step_order'];
                mysqli_set_charset($db_con, "utf8");
                $workflName = mysqli_query($db_con, "select workflow_name from tbl_workflow_master where workflow_id = '$workFlowId'"); //or die('Error in getting wrkflow name:' . mysqli_error($db_con));
                $rwworkflName = mysqli_fetch_assoc($workflName);
                $nameworkflw = $rwworkflName['workflow_name'];
                if ($adStep) {
                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'$nameStep Created in $nameworkflw','$date',null,'$host',null)"); //or die('error : ' . mysqli_error($db_con));
                    echo'<script>taskSuccess("workFlowStep?idwork=' . urlencode($_GET[idwork]) . '","' . $lang['Stp_Cratd_Susfly'] . '");</script>';
                } else {
                    echo'<script>taskFailed("workFlowStep?idwork=' . urlencode($_GET[idwork]) . '","' . $lang['Stp_nt_Crted'] . '");</script>';
                }
            }
            mysqli_close($db_con);
        }
        if (isset($_POST['stepdelete'], $_POST['token'])) {

            $idstp = $_POST['sid'];
            mysqli_set_charset($db_con, "utf8");
            $delstepName = mysqli_query($db_con, "select step_name from tbl_step_master where step_id = '$idstp'"); //or die('Error in getting step name:' . mysqli_error($db_con));
            $rwdelstepName = mysqli_fetch_assoc($delstepName);
            $Stepdel = $rwdelstepName['step_name'];
            $stepwkflid = $rwdelstepName['workflow_id'];
            mysqli_set_charset($db_con, "utf8");
            $delstpworkflName = mysqli_query($db_con, "select workflow_name from tbl_workflow_master where workflow_id = '$stepwkflid'"); //or die('Error in getting wrkfl name:' . mysqli_error($db_con));
            $delstpwrkflName = mysqli_fetch_assoc($delstpworkflName);
            $stepwrkflwname = $delstpwrkflName['workflow_name'];
            mysqli_set_charset($db_con, "utf8");
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Step Name $Stepdel Deleted from $stepwrkflwname','$date',null,'$host',null)"); //or die('error : ' . mysqli_error($db_con));
            $delstp = mysqli_query($db_con, "delete from tbl_step_master where step_id='$idstp'"); //or die('Error in delstp:' . mysqli_error($db_con));
            $delFrTask = mysqli_query($db_con, "delete from tbl_task_master where workflow_id='$idwork' and step_id='$idstp'");

            if ($delFrTask) {

                echo'<script>taskSuccess("workFlowStep?idwork=' . urlencode($_GET[idwork]) . '","' . $lang['Stp_Dltd_Scesfly'] . '");</script>';
            } else {
                echo'<script>taskFailed("workFlowStep?idwork=' . urlencode($_GET[idwork]) . '","' . $lang['Stp_nt_Dltd'] . '");</script>';
            }
            mysqli_close($db_con);
        }

//edit steps 
        if (isset($_POST['editSteps'], $_POST['token'])) {
            $workFlowId = $idwork;
            $sid = $_POST['stepid'];
            $workStepName = xss_clean(trim($_POST['workflowStep']));
            $workStepName = mysqli_real_escape_string($db_con, $workStepName);
            $workStepOrd = $_POST['workStepOrd'];
            $workStepDesc = xss_clean(trim($_POST['workStepDesc']));
            $workStepDesc = mysqli_real_escape_string($db_con, $workStepDesc);
            mysqli_set_charset($db_con, "utf8");
            $stpName = mysqli_query($db_con, "select step_name from tbl_step_master where step_id = '$sid'"); //or die('Error in getting step name:' . mysqli_error($db_con));
            $rwstpName = mysqli_fetch_assoc($stpName);
            $getstpname = $rwstpName['step_name'];
            mysqli_set_charset($db_con, "utf8");
            $adStep = mysqli_query($db_con, "update tbl_step_master set step_name='$workStepName', step_description='$workStepDesc', step_order='$workStepOrd' where step_id='$sid'"); //or die('Error in upstp:' . mysqli_error($db_con));
            $newstpName = mysqli_query($db_con, "select step_name from tbl_step_master where step_id = '$sid'"); //or die('Error in getting new step name:' . mysqli_error($db_con));
            $rwnewstpName = mysqli_fetch_assoc($newstpName);
            $stpnameNew = $rwnewstpName['step_name'];
            if ($adStep) {
                mysqli_set_charset($db_con, "utf8");
                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Step Name $getstpname to $stpnameNew Updated','$date',null,'$host',null)"); //or die('error : ' . mysqli_error($db_con));
                echo'<script>taskSuccess("workFlowStep?idwork=' . urlencode($_GET['idwork']) . '","' . $lang['Stp_Updatd_Sucesfly'] . '");</script>';
            } else {
                echo'<script>taskFailed("workFlowStep?idwork=' . urlencode($_GET['idwork']) . '","' . $lang['Stp_nt_Updtd_Crtd'] . '");</script>';
            }
            mysqli_close($db_con);
        }
        ?>

        <?php
        if (isset($_POST['addTask'], $_POST['token'])) {

            $wfid = $_POST['wfid'];
            $stepid = $_POST['stepid'];
            $taskName = xss_clean(trim($_POST['taskName']));
            $task_name = mysqli_real_escape_string($db_con, $taskName);
            $taskOrder = $_POST['taskOrder'];
            $assiUsers = $_POST['asiusr'];
            $actions = implode(',', $_POST['action']);
            $prity = $_POST['prity'];
            $wfrm = $_POST['frm'];
            $wto = $_POST['to'];

            $wrkHur = $wfrm . ' To ' . $wto;

            $altrusr = $_POST['altrusr'];


            $deadlineType = $_POST['radio'];
            if ($deadlineType == 'Date') {
                $daterange = $_POST['daterange'];
                $daterange = explode("To", $daterange);

                $startDate = date('Y-m-d H:i:s', strtotime($daterange[0]));

                $endDate = date('Y-m-d H:i:s', strtotime($daterange[1]));
                $date1 = new DateTime($startDate);
                $date2 = new DateTime($endDate);
                //print_r($date1);
                // print_r($date2);
                $diff = $date1->diff($date2);

                $deadLine = $diff->h * 60 + $diff->days * 24 * 60 + $diff->i;
                //echo $deadLine=$deadLine.'.'.$diff->i;
                //echo   $deadLine=round($deadLine/60*60,1);
                // die('ok');
            } else if ($deadlineType == 'Days') {
                $deadLine = $_POST['days'];
            } else if ($deadlineType == 'Hrs') {
                $deadLine = $_POST['hrs'] * 60;
            }

            $taskIns = $_POST['taskIns'];
            $supvsr = $_POST['supvsr'];

            //getting workflow Name
            mysqli_set_charset($db_con, "utf8");
            $TaskwrkflN = mysqli_query($db_con, "select workflow_name from tbl_workflow_master where workflow_id = '$wfid'"); //or die('Error in getting wrkfl name:' . mysqli_error($db_con));
            $Taskwrkfl = mysqli_fetch_assoc($TaskwrkflN);
            $wrkflwnameTask = $Taskwrkfl['workflow_name'];

            $chkTskOrd = mysqli_query($db_con, "select * from tbl_task_master where workflow_id='$wfid' and step_id='$stepid' and task_order='$taskOrder'") or die('Error in chktsk:' . mysqli_error($db_con));

            if (mysqli_num_rows($chkTskOrd)) {
                echo'<script>taskFailed("workFlowStep?idwork=' . urlencode($_GET[idwork]) . '","' . $lang['Tk_Ordr_alrady_Exst'] . '");</script>';
            } else {
                mysqli_set_charset($db_con, "utf8");
                $insertTask = mysqli_query($db_con, "insert into tbl_task_master (task_name, assign_user,alternate_user, priority_id, deadline, deadline_type,working_hour, task_instructions, supervisor, task_order, step_id, workflow_id, task_created_date,actions) values('$taskName', '$assiUsers','$altrusr', '$prity', '$deadLine', '$deadlineType','$wrkHur', '$taskIns', '$supvsr', '$taskOrder', '$stepid', '$wfid', '$date','$actions')") or die('Error' . mysqli_error($db_con));
                if ($insertTask) {
                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Task Name $taskName Added in workFlow $wrkflwnameTask','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                    echo'<script>taskSuccess("workFlowStep?idwork=' . urlencode($_GET[idwork]) . '","' . $lang['Task_Ad_Sucesfully'] . '");</script>';
                } else {
                    echo'<script>taskFailed("workFlowStep?idwork=' . urlencode($_GET[idwork]) . '","' . $lang['Tsk_nt_Aded'] . '");</script>';
                }
            }
            mysqli_close($db_con);
        }
        ?>

        <?php
        if (isset($_POST['updateTask'], $_POST['token'])) {
            $wfid = $_POST['wfid'];
            $stepid = $_POST['stepid'];
            $taskName = xss_clean(trim($_POST['taskName']));
            $task_name = mysqli_real_escape_string($db_con, $taskName);
            $taskId = $_POST['taskId'];
            $taskOrder = $_POST['taskOrder'];
            $assiUsers = $_POST['asiusr'];
            $actions = implode(',', $_POST['action']);
            $prity = $_POST['prity'];

            $wfrm = $_POST['frm'];
            $wto = $_POST['to'];

            $wrkHur = $wfrm . ' To ' . $wto;

            $altrusr = $_POST['altrusr'];

            $deadlineType = $_POST['radio'];
            if ($deadlineType == 'Date') {
                $daterange = $_POST['daterange'];
                $daterange = explode("To", $daterange);

                $startDate = date('Y-m-d H:i:s', strtotime($daterange[0]));

                $endDate = date('Y-m-d H:i:s', strtotime($daterange[1]));
                $date1 = new DateTime($startDate);
                $date2 = new DateTime($endDate);
                //print_r($date1);
                //print_r($date2);
                $diff = $date1->diff($date2);

                $deadLine = $diff->h + $diff->days * 24 + $diff->i; //conert in minute
                ;
                //echo   $deadLine=round($deadLine/60*60,1);
            } else if ($deadlineType == 'Days') {
                $deadLine = $_POST['days'];
            } else if ($deadlineType == 'Hrs') {
                $deadLine = $_POST['hrs'] * 60;
            }

            $taskIns = $_POST['taskIns'];
            $supvsr = $_POST['supvsr'];
            mysqli_set_charset($db_con, "utf8");
            $tskName = mysqli_query($db_con, "select task_name from tbl_task_master where task_id = '$taskId'"); //or die('Error in getting task name:' . mysqli_error($db_con));
            $rwTaskName = mysqli_fetch_assoc($tskName);
            $getTskName = $rwTaskName['task_name'];
            mysqli_set_charset($db_con, "utf8");
            $wrkfNme = mysqli_query($db_con, "select workflow_name from tbl_workflow_master where workflow_id = '$wfid'"); // or die('Error in getting wrkfl name:' . mysqli_error($db_con));
            $GetwrkfNme = mysqli_fetch_assoc($wrkfNme);
            $wrkflwName = $GetwrkfNme['workflow_name'];

            $updTask = mysqli_query($db_con, "update tbl_task_master set task_name='$taskName', assign_user='$assiUsers', alternate_user='$altrusr', priority_id='$prity', deadline='$deadLine',working_hour='$wrkHur', deadline_type='$deadlineType', task_instructions='$taskIns', supervisor='$supvsr', task_order='$taskOrder', step_id='$stepid', workflow_id='$wfid', task_created_date='$date',actions='$actions' where task_id = '$taskId'"); // or die('Error' . mysqli_error($db_con));


            if ($updTask) {
                mysqli_set_charset($db_con, "utf8");
                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Task Name $getTskName to $taskName  Updated in workFlow $wrkflwName','$date',null,'$host',null)"); // or die('error : ' . mysqli_error($db_con));
                echo'<script>taskSuccess("workFlowStep?idwork=' . urlencode($_GET[idwork]) . '","' . $lang['Tk_Updtd'] . '");</script>';
            } else {
                echo'<script>taskFailed("workFlowStep?idwork=' . urlencode($_GET[idwork]) . '","' . $lang['Tk_nt_Updtd'] . '");</script>';
            }
            mysqli_close($db_con);
        }
        ?>  

        <?php
        if (isset($_POST['taskdelete'], $_POST['token'])) {

            $idtsk = $_POST['tid'];
            mysqli_set_charset($db_con, "utf8");
            $deltskName = mysqli_query($db_con, "select task_name from tbl_task_master where task_id = '$idtsk'") or die('Error in getting task name:' . mysqli_error($db_con));
            $rwdelTskName = mysqli_fetch_assoc($deltskName);
            $getdelTskName = $rwdelTskName['task_name'];
            $getwrkflid = $rwdelTskName['workflow_id'];
            mysqli_set_charset($db_con, "utf8");
            $wrkFlNme = mysqli_query($db_con, "select workflow_name from tbl_workflow_master where workflow_id = '$getwrkflid'") or die('Error in getting wrkfl name:' . mysqli_error($db_con));
            $getwrkFlNme = mysqli_fetch_assoc($wrkFlNme);
            $wrkflowName = $getwrkFlNme['workflow_name'];
            mysqli_set_charset($db_con, "utf8");
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Task Name $getdelTskName Deleted From Workflow $wrkflowName','$date',null,'$host',null)"); //or die('error : ' . mysqli_error($db_con));
            $delstp = mysqli_query($db_con, "delete from tbl_task_master where task_id='$idtsk'"); //or die('Error in delstp:' . mysqli_error($db_con));

            if ($delstp) {

                echo'<script>taskSuccess("workFlowStep?idwork=' . urlencode($_GET[idwork]) . '","' . $lang['Task_Delted_Sucesfly'] . '");</script>';
            } else {
                echo'<script>taskFailed("workFlowStep?idwork=' . urlencode($_GET[idwork]) . '","' . $lang['Task_nt_Dltd'] . '");</script>';
            }
            mysqli_close($db_con);
        }
        ?>
    </body>
</html>






