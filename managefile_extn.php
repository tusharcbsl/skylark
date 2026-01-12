<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    require_once './application/config/database.php';
    require_once './application/pages/head.php';

    if ($rwgetRole['view_exten'] != '1') {
        header('Location: ./index');
    }
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
                                        <a href="metasearch"><?php echo $lang['Administrative_tool']; ?></a>
                                    </li>
                                    <li class="active">
                                        <?php echo $lang['managefile_exten']; ?>
                                    </li>
                                    <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                                </ol>
                            </div>
                        </div>

                        <div class="panel">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <?php if($rwgetRole['add_exten']){ ?>
                                    <div class="col-sm-12">
                                        <form method="post">
                                            <div class="col-sm-3">
                                                <input type="text" class="form-control" name="exten" value="" parsley-trigger="change" placeholder="Enter file extension." required data-parsley-pattern="^[A-Za-z0-9]*$"  >
                                            </div>
                                            <div class="col-sm-3">
                                                <button type="submit" class="btn btn-primary" name="addextn"><i class="fa fa-plus"></i> Add</button>
                                                <a href="managefile_extn" class="btn btn-warning"><i class="fa fa-refresh"></i> Reset</a>
                                            </div>
                                        </form>
                                    </div>
                                <?php } ?>
                                </div>
                                <div class="panel-body">
                                    
                                    <?php
                                    $extensions = "SELECT * FROM `tbl_file_extensions`";
                                    $run = mysqli_query($db_con, $extensions) or die('Error' . mysqli_error($db_con));

                                    $foundnum = mysqli_num_rows($run);
                                    if ($foundnum > 0) {
                                        if (is_numeric($_GET['limit'])) {
                                            $per_page = $_GET['limit'];
                                        } else {
                                            $per_page = 10;
                                        }
                                         $start = isset($_GET['start']) ? ($_GET['start']>0)?$_GET['start']:0 : 0;
                                        $max_pages = ceil($foundnum / $per_page);
                                        if (!$start) {
                                            $start = 0;
                                        }
                                         $limit = $_GET['limit'];
                                        ?>
                                        <div class="container">
                                           
                                            <div class="box-body">
                                               
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
                                                </select> <label> <?php echo $lang['entries_lst']; ?></label>
                                                 <div class="pull-right record">
                                                <?php echo $start + 1 ?> <?php echo $lang['To']; ?> <?php
                                                if (($start + $per_page) > $foundnum) {
                                                    echo $foundnum;
                                                } else {
                                                    echo ($start + $per_page);
                                                }
                                                ?> <span><?php echo $lang['Ttal_Rcrds']; ?>: <?php echo $foundnum; ?></span>
                                            </div>
                                            </div>
                                            <table class="table table-striped table-bordered js-sort-table" id="query" role="grid" aria-describedby="datatable_info">
                                                <thead>
                                                    <tr>
                                                        <th class="sort-js-none" ><?php echo $lang['Sr_No']; ?></th> 
                                                        <th><?php echo $lang['exten_name']; ?></th>
                                                        <?php if($rwgetRole['delete_exten'] || $rwgetRole['enable_exten']){ ?>
                                                        <th class="sort-js-none" ><?php echo $lang['Actions']; ?></th>
                                                    <?php } ?>

                                                    </tr>

                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $query_ft = mysqli_query($db_con, "SELECT * FROM `tbl_file_extensions` order by name asc LIMIT $start, $per_page") or die("Error: test" . mysqli_error($db_con));
                                                    if (isset($start) && $start != 0) {
                                                        $i = $start + 1;
                                                    } else {
                                                        $i = 1;
                                                    }


                                                    $i = 1;
                                                     $i = $start +1;
                                                    while ($query_row = mysqli_fetch_assoc($query_ft)) {
                                                        ?>
                                                        <tr>
                                                            <td><?php echo $i; ?></td>

                                                            <td><?php echo $query_row['name']; ?></td>
                                                            <td>
                                                                <?php if($rwgetRole['delete_exten']){ ?>
                                                                <a href="#" class="on-default remove-row btn btn-danger" data-toggle="modal" data-target="#dialog" id="removeRow" data="<?php echo $query_row['id']; ?>" title="<?php echo $lang['Delete']; ?>"><i class="fa fa-trash-o"></i></a>
                                                                <?php }
                                                                if($rwgetRole['enable_exten']){ ?>

                                                                <?php if ($query_row['status'] == 1) { ?>
                                                                    <a href="#" class="on-default edit-row btn btn-success" data-toggle="modal" id="dective" data-target="#activate" data="<?php echo $query_row['id']; ?>" title="<?php echo $lang['disable']; ?>"><i class="fa fa-toggle-on"></i></a>
                                                                <?php } else { ?>
                                                                    <a href="#" class="on-default edit-row btn btn-danger" data-toggle="modal" data-target="#deactivate" id="active" data="<?php echo $query_row['id']; ?>" title="<?php echo $lang['disable']; ?>"><i class="fa fa-toggle-off"></i></a>
                                                                    <?php
                                                                }
                                                            }
                                                                ?>
                                                            </td>

                                                        </tr>
                                                        <?php
                                                        $i = $i + 1;
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
                                                            echo " <li><a href='?start=$prev&limit=$per_page&limit=" . $_GET['limit'] . "'>$lang[Prev]</a> </li>";
                                                        else
                                                            echo " <li class='disabled'><a href='javascript:void(0)'>$lang[Prev]</a> </li>";
                                                        //pages 
                                                        if ($max_pages < 7 + ($adjacents * 2)) {   //not enough pages to bother breaking it up
                                                            $i = 0;
                                                            for ($counter = 1; $counter <= $max_pages; $counter++) {
                                                                if ($i == $start) {
                                                                    echo "<li class='active'><a href='?start=$i&limit=$per_page&limit=" . $_GET['limit'] . "'><b>$counter</b></a> </li>";
                                                                } else {
                                                                    echo "<li><a href='?start=$i&limit=$per_page&limit=" . $_GET['limit'] . "'>$counter</a></li> ";
                                                                }
                                                                $i = $i + $per_page;
                                                            }
                                                        } elseif ($max_pages > 5 + ($adjacents * 2)) {    //enough pages to hide some
                                                            //close to beginning; only hide later pages
                                                            if (($start / $per_page) < 1 + ($adjacents * 2)) {
                                                                $i = 0;
                                                                for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                                                                    if ($i == $start) {
                                                                        echo " <li class='active'><a href='?start=$i&limit=$per_page&limit=" . $_GET['limit'] . "'><b>$counter</b></a></li> ";
                                                                    } else {
                                                                        echo "<li> <a href='?start=$i&limit=$per_page&limit=" . $_GET['limit'] . "'>$counter</a> </li>";
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
                                                                        echo " <li class='active'><a href='?start=$i&limit=$per_page&limit=" . $_GET['limit'] . "'><b>$counter</b></a></li> ";
                                                                    } else {
                                                                        echo " <li><a href='?start=$i&limit=$per_page&limit=" . $_GET['limit'] . "'>$counter</a> </li>";
                                                                    }
                                                                    $i = $i + $per_page;
                                                                }
                                                            }
                                                            //close to end; only hide early pages
                                                            else {
                                                                echo "<li> <a href='?start=0'>1</a> </li>";
                                                                echo "<li><a href='?start=$per_page&limit=" . $_GET['limit'] . "'>2</a></li>";
                                                                echo "<li><a href='javascript:void(0)'>...</a></li>";

                                                                $i = $start;
                                                                for ($counter = ($start / $per_page) + 1; $counter <= $max_pages; $counter++) {
                                                                    if ($i == $start) {
                                                                        echo " <li class='active'><a href='?start=$i&limit=$per_page&limit=" . $_GET['limit'] . "'><b>$counter</b></a></li> ";
                                                                    } else {
                                                                        echo "<li> <a href='?start=$i&limit=$per_page&limit=" . $_GET['limit'] . "'>$counter</a></li> ";
                                                                    }
                                                                    $i = $i + $per_page;
                                                                }
                                                            }
                                                        }
                                                        //next button
                                                        if (!($start >= $foundnum - $per_page))
                                                            echo "<li><a href='?start=$next&limit=$per_page&limit=" . $_GET['limit'] . "''>$lang[Next]</a></li>";
                                                        else
                                                            echo "<li class='disabled'><a href='javascript:void(0)'>$lang[Next]</a></li>";
                                                        ?>
                                                    </ul>
                                                <?php
                                            }
                                            echo "</center>";
                                        }else { ?>
                                           <div class="form-group form-group no-records-found"><label><strong class="text-danger"><i class="ti-face-sad text-pink"></i> <?php echo $lang['Who0ps!_No_Records_Found']; ?></strong></label></div>
                                      <?php  }
                                        ?>
                                    </div>
                                    <!-- end: page -->
                                </div>
                            </div> <!-- end Panel -->
                        </div> <!-- container -->

                    </div> <!-- content -->

                    <?php require_once './application/pages/footer.php'; ?>                
                </div>
                <!-- Right Sidebar -->
                <?php require_once './application/pages/rightSidebar.php'; ?>
            </div>



            <!-- END wrapper -->
            <?php require_once './application/pages/footerForjs.php'; ?>


            <div id="dialog" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

                <div class="modal-dialog"> 

                    <div class="panel panel-color panel-danger"> 
                        <div class="panel-heading"> 
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                            <label><h2 class="panel-title"><?php echo $lang['Are_u_confirm']; ?></h2></label> 
                        </div> 
                        <form method="post" >
                            <div class="panel-body">
                                <p style="color: red;"><?php echo $lang['are_you_sure_delete_this_extn']; ?></p>
                            </div>

                            <div class="modal-footer">
                                <div class="col-md-12 text-right">
                                    <input type="hidden" id="uid" name="uid">
                                    <input type="hidden" id="del_id" name="del_id">
                                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                                    <button type="submit" name="delete" id="dialogConfirm" class="btn btn-danger waves-effect waves-light"><i class="fa fa-trash-o"></i> <?php echo $lang['Delete']; ?></button>

                                </div>
                            </div>
                        </form>
                    </div> 
                </div>
            </div>

            <div id="activate" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog"> 
                    <div class="panel panel-color panel-danger"> 
                        <div class="panel-heading"> 
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                            <label><h2 class="panel-title"><?= $lang['Are_u_confirm'] ?></h2></label> 
                        </div> 
                        <form method="post">
                            <div class="panel-body">
                                <p class="text-danger"><?php echo $lang['Are_you_sure_that_you_want_to_disable_this_extension'] ?> ? </p>
                            </div>
                            <div class="modal-footer">
                                <div class="col-md-12 text-right">
                                    <input type="hidden" id="dectiveId" name="dacvtUsr">
                                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close'] ?></button> 
                                    <button type="submit" name="disable" id="dialogConfirm" class="btn btn-danger waves-effect waves-light"><?= $lang['confirm'] ?></button>

                                </div>
                            </div>
                        </form>
                    </div> 
                </div>
            </div>

            <div id="deactivate" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog"> 
                    <div class="panel panel-color panel-success"> 
                        <div class="panel-heading"> 
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                            <label><h2 class="panel-title"><?= $lang['Are_u_confirm'] ?></h2></label> 
                        </div> 
                        <form method="post" >
                            <div class="panel-body">
                                <p class="text-danger"><?= $lang['Are_you_sure_that_you_want_to_enable_this_extension'] ?> ? </p>
                            </div>
                            <div class="modal-footer">
                                <div class="col-md-12 text-right">
                                    <input type="hidden" id="activateId" name="actUser" value="">
                                    <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal"><?= $lang['Close'] ?></button> 
                                    <button type="submit" name="enable" id="dialogConfirm" class="btn btn-success waves-effect waves-light"><?= $lang['confirm'] ?></button>

                                </div>
                            </div>
                        </form>
                    </div> 
                </div>
            </div>


            <script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
            <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>

            <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>

            <!----data table------>
            <script src="assets/plugins/datatables/jquery.dataTables.min.js"></script>
            <script src="assets/plugins/datatables/dataTables.bootstrap.js"></script>
            <script type="text/javascript" src="assets/multi_function_script.js"></script>
            <script type="text/javascript" src="assets/jquery.quicksearch.js"></script>
            <script type="text/javascript">
                $(function () {

                    $('form').parsley();

                });
            </script>
            <script>
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

                $("a#removeRow").click(function () {
                        var id = $(this).attr('data');
                        $("#del_id").val(id);
                });

                $("a#active").click(function () {
                    var id = $(this).attr('data');
                    $("#activateId").val(id);

                });
                $("a#dective").click(function () {
                    var id = $(this).attr('data');
                    $("#dectiveId").val(id);
                });
            </script>



    </body>

</html>


<?php 
if(isset($_POST['addextn'])){

    $exten = $_POST['exten'];
    $exten = strtolower($_POST['exten']);
 
    $exten = preg_replace("/[^A-Za-z0-9[:space:]]/","",$exten);

    $arrayext =  array('php', 'js');

    if($exten){

        if(!in_array($exten, $arrayext)){

            $check = mysqli_query($db_con, "select name from tbl_file_extensions where name='$exten'");
            if (mysqli_num_rows($check) > 0) {

                echo '<script>taskFailed("' . $_SERVER['REQUEST_URI'] . '","' . $lang['exten_already_added'] . '");</script>';

            }else{

                $added = mysqli_query($db_con, "insert into tbl_file_extensions(name) values('$exten')");

                if($added){

                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','File extension added','$date','$host','File extension $exten added.')") or die('error1 : ' . mysqli_error($db_con));
                    echo '<script>taskSuccess("' . $_SERVER['REQUEST_URI'] . '","' . $lang['exten_added'] . '");</script>';
                } else {
                    echo '<script>taskFailed("' . $_SERVER['REQUEST_URI'] . '","' . $lang['exten_failed_to_add'] . '");</script>';
                }

            }

        }else{
            echo '<script>taskFailed("' . $_SERVER['REQUEST_URI'] . '","' . $lang['you_can_not_add_this_exten'] . '");</script>';
            
        }
        

        

        mysqli_close($db_con);

    }

}

if (isset($_POST['delete'], $_POST['token'])) {

    $del_id = intval($_POST['del_id']);
    $ex = mysqli_query($db_con, "select name from tbl_file_extensions where id='$del_id'");
     $rl_row = mysqli_fetch_array($rl);
     $exten = $rl_row['name'];
    $query = mysqli_query($db_con, "delete from tbl_file_extensions where id='$del_id'");
    if ($query) {
        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','File extension deleted','$date','$host','File extension $exten deleted.')") or die('error1 : ' . mysqli_error($db_con));
        echo '<script>taskSuccess("' . $_SERVER['REQUEST_URI'] . '","' . $lang['exten_deleted'] . '");</script>';
    } else {
        echo '<script>taskFailed("' . $_SERVER['REQUEST_URI'] . '","' . $lang['unable_to_delete'] . '");</script>';
    }

    mysqli_close($db_con);
}



if (isset($_POST['enable'], $_POST['token'])) {
    $id = mysqli_escape_string($db_con, $_POST['actUser']);
    $id = preg_replace("/[^0-9]/", "", $id);
    $enabled = mysqli_query($db_con, "update tbl_file_extensions set status='1' where id = '$id'"); //or die('Error:' . mysqli_error($db_con));
    if ($enabled) {

         $ex = mysqli_query($db_con, "select name from tbl_file_extensions where id='$id'");
         $rl_row = mysqli_fetch_array($rl);
         $exten = $rl_row['name'];

        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','File extension deleted','$date','$host','File extension $exten enabled.')") or die('error1 : ' . mysqli_error($db_con));


        echo '<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['exten_enable'] . '");</script>';
    }
    mysqli_close($db_con);
}

if (isset($_POST['disable'], $_POST['token'])) {
    $id = mysqli_escape_string($db_con, $_POST['dacvtUsr']);
    $id = preg_replace("/[^0-9]/", "", $id);
    $disabled = mysqli_query($db_con, "update tbl_file_extensions set status='0' where id = '$id'"); //or die('Error:' . mysqli_error($db_con));
    if ($disabled) {

        $ex = mysqli_query($db_con, "select name from tbl_file_extensions where id='$id'");
         $rl_row = mysqli_fetch_array($rl);
         $exten = $rl_row['name'];

        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','File extension disabled','$date','$host','File extension $exten disabled.')") or die('error1 : ' . mysqli_error($db_con));

        echo '<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['exten_disable'] . '");</script>';
    }

    mysqli_close($db_con);
}