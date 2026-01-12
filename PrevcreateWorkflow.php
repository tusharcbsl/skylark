<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    require_once './application/config/database.php';
    require_once './application/pages/head.php';
    ?>

    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />

    <body class="fixed-left">

        <!-- Begin page -->
        <div id="wrapper">

            <!-- Top Bar Start -->
            <?php require_once './application/pages/topBar.php'; ?>
            <!-- Top Bar End -->
            <!-- ========== Left Sidebar Start ========== 1001/10556/00959 12/12/2011 14:33:58-->

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
                            <ol class="breadcrumb">
                                <li><a href="createWorkflow"><?php echo $lang['Workflow_management'];?></a></li>
                                <li class="active"><?php echo $lang['Create_Workflow'];?></li>
                            </ol>
                        </div>
                        <div class="row">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h4 class="header-title"><?php echo $lang['Crte_nw_wfw']?> <a href="addWorkflow" class="btn btn-primary btn-xs pull-right" style="margin-right: 10px;"><?php echo $lang['Bk'];?></a></h4>
                                </div>
                                <div class="box-body">
                                    <div class="col-lg-12">
                                        <div class="card-box">
                                            <div class="row">
                                                <form action=""  method="post">
                                                    <div class="form-group row">
                                                        <div class="col-md-2">
                                                            <label for="userName"><?php echo $lang['Workflow_Name'];?>:<span style="color: red;">*</span></label>
                                                        </div>
                                                        <div class="col-md-10">
                                                            <input type="text" class="form-control" name="workflowName" required>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-2">
                                                            <label for="userName"><?php echo $lang['Slt_Grp'];?>:<span style="color: red;">*</span></label>

                                                        </div>
                                                        <div class="col-md-10">
                                                            <select class="select2 select2-multiple" name="groupswf[]" multiple="multiple" multiple data-placeholder="<?php echo $lang['Select_Group'];?>" required parsley-trigger="change">
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
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-2">
                                                            <label for="userName"><?php echo $lang['Form_Required'];?></label>
                                                        </div>
                                                        <div class="col-md-10">
                                                            <input class="form_req" type="checkbox" name="formRequire" value="1" onchange="valueChanged()"/>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row description" style="display:none;">
                                                        <div class="col-md-12">

                                                            <textarea class="form-control" rows="5" name="workDesc" id="editor"></textarea>

                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="col-md-2">&nbsp;</div>
                                                        <div class="col-md-10">
                                                            <div class="form-group  m-b-0">
                                                                <button class="btn btn-primary waves-effect waves-light" type="submit" name="createWorkflow">
                                                                    <?php echo $lang['Create_Workflow'];?>
                                                                </button>
                                                                <button type="reset" class="btn btn-warning waves-effect waves-light m-l-5">
                                                                    <?php echo $lang['Reset'];?>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>				
                        </div>
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
        </div>
        <!-- END wrapper -->

        <?php require_once './application/pages/footerForjs.php'; ?>
        <script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>
        <script type="text/javascript">
                                                                function valueChanged()
                                                                {
                                                                    if ($('.form_req').is(":checked"))
                                                                        $(".description").show();
                                                                    else
                                                                        $(".description").hide();
                                                                }
        </script>

        <script type="text/javascript">
            $(document).ready(function () {
                $('form').parsley();

            });
            $(".select2").select2();
        </script>
        <!---html textarea editor js code--->
        <script src="assets/plugins/tinymce/tinymce.min.js"></script>

        <script type="text/javascript">
            $(document).ready(function () {
                if ($("#editor").length > 0) {
                    tinymce.init({
                        selector: "textarea#editor",
                        theme: "modern",
                        height: 200,
                        plugins: [
                            "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
                            "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                            "save table contextmenu directionality emoticons template paste textcolor"
                        ],
                        toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | forecolor backcolor emoticons",
                        style_formats: [
                            {title: 'Bold text', inline: 'b'},
                            {title: 'Red text', inline: 'span', styles: {color: '#ff0000'}},
                            {title: 'Red header', block: 'h1', styles: {color: '#ff0000'}},
                        ]
                    });
                }
            });
        </script>

    </body>
</html>
<?php
if (isset($_POST['createWorkflow'])) {

    if (!empty($_POST['workflowName'])) {

        $workflowName = $_POST['workflowName'];

        $checkWrkFlwName = mysqli_query($db_con, "select workflow_name from tbl_workflow_master where FIND_IN_SET('$workflowName', workflow_name)") or die('Error: ' . mysqli_error($db_con));

        if (mysqli_num_rows($checkWrkFlwName) == 1) {//check duplicate name of workflow
            echo'<script>taskFailed("addWorkflow","Workflow Already Exist !");</script>';
        }else {

            if (!empty($_POST['workDesc'])) {

                $workflowDesc = $_POST['workDesc'];
                $workflowDesc = mysqli_real_escape_string($db_con, $workflowDesc);
            }
            if (!empty($_POST['groupswf'])) {

                $workflowgroups = $_POST['groupswf'];
                echo $workflowgroups = implode(",", $workflowgroups);
            }
            if (!empty($_POST['formRequire'])) {

                $formReq = $_POST['formRequire'];
            }
            if ($formReq == 1) {
                $insertWorkflow = mysqli_query($db_con, "insert into tbl_workflow_master (workflow_name,workflow_description,form_req) values ('$workflowName','$workflowDesc','$formReq')") or die('Error in workflow:' . mysqli_error($db_con));
                $workflId = mysqli_insert_id($db_con);
            } else {
                $insertWorkflow = mysqli_query($db_con, "insert into tbl_workflow_master (workflow_name) values ('$workflowName')") or die('Error in workflow:' . mysqli_error($db_con));
                $workflId = mysqli_insert_id($db_con);
            }
            if ($insertWorkflow) {
                $insertworkflowgrp = mysqli_query($db_con, "insert into tbl_workflow_to_group(workflow_id,group_id) values ('$workflId','$workflowgroups')") or die('Error in workflow:' . mysqli_error($db_con));
                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Workflow $workflowName Created','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                echo'<script>taskSuccess("addWorkflow","Workflow Created Successfully !");</script>';
            } else {
                echo'<script>taskFailed("addWorkflow","Workflow not Created !");</script>';
            }
        }
    }
}
?>