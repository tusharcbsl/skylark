<?php
require_once '../../sessionstart.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout.php");
}
require './../config/database.php';
if (isset($_SESSION['lang'])) {
    $file = "../../" . $_SESSION['lang'] . ".json";
} else {
    $file = "../../English.json";
}
//for user role
$data = file_get_contents($file);
$lang = json_decode($data, true);
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

// echo $rwgetRole['dashboard_mydms']; die;
if ($rwgetRole['workflow_step'] != '1') {
    header('Location: ../../index');
}

$id = $_POST['ID'] + 1;
?>

<link href="assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
<link href="assets/plugins/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
<link href="assets/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" />  

<div class="row">
    <div class="col-sm-2">
        <input type="number" class="form-control" name="taskOrder[]" min="1">
    </div>
    <div class="col-sm-2">
        <select class="selectpicker" data-live-search="true" name="assignUsr[]" data-style="btn-white">
            <option selected disabled style="background: #808080; color: #fff;"><?php echo $lang['Select_User'] ?></option>
            <?php
            $user = mysqli_query($db_con, "select * from tbl_user_master where user_id in($sameGroupIDs) order by first_name, last_name asc")or die("Error in user" . mysqli_error($db_con));
            while ($rwUser = mysqli_fetch_assoc($user)) {
                ?>
                <?php if ($rwUser['user_id'] != 1 && $_SESSION['cdes_user_id'] != $rwUser['user_id']) { ?>
                    <option value="<?php echo $rwUser['user_id']; ?>"><?php echo $rwUser['first_name'] . ' ' . $rwUser['last_name']; ?></option>

                    <?php
                }
            }
            ?>
        </select>
    </div>
    <div class="col-sm-2">
        <select class="selectpicker" data-live-search="true" name="altrUsr[]" data-style="btn-white">
            <option selected disabled style="background: #808080; color: #fff;"><?php echo $lang['Select_User'] ?></option>
            <?php
            $user = mysqli_query($db_con, "select * from tbl_user_master where user_id in($sameGroupIDs) order by first_name, last_name asc")or die("Error in user" . mysqli_error($db_con));
            while ($rwUser = mysqli_fetch_assoc($user)) {
                ?>
                <?php if ($rwUser['user_id'] != 1 && $_SESSION['cdes_user_id'] != $rwUser['user_id']) { ?>
                    <option value="<?php echo $rwUser['user_id']; ?>"><?php echo $rwUser['first_name'] . ' ' . $rwUser['last_name']; ?></option>

                    <?php
                }
            }
            ?>
        </select>
    </div>
    <div class="col-sm-2">
        <select class="selectpicker" data-live-search="true" name="supvsr[]" data-style="btn-white">
            <option selected disabled style="background: #808080; color: #fff;"><?php echo $lang['Select_User'] ?></option>
            <?php
            $user = mysqli_query($db_con, "select * from tbl_user_master where user_id in($sameGroupIDs) order by first_name, last_name asc")or die("Error in user" . mysqli_error($db_con));
            while ($rwUser = mysqli_fetch_assoc($user)) {
                ?>
                <?php if ($rwUser['user_id'] != 1 && $_SESSION['cdes_user_id'] != $rwUser['user_id']) { ?>
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
        <div class="form-group">
            <table>
                <tr>
                    <td>
                        <div class="radio radio-success">
                            <input type="radio" name="radio<?php echo $id; ?>" id="radio3<?php echo $id; ?>" value="Date" checked>
                            <label for="radio3<?php echo $id; ?>"><?php echo $lang['Date']; ?> &nbsp;</label>
                        </div>
                    </td>
                    <td>
                        <div class="radio radio-success">
                            <input type="radio" name="radio<?php echo $id; ?>" id="radio4<?php echo $id; ?>" value="Days">
                            <label for="radio4<?php echo $id; ?>">
                                <?php echo $lang['Days']; ?> &nbsp;
                            </label>
                        </div>
                    </td>
                    <td>
                        <div class="radio radio-success">
                            <input type="radio" name="radio<?php echo $id; ?>" id="radio5<?php echo $id; ?>" value="Hrs">
                            <label for="radio5<?php echo $id; ?>"><?php echo $lang['Hrs']; ?> &nbsp;</label>
                        </div>
                    </td>
                </tr>
            </table>
            <input type="text" class="form-control input-daterange-timepicker" name="daterange[]" value="" id="dateRange<?php echo $id; ?>" style="height: 35px;" />
            <input type="text" class="form-control days" name="days[]" value="" id="days<?php echo $id; ?>" style="display: none; height: 35px;" placeholder="<?php echo $lang['Days']; ?>"/>
            <input type="text" class="form-control days" name="hrs[]" value="" id="hrs<?php echo $id; ?>" style="display: none; height: 35px;" placeholder="<?php echo $lang['Hrs']; ?>"/>
        </div>
    </div>
    <div class="col-sm-1">
        <a href="javascript:(0)" id="hideOwnflowr" class="btn btn-primary" data="<?php echo $id; ?>"><i class="fa fa-minus-circle" title="<?= $lang['Remove'] ?>"></i></a>
    </div>
</div>
<div id="createTaskFlowr<?php echo $id; ?>">

    <div class="form-group">
        <a href="javascript:(0)" id="createOwnflowr" class="btn btn-primary" style="margin-top: -90px; margin-left: 956px;" data="<?php echo $id; ?>"><i class="fa fa-plus-circle" title="<?= $lang['Add_more'] ?>"></i></a>
    </div>

</div>
<script src="assets/plugins/moment/moment.js"></script>
<script src="assets/plugins/timepicker/bootstrap-timepicker.js"></script>
<script src="assets/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
<script src="assets/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
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
    $("input:radio[name='radio<?php echo $id; ?>']").click(function () {

        var val = $(this).val();

        if (val == 'Date') {
            $("#dateRange<?php echo $id; ?>").css("display", "block");
            $("#days<?php echo $id; ?>").css("display", "none");
            $("#hrs<?php echo $id; ?>").css("display", "none");
        }
        if (val == 'Days') {
            $("#dateRange<?php echo $id; ?>").css("display", "none");
            $("#days<?php echo $id; ?>").css("display", "block");
            $("#hrs<?php echo $id; ?>").css("display", "none");
        }
        if (val == 'Hrs') {

            $("#dateRange<?php echo $id; ?>").css("display", "none");
            $("#days<?php echo $id; ?>").css("display", "none");
            $("#hrs<?php echo $id; ?>").css("display", "block");
        }
    });
</script>

<script>
    $("a#createOwnflowr").click(function () {
        var createown = $(this).attr('data');
        // alert(id);

        $.post("application/ajax/createownReviewFlow.php", {ID: createown}, function (result, status) {
            if (status == 'success') {
                $("#createTaskFlowr<?php echo $id; ?>").html(result);
                // alert(result);
            }
        });
    });

    $("a#hideOwnflowr").click(function () {
        $(this).parent().parent("div").remove();
    });

</script>

