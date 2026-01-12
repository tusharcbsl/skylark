<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    require_once './application/config/database.php';
    require_once './application/pages/function.php';
    require_once './application/pages/head.php';
    require_once './classes/fileManager.php';
	$fileManager = new fileManager();
    //for user role
    $chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));
    $rwgetRole = mysqli_fetch_assoc($chekUsr);
    $ses_val = $_SESSION;
    $langDetail = mysqli_query($db_con, "select * from tbl_language where lang_name='$_SESSION[lang]'") or die('Error : ' . mysqli_error($db_con));
    $langDetail = mysqli_fetch_assoc($langDetail);
    if ($rwgetRole['view_recycle_bin'] != 1) {
        header('Location: ./index');
    }

    $perm = mysqli_query($db_con, "select sl_id from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]' group by sl_id");
    $slperms = array();
    while ($rwPerm = mysqli_fetch_assoc($perm)) {
        $slperms[] = $rwPerm['sl_id'];
    }

    $sl_perm = implode(',', $slperms);
    $slids = findsubfolder($sl_perm, $db_con);

    $slids = implode(',', $slids);
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
                                        <a href="recycle"><?php echo $lang['Recycle_Bin']; ?></a>
                                    </li>
                                     <li> <a href="#" data-toggle="modal" data-target="#help-modal" id="helpview" data="33" title="<?= $lang['help']; ?>"><i class="fa fa-question-circle"></i> </a></li>
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
                                                        <input type="text" class="form-control translatetext" name="recyclefile" id="recyclefile" value="<?php echo xss_clean(trim($_GET['recyclefile'])); ?>" parsley-trigger="change"  placeholder="<?php echo $lang['search_deleted_files']; ?>" required />
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <button type="submit" class="btn btn-primary"><?php echo $lang['Search']; ?> <i class="fa fa-search"></i> </button>
                                                        <a href="recycle" class="btn btn-warning"> <?php echo $lang['Reset']; ?></a>
                                                    </div>
                                                    <?php if ($rwgetRole['view_recycle_storage'] == 1) { ?>
                                                    <div class="col-sm-3">
                                                        <a href="recycle-folder" class="btn btn-primary"><i class="fa fa-folder"></i> Recycle Folder</a>
                                                    </div>
                                                    <?php } ?>
                                                </form>

                                            </div>
                                        </div>
                                        <?php
                                        $deleteFile ="";
                                       $where = "WHERE flag_multidelete='0' and doc_name in($slids) and action_name='Document recycle'";
                                        if (isset($_GET['recyclefile']) && !empty($_GET['recyclefile'])) {
                                            $deleteFile = trim($_GET['recyclefile']);
                                            $deleteFile = xss_clean($deleteFile);
                                            $deleteFile = mysqli_real_escape_string($db_con, $deleteFile);
                                            
                                            $where .= "and old_doc_name like '%$deleteFile%'";
                                        }
                                       $constructs = "SELECT d.doc_id, d.flag_multidelete FROM tbl_document_master as d left join tbl_ezeefile_logs as e on d.doc_id=e.doc_id $where";
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
                                                <table class="table table-striped table-bordered js-sort-table">
                                                    <thead>
                                                        <tr>
                                                            <th class="js-sort-none"><div class="checkbox checkbox-primary"><input  type="checkbox" class="checkbox-primary" id="select_all"> <label for="checkbox6"> <strong><?php echo $lang['All']; ?></strong></label></div></th>
                                                            <th class="js-sort-none" ><?php echo $lang['File_Name']; ?></th>
                                                            <th class="js-sort-none" ><?php echo $lang['File_Ext']; ?></th>
                                                            <th class="js-sort-none" ><?php echo $lang['Nme_of_Strg']; ?></th>
                                                            <th class="js-sort-none" ><?php echo $lang['File_Size']; ?></th>
															<th class="js-sort-date"> <?php echo $lang['doc_deleted_date']; ?> <i class="fa fa-sort"></i></th>
                                                            <th class="js-sort-none"> <?php echo $lang['deleted_by']; ?></th>
                                                            <th class="js-sort-none" ><?php echo $lang['Actions']; ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                      
                                                        mysqli_set_charset($db_con, "utf8");
                                                        $recyle = mysqli_query($db_con, "SELECT e.start_date, e.user_name, d.* FROM `tbl_document_master` as d left join tbl_ezeefile_logs as e on d.doc_id=e.doc_id $where order by e.start_date desc LIMIT $start, $per_page") or die('Error:' . mysqli_error($db_con));
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
															
															
															
															/* $deletedDate = mysqli_query($db_con, "select start_date,user_name from tbl_ezeefile_logs where doc_id='" . $rw_recyle['doc_id'] . "' and (action_name='Document recycle' or action_name like'%recycle%' or action_name like'%Document Deleted%') ORDER BY id DESC LIMIT 1"); */
                                                            $rwdeldDatetime = mysqli_fetch_assoc($deletedDate); 
                                                            $deleteby = $rw_recyle['user_name'];
                                                            $deldatetime = $rw_recyle['start_date'];
                                                            $docdeldatetime = date('m-d-Y h:i A', strtotime($deldatetime));
															
                                                            ?>
                                                            <tr class="gradeX">
                                                                <td>
                                                                    <div class="checkbox checkbox-primary"><input type="checkbox" class="checkbox-primary emp_checkbox" data-doc-id="<?php echo $rw_recyle['doc_id']; ?>"><label for="checkbox6">  <?php echo $i . '.'; ?> </label></div>
                                                                </td>

                                                                <td style="width:450px;">
                                                                    <?php
                                                                    //@sk(221118): include view handler to handle different file formats
                                                                    echo $rw_recyle['old_doc_name'];
                                                                    if ($rw_recyle['checkin_checkout'] == '1') {
                                                                        $file_row = $rw_recyle;
                                                                        require 'view-handler.php';
                                                                    } else {
                                                                        echo ' <i class="fa fa-eye-slash" title="' . $lang['Checkout'] . ' ' . $lang['files'] . '"></i>';
                                                                    }
                                                                    ?>
                                                                </td>

                                                                <td><?php echo strtolower($rw_recyle['doc_extn']); ?></td>
                                                                <td><?php echo $rwgetSlName['sl_name']; ?></td>
                                                                <td><?php echo formatSizeUnits($rw_recyle['doc_size']); ?></td>
																  <td><?php echo (!empty($docdeldatetime) ? $docdeldatetime : "--"); ?> </td>
                                                                <td><?php echo (!empty($deleteby) ? $deleteby : "--"); ?> </td>
                                                                <td>
                                                                    <?php if ($rwgetRole['restore_file'] == '1') { ?>
                                                                        <button class="btn btn-primary" data-toggle="modal" data-target="#recycle" id="recycleRow" data="<?php echo $rw_recyle['doc_id']; ?>"><i class="fa fa-recycle" aria-hidden="true" title="<?php echo $lang['Restore_File']; ?>"></i></button>
                                                                    <?php }if ($rwgetRole['permanent_del'] == '1') { ?>
                                                                        <button class="btn btn-danger" data-toggle="modal" data-target="#delrecycle" id="delrestore" data="<?php echo $rw_recyle['doc_id']; ?>"><i class="fa fa-trash-o" aria-hidden="true" title="<?php echo $lang['Permanent_Delete_File']; ?>"></i> </button>
                                                                    <?php } if ($rwgetRole['rename_file'] == '1' && ($exfile == $rw_recyle['old_doc_name'])) { ?>
                                                                        <button class="btn btn-primary" data-toggle="modal" data-target="#changeFileame" id="renamefile" data="<?php echo $rw_recyle['doc_id']; ?>"><i class="fa fa-edit" aria-hidden="true" title="<?php echo $lang['edit_File_name']; ?>"></i> </button>
                                                                    <?php } ?>
                                                                </td>

                                                            </tr>
                                                            <?php
                                                            $i++;
                                                        }
                                                        ?>
                                                    </tbody>
                                                </table>
												
												<ul class="delete_export" style="margin-left:-40px;">
													<input type="hidden" name="slid" id="slid_recyle" value="<?php echo $slid; ?>">
													<input type="hidden" name="sty" id="sty_recyle" value="<?php echo preg_replace("/[^0-9 ]/", "", $_GET['id']); ?>">
													<?php if ($rwgetRole['permanent_del'] == '1') { ?>
														<li><button id="del_file_recylebin" class="rows_selected btn btn-danger" data-toggle="modal" data-target="#del_send_to_recycle"><i class=" fa fa-trash-o"></i> <?php echo $lang['Del_fles']; ?></button></li>
													<?php } if ($rwgetRole['restore_file'] == '1') { ?>
														<li><button class="btn btn-primary" id="multi_restore_files"  data-toggle="modal" data-target="#multi_recycle"><i class="fa fa-recycle"></i> <?php echo $lang['Restore_Files']; ?></button></li>
													<?php } ?>
												</ul>
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
															 <th> <?php echo $lang['doc_deleted_date']; ?> <i class="fa fa-sort"></i></th>
                                                            <th class="js-sort-none"> <?php echo $lang['deleted_by']; ?></th>
                                                            <th><?php echo $lang['Actions']; ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tr>
                                                        <td colspan="8" class="text-center"><strong class="text-danger"> <?php echo $lang['Who0ps!_No_Records_Found']; ?></strong></td>
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
														 <th> <?php echo $lang['doc_deleted_date']; ?> <i class="fa fa-sort"></i></th>
                                                            <th class="js-sort-none"> <?php echo $lang['deleted_by']; ?></th>
                                                        <th><?php echo $lang['Actions']; ?></th>
                                                    </tr>
                                                </thead>
                                                <tr>
                                                    <td colspan="8" class="text-center"><strong class="text-danger"> <?php echo $lang['storage_permission']; ?></strong></td>
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
            <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>

            <script type="text/javascript" src="assets/multi_function_script.js"></script>

            <script>
                                        $("button#recycleRow").click(function () {
                                            var id = $(this).attr('data');
                                            // alert(id);
                                            $.post("application/ajax/recycle-file.php", {ID: id}, function (result, status) {
                                                if (status == 'success') {
                                                    $("#restore").html(result);
                                                    //alert(result);
                                                }
                                            });
                                        });

                                        $("button#delrestore").click(function () {
                                            var id = $(this).attr('data');
                                            $("#reDel").val(id);
                                        });

                                        $("button#renamefile").click(function () {
                                            var id = $(this).attr('data');
                                            var token = $("input[name='token']").val();
                                            $.post("application/ajax/rename-recycle-file.php", {FID: id, token:token}, function (result, status) {
                                                if (status == 'success') {
                                                    getToken();
                                                    $("#refile").html(result);
                                                    //alert(result);
                                                }
                                            });
                                        });
            </script>

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
            <script>

                function recyleFileSearch() {
                    var input, filter, table, tr, td, i;
                    input = document.getElementById("SearchInput");
                    filter = input.value;
                    table = document.getElementById("recyleTable");
                    tr = table.getElementsByTagName("tr");
                    for (i = 0; i < tr.length; i++) {
                        td = tr[i].getElementsByTagName("td")[0];
                        if (td) {
                            if (td.innerHTML.indexOf(filter) > -1) {
                                tr[i].style.display = "";
                            } else {
                                tr[i].style.display = "none";
                            }
                        }
                    }
                }
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
                    var ids = ["recyclefile"];
                    control.makeTransliteratable(ids);


                    // Show the transliteration control which can be used to toggle between
                    // English and Hindi and also choose other destination language.
                    // control.showControl('translControl');

                }
                google.setOnLoadCallback(onLoad);

            </script>
            <div id="recycle" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog "> 
                    <div class="panel panel-primary panel-color"> 

                        <div class="panel-heading"> 
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                            <h2 class="panel-title"><?php echo $lang['r_u_Sure_Want_to_Restore_This_Document']; ?></h2> 
                        </div>
                        <form method="post">
                            <div class="modal-body" id="restore">

                            </div> 
                            <div class="modal-footer">

                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                                <button type="submit" name="Restore" class="btn btn-primary"><i class="fa fa-recycle" aria-hidden="true"></i> <?php echo $lang['Restore']; ?></button> 
                            </div>
                        </form>

                    </div> 
                </div>
            </div>
            <!-- /.modal for multi delete from Recycle-->

            <div id="del_send_to_recycle" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog"> 
                    <div class="panel panel-color panel-danger"> 

                        <div class="panel-heading"> 
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h2 class="panel-title" style="display:none;" id="hid"> <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <?php echo $lang['Hre_msge']; ?></h2>
                            <h2 class="panel-title" id="recycle_confirm"> <?php echo $lang['Are_u_confirm']; ?> </h2> 
                        </div>
                        <form method="post">
                            <div class="panel-body">
                                <span id="recycle_errmessage" style="display:none;"> <h5 class="text-alert"><?php echo $lang['Pls_sl_fls_fr_Dl']; ?></h5></span>
                                <label id="recycle_hide"><p class="text-alert"><?php echo $lang['r_u_sure_want_to_Dlt_thse_Doc_Pr']; ?>?</p></label>
                            </div> 
                            <div class="modal-footer">
                                <input type="hidden" id="reDel_recyle" name="reDel">
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"> <?php echo $lang['Close']; ?></button>
                                <button type="submit"  name="Delmultiple" class="btn btn-danger" id="mulDel"><i class="fa fa-trash-o"></i> <?php echo $lang['Delete']; ?></button>
                                </button> 
                            </div>
                        </form>

                    </div> 
                </div>
            </div>
            <div id="multi_recycle" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog"> 
                    <div class="panel panel-color panel-danger"> 

                        <div class="panel-heading"> 
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h2 class="panel-title" id="titlehid"> <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <?php echo $lang['Hre_msge']; ?></h2>
                            <h2 class="panel-title" id="multi_restore_confirm"> <?php echo $lang['Are_u_confirm']; ?>  </h2> 
                        </div>
                        <form method="post" >
                            <div class="panel-body">
                                <span id="multi_restore_errmessage" style="display:none;"> <p class="text-alert"><?php echo $lang['Pls_sel_files_for_Restore']; ?></p></span>
                                <label id="multi_restore_hide"><p class="text-danger"><?php echo $lang['Are_you_sure_want_to_Rstre_selcted_fles']; ?></p></label>
                            </div> 
                            <div class="modal-footer">
                                <input type="hidden" id="reDel_multi_restore" name="reDel" value="">
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                                <button type="submit"  name="multi_Restore" class="btn btn-primary" id="hiddel"><i class="fa fa-recycle"></i> <?php echo $lang['Restore']; ?></button>
                                </button> 
                            </div>
                        </form>

                    </div> 
                </div>
            </div>

            <div id="delrecycle" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog"> 
                    <div class="panel panel-color panel-danger"> 

                        <div class="panel-heading"> 
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                            <h2 class="panel-title"><?php echo $lang['Are_u_confirm']; ?> </h2> 
                        </div>
                        <form method="post">
                            <div class="panel-body" >
                                <label class="text-danger"><?php echo $lang['r_u_sue_wnt_to_Del_tis_Docs']; ?></label>
                            </div> 
                            <div class="modal-footer">
                                <input type="hidden" id="reDel" name="DelFile">
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                                <button type="submit" name="DelRstr" class="btn btn-danger"> <i class="fa fa-trash-o"></i> <?php echo $lang['Delete']; ?></button> 
                            </div>
                        </form>

                    </div> 
                </div>
            </div><!-- /.modal -->
            <div id="changeFileame" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog"> 
                    <div class="panel panel-color panel-primary"> 

                        <div class="panel-heading"> 
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                            <h2 class="panel-title"><?php echo $lang['Are_u_confirm']; ?> </h2> 
                        </div>
                        <form method="post">
                            <div class="panel-body" id="refile">

                            </div> 
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                                <button type="submit" name="rename" class="btn btn-primary"> <?php echo $lang['Submit']; ?></button> 
                            </div>
                        </form>

                    </div> 
                </div>
            </div><!-- /.modal -->
    </body>

</html>
<?php
if (isset($_POST['Delmultiple'], $_POST['token'])) {
    $permission = trim($_POST['Delmultiple']);
    $docDelete = trim($_POST['reDel']);
    $user_id4 = $_SESSION['cdes_user_id'];

    $chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um where FIND_IN_SET('$user_id4', user_ids) > 0"); // or die('Error:' . mysqli_error($db_con));
    $rwcheckUser = mysqli_fetch_assoc($chekUsr);
    $filePath = array();
    $pathtxt = array();
    $filename = array();
    mysqli_set_charset($db_con, 'utf8');
    $getDocPath = mysqli_query($db_con, "select doc_path,old_doc_name,doc_name, doc_id from tbl_document_master where doc_id in($docDelete)"); //or die('Error:' . mysqli_error($db_con));
    while($rwgetDocPath = mysqli_fetch_assoc($getDocPath)){
		
		$filePath[] = $rwgetDocPath['doc_path'];
		$path = substr($rwgetDocPath['doc_path'], 0, strrpos($rwgetDocPath['doc_path'], '/') + 1);
		//$pathtxt[] = 'extract-here/' . $path;
		$pathtxt[] = 'extract-here/' . $path . 'TXT/' . $rwgetDocPath['doc_id'] . '.txt';
		$filename[] = $rwgetDocPath['old_doc_name'];
		$storgId = $rwgetDocPath['doc_name'];
	}

    $del = mysqli_query($db_con, "DELETE FROM tbl_document_master WHERE doc_id in($docDelete)"); //or die('Error:' . mysqli_error($db_con));
    if ($del) {
        $delDocShare = mysqli_query($db_con, "DELETE FROM tbl_document_share WHERE doc_ids in($docDelete)"); //or die('Error:' . mysqli_error($db_con));
    }
	$fileManager->conntFileServer();
    for ($i = 0; $i < count($filePath[$i]); $i++) {
        /* $pathtxt = 'extract-here/' . $path . 'TXT/' . $delDocs[$i] . '.txt';
        $file_dir = 'extract-here/' . $path . 'TXT/';
        $path = 'extract-here/' . $filePath[$i];
        $ftppath = explode('/', $filePath[$i]); */
		
		//connect file server
		if(!file_exists('extract-here/' . $filePath[$i])){
			if($fileManager->deleteFile(ROOT_FTP_FOLDER . '/' . $filePath[$i])){ // delete file from file server
				
				if(file_exists($pathtxt[$i])){
					unlink($pathtxt[$i]);
				}
				 
			}
		}else{
			unlink('extract-here/' . $filePath[$i]);
		}
    }
    if ($del) {
        foreach ($filename as $filenames) {
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Storage Document $filenames Deleted From Recyle-bin','$date',null,'$host',null)"); // or die('error : ' . mysqli_error($db_con));
        }

        echo'<script>taskSuccess("recycle","' . $lang['Doc_Dltd_Sucesfly'] . '");</script>';
    } else {
        echo'<script>taskFailed("recycle","' . $lang['Doc_Nt_Dltd'] . '");</script>';
    }

    mysqli_close($db_con);
}
//Restore Again
if (isset($_POST['Restore'], $_POST['token']) && intval($_POST['DocId'])) {
    $DocId = filter_input(INPUT_POST, "DocId");
    $DocName = mysqli_query($db_con, "SELECT old_doc_name,doc_name FROM `tbl_document_master` WHERE doc_id = '$DocId'"); // or die('Error in old name' . mysqli_error($db_con));
    $rwDocName = mysqli_fetch_assoc($DocName); // or die('Error old doc name' . mysqli_error($db_con));
    $strgId = $rwDocName['doc_name'];
    $docname = $rwDocName['old_doc_name'];
    $duplicatefile = mysqli_query($db_con, "select * from tbl_document_master where doc_name='$strgId' and old_doc_name='$docname' and flag_multidelete='1'");
    if (mysqli_num_rows($duplicatefile) < 1) {
        $RestoreFile = ("UPDATE `tbl_document_master` SET flag_multidelete=1 WHERE doc_id = '$DocId'"); // or die('Error in update database' . mysqli_error($db_con));
        $rwRestoreFile = mysqli_query($db_con, $RestoreFile); // or die('Error faq' . mysqli_error($db_con));
        if ($rwRestoreFile) {
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`, `action_name`, `start_date`, `system_ip`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Document $rwDocName[old_doc_name] restored.','$date','$host')"); // or die('error : ' . mysqli_error($db_con));
            echo'<script>taskSuccess("recycle", "' . $lang['doc_Restore_Success'] . '");</script>';
        } else {
            echo'<script>taskFailed("recycle", "' . $lang['document_Restore_Failed'] . '");</script>';
        }
    } else {
        echo'<script>taskFailed("recycle", "' . $lang['uploaded_already'] . '");</script>';
    }
    mysqli_close($db_con);
}
//Delete recycle bin files
if (isset($_POST['DelRstr'], $_POST['token']) && intval($_POST['DelFile'])) {
    $reDel = $_POST['DelFile'];
    mysqli_set_charset($db_con, 'utf8');
    $DelRestore = mysqli_query($db_con, "SELECT * FROM `tbl_document_master` WHERE doc_id = '$reDel'"); //or die('Error in delete file' . mysqli_error($db_con));
    $rwDelRestore = mysqli_fetch_assoc($DelRestore); // or die('Error file del fetch' . mysqli_error($db_con));
    $delfrmShre = mysqli_query($db_con, "DELETE FROM `tbl_document_share` WHERE doc_ids='$reDel'"); // or die("Error in del" . mysqli_error($db_con));
    $delrecycle = "DELETE FROM `tbl_document_master` WHERE doc_id = '$reDel'";
    $Rwdelrecycle = mysqli_query($db_con, $delrecycle); // or die('Error file del' . mysqli_error($db_con));

    if ($Rwdelrecycle) {
		
		//connect file server
		$fileManager->conntFileServer();
		if($fileManager->deleteFile(ROOT_FTP_FOLDER . '/' . $rwDelRestore['doc_path'])){ // delete file from file server
			$path = substr($rwgetDocPath['doc_path'], 0, strrpos($rwDelRestore['doc_path'], '/') + 1);
			
			$pathtxt = 'extract-here/' . $path . 'TXT/' . $reDel . '.txt';
			if(file_exists($pathtxt)){
				unlink($pathtxt);
			} 
		}
		
		
        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Document $rwDelRestore[old_doc_name]; Deleted from Recycle Bin','$date',null,'$host',null)"); // or die('error : ' . mysqli_error($db_con));
        echo'<script>taskSuccess("recycle", "' . $lang['File_dlt_Success'] . '");</script>';
    } else {
        echo'<script>taskFailed("recycle", "' . $lang['Failed_to_Delete'] . '");</script>';
    }
    mysqli_close($db_con);
}
if (isset($_POST['multi_Restore'], $_POST['token'])) {
    $DocIds = filter_input(INPUT_POST, "reDel");
    mysqli_set_charset($db_con, 'utf8');
    $DocName = mysqli_query($db_con, "SELECT old_doc_name,doc_id,doc_name FROM `tbl_document_master` WHERE doc_id in($DocIds)"); // or die('Error in old name' . mysqli_error($db_con));
    $successFlag = array();
    while ($rwDocName = mysqli_fetch_assoc($DocName)) {
        //echo "select * from tbl_document_master where doc_name='$rwDocName[doc_name]' and old_doc_name='$rwDocName[old_doc_name]' and flag_multidelete=1"; die;
        $checkDub = mysqli_query($db_con, "select * from tbl_document_master where doc_name='$rwDocName[doc_name]' and old_doc_name='$rwDocName[old_doc_name]' and flag_multidelete=1");
        if (mysqli_num_rows($checkDub) <= 0) {
            mysqli_set_charset($db_con, 'utf8');
            $RestoreFile = mysqli_query($db_con, "UPDATE `tbl_document_master` SET flag_multidelete=1 WHERE doc_id='$rwDocName[doc_id]'"); // or die('Error in update database' . mysqli_error($db_con));
            if ($RestoreFile) {
                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Document $rwDocName[old_doc_name]; Recycle','$date',null,'$host',null)"); // or die('error : ' . mysqli_error($db_con));
                if ($log) {
                    $successFlag[] = "success";
                }
            }
        } else {
            $flag = 1;
        }
    }
    if (count($successFlag) > 0) {
        echo'<script>taskSuccess("recycle", "' . $lang['doc_Restore_Success'] . '");</script>';
    } else if ($flag == 1) {
        echo'<script>taskFailed("recycle", "' . $lang['uploaded_already'] . '");</script>';
    } else {
        echo'<script>taskFailed("recycle", "' . $lang['document_Restore_Failed'] . '");</script>';
    }

    mysqli_close($db_con);
}
if (isset($_POST['rename'], $_POST['token'])) {
    mysqli_set_charset($db_con, 'utf8');
    
    $filename = mysqli_real_escape_string($db_con, $_POST['filename']);
    $docId = mysqli_real_escape_string($db_con, $_POST['docId']);
    $slId = mysqli_real_escape_string($db_con, $_POST['slId']);
    $oldname = mysqli_query($db_con, "select old_doc_name, doc_extn from tbl_document_master where doc_id ='$docId'");
    $rwoldname = mysqli_fetch_assoc($oldname);
    $filename = $filename.'.'.$rwoldname['doc_extn'];
    $rename = mysqli_query($db_con, "update tbl_document_master set old_doc_name='$filename' where doc_id ='$docId'");
    if ($rename) {
        mysqli_set_charset($db_con, 'utf8');
        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`, `action_name`, `start_date`, `system_ip`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Document name $rwoldname[old_doc_name] to $filename renamed for restored','$date','$host')"); // or die('error : ' . mysqli_error($db_con));
        echo'<script>taskSuccess("recycle", "' . $lang['file_rename_success'] . '");</script>';
    } else {
        echo'<script>taskFailed("recycle", "' . $lang['file_rename_failed'] . '");</script>';
    }

    mysqli_close($db_con);
}
?>