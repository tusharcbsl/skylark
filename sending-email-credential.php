<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    require_once './application/config/database.php';
    require_once './application/pages/head.php';

    // echo $rwgetRole['dashboard_mydms']; die;
    if ($rwgetRole['email_credential'] != '1') {
        header('Location: ./index');
    }


    ?>
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
                    <!-- Page-Title -->
                    <div class="row">
                        <div class="col-sm-12">
                            <ol class="breadcrumb">
                                <li>
                                    <a href="#"><?php echo $lang['admin_tool']; ?></a>
                                </li>
                                <li class="active">
                                    <a href="sending-email-credential"><?php echo $lang['config_credential']; ?></a>
                                </li>

                             <!--li> <a href="#" data-toggle="modal" data-target="#help-modal" id="helpview" data="1" title="<?= $lang['help']; ?>"><i class="fa fa-question-circle"></i> </a></li-->
                                <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                            </ol>
                        </div>
                    </div>
                    <?php
                    $retval = mysqli_query($db_con, "SELECT * FROM  `tbl_email_configuration_credential`") or die('Could not get data: ' . mysqli_error($db_con));
                    $foundnum = mysqli_num_rows($retval);
                    ?>
                    <div class="box box-primary">
                        <div class="panel">
                            <div class="panel-body">
                                <div class="container">
                                    <div class="row">
                                        <?php if ($rwgetRole['add_email_credential'] == '1' && $foundnum == '0') { ?>
                                            <div class="col-sm-12">
                                                <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right" data-toggle="modal" data-target="#email-add"><?php echo $lang['add_email_credential']; ?> <i class="fa fa-plus"></i></a>
                                            </div>
                                        <?php } ?>

                                    </div>
                                    <div class="row">
                                        <?php
                                        mysqli_set_charset($db_con, "utf8");
                                        if ($foundnum > 0) {
                                            $StartPoint = preg_replace("/[^0-9]/", "", $_GET['limit']); //filter limit from all special chars
                                            if (is_numeric($StartPoint)) {
                                                $per_page = $StartPoint;
                                            } else {
                                                $per_page = 10;
                                            }
                                            //$start = preg_replace("/[^0-9]/", "", $_GET['start']); //filter start variable
                                            $start = isset($_GET['start']) ? ($_GET['start'] > 0) ? $_GET['start'] : 0 : 0;
                                            $max_pages = ceil($foundnum / $per_page);
                                            if (!$start) {
                                                $start = 0;
                                            }
                                            $limit = $_GET['limit'];
                                            ?>
                                            <div class="box-body">
                                                <label><?php echo $lang['show_lst']; ?> </label> 
                                                <select id="limit" class="input-sm">
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
                                                <label><?php echo $lang['entries_lst']; ?></label>

                                                <div class="pull-right record m-b-15">
                                                    <label><?php echo $start + 1 ?> <?php echo $lang['To']; ?> <?php
                                                        if ($start + $per_page > $foundnum) {
                                                            echo $foundnum;
                                                        } else {
                                                            echo ($start + $per_page);
                                                        }
                                                        ?> <span><?php echo $lang['ttl_recrds']; ?>: <?php echo $foundnum; ?></span></label>
                                                </div>
                                                <?php
                                                mysqli_set_charset($db_con, "utf8");
                                                $emailcre = mysqli_query($db_con, "SELECT * FROM  `tbl_email_configuration_credential` LIMIT $start, $per_page") or die('Error sss:' . mysqli_error($db_con));
                                                showData($emailcre, $rwgetRole, $db_con, $start, $lang);
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
                                                            echo " <li><a href='?start=$prev'>$lang[Prev]</a> </li>";
                                                        else
                                                            echo " <li class='disabled'><a href='javascript:void(0)'>$lang[Prev]</a> </li>";
                                                        //pages 
                                                        if ($max_pages < 7 + ($adjacents * 2)) {   //not enough pages to bother breaking it up
                                                            $i = 0;
                                                            for ($counter = 1; $counter <= $max_pages; $counter++) {
                                                                if ($i == $start) {
                                                                    echo "<li class='active'><a href='?start=$i&limit=$per_page&grpname=" . $_GET['grpname'] . "''><b>$counter</b></a> </li>";
                                                                } else {
                                                                    echo "<li><a href='?start=$i&limit=$per_page&grpname=" . $_GET['grpname'] . "''>$counter</a></li> ";
                                                                }
                                                                $i = $i + $per_page;
                                                            }
                                                        } elseif ($max_pages > 5 + ($adjacents * 2)) {    //enough pages to hide some
                                                            //close to beginning; only hide later pages
                                                            if (($start / $per_page) < 1 + ($adjacents * 2)) {
                                                                $i = 0;
                                                                for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                                                                    if ($i == $start) {
                                                                        echo " <li class='active'><a href='?start=$i&limit=$per_page&grpname=" . $_GET['grpname'] . "''><b>$counter</b></a></li> ";
                                                                    } else {
                                                                        echo "<li> <a href='?start=$i&limit=$per_page&grpname=" . $_GET['grpname'] . "''>$counter</a> </li>";
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
                                                                        echo " <li class='active'><a href='?start=$i&limit=$per_page&grpname=" . $_GET['grpname'] . "''><b>$counter</b></a></li> ";
                                                                    } else {
                                                                        echo " <li><a href='?start=$i&limit=$per_page&grpname=" . $_GET['grpname'] . "''>$counter</a> </li>";
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
                                                                        echo " <li class='active'><a href='?start=$i&limit=$per_page&grpname=" . $_GET['grpname'] . "''><b>$counter</b></a></li> ";
                                                                    } else {
                                                                        echo "<li> <a href='?start=$i&limit=$per_page&grpname=" . $_GET['grpname'] . "''>$counter</a></li> ";
                                                                    }
                                                                    $i = $i + $per_page;
                                                                }
                                                            }
                                                        }
                                                        //next button
                                                        if (!($start >= $foundnum - $per_page))
                                                            echo "<li><a href='?start=$next'>$lang[Next]</a></li>";
                                                        else
                                                            echo "<li class='disabled'><a href='javascript:void(0)'>$lang[Next]</a></li>";
                                                        ?>
                                                    </ul>
                                                    <?php
                                                }
                                                echo "</center>";
                                            } else {
                                                ?>
                                                <table class="table table-striped table-bordered m-t-15">
                                                    <thead>
                                                        <tr>
                                                            <th><?php echo $lang['Sr_No']; ?></th>
                                                            <th><?php echo $lang['host_name']; ?></th>
                                                            <th><?php echo $lang['port_number']; ?></th>
                                                            <th><?php echo $lang['username']; ?></th>
                                                            <th><?php echo $lang['Password']; ?></th>
                                                            <th><?php echo $lang['setFrom']; ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr class="text-center"><td colspan="3"><label><strong class="text-danger"><i class="ti-face-sad text-pink"></i> <?php echo $lang['Who0ps!_No_Records_Found']; ?></strong></label></td></tr>
                                                    </tbody>
                                                </table>
                                            <?php }
                                            ?>	
                                        </div>
                                    </div>
                                    <!-- end: page -->
                                </div> <!-- end Panel -->
                            </div> <!-- container -->

                        </div> <!-- content -->
                    </div>
                    <!-- /Right-bar -->

                    <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog"> 
                            <div class="modal-content"> 

                                <div class="modal-header"> 
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                                    <h4 class="modal-title"><?php echo $lang['edit_email_credential']; ?></h4> 
                                </div>
                                <form method="post" >
                                    <div class="modal-body" id="editcredential">
                                        <img src="assets/images/load.gif" alt="load" class="img-responsive center-block" style="height: 60px;"/>
                                    </div> 
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                                        <button type="submit" name="editmail" class="btn btn-primary"><?php echo $lang['Save']; ?></button> 
                                    </div>
                                </form>

                            </div> 
                        </div>
                    </div>
                    <!-- /.modal -->
                    <div id="email-add" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog "> 
                            <div class="modal-content"> 
                                <form method="post" >
                                    <div class="modal-header"> 
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                                        <h4 class="modal-title"><?php echo $lang['config_email_credential']; ?></h4> 
                                    </div>

                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-sm-12">

                                                <div class="form-group">
                                                    <label class="text-weight"> <?= $lang['host_name']; ?><span class="text-alert">*</span></label>
                                                    <input type="text" class="form-control" placeholder="<?= $lang['host_name']; ?>" name="hostname" required="" maxlength="30"/>
                                                </div>
                                                <div class="form-group">
                                                    <label class="text-weight"> <?= $lang['port_number']; ?><span class="text-alert">*</span></label>
                                                    <input type="text" class="form-control" placeholder="<?= $lang['port_number']; ?>" name="portnumber" required="" maxlength="10"/>
                                                </div>
                                                <div class="form-group">
                                                    <label class="text-weight"> <?= $lang['username']; ?><span class="text-alert">*</span></label>
                                                    <input type="text" class="form-control" placeholder="<?= $lang['username']; ?>" name="username" required="" maxlength="50"/>
                                                </div>
                                                <div class="form-group">
                                                    <label class="text-weight"> <?= $lang['Password']; ?><span class="text-alert">*</span></label>
                                                    <input type="text" class="form-control" placeholder="<?= $lang['Password']; ?>" name="pwd" required="" maxlength="40"/>
                                                </div>
                                                <div class="form-group">
                                                    <label class="text-weight"> <?= $lang['setFrom']; ?><span class="text-alert">*</span></label>
                                                    <input type="text" class="form-control" placeholder="<?= $lang['setFrom']; ?>" name="setform" required="" maxlength="30"/>
                                                </div>
                                            </div>
                                        </div>

                                    </div> 
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                                        <button type="submit" name="setcredential" class="btn btn-primary waves-effect waves-light"><?php echo $lang['Submit']; ?></button>
                                    </div>
                                </form>

                            </div> 
                        </div>
                    </div><!-- /.modal -->
                    <!-- /.modal -->
                    <!-- END wrapper -->
                    <?php require_once './application/pages/footer.php'; ?>

                </div>
                <!-- ============================================================== -->
                <!-- End Right content here -->
                <!-- ============================================================== -->
                <!-- Right Sidebar -->
                <?php //require_once './application/pages/rightSidebar.php';   ?>
                <?php require_once './application/pages/footerForjs.php'; ?>

                <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>

                <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>
                <script type="text/javascript">
                                    $(document).ready(function () {
                                        $('form').parsley();
                                        $('#groupName').on("cut copy paste", function (e) {
                                            e.preventDefault();
                                        });
                                    });
                                    $(".select2").select2();
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
                        var id = $(this).attr('data');
                        var token = $("input[name='token']").val();
                        $.post("application/ajax/edit-email-credential.php", {ID: id, token: token}, function (result, status) {
                            if (status == 'success') {
                                $("#editcredential").html(result);
                                getToken();
                            }
                        });

                    });

                </script>  

                <script type="text/javascript">

                    $(document).ready(function () {
                        $('form').parsley();
                    });
                </script>
                <?php
                if (isset($_POST['setcredential'], $_POST['token'])) {
                    $hostname = preg_replace("/[^a-zA-Z0-9_@.-]/", "", $_POST['hostname']);
                    $portnumber = preg_replace("/[^0-9]/", "", $_POST['portnumber']);
                    $username = base64_encode(preg_replace("/[^a-zA-Z0-9_@.-]/", "", $_POST['username']));
                    $password = base64_encode($_POST['pwd']);
                    $setform = preg_replace("/[^a-zA-Z0-9_@.-]/", "", $_POST['setform']);
                    $setupassword = mysqli_query($db_con, "INSERT INTO `tbl_email_configuration_credential`(`host_name`, `port_number`, `username`, `password`, `setfrom`) VALUES ('$hostname','$portnumber','$username','$password','$setform')") or die('error : set user email password ' . mysqli_error($db_con));
                    if ($setupassword) {
                        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`,`system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Email credential added','$date','$host', 'Sending system email credential added')") or die('error : ' . mysqli_error($db_con));
                        echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Sending_system_email_added'] . '");</script>';
                    } else {
                        echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['failed_Sending_system_email_added'] . '");</script>';
                    }
                }
                ?>
                <?php
                if (isset($_POST['editmail'], $_POST['token'])) {
                    $id = $_POST['id'];
                    $hostname = preg_replace("/[^a-zA-Z0-9_@.-]/", "", $_POST['hostname']);
                    $portnumber = preg_replace("/[^0-9]/", "", $_POST['portnumber']);
                    $username = base64_encode(preg_replace("/[^a-zA-Z0-9_@.-]/", "", $_POST['username']));
                    $password = base64_encode($_POST['pwd']);
                    $setform = preg_replace("/[^a-zA-Z0-9_@.-]/", "", $_POST['setform']);
                    $setupassword = mysqli_query($db_con, "UPDATE `tbl_email_configuration_credential` SET `host_name`='$hostname', `port_number`='$portnumber', `username`='$username', `password`='$password', `setfrom`='$setform' WHERE id='$id'") or die('error : set user email password ' . mysqli_error($db_con));
                    if ($setupassword) {
                        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`,`system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Email credential added','$date','$host', 'Sending system email credential added')") or die('error : ' . mysqli_error($db_con));
                        echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Sending_system_email_edit'] . '");</script>';
                    } else {
                        echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['failed_Sending_system_email_edit'] . '");</script>';
                    }
                }
                ?>

                <?php

                function showData($email, $rwgetRole, $db_con, $start, $lang) {
                    ?>

                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th><?php echo $lang['Sr_No']; ?></th>
                                <th><?php echo $lang['host_name']; ?></th>
                                <th><?php echo $lang['port_number']; ?></th>
                                <th><?php echo $lang['username']; ?></th>
                                <th><?php echo $lang['Password']; ?></th>
                                <th><?php echo $lang['setFrom']; ?></th>
                                <th><?php echo $lang['Actions']; ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            $i += $start;
                            while ($rwEmail = mysqli_fetch_assoc($email)) {
                                ?>
                                <tr class="gradeX">
                                    <td><?php echo $i . '.'; ?></td>
                                    <td><?php echo $rwEmail['host_name']; ?></td>
                                    <td><?php echo $rwEmail['port_number']; ?></td>
                                    <td><?php echo base64_decode($rwEmail['username']); ?></td>
                                    <td><?php echo base64_decode($rwEmail['password']); ?></td>
                                    <td><?php echo $rwEmail['setfrom']; ?></td>
                                    <td class="actions">
                                        <?php if ($rwgetRole['edit_email_credential'] == '1') { ?>
                                            <a href="#" class="on-default edit-row btn btn-primary" data-toggle="modal" data-target="#con-close-modal" id="editRow" data="<?php echo $rwEmail['id']; ?>" title="<?php echo $lang['Modify_column']; ?>"><i class="fa fa-edit"></i> </a>
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