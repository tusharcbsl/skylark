<script src="assets/js/jquery.min.js"></script>
<link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
<style>

    body{ font-family: sans-serif;}
    .none{ display:none;}
    .dropdown{color: #444444;font-size:17px;}
    #calender_section{ width:700px; margin:30px auto 0;}
    #calender_section h2{ background-color:#efefef; color:#444444; font-size:17px; text-align:center; line-height:40px;}
    #calender_section h2 a{ color:#F58220; float:none;}
    #calender_section_top{ width:100%; float:left; margin-top:10px;}
    #calender_section_top ul{padding:0; list-style-type:none;}
    #calender_section_top ul li{ float:left; display:block; width:99px; border-right:1px solid #fff;  text-align:center; font-size:14px; min-height:0; background:none; box-shadow:none; margin:0; padding:0; color: #193860;}
    #calender_section_bot{ width:100%; margin-top:10px; float:left; border-left:1px solid #ccc; border-bottom:1px solid #ccc;}
    #calender_section_bot ul{ margin:0; padding:0; list-style-type:none;}
    #calender_section_bot ul li{ float:left; width:99px; height:80px; text-align:center; border-top:1px solid #ccc; border-right:1px solid #ccc; min-height:0; background:none; box-shadow:none; margin:0; padding:0; position:relative;}
    #calender_section_bot ul li span{ margin-top:7px; float:left; margin-left:7px; text-align:center; color: #193860;}

    .grey{ background-color:#3c8dbc !important;}
    .light_sky{ background-color:#f05050!important;}

    /*========== Hover Popup ===============*/
    .date_cell { cursor: pointer; cursor: hand; }
    .date_cell:hover { background: #DDDDDD !important; }
    .date_popup_wrap {
        position: absolute;
        width: 143px;
        height: 115px;
        z-index: 9999;
        top: -115px;
        left:-55px;
        background: transparent url(assets/images/add-new-event.png) no-repeat top left;
        color: #666 !important;
    }
    .events_window {
        overflow: hidden;
        overflow-y: auto;
        width: 133px;
        height: 115px;
        margin-top: 28px;
        margin-left: 25px;
    }
    .event_wrap {
        margin-bottom: 10px; padding-bottom: 10px;
        border-bottom: solid 1px #E4E4E7;
        font-size: 12px;
        padding: 3px;
    }
    .date_window {
        margin-top:20px;
        margin-bottom: 2px;
        padding: 5px;
        font-size: 16px;
        margin-left:9px;
        margin-right:14px
    }
    .popup_event {
        margin-bottom: 2px;
        padding: 2px;
        font-size: 11px;
        width:100%;
        color: #f05050;
    }
    .popup_event a {color: #000000 !important;}
    .packeg_box a {color: #F58220;float: right;}
    a:hover {color: #181919;text-decoration: underline;}

    @media only screen and (min-width:480px) and (max-width:767px) {
        #calender_section{ width:336px;}
        #calender_section_top ul li{ width:47px;}
        #calender_section_bot ul li{ width:47px;}
    }
    @media only screen and (min-width: 320px) and (max-width: 479px) {
        #calender_section{ width:219px;}
        #calender_section_top ul li{ width:30px; font-size:11px;}
        #calender_section_bot ul li{ width:30px;}
        #calender_section_bot{ width:217px;}
        #calender_section_bot ul li{ height:50px;}
    }

    @media only screen and (min-width: 768px) and (max-width: 1023px) {
        #calender_section{ width:530px;}
        #calender_section_top ul li{ width:74px;}
        #calender_section_bot ul li{ width:74px;}
        #calender_section_bot{ width:525px;}
        #calender_section_bot ul li{ height:50px;}
    }
</style>
<script>
    jQuery(document).ready(function () {
        $('.select2').select2();
    });
</script>
<?php
require_once './loginvalidate.php';
require_once './application/config/database.php';
if (isset($_POST['func']) && !empty($_POST['func'])) {
    switch ($_POST['func']) {
        case 'getCalender':
            getCalender($_POST['year'], $_POST['month'], $db_con, $lang);
            break;
        case 'getEvents':
            getEvents($_POST['date']);
            break;
    }
}
/*
 * Get calendar full HTML
 */

function getCalender($year = '', $month = '', $db_con, $lang) {
    $dateYear = ($year != '') ? $year : date("Y");
    $dateMonth = ($month != '') ? $month : date("m");
    $date = $dateYear . '-' . $dateMonth . '-01';
    $currentMonthFirstDay = date("N", strtotime($date));
    $totalDaysOfMonth = cal_days_in_month(CAL_GREGORIAN, $dateMonth, $dateYear);
    $totalDaysOfMonthDisplay = ($currentMonthFirstDay == 7) ? ($totalDaysOfMonth) : ($totalDaysOfMonth + $currentMonthFirstDay);
    $boxDisplay = ($totalDaysOfMonthDisplay <= 35) ? 35 : 42;
    global $db_con;
    ?>

    <div id="calender_section">
        <h2>
            <a href="javascript:void(0);" onclick="getCalendar('calendar_div', '<?php echo date("Y", strtotime($date . ' - 1 Month')); ?>', '<?php echo date("m", strtotime($date . ' - 1 Month')); ?>');"></a>
            <div class="col-sm-6">
                <select name="month_dropdown" class="month_dropdown dropdown select2"><?php echo getAllMonths($dateMonth, $lang); ?></select>
            </div>
            <div class="col-sm-6">
                <select name="year_dropdown" class="year_dropdown dropdown select2"><?php echo getYearList($dateYear); ?></select>
            </div>
            <a href="javascript:void(0);" onclick="getCalendar('calendar_div', '<?php echo date("Y", strtotime($date . ' + 1 Month')); ?>', '<?php echo date("m", strtotime($date . ' + 1 Month')); ?>');"></a>
        </h2>
        <div id="event_list" class="none"></div>
        <div id="calender_section_top">
            <ul>
                <li><?= $lang['sunday']; ?></li>
                <li><?= $lang['monday']; ?></li>
                <li><?= $lang['tuesday']; ?></li>
                <li><?= $lang['wednesday']; ?></li>
                <li><?= $lang['thursday']; ?></li>
                <li><?= $lang['friday']; ?></li>
                <li><?= $lang['saturday']; ?></li>
            </ul>
        </div>
        <div id="calender_section_bot">
            <ul>
                <?php
                $dayCount = 1;
                for ($cb = 1; $cb <= $boxDisplay; $cb++) {
                    if (($cb >= $currentMonthFirstDay + 1 || $currentMonthFirstDay == 7) && $cb <= ($totalDaysOfMonthDisplay)) {
                        //Current date
                        $currentDate = $dateYear . '-' . $dateMonth . '-' . $dayCount;
                        $currentDate = date('Y-m-d', strtotime($currentDate));
                        $eventNum = 0;
                        mysqli_set_charset($db_con, "utf8");
                        $result = mysqli_query($db_con, "SELECT * FROM tbl_events_master WHERE date='$currentDate'") or die("ERROR : DD" . mysqli_error($db_con));
                        $eventNum = mysqli_num_rows($result);
                        //Define date cell color
                        if (strtotime($currentDate) == strtotime(date("Y-m-d"))) {
                            echo '<li date="' . $currentDate . '" class="grey date_cell">';
                        } elseif ($eventNum > 0) {
                            echo '<li date="' . $currentDate . '" class="light_sky date_cell">';
                        } else {
                            echo '<li date="' . $currentDate . '" class="date_cell">';
                        }
                        //Date cell
                        echo '<span>';
                        echo $dayCount;
                        echo '</span>';

                        //Hover event popup
                        if ($eventNum > 0) {

                            echo '<div id="date_popup_' . $currentDate . '" class="date_popup_wrap none">';

                            echo '<div class="date_window">';
                            while ($eventdata = mysqli_fetch_assoc($result)) {
                                echo '<div class="popup_event">' . $eventdata["holiday_name"] . '</div>';
                            }
                            //  echo ($eventNum > 0)?'<a href="javascript:;" onclick="getEvents(\''.$currentDate.'\');">view events</a>':'';
                            //echo '<a href="javascript:;" onclick="removeEvent(\'' . $eventsArray[$currentDate]["id"] . '\');">Remove</a>';
                            echo '</div></div>';
                        }
                        echo '</li>';

                        $dayCount++;
                        ?>
                    <?php } else { ?>
                        <li><span>&nbsp;</span></li>
                        <?php
                    }
                }
                ?>
            </ul>
        </div>
    </div>

    <script type="text/javascript">
        function getCalendar(target_div, year, month, token) {
            $.ajax({
                type: 'POST',
                url: 'calendar.php',
                data: 'func=getCalender&year=' + year + '&month=' + month +'&token='+token,
                success: function (html) {
                    getToken();
                    $('#' + target_div).html(html);
                    
                }
            });
        }

        function getEvents(date, token) {
            $.ajax({
                type: 'POST',
                url: 'calendar.php',
                data: 'func=getEvents&date=' + date+'&token='+token,
                success: function (html) {
                    $('#event_list').html(html);
                    $('#event_list').slideDown('slow');
                     getToken();
                }
            });
        }

        //        function removeEvent(id) {
        //            $.ajax({
        //                type: 'POST',
        //                url: 'calendar.php',
        //                data: 'func=removeEvent&date=' + id,
        //                success: function (html) {
        //                    $('#event_list').html(html);
        //                    $('#event_list').slideDown('slow');
        //                }
        //            });
        //        }

        $(document).ready(function () {
            $('.date_cell').mouseenter(function () {
                date = $(this).attr('date');
                $(".date_popup_wrap").fadeOut();
                $("#date_popup_" + date).fadeIn();
            });
            $('.date_cell').mouseleave(function () {
                $(".date_popup_wrap").fadeOut();
            });
            $('.month_dropdown').on('change', function () {
                var token = $("input[name='token']").val();
                getCalendar('calendar_div', $('.year_dropdown').val(), $('.month_dropdown').val(), token);

            });
            $('.year_dropdown').on('change', function () {
                var token = $("input[name='token']").val();
                getCalendar('calendar_div', $('.year_dropdown').val(), $('.month_dropdown').val(), token);
            });
            $(document).click(function () {
                $('#event_list').slideUp('slow');
            });
        });
    </script>
    <?php
}

/*
 * Get months options list.
 */

function getAllMonths($selected = '', $lang) {
    $options = '';
    $months = array(
        $lang['January'],
        $lang['February'],
        $lang['March'],
        $lang['April'],
        $lang['May'],
        $lang['June'],
        $lang['July'],
        $lang['August'],
        $lang['September'],
        $lang['October'],
        $lang['November'],
        $lang['December'],
    );
    $i = 1;
    foreach ($months as $monthname) {
        // echo '<script> alert("' . $monthname . '");</script>';
        $value = ($i < 10) ? '0' . $i : $i;
        $selectedOpt = ($value == $selected) ? 'selected' : '';
        $options .= '<option value="' . $value . '" ' . $selectedOpt . ' >' . $monthname . '</option>';
        $i++;
    }
//    for ($i = 1; $i <= 12; $i++) {
//        $value = ($i < 10) ? '0' . $i : $i;
//        $selectedOpt = ($value == $selected) ? 'selected' : '';
//        $options .= '<option value="' . $value . '" ' . $selectedOpt . ' >' . date("F", mktime(0, 0, 0, $i + 1, 0, 0)) . '</option>';
//    }
    return $options;
}

/*
 * Get years options list.
 */

function getYearList($selected = '') {
    $options = '';
    for ($i = 2015; $i <= 2025; $i++) {
        $selectedOpt = ($i == $selected) ? 'selected' : '';
        $options .= '<option value="' . $i . '" ' . $selectedOpt . ' >' . $i . '</option>';
    }
    return $options;
}

/*
 * Get events by date
 */

function getEvents($date = '') {
    //Include db configuration file
//    include 'dbConfig.php';
    // require 'application/config/database.php';
    $eventListHTML = '';
    $date = $date ? $date : date("Y-m-d");
    //Get events based on the current date
    $result = $db_con->query("SELECT holiday_name FROM tbl_events_master WHERE date = '" . $date . "'");
    if ($result->num_rows > 0) {
        $eventListHTML = '<h2>Events on ' . date("l, d M Y", strtotime($date)) . '</h2>';
        $eventListHTML .= '<ul>';
        while ($row = $result->fetch_assoc()) {
            $eventListHTML .= '<li>' . $row['holiday_name'] . '</li>';
        }
        $eventListHTML .= '</ul>';
    }
    echo $eventListHTML;
}

//function removeEvent($id) {
//    echo "<script>console.log($id);</script>";
//
//    require 'application/config/database.php';
//    $id = mysqli_escape_string($db_con, $id);
//     $result = $db_con->query("delete from tbl_events_master where id='$id'") or die(mysqli_error);
//    if ($result) {
//        echo '<script>taskSuccess("working-day", "Holiday Removed Successfully!!");</script>';
//    } else {
//        echo '<script>taskFailed("working-day", "Holiday Removed Failed!!")</script>';
//    }
//}
?>

