<!DOCTYPE html>
<html>
    <?php
    //$path = $_SERVER['REQUEST_URI'];
    // $uri = $path; 
    require_once './loginvalidate.php';
    require_once './application/config/database.php';
    require_once './application/pages/head.php';

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
                                        <?php echo $lang['User_Wise']; ?>
                                    </li>
                                </ol>
                            </div>
                        </div>
                        <div class="box box-primary">
                            <div class="panel">

                                <div class="panel-body">
                                    <div class="">
                                        <div class="box-body">
                                            <!--div class="row">

                                            <!--div class="col-md-6">
                                                Show <select id="limit">
                                                    <option value="10" <?php
                                            if ($_GET['limit'] == 10) {
                                                //echo 'selected';
                                            }
                                            ?>>10</option>
                                                    <option value="25" <?php
                                            if ($_GET['limit'] == 25) {
                                                //echo 'selected';
                                            }
                                            ?>>25</option>
                                                    <option value="50" <?php
                                            if ($_GET['limit'] == 50) {
                                                // echo 'selected';
                                            }
                                            ?>>50</option>
                                                    <option value="100" <?php
                                            if ($_GET['limit'] == 100) {
                                                //echo 'selected';
                                            }
                                            ?>>100</option>
                                                    <option value="200" <?php
                                            if ($_GET['limit'] == 200) {
                                                //echo 'selected';
                                            }
                                            ?>>200</option>
                                                </select> Users
                                            </div-->

                                            <form method="get">
                                                <div class="col-md-3">
                                                    <?php
                                                    $user = "SELECT distinct user_name FROM tbl_ezeefile_logs where user_id in($sameGroupIDs) AND user_id!=1  order by user_name";
                                                    $user_run = mysqli_query($db_con, $user) or die('Error:' . mysqli_error($db_con));
                                                    ?>
                                                    <select class="form-control pull-left select3" id="my_multi_select1" name="userLog" required>
                                                        <option selected disabled style="background: #808080; color: #121213;"><?php echo $lang['Select_User_Name']; ?></option>
                                                        <?php
                                                        while ($rwUser = mysqli_fetch_assoc($user_run)) {
                                                            if ($rwUser['user_id'] != 1) {
                                                                ?>
                                                                <option <?php
                                                                if (isset($_GET['userLog']) && $_GET['userLog'] == $rwUser['user_name']) {
                                                                    echo'selected';
                                                                }
                                                                ?>><?php echo $rwUser['user_name']; ?></option>

                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <button type="submit"  class="btn btn-primary pull-left"><?php echo $lang['Search']; ?><i class="fa fa-search"></i></button>
                                                    <button  type="reset" name="reset" class="btn btn-primary pull-left" style="margin-left: 11px;"><?php echo $lang['Reset']; ?></button>
                                                </div>
                                            </form>
                                        </div>


                                        <?php
                                        $where = '';
                                        if (isset($_GET['userLog']) && !empty($_GET['userLog'])) {
                                            $where = "where action_name ='Login/Logout' AND user_name ='$_GET[userLog]' and user_id in($sameGroupIDs) order by start_date desc";
                                        } else {
                                            $where = "where action_name ='Login/Logout' and user_id in($sameGroupIDs) order by start_date desc";
                                        }
//                                            $constructs = "SELECT * FROM tbl_ezeefile_logs $where";
//                                            $run = mysqli_query($db_con, $constructs) or die('Error' . mysqli_error($con));
//
//                                            $foundnum = mysqli_num_rows($run);
//
//
//                                            if ($foundnum > 0) {
//
//                                                if (isset($_GET['limit'])) {
//                                                    if (!empty($_GET['limit'])) {
//                                                        $per_page = $_GET['limit'];
//                                                    } else {
//                                                        $per_page = 10;
//                                                    }
//                                                } else {
//                                                    $per_page = 10;
//                                                }
//
//
//                                                //$per_page = 10;
//                                                $start = isset($_GET['start']) ? $_GET['start'] : '';
//                                                $max_pages = ceil($foundnum / $per_page);
//                                                if (!$start) {
//                                                    $start = 0;
//                                                }
                                        $allot = "select * from tbl_ezeefile_logs $where";

                                        $allot_query = mysqli_query($db_con, $allot) or die("Error: " . mysqli_error($con));
                                        ?>
                                        <div class="container" >
                                            <!--div class="pull-right record">
                                            <?php // echo $start + 1 ?> to <?php
//                                                    if (($start + $per_page) > $foundnum) {
//                                                        echo $foundnum;
//                                                    } else {
//                                                        echo ($start + $per_page);
//                                                    };
                                            ?> Out Of <span>Total Records: <?php //echo $foundnum;  ?></span>
                                            </div-->
                                            <table class="table table-striped table-bordered dataTable no-footer" id="datatable">
                                                <thead>
                                                    <tr>
                                                        <th><?php echo $lang['Sr_No']; ?></th>
                                                        <th><?php echo $lang['User_Name']; ?></th>
                                                        <th><?php echo $lang['Action_Performed']; ?></th>
                                                        <th><?php echo $lang['Action_Start_Date']; ?></th>
                                                        <th><?php echo $lang['Action_End_Date']; ?></th>
                                                        <th><?php echo $lang['Sys_IP']; ?></th>
                                                        <th><?php echo $lang['Remarks']; ?></th>
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
                                                            <td><?php echo $allot_row['remarks']; ?></td>
                                                        </tr>
                                                        <?php
                                                        $n++;
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>

                                            <center>
                                                <?php
//                                                        $prev = $start - $per_page;
//                                                        $next = $start + $per_page;
//
//                                                        $adjacents = 3;
//                                                        $last = $max_pages - 1;
//                                                        if ($max_pages > 1) {
//                                                            
                                                ?>
                                                <!--ul class='pagination'>
                                                //<?php
//                                                            //previous button
//                                                            if (!($start <= 0))
//                                                                echo " <li><a href='?userLog=$_GET[userLog]&start=$prev&limit=$per_page'>Prev</a> </li>";
//                                                            else
//                                                                echo " <li class='disabled'><a href='javascript:(0)'>Prev</a> </li>";
//                                                            //pages 
//                                                            if ($max_pages < 7 + ($adjacents * 2)) {   //not enough pages to bother breaking it up
//                                                                $i = 0;
//                                                                for ($counter = 1; $counter <= $max_pages; $counter++) {
//                                                                    if ($i == $start) {
//                                                                        echo " <li><a href='?userLog=$_GET[userLog]&start=$i&limit=$per_page'><b>$counter</b></a> </li>";
//                                                                    } else {
//                                                                        echo "<li><a href='?userLog=$_GET[userLog]&start=$i&limit=$per_page'>$counter</a></li> ";
//                                                                    }
//                                                                    $i = $i + $per_page;
//                                                                }
//                                                            } elseif ($max_pages > 5 + ($adjacents * 2)) {    //enough pages to hide some
//                                                                //close to beginning; only hide later pages
//                                                                if (($start / $per_page) < 1 + ($adjacents * 2)) {
//                                                                    $i = 0;
//                                                                    for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
//                                                                        if ($i == $start) {
//                                                                            echo " <li><a href='?userLog=$_GET[userLog]&start=$i&limit=$per_page'><b>$counter</b></a></li> ";
//                                                                        } else {
//                                                                            echo "<li> <a href='?userLog=$_GET[userLog]&start=$i&limit=$per_page'>$counter</a> </li>";
//                                                                        }
//                                                                        $i = $i + $per_page;
//                                                                    }
//                                                                }
//                                                                //in middle; hide some front and some back
//                                                                elseif ($max_pages - ($adjacents * 2) > ($start / $per_page) && ($start / $per_page) > ($adjacents * 2)) {
//                                                                    echo " <li><a href='?userLog=$_GET[userLog]&start=0&limit=$per_page'>1</a></li> ";
//                                                                    echo "<li><a href='?userLog=$_GET[userLog]&start=$per_page&limit=$per_page'>2</a></li>";
//                                                                    echo "<li><a href='javascript:(0)'>...</a></li>";
//
//                                                                    $i = $start;
//                                                                    for ($counter = ($start / $per_page) + 1; $counter < ($start / $per_page) + $adjacents + 2; $counter++) {
//                                                                        if ($i == $start) {
//                                                                            echo " <li><a href='?userLog=$_GET[userLog]&start=$i&limit=$per_page'><b>$counter</b></a></li> ";
//                                                                        } else {
//                                                                            echo " <li><a href='?userLog=$_GET[userLog]&start=$i&limit=$per_page'>$counter</a> </li>";
//                                                                        }
//                                                                        $i = $i + $per_page;
//                                                                    }
//                                                                }
//                                                                //close to end; only hide early pages
//                                                                else {
//                                                                    echo "<li> <a href='?userLog=$_GET[userLog]&start=0&limit=$per_page'>1</a> </li>";
//                                                                    echo "<li><a href='?userLog=$_GET[userLog]&start=$per_page&limit=$per_page'>2</a></li>";
//                                                                    echo "<li><a href='javascript:(0)'>...</a></li>";
//
//                                                                    $i = $start;
//                                                                    for ($counter = ($start / $per_page) + 1; $counter <= $max_pages; $counter++) {
//                                                                        if ($i == $start) {
//                                                                            echo " <li><a href='?userLog=$_GET[userLog]&start=$i&limit=$per_page'><b>$counter</b></a></li> ";
//                                                                        } else {
//                                                                            echo "<li> <a href='?userLog=$_GET[userLog]&start=$i&limit=$per_page'>$counter</a></li> ";
//                                                                        }
//                                                                        $i = $i + $per_page;
//                                                                    }
//                                                                }
//                                                            }
//                                                            //next button
//                                                            if (!($start >= $foundnum - $per_page))
//                                                                echo "<li><a href='?userLog=$_GET[userLog]&start=$next&limit=$per_page'>Next</a></li>";
//                                                            else
//                                                                echo "<li class='disabled'><a href='javascript:(0)'>Next</a></li>";
//                                                            
                                                ?>
                                                </ul-->
                                                <?php
//                                                        }
                                                ?>
                                            </center>
                                        </div>
                                        <?php
                                        //}
//                                            else {
//                                                //echo '<!--div style="text-align:center;"><h2>No Records Founds</h2>'
//                                                //. '<p style="color:red;">This is Deleted User...!!!</p></div-->';
//                                            }
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

    <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>

    <!----data table------>
    <script src="assets/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="assets/plugins/datatables/dataTables.bootstrap.js"></script>


    <script type="text/javascript">

        $(document).ready(function () {
            $('form').parsley();
            $('#datatable').dataTable();
        });
        //for searchable select

        $(".select3").select2();
    </script>
    <!-------------->

<!--    <script type="text/javascript">
        $(document).ready(function () {
            $('form').parsley();

        });
        $(".select2").select2();
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
            var $row = $(this).closest('tr');
            var name = '';
            var values = [];
            values = $row.find('td:first-child').map(function () {
                var $this = $(this);
                if ($this.hasClass('actions')) {

                } else {
                    name = $.trim($this.text());
                    //$.trim( $this.text());
                }

                $("#con-close-modal .modal-title").text("Update " + name + "'s Profile");
                $.post("application/ajax/updateUser.php", {ID: $id}, function (result, status) {
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

    </script>-->

</body>


</html>

