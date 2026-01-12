<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    require_once './application/config/database.php';
    require_once './application/pages/function.php';
    require_once './application/pages/head.php';
    require_once './classes/ftp.php';

    //for user role
    $chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

    $rwgetRole = mysqli_fetch_assoc($chekUsr);
    $ses_val = $_SESSION;
    $langDetail = mysqli_query($db_con, "select * from tbl_language where lang_name='$_SESSION[lang]'") or die('Error : ' . mysqli_error($db_con));
    $langDetail = mysqli_fetch_assoc($langDetail);
    if ($rwgetRole['doc_weeding_out'] != 1) {
        header('Location: ./index');
    }

    $slids = findsubfolder($slpermIdes, $db_con);

    $slids = implode(',', $slids);
    ?>
    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />

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
            var control = new google.elements.transliteration.TransliterationControl(options);

            // Enable transliteration in the text fields with the given ids.
            var ids = ["recyclefile"];
            control.makeTransliteratable(ids);
            // Show the transliteration control which can be used to toggle between
            // English and Hindi and also choose other destination language.
            // control.showControl('translControl');
        }
        google.setOnLoadCallback(onLoad);
    </script>   
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
                                        <a href="retention-period-document"><?php echo $lang['Retention_doc_list']; ?></a>
                                    </li>
                                     <li> <a href="#" data-toggle="modal" data-target="#help-modal" id="helpview" data="55" title="<?= $lang['help']; ?>"><i class="fa fa-question-circle"></i> </a></li>
                                    <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                                </ol>
                            </div>
                        </div>
                        <div class="box box-primary">
                            <div class="panel">
                                <div class="panel-body">
                                    <?php if (!empty($slids)) { ?>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <form method="get">
                                                    <div class="col-sm-3">
                                                        <input type="text" class="form-control translatetext" name="recyclefile" id="recyclefile" value="<?php echo xss_clean(trim($_GET['recyclefile'])); ?>" parsley-trigger="change"  placeholder="<?php echo $lang['Search']; ?>" required />
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <button type="submit" class="btn btn-primary"><?php echo $lang['Search']; ?> <i class="fa fa-search"></i> </button>
                                                        <a href="retention-period-document" class="btn btn-warning"> <?php echo $lang['Reset']; ?></a>
                                                    </div>
                                                </form>


                                                 <button class="btn btn-primary" id="export4" data-toggle="modal" data-target="#multi-csv-export-model" title="Export Users"><i class="fa fa-download"></i> Export</button>

                                            </div>
                                        </div>
                                        <?php
                                        $deleteFile="";
                                        $where = "where retention_period IS NOT NULL and flag_multidelete='1' and doc_name in($slids)";
                                        if (isset($_GET['recyclefile']) && !empty($_GET['recyclefile'])) {
                                            $deleteFile = trim($_GET['recyclefile']);
                                            $deleteFile = xss_clean($deleteFile);
                                            $deleteFile  =  mysqli_real_escape_string($db_con, $deleteFile);
                                            $where .= "and old_doc_name like '%$deleteFile%'";
                                        }
                                        $constructs = "SELECT * FROM `tbl_document_master` $where";
                                        mysqli_set_charset($db_con, 'utf8');
                                        $run = mysqli_query($db_con, $constructs) or die('Error' . mysqli_error($db_con));

                                        $foundnum = mysqli_num_rows($run);
                                        if ($foundnum > 0) {
                                            $limit = preg_replace("/[^0-9 ]/", "", $_GET['limit']);
                                            if (is_numeric($limit)) {
                                                $per_page = $limit;
                                            } else {
                                                $per_page = 10;
                                            }
                                            $start = preg_replace("/[^0-9 ]/", "", $_GET['start']);
                                            $start = isset($start) ? ($start > 0) ? $start : 0 : 0;
                                            $max_pages = ceil($foundnum / $per_page);
                                            if (!$start) {
                                                $start = 0;
                                            }
                                            ?>
                                            <div class="container">

                                                <div class="box-body">

                                                    <label><?php echo $lang['Show']; ?></label>
                                                    <select id="limit" class="input-sm">
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
                                                        <label> <?php echo $start + 1 ?> <?php echo $lang['To']; ?> <?php
                                                            if (($start + $per_page) > $foundnum) {
                                                                echo $foundnum;
                                                            } else {
                                                                echo ($start + $per_page);
                                                            }
                                                            ?> <span><?php echo $lang['ttl_recrds']; ?>: <?php echo $foundnum; ?></span></label>
                                                    </div>
                                                </div>
                                                <table class="table table-striped table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th><strong><?php echo $lang['SNO']; ?></strong></th>
                                                            <th><?php echo $lang['File_Name']; ?></th>
                                                            <th><?php echo $lang['weed_out_time']; ?></th>
                                                            <th><?php echo $lang['File_Ext']; ?></th>
                                                            <th><?php echo $lang['Nme_of_Strg']; ?></th>
                                                            <th><?php echo $lang['File_Size']; ?></th>

                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                       
                                                        $recyle = mysqli_query($db_con, "SELECT * FROM `tbl_document_master` $where order by old_doc_name LIMIT $start, $per_page") or die('Error:' . mysqli_error($db_con));
                                                        if (isset($start) && $start != 0) {
                                                            $i = $start + 1;
                                                        } else {
                                                            $i = 1;
                                                        }
                                                        while ($rw_recyle = mysqli_fetch_assoc($recyle)) {
                                                            $getSlName = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id = '$rw_recyle[doc_name]'") or die('Error in get name' . mysqli_error($db_con));
                                                            $rwgetSlName = mysqli_fetch_assoc($getSlName);
                                                            $existfile = mysqli_query($db_con, "select old_doc_name,doc_name from tbl_document_master where old_doc_name='$rw_recyle[old_doc_name]' and doc_name='$rw_recyle[doc_name]' and flag_multidelete='1'");
                                                            $rwexistfile = mysqli_fetch_assoc($existfile);
                                                            $exfile = $rwexistfile['old_doc_name'];
                                                            ?>
                                                            <tr class="gradeX">
                                                                <td>
                                                                    <?php echo $i . '.'; ?>
                                                                </td>

                                                                <td style="width:450px;">
                                                                    <?php
                                                                    //@sk(221118): include view handler to handle different file formats
                                                                    echo $rw_recyle['old_doc_name'];
                                                                    if ($rw_recyle['checkin_checkout'] == '1') {
                                                                        $file_row = $rw_recyle;
                                                                        require 'view-handler.php';
                                                                    } else {
                                                                        echo ' <i class="fa fa-eye-slash" data-toggle="tooltip" title="' . $lang['Checkout'] . ' ' . $lang['files'] . '"></i>';
                                                                    }
                                                                    ?>
                                                                </td>

                                                                <td><?php echo date('d-m-Y H:i:s', strtotime($rw_recyle['retention_period'])); ?></td>
                                                                <td><?php echo strtolower($rw_recyle['doc_extn']); ?></td>
                                                                <td><?php echo $rwgetSlName['sl_name']; ?></td>
                                                                <td><?php echo round($rw_recyle['doc_size'] / (1000 * 1000), 2); ?> MB</td>


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
                                                            echo " <li><a href='?start=$prev&limit=$per_page&recyclefile=".$deleteFile."'>$lang[Prev]</a> </li>";
                                                        else
                                                            echo " <li class='disabled'><a href='javascript:void(0)'>$lang[Prev]</a> </li>";
                                                        //pages 
                                                        if ($max_pages < 7 + ($adjacents * 2)) {   //not enough pages to bother breaking it up
                                                            $i = 0;
                                                            for ($counter = 1; $counter <= $max_pages; $counter++) {
                                                                if ($i == $start) {
                                                                    echo " <li class='active'><a href='?start=$i&limit=$per_page&recyclefile=".$deleteFile."'><b>$counter</b></a> </li>";
                                                                } else {
                                                                    echo "<li><a href='?start=$i&limit=$per_page&recyclefile=".$deleteFile."'>$counter</a></li> ";
                                                                }
                                                                $i = $i + $per_page;
                                                            }
                                                        } elseif ($max_pages > 5 + ($adjacents * 2)) {    //enough pages to hide some
                                                            //close to beginning; only hide later pages
                                                            if (($start / $per_page) < 1 + ($adjacents * 2)) {
                                                                $i = 0;
                                                                for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                                                                    if ($i == $start) {
                                                                        echo " <li class='active'><a href='?start=$i&limit=$per_page&recyclefile=".$deleteFile."'><b>$counter</b></a></li> ";
                                                                    } else {
                                                                        echo "<li> <a href='?start=$i&limit=$per_page&recyclefile=".$deleteFile."'>$counter</a> </li>";
                                                                    }
                                                                    $i = $i + $per_page;
                                                                }
                                                            }
                                                            //in middle; hide some front and some back
                                                            elseif ($max_pages - ($adjacents * 2) > ($start / $per_page) && ($start / $per_page) > ($adjacents * 2)) {
                                                                echo " <li><a href='?start=0&limit=$per_page&recyclefile=".$deleteFile."'>1</a></li> ";
                                                                echo "<li><a href='?start=$per_page&limit=$per_page&recyclefile=".$deleteFile."'>2</a></li>";
                                                                echo "<li><a href='javascript:void(0)'>...</a></li>";

                                                                $i = $start;
                                                                for ($counter = ($start / $per_page) + 1; $counter < ($start / $per_page) + $adjacents + 2; $counter++) {
                                                                    if ($i == $start) {
                                                                        echo " <li class='active'><a href='?start=$i&limit=$per_page&recyclefile=".$deleteFile."'><b>$counter</b></a></li> ";
                                                                    } else {
                                                                        echo " <li><a href='?start=$i&limit=$per_page&recyclefile=".$deleteFile."'>$counter</a> </li>";
                                                                    }
                                                                    $i = $i + $per_page;
                                                                }
                                                            }
                                                            //close to end; only hide early pages
                                                            else {
                                                                echo "<li> <a href='?start=0&limit=$per_page&recyclefile=".$deleteFile."'>1</a> </li>";
                                                                echo "<li><a href='?start=$per_page&limit=$per_page&recyclefile=".$deleteFile."'>2</a></li>";
                                                                echo "<li><a href='javascript:void(0)'>...</a></li>";

                                                                $i = $start;
                                                                for ($counter = ($start / $per_page) + 1; $counter <= $max_pages; $counter++) {
                                                                    if ($i == $start) {
                                                                        echo " <li class='active'><a href='?start=$i&limit=$per_page&recyclefile=".$deleteFile."'><b>$counter</b></a></li> ";
                                                                    } else {
                                                                        echo "<li> <a href='?start=$i&limit=$per_page&recyclefile=".$deleteFile."'>$counter</a></li> ";
                                                                    }
                                                                    $i = $i + $per_page;
                                                                }
                                                            }
                                                        }
                                                        //next button
                                                        if (!($start >= $foundnum - $per_page))
                                                            echo "<li><a href='?start=$next&limit=$per_page&recyclefile=".$deleteFile."'>$lang[Next]</a></li>";
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
                                                            <th><?php echo $lang['File_Name']; ?></th>
                                                            <th><?php echo $lang['File_Ext']; ?></th>
                                                            <th><?php echo $lang['Nme_of_Strg']; ?></th>
                                                            <th><?php echo $lang['File_Size']; ?></th>
                                                            <th><?php echo $lang['Actions']; ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tr>
                                                        <td colspan="6" class="text-center"><strong class="text-danger"> <?php echo $lang['Who0ps!_No_Records_Found']; ?></strong></td>
                                                    </tr>
                                                </table>

                                                <?php
                                            }
                                        } else {
                                            ?>
                                            <table class="table table-striped table-bordered m-t-15">
                                                <thead>
                                                    <tr>
                                                        <th><?php echo $lang['Sr_No']; ?></th>
                                                        <th><?php echo $lang['File_Name']; ?></th>
                                                        <th><?php echo $lang['File_Ext']; ?></th>
                                                        <th><?php echo $lang['Nme_of_Strg']; ?></th>
                                                        <th><?php echo $lang['File_Size']; ?></th>
                                                        <th><?php echo $lang['Actions']; ?></th>
                                                    </tr>
                                                </thead>
                                                <tr>
                                                    <td colspan="6" class="text-center"><strong class="text-danger"> <?php echo $lang['storage_permission']; ?></strong></td>
                                                </tr>
                                            </table>

                                        <?php }
                                        ?>
                                    </div>
                                    <!-- end: page -->
                                </div>
                            </div> <!-- end Panel -->
                        </div> <!-- container -->

                    </div> <!-- content -->

                    <?php require_once './application/pages/footer.php'; ?>                
                </div>
                <!-- Right Sidebar -->
                <?php //require_once './application/pages/rightSidebar.php';       ?>
            </div>

            <!-- END wrapper -->
            <?php require_once './application/pages/footerForjs.php'; ?>


            <div id="multi-csv-export-model" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog"> 
                    <div class="panel panel-color panel-primary"> 
                        <div class="panel-heading"> 
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                            <label><h2 class="panel-title"><?php echo $lang['export_retention_document']; ?></h2></label> 
                        </div> 
                        <form action="export-retention-document"  method="post">
                            <div class="panel-body">
                                <div class="col-md-5  m-t-10">
                                    <strong class="text-primary"><?php echo $lang['Select_Files_for_Export_Format']; ?> : </strong>
                                </div>
                                <div class="col-md-3">
                                    <select class="select2 input-sm" id="my_multi_select1" name="select_Fm">

                                        <option value="xlsx"><?php echo $lang['Excel']; ?></option>
                                        <!--  <option value="excel">Excel</option>-->
                                        <option value="pdf"><?php echo $lang['Pdf']; ?></option>
                                        <option value="csv"><?php echo $lang['Csv']; ?></option>
                                        <option value="word"><?php echo $lang['Word']; ?></option>
                                    </select>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <input type="hidden" value="<?php echo $slids; ?>" name="slids">
                                <input type="hidden" value="<?php echo $_GET['recyclefile']; ?>" name="recyclefile">
                                <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                                <button class="btn btn-primary waves-effect waves-light" type="submit" name="exportUser"><i class="fa fa-download"></i> <?php echo $lang['Export']; ?></button>
                            </div>
                        </form>

                    </div> 
                </div>
            </div>


            <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>
            <script>
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

            </script>
       
         
          
    </body>

</html>
