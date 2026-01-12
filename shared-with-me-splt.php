<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    //require_once './application/config/database.php';
    require_once './application/pages/head.php';
    if ($rwgetRole['share_with_me'] != '1') {
        header('Location: ./index');
    }
    $loginUser = $_SESSION['cdes_user_id'];

    $searchTxt = '';
    if (!empty($_GET['folder'])) {
        $searchTxt = preg_replace("/[^a-zA-Z0-9_ -]/", "", $_GET['folder']);
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
                    <div class="container">

                        <!-- Page-Title -->
                        <div class="row">
                            <div class="col-sm-12">
                                <ol class="breadcrumb">
                                    <li>
                                        <a href="#"><?php echo $lang['Shared_With_Me']; ?></a>
                                    </li>
                                    <li>
                                        <a href="shared-with-me"><?php echo $lang['LST_OF_SHRD_DMNTS_WT_U']; ?></a>
                                    </li>
                                    <li> <a href="#" data-toggle="modal" data-target="#help-modal" id="helpview" data="34" title="<?= $lang['help']; ?>"><i class="fa fa-question-circle"></i> </a></li>

                                    <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                                </ol>
                            </div>
                        </div>
                        <div class="panel">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <div class="col-sm-12">
                                        <div class="col-sm-5">
                                            <input type="text" class="form-control translatetxt" name="shared_doc" id="shared_doc" value="<?php echo $searchTxt; ?>" parsley-trigger="change" placeholder="<?php echo $lang['search_shared_doc']; ?>" required />
                                        </div>
                                        <div class="col-sm-4">
                                            <button type="submit" class="btn btn-primary" id="shareddoc"><i class="fa fa-search"></i> <?php echo $lang['Search']; ?></button>
                                            <a href="shared-with-me-splt" class="btn btn-warning"> <?php echo $lang['Reset']; ?></a>
                                        </div>
                                       

                                    </div>
                                </div>
                                <div class="panel-body">
                                    <?php
                                    // $searchTxt = filter_var($_GET['shared_doc'], FILTER_SANITIZE_STRING);

                                    $searchTxt = trim($searchTxt);
                                    $where = "WHERE tds.to_ids='$loginUser' and flag_multidelete='1'";
                                    if (isset($_GET['shared_doc']) && !empty($searchTxt)) {
                                        $where = "WHERE tdm.old_doc_name LIKE '%$searchTxt%' and tds.to_ids='$loginUser' and flag_multidelete='1'";
                                    }
                                    mysqli_set_charset($db_con, 'utf8');
                                    $ShDocId = mysqli_query($db_con, "SELECT tdm.doc_id, tdm.doc_name,tdm.old_doc_name,tdm.doc_extn,tds.from_id,tds.to_ids,tds.doc_ids,tds.share_date FROM `tbl_document_share_for_split_pdf` tds INNER JOIN tbl_document_master_for_split_pdf tdm ON tds.doc_ids=tdm.doc_id $where") or die('Error in share with id fetch' . mysqli_error($db_con));
                                    $foundnum = mysqli_num_rows($ShDocId);
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


                                        <div class="box-body">
                                            <label><?php echo $lang['Show']; ?> </label>
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
                                            </select> <label><?php echo $lang['Documents']; ?></label>
                                            <div class="pull-right record">
                                                <label><?php echo $start + 1 ?> <?php echo $lang['To'] ?> <?php
                                                    if (($start + $per_page) > $foundnum) {
                                                        echo $foundnum;
                                                    } else {
                                                        echo ($start + $per_page);
                                                    }
                                                    ?> <span><?php echo $lang['Ttal_Rcrds']; ?> : <?php echo $foundnum; ?></span></label>
                                            </div>
                                            <table class="table table-striped table-bordered"> 
                                                <?php
                                                $ShareDoc = mysqli_query($db_con, "SELECT tdm.doc_id, tdm.doc_name,tdm.old_doc_name,tdm.doc_extn,tdm.checkin_checkout,tds.from_id,tds.to_ids,tds.doc_ids,tds.share_date,tds.doc_share_valid_upto FROM `tbl_document_share_for_split_pdf` tds INNER JOIN tbl_document_master_for_split_pdf tdm ON tds.doc_ids=tdm.doc_id $where order by tdm.old_doc_name LIMIT $start, $per_page") or die('Error in share id fetch' . mysqli_error($db_con));
                                               
                                                if (mysqli_num_rows($ShareDoc) > 0) {
                                                     while ($row = mysqli_fetch_array($ShareDoc, MYSQLI_ASSOC)) {
                                                    $storeArray[] =  $row;  
                                                }

                                                //ab@100421 alphanumeric sorting
                                                $doc_column = array_column($storeArray, 'old_doc_name');
                                                array_multisort($doc_column, SORT_NATURAL, $storeArray);
                                                if($_GET['stype']=='2'){
                                                    $storeArray = array_reverse($storeArray);
                                                }
                                                    ?>
                                                    <thead>
                                                        <tr>
                                                            <th class="js-sort-none"><?php echo $lang['Sr_No']; ?></th>
                                                            <th class="js-sort-none" id="name_col"><?php echo $lang['File_Name']; ?> <i class="fa fa-sort"></i></th>
                                                            <th class="js-sort-none"><?php echo $lang['Storage_Name']; ?></th>
                                                            <th class="js-sort-none"><?php echo $lang['Shared_by']; ?></th>
                                                            <th class="js-sort-date"><?php echo $lang['Shre_Dte']; ?></th>
                                                            
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $i = 1;
                                                        $i += $start;

                                                       // while ($rwShareName = mysqli_fetch_assoc($ShareDoc)) {
                                                         foreach($storeArray as $rwShareName){
                                                            $ShUserName = mysqli_query($db_con, "SELECT * FROM `tbl_user_master` WHERE user_id = '$rwShareName[from_id]' order by first_name, last_name asc") or die('Error in share userNane fetch' . mysqli_error($db_con));
                                                            $rwUserName = mysqli_fetch_assoc($ShUserName);
                                                            ?>
                                                            <tr class="gradeX">
                                                                <td><?php echo $i . '.'; ?></td>
                                                                <td>
                                                                    <?php
                                                                    $checkfileLock = mysqli_query($db_con, "SELECT * FROM `tbl_locked_file_master` WHERE doc_id='" . $rwShareName['doc_id'] . "' and is_active='1' and user_id='" . $_SESSION['cdes_user_id'] . "'");
                                                                    $rwfileLock = mysqli_fetch_assoc($checkfileLock);
                                                                    if ($rwfileLock['doc_id'] != $rwShareName['doc_id']) {
                                                                        //@sk(221118): include view handler to handle different file formats
                                                                        if (file_exists('thumbnail/' . base64_encode($rwShareName['doc_id']) . '.jpg')) {
                                                                            ?><div> <img class="thumb-image" src="thumbnail/<?= base64_encode($rwShareName['doc_id']) ?>.jpg"> </div>
                                                                            <?php
                                                                        }
                                                                        echo $rwShareName['old_doc_name'];
                                                                        if ($rwShareName['checkin_checkout'] == '1') {
                                                                            $file_row = $rwShareName;
                                                                            require 'view-handler-splt.php';
                                                                        } else {
                                                                            echo ' <i class="fa fa-eye-slash" title="' . $lang['Checkout'] . ' ' . $lang['files'] . '"></i>';
                                                                        }
                                                                    } else {
                                                                        echo $rwShareName['old_doc_name'];
                                                                        ?>
                                                                        <a href="javascript:void(0)"  data="<?php echo $rwShareName['doc_id'] ?>" class="send_lock_request" data-toggle="dropdown" aria-expanded="true" style="font-size: 20px;"><i class="md md-lock" title="<?php echo $lang['lock_file']; ?>"></i></a>  
                                                                        <?php
                                                                    }
                                                                    ?> 

                                                                </td>
                                                                <?php
                                                                $ShDocslname = mysqli_query($db_con, "SELECT * FROM `tbl_storage_level` WHERE sl_id = '$rwShareName[doc_name]'") or die('Error in share id fetch' . mysqli_error($db_con));
                                                                $rwslname = mysqli_fetch_assoc($ShDocslname);
                                                                ?>
                                                                <td><?php echo $rwslname['sl_name']; ?></td>
                                                                <td><?php echo $rwUserName['first_name'] . ' ' . $rwUserName['last_name']; ?></td>
                                                                <td><?php echo '<label class="label label-primary">' . date('d-m-Y h:i:s A', strtotime($rwShareName['share_date'])) . '</label>'; ?></td> 
                                                                
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
                                                      if(isset($_GET['stype']) and $_GET['stype']!=''){
                                                            $stype = "&stype=".$_GET['stype'];
                                                        }else{
                                                            $stype = '';
                                                        }
                                                    ?>
                                                    <ul class='pagination strgePage'>
                                                        <?php
                                                        //previous button
                                                        if (!($start <= 0))
                                                            echo " <li><a href='?start=$prev&shared_doc=" . $_GET['shared_doc'] . "&stype=".$stype."&limit=" . $per_page . "'>$lang[Prev]</a> </li>";
                                                        else
                                                            echo " <li class='disabled'><a href='javascript:void(0)'>$lang[Prev]</a> </li>";
                                                        //pages 
                                                        if ($max_pages < 7 + ($adjacents * 2)) {   //not enough pages to bother breaking it up
                                                            $i = 0;
                                                            for ($counter = 1; $counter <= $max_pages; $counter++) {
                                                                if ($i == $start) {
                                                                    echo "<li class='active'><a href='?start=$i&limit=$per_page&shared_doc=" . $_GET['shared_doc'] . "&stype=".$stype."'><b>$counter</b></a> </li>";
                                                                } else {
                                                                    echo "<li><a href='?start=$i&limit=$per_page&shared_doc=" . $_GET['shared_doc'] . "&stype=".$stype."'>$counter</a></li> ";
                                                                }
                                                                $i = $i + $per_page;
                                                            }
                                                        } elseif ($max_pages > 5 + ($adjacents * 2)) {    //enough pages to hide some
                                                            //close to beginning; only hide later pages
                                                            if (($start / $per_page) < 1 + ($adjacents * 2)) {
                                                                $i = 0;
                                                                for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                                                                    if ($i == $start) {
                                                                        echo " <li class='active'><a href='?start=$i&limit=$per_page&shared_doc=" . $_GET['shared_doc'] . "&stype=".$stype."'><b>$counter</b></a></li> ";
                                                                    } else {
                                                                        echo "<li> <a href='?start=$i&limit=$per_page&shared_doc=" . $_GET['shared_doc'] . "&stype=".$stype."'>$counter</a> </li>";
                                                                    }
                                                                    $i = $i + $per_page;
                                                                }
                                                            }
                                                            //in middle; hide some front and some back
                                                            elseif ($max_pages - ($adjacents * 2) > ($start / $per_page) && ($start / $per_page) > ($adjacents * 2)) {
                                                                echo " <li><a href='?start=0&limit=$per_page&shared_doc=" . $_GET['shared_doc'] . "&stype=".$stype."'>1</a></li> ";
                                                                echo "<li><a href='?start=$per_page&limit=$per_page&shared_doc=" . $_GET['shared_doc'] . "&stype=".$stype."'>2</a></li>";
                                                                echo "<li><a href='javascript:void(0)'>...</a></li>";

                                                                $i = $start;
                                                                for ($counter = ($start / $per_page) + 1; $counter < ($start / $per_page) + $adjacents + 2; $counter++) {
                                                                    if ($i == $start) {
                                                                        echo " <li class='active'><a href='?start=$i&limit=$per_page&shared_doc=" . $_GET['shared_doc'] . "&stype=".$stype."'><b>$counter</b></a></li> ";
                                                                    } else {
                                                                        echo " <li><a href='?start=$i&limit=$per_page&shared_doc=" . $_GET['shared_doc'] . "&stype=".$stype."'>$counter</a> </li>";
                                                                    }
                                                                    $i = $i + $per_page;
                                                                }
                                                            }
                                                            //close to end; only hide early pages
                                                            else {
                                                                echo "<li> <a href='?start=0&limit=$per_page&shared_doc=" . $_GET['shared_doc'] . "&stype=".$stype."'>1</a> </li>";
                                                                echo "<li><a href='?start=$per_page&limit=$per_page&shared_doc=" . $_GET['shared_doc'] . "&stype=".$stype."'>2</a></li>";
                                                                echo "<li><a href='javascript:void(0)'>...</a></li>";

                                                                $i = $start;
                                                                for ($counter = ($start / $per_page) + 1; $counter <= $max_pages; $counter++) {
                                                                    if ($i == $start) {
                                                                        echo " <li class='active'><a href='?start=$i&limit=$per_page&shared_doc=" . $_GET['shared_doc'] . "&stype=".$stype."'><b>$counter</b></a></li> ";
                                                                    } else {
                                                                        echo "<li> <a href='?start=$i&limit=$per_page&shared_doc=" . $_GET['shared_doc'] . "&stype=".$stype."'>$counter</a></li> ";
                                                                    }
                                                                    $i = $i + $per_page;
                                                                }
                                                            }
                                                        }
                                                        //next button
                                                        if (!($start >= $foundnum - $per_page))
                                                            echo "<li><a href='?start=$next&shared_doc=" . $_GET['shared_doc'] . "&stype=".$stype."&limit=" . $per_page . "'>$lang[Next]</a></li>";
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
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th class="js-sort-number"><?php echo $lang['Sr_No']; ?></th>
                                                        <th><?php echo $lang['File_Name']; ?></th>
                                                        <th><?php echo $lang['Storage_Name']; ?></th>
                                                        <th><?php echo $lang['Shared_by']; ?></th>
                                                        <th><?php echo $lang['Shre_Dte']; ?></th>
                                                       
                                                </thead>
                                                </tr>
                                                <?php
                                                if (isset($_GET['shared_doc']) && !empty($_GET['shared_doc'])) {
                                                    echo '<tr><td colspan="5" class="text-center"><label><strong class="text-danger"><i class="ti-face-sad text-pink"></i> ' . $lang['Who0ps!_No_Records_Found'] . '</strong></label></td></tr>';
                                                } else {
                                                    echo '<tr><td colspan="5" class="text-center"><label><strong class="text-danger">' . $lang['Yu_Hv_No_Shred_Fles'] . '</strong></label></td></tr>';
                                                }
                                                ?>
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
            </div>
        </div>
        <?php require_once './application/pages/footer.php'; ?>

    </div>

    <!-- Right Sidebar -->
    <?php //require_once './application/pages/rightSidebar.php';      ?>
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
                var $shared = $("#shared_doc").val();
                url = removeParam("start", url);
                url = removeParam("shared_doc", url);
                if ($shared != '') {
                    url = url + "&shared_doc=" + $shared;
                }
                window.open(url, "_parent");
            })

        })

//array.sort(function(a,b){
//  // Turn your strings into dates, and then subtract them
//  // to get a value that is either negative, positive, or zero.
//  return new Date(b.date) - new Date(a.date);
//});
    </script>

</body>
</html>