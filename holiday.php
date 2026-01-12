<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    require_once './application/config/database.php';
    require_once './application/pages/head.php';
	
    if ($rwgetRole['view_holiday'] != '1') {
        header('Location: ./index');
    }
    ?>
    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" />
    <!-- Plugin Css-->
    <link rel="stylesheet" href="assets/plugins/magnific-popup/css/magnific-popup.css" />
    <link rel="stylesheet" href="assets/plugins/jquery-datatables-editable/datatables.css" />
    <link href="assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet">

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
                    <div class="container">
                        <!-- Page-Title -->
                        <div class="row">
                            <div class="col-sm-12">
                                <ol class="breadcrumb">
                                    <li>
                                        <a href="#"><?= $lang['holiday_manager'] ?></a>
                                    </li>
                                    <li class="active">
                                        <a href="holiday"><?= $lang['holiday_list'] ?></a>
                                    </li>
                                     <li> <a href="#" data-toggle="modal" data-target="#help-modal" id="helpview" data="38" title="<?= $lang['help']; ?>"><i class="fa fa-question-circle"></i> </a></li>
                                    <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" holiday_name="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                                </ol>
                            </div>
                        </div>

                        <div class="panel">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <div class="col-sm-10">
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control translatetext" name="shared_doc" id="holi" value="<?php echo xss_clean(trim($_GET['holi'])); ?>" parsley-trigger="change" placeholder="<?php echo $lang['Search']; ?>" required />
                                        </div>
                                        <div class="col-sm-3">
                                            <button type="submit" class="btn btn-primary" id="holiday"><i class="fa fa-search"></i> <?php echo $lang['Search']; ?></button>
                                            <a href="holiday" class="btn btn-warning"><i class="fa fa-refresh"></i>  <?php echo $lang['Reset']; ?></a>
                                        </div>
                                    </div>
                                    <?php if ($rwgetRole['add_holiday'] == '1') { ?>
                                        <div class="col-sm-2">
                                            <div class="">
                                                <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right" data-toggle="modal" data-target="#group-add"><?= $lang['add_holiday'] ?> <i class="fa fa-calendar-plus-o"></i></a>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="panel-body">
                                    <?php
                                    $where = "";
									$searchHoli = "";
                                    if (isset($_GET['holi']) && !empty($_GET['holi'])) {
                                        $searchHoli = xss_clean(trim($_GET['holi']));
                                        $searchHoli = mysqli_real_escape_string($db_con, $searchHoli);
                                        $where .= " where holiday_name like'%$searchHoli%' or date like'%$searchHoli%'";
                                    }

                                    $sql = "SELECT * FROM  tbl_events_master $where";
                                    mysqli_set_charset($db_con, "utf8");
                                    $retval = mysqli_query($db_con, $sql); // or die('Could not get data: ' . mysqli_error($db_con));
                                    $foundnum = mysqli_num_rows($retval);
                                    if ($foundnum > 0) {
                                        $StartPoint = preg_replace("/[^0-9]/", "", $_GET['limit']); //filter limit from all special chars
                                        if (is_numeric($StartPoint)) {
                                            $per_page = $StartPoint;
                                        } else {
                                            $per_page = 10;
                                        }
                                        $start = preg_replace("/[^0-9]/", "", $_GET['start']); //filter start variable
                                        $start = isset($start) ? ($start>0)? $start:0 : 0;
                                        $max_pages = ceil($foundnum / $per_page);
                                        if (!$start) {
                                            $start = 0;
                                        }
                                        $limit = preg_replace("/[^0-9]/", "", $_GET['limit']);
                                        ?>
                                        <label><?php echo $lang['show_lst']; ?> </label>
                                        <select id="limit" class="input-sm m-b-10">
                                            <option value="10" <?php
                                            if ($limit == 10) {
                                                echo 'selected';
                                            }
                                            ?>>10</option>
                                            <option value="25" <?php
                                            if ($limit == 25) {
                                                echo 'selected';
                                            }
                                            ?>>25</option>
                                            <option value="50" <?php
                                            if ($limit == 50) {
                                                echo 'selected';
                                            }
                                            ?>>50</option>
                                            <option value="250" <?php
                                            if ($limit == 250) {
                                                echo 'selected';
                                            }
                                            ?>>250</option>
                                            <option value="500" <?php
                                            if ($limit == 500) {
                                                echo 'selected';
                                            }
                                            ?>>500</option>
                                        </select> 
                                        <label><?= $lang['holiday_list'] ?></label>

                                        <div class="record pull-right m-b-10">
                                            <?php echo $start + 1 ?> <?php echo $lang['To']; ?> <?php
                                            if ($start + $per_page > $foundnum) {
                                                echo $foundnum;
                                            } else {
                                                echo ($start + $per_page);
                                            };
                                            ?> <span><?php echo $lang['ttl_recrds']; ?>: <?php echo $foundnum; ?></span>
                                        </div>

                                        <table class="table table-striped table-bordered js-sort-table">
                                            <thead>
                                                <tr>
                                                    <th class="sort-js-none" ><?= $lang['SNO'] ?></th>
                                                    <th><?= $lang['holiday_name'] ?></th>
                                                    <th class="sort-js-date" ><?= $lang['holiday_date'] ?></th>
                                                    <th class="sort-js-none" ><?= $lang['Actions'] ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                <?php
                                                $i = 1;
                                                $i += $start;
                                                $Holidy = mysqli_query($db_con, "SELECT * FROM tbl_events_master $where order by holiday_name asc LIMIT $start, $per_page");
                                                while ($rwHolidy = mysqli_fetch_assoc($Holidy)) {
                                                    ?>
                                                    <tr class="gradeX">
                                                        <td><?php echo $i . '.'; ?> </td>
                                                        <td><?php echo $rwHolidy['holiday_name']; ?></td>
                                                        <td><?php echo $rwHolidy['date']; ?></td>


                                                        <td class="actions">
                                                            <?php if ($rwgetRole['edit_holiday'] == '1') { ?>
                                                                <a href="#" class="on-default edit-row btn btn-primary" data-toggle="modal" data-target="#con-close-modal" id="editHolidy" data="<?php echo $rwHolidy['id']; ?>" ><?= $lang['Modify_column']; ?> <i class="fa fa-edit"></i></a>
                                                            <?php } ?>
                                                            <?php if ($rwgetRole['delete_holiday'] == '1') { ?>
                                                                <a href="#" class="on-default remove-row btn btn-danger" data-toggle="modal" data-target="#dialog" id="removeHolidy" data="<?php echo $rwHolidy['id']; ?>"><?= $lang['Delete']; ?> <i class="fa fa-trash-o"></i></a>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                    $i++;
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                        <?php
                                        echo "<center>";
                                        $prev = $start - $per_page;
                                        $next = $start + $per_page;

                                        $adjacents = 3;
                                        $last = $max_pages - 1;
                                        if ($max_pages > 1) {
                                            ?>

                                            <ul class='pagination strgePage'>
                                                <?php
                                                //previous button
                                                if (!($start <= 0)){
                                                    echo " <li><a href='?start=$prev&limit=$per_page&limit=" . $limit . "&holi=" . $searchHoli . "'>$lang[Prev]</a> </li>";
                                                }else{
                                                    echo " <li class='disabled'><a href='javascript:void(0)'>$lang[Prev]</a> </li>";
                                                }
                                                //pages 
                                                if ($max_pages < 7 + ($adjacents * 2)) {   //not enough pages to bother breaking it up
                                                    $i = 0;
                                                    for ($counter = 1; $counter <= $max_pages; $counter++) {
                                                        if ($i == $start) {
                                                            echo "<li class='active'><a href='?start=$i&limit=$per_page&limit=" . $limit . "&holi=" . $searchHoli . "'><b>$counter</b></a> </li>";
                                                        } else {
                                                            echo "<li><a href='?start=$i&limit=$per_page&limit=" . $limit . "&holi=" . $searchHoli . "'>$counter</a></li> ";
                                                        }
                                                        $i = $i + $per_page;
                                                    }
                                                } elseif ($max_pages > 5 + ($adjacents * 2)) {    //enough pages to hide some
                                                    //close to beginning; only hide later pages
                                                    if (($start / $per_page) < 1 + ($adjacents * 2)) {
                                                        $i = 0;
                                                        for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                                                            if ($i == $start) {
                                                                echo " <li class='active'><a href='?start=$i&limit=$per_page&limit=" . $limit . "&holi=" . $searchHoli . "'><b>$counter</b></a></li> ";
                                                            } else {
                                                                echo "<li> <a href='?start=$i&limit=$per_page&limit=" . $limit . "&holi=" . $searchHoli . "'>$counter</a> </li>";
                                                            }
                                                            $i = $i + $per_page;
                                                        }
                                                    }
                                                    //in middle; hide some front and some back
                                                    elseif ($max_pages - ($adjacents * 2) > ($start / $per_page) && ($start / $per_page) > ($adjacents * 2)) {
                                                        echo " <li><a href='?start=0'>1</a></li> ";
                                                        echo "<li><a href='?start=$per_page'>2</a></li>";
                                                        echo "<li><a href='javascript:void(0)'>...</a></li>";

                                                        $i = $start;
                                                        for ($counter = ($start / $per_page) + 1; $counter < ($start / $per_page) + $adjacents + 2; $counter++) {
                                                            if ($i == $start) {
                                                                echo " <li class='active'><a href='?start=$i&limit=$per_page&limit=" . $limit . "&holi=" . $searchHoli . "'><b>$counter</b></a></li> ";
                                                            } else {
                                                                echo " <li><a href='?start=$i&limit=$per_page&limit=" . $limit . "&holi=" . $searchHoli . "'>$counter</a> </li>";
                                                            }
                                                            $i = $i + $per_page;
                                                        }
                                                    }
                                                    //close to end; only hide early pages
                                                    else {
                                                        echo "<li> <a href='?start=0'>1</a> </li>";
                                                        echo "<li><a href='?start=$per_page&limit=" . $limit . "&holi=" . $searchHoli . "'>2</a></li>";
                                                        echo "<li><a href='javascript:void(0)'>...</a></li>";

                                                        $i = $start;
                                                        for ($counter = ($start / $per_page) + 1; $counter <= $max_pages; $counter++) {
                                                            if ($i == $start) {
                                                                echo " <li class='active'><a href='?start=$i&limit=$per_page&limit=" . $limit . "&holi=" . $searchHoli . "'><b>$counter</b></a></li> ";
                                                            } else {
                                                                echo "<li> <a href='?start=$i&limit=$per_page&limit=" . $limit . "&holi=" . $searchHoli . "'>$counter</a></li> ";
                                                            }
                                                            $i = $i + $per_page;
                                                        }
                                                    }
                                                }
                                                //next button
                                                if (!($start >= $foundnum - $per_page))
                                                    echo "<li><a href='?start=$next&limit=$per_page&limit=" . $limit . "&holi=" . $searchHoli. "''>$lang[Next]</a></li>";
                                                else
                                                    echo "<li class='disabled'><a href='javascript:void(0)'>$lang[Next]</a></li>";
                                                ?>
                                            </ul>
                                            <?php
                                        }
                                        echo "</center>";
                                    }

                                    else {
                                        ?>
                                        <div class="form-group form-group no-records-found"><label><strong class="text-danger"><i class="ti-face-sad text-pink"></i> <?php echo $lang['Who0ps!_No_Records_Found']; ?></strong></label></div>
                                    <?php }
                                    ?>	
                                </div>
                            </div>
                        </div>
                        <!-- /Right-bar -->
                        <!-- MODAL -->
                        <div id="dialog" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog"> 
                                <div class="panel panel-color panel-danger"> 
                                    <div class="panel-heading"> 
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                                        <label><h2 class="panel-title"><?= $lang['Are_u_confirm'] ?></h2></label> 
                                    </div> 
                                    <form method="post">
                                        <div class="panel-body">
                                            <p style="color: red;"><?= $lang['delete_this_Holiday'] ?></p>
                                        </div>
                                        <div class="modal-footer">
                                            <div class="col-md-12 text-right">
                                                <input type="hidden" id="hid" name="hldy">
                                                <button type="submit" name="deleteHoliyday" id="dialogConfirm" class="btn btn-danger waves-effect waves-light"><i class="fa fa-trash-o"></i> Delete</button>
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> 
                                            </div>
                                        </div>
                                    </form>
                                </div> 
                            </div>
                        </div>
                        <!-- end Modal -->

                        <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog"> 
                                <div class="modal-content"> 
                                    <form method="post" >
                                        <div class="modal-header"> 
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                                            <h4 class="modal-title"><?= $lang['holiday_name'] . ' ' . $lang['Modify_column'] ?></h4> 
                                        </div>

                                        <div class="modal-body" id="editevent">

                                            <img src="assets/images/load.gif" alt="load" class="img-responsive center-block" />
                                        </div> 
                                        <div class="modal-footer">

                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><?= $lang['Close'] ?></button> 
                                            <button type="submit" name="editHoliday" class="btn btn-primary"><?= $lang['Submit'] ?> </button> 
                                        </div>
                                    </form>

                                </div> 
                            </div>
                        </div><!-- /.modal -->
                        <div id="group-add" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog">
                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title"><?= $lang['add_holiday'] ?></h4>
                                    </div>
                                    <form method="post">
                                        <div class="modal-body">
                                            <div class="row form-group">
                                                <label> <?= $lang['holiday_name'] ?> <span class="astrick">*</span></label>
                                                <input type="text" class="form-control translatetext" placeholder="<?= $lang['holiday_name'] ?>" name="hname" id="hday" required>
                                            </div>
                                            <div class="row form-group">
                                                <label><?= $lang['Select_Holiday'] ?> <span class="astrick">*</span></label>
                                                <div class="input-group">
                                                    <input type="text" value=""  class="form-control datepicker" placeholder="<?= $lang['dd_mm_yyyy'] ?>" name="hdate" required>
                                                    <span class="input-group-addon bg-custom b-0 text-white"><i class="icon-calender"></i></span>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><?= $lang['Close'] ?></button>
                                            <button type="submit" name="addHoliday" class="btn btn-primary" ><?= $lang['Submit'] ?></button>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div><!-- /.modal -->
                        <!-- end: page -->
                    </div> <!-- end Panel -->
                    <!-- end: page -->
                </div> <!-- end Panel -->
            </div> <!-- container -->
            <!-- END wrapper -->
            <?php require_once './application/pages/footer.php'; ?>
        </div>
        <!-- ============================================================== -->
        <!-- End Right content here -->
        <!-- ============================================================== -->
        <!-- Right Sidebar -->
        <?php //require_once './application/pages/rightSidebar.php';    ?>
        <?php require_once './application/pages/footerForjs.php'; ?>
        <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
        <script src="assets/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>     
        <script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
        <script src="assets/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
        <script type="text/javascript">

                                        $(document).ready(function () {
                                            $('form').parsley();
                                       });

                                        //for delete holiday
                                        $("a#removeHolidy").click(function () {
                                            var id = $(this).attr('data');
                                            $("#hid").val(id);
                                            //alert(id);

                                        });

                                        $("a#editHolidy").click(function () {
                                            var $id = $(this).attr('data');
                                            var $row = $(this).closest('tr');
                                            var name = '';
                                            var values = [];
                                            values = $row.find('td:nth-child(2)').map(function () {
                                                var $this = $(this);
                                                if ($this.hasClass('actions')) {

                                                } else {
                                                    name = $.trim($this.text());
                                                    //$.trim( $this.text());
                                                }
                                                var token = $("input[name='token']").val();
                                                $("#con-close-modal .modal-title").text("<?= $lang['holiday_name'] ?> ( " + name + " ) <?= $lang['Modify_column'] ?>");
                                                $.post("application/ajax/updateHoliday.php", {HID: $id, token:token}, function (result, status) {
                                                    if (status == 'success') {
                                                        getToken();
                                                        $("#editevent").html(result);
                                                    }
                                                });
                                            });
                                        });

                                        //limit filter
                                        var url = window.location.href + "?";
                                        function removeParam(key, sourceURL) {
                                            sourceURL = String(sourceURL).replace("#/", "");
                                            var rtn = sourceURL.split("?")[0],
                                                    param,
                                                    params_arr = [],
                                                    queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";
                                            if (queryString !== "") {
                                                params_arr = queryString.split("&");
                                                for (var i = params_arr.length - 1; i >= 0; i -= 1) {
                                                    param = params_arr[i].split("=")[0];
                                                    if (param === key) {
                                                        params_arr.splice(i, 1);
                                                    }
                                                }
                                                rtn = rtn + "?" + params_arr.join("&");
                                            } else {
                                                rtn = rtn + '?';
                                            }
                                            return rtn;
                                        }
                                        jQuery(document).ready(function ($) {
                                            $("#limit").change(function () {
                                                lval = $(this).val();
                                                url = removeParam("limit", url);
                                                url = url + "&limit=" + lval;
                                                window.open(url, "_parent");
                                            });
                                        });
                                        $(document).ready(function () {
                                            $("#holiday").click(function () {
                                                var $holiday = $("#holi").val();
                                                url = removeParam("start", url);
                                                url = removeParam("holi", url);
                                                if ($holiday != '') {
                                                    url = url + "&holi=" + $holiday;
                                                }
                                                window.open(url, "_parent");
                                            })

                                        })
        </script> 
        <script>
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
if (isset($_POST['addHoliday'], $_POST['token'])) {
    $hdate = xss_clean($_POST['hdate']);
    $hname = xss_clean(trim($_POST['hname']));
    $hname = mysqli_real_escape_string($db_con, $hname);
    $dupliHoliday = mysqli_query($db_con, "select * from tbl_events_master where holiday_name='$hname'");
    $CheckduliHoliday = mysqli_num_rows($dupliHoliday);
    if ($CheckduliHoliday > 0) {
        echo '<script>taskFailed("holiday", "' . $lang['Holiday_exist'] . '")</script>';
    } else {
        mysqli_set_charset($db_con, "utf8");
        $qryEvent = mysqli_query($db_con, "insert into tbl_events_master(`holiday_name`,`date`,`created`,`modified`) values('$hname','$hdate','$hdate','$hdate')"); //or die(mysqli_errno($db_con));
        if ($qryEvent) {
            mysqli_set_charset($db_con, "utf8");
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Holiday $hname Added','$date', null,'$host','')"); //or die('error :' . mysqli_error($db_con));
            echo '<script>taskSuccess("holiday", "' . $lang['added_holiday'] . '");</script>';
        } else {
            echo '<script>taskFailed("holiday", "' . $lang['failed_holiday'] . '")</script>';
        }
    }
    mysqli_close($db_con);
}

//for delete holiday

if (isset($_POST['deleteHoliyday'], $_POST['token']) && intval($_POST['hldy'])) {
    $id = $_POST['hldy'];
    $id = mysqli_real_escape_string($db_con, $id);
    mysqli_set_charset($db_con, "utf8");
    $delhlyNme = mysqli_query($db_con, "select holiday_name from tbl_events_master where id='$id'");
    $rwhldel = mysqli_fetch_assoc($delhlyNme);
    $delName = $rwhldel['holiday_name'];
    mysqli_set_charset($db_con, "utf8");
    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Holiday $delName Deleted','$date', null,'$host','')"); //or die('error :' . mysqli_error($db_con));
    $delhlyday = mysqli_query($db_con, "delete from tbl_events_master where id='$id'");
    if ($delhlyday) {
        echo'<script>taskSuccess("holiday","' . $lang['Holiday_Deleted'] . '");</script>';
    }

    mysqli_close($db_con);
}

if (isset($_POST['editHoliday'], $_POST['token'])) {
    $hid = filter_input(INPUT_POST, "hid");
    $HoliName = filter_input(INPUT_POST, "HoliName");
    $HoliName = mysqli_real_escape_string($db_con, $HoliName);
    $HoliDate = $_POST['HoliDate'];
    $checkHoliday = mysqli_query($db_con, "select * from tbl_events_master where holiday_name='$HoliName' and id!='$hid'");
    if (mysqli_num_rows($checkHoliday) > 0) {
        echo '<script>taskFailed("holiday", "' . $lang['Holiday_exist'] . '");</script>';
    } else {
        $editHoli = mysqli_query($db_con, "update tbl_events_master set `holiday_name`='$HoliName', date='$HoliDate' where id='$hid'"); //or die('Error : ' . mysqli_error($db_con));
        if ($editHoli) {
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Holiday Name $HoliName Updated','$date', null,'$host',null)"); //or die('error :' . mysqli_error($db_con));
            echo'<script>taskSuccess("holiday","' . $lang['Holiday_update'] . '");</script>';
        }
    }
    mysqli_close($db_con);
}
?>
</body>
</html>