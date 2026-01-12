<?php
require_once '../../sessionstart.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout.php");
}
require './../config/database.php';
if (isset($_SESSION['lang'])){
     $file = "../../".$_SESSION['lang'].".json";
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

$id = $_POST['ID'] + 1;
?>

<link href="<?=BASE_URL?>assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
<link href="<?=BASE_URL?>assets/plugins/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
<link href="<?=BASE_URL?>assets/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" />
   <link href="<?=BASE_URL?>assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />


<div class="row">
    <!--div class="col-sm-2 m-t-10"><br/>
         <label for="userName">Order<span style="color: red;">*</span></label>
        <input type="number" class="form-control" name="taskOrder[]" min="1" style="height:35px;">
    </div-->
    <div class="col-sm-2 m-t-10"><br/>
         <label for="userName"><?php echo $lang['Assign_User'];?><span style="color: red;">*</span></label>
        <select class="selectpicker" data-live-search="true" name="assignUsrAdd" data-style="btn-white">
            <option selected disabled style="background: #808080; color: #121213;"><?php echo $lang['Select_User'];?></option>
            <?php
             $user = mysqli_query($db_con, "select * from tbl_user_master where user_id in($sameGroupIDs) order by first_name, last_name asc") or die("Error in user" . mysqli_error($db_con));
            while ($rwUser = mysqli_fetch_assoc($user)) {
                if($rwUser['user_id'] !=1 && $_SESSION['cdes_user_id'] != $rwUser['user_id']){
                ?>
                <option value="<?php echo $rwUser['user_id']; ?>"><?php echo $rwUser['first_name'] . ' ' . $rwUser['last_name']; ?></option>

            <?php
             }
             }     
            ?>
        </select>
    </div>
    <div class="col-sm-3 m-t-30">
         <label for="userName"><?php echo $lang['Alternate_User'];?><span style="color: red;">*</span></label>
     <select class="selectpicker" data-live-search="true" name="altrUsrAdd" data-style="btn-white">
            <option selected disabled style="background: #808080; color: #121213;"><?php echo $lang['Select_User'];?></option>
            <?php
            $user = mysqli_query($db_con, "select * from tbl_user_master where user_id in($sameGroupIDs) order by first_name, last_name asc")or die("Error in user" . mysqli_error($db_con));
            while ($rwUser = mysqli_fetch_assoc($user)) {
                if($rwUser['user_id'] !=1 && $_SESSION['cdes_user_id'] != $rwUser['user_id']){
                ?>
            
                <option value="<?php echo $rwUser['user_id']; ?>"><?php echo $rwUser['first_name'] . ' ' . $rwUser['last_name']; ?></option>

            <?php
            }
            } ?>
        </select>
    </div>
    <div class="col-sm-3 m-t-30">
          <label for="userName"><?php echo $lang['Select_Supervisor'];?><span style="color: red;">*</span></label>
           <select class="selectpicker" data-live-search="true" name="supvsrAdd" data-style="btn-white">
            <option selected disabled style="background: #808080; color: #121213;"><?php echo $lang['Select_Supervisor'];?></option>
            <?php
            $user = mysqli_query($db_con, "select * from tbl_user_master where user_id in($sameGroupIDs) order by first_name, last_name asc")or die("Error in user" . mysqli_error($db_con));
            while ($rwUser = mysqli_fetch_assoc($user)) {
                if($rwUser['user_id'] !=1 && $_SESSION['cdes_user_id'] != $rwUser['user_id']){
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
    <div class="col-sm-4 m-t-5">
        <div class="form-group row">
            <label style="margin-top: 25px;">Deadline<span style="color: red;">*</span></label>
            <table style="float: right; margin-top: 10px;">
                <tr>
                    <td>
                        <div class="radio radio-primary">
                            <input type="radio" name="radio" id="radio3<?php echo $id; ?>" value="Date" checked>
                            <label for="radio3<?php echo $id; ?>"><?php echo $lang['Date'];?> &nbsp;</label>
                        </div>
                    </td>
                    <td>
                        <div class="radio radio-primary">
                            <input type="radio" name="radio" id="radio4<?php echo $id; ?>" value="Days">
                            <label for="radio4<?php echo $id; ?>">
                                <?php echo $lang['Days'];?> &nbsp;
                            </label>
                        </div>
                    </td>
                    <td>
                        <div class="radio radio-primary">
                            <input type="radio" name="radio" id="radio5<?php echo $id; ?>" value="Hrs">
                            <label for="radio5<?php echo $id; ?>"><?php echo $lang['Hrs'];?> &nbsp;</label>
                        </div>
                    </td>
                </tr>
            </table>
            <input type="text" class="form-control input-daterange-timepicker" name="daterangeAdd" value="" id="dateRange" style="height: 35px;" />
            <input type="text" class="form-control days" name="daysAdd" value="" id="days" style="display: none; height: 35px;" placeholder="<?php echo $lang['Days'];?>"/>
            <input type="text" class="form-control days" name="hrsAdd" value="" id="hrs" style="display: none; height: 35px;" placeholder="<?php echo $lang['Hrs'];?>"/>
        </div>
    </div>
    <div class="pull-right">
        <a href="#" id="hideOwnflowr" class="btn btn-primary" style="margin-top: -260px;" data="<?php echo $id; ?>"><?php echo $lang['Cancel'];?></a>
  </div>
</div>
 
<script src="<?=BASE_URL?>assets/plugins/moment/moment.js"></script>
<script src="<?=BASE_URL?>assets/plugins/timepicker/bootstrap-timepicker.js"></script>
<script src="<?=BASE_URL?>assets/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
<script src="<?=BASE_URL?>assets/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
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

<script>
    $("a#hideOwnflowr").click(function(){
    $(this).parent().parent("div").remove();
     $("#createOwnflowr").show();
    });
 $(".select12").select2();
</script>

