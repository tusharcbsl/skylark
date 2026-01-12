<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    require_once './application/config/database.php';
    require_once './application/pages/head.php';
    require_once './application/pages/function.php';
    require_once './application/pages/sendSms.php';
    require_once './mail.php';
	require_once './classes/fileManager.php';
	
    mysqli_set_charset($db_con, "utf8");
    $sameGroupIDs = array();
    $group = mysqli_query($db_con, "select * from tbl_bridge_grp_to_um where find_in_set('$_SESSION[cdes_user_id]',user_ids)") or die('Error' . mysqli_error($db_con));
    while ($rwGroup = mysqli_fetch_assoc($group)) {
        $sameGroupIDs[] = $rwGroup['user_ids'];
    }
    $sameGroupIDs = array_unique($sameGroupIDs);
    sort($sameGroupIDs);
    $sameGroupIDs = implode(',', $sameGroupIDs);
   

    if ($rwgetRole['file_review'] != '1') {
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
                    <div class="container" id="afterSubmt">
                        <!-- Page-Title -->
                        <div class="row">
                            <div class="col-sm-12">
                                <ol class="breadcrumb">
                                    <li>
                                        <a href="initiateFile"><?php echo $lang['docforreview']; ?></a>
                                    </li>
                                    <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                                </ol>
                            </div>
                        </div>
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h4 class="header-title"> <?php echo $lang['docforreview']; ?></h4>
                            </div>
                            <div class="card-box">
                                <form method="post" enctype="multipart/form-data" id="initiate_form" novalidate>
                                    <?php
                                    if (isset($_GET['doc_Id']) && !empty($_GET['doc_Id']) && !isset($_GET['ticket_id']) && empty($_GET['ticket_id'])) {
                                        ?>
                                        <div class="row step-3">
                                            <div class="row rview" >
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

                                                                <select class="form-control select2"  name="review[]" id="review" required>
                                                                    <option value=""><?php echo $lang['Select_reviewer']; ?></option>
                                                                    <?php
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
                                           
                                            <div class="form-group col-sm-6" style="display: none">
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

                                                <button class="btn btn-primary  pull-right"    name="docReview" id="dybuttonn" ><?php echo $lang['Save']; ?></button>
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
                                                            <input type="text" name="subject" class="form-control" placeholder="<?= $lang['enter_subject']; ?>" maxlength="40" value="<?= isset($_POST['subject']) ? $_POST['subject'] : '' ?>" required=""/>
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
    <div id="multi-csv-export-model" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
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
                
    $(document).ready(function () {
        //$('form').parsley();
         var heiht = $(document).height();
        $('#wait').css('height', heiht);
        
        $("#initiate_form").on('submit', function (event) {
            
            $("#wait").show();
        });

        //addTranslationClass();

    });
   
    //for wait gif display after submit
//                                    var heiht = $(document).height();
//                                    //alert(heiht);
//                                    $('#wait').css('height', heiht);
//                                    $('#initiate_form').submit(function () {
//                                        $('#wait').show();
//                                        //$('#wait').css('height',heiht);
//                                        $('#afterSubmt').hide();
//                                        return true;
//                                    });

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
         *
         */

        
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
/*
 * Doc Reviewer insert assaign to
 */
if (isset($_POST['docReview'])) {
    mysqli_autocommit($db_con, FALSE);
    //@sk(61218): Document id from document master
    $document_id = base64_decode(urldecode($_GET['doc_Id']));
    // get doc id from document reviewer after inserting record.
    $doc_id=reviewStorageFile($db_con,$document_id,$date,$lang);
  
    //$doc_id = base64_decode(urldecode($_GET['doc_Id']));
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
    $workFlowArray = explode(" ", $dname);
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
                //$docSrcPath = 'extract-here/';    //SET STATIC ROOT FOLDER FOR SOURCE
                //$ftpfile = $docSrcPath . $docInfoFetch['doc_path'];
                
                 
               //sk@261218 : get file path;
              $ftpfile = getfilePath($db_con,$docInfoFetch);
              //die("okk");
                //echo $ftpfile;
                
                /*
                 * Validate file exist on doc review or not
                 */
                if (file_exists($ftpfile)) {
                    for ($i = 0; $i < count($assignTo); $i++) {
                        if(!empty($assignTo[$i])){
                            $order = $oredr[$i];

                            if (min($oredr) == $oredr[$i]) {

                                $insertDocAssign = mysqli_query($db_con, "Insert into `tbl_doc_review` (`doc_id`,`start_date`,`task_status`,`assign_by`,`action_by`,`next_task`,`ticket_id`,`review_order`)values('$doc_id','$date','Pending','$user_id','$assignTo[$i]','0','$ticketReviewer','$order')")or die(mysqli_error($db_con)); //Insert Doc review in doc reviw table
                                $firstReview = mysqli_insert_id($db_con);
                            } else {
                                $insertDocAssign = mysqli_query($db_con, "Insert into `tbl_doc_review` (`doc_id`,`start_date`,`task_status`,`assign_by`,`action_by`,`next_task`,`ticket_id`,`review_order`)values('$doc_id','$date','Pending','$user_id','$assignTo[$i]','2','$ticketReviewer','$order')")or die(mysqli_error($db_con));
                                ; //Insert Doc review in doc reviw table
                            }
                        
                        }
                    }

                    

                    if ($insertDocAssign) {


//                        if (isset($_POST['lastMoveId']) && !empty($_POST['lastMoveId'])) {
                        $qry = mysqli_query($db_con, "INSERT INTO `tbl_reviews_log`(`user_id`,`doc_id`,`action_name`,`start_date`,`end_date`,`system_ip`,`remarks`)values('$user_id','$doc_id','Document Added For Review From Storage','$date','$date','$host/$ip','')");


                        if (MAIL_BY_SOCKET) {
                            $paramsArray = array(
                                'ticketReviewer' => $ticketReviewer,
                                'firstReview' => $firstReview,
                                'action' => 'assignReview',
                                'projectName' => $projectName,
                                'subject' => $subject
                            );
                            mailBySocket($paramsArray);
                        } else {
                            $mail = assignReview($ticketReviewer, $firstReview, $db_con, $projectName, $subject);
                        }



                       // if ($mail) {
                            //for document alert to subscribe user
                            $subdocId = $document_id;
                            $userId = array();
                            $checksubs = mysqli_query($db_con, "SELECT * FROM tbl_document_subscriber WHERE subscribe_docid='$subdocId' and find_in_set('4',action_id)");
                            while ($rwcheckSubs = mysqli_fetch_assoc($checksubs)) {
                                $userId[] = $rwcheckSubs['subscriber_userid'];
                            }
                            $userIds = implode(',', $userId);
                            $mailto = array();
                            $k = 1;
                            $touser = mysqli_query($db_con, "SELECT user_email_id,first_name FROM tbl_user_master WHERE user_id in($userIds)");
                            while ($rwtouser = mysqli_fetch_assoc($touser)) {
                                $mailto[$k]['user_email_id'] = $rwtouser['user_email_id'];
                                $mailto[$k]['first_name'] = $rwtouser['first_name'];
                                $k++;
                            }


                            $fileaction = "$dname document added for review from storage.";
                            $documentName = $dname;
                            require_once './mail.php';
                           
                            foreach ($mailto as $to) {
                                $email = $to['user_email_id'];
                                $name = $to['first_name'];
                                if (MAIL_BY_SOCKET) {
                                    $paramsArray = array(
                                        'email' => $email,
                                        'filenamed' => $filenamed,
                                        'action' => 'filesubscribe',
                                        'projectName' => $projectName,
                                        'fileaction' => $fileaction,
                                        'name' => $name
                                    );
                                    mailBySocket($paramsArray);
                                } else {
                                    $mailsent = mailsenttoDocumentSubscribeUsers($email, $filenamed, $projectName, $fileaction, $name);
                                }
                            }

                            mysqli_commit($db_con);
                            echo '<script>taskSuccess("sentreview", "' . $lang['Assigned_to_reviewer_success'] . '");</script>';

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

function uploadFileInFtpServer($destinationPath, $sourcePath) {
	
	$fileManager = new fileManager();
	// Connect to file server
	$fileManager->conntFileServer();
	if($fileManager->uploadFile($sourcePath, ROOT_FTP_FOLDER . '/' . $destinationPath)){
		 return true;
	}else{
		 return false;
	}
}

//sk@61218 : insert record into document_reviewer from document_master for review
function reviewStorageFile($db_con,$doc_id,$date,$lang){

    $doc_res= mysqli_fetch_assoc(mysqli_query($db_con,"select * from tbl_document_master where doc_id='$doc_id'"));

    $metadataArray = [];
    if(!empty($doc_res)){

        $getMetaId = mysqli_query($db_con, "select * from tbl_metadata_to_storagelevel where sl_id = '$doc_res[doc_name]'") or die('Error:gg' . mysqli_error($db_con));

        if(mysqli_num_rows($getMetaId)>0){
            while ($rwgetMetaId = mysqli_fetch_assoc($getMetaId)) {

                $getMetaName = mysqli_query($db_con, "select * from tbl_metadata_master where id = '$rwgetMetaId[metadata_id]'") or die('Error:' . mysqli_error($db_con));
                if(mysqli_num_rows($getMetaName)>0){
                    while ($rwgetMetaName = mysqli_fetch_assoc($getMetaName)) {

                        if (!empty($doc_res[$rwgetMetaName['field_name']])) {
                            if ($doc_res['field_name'] == 'noofpages' || $doc_res['field_name'] == 'filename') {
                                
                            } else {

                                    $metadataArray[$rwgetMetaName['field_name']]= $doc_res[$rwgetMetaName['field_name']];
                                
                            }
                        }
                    }
                }
            }
        }

        $metaJson="";
        if(count($metadataArray)>0){
            $metaJson = json_encode($metadataArray);
        }
        



        $rev_sql="insert into tbl_document_reviewer set 
                                                       doc_name='$doc_res[doc_name]',
                                                       old_doc_name='$doc_res[old_doc_name]',
                                                       doc_extn='$doc_res[doc_extn]',
                                                       doc_path='$doc_res[doc_path]',
                                                       uploaded_by='$doc_res[uploaded_by]',
                                                       doc_size='$doc_res[doc_size]',
                                                       noofpages='$doc_res[noofpages]',
                                                       dateposted='$doc_res[dateposted]',
                                                       File_Number='$doc_res[File_Number]',
                                                       doc_desc='$doc_res[doc_desc]',
                                                       filename='$doc_res[filename]',
                                                       flag_multidelete='$doc_res[flag_multidelete]',    
                                                       storage_doc_id='$doc_res[doc_id]',
                                                       storage_doc_name='$doc_res[old_doc_name]',
                                                       metadata='$metaJson'
                                                       ";
        $rev_query= mysqli_query($db_con, $rev_sql);
        if($rev_query){
            $review_doc_id= mysqli_insert_id($db_con);
            if(!empty($review_doc_id)){
                //$up_doc_query= mysqli_query($db_con, "update tbl_document_master set is_inreview='1' where doc_id='$doc_id'");
                $up_doc_query= mysqli_query($db_con, "delete from tbl_document_master where doc_id='$doc_id'");
                if(!$up_doc_query){
                    echo '<script>taskFailed("' . basename($_SERVER[REQUEST_URI]) . '", "' . $lang['record_updation_error'] . '")</script>'; 
                }
            } else {
                echo '<script>taskFailed("' . basename($_SERVER[REQUEST_URI]) . '", "' . $lang['missing_required_parameter'] . '")</script>'; 
            }
        }else{
            echo '<script>taskFailed("' . basename($_SERVER[REQUEST_URI]) . '", "' . $lang['record_insertion_error'] . '")</script>';
        }
    } else {
        echo '<script>taskFailed("' . basename($_SERVER[REQUEST_URI]) . '", "' . $lang['record_not_found'] . '")</script>';  
    }
    return $review_doc_id;

}

//sk@261218 : Get file path when ftp is enabled or disabled
function getfilePath($db_con,$doc_info){
    
$fileName = $doc_info['old_doc_name'];
$filePath = $doc_info['doc_path']; 

if(!file_exists('extract-here/'.$filePath)){
   
	$slid=$doc_info['doc_name'];
	$doc_extn=$doc_info['doc_extn'];
	$rwStor = mysqli_fetch_assoc(mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id='$slid'")) or die('Error');
	$folderName="temp";
	if (!dir($folderName)) {
		mkdir($folderName, 0777, TRUE);
	}
	$folderName=$folderName.'/'.$_SESSION['cdes_user_id'];
	if (!dir($folderName)) {
		mkdir($folderName, 0777, TRUE);
	}
	$folderName = $folderName.'/'.preg_replace('/[^A-Za-z0-9\-]/', '',$rwStor['sl_name']);//preg_replace('/[^A-Za-z0-9\-]/', '', $string);
	if (!dir($folderName)) {
		mkdir($folderName, 0777, TRUE);
		
		$localPath = $folderName . '/' . preg_replace('/[^A-Za-z0-9\-]/', '',$fileName).'.'.$doc_extn;
		if (!empty($fileName)) {
			$fileManager = new fileManager();
			$fileManager->conntFileServer();
			$server_path = ROOT_FTP_FOLDER.'/'.$filePath;
			if($fileManager->downloadFile($localPath, $server_path)){
				
				return $localPath;
			}else{
				return false;
			}
			
		}
		
	}
}else{

	return 'extract-here/'.$filePath;
}
}
?>
