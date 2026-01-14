<?php
require_once '../../sessionstart.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout.php");
}
if (isset($_SESSION['lang'])) {
    $file = "../../" . $_SESSION['lang'] . ".json";
} else {
    $file = "../../English.json";
}
$data = file_get_contents($file);
$lang = json_decode($data, true);
require './../config/database.php';

$langDetail = mysqli_query($db_con, "select * from tbl_language where lang_name='" . $_SESSION['lang'] . "'") or die('Error : ' . mysqli_error($db_con));
$langDetail = mysqli_fetch_assoc($langDetail);

//for user role
$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));
$rwgetRole = mysqli_fetch_assoc($chekUsr);

// echo $rwgetRole['dashboard_mydms']; die;
if ($rwgetRole['workflow_step'] != '1') {
    header('Location: ../../index');
}
if (!isset($_POST['ID'], $_POST['token'])) {
    echo "Unauthorised access !";
    exit;
}
if (intval($_POST['ID'])) {
    $tid = preg_replace("/[^0-9 ]/", "", $_POST['ID']);
    mysqli_set_charset($db_con, "utf8");
    $getTask = mysqli_query($db_con, "select * from tbl_task_master where task_id='$tid'") or die('Error in stepfetch:' . mysqli_error($db_con));
    $rwgetTask = mysqli_fetch_assoc($getTask);
?>
    <link href="assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
    <link href="assets/plugins/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
    <link href="assets/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" />
    <div class="row">
        <div class="col-md-6">
            <div class="form-group row">
                <div class="col-md-4">
                    <label for="userName"><?php echo $lang['Stp_Nmber']; ?></label>
                </div>
                <div class="col-md-8">
                    <?php
                    $getStepId = mysqli_query($db_con, "select * from tbl_step_master where step_id='$rwgetTask[step_id]'") or die('Error in stepfetch:' . mysqli_error($db_con));
                    $rwgetStepId = mysqli_fetch_assoc($getStepId);
                    ?>
                    <label><?php echo $rwgetStepId['step_order']; ?></label>
                    <input type="hidden" value="<?php echo $rwgetTask['step_id']; ?>" name="stepid" />
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group row">
                <div class="col-md-4">
                    <label for="userName"><?php echo $lang['Tsk_Ordr']; ?><span style="color: red;">*</span></label>
                </div>
                <div class="col-md-8">
                    <input type="number" class="form-control" name="taskOrder" value="<?php echo $rwgetTask['task_order']; ?>" required>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group row">
                <div class="col-md-4">
                    <label for="userName"><?php echo $lang['Task_Name']; ?><span style="color: red;">*</span></label>
                </div>
                <div class="col-md-8">
                    <input type="text" class="form-control translatetext" name="taskName" id="taskName" value="<?php echo $rwgetTask['task_name']; ?>" maxlength="40" required>
                    <input type="hidden" class="form-control" name="taskId" value="<?php echo $rwgetTask['task_id']; ?>" required>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group row">
                <div class="col-md-4">
                    <label for="userName"><?php echo $lang['Priority']; ?><span style="color: red;">*</span></label>
                </div>
                <div class="col-md-8">
                    <select class="form-control selectpicker" data-live-search="true" id="" name="prity" required>
                        <option selected disabled style="background: #808080; color: #121213;"><?php echo $lang['Slt_Prorty_Lvl']; ?></option>
                        <option value="3" <?php
                                            if ($rwgetTask['priority_id'] == '3') {
                                                echo 'selected';
                                            }
                                            ?>><?php echo $lang['Normal']; ?></option>
                        <option value="2" <?php
                                            if ($rwgetTask['priority_id'] == '2') {
                                                echo 'selected';
                                            }
                                            ?>><?php echo $lang['Medium']; ?></option>
                        <option value="1" <?php
                                            if ($rwgetTask['priority_id'] == '1') {
                                                echo 'selected';
                                            }
                                            ?>><?php echo $lang['Urgent']; ?></option>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group row">
                <div class="col-md-4">
                    <label><?php echo $lang['Slct_Dadlne']; ?><!--<span style="color: red;">*</span>--></label>
                </div>
                <div class="col-md-8">
                    <table>
                        <tr>
                            <!--td>
                                <div class="radio radio-primary">
                                    <input type="radio" name="radio" id="radio" value="Date" <?php
                                                                                                //                                    if ($rwgetTask['deadline_type'] == 'Date') {
                                                                                                //                                        echo'checked';
                                                                                                //                                    }
                                                                                                ?>>
                                    <label for="radio">
                                        <?php //echo $lang['Date']; 
                                        ?> &nbsp;&nbsp;&nbsp;
                                    </label>
                                </div>
                            </td-->

                            <td>
                                <div class="radio radio-primary">
                                    <input type="radio" name="radio" id="radio1" value="Days" <?php
                                                                                                if ($rwgetTask['deadline_type'] == 'Days') {
                                                                                                    echo 'checked';
                                                                                                }
                                                                                                ?>>
                                    <label for="radio1">
                                        <?php echo $lang['Days']; ?> &nbsp;&nbsp;&nbsp;
                                    </label>
                                </div>
                            </td>
                            <td>
                                <div class="radio radio-primary">
                                    <input type="radio" name="radio" id="radio2" value="Hrs" <?php
                                                                                                if ($rwgetTask['deadline_type'] == 'Hrs') {
                                                                                                    echo 'checked';
                                                                                                }
                                                                                                ?>>
                                    <label for="radio2">
                                        <?php echo $lang['Hrs']; ?> &nbsp;&nbsp;&nbsp;
                                    </label>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <?php
                    if ($rwgetTask['deadline_type'] == 'Date') {
                        $startDate = date('d-m-Y h:i A', strtotime($date));
                        $endDate = date('d-m-Y h:i A', (strtotime($date) + $rwgetTask['deadline'] * 60 * 60));
                        $daterange = $startDate . ' To ' . $endDate;
                        $display = "";
                        $display1 = "display:none";
                        $display2 = "display:none";
                    } else if ($rwgetTask['deadline_type'] == 'Days') {
                        $display = "display:none";
                        $display1 = "";
                        $display2 = "display:none";
                        $val = $rwgetTask['deadline'];
                    } else if ($rwgetTask['deadline_type'] == 'Hrs') {
                        $display = "display:none";
                        $display1 = "display:none";
                        $display2 = "";
                        $val1 = $rwgetTask['deadline'] / 60;
                    }
                    ?>
                    <!--input type="text" class="form-control input-daterange-timepicker" name="daterange" value="<?php //echo $daterange 
                                                                                                                    ?>" id="dateRange" style="<?php //echo $display; 
                                                                                                                                                                    ?>"-->
                    <input type="text" class="form-control days " name="days" value="<?php echo $val; ?>" id="days" style="<?php echo $display1; ?>" placeholder="Days" />
                    <input type="text" class="form-control days" name="hrs" value="<?php echo $val1; ?>" id="hrs" style="<?php echo $display2; ?>" placeholder="Hrs" />
                </div>
            </div>
        </div>
        <?php
        $wrkh = explode('To', $rwgetTask['working_hour']);
        $wfrm = $wrkh[0];
        $wto = $wrkh[1];
        ?>
        <!--div class="col-md-6">
            <div class="form-group row">
                <div class="col-md-4">
                    <label for="userName"><?php echo $lang['Wrk_Hrs']; ?></label>
                </div>
                <div class="col-md-8">
                    <div class="input-group m-b-15">

                        <div class="bootstrap-timepicker">
                            <input id="timepicker" type="text" class="form-control" name="frm" value="<?php //echo $wfrm; 
                                                                                                        ?>" readonly >
                        </div>
                        <span class="input-group-addon bg-custom b-0 text-white"><i class="glyphicon glyphicon-time"></i></span>
                    </div>
                    <?php //echo $lang['to']; 
                    ?>
                    <div class="input-group">

                        <div class="bootstrap-timepicker">
                            <input id="timepicker2" type="text" class="form-control" name="to" value="<?php //echo $wto; 
                                                                                                        ?>" readonly >
                        </div>
                        <span class="input-group-addon bg-custom b-0 text-white"><i class="glyphicon glyphicon-time"></i></span>
                    </div>
                </div>
            </div>
        </div-->
        <div class="col-md-6">
            <div class="form-group row" id="multiselect">
                <div class="col-md-4">
                    <label for="userName"><?php echo $lang['User_Assign']; ?><span style="color: red;">*</span></label>
                </div>
                <?php
                $sameGroupIDs = array();
                $group = mysqli_query($db_con, "select * from tbl_bridge_grp_to_um where find_in_set('$_SESSION[cdes_user_id]',user_ids)") or die('Error' . mysqli_error($db_con));
                while ($rwGroup = mysqli_fetch_assoc($group)) {
                    $sameGroupIDs[] = $rwGroup['user_ids'];
                }
                $sameGroupIDs = array_unique($sameGroupIDs);
                sort($sameGroupIDs);
                $sameGroupIDs = implode(',', $sameGroupIDs);
                ?>
                <div class="col-md-8">
                    <select class="selectpicker" data-live-search="true" name="asiusr" data-style="btn-white" required>
                        <option selected disabled style="background: #808080; color: #121213;"><?php echo $lang['User_Assign']; ?></option>
                        <?php
                        mysqli_set_charset($db_con, "utf8");
                        $user = mysqli_query($db_con, "select * from tbl_user_master where user_id in($sameGroupIDs)");
                        while ($rwUser = mysqli_fetch_assoc($user)) {
                            if ($rwUser['user_id'] != 1 && $_SESSION['cdes_user_id'] != $rwUser['user_id']) {
                        ?>
                                <option <?php
                                        if ($rwgetTask['assign_user'] == $rwUser['user_id']) {
                                            echo 'selected';
                                        }
                                        ?> value="<?php echo $rwUser['user_id']; ?>"><?php echo $rwUser['first_name'] . ' ' . $rwUser['last_name']; ?></option>

                        <?php
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group row">
                <div class="col-md-4">
                    <label for="userName"><?php echo $lang['Alternate_User']; ?></label>
                </div>
                <div class="col-md-8">

                    <select class="selectpicker" data-live-search="true" name="altrusr" data-style="btn-white">
                        <option selected style="background: #808080; color: #121213;"><?php echo $lang['Sl_Altnte_Ur']; ?></option>
                        <?php
                        mysqli_set_charset($db_con, "utf8");
                        $user = mysqli_query($db_con, "select * from tbl_user_master where user_id in($sameGroupIDs)");
                        while ($rwUser = mysqli_fetch_assoc($user)) {
                            if ($rwUser['user_id'] != 1 && $_SESSION['cdes_user_id'] != $rwUser['user_id']) {
                        ?>
                                <option <?php
                                        if ($rwgetTask['alternate_user'] == $rwUser['user_id']) {
                                            echo 'selected';
                                        }
                                        ?> value="<?php echo $rwUser['user_id']; ?>"><?php echo $rwUser['first_name'] . ' ' . $rwUser['last_name']; ?></option>

                        <?php
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group row">
                <div class="col-md-4">
                    <label for="userName"><?php echo $lang['Supervisors']; ?><span style="color: red;">*</span></label>
                </div>
                <div class="col-md-8">

                    <select class="selectpicker" data-live-search="true" name="supvsr" data-style="btn-white" required>
                        <option selected disabled style="background: #808080; color: #121213;"><?php echo $lang['Slt_Supervisors']; ?></option>
                        <?php
                        mysqli_set_charset($db_con, "utf8");
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
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group row">
                <div class="col-md-4">
                    <label for="userName"><?php echo $lang['Tsk_Instrction']; ?><!--<span style="color: red;">*</span>--></label>
                </div>
                <div class="col-md-8">
                    <textarea class="form-control translatetext" rows="2" name="taskIns"><?php echo $rwgetTask['task_instructions']; ?></textarea>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group row">
                <div class="col-md-6">
                    <label for="action"><?php echo $lang['Slt_Act']; ?><span style="color:red;">*</span></label>
                </div>
                <select class="select3 select2-multiple" name="action[]" multiple="multiple" multiple data-placeholder="Select Action" parsley-trigger="change" id="group" required>
                    <?php
                    $dbaction = explode(",", $rwgetTask['actions']);
                    //                    $actions = array('Approved', 'Rejected', 'Aborted', 'Processed', 'Complete', 'Done');
                    //                    foreach ($actions as $action) {
                    //                        if (in_array($action, $dbaction)) {
                    //                            echo'<option value="' . $action . '" selected>' . $action . '</option>';
                    //                        } else {
                    //                            echo'<option value="' . $action . '">' . $action . '</option>';
                    //                        }
                    //                    }
                    ?>
                    <option value="Approved" <?php if (in_array('Approved', $dbaction)) {
                                                    echo "selected";
                                                } ?>><?php echo $lang['Approved']; ?></option>
                    <option value="Rejected" <?php if (in_array('Rejected', $dbaction)) {
                                                    echo "selected";
                                                } ?>><?php echo $lang['Rejected']; ?></option>
                    <option value="Aborted" <?php if (in_array('Aborted', $dbaction)) {
                                                echo "selected";
                                            } ?>><?php echo $lang['Aborted']; ?></option>
                    <option value="Processed" <?php if (in_array('Processed', $dbaction)) {
                                                    echo "selected";
                                                } ?>><?php echo $lang['Processed']; ?></option>
                    <option value="Complete" <?php if (in_array('Complete', $dbaction)) {
                                                    echo "selected";
                                                } ?>><?php echo $lang['Complete']; ?></option>
                    <option value="Done" <?php if (in_array('Done', $dbaction)) {
                                                echo "selected";
                                            } ?>><?php echo $lang['Done']; ?></option>


                </select>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="form-group row">
                <div class="col-md-4">
                    <label for="userName"><?php echo $lang['task_request_on']; ?></label>
                </div>
                <div class="col-md-8">
                    <select class="form-control" id="" name="enable_edit_btn" required>
                        <option><?php echo "Select Option" ?></option>
                        <option value="1"
                            <?= ($rwgetTask['enable_edit_btn'] == '1') ? 'selected' : '' ?>>
                            <?php echo $lang['edit_form']; ?>
                        </option>

                    </select>
                </div>
            </div>
        </div>
    </div>
    </div>
    <script src="assets/plugins/moment/moment.js"></script>
    <script src="assets/plugins/timepicker/bootstrap-timepicker.js"></script>
    <script src="assets/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
    <script src="assets/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
    <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
    <script type="text/javascript">
        $("input.days").keypress(function(e) {
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


        jQuery(document).ready(function() {
            $('.selectpicker').selectpicker();

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
        }, function(start, end, label) {
            console.log(start.toISOString(), end.toISOString(), label);
            $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        });
    </script>
    <!-- jQuery  -->
    <script>
        $("input:radio[name='radio']").click(function() {

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
        $(document).ready(function() {
            //$('form').parsley();

            $('input, textarea').keyup(function() {
                var groupName = $(this).val();
                re = /[`1234567890~!@#$%^&*()|+\=?;:'",.<>\{\}\[\]\\\/]/gi;
                var isSplChar = re.test(groupName);
                if (isSplChar) {
                    var no_spl_char = groupName.replace(/[`~!@#$%^&*()|+\=?;:'",.<>\{\}\[\]\\\/]/gi, '');
                    $(this).val(no_spl_char);
                }
            });

        });
        $(".select3").select2();
    </script>

    <script type="text/javascript">
        google.load("elements", "1", {
            packages: "transliteration"
        });

        function onLoad() {
            var langcode = '<?php echo $langDetail['lang_code']; ?>';
            var options = {
                sourceLanguage: 'en',
                destinationLanguage: [langcode],
                shortcutKey: 'ctrl+g',
                transliterationEnabled: true
            };

            var control =
                new google.elements.transliteration.TransliterationControl(options);
            //var ids = ["groupName12"];
            var elements = document.getElementsByClassName('translatetext');
            control.makeTransliteratable(elements);
        }
        $.getScript('assets/js/test.js', function() {
            // Call custom function defined in script
            onLoad();
        });
        google.setOnLoadCallback(onLoad);
    </script>
<?php } ?>