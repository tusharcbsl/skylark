<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    require_once './application/config/database.php';
    require_once './application/pages/function.php';
    require_once './application/pages/head.php';
	require_once './classes/fileManager.php';
	$fileManager = new fileManager();
    
    if ($rwgetRole['view_recycle_storage'] != 1) {
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

    $folderId = array();

    $folders = mysqli_query($db_con, "SELECT sl_name, sl_id FROM tbl_storage_level where delete_status=1");

    while($rowf = mysqli_fetch_assoc($folders)){

        $folderId[] =$rowf['sl_id']; 

    }

    $folderIds = "";
    if(count($folderId)>0){
        $folderIds = implode(",", $folderId);
    }

    $numFile = 0;
    $totalFSize = 0;
    $totalFolder = 0;
    $noofpages = 0;

    $perm = mysqli_query($db_con, "select * from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]' group by sl_id");
    $rwPerm = mysqli_fetch_assoc($perm);
    $slperm = $rwPerm['sl_id'];
    mysqli_set_charset($db_con, "utf8");
    $sllevel = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slperm' and delete_status=0");
    $rwSllevel = mysqli_fetch_assoc($sllevel);
    $level = $rwSllevel['sl_depth_level'];
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
                                                        <input type="text" class="form-control translatetext" name="storagename" id="recyclefile" value="<?php echo xss_clean(trim($_GET['storagename'])); ?>" parsley-trigger="change"  placeholder="<?php echo $lang['search_storage']; ?>" required />
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <button type="submit" class="btn btn-primary"><?php echo $lang['Search']; ?> <i class="fa fa-search"></i> </button>
                                                        <a href="recycle-folder" class="btn btn-warning"> <?php echo $lang['Reset']; ?></a>
                                                    </div>
                                                </form>

                                            </div>
                                        </div>
                                        <?php
                                        $deleteFile ="";
                                        $where = "WHERE delete_status='1' and action_name='Storage deleted'";

                                        if($folderIds!=""){
                                            $where .= " and sl_parent_id NOT IN ($folderIds)";
                                        }
                                        
                                        if (isset($_GET['storagename']) && !empty($_GET['storagename'])) {
                                            $storagename = trim($_GET['storagename']);
                                            $storagename = xss_clean($storagename);
                                            $storagename = mysqli_real_escape_string($db_con, $storagename);
                                            
                                            $where .= "and sl_name like '%$storagename%'";
                                        }
                                       $constructs = "SELECT s.sl_name, s.sl_id FROM tbl_storage_level as s left join tbl_ezeefile_logs e on s.sl_id=e.sl_id $where";
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
                                                            <th class="js-sort-none" ><div class="checkbox checkbox-primary"><input  type="checkbox" class="checkbox-primary" id="select_all"> <label for="checkbox6"> <strong><?php echo $lang['All']; ?></strong></label></div></th>
                                                            <th class="js-sort-none" ><?php echo $lang['Nme_of_Strg']; ?></th>
                                                            <th class="js-sort-none" ><?php echo $lang['Storage_Path']; ?></th>
                                                            <th class="js-sort-none" ><?php echo $lang['Total_size']; ?></th>
															<th class="js-sort-date" > <?php echo $lang['doc_deleted_date']; ?> </th>
															<th class="js-sort-none" > <?php echo $lang['deleted_by']; ?></th>
                                                            <th class="js-sort-none" ><?php echo $lang['Actions']; ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                      
                                                        mysqli_set_charset($db_con, "utf8");
                                                        $recyle = mysqli_query($db_con, "SELECT e.start_date, e.user_name, s.* FROM `tbl_storage_level` as s left join tbl_ezeefile_logs e on s.sl_id=e.sl_id $where order by e.start_date desc LIMIT $start, $per_page") or die('Error:' . mysqli_error($db_con));
                                                        if (isset($start) && $start != 0) {
                                                            $i = $start + 1;
                                                        } else {
                                                            $i = 1;
                                                        }
                                                        while ($rw_recyle = mysqli_fetch_assoc($recyle)) {

                                                           $totalFiles =  findTotalFile($rw_recyle['sl_id']);

                                                           $sl_name = $rw_recyle['sl_name'];
                                                           $sl_id = $rw_recyle['sl_id'];

                                                           $checkf = mysqli_query($db_con, "SELECT * FROM `tbl_storage_level` where sl_name='$sl_name' and sl_id!='$sl_id' and delete_status='0'") or die('Error:' . mysqli_error($db_con));
														   
															/* $deletedDate = mysqli_query($db_con, "select start_date,user_name from tbl_ezeefile_logs where sl_id='" . $rw_recyle['sl_id'] . "' and (action_name='Storage deleted') ORDER BY id DESC LIMIT 1");
															$rwdeldDatetime = mysqli_fetch_assoc($deletedDate); */
															$deleteby = $rw_recyle['user_name'];
															$deldatetime = $rw_recyle['start_date'];
															$docdeldatetime = date('m-d-Y h:i A', strtotime($deldatetime));

                                                            ?>
                                                            <tr class="gradeX">
                                                                <td>
                                                                    <div class="checkbox checkbox-primary"><input type="checkbox" class="checkbox-primary emp_checkbox" data-doc-id="<?php echo $rw_recyle['sl_id']; ?>"><label for="checkbox6">  <?php echo $i . '.'; ?> </label></div>
                                                                </td>

                                                                <td><?php echo $rw_recyle['sl_name']; ?></td>
                                                                <td><?php parentLevel($rw_recyle['sl_id'], $db_con, $slpermIdes, $level, $lang, ''); ?></td>
                                                                <td><?php  
                                                                 echo (($totalFiles['fileSize'] > 999) ? round($totalFiles['fileSize'] / 1024, 2) : $totalFiles['fileSize']) . (($totalFiles['fileSize'] > 999) ? $lang['GB'] : $lang['MB'])

                                                                ?></td>
																
																 <td><?php echo (!empty($deldatetime) ? $docdeldatetime : "--"); ?> </td>
                                                                <td><?php echo (!empty($deleteby) ? $deleteby : "--"); ?> </td>
                                                                
                                                                <td>
                                                                    <?php if ($rwgetRole['restore_storage'] == '1') { ?>
                                                                        <button class="btn btn-primary" data-toggle="modal" data-target="#recycle" id="recycleRow" data="<?php echo $rw_recyle['doc_id']; ?>" onclick="restoreStorage('<?php echo $rw_recyle['sl_id']; ?>', '<?php echo $rw_recyle['sl_name']; ?>');" ><i class="fa fa-recycle" aria-hidden="true" title="<?php echo $lang['restore_storage']; ?>"></i></button>


                                                                    <?php }
                                                                    if ($rwgetRole['delete_storage'] == '1') { ?>
                                                                        <button class="btn btn-danger" data-toggle="modal" data-target="#delrecycle" id="delrestore" data="<?php echo $rw_recyle['sl_id']; ?>"><i class="fa fa-trash-o" aria-hidden="true" title="<?php echo $lang['Dlt_Storage']; ?>"></i> </button>
                                                                    <?php } 

                                                                    if (($rwgetRole['rename_storage'] == '1' && ($exfile == $rw_recyle['old_doc_name'])) && mysqli_num_rows($checkf)>0) { ?>
                                                                        <button class="btn btn-primary" data-toggle="modal" data-target="#changeFileame" id="renamefile" data="<?php echo $rw_recyle['sl_id']; ?>" onclick="restoreStorage('<?php echo $rw_recyle['sl_id']; ?>', '<?php echo $rw_recyle['sl_name']; ?>');" ><i class="fa fa-edit" aria-hidden="true" title="<?php echo $lang['edit_File_name']; ?>"></i> </button>
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
                                                                    <?php if ($rwgetRole['delete_storage'] == '1') { ?>
                                                                        <li><button id="del_file_recylebin" class="rows_selected btn btn-danger" data-toggle="modal" data-target="#del_send_to_recycle"><i class=" fa fa-trash-o"></i> <?php echo $lang['Dlt_Storage']; ?></button></li>
                                                                    <?php } if ($rwgetRole['restore_storage'] == '1') { ?>
                                                                        <li><button class="btn btn-primary" id="multi_restore_files"  data-toggle="modal" data-target="#multi_recycle"><i class="fa fa-recycle"></i> <?php echo $lang['restore_storage']; ?></button></li>
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
                                                            <th><?php echo $lang['Nme_of_Strg']; ?></th>
                                                            <th><?php echo $lang['Storage_Path']; ?></th>
                                                            <th><?php echo $lang['Total_size']; ?></th>
															<th> <?php echo $lang['doc_deleted_date']; ?> <i class="fa fa-sort"></i></th>
															<th class="js-sort-none"> <?php echo $lang['deleted_by']; ?></th>
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
                                                        <th><?php echo $lang['Nme_of_Strg']; ?></th>
                                                        <th><?php echo $lang['Storage_Path']; ?></th>
                                                        <th><?php echo $lang['Total_size']; ?></th>
														<th> <?php echo $lang['doc_deleted_date']; ?> <i class="fa fa-sort"></i></th>
														<th class="js-sort-none"> <?php echo $lang['deleted_by']; ?></th>
                                                        <th><?php echo $lang['Actions']; ?></th>
                                                    </tr>
                                                </thead>
                                                <tr>
                                                    <td colspan="5" class="text-center"><strong class="text-danger"> <?php echo $lang['storage_permission']; ?></strong></td>
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

            function restoreStorage(slid, slname){

                $(".storageId").val(slid);
                $(".storageName").val(slname);
            }



            $("button#delrestore").click(function () {
                var id = $(this).attr('data');
                $("#reDel").val(id);
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

                    var langcode = '<?php echo $rwgetRole['langCode']; ?>';



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
                            <h2 class="panel-title"><?php echo $lang['Are_u_confirm']; ?> ?</h2> 
                        </div>
                        <form method="post">
                            <div class="modal-body" id="restore">
                                <label><p class="text-alert"><?php echo $lang['r_u_sure_want_to_restore_this_storage']; ?> ?</p></label>
                            </div> 
                            <div class="modal-footer">

                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>

                                <input type="hidden" name="storageId" class="storageId"> 
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
                            <h2 class="panel-title" id="recycle_confirm"> <?php echo $lang['Are_u_confirm']; ?> ?</h2> 
                        </div>
                        <form method="post">
                            <div class="panel-body">
                                <span id="recycle_errmessage" style="display:none;"> <h5 class="text-alert"><?php echo $lang['Pls_sl_storage_fr_Dl']; ?></h5></span>
                                <label id="recycle_hide"><p class="text-alert"><?php echo $lang['r_u_sure_want_to_Dlt_thse_storage_Pr']; ?> ?</p></label>
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
                            <h2 class="panel-title" id="multi_restore_confirm"> <?php echo $lang['Are_u_confirm']; ?> ? </h2> 
                        </div>
                        <form method="post" >
                            <div class="panel-body">
                                <span id="multi_restore_errmessage" style="display:none;"> <p class="text-alert"><?php echo $lang['Pls_sel_storage_for_Restore']; ?></p></span>
                                <label id="multi_restore_hide"><p class="text-danger"><?php echo $lang['Are_you_sure_want_to_Rstre_selcted_storage']; ?></p></label>
                            </div> 
                            <div class="modal-footer">
                                <input type="hidden" id="reDel_multi_restore" name="storageId" value="">
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
                            <h2 class="panel-title"><?php echo $lang['Are_u_confirm']; ?> ?</h2> 
                        </div>
                        <form method="post">
                            <div class="panel-body" >
                                <label class="text-danger"><?php echo $lang['r_u_sue_wnt_to_Del_tis_storage']; ?></label>
                            </div> 
                            <div class="modal-footer">
                                <input type="hidden" id="reDel" name="storageId">
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
                            <h2 class="panel-title"><?php echo $lang['Are_u_confirm']; ?> ?</h2> 
                        </div>
                        <form method="post">
                            <div class="panel-body" id="refile">
                                 <div class="form-group txt">
                                    <label><?php echo $lang['Nme_of_Strg']; ?> <span style="color:red;">*</span></label>
                                    <input type="text"  name="storagename" id="storage_name" class="form-control storageName" required >
                                </div>
                            </div> 
                            <div class="modal-footer">
                                <input type="hidden" name="storageId" id="slid" class="storageId">
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

    $storageIds = $_POST['reDel'];

    $storageIds = explode(",", $storageIds);

    $flag =false;
	//connect to file sever 
	$fileManager->conntFileServer();
	
    foreach ($storageIds as $key => $sl_id) {

        $deleteStorage = mysqli_query($db_con, "Select * from tbl_storage_level where sl_id = '$sl_id'"); //or die('Error :' . mysqli_error($db_con));
        $rwdeleteStorage = mysqli_fetch_assoc($deleteStorage);

        $deletStorageName = $rwdeleteStorage['sl_name'];
        
        $dirPath = "extract-here/" . str_replace(" ", "", $$deletStorageName);

        $delStrg = mysqli_query($db_con, "Select sl_id from tbl_storagelevel_to_permission where user_id = '$_SESSION[cdes_user_id]'"); //or die('Error :' . mysqli_error($db_con));

        $rwdelStrg = mysqli_fetch_assoc($delStrg);
        //echo $rwdelStrg['sl_id']; die;
        if ($rwdelStrg['sl_id'] != $sl_id) {

            mysqli_query($db_con, "DELETE FROM tbl_storage_level WHERE sl_id='$sl_id' and (sl_parent_id!='0' and sl_depth_level!='0')"); //or die('Error:' . mysqli_error($db_con));

            deleteDocument($db_con, $sl_id, $dirPath, $fileManager);

            deleteSubFolders($db_con, $sl_id, $fileManager, 'yes');
			
			if(is_dir($dirPath)){
				rmdir($dirPath);
			}
            

            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$sl_id','Storage Deleted','$date', null,'$host','Storage Name $deletStorageName deleted from bin.')"); //or die('error :' . mysqli_error($db_con));
           
           $flag =true;

        } else {

            $flag =false;
            
        }

    }

    if($flag){

        echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Strg_Deleted_Successfully'] . '");</script>';

    }else{

        echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['root_storage_cannot_deleted'] . '");</script>';
    }

    mysqli_close($db_con);
}


//Restore Again
if (isset($_POST['Restore'], $_POST['token']) && intval($_POST['storageId'])) {

        $sl_id = filter_input(INPUT_POST, "storageId");

        mysqli_set_charset($db_con, 'utf8');

        $storage = mysqli_query($db_con, "SELECT sl_id, sl_name, sl_depth_level FROM `tbl_storage_level` WHERE sl_id='$sl_id'"); 

        $rows = mysqli_fetch_assoc($storage);
        $storagename = $rows['sl_name'];
        $level = $rows['sl_depth_level'];

        $storageexit = mysqli_query($db_con, "SELECT sl_id, sl_name FROM `tbl_storage_level` WHERE sl_depth_level='$level' and sl_name='$storagename' and delete_status=0 and sl_id!='$sl_id'"); 

        if(mysqli_num_rows($storageexit)==0){

            $RestoreFile = mysqli_query($db_con, "UPDATE `tbl_storage_level` SET delete_status=0 WHERE sl_id='$sl_id'"); 
            moveFilesInRecycleBin($db_con, $sl_id, 1);
            if ($RestoreFile) {

                reStoreFolders($db_con, $sl_id);

                $action = "Storage ". $rows['sl_name'] ." restored";

                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Storage Restored','$date',null,'$host','$action')"); // or die('error : ' . mysqli_error($db_con));

                echo'<script>taskSuccess("recycle-folder", "' . $lang['doc_Restore_Success'] . '");</script>';
                
            }else{

                 echo'<script>taskFailed("recycle-folder", "' . $lang['document_Restore_Failed'] . '");</script>';
            }

        }else{

             echo'<script>taskFailed("recycle-folder", "' . $lang['storage_already_exist_pls_rename_storage_to_restore'] . '");</script>';
        }

        

        mysqli_close($db_con);
}

//Delete recycle bin files
if (isset($_POST['DelRstr'], $_POST['token']) && intval($_POST['storageId'])) {

    $sl_id = $_POST['storageId'];

    $deleteStorage = mysqli_query($db_con, "Select * from tbl_storage_level where sl_id = '$sl_id'"); //or die('Error :' . mysqli_error($db_con));
    $rwdeleteStorage = mysqli_fetch_assoc($deleteStorage);
    $deletStorageName = $rwdeleteStorage['sl_name'];
    
    $dirPath = "extract-here/" . str_replace(" ", "", $$deletStorageName);

    $delStrg = mysqli_query($db_con, "Select sl_id from tbl_storagelevel_to_permission where user_id = '$_SESSION[cdes_user_id]'"); //or die('Error :' . mysqli_error($db_con));
    $rwdelStrg = mysqli_fetch_assoc($delStrg);
    //echo $rwdelStrg['sl_id']; die;
    if ($rwdelStrg['sl_id'] != $sl_id) {

        mysqli_query($db_con, "DELETE FROM tbl_storage_level WHERE sl_id='$sl_id' and (sl_parent_id!='0' and sl_depth_level!='0')"); //or die('Error:' . mysqli_error($db_con));
		//connect to file sever 
		$fileManager->conntFileServer();
		
        deleteDocument($db_con, $sl_id, $dirPath, $fileManager);

        deleteSubFolders($db_con, $sl_id, $fileManager, 'yes');

        //rmdir($dirPath);

        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$sl_id','Storage Deleted','$date', null,'$host','Storage Name $deletStorageName deleted from bin.')"); //or die('error :' . mysqli_error($db_con));
        $delParentId = $rwdeleteStorage['sl_parent_id'];
        echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Strg_Deleted_Successfully'] . '");</script>';
    } else {
        echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['root_storage_cannot_deleted'] . '");</script>';
    }
    //}
    mysqli_close($db_con);
}

if (isset($_POST['multi_Restore'], $_POST['token'])) {

    $storageIdss = $_POST['storageId'];

    $storageIds = explode(",", $storageIdss);

    mysqli_set_charset($db_con, 'utf8');

    $flag=false;

    mysqli_autocommit($db_con, FALSE);

    $alreayexist=false;
    foreach ($storageIds as $key => $sl_id) {

        $storage = mysqli_query($db_con, "SELECT sl_id, sl_name, sl_depth_level FROM `tbl_storage_level` WHERE sl_id='$sl_id'") or die('error : ' . mysqli_error($db_con)); 

        $rows = mysqli_fetch_assoc($storage);

        $storagename = $rows['sl_name'];
        $level = $rows['sl_depth_level'];

        $storageexit = mysqli_query($db_con, "SELECT sl_id, sl_name FROM `tbl_storage_level` WHERE sl_depth_level='$level' and sl_name='$storagename' and delete_status=0 and sl_id!='$sl_id'"); 

        if(mysqli_num_rows($storageexit)==0){

            $RestoreFile = mysqli_query($db_con, "UPDATE `tbl_storage_level` SET delete_status=0 WHERE sl_id='$sl_id'"); 

            moveFilesInRecycleBin($db_con, $sl_id, 1);

            if ($RestoreFile) {

                reStoreFolders($db_con, $sl_id);

                $action = "Storage ". $rows['sl_name'] ." restored";

                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Storage Restored','$date',null,'$host','$action')") or die('error : ' . mysqli_error($db_con));

                $flag=true;
                
            }else{

                $flag=false;
            }
        }else{

            $alreayexist=true;

            break;
        }

    }

    if($alreayexist){

         echo'<script> taskFailed("' . basename($_SERVER['REQUEST_URI']) . '", "' . $lang['storage_already_exist_pls_rename_storage_to_restore'] . '");</script>';
         die();

    }

    if($flag==true){

        mysqli_autocommit($db_con, true);

        echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '", "' . $lang['storage_restore_succssfully'] . '");</script>';

    }else{
        echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '", "' . $lang['storage_failed_tostorage'] . '");</script>';
    }

    mysqli_close($db_con);
}

if (isset($_POST['rename'], $_POST['token'])) {

    mysqli_set_charset($db_con, 'utf8');
    
    $storagename = mysqli_real_escape_string($db_con, $_POST['storagename']);

    $slId = mysqli_real_escape_string($db_con, $_POST['storageId']);

    $oldname = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id ='$slId'");
    $rwoldname = mysqli_fetch_assoc($oldname);
    $old_storage_name = $rwoldname['sl_name'];

    if($storagename!=$old_storage_name){

        $rename = mysqli_query($db_con, "update tbl_storage_level set sl_name='$storagename' where sl_id ='$slId'") or die('error : ' . mysqli_error($db_con));
        if ($rename) {
            mysqli_set_charset($db_con, 'utf8');
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`, `action_name`, `start_date`, `system_ip`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Storage name $old_storage_name to $storagename renamed for restored','$date','$host')"); // or die('error : ' . mysqli_error($db_con));
            echo'<script>taskSuccess("recycle-folder", "' . $lang['storage_rename_success'] . '");</script>';
        } else {
            echo'<script>taskFailed("recycle-folder", "' . $lang['storage_rename_failed'] . '");</script>';
        }
    }else{

        echo'<script>taskFailed("recycle-folder", "' . $lang['Storage_Name_Already_Exist'] . '");</script>';
    }

    mysqli_close($db_con);
}


function findTotalFile($slperm) {
    global $list;
    $list = array();
    global $db_con;
    global $numFile;
    global $totalFSize;
    global $totalFolder;
    global $noofpages;
    //$contFile = mysqli_query($db_con, "select sum(doc_size) as total, count(doc_name) as count,sum(noofpages) as numPage from tbl_document_master where FIND_IN_SET('$slperm',doc_name) && flag_multidelete = 1") or die('Error :' . mysqli_error($db_con));
    $contFile = mysqli_query($db_con, "select sum(doc_size) as total, count(doc_name) as count,sum(noofpages) as numpages from tbl_document_master where doc_name='$slperm'  and flag_multidelete=3") or die('Error :' . mysqli_error($db_con));
    $rwcontFile = mysqli_fetch_assoc($contFile);
    $totalFSize1 = $rwcontFile['total'];
    $totalFSize += round($totalFSize1 / (1024 * 1024), 2);
    $numFile += $rwcontFile['count'];
    $list["files"] = $numFile;
    $list["fileSize"] = $totalFSize;
    if (!empty($slperm)) {
        $totalFolder += 1;
    }
    $list["totalFolder"] = $totalFolder;
    $noofpages += $rwcontFile['numPage'];
    $list['numPages'] = $noofpages;
    $sql_child = "select * FROM tbl_storage_level WHERE sl_parent_id = '$slperm' ";
    $sql_child_run = mysqli_query($db_con, $sql_child) or die('Error: ' . mysqli_error($db_con));
    if (mysqli_num_rows($sql_child_run) > 0) {

        while ($rwchild = mysqli_fetch_assoc($sql_child_run)) {

            $child = $rwchild['sl_id'];
            $clagain = findTotalFile($child);
        }
    }
    return $list;
}


function parentLevel($slid, $db_con, $slperm, $level, $lang, $value) {

    $flag = 0;
    $slPermIds = explode(',', $slperm);
    if (in_array($slid, $slperm)) {
        $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid'") or die('Error' . mysqli_error($db_con));
        $rwParent = mysqli_fetch_assoc($parent);

        if ($level < $rwParent['sl_depth_level']) {
            parentLevel($rwParent['sl_parent_id'], $db_con, $slperm, $level, $lang, $rwParent['sl_name']);
        }
        $flag = 1;

    } else {

        $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid' and sl_parent_id='$slperm'") or die('Error' . mysqli_error($db_con));
        if (mysqli_num_rows($parent) > 0) {

            $rwParent = mysqli_fetch_assoc($parent);
            if ($level < $rwParent['sl_depth_level']) {
                parentLevel($rwParent['sl_parent_id'], $db_con, $slperm, $level, $lang, $rwParent['sl_name']);
            }
            $flag = 1;
        } else {
            $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid'") or die('Error' . mysqli_error($db_con));
            $rwParent = mysqli_fetch_assoc($parent);
            $getparnt = $rwParent['sl_parent_id'];
            if ($level <= $rwParent['sl_depth_level']) {
                parentLevel($getparnt, $db_con, $slperm, $level, $lang, $rwParent['sl_name']);
                $flag = 1;
            } else {
                $flag = 0;
                //header("Location: ./storage_test?id=" . urlencode(base64_encode($slperm)));
            }
        }
    }
    if ($flag == 1) {
        ?>
        <span> <?php
            if (!empty($value)) {
                echo $value = $rwParent['sl_name'] . ' > ';
            } else {
               echo $value = $rwParent['sl_name'];
            }
           
            ?></span>
    <?php
    }
}
?>
?>