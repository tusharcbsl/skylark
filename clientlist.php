<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    require_once './application/config/database.php';
    require_once './application/pages/head.php';
    require_once './application/pages/function.php';
    $sameGroupIDs = array();
    $group = mysqli_query($db_con, "select group_id from tbl_bridge_grp_to_um where find_in_set('$_SESSION[cdes_user_id]',user_ids)") or die('Error' . mysqli_error($db_con));
    while ($rwGroup = mysqli_fetch_assoc($group)) {
        $sameGroupIDs[] = $rwGroup['group_id'];
    }
    $sameGroupIDs = array_unique($sameGroupIDs);
    sort($sameGroupIDs);
    $sameGroupIDs = implode(',', $sameGroupIDs);
   
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
                                        <a href="clientlist">Client List</a>
                                    </li>
                                    <li class="active">
                                        Client List
                                    </li>
                                </ol>
                            </div>
                        </div>
                        <div class="panel">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <form method="get">
                                            <div class="col-sm-4">
                                                <input type="text" class="form-control" name="cname" value="<?php echo $_GET['cname'] ?>" parsley-trigger="change"  data-parsley-required-message="Enter Company Name for Search"placeholder="Enter Company Name for Search"  />
                                            </div>
                                            <div class="col-sm-3">
                                                <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i>Search</button>
                                                <a href="clientlist" class="btn btn-warning"><i class="fa fa-refresh"></i> Reset</a>
                                            </div>
                                            <div class="col-sm-4">
                                                <a href="exportClient" class="btn btn-primary pull-right"> Export</a>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="" style=" overflow-x:scroll;">
                                    <?php
                                    if (!empty($_GET['cname'])) {
                                        $company = $_GET['cname'];
                                        $condition = " where company like '%$company%'";
                                    }
                                    $sql = "SELECT * FROM  tbl_client_master $condition";
                                    $retval = mysqli_query($db_con, $sql) or die('Could not get data: ' . mysqli_error($db_con));
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

                                        <div class="box-body">
                                            <?php
                                            $limit = trim($_GET['limit']);

                                            if (isset($limit) and!empty($limit) and $limit == '') {

                                                $rec_limit = $limit;
                                            } else {

                                                $rec_limit = 10;
                                            }
                                            $sql = "SELECT count(client_id) FROM  `tbl_client_master` $condition";

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
                                            </select> Client
                                            <div class="pull-right record">
                                                <?php echo $start + 1 ?> to <?php
                                                if (($start + 10) > $foundnum) {
                                                    echo $foundnum;
                                                } else {
                                                    echo ($start + 10);
                                                };
                                                ?> Out Of <span>Total Records: <?php echo $foundnum; ?></span>

                                            </div>
                                            <?php
                                            $where = '';
                                            $users = mysqli_query($db_con, "select * from tbl_client_master $condition order by client_id asc LIMIT $start, $per_page") or die('Error:' . mysqli_error($db_con));
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
                                                        echo " <li><a href='?start=" . $prev . "&limit=" . $_GET['limit'] . "'>Prev</a> </li>";
                                                    else
                                                        echo " <li class='disabled'><a href='javascript:void(0)'>Prev</a> </li>";
                                                    //pages 
                                                    if ($max_pages < 7 + ($adjacents * 2)) {   //not enough pages to bother breaking it up
                                                        $i = 0;
                                                        for ($counter = 1; $counter <= $max_pages; $counter++) {
                                                            if ($i == $start) {
                                                                echo "<li class='active'><a href='?start=" . $i . "&limit=" . $_GET['limit'] . "'><b>$counter</b></a> </li>";
                                                            } else {
                                                                echo "<li><a href='?start=" . $i . "&limit=" . $_GET['limit'] . "'>$counter</a></li> ";
                                                            }
                                                            $i = $i + $per_page;
                                                        }
                                                    } elseif ($max_pages > 5 + ($adjacents * 2)) {    //enough pages to hide some
                                                        //close to beginning; only hide later pages
                                                        if (($start / $per_page) < 1 + ($adjacents * 2)) {
                                                            $i = 0;
                                                            for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                                                                if ($i == $start) {
                                                                    echo " <li class='active'><a href='?start=" . $i . "&limit=" . $_GET['limit'] . "'><b>$counter</b></a></li> ";
                                                                } else {
                                                                    echo "<li> <a href='?start=" . $i . "&limit=" . $_GET['limit'] . "'>$counter</a> </li>";
                                                                }
                                                                $i = $i + $per_page;
                                                            }
                                                        }
                                                        //in middle; hide some front and some back
                                                        elseif ($max_pages - ($adjacents * 2) > ($start / $per_page) && ($start / $per_page) > ($adjacents * 2)) {
                                                            echo " <li><a href='?start=0&limit=" . $_GET['limit'] . "'>1</a></li> ";
                                                            echo "<li><a href='?start=" . $per_page . "&limit=" . $_GET['limit'] . "'>2</a></li>";
                                                            echo "<li><a href='javascript:void(0)'>...</a></li>";

                                                            $i = $start;
                                                            for ($counter = ($start / $per_page) + 1; $counter < ($start / $per_page) + $adjacents + 2; $counter++) {
                                                                if ($i == $start) {
                                                                    echo " <li class='active'><a href='?start=" . $i . "&limit=" . $_GET['limit'] . "'><b>$counter</b></a></li> ";
                                                                } else {
                                                                    echo " <li><a href='?start=" . $i . "&limit=" . $_GET['limit'] . "'>$counter</a> </li>";
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
                                                                    echo " <li class='active'><a href='?start=" . $i . "&limit=" . $_GET['limit'] . "'><b>$counter</b></a></li> ";
                                                                } else {
                                                                    echo "<li> <a href='?start=" . $i . "&limit=" . $_GET['limit'] . "'>$counter</a></li> ";
                                                                }
                                                                $i = $i + $per_page;
                                                            }
                                                        }
                                                    }
                                                    //next button
                                                    if (!($start >= $foundnum - $per_page))
                                                        echo "<li><a href='?start=" . $next . "&limit=" . $_GET['limit'] . "'>Next</a></li>";
                                                    else
                                                        echo "<li class='disabled'><a href='javascript:void(0)'>Next</a></li>";
                                                    ?>
                                                </ul>
                                                <?php
                                            }
                                            echo "</center>";
                                        } else {
                                            ?>
                                            <div class="form-group no-records-found"><label><i>No Client Found !!</i></label></div>
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

                <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog"> 
                        <div class="modal-content"> 
                            <form method="post" >
                                <div class="modal-header"> 
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> 
                                    <h4 class="">Update Client</h4> 
                                </div>

                                <div class="modal-body" id="modalModify">

                                    <img src="assets/images/load.gif" alt="load" class="img-responsive center-block" />
                                </div> 
                                <div class="modal-footer">

                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button> 
                                    <button type="submit" name="editplan" class="btn btn-primary">Save </button> 
                                </div>
                            </form>

                        </div> 
                    </div>
                </div><!-- /.modal -->
                <!-- /Right-bar -->
                <!-- MODAL -->
                <!-- end Modal -->
            </div><!-- /.modal -->
        </div>
        <!-- END wrapper -->
        <div id="con-close-modal2" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog"> 
                <div class="modal-content"> 
                    <div class="modal-header"> 
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button> 
                        <h4 class="modal-title">Delete Client</h4> 
                    </div> 
                    <form method="post">
                        <div class="modal-body">
                            <p style="color: red;">Are you sure that you want to delete this client? </p>
                        </div>
                        <div class="modal-footer"> 
                            <input type="hidden" id="uid" name="uid">
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                            <input type="submit" name="deleteclient" class="btn btn-danger" value="Delete">
                        </div>
                    </form>
                </div> 
            </div>
        </div><!--ends delete modal -->
        <div id="activeClient" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog"> 
                <div class="modal-content"> 
                    <div class="modal-header"> 
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button> 
                        <h4 class="modal-title">Active Client</h4> 
                    </div> 
                    <form method="post">
                        <div class="modal-body">
                            <p class="text-danger">Are you sure that you want to de-activate this client? </p>
                        </div>
                        <div class="modal-footer"> 
                            <input type="hidden" id="cid" name="cid">
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                            <button type="submit" name="deactiveclient" class="btn btn-danger"><i class="fa fa-toggle-off"></i> De-activate</button>
                        </div>
                    </form>
                </div> 
            </div>
        </div><!--ends delete modal -->
        <div id="deactiveClient" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog"> 
                <div class="modal-content"> 
                    <div class="modal-header"> 
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button> 
                        <h4 class="modal-title">In-active Client</h4> 
                    </div> 
                    <form method="post">
                        <div class="modal-body">
                            <p class="text-primary">Are you sure that you want to activate this client? </p>
                        </div>
                        <div class="modal-footer"> 
                            <input type="hidden" id="aid" name="aid">
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                            <button type="submit" name="activateclient" class="btn btn-success"><i class="fa fa-toggle-off"></i> De-activate</button>
                        </div>
                    </form>
                </div> 
            </div>
        </div><!--ends delete modal -->

        <!-- END wrapper -->
        <div id="con-close-modal23" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog"> 
                <div class="modal-content"> 
                    <div class="modal-header"> 
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> 
                        <h4 class="modal-title">Update Client Database</h4> 
                    </div> 
                    <form method="post">
                        <div class="modal-body">
                            <p style="color: red;">Are you sure that you want to update this client database? </p>
                        </div>
                        <div class="modal-footer"> 
                            <input type="hidden" id="gid" name="gid">
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                            <input type="submit" name="dbupdate" class="btn btn-primary" value="Update">
                        </div>
                    </form>
                </div> 
            </div>
        </div><!--ends delete modal -->
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
                $.post("application/ajax/update_client_list.php", {id: $id}, function (result, status) {
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
        $("a#deActiveClient").click(function () {
            var id = $(this).attr('data');
            $("#aid").val(id);
        });
        $("a#deActiveClient").click(function () {
            var id = $(this).attr('data');
            $("#cid").val(id);
        });

        $("a#updateDB").click(function () {
            var id = $(this).attr('data');
            $("#gid").val(id);
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

        function copyText(Id) {
            /* Get the text field */
            var copyText = document.getElementById("license_key" + Id);

            copyText.select();
            document.execCommand("copy");

            $("#copybtn" + Id).val('Copied');

            setInterval(function () {
                $("#copybtn" + Id).val('Copy');
            }, 2000);

        }

    </script>
    <!-------------->
    <?php
    if (isset($_POST['editplan']) && !empty($_POST['pid'])) {
        $id = $_POST['pid'];
        $plantype = $_POST['plantype'];
        $num_User = $_POST['nouser'];
        $total_Memory = $_POST['tomemory'];
        $producttype = $_POST['product_type'];
        $client_db = mysqli_query($db_con, "select * from tbl_client_master where client_id='$id'")or die(mysqli_error($db_con));


        $clientName = mysqli_fetch_assoc($client_db);
        $insertKey = "";
        if (empty($clientName['license_key'])) {

            $clientdb = $clientName['db_name'];
            $clientId = $clientName['client_id'];
            $license_key = generateLicenseKey($clientdb, $clientId);
            $insertKey = ", license_key='$license_key'";
        }
        $plan_type = $clientName['plan_type']; //fetch plantype from client master table
//            $current_plan_qry = mysqli_query($db_con, "select * from `tbl_plantype` where plantype='$plantype'");
//            $fetch_plan = mysqli_fetch_assoc($current_plan_qry);
        $no_users = $_POST['nouser']; //total number of users
        $total_memory = $_POST['tomemory']; //total memory assign to cilent in GB
        $data_name = $clientName['db_name']; //fetch database name from client master table

        if (!empty($_POST['validupto_month']) || !empty($_POST['validupto_year'])) {
            $validupto = filter_input(INPUT_POST, "validupto_month");
            $validupto = mysqli_real_escape_string($db_con, $validupto);
            $validupto_year = filter_input(INPUT_POST, "validupto_year");
            $lastvalidity = date("Y-m-d", $clientName['valid_upto']);
            $validupto = strtotime(date("Y-m-d", strtotime($lastvalidity . " +" . $validupto . " " . $validupto_year))); //end of validity in time stamp
            //echo date("Y-m-d",$validupto);
            //die();
        } else {
            $validupto = strtotime($_POST['enddate']);
        }
        if (!empty($_POST['product_type'] && !empty($clientName))) {
            $conn = new mysqli($dbHost, $dbUser, $dbPwd, $data_name) or die(mysqli_connect_error());
            if ($conn) {
                //echo "furrrrrun1";
                // print_r($conn);
                $validate_memory_size = mysqli_query($conn, "select sum(doc_size) as total_memory from `tbl_document_master`") or die(mysqli_error($conn));
                $total_consume_memory = mysqli_fetch_assoc($validate_memory_size);
                $t_user += 1; //1 extra user for super user
                $validate_client_user_qry = mysqli_query($conn, "select count(user_id) as total_users from `tbl_user_master`") or die(mysqli_error($db_con));
                ;
                $total_client_user = mysqli_fetch_assoc($validate_client_user_qry);
                $total_memory = convertIntoBytesMethod($total_memory);
                $no_users += 1; //1 extra for super user
                if (($total_consume_memory['total_memory'] < $total_memory) && ($total_client_user['total_users'] <= $no_users)) {
                    // echo "runn2";
                    $update = mysqli_query($db_con, "update tbl_client_master set product_type='$producttype',total_memory='$total_Memory',total_user='$num_User',valid_upto=$validupto $insertKey where client_id=$id") or die(mysqli_error($db_con));

                    if ($update) {
                        //  $delete = mysqli_query($conn, "delete  from tbl_user_roles where role_id='2'")or die(mysqli_error($conn));
                        $data_role_col = mysqli_query($db_con, "show COLUMNS FROM `tbl_user_roles`")or die(mysqli_error($db_con));
                        while ($row = mysqli_fetch_assoc($data_role_col)) {
                            $role_fieldName[] = $row['Field'];
                        }
                        //$producttypename= $role_fieldName[1];
                        array_splice($role_fieldName, 0, 1); //remove auto increment and rolename from array
                        //array_splice($role_fieldName,1, 1);
                        //unset($role_fieldName[0]); // remove role id becoz it is autoincrement

                        $data_cols_role = implode(",", $role_fieldName);
                        // print_r($role_fieldName);
                        $result_cols = array();
                        $selected_roles_data = mysqli_query($db_con, "select $data_cols_role from `tbl_user_roles` where role_id='$producttype'");
                        $newdata = mysqli_fetch_all($selected_roles_data);
                        //print_r($newdata[0]);
                        $new_update = "update `tbl_user_roles` SET ";

                        for ($i = 0; $i < count($newdata[0]); $i++) {
                            if ($i < count($newdata[0]) - 1) {
                                $new_update .= $role_fieldName[$i] . "=" . "'" . $newdata[0][$i] . "',";
                            } else {
                                $new_update .= $role_fieldName[$i] . "=" . "'" . $newdata[0][$i] . "'";
                            }
                        }
                        $new_update .= " where role_id='2'";
                        // echo $new_update;
                        // $new_imploded_data = "'" . implode("'" . "," . "'", $newdata[0]) . "'";
                        $Insert_New_User = mysqli_query($conn, $new_update);
                        $qry = mysqli_query($conn, "select * from tbl_user_roles where role_id>2");
                        while ($row = mysqli_fetch_assoc($qry)) {
                            array_splice($role_fieldName, 0, 2); //remove auto increment and rolename from array


                            $data_cols_role = implode(",", $role_fieldName);
                            // print_r($role_fieldName);
                            $result_cols = array();
                            $selected_roles_data = mysqli_query($db_con, "select $data_cols_role from `tbl_user_roles` where role_id='$producttype'");
                            $newdata = mysqli_fetch_all($selected_roles_data);
                            //    print_r($newdata[0]);
                            $new_update = "update `tbl_user_roles` SET user_role='$row[user_role]',";

                            for ($i = 0; $i < count($newdata[0]); $i++) {
                                if ($i < count($newdata[0]) - 1) {
                                    $new_update .= $role_fieldName[$i] . "=" . "'" . $newdata[0][$i] . "',";
                                } else {
                                    $new_update .= $role_fieldName[$i] . "=" . "'" . $newdata[0][$i] . "'";
                                }
                            }
                            $new_update .= " where role_id='$row[role_id]'";
                            // echo $new_update;
                            $Insert_New_User = mysqli_query($conn, $new_update);
                        }

                        if ($Insert_New_User) {
                            echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","Client Updated Successfully !");</script>';
                        } else {
                            echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","Client Validity updated sucessfully.Failed to change plan.!!!");</script>';
                        }
                    } else {
                        echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","Client Update Failed!");</script>';
                    }
                } else {
                    echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","Client Changed Plan Failed!");</script>';
                }
                mysqli_close($conn);
            } else {
                echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","Connection Failed !");</script>';
            }
        }
    }

    function generateLicenseKey($clientdb, $clientId) {

        $key = '987654123';
        $plaintext = $clientdb . '%' . $clientId;
        $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($plaintext, $cipher, $key, $options = OPENSSL_RAW_DATA, $iv);
        $ciphertext = base64_encode($iv . /* $hmac. */$ciphertext_raw);

        return $ciphertext;
    }

    /*
     * Remove client
     */
    if (isset($_POST['deleteclient'])) {
        $clientId = $_POST['uid'];
        $fetchqry = mysqli_query($db_con, "select * from `tbl_client_master` where client_id='$clientId'");
        $fetchCInfo = mysqli_fetch_assoc($fetchqry);
        $dbInfo = $fetchCInfo['db_name'];
        if (!empty($dbInfo)) {
            $pathdirdel = $_SERVER['DOCUMENT_ROOT'] . "/ezeefile_saas_client/" . $dbInfo;

            if (is_dir($pathdirdel)) {
                rrmdir($pathdirdel); //  or die('Error:'.print_r(error_get_last()));
            }
        } else {
            echo "no exist";
        }
        //die('ok');
        $dropConn = new mysqli($dbHost, $dbUser, $dbPwd, $dbInfo) or die(mysqli_connect_error());
        $qryDrop = mysqli_query($dropConn, "DROP DATABASE " . $dbInfo);
        if ($qryDrop) {

            $delfromclient = mysqli_query($db_con, "delete from `tbl_client_master` where client_id='$clientId'");
            if ($delfromclient) {


                echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","Delete Successfully");</script>';
            } else {
                echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","Delete Failed!");</script>';
            }
        }
    }

    if (isset($_POST['dbupdate'])) {
        $clientId = $_POST['gid'];
        $fetchqry = mysqli_query($db_con, "select * from `tbl_client_master` where client_id='$clientId'");
        $fetchCInfo = mysqli_fetch_assoc($fetchqry);
        $dbInfo = $fetchCInfo['db_name'];
        if (!empty($dbInfo)) {
            require './updatedb.php';
            $updateTable = new MatchTable();
            $updateTable->setTableOneConnection($dbHost, $dbUser, $dbPwd, "ezeefile_saas");
            $updateTable->setTableTwoConnection($dbHost, $dbUser, $dbPwd, $dbInfo);

            if ($updateTable->fetchTablesCols()) {
                echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","Update Successfully");</script>';
            } else {
                echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","Update Failed!");</script>';
            }
        } else {
            echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","Something Went Worng!");</script>';
        }
    }

    function rrmdir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir . "/" . $object))
                        rrmdir($dir . "/" . $object);
                    else
                        unlink($dir . "/" . $object);
                }
            }
            rmdir($dir);
        }
    }
    ?>

    <?php

    function showData($user, $rwgetRole, $db_con) {
        ?>
        <table class="table table-striped table-bordered" id="gtable" style=" overflow:scroll">
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>CRM Customer ID</th>
                    <th>Name</th>
                    <th>Client Email</th>
                    <th>Company Name</th>
                    <th>Plan Type</th>
                    <th>Product Type</th>
                    <th>Expired Date</th>
                    <th>Database Name</th>
                    <th>License Key</th>
                    <th>Domain Name</th>
                    <th>Action</th>
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
                        <td><?php echo $rwUser['crm_cid']; ?> </td>
                        <td><?php echo $rwUser['fname'] . " " . $rwUser['lname']; ?> </td>
                        <td><?php echo $rwUser['email']; ?> </td>
                        <td><?php echo $rwUser['company']; ?> </td>
                        <td><label class="label label-primary"><?php
                            echo $rwUser['total_user'] . " Users" . " " . $rwUser['total_memory'] . " " . "GB";
                            ?> </label></td>
                        <td><label class="label label-info"><?php
                            $qry_product = mysqli_query($db_con, "select * from tbl_user_roles where role_id='$rwUser[product_type]'");
                            $qry_product_fetch = mysqli_fetch_assoc($qry_product);
                            echo $qry_product_fetch['user_role'];
                            ?> </label></td>
                        <td><label class="label label-success"><?php echo date("d-m-Y", $rwUser['valid_upto']); ?> </label></td>
                        <td><?php echo $rwUser['db_name']; ?> </td>
                        <td><?php echo ((!empty($rwUser['license_key'])) ? substr($rwUser['license_key'], 0, 20) . '...' : ""); ?> 
                            <?php if ($rwUser['license_key']) { ?>
                            <a href="javascript:void(0);" data-toggle="modal" class="btn btn-sm btn-primary" data-target="#licensekeyModal<?php echo $i; ?>" style="float:left;"><i class="fa fa-eye"></i></a>

                                <div id="licensekeyModal<?php echo $i; ?>" class="modal fade" role="dialog">
                                    <div class="modal-dialog">

                                        <!-- Modal content-->
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                <h4 class="modal-title">License Key #<?php echo $rwUser['crm_cid']; ?></h4>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label for="emailAddress">License Key<span style="color:red;"></span></label>
                                                    <div class="input-group">

                                                        <input type="text" name="licensekey" parsley-trigger="change" placeholder="" class="form-control" id="license_key<?php echo $i; ?>" value="<?php echo $rwUser['license_key']; ?>" readonly>
                                                        <span class="input-group-btn">  
                                                            <input type="button" name="copybtn" parsley-trigger="change" placeholder="" class="form-control btn-primary" id="copybtn<?php echo $i; ?>" title="Click to copy" value="Copy" onclick="copyText('<?php echo $i; ?>');" >
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            <?php } ?>
                        </td>
                        <td><?php echo $rwUser['subdomain']; ?></td>
                        <td class="actions">

                            <a href="#" class="on-default edit-row btn btn-primary btn-sm" data-toggle="modal" data-target="#con-close-modal" title="Edit Client" id="editRow" data="<?php echo $rwUser['client_id']; ?>" 

                               ><i class="fa fa-pencil"></i> </a>
                            <a href="javascript:void(0)" data-toggle="modal" class="btn btn-info btn-sm" data-target="#con-close-modal23" title="Update Client Database" id="updateDB" data="<?php echo $rwUser['client_id']; ?>"><i class="fa fa-bookmark"></i> </a>
                            <a href="javascript:void(0)" data-toggle="modal" class="btn btn-danger btn-sm" data-target="#con-close-modal2" title="Delete Client" id="removeRow" data="<?php echo $rwUser['client_id']; ?>"><i class="fa fa-trash-o"></i> </a>
                            <?php if ($rwUser['active_status'] == '1') { ?>
                                <a href="javascript:void(0)" data-toggle="modal" class="btn btn-success btn-sm" data-target="#activeClient" title="De-activate Client" id="deActiveClient" data="<?php echo $rwUser['client_id']; ?>"><i class="fa fa-toggle-on"></i> </a>
                            <?php } elseif ($rwUser['active_status'] == '0') { ?>
                                <a href="javascript:void(0)" data-toggle="modal" class="btn btn-danger btn-sm" data-target="#deactiveClient" title="Activate Client" id="ActiveClient" data="<?php echo $rwUser['client_id']; ?>"><i class="fa fa-toggle-off"></i> </a>
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
    }
    ?>
</body>
</html>
<?php
if (isset($_POST['activateclient'])) {
    $inactiveClientId = $_POST['aid'];
    $userNme = mysqli_query($db_con, "SELECT * FROM tbl_client_master where client_id='$inactiveClientId'");
    $rwUserNme = mysqli_fetch_assoc($userNme);
    $firstName = $rwUserNme['first_name'];
    $lastName = $rwUserNme['last_name'];
    $status = mysqli_query($db_con, "UPDATE tbl_client_master set active_status='1' WHERE client_id='$inactiveClientId'");
    if ($status) {
        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`, `action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Client Activated','$date','$host','Client name $firstName $lastName Activated')"); //or die('error : ' . mysqli_error($db_con));
        echo '<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","Client Activated Successfully.");</script>';
    }
}
?>
<?php
if (isset($_POST['deactiveclient'])) {
    $activeClientId = $_POST['cid'];
    $userNme = mysqli_query($db_con, "SELECT * FROM tbl_client_master where client_id='$activeClientId'");
    $rwUserNme = mysqli_fetch_assoc($userNme);
    $firstName = $rwUserNme['first_name'];
    $lastName = $rwUserNme['last_name'];
    $status = mysqli_query($db_con, "UPDATE tbl_client_master set active_status='0' WHERE client_id='$activeClientId'");
    if ($status) {
        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`, `action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Client De-activated','$date','$host','Client name $firstName $lastName de-activated')"); //or die('error : ' . mysqli_error($db_con));
        echo '<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","Client De-activated Successfully.");</script>';
    }
}
?>