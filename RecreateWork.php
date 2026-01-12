
<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    require_once './application/config/database.php';

    error_reporting(E_ALL);
    require_once './application/pages/head.php';
    require_once './application/pages/function.php';
    require_once './application/pages/sendSms.php';
    $user_id = $_SESSION['cdes_user_id'];
    $tkt = base64_decode(urldecode($_GET['tkt']));

    $task = mysqli_query($db_con, "SELECT ttm.workflow_id,tdaf.doc_id,tdaf.task_remarks FROM tbl_task_master as ttm inner join `tbl_doc_assigned_wf` as tdaf on ttm.task_id=tdaf.task_id  WHERE ticket_id='$tkt'");
    $rwTask = mysqli_fetch_array($task);
    $work = mysqli_query($db_con, "select * from tbl_workflow_master where workflow_id='$rwTask[workflow_id]'");
    $rwWork = mysqli_fetch_assoc($work);
    $workfid = $rwWork['workflow_id'];
    $dcAsndc_id = $rwTask['doc_id'];
    //for user role
    $chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

    $rwgetRole = mysqli_fetch_assoc($chekUsr);

    if ($rwgetRole['workflow_initiate_file'] != '1') {
        header('Location: ./index');
    }
    ?>
    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <!--for searchable select-->
    <link href="assets/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" />   
    <!-- Plugin Css-->
    <link rel="stylesheet" href="assets/plugins/magnific-popup/css/magnific-popup.css" />


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
                    <div class="container">

                        <!-- Page-Title -->
                        <div class="row">
                            <div class="col-sm-12">

                                <ol class="breadcrumb">
                                    <li>
                                        <a href="#"><?php echo $lang['Workflow_management'] ?></a>
                                    </li>
                                    <li>
                                        <a href="taskTrack"><?php echo $lang['trck_yr_tsk_status']; ?></a>
                                    </li>
                                    <li>
                                        <a href="#"><?php echo $lang['Re-initiate_workflow']; ?></a>
                                    </li>
                                    <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                                </ol>
                            </div>
                        </div>
                        <div class="box box-primary">
                            <div class="panel">

                                <div class="panel-body">
                                    <form method="post" enctype="multipart/form-data">

                                        <div class="row" id="descp">

                                            <div class="col-md-12 form-group" >
                                                <?php
                                                $getformid = mysqli_query($db_con, "select * from tbl_bridge_workflow_to_form where workflow_id='$workfid'")or die('Error in getWorkflw upload:' . mysqli_error($db_con));
                                                $form_id = mysqli_fetch_assoc($getformid);
                                                $formid = $form_id['form_id'];
                                                ?>
                                                <?php
                                                if ($rwWork['form_req'] == 1 && mysqli_num_rows($getformid) > 0) {
                                                    $formqry = mysqli_query($db_con, "select * from tbl_form_attribute where fid='$formid' order by aid asc") or die('Form:' . mysqli_error($db_con));
                                                    $sqlqry = mysqli_query($db_con, "select * from  tbl_form_master where fid='$formid'")or die('Form:' . mysqli_error($db_con));

                                                    $formname = mysqli_fetch_assoc($sqlqry);
                                                    $i = 0;
                                                    while ($row = mysqli_fetch_assoc($formqry)) {
                                                        $i++;
                                                        ?>

                                                        <?php
                                                        if ($row['subtype'] == "h1" || $row['subtype'] == "h2" || $row['subtype'] == "h3") {
                                                            ?>
                                                            <<?= $row['subtype'] ?> ><?= $formname['form_name'] ?><?= $row['subtype'] ?>
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

                                                                    <label class="" style="margin-right: 10px;">  <input type="radio" value="<?= $chkdata['value'] ?>" name="<?= $row['name'] ?>">  <?= $chkdata['label'] ?></label>

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
                                                                <div class="">
                                                                    <label><?= $row['label'] ?></label> <input type="<?= $row['type'] ?>" value="<?= $row['value'] ?>"  class="<?= $row['class'] ?>" placeholder="<?= $row['placeholder'] ?>" name="<?= $row['name'] ?>" maxlength="<?= $row['maxlength'] == 0 ? '255' : $row['maxlength'] ?>" <?= $row['required'] == 1 ? 'required' : '' ?>>
                                                                </div>
                                                            </div>
                                                        <?php } ?>

                                                        <?php
                                                        if ($row['type'] == "date") {
                                                            ?>
                                                            <div class="col-md-6 m-t-5">
                                                                <label><?= $row['label'] ?></label>
                                                                <div class="input-group">
                                                                    <input type="text" value="<?= $row['value'] ?>"  class="form-control datepicker" placeholder="<?= $row['placeholder'] ?>" name="<?= $row['name'] ?>" maxlength="<?= $row['maxlength'] == 0 ? '255' : $row['maxlength'] ?>" <?= $row['required'] == 1 ? 'required' : '' ?>>
                                                                    <span class="input-group-addon bg-custom b-0 text-white"><i class="icon-calender"></i></span>
                                                                </div>
                                                            </div>
                                                        <?php } ?>

                                                        <?php
                                                        if ($row['type'] == "select") {
                                                            ?>
                                                            <div class="col-md-6 m-t-5">

                                                                <label><?= $row['label']; ?></label>
                                                                <select name="<?= $row['name']; ?>" class="<?= $row['class']; ?>"  <?= $row['required'] == 1 ? 'required' : '' ?> onchange="leavetype(this.value)">
                                                                    <?php
                                                                    $selectid = $row['aid'];
                                                                    $selectqry = mysqli_query($db_con, "select * from tbl_form_attribute where fid='$formid' and dependency_id='$selectid'");
                                                                    while ($chkdata = mysqli_fetch_assoc($selectqry)) {
                                                                        ?>
                                                                        <<?= $chkdata['type'] ?>><?= $chkdata['label'] ?></<?= $chkdata['type'] ?>>
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
                                                                    <label class="<?= $row['inline'] == 1 ? 'checkbox-inline' : '' ?>"  style="margin-right: 10px;"> <input type="checkbox" name="<?= $chkdata['name'] ?>" value="<?= $chkdata['value'] ?>"><?= $chkdata['label'] ?></label>
                                                                <?php } ?>
                                                            </div>
                                                        <?php } ?>


                                                        <?php
                                                        if ($row['subtype'] == "tinymce") {
                                                            ?>
                                                            <div class="col-md-12 m-t-5">
                                                                <div class="">
                                                                    <label><?= $row['label']; ?></label>
                                                                    <textarea  class="<?= $row['class'] ?>" rows="5" name="<?= $row['name'] ?>" id="editor" ><?= $row['placeholder']; ?><?= ($rwWork['pdf_req'] == 1) ? (isset($row['value']) ? $row['value'] : '') : $rwTask['task_remarks'] ?></textarea>
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
                                                                    <textarea  class="<?= $row['class'] ?>" rows="5" name="<?= $row['name'] ?>" ><?= $row['placeholder']; ?></textarea>
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                        <?php
                                                    }
                                                }if ($rwWork['form_req'] == 0) {
                                                    ?>
                                                    <textarea class="form-control" rows="5" name="taskRemark" id="editor" ></textarea>
                                                <?php } ?>
                                                <?php
                                                if (mysqli_num_rows($getformid) == 0 && $rwWork['form_req'] == 1) {
                                                    $getworkdesp = mysqli_query($db_con, "select * from tbl_workflow_master where workflow_id='$workfid'")or die(mysqli_error($db_con));
                                                    $resultdesp = mysqli_fetch_assoc($getworkdesp);
                                                    ?>
                                                    <textarea class="form-control" rows="5" name="taskRemark" id="editor" ><?= $rwTask['task_remarks'] ?></textarea>
                                                <?php }
                                                ?>

                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 form-group m-t-10">
                                                <label style="color: olivedrab"> <?php echo $lang['Ch_fl_op']; ?> :- </label> 
                                                <input class="filestyle" id="myImage" multiple name="fileName[]" data-buttonname="btn-primary" id="filestyle-4" style="position: absolute; clip: rect(0px, 0px, 0px, 0px);" tabindex="-1" type="file">
                                                <input type="hidden" id="pCount" name="pageCount">
                                            </div>  
                                        </div>

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
                                                            $storeName = mysqli_query($db_con, "select * from tbl_storage_level where sl_id in($sl_Pid)") or die('Error: ' . mysqli_error($db_con));
                                                        $rwstoreName = mysqli_fetch_assoc($storeName);
                                                        ?>
                                                        <option value="<?php echo $rwstoreName['sl_id']; ?>"><?php echo $rwstoreName['sl_name']; ?></option>
                                                        <?php }?>
                                                    </select>
                                                </div>

                                                <div class="form-group" id="child">
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="wfid" value="<?= $_Get['wid'] ?>">
                                        <input type="hidden" id="pCount" name="pageCount">

                                        <!--<button class="btn btn-primary nextBtn pull-right" type="submit" style="margin-top: -34px; margin-right: -6px; " name="uploaddWfd" id="subb">Submit</button>
                                        <button class="btn btn-primary nextBtn pull-right" type="submit" style="margin-top: -34px; margin-right: -6px;" name="uploaddWfd" id="subb">preview</button>-->
                                        <div class="col-sm-12">
                                            <a href="#" id="dataprev" class="rows_selected btn btn-primary pull-right" data-toggle="modal" data-target="#preview"><?php echo $lang['Preview']; ?></a>
                                            <button class="btn btn-danger pull-right m-r-5" type="reset" onclick="fun_hid()"><?php echo $lang['Reset']; ?></button>
                                        </div>
                                        <div id="preview" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                            <div class="modal-dialog modal-full"> 
                                                <div class="modal-content"> 

                                                    <div class="modal-header"> 
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

                                                        <h4 class="modal-title"><?php echo $lang['Preview_Form']; ?></h4>
                                                    </div>
                                                    <div class="modal-body" style="width:100%; margin: auto; text-align: center; vertical-align: middle;">
                                                        <div id="viewpreview"> 

                                                        </div>
                                                    </div> 
                                                    <div class="modal-footer">
                                                        <input type="hidden" id="sl_id1" name="sl_id1">
                                                        <input type="hidden" id="reDel" name="DelFile">
                                                        <button class="btn btn-primary nextBtn" type="submit"  name="uploaddWfd" id="subb"><?php echo $lang['Submit']; ?></button>
                                                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?> </button>
                                                    </div>
                                                </div> 
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- end: page -->
                    </div> <!-- end Panel -->
                </div> <!-- container -->

            </div> <!-- content -->

            <?php require_once './application/pages/footer.php'; ?>
        </div>   

        <?php require_once './application/pages/footerForjs.php'; ?>

        <script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>

        <!--for searchable select -->
        <script type="text/javascript" src="assets/plugins/jquery-quicksearch/jquery.quicksearch.js"></script>

        <script src="assets/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
        <script src="assets/plugins/tinymce/tinymce.min.js"></script>
        <link href="assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet">
        <script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
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
                                             $(document).ready(function () {
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
                                                     startDate: output
                                                 });
                                             });
                                             function leavetype(res)
                                             {
                                                 var result = res.split("(");
                                                 var co = result[0];
                                                 if (co == "CO")
                                                 {


                                                     //alert(lbl);
                                                     $.post("application/ajax/formFieldDynamic.php", {}, function (result, status) {
                                                         if (status == 'success') {
                                                             $("#co").html(result);

                                                         }
                                                     });

                                                 } else {
                                                     $("#co").html("");
                                                 }

                                             }

        </script>
        <script>
            $(document).ready(function () {
                $('#dataprev').click(function () {
                    //var Remark = $('#editor').val();
                    var content = tinymce.get("editor").getContent();

                    //$("#data-container").html(content);
                    //alert(content);
                    $('#viewpreview').html(content);

                });
            })

        </script>
        <!--show wait gif-->
        <div style=" display: none; background: rgba(0,0,0,0.5); width: 100%; z-index: 2000; position: fixed; top:0;" id="wait">
            <img src="assets/images/proceed.gif" alt="load"  style=" margin-left: 48%; margin-top: 250px; width: 100px; height:100px; position: fixed;" />
        </div> 
        <script>
            //for wait gif display after submit
            var heiht = $(document).height();
            //alert(heiht);
            $('#wait').css('height', heiht);
            $('#subb').click(function () {

                $('#wait').show();
                //$('#wait').css('height',heiht);

                $('#afterClickHide').hide();
                return true;
            });
        </script>
        <script type="text/javascript">
            $(document).ready(function () {
                $('form').parsley();

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
            $("#parentMoveLevel").change(function () {
                var lbl = $(this).val();
                //alert(lbl);
                $.post("application/ajax/uploadWorkFlow.php", {parentId: lbl, levelDepth: 0, sl_id:<?php echo $slid; ?>}, function (result, status) {
                    if (status == 'success') {
                        $("#child").html(result);
                        //alert(result);
                    }
                });
            });

            $("#wfid").change(function () {
                var wfId = $(this).val();

                //alert(lbl);
                $("#subb").show();

            });
            $('input[type=file]').change(function () {
                $("#hidden_div").show();
            });

            function fun_hid() {
                $("#hidden_div").hide();

            }
            //image detail              
            $('#myImage').bind('change', function () {
                //this.files[0].size gets the size of your file.
                if (this.files[0].type == 'application/pdf') {
                    var reader = new FileReader();
                    reader.readAsBinaryString(this.files[0]);
                    reader.onloadend = function () {
                        var count = reader.result.match(/\/Type[\s]*\/Page[^s]/g).length;
                        $("#pageCount").html(count);
                        $("#pCount").val(count);
                        // console.log('Number of Pages:',count );
                    }
                } else {
                    $("#pageCount").html('1');
                    $("#pCount").val('1');
                }

            });

        </script>
        <!--form validation init-->
        <script>
            $("#wfid").change(function () {
                var wfId = $(this).val();
                //alert(lbl);
                $.post("application/ajax/RequireForm.php", {wid: wfId}, function (result, status) {
                    if (status == 'success') {
                        $("#descp").html(result);
                        $("#descp").show();
                    }
                });
            });
        </script>
        <script>
            function checkCODate(date)
            {
                $.post("application/ajax/formFieldAjax.php", {userid: <?= $user_id; ?>, date: date}, function (result, status) {
                    if (status == 'success') {
                        var data = JSON.parse(result);

                        if (data.Status == "true")
                        {
                            $("#result").html("<p style='color:red'>Already Applied For CO Date</p>");
                            $("#subb").attr("disabled", "disabled");
                        }
                        if (data.Status == "False")
                        {
                            $("#subb").removeAttr("disabled");
                            $("#result").html("");
                        }

                    }
                });
            }
        </script>
    </body>
</html>

<?php
if (isset($_POST['uploaddWfd'])) {
    $wfid = base64_decode(urldecode($_GET['wid']));
    $formExistQry = mysqli_query($db_con, "select * from tbl_bridge_workflow_to_form where workflow_id='$wfid'") or die("Error:" . mysqli_error($db_con));
    if (mysqli_num_rows($formExistQry) > 0) {
        // $_POST['taskRemark'];
        $docId = '0';
        $wfid = base64_decode(urldecode($_GET['wid']));
        $wfd = mysqli_query($db_con, "select * from tbl_workflow_master where workflow_id='$wfid'") or die('Error:' . mysqli_error($db_con));
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
        if ($rwWfd['form_req'] == 1) {
            $taskRemark = "";
        } else {
            $taskRemark = mysqli_real_escape_string($db_con, $_POST['taskRemark']);
        }
        //if file uploaded then
        if (!empty($_POST['lastMoveId'])) {

            $chkrw = mysqli_query($db_con, "select * from tbl_task_master where workflow_id = '$wfid'") or die('Error:' . mysqli_error($db_con));

            if (mysqli_num_rows($chkrw) > 0) {
                $sl_id = $_POST['lastMoveId'];
                $id = $sl_id . '_' . $wfid;
                //$docs_name =  $rwslname['sl_name'];
                if ($rwWfd['form_req'] == 1) {
                    $workFlowTblName = $rwWfd['form_tbl_name'];
                    $chkColExist = mysqli_query($db_con, "SELECT COLUMN_NAME FROM  information_schema.COLUMNS where  TABLE_NAME ='$workFlowTblName'  and COLUMN_NAME = 'co';")or die('Error:' . mysqli_error($db_con));

                    if (!empty($_POST['CO']) && mysqli_num_rows($chkColExist) > 0) {
                        $workFlowTblName = mysqli_escape_string($db_con, $workFlowTblName);
                        $dateofco = mysqli_escape_string($db_con, $_POST['CO']);
                        //echo "select tbl_id from '$workFlowTblName' where user_id='$user_id' and co='$dateofco'" ;
                        $qrycochk = mysqli_query($db_con, "select tbl_id from " . $workFlowTblName . " where user_id='$user_id' and co='$dateofco'") or die("Error:" . mysqli_error($db_con));
                        if (mysqli_num_rows($qrycochk) > 0) {
                            echo '<script>taskFailed("RecreateWork", "Opps!! Submission failed")</script>';
                            die();
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
                                // array_push($formvalues, $_POST[$names]);
                            }
                            $sqlForm = "insert into " . $workFlowTblName . "($coloum) values ($values)";

                            $insertqry = mysqli_query($db_con, $sqlForm) or die('Error:' . mysqli_error($db_con));
                            if ($insertqry) {
                                $LastValuesId = mysqli_insert_id($db_con);
                                if (!empty($_POST['CO'])) {
                                    $coDate = $_POST['CO'];

                                    $chkColExist = mysqli_query($db_con, "SELECT COLUMN_NAME FROM  information_schema.COLUMNS where  TABLE_NAME ='$workFlowTblName'  and COLUMN_NAME = 'co';")or die('Error:' . mysqli_error($db_con));
                                    if (mysqli_num_rows($chkColExist) > 0) {
                                        $updateco = mysqli_query($db_con, "update " . $workFlowTblName . " Set co='$coDate' where tbl_id='$LastValuesId'")or die('Error:' . mysqli_error($db_con));
                                    } else {
                                        $qry = mysqli_query($db_con, "ALTER TABLE " . $workFlowTblName . " ADD co varchar(255)")or die('Error:' . mysqli_error($db_con));
                                        if ($qry) {
                                            $updateco = mysqli_query($db_con, "update " . $workFlowTblName . " Set co='$coDate' where tbl_id='$LastValuesId'")or die('Error:' . mysqli_error($db_con));
                                        }
                                    }
                                }

                                $form_id = $formid['form_id'];

                                $data .= "<table class='table'>";
                                $qry = mysqli_query($db_con, "select * from tbl_form_attribute where fid='$form_id' and dependency_id is null")or die(mysqli_error($db_con));

                                $colname = mysqli_query($db_con, "select * from $workFlowTblName where tbl_id='$LastValuesId'") or die("Error:" . mysqli_error($db_con));
                                $fetch = mysqli_fetch_fields($colname);
                                //print_r($fetch);
                                $userdata = mysqli_query($db_con, "select first_name,last_name,designation from tbl_user_master where user_id='$user_id'")or die("Error:" . mysqli_errno($db_con));
                                $userresult = mysqli_fetch_assoc($userdata);
                                $fetchdata = mysqli_fetch_all($colname, MYSQLI_ASSOC);
                                $i = 1;
                                while ($row = mysqli_fetch_assoc($qry)) {


                                    if ($row['type'] == "header") {
                                        $data .= "<tr>";
                                        $data .= "<td colspan='2' align='center'>" . "<b>" . $row['label'] . "</b>" . "</td>";
                                        $data .= "</tr>";
                                    }
                                    if ($i == 1) {
                                        $data .= "<tr>";
                                        $data .= "<td><b>" . Name . "</b></td>";
                                        $data .= "<td>" . $userresult['first_name'] . " " . $userresult['last_name'] . "</td>";
                                        $data .= "</tr>";
                                        $data .= "<tr>";
                                        $data .= "<td><b>" . Designation . "</b></td>";
                                        $data .= "<td>" . $userresult['designation'] . "</td>";
                                        $data .= "</tr>";
                                    }

                                    $data .= "<tr>";
                                    $data .= "<td><b>" . $row['label'] . "</b></td>";
                                    foreach ($fetch as $val) {

                                        if ($row['name'] == $val->name) {

                                            $name = $val->name;
                                            foreach ($fetchdata as $values) {
                                                $data .= "<td>" . $values[$name] . (($values[$name] == 'CO(Compensatory off)') ? ' - ' . $values['co'] : '') . "</td>";
                                            }
                                        }
                                    }
                                    $data .= "</tr>";

                                    $i++;
                                }

                                $data .= "</table>";
                            }
                        }
                    } else {
                        $formbrige = mysqli_query($db_con, "select form_id from tbl_bridge_workflow_to_form where workflow_id='$wfid'") or die('Error:' . mysqli_error($db_con));
                        $formid = mysqli_fetch_assoc($formbrige);
                        $userdata = mysqli_query($db_con, "select first_name,last_name,designation from tbl_user_master where user_id='$user_id'")or die("Error:" . mysqli_errno($db_con));
                        $userresult = mysqli_fetch_assoc($userdata);
                        $formnameqry = mysqli_query($db_con, "select name from tbl_form_attribute where fid='$formid[form_id]' and dependency_id is null") or die('Error:' . mysqli_error($db_con));
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
                        echo $sqlForm = "insert into " . $workFlowTblName . "($coloum) values ($values)";

                        $insertqry = mysqli_query($db_con, $sqlForm) or die('Error:' . mysqli_error($db_con));
                        if ($insertqry) {
                            $LastValuesId = mysqli_insert_id($db_con);
                            if (!empty($_POST['CO'])) {
                                $coDate = $_POST['CO'];

                                $chkColExist = mysqli_query($db_con, "SELECT COLUMN_NAME FROM  information_schema.COLUMNS where  TABLE_NAME ='$workFlowTblName'  and COLUMN_NAME = 'co';")or die('Error:' . mysqli_error($db_con));
                                if (mysqli_num_rows($chkColExist) > 0) {
                                    $updateco = mysqli_query($db_con, "update " . $workFlowTblName . " Set co='$coDate' where tbl_id='$LastValuesId'")or die('Error:' . mysqli_error($db_con));
                                } else {
                                    $qry = mysqli_query($db_con, "ALTER TABLE " . $workFlowTblName . " ADD co varchar(255)")or die('Error:' . mysqli_error($db_con));
                                    if ($qry) {
                                        $updateco = mysqli_query($db_con, "update " . $workFlowTblName . " Set co='$coDate' where tbl_id='$LastValuesId'")or die('Error:' . mysqli_error($db_con));
                                    }
                                }
                            }

                            $form_id = $formid['form_id'];

                            $data .= "<table border='1'  cellpadding='20px' width='70%'>";
                            $qry = mysqli_query($db_con, "select * from tbl_form_attribute where fid='$form_id' and dependency_id is null")or die(mysqli_error($db_con));

                            $colname = mysqli_query($db_con, "select * from $workFlowTblName where tbl_id='$LastValuesId'") or die("Error:" . mysqli_error($db_con));
                            $fetch = mysqli_fetch_fields($colname);

                            $fetchdata = mysqli_fetch_all($colname, MYSQLI_ASSOC);
                            $i = 1;
                            while ($row = mysqli_fetch_assoc($qry)) {

                                if ($row['type'] == "header") {
                                    $data .= "<tr>";
                                    $data .= "<td colspan='2' align='center'>" . "<b>" . $row['label'] . "</b>" . "</td>";
                                    $data .= "</tr>";
                                }
                                if ($i == 1) {
                                    $data .= "<tr>";
                                    $data .= "<td><b>" . Name . "</b></td>";
                                    $data .= "<td>" . $userresult['first_name'] . " " . $userresult['last_name'] . "</td>";
                                    $data .= "</tr>";
                                    $data .= "<tr>";
                                    $data .= "<td><b>" . Designation . "</b></td>";
                                    $data .= "<td>" . $userresult['designation'] . "</td>";
                                    $data .= "</tr>";
                                }

                                $data .= "<tr>";
                                $data .= "<td><b>" . $row['label'] . "</b></td>";
                                foreach ($fetch as $val) {

                                    if ($row['name'] == $val->name) {
                                        $name = $val->name;
                                        foreach ($fetchdata as $values) {
                                            $data .= "<td>" . $values[$name] . (($values[$name] == 'CO(Compensatory off)') ? ' - ' . $values['co'] : '') . "</td>";
                                        }
                                    }
                                }
                                $data .= "</tr>";

                                $i++;
                            }

                            $data .= "</table>";
                        }
                    }
                    if ($pdf_req == 1) {
                        include 'exportpdf.php';

//                if ((isset($_POST['taskRemark'])) && (!empty(trim($_POST['taskRemark'])))) { //if content of CKEditor ISN'T empty
                        $posted_editor = $data; //get content of CKEditor
                        $slperm = mysqli_query($db_con, "select * from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]'");
                        $rwSlperm = mysqli_fetch_assoc($slperm);
                        $sl_id = $rwSlperm['sl_id'];
                        $docName = mysqli_query($db_con, "select sl_id,sl_name from tbl_storage_level where sl_id = '$sl_id'") or die('Eror:' . mysqli_error($db_con));
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
                        $wrkflowDoc = mysqli_query($db_con, "INSERT INTO tbl_document_master (doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages, dateposted) VALUES ('$doc_name', '$pdfName', 'pdf', '$folderName/$pdfName', '$user_id', '$wrkflowFsize', '$pagecount', '$date')") or die('Eror:' . mysqli_error($db_con));
                        $docId = mysqli_insert_id($db_con);
                        exportPDF($posted_editor, $path);
                        $id = $sl_id . '_' . $docId . '_' . $wfid;
//                }
                    } else {
                        $taskRemark = $data;
                    }
                }

                $files = $_FILES['fileName']['name'];
                //print_r($files);
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
                            $folder = str_replace(" ", "", $workFlowName);
                            $image_path = 'extract-here/' . $folder . '/';

                            if (!dir($image_path)) {
                                mkdir($image_path, 0777, TRUE); // or die("Error local folder:". print_r(error_get_last()));
                            }
                            $file_name = time() . '_' . $file_name;
                            $image_path = $image_path . $file_name;

                            $upload = move_uploaded_file($file_tmp, $image_path) or die(print_r(error_get_last()));

                            if ($upload) {

                                $query = "INSERT INTO tbl_document_master(doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages, dateposted) VALUES ('$id', '$fname', '$fileExtn', '$folder/$file_name', '$user_id', '$file_size', '$pageCount', '$date')";
                                $exe = mysqli_query($db_con, $query) or die('Error query failed' . mysqli_error($db_con));
                                if (empty($docId)) {
                                    $docId = mysqli_insert_id($db_con);
                                    $id = $sl_id . '_' . $docId . '_' . $wfid;
                                }
                            }
                        }
                    }
                }
                $getStep = mysqli_query($db_con, "select * from tbl_step_master where workflow_id = '$wfid' ORDER BY step_order ASC LIMIT 1") or die('Error:' . mysqli_error($db_con));
                $getStpId = mysqli_fetch_assoc($getStep);
                $stpId = $getStpId['step_id'];

                $getTask = mysqli_query($db_con, "select * from tbl_task_master where step_id = '$stpId' ORDER BY task_order ASC LIMIT 1") or die('Error:' . mysqli_error($db_con));
                $getTaskId = mysqli_fetch_assoc($getTask);
                // echo 'ok';
                $tskId = $getTaskId['task_id'];

                $getTaskDl = mysqli_query($db_con, "select * from tbl_task_master where task_id='$tskId'") or die('Error:' . mysqli_error($db_con));
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

                $getTask = mysqli_query($db_con, "select * from tbl_task_master where task_id = '$tskId'") or die('Error:' . mysqli_error($db_con));
                $rwgetTask = mysqli_fetch_assoc($getTask);
                $TskStpId = $rwgetTask['step_id'];
                $TskWfId = $rwgetTask['workflow_id'];
                $TskOrd = $rwgetTask['task_order'];
                $TskAsinToId = $rwgetTask['assign_user'];
                $nextTaskOrd = $TskOrd + 1;

                nextTaskAsin($nextTaskOrd, $TskWfId, $TskStpId, $docId, $date, $user_id, $db_con, $taskRemark, $ticket);
                if ($insertInTask) {
                    //echo '<script> alert("ok")</script>';
                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `doc_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,null,'Task Created','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                    require_once './mail.php';
                    $mail = assignTask($ticket, $idins, $db_con, $projectName);
                    if ($mail) {

                        //send sms to mob who submit
//                                $getMobNum = mysqli_query($db_con, "select * from tbl_user_master where user_id = '$user_id'") or die('Error:' . mysqli_error($db_con));
//                                $rwgetMobNum = mysqli_fetch_assoc($getMobNum);
//                                $submtByMob = $rwgetMobNum['phone_no'];
//                                $msg = 'Your Ticket Id is: ' . $ticket . ' and Your Task is in Process.';
//                                $sendMsgToMbl = smsgatewaycenter_com_Send($submtByMob, $msg, $debug = false);
                        //

                         echo '<script>taskSuccess("RecreateWork", "' . $lang['Sumitd_Sucsfly'] . '");</script>';
                    } else {

                        echo '<script>taskFailed("RecreateWork", "' . $lang['Ops_Ml_nt_snt'] . '")</script>';
                    }
                } else {
                    echo '<script>taskFailed("RecreateWork", "' . $lang['Opps_Sbmsn_fld'];
                    '")</script>';
                }
            } else {
                echo '<script>taskFailed("RecreateWork", "' . $lang['Tre_is_no_tsk_in_ts_wfw'] . '")</script>';
            }
        } else if (empty($_POST['lastMoveId'])) {
            $slperm = mysqli_query($db_con, "select * from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]'") or die('Error:' . mysqli_error($db_con));
            ;
            $rwSlperm = mysqli_fetch_assoc($slperm);
            $sl_id = $rwSlperm['sl_id'];
            $chkrw = mysqli_query($db_con, "select * from tbl_task_master where workflow_id = '$wfid'") or die('Error:' . mysqli_error($db_con));
            $id = $sl_id . '_' . $wfid;
            if (mysqli_num_rows($chkrw) > 0) {
                $getStep = mysqli_query($db_con, "select * from tbl_step_master where workflow_id = '$wfid' ORDER BY step_order ASC LIMIT 1") or die('Error:' . mysqli_error($db_con));
                $getStpId = mysqli_fetch_assoc($getStep);
                $stpId = $getStpId['step_id'];

                $getTask = mysqli_query($db_con, "select * from tbl_task_master where step_id = '$stpId' ORDER BY task_order ASC LIMIT 1") or die('Error:' . mysqli_error($db_con));
                $getTaskId = mysqli_fetch_assoc($getTask);
                $tskId = $getTaskId['task_id'];

                $getTaskDl = mysqli_query($db_con, "select * from tbl_task_master where task_id='$tskId'") or die('Error:' . mysqli_error($db_con));
                $rwgetTaskDl = mysqli_fetch_assoc($getTaskDl);

                if ($rwgetTaskDl['deadline_type'] == 'Date' || $rwgetTaskDl['deadline_type'] == 'Hrs') {

                    $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 60));
                }
                if ($rwgetTaskDl['deadline_type'] == 'Days') {

                    $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 24 * 60 * 60));
                }
                //create pdf from form
                if ($rwWfd['form_req'] == 1) {
                    $workFlowTblName = $rwWfd['form_tbl_name'];
                    $chkColExist = mysqli_query($db_con, "SELECT COLUMN_NAME FROM  information_schema.COLUMNS where  TABLE_NAME ='$workFlowTblName'  and COLUMN_NAME = 'co';")or die('Error:' . mysqli_error($db_con));

                    if (!empty($_POST['CO']) && mysqli_num_rows($chkColExist) > 0) {
                        $workFlowTblName = mysqli_escape_string($db_con, $workFlowTblName);
                        $dateofco = mysqli_escape_string($db_con, $_POST['CO']);
                        echo "select tbl_id from '$workFlowTblName' where user_id='$user_id' and co='$dateofco'";
                        $qrycochk = mysqli_query($db_con, "select tbl_id from " . $workFlowTblName . " where user_id='$user_id' and co='$dateofco'") or die("Error:" . mysqli_error($db_con));
                        if (mysqli_num_rows($qrycochk) > 0) {
                            echo '<script>taskFailed("createWork", "Opps!! Submission failed")</script>';
                            die();
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
//                    array_push($formvalues, $_POST[$names]);
                            }
                            $sqlForm = "insert into " . $workFlowTblName . "($coloum) values ($values)";

                            $insertqry = mysqli_query($db_con, $sqlForm) or die('Error:' . mysqli_error($db_con));
                            if ($insertqry) {
                                $LastValuesId = mysqli_insert_id($db_con);
                                if (!empty($_POST['CO'])) {
                                    $coDate = $_POST['CO'];

                                    $chkColExist = mysqli_query($db_con, "SELECT COLUMN_NAME FROM  information_schema.COLUMNS where  TABLE_NAME ='$workFlowTblName'  and COLUMN_NAME = 'co';")or die('Error:' . mysqli_error($db_con));
                                    if (mysqli_num_rows($chkColExist) > 0) {
                                        $updateco = mysqli_query($db_con, "update `" . $workFlowTblName . "` Set co='$coDate' where tbl_id='$LastValuesId'")or die('Error:' . mysqli_error($db_con));
                                    } else {
                                        $qry = mysqli_query($db_con, "ALTER TABLE `" . $workFlowTblName . "` ADD co varchar(255)")or die('Error:' . mysqli_error($db_con));
                                        if ($qry) {
                                            $updateco = mysqli_query($db_con, "update `" . $workFlowTblName . "` Set co='$coDate' where tbl_id='$LastValuesId'")or die('Error:' . mysqli_error($db_con));
                                        }
                                    }
                                }

                                $form_id = $formid['form_id'];

                                $data = "<table class='table'>";
                                $qry = mysqli_query($db_con, "select * from tbl_form_attribute where fid='$form_id' and dependency_id is null")or die(mysqli_error($db_con));

                                $colname = mysqli_query($db_con, "select * from `$workFlowTblName` where tbl_id='$LastValuesId'") or die("Error:" . mysqli_error($db_con));
                                $fetch = mysqli_fetch_fields($colname);
                                print_r($fetch);
                                $userdata = mysqli_query($db_con, "select first_name,last_name,designation from tbl_user_master where user_id='$user_id'")or die("Error:" . mysqli_errno($db_con));
                                $userresult = mysqli_fetch_assoc($userdata);
                                $fetchdata = mysqli_fetch_all($colname, MYSQLI_ASSOC);
                                print_r($fetchdata);
                                $i = 1;
                                while ($row = mysqli_fetch_assoc($qry)) {


                                    if ($row['type'] == "header") {
                                        $data .= "<tr>";
                                        $data .= "<td colspan='2' align='center'>" . "<b>" . $row['label'] . "</b>" . "</td>";
                                        $data .= "</tr>";
                                    }
                                    if ($i == 1) {
                                        $data .= "<tr>";
                                        $data .= "<td><b>" . Name . "</b></td>";
                                        $data .= "<td>" . $userresult['first_name'] . " " . $userresult['last_name'] . "</td>";
                                        $data .= "</tr>";
                                        $data .= "<tr>";
                                        $data .= "<td><b>" . Designation . "</b></td>";
                                        $data .= "<td>" . $userresult['designation'] . "</td>";
                                        $data .= "</tr>";
                                    }

                                    $data .= "<tr>";
                                    $data .= "<td><b>" . $row['label'] . "</b></td>";
                                    foreach ($fetch as $val) {

                                        if ($row['name'] == $val->name) {

                                            $name = $val->name;
                                            foreach ($fetchdata as $values) {
                                                $data .= "<td>" . $values[$name] . (($values[$name] == 'CO(Compensatory off)') ? ' - ' . $values['co'] : '') . "</td>";
                                            }
                                        }
                                    }
                                    $data .= "</tr>";


                                    $i++;
                                }

                                $data .= "</table>";
                            }
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
                                $values .= ",'" . mysqli_real_escape_string($db_con, $_POST["$names"]) . "'";
                            }
//                    array_push($formvalues, $_POST[$names]);
                        }
                        //echo $values;
                        $sqlForm = "insert into " . $workFlowTblName . "($coloum) values ($values)";

                        $insertqry = mysqli_query($db_con, $sqlForm) or die('Error:' . mysqli_error($db_con));
                        if ($insertqry) {
                            $LastValuesId = mysqli_insert_id($db_con);
                            if (!empty($_POST['CO'])) {
                                $coDate = $_POST['CO'];

                                $chkColExist = mysqli_query($db_con, "SELECT COLUMN_NAME FROM  information_schema.COLUMNS where  TABLE_NAME ='$workFlowTblName'  and COLUMN_NAME = 'co';")or die('Error:' . mysqli_error($db_con));
                                if (mysqli_num_rows($chkColExist) > 0) {
                                    $updateco = mysqli_query($db_con, "update `" . $workFlowTblName . "` Set co='$coDate' where tbl_id='$LastValuesId'")or die('Error:' . mysqli_error($db_con));
                                } else {
                                    $qry = mysqli_query($db_con, "ALTER TABLE `" . $workFlowTblName . "` ADD co varchar(255)")or die('Error:' . mysqli_error($db_con));
                                    if ($qry) {
                                        $updateco = mysqli_query($db_con, "update `" . $workFlowTblName . "` Set co='$coDate' where tbl_id='$LastValuesId'")or die('Error:' . mysqli_error($db_con));
                                    }
                                }
                            }

                            $form_id = $formid['form_id'];

                            $data = "<table class='table'>";
                            $qry = mysqli_query($db_con, "select * from tbl_form_attribute where fid='$form_id' and dependency_id is null")or die(mysqli_error($db_con));

                            $colname = mysqli_query($db_con, "select * from $workFlowTblName where tbl_id='$LastValuesId'") or die("Error:" . mysqli_error($db_con));
                            $fetch = mysqli_fetch_fields($colname);

                            $userdata = mysqli_query($db_con, "select first_name,last_name,designation from tbl_user_master where user_id='$user_id'")or die("Error:" . mysqli_errno($db_con));
                            $userresult = mysqli_fetch_assoc($userdata);
                            $fetchdata = mysqli_fetch_all($colname, MYSQLI_ASSOC);

                            //echo'table'. $fetchdata['wf_materialrequired'];
                            //echo $fetchdata['ticket_id'];
                            $i = 1;
                            while ($row = mysqli_fetch_assoc($qry)) {
                                echo $row['name'];

                                if ($row['type'] == "header") {
                                    $data .= "<tr>";
                                    $data .= "<td colspan='2' align='center'>" . "<b>" . $row['label'] . "</b>" . "</td>";
                                    $data .= "</tr>";
                                }
                                if ($i == 1) {
                                    $data .= "<tr>";
                                    $data .= "<td><b>" . Name . "</b></td>";
                                    $data .= "<td>" . $userresult['first_name'] . " " . $userresult['last_name'] . "</td>";
                                    $data .= "</tr>";
                                    $data .= "<tr>";
                                    $data .= "<td><b>" . Designation . "</b></td>";
                                    $data .= "<td>" . $userresult['designation'] . "</td>";
                                    $data .= "</tr>";
                                }

                                $data .= "<tr>";
                                $data .= "<td><b>" . $row['label'] . "</b></td>";
                                foreach ($fetch as $val) {

                                    if ($row['name'] == $val->name) {

                                        echo $name = $val->name;
                                        foreach ($fetchdata as $values) {
                                            $data .= "<td>" . $values[$name] . (($values[$name] == 'CO(Compensatory off)') ? ' - ' . $values['co'] : '') . "</td>";
                                        }
                                    }
                                }
                                $data .= "</tr>";

                                $i++;
                            }

                            $data .= "</table>";
                        }
                    }
                    if ($pdf_req == 1) {

                        include 'exportpdf.php';

//                if ((isset($_POST['taskRemark'])) && (!empty(trim($_POST['taskRemark'])))) { //if content of CKEditor ISN'T empty
                        echo $posted_editor = $data; //get content of CKEditor
                        $slperm = mysqli_query($db_con, "select * from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]'");
                        $rwSlperm = mysqli_fetch_assoc($slperm);
                        $sl_id = $rwSlperm['sl_id'];
                        $docName = mysqli_query($db_con, "select sl_id,sl_name from tbl_storage_level where sl_id = '$sl_id'") or die('Eror:' . mysqli_error($db_con));
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
                        $wrkflowDoc = mysqli_query($db_con, "INSERT INTO tbl_document_master (doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages, dateposted) VALUES ('$doc_name', '$pdfName', 'pdf', '$folderName/$pdfName', '$user_id', '$wrkflowFsize', '$pagecount', '$date')") or die('Eror:' . mysqli_error($db_con));
                        $docId = mysqli_insert_id($db_con);
                        exportPDF($posted_editor, $path);
                        $id = $sl_id . '_' . $docId . '_' . $wfid;
//                }
                    } else {
                        $taskRemark = $data;
                    }
                }
                //end create pdf
                //upload files if any
                $files = $_FILES['fileName']['name'];
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

                                $query = "INSERT INTO tbl_document_master(doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages, dateposted) VALUES ('$id', '$fname', '$fileExtn', '$folder/$file_name', '$user_id', '$file_size', '$pageCount', '$date')";
                                $exe = mysqli_query($db_con, $query) or die('Error query failed' . mysqli_error($db_con));
                                if (empty($docId)) {
                                    $docId = mysqli_insert_id($db_con);
                                    $id = $sl_id . '_' . $docId . '_' . $wfid;
                                }
                            }
                        }
                    }
                }
                //end upload file
               // echo "INSERT INTO tbl_doc_assigned_wf(task_id, doc_id, start_date, end_date, task_status, assign_by, task_remarks,ticket_id) VALUES ('$tskId','$docId',  '$date', '$endDate', 'Pending', '$user_id','$taskRemark','$ticket')";
                $taskRemark = mysqli_real_escape_string($db_con, $taskRemark);
                $insertInTask = mysqli_query($db_con, "INSERT INTO tbl_doc_assigned_wf(task_id, doc_id, start_date, end_date, task_status, assign_by, task_remarks,ticket_id) VALUES ('$tskId','$docId',  '$date', '$endDate', 'Pending', '$user_id','$taskRemark','$ticket')") or die('Erorr: ' . mysqli_error($db_con));
                $idins = mysqli_insert_id($db_con);

                $getTask = mysqli_query($db_con, "select * from tbl_task_master where task_id = '$tskId'") or die('Error:' . mysqli_error($db_con));
                $rwgetTask = mysqli_fetch_assoc($getTask);
                $TskStpId = $rwgetTask['step_id'];
                $TskWfId = $rwgetTask['workflow_id'];
                $TskOrd = $rwgetTask['task_order'];
                $TskAsinToId = $rwgetTask['assign_user'];
                $nextTaskOrd = $TskOrd + 1;
                //for export pdf

                nextTaskAsin($nextTaskOrd, $TskWfId, $TskStpId, $docId, $date, $user_id, $db_con, $taskRemark, $ticket);

                if ($insertInTask) {
                    //echo '<script>taskSuccess("createWork", "Submitted Successfully!!");</script>';
                    require_once './mail.php';
                    //echo '<script>alert("ok")</script>';
                    $mail = assignTask($ticket, $idins, $db_con, $projectName);

                    if ($mail) {

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

                      echo '<script>taskSuccess("RecreateWork", "' . $lang['Sumitd_Sucsfly'] . '");</script>';
                    } else {
                        //echo'Opps!! Mail not sent!';
                        echo '<script>taskFailed("RecreateWork", "' . $lang['Ops_Ml_nt_snt'] . '")</script>';
                    }
                } else {
                    echo '<script>taskFailed("RecreateWork", "' . $lang['Opps_Sbmsn_fld'].'")</script>';
                }
            } else {
                echo '<script>taskFailed("RecreateWork", "' . $lang['Tre_is_no_tsk_in_ts_wfw'] . '")</script>';
            }
        } else {
            echo '<script>taskFailed("RecreateWork", "' . $lang['Task_Creation_Failed_Please_Select_storage'] . '")</script>';
        }
    } else {
        $docId = '0';
        $wfid = base64_decode(urldecode($_GET['wid']));

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
        if ($rwWfd['form_req'] == 1) {
            $taskRemark = "";
        } else {
            $taskRemark = mysqli_real_escape_string($db_con, $_POST['taskRemark']);
        }
        //if file uploaded then
        if (!empty($_POST['lastMoveId'])) {

            $chkrw = mysqli_query($db_con, "select * from tbl_task_master where workflow_id = '$wfid'") or die('Error:' . mysqli_error($db_con));

            if (mysqli_num_rows($chkrw) > 0) {
                $sl_id = $_POST['lastMoveId'];
                $id = $sl_id . '_' . $wfid;


                //$docs_name =  $rwslname['sl_name'];
                if ($rwWfd['form_req'] == 1) {
                    include 'exportpdf.php';

                    if ((isset($_POST['taskRemark'])) && (!empty(trim($_POST['taskRemark'])))) { //if content of CKEditor ISN'T empty
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
                        $doc_name = $sl_id . '_' . $wfid;
                        $pagecount = count_pages($path);
                        $wrkflowDoc = mysqli_query($db_con, "INSERT INTO tbl_document_master (doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages, dateposted) VALUES ('$doc_name', '$pdfName', 'pdf', '$folderName/$pdfName', '$user_id', '$wrkflowFsize', '$pagecount', '$date')") or die('Eror:' . mysqli_error($db_con));
                        $docId = mysqli_insert_id($db_con);
                        exportPDF($posted_editor, $path);
                        $id = $sl_id . '_' . $docId . '_' . $wfid;
                    }
                }

                $files = $_FILES['fileName']['name'];
                //print_r($files);
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
                            $folder = str_replace(" ", "", $workFlowName);
                            $image_path = 'extract-here/' . $folder . '/';

                            if (!dir($image_path)) {
                                mkdir($image_path, 0777, TRUE); // or die("Error local folder:". print_r(error_get_last()));
                            }
                            $file_name = time() . '_' . $file_name;
                            $image_path = $image_path . $file_name;

                            $upload = move_uploaded_file($file_tmp, $image_path) or die(print_r(error_get_last()));

                            if ($upload) {

                                $query = "INSERT INTO tbl_document_master(doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages, dateposted) VALUES ('$id', '$fname', '$fileExtn', '$folder/$file_name', '$user_id', '$file_size', '$pageCount', '$date')";
                                $exe = mysqli_query($db_con, $query) or die('Error query failed' . mysqli_error($db_con));
                                if (empty($docId)) {
                                    $docId = mysqli_insert_id($db_con);
                                    $id = $sl_id . '_' . $docId . '_' . $wfid;
                                }
                            }
                        }
                    }
                }
                $getStep = mysqli_query($db_con, "select * from tbl_step_master where workflow_id = '$wfid' ORDER BY step_order ASC LIMIT 1") or die('Error:' . mysqli_error($db_con));
                $getStpId = mysqli_fetch_assoc($getStep);
                $stpId = $getStpId['step_id'];

                $getTask = mysqli_query($db_con, "select * from tbl_task_master where step_id = '$stpId' ORDER BY task_order ASC LIMIT 1") or die('Error:' . mysqli_error($db_con));
                $getTaskId = mysqli_fetch_assoc($getTask);
                // echo 'ok';
                $tskId = $getTaskId['task_id'];

                $getTaskDl = mysqli_query($db_con, "select * from tbl_task_master where task_id='$tskId'") or die('Error:' . mysqli_error($db_con));
                $rwgetTaskDl = mysqli_fetch_assoc($getTaskDl);

                if ($rwgetTaskDl['deadline_type'] == 'Date' || $rwgetTaskDl['deadline_type'] == 'Hrs') {

                    $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 60));
                }
                if ($rwgetTaskDl['deadline_type'] == 'Days') {

                    $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 24 * 60 * 60));
                }

                $insertInTask = mysqli_query($db_con, "INSERT INTO tbl_doc_assigned_wf(task_id, doc_id, start_date, end_date, task_status, assign_by, task_remarks,ticket_id) VALUES ('$tskId', '$docId', '$date', '$endDate', 'Pending', '$user_id','$taskRemark','$ticket')"); // or die('Erorr: hh' . mysqli_error($db_con));
                $idins = mysqli_insert_id($db_con);

                $getTask = mysqli_query($db_con, "select * from tbl_task_master where task_id = '$tskId'") or die('Error:' . mysqli_error($db_con));
                $rwgetTask = mysqli_fetch_assoc($getTask);
                $TskStpId = $rwgetTask['step_id'];
                $TskWfId = $rwgetTask['workflow_id'];
                $TskOrd = $rwgetTask['task_order'];
                $TskAsinToId = $rwgetTask['assign_user'];
                $nextTaskOrd = $TskOrd + 1;

                nextTaskAsin($nextTaskOrd, $TskWfId, $TskStpId, $docId, $date, $user_id, $db_con, $taskRemark, $ticket);
                if ($insertInTask) {
                    //echo '<script> alert("ok")</script>';
                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `doc_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,null,'Task Created','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                    require_once './mail.php';
                    $mail = assignTask($ticket, $idins, $db_con, $projectName);
                    if ($mail) {

                        //send sms to mob who submit
//                                $getMobNum = mysqli_query($db_con, "select * from tbl_user_master where user_id = '$user_id'") or die('Error:' . mysqli_error($db_con));
//                                $rwgetMobNum = mysqli_fetch_assoc($getMobNum);
//                                $submtByMob = $rwgetMobNum['phone_no'];
//                                $msg = 'Your Ticket Id is: ' . $ticket . ' and Your Task is in Process.';
//                                $sendMsgToMbl = smsgatewaycenter_com_Send($submtByMob, $msg, $debug = false);
                        //

                           echo '<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '", "' . $lang['Sumitd_Sucsfly'] . '");</script>';
                    } else {

                        echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '", "' . $lang['Ops_Ml_nt_snt'] . '")</script>';
                    }
                } else {
                    echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '", "' . $lang['Opps_Sbmsn_fld'];
                    '")</script>';
                }
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
                $getStep = mysqli_query($db_con, "select * from tbl_step_master where workflow_id = '$wfid' ORDER BY step_order ASC LIMIT 1") or die('Error:' . mysqli_error($db_con));
                $getStpId = mysqli_fetch_assoc($getStep);
                $stpId = $getStpId['step_id'];

                $getTask = mysqli_query($db_con, "select * from tbl_task_master where step_id = '$stpId' ORDER BY task_order ASC LIMIT 1") or die('Error:' . mysqli_error($db_con));
                $getTaskId = mysqli_fetch_assoc($getTask);
                $tskId = $getTaskId['task_id'];

                $getTaskDl = mysqli_query($db_con, "select * from tbl_task_master where task_id='$tskId'") or die('Error:' . mysqli_error($db_con));
                $rwgetTaskDl = mysqli_fetch_assoc($getTaskDl);

                if ($rwgetTaskDl['deadline_type'] == 'Date' || $rwgetTaskDl['deadline_type'] == 'Hrs') {

                    $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 60));
                }
                if ($rwgetTaskDl['deadline_type'] == 'Days') {

                    $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 24 * 60 * 60));
                }
                //create pdf from form
                if ($rwWfd['form_req'] == 1) {
                    include 'exportpdf.php';

                    if ((isset($_POST['taskRemark'])) && (!empty(trim($_POST['taskRemark'])))) { //if content of CKEditor ISN'T empty
                        $posted_editor = trim($_POST['taskRemark']); //get content of CKEditor

                        $docName = mysqli_query($db_con, "select sl_id,sl_name from tbl_storage_level where sl_id = '$sl_id'") or die('Eror:' . mysqli_error($db_con));
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
                        $wrkflowDoc = mysqli_query($db_con, "INSERT INTO tbl_document_master (doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages, dateposted) VALUES ('$doc_name', '$pdfName', 'pdf', '$folderName/$pdfName', '$user_id', '$wrkflowFsize', '$pagecount', '$date')") or die('Eror:' . mysqli_error($db_con));
                        $docId = mysqli_insert_id($db_con);
                        exportPDF($posted_editor, $path);
                        $id = $sl_id . '_' . $docId . '_' . $wfid;
                    }
                }
                //end create pdf
                //upload files if any
                $files = $_FILES['fileName']['name'];
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

                                $query = "INSERT INTO tbl_document_master(doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages, dateposted) VALUES ('$id', '$fname', '$fileExtn', '$folder/$file_name', '$user_id', '$file_size', '$pageCount', '$date')";
                                $exe = mysqli_query($db_con, $query) or die('Error query failed' . mysqli_error($db_con));
                                if (empty($docId)) {
                                    $docId = mysqli_insert_id($db_con);
                                    $id = $sl_id . '_' . $docId . '_' . $wfid;
                                }
                            }
                        }
                    }
                }
                //end upload file

                $insertInTask = mysqli_query($db_con, "INSERT INTO tbl_doc_assigned_wf(task_id, doc_id, start_date, end_date, task_status, assign_by, task_remarks,ticket_id) VALUES ('$tskId','$docId',  '$date', '$endDate', 'Pending', '$user_id', '$taskRemark','$ticket')"); // or die('Erorr: hh1' . mysqli_error($db_con));
                $idins = mysqli_insert_id($db_con);

                $getTask = mysqli_query($db_con, "select * from tbl_task_master where task_id = '$tskId'") or die('Error:' . mysqli_error($db_con));
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
                    //echo '<script>alert("ok")</script>';
                    $mail = assignTask($ticket, $idins, $db_con, $projectName);
                    if ($mail) {

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

                          echo '<script>taskSuccess("RecreateWork", "' . $lang['Ops_Ml_nt_snt'] . '");</script>';
                    } else {
                        //echo'Opps!! Mail not sent!';
                        echo '<script>taskFailed("RecreateWork", "' . $lang['Sumitd_Sucsfly'] . '")</script>';
                    }
                } else {
                    echo '<script>taskFailed("RecreateWork", "' . $lang['Opps_Sbmsn_fld'] . '")</script>';
                }
            } else {
                echo '<script>taskFailed("RecreateWork", "' . $lang['Tre_is_no_tsk_in_ts_wfw'] . '")</script>';
            }
        } else {
            echo '<script>taskFailed("RecreateWork", "' . $lang['Task_Creation_Failed_Please_Select_storage'] . '")</script>';
        }
    }
    mysqli_close($db_con);
}

function count_pages($pdfname) {

    $pdftext = file_get_contents($pdfname);

    $num = preg_match_all("/\/Page\W/", $pdftext, $dummy);

    return $num;
}
?>




