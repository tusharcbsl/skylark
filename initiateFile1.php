<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    require_once './application/config/database.php';
    require_once './application/pages/head.php';
    require_once './application/pages/function.php';
    require_once './application/pages/sendSms.php';
    $sameGroupIDs = array();
    $group = mysqli_query($db_con, "select * from tbl_bridge_grp_to_um where find_in_set('$_SESSION[cdes_user_id]',user_ids)") or die('Error' . mysqli_error($db_con));
    while ($rwGroup = mysqli_fetch_assoc($group)) {
        $sameGroupIDs[] = $rwGroup['user_ids'];
    }
    $sameGroupIDs = array_unique($sameGroupIDs);
    sort($sameGroupIDs);
    $sameGroupIDs = implode(',', $sameGroupIDs);
    //for user role
    $chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

    $rwgetRole = mysqli_fetch_assoc($chekUsr);

    if ($rwgetRole['initiate_file'] != '1') {
        header('Location: ./index');
    }
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

                                </ol>
                            </div>
                        </div>
                        <div class="box box-primary">
                                 <div class="box-header with-border">
                                   
                                    <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><?php echo $lang['go_back']; ?> </a>
                                </div>
                            <div class="card-box">

                                <div class="stepwizard">
                                    <div class="stepwizard-row setup-panel">
                                        <div class="stepwizard-step">
                                            <a href="#step-1" type="button" class="btn btn-primary btn-circle">1</a>
                                            <h4 style="text-transform:uppercase;"><b><?php echo $lang['Initiate_File']; ?></b></h4>
                                        </div>
                                        <div class="stepwizard-step">
                                            <a href="#step-2" type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
                                            <h4><b><?php echo $lang['Review']; ?></b></h4>
                                        </div>
                                         <div class="stepwizard-step">
                                            <a href="#step-3" type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
                                            <h4><b><?php echo $lang['PROCEED']; ?></b></h4>
                                        </div>
                                    </div>
                                </div>
                                <form method="post" enctype="multipart/form-data" id="initiate_form">
                                    <div class="row setup-content" id="step-1">
                                        <div class="form-group col-sm-12 m-t-10">
                                            <div class="row">
                                                <div class="col-sm-8">
                                                    <div class="col-sm-2 m-t-10" style="    margin-left: -19px;">
                                                        <label><?php echo $lang['Subject']; ?> :-</label>
                                                    </div>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="subject" class="form-control" placeholder="Enter Subject" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                            <label><?php echo $lang['Do_You_Want_To_Generate_File_Number']?></label>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="col-md-2">
                                                    <label><?php echo $lang['Yes'] ?></label>
                                                    <input type="radio" name="fnumber" value="1" class="fnum">
                                                </div>
                                                 <div class="col-md-2">
                                                    <label><?php echo $lang['No'] ?></label>
                                                    <input type="radio" name="fnumber" value="0" class="fnum">
                                                </div>
                                                
                                            </div>
                                        </div>
                                        <div class="row fnumdiv">
                                            <div class="col-sm-12 m-t-20" id="fnumber">
                                                <div class="col-sm-2 m-t-10" style="width: auto;    margin-left: -8px;">
                                                  <label><?php echo $lang['file_num']; ?>:- </label>  
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            
                                            <div class="col-md-12 m-t-20" >
                                                <label><?php echo $lang['Des']; ?>:- </label>
                                                <div class="form-group">
                                                    <textarea class="form-control" rows="5" name="taskRemark" id="editor" ></textarea>
                                                </div>
                                            </div>
                                        </div>
                                      
                                        <div class="form-group m-t-20">
                                            <button class="btn btn-primary nextBtn pull-right" type="button" ><?php echo $lang['Next']; ?></button>
                                        </div>
                                    </div>
                                       <div class="row setup-content" id="step-2">
                               
                              
                               

                                 
                                        <div class="form-group col-sm-6 m-t-20">
                                            <label style="color: olivedrab"><?php echo $lang['Ch_fl_op']; ?> :- </label>
                                            <input class="filestyle" id="myImage" name="fileName" data-buttonname="btn-primary" id="filestyle-4" style="position: absolute; clip: rect(0px, 0px, 0px, 0px);" tabindex="-1" type="file">
                                            <input type="hidden" id="pCount" name="pageCount">
                                        </div>
                                        <div class="col-md-6 form-group m-t-30">
                                            <!--label style="font-weight: 600; font-size: 20px;">(.pdf )</label-->
                                        </div>
                                        <div style="display: none" id="hidden_div">

                                            <div class="form-group col-sm-12 m-t-20">
                                                <label><?php echo $lang['Select_Storage']; ?> :-</label>
                                                <div class="row" >
                                                    <div class="col-md-3 form-group">

                                                        <select class="form-control" name="moveToParentId" id="parentMoveLevel" >

                                                            <option selected disabled style="background: #808080; color: #121213;"><?php echo $lang['Sel_Strg_Lvl']; ?></option>

                                                            <?php
                                                            $perm = mysqli_query($db_con, "select * from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]' group by sl_id");
                                                            $rwPerm = mysqli_fetch_assoc($perm);
                                                            $slperm = $rwPerm['sl_id'];

                                                            $storeName = mysqli_query($db_con, "select * from tbl_storage_level where sl_id= '$slperm'") or die('Error: ' . mysqli_error($db_con));

                                                            while ($rwstoreName = mysqli_fetch_assoc($storeName)) {
                                                                ?>
                                                                <option value="<?php echo $rwstoreName['sl_id']; ?>"><?php echo $rwstoreName['sl_name']; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>

                                                    <div class="" id="child">
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="form-group m-t-20">
                                            <button class="btn btn-primary nextBtn pull-right" type="button" ><?php echo $lang['Next']; ?></button>
                                        </div>
                                    </div>
                                    <div class="row setup-content" id="step-3">
                                        <div class="form-group m-t-20">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label for="userName"><?php echo $lang['SELECT_EXISTING_WORK_FLOW']; ?> :-</label>
                                                    <select class="selectpicker" data-live-search="true" id="wfid" data-style="btn-white" style="" name="wfid">
                                                        <option style="background: #808080; color: #121213;" value="0"><?php echo $lang['SELECT_EXISTING_WORK_FLOW']; ?></option>
                                                        <?php
                                                        $privileges = array();
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
                                                        // $wfids = implode(",", $wfids);
                                                        // echo 'ok';
                                                        //echo $wfids;
                                                        //echo "<script>alert('$wfids')</script>";
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
                                        <div id="hideonselWf"> <!--hide this div on select existing wf-->
                                            <label><?php echo $lang['Or']; ?></label>
                                            <br /><br />
                                            <label><?php echo $lang['CrT_USRS_FLOW']; ?> :-</label>
                                            <div class="form-group col-xs-12 well">

                                                <div class="row">
                                                    <div class="col-sm-2">
                                                        <label for="userName"><?php echo $lang['Order']; ?><span style="color: red;">*</span></label>
                                                        <input type="number" class="form-control" name="taskOrder[]" min="1" style="height:35px;">
                                                    </div> 
                                                    <div class="col-sm-2">
                                                        <label for="userName"><?php echo $lang['Assign_User']; ?><span style="color: red;">*</span></label>
                                                        <select class="selectpicker" data-live-search="true" name="assignUsr[]" data-style="btn-white">
                                                            <option selected disabled style="background: #808080; color: #121213;"><?php echo $lang['Slt_Usrs']; ?></option>
                                                            <?php
                                                            $user = mysqli_query($db_con, "select * from tbl_user_master where user_id in($sameGroupIDs)");
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
                                                            <option selected disabled style="background: #808080; color: #121213;"><?php echo $lang['Slt_Usrs']; ?></option>
                                                            <?php
                                                            $user = mysqli_query($db_con, "select * from tbl_user_master where user_id in($sameGroupIDs)");
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
                                                        <select class="selectpicker" data-live-search="true" name="supvsr[]" data-style="btn-white">
                                                            <option selected disabled style="background: #808080; color: #121213;"><?php echo $lang['Select_Supervisor']; ?></option>
                                                            <?php
                                                            $user = mysqli_query($db_con, "select * from tbl_user_master where user_id in($sameGroupIDs)");
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
                                                            <label>Deadline<span style="color: red;">*</span></label>
                                                            <table>
                                                                <tr>
                                                                    <td>
                                                                        <div class="radio radio-primary">
                                                                            <input type="radio" name="radio0" id="radio" value="Date" checked>
                                                                            <label for="radio"> <?php echo $lang['Date']; ?> &nbsp;</label>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="radio radio-primary">
                                                                            <input type="radio" name="radio0" id="radio1" value="Days">
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
                                                            <input type="text" class="form-control input-daterange-timepicker" name="daterange[]" value="" style="height: 35px;" id="dateRange" />
                                                            <input type="text" class="form-control days" name="days[]" value="" id="days" style="display: none; height:35px;" placeholder="Days"/>
                                                            <input type="text" class="form-control days" name="hrs[]" value="" id="hrs" style="display: none; height:35px;" placeholder="Hrs"/>
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
                                        <button class="btn btn-primary pull-right" type="submit" name="iniFileSub" id="waitOnSubmit"><?php echo $lang['Submit']; ?></button>


                                    </div>
                                    <!-- end: page -->
                                </form>
                            </div> <!-- end Panel -->
                        </div> <!-- container -->
                    </div> <!-- content -->
                </div>
            </div>
        </div>

        <!--display wait gif image after submit-->
        <div style=" display: none; background: rgba(0,0,0,0.5); width: 100%; z-index: 2000; position: fixed; top:0;" id="wait">;
            <img src="assets/images/proceed.gif" alt="load"  style=" margin-left: 48%; margin-top: 250px; width: 100px; height:100px; position: fixed; "/>
        </div>     
        <?php require_once './application/pages/footer.php'; ?>
        <!-- Right Sidebar -->
        <?php require_once './application/pages/rightSidebar.php'; ?>
        <?php require_once './application/pages/footerForjs.php'; ?>

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
            //for wait gif display after submit
            var heiht = $(document).height();
            //alert(heiht);
            $('#wait').css('height', heiht);
            $('#initiate_form').submit(function () {
                $('#wait').show();
                //$('#wait').css('height',heiht);
                $('#afterSubmt').hide();
                return true;
            });
        </script>
        <!--on select existing wf hide create user flow-->
        <script>
            $(function () {

                $('#wfid').change(function () {
                    if ($('#wfid').val() === '0') {
                        $('#hideonselWf').show();
                    } else {
                        $('#hideonselWf').hide();
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
            $("input:radio[name='radio0']").click(function () {

                var val = $(this).val();

                if (val == 'Date') {
                    $("#dateRange").css("display", "block");
                    $("#days").css("display", "none");
                    $("#hrs").css("display", "none");
                }
                if (val == 'Days') {
                    $("#dateRange").css("display", "none");
                    $("#days").css("display", "block");
                    $("#hrs").css("display", "none");
                }
                if (val == 'Hrs') {
                    $("#dateRange").css("display", "none");
                    $("#days").css("display", "none");
                    $("#hrs").css("display", "block");
                }
            });
        </script>
        <script type="text/javascript">
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
            /* 
             * For File number field remove and javascript
             */
            $(document).ready(function(){
                $(".fnumdiv").hide();
                $(".fnum").change(function(){
                var valu=$(this).val();
                if(valu==1)
                {
                   $(".fnumdiv").show();
                   $("#fnumber").append("<div class='col-sm-5 dymicadd'> <div class='input-group' style='    width: 440px;'><input type='text' class='form-control' name='fnumber' placeholder='Enter File Number'><span class='input-group-addon bg-custom b-0 text-white'><i class='icon-eye'></i></span></div></div>") 
                }
                if(valu==0)
                {
                    $(".fnumdiv").hide();
                    $(".dymicadd").remove() ;
                }
            })  
            })
          

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

        <!--for  intitate file-->
        <script>
            $("a#createOwnflowr").click(function () {
                var createown = 0;
                // alert(id);

                $.post("application/ajax/createownFlow.php", {ID: createown}, function (result, status) {
                    if (status == 'success') {
                        $("#createTaskFlowr").html(result);
                        // alert(result);
                    }
                });
            });
        </script>

    </body>
</html>
<?php
if (isset($_POST['iniFileSub'])) {

    $docId = '';
    $taskRemark = mysqli_real_escape_string($db_con, str_replace("script", "",$_POST['taskRemark']));

    if (empty($taskRemark)) {
        $taskRemark = '';
    }


    $lastMoveId = $_POST['lastMoveId'];

    $user_id = $_SESSION['cdes_user_id'];


    if (empty($_POST['wfid'])) {
        if (!empty($_POST['subject'])) {
            $subject= preg_replace("/[^a-zA-Z0-9& ]/","",$_POST['subject']);
            $checkWrkFlwName = mysqli_query($db_con, "select workflow_name from tbl_workflow_master where FIND_IN_SET('$subject', workflow_name)") or die('Error: ' . mysqli_error($db_con));

            if (mysqli_num_rows($checkWrkFlwName) == 1) {//check duplicate name of workflow
                echo'<script>taskFailed("initiateFile","Workflow of this Subject Already Exist !");</script>';
            } else {
                // if(!empty($_POST['$taskRemark'])){
                //create task

                $taskOrder = $_POST['taskOrder'];
                $assiUsers = $_POST['assignUsr'];
                $altrusr =$_POST['altrUsr'];
                $supvsr =$_POST['supvsr'];

                // if (!empty($taskOrder) && !empty($assiUsers) && !empty($altrusr) && !empty($supvsr)) {
                if (!empty($taskOrder) && !empty($assiUsers) && !empty($supvsr)) {

                    if (count(array_unique($taskOrder)) < count($taskOrder)) {
                        echo '<script>taskFailed("index", "Order No. Can\'t be Same! ")</script>';
                    } else {

                        if (!empty($_FILES['fileName']['name'])) {
                            if (!empty($lastMoveId)) {

                                //create workflow
                                //$taskRemark = mysqli_real_escape_string($db_con, $_POST['taskRemark']);
                                $workflowName = preg_replace("/[^a-zA-Z0-9& ]/","",$_POST['subject']);

                                $insertWorkflow = mysqli_query($db_con, "insert into tbl_workflow_master (workflow_name, workflow_description) values ('$workflowName', '$taskRemark')") or die('Error in workflow:' . mysqli_error($db_con));
                                $workflId = mysqli_insert_id($db_con);

                                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'WorkFlow $workflowName Created','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));

                                //asign workflow to group
                                if ($insertWorkflow) {

                                    $usrGrp = mysqli_query($db_con, "SELECT * FROM `tbl_bridge_grp_to_um` WHERE FIND_IN_SET('$user_id',user_ids)") or die('Error:' . mysqli_error($db_con));

                                    while ($rwusrGrp = mysqli_fetch_assoc($usrGrp)) {
                                        $arrayGrp[] = $rwusrGrp['group_id'];
                                    }

                                    $workflowgroups = implode(",", $arrayGrp);

                                    $insertworkflowgrp = mysqli_query($db_con, "insert into tbl_workflow_to_group(workflow_id,group_id) values ('$workflId','$workflowgroups')") or die('Error in workflow:' . mysqli_error($db_con));
                                }

                                //create step
                                $workStepName = "Step";
                                $workStepOrd = 1;
                                $adStep = mysqli_query($db_con, "insert into tbl_step_master (step_name, workflow_id, step_order) values ('$workStepName', '$workflId', '$workStepOrd')") or die('Error in workflow:' . mysqli_error($db_con));

                                $stepid = mysqli_insert_id($db_con);

                                for ($i = 0; $i < count($taskOrder); $i++) {
                           
                                    $ord = $taskOrder[$i];
                                    $taskName = 'Task' . $ord;
                                    $asUsr =$assiUsers[$i];
                                    $altUsr = $altrusr[$i];
                                    $supVsr = $supvsr[$i];
                                    $deadlineType =$_POST['radio' . $i];

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

                                        $deadLine = $diff->h * 60 + $diff->days * 24 * 60 + $diff->i; //convert in minute
                                        //echo $deadLine=$deadLine.'.'.$diff->i;
                                        //echo   $deadLine=round($deadLine/60*60,1);
                                        // die('ok');
                                    } else if ($deadlineType == 'Days') {
                                        $deadLinee = $_POST['days'];
                                        $deadLine = $deadLinee[$i];
                                    } else if ($deadlineType == 'Hrs') {

                                        $deadLinee = $_POST['hrs'];
                                        $deadLine = $deadLinee[$i] * 60;
                                    }

                                    $dedTyp = $deadlineType;
                                    $dedLn = $deadLine;

                                    $insertTask = mysqli_query($db_con, "insert into tbl_task_master (task_name, assign_user,alternate_user, supervisor, task_order, step_id, workflow_id, task_created_date, deadline, deadline_type) values('$taskName', '$asUsr','$altUsr', '$supVsr', '$ord', '$stepid', '$workflId', '$date', '$dedLn', '$dedTyp')") or die('Error1' . mysqli_error($db_con));
                                    //$insertTask = mysqli_query($db_con, "insert into tbl_task_master (task_order, step_id, workflow_id, task_created_date) values('$ord', '$stepid', '$workflId', '$date')") or die('Error' . mysqli_error($db_con));
                                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'$taskName Created','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                                }
                                if (!empty($lastMoveId)) {

                                    $id = $lastMoveId . '_' . $workflId;
                                }

                                $workFlowArray = explode(" ", $workflowName);
                                $ticket = '';
                                for ($w = 0; $w < count($workFlowArray); $w++) {
                                    $name = $workFlowArray[$w];
                                    $ticket = $ticket . substr($name, 0, 1);
                                }


                                $ticket = $ticket . '_' . $user_id . '_' . strtotime($date);

                                $file_name = $_FILES['fileName']['name'];
                                $file_size = $_FILES['fileName']['size'];
                                $file_type = $_FILES['fileName']['type'];
                                $file_tmp = $_FILES['fileName']['tmp_name'];
                                $pageCount = preg_replace("/[^a-zA-Z0-9& ]/","",$_POST['pageCount']);

                                // $name = explode(".", $file_name);
                                //$encryptName = urlencode(base64_encode($name[0]));
                                //$fileExtn = $name[1];
                                $fileExtn = substr($file_name, strrpos($file_name, ".") + 1);

                                $file_name = time() . '_' . $file_name;

                                $image_path = "extract-here/images/";

                                if (!dir($image_path)) {
                                    mkdir($image_path, 0777, TRUE); // or die("Error local folder:". print_r(error_get_last()));
                                }
                                $image_path = $image_path . $file_name;
                                $upload = move_uploaded_file($file_tmp, $image_path) or die(print_r(error_get_last()));

                                if ($upload) {
                                    
                                    $destinationPath = 'images/'.$file_name;      
                                    $sourcePath = $image_path; 
                                    uploadFileInFtpServer($fileserver, $port, $ftpUser, $ftpPwd, $destinationPath, $sourcePath);

                                    $query = "INSERT INTO tbl_document_master(doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages , dateposted) VALUES ('$id', '$file_name', '$fileExtn', 'images/$file_name', '$user_id', '$file_size', '$pageCount', '$date')";
                                    $exe = mysqli_query($db_con, $query) or die('Error query failed' . mysqli_error($db_con));

                                    $docId = mysqli_insert_id($db_con);

                                    $getFirstTask = mysqli_query($db_con, "select * from tbl_task_master where workflow_id = '$workflId' ORDER BY task_order ASC LIMIT 1") or die('Erorr:' . mysqli_error($db_con));
                                    $rwgetTask = mysqli_fetch_assoc($getFirstTask);
                                    $wTaskId = $rwgetTask[task_id];

                                    $getTaskDl = mysqli_query($db_con, "select * from tbl_task_master where task_id='$wTaskId'") or die('Error:' . mysqli_error($db_con));
                                    $rwgetTaskDl = mysqli_fetch_assoc($getTaskDl);
                                    print_r($rwgetTaskDl);
                                     if ($rwgetTaskDl['deadline_type'] == 'Date' || $rwgetTaskDl['deadline_type'] == 'Hrs') {

                    $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 60));
                }
                if ($rwgetTaskDl['deadline_type'] == 'Days') {

                    $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 24 * 60 * 60));
                    echo $rwgetTaskDl['deadline_type'] ;
                }

                                    $insertInTask = mysqli_query($db_con, "INSERT INTO tbl_doc_assigned_wf(task_id, doc_id, start_date, end_date, task_remarks, task_status, assign_by, ticket_id) VALUES ('$wTaskId', '$docId', '$date', '$endDate', '$taskRemark', 'Pending', '$user_id', '$ticket')") or die('Erorr123:' . mysqli_error($db_con));

                                    $idins = mysqli_insert_id($db_con);


                                    $getTask = mysqli_query($db_con, "select * from tbl_task_master where task_id = '$wTaskId'") or die('Error:' . mysqli_error($db_con));
                                    $rwgetTask = mysqli_fetch_assoc($getTask);
                                    $TskStpId = $rwgetTask['step_id'];
                                    $TskWfId = $rwgetTask['workflow_id'];
                                    $TskOrd = $rwgetTask['task_order'];
                                    $TskAsinToId = $rwgetTask['assign_user'];
                                    $nextTaskOrd = $TskOrd + 1;

                                    nextTaskAsin($nextTaskOrd, $TskWfId, $TskStpId, $docId, $date, $user_id, $db_con, $taskRemark, $ticket);

                                    // echo "<img src='../assets/images/anote-wait.gif' alt='load' id='anotWt' style='display: none;'/> ";
                                    //send mail
                                    require_once './mail.php';
                                    $mail = assignTask($ticket, $idins, $db_con,$projectName);
                                    if ($mail) {


                                        //send sms to mob who submit
//                                $getMobNum = mysqli_query($db_con, "select * from tbl_user_master where user_id = '$user_id'") or die('Error:' . mysqli_error($db_con));
//                                $rwgetMobNum = mysqli_fetch_assoc($getMobNum);
//                                $submtByMob = $rwgetMobNum['phone_no'];
//                                $msg = 'Your Ticket Id is: ' . $ticket . ' and Your Task is in Process.';
//                                $sendMsgToMbl = smsgatewaycenter_com_Send($submtByMob, $msg, $debug = false);
                                        //

                                  
                                   echo '<script>taskSuccess("initiateFile", "'.$lang['Sumitd_Sucsfly'].'");</script>';
                                    } else {

                                        echo '<script>taskFailed("initiateFile", "'.$lang['Ops_Ml_nt_snt'].'")</script>';
                                    }

                                    echo '<script>uploadSuccess("initiateFile", "'.$lang['Process_Assigned_in_Tray'].'");</script>';
                                }
                            } else {
                                echo '<script>taskFailed("initiateFile", "'.$lang['please_Select_Storage'].'")</script>';
                            }
                        } else if (!empty($taskRemark)) {

                            //create workflow
                            $workflowName = preg_replace("/[^a-zA-Z0-9& ]/","",$_POST['subject']);
                            $insertWorkflow = mysqli_query($db_con, "insert into tbl_workflow_master (workflow_name, workflow_description) values ('$workflowName', '$taskRemark')") or die('Error in workflow:' . mysqli_error($db_con));
                            $workflId = mysqli_insert_id($db_con);

                            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'WorkFlow Name $workflowName Created','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));

                            //asign workflow to group
                            if ($insertWorkflow) {

                                $usrGrp = mysqli_query($db_con, "SELECT * FROM `tbl_bridge_grp_to_um` WHERE FIND_IN_SET('$user_id',user_ids)") or die('Error:' . mysqli_error($db_con));

                                while ($rwusrGrp = mysqli_fetch_assoc($usrGrp)) {
                                    $arrayGrp[] = $rwusrGrp['group_id'];
                                }
                                // print_r($arrayGrp);
                                $workflowgroups = implode(",", $arrayGrp);

                                $insertworkflowgrp = mysqli_query($db_con, "insert into tbl_workflow_to_group(workflow_id,group_id) values ('$workflId','$workflowgroups')") or die('Error in workflow:' . mysqli_error($db_con));
                            }

                            //create step
                            $workStepName = "Step";
                            $workStepOrd = 1;
                            $adStep = mysqli_query($db_con, "insert into tbl_step_master (step_name, workflow_id, step_order) values ('$workStepName', '$workflId', '$workStepOrd')") or die('Error in workflow:' . mysqli_error($db_con));

                            $stepid = mysqli_insert_id($db_con);

                            for ($i = 0; $i < count($taskOrder); $i++) {

                                $ord =$taskOrder[$i];
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


                                $insertTask = mysqli_query($db_con, "insert into tbl_task_master (task_name, assign_user,alternate_user, supervisor, task_order, step_id, workflow_id, task_created_date, deadline, deadline_type) values('$taskName', '$asUsr','$altUsr', '$supVsr', '$ord', '$stepid', '$workflId', '$date', '$dedLn', '$dedTyp')") or die('Error1' . mysqli_error($db_con));
                                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,' $taskName Task Added','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                            }

                            if (!empty($lastMoveId)) {

                                $id = $lastMoveId . '_' . $workflId;
                            }

                            $workFlowArray = explode(" ", $workflowName);
                            $ticket = '';
                            for ($w = 0; $w < count($workFlowArray); $w++) {
                                $name = $workFlowArray[$w];
                                $ticket = $ticket . substr($name, 0, 1);
                            }


                            $ticket = $ticket . '_' . $user_id . '_' . strtotime($date);

                            $getFirstTask = mysqli_query($db_con, "select * from tbl_task_master where workflow_id = '$workflId' ORDER BY task_order ASC LIMIT 1") or die('Erorr:' . mysqli_error($db_con));
                            $rwgetTask = mysqli_fetch_assoc($getFirstTask);
                            $wTaskId = $rwgetTask[task_id];


                            $getTaskDl = mysqli_query($db_con, "select * from tbl_task_master where task_id='$wTaskId'") or die('Error:' . mysqli_error($db_con));
                            $rwgetTaskDl = mysqli_fetch_assoc($getTaskDl);
                            print_r($rwgetTaskDl);
                            if ($rwgetTaskDl['deadline_type'] == 'Date' || $rwgetTaskDl['deadline_type'] == 'Hrs') {

                    $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 60));
                }
                if ($rwgetTaskDl['deadline_type'] == 'Days') {

                    $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 24 * 60 * 60));
                }

                            echo 'run';
                            $insertInTask = mysqli_query($db_con, "INSERT INTO tbl_doc_assigned_wf(task_id, doc_id, start_date, end_date, task_remarks, task_status, assign_by, ticket_id) VALUES ('$wTaskId', '0', '$date', '$endDate', '$taskRemark', 'Pending', '$user_id', '$ticket')") or die('Erorr:' . mysqli_error($db_con));
                            $idins = mysqli_insert_id($db_con);

                            $getTask = mysqli_query($db_con, "select * from tbl_task_master where task_id = '$wTaskId'") or die('Error:' . mysqli_error($db_con));
                            $rwgetTask = mysqli_fetch_assoc($getTask);
                            $TskStpId = $rwgetTask['step_id'];
                            $TskWfId = $rwgetTask['workflow_id'];
                            $TskOrd = $rwgetTask['task_order'];
                            $TskAsinToId = $rwgetTask['assign_user'];
                            $nextTaskOrd = $TskOrd + 1;

                            nextTaskAsin($nextTaskOrd, $TskWfId, $TskStpId, $docId, $date, $user_id, $db_con, $taskRemark, $ticket);

                            require_once './mail.php';
                            $mail = assignTask($ticket, $idins, $db_con,$projectName);
                            if ($mail) {

                                //send sms to mob
//                        require_once('login-function.php');
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

                        echo '<script>taskSuccess("initiateFile", "'.$lang['Sumitd_Sucsfly'].'");</script>';
                            } else {

                                echo '<script>taskFailed("initiateFile", "'.$lang['Opps_Sbmsn_fld'].'")</script>';
                            }
                            echo '<script>uploadSuccess("initiateFile", "'.$lang['Process_Assigned_in_Tray'].'");</script>';
                        } else {
                            echo '<script>taskFailed("initiateFile", "'.$lang['please_Select_Storage'].'")</script>';
                        }
                    }
                } else {
                    echo '<script>taskFailed("initiateFile", "'.$lang['Please_Fill_Atleast_One_Order'].'")</script>';
                }
                /*
                  }else{
                  echo '<script>taskFailed("index", "Description is Required !")</script>';
                  }
                 */
            }
        } else {

            echo '<script>taskFailed("initiateFile", "'.$lang['Subject_Name_or_Existing_Work_Flow_should_be_filled'].'")</script>';
        }
    } else {

        $workflId = preg_replace("/[^a-zA-Z0-9& ]/","",$_POST['wfid']);

        if (!empty($workflId) && $workflId != '0') {

            $wrkFlwName = mysqli_query($db_con, "select * from tbl_workflow_master where workflow_id = '$workflId'") or die('Error:' . mysqli_error($db_con));
            $rwwrkFlwName = mysqli_fetch_assoc($wrkFlwName);
            $workflowName = $rwwrkFlwName['workflow_name'];


            if (!empty($lastMoveId)) {
                $id = $lastMoveId . '_' . $workflId;
            }

            $workFlowArray = explode(" ", $workflowName);
            $ticket = '';
            for ($w = 0; $w < count($workFlowArray); $w++) {
                $name = $workFlowArray[$w];
                $ticket = $ticket . substr($name, 0, 1);
            }


            $ticket = $ticket . '_' . $user_id . '_' . strtotime($date);

            if (!empty($_FILES['fileName']['name'])) {
                if (!empty($lastMoveId)) {

                    $file_name = $_FILES['fileName']['name'];
                    $file_size = $_FILES['fileName']['size'];
                    $file_type = $_FILES['fileName']['type'];
                    $file_tmp = $_FILES['fileName']['tmp_name'];
                    $pageCount = preg_replace("/[a-zA-Z0-9& ]/","",$_POST['pageCount']);

                    //$name = explode(".", $file_name);
                    //$encryptName = urlencode(base64_encode($name[0]));
                    //$fileExtn = $name[1];
                    $fileExtn = substr($file_name, strrpos($file_name, ".") + 1);

                    $file_name = time() . '_' . $file_name;

                    $image_path = "extract-here/images/";

                    if (!dir($image_path)) {
                        mkdir($image_path, 0777, TRUE); // or die("Error local folder:". print_r(error_get_last()));
                    }
                    $image_path = $image_path . $file_name;
                    $upload = move_uploaded_file($file_tmp, $image_path) or die(print_r(error_get_last()));

                    if ($upload) {
                        
                        $destinationPath = 'images/'.$file_name;      
                       $sourcePath = $image_path; 
                       uploadFileInFtpServer($fileserver, $port, $ftpUser, $ftpPwd, $destinationPath, $sourcePath);


                        $query = "INSERT INTO tbl_document_master(doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages , dateposted) VALUES ('$id', '$file_name', '$fileExtn', 'images/$file_name', '$user_id', '$file_size', '$pageCount', '$date')";
                        $exe = mysqli_query($db_con, $query) or die('Error query failed' . mysqli_error($db_con));
                        $docId = mysqli_insert_id($db_con);

                        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Document Upload in workflow','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));


                        $getFirstTask = mysqli_query($db_con, "select * from tbl_task_master where workflow_id = '$workflId' ORDER BY task_order ASC LIMIT 1") or die('Erorr:' . mysqli_error($db_con));
                        $rwgetTask = mysqli_fetch_assoc($getFirstTask);
                        $wTaskId = $rwgetTask[task_id];

                        $getTaskDl = mysqli_query($db_con, "select * from tbl_task_master where task_id='$wTaskId'") or die('Error:' . mysqli_error($db_con));
                        $rwgetTaskDl = mysqli_fetch_assoc($getTaskDl);

                       if ($rwgetTaskDl['deadline_type'] == 'Date' || $rwgetTaskDl['deadline_type'] == 'Hrs') {

                    $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 60));
                }
                if ($rwgetTaskDl['deadline_type'] == 'Days') {

                    $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 24 * 60 * 60));
                }

                        $insertInTask = mysqli_query($db_con, "INSERT INTO tbl_doc_assigned_wf(task_id, doc_id, start_date, end_date, task_remarks, task_status, assign_by, ticket_id) VALUES ('$wTaskId', '$docId', '$date', '$endDate', '$taskRemark', 'Pending', '$user_id', '$ticket')") or die('Erorr:' . mysqli_error($db_con));

                        $idins = mysqli_insert_id($db_con);

                        $getTask = mysqli_query($db_con, "select * from tbl_task_master where task_id = '$wTaskId'") or die('Error:' . mysqli_error($db_con));
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



                            echo '<script>taskSuccess("initiateFile", "'.$lang['Sumitd_Sucsfly'].'");</script>';
                        } else {

                            echo '<script>taskFailed("initiateFile", "'.$lang['Ops_Ml_nt_snt'].'")</script>';
                        }

                        echo '<script>uploadSuccess("initiateFile", "'.$lang['Process_Assigned_in_Tray'].'");</script>';
                    }
                } else {
                    echo '<script>taskFailed("initiateFile", "'.$lang['please_Select_Storage'].'")</script>';
                }
            } else if (!empty($taskRemark)) {

                $getFirstTask = mysqli_query($db_con, "select * from tbl_task_master where workflow_id = '$workflId' ORDER BY task_order ASC LIMIT 1") or die('Erorr:' . mysqli_error($db_con));
                $rwgetTask = mysqli_fetch_assoc($getFirstTask);
                $wTaskId = $rwgetTask[task_id];

                $getTaskDl = mysqli_query($db_con, "select * from tbl_task_master where task_id='$wTaskId'") or die('Error:' . mysqli_error($db_con));
                $rwgetTaskDl = mysqli_fetch_assoc($getTaskDl);

             if ($rwgetTaskDl['deadline_type'] == 'Date' || $rwgetTaskDl['deadline_type'] == 'Hrs') {

                    $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 60));
                }
                if ($rwgetTaskDl['deadline_type'] == 'Days') {

                    $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 24 * 60 * 60));
                }

                $insertInTask = mysqli_query($db_con, "INSERT INTO tbl_doc_assigned_wf(task_id, doc_id, start_date, end_date, task_remarks, task_status, assign_by, ticket_id) VALUES ('$wTaskId', '0', '$date', '$endDate', '$taskRemark', 'Pending', '$user_id', '$ticket')") or die('Erorr:' . mysqli_error($db_con));
                $idins = mysqli_insert_id($db_con);

                $getTask = mysqli_query($db_con, "select * from tbl_task_master where task_id = '$wTaskId'") or die('Error:' . mysqli_error($db_con));
                $rwgetTask = mysqli_fetch_assoc($getTask);
                $TskStpId = $rwgetTask['step_id'];
                $TskWfId = $rwgetTask['workflow_id'];
                $TskOrd = $rwgetTask['task_order'];
                $TskAsinToId = $rwgetTask['assign_user'];
                $nextTaskOrd = $TskOrd + 1;

                nextTaskAsin($nextTaskOrd, $TskWfId, $TskStpId, $docId, $date, $user_id, $db_con, $taskRemark, $ticket);
                require_once './mail.php';
                $mail = assignTask($ticket, $idins, $db_con,$projectName);
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
    
                


                    echo '<script>taskSuccess("initiateFile", "'.$lang['Sumitd_Sucsfly'].'");</script>';
                } else {

                    echo '<script>taskFailed("initiateFile", "'.$lang['Ops_Ml_nt_snt'].'")</script>';
                }
                echo '<script>uploadSuccess("initiateFile", "'.$lang['Process_Assigned_in_Tray'].'");</script>';
            } else {
                echo '<script>taskFailed("initiateFile", "'.$lang['please_Select_Storage'].'")</script>';
            }
        } else {
            echo '<script>taskFailed("initiateFile", "'.$lang['Please_Select_Workflow_or_Write_Subject'].'")</script>';
        }
    }
    mysqli_close($db_con);
}

function uploadFileInFtpServer($fileserver, $port, $ftpUser, $ftpPwd, $destinationPath, $sourcePath){
    
    require_once './classes/ftp.php';
                
    $ftp = new ftp();
    $ftp->conn("$fileserver", "$port", "$ftpUser", "$ftpPwd");

    $ftp->put(ROOT_FTP_FOLDER.'/'.$destinationPath,$sourcePath); 
    $arr = $ftp->getLogData();
    if ($arr['error'] != ""){

        echo '<h2>Error:</h2>' . implode('<br />', $arr['error']);
    }
}
?> 