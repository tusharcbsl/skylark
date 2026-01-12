<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    //require_once './application/config/database.php';
    require_once './application/pages/head.php';
    $loginUser = $_SESSION[cdes_user_id];
    //for user role
    $ses_val = $_SESSION;
    $langDetail = mysqli_query($db_con, "select * from tbl_language where lang_name='$_SESSION[lang]'") or die('Error : ' . mysqli_error($db_con));
    $langDetail = mysqli_fetch_assoc($langDetail);
    $chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));
    $rwgetRole = mysqli_fetch_assoc($chekUsr);
    if ($rwgetRole['subscribe_document'] != '1') {
        header('Location: ./index');
    }
    ?>
    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <!-- Plugin Css-->
    <link rel="stylesheet" href="assets/plugins/magnific-popup/css/magnific-popup.css" />
    <link href="assets/plugins/multiselect/css/multi-select.css"  rel="stylesheet" type="text/css" />

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
                                        <a href="#"><?php echo $lang['subscribe_doclist']; ?></a>
                                    </li>

                                    <li> <a href="#" data-toggle="modal" data-target="#help-modal" id="helpview" data="63" title="<?= $lang['help']; ?>"><i class="fa fa-question-circle"></i> </a></li>

                                    <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                                </ol>
                            </div>
                        </div>

                        <div class="panel">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <div class="col-sm-10">
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" name="shared_doc" id="shared_doc" value="<?php echo $_GET['shared_doc'] ?>" parsley-trigger="change" placeholder="<?php echo $lang['search_subscribe_document']; ?>" required />
                                        </div>
                                        <div class="col-sm-3">
                                            <button type="submit" class="btn btn-primary" id="shareddoc"><i class="fa fa-search"></i>  <?php echo $lang['Search']; ?></button>
                                            <a href="subscribe-document-list" class="btn btn-warning"> <?php echo $lang['Reset']; ?></a>
                                        </div>

                                    </div>

                                </div>
                                <div class="panel-body">
                                    <?php
                                    // $searchTxt = filter_var($_GET['shared_doc'], FILTER_SANITIZE_STRING);
                                    $searchTxt = trim($_GET['shared_doc']);
                                    $where = "WHERE tds.subscriber_userid = '$loginUser' and flag_multidelete='1'";
                                    if (isset($_GET['shared_doc']) && !empty($searchTxt)) {

                                        $where .= " and tdm.old_doc_name like '%$searchTxt%' and tds.subscriber_userid = '$loginUser' and flag_multidelete='1'";
                                    }
                                    $sql = "SELECT tdm.doc_id,tdm.doc_name,tdm.doc_extn,tdm.noofpages,tdm.old_doc_name,tds.id,tds.subscribe_docid,tds.subscriber_userid  FROM `tbl_document_subscriber` tds INNER JOIN `tbl_document_master` tdm ON tds.subscribe_docid=tdm.doc_id $where";
                                    mysqli_set_charset($db_con, 'utf8');
                                    $retval = mysqli_query($db_con, $sql) or die('Could not get data: ' . mysqli_error($db_con));
                                    $foundnum = mysqli_num_rows($retval);
                                    if ($foundnum > 0) {
                                        if (is_numeric($_GET['limit'])) {
                                            $per_page = preg_replace("/[^0-9]/", "", $_GET['limit']);
                                        } else {
                                            $per_page = 10;
                                        }
                                        $start = isset($_GET['start']) ? ($_GET['start'] > 0) ? $_GET['start'] : 0 : 0;
                                        $max_pages = ceil($foundnum / $per_page);
                                        if (!$start) {
                                            $start = 0;
                                        }
                                        ?>
                                        <div class="row">
                                            <div class="box-body limit">
                                                <?php echo $lang['Show']; ?>  
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
                                                </select> <?php echo $lang['Documents']; ?>
                                                <div class="pull-right record">
                                                    <?php echo $start + 1 ?> <?php echo $lang['To'] ?> <?php
                                                    if (($start + $per_page) > $foundnum) {
                                                        echo $foundnum;
                                                    } else {
                                                        echo ($start + $per_page);
                                                    }
                                                    ?> <span><?php echo $lang['ttl_recrds']; ?> : <?php echo $foundnum; ?></span>
                                                </div>
                                                <table class="table table-striped table-bordered">
                                                    <?php
                                                    $ShDocId = mysqli_query($db_con, "SELECT tdm.doc_id,tdm.doc_name,tdm.doc_extn,tdm.noofpages,tdm.old_doc_name,tds.id,tds.subscribe_docid,tds.subscriber_userid  FROM `tbl_document_subscriber` tds INNER JOIN `tbl_document_master` tdm ON tds.subscribe_docid=tdm.doc_id $where order by tdm.old_doc_name asc LIMIT $start, $per_page") or die('Error in share id fetch' . mysqli_error($db_con));
                                                    if (mysqli_num_rows($ShDocId) > 0) {
                                                        ?>
                                                        <thead>
                                                            <tr>
                                                                <th><?php echo $lang['Sr_No']; ?></th>
                                                                <th><?php echo $lang['File_Name']; ?></th>
                                                                <th><?php echo $lang['Storage_Name']; ?></th>
                                                                <th><?php echo $lang['Actions']; ?></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody> 
                                                            <?php
                                                            $i = $start + 1;
                                                            while ($rwShDocId = mysqli_fetch_assoc($ShDocId)) {
                                                                ?>

                                                                <tr class="gradeX">
                                                                    <td><?php echo $i; ?></td>
                                                                    <td>
                                                                        <?php
                                                                        $checkfileLock = mysqli_query($db_con, "SELECT * FROM `tbl_locked_file_master` WHERE doc_id='" . $rwShDocId['doc_id'] . "' and is_active='1' and user_id='" . $_SESSION['cdes_user_id'] . "'");
                                                                        $rwfileLock = mysqli_fetch_assoc($checkfileLock);
                                                                        if ($rwfileLock['doc_id'] != $rwShDocId['doc_id']) {
                                                                            //@sk(221118): include view handler to handle different file formats
                                                                            echo $rwShDocId['old_doc_name'];
                                                                            $file_row = $rwShDocId;
                                                                            require 'view-handler.php';
                                                                        } else {
                                                                            echo $rwShDocId['old_doc_name'];
                                                                            ?>
                                                                            <a href="javascript:void(0)"  data="<?php echo $rwShDocId['doc_id'] ?>" class="send_lock_request" data-toggle="dropdown" aria-expanded="true" style="font-size: 20px;"><i class="md md-lock" title="<?php echo $lang['lock_file']; ?>"></i></a>  
                                                                            <?php
                                                                        }
                                                                        ?> 

                                                                    </td>
                                                                    <?php
                                                                    $ShDocslname = mysqli_query($db_con, "SELECT * FROM `tbl_storage_level` WHERE sl_id = '$rwShDocId[doc_name]'") or die('Error in share id fetch' . mysqli_error($db_con));
                                                                    $rwslname = mysqli_fetch_assoc($ShDocslname);
                                                                    ?>
                                                                    <td><?php echo $rwslname['sl_name']; ?></td>

                                                                    <td class="actions">
                                                                        <a href="#" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#dialog" id="unsubcribe" data="<?php echo $rwShDocId['subscriber_userid']; ?>" data1="<?php echo $rwShDocId['doc_id']; ?>">
                                                                            <i class="fa fa-undo"></i> <?php echo $lang['Unsubscribe']; ?></a>
                                                                        <a href="#" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#subcribe" id="modifysubcribe" data="<?php echo $rwShDocId['subscriber_userid']; ?>" data1="<?php echo $rwShDocId['doc_id']; ?>">
                                                                            <i class="fa fa-edit"></i> </a>
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
                                                                echo " <li><a href='?start=$prev&shared_doc=" . $_GET['shared_doc'] . "&limit=" . $per_page . "'>$lang[Prev]</a> </li>";
                                                            else
                                                                echo " <li class='disabled'><a href='javascript:void(0)'>$lang[Prev]</a> </li>";
                                                            //pages 
                                                            if ($max_pages < 7 + ($adjacents * 2)) {   //not enough pages to bother breaking it up
                                                                $i = 0;
                                                                for ($counter = 1; $counter <= $max_pages; $counter++) {
                                                                    if ($i == $start) {
                                                                        echo "<li class='active'><a href='?start=$i&limit=$per_page&shared_doc=" . $_GET['shared_doc'] . "'><b>$counter</b></a> </li>";
                                                                    } else {
                                                                        echo "<li><a href='?start=$i&limit=$per_page&shared_doc=" . $_GET['shared_doc'] . "'>$counter</a></li> ";
                                                                    }
                                                                    $i = $i + $per_page;
                                                                }
                                                            } elseif ($max_pages > 5 + ($adjacents * 2)) {    //enough pages to hide some
                                                                //close to beginning; only hide later pages
                                                                if (($start / $per_page) < 1 + ($adjacents * 2)) {
                                                                    $i = 0;
                                                                    for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                                                                        if ($i == $start) {
                                                                            echo " <li class='active'><a href='?start=$i&limit=$per_page&shared_doc=" . $_GET['shared_doc'] . "'><b>$counter</b></a></li> ";
                                                                        } else {
                                                                            echo "<li> <a href='?start=$i&limit=$per_page&shared_doc=" . $_GET['shared_doc'] . "'>$counter</a> </li>";
                                                                        }
                                                                        $i = $i + $per_page;
                                                                    }
                                                                }
                                                                //in middle; hide some front and some back
                                                                elseif ($max_pages - ($adjacents * 2) > ($start / $per_page) && ($start / $per_page) > ($adjacents * 2)) {
                                                                    echo " <li><a href='?start=0&limit=$per_page&shared_doc=" . $_GET['shared_doc'] . "'>1</a></li> ";
                                                                    echo "<li><a href='?start=$per_page&limit=$per_page&shared_doc=" . $_GET['shared_doc'] . "'>2</a></li>";
                                                                    echo "<li><a href='javascript:void(0)'>...</a></li>";

                                                                    $i = $start;
                                                                    for ($counter = ($start / $per_page) + 1; $counter < ($start / $per_page) + $adjacents + 2; $counter++) {
                                                                        if ($i == $start) {
                                                                            echo " <li class='active'><a href='?start=$i&limit=$per_page&shared_doc=" . $_GET['shared_doc'] . "'><b>$counter</b></a></li> ";
                                                                        } else {
                                                                            echo " <li><a href='?start=$i&limit=$per_page&shared_doc=" . $_GET['shared_doc'] . "'>$counter</a> </li>";
                                                                        }
                                                                        $i = $i + $per_page;
                                                                    }
                                                                }
                                                                //close to end; only hide early pages
                                                                else {
                                                                    echo "<li> <a href='?start=0&limit=$per_page&shared_doc=" . $_GET['shared_doc'] . "'>1</a> </li>";
                                                                    echo "<li><a href='?start=$per_page&limit=$per_page&shared_doc=" . $_GET['shared_doc'] . "'>2</a></li>";
                                                                    echo "<li><a href='javascript:void(0)'>...</a></li>";

                                                                    $i = $start;
                                                                    for ($counter = ($start / $per_page) + 1; $counter <= $max_pages; $counter++) {
                                                                        if ($i == $start) {
                                                                            echo " <li class='active'><a href='?start=$i&limit=$per_page&shared_doc=" . $_GET['shared_doc'] . "'><b>$counter</b></a></li> ";
                                                                        } else {
                                                                            echo "<li> <a href='?start=$i&limit=$per_page&shared_doc=" . $_GET['shared_doc'] . "'>$counter</a></li> ";
                                                                        }
                                                                        $i = $i + $per_page;
                                                                    }
                                                                }
                                                            }
                                                            //next button
                                                            if (!($start >= $foundnum - $per_page))
                                                                echo "<li><a href='?start=$next&shared_doc=" . $_GET['shared_doc'] . "&limit=" . $per_page . "'>$lang[Next]</a></li>";
                                                            else
                                                                echo "<li class='disabled'><a href='javascript:void(0)'>$lang[Next]</a></li>";
                                                            ?>
                                                        </ul>

                                                        <?php
                                                    }
                                                    echo "</center>";
                                                }
                                            } else {
                                                ?>
                                                <table class="table table-striped table-bordered">

                                                    <thead>
                                                        <tr>
                                                            <th><?php echo $lang['Sr_No']; ?></th>
                                                            <th><?php echo $lang['File_Name']; ?></th>
                                                            <th><?php echo $lang['Storage_Name']; ?></th>
                                                            <th><?php echo $lang['Actions']; ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if (isset($_GET['shared_doc']) && !empty($_GET['shared_doc'])) {
                                                            ?>

                                                            <tr>
                                                                <td colspan="4" class="text-center"><strong class="text-danger"><i class="ti-face-sad text-pink"></i><?php echo $lang['Who0ps!_No_Records_Found']; ?></strong></td>
                                                            </tr>

                                                        <?php } else {
                                                            ?>
                                                            <tr><td colspan="4"><label style="font-weight:600; color:red; margin-left: 440px;"><?php echo $lang['Yu_Hv_No_subscribe_Fles']; ?></label></td></tr>
                                                        <?php }
                                                        ?>
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

                        <?php require_once './application/pages/footer.php'; ?>

                    </div>
                    <!-- Right Sidebar -->
                    <?php //require_once './application/pages/rightSidebar.php';   ?>
                    <!-- /Right-bar -->
                    <!-- MODAL -->
                    <div id="dialog" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog"> 
                            <div class="panel panel-color panel-danger"> 
                                <div class="panel-heading"> 
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                                    <label><h2 class="panel-title"><?php echo $lang['Are_u_confirm'] ?> ?</h2></label> 
                                </div> 
                                <form method="post">
                                    <div class="panel-body">
                                        <p style="color: red;"><?php echo $lang['r_u_sure_want_unsubscribe_Documents']; ?> ?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <div class="col-md-12 text-right">
                                            <input type="hidden" id="undo" name="undo">
                                            <input type="hidden" id="did" name="did">
                                            <button type="submit" name="unsubscribe" id="dialogConfirm" class="btn btn-danger waves-effect waves-light"><?php echo $lang['confirm'] ?></button>
                                            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $lang['Close'] ?></button> 
                                        </div>
                                    </div>
                                </form>
                            </div> 
                        </div>
                    </div>
                    <!-- end Modal -->  
                </div>
                <!-- END wrapper -->
                <div id="subcribe" class="modal fade"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog"> 
                        <div class="panel panel-color panel-danger"> 
                            <div class="panel-heading"> 
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                                <h2 class="panel-title"> <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <?php echo $lang['Hre_msge']; ?></h2>
                            </div>
                            <form method="post">
                                <div class="panel-body">
                                    <div id="action"></div>
                                </div> 
                                <div class="modal-footer">
                                    <input type="hidden" id="editsinglesubsdocId" name="editsinglesubsdocId">
                                    <input type="hidden" id="editsubs" name="editsubs">

                                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close'] ?></button>
                                    <button type="submit" name="editAction" class="btn btn-primary"><?php echo $lang['Save_changes'] ?></button>
                                </div>
                            </form>

                        </div> 
                    </div>
                </div><!-- /.modal -->

                <?php require_once './application/pages/footerForjs.php'; ?>
               <!--for multiselect-->
                <script type="text/javascript" src="assets/plugins/multiselect/js/jquery.multi-select.js"></script>
                <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>

                <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>  

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
                                                var $shared = $("#shared_doc").val();
                                                url = removeParam("start", url);
                                                url = removeParam("shared_doc", url);
                                                if ($shared != '') {
                                                    url = url + "&shared_doc=" + $shared;
                                                }
                                                window.open(url, "_parent");
                                            })

                                        })
                                        $("a#modifysubcribe").click(function () {
                                            var subsDocId = $(this).attr('data1');
                                            $("#editsinglesubsdocId").val(subsDocId);
                                        });
                </script>
                <script type="text/javascript">

                    $(document).ready(function () {
                        $('.select2').select2();
                        $('form').parsley();

                    });
                </script>

                <script>
                    //for undo share
                    $("a#unsubcribe").click(function () {
                        var uid = $(this).attr('data');
                        var id = $(this).attr('data1');
                        $("#undo").val(uid);
                        $("#did").val(id);
                        $("#editsubs").val(id);
                    });

                    $("a#modifysubcribe").click(function () {
                        var id = $(this).attr('data1');
                        var uid = $(this).attr('data');
                        $.post("application/ajax/notification-action.php", {docId: id, userId: uid}, function (result, status) {
                            if (status == 'success') {
                                $("#action").html(result);
                            }
                        });
                    });
                </script>

                <?php
                if (isset($_POST['unsubscribe'])) {
                    $id = $_POST['undo']; //user_id
                    $delDoc = $_POST['did'];
                    $undo = mysqli_query($db_con, "SELECT old_doc_name FROM `tbl_document_master` WHERE doc_id ='$delDoc'") or die('Error:' . mysqli_error($db_con));
                    $rwundoDocNm = mysqli_fetch_assoc($undo);
                    $unsubcribe = mysqli_query($db_con, "delete from `tbl_document_subscriber` where subscriber_userid='$id' and subscribe_docid='$delDoc'") or die('Error:' . mysqli_error($db_con));
                    if ($unsubcribe) {
                        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`,`system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Document unsubscribe', '$date','$host','You unsubscribe subscribe ($rwundoDocNm[old_doc_name])')") or die('error22 : ' . mysqli_error($db_con));
                        echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '", "' . $lang['Undo_subscribe_Document_Successfully'] . ' !!");</script>';
                    } else {
                        echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Undo_subscribe_Document_Failed'] . '")</script>';
                    }

                    mysqli_close($db_con);
                }
                ?>
                <?php
                if (isset($_POST['editAction'])) {
                    $docId = $_POST['editsinglesubsdocId']; //doc_id
                    $id = $_SESSION['cdes_user_id'];
                    $editfileactions = $_POST['editfileactions'];
                    $fileactions = implode(',', $editfileactions);
                    $docedit = mysqli_query($db_con, "SELECT old_doc_name FROM `tbl_document_master` WHERE doc_id ='$docId'") or die('Error:' . mysqli_error($db_con));
                    $rwdocedit = mysqli_fetch_assoc($docedit);
                    $unsubcribe = mysqli_query($db_con, "update `tbl_document_subscriber` set action_id='$fileactions' where subscriber_userid='$id' and subscribe_docid='$docId'") or die('Error:' . mysqli_error($db_con));
                    if ($unsubcribe) {
                        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`,`system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Action edited', '$date','$host','$rwdocedit[old_doc_name] document notification actions changed.')") or die('error22 : ' . mysqli_error($db_con));
                        echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '", "' . $lang['Undo_subscribe_Document_Successfully'] . ' !!");</script>';
                    } else {
                        echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Undo_subscribe_Document_Failed'] . '")</script>';
                    }

                    mysqli_close($db_con);
                }
                ?>
                </body>
                </html>