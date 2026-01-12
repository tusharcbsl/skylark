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
    require_once './application/pages/head.php';
    require_once './application/pages/function.php';
    require_once './excel-viewer/excel_reader.php';
   
    $queryString = $_SERVER["QUERY_STRING"];
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
                                        <a href="#"><?php echo $lang['Storage_Manager']; ?></a>
                                    </li>
                                    <li class="active">
                                       <?= $lang['dynamic_search']; ?>
                                    </li>
                                    <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                                </ol>
                            </div>
                        </div>

                        <div class="box box-primary">

                            <div class="box-body ">
                                <div class="row">
                                    <div class="pull-left col-md-12">
                                        <form method="get">
                                            <div class="col-md-4 form-group" style="margin-left: 35%;">
                                                <input type="text" name="searchText" placeholder="<?php echo $lang['entr_srch_txt_hr']; ?>" class="form-control translatetext" value="<?php echo xss_clean($_GET['searchText']); ?>">
                                                <input type="hidden" value="<?php echo $_GET['id']; ?>" name="id">
                                            </div>  
                                            <div class="col-md-2">
                                                <a href="#" onclick="$(this).closest('form').submit()" class="btn btn-primary" title="Search"><i class="fa fa-search"></i></a>
                                            </div>

                                        </form>
                                    </div>
                                </div>
                                <div class="" style="overflow: auto;">
                                    <?php
                                    $slperm = base64_decode(urldecode($_GET['id']));
                                    if (isset($_GET['searchText'])) {
                                        $queryString = $_SERVER["QUERY_STRING"];
                                        $limit = $_GET['limit'];
                                        $start = $_GET['start'];
                                        $searchText = xss_clean($_GET['searchText']);
                                        $searchText = mysqli_real_escape_string($db_con, $searchText);
                                        $res = searchAllDB($searchText, $db_con, $slperm, $limit, $start, $queryString, $rwgetRole, $lang);
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
            <div id="assign-workflow" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" title="Close">×</button>
                            <h4 class="modal-title">Assign in Work flow</h4> 
                        </div>
                        <form method="post" class="form-inline" id="wfasign">
                            <div class="modal-body">
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <label>Assign To:</label>
                                        <select class="form-control" class="selectpicker" data-live-search="true" id="wfid" data-style="btn-white" style="" name="wfid">
                                            <option selected disabled style="background: #808080; color: #121213;">Select Workflow</option>
                                            <?php
                                            $WorkflwGet = mysqli_query($db_con, "select * from tbl_workflow_master") or die('Error in getWorkflw Assign:' . mysqli_error($db_con));
                                            while ($rwWorkflwGet = mysqli_fetch_assoc($WorkflwGet)) {
                                                ?> 
                                                <option value="<?php echo $rwWorkflwGet['workflow_id']; ?>" name="wrkname"><?php echo $rwWorkflwGet['workflow_name']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer"> 
                                <input type="hidden" id="mTowf" name="mTowf">
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button> 
                                <input type="submit" name="assignTo" class="btn btn-primary" value="Submit">
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
        <link href="assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet">
        <script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
        <!--display wait gif image after submit-->
        <?php
        $aa = array();

        function findChildIds($slperm) {

            global $db_con;
            global $aa;
            $aa[] = $slperm;
            $sql_child = "select * FROM tbl_storage_level WHERE sl_parent_id = '$slperm' ";
            $sql_child_run = mysqli_query($db_con, $sql_child) or die('Error:' . mysqli_error($db_con));
            if (mysqli_num_rows($sql_child_run) > 0) {

                while ($rwchild = mysqli_fetch_assoc($sql_child_run)) {

                    $child = $rwchild['sl_id'];
                    //$aa[] = $child;
                    $clagain = findChildIds($child);
                }
            }
            return $aa;
        }

        function searchAllDB($search, $db_con, $slperm, $limit, $start, $queryString, $rwgetRole, $lang) {
            //  $out = "";
            $table = "tbl_document_master";

            //$out .= $table.";";
            $sql_search = "select doc_extn,doc_name,doc_id,doc_path,old_doc_name,doc_size,noofpages,checkin_checkout from " . $table . " where ";
            // findChild($slperm);
            $sql_search_fields = Array();
            $fields = array();
            $marray = findChildIds($slperm);
            //print_r($marray);
            $sql_search .= "( substring_index(doc_name,'_',1)=";
            $sql_search .= implode(" OR substring_index(doc_name,'_',1)=", $marray);
            //$sql_search .= ' and '.findChild($slperm);
            $sql_search .= ") and (";
            $sql2 = "SHOW COLUMNS FROM " . $table;
            $rs2 = mysqli_query($db_con, $sql2);
            while ($r2 = mysqli_fetch_array($rs2)) {
                $colum = $r2[0];

                $sql_search_fields[] = 'CONVERT(`' . $colum . "` USING utf8) like('%" . $search . "%')";
                $fields[] = $colum;
            }
            $sql_search .= implode(" OR ", $sql_search_fields);
            $sql_search .= ") and flag_multidelete=1";
            $totalrowSql = $sql_search;
            $foundnumQuery = mysqli_query($db_con, $totalrowSql);
            $foundnum = mysqli_num_rows($foundnumQuery);
            if ($foundnum > 0) {
                if (is_numeric($limit)) {
                    $per_page = $limit;
                } else {
                    $per_page = 10;
                    $limit = 10;
                }
                $start = isset($_GET['start']) ? ($_GET['start']>0)?$_GET['start']:0 : 0;
                $max_pages = ceil($foundnum / $per_page);
                if (!$start) {
                    $start = 0;
                }
                //echo  $sql_search;
                $sql_search .= " limit $start,$per_page";

                $rs3 = mysqli_query($db_con, $sql_search);
                ?>
                <div class="container">

                    <div class="box-body">
                        <div class="col-sm-9">
                            <label><?php echo $lang['Show']; ?></label> <select id="limit" class="input-sm">
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
                            </select> <label><?php echo ' ' . $lang['Documents']; ?></label>
                        </div>
                        <div class="col-sm-3">
                            <div class="pull-right record">
                                <?php echo $start + 1 ?> <?php echo $lang['To']; ?> <?php
                                if (($start + $per_page) > $foundnum) {
                                    echo $foundnum;
                                } else {
                                    echo ($start + $per_page);
                                }
                                ?>  <span> <?php echo $lang['Ttal_Rcrds']; ?> : <?php echo $foundnum; ?> </span>
                            </div>
                        </div>
                    </div>
                    <table class="table table-striped table-bordered js-sort-table" role="grid" aria-describedby="datatable_info">
                        <?php
                        if (mysqli_num_rows($rs2) > 0) {
                            echo '<thead><tr>';
                            echo '<th class="sort-js-none" >' . $lang['Sr_No'] . '</th>';
                            echo '<th>' . $lang['File_Name'] . '</th>';
                            echo '<th class="sort-js-number" >' . $lang['File_Size'] . '</th>';
                            echo '<th class="sort-js-number">' . $lang['No_of_Pages'] . '</th>';
                            echo '<th>' . $lang['Storage_Name'] . '</th>';
                            echo '<th>' . $lang['MetaData'] . '</th>';
                            echo'</tr></thead>';

                            mysqli_close($rs2);
                        }
                        echo'<tbody>';
                        if (mysqli_num_rows($rs3) > 0) {
                            $i = $start + 1;
                            while ($rw = mysqli_fetch_assoc($rs3)) {
                                if (isset($rw['weeding_Out_Date'])) {
                                    $wedDate = $rw['weeding_Out_Date'];
                                    $weedDate = strtotime($wedDate);
                                    $todate = strtotime(date("Y-m-d"));
                                    if ($todate >= ($weedDate - 30 * 24 * 60 * 60)) {
                                        $weed = '#FFAAAA';
                                        $weedTile = 'weeding out date for this file is : ' . date('Y-m-d', $weedDate);
                                    } else {
                                        $weed = '#ebeff2';
                                        $weedTile = '';
                                    }
                                }
                                $doc_name = explode('_', $rw['doc_name']);
                                //if (count($doc_name) == 1) {
                                ?>
                                <tr style="background-color: <?php echo $weed; ?> !important" title="<?php echo $weedTile; ?>">
                                    <?php
                                    echo '<td>' . $i . '</td>';
                                    echo '<td>';
                                    
                                        $chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));
                                        $rwgetRole = mysqli_fetch_assoc($chekUsr);

                                        $file_row = $rw;
                                        
                                        require 'view-handler-search.php';
                                        echo $rw['old_doc_name'];


                                    '</td>';
                                    echo'<td>' . round($rw['doc_size'] / 1024) . 'KB</td>';
                                    echo'<td>' . $rw['noofpages'] . '</td>';
                                    // echo'</tr>';
                                    ?>
                               <!-- <tr> -->
                                    <td>
                                        <?php
                                        //storage name
                                        $strgName = mysqli_query($db_con, "select * from tbl_storage_level where sl_id = '$rw[doc_name]'"); //or die('Error:' . mysqli_error($db_con));
                                        $rwstrgName = mysqli_fetch_assoc($strgName);
                                        echo $rwstrgName['sl_name'];
                                        ?>
                                    </td>
                                    <td>

                                        <?php
                                        $getMetaId = mysqli_query($db_con, "select * from tbl_metadata_to_storagelevel where sl_id = '$rw[doc_name]'"); //or die('Error:' . mysqli_error($db_con));

                                        while ($rwgetMetaId = mysqli_fetch_assoc($getMetaId)) {

                                            $getMetaName = mysqli_query($db_con, "select * from tbl_metadata_master where id = '$rwgetMetaId[metadata_id]'"); //or die('Error:' . mysqli_error($db_con));

                                            while ($rwgetMetaName = mysqli_fetch_assoc($getMetaName)) {


                                                $meta = mysqli_query($db_con, "select `$rwgetMetaName[field_name]` from tbl_document_master where doc_id='$rw[doc_id]'");
                                                $rwMeta = mysqli_fetch_array($meta);
                                                if (!empty($rwMeta[$rwgetMetaName['field_name']])) {
                                                    echo "<strong>" . $rwgetMetaName['field_name'] . "</strong>: ";
                                                    echo $rwMeta[$rwgetMetaName['field_name']];
                                                    echo " | ";
                                                }
                                            }
                                        }
                                        ?>
                                    </td>

                                    <?php
                                    echo'</tr>';
                                    $i++;
                                    //}
                                }
                                mysqli_close($rs3);
                            }
                            echo '</tbody>';
                            ?>
                    </table> 
                    <?php
                    if ($limit && $start) {
                        $subString = "&start=$start&limit=$limit";
                        $queryString = str_replace($subString, "", $queryString);
                    } elseif (!empty($limit)) {
                        $subString = "&limit=$limit";
                        $queryString = str_replace($subString, "", $queryString);
                    }
                    echo "<center>";

                    $prev = $start - $per_page;
                    $next = $start + $per_page;

                    $adjacents = 3;
                    $last = $max_pages - 1;
                    if ($max_pages > 1) {
                        ?>
                        <ul class='pagination'>
                            <?php
                            //previous button
                            if (!($start <= 0))
                                echo " <li><a href='?$queryString&start=$prev&limit=$limit'>$lang[Prev]</a> </li>";
                            else
                                echo " <li class='disabled'><a href='javascript:(0)'>$lang[Prev]</a> </li>";
                            //pages 
                            if ($max_pages < 7 + ($adjacents * 2)) {   //not enough pages to bother breaking it up
                                $i = 0;
                                for ($counter = 1; $counter <= $max_pages; $counter++) {
                                    if ($i == $start) {
                                        echo " <li class='active'><a href='?$queryString&start=$i&limit=$limit'><b>$counter</b></a> </li>";
                                    } else {
                                        echo "<li><a href='?$queryString&start=$i&limit=$limit'>$counter</a></li> ";
                                    }
                                    $i = $i + $per_page;
                                }
                            } elseif ($max_pages > 5 + ($adjacents * 2)) {    //enough pages to hide some
                                //close to beginning; only hide later pages
                                if (($start / $per_page) < 1 + ($adjacents * 2)) {
                                    $i = 0;
                                    for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                                        if ($i == $start) {
                                            echo " <li class='active'><a href='?$queryString&start=$i&limit=$limit'><b>$counter</b></a></li> ";
                                        } else {
                                            echo "<li> <a href='?$queryString&start=$i&limit=$limit'>$counter</a> </li>";
                                        }
                                        $i = $i + $per_page;
                                    }
                                }
                                //in middle; hide some front and some back
                                elseif ($max_pages - ($adjacents * 2) > ($start / $per_page) && ($start / $per_page) > ($adjacents * 2)) {
                                    echo " <li><a href='?$queryString&start=0&limit=$limit'>1</a></li> ";
                                    echo "<li><a href='?$queryString&start=$per_page&limit=$limit'>2</a></li>";
                                    echo "<li><a href='javascript:(0)'>...</a></li>";

                                    $i = $start;
                                    for ($counter = ($start / $per_page) + 1; $counter < ($start / $per_page) + $adjacents + 2; $counter++) {
                                        if ($i == $start) {
                                            echo " <li class='active'><a href='?$queryString&start=$i&limit=$limit'><b>$counter</b></a></li> ";
                                        } else {
                                            echo " <li><a href='?$queryString&start=$i&limit=$limit'>$counter</a> </li>";
                                        }
                                        $i = $i + $per_page;
                                    }
                                }
                                //close to end; only hide early pages
                                else {
                                    echo "<li> <a href='?$queryString&start=0&limit=$limit'>1</a> </li>";
                                    echo "<li><a href='?$queryString&start=$per_page'>2</a></li>";
                                    echo "<li><a href='javascript:(0)'>...</a></li>";

                                    $i = $start;
                                    for ($counter = ($start / $per_page) + 1; $counter <= $max_pages; $counter++) {
                                        if ($i == $start) {
                                            echo " <li class='active'><a href='?$queryString&start=$i&limit=$limit'><b>$counter</b></a></li> ";
                                        } else {
                                            echo "<li> <a href='?$queryString&start=$i&limit=$limit'>$counter</a></li> ";
                                        }
                                        $i = $i + $per_page;
                                    }
                                }
                            }
                            //next button
                            if (!($start >= $foundnum - $per_page))
                                echo "<li><a href='?$queryString&start=$next&limit=$limit'>$lang[Next]</a></li>";
                            else
                                echo "<li class='disabled'><a href='javascript:(0)'>$lang[Next]</a></li>";
                            ?>
                        </ul>
                        <?php
                    }
                    echo "</center>";
                    ?>
                    <?php
                }else {

                    echo '<div class="form-group no-records-found"><label><strong class="text-danger"><i class="ti-face-sad text-pink"> </i> ' . $lang['Who0ps!_No_Records_Found'] . '</strong></label></div>';
                }
                ?>
                <?php
                //return $out;
            }
            ?>
        </div>

        <div style="display: none; background: rgba(0,0,0,0.5); width: 100%; z-index: 2000; position: fixed; top:0;" id="wait">;

            <img src="assets/images/proceed.gif" alt="load"  style=" margin-left: 48%; margin-top: 250px; width: 100px; height:100px; position: fixed; "/>
        </div>  
        <script>
                                                    //for wait gif display after submit
                                                    var heiht = $(document).height();
                                                    //alert(heiht);
                                                    $('#wait').css('height', heiht);

                                                    $('#wfasign').submit(function () {
                                                        if ($.trim($("#wfid").val()) != "") {
                                                            $('#wait').show();
                                                            //$('#wait').css('height',heiht);
                                                            $('#assign-workflow').hide();
                                                            return true;
                                                        }
                                                    });
        </script>
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
        <!-- for audio model-->
        <div id="modal-audio" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" title="Close">x</button>
                        <h4 class="modal-title" id="myModalLabel">Play/Download Audio</h4>
                    </div>
                    <div id="foraudio">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal" title="Close">Close</button>

                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

        <div id="modal-video" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel">Play/Download video</h4>
                    </div>
                    <div  id="videofor">


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal" title="Close">Close</button>

                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

        <script type="text/javascript">

            $(document).ready(function () {
                $('form').parsley();
            });

            $(document).ready(function () {
                var d = new Date();
                var month = d.getMonth() + 1;
                var day = d.getDate();
                var output = d.getFullYear() + '-' +
                        (('' + month).length < 2 ? '0' : '') + month + '-' +
                        (('' + day).length < 2 ? '0' : '') + day;
                //alert(output);
                $('.datepicker').datepicker({
                    format: "dd-mm-yyyy",
                    //startDate: output,

                });

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
            $("a#moveToWf").click(function () {
                var id = $(this).attr('data');

                // alert(id);
                $("#mTowf").val(id);
            });
        </script>
    </body>
</html>
