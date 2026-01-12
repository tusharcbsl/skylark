<!DOCTYPE html>
<html>
    <?php
    // $uri = $path; //$path = $_SERVER['REQUEST_URI'];
    // $uri = $path; 
    require_once './loginvalidate.php';
    require_once './application/config/database.php';
    require_once './application/pages/head.php';

    //for user role
    $chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

    $rwgetRole = mysqli_fetch_assoc($chekUsr);


    if ($rwgetRole['backup'] != '1') {
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
    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
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
                                        <a href="#"><?php echo $lang['admin_tool']; ?></a>
                                    </li>
                                    <li class="active">
                                        <?php echo $lang['restore_backup']; ?>
                                    </li>
                                    <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                                </ol>
                            </div>
                        </div>
                        <div class="box box-primary">
                            <div class="panel">
                                <div class="panel-body">
                                    <div class="">
                                        <div class="box-body">
                                            <form method="get">
                                                <div class="col-md-3">
                                                    
                                                    <select class="form-control select4" name="bktype" required>
                                                        <option selected disabled style="background: #808080; color: #121213;"><?php echo $lang['Select_User_Name']; ?></option>

                                                        <option <?php
                                                            if (isset($_GET['bktype']) && $_GET['bktype'] == 'Scheduled') {
                                                                echo'selected';
                                                            }
                                                            ?>>Scheduled</option>
                                                        
                                                        <option <?php
                                                            if (isset($_GET['bktype']) && $_GET['bktype'] == 'Incremental') {
                                                                echo'selected';
                                                            }
                                                            ?>>Incremental</option>

                                                            
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <button type="submit" class="btn btn-primary"> <?php echo $lang['Search']; ?> <i class="fa fa-search"></i></button>
                                                    <a href="backup-restore.php"  class="btn btn-warning"><?php echo $lang['Reset']; ?> <i class="fa fa-refresh"></i></a>
                                                </div>

                                            </form>

                                        </div>

                                        <div class="container">
                                            <?php
                                            if (isset($_GET['bktype']) && !empty($_GET['bktype'])) {
                                              $where = " where backup_type='$_GET[bktype]'";
                                            }

                                            $allot = "select * from tbl_db_backup_log $where";

                                            $allot_query = mysqli_query($db_con, $allot) or die("Error:d " . mysqli_error($con));

                                            $foundnum = mysqli_num_rows($allot_query);
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

                                                $allot = "select * from tbl_db_backup_log $where order by id desc LIMIT $start, $per_page";

                                                $allot_query = mysqli_query($db_con, $allot) or die("ErrorC: " . mysqli_error($db_con));
                                                ?>

                                                <div class="box-body">
                                                    <div class="col-sm-12 m-b-10">
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
                                                        </select> <label><?php echo $lang['entries_lst']; ?></label>
                                                        <div class="pull-right record">
                                                            <?php echo $start + 1 ?> <?php echo $lang['To']; ?> <?php
                                                            if (($start + $per_page) > $foundnum) {
                                                                echo $foundnum;
                                                            } else {
                                                                echo ($start + $per_page);
                                                            }
                                                            ?> <span><?php echo $lang['ttl_recrds']; ?>: <?php echo $foundnum; ?></span>
                                                        </div>
                                                    </div>
                                                    <table class="table table-striped table-bordered">
                                                        <thead>
                                                            <tr>

                                                                <th>
                                                                    <?php if ($rwgetRole['delete_storage_log'] == '1') { ?>
                                                                        <div class="checkbox checkbox-primary"><input  type="checkbox" class="checkbox-primary" id="select_all"> <label for="checkbox6"> <strong><?php echo $lang['All']; ?></strong></label></div>  
                                                                        <?php
                                                                    } else {
                                                                        echo $lang['SNO'];
                                                                    }
                                                                    ?>

                                                                </th>
                                                                <th><?php echo $lang['backup_type']; ?></th>
                                                                <th><?php echo $lang['backup_name']; ?></th>
                                                                <th><?php echo $lang['Date']; ?></th>
                                                                <?php if ($rwgetRole['restore'] == '1') { ?>
                                                                    <th><?php echo $lang['restore']; ?></th>
                                                                <?php } ?>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $n = $start + 1;
                                                            while ($allot_row = mysqli_fetch_assoc($allot_query)) {
                                                                ?>
                                                                <tr class="gradeX">
                                                                 <td>
                                                                        <?php if ($rwgetRole['delete_storage_log'] == '1') { ?>
                                                                            <div class="checkbox checkbox-primary"><input type="checkbox" class="checkbox-primary emp_checkbox" data-doc-id="<?php echo $allot_row['id']; ?>"> <label for="checkbox6"> <?php echo $n . '.'; ?></label></div>
                                                                            <?php
                                                                        } else {
                                                                            echo $n . '.';
                                                                        }
                                                                        ?>
                                                                    </td>
                                                                    <td><?php echo $allot_row['backup_type']; ?></td>
                                                                    <td><?php echo $allot_row['backup_name']; ?></td>
                                                                    <td><?php echo $allot_row['tstp']; ?></td>
                                                                    <?php if ($rwgetRole['restore'] == '1') { ?>
                                                                        <td> <button class="btn btn-success" data-toggle="modal" data-target="#delHistory" id="deladit" data="<?php echo $allot_row['id']; ?>">Restore</button></td>
                                                                    <?php } ?>
                                                                </tr>
                                                                <?php
                                                                $n++;
                                                            }
                                                            ?>
                                                            <?php //if ($rwgetRole['delete_storage_log'] == '1') { ?>
                                                                <!--<tr>
                                                                            <td colspan="7">
                                                                                <ul class="delete_export" style="margin-left:-40px;">
                                                                            <li><button id="del_mul_histry" class="rows_selected btn btn-danger" data-toggle="modal" data-target="#del_mul_history"><i class="fa fa-trash-o"></i> <?php echo $lang['Delete_Audit']; ?></button></li>
                                                                        </ul>
                                                                    </td>
                                                                </tr>-->
                                                            <?php //} ?>
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
                                                            if (!($start <= 0)) {
                                                                echo " <li><a href='?start=$prev&limit=$per_page&bktype=" . $_GET['bktype'] . "'>$lang[Prev]</a> </li>";
                                                            } else {
                                                                echo " <li class='disabled'><a href='javascript:void(0)'>$lang[Prev]</a> </li>";
                                                            }
                                                            //pages 
                                                            if ($max_pages < 7 + ($adjacents * 2)) {   //not enough pages to bother breaking it up
                                                                $i = 0;
                                                                for ($counter = 1; $counter <= $max_pages; $counter++) {
                                                                    if ($i == $start) {
                                                                        echo "<li class='active'><a href='?start=$i&limit=$per_page&bktype=" . $_GET['bktype'] . "''><b>$counter</b></a> </li>";
                                                                    } else {
                                                                        echo "<li><a href='?start=$i&limit=$per_page&bktype=" . $_GET['bktype'] . "''>$counter</a></li> ";
                                                                    }
                                                                    $i = $i + $per_page;
                                                                }
                                                            } elseif ($max_pages > 5 + ($adjacents * 2)) {    //enough pages to hide some
                                                                //close to beginning; only hide later pages
                                                                if (($start / $per_page) < 1 + ($adjacents * 2)) {
                                                                    $i = 0;
                                                                    for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                                                                        if ($i == $start) {
                                                                            echo " <li class='active'><a href='?start=$i&limit=$per_page&bktype=" . $_GET['bktype'] . "'><b>$counter</b></a></li> ";
                                                                        } else {
                                                                            echo "<li> <a href='?start=$i&limit=$per_page&bktype=" . $_GET['bktype'] . "'>$counter</a> </li>";
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
                                                                            echo " <li class='active'><a href='?start=$i&limit=$per_page&bktype=" . $_GET['bktype'] . "'><b>$counter</b></a></li> ";
                                                                        } else {
                                                                            echo " <li><a href='?start=$i&limit=$per_page&bktype=" . $_GET['bktype'] . "'>$counter</a> </li>";
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
                                                                            echo " <li class='active'><a href='?start=$i&limit=$per_page&bktype=" . $_GET['bktype'] . "'><b>$counter</b></a></li> ";
                                                                        } else {
                                                                            echo "<li> <a href='?start=$i&limit=$per_page&bktype=" . $_GET['bktype'] . "'>$counter</a></li> ";
                                                                        }
                                                                        $i = $i + $per_page;
                                                                    }
                                                                }
                                                            }
                                                            //next button
                                                            if (!($start >= $foundnum - $per_page))
                                                                echo "<li><a href='?start=$next&limit=$per_page&bktype=" . $_GET['bktype'] . "'>$lang[Next]</a></li>";
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
            <script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
            <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
            <script type="text/javascript" src="assets/multi_function_script.js"></script>
            <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>
            <script>
                                        $("#fltr").click(function () {
                                            var heiht = $(document).height();
                                            $('#wait').css('height', heiht);
                                            $('#wait').show();
                                            var startDate = $('#startDate').val();
                                            var endDate = $('#endDate').val();
                                            var limit = $('#limit').val();
                                            $.post("application/ajax/UploadReport.php",
                                                    {
                                                        startDate: startDate,
                                                        endDate: endDate,
                                                        limit: limit
                                                    },
                                                    function (data, status) {

                                                        if (status == 'success') {
                                                            $('#wait').hide();
                                                            $("#container").hide();
                                                            $("#ajaxcontainer").html(data);
                                                            $("#ajaxcontainer").show();

                                                        }
                                                    });
                                        });

                                        $("button#deladit").click(function () {
                                            var id = $(this).attr('data');
                                            $("#AuditDel").val(id);
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

            <script type="text/javascript">

                $(document).ready(function () {
                    $('form').parsley();
                    //$('#datatable').dataTable();

                });
                $(".select4").select2();
            </script>

            
            <div id="delHistory" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog"> 
                    <div class="panel panel-color panel-danger"> 
                        <div class="panel-heading"> 
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                            <h2 class="panel-title"><?php echo $lang['Are_u_confirm']; ?></h2> 
                        </div>
                        <form method="post">
                            <div class="panel-body">
                                <label><p class="text-alert"><?php echo $lang['r_u_sure_want_to_restore']; ?>?</p></label>
                            </div> 
                            <div class="modal-footer">
                                <input type="hidden" id="AuditDel" name="DelHistry">
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"> <?php echo $lang['Close']; ?></button> 
                                <button type="submit" name="restore" class="btn btn-danger"> <?php echo $lang['restore']; ?></button> 
                            </div>
                        </form>

                    </div> 
                </div>
            </div><!-- /.modal -->
    </body>
</html>
<?php
if(isset($_POST['restore'])){
    
    // Incremental Restoration.
//$binlog_query = mysqli_query($db_con, "Show binary logs");
$binlog_query = mysqli_query($db_con, "select * from tbl_db_backup_log where id < '$bkid' or id='$bkid'");
$binlog_path = "binlog/";
while ($binlog_res = mysqli_fetch_assoc($binlog_query)) {
    $binlog_array[] = $binlog_path . $binlog_res['Log_name']; 
}
//print_r($binlog_array); 

$binlog_str = implode(" ", $binlog_array);
echo $binlog_str;
//Prepare command.
$cmd = "mysqlbinlog --database $dbName $binlog_str | mysql -u $dbUser -p$dbPwd $dbName";
echo $cmd; die;
exec($cmd);
}
?>

