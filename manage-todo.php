<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    require_once './application/config/database.php';
    require_once './application/pages/head.php';
	
    $sameGroupIDs = array();
    $group = mysqli_query($db_con, "select * from tbl_bridge_grp_to_um where find_in_set('$_SESSION[cdes_user_id]',user_ids)") or die('Error' . mysqli_error($db_con));
    while ($rwGroup = mysqli_fetch_assoc($group)) {
        $sameGroupIDs[] = $rwGroup['user_ids'];
    }
    $sameGroupIDs = implode(',', $sameGroupIDs);
    $sameGroupIDs = explode(',', $sameGroupIDs);
    $sameGroupIDs = array_unique($sameGroupIDs);
    sort($sameGroupIDs);
    $sameGroupIDs = implode(',', $sameGroupIDs);
    // print_r($sameGroupIDs);

    if ($rwgetRole['todo_view'] != '1') {
        header('Location: ./index');
    }
    $_SESSION['cdes_user_id'] == $rwUser['user_id'];
    if (isset($_GET['GrpId']) && !empty($_GET['GrpId']) ) {
        $group_id = base64_decode(urldecode($_GET['GrpId']));
        $group_id = preg_replace("/[^0-9]/", "", $group_id);
        $getUserID = mysqli_query($db_con, "select user_ids,group_id from tbl_bridge_grp_to_um WHERE group_id='$group_id' ") or die("Error " . mysqli_error($db_con));
        $RwgetUserID = mysqli_fetch_assoc($getUserID);
        $userIds_selected_group = $RwgetUserID['user_ids'];
    }

//sk@29918
    
    $tdid = intval(base64_decode(urldecode($_GET['tdid'])));
    $tdid = preg_replace("/[^0-9]/", "", $tdid);
    ?>
    <!-- Plugin Css-->
    <link rel="stylesheet" href="assets/plugins/magnific-popup/css/magnific-popup.css" />
    <link rel="stylesheet" href="assets/plugins/jquery-datatables-editable/datatables.css" />
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
                                        <a href="manage-todo"><?php echo $lang['to_do_list']; ?></a>
                                    </li>
                                    <li class="active">
                                        <?php echo $lang['All']; ?>
                                    </li>
                                     <a href="#" data-toggle="modal" data-target="#help-modal" id="helpview" data="52" title="<?= $lang['help']; ?>"><i class="fa fa-question-circle"></i> </a></li>
                                    <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                                </ol>
                            </div>
                        </div>
                        <div class="box box-primary">
                            <div class="panel">
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-sm-12">

                                            <?php if ($rwgetRole['todo_add'] == '1') { ?>
                                                <div class="col-sm-1">
                                                    <a href="createtodo" class="btn btn-primary waves-effect waves-light" style="height:36px;"> <?php echo $lang['Add']; ?> <i class="fa fa-plus"></i></a>
                                                </div>
                                            <?php } ?>

                                            <div class="form-group col-md-3">
                                                <select class="select2" name="tddt"  id="tddt">
                                                    <option value=""><?php echo $lang['select_date']; ?></option>
                                                    <?php
                                                    $tddt = base64_decode(urldecode($_GET['tddt']));
                                                    $tddt = strtolower($tddt);
                                                    ?>
                                                    <option value="today" <?= ($tddt == 'today' ? 'selected' : '') ?>><?= $lang['today'] ?></option>
                                                    <option value="tomorrow" <?= ($tddt == 'tomorrow' ? 'selected' : '') ?>><?= $lang['tomorrow'] ?></option>
                                                    <option value="this_week" <?= ($tddt == 'this_week' ? 'selected' : '') ?>><?= $lang['this_week'] ?></option>

                                                </select>
                                            </div>
                                            <div class="form-group col-md-1">
                                                <input type="button" name="search" id="search" class="btn btn-primary" value="<?php echo $lang['Apply']; ?>" onclick="searchToDo();" title="<?php echo $lang['Search']; ?>" >
                                            </div>
                                            <div class="form-group col-md-1">
                                                <a href="manage-todo" class="btn btn-warning" title="<?php echo $lang['Reset']; ?>" ><?php echo $lang['Reset']; ?></a>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="">
                                        <?php
                                        //require Once
                                        require_once 'tdn-appoint.php';
                                        $where = " where 1=1 ";
                                        //if($_SESSION[cdes_user_id]!='1'){
                                        $where .= " and find_in_set($_SESSION[cdes_user_id],emp_id)";
                                        //}
                                        if ($tdid) {
                                            $where .= " and id='$tdid'";
                                        }
                                        if ($tddt) {
                                            if ($tddt == 'today') {
                                                $where .= " and task_date=CURDATE()";
                                            } else if ($tddt == 'tomorrow') {
                                                $tmrw_date = getTommorowDate();
                                                $where .= " and task_date='$tmrw_date'";
                                            } else if ($tddt == 'this_week') {
                                                $wk_dates = getWeekDates();
                                                $wk_start = $wk_dates['start'];
                                                $wk_end = $wk_dates['end'];
                                                $where .= " and task_date BETWEEN '$wk_start' AND '$wk_end'";
                                            }
                                        }
                                        $where .= " and is_archived='0'";
                                        if (isset($_GET['searchtxt']) && !empty($_GET['searchtxt'])) {
                                            $searchText = base64_decode($_GET['searchtxt']);
                                            $searchText = xss_clean($searchText);
                                            $where .= " and  (first_name like '%$searchText%' or designation like '%$searchText%' or designation like '%$searchText%')";
                                        }
                                        $sql = "SELECT * FROM  todo_list $where";
                                        mysqli_set_charset($db_con, "utf8");
                                        $retval = mysqli_query($db_con, $sql); //or die('Could not get data: ' . mysqli_error($db_con));
                                        $foundnum = mysqli_num_rows($retval);
                                        if ($foundnum > 0) {
                                            $StartPoint = preg_replace("/[^0-9]/", "", $_GET['limit']); //filter limit from all special chars
                                            if (is_numeric($StartPoint)) {
                                                $per_page = $StartPoint;
                                            } else {
                                                $per_page = 10;
                                            }
                                            $start = preg_replace("/[^0-9]/", "", $_GET['start']); //filter start variable
                                            $start = isset($start) ? ($start > 0) ? $start : 0 : 0;
                                            $max_pages = ceil($foundnum / $per_page);
                                            if (!$start) {
                                                $start = 0;
                                            }
                                            $limit = preg_replace("/[^0-9]/", "", $_GET['limit']);
                                            ?>
                                            <div class="container">
                                                <div class="pull-right record">
                                                    <?php echo $start + 1 ?> <?php echo $lang['To']; ?> <?php
                                                    if ($start + $per_page > $foundnum) {
                                                        echo $foundnum;
                                                    } else {
                                                        echo ($start + $per_page);
                                                    }
                                                    ?> <span><?php echo $lang['ttl_recrds']; ?>: <?php echo $foundnum; ?></span>
                                                </div>

                                                <div class="box-body">

                                                    <?php echo $lang['show_lst']; ?> 
                                                    <select id="limit">
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
                                                    <?php echo $lang['entries_lst']; ?>
                                                    <div class="box-body">
                                                        <?php
                                                        mysqli_set_charset($db_con, "utf8");
                                                        
                                                        $users = mysqli_query($db_con, "select * from todo_list $where order by id desc LIMIT $start, $per_page");
                                                        showData($users, $rwgetRole, $db_con, $start, $lang);
                                                        ?>
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
                                                                    echo " <li><a href='?start=$prev&limit=$per_page'>" . $lang['Prev'] . "</a> </li>";
                                                                else
                                                                    echo " <li class='disabled'><a href='javascript:void(0)'>" . $lang['Prev'] . "</a> </li>";
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
                                                                    echo "<li><a href='?start=$next'>" . $lang['Next'] . "</a></li>";
                                                                else
                                                                    echo "<li class='disabled'><a href='javascript:void(0)'>" . $lang['Next'] . "</a></li>";
                                                                ?>
                                                            </ul>
                                                            <?php
                                                        }
                                                        echo "</center>";
                                                    }else {
                                                        ?>
                                                        <div class="form-group no-records-found"><label><?= $lang['no_to_do']; ?></label></div>
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
                    <!-- MODAL -->
                    <div id="dialog" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

                        <div class="modal-dialog"> 

                            <div class="panel panel-color panel-danger"> 
                                <div class="panel-heading"> 
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                                    <label><h2 class="panel-title"><?php echo $lang['Are_u_confirm']; ?></h2></label> 
                                </div> 
                                <form method="post" >
                                    <div class="panel-body">
                                        <p style="color: red;"><?php echo $lang['aysywtatd']; ?></p>
                                    </div>

                                    <div class="modal-footer">
                                        <div class="col-md-12 text-right">
                                            <input type="hidden" id="uid" name="uid">
                                            <input type="hidden" id="del_id" name="del_id">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                                            <button type="submit" name="delete" id="dialogConfirm" class="btn btn-danger waves-effect waves-light"><i class="fa fa-archive"></i> <?php echo $lang['archive']; ?></button>

                                        </div>
                                    </div>
                                </form>
                            </div> 
                        </div>
                    </div>
                    <!-- end Modal -->
                    <div id="multi-csv-export-model" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog"> 

                            <div class="modal-content"> 
                                <form action="multi-export-user-data"  method="post">
                                    <div class="modal-header"> 
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                                        <label><h2 class="panel-title"><?php echo $lang['Export_Listed_User_Lists']; ?></h2></label> 
                                    </div> 
                                    <div class="modal-body">
                                        <div class="col-md-12 shiv metaa" style="margin-top:-10px;">
                                            <strong><?php echo $lang['Select_Files_for_Export_Format']; ?>:</strong>
                                            <select  class="multi-select" id="my_multi_select1" name="select_Fm">

                                                <option value="csv"><?php echo $lang['Csv']; ?></option>
                                                <!--  <option value="excel">Excel</option>-->
                                                <option value="pdf"><?php echo $lang['Pdf']; ?></option>
                                            </select>
                                        </div>

                                    </div>
                                    <div class="modal-footer">
                                        <input type="hidden" value="<?php echo $group_id; ?>" name="userIds">
                                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                                        <button class="btn btn-primary waves-effect waves-light fa fa-download" type="submit" name="exportUser"> <?php echo $lang['Export']; ?></button>
                                    </div>
                                </form>

                            </div> 
                        </div>
                    </div>

                    <div id="activate" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog"> 

                            <div class="modal-content"> 
                                <form method="post" >
                                    <div class="modal-header"> 
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                                        <label><h2 class="panel-title"><?= $lang['ARE_YOU_SURE'] ?>?</h2></label> 
                                    </div> 
                                    <div class="modal-body">
                                        <p style="color: red;"><?= $lang['Are_you_sure_that_you_want_to_deactivate_this_User'] ?>?</p>
                                    </div>

                                    <div class="modal-footer">
                                        <div class="col-md-12 text-right">
                                            <input type="hidden" id="dectiveId" name="dacvtUsr">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?= $lang['Close'] ?></button> 
                                            <button type="submit" name="deactivate" id="dialogConfirm" class="btn btn-danger waves-effect waves-light"><i class="fa fa-toggle-off"> <?= $lang['Deactivate'] ?></i></button>

                                        </div>
                                    </div>
                                </form>
                            </div> 
                        </div>
                    </div>

                    <div id="deactivate" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog"> 

                            <div class="modal-content"> 
                                <form method="post" >
                                    <div class="modal-header"> 
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                                        <label><h2 class="panel-title"><?= $lang['ARE_YOU_SURE'] ?>?</h2></label> 
                                    </div> 
                                    <div class="modal-body">
                                        <p style="color: red;"><?= $lang['Are_you_sure_that_you_want_to_activate_this_User'] ?>?</p>
                                    </div>

                                    <div class="modal-footer">
                                        <div class="col-md-12 text-right">
                                            <input type="hidden" id="activateId" name="actUser" value="">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?= $lang['Close'] ?></button> 
                                            <button type="submit" name="activate" id="dialogConfirm" class="btn btn-primary waves-effect waves-light"><i class="fa fa-toggle-on"> <?= $lang['Activate'] ?></i></button>

                                        </div>
                                    </div>
                                </form>
                            </div> 
                        </div>
                    </div>


                </div>
                <!-- END wrapper -->

                <?php require_once './application/pages/footerForjs.php'; ?>

                <script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>

                <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>


                <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>

                <!----data table------>
                <script src="assets/plugins/datatables/jquery.dataTables.min.js"></script>
                <script src="assets/plugins/datatables/dataTables.bootstrap.js"></script>

                <script src="assets/pages/datatables.init.js"></script>
                <script src="assets/js/gs_sortable.js"></script>
                <!-- for searchable select-->
                <script type="text/javascript">

                                                    var TSort_Data = new Array('table_demo_icons', 'i', 's', 's', 's', 's');
                                                    var TSort_Icons = new Array('<i class="fa fa-caret-up"></i>', '<i class="fa fa-caret-down"></i>');
                                                    tsRegister();

                </script>
                <script type="text/javascript">
                    $(document).ready(function () {
                        $('form').parsley();
                        $(".select2").select2();
                        $('#datatable').dataTable();
                    });

                    //export user in groupwise
                    $(document).ready(function () {

//                        $("#usergroup").change(function () {
//                            var group_id = $(this).val();
//                            //alert(group_id);
//                            window.location.href = "?GrpId=" + btoa(encodeURI(group_id));
//                        });
                    });
                    //firstname last name 
                    $("input#userName, input#lastName").keypress(function (e) {
                        //if the letter is not digit then display error and don't type anything
                        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57) && e.which != 46) {
                            //display error message
                            return true;
                        } else {
                            return false;
                        }
                        str = $(this).val();
                        str = str.split(".").length - 1;
                        if (str > 0 && e.which == 46) {
                            return false;
                        }
                    });
                    $("input#phone").keypress(function (e) {
                        //if the letter is not digit then display error and don't type anything
                        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57) && e.which != 46) {
                            //display error message
                            return false;
                        }
                        str = $(this).val();
                        str = str.split(".").length - 1;
                        if (str > 0 && e.which == 46) {
                            return false;
                        }
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

                    $("a#editRow").click(function () {
                        var $id = $(this).attr('data');

                        // $("#con-close-modal .modal-title").text("Update " +name+ "'s Profile");
                        $.post("application/ajax/updateUser.php", {ID: $id}, function (result, status) {
                            if (status == 'success') {
                                $("#modalModify").html(result);
                            }
                        });

                    });
                    $("a#removeRow").click(function () {
                        var id = $(this).attr('data');
                        $("#del_id").val(id);
                    });

                </script>
                <script type="text/javascript">
                    $("a#active").click(function () {
                        var id = $(this).attr('data');
                        $("#activateId").val(id);

                    });
                    $("a#dective").click(function () {
                        var id = $(this).attr('data');
                        $("#dectiveId").val(id);

                    });
                    //TableManageButtons.init();

                    function searchToDo() {
                        var tddt = $('#tddt').val();
                        //alert(tddt);
                        window.location.href = "?tddt=" + btoa(encodeURI(tddt));
                    }


                </script>
                <?php
                function showData($user, $rwgetRole, $db_con, $start, $lang) {
                    ?>
                    <table class="table table-striped table-bordered js-sort-table" id="table_demo_icons">
                        <thead>
                            <tr>
                                <th class="sort-js-none" ><?php echo $lang['Sr_No']; ?></th>
                                <th><?php echo $lang['Task_Name']; ?></th>
                                <th><?php echo $lang['task_description']; ?></th>
                                <th class="sort-js-date" ><?php echo $lang['task_date']; ?></th>
                                <th class="sort-js-date" ><?php echo $lang['task_time']; ?></th>
                                <th><?php echo $lang['task_noty_freq']; ?></th>
                                <th class="sort-js-date" ><?php echo $lang['noty_time']; ?></th>
                                <?php if ($rwgetRole['todo_edit'] == '1' || $rwgetRole['todo_archive'] == '1') { ?>
                                    <th class="sort-js-none" ><?php echo $lang['Action']; ?></th>
                                <?php } ?>

                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            $i += $start;
                            while ($rwUser = mysqli_fetch_assoc($user)) {
                                $tnf = $rwUser['task_notification_frequency'];
                                ?>
                                <tr class="gradeX"> 
                                    <td><?php echo $i; ?></td>
                                    <td>
                                        <?php echo $rwUser['task_name']; ?>
                                    </td>
                                    <td><?php echo $rwUser['task_description']; ?></td>
                                    <td><?php echo date('d-m-Y', strtotime($rwUser['task_date'])); ?> </td>
                                    <td><?php echo date('h:i A',strtotime($rwUser['task_time'])); ?> </td>
                                    <td><?php echo ($tnf == '0' ? 'Same Day' : $tnf . ($tnf > '1' ? " days" : " day") . ' before'); ?> </td>
                                    <td><?php echo date('h:i A', strtotime($rwUser['task_notify_time'])); ?> </td>
                                    <td class="actions" style="width: 12%">
                                        <?php if ($rwgetRole['todo_edit'] == '1') { ?>
                                            <a href="createtodo?tdid=<?= urlencode(base64_encode($rwUser['id'])) ?>" class="on-default edit-row btn btn-primary"  title="<?php echo $lang['edit_to_do']; ?>"><i class="fa fa-edit"></i></a>

                                        <?php } ?>
                                        <?php if ($rwgetRole['todo_archive'] == '1') { ?>
                                            <a href="#" class="on-default remove-row btn btn-danger" data-toggle="modal" data-target="#dialog" id="removeRow" data="<?php echo $rwUser['id']; ?>" title="<?php echo $lang['archive_to_do']; ?>"><i class="fa fa-archive"></i></a>
                                            <?php } ?>
                                    </td>
                                </tr>
                                <?php
                                $i++;
                            }
                            ?>
                        </tbody>
                    </table>
                <?php } ?>
                </body>
                </html>
                <?php
                if (isset($_POST['delete'], $_POST['token'])) {
                    $del_id = intval($_POST['del_id']);
                    $query = mysqli_query($db_con, "update todo_list set is_archived='1' where id='$del_id'");
                    if ($query) {
                        echo '<script>taskSuccess("manage-todo","' . $lang['to_do_archived'] . '");</script>';
                    } else {
                        echo '<script>taskFailed("manage-todo","' . $lang['uta'] . '");</script>';
                    }
                    mysqli_close($db_con);
                }
                ?>
                