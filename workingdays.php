<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    require './sessionstart.php';
    require_once './application/config/database.php';
    require_once './application/pages/head.php';
    require_once 'calendar.php';
    //user role wise view
    $chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

    $rwgetRole = mysqli_fetch_assoc($chekUsr);

    // echo $rwgetRole['dashboard_mydms']; die;
//    if ($rwgetRole['holidays'] != '1') {
//        header('Location: ./index');
//    }
    ?>


    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <!--    <link rel="stylesheet" href="assets/css/calender.css">-->
    <!--    <link rel="stylesheet" href="assets/js/daterangepicker/daterangepicker.css" />-->
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
                        <div class="row">

                            <ol class="breadcrumb">
                                <li><a href="workingdays">Holidays Sheet</a></li>
                                <li class="active"><i class="fa fa-inbox"></i> Holidays of <?php echo date('F'); ?></li>
                            </ol>

                        </div>
                        <!-- Modal -->
                        <div class="modal fade" id="myModal" role="dialog">
                            <div class="modal-dialog">

                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title">Add Holiday</h4>
                                    </div>
                                    <form method="post">
                                        <div class="modal-body">
                                            <div class="row">
                                                <label>Select Holiday Date</label>
                                                <div class="input-group">
                                                    <input type="text" value=""  class="form-control datepicker" placeholder="Select Date" name="hdate" >
                                                    <span class="input-group-addon bg-custom b-0 text-white"><i class="icon-calender"></i></span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <label> Holiday Name</label>

                                                <input type="text" value=""  class="form-control" placeholder="Holiday name" name="hname" id="hday">


                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            <button type="submit" name="submit" class="btn btn-primary" >Submit</button>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                        <!-- Page-Title -->
                        <div class="row">

                            <section class="content">
                                <div class="row">



                                </div>
                                <div class="box">
                                    <div class="box-header">




                                        <a href="javascript:(0)" class="btn btn-primary pull-right " id="attendance"  data-toggle="modal" data-target="#myModal">ADD Holiday<i class="fa fa-plus-square m-l-10"></i></a>




                                    </div>
                                    <!-- /.box-header -->
                                    <div class="box-body" style="overflow: auto">
                                        <div class="box-body">
                                            <div class="row">
                                                <!-- Left col -->
                                                <section class="col-lg-12">
                                                    <!-- Custom tabs (Charts with tabs)-->
                                                    <div class="row nav-tabs-custom p-b-30">
                                                        <!-- Tabs within a box -->

                                                        <div class="col-md-7">
                                                            <div id="calendar_div">
                                                                <?php echo getCalender(); ?>
                                                            </div>
                                                            <!--                                                            <div class="tab-content ">
                                                            <?php
//                                                                if (isset($_GET['month']) && isset($_GET['year'])) {
//                                                                    $month = $_GET['month'];
//                                                                    $year = $_GET['year'];
//                                                                } else {
//                                                                    $month = date('m');
//                                                                    $year = date('Y');
//                                                                }
//                                                                $arrDay = array();
//                                                                $arrMonth = array();
//                                                                $arrYear = array();
//                                                                $arrStatus = array();
//                                                                $inIP = array();
//                                                                $outIP = array();
//                                                                $absent = 0;
//                                                                $present = 0;
//                                                                $halfday = 0;
//                                                                $leave = 0;
//                                                                $short = 0;
//                                                                $od = 0;
//                                                                $attn = mysqli_query($db_con, "select * from attendance where  Month='$month' and Year='$year'");
//                                                                while ($rwAttn = mysqli_fetch_array($attn)) {
//                                                                    array_push($arrDay, $rwAttn["Day"]);
//                                                                    array_push($arrMonth, $rwAttn['Month']);
//                                                                    array_push($arrYear, $rwAttn['Year']);
//                                                                    $arrStatus[$rwAttn['Day']] = $rwAttn['Status'];
//                                                                    $intime[$rwAttn['Day']] = $rwAttn['InDateTime'];
//                                                                    $outTime[$rwAttn['Day']] = $rwAttn['OutDateTime'];
//                                                                    $inIP[$rwAttn['Day']] = $rwAttn['InIP'];
//                                                                    $outIP[$rwAttn['Day']] = $rwAttn['OutIP'];
//                                                                    if ($rwAttn['Status'] == 1) {
//                                                                        $present = $present + 1;
//                                                                    }
//                                                                }
//                                                                //$leaves = mysqli_query($db_con, "select * from leave_system where UserID='$_SESSION[cdes_user_id]'");
//
//                                                                require_once './application/pages/calendar.php';
//                                                                $calendar = new Calendar();
//
//                                                                echo $calendar->show($arrDay, $arrStatus, $intime, $outTime, $inIP, $outIP);
//                                                                $daysCount = cal_days_in_month(CAL_GREGORIAN, $month, $year);
//                                                                $sDate = $year . '-' . $month . '-' . '01';
//                                                                $eDate = $year . '-' . $month . '-' . $daysCount;
//                                                                $workingDays = number_of_working_days($sDate, $eDate);
                                                            ?>  
                                                                                                                        </div>-->

                                                        </div>
                                                        <div class="col-md-5">
                                                            <div class="tab-content ">




                                                                <!-- /.progress-group -->
                                                                <?php
//The function returns the no. of business days between two dates and it skips the holidays
//                                                                function getWorkingDays($startDate, $endDate, $holidays) {
//                                                                    // do strtotime calculations just once
//                                                                    $endDate = strtotime($endDate);
//                                                                    $startDate = strtotime($startDate);
//
//
//                                                                    //The total number of days between the two dates. We compute the no. of seconds and divide it to 60*60*24
//                                                                    //We add one to inlude both dates in the interval.
//                                                                    $days = ($endDate - $startDate) / 86400 + 1;
//
//                                                                    $no_full_weeks = floor($days / 7);
//                                                                    $no_remaining_days = fmod($days, 7);
//
//                                                                    //It will return 1 if it's Monday,.. ,7 for Sunday
//                                                                    $the_first_day_of_week = date("N", $startDate);
//                                                                    $the_last_day_of_week = date("N", $endDate);
//
//                                                                    //---->The two can be equal in leap years when february has 29 days, the equal sign is added here
//                                                                    //In the first case the whole interval is within a week, in the second case the interval falls in two weeks.
//                                                                    if ($the_first_day_of_week <= $the_last_day_of_week) {
//                                                                        if ($the_first_day_of_week <= 6 && 6 <= $the_last_day_of_week)
//                                                                            $no_remaining_days--;
//                                                                        if ($the_first_day_of_week <= 7 && 7 <= $the_last_day_of_week)
//                                                                            $no_remaining_days--;
//                                                                    }
//                                                                    else {
//                                                                        // (edit by Tokes to fix an edge case where the start day was a Sunday
//                                                                        // and the end day was NOT a Saturday)
//                                                                        // the day of the week for start is later than the day of the week for end
//                                                                        if ($the_first_day_of_week == 7) {
//                                                                            // if the start date is a Sunday, then we definitely subtract 1 day
//                                                                            $no_remaining_days--;
//
//                                                                            if ($the_last_day_of_week == 6) {
//                                                                                // if the end date is a Saturday, then we subtract another day
//                                                                                $no_remaining_days--;
//                                                                            }
//                                                                        } else {
//                                                                            // the start date was a Saturday (or earlier), and the end date was (Mon..Fri)
//                                                                            // so we skip an entire weekend and subtract 2 days
//                                                                            $no_remaining_days -= 2;
//                                                                        }
//                                                                    }
//
//                                                                    //The no. of business days is: (number of weeks between the two dates) * (5 working days) + the remainder
////---->february in none leap years gave a remainder of 0 but still calculated weekends between first and last day, this is one way to fix it
//                                                                    $workingDays = $no_full_weeks * 6;
//                                                                    if ($no_remaining_days > 0) {
//                                                                        $workingDays += $no_remaining_days;
//                                                                    }
//
//                                                                    //We subtract the holidays
//                                                                    foreach ($holidays as $holiday) {
//                                                                        $time_stamp = strtotime($holiday);
//                                                                        //If the holiday doesn't fall in weekend
//                                                                        if ($startDate <= $time_stamp && $time_stamp <= $endDate && date("N", $time_stamp) != 6 && date("N", $time_stamp) != 7)
//                                                                            $workingDays--;
//                                                                    }
//
//                                                                    return $workingDays;
//                                                                }
//
//                                                                function number_of_working_days($from, $to) {
//                                                                    $workingDays = [1, 2, 3, 4, 5, 6]; # date format = N (1 = Monday, ...)
//                                                                    $holidayDays = ['*-12-25', '*-01-01', '2017-01-1']; # variable and fixed holidays
//
//                                                                    $from = new DateTime($from);
//                                                                    $to = new DateTime($to);
//                                                                    $to->modify('+1 day');
//                                                                    $interval = new DateInterval('P1D');
//                                                                    $periods = new DatePeriod($from, $interval, $to);
//
//                                                                    $days = 0;
//                                                                    foreach ($periods as $period) {
//                                                                        if (!in_array($period->format('N'), $workingDays))
//                                                                            continue;
//                                                                        if (in_array($period->format('Y-m-d'), $holidayDays))
//                                                                            continue;
//                                                                        if (in_array($period->format('*-m-d'), $holidayDays))
//                                                                            continue;
//                                                                        $days++;
//                                                                    }
//                                                                    return $days;
//                                                                }
//
////Example:
//
//                                                                $holidays = array();
//                                                                $sDate = $year . '-' . $month . '-' . '01';
//                                                                $eDate = $year . '-' . $month . '-' . $daysCount;
//                                                                getWorkingDays($sDate, $eDate, $holidays); // .times holidays deepak agrawal/ prashant 
//                                                                
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- /.nav-tabs-custom -->
                                                </section>

                                            </div>

                                        </div>
                                    </div>
                                    <!-- /.box-body -->
                                </div>
                        </div> <!-- container -->

                    </div> <!-- content -->

                    <?php require_once './application/pages/footer.php'; ?>
                </div>          
            </div>
        </div>
        <!-- END wrapper -->
        <?php require_once './application/pages/footerForjs.php'; ?>
        <script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
<!--           <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
        <script src="assets/js/daterangepicker/daterangepicker.js"></script>-->
        <link href="assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet">
        <script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
        <script>
//             $(function () {
//                $('#reservationtime').daterangepicker({timePicker: true, timePickerIncrement: 10, locale: {format: 'YYYY/MM/DD h:mm A'}});
//            });

// Get the modal for apply leave
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


            $('#hday').bind("keyup change", function ()
            {
                var GrpNme = $(this).val();
                re = /[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi;
                var isSplChar = re.test(GrpNme);
                if (isSplChar)
                {
                    var no_spl_char = GrpNme.replace(/[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi, '');
                    $(this).val(no_spl_char);
                }
            });
            $('#hday').bind(function () {
                $(this).val($(this).val().replace(/[<>]/g, ""))
            });
        </script>

    </body>
</html>
<?php
if (isset($_POST['submit'], $_POST['token'])) {
    $date = $_POST['hdate'];
    $hname = $_POST['hname'];
//    $newdatearray = explode("-", $date);
//    $year = $newdatearray[0];
//    $month = $newdatearray[1];
//    $day = $newdatearray[2];
    $qry = mysqli_query($db_con, "insert into  events(`title`,`date`,`created`,`modified`) values('$hname','$date','$date','$date')") or die(mysqli_error($db_con));
    if ($qry) {
        echo '<script>taskSuccess("workingdays", "Holiday Submitted Successfully!!");</script>';
    } else {
        echo '<script>taskFailed("workingdays", "Holiday Added Failed")</script>';
    }
}
?>
