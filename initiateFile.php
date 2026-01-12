<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    require_once './application/config/database.php';
    require_once './application/pages/head.php';
    require_once './application/pages/function.php';
    require_once './application/pages/sendSms.php';
	require_once './classes/fileManager.php';

    $sameGroupIDs = array();
    $group = mysqli_query($db_con, "select * from tbl_bridge_grp_to_um where find_in_set('$_SESSION[cdes_user_id]',user_ids)") or die('Error' . mysqli_error($db_con));
    while ($rwGroup = mysqli_fetch_assoc($group)) {
        $sameGroupIDs[] = $rwGroup['user_ids'];
    }
    $sameGroupIDs = array_unique($sameGroupIDs);
    sort($sameGroupIDs);
    $sameGroupIDs = implode(',', $sameGroupIDs);
 

    if ($rwgetRole['initiate_file'] != '1') {
        header('Location: ./index');
    }
    ?>
    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />

    <!--for searchable select-->
    <link href="assets/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" />
    <link href="assets/plugins/jstree/style.css" rel="stylesheet" type="text/css" />
	<style>
		.tox-notifications-container{
			display: none !important;
		}
	</style>
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
                    <div class="container" id="afterSubmt">
                        <!-- Page-Title -->
                        <div class="row">
                            <div class="col-sm-12">
                                <ol class="breadcrumb">
                                    <li>
                                        <a href="initiateFile"><?php echo $lang['Initiate_File']; ?></a>
                                    </li>
                                    <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                                </ol>
                            </div>
                        </div>
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h4 class="header-title"> <?php echo $lang['Initiate_File']; ?></h4>
                            </div>
                            <div class="card-box">
                                <form method="post" enctype="multipart/form-data" id="initiate_form" novalidate>
                                    <?php
                                    if (isset($_GET['doc_Id']) && !empty($_GET['doc_Id']) && !isset($_GET['ticket_id']) && empty($_GET['ticket_id'])) {
                                        ?>
                                        <div class="row step-3">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="col-sm-6">
                                                        <label class="text-weight"><?php echo $lang['do_you_want_select_reviewer']; ?> </label>
                                                        <div class="form-group">
                                                            <div class="radio radio-success radio-inline">
                                                                <input type="radio" name="revchk" value="1" class="review">
                                                                <label for="inlineRadio1"> <?php echo $lang['Yes'] ?> </label>
                                                            </div>
                                                            <div class="radio radio-danger radio-inline">
                                                                <input type="radio" name="revchk" value="0" class="review">
                                                                <label for="inlineRadio1"> <?php echo $lang['No'] ?> </label>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row rview" style="display: none">
                                                <div class="col-sm-12">
                                                    <div class="col-md-2">
                                                        <div class="form-group">

                                                            <label class="control-label"><?= $lang['Receiving_Order']; ?></label>
                                                            <div class="form-group">
                                                                <input type="number"  name="order[]" id="fro" class="form-control" placeholder="<?= $lang['Receiving_Order']; ?>"  min=0 max=15 required >
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">

                                                        <div class="form-group">

                                                            <label class="control-label"><?php echo $lang['Select_reviewer']; ?> </label>
                                                            <div class="input-group">

                                                                <select class="form-control selectpicker" data-live-search="true" name="review[]" id="review">
                                                                    <option value=""><?php echo $lang['Select_reviewer']; ?></option>
                                                                    <?php
                                                                     mysqli_set_charset($db_con, "utf8");
                                                                    $user = mysqli_query($db_con, "select * from tbl_user_master where user_id in($sameGroupIDs) order by first_name, last_name asc");
                                                                    $totalUser = 0;
                                                                    while ($rwUser = mysqli_fetch_assoc($user)) {
                                                                        if ($rwUser['user_id'] != 1 && $_SESSION['cdes_user_id'] != $rwUser['user_id']) {
                                                                            $totalUser++;
                                                                            ?>
                                                                            <option value="<?php echo $rwUser['user_id']; ?>"><?php echo $rwUser['first_name'] . ' ' . $rwUser['last_name']; ?></option>

                                                                            <?php
                                                                        }
                                                                    }
                                                                    ?>
                                                                </select>
                                                                <span class="input-group-addon btn-primary addmore"><i class="fa fa-plus"></i></span>
                                                            </div>

                                                        </div>

                                                    </div>

                                                </div>
                                            </div>
                                            <div class=" col-sm-12">
                                                <label class="text-weight"><?php echo $lang['Select_Storage']; ?></label>
                                                <div class="row">
                                                    <div class="col-md-3 form-group">

                                                        <select class="form-control select2" name="moveToParentId" id="parentMoveLevel" required="">

                                                            <option selected disabled style="background: #808080; color: #121213;"><?php echo $lang['Sel_Strg_Lvl']; ?></option>

                                                            <?php
                                                            $perm = mysqli_query($db_con, "select * from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]' ");
                                                            $rwPerm = mysqli_fetch_assoc($perm);
                                                            $slperm = $rwPerm['sl_id'];

                                                            $storeName = mysqli_query($db_con, "select * from tbl_storage_level where sl_id= '$slperm' and delete_status=0 order by sl_name asc") or die('Error: ' . mysqli_error($db_con));

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
                                            <div class="form-group col-sm-6">
                                                <label style="color: olivedrab" class="text-weight"><?php echo $lang['Ch_fl_op']; ?></label>
                                                <input class="filestyle" id="myImage" multiple="" name="fileName[]" data-buttonname="btn-primary" id="filestyle-4" style="position: absolute; clip: rect(0px, 0px, 0px, 0px);" tabindex="-1" type="file">
                                                <input type="hidden" id="pCount[]" name="pageCount">
                                            </div>
                                            <div class="col-md-6 form-group m-t-30">

                                            </div>
                                            <!--<div style="display: none" id="hidden_div">

                                             <div class="form-group col-sm-12 m-t-20">
                                                 <label><?php echo $lang['Select_Storage']; ?> :-</label>
                                                 <div class="row" >
                                                     <div class="col-md-3 form-group">

                                                         <select class="form-control" name="moveToParentId" id="parentMoveLevel" >

                                                             <option selected disabled style="background: #808080; color: #121213;"><?php echo $lang['Sel_Strg_Lvl']; ?></option>
                                         
                                            <?php
                                            $perm = mysqli_query($db_con, "select * from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]' ");
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
                             </div>-->
                                            <div class="form-group m-t-40 mybtn2 m-r-15">

                                                <a class="btn btn-primary nextBtn pull-right" ><?php echo $lang['Save']; ?></a>
    <!--                                          <a class="btn btn-primary nextBtn pull-right" id="removeNext" ><?php echo $lang['Save']; ?></a>-->
                                            </div>
                                        </div>
                                        <div class="container step-2">

                                        </div>
                                        <?php
                                    } else {
                                        ?>
                                        <div class="container step-1">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label class="text-weight"><?php echo $lang['Subject']; ?> <span style="color:red">*</span></label>
                                                            <input type="text" name="subject" class="form-control specialchaecterlock" placeholder="<?= $lang['enter_subject']; ?>" maxlength="40" value="<?= isset($_POST['subject']) ? $_POST['subject'] : '' ?>" required=""/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-12 m-l-15">
                                                    <label><strong><?php echo $lang['Do_You_Want_To_Generate_File_Number'] ?></strong></label>
                                                    <div class="form-group">
                                                        <div class="radio radio-success radio-inline">
                                                            <input type="radio" name="fnumberw" value="1" class="fnum">
                                                            <label for="inlineRadio1"> <?php echo $lang['Yes'] ?> </label>
                                                        </div>
                                                        <div class="radio radio-danger radio-inline">
                                                            <input type="radio" name="fnumberw" value="0" class="fnum">
                                                            <label for="inlineRadio2"> <?php echo $lang['No'] ?> </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row fnumdiv m-b-10">
                                                <div class="col-sm-12 " id="fnumber">

                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-12" >
                                                    <div class="col-sm-12">
                                                        <label class="text-weight"><?php echo $lang['file_content']; ?> <span style="color:red">*</span> </label>
                                                        <div class="form-group">
                                                            <textarea class="form-control" rows="5" name="taskRemark" id="editor" value="<?= isset($_POST['taskRemark']) ? $_POST['taskRemark'] : '' ?>" required=""></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="docWidth" id="docWidth" value="210">
                                                <input type="hidden" name="docHeight" id="docHeight" value="297">
                                            </div>
                                            <div class="form-group m-t-20 mybtn2">
                                                <a class="btn btn-primary  pull-right"  data-toggle="modal"  data-target="#multi-csv-export-model"  name="docReview" id="dybuttonn" ><?php echo $lang['Save']; ?></a>
                                                    <!--    <a class="btn btn-primary nextBtn pull-right" id="removeNext" ><?php echo $lang['Save']; ?></a>-->
                                            </div>
                                        </div>
                                    <?php } ?>
                            </div>

                            <input type="hidden" name="id" value="" id="ip">
                            <!-- end: page -->
                            </form>
                        </div> <!-- end Panel -->
                    </div> <!-- container -->
                </div> <!-- content -->
            </div>
        </div>
    </div>
    <div id="multi-csv-export-model" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="panel panel-color panel-primary">
                <div class="panel-heading">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <label><h2 class="panel-title"><?= $lang['Select_Page_Format']; ?></h2></label>
                </div>
                <div class="panel-body">
                    <label><?= $lang['Select_Page_Format']; ?></label>
                    <select class="multi-select form-control select2" id="docformat" name="select_Fm" required>
                        <option value=""><?= $lang['Select_Page_Format']; ?></option>
                        <option value="A0">A0</option>
                        <option value="A1">A1</option>
                        <option value="A2">A2</option>
                        <option value="A3">A3</option>
                        <option value="A4">A4</option>
                        <option value="A5">A5</option>
                        <option value="A6">A6</option>
                        <option value="A7">A7</option>
                        <option value="A8">A8</option>
                        <option value="A9">A9</option>
                        <option value="A10">A10</option>
                        <option value="Letter">Letter</option>
                        <option value="Legal">Legal</option>
                        <option value="Envelop">Envelope</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?= $lang['Close']; ?></button>
                    <button class="btn btn-primary waves-effect waves-light" type="submit" id="reviewDoc" name="reviewDoc"><?= $lang['Submit']; ?></button>
                </div>
            </div>
        </div>
    </div>
    <!--display wait gif image after submit-->
    <div style=" display: none; background: rgba(0,0,0,0.5); width: 100%; z-index: 2000; position: fixed; top:0;" id="wait">;
        <img src="assets/images/proceed.gif" alt="load"  style=" margin-left: 48%; margin-top: 250px; width: 100px; height:100px; position: fixed;"/>
    </div>
    <?php require_once './application/pages/footer.php'; ?>
    <!-- Right Sidebar -->
    <?php require_once './application/pages/rightSidebar.php'; ?>
    <?php require_once './application/pages/footerForjs.php'; ?>
    <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
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
        /*
         *
         * @submit form usinf modal button popup
         */
        $("#reviewDoc").click(function () {
            document.getElementById("initiate_form").submit();
        })

        /*
         *
         * @Change page format
         */
        $("#docformat").change(function () {
            var valu = $(this).val();
            if (valu == "A3")
            {
                $("#docWidth").val("297");//width of doc
                $("#docHeight").val("420");//Height of doc
            } else if (valu == "A4")
            {
                $("#docWidth").val("210");//width of doc
                $("#docHeight").val("297");//Height of doc
            } else if (valu == "A5")
            {
                $("#docWidth").val("148");//width of doc
                $("#docHeight").val("210");//Height of doc
            } else if (valu == "Letter")
            {
                $("#docWidth").val("215.9");//width of doc
                $("#docHeight").val("279.4");//Height of doc
            } else if (valu == "Legal")
            {
                $("#docWidth").val("216");//width of doc
                $("#docHeight").val("356");//Height of doc
            } else if (valu == "Envelop")
            {
                $("#docWidth").val("114");//width of doc
                $("#docHeight").val("162");//Height of doc
            } else if (valu == "A0")
            {
                $("#docWidth").val("841");//width of doc
                $("#docHeight").val("1189");//Height of doc
            } else if (valu == "A1")
            {
                $("#docWidth").val("594");//width of doc
                $("#docHeight").val("841");//Height of doc
            } else if (valu == "A2")
            {
                $("#docWidth").val("420");//width of doc
                $("#docHeight").val("594");//Height of doc
            } else if (valu == "A6")
            {
                $("#docWidth").val("105");//width of doc
                $("#docHeight").val("148");//Height of doc
            } else if (valu == "A7")
            {
                $("#docWidth").val("74");//width of doc
                $("#docHeight").val("105");//Height of doc
            } else if (valu == "A8")
            {
                $("#docWidth").val("52");//width of doc
                $("#docHeight").val("74");//Height of doc
            } else if (valu == "A9")
            {
                $("#docWidth").val("37");//width of doc
                $("#docHeight").val("52");//Height of doc
            } else if (valu == "A10")
            {
                $("#docWidth").val("26");//width of doc
                $("#docHeight").val("37");//Height of doc
            }
        })
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
            // alert(lbl);
            $.post("application/ajax/uploadWorkFlow.php", {parentId: lbl, levelDepth: 0, sl_id:<?php echo $slid; ?>}, function (result, status) {
                if (status == 'success') {
                    $("#child").html(result);
                    // alert(result);
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
        $(document).ready(function () {
            $(".fnumdiv").hide();
            $(".fnum").change(function () {
                var valu = $(this).val();
                /*
                 * If file Number Required
                 */
                if (valu == 1)
                {
                    $(".fnumdiv").show();
                    $.post("application/ajax/doc_number.php", {}, function (result, status) {
                        if (status == 'success')
                        {
                            $("#fnumber").append(result)
                        }
                    });
                }
                /*
                 * If file Number Not Required
                 */
                if (valu == 0)
                {
                    $(".fnumdiv").hide();
                    $(".dymicadd").remove();
                }
            })


        })
        /*
         * If Receiver select
         */
        $(".review").change(function () {
            var valu = $(this).val();
            if (valu == "" || valu == 0)
            {
                //alert("run");
                $("#dybuttonn").remove();
                $(".rview").hide();
                $(".nextBtn").show();


//                    $.post("application/ajax/initiateFile.php", {}, function (result, status) {
//                        if (status == 'success')
//                        {
//                            $(".step-2").append(result)
//                        }
//                    });
            } else
            {
                $(".nextBtn").css("display", "none");
                $(".rview").show();
                $(".mybtn2").append(' <button class="btn btn-primary  pull-right dbtn"    name="docReview" id="dybuttonn" ><?php echo $lang['Save']; ?></button>');
                $(".step-2").empty();
                //data-toggle="modal"  data-target="#multi-csv-export-model"
            }
        })
        /*
         *
         */

        $(".nextBtn").click(function (e) {
            console.log('run');
            $(".step-3").hide();
            $.post("application/ajax/initiateFile.php", {}, function (result, status) {
                if (status == 'success')
                {
                    $(".step-2").append(result)
                }

            })
        })
        var total = 2;
        $(document).ready(function () {
            $("#fro").val(1);
            var avilableTotal = <?= !empty($totalUser) ? $totalUser : 0 ?>;


            $(".addmore").click(function (e) {
                if (total <= avilableTotal)
                {

                    // console.log(total);
                    var htmlString = '<div class="col-sm-12 " id="remove' + total + '"><div class="col-md-2"> <div class="form-group"><label class="control-label"><?= $lang['Receiving_Order']; ?></label><div class="form-group"> <input type="number"  name="order[]" value="' + total + '" class="form-control" placeholder="<?= $lang['Receiving_Order']; ?>"  min=0 max=15 required readonly novalidate></div></div></div><div class="col-sm-6"><div class="form-group">\n\
            <label class="control-label"><?php echo $lang['Select_reviewer']; ?> </label> <div class="input-group">\n\
            ';
                    var htmString2 = '<span class="input-group-addon btn-primary" id="rmve" onclick="rmv(this.id,' + total + ')"><i class="fa fa-minus" id="rmve"></i></span>\n\
                            </div>   </div> </div></div>';

                    $.post("application/ajax/selectReceiver.php", {}, function (result, status) {
                        if (status == 'success')
                        {
                            $(".rview").append(htmlString + result + htmString2);
                            total++;
                        }

                    })
                } else
                {
                    alert("No More Reviewer Available");
                }
            });

        })
        function rmv(id, totall)
        {
            $("#remove" + totall).remove();
            total--;
        }
        $(".select2").select2();
        //image detail
//            $('#myImage').bind('change', function () {
//                //this.files[0].size gets the size of your file.
//                if (this.files[0].type == 'application/pdf') {
//                    var reader = new FileReader();
//                    reader.readAsBinaryString(this.files[0]);
//                    reader.onloadend = function () {
//                        var count = reader.result.match(/\/Type[\s]*\/Page[^s]/g).length;
//                        $("#pageCount").html(count);
//                        $("#pCount").val(count);
//                        // console.log('Number of Pages:',count );
//                    }
//                } else {
//                    $("#pageCount").html('1');
//                    $("#pCount").val('1');
//                }
//
//            });

    </script>
    <script src="assets/plugins/tinymce/tinymce.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            if ($("#editor").length > 0) {
                tinymce.init({
                    selector: "textarea#editor",
                    //theme: "modern",
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
		
		
		$(".tox-notification__dismiss").on('click',function(){
			alert();
			
		});
		
        $("#myImage").change(function () {
            var size = document.getElementById("myImage").files[0].size;
            // alert(size);
            var name = document.getElementById("myImage").files[0].name;
            //alert(lbl);
            if (name.length < 100)
            {
                //alert(lbl);
                $.post("application/ajax/valiadate_client_memory.php", {size: size}, function (result, status) {
                    if (status == 'success') {
                        //$("#stp").html(result);
                        var res = JSON.parse(result);
                        if (res.status == "true")
                        {
                            // $("#memoryres").html("<span style=color:green>" + res.msg + "</span>");
                            $.Notification.autoHideNotify('success', 'top center', 'Success', res.msg)
                            //$("#dataprev").attr('data-target');
                            $(".nextBtn").removeAttr('disabled', 'disabled');
                            $("#hidden_div").show();
                            $(".dbtn").removeAttr('disabled', 'disabled');
                        } else {
                            $.Notification.autoHideNotify('warning', 'top center', 'Oops', res.msg)
                            $(".nextBtn").attr('disabled', 'disabled');
                            $(".dbtn").attr('disabled', 'disabled');

                            // $("#dataprev").removeAttr('data-target');
                            $("#hidden_div").hide();
                            //$("#memoryres").html("<span style=color:red>" + res.msg + "</span>");
                        }

                    }
                });
            } else {
                var input = $("#myImage");
                var fileName = input.val();

                if (fileName) { // returns true if the string is not empty
                    input.val('');
                }
                $("#hidden_div").hide();
                $("#waitOnSubmit").attr('disabled', 'disabled');
                $.Notification.autoHideNotify('error', 'top center', 'Error', "File Name Too Long");
            }

        });
    </script>
</body>
</html>

<?php
if (isset($_POST['subject'], $_POST['token'])) {
    $assignBy = $_SESSION['cdes_user_id'];
    $subjectDoc = mysqli_escape_string($db_con, filter_var($_POST['subject'], FILTER_SANITIZE_STRING));
    $fileNumber = isset($_POST['fnumberw']) == 1 ? $_POST['fnumber'] . $_POST['autoFNum'] : '';
    $docDescription = $_POST['taskRemark'];
    $docHeight = mysqli_escape_string($db_con, filter_var($_POST['docHeight'], FILTER_SANITIZE_STRING));
    $docWidth = mysqli_escape_string($db_con, filter_var($_POST['docWidth'], FILTER_SANITIZE_STRING));
	
    
     $files = $_FILES['fileName']['name'];
    if (!empty($files)) {

        for ($k = 0; $k < count($files); $k++) {

            $file_name = $_FILES['fileName']['name'][$k];

            $allowed = ALLOWED_EXTN;
            $allowext = implode(", ", $allowed);
            $ext = pathinfo($file_name, PATHINFO_EXTENSION);
            if (!in_array(strtolower($ext), $allowed)) {

                echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '", "' . str_replace("ext", $allowext, $lang['document_allowed']) . '")</script>';
                exit();
            }
        }
    }
    
    $res = docReview($assignBy, $subjectDoc, $fileNumber, $docDescription, $db_con, $date, $docHeight, $docWidth, $lang);
    if ($res['status']) {
        $docId = base64_encode(urldecode($res['doc_id']));
        header("Location:initiateFile?doc_Id=$docId");
    } else {
        echo '<script>taskFailed("' . basename($_SERVER[REQUEST_URI]) . '", "' . $res['msg'] . '")</script>';
    }
}
/*
 * Insert Into Doc Review Table becoz
 */

function docReview($assignBy, $subjectDoc, $fileNumber, $docDescription, $db_con, $date, $docHeight, $docWidth, $lang) {


    if (!empty($assignBy)) {
        if (!empty($subjectDoc)) {
            $checkWrkFlwName = mysqli_query($db_con, "select workflow_name from tbl_workflow_master where FIND_IN_SET('$subjectDoc', workflow_name)"); //or die('Error: ' . mysqli_error($db_con));
            if (mysqli_num_rows($checkWrkFlwName) == 1) {
                //check duplicate name of workflow
                return array("status" => FALSE, "msg" => "Workflow of this Subject Already Exist !", "dev_msg" => "Workflow name is already exist");
            } else {
                if (!empty($docDescription)) {
                    $dochtml = trim($docDescription);
//                    $docDescription=htmlentities($docDescription,$docDescription);
//                    $docDescription = mysqli_escape_string($db_con, $docDescription);

                    $sub = str_replace(" ", "", $subjectDoc);
                    $path = 'extract-here/ReviewerDoc';
                    if (!is_dir($path)) {
                        mkdir($path, 0777, true);
                    }
                    $fname = $sub . "_" . strtotime($date);
                    $filterename = preg_replace('/[^A-Za-z0-9]/', '', $sub);
                    $fileEncName = strtotime($date) . urlencode(base64_encode($filterename));
                    $fileEncName = preg_replace('/[^A-Za-z0-9]/', '', $fileEncName);
                    $path = $path . "/" . $fileEncName . ".html";
                    $docdescLayout = json_encode(array("subject" => $subjectDoc, "height" => $docHeight, "width" => $docWidth));
                    $myfile = fopen($path, "w");
                    fwrite($myfile, '<p>MNAasjhfjkdshfjk</p>');
					fclose($myfile);
                    $Fsize = filesize($path);
                    $Fsize = round(($Fsize / 1000), 2);
                    chmod($path, 0777);
                    $doc_name = 113;
                    $pagecount = 1;
                    $path = "ReviewerDoc/$fileEncName" . ".html";
                    $Doc = mysqli_query($db_con, "INSERT INTO tbl_document_reviewer (doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages, dateposted,doc_desc,File_Number) VALUES ('$doc_name', '$subjectDoc', 'docx', '$path', '$assignBy', '$Fsize', '$pagecount', '$date','$docdescLayout','$fileNumber')"); //or die(mysqli_error($db_con));
                    $docId = mysqli_insert_id($db_con);
                    if ($Doc) {


                        return array("status" => True, "msg" => "Export Success", "doc_id" => $docId);
                    } else {
                        return array("status" => False, "msg" => "Export Failed !!");
                    }
                } else {
                    return array("status" => False, "msg" => "Description of Document Required");
                }
            }
        } else {
            return array("status" => False, "msg" => $lang['Subject_Document_req']);
        }
    } else {
        return array("status" => False, "msg" => $lang['Invalid_User']);
    }
}

/*
 * Doc Reviewer insert assaign to
 */

if (isset($_POST['docReview'])) {

    //mysqli_autocommit($db_con, FALSE);
    $doc_id = base64_decode(urldecode($_GET['doc_Id']));
    $doc_id = intval($doc_id);
    $assignTo = $_POST['review'];
    $user_id = $_SESSION['cdes_user_id'];
    $docQry = mysqli_query($db_con, "select * from `tbl_document_reviewer` where doc_id='$doc_id'");
    $docInfoFetch = mysqli_fetch_assoc($docQry);
    $dname = $docInfoFetch['old_doc_name'];
    $docDesp = json_decode($docInfoFetch['doc_desc'], TRUE);
    $subject = $docDesp['subject'];
    //$taskRemark = $docDesp['docdesp'];
    $height = $docDesp['height'];
    $width = $docDesp['width'];
    $ip = $_POST['id'];
    $workFlowArray = explode(" ", $subject);
    $ticket = '';
    $oredr = $_POST['order'];
    for ($w = 0; $w < count($workFlowArray); $w++) {
        $name = $workFlowArray[$w];
        $ticket = $ticket . substr($name, 0, 1);
    }
    $ticketReviewer = $ticket . '_' . $user_id . '_' . strtotime($date);
    if (isset($oredr) && isset($assignTo) && !empty($oredr[0]) && !empty($assignTo[0])) {
        if (count(array_unique($oredr)) < count($oredr)) {
            echo '<script>taskFailed("' . basename($_SERVER[REQUEST_URI]) . '", "Order No. Can\'t be Same! ")</script>';
        } else {
            if (empty($_POST['lastMoveId']) && !empty($_FILES['fileName']['name'][0])) {
                echo '<script>taskFailed("' . basename($_SERVER[REQUEST_URI]) . '", "Please Select Storage First");</script>';
            } else {
                $docSrcPath = 'extract-here/';    //SET STATIC ROOT FOLDER FOR SOURCE
                $ftpfile = $docSrcPath . $docInfoFetch['doc_path'];
                /*
                 * Validate file exist on doc review or not
                 */
                if (file_exists($ftpfile)) {
                    for ($i = 0; $i < count($assignTo); $i++) {
                        $order = $oredr[$i];

                        if (min($oredr) == $oredr[$i]) {

                            $insertDocAssign = mysqli_query($db_con, "Insert into `tbl_doc_review` (`doc_id`,`start_date`,`task_status`,`assign_by`,`action_by`,`next_task`,`ticket_id`,`review_order`)values('$doc_id','$date','Pending','$user_id','$assignTo[$i]','0','$ticketReviewer','$order')"); //or die(mysqli_error($db_con)); //Insert Doc review in doc reviw table
                            $firstReview = mysqli_insert_id($db_con);
                        } else {
                            $insertDocAssign = mysqli_query($db_con, "Insert into `tbl_doc_review` (`doc_id`,`start_date`,`task_status`,`assign_by`,`action_by`,`next_task`,`ticket_id`,`review_order`)values('$doc_id','$date','Pending','$user_id','$assignTo[$i]','2','$ticketReviewer','$order')"); //or die(mysqli_error($db_con));
                            ; //Insert Doc review in doc reviw table
                        }
                    }
                    if ($insertDocAssign) {
//                        if (isset($_POST['lastMoveId']) && !empty($_POST['lastMoveId'])) {
                        if (isset($_FILES['fileName']['name']) && !empty($_FILES['fileName']['name'])) {
                            $sl_id = $_POST['lastMoveId'];
                            $qryRun = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id='$sl_id'");
                            $folder_name = mysqli_fetch_assoc($qryRun);
                            $folder = $folder_name['sl_name'];
                            $image_path = $docSrcPath . $folder;
                            if (!is_dir($image_path)) {
                                mkdir($image_path, 0777, true);
                            }
                            $image_path = $image_path . '/';
                            $dname = time() . urlencode(base64_encode(preg_replace('/[^A-Za-z0-9]/', '', $dname)));
                            $dname = preg_replace('/[^A-Za-z0-9]/', '', $dname);
                            $dname = $dname . ".html";
                            /*
                             * Copy file from reviewdoc to selcted storage
                             */
                            if (copy($ftpfile, $image_path . $dname)) {
                                $destinationPath = $folder . '/' . $dname;
                                $sourcePath = $image_path . $dname;
                                if (uploadFileInFtpServer($destinationPath, $sourcePath)) {
                                    $updateDocName = mysqli_query($db_con, "update tbl_document_reviewer set doc_name='$sl_id',doc_path='$folder/$dname' where doc_id='$doc_id'"); //or die(mysqli_error($db_con));
                                    if ($updateDocName) {
                                        $files = $_FILES['fileName']['name'];
                                        for ($i = 0; $i < count($files); $i++) {
                                            $file_name = $_FILES['fileName']['name'][$i];
                                            $file_size = $_FILES['fileName']['size'][$i];
                                            $file_type = $_FILES['fileName']['type'][$i];
                                            $file_tmp = $_FILES['fileName']['tmp_name'][$i];
                                            if (!empty($file_name)) {
                                                $fileExtn = substr($file_name, strrpos($file_name, ".") + 1);
                                                $fname = substr($file_name, 0, strrpos($file_name, '.'));
                                                $encryptName = time() . urlencode(base64_encode(preg_replace('/[^A-Za-z0-9]/', '', $fname)));
                                                $encryptName = preg_replace('/[^A-Za-z0-9]/', '', $encryptName);
                                                $fileExtn = substr($file_name, strrpos($file_name, '.') + 1);
                                                // $file_name = time() . '_' . $file_name;
                                                $image_path1 = $image_path . $encryptName . "." . $fileExtn;
                                                $upload = move_uploaded_file($file_tmp, $image_path1) or die(print_r(error_get_last()));

                                                if ($upload) {
                                                    $destinationPath1 = $folder . '/' . $encryptName . "." . $fileExtn;
                                                    $sourcePath1 = $image_path1;
                                                    uploadFileInFtpServer($destinationPath1, $sourcePath1);
                                                    $pageCount = count_pages($image_path1);

                                                    if (empty($docId)) {
                                                        $id = $sl_id . '_' . $doc_id;
                                                    }

                                                    $query = "INSERT INTO tbl_document_reviewer(doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages , dateposted) VALUES ('$id', '$fname', '$fileExtn', '$destinationPath1', '$user_id', '$file_size', '$pageCount', '$date')";
                                                    $exe = mysqli_query($db_con, $query); // or die('Error query failed' . mysqli_error($db_con));

                                                   $docId = mysqli_insert_id($db_con);

                                                    // Decrypt file
                                                    decrypt_my_file($image_path1);

                                                    $newdocname = base64_encode($docId);

                                                    //create thumbnail
                                                    $uploadedfilename = $image_path1;

                                                    if($fileExtn=='jpg' || $fileExtn=='jpeg' || $fileExtn=='png'){
                                                        createThumbnail2($uploadedfilename,$newdocname, true);
                                                    }elseif($fileExtn=='pdf'){
                                                        changePdfToImage($uploadedfilename,$newdocname, true);
                                                    }
                                                }
                                            }
                                        }
                                    } else {
                                        echo '<script>taskFailed("' . basename($_SERVER[REQUEST_URI]) . '", "Failed To Insert");</script>';
                                    }
                                } else {
                                    echo '<script>taskFailed("' . basename($_SERVER[REQUEST_URI]) . '", "Failed To Move File On FTP");</script>';
                                }
                            } else {
                                echo '<script>taskFailed("' . basename($_SERVER[REQUEST_URI]) . '", "' . $lang['file_not_copy'] . '");</script>';
                            }
                        } else {

                            $destinationPath = $docInfoFetch['doc_path'];
                            $updateDocName = mysqli_query($db_con, "update tbl_document_reviewer set doc_name='$_POST[lastMoveId]' where doc_id='$doc_id'"); //or die(mysqli_error($db_con));
                            uploadFileInFtpServer($destinationPath, $ftpfile);
                        }

                        $qry = mysqli_query($db_con, "INSERT INTO `tbl_reviews_log`(`user_id`,`doc_id`,`action_name`,`start_date`,`end_date`,`system_ip`,`remarks`)values('$user_id','$doc_id','Document Created For Review','$date','$date','$host/$ip','')");

                        require './mail.php';
                        

                        if(!MAIL_BY_SOCKET){

                            $paramsArray = array(
                                'ticket' => $ticketReviewer,
                                'idins' => $firstReview,
                                'db_con' => $db_con,
                                'projectName' => $projectName,
                                'subject' => $subject,
                                'action' => 'assignReview'
                            );
                            
                            mailBySocket($paramsArray);
                            
                        }else{

                            $mail = assignReview($ticketReviewer, $firstReview, $db_con, $projectName, $subject);
                        }


                        //if ($mail) {
                            //mysqli_commit($db_con);
                            echo '<script>taskSuccess("initiateFile", "' . $lang['Assigned_to_reviewer_success'] . '");</script>';
                        // } else {
                        //     echo '<script>taskFailed("' . basename($_SERVER[REQUEST_URI]) . '", "' . $lang['Ops_Ml_nt_snt'] . '")</script>';
                        // }
                    } else {
                        echo '<script>taskFailed("' . basename($_SERVER[REQUEST_URI]) . '", "' . $lang['Failed_To_Assign_Reciever'] . '")</script>';
                    }
                } else {
                    echo '<script>taskFailed("' . basename($_SERVER[REQUEST_URI]) . '", "' . $lang['File_Not_fnd'] . '");</script>';
                }
            }
        }
    } else {
        echo '<script>taskFailed("' . basename($_SERVER[REQUEST_URI]) . '", "' . $lang['Order_Assign'] . '");</script>';
    }
}

function count_pages($pdfname) {

    $pdftext = file_get_contents($pdfname);

    $num = preg_match_all("/\/Page\W/", $pdftext, $dummy);

    return $num;
}

/*
 * Existing Workflow Select
 */
if (isset($_POST['iniFileSub'])) {

    $user_id = $_SESSION['cdes_user_id'];
    $workflId = xss_clean($_POST['wfid']);
    $doc_id = base64_decode(urldecode($_GET['doc_Id']));
    $docQry = mysqli_query($db_con, "select * from `tbl_document_reviewer` where doc_id='$doc_id'");
    $docInfoFetch = mysqli_fetch_assoc($docQry);
    $dname = $docInfoFetch['old_doc_name'];
    $fileNumber = $docInfoFetch['File_Number'];
    $docDesp = json_decode($docInfoFetch['doc_desc'], TRUE);
    $subject = $docDesp['subject'];
    $docReviewFilePath = "extract-here/" . $docInfoFetch['doc_path'];
    
   $taskRemark = file_get_contents($docReviewFilePath);

    $height = $docDesp['height'];
    $width = $docDesp['width'];
    $ip = xss_clean($_POST['id']);
    $lastMoveId = xss_clean($_POST['lastMoveId']);
    /*
     * Message
     * IF WORKFLOW ALREADY EXIST
     */
    if (!empty($workflId) && $workflId != '0') {
        /*
         * Message
         * StoreFile In Storage
         */

        if (isset($_FILES['fileName']['name'][0]) && !empty($_FILES['fileName']['name'][0])) {

            if (!empty($_FILES['fileName']['name'][0])) {

                $wrkFlwName = mysqli_query($db_con, "select * from tbl_workflow_master where workflow_id = '$workflId'"); // or die('Error1' . mysqli_error($db_con));
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

                include 'exportpdf.php';
                $ticket = $ticket . '_' . $user_id . '_' . strtotime($date);
                $posted_editor = $taskRemark; //get content of CKEditor
                $slperm = mysqli_query($db_con, "select * from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]'");
                $rwSlperm = mysqli_fetch_assoc($slperm);
                //$sl_id = $rwSlperm['sl_id'];
                $sl_id = $lastMoveId;
                $docName = mysqli_query($db_con, "select sl_id,sl_name from tbl_storage_level where sl_id = '$sl_id'"); // or die('Eror2:' . mysqli_error($db_con));
                $rwdocName = mysqli_fetch_assoc($docName);
                $realName = $subject;
                $folderName = str_replace(" ", "", $workflowName);
                $pdfName = trim(str_replace(" ", "", $workflowName)); //specify the file save location and the file name
                $nfilename = preg_replace('/[^A-Za-z0-9_\-]/', '', $pdfName);
                $filenameEnct = urlencode(base64_encode($nfilename));
                $filenameEnct = preg_replace('/[^A-Za-z0-9_\-]/', '', $filenameEnct);
                $filenameEnct = $filenameEnct . '.' . "pdf";
                $filenameEnct = time() . $filenameEnct;
                $path = 'extract-here/' . str_replace(" ", "", $workflowName);
                if (!is_dir($path)) {
                    mkdir($path, 0777, true);
                }
                $path = $path . '/' . $filenameEnct;
                exportPDFSize($posted_editor, $path, $height, $width); //EXPORT FILE IN PDF FORMAT
                $wrkflowFsize = filesize($path); //COUNT THE SIZE OF FILE

                $wrkflowFsize = round(($wrkflowFsize / 1000), 2);
                $doc_name = $sl_id . '_' . $workflId;
                $pagecount = count_pages($path);
                $wrkflowDoc = mysqli_query($db_con, "INSERT INTO tbl_document_master (doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages, dateposted,File_Number) VALUES ('$doc_name', '$realName', 'pdf', '$folderName/$filenameEnct', '$user_id', '$wrkflowFsize', '$pagecount', '$date','$fileNumber')"); // or die('Eror3:' . mysqli_error($db_con));
                $docId = mysqli_insert_id($db_con);

                $newdocname = base64_encode($docId);

                //create thumbnail
                $uploadedfilename = $path;

               
                changePdfToImage($uploadedfilename,$newdocname, true);

                $id = $sl_id . '_' . $docId . '_' . $workflId;
                $destinationPath = str_replace(" ", "", $workflowName) . '/' . $filenameEnct;
                $sourcePath = $path;
                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`,`doc_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,$sl_id,$docId,'Document Upload in $workflowName workflow','$date','$date','$host',null)"); // or die('error23 : ' . mysqli_error($db_con));
                uploadFileInFtpServer($destinationPath, $sourcePath);

                //upload files if any
                $files = $_FILES['fileName']['name'];

                if (!empty($lastMoveId)) {
                    for ($i = 0; $i < count($files); $i++) {
                        $file_name = $_FILES['fileName']['name'][$i];
                        $file_size = $_FILES['fileName']['size'][$i];
                        $file_type = $_FILES['fileName']['type'][$i];
                        $file_tmp = $_FILES['fileName']['tmp_name'][$i];
                        if (!empty($file_name)) {
                            $pageCount = $_POST['pageCount'][$i];

                            //$name = explode(".", $file_name);
                            //$encryptName = urlencode(base64_encode($name[0]));
                            //$fileExtn = $name[1];
                            $fileExtn = substr($file_name, strrpos($file_name, ".") + 1);
                            $folder = str_replace(" ", "", $workflowName);
                            $fname = substr($file_name, 0, strrpos($file_name, '.'));
                            $nfilename = preg_replace('/[^A-Za-z0-9_\-]/', '', $fname);
                            $filenameEnct = urlencode(base64_encode($nfilename));
                            $filenameEnct = preg_replace('/[^A-Za-z0-9_\-]/', '', $filenameEnct);
                            $filenameEnct = $filenameEnct . '.' . $fileExtn;
                            $filenameEnct = time() . $filenameEnct;
//                            $fileExtn = substr($file_name, strrpos($file_name, '.') + 1);
//                            $file_name = time() . '_' . $file_name;

                            $image_path = 'extract-here/' . $folder . '/';

                            if (!dir($image_path)) {
                                mkdir($image_path, 0777, TRUE); // or die("Error local folder:". print_r(error_get_last()));
                            }
                            $image_path = $image_path . $filenameEnct;

                            $upload = move_uploaded_file($file_tmp, $image_path) or die(print_r(error_get_last()));

                            if ($upload) {

                                $destinationPath = $folder . '/' . $filenameEnct;
                                $sourcePath = $image_path;
                                $pageCount = count_pages($image_path);
                                uploadFileInFtpServer($destinationPath, $sourcePath);


                                $query = "INSERT INTO tbl_document_master(doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages , dateposted) VALUES ('$id', '$fname', '$fileExtn', '$folder/$filenameEnct', '$user_id', '$file_size', '$pageCount', '$date')";
                                $exe = mysqli_query($db_con, $query); // or die('Error query failed' . mysqli_error($db_con));

                                $docId2 = mysqli_insert_id($db_con);

                                // Decrypt file
                                decrypt_my_file($image_path);

                                $newdocname = base64_encode($docId2);

                                //create thumbnail
                                $uploadedfilename = $image_path;

                                if($fileExtn=='jpg' || $fileExtn=='jpeg' || $fileExtn=='png'){
                                    createThumbnail2($uploadedfilename,$newdocname, true);
                                }elseif($fileExtn=='pdf'){
                                    changePdfToImage($uploadedfilename,$newdocname, true);
                                }

                                if (empty($docId)) {
                                    $docId = $docId2;
                                    $id = $sl_id . '_' . $docId . '_' . $wfid;
                                    //$vid=$sl_id . '_' . $docId ;
                                }

                                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`,`doc_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,$sl_id,'$docId','Document Upload in $workflowName workflow','$date','$date','$host',null)"); // or die('error23 : ' . mysqli_error($db_con));
                            }
                        }
                    }

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
                    $filterTaskRemark = mysqli_escape_string($db_con, $taskRemark);
                    $insertInTask = mysqli_query($db_con, "INSERT INTO tbl_doc_assigned_wf(task_id, doc_id, start_date, end_date, task_remarks, task_status, assign_by, ticket_id) VALUES ('$wTaskId', '$docId', '$date', '$endDate', '$filterTaskRemark', 'Pending', '$user_id', '$ticket')"); // or die('Erorr:' . mysqli_error($db_con));

                    $idins = mysqli_insert_id($db_con);

                    $getTask = mysqli_query($db_con, "select * from tbl_task_master where task_id = '$wTaskId'") or die('Error:' . mysqli_error($db_con));
                    $rwgetTask = mysqli_fetch_assoc($getTask);
                    $TskStpId = $rwgetTask['step_id'];
                    $TskWfId = $rwgetTask['workflow_id'];
                    $TskOrd = $rwgetTask['task_order'];
                    $TskAsinToId = $rwgetTask['assign_user'];
                    $nextTaskOrd = $TskOrd + 1;

                    nextTaskAsin($nextTaskOrd, $TskWfId, $TskStpId, $docId, $date, $user_id, $db_con, $filterTaskRemark, $ticket);
                    // unlink("extract-here/ReviewerDoc/$dname");
                    $qryDelDoc = mysqli_query($db_con, "delete from tbl_document_reviewer where doc_id='$doc_id'");
                    require_once './mail.php';

                    if(MAIL_BY_SOCKET){

                        $paramsArray = array(
                            'ticket' => $ticket,
                            'idins' => $idnxt,
                            'db_con' => $db_con,
                            'projectName' => $projectName,
                            'action' => 'assignTask'
                        );

                        mailBySocket($paramsArray);
                        
                    }else{

                        $mail = assignTask($ticket, $idins, $db_con, $projectName);
                    }


                    //if ($mail) {

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



                        echo '<script>taskSuccess("initiateFile", "' . $lang['Sumitd_Sucsfly'] . '");</script>';
                    // } else {

                    //     echo '<script>taskFailed("' . basename($_SERVER[REQUEST_URI]) . '", "' . $lang['Ops_Ml_nt_snt'] . '")</script>';
                    // }

                    echo '<script>uploadSuccess("initiateFile", "' . $lang['Process_Assigned_in_Tray'] . '");</script>';
                } else {
                    echo '<script>taskFailed("' . basename($_SERVER[REQUEST_URI]) . '", "' . $lang['please_Select_Storage'] . '")</script>';
                }
            } else {
                echo '<script>taskFailed("' . basename($_SERVER[REQUEST_URI]) . '", "' . $lang['plz_Select_file_first'] . '")</script>';
            }
            mysqli_close($db_con);
        } else {
            /* Message
             * This code Run when user not upload file but workflow already exist
             */
            $wrkFlwName = mysqli_query($db_con, "select * from tbl_workflow_master where workflow_id = '$workflId'"); // or die('Error1' . mysqli_error($db_con));
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

            include 'exportpdf.php';
            $ticket = $ticket . '_' . $user_id . '_' . strtotime($date);
            $posted_editor = $taskRemark; //get content of CKEditor
            $slperm = mysqli_query($db_con, "select * from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]'");
            $rwSlperm = mysqli_fetch_assoc($slperm);
            $sl_id = $rwSlperm['sl_id'];
            $docName = mysqli_query($db_con, "select sl_id,sl_name from tbl_storage_level where sl_id = '$sl_id'"); // or die('Eror2:' . mysqli_error($db_con));
            $rwdocName = mysqli_fetch_assoc($docName);
            $folderName = str_replace(" ", "", $workflowName);
            //$pdfName = trim(str_replace(" ", "", $workflowName)) . "_" . mktime() . ".pdf"; //specify the file save location and the file name
            $realName = $subject;
            $pdfName = trim(str_replace(" ", "", $subject)) . "_" . mktime(); //specify the file save location and the file name
            $nfilename = preg_replace('/[^A-Za-z0-9_\-]/', '', $pdfName);
            $filenameEnct = urlencode(base64_encode($nfilename));
            $filenameEnct = preg_replace('/[^A-Za-z0-9_\-]/', '', $filenameEnct);
            $filenameEnct = $filenameEnct . '.' . "pdf";
            $filenameEnct = time() . $filenameEnct;
            $path = 'extract-here/' . str_replace(" ", "", $workflowName);
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }
            $path = $path . '/' . $filenameEnct;
            exportPDFSize($posted_editor, $path, $height, $width);
            $wrkflowFsize = filesize($path);
            $wrkflowFsize = round(($wrkflowFsize / 1000), 2);
            $doc_name = $lastMoveId . '_' . $workflId;
            $pagecount = count_pages($path);
            $wrkflowDoc = mysqli_query($db_con, "INSERT INTO tbl_document_master (doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages, dateposted,File_Number) VALUES ('$doc_name', '$realName', 'pdf', '$folderName/$filenameEnct', '$user_id', '$wrkflowFsize', '$pagecount', '$date','$fileNumber')"); // or die('Eror3:' . mysqli_error($db_con));
            $docId = mysqli_insert_id($db_con);

            $newdocname = base64_encode($docId);

            //create thumbnail
            $uploadedfilename = $path;

           
            changePdfToImage($uploadedfilename,$newdocname, true);

            $id = $lastMoveId . '_' . $docId . '_' . $workflId;
            $destinationPath = str_replace(" ", "", $workflowName) . '/' . $filenameEnct;
            $sourcePath = $path;
            uploadFileInFtpServer($destinationPath, $sourcePath);
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`,`doc_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,$sl_id,$docId,'Document Upload in $workflowName workflow','$date','$date','$host',null)") or die('error23 : ' . mysqli_error($db_con));
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
            $filterTaskRemark = mysqli_escape_string($db_con, $taskRemark);
            $insertInTask = mysqli_query($db_con, "INSERT INTO tbl_doc_assigned_wf(task_id, doc_id, start_date, end_date, task_remarks, task_status, assign_by, ticket_id) VALUES ('$wTaskId', '$docId', '$date', '$endDate', '$filterTaskRemark', 'Pending', '$user_id', '$ticket')") or die('Erorr:' . mysqli_error($db_con));

            $idins = mysqli_insert_id($db_con);

            $getTask = mysqli_query($db_con, "select * from tbl_task_master where task_id = '$wTaskId'"); // or die('Error:' . mysqli_error($db_con));
            $rwgetTask = mysqli_fetch_assoc($getTask);
            $TskStpId = $rwgetTask['step_id'];
            $TskWfId = $rwgetTask['workflow_id'];
            $TskOrd = $rwgetTask['task_order'];
            $TskAsinToId = $rwgetTask['assign_user'];
            $nextTaskOrd = $TskOrd + 1;

            nextTaskAsin($nextTaskOrd, $TskWfId, $TskStpId, $docId, $date, $user_id, $db_con, $filterTaskRemark, $ticket);
            //  unlink("extract-here/ReviewerDoc/$dname");
            $qryDelDoc = mysqli_query($db_con, "delete from tbl_document_reviewer where doc_id='$doc_id'");
            require_once './mail.php';
            
             if(MAIL_BY_SOCKET){

                $paramsArray = array(
                    'ticket' => $ticket,
                    'idins' => $idnxt,
                    'db_con' => $db_con,
                    'projectName' => $projectName,
                    'action' => 'assignTask'
                );
                
                mailBySocket($paramsArray);
                
            }else{

                $mail = assignTask($ticket, $idins, $db_con, $projectName);
            }

            //if ($mail) {

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



                echo '<script>taskSuccess("initiateFile", "' . $lang['Sumitd_Sucsfly'] . '");</script>';
            // } else {

            //     echo '<script>taskFailed("' . basename($_SERVER[REQUEST_URI]) . '", "' . $lang['Ops_Ml_nt_snt'] . '")</script>';
            // }

            echo '<script>uploadSuccess("initiateFile", "' . $lang['Process_Assigned_in_Tray'] . '");</script>';
        }
    }




    /*
     * Message
     * New workflow create with subject
     */
    if (empty($_POST['wfid'])) {
        $lastMoveId = $_POST['lastMoveId'];
        $ip = $_POST['id'];
        if (!empty($subject)) {
            $subject = preg_replace("/[^a-zA-Z0-9& ]/", "", $subject);
            $checkWrkFlwName = mysqli_query($db_con, "select workflow_name from tbl_workflow_master where FIND_IN_SET('$subject', workflow_name)") ; //or die('Error: ' . mysqli_error($db_con));
            $filterTaskRemark = mysqli_escape_string($db_con, $taskRemark);
            if (mysqli_num_rows($checkWrkFlwName) == 1) {//check duplicate name of workflow
                echo'<script>taskFailed("' . basename($_SERVER[REQUEST_URI]) . '","' . $lang['Work_flow_Subject_Already'] . ' !!");</script>';
            } else {
                // if(!empty($_POST['$taskRemark'])){
                //create task

                $taskOrder = $_POST['taskOrder'];
                $assiUsers = $_POST['assignUsr'];
                $altrusr = $_POST['altrUsr'];
                $supvsr = $_POST['supvsr'];

                // if (!empty($taskOrder) && !empty($assiUsers) && !empty($altrusr) && !empty($supvsr)) {
                if (!empty($taskOrder) && !empty($assiUsers) && !empty($supvsr)) {

                    if (count(array_unique($taskOrder)) < count($taskOrder)) {
                        echo '<script>taskFailed("index", "Order No. Can\'t be Same! ")</script>';
                    } else {

                        if (isset($_FILES['fileName']['name'][0]) && !empty($_FILES['fileName']['name'][0])) {
                            if (!empty($lastMoveId)) {
                                if (!empty($_FILES['fileName']['name'][0])) {

                                    //create workflow
                                    //$taskRemark = mysqli_real_escape_string($db_con, $_POST['taskRemark']);
                                    $workflowName = preg_replace("/[^a-zA-Z0-9& ]/", "", $subject);

                                    $insertWorkflow = mysqli_query($db_con, "insert into tbl_workflow_master (workflow_name, workflow_description) values ('$workflowName', '$filterTaskRemark')") ; //or die('Error in workflow:' . mysqli_error($db_con));
                                    $workflId = mysqli_insert_id($db_con);

                                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'WorkFlow $workflowName Created','$date','$date','$host',null)"); // or die('error 1: ' . mysqli_error($db_con));

                                    //asign workflow to group
                                    if ($insertWorkflow) {

                                        $usrGrp = mysqli_query($db_con, "SELECT * FROM `tbl_bridge_grp_to_um` WHERE FIND_IN_SET('$user_id',user_ids)"); //or die('Error:' . mysqli_error($db_con));

                                        while ($rwusrGrp = mysqli_fetch_assoc($usrGrp)) {
                                            $arrayGrp[] = $rwusrGrp['group_id'];
                                        }

                                        $workflowgroups = implode(",", $arrayGrp);

                                        $insertworkflowgrp = mysqli_query($db_con, "insert into tbl_workflow_to_group(workflow_id,group_id) values ('$workflId','$workflowgroups')"); // or die('Error in workflow:' . mysqli_error($db_con));
                                    }

                                    //create step
                                    $workStepName = "Step";
                                    $workStepOrd = 1;
                                    $adStep = mysqli_query($db_con, "insert into tbl_step_master (step_name, workflow_id, step_order) values ('$workStepName', '$workflId', '$workStepOrd')"); // or die('Error in workflow:' . mysqli_error($db_con));

                                    $stepid = mysqli_insert_id($db_con);

                                    for ($i = 0; $i < count($taskOrder); $i++) {

                                        $ord = $taskOrder[$i];
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

                                        $insertTask = mysqli_query($db_con, "insert into tbl_task_master (task_name, assign_user,alternate_user, supervisor, task_order, step_id, workflow_id, task_created_date, deadline, deadline_type) values('$taskName', '$asUsr','$altUsr', '$supVsr', '$ord', '$stepid', '$workflId', '$date', '$dedLn', '$dedTyp')"); // or die('Error1' . mysqli_error($db_con));
                                        //$insertTask = mysqli_query($db_con, "insert into tbl_task_master (task_order, step_id, workflow_id, task_created_date) values('$ord', '$stepid', '$workflId', '$date')") or die('Error' . mysqli_error($db_con));
                                        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'$taskName Created','$date',null,'$host',null)"); // or die('error2 : ' . mysqli_error($db_con));
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
                                    include 'exportpdf.php';
                                    $ticket = $ticket . '_' . $user_id . '_' . strtotime($date);
                                    $posted_editor = $taskRemark; //get content of CKEditor
                                    $slperm = mysqli_query($db_con, "select * from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]'");
                                    $rwSlperm = mysqli_fetch_assoc($slperm);
                                    // $sl_id = $rwSlperm['sl_id'];
                                    $sl_id = $lastMoveId;
                                    $docName = mysqli_query($db_con, "select sl_id,sl_name from tbl_storage_level where sl_id = '$sl_id'"); // or die('Eror2:' . mysqli_error($db_con));
                                    $rwdocName = mysqli_fetch_assoc($docName);
                                    $folderName = str_replace(" ", "", $workflowName);
                                    $pdfName = trim(str_replace(" ", "", $workflowName)) . "_" . mktime(); //specify the file save location and the file name
                                    $nfilename = preg_replace('/[^A-Za-z0-9_\-]/', '', $pdfName);
                                    $filenameEnct = urlencode(base64_encode($nfilename));
                                    $filenameEnct = preg_replace('/[^A-Za-z0-9_\-]/', '', $filenameEnct);
                                    $filenameEnct = $filenameEnct . '.' . "pdf";
                                    $filenameEnct = time() . $filenameEnct;
                                    $path = 'extract-here/' . str_replace(" ", "", $workflowName);
                                    if (!is_dir($path)) {
                                        mkdir($path, 0777, true);
                                    }
                                    $path = $path . '/' . $filenameEnct;
                                    exportPDFSize($posted_editor, $path, $height, $width);
                                    $wrkflowFsize = filesize($path);

                                    $wrkflowFsize = round(($wrkflowFsize / 1000), 2);
                                    $doc_name = $sl_id . '_' . $workflId;
                                    $pagecount = count_pages($path);
                                    $wrkflowDoc = mysqli_query($db_con, "INSERT INTO tbl_document_master (doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages, dateposted,File_Number) VALUES ('$doc_name', '$subject', 'pdf', '$folderName/$filenameEnct', '$user_id', '$wrkflowFsize', '$pagecount', '$date','$fileNumber')"); // or die('Eror3:' . mysqli_error($db_con));
                                    $docId = mysqli_insert_id($db_con);

                                    $newdocname = base64_encode($docId);

                                    //create thumbnail
                                    $uploadedfilename = $path;

                                   
                                    changePdfToImage($uploadedfilename,$newdocname, true);

                                    $id = $sl_id . '_' . $docId . '_' . $workflId;
                                    $destinationPath = str_replace(" ", "", $workflowName) . '/' . $filenameEnct;
                                    $sourcePath = $path;
                                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`,`doc_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,$sl_id,$docId,'Document Upload in workflow','$date','$date','$host',null)"); // or die('errordeve : ' . mysqli_error($db_con));
                                    uploadFileInFtpServer($destinationPath, $sourcePath);

                                    //upload files if any
                                    $files = $_FILES['fileName']['name'];


                                    for ($i = 0; $i < count($files); $i++) {
                                        $file_name = $_FILES['fileName']['name'][$i];
                                        $file_size = $_FILES['fileName']['size'][$i];
                                        $file_type = $_FILES['fileName']['type'][$i];
                                        $file_tmp = $_FILES['fileName']['tmp_name'][$i];
                                        if (!empty($file_name)) {
                                            $pageCount = $_POST['pageCount'][$i];

                                            //$name = explode(".", $file_name);
                                            //$encryptName = urlencode(base64_encode($name[0]));
                                            //$fileExtn = $name[1];
                                            $fileExtn = substr($file_name, strrpos($file_name, ".") + 1);
                                            $folder = str_replace(" ", "", $workflowName);
                                            $fname = substr($file_name, 0, strrpos($file_name, '.'));
                                            $nfilename = preg_replace('/[^A-Za-z0-9_\-]/', '', $fname);
                                            $filenameEnct = urlencode(base64_encode($nfilename));
                                            $filenameEnct = preg_replace('/[^A-Za-z0-9_\-]/', '', $filenameEnct);
                                            $filenameEnct = $filenameEnct . '.' . $fileExtn;
                                            $filenameEnct = time() . $filenameEnct;

                                            $image_path = 'extract-here/' . $folder . '/';

                                            if (!dir($image_path)) {
                                                mkdir($image_path, 0777, TRUE); // or die("Error local folder:". print_r(error_get_last()));
                                            }
                                            $image_path = $image_path . $filenameEnct;
                                            $upload = move_uploaded_file($file_tmp, $image_path) or die(print_r(error_get_last()));

                                            if ($upload) {
                                                $pageCount = count_pages($image_path);

                                                $destinationPath = $folder . '/' . $filenameEnct;
                                                $sourcePath = $image_path;
                                                uploadFileInFtpServer($destinationPath, $sourcePath);


                                                $query = "INSERT INTO tbl_document_master(doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages , dateposted) VALUES ('$id', '$fname', '$fileExtn', '$folder/$filenameEnct', '$user_id', '$file_size', '$pageCount', '$date')";
                                                $exe = mysqli_query($db_con, $query); // or die('Error query failed' . mysqli_error($db_con));

                                                $docId2 = mysqli_insert_id($db_con);

                                                // Decrypt file
                                                decrypt_my_file($image_path);

                                                $newdocname = base64_encode($docId2);

                                                //create thumbnail
                                                $uploadedfilename = $image_path;

                                                if($fileExtn=='jpg' || $fileExtn=='jpeg' || $fileExtn=='png'){
                                                    createThumbnail2($uploadedfilename,$newdocname, true);
                                                }elseif($fileExtn=='pdf'){
                                                    changePdfToImage($uploadedfilename,$newdocname, true);
                                                }

                                                if (empty($docId)) {
                                                    $docId = $docId2;
                                                    $id = $sl_id . '_' . $docId . '_' . $wfid;
                                                    //$vid=$sl_id . '_' . $docId ;
                                                }

                                                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`,`doc_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,$sl_id,$docId,'Document Upload in workflow','$date','$date','$host',null)"); // or die('errorss : ' . mysqli_error($db_con));
                                            }
                                        }
                                    }

                                    $getFirstTask = mysqli_query($db_con, "select * from tbl_task_master where workflow_id = '$workflId' ORDER BY task_order ASC LIMIT 1"); // or die('Erorr:' . mysqli_error($db_con));
                                    $rwgetTask = mysqli_fetch_assoc($getFirstTask);
                                    $wTaskId = $rwgetTask[task_id];

                                    $getTaskDl = mysqli_query($db_con, "select * from tbl_task_master where task_id='$wTaskId'"); // or die('Error:' . mysqli_error($db_con));
                                    $rwgetTaskDl = mysqli_fetch_assoc($getTaskDl);
                                    // print_r($rwgetTaskDl);
                                    if ($rwgetTaskDl['deadline_type'] == 'Date' || $rwgetTaskDl['deadline_type'] == 'Hrs') {

                                        $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 60));
                                    }
                                    if ($rwgetTaskDl['deadline_type'] == 'Days') {

                                        $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 24 * 60 * 60));
                                        echo $rwgetTaskDl['deadline_type'];
                                    }

                                    $insertInTask = mysqli_query($db_con, "INSERT INTO tbl_doc_assigned_wf(task_id, doc_id, start_date, end_date, task_remarks, task_status, assign_by, ticket_id) VALUES ('$wTaskId', '$docId', '$date', '$endDate', '$filterTaskRemark', 'Pending', '$user_id', '$ticket')"); // or die('Erorr123:' . mysqli_error($db_con));

                                    $idins = mysqli_insert_id($db_con);


                                    $getTask = mysqli_query($db_con, "select * from tbl_task_master where task_id = '$wTaskId'"); // or die('Error:' . mysqli_error($db_con));
                                    $rwgetTask = mysqli_fetch_assoc($getTask);
                                    $TskStpId = $rwgetTask['step_id'];
                                    $TskWfId = $rwgetTask['workflow_id'];
                                    $TskOrd = $rwgetTask['task_order'];
                                    $TskAsinToId = $rwgetTask['assign_user'];
                                    $nextTaskOrd = $TskOrd + 1;
                                    // unlink("extract-here/ReviewerDoc/$dname");
                                    $qryDelDoc = mysqli_query($db_con, "delete from tbl_document_reviewer where doc_id='$doc_id'");
                                    nextTaskAsin($nextTaskOrd, $TskWfId, $TskStpId, $docId, $date, $user_id, $db_con, $filterTaskRemark, $ticket);

                                    // echo "<img src='../assets/images/anote-wait.gif' alt='load' id='anotWt' style='display: none;'/> ";
                                    //send mail
                                    require_once './mail.php';
                                   

                                    if(MAIL_BY_SOCKET){

                                        $paramsArray = array(
                                            'ticket' => $ticket,
                                            'idins' => $idnxt,
                                            'db_con' => $db_con,
                                            'projectName' => $projectName,
                                            'action' => 'assignTask'
                                        );
                                        
                                        mailBySocket($paramsArray);
                                        
                                    }else{

                                        $mail = assignTask($ticket, $idins, $db_con, $projectName);
                                    }
                                    
                                    //if ($mail) {


                                        //send sms to mob who submit
//                                $getMobNum = mysqli_query($db_con, "select * from tbl_user_master where user_id = '$user_id'") or die('Error:' . mysqli_error($db_con));
//                                $rwgetMobNum = mysqli_fetch_assoc($getMobNum);
//                                $submtByMob = $rwgetMobNum['phone_no'];
//                                $msg = 'Your Ticket Id is: ' . $ticket . ' and Your Task is in Process.';
//                                $sendMsgToMbl = smsgatewaycenter_com_Send($submtByMob, $msg, $debug = false);
                                        //


                                    echo '<script>taskSuccess("initiateFile", "' . $lang['Sumitd_Sucsfly'] . '");</script>';
                                    // } else {

                                    //     echo '<script>taskFailed("' . basename($_SERVER[REQUEST_URI]) . '", "' . $lang['Ops_Ml_nt_snt'] . '")</script>';
                                    // }

                                   /// echo '<script>uploadSuccess("initiateFile", "' . $lang['Process_Assigned_in_Tray'] . '");</script>';
                                } else {
                                    echo '<script>taskFailed("' . basename($_SERVER[REQUEST_URI]) . '", "' . $lang['please_Select_Storage'] . '")</script>';
                                }
                            }
                        } else {

                            //create workflow
                            $workflowName = preg_replace("/[^a-zA-Z0-9& ]/", "", $subject);
                            $filterTaskRemark = mysqli_escape_string($db_con, $taskRemark);
                            $insertWorkflow = mysqli_query($db_con, "insert into tbl_workflow_master (workflow_name, workflow_description) values ('$workflowName', '$filterTaskRemark')"); // or die('Error in workflow:' . mysqli_error($db_con));
                            $workflId = mysqli_insert_id($db_con);

                            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'WorkFlow Name $workflowName Created','$date','$date','$host',null)"); // or die('error : ' . mysqli_error($db_con));

                            //asign workflow to group
                            if ($insertWorkflow) {

                                $usrGrp = mysqli_query($db_con, "SELECT * FROM `tbl_bridge_grp_to_um` WHERE FIND_IN_SET('$user_id',user_ids)"); // or die('Error:' . mysqli_error($db_con));

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
                            $adStep = mysqli_query($db_con, "insert into tbl_step_master (step_name, workflow_id, step_order) values ('$workStepName', '$workflId', '$workStepOrd')"); // or die('Error in workflow:' . mysqli_error($db_con));

                            $stepid = mysqli_insert_id($db_con);

                            for ($i = 0; $i < count($taskOrder); $i++) {

                                $ord = $taskOrder[$i];
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


                                $insertTask = mysqli_query($db_con, "insert into tbl_task_master (task_name, assign_user,alternate_user, supervisor, task_order, step_id, workflow_id, task_created_date, deadline, deadline_type) values('$taskName', '$asUsr','$altUsr', '$supVsr', '$ord', '$stepid', '$workflId', '$date', '$dedLn', '$dedTyp')"); // or die('Error1' . mysqli_error($db_con));
                                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,' $taskName Task Added','$date','$date','$host',null)"); // or die('error : ' . mysqli_error($db_con));
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
                            include 'exportpdf.php';
                            $ticket = $ticket . '_' . $user_id . '_' . strtotime($date);
                            $posted_editor = $taskRemark; //get content of CKEditor
                            $slperm = mysqli_query($db_con, "select * from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]'");
                            $rwSlperm = mysqli_fetch_assoc($slperm);
                            // $sl_id = $rwSlperm['sl_id'];
                            $sl_id = $lastMoveId;
                            $docName = mysqli_query($db_con, "select sl_id,sl_name from tbl_storage_level where sl_id = '$sl_id'") or die('Eror2:' . mysqli_error($db_con));
                            $rwdocName = mysqli_fetch_assoc($docName);
                            $folderName = str_replace(" ", "", $workflowName);
                            $pdfName = trim(str_replace(" ", "", $workflowName)) . "_" . mktime(); //specify the file save location and the file name
                            $nfilename = preg_replace('/[^A-Za-z0-9_\-]/', '', $pdfName);
                            $filenameEnct = urlencode(base64_encode($nfilename));
                            $filenameEnct = preg_replace('/[^A-Za-z0-9_\-]/', '', $filenameEnct);
                            $filenameEnct = $filenameEnct . '.' . "pdf";
                            $filenameEnct = time() . $filenameEnct;
                            $path = 'extract-here/' . str_replace(" ", "", $workflowName);
                            if (!is_dir($path)) {
                                mkdir($path, 0777, true);
                            }
                            $path = $path . '/' . $filenameEnct;
                            exportPDFSize($posted_editor, $path, $height, $width);
                            $wrkflowFsize = filesize($path);

                            $wrkflowFsize = round(($wrkflowFsize / 1000), 2);
                            $doc_name = $sl_id . '_' . $workflId;
                            $pagecount = count_pages($path);
                            $wrkflowDoc = mysqli_query($db_con, "INSERT INTO tbl_document_master (doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages, dateposted,File_Number) VALUES ('$doc_name', '$subject', 'pdf', '$folderName/$filenameEnct', '$user_id', '$wrkflowFsize', '$pagecount', '$date','$fileNumber')") or die('Eror3:' . mysqli_error($db_con));
                            $docId = mysqli_insert_id($db_con);

                            $newdocname = base64_encode($docId);

                            //create thumbnail
                            $uploadedfilename = $path;

                            changePdfToImage($uploadedfilename,$newdocname, true);
                            
                            $id = $sl_id . '_' . $docId . '_' . $workflId;
                            $destinationPath = str_replace(" ", "", $workflowName) . '/' . $filenameEnct;
                            $sourcePath = $path;
                            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`,`doc_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,$sl_id,$docId,'Document Upload in $workflowName workflow','$date','$date','$host',null)") or die('error : ' . mysqli_error($db_con));
                            uploadFileInFtpServer($destinationPath, $sourcePath);

                            $getFirstTask = mysqli_query($db_con, "select * from tbl_task_master where workflow_id = '$workflId' ORDER BY task_order ASC LIMIT 1") or die('Erorr:' . mysqli_error($db_con));
                            $rwgetTask = mysqli_fetch_assoc($getFirstTask);
                            $wTaskId = $rwgetTask[task_id];


                            $getTaskDl = mysqli_query($db_con, "select * from tbl_task_master where task_id='$wTaskId'") or die('Error:' . mysqli_error($db_con));
                            $rwgetTaskDl = mysqli_fetch_assoc($getTaskDl);
                            // print_r($rwgetTaskDl);
                            if ($rwgetTaskDl['deadline_type'] == 'Date' || $rwgetTaskDl['deadline_type'] == 'Hrs') {

                                $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 60));
                            }
                            if ($rwgetTaskDl['deadline_type'] == 'Days') {

                                $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 24 * 60 * 60));
                            }


                            $insertInTask = mysqli_query($db_con, "INSERT INTO tbl_doc_assigned_wf(task_id, doc_id, start_date, end_date, task_remarks, task_status, assign_by, ticket_id) VALUES ('$wTaskId', '$docId', '$date', '$endDate', '$filterTaskRemark', 'Pending', '$user_id', '$ticket')") or die('Erorr:' . mysqli_error($db_con));
                            $idins = mysqli_insert_id($db_con);

                            $getTask = mysqli_query($db_con, "select * from tbl_task_master where task_id = '$wTaskId'") or die('Error:' . mysqli_error($db_con));
                            $rwgetTask = mysqli_fetch_assoc($getTask);
                            $TskStpId = $rwgetTask['step_id'];
                            $TskWfId = $rwgetTask['workflow_id'];
                            $TskOrd = $rwgetTask['task_order'];
                            $TskAsinToId = $rwgetTask['assign_user'];
                            $nextTaskOrd = $TskOrd + 1;
                            //unlink("extract-here/ReviewerDoc/$dname");
                            $qryDelDoc = mysqli_query($db_con, "delete from tbl_document_reviewer where doc_id='$doc_id'");
                            nextTaskAsin($nextTaskOrd, $TskWfId, $TskStpId, $docId, $date, $user_id, $db_con, $filterTaskRemark, $ticket);

                            require_once './mail.php';
                            

                            if(MAIL_BY_SOCKET){

                                $paramsArray = array(
                                    'ticket' => $ticket,
                                    'idins' => $idnxt,
                                    'db_con' => $db_con,
                                    'projectName' => $projectName,
                                    'action' => 'assignTask'
                                );
                                
                                mailBySocket($paramsArray);
                                
                            }else{

                                $mail = assignTask($ticket, $idins, $db_con, $projectName);
                            }

                           // if ($mail) {

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

                                echo '<script>taskSuccess("initiateFile", "' . $lang['Sumitd_Sucsfly'] . '");</script>';
                            // } else {

                            //     echo '<script>taskFailed("' . basename($_SERVER[REQUEST_URI]) . '", "' . $lang['Opps_Sbmsn_fld'] . '")</script>';
                            // }
                            echo '<script>uploadSuccess("initiateFile", "' . $lang['Process_Assigned_in_Tray'] . '");</script>';
                        }
                    }
                } else {
                    echo '<script>taskFailed("' . basename($_SERVER[REQUEST_URI]) . '", "' . $lang['Please_Fill_Atleast_One_Order'] . '")</script>';
                }
                /*
                  }else{
                  echo '<script>taskFailed("index", "Description is Required !")</script>';
                  }
                 */
            }
        } else {

            echo '<script>taskFailed("' . basename($_SERVER[REQUEST_URI]) . '", "' . $lang['Subject_Name_or_Existing_Work_Flow_should_be_filled'] . '")</script>';
        }
    }
}

function uploadFileInFtpServer($destinationPath, $sourcePath) {

    /* encrypt_my_file($sourcePath);

    $fileManager = new fileManager();
	// Connect to file server
	$fileManager->conntFileServer();
	if($fileManager->uploadFile($sourcePath, ROOT_FTP_FOLDER . '/' . $destinationPath)){
		 return true;
	}else{
		 return false;
	} */
	
	return true;
}
?>
