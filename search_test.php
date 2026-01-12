<!DOCTYPE html>
<html>

    <?php
    ini_set('memory_limit', '-1');
    set_time_limit(0);
    require_once './loginvalidate.php';
    if (!$_SESSION['cdes_user_id']) {
        header('Location:index');
    }
    require_once './application/config/database.php';
    //require_once './application/config/db_sql.php';
    require_once './application/pages/head.php';
    require_once './application/pages/function.php';
    require_once './excel-viewer/excel_reader.php';
    $chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

    $rwgetRole = mysqli_fetch_assoc($chekUsr);

    // echo $rwgetRole['dashboard_mydms']; die;
    if ($rwgetRole['metadata_quick_search'] != '1') {
        header('Location: ./index');
    }
    ?>
    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />


    <!-- Plugin Css-->
    <link rel="stylesheet" href="assets/plugins/magnific-popup/css/magnific-popup.css" />
    <link rel="stylesheet" href="assets/plugins/jquery-datatables-editable/datatables.css" />
    <link href="assets/plugins/datatables/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css"/>

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
                                        <a href="search"><?php echo $lang['Ezeefile_DMS']; ?></a>
                                    </li>
                                    <li class="active">
                                        <?php echo $lang['quich_search']; ?>
                                    </li>
                                </ol>
                            </div>
                        </div>

                        <div class="box box-primary">
                            <div class="box-body ">

                                <div class="card-box">
                                    <div class="row">
                                        <form >
                                            <div class="col-md-12">

                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" name="searchText" value="<?php echo $_GET['searchText'] ?>" placeholder="<?php echo $lang['entr_srch_txt_hr']; ?>">
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="submit" class="btn btn-primary" id="search"><i class="fa fa-search"></i></button>
                                                </div>
                                            </div>

                                        </form>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="" style="overflow: auto;">

                                    <?php
                                    $perm = mysqli_query($db_con, "select sl_id from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]' group by sl_id");
                                    $rwPerm = mysqli_fetch_assoc($perm);
                                    $slperm = $rwPerm['sl_id'];
                                    $sllevel = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slperm'");
                                    $rwSllevel = mysqli_fetch_assoc($sllevel);
                                    $level = $rwSllevel['sl_depth_level'];
                                    //echo $slperm;

                                    if (isset($_GET['searchText'])) {

                                        //echo $slperm;

                                        $searchText = $_GET['searchText'];
                                        $searchText = mysqli_real_escape_string($db_con, $searchText);
                                        $res = searchAllDB($searchText, $db_con, $slperm);
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

        </div>
        <!-- END wrapper -->
        <?php require_once './application/pages/footerForjs.php'; ?>
        <script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>

        <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>

        <script type="text/javascript">
            $(".select2").select2();
            //firstname last name 
            $("input#groupName").keypress(function (e) {
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



//filenotfound();
        </script>

        <?php
//print_r($res);


        $aa = array();

        // $marray = findChild($slperm);
        //  print_r($marray);

        function findChild($slperm) {

            global $db_con;
            global $aa;
            $aa[] = $slperm;
            $sql_child = "select * FROM tbl_storage_level WHERE sl_parent_id = '$slperm' ";
            $sql_child_run = mysqli_query($db_con, $sql_child) or die('Error:' . mysqli_error($db_con));
            if (mysqli_num_rows($sql_child_run) > 0) {

                while ($rwchild = mysqli_fetch_assoc($sql_child_run)) {

                    $child = $rwchild['sl_id'];
                    //$aa[] = $child;
                    $clagain = findChild($child);
                }
            }
            return $aa;
        }

        function searchAllDB($search, $db_con, $slperm) {
            //  $out = "";
            if (isset($_SESSION['lang'])) {
                $file = $_SESSION['lang'] . ".json";
            } else {
                $file = "English.json";
            }
            $data = file_get_contents($file);
            $lang = json_decode($data, true);
            ?>
            <?php
            $table = "tbl_document_master";
            $sql_search_fields = Array();
            $sql_searchCt = "select doc_extn,doc_name,doc_id,doc_path,old_doc_name,doc_size,noofpages from " . $table . " where flag_multidelete=1 and";
            $fields = array();
            $sql_count = "SHOW COLUMNS FROM " . $table;
            $rscont = mysqli_query($db_con, $sql_count);
            if (mysqli_num_rows($rscont) > 0) {

                while ($rfield = mysqli_fetch_array($rscont)) {
                    $colum = $rfield[0];

                    $sql_search_fields[] = 'CONVERT(`' . $colum . "` USING utf8) like('%" . $search . "%')";
                    $fields[] = $colum;
                }

                mysqli_close($rs2);
            }
            $marray = findChild($slperm);
            $sql_searchCt .= "( doc_name=";
            $sql_searchCt .= implode(" OR doc_name=", $marray);
            //$sql_search .= ' and '.findChild($slperm);
            $sql_searchCt .= ") and (";

            $sql_searchCt .= implode(" OR ", $sql_search_fields);
            $sql_searchCt .= ")";
            $rscontqry = mysqli_query($db_con, $sql_searchCt);
            $foundnum = mysqli_num_rows($rscontqry);
            if ($foundnum > 0) {
                if (is_numeric($_GET['limit'])) {
                    $per_page = $_GET['limit'];
                } else {
                    $per_page = 10;
                }
                $start = isset($_GET['start']) ? $_GET['start'] : '';
                $max_pages = ceil($foundnum / $per_page);
                if (!$start) {
                    $start = 0;
                }

                //$allot = "select * from tbl_document_master $where order by old_doc_name LIMIT $start, $per_page";
                //$allot_query = mysqli_query($db_con, $allot) or die("Error: " . mysqli_error($db_con));
                ?>
                <div class="container" >
                    <div class="pull-right record">
                        <?php echo $start + 1 ?> <?php echo $lang['To']; ?> <?php
                        if (($start + 10) > $foundnum) {
                            echo $foundnum;
                        } else {
                            echo ($start + 10);
                        };
                        ?>  <span><?php echo $lang['Ttal_Rcrds']; ?>: <?php echo $foundnum; ?></span>
                    </div>
                    <div class="box-body limit">
                        <?php
                        $limit = trim($_GET['limit']);

                        if (isset($limit) and ! empty($limit) and $limit == '') {

                            $rec_limit = $limit;
                        } else {

                            $rec_limit = 10;
                        }
                        $user_id = $_SESSION[cdes_user_id];
                        $chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um where FIND_IN_SET('$user_id', user_ids) > 0") or die('Error:' . mysqli_error($db_con));
                        $rwcheckUser = mysqli_fetch_assoc($chekUsr);
                        if ($rwcheckUser['role_id'] == 1) {
                            $sql = "SELECT count(doc_id) FROM  tbl_document_master where  flag_multidelete=1 and ";
                        } else {
                            $sql = "SELECT count(doc_id) FROM  tbl_document_master where flag_multidelete=1 and ";
                        }
                        $sql .= "( doc_name=";
                        $sql .= implode(" OR doc_name=", $marray);
                        //$sql_search .= ' and '.findChild($slperm);
                        $sql .= ") and (";

                        $sql .= implode(" OR ", $sql_search_fields);
                        $sql .= ")";

                        $retval = mysqli_query($db_con, $sql) or die('Could not get data: ' . mysqli_error($db_con));
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
                        <?php echo $lang['Show']; ?> <select id="limit">
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
                        </select><?php echo ' ' . $lang['Documents']; ?>
                    </div>
                    <table class="table table-striped table-bordered dataTable no-footer" id="" role="grid" aria-describedby="datatable_info">
                        <?php
                        //$out .= $table.";";
                        $sql_search = "select doc_extn,doc_name,doc_id,doc_path,old_doc_name,doc_size,noofpages from " . $table . " where ";
                        // findChild($slperm);


                        $sql2 = "SHOW COLUMNS FROM " . $table;
                        $rs2 = mysqli_query($db_con, $sql2);
                        if (mysqli_num_rows($rs2) > 0) {
                            echo '<thead><tr>';
                            echo '<th>' . $lang['Sr_No'] . '</th>';
                            echo '<th>' . $lang['File_Name'] . '</th>';
                            echo '<th>' . $lang['File_Size'] . '</th>';
                            echo '<th>' . $lang['No_of_Pages'] . '</th>';
                            echo '<th>' . $lang['Storage_Name'] . '</th>';
                            echo '<th>' . $lang['MetaData'] . '</th>';
//                            while ($r2 = mysqli_fetch_array($rs2)) {
//                                $colum = $r2[0];
//
//                                $sql_search_fields[] = 'CONVERT(`' . $colum . "` USING utf8) like('%" . $search . "%')";
//                                $fields[] = $colum;
//                            }
                            echo'</tr></thead>';
                            mysqli_close($rs2);
                        }


                        $sql_search .= "( doc_name=";
                        $sql_search .= implode(" OR doc_name=", $marray);
                        //$sql_search .= ' and '.findChild($slperm);
                        $sql_search .= ") and (";

                        $sql_search .= implode(" OR ", $sql_search_fields);
                        $sql_search .= ") LIMIT $start, $per_page";

                        $rs3 = mysqli_query($db_con, $sql_search);
                        //$out .= mysqli_num_rows($rs3)."\n ok";
                        echo'<tbody>';
                        if (mysqli_num_rows($rs3) > 0) {


                            $i = 1;
                            while ($rw = mysqli_fetch_assoc($rs3)) {

                                if ($rw['doc_extn'] == 'pdf') {
                                    $sl_name = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id='$rw[doc_name]'");
                                    $sl_row = mysqli_fetch_assoc($sl_name);
                                    $file = "extract-here/" . $sl_row['sl_name'] . "/TXT/" . $rw['doc_id'] . ".txt";
                                } else if ($rw['doc_extn'] == 'text' || $rw['doc_extn'] == 'txt') {
                                    $file = "extract-here/" . $rw['doc_path'];
                                } else if ($rw['doc_extn'] == "xls" || strtolower($rw['doc_extn']) == 'xlsx') {
                                    $file = "extract-here/" . $rw['doc_path'];
                                    $line = file_get_contents($file);
                                    $contents = preg_replace("/[^a-zA-Z0-9\s\,\.\-\n\r\t@\/\_\(\)]/", "", $line);
                                } else if ($rw['doc_extn'] == "doc") {
                                    $file = "extract-here/" . $rw['doc_path'];
                                    $contents = read_doc($file);
                                    //$contents = preg_replace("/[^a-zA-Z0-9\s\,\.\-\n\r\t@\/\_\(\)]/", "", $line);  
                                } else if ($rw['doc_extn'] == "docx") {
                                    $file = "extract-here/" . $rw['doc_path'];
                                    $contents = read_docx($file);
                                }
                                $doc_name = explode('_', $rw['doc_name']);


                                if (count($doc_name) == 1) {




                                    if (strpos($contents, $search) != false) {

                                        echo '<tr>';
                                        echo '<td>' . $i . '</td>';
                                        echo '<td>';
                                        $chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));
                                        $rwgetRole = mysqli_fetch_assoc($chekUsr);
                                        ?>

                                        <!--for pdf viewer--> 
                                        <?php if ($rw['doc_extn'] == 'pdf') { ?>
                                            <a href="flipflop-viewer?i=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])) ?>&i=<?php echo urlencode(base64_encode($rw['doc_id'])); ?>" id="fancybox-inner" class="pdfview" target="_blank"><?php echo $rw['old_doc_name']; ?>
                                                <i class="ti-book" style="font-size: 18px;"></i></a>
                                            <a href="viewer?id=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])) ?>&i=<?php echo urlencode(base64_encode($rw['doc_id'])); ?>" id="fancybox-inner" class="pdfview"  target="_blank">
                                                <i class="fa fa-file-pdf-o"></i></a>
                                            <!--for image viewer -->
                                        <?php } else if ($rw['doc_extn'] == 'jpg' || $rw['doc_extn'] == 'png' || $rw['doc_extn'] == 'gif') { ?>
                                            <a href="#" data-toggle="modal" data-target="#full-width-modal" id="showPic" data="extract-here/<?php echo $rw['doc_path']; ?>"><?php echo $rw['old_doc_name']; ?> <i class="fa fa-picture-o"></i></a>
                                            <!--for tiff files -->
                                        <?php } else if (strtolower($rw['doc_extn']) == 'tif' || strtolower($rw['doc_extn']) == 'tiff') { ?>
                                            <a href="file?uid=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&file=<?php echo urlencode(base64_encode($rw['doc_id'])); ?>" target="_blank" >
                                                <?php if ($rwgetRole['tif_file'] == '1') { ?>
                                                    <?php echo $rw['old_doc_name']; ?> <i class="fa fa-picture-o"></i>
                                                </a>
                                                <!--for xlsx files -->
                                                <?php
                                            }
                                        } else if (strtolower($rw['doc_extn']) == 'xlsx') {
                                            ?>
                                            <a href="xlsx?uid=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&file=<?php echo urlencode(base64_encode($rw['doc_id'])); ?>" target="_blank">
                                                <?php if ($rwgetRole['excel_file'] == '1') { ?>
                                                    <?php echo $rw['old_doc_name']; ?> <i class="fa fa-file-excel-o"></i></a>
                                                <?php } ?>

                                        <?php } else if (strtolower($rw['doc_extn']) == 'xls') {
                                            ?>
                                            <a href="xls?uid=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&file=<?php echo urlencode(base64_encode($rw['doc_id'])); ?>" target="_blank">
                                                <?php if ($rwgetRole['excel_file'] == '1') { ?>
                                                    <?php echo $rw['old_doc_name']; ?> <i class="fa fa-file-excel-o"></i></a>
                                                <?php } ?>
                                            <!--for docx files -->
                                        <?php } else if ($rw['doc_extn'] == 'docx' || $rw['doc_extn'] == 'doc') { ?>
                                            <a href="docx?uid=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&file=<?php echo urlencode(base64_encode($rw['doc_id'])); ?>" target="_blank"><?php echo $rw['old_doc_name']; ?> <i class="fa fa-file-word-o"></i></a>

                                            <!--for audio player -->
                                        <?php } else if ($rw['doc_extn'] == 'mp3') { ?>
                                            <a href="javascript:void(0)" data-toggle="modal" data-target="#modal-audio" data="<?php echo $rw['doc_id']; ?>" id="audio">
                                                <?php echo $rw['old_doc_name']; ?> <i class="fa fa-music"></i>
                                            </a>
                                            <!--for video player -->
                                        <?php } else if ($rw['doc_extn'] == 'mp4') { ?>
                                            <a href="javascript:void(0)" data-toggle="modal" data-target="#modal-video" data="<?php echo $rw['doc_id']; ?>" id="video"> <?php echo $rw['old_doc_name']; ?> <i class="fa fa-video-camera"></i></a>
                                        <?php } else { ?>

                                            <a href="extract-here/<?php echo $rw['doc_path']; ?>" id="fancybox-inner" target="_blank"><?php echo $rw['old_doc_name']; ?>
                                            </a>
                                        <?php } ?>
                                        <?php
                                        //echo substr($rw['old_doc_name'], stripos($rw['old_doc_name'], "_")+1) .
                                        '</td>';
                                        echo'<td>' . round($rw['doc_size'] / 1024) . 'KB</td>';
                                        echo'<td>' . $rw['noofpages'] . '</td>';
                                        // echo'</tr>';
                                        ?>
                        <!-- <tr> -->
                                        <td>
                                            <?php
                                            //storage name
                                            $strgName = mysqli_query($db_con, "select * from tbl_storage_level where sl_id = '$rw[doc_name]'") or die('Error:' . mysqli_error($db_con));
                                            $rwstrgName = mysqli_fetch_assoc($strgName);
                                            echo $rwstrgName['sl_name'];
                                            ?>
                                        </td>
                                        <td colspan="50">

                                            <?php
                                            $getMetaId = mysqli_query($db_con, "select * from tbl_metadata_to_storagelevel where sl_id = '$rw[doc_name]'") or die('Error:' . mysqli_error($db_con));

                                            while ($rwgetMetaId = mysqli_fetch_assoc($getMetaId)) {

                                                $getMetaName = mysqli_query($db_con, "select * from tbl_metadata_master where id = '$rwgetMetaId[metadata_id]'") or die('Error:' . mysqli_error($db_con));

                                                while ($rwgetMetaName = mysqli_fetch_assoc($getMetaName)) {

                                                    echo "<strong>" . $rwgetMetaName['field_name'] . "</strong>: ";
                                                    $meta = mysqli_query($db_con, "select `$rwgetMetaName[field_name]` from tbl_document_master where doc_id='$rw[doc_id]'");
                                                    $rwMeta = mysqli_fetch_array($meta);
                                                    echo $rwMeta[$rwgetMetaName['field_name']];
                                                    echo " | ";
                                                }
                                            }
                                            ?>
                                        </td>
                                        <!-- </tr> -->
                                        <?php
                                        echo'</tr>';
                                        $i++;
                                    } else {
                                        echo '<tr>';
                                        echo '<td>' . $i . '</td>';
                                        echo '<td>';
                                        $chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));
                                        $rwgetRole = mysqli_fetch_assoc($chekUsr);
                                        ?>

                                        <!--for pdf viewer--> 
                                        <?php if ($rw['doc_extn'] == 'pdf') { ?>
                                            <a href="flipflop-viewer?i=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])) ?>&i=<?php echo urlencode(base64_encode($rw['doc_id'])); ?>" id="fancybox-inner" class="pdfview" target="_blank"><?php echo $rw['old_doc_name']; ?>
                                                <i class="ti-book" style="font-size: 18px;"></i></a>
                                            <a href="viewer?id=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])) ?>&i=<?php echo urlencode(base64_encode($rw['doc_id'])); ?>" id="fancybox-inner" class="pdfview"  target="_blank">
                                                <i class="fa fa-file-pdf-o"></i></a>
                                            <!--for image viewer -->
                                        <?php } else if ($rw['doc_extn'] == 'jpg' || $rw['doc_extn'] == 'png' || $rw['doc_extn'] == 'gif') { ?>
                                            <a href="#" data-toggle="modal" data-target="#full-width-modal" id="showPic" data="extract-here/<?php echo $rw['doc_path']; ?>"><?php echo $rw['old_doc_name']; ?> <i class="fa fa-picture-o"></i></a>
                                            <!--for tiff files -->
                                        <?php } else if (strtolower($rw['doc_extn']) == 'tif' || strtolower($rw['doc_extn']) == 'tiff') { ?>
                                            <a href="file?uid=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&file=<?php echo urlencode(base64_encode($rw['doc_id'])); ?>" target="_blank" >
                                                <?php if ($rwgetRole['tif_file'] == '1') { ?>
                                                    <?php echo $rw['old_doc_name']; ?> <i class="fa fa-picture-o"></i>
                                                </a>
                                                <!--for xlsx files -->
                                                <?php
                                            }
                                        } else if (strtolower($rw['doc_extn']) == 'xlsx') {
                                            ?>
                                            <a href="xlsx?uid=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&file=<?php echo urlencode(base64_encode($rw['doc_id'])); ?>" target="_blank">
                                                <?php if ($rwgetRole['excel_file'] == '1') { ?>
                                                    <?php echo $rw['old_doc_name']; ?> <i class="fa fa-file-excel-o"></i></a>
                                                <?php } ?>

                                        <?php } else if (strtolower($rw['doc_extn']) == 'xls') {
                                            ?>
                                            <a href="xls?uid=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&file=<?php echo urlencode(base64_encode($rw['doc_id'])); ?>" target="_blank">
                                                <?php if ($rwgetRole['excel_file'] == '1') { ?>
                                                    <?php echo $rw['old_doc_name']; ?> <i class="fa fa-file-excel-o"></i></a>
                                                <?php } ?>
                                            <!--for docx files -->
                                        <?php } else if ($rw['doc_extn'] == 'docx' || $rw['doc_extn'] == 'doc') { ?>
                                            <a href="docx?uid=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&file=<?php echo urlencode(base64_encode($rw['doc_id'])); ?>" target="_blank"><?php echo $rw['old_doc_name']; ?> <i class="fa fa-file-word-o"></i></a>

                                            <!--for audio player -->
                                        <?php } else if ($rw['doc_extn'] == 'mp3') { ?>
                                            <a href="javascript:void(0)" data-toggle="modal" data-target="#modal-audio" data="<?php echo $rw['doc_id']; ?>" id="audio">
                                                <?php echo $rw['old_doc_name']; ?> <i class="fa fa-music"></i>
                                            </a>
                                            <!--for video player -->
                                        <?php } else if ($rw['doc_extn'] == 'mp4') { ?>
                                            <a href="javascript:void(0)" data-toggle="modal" data-target="#modal-video" data="<?php echo $rw['doc_id']; ?>" id="video"> <?php echo $rw['old_doc_name']; ?> <i class="fa fa-video-camera"></i></a>
                                        <?php } else { ?>

                                            <a href="extract-here/<?php echo $rw['doc_path']; ?>" id="fancybox-inner" target="_blank"><?php echo $rw['old_doc_name']; ?>
                                            </a>
                                        <?php } ?>
                                        <?php
                                        //echo substr($rw['old_doc_name'], stripos($rw['old_doc_name'], "_")+1) .
                                        '</td>';
                                        echo'<td>' . round($rw['doc_size'] / 1024) . 'KB</td>';
                                        echo'<td>' . $rw['noofpages'] . '</td>';
                                        // echo'</tr>';
                                        ?>
                        <!-- <tr> -->
                                        <td>
                                            <?php
                                            //storage name
                                            $strgName = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id = '$rw[doc_name]'") or die('Error:' . mysqli_error($db_con));
                                            $rwstrgName = mysqli_fetch_assoc($strgName);
                                            echo $rwstrgName['sl_name'];
                                            ?>
                                        </td>
                                        <td colspan="50">

                                            <?php
                                            $getMetaId = mysqli_query($db_con, "select metadata_id from tbl_metadata_to_storagelevel where sl_id = '$rw[doc_name]'") or die('Error:' . mysqli_error($db_con));

                                            while ($rwgetMetaId = mysqli_fetch_assoc($getMetaId)) {

                                                $getMetaName = mysqli_query($db_con, "select field_name from tbl_metadata_master where id = '$rwgetMetaId[metadata_id]'") or die('Error:' . mysqli_error($db_con));

                                                while ($rwgetMetaName = mysqli_fetch_assoc($getMetaName)) {

                                                    echo "<strong>" . $rwgetMetaName['field_name'] . "</strong>: ";
                                                    $meta = mysqli_query($db_con, "select `$rwgetMetaName[field_name]` from tbl_document_master where doc_id='$rw[doc_id]'");
                                                    $rwMeta = mysqli_fetch_array($meta);
                                                    echo $rwMeta[$rwgetMetaName['field_name']];
                                                    echo " | ";
                                                }
                                            }
                                            ?>
                                        </td>
                                        <!-- </tr> -->
                                        <?php
                                        echo'</tr>';
                                        $i++;
                                    }
                                }
                            }
                            mysqli_close($rs3);
                        } else {
                            $sql_cont_search = "select doc_extn,doc_name,doc_id,doc_path,old_doc_name,doc_size,noofpages from " . $table;
                            $rs4 = mysqli_query($db_con, $sql_cont_search);
                            $i = 1;
                            while ($rw_content_serch = mysqli_fetch_assoc($rs4)) {
                                if ($rw_content_serch['doc_extn'] == 'pdf') {
                                    $sl_name = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id='$rw_content_serch[doc_name]'");
                                    $sl_row = mysqli_fetch_assoc($sl_name);
                                    $file = "extract-here/" . $sl_row['sl_name'] . "/TXT/" . $rw_content_serch['doc_id'] . ".txt";
                                    $contents = file_get_contents($file);
                                } elseif ($rw_content_serch['doc_extn'] == 'xlsx') {
                                    $file = "extract-here/" . $rw_content_serch['doc_path'];
                                    $contents = xlsx_to_text($file);
                                    //$line = file_get_contents($file);
                                    //$contents = preg_replace("/[^a-zA-Z0-9\s\,\.\-\n\r\t@\/\_\(\)]/", "", $line);
                                } elseif ($rw_content_serch['doc_extn'] == "xls") {
                                    $file = "extract-here/" . $rw_content_serch['doc_path'];
                                    $contents = xls_to_txt($file);
                                    //$line = file_get_contents($file);
                                    //$contents = preg_replace("/[^a-zA-Z0-9\s\,\.\-\n\r\t@\/\_\(\)]/", "", $line);
                                } elseif ($rw_content_serch['doc_extn'] == 'txt' || $rw_content_serch['doc_extn'] == 'text') {
                                    $file = "extract-here/" . $rw_content_serch['doc_path'];
                                    $contents = file_get_contents($file);
                                } elseif ($rw_content_serch['doc_extn'] == 'docx') {
                                    $filename = "extract-here/" . $rw_content_serch['doc_path'];
                                    $contents = read_docx($filename);
                                } elseif ($rw_content_serch['doc_extn'] == "doc") {
                                    $filename = "extract-here/" . $rw_content_serch['doc_path'];
                                    $contents = read_doc($filename);
                                }
                                $doc_name = explode('_', $rw_content_serch['doc_name']);

                                if (count($doc_name) == 1) {
                                    if (strpos($contents, $search) != false) {
                                        echo '<tr>';
                                        echo '<td>' . $i . '</td>';
                                        echo '<td>';
                                        $chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));
                                        $rwgetRole = mysqli_fetch_assoc($chekUsr);
                                        ?>
                                        <!--for pdf viewer--> 
                                        <?php if ($rw_content_serch['doc_extn'] == 'pdf' && $rwgetRole['pdf_file'] == 1) { ?>
                                            <a href="flipflop-viewer?i=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])) ?>&i=<?php echo urlencode(base64_encode($rw['doc_id'])); ?>" id="fancybox-inner" class="pdfview" target="_blank"><?php echo $rw['old_doc_name']; ?>
                                                <i class="ti-book" style="font-size: 18px;"></i></a>
                                            <a href="viewer?id=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])) ?>&i=<?php echo urlencode(base64_encode($rw['doc_id'])); ?>" id="fancybox-inner" class="pdfview"  target="_blank">
                                                <i class="fa fa-file-pdf-o"></i></a>

                                            <?php
                                        } elseif ($rw_content_serch['doc_extn'] == 'pdf' && $rwgetRole['pdf_file'] != 1) {
                                            echo $rw_content_serch['old_doc_name'];
                                        }

                                        // for audio viwer
                                        else if ($rw_content_serch['doc_extn'] == 'mp3' && $rwgetRole['audio_file'] == 1) {
                                            ?>
                                            <a href="javascript:void(0)" data-toggle="modal" data-target="#modal-audio" data="<?php echo $rw_content_serch['doc_id']; ?>" id="audio"> <?php echo substr($rw_content_serch['old_doc_name'], stripos($rw_content_serch['old_doc_name']) + 0); ?>
                                                <i class="fa fa-music"></i></a>
                                            <?php
                                        } elseif ($rw_content_serch['doc_extn'] == 'mp3' && $rwgetRole['audio_file'] != 1) {
                                            echo $rw_content_serch['old_doc_name'];
                                        }

                                        //for video viwer
                                        else if ($rw_content_serch['doc_extn'] == 'mp4' && $rwgetRole['video_file'] == 1) {
                                            ?>
                                            <a href="javascript:void(0)" data-toggle="modal" data-target="#modal-video" data="<?php echo $rw_content_serch['doc_id']; ?>" id="video"> <?php echo substr($rw_content_serch['old_doc_name'], stripos($rw_content_serch['old_doc_name']) + 0); ?> <i class="fa fa-video-camera"></i></a>
                                            <?php
                                        } elseif ($rw_content_serch['doc_extn'] == 'mp4' && $rwgetRole['audio_file'] != 1) {
                                            echo $rw_content_serch['old_doc_name'];
                                        }

                                        //for Images viwer
                                        elseif (strtolower($rw_content_serch['doc_extn']) == 'jpg' || strtolower($rw_content_serch['doc_extn']) == 'png' || strtolower($rw_content_serch['doc_extn']) == 'gif' && $rwgetRole['image_file'] == 1) {
                                            ?>                                                
                                            <a href="#" data-toggle="modal" data-target="#full-width-modal" id="showPic" data="extract-here/<?php echo $rw_content_serch['doc_path']; ?>">
                                                <?php echo substr($rw_content_serch['old_doc_name'], stripos($rw_content_serch['old_doc_name']) + 0); ?> <i class="fa fa-picture-o"></i></a>
                                                <?php
                                            }

                                            //for Tiff viwer
                                            else if ($rw_content_serch['doc_extn'] == 'tiff' || $rw_content_serch['doc_extn'] == 'tif' && $rwgetRole['tif_file'] == 1) {
                                                ?>
                                            <a href="file?uid=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&file=<?php echo urlencode(base64_encode($rw_content_serch['doc_id'])); ?>" target="_blank" >
                                                <?php echo $rw_content_serch['old_doc_name']; ?> <i class="fa fa-picture-o"></i>
                                            </a>
                                            <?php
                                        } else if (strtolower($rw_content_serch['doc_extn']) == 'xlsx') {
                                            ?>
                                            <a href="xlsx?uid=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&file=<?php echo urlencode(base64_encode($rw_content_serch['doc_id'])); ?>" target="_blank">
                                                <?php if ($rwgetRole['excel_file'] == '1') { ?>
                                                    <?php echo $rw_content_serch['old_doc_name']; ?> <i class="fa fa-file-excel-o"></i></a>
                                                <?php } ?>

                                        <?php } else if (strtolower($rw_content_serch['doc_extn']) == 'xls') {
                                            ?>
                                            <a href="xls?uid=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&file=<?php echo urlencode(base64_encode($rw_content_serch['doc_id'])); ?>" target="_blank">

                                                <?php echo $rw_content_serch['old_doc_name']; ?> <i class="fa fa-file-excel-o"></i></a>
                                            <?php } else if ($rw_content_serch['doc_extn'] == 'doc' || $rw_content_serch['doc_extn'] == 'docx' && $rwgetRole['doc_file'] == 1) {
                                                ?>
                                            <a href="docx?uid=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&file=<?php echo urlencode(base64_encode($rw['doc_id'])); ?>" target="_blank"><?php echo $rw_content_serch['old_doc_name']; ?> <i class="fa fa-file-word-o"></i></a>
                                            <?php
                                        } else if ($rw_content_serch['doc_extn'] == 'doc' || $rw_content_serch['doc_extn'] == 'docx' && $rwgetRole['doc_file'] != 1) {
                                            echo $rw_content_serch['old_doc_name'];
                                        } else {
                                            ?>
                                            <a href="extract-here/<?php echo $rw_content_serch['doc_path']; ?>" id="fancybox-inner" target="_blank"><?php echo $rw_content_serch['old_doc_name']; ?>
                                            </a> 
                                        <?php } ?>
                                        <?php
                                        //echo substr($rw['old_doc_name'], stripos($rw['old_doc_name'], "_")+1) .
                                        '</td>';
                                        echo'<td>' . round($rw_content_serch['doc_size'] / 1024) . 'KB</td>';
                                        echo'<td>' . $rw_content_serch['noofpages'] . '</td>';
                                        // echo'</tr>';
                                        ?>
                        <!-- <tr> -->
                                        <td>
                                            <?php
                                            //storage name
                                            $strgName = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id = '$rw_content_serch[doc_name]'") or die('Error:' . mysqli_error($db_con));
                                            $rwstrgName = mysqli_fetch_assoc($strgName);
                                            echo $rwstrgName['sl_name'];
                                            ?>
                                        </td>
                                        <td colspan="50">

                                            <?php
                                            $getMetaId = mysqli_query($db_con, "select metadata_id from tbl_metadata_to_storagelevel where sl_id = '$rw_content_serch[doc_name]'") or die('Error:' . mysqli_error($db_con));

                                            while ($rwgetMetaId = mysqli_fetch_assoc($getMetaId)) {

                                                $getMetaName = mysqli_query($db_con, "select field_name from tbl_metadata_master where id = '$rwgetMetaId[metadata_id]'") or die('Error:' . mysqli_error($db_con));

                                                while ($rwgetMetaName = mysqli_fetch_assoc($getMetaName)) {

                                                    echo "<strong>" . $rwgetMetaName['field_name'] . "</strong>: ";
                                                    $meta = mysqli_query($db_con, "select `$rwgetMetaName[field_name]` from tbl_document_master where doc_id='$rw_content_serch[doc_id]'");
                                                    $rwMeta = mysqli_fetch_array($meta);
                                                    echo $rwMeta[$rwgetMetaName['field_name']];
                                                    echo " | ";
                                                }
                                            }
                                            ?>
                                        </td>
                                        <!-- </tr> -->
                                        <?php
                                        echo'</tr>';
                                        $i++;
                                    }
                                }
                            }


                            mysqli_close($rs3);
                        }
                        echo '</tbody>';
                        ?>
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
                                echo " <li><a href='?searchText=$search&id=$_GET[id]&start=$prev'>" . $lang['Prev'] . "</a> </li>";
                            else
                                echo " <li class='disabled'><a href='javascript:void(0)'>" . $lang['Prev'] . "</a> </li>";
                            //pages 
                            if ($max_pages < 7 + ($adjacents * 2)) {   //not enough pages to bother breaking it up
                                $i = 0;
                                for ($counter = 1; $counter <= $max_pages; $counter++) {
                                    if ($i == $start) {
                                        echo " <li><a href='?searchText=$search&id=$_GET[id]&start=$i'><b>$counter</b></a> </li>";
                                    } else {
                                        echo "<li><a href='?searchText=$search&id=$_GET[id]&start=$i'>$counter</a></li> ";
                                    }
                                    $i = $i + $per_page;
                                }
                            } elseif ($max_pages > 5 + ($adjacents * 2)) {    //enough pages to hide some
                                //close to beginning; only hide later pages
                                if (($start / $per_page) < 1 + ($adjacents * 2)) {
                                    $i = 0;
                                    for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                                        if ($i == $start) {
                                            echo " <li><a href='?searchText=$search&id=$_GET[id]&start=$i'><b>$counter</b></a></li> ";
                                        } else {
                                            echo "<li> <a href='?searchText=$search&id=$_GET[id]&start=$i'>$counter</a> </li>";
                                        }
                                        $i = $i + $per_page;
                                    }
                                }
                                //in middle; hide some front and some back
                                elseif ($max_pages - ($adjacents * 2) > ($start / $per_page) && ($start / $per_page) > ($adjacents * 2)) {
                                    echo " <li><a href='?searchText=$search&id=$_GET[id]&start=0'>1</a></li> ";
                                    echo "<li><a href='?searchText=$search&id=$_GET[id]&start=$per_page'>2</a></li>";
                                    echo "<li><a href='javascript:void(0)'>...</a></li>";

                                    $i = $start;
                                    for ($counter = ($start / $per_page) + 1; $counter < ($start / $per_page) + $adjacents + 2; $counter++) {
                                        if ($i == $start) {
                                            echo " <li><a href='?searchText=$search&id=$_GET[id]&start=$i'><b>$counter</b></a></li> ";
                                        } else {
                                            echo " <li><a href='?searchText=$search&id=$_GET[id]&start=$i'>$counter</a> </li>";
                                        }
                                        $i = $i + $per_page;
                                    }
                                }
                                //close to end; only hide early pages
                                else {
                                    echo "<li> <a href='?searchText=$search&id=$_GET[id]&start=0'>1</a> </li>";
                                    echo "<li><a href='?searchText=$search&id=$_GET[id]&start=$per_page'>2</a></li>";
                                    echo "<li><a href='javascript:void(0)'>...</a></li>";

                                    $i = $start;
                                    for ($counter = ($start / $per_page) + 1; $counter <= $max_pages; $counter++) {
                                        if ($i == $start) {
                                            echo " <li><a href='?searchText=$search&id=$_GET[id]&start=$i'><b>$counter</b></a></li> ";
                                        } else {
                                            echo "<li> <a href='?searchText=$search&id=$_GET[id]&start=$i'>$counter</a></li> ";
                                        }
                                        $i = $i + $per_page;
                                    }
                                }
                            }
                            //next button
                            if (!($start >= $foundnum - $per_page))
                                echo "<li><a href='?searchText=$search&id=$_GET[id]&start=$next'>" . $lang['Next'] . "</a></li>";
                            else
                                echo "<li class='disabled'><a href='javascript:void(0)'>" . $lang['Next'] . "</a></li>";
                            ?>
                        </ul>
                        <?php
                    }
                    echo "</center>";
                    ?>
                </div>
                <?php
            } else {

                echo'<div style="text-align:center;"><h4 style="color:red;">' . $lang['File_Not_fnd'] . '</h4></div>';
            }
            //return $out;
        }
        ?>
        <script>
            $("a#showPic").click(function () {
                var path = $(this).attr('data');
                // alert(id);

                $.post("application/ajax/displayImage.php", {PATH: path}, function (result, status) {
                    if (status == 'success') {
                        $("#Display").html(result);
                        //alert(result);
                    }
                });
            });

        </script>

        <div id="full-width-modal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myLargeModalLabel"><?php echo $lang['Image_viewer']; ?></h4>
                    </div>
                    <div class="modal-body">
                        <div id="Display"></div>
                    </div>
                    <div class="modal-footer">

                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                    </div>
                </div>
            </div>

        </div>
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
        <!--for data table-->
        <script src="assets/plugins/datatables/jquery.dataTables.min.js"></script>
        <script src="assets/plugins/datatables/dataTables.bootstrap.js"></script>
        <script type="text/javascript">

            $(document).ready(function () {
                $('form').parsley();
                $('#datatable').dataTable();

            });

            $("a#video").click(function () {
                var id = $(this).attr('data');

                $.post("application/ajax/videoformat.php", {vid: id}, function (result, status) {
                    if (status == 'success') {
                        $("#videofor").html(result);
                        //alert(result);

                    }
                });
            });
            $("a#audio").click(function () {
                var id = $(this).attr('data');

                $.post("application/ajax/audioformat.php", {aid: id}, function (result, status) {
                    if (status == 'success') {
                        $("#foraudio").html(result);
                        //alert(result);

                    }
                });
            });
//            $(document).ready(function () {
//                $("#limit").change(function () {
//                    lval = $(this).val();
//                    url = removeParam("limit", url);
//                    url = url + "&limit=" + lval;
//                    window.open(url, "_parent");
//                });
//            });

        </script>
    </body>
</html>