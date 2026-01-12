<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    require_once './application/config/database.php';
    require_once './application/pages/head.php';

    // declaring some further used variable;
    $title = $user_id = $title = $contact = $agenda = $notify_frequency = $notify_time = $app_date = $app_time = $app_notes = $contact_email = '';
   

    // echo $rwgetRole['dashboard_mydms']; die;
    if ($rwgetRole['todo_edit'] != '1') {
        header('Location: ./index');
    }

    // check for to do id
    if (isset($_GET['aid'])) {
        //check for Authority
        ($rwgetRole['appoint_edit'] == '1' ?: header('Location: ./index'));

        $aid = base64_decode(urldecode($_GET['aid']));
        $aid = intval($aid);
        mysqli_set_charset($db_con, "utf8");
        $td_res = mysqli_fetch_assoc(mysqli_query($db_con, "select * from appointments where id='$aid'"));
        $title = $td_res['title'];
        $contact = $td_res['contact'];
        $app_date = $td_res['app_date'];
        $app_date = date('d-m-Y', strtotime($app_date));
        $app_time = $td_res['app_time'];
        $agenda = $td_res['agenda'];
        $notify_frequency = $td_res['notify_frequency'];
        $notify_time = $td_res['notify_time'];
        $user_id = $td_res['user_id'];
        $notes = $td_res['app_notes'];
        $notes = $td_res['app_notes'];
        $contact_email = $td_res['contact_email'];
    } else {
//check for Authority
        ($rwgetRole['todo_add'] == '1' ?: header('Location: ./index'));
    }
    require_once('tdn-appoint.php');
    ?>
    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
    <style>
        /*        #cldr .parsley-required{
                    margin-top: -18px !important;
                }*/
    </style>
    <body class="fixed-left">
        <!-- Begin page -->
        <div id="wrapper">
            <!-- Top Bar Start -->
            <?php require_once './application/pages/topBar.php'; ?>
            <!-- Top Bar End -->
            <?php require_once './application/pages/sidebar.php'; ?>
            <!-- Left Sidebar End --> 
            <div class="content-page">
                <!-- Start content -->
                <div class="content">
                    <div class="container">
                        <!-- Page-Title -->
                        <div class="row">
                            <ol class="breadcrumb">
                                <li><a href="manage-appointment"><?php echo $lang['appoint']; ?></a></li>
                                <li class="active"><?php echo ($aid ? $lang['appoint_edit'] : $lang['appoint_add']); ?></li>
                               <li> <a href="#" data-toggle="modal" data-target="#help-modal" id="helpview" data="31" title="<?= $lang['help']; ?>"><i class="fa fa-question-circle"></i> </a></li>
                                <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                            </ol>
                        </div>
                        <div class="row" id="afterClickHide">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h4 class="header-title col-lg-6"> <?php echo $lang['Required_fields_are_marked_with_a']; ?>(<span style="color:red;">*</span>)</h4>
                                </div>
                                <div class="box-body">
                                    <div class="col-lg-12">
                                        <form action="#" data-parsley-validate novalidate method="post" enctype="multipart/form-data" id="todo-form">
                                            <div class="row m-b-15"> 
                                                <input type="hidden"  name="aid" value="<?= urlencode(base64_encode($aid)) ?>">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <div class="col-md-4">
                                                            <label for="userName"><?php echo $lang['Title']; ?><span style="color: red;">*</span></label>
                                                        </div>
                                                        <div class="col-md-8">

                                                            <input type="text" placeholder="<?= $lang['ent_appoint_title'] ?>" class="form-control translatetext specialchaecterlock" name="title" value="<?= $title ?>" maxlength="250" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <div class="col-md-4">
                                                            <label for="userName"><?php echo $lang['contact_name']; ?><span style="color: red;">*</span></label>
                                                        </div>
                                                        <div class="col-md-8">

                                                            <input type="text" placeholder="<?= $lang['ent_contact_name'] ?>" class="form-control translatetext specialchaecterlock" name="contact" value="<?= $contact ?>" maxlength="100" required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row m-b-15"> 
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <div class="col-md-4">
                                                            <label for="userName"><?php echo $lang['contact_email']; ?></label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="email" placeholder="<?= $lang['ent_contact_email'] ?>" class="form-control" name="contact_email" value="<?= $contact_email ?>"   maxlength="100">
                                                        </div>
                                                    </div>
                                                </div>  

                                                <div class="col-md-6" id="cldr">
                                                    <div class="form-group">
                                                        <div class="col-md-4">
                                                            <label for="userName"><?php echo $lang['Date']; ?><span style="color: red;">*</span></label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <div class="input-group">
                                                                <input type="text" placeholder="<?= $lang['select_date'] ?>" class="form-control datepicker" name="app_date" value="<?= $app_date ?>" maxlength="40" required>
                                                                <span class="input-group-addon bg-custom b-0 text-white"><i class="icon-calender"></i></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row"> 
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <div class="col-md-4">
                                                            <label for="userName"><?php echo $lang['time']; ?><span style="color: red;">*</span></label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <div class="input-group m-b-15">
                                                                <div class="bootstrap-timepicker">
                                                                    <input type="text" class="form-control timepicker" name="app_time" value="<?= $app_time ?>" maxlength="40" required>
                                                                </div>
                                                                <span class="input-group-addon bg-custom b-0 text-white"><i class="glyphicon glyphicon-time"></i></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <div class="col-md-4">
                                                            <label for="userName"><?php echo $lang['noty_time']; ?><span style="color: red;">*</span></label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <div class="input-group m-b-15">
                                                                <div class="bootstrap-timepicker">
                                                                    <input type="text" class="form-control timepicker" name="notify_time" value="<?= $notify_time ?>" maxlength="40" required>
                                                                </div>
                                                                <span class="input-group-addon bg-custom b-0 text-white"><i class="glyphicon glyphicon-time"></i></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="row m-b-15"> 
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <div class="col-md-4">
                                                            <label for="userName"><?php echo $lang['noty_freq'] ?><span style="color: red;">*</span></label>
                                                        </div>
                                                        <div class="col-md-8">

                                                            <select class="select3" name="notify_frequency" required>
                                                                <option value=""  selected><?php echo $lang['noty_freq'] ?></option>
                                                                <option value="0" <?= ($notify_frequency == '0' ? 'selected' : '') ?>><?= $lang['same_day']; ?></option>
                                                                <option value="1" <?= ($notify_frequency == '1' ? 'selected' : '') ?>>1 <?= $lang['day_before']; ?></option>
                                                                <option value="2" <?= ($notify_frequency == '2' ? 'selected' : '') ?>>2 <?= $lang['day_before']; ?></option>
                                                                <option value="3" <?= ($notify_frequency == '3' ? 'selected' : '') ?>>3 <?= $lang['day_before']; ?></option>
                                                                <option value="4" <?= ($notify_frequency == '4' ? 'selected' : '') ?>>4 <?= $lang['day_before']; ?></option>
                                                                <option value="5" <?= ($notify_frequency == '5' ? 'selected' : '') ?>>5 <?= $lang['day_before']; ?></option>
                                                                <option value="6" <?= ($notify_frequency == '6' ? 'selected' : '') ?>>6 <?= $lang['day_before']; ?></option>
                                                                <option value="7" <?= ($notify_frequency == '7' ? 'selected' : '') ?>>7 <?= $lang['day_before']; ?></option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <div class="col-md-4">
                                                            <label for="userName"><?php echo $lang['agenda']; ?><span style="color: red;">*</span></label>
                                                        </div>
                                                        <div class="col-md-8">

                                                            <input placeholder="<?= $lang['ent_agenda'] ?>" type="text" class="form-control translatetext specialchaecterlock" name="agenda" value="<?= $agenda ?>"  required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <div class="col-md-2">
                                                            <label for="userName"><?php echo $lang['notes']; ?></label>
                                                        </div>
                                                        <div class="col-md-10 m-b-10">
                                                            <textarea  class="form-control translatetext specialchaecterlock" rows="5" name="notes" id="editors" placeholder="<?= $lang['ent_notes'] ?>"><?= $notes ?></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="form-group text-right m-r-10">
                                                        <button class="btn btn-primary waves-effect waves-light" type="submit" name="submit-todo" id="submit-todo">
                                                            <?php echo $lang["Submit"]; ?>
                                                        </button>
                                                        <a href="manage-appointment" class="btn btn-default waves-effect waves-light m-l-5">
                                                            <?php echo $lang["Cancel"]; ?>
                                                        </a>
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
</div>
<!-- END wrapper -->
<?php require_once './application/pages/footerForjs.php'; ?>

<script src="assets/plugins/moment/moment.js"></script>
<script src="assets/plugins/timepicker/bootstrap-timepicker.js"></script>
<script src="assets/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
<script src="assets/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
<script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
<script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script src="assets/plugins/tinymce/tinymce.min.js"></script>
<script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>

<script type="text/javascript">


                               $(document).ready(function () {
                                   if ($("#editor").length > 0) {
                                       tinymce.init({
                                           selector: "textarea#editor",
                                           theme: "modern",
                                           height: 180,
                                           plugins: [
                                               "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
                                               "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                                               "save table contextmenu directionality emoticons template paste textcolor"
                                           ],

                                           /* plugins: [
                                            "lists preview spellchecker","code",
                                            "save paste textcolor"
                                            ],*/
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
    $("input:radio[name='radio']").click(function () {

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
    $(document).ready(function (){$('#todo-form').validate({submitHandler: function(form) {saveToDo();}});});

    $(".select3").select2();
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



    // Date Picker
    $('.datepicker').datepicker({
        autoclose: true,
        todayHighlight: true
    });

    // Date Picker
    $('.timepicker').timepicker({
        defaultTIme: false,
        minuteStep: 1
    });



    //////// save todo //////////////////
//    $('#todo-form').parsley().on('form:submit', function (event) {
//        saveToDo();
//    });
    function saveToDo() {
        $.ajax({
            url: "application/ajax/save-appoint.php",
            type: "POST",
            dataType: "json",
            data: $("#todo-form").serialize(),
            beforeSend: function ()
            {
                $("#submit-todo").html("Wait...");
                $("#submit-todo").prop('disabled', true);
            },
            success: function (r)
            {
                if (r.status == 'success') {
                    taskSuccess('manage-appointment', r.msg)
                }
                $("#submit-todo").html("Submit");
                $("#submit-todo").prop('disabled', false);
                getToken();
            }
        });
    }

</script>

</body>


</html>