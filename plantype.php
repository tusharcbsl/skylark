<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    require_once './application/config/database.php';
    require_once './application/pages/head.php';
    $sameGroupIDs = array();
    $group = mysqli_query($db_con, "select group_id from tbl_bridge_grp_to_um where find_in_set('$_SESSION[cdes_user_id]',user_ids)") or die('Error' . mysqli_error($db_con));
    while ($rwGroup = mysqli_fetch_assoc($group)) {
        $sameGroupIDs[] = $rwGroup['group_id'];
    }
    $sameGroupIDs = array_unique($sameGroupIDs);
    sort($sameGroupIDs);
    $sameGroupIDs = implode(',', $sameGroupIDs);
    //for user role
    $chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));
    $rwgetRole = mysqli_fetch_assoc($chekUsr);
    if ($rwgetRole['view_group_list'] != '1') {
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
                                        <a href="plantype">Add Plan Type</a>
                                    </li>
                                    <li class="active">
                                        Plan Type
                                    </li>
                                </ol>
                            </div>
                        </div>

                        <div class="panel">

                            <div class="panel-body">


                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="m-b-30">
                                            <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right" data-toggle="modal" data-target="#group-add">Add New Plan <i class="fa fa-plus"></i></a>
                                        </div>
                                    </div>
                                </div>

                                <div class="">
                                    <?php
                                    $sql = "SELECT * FROM  tbl_plantype ";
                                    $retval = mysqli_query($db_con, $sql); // or die('Could not get data: ' . mysqli_error($db_con));
                                    $foundnum = mysqli_num_rows($retval);
                                    if ($foundnum > 0) {
                                        if (is_numeric($_GET['limit'])) {
                                            $per_page = preg_replace("/[^0-9]/", "", $_GET['limit']);
                                        } else {
                                            $per_page = 10;
                                        }
                                        $start = isset($_GET['start']) ? $_GET['start'] : '';
                                        $max_pages = ceil($foundnum / $per_page);
                                        if (!$start) {
                                            $start = 0;
                                        }
                                        ?>
                                        <div class="container">
                                            <div class="pull-right record">
                                                <?php echo $start + 1 ?> to <?php
                                                if (($start + 10) > $foundnum) {
                                                    echo $foundnum;
                                                } else {
                                                    echo ($start + 10);
                                                };
                                                ?> Out Of <span>Total Records: <?php echo $foundnum; ?></span>
    <!--                                                 <input type="text" name="searchfile" id="SearchInput"  class="form-control" style="height:30px">-->
                                            </div>
                                            <div class="box-body">
                                                <?php
                                                $limit = trim($_GET['limit']);

                                                if (isset($limit) and ! empty($limit) and $limit == '') {

                                                    $rec_limit = $limit;
                                                } else {

                                                    $rec_limit = 10;
                                                }
                                                $sql = "SELECT count(plantype) FROM  tbl_plantype ";

                                                $retval = mysqli_query($db_con, $sql);
                                                $row = mysqli_fetch_array($retval, MYSQLI_NUM);
                                                $rec_count = $row[0];
                                                $maxpage = $rec_count / $rec_limit;
                                                $maxpage = ceil($maxpage);
                                                if (isset($_GET{'page'})) {
                                                    $page = $_GET{'page'} + 1;
                                                    $offset = $rec_limit * $page;
                                                    $i = $_GET['index'];
                                                } else {
                                                    $page = 0;
                                                    $offset = 0;
                                                }
                                                $left_rec = $rec_count - ($page * $rec_limit);
                                                $bg = '#E3EDF0'; //variable used to store alternate row colors
                                                ?>
                                                Show <select id="limit">
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
                                                </select> Documents
                                                <?php
                                                $where = '';


                                                $users = mysqli_query($db_con, "select * from tbl_plantype order by plantype asc LIMIT $start, $per_page") or die('Error:' . mysqli_error($db_con));
//                                             
                                                showData($users, $rwgetRole, $db_con, $privilegeSession);
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
                                                            echo " <li><a href='?start=$prev'>Prev</a> </li>";
                                                        else
                                                            echo " <li class='disabled'><a href='javascript:void(0)'>Prev</a> </li>";
                                                        //pages 
                                                        if ($max_pages < 7 + ($adjacents * 2)) {   //not enough pages to bother breaking it up
                                                            $i = 0;
                                                            for ($counter = 1; $counter <= $max_pages; $counter++) {
                                                                if ($i == $start) {
                                                                    echo "<li class='active'><a href='?start=$i'><b>$counter</b></a> </li>";
                                                                } else {
                                                                    echo "<li><a href='?start=$i'>$counter</a></li> ";
                                                                }
                                                                $i = $i + $per_page;
                                                            }
                                                        } elseif ($max_pages > 5 + ($adjacents * 2)) {    //enough pages to hide some
                                                            //close to beginning; only hide later pages
                                                            if (($start / $per_page) < 1 + ($adjacents * 2)) {
                                                                $i = 0;
                                                                for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                                                                    if ($i == $start) {
                                                                        echo " <li class='active'><a href='?start=$i'><b>$counter</b></a></li> ";
                                                                    } else {
                                                                        echo "<li> <a href='?start=$i'>$counter</a> </li>";
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
                                                                        echo " <li class='active'><a href='?start=$i'><b>$counter</b></a></li> ";
                                                                    } else {
                                                                        echo " <li><a href='?start=$i'>$counter</a> </li>";
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
                                                                        echo " <li class='active'><a href='?start=$i'><b>$counter</b></a></li> ";
                                                                    } else {
                                                                        echo "<li> <a href='?start=$i'>$counter</a></li> ";
                                                                    }
                                                                    $i = $i + $per_page;
                                                                }
                                                            }
                                                        }
                                                        //next button
                                                        if (!($start >= $foundnum - $per_page))
                                                            echo "<li><a href='?start=$next'>Next</a></li>";
                                                        else
                                                            echo "<li class='disabled'><a href='javascript:void(0)'>Next</a></li>";
                                                        ?>
                                                    </ul>
                                                    <?php
                                                }
                                                echo "</center>";
                                            }else {
                                                ?>
                                                <div class="form-group no-records-found"><label><i>Add Plan Type !!</i></label></div>
                                            <?php }
                                            ?>	
                                        </div>
                                    </div>
                                    <!-- end: page -->
                                </div> <!-- end Panel -->
                                <!-- end: page -->
                            </div> <!-- end Panel -->
                        </div> <!-- container -->

                    </div> <!-- content -->


                    <!-- /Right-bar -->
                    <!-- MODAL -->
                    <div id="dialog" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog"> 
                            <div class="modal-content"> 
                                <div class="modal-header"> 
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                                    <label><h2 class="panel-title">Are you sure?</h2></label> 
                                </div> 
                                <form method="post">
                                    <div class="modal-body">
                                        <p style="color: red;">Are you sure that you want to delete this Plan?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <div class="col-md-12 text-right">
                                            <input type="hidden" id="uid" name="uid">
                                            <input type="hidden" name="ip" id="ip" class="vkm" >
                                            <button type="submit" name="delete" id="dialogConfirm" class="btn btn-danger waves-effect waves-light">Confirm</button>
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
                                        <h4 class="">Update PlanType</h4> 
                                    </div>

                                    <div class="modal-body" id="modalModify">

                                        <img src="assets/images/load.gif" alt="load" class="img-responsive center-block" />
                                    </div> 
                                    <div class="modal-footer">
                                        <input type="hidden" name="ip" id="ip" class="vkm" >
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button> 
                                        <button type="submit" name="editplan" class="btn btn-primary">Save </button> 
                                    </div>
                                </form>

                            </div> 
                        </div>
                    </div><!-- /.modal -->
                    <div id="group-add" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog "> 
                            <div class="modal-content"> 
                                <form method="post" >
                                    <div class="modal-header"> 
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                                        <h4 class="modal-title">Add New Plan</h4> 
                                    </div>

                                    <div class="modal-body" >

                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label>Number Of User<span style="color:red;">*</span></label>
                                                <input type="text" name="nouser" required class="form-control" id="groupName" placeholder="Number Of User" maxlength="10">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Total Memory<span style="color:red;">*</span></label>
                                                <input type="text" name="tmemory" required class="form-control" id="groupName" placeholder="Total Memory In GB" maxlength="10">
                                            </div>
                                        </div>

                                    </div>


                                    <div class="modal-footer">
                                        <input type="hidden" name="ip" id="ip" class="vkm" >
                                        <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">Close</button> 
                                        <button type="submit" name="addplan" class="btn btn-primary">Add</button> 
                                    </div>

                                </form>
                            </div> 
                        </div> 
                    </div>
                </div><!-- /.modal -->
            </div>
            <!-- END wrapper -->
            <?php require_once './application/pages/footer.php'; ?>

        </div>
        <!-- ============================================================== -->
        <!-- End Right content here -->
        <!-- ============================================================== -->
        <!-- Right Sidebar -->
        <?php require_once './application/pages/rightSidebar.php'; ?>
        <?php require_once './application/pages/footerForjs.php'; ?>

        <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>

        <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>
        <script type="text/javascript" src="assets/jquery.quicksearch.js"></script>
        <script type="text/javascript">
            $(function () {
                /*
                 Example 1
                 */
                $('input#SearchInput').quicksearch('table#gtable tbody tr');

                /*
                 Example 2 
                 */
                /* $('input#SearchInput').quicksearch('table#recyleTable tbody tr', {
                 'delay': 300,
                 'selector': 'th',
                 'stripeRows': ['odd', 'even'],
                 'loader': 'span.loading',
                 'bind': 'keyup click',
                 'show': function () {
                 this.style.color = '';
                 },
                 'hide': function () {
                 this.style.color = '#ccc';
                 },
                 'prepareQuery': function (val) {
                 return new RegExp(val, "i");
                 },
                 'testQuery': function (query, txt, _row) {
                 return query.test(txt);
                 }
                 });*/


            });
            $(document).ready(function () {
                $('form').parsley();

            });
            $(".select2").select2();

        </script>
        <script>
            //for avoid special charecter
            $('#groupName').keyup(function ()
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
            $('#groupName').bind(function () {
                $(this).val($(this).val().replace(/[<>]/g, ""))
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

                    $("#con-close-modal .modal-title").text("Update PlanType " + name + "");
                    $.post("application/ajax/update_plantype.php", {id: $id}, function (result, status) {
                        if (status == 'success') {
                            $("#modalModify").html(result);
                        }
                    });
                });
            });
            $("a#removeRow").click(function () {
                var id = $(this).attr('data');
                $("#uid").val(id);
            });

        </script>
        <!----data table------>
        <script src="assets/plugins/datatables/jquery.dataTables.min.js"></script>
        <script src="assets/plugins/datatables/dataTables.bootstrap.js"></script>
        <script type="text/javascript">

            $(document).ready(function () {
                $('form').parsley();
                $('#datatable').dataTable();

            });
        </script>
        <!-------------->
        <?php
        if (isset($_POST['addplan'])) {


            $ip = $_POST['ip'];
            $num_user = $_POST['nouser'];
            $total_memory = $_POST['tmemory'];
            $check = mysqli_query($db_con, "select * from tbl_plantype where no_users='$num_user' and memory_size='$total_memory'");
            if (mysqli_num_rows($check) <= 0) {
                $insert = mysqli_query($db_con, "insert into tbl_plantype(no_users,memory_size) values($num_user,'$total_memory')") or die('Error : ' . mysqli_error($db_con));
                $gid = mysqli_insert_id($db_con);
                if ($insert) {

                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Plan $num_user-$total_memory Added','$date', null,'$host/$ip','')") or die('error :' . mysqli_error($db_con));
                    echo'<script>taskSuccess("plantype","PlanType Added Successfully !");</script>';
                }
            } else {
                echo'<script>taskFailed("plantype","PlanType already exist !");</script>';
            }
            mysqli_close($db_con);
        }


        if (isset($_POST['editplan'])) {
            $ip = $_POST['ip'];
            $pid = filter_input(INPUT_POST, "pid");
            $pid = preg_replace("/[^0-9]/", "", $pid); //filter name
            $num_user = $_POST['nouser'];
            $total_memory = $_POST['tmemory'];
            $check = mysqli_query($db_con, "select * from tbl_plantype where no_users='$num_user' and memory_size='$total_memory' and plantype!='$pid'");
            if (mysqli_num_rows($check) > 0) {
                echo'<script>taskFailed("plantype","Plantype Name Already Exist !");</script>';
            } else {
                $edit = mysqli_query($db_con, "update tbl_plantype set `no_users`='$num_user',memory_size='$total_memory' where plantype='$pid'") or die('Error : ' . mysqli_error($db_con));
                if ($edit) {

                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Plantype $num_user-$total_memory Updated','$date', null,'$host/$ip',null)") or die('error :' . mysqli_error($db_con));
                    echo'<script>taskSuccess("plantype","Plantype Updated Successfully !");</script>';
                }
            }
            mysqli_close($db_con);
        }
        if (isset($_POST['delete'])) {
            $id = $_POST['uid'];
            $ip = $_POST['ip'];
            $id = preg_replace("/[^0-9]/", "", $id); //filter name
            $id = mysqli_real_escape_string($db_con, $id);
            $delNme = mysqli_query($db_con, "select * from tbl_plantype where plantype='$id'");
            $rwdel = mysqli_fetch_assoc($delNme);
            $deluser = $rwdel['no_users'];
            $delstorage = $rwdel['memory_size'];
            $nouser_plan = $deluser . "-" . $delstorage;
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Plantype $nouser_plan Deleted','$date', null,'$host/$ip','')") or die('error :' . mysqli_error($db_con));
            $del = mysqli_query($db_con, "delete from tbl_plantype where plantype='$id'");
            if ($del) {

                echo'<script>taskSuccess("plantype","Plantype Deleted Successfully !");</script>';
            }
            mysqli_close($db_con);
        }
        ?>

        <?php

        function showData($user, $rwgetRole, $db_con) {
            ?>
            <table class="table table-striped" id="gtable">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Number Of User</th>
                        <th>Total Memory</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    $i += $_GET['start'];
                    while ($rwUser = mysqli_fetch_assoc($user)) {
                        ?>
                        <tr class="gradeX">


                            <td><?php echo $i; ?></td>
                            <td><?php echo $rwUser['no_users']; ?> Users</td>
                            <td><?php echo $rwUser['memory_size']; ?> </td>
                            <td class="actions">

                                <a href="#" class="on-default edit-row btn btn-primary" data-toggle="modal" data-target="#con-close-modal" id="editRow" data="<?php echo $rwUser['plantype']; ?>" 

                                   ><i class="fa fa-pencil"></i> Modify</a>



                                <a href="#" class="on-default remove-row btn btn-danger" data-toggle="modal" data-target="#dialog" id="removeRow" data="<?php echo $rwUser['plantype']; ?>"><i class="fa fa-trash-o"></i> Delete</a>


                            </td>

                        </tr>
                        <?php
                        $i++;
                    }
                    ?>
                </tbody>
            </table>
            <?php
        }
        ?>
    </body>
</html>