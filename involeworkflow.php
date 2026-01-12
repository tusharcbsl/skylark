<!DOCTYPE html>
<html>
    <?php
    error_reporting(0);
    //$path = $_SERVER['REQUEST_URI'];
    // $uri = $path;
    require_once './loginvalidate.php';
    require_once './application/config/database.php';
    require_once './application/pages/head.php';
    require_once './application/pages/function.php';

    // echo $rwgetRole['dashboard_mydms']; die;
    if ($rwgetRole['dashboard_mytask'] != '1') {
        header('Location: ./index');
    }
    $roleids = array();
mysqli_set_charset($db_con, "utf8");
    $grp_by_rl_ids = mysqli_query($db_con, "SELECT group_id,user_ids FROM `tbl_bridge_grp_to_um` where find_in_set($_SESSION[cdes_user_id],user_ids)");
    while ($rwGrp = mysqli_fetch_array($grp_by_rl_ids)) {

        if (!empty($rwGrp['user_ids'])) {
            $user_ids[] = $rwGrp['user_ids'];
        }
    }
    $user_ids = implode(',', $user_ids);

    $user_ids = explode(',', $user_ids);
    $user_ids = array_unique($user_ids);
    $user_ids = implode(',', $user_ids);
    ?>

    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <!-- Plugin Css-->
    <link rel="stylesheet" href="assets/plugins/magnific-popup/css/magnific-popup.css" />

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
                                        <a href="#"><?php echo $lang['Workflow_Reports'] ?></a>
                                    </li>
                                    <li class="active"><?php echo $lang['Involved_WorkFlow'] ?></li>
                                    <li> <a href="#" data-toggle="modal" data-target="#help-modal" id="helpview" data="24" title="<?= $lang['help']; ?>"><i class="fa fa-question-circle"></i> </a></li>
                                    <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                                </ol>
                            </div>
                        </div>
                        <div class="box box-primary">
                            <div class="panel">
                                <div class="panel-body">
                                    <div class="box-body">
                                        <?php
                                        if ($_SESSION['cdes_user_id'] == 1) {
                                            $retval = mysqli_query($db_con, "SELECT twm.workflow_name,tsm.task_name,assign_user,alternate_user,supervisor FROM tbl_task_master tsm INNER JOIN tbl_workflow_master twm ON tsm.workflow_id=twm.workflow_id LEFT JOIN tbl_user_master tum ON
		                            tsm.assign_user=tum.user_id LEFT JOIN tbl_user_master tuma ON tsm.alternate_user=tuma.user_id LEFT JOIN tbl_user_master tums ON tsm.supervisor=tums.user_id group by tsm.task_id") or die('Could not get data: ' . mysqli_error($db_con));
                                        } else {
                                            $retval = mysqli_query($db_con, "SELECT twm.workflow_name,tsm.task_name,assign_user,alternate_user,supervisor FROM tbl_task_master tsm INNER JOIN tbl_workflow_master twm ON tsm.workflow_id=twm.workflow_id LEFT JOIN tbl_user_master tum ON
		                            tsm.assign_user=tum.user_id LEFT JOIN tbl_user_master tuma ON tsm.alternate_user=tuma.user_id LEFT JOIN tbl_user_master tums ON tsm.supervisor=tums.user_id where assign_user='$_SESSION[cdes_user_id]' or alternate_user='$_SESSION[cdes_user_id]' or supervisor='$_SESSION[cdes_user_id]' group by tsm.task_id") or die('Could not get data: ' . mysqli_error($db_con));
                                        }
                                        $foundnum = mysqli_num_rows($retval);
                                        if ($foundnum > 0) {
                                            if (is_numeric($_GET['limit'])) {
                                                $per_page = $_GET['limit'];
                                            } else {
                                                $per_page = 10;
                                            }
                                            $start = isset($_GET['start']) ? ($_GET['start'] > 0) ? $_GET['start'] : 0 : 0;
                                            $max_pages = ceil($foundnum / $per_page);
                                            if (!$start) {
                                                $start = 0;
                                            }
                                            $limit = $_GET['limit'];
                                            ?>
                                            <div class="box-body">
                                                <div class="m-b-10">
                                                    <label><?php echo $lang['show_lst']; ?></label>
                                                    <select id="limit" class="input-sm">
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
                                                        <option value="250" <?php
                                                        if ($_GET['limit'] == 250) {
                                                            echo 'selected';
                                                        }
                                                        ?>>250</option>
                                                        <option value="500" <?php
                                                        if ($_GET['limit'] == 500) {
                                                            echo 'selected';
                                                        }
                                                        ?>>500</option>
                                                    </select>  <label><?php echo $lang['Workflow_Reports'] ?></label>

                                                    <div class="pull-right record">
                                                        <?php echo $start + 1 ?> <?php echo $lang['To'] ?> <?php
                                                        if (($start + $per_page) > $foundnum) {
                                                            echo $foundnum;
                                                        } else {
                                                            echo ($start + $per_page);
                                                        };
                                                        ?> <span><?php echo $lang['ttl_recrds']; ?> : <?php echo $foundnum; ?></span>
                                                    </div>
                                                </div>
                                                <table  class="table table-striped table-bordered js-sort-table">
                                                    <thead>
                                                        <tr>
                                                            <th class="sort-js-none" ><?php echo $lang['Sr_No']; ?></th>
                                                            <th><?php echo $lang['Workflow_Name']; ?></th>
                                                            <th><?php echo $lang['Task_Name']; ?></th>
                                                            <th><?php echo $lang['Assign_User']; ?></th>
                                                            <th><?php echo $lang['Alternate_User']; ?></th>
                                                            <th><?php echo $lang['Supervisor'] ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                         $start = xss_clean(trim($start));
                                                        $per_page = xss_clean(trim($per_page));
                                                        if ($_SESSION['cdes_user_id'] == 1) {
                                                            $data = mysqli_query($db_con, "SELECT twm.workflow_name,tsm.task_name,assign_user,alternate_user,supervisor FROM tbl_task_master tsm INNER JOIN tbl_workflow_master twm ON tsm.workflow_id=twm.workflow_id LEFT JOIN tbl_user_master tum ON
		                                                tsm.assign_user=tum.user_id LEFT JOIN tbl_user_master tuma ON tsm.alternate_user=tuma.user_id LEFT JOIN tbl_user_master tums ON tsm.supervisor=tums.user_id group by tsm.task_id LIMIT $start,$per_page");
                                                        } else {
                                                            $data = mysqli_query($db_con, "SELECT twm.workflow_name,tsm.task_name,assign_user,alternate_user,supervisor FROM tbl_task_master tsm INNER JOIN tbl_workflow_master twm ON tsm.workflow_id=twm.workflow_id LEFT JOIN tbl_user_master tum ON
		                                                 tsm.assign_user=tum.user_id LEFT JOIN tbl_user_master tuma ON tsm.alternate_user=tuma.user_id LEFT JOIN tbl_user_master tums ON tsm.supervisor=tums.user_id where assign_user='$_SESSION[cdes_user_id]' or alternate_user='$_SESSION[cdes_user_id]' or supervisor='$_SESSION[cdes_user_id]' group by tsm.task_id LIMIT $start,$per_page");
                                                        }

                                                        $n = 1;
                                                        $n += $start;
                                                        while ($allot_row = mysqli_fetch_assoc($data)) {
                                                            ?>
                                                            <tr class="gradeX" style="vertical-align: middle;">
                                                                <td style="width:60px"><?php echo $n; ?></td>

                                                                <td><?php echo $allot_row['workflow_name']; ?></td>
                                                                <td><?php echo $allot_row['task_name']; ?></td>

                                                                <td><?php
                                                                    $user = mysqli_query($db_con, "select first_name,last_name from tbl_user_master where user_id='$allot_row[assign_user]'");
                                                                    $rwUser = mysqli_fetch_assoc($user);
                                                                    echo $rwUser['first_name'] . ' ' . $rwUser['last_name'];
                                                                    ?></td>

                                                                <td><?php
                                                                    $user = mysqli_query($db_con, "select first_name,last_name from tbl_user_master where user_id='$allot_row[alternate_user]'");
                                                                    $rwUser = mysqli_fetch_assoc($user);
                                                                    echo $rwUser['first_name'] . ' ' . $rwUser['last_name'];
                                                                    ?></td>
                                                                <td><?php
                                                                    $user = mysqli_query($db_con, "select first_name,last_name from tbl_user_master where user_id='$allot_row[supervisor]'");
                                                                    $rwUser = mysqli_fetch_assoc($user);
                                                                    echo $rwUser['first_name'] . ' ' . $rwUser['last_name'];
                                                                    ?></td>

                                                            </tr>
                                                            <?php
                                                            $n++;
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
                                                        if (!($start <= 0))
                                                            echo " <li><a href='?start=$prev&limit=$per_page'>$lang[Prev]</a> </li>";
                                                        else
                                                            echo " <li class='disabled'><a href='javascript:void(0)'>$lang[Prev]</a> </li>";
                                                        //pages 
                                                        if ($max_pages < 7 + ($adjacents * 2)) {   //not enough pages to bother breaking it up
                                                            $i = 0;
                                                            for ($counter = 1; $counter <= $max_pages; $counter++) {
                                                                if ($i == $start) {
                                                                    echo "<li class='active'><a href='?start=$i&limit=$per_page'><b>$counter</b></a> </li>";
                                                                } else {
                                                                    echo "<li><a href='?start=$i&limit=$per_page'>$counter</a></li> ";
                                                                }
                                                                $i = $i + $per_page;
                                                            }
                                                        } elseif ($max_pages > 5 + ($adjacents * 2)) {    //enough pages to hide some
                                                            //close to beginning; only hide later pages
                                                            if (($start / $per_page) < 1 + ($adjacents * 2)) {
                                                                $i = 0;
                                                                for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                                                                    if ($i == $start) {
                                                                        echo " <li class='active'><a href='?start=$i&limit=$per_page'><b>$counter</b></a></li> ";
                                                                    } else {
                                                                        echo "<li> <a href='?start=$i&limit=$per_page'>$counter</a> </li>";
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
                                                                        echo " <li class='active'><a href='?start=$i&limit=$per_page'><b>$counter</b></a></li> ";
                                                                    } else {
                                                                        echo " <li><a href='?start=$i&limit=$per_page'>$counter</a> </li>";
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
                                                                        echo " <li class='active'><a href='?start=$i&limit=$per_page'><b>$counter</b></a></li> ";
                                                                    } else {
                                                                        echo "<li> <a href='?start=$i&limit=$per_page'>$counter</a></li> ";
                                                                    }
                                                                    $i = $i + $per_page;
                                                                }
                                                            }
                                                        }
                                                        //next button
                                                        if (!($start >= $foundnum - $per_page))
                                                            echo "<li><a href='?start=$next&limit=$per_page'>$lang[Next]</a></li>";
                                                        else
                                                            echo "<li class='disabled'><a href='javascript:void(0)'>$lang[Next]</a></li>";
                                                        ?>
                                                    </ul>
                                                    <?php
                                                }
                                                echo "</center>";
                                            }else {
                                                ?>
                                                <div class="form-group form-group no-records-found"><label><strong class="text-danger"><i class="ti-face-sad text-danger"></i> <?php echo $lang['Who0ps!_No_Records_Found']; ?></strong></label></div>
                                                        <?php }
                                                        ?>	
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
        </div>
        <script type="text/javascript">

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
    </body>
</html>

