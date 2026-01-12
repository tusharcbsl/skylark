<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    //require_once './application/config/database.php';
    require_once './application/pages/head.php';
    $loginUser = $_SESSION['cdes_user_id'];
    if ($rwgetRole['shared_file'] != '1') {
        header('Location: ./index');
    }
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
                                        <a href="#"><?php echo $lang['Shared_files']; ?></a>
                                    </li>
                                    <li>
                                        <a href="shared-files-splt"><?php echo $lang['YOUR_SHARED_DOCUMENTS_LIST']; ?></a>
                                    </li>
                                    <li> <a href="#" data-toggle="modal" data-target="#help-modal" id="helpview" data="39" title="<?= $lang['help']; ?>"><i class="fa fa-question-circle"></i> </a></li>

                                    <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                                </ol>
                            </div>
                        </div>

                        <div class="panel">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <div class="col-sm-10">
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" name="shared_doc" id="shared_doc" value="<?php echo $_GET['shared_doc'] ?>" parsley-trigger="change" placeholder="<?php echo $lang['search_shared_doc']; ?>" required />
                                        </div>
                                        <div class="col-sm-3">
                                            <button type="submit" class="btn btn-primary" id="shareddoc"><i class="fa fa-search"></i>  <?php echo $lang['Search']; ?></button>
                                            <a href="shared-files-splt" class="btn btn-warning"><i class="fa fa-refresh"></i>  <?php echo $lang['Reset']; ?></a>
                                        </div>

                                    </div>

                                </div>
                                <div class="panel-body">
                                    <?php
                                    // $searchTxt = filter_var($_GET['shared_doc'], FILTER_SANITIZE_STRING);
                                    $searchTxt = trim($_GET['shared_doc']);
                                    $where = "WHERE tds.from_id = '$loginUser' and flag_multidelete='1'";
                                    if (isset($_GET['shared_doc']) && !empty($searchTxt)) {

                                        $where .= " and tdm.old_doc_name like '%$searchTxt%' and tds.from_id = '$loginUser' and flag_multidelete='1'";
                                    }
                                    $sql = "SELECT tdm.doc_id,tdm.doc_name,tdm.doc_extn,tdm.noofpages,tdm.old_doc_name,tds.id,tds.from_id,tds.to_ids,tds.doc_ids,tds.share_date  FROM `tbl_document_share_for_split_pdf` tds INNER JOIN `tbl_document_master_for_split_pdf` tdm ON tds.doc_ids=tdm.doc_id $where";
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
                                        <div class="container">
                                            <div class="box-body limit">
                                                <?php echo $lang['Show']; ?>  
                                                <select id="limit" class="input-sm m-b-10">
                                                    <option value="10" <?php
                                                    if ($_GET['limit'] == 10) {
                                                        echo 'selected';
                                                    }
                                                    ?>>10</option>
                                                    <option value="30" <?php
                                                    if ($_GET['limit'] == 30) {
                                                        echo 'selected';
                                                    }
                                                    ?>>30</option>
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
                                                    <option value="1000" <?php
                                                    if ($limit == 1000) {
                                                        echo 'selected';
                                                    }
                                                    ?>>1000</option>
                                                </select> <?php echo $lang['Documents']; ?>
                                                <div class="pull-right record">
                                                    <?php echo $start + 1 ?> <?php echo $lang['To'] ?> <?php
                                                    if (($start + $per_page) > $foundnum) {
                                                        echo $foundnum;
                                                    } else {
                                                        echo ($start + $per_page);
                                                    };
                                                    ?> <span><?php echo $lang['ttl_recrds']; ?> : <?php echo $foundnum; ?></span>
                                                </div>
                                                <table class="table table-striped table-bordered js-sort-table">
                                                    <?php
                                                    $ShDocId = mysqli_query($db_con, "SELECT tdm.doc_id,tdm.doc_name,tdm.doc_extn,tdm.noofpages,tdm.old_doc_name,tds.id,tds.from_id,tds.to_ids,tds.doc_ids,tds.share_date FROM `tbl_document_share_for_split_pdf` tds INNER JOIN `tbl_document_master_for_split_pdf` tdm ON tds.doc_ids=tdm.doc_id $where order by tdm.old_doc_name asc LIMIT $start, $per_page") or die('Error in share id fetch' . mysqli_error($db_con));
                                                    if (mysqli_num_rows($ShDocId) > 0) {
                                                        ?>
                                                        <thead>
                                                            <tr>
                                                                <th class="js-sort-none"><?php echo $lang['Sr_No']; ?></th>
                                                                <th><?php echo $lang['File_Name']; ?> <i class="fa fa-caret-up"></i></th>
                                                                <th><?php echo $lang['Storage_Name']; ?><i class="fa fa-caret-up"></i></th>
                                                                <th><?php echo $lang['shared_to']; ?> <i class="fa fa-caret-up"></i></th>
                                                                <th><?php echo $lang['Share_Date']; ?> <i class="fa fa-caret-up"></i></th>
                                                                <th class="js-sort-none"><?php echo $lang['Actions']; ?></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody> 
                                                            <?php
                                                            $i = $start + 1;
                                                            while ($rwShDocId = mysqli_fetch_assoc($ShDocId)) {
                                                                $ShUserName = mysqli_query($db_con, "SELECT * FROM `tbl_user_master` WHERE user_id = '" . $rwShDocId['to_ids'] . "' order by first_name, last_name asc") or die('Error in share userNane fetch' . mysqli_error($db_con));
                                                                $rwUserName = mysqli_fetch_assoc($ShUserName);
                                                                ?>
                                                                <tr class="gradeX">
                                                                    <td><?php echo $i; ?></td>
                                                                    <td>
                                                                        <?php
                                                                        $checkfileLock = mysqli_query($db_con, "SELECT * FROM `tbl_locked_file_master` WHERE doc_id='" . $rwShDocId['doc_id'] . "' and is_active='1' and user_id='" . $_SESSION['cdes_user_id'] . "'");
                                                                        $rwfileLock = mysqli_fetch_assoc($checkfileLock);
                                                                        if (mysqli_num_rows($rwfileLock) > 0) {
                                                                            if ($rwfileLock['doc_id'] == $rwShDocId['doc_id']) {
                                                                                //@sk(221118): include view handler to handle different file formats
                                                                                if (file_exists('thumbnail/' . base64_encode($rwShDocId['doc_id']) . '.jpg')) {
                                                                                    ?><div> <img class="thumb-image" src="thumbnail/<?= base64_encode($rwShDocId['doc_id']) ?>.jpg"> </div>
                                                                                    <?php
                                                                                }
                                                                                echo $rwShDocId['old_doc_name'];
                                                                                $file_row = $rwShDocId;
                                                                                require 'view-handler-splt.php';
                                                                            } else {
                                                                                echo $rwShDocId['old_doc_name'];
                                                                                ?>
                                                                                <a href="javascript:void(0)"  data="<?php echo $rwShDocId['doc_id'] ?>" class="send_lock_request" data-toggle="dropdown" aria-expanded="true" style="font-size: 20px;"><i class="md md-lock" title="<?php echo $lang['lock_file']; ?>"></i></a>  
                                                                                <?php
                                                                            }
                                                                        } else {
                                                                            echo $rwShDocId['old_doc_name'];
                                                                            $file_row = $rwShDocId;
                                                                            require 'view-handler-splt.php';
                                                                        }
                                                                        ?> 

                                                                    </td>
                                                                    <?php
                                                                    $ShDocslname = mysqli_query($db_con, "SELECT * FROM `tbl_storage_level` WHERE sl_id = '$rwShDocId[doc_name]'") or die('Error in share id fetch' . mysqli_error($db_con));
                                                                    $rwslname = mysqli_fetch_assoc($ShDocslname);
                                                                    ?>
                                                                    <td><?php echo $rwslname['sl_name']; ?></td>
                                                                    <td><?php echo $rwUserName['first_name'] . ' ' . $rwUserName['last_name']; ?></td>
                                                                    <td><?php echo date('d-m-Y h:s A', strtotime($rwShDocId['share_date'])); ?></td>
                                                                    <td class="actions">
                                                                        <a href="#" class="btn btn-danger" data-toggle="modal" data-target="#dialog" id="undoShare" data="<?php echo $rwShDocId['to_ids']; ?>" data1="<?php echo $rwShDocId['doc_id']; ?>">
                                                                            <i class="fa fa-undo"></i><strong> <?php echo $lang['Undo_Share']; ?></strong></a>
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
                                                if (isset($_GET['shared_doc']) && !empty($_GET['shared_doc'])) {
                                                    echo '<div class="form-group form-group no-records-found"><label><strong class="text-danger"><i class="ti-face-sad text-pink"></i> ' . $lang['Who0ps!_No_Records_Found'] . '</strong></label></div>';
                                                } else {
                                                    echo '<tr><td colspan="10"><label style="font-weight:600; color:red; margin-left: 440px;">' . $lang['Yu_Hv_No_Shred_Fles'] . '</label></td></tr>';
                                                }
                                            }
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
                    <?php require_once './application/pages/rightSidebar.php'; ?>
                    <!-- /Right-bar -->
                    <!-- MODAL -->
                    <div id="dialog" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog"> 
                            <div class="panel panel-color panel-danger"> 
                                <div class="panel-heading"> 
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                                    <label><h2 class="panel-title"><?php echo $lang['Are_u_confirm'] ?></h2></label> 
                                </div> 
                                <form method="post">
                                    <div class="panel-body">
                                        <p style="color: red;"><?php echo $lang['Are_you_sure_that_you_want_to_Undo_shared_Documents']; ?> ?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <div class="col-md-12 text-right">
                                            <input type="hidden" id="undo" name="undo">
                                            <input type="hidden" id="did" name="did">
                                            <button type="submit" name="undoshare" id="dialogConfirm" class="btn btn-danger waves-effect waves-light"><?php echo $lang['confirm'] ?></button>
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

                <?php require_once './application/pages/footerForjs.php'; ?>
                <script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
                <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>

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

                </script>
                <script type="text/javascript">

                    $(document).ready(function () {
                        $('form').parsley();
                    });
                </script>

                <!-- for audio model-->
                <div id="modal-audio" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h4 class="modal-title" id="myModalLabel"><?php echo $lang['Play_Audio'] ?></h4>
                            </div>
                            <div id="foraudio">

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close'] ?></button>

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
                                <h4 class="modal-title" id="myModalLabel"><?php echo $lang['Play_video'] ?></h4>
                            </div>
                            <div  id="videofor">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close'] ?></button>

                            </div>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->
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
                    //for undo share
                    $("a#undoShare").click(function () {
                        var uid = $(this).attr('data');
                        var id = $(this).attr('data1');
                        $("#undo").val(uid);
                        $("#did").val(id);
                    });
                </script>
                <script src="https://www.google.com/jsapi" type="text/javascript">
                </script>  

                <script type="text/javascript">

                    // Load the Google Transliterate API
                    google.load("elements", "1", {
                        packages: "transliteration"
                    });

                    function onLoad() {

                        var langcode = '<?php echo $langDetail['lang_code']; ?>';



                        var options = {
                            sourceLanguage: 'en',
                            destinationLanguage: [langcode],
                            shortcutKey: 'ctrl+g',
                            transliterationEnabled: true
                        };
                        // Create an instance on TransliterationControl with the required
                        // options.
                        var control =
                                new google.elements.transliteration.TransliterationControl(options);

                        // Enable transliteration in the text fields with the given ids.
                        var ids = ["shared_doc"];
                        control.makeTransliteratable(ids);


                        // Show the transliteration control which can be used to toggle between
                        // English and Hindi and also choose other destination language.
                        // control.showControl('translControl');

                    }
                    google.setOnLoadCallback(onLoad);

                </script> 	 
                <?php
                if (isset($_POST['undoshare'])) {
                    $id = $_POST['undo']; //user_id
                    $delDoc = $_POST['did'];
                    $delUserDoc = mysqli_query($db_con, "SELECT first_name,last_name FROM `tbl_user_master` WHERE user_id = '$id'") or die('Error name' . mysqli_error($db_con));
                    $rwUserName = mysqli_fetch_assoc($delUserDoc);
                    $undo = mysqli_query($db_con, "SELECT old_doc_name FROM `tbl_document_master_for_split_pdf` WHERE doc_id ='$delDoc'") or die('Error:' . mysqli_error($db_con));
                    $rwundoDocNm = mysqli_fetch_assoc($undo);
                    $undoShare = mysqli_query($db_con, "delete from `tbl_document_share_for_split_pdf` where to_ids='$id' and doc_ids='$delDoc'") or die('Error:' . mysqli_error($db_con));
                    if ($undoShare) {
                        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`,`system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Document Undo', '$date','$host','You undo share ($rwundoDocNm[old_doc_name]) Document from $rwUserName[first_name] $rwUserName[last_name]')") or die('error22 : ' . mysqli_error($db_con));
                        echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '", "' . $lang['Undo_Share_Document_Successfully'] . ' !!");</script>';
                    } else {
                        echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Undo_Share_Document_failed'] . '")</script>';
                    }

                    mysqli_close($db_con);
                }
                ?>
                </body>
                </html>