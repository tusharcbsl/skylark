<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    require_once './application/config/database.php';
    require_once './application/pages/head.php';

    //for user role
     mysqli_set_charset($db_con, 'utf8');
    $chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

    $rwgetRole = mysqli_fetch_assoc($chekUsr);

    if ($rwgetRole['lock_file'] != '1') {
        header('Location: ./index');
    }
    $loginUser = $_SESSION[cdes_user_id];
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
                                        <a href="lock_request_list"><?php echo $lang['list_of_req_for_unlock_dile']; ?></a>
                                    </li>
                                    <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                                </ol>
                            </div>
                        </div>
                        <div class="panel">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <div class="col-sm-12">
                                        <div class="col-sm-5">
                                            <input type="text" class="form-control" name="locked_doc" id="locked_doc" value="<?php echo xss_clean(trim($_GET['locked_doc'])); ?>" parsley-trigger="change" placeholder="<?php echo $lang['search_lock_file']; ?>" required />
                                            <span class="error lock_error"></span>
                                        </div>
                                        <div class="col-sm-4">
                                            <button type="submit" class="btn btn-primary" id="shareddoc"><i class="fa fa-search"></i> <?php echo $lang['Search']; ?></button>
                                            <a href="lock_request_list" class="btn btn-warning"><i class="fa fa-refresh"></i> <?php echo $lang['Reset']; ?></a>
                                        </div>

                                    </div>
                                </div>
                                <div class="panel-body">
                                    <?php
                                    $searchTxt = filter_var($_GET['locked_doc'], FILTER_SANITIZE_STRING);
                                    $searchTxt = xss_clean(trim($searchTxt));
                                    $where = "WHERE tds.locker_userid='$loginUser' and tds.request_status='0' and flag_multidelete='1'";
                                    if (isset($_GET['locked_doc']) && !empty($searchTxt)) {
                                        $where = "WHERE tdm.old_doc_name LIKE '%$searchTxt%' and tds.locker_userid='$loginUser' and tds.request_status='0'  and flag_multidelete='1'";
                                    }
                                     mysqli_set_charset($db_con, 'utf8');
                                    $lockdocid = mysqli_query($db_con, "SELECT tdm.doc_id, tdm.doc_name,tdm.old_doc_name,tdm.doc_extn,tds.locker_userid,tds.requester_userid,tds.doc_id,tds.request_date FROM `tbl_lock_file_request_master` tds INNER JOIN tbl_document_master tdm ON tds.doc_id=tdm.doc_id $where") or die('Error in share with id fetch' . mysqli_error($db_con));
                                    $foundnum = mysqli_num_rows($lockdocid);
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
                                        ?>
                                        <div class="container">

                                            <div class="box-body">
                                                <label><?php echo $lang['Show']; ?> </label>
                                                <select id="limit" class="input-sm m-b-10">
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
                                                </select> <label><?php echo $lang['Documents']; ?></label>
                                                <div class="pull-right record">
                                                    <?php echo $start + 1 ?> <?php echo $lang['To'] ?> <?php
                                                    if (($start + $per_page) > $foundnum) {
                                                        echo $foundnum;
                                                    } else {
                                                        echo ($start + $per_page);
                                                    }
                                                    ?> <span><?php echo $lang['Ttal_Rcrds']; ?> : <?php echo $foundnum; ?></span>
                                                </div>
                                                <table class="table table-striped table-bordered js-sort-table"> 
                                                    <?php
                                                    $lockDoc = mysqli_query($db_con, "SELECT tdm.doc_id, tdm.doc_name,tdm.old_doc_name,tdm.doc_extn,tdm.checkin_checkout,tds.locker_userid,tds.requester_userid,tds.doc_id,tds.request_date,tds.lock_req_id FROM `tbl_lock_file_request_master` tds INNER JOIN tbl_document_master tdm ON tds.doc_id=tdm.doc_id $where order by tdm.old_doc_name LIMIT $start, $per_page") or die('Error in share id fetch' . mysqli_error($db_con));
                                                    if (mysqli_num_rows($lockDoc) > 0) {
                                                        ?>
                                                        <thead>
                                                            <tr>
                                                                <th class="sort-js-none" ><?php echo $lang['Sr_No']; ?></th>
                                                                <th><?php echo $lang['File_Name']; ?></th>
                                                                <th><?php echo $lang['Storage_Name']; ?></th>
                                                                <th ><?php echo $lang['req_by']; ?></th>
                                                                <th class="sort-js-date" ><?php echo $lang['req_date']; ?></th>
                                                                <th class="sort-js-none" ><?php echo $lang['actions']; ?></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $i = 1;
                                                            $i += $start;

                                                            while ($rwlockName = mysqli_fetch_assoc($lockDoc)) {
                                                                $lockUserName = mysqli_query($db_con, "SELECT * FROM `tbl_user_master` WHERE user_id = '$rwlockName[requester_userid]' order by first_name, last_name asc") or die('Error in share userNane fetch' . mysqli_error($db_con));
                                                                $rwUserName = mysqli_fetch_assoc($lockUserName);
                                                                ?>
                                                                <tr class="gradeX">
                                                                    <td><?php echo $i . '.'; ?></td>
                                                                    <td>
                                                                        <?php
                                                                        //@sk(221118): include view handler to handle different file formats
                                                                        if(file_exists('thumbnail/'.base64_encode($rwlockName['doc_id']).'.jpg')){ ?><div> <img class="thumb-image" src="thumbnail/<?=base64_encode($rwlockName['doc_id'])?>.jpg"> </div>
                                                                        <?php }
                                                                        echo $rwlockName['old_doc_name'];
                                                                        if ($rwlockName['checkin_checkout'] == '1') {
                                                                            $file_row = $rwlockName;
                                                                            require 'view-handler.php';
                                                                        } else {
                                                                            echo ' <i class="fa fa-eye-slash" title="' . $lang['Checkout'] . ' ' . $lang['files'] . '"></i>';
                                                                        }
                                                                        ?>     
                                                                    </td>
                                                                    <?php
                                                                    $ShDocslname = mysqli_query($db_con, "SELECT * FROM `tbl_storage_level` WHERE sl_id = '$rwlockName[doc_name]'") or die('Error in share id fetch' . mysqli_error($db_con));
                                                                    $rwslname = mysqli_fetch_assoc($ShDocslname);
                                                                    ?>
                                                                    <td><?php echo $rwslname['sl_name']; ?></td>
                                                                    <td><?php echo $rwUserName['first_name'] . ' ' . $rwUserName['last_name']; ?></td>
                                                                    <td><?php echo $rwlockName['request_date']; ?></td>  
                                                                    <td>
                                                                        <button class="btn btn-primary lock_file" data="<?php echo $rwlockName[doc_id]; ?>" title="Share Lock FIle" lockid="<?php echo $rwlockName[lock_req_id]; ?>"><i class="fa fa-share-alt"></i></button>
                                                                        <button class="btn btn-danger unlock_file"  data="<?php echo $rwlockName[doc_id]; ?>" title="Unlock FIle" user="<?php echo $rwlockName[requester_userid]; ?>" ><i class="fa fa-unlock"></i></button>

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
                                                            if (!($start <= 0))
                                                                echo " <li><a href='?start=$prev&locked_doc=" . $_GET['locked_doc'] . "&limit=" . $per_page . "'>$lang[Prev]</a> </li>";
                                                            else
                                                                echo " <li class='disabled'><a href='javascript:void(0)'>$lang[Prev]</a> </li>";
                                                            //pages 
                                                            if ($max_pages < 7 + ($adjacents * 2)) {   //not enough pages to bother breaking it up
                                                                $i = 0;
                                                                for ($counter = 1; $counter <= $max_pages; $counter++) {
                                                                    if ($i == $start) {
                                                                        echo "<li class='active'><a href='?start=$i&limit=$per_page&locked_doc=" . $_GET['locked_doc'] . "'><b>$counter</b></a> </li>";
                                                                    } else {
                                                                        echo "<li><a href='?start=$i&limit=$per_page&locked_doc=" . $_GET['locked_doc'] . "'>$counter</a></li> ";
                                                                    }
                                                                    $i = $i + $per_page;
                                                                }
                                                            } elseif ($max_pages > 5 + ($adjacents * 2)) {    //enough pages to hide some
                                                                //close to beginning; only hide later pages
                                                                if (($start / $per_page) < 1 + ($adjacents * 2)) {
                                                                    $i = 0;
                                                                    for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                                                                        if ($i == $start) {
                                                                            echo " <li class='active'><a href='?start=$i&limit=$per_page&locked_doc=" . $_GET['locked_doc'] . "'><b>$counter</b></a></li> ";
                                                                        } else {
                                                                            echo "<li> <a href='?start=$i&limit=$per_page&locked_doc=" . $_GET['locked_doc'] . "'>$counter</a> </li>";
                                                                        }
                                                                        $i = $i + $per_page;
                                                                    }
                                                                }
                                                                //in middle; hide some front and some back
                                                                elseif ($max_pages - ($adjacents * 2) > ($start / $per_page) && ($start / $per_page) > ($adjacents * 2)) {
                                                                    echo " <li><a href='?start=0&limit=$per_page&locked_doc=" . $_GET['locked_doc'] . "'>1</a></li> ";
                                                                    echo "<li><a href='?start=$per_page&limit=$per_page&locked_doc=" . $_GET['locked_doc'] . "'>2</a></li>";
                                                                    echo "<li><a href='javascript:void(0)'>...</a></li>";

                                                                    $i = $start;
                                                                    for ($counter = ($start / $per_page) + 1; $counter < ($start / $per_page) + $adjacents + 2; $counter++) {
                                                                        if ($i == $start) {
                                                                            echo " <li class='active'><a href='?start=$i&limit=$per_page&locked_doc=" . $_GET['locked_doc'] . "'><b>$counter</b></a></li> ";
                                                                        } else {
                                                                            echo " <li><a href='?start=$i&limit=$per_page&locked_doc=" . $_GET['locked_doc'] . "'>$counter</a> </li>";
                                                                        }
                                                                        $i = $i + $per_page;
                                                                    }
                                                                }
                                                                //close to end; only hide early pages
                                                                else {
                                                                    echo "<li> <a href='?start=0&limit=$per_page&locked_doc=" . $_GET['locked_doc'] . "'>1</a> </li>";
                                                                    echo "<li><a href='?start=$per_page&limit=$per_page&locked_doc=" . $_GET['locked_doc'] . "'>2</a></li>";
                                                                    echo "<li><a href='javascript:void(0)'>...</a></li>";

                                                                    $i = $start;
                                                                    for ($counter = ($start / $per_page) + 1; $counter <= $max_pages; $counter++) {
                                                                        if ($i == $start) {
                                                                            echo " <li class='active'><a href='?start=$i&limit=$per_page&locked_doc=" . $_GET['locked_doc'] . "'><b>$counter</b></a></li> ";
                                                                        } else {
                                                                            echo "<li> <a href='?start=$i&limit=$per_page&locked_doc=" . $_GET['locked_doc'] . "'>$counter</a></li> ";
                                                                        }
                                                                        $i = $i + $per_page;
                                                                    }
                                                                }
                                                            }
                                                            //next button
                                                            if (!($start >= $foundnum - $per_page))
                                                                echo "<li><a href='?start=$next&locked_doc=" . $_GET['locked_doc'] . "&limit=" . $per_page . "'>$lang[Next]</a></li>";
                                                            else
                                                                echo "<li class='disabled'><a href='javascript:void(0)'>$lang[Next]</a></li>";
                                                            ?>
                                                        </ul>

                                                        <?php
                                                    }
                                                    echo "</center>";
                                                }
                                            } else {
                                                if (isset($_GET['locked_doc']) && !empty($_GET['locked_doc'])) {
                                                    echo '<div class="form-group form-group no-records-found"><label><strong class="text-danger"><i class="ti-face-sad text-pink"></i> ' . $lang['Who0ps!_No_Records_Found'] . '</strong></label></div>';
                                                } else {
                                                    echo '<tr><td colspan="10"><label style="font-weight:600; color:red; margin-left: 440px;">You Have No Lock Request</label></td></tr>';
                                                }
                                            }
                                            ?> 
                                        </div>
                                    </div>
                                    <!-- end: page -->
                                </div> <!-- end Panel -->
                            </div> <!-- container -->

                        </div> <!-- content -->
                    </div>
                </div>
            </div>
            <!--lock files with users-->
            <div id="unlock-selected-files" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog"> 
                    <div class="panel panel-color panel-primary"> 
                        <div class="panel-heading"> 
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                            <h2 class="panel-title" id="shr"> <i class="fa fa-lock" aria-hidden="true"></i> <?php echo $lang['unlock_file']; ?></h2>
                            <h2 class="panel-title" style="display:none;" id="stitle"> <?php echo $lang['unlock_file']; ?></h2> 
                        </div>
                        <div id="">
                            <form method="post">
                                <div class="panel-body" >
                                    <div class="row">
                                        <label class="text-primary"><?php echo $lang['do_u_want_to_unlock_file']; ?> </label>

                                    </div>
                                </div> 
                                <div class="modal-footer">
                                    <input type="hidden" id="unlock_docid" name="unlockdoc_id">
                                    <input type="hidden" id="requester_userid" name="requester_userid">
                                    <a type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close'] ?></a>

                                    <button type="submit" name="unlockfile_req" class="btn btn-primary"> <i class="fa fa-lock"></i> <?php echo $lang['unlock_file'] ?></button>

                                </div>
                            </form>
                        </div>
                    </div> 
                </div>
            </div><!-- /.modal -->
            <!--lock files with users-->
            <!--lock files with users-->
            <div id="lock-selected-files" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog"> 
                    <div class="panel panel-color panel-primary"> 
                        <div class="panel-heading"> 
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                            <h2 class="panel-title" id="shr"> <i class="fa fa-lock" aria-hidden="true"></i> <?php echo $lang['lock_file']; ?></h2>
                            <h2 class="panel-title" style="display:none;" id="stitle"> <?php echo $lang['lock_file']; ?></h2> 
                        </div>
                        <div id="">
                            <form method="post">
                                <div class="panel-body" id="info">
                                </div> 
                                <div class="modal-footer">
                                     
                                    <a type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close'] ?></a>

                                    <button type="submit" name="lockfile_req" class="btn btn-primary"> <i class="fa fa-lock"></i> <?php echo $lang['lock_file'] ?></button>

                                </div>
                            </form>
                        </div>
                    </div> 
                </div>
            </div><!-- /.modal -->
            <!--lock files with users-->
            <?php require_once './application/pages/footer.php'; ?>

        </div>

        <!-- Right Sidebar -->
        <?php //require_once './application/pages/rightSidebar.php';   ?>
        <!-- /Right-bar -->

        <!-- END wrapper -->
        <?php require_once './application/pages/footerForjs.php'; ?>
        <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>

        <!-- for audio model-->
        <div id="modal-audio" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel"><?php echo $lang['Play_Audio']; ?></h4>
                    </div>
                    <div id="foraudio">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>

                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <!--for video model-->
        <div id="modal-video" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel"><?php echo $lang['Play_video']; ?></h4>
                    </div>
                    <div  id="videofor">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>

                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <script type="text/javascript">

                                        $(document).ready(function () {
                                            $('form').parsley();
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

            $(document).ready(function () {
                $("#shareddoc").click(function () {
                    
                    var shared = $("#locked_doc").val();
                    
                    if(shared!=""){
                        url = removeParam("start", url);
                        url = removeParam("locked_doc", url);
                        if (shared != '') {
                            url = url + "&locked_doc=" + shared;
                        }
                        window.open(url, "_parent");
                        
                    }else{
                        $(".lock_error").text("This value is required.");
                    }
                })

            })
            $(".unlock_file").click(function () {
                var id = $(this).attr("data");
                var userid = $(this).attr("user");
                $("#unlock-selected-files").modal("show");
                $("#unlock_docid").val(id);
                $("#requester_userid").val(userid);
            });

            $(".lock_file").click(function () {
                var id = $(this).attr("data");
                var lockid = $(this).attr("lockid");
                console.log(lockid);
                $.post("application/ajax/lock_request_ajax.php", {doc_id: id, lock_req_docid: lockid}, function (result, status) {
                    if (status == 'success') {
                        $("#info").html(result);
                        $("#lock-selected-files").modal("show");
//                        $("#lock_docid").val(id);
//                        $("#lock_reqid").val(lockid);


                    }
                });

            });

        </script>
        <script>

            //for video clip
            $("a#video").click(function () {
                var id = $(this).attr('data');

                $.post("application/ajax/videoformat.php", {vid: id}, function (result, status) {
                    if (status == 'success') {
                        $("#videofor").html(result);
                        //alert(result);

                    }
                });
            });
            //for audio player
            $("a#audio").click(function () {
                var id = $(this).attr('data');

                $.post("application/ajax/audioformat.php", {aid: id}, function (result, status) {
                    if (status == 'success') {
                        $("#foraudio").html(result);
                        //alert(result);

                    }
                });
            });

        </script>

    </body>
</html>
<?php
if (isset($_POST['unlockfile_req'], $_POST['token'])) {
    mysqli_autocommit($db_con, FALSE);
    $docid = $_POST['unlockdoc_id'];
    $userid = $_POST['requester_userid'];
    $qry = mysqli_query($db_con, "UPDATE `tbl_locked_file_master`  SET is_active='0'  WHERE doc_id='$docid' and user_id='$userid'")or die(mysqli_error($db_con));
    $updaterequest = mysqli_query($db_con, "UPDATE `tbl_lock_file_request_master` SET request_status='1' WHERE doc_id='$docid' and requester_userid='$userid'");
    if ($updaterequest) {
        $qrydocmaster = mysqli_query($db_con, "SELECT * FROM `tbl_document_master` WHERE doc_id='$docid'")or die(mysqli_error($db_con));
        $fetchdocdata = mysqli_fetch_assoc($qrydocmaster);
        $files = $fetchdocdata['old_doc_name'];
        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `doc_id`,`action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'0', '$docid','Document $files is unlocked','$date',null,'$host',NULL)") or die('error : ' . mysqli_error($db_con));

        if ($qry) {
            mysqli_commit($db_con);
            echo'<script>taskSuccess("' . $_SERVER['RESQUEST_URI'] . '","' . $lang['doc_unlocked_successfully'] . '");</script>';
        } else {
            echo'<script>taskFailed("' . $_SERVER['RESQUEST_URI'] . '","' . $lang['doc_unlocked_failed'] . '");</script>';
        }
    } else {
        echo'<script>taskFailed("' . $_SERVER['RESQUEST_URI'] . '","' . $lang['doc_unlocked_failed'] . '");</script>';
    }
}

if (isset($_POST['lockfile_req'], $_POST['token'])) {
    
    
    mysqli_autocommit($db_con, FALSE);
    $docid = $_POST['lockdoc_id'];
    $lockid = $_POST['lock_req_docid'];
    /* ------------doc id start-------------- */
    $useridsinfo = array();
    $lockqry = mysqli_query($db_con, "SELECT * FROM `tbl_locked_file_master` WHERE doc_id='$docid' and is_active='1'");
    while ($locdata = mysqli_fetch_assoc($lockqry)) {
        array_push($useridsinfo, $locdata['user_id']);
    }
    $uniquearray = explode(",", implode(",", array_unique($useridsinfo))); //reindexing array
    /* ------------doc id end-------------- */

    $userids = isset($_POST['userid']) ? $_POST['userid'] : array(); //if user select create array else create array
    array_push($userids, 1); //add super user every lock file
    array_push($userids, $_SESSION[cdes_user_id]); //add current user in user id
    $userdata = array_unique($userids);


    /* --------remove array--------- */

    $removeid = array_diff($uniquearray, $userdata);
    /* --------add array--------- */
    $addid = array_diff($userdata, $uniquearray);
    /* -------------reindexing array----------- */
    $removearray = explode(",", implode(",", array_unique($removeid))); //reindexing array
    $addarray = explode(",", implode(",", array_unique($addid))); //reindexing array


    $statusRemoveId = 0;
    $statusAddId = 0;
    $updaterequest = mysqli_query($db_con, "UPDATE `tbl_lock_file_request_master` SET request_status='1' WHERE lock_req_id='$lockid'");
    if ($updaterequest) {
        
        $qrydocmaster = mysqli_query($db_con, "SELECT * FROM `tbl_document_master` WHERE doc_id='$docid'")or die('Error d'.mysqli_error($db_con));
        $fetchdocdata = mysqli_fetch_assoc($qrydocmaster);
        
        $files = $fetchdocdata['old_doc_name'];
        for ($k = 0; $k < count($removearray); $k++) {
            if (!empty($removearray[$k])) {
                $updateremoveqry = mysqli_query($db_con, "UPDATE `tbl_locked_file_master` SET is_active='0' WHERE doc_id='$docid' and user_id='$removearray[$k]'");
                if ($updateremoveqry) {

                    $qryusermaster = mysqli_query($db_con, "SELECT * FROM `tbl_user_master` WHERE user_id='$removearray[$k]'")or die(mysqli_error($db_con));
                    $fetchuserdata = mysqli_fetch_assoc($qryusermaster);
                    $toname = $fetchuserdata['first_name'] . ' ' . $fetchuserdata['last_name'];

                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `doc_id`,`action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'0', '$docid','User $toname Removed From Locked File $files','$date',null,'$host',NULL)") or die('error : ' . mysqli_error($db_con));
                } else {
                    $statusRemoveId = 1;
                }
            }
        }
        
        
        for ($f = 0; $f < count($addarray); $f++) {
            if (!empty($addarray[$f])) {
                $status = ($_SESSION[cdes_user_id] == $addarray[$f]) ? 1 : 0;
                $qry = mysqli_query($db_con, "INSERT INTO `tbl_locked_file_master`(`doc_id`,`user_id`,`is_locked`,`locked_date`) values('$docid','$addarray[$f]','$status','$date')")or die('ssssss'.mysqli_error($db_con));
                if ($qry) {
                    $qryusermaster = mysqli_query($db_con, "SELECT * FROM `tbl_user_master` WHERE user_id='$addarray[$f]'")or die(mysqli_error($db_con));
                    $fetchuserdata = mysqli_fetch_assoc($qryusermaster);
                    $toname = $fetchuserdata['first_name'] . ' ' . $fetchuserdata['last_name'];
                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `doc_id`,`action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'0', '$docid','User $toname Added To Locked File $files' ,'$date',null,'$host',NULL)") or die('error : ' . mysqli_error($db_con));
                } else {
                    $statusAddId = 1;
                }
            }
        }


        if (($statusRemoveId == 0) && ($statusAddId == 0)) {
            mysqli_commit($db_con);
            echo'<script>taskSuccess("' . $_SERVER['RESQUEST_URI'] . '","' . $lang['doc_locked_successfully'] . '");</script>';
        } else {
            echo'<script>taskFailed("' . $_SERVER['RESQUEST_URI'] . '","' . $lang['doc_locked_failed'] . '");</script>';
        }
    } else {
        echo'<script>taskFailed("' . $_SERVER['RESQUEST_URI'] . '","' . $lang['doc_locked_failed'] . '");</script>';
    }
}
?>