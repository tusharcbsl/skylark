<?php
require_once '../../sessionstart.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout");
}
if (isset($_SESSION['lang'])) {
    $file = "../../" . $_SESSION['lang'] . ".json";
} else {
    $file = "../../English.json";
}
//for user role
$data = file_get_contents($file);
$lang = json_decode($data, true);

require_once '../config/database.php';
$sameGroupIDs = array();
$group = mysqli_query($db_con, "select * from tbl_bridge_grp_to_um where find_in_set('$_SESSION[cdes_user_id]',user_ids)") or die('Error' . mysqli_error($db_con));
while ($rwGroup = mysqli_fetch_assoc($group)) {
    $sameGroupIDs[] = $rwGroup['user_ids'];
}
$sameGroupIDs = array_unique($sameGroupIDs);
sort($sameGroupIDs);
$sameGroupIDs = implode(',', $sameGroupIDs);
?>
<div class="form-group">
    <div class="row">
        <div class="col-md-6">
            <label class="text-weight"><?php echo $lang['SELECT_EXISTING_WORK_FLOW']; ?></label>
            <select class="select2 form-control" id="wfid" data-style="btn-white" style="" name="wfid">
                <option style="background: #808080; color: #fff;" value="0"><?php echo $lang['SELECT_EXISTING_WORK_FLOW']; ?></option>
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
    <label class="text-alert"><strong><?php echo $lang['Or']; ?></strong></label>
    <br />
    <label class="text-weight"><?php echo $lang['CrT_USRS_FLOW']; ?></label>
    <div class="form-group col-xs-12 well">

        <div class="row">
            <div class="col-sm-1">
                <label for="userName"><?php echo $lang['Order']; ?><span style="color: red;">*</span></label>
                <input type="number" class="form-control" name="taskOrder[]" min="1" style="height:35px;">
            </div> 
            <div class="col-sm-2">
                <label for="userName"><?php echo $lang['Assign_User']; ?><span style="color: red;">*</span></label>
                <select class="selectpicker" data-live-search="true" name="assignUsr[]" data-style="btn-white">
                    <option selected disabled style="background: #808080; color: #fff;"><?php echo $lang['Slt_Usrs']; ?></option>
                    <?php
                    $user = mysqli_query($db_con, "select * from tbl_user_master where user_id in($sameGroupIDs) order by first_name, last_name asc");
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
                    <option selected disabled style="background: #808080; color: #fff;"><?php echo $lang['Slt_Usrs']; ?></option>
                    <?php
                    $user = mysqli_query($db_con, "select * from tbl_user_master where user_id in($sameGroupIDs) order by first_name, last_name asc");
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
                    <option selected disabled style="background: #808080; color: #fff;"><?php echo $lang['Select_User']; ?></option>
                    <?php
                    $user = mysqli_query($db_con, "select * from tbl_user_master where user_id in($sameGroupIDs) order by first_name, last_name asc");
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
            <div class="col-sm-4">
                <div class="form-group row">
                    <label><?= $lang['Deadline']; ?><span style="color: red;">*</span></label>
                    <table>
                        <tr>
                            <td>
                                <div class="radio radio-success">
                                    <input type="radio" name="radio0" id="radio" value="Date" checked>
                                    <label for="radio"> <?php echo $lang['Date']; ?> &nbsp;</label>
                                </div>
                            </td>
                            <td>
                                <div class="radio radio-success">
                                    <input type="radio" name="radio0" id="radio1" value="Days">
                                    <label for="radio1">
                                        <?php echo $lang['Days']; ?> &nbsp;
                                    </label>
                                </div>
                            </td>
                            <td>
                                <div class="radio radio-success">
                                    <input type="radio" name="radio0" id="radio2" value="Hrs">
                                    <label for="radio2"><?php echo $lang['Hrs']; ?> &nbsp;</label>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <input type="text" class="form-control input-daterange-timepicker" name="daterange[]" value="" style="height: 35px;" id="dateRange" />
                    <input type="text" class="form-control days" name="days[]" value="" id="days" style="display: none; height:35px;" placeholder="<?php echo $lang['Days']; ?>"/>
                    <input type="text" class="form-control days" name="hrs[]" value="" id="hrs" style="display: none; height:35px;" placeholder="<?php echo $lang['Hrs']; ?>"/>
                </div>
            </div>

        </div>
        <div id="createTaskFlowr">
            <div class="form-group">
                <a href="#" id="createOwnflowr" class="btn btn-primary" style="margin-top: -88px; margin-left: 915px;" data=""><i class="fa fa-plus-circle" title="<?= $lang['Add_more'] ?>"></i></a>
            </div>
        </div>
        
    </div> 
</div><!--hide this div on select existing wf-->
<button class="btn btn-primary pull-right" type="submit" name="iniFileSub" id="waitOnSubmit"><?php echo $lang['Submit']; ?></button>
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
     $(".select2").select2();
</script>
