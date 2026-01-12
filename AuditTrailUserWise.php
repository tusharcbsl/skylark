<!DOCTYPE html>
<html>
    <?php
    //$path = $_SERVER['REQUEST_URI'];
    // $uri = $path; 
    require_once './loginvalidate.php';
    require_once './application/config/database.php';
    require_once './application/pages/head.php';
    
     mysqli_set_charset($db_con, "utf8");
    //for user role
    $chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

    $rwgetRole = mysqli_fetch_assoc($chekUsr);


    if ($rwgetRole['view_user_audit'] != '1') {
        header('Location: ./index');
    }
    $sameGroupIDs = array();
    $group = mysqli_query($db_con, "select * from tbl_bridge_grp_to_um where find_in_set('$_SESSION[cdes_user_id]',user_ids)") or die('Error' . mysqli_error($db_con));
    while ($rwGroup = mysqli_fetch_assoc($group)) {
        $sameGroupIDs[] = $rwGroup['user_ids'];
    }

    $sameGroupIDs = implode(',', $sameGroupIDs);
    $sameGroupIDs = explode(",", $sameGroupIDs);
    $sameGroupIDs = array_unique($sameGroupIDs);
    sort($sameGroupIDs);
    $sameGroupIDs = implode(',', $sameGroupIDs);
    ?>
    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />

    <!-- Plugin Css-->
    <link rel="stylesheet" href="assets/plugins/magnific-popup/css/magnific-popup.css" />
    <link rel="stylesheet" href="assets/plugins/jquery-datatables-editable/datatables.css" />

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
                                        <a href="#"><?php echo $lang['Audit_Trail']; ?></a>
                                    </li>
                                    <li class="active">
                                        <?php echo $lang['User_Audit']; ?>
                                    </li>
                                      <li> <a href="#" data-toggle="modal" data-target="#help-modal" id="helpview" data="27" title="<?= $lang['help']; ?>"><i class="fa fa-question-circle"></i> </a></li>
                                    <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>


                                </ol>
                            </div>
                        </div>
                        <div class="box box-primary">
                            <div class="panel">
                                <div class="panel-body">
                                    <div class="box-body">
                                        <div class="row">
                                            <form method="get">
                                                <div class="col-md-3">
                                                    <?php
                                                    
                                                    if ($_SESSION['cdes_user_id'] == '1') {
                                                        $user = "SELECT distinct user_name FROM tbl_ezeefile_logs where user_id in($sameGroupIDs) and user_name!='' order by user_name ASC";
                                                    } else {
                                                        $user = "SELECT distinct user_name FROM tbl_ezeefile_logs where user_id in($sameGroupIDs) and user_id!=1 and user_name!='' order by user_name ASC";
                                                    }
                                                    mysqli_set_charset($db_con, "utf8");
                                                    $user_run = mysqli_query($db_con, $user); //or die('Error:' . mysqli_error($db_con));
                                                    ?>
                                                    <select class="form-control select3" name="userLog" tabindex="-1" aria-hidden="true">
                                                        <option disabled="disabled" selected><?php echo $lang['Select_User_Name']; ?></option>
                                                        <?php
                                                        while ($rwUserlogs = mysqli_fetch_assoc($user_run)) {
                                                            ?>
                                                            <option <?php
                                                            if (isset($_GET['userLog']) && $_GET['userLog'] == $rwUserlogs['user_name']) {
                                                                echo'selected';
                                                            }
                                                            ?>><?php echo $rwUserlogs['user_name']; ?></option>

                                                            <?php
                                                        }
                                                        ?>
                                                    </select>

                                                </div>

                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <div class="input-daterange input-group" id="date-range">
                                                                <input type="text" class="form-control readonly" name="startDate" id="startDate" value="<?php echo $_GET['startDate']; ?>" placeholder="<?= $lang['dd_mm_yyyy']; ?>" />
                                                                <span class="input-group-addon bg-custom b-0 text-white"><?php echo $lang['to']; ?></span>
                                                                <input type="text" class="form-control readonly" name="endDate" id="endDate" value="<?php echo $_GET['endDate']; ?>"   placeholder="<?= $lang['dd_mm_yyyy']; ?>" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <button type="submit" class="btn btn-primary"><?php echo $lang['Search']; ?> <i class="fa fa-search"></i></button>
                                                    <a href="AuditTrailUserWise"  class="btn btn-warning"><?php echo $lang['Reset']; ?> <i class="fa fa-refresh"></i></a>
                                                </div>


                                            </form>

                                            <button class="btn btn-primary" id="export4" data-toggle="modal" data-target="#multi-csv-export-model" title="Export Users"><i class="fa fa-download"></i> Export</button>

                                        </div>
                                        <?php

                                        


                                        if ($_SESSION['cdes_user_id'] == '1') {
                                            $where = "where action_name ='Login/Logout' and user_id in($sameGroupIDs)";
                                        } else {
                                            $where = "where action_name ='Login/Logout' and user_id in($sameGroupIDs) and user_id !='1'";
                                        }
                                        if (isset($_GET['userLog']) && !empty($_GET['userLog'])) {
                                            $userLog = xss_clean(trim($_GET['userLog']));
                                            if ($_SESSION['cdes_user_id'] == '1') {
                                                $where .= " and user_name ='$userLog'";
                                            } else {
                                                $where .= "and user_name ='$userLog' and user_id!='1'";
                                            }
                                        }

                                        if ((isset($_GET['startDate']) && !empty($_GET['startDate'])) && (isset($_GET['endDate']) && !empty($_GET['endDate']))) {
                                            $startdate = date('Y-m-d', strtotime($_GET['startDate']));
                                            $enddate = date('Y-m-d', strtotime($_GET['endDate']));
                                            if (strtotime($startdate) == strtotime($enddate)) {
                                                $where .= " and date(start_date)='" . xss_clean(trim($startdate)) . "'";
                                            } else {
                                                $where .= " and date(start_date) BETWEEN '" . xss_clean(trim($startdate)) . "' AND '" . xss_clean(trim($enddate)) . "'";
                                            }
                                        }


                                        $constructs = "SELECT * FROM tbl_ezeefile_logs $where";
                                        mysqli_set_charset($db_con, "utf8");
                                        $run = mysqli_query($db_con, $constructs); //or die('Error' . mysqli_error($con));
                                        $foundnum = mysqli_num_rows($run);
                                        if ($foundnum > 0) {
                                            $limit = preg_replace("/[^0-9 ]/", "", $_GET['limit']);
                                            if (isset($limit)) {
                                                if (!empty($limit)) {
                                                    $per_page = $limit;
                                                } else {
                                                    $per_page = 10;
                                                }
                                            } else {
                                                $per_page = 10;
                                            }
                                            //$per_page = 10;
                                            $start = preg_replace("/[^0-9 ]/", "", $_GET['start']);
                                            $start = isset($start) ? ($start > 0) ? $start : 0 : 0;
                                            $max_pages = ceil($foundnum / $per_page);
                                            if (!$start) {
                                                $start = 0;
                                            }
                                            
                                            $allot = "select * from tbl_ezeefile_logs $where order by start_date DESC LIMIT $start, $per_page";

                                            $allot_query = mysqli_query($db_con, $allot); //or die("Error: " . mysqli_error($con));
                                            $limit = $_GET['limit'];
                                            ?>
                                            <div class="box-body limit">
                                                <?php echo $lang['show_lst']; ?> <select id="limit" class="input-sm">
                                                    <option value="10" <?php
                                                    if ($_GET['limit'] == 10) {
                                                        echo 'selected';
                                                    }
                                                    ?>>10</option>
                                                    <option value="25" <?php
                                                    if ($_GET['limit'] == 25) {
                                                        echo 'selected';
                                                    }
                                                    ?>>25</option>
                                                    <option value="50" <?php
                                                    if ($_GET['limit'] == 50) {
                                                        echo 'selected';
                                                    }
                                                    ?>>50</option>
                                                    <option value="100" <?php
                                                    if ($_GET['limit'] == 100) {
                                                        echo 'selected';
                                                    }
                                                    ?>>100</option>
                                                    <option value="200" <?php
                                                    if ($_GET['limit'] == 200) {
                                                        echo 'selected';
                                                    }
                                                    ?>>200</option>
                                                </select> <?php echo $lang['User_Audit']; ?>
                                                <div class="pull-right record">
                                                    <?php echo $start + 1 ?> <?php echo $lang['To']; ?> <?php
                                                    if (($start + $per_page) > $foundnum) {
                                                        echo $foundnum;
                                                    } else {
                                                        echo ($start + $per_page);
                                                    }
                                                    ?>  <span><?php echo $lang['ttl_recrds']; ?>: <?php echo $foundnum; ?></span>
                                                </div>
                                            </div>
                                            <table class="table table-striped table-bordered js-sort-table">
                                                <thead>
                                                    <tr>
                                                        <th class="sort-js-none" ><?php echo $lang['SNO']; ?></th>
                                                        <th><?php echo $lang['User_Name']; ?></th>
                                                        <th><?php echo $lang['Action_Performed']; ?></th>
                                                        <th class="sort-js-date" ><?php echo $lang['Action_Start_Date']; ?></th>
                                                        <th class="sort-js-date" ><?php echo $lang['Action_End_Date']; ?></th>
                                                        <th class="sort-js-number" ><?php echo $lang['Sys_IP']; ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $n = $start + 1;
                                                    while ($allot_row = mysqli_fetch_assoc($allot_query)) {
                                                        ?>
                                                        <tr class="gradeX">

                                                            <td><?php echo $n; ?></td>
                                                            <td><?php echo $allot_row['user_name']; ?></td>
                                                            <td><?php echo $allot_row['action_name']; ?></td>
                                                            <td><?php echo $allot_row['start_date']; ?></td>
                                                            <td><?php echo $allot_row['end_date']; ?></td>
                                                            <td><?php echo $allot_row['system_ip']; ?></td>
                                                        </tr>
                                                        <?php
                                                        $n++;
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>

                                            <center>
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
                                                        if (!($start <= 0))
                                                            echo " <li><a href='?start=$prev&limit=$per_page&userLog=" . $_GET['userLog'] . "&startDate=" . $_GET['startDate'] . "&endDate=" . $_GET['endDate'] . "'>$lang[Prev]</a> </li>";
                                                        else
                                                            echo " <li class='disabled'><a href='javascript:void(0)'>$lang[Prev]</a> </li>";
                                                        //pages 
                                                        if ($max_pages < 7 + ($adjacents * 2)) {   //not enough pages to bother breaking it up
                                                            $i = 0;
                                                            for ($counter = 1; $counter <= $max_pages; $counter++) {
                                                                if ($i == $start) {
                                                                    echo "<li class='active'><a href='?start=$i&limit=$per_page&userLog=" . $_GET['userLog'] . "&startDate=" . $_GET['startDate'] . "&endDate=" . $_GET['endDate'] . "'><b>$counter</b></a> </li>";
                                                                } else {
                                                                    echo "<li><a href='?start=$i&limit=$per_page&userLog=" . $_GET['userLog'] . "&startDate=" . $_GET['startDate'] . "&endDate=" . $_GET['endDate'] . "''>$counter</a></li> ";
                                                                }
                                                                $i = $i + $per_page;
                                                            }
                                                        } elseif ($max_pages > 5 + ($adjacents * 2)) {    //enough pages to hide some
                                                            //close to beginning; only hide later pages
                                                            if (($start / $per_page) < 1 + ($adjacents * 2)) {
                                                                $i = 0;
                                                                for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                                                                    if ($i == $start) {
                                                                        echo " <li class='active'><a href='?start=$i&limit=$per_page&userLog=" . $_GET['userLog'] . "&startDate=" . $_GET['startDate'] . "&endDate=" . $_GET['endDate'] . "''><b>$counter</b></a></li> ";
                                                                    } else {
                                                                        echo "<li> <a href='?start=$i&limit=$per_page&userLog=" . $_GET['userLog'] . "&startDate=" . $_GET['startDate'] . "&endDate=" . $_GET['endDate'] . "''>$counter</a> </li>";
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
                                                                        echo " <li class='active'><a href='?start=$i&limit=$per_page&userLog=" . $_GET['userLog'] . "''><b>$counter</b></a></li> ";
                                                                    } else {
                                                                        echo " <li><a href='?start=$i&limit=$per_page&userLog=" . $_GET['userLog'] . "&startDate=" . $_GET['startDate'] . "&endDate=" . $_GET['endDate'] . "''>$counter</a> </li>";
                                                                    }
                                                                    $i = $i + $per_page;
                                                                }
                                                            }
                                                            //close to end; only hide early pages
                                                            else {
                                                                echo "<li> <a href='?start=0'>1</a> </li>";
                                                                echo "<li><a href='?start=$per_page'>2</a></li>";
                                                                echo "<li><a href='javascript:void(0)'>...</a></li>";

                                                                $i = $start;
                                                                for ($counter = ($start / $per_page) + 1; $counter <= $max_pages; $counter++) {
                                                                    if ($i == $start) {
                                                                        echo " <li class='active'><a href='?start=$i&limit=$per_page&userLog=" . $_GET['userLog'] . "&startDate=" . $_GET['startDate'] . "&endDate=" . $_GET['endDate'] . "''><b>$counter</b></a></li> ";
                                                                    } else {
                                                                        echo "<li> <a href='?start=$i&limit=$per_page&userLog=" . $_GET['userLog'] . "&startDate=" . $_GET['startDate'] . "&endDate=" . $_GET['endDate'] . "'>$counter</a></li> ";
                                                                    }
                                                                    $i = $i + $per_page;
                                                                }
                                                            }
                                                        }
                                                        //next button
                                                        if (!($start >= $foundnum - $per_page))
                                                            echo "<li><a href='?start=$next&limit=$per_page&userLog=" . $_GET['userLog'] . "&startDate=" . $_GET['startDate'] . "&endDate=" . $_GET['endDate'] . "'>$lang[Next]</a></li>";
                                                        else
                                                            echo "<li class='disabled'><a href='javascript:void(0)'>$lang[Next]</a></li>";
                                                        ?>
                                                    </ul>
                                                    <?php
                                                }
                                                echo "</center>";
                                            }else {
                                                ?>

                                                <div class="form-group form-group no-records-found"><label><strong class="text-danger"><i class="ti-face-sad text-pink"></i> <?php echo $lang['Who0ps!_No_Records_Found']; ?></strong></label></div>
                                            <?php }
                                            ?>	
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end: page -->

                    </div> <!-- end Panel -->
                </div>
            </div> <!-- container -->

        </div> <!-- content -->

        <?php require_once './application/pages/footer.php'; ?>

    </div>

</div>
<!-- END wrapper -->
<?php require_once './application/pages/footerForjs.php'; ?>

<script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script src="assets/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
<script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
<script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
<script type="text/javascript" src="assets/multi_function_script.js"></script>
<script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>
<script type="text/javascript">

$(document).ready(function () {

    $(".select3").select2();

    $('form').parsley();

     setTimeout(function(){ $("input[type='search']").addClass('translatetext'); }, 3000);

});

 jQuery('#date-range').datepicker({
        toggleActive: true
    });

//for searchable select
                                       
</script>

<script>
    $("button#removelog").click(function () {
        var id = $(this).attr('data');
        $("#uselog").val(id);
    });
</script>
<script>

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

</script>
<div id="del_mul_history" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog"> 
        <div class="panel panel-color panel-danger"> 

            <div class="panel-heading"> 
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h2 class="panel-title" id="titleAudit"> <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <?php echo $lang['Hre_msge']; ?> </h2>
                <h2 class="panel-title" id="multi_Audit_confirm"> <?php echo $lang['Are_u_confirm']; ?></h2> 
            </div>
            <form method="post">
                <div class="panel-body">
                    <span id="multi_audit_errmessage" style="display:none;"> <p class="text-alert"><?php echo $lang['Pls_sel_Audit_for_Del']; ?></p></span>
                    <label class="text-danger" id="multi_audit_hide"><p class="text-danger"><?php echo $lang['Are_you_sure_want_to_Del_selcted_his']; ?>?</p></label>
                </div> 
                <div class="modal-footer">
                    <input type="hidden" id="Del_multi_Audit" name="DelUsrAudit" value="">
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"> <?php echo $lang['Close']; ?></button>
                    <button type="submit"  name="Usr_History" class="btn btn-danger" id="hiddelHis"><i class="fa fa-trash-o"></i> <?php echo $lang['Delete']; ?></button>
                    </button> 
                </div>
            </form>

        </div> 
    </div>
</div>
<div id="delHistory" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog "> 
        <div class="panel panel-color panel-danger"> 
            <div class="panel-heading">  
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                <h2 class="panel-title"><?php echo $lang['Are_u_confirm']; ?></h2> 
            </div>
            <form method="post">
                <div class="panel-body" >
                    <label class="text-danger"><p class="text-danger"><?php echo $lang['r_u_sure_want_to_Delt_audit_his']; ?>?</p></label>
                </div>  
                <div class="modal-footer">
                    <input type="hidden" id="uselog" name="DelUsrAudt" value="">
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"> <?php echo $lang['Close']; ?></button> 
                    <button type="submit" name="DelUserAdt" class="btn btn-danger"> <i class="fa fa-trash-o"></i> <?php echo $lang['Delete']; ?></button> 
                </div>
            </form>

        </div> 
    </div>
</div><!-- /.modal -->

<div id="multi-csv-export-model" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog"> 
        <div class="panel panel-color panel-primary"> 
            <div class="panel-heading"> 
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                <label><h2 class="panel-title"><?php echo $lang['export_user_audit']; ?></h2></label> 
            </div> 
            <form action="export-user-audit"  method="post">
                <div class="panel-body">
                    <div class="col-md-5  m-t-10">
                        <strong class="text-primary"><?php echo $lang['Select_Files_for_Export_Format']; ?> : </strong>
                    </div>
                    <div class="col-md-3">
                        <select class="select2 input-sm" id="my_multi_select1" name="select_Fm">

                            <option value="xlsx"><?php echo $lang['Excel']; ?></option>
                            <!--  <option value="excel">Excel</option>-->
                            <option value="pdf"><?php echo $lang['Pdf']; ?></option>
                            <option value="csv"><?php echo $lang['Csv']; ?></option>
                            <option value="word"><?php echo $lang['Word']; ?></option>
                        </select>
                    </div>

                </div>
                <div class="modal-footer">

                    <?php
                    $startdate = "";
                    $enddate = "";
                    if ((isset($_GET['startDate']) && !empty($_GET['startDate'])) && (isset($_GET['endDate']) && !empty($_GET['endDate']))) {
                        $startdate = date('Y-m-d', strtotime($_GET['startDate']));
                        $enddate = date('Y-m-d', strtotime($_GET['endDate']));
                    }
                    ?>
                    <input type="hidden" value="<?php echo $startdate; ?>" name="sdate">
                    <input type="hidden" value="<?php echo $enddate; ?>" name="edate">
                    <input type="hidden" value="<?php echo $_GET['userLog']; ?>" name="userLog">
                    <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                    <button class="btn btn-primary waves-effect waves-light" type="submit" name="exportUser"><i class="fa fa-download"></i> <?php echo $lang['Export']; ?></button>
                </div>
            </form>

        </div> 
    </div>
</div>

</body>
</html>

<?php
if (isset($_POST['DelUserAdt'], $_POST['token'])) {
    $AuditId = $_POST['DelUsrAudt'];
    $delSnglAudit = mysqli_query($db_con, "DELETE FROM `tbl_ezeefile_logs` WHERE id ='$AuditId'"); //or die("Error in del" . mysqli_error($db_con));
    if ($delSnglAudit) {
        echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '", "' . $lang['Audit_Deleted'] . '");</script>';
    } else {
        echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '", "' . $lang['Failed_Delete_Audit'] . '");</script>';
    }
    mysqli_close($db_con);
}
if (isset($_POST['Usr_History'], $_POST['token'])) {
    $AuditIds = $_POST['DelUsrAudit'];
    $delAudit = mysqli_query($db_con, "DELETE FROM `tbl_ezeefile_logs` WHERE id in($AuditIds)"); //or die("Error in del" . mysqli_error($db_con));
    if ($delAudit) {
        echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '", "' . $lang['Audit_Deleted'] . '");</script>';
    } else {
        echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '", "' . $lang['Failed_Delete_Audit'] . '");</script>';
    }
    mysqli_close($db_con);
}
?>