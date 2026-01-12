<!DOCTYPE html>
<html>
    <?php
    set_time_limit(0);
    require_once './loginvalidate.php';
    require_once './application/pages/head.php';
    require_once './classes/ftp.php';
    require_once './application/pages/feature-enable-disable.php';
    require_once './application/pages/function.php';
	require_once './classes/fileManager.php';

	$fileManager = new fileManager();
   
    if (($rwgetRole['advance_search'] != '1' && $rwgetRole['dashboard_mydms'] != '1') || empty($slpermIdes)) {
        header('Location: ./index');
        exit();
    }
    //for save query
    $sq = urldecode(base64_decode($_GET['sq']));
    $sqId = preg_replace('/[^0-9]/', '', $sq);
    if (isset($sqId) && !empty($sqId)) {
        $savequery = mysqli_query($db_con, "SELECT `metadata_ids` FROM `query` WHERE id='$sqId'");
        $rowsavequery = mysqli_fetch_assoc($savequery);
        $metaIds = $rowsavequery['metadata_ids'];
    }
    $slid = urldecode(base64_decode($_GET['id']));
    $selectedStorage = preg_replace('/[^0-9]/', '', $slid);
    if (isset($selectedStorage) && !empty($selectedStorage)) {
        $folder = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$selectedStorage'");
    }
    $rwFolder = mysqli_fetch_assoc($folder);
    $slid = $rwFolder['sl_id'];
    $parentid = $rwFolder['sl_name'];

    //start checking storage permission
    $perm = mysqli_query($db_con, "select sl_id from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]' group by sl_id");
    $slperms = array();
    while ($rwPerm = mysqli_fetch_assoc($perm)) {
        $slperms[] = $rwPerm['sl_id'];
    }
    $sl_perm = implode(',', $slperms);
    $slids = findsubfolder($sl_perm, $db_con);

    $slids = implode(',', $slids);
    //end checking storage permission
    ?>
    <link href="assets/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" />
    <link href="assets/plugins/multiselect/css/multi-select.css"  rel="stylesheet" type="text/css" />
    <link href="assets/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" />   
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
            var ids = ["qry_name", "metaser", "metaser1", "qry_name", "reason"];
            control.makeTransliteratable(ids);


            // Show the transliteration control which can be used to toggle between
            // English and Hindi and also choose other destination language.
            // control.showControl('translControl');

        }
        google.setOnLoadCallback(onLoad);

    </script> 
    <style>
        /*        .frmSearch {border: 1px solid #a8d4b1; background-color: #c6f7d0; margin: 2px 0px; padding:40px; border-radius:4px;}*/
        #keyword-list{float:left; list-style:none; padding:0; z-index: 999999; position: absolute;}
        #keyword-list li{padding: 5px; background: #ebeff2; border-bottom: #ffffff 1px solid; width: 333px; position: relative; border-radius: 2px; color: #193860; overflow: auto;}
        #keyword-list li:hover{background:#193860; cursor: pointer; color: #ffffff;}
        .ms-container{
            width: 100%;
        }
    </style>
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
                                        <a href="metasearch"><?php echo $lang['Search']; ?></a>
                                    </li>
                                    <li class="active">
                                        <?php echo $lang['advance_keyword_search']; ?>
                                    </li>
                                    <li> <a href="#" data-toggle="modal" data-target="#help-modal" id="helpview" data="62" title="<?= $lang['help']; ?>"><i class="fa fa-question-circle"></i> </a></li>
                                    <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                                </ol>
                            </div>
                        </div>
                        <div class="box box-primary m-b-50" style="padding-bottom:150px;">
                            <div class="box-header with-border">
                                <div class="col-sm-6">
                                    <h4 class="header-title"><?php echo $lang['Required_fields_are_marked_with_a']; ?>(<span style="color:red;">*</span>)</h4>
                                </div>
                                <div class="col-sm-6">
                                    <?php if ($rwgetRole['save_query'] == '1' && !empty($_GET['advnc'])) { ?>
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#savequery" id="svequery"><i class="fa fa-save" aria-hidden="true" title="<?php echo $lang['Sve_Qry']; ?>"></i> <?php echo $lang['Sve_Qry']; ?></button>
                                    <?php } ?>
                                    <a href="Frequently_queries" class="btn btn-primary"><i class="fa fa-eye"></i> <?php echo $lang['save_queries']; ?></a>

                                    <a href="advance-keyword-search" class="btn btn-warning"><?php echo $lang['Reset']; ?></a>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="container">
                                    <div class="row">
                                        <form method="get">
                                            <div class="row">
                                                <div class="form-group">
                                                    <div class="col-md-6">
                                                        <label><?php echo $lang['Select_Storage']; ?><span class="text-danger">*</span></label>
                                                        <select class="form-control parent select2" required="required" data-live-search="true" id="parent" name="id">
                                                            <option value=""><?php echo $lang['Select_Storage']; ?></option>
                                                            <?php
                                                            if (isset($selectedStorage) && !empty($selectedStorage)) {
                                                                $parentId = $selectedStorage;
                                                            }
                                                            if (!empty($slpermIdes)) {
                                                                $sllevel = mysqli_query($db_con, "select * from tbl_storage_level where sl_id in($slpermIdes) and delete_status=0 order by sl_name asc");
                                                                while ($rwSllevel = mysqli_fetch_assoc($sllevel)) {
                                                                    $level = $rwSllevel['sl_depth_level'];
                                                                    $slId = $rwSllevel['sl_id'];
                                                                    $slperm = $rwSllevel['sl_id'];
                                                                    findChilds($slId, $level, $slperm, $parentId);
                                                                }
                                                            }
                                                            ?>
                                                        </select> 
                                                        <?php

                                                        function findChilds($sl_id, $level, $slperm, $parentId) {

                                                            global $db_con;

                                                            if ($sl_id == $parentId) {
                                                                echo '<option value="' . base64_encode(urlencode($sl_id)) . '"  selected>';
                                                                parentLevels($sl_id, $db_con, $slperm, $level, '');
                                                                echo '</option>';
                                                            } else {
                                                                echo '<option value="' . base64_encode(urlencode($sl_id)) . '">';
                                                                parentLevels($sl_id, $db_con, $slperm, $level, '');
                                                                echo '</option>';
                                                            }

                                                            $sql_child = "select * FROM tbl_storage_level WHERE sl_parent_id = '$sl_id' and delete_status=0 order by sl_name asc";

                                                            $sql_child_run = mysqli_query($db_con, $sql_child) or die('Error:' . mysqli_error($db_con));

                                                            if (mysqli_num_rows($sql_child_run) > 0) {

                                                                while ($rwchild = mysqli_fetch_assoc($sql_child_run)) {

                                                                    $child = $rwchild['sl_id'];
                                                                    findChilds($child, $level, $slperm, $parentId);
                                                                }
                                                            }
                                                        }

                                                        
                                                        ?>

                                                    </div>

                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group">
                                                    <div class="col-md-4">
                                                        <label>Filename</label>
                                                        <input type="text" class="form-control search-box old_doc_name" name="old_doc_name" data="old_doc_name" value="<?php echo $_GET['old_doc_name']; ?>" placeholder="<?php echo $lang['entr_srch_txt_hr'] ?> " title="<?php echo $_GET['old_doc_name']; ?>">
                                                        <div id="old_doc_name"></div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="col-md-4">
                                                            <label>No of pages</label>
                                                            <input type="text" class="form-control search-box noofpages" name="noofpages" data="noofpages" value="<?php echo $_GET['noofpages']; ?>" placeholder="<?php echo $lang['entr_srch_txt_hr'] ?> ">
                                                            <div id="noofpages"></div>
                                                            <input type="hidden" name="advnc" value="ZXplZW9mZmljZQ==" />
                                                        </div>
                                                    </div>

                                                    <?php
                                                    $i = 1;
                                                    $metas = mysqli_query($db_con, "select distinct metadata_id from tbl_metadata_to_storagelevel where sl_id='$selectedStorage'");
                                                    while ($metaval = mysqli_fetch_assoc($metas)) {
                                                        $meta = mysqli_query($db_con, "select * from tbl_metadata_master WHERE id='$metaval[metadata_id]' order by field_name asc");
                                                        $rwMeta = mysqli_fetch_assoc($meta);
                                                        ?>
                                                        <div class="form-group">
                                                            <div class="col-md-4 m-b-10">
                                                                <label><?php echo $rwMeta['field_name']; ?></label>
                                                                <input type="text" class="form-control search-box <?php echo $rwMeta['field_name']; ?>" data="<?php echo $rwMeta['field_name']; ?>" name="<?php echo $rwMeta['field_name']; ?>" value="<?php echo $_GET[$rwMeta['field_name']] ?>" placeholder="<?php echo $lang['entr_srch_txt_hr'] ?>" title="<?php echo $_GET[$rwMeta['field_name']] ?>">
                                                                <div id="<?php echo $rwMeta['field_name']; ?>"></div>
                                                            </div>
                                                        </div>

                                                        <?php
                                                        $i++;
                                                    }
                                                    ?>

                                                </div>
                                                <div class="col-md-2" style="margin-top:25px;">
                                                    <button type="submit" class="btn btn-primary" id="search" title="<?= $lang['Search'] ?>"><i class="fa fa-search"></i> <?= $lang['Search'] ?></button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>


                                    <?php
                                    if (isset($_GET['advnc'])) {
                                        $query = basename($_SERVER['REQUEST_URI'])
                                        ?>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <?php
                                if (isset($_GET['advnc'])) {
                                    $slid = urldecode(base64_decode($_GET['id']));
                                    $selectedStorage = preg_replace('/[^0-9]/', '', $slid);
                                    $limit = $_GET['limit'];
                                    $start = $_GET['start'];
                                    $queryString = $_SERVER["QUERY_STRING"];
                                    $res = searchAllDB($selectedStorage, $db_con, $rwgetRole, $limit, $start, $queryString, $lang, $metaIds);
                                }
                                ?>
                            </div>
                        </div>
                        <!---assign meta-data model start ---->
                        <div id="con-close-modal5" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog"> 
                                <div class="modal-content"> 
                                    <div class="modal-header"> 
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                                        <h4 class="modal-title">Customized table column for <?php echo $rwFolder['sl_name']; ?> folder.</h4> 
                                    </div> 
                                    <form method="post">
                                        <div class="modal-body">
                                            <div class="row shiv metaa">
                                                <span><strong><?php echo $lang['select_keyword']; ?></strong></span>
                                                <strong style="margin-left: 176px;"><?php echo $lang['keyword_view_in_table']; ?></strong>
                                                <select multiple="multiple" class="multi-select" id="my_multi_select1" name="my_multi_select1[]" data-plugin="multiselect">
                                                    <?php
                                                    //$arrarMeta = array();
                                                    $metas = mysqli_query($db_con, "select * from tbl_advance_search_userwise where sl_id = '$selectedStorage' and user_id='$_SESSION[cdes_user_id]'") or die('Error: metadata' . mysqli_error($db_con));
                                                    $metaval = mysqli_fetch_assoc($metas);
                                                    $keyId = explode(',', $metaval['metadata_ids']);

                                                    mysqli_set_charset($db_con, "utf8");
                                                    $assignKeyword = mysqli_query($db_con, "SELECT * FROM `tbl_metadata_to_storagelevel` where sl_id='$selectedStorage'");
                                                    while ($rwassignKeyword = mysqli_fetch_assoc($assignKeyword)) {
                                                        $keywordId = $rwassignKeyword['metadata_id'];
                                                        $meta = mysqli_query($db_con, "select * from tbl_metadata_master where id='$keywordId' order by field_name asc");
                                                        $rwMeta = mysqli_fetch_assoc($meta);
                                                        if (in_array($rwMeta['id'], $keyId)) {
                                                            echo '<option value="' . $rwMeta['id'] . '" selected>' . $rwMeta['field_name'] . '</option>';
                                                        } else {
                                                            echo '<option value="' . $rwMeta['id'] . '">' . $rwMeta['field_name'] . '</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <input type="hidden" value="<?php echo $selectedStorage; ?>" name="id">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                                            <button class="btn btn-primary waves-effect waves-light" type="submit" name="customizedsearch"><?php echo $lang['Submit']; ?></button>
                                        </div>
                                    </form>

                                </div> 
                            </div>
                        </div>
                        <div id="savequery" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog "> 
                                <div class="panel panel-color panel-danger"> 
                                    <div class="panel-heading"> 
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                                        <h2 class="panel-title"><?php echo $lang['r_u_wnt_to_sv_qry']; ?></h2> 
                                    </div>
                                    <form method="post">
                                        <div class="panel-body" >
                                            <label><?php echo $lang['Query']; ?><span class="text-danger">*</span></label>
                                            <input type="text" id="qry_name" name="qry_name" class="form-control specialchaecterlock" value="" placeholder="<?= $lang['eyqh'] ?>" required>
                                            <input type="hidden" name="url" id="url" value="<?php echo $query; ?>">
                                        </div> 
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                                            <input type="submit" class="btn btn-primary " name="savqry" value="<?php echo $lang['Sve_Qry']; ?>">

                                        </div>
                                    </form>

                                </div> 
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end: page -->

            </div> <!-- end Panel -->
            <?php require_once './application/pages/footer.php'; ?>

            <?php require_once './application/pages/footerForjs.php'; ?>
        </div> <!-- container -->
        <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>

        <?php require_once 'file-action-js.php'; ?>
        <script type="text/javascript">
                                        $(document).ready(function () {

                                            $(".search-box").keyup(function () {
                                                var fieldname = $(this).attr('data');
                                                $.ajax({
                                                    type: "POST",
                                                    url: "./application/ajax/readKeyword.php",
                                                    data: {
                                                        keyword: $(this).val(),
                                                        key: fieldname,
                                                        slperm: '<?php echo $selectedStorage; ?>'
                                                    },

                                                    beforeSend: function () {
                                                        // $(".search-box").css("background", "#FFF url(./application/ajax/LoaderIcon.gif) no-repeat 165px");
                                                    },

                                                    success: function (data) {
                                                        $("#" + fieldname).show();
                                                        $("#" + fieldname).html(data);
                                                        $(".search-box").css("background", "#FFF");
                                                    }
                                                });
                                            });
                                        });

                                        function selectKeyword(val, keyname) {
                                            $("." + keyname).val(val);
                                            $("#" + keyname).hide();
                                        }
        </script>
        <script type="text/javascript">

            $("#parent").change(function () {
                var slId = $(this).val();
                window.location.href = "advance-keyword-search?id=" + slId;

            });


            //for delete version document
            $("#con-close-modal-history").delegate("a#deleteVersionDocument", "click", function () {
                var id = $(this).attr("data");
                //alert(id);
                $("#docidversion").val(id);
            });
            $('form').parsley();
        </script>
        <script type="text/javascript">
            $('#downloadcheckedfile').on('click', function (e) {

                var file = [];
                $(".emp_checkbox:checked").each(function () {
                    file.push($(this).data('doc-id'));
                });
                if (file.length <= 0) {
                    $("#unselectfile").show();
                    $("#filedownload").hide();
                    $("#download1").show();
                    $("#download2").hide();
                } else {
                    $("#unselectfile").hide();
                    $("#filedownload").show();
                    $("#download1").hide();
                    $("#download2").show();

                    var selected_values = file.join(",");
                    $('#totaldocId').val(selected_values);


                }
            });
        </script>


        <?php

        function searchAllDB($selectedStorage, $db_con, $rwgetRole, $limit, $start, $queryString, $lang, $metaIds) {

            $keyword = mysqli_query($db_con, "SELECT tmm.field_name FROM tbl_metadata_to_storagelevel tms INNER JOIN tbl_metadata_master tmm  ON tms.metadata_id = tmm.id where tms.sl_id='$selectedStorage'");
            $columns = array();
            while ($rwmeta = mysqli_fetch_assoc($keyword)) {
                if (!empty($_GET[$rwmeta['field_name']]) || $_GET[$rwmeta['field_name']] == '0') {
                    $columns[] = $rwmeta['field_name'];
                }
            }
            if (!empty($_GET['old_doc_name'])) {
                $columns[] = 'old_doc_name';
            }
            if (!empty($_GET['noofpages'])) {
                $columns[] = 'noofpages';
            }
            $table = "tbl_document_master";
            mysqli_set_charset($db_con, 'utf8');
            if (!empty($selectedStorage)) {
                $sql_search = "select * from " . $table . " where flag_multidelete=1 and doc_name='$selectedStorage'";
            } else {
                $sql_search = "select * from " . $table . " where flag_multidelete=1 and doc_name in($slids)";
            }
            //print_r($columns);
            $sql_search_fields = Array();
            for ($i = 0; $i < count($columns); $i++) {
                $sql_search_fields[] = "`" . preg_replace("/[^\w$\x{0080}-\x{FFFF}!(){}+=~@%&#.,: -]+/u", "", $columns[$i]) . "` = '" . preg_replace("/[^\w$\x{0080}-\x{FFFF}!(){}+=~@%&#.,: -]+/u", "", $_GET[$columns[$i]]) . "'";
            }
            $sql_search .= ' and (';
            $sql_search .= implode(" and ", $sql_search_fields);
            $sql_search .= ')';
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
                $start = isset($start) ? ($start > 0) ? $start : 0 : 0;
                $max_pages = ceil($foundnum / $per_page);
                if (!$start) {
                    $start = 0;
                }

                $sql_search .= " limit $start,$per_page";
                $rs3 = mysqli_query($db_con, $sql_search);
                ?>
                <div class="box-body limit">
                    <div class="col-sm-8">
                        <?php echo $lang['Show']; ?>
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
                        </select> <?php echo $lang['Documents']; ?>
                    </div>

                    <div class="col-sm-4">
                        <label style="margin-top:7px;"><?php echo $start + 1 ?> <?php echo $lang['To'] ?> <?php
                            if (($start + $per_page) > $foundnum) {
                                echo $foundnum;
                            } else {
                                echo ($start + $per_page);
                            }
                            ?> <span><?php echo $lang['ttl_recrds']; ?> : <?php echo $foundnum; ?></span></label>
                        <a data-toggle="modal" data-target="#con-close-modal5" class="btn btn-primary pull-right btn-sm" data-toggle="tooltip" title="<?php echo $lang['add_remove']; ?>"><i class="fa fa-exchange"></i> <?php echo $lang['add_remove_column']; ?></a>
                    </div>
                </div>
                <div style="overflow:auto">
                    <table class="table table-striped table-bordered m-b-50 js-sort-table">
                        <thead>
                            <tr>
                                <th class="sort-js-none" ><div class="checkbox checkbox-primary"><input  type="checkbox" class="checkbox-primary" id="select_all"> <label for="checkbox6"> <strong><?php echo $lang['All']; ?></strong></label></div>  </th>
                                <th><?php echo $lang['File_Name']; ?></th>
                                <th><?php echo $lang['storage']; ?></th>
                                <th class="sort-js-number" ><?php echo $lang['File_Size']; ?></th>
                                <th class="sort-js-number" ><?php echo $lang['No_of_Pages']; ?></th>
                                <?php
                                if (empty($_GET['sq']) || empty($metaIds)) {
                                    $checkcolumn = mysqli_query($db_con, "select * from tbl_advance_search_userwise where sl_id='$selectedStorage' and user_id='" . $_SESSION['cdes_user_id'] . "'");
                                    if (mysqli_num_rows($checkcolumn) <= 0) {

                                        $getMetaId = mysqli_query($db_con, "select * from tbl_metadata_to_storagelevel where sl_id = '$selectedStorage'") or die('Error:gg' . mysqli_error($db_con));

                                        while ($rwgetMetaId = mysqli_fetch_assoc($getMetaId)) {

                                            $getMetaName = mysqli_query($db_con, "select * from tbl_metadata_master where id = '$rwgetMetaId[metadata_id]'") or die('Error:' . mysqli_error($db_con));

                                            while ($rwgetMetaName = mysqli_fetch_assoc($getMetaName)) {
                                                $meta = mysqli_query($db_con, "select `$rwgetMetaName[field_name]` from tbl_document_master where doc_id='$file_row[doc_id]'");
                                                $rwMeta = mysqli_fetch_assoc($meta);

                                                if (!empty($rwgetMetaName['field_name'])) {
                                                    if ($rwgetMetaName['field_name'] == 'noofpages' || $rwgetMetaName['field_name'] == 'filename') {
                                                        
                                                    } else {
                                                        echo "<th>" . $rwgetMetaName['field_name'] . "</th>";
                                                    }
                                                }
                                            }
                                        }
                                    } else {
                                        $rwcheckcolumn = mysqli_fetch_assoc($checkcolumn);
                                        $keywordIds = $rwcheckcolumn['metadata_ids'];

                                        $keywordName = mysqli_query($db_con, "select * from tbl_metadata_master where id in($keywordIds)") or die('Error:' . mysqli_error($db_con));
                                        while ($rwkeywordName = mysqli_fetch_assoc($keywordName)) {
                                            echo '<th>' . $rwkeywordName['field_name'] . ' </th>';
                                        }
                                    }
                                } else {

                                    $keywordName = mysqli_query($db_con, "select * from tbl_metadata_master where id in($metaIds)") or die('Error:xxxxx ' . mysqli_error($db_con));
                                    while ($rwkeywordName = mysqli_fetch_assoc($keywordName)) {
                                        echo '<th>' . $rwkeywordName['field_name'] . ' </th>';
                                    }
                                }
                                ?>
                                <th><?php echo $lang['Actions']; ?></th>
                            </tr></thead>
                        <?php
                        $rs3 = mysqli_query($db_con, $sql_search);
                        if (mysqli_num_rows($rs3) > 0) {
                            echo'<tbody>';
                            $j = $start + 1;
                            while ($rw = mysqli_fetch_assoc($rs3)) {
                                $getSlName = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id = '" . $rw['doc_name'] . "'") or die('Error in get name' . mysqli_error($db_con));
                                $rwgetSlName = mysqli_fetch_assoc($getSlName);
                                ?>
                                <tr>
                                    <td>  <div class="checkbox checkbox-primary m-r-15"> <input  type="checkbox" class="checkbox-primary emp_checkbox" data-doc-id="<?php echo $rw['doc_id']; ?>" id="shreId"> <label for="checkbox6"> <?= $j . '.'; ?> </label></div></td>
                                    <td> <?= $rw['old_doc_name']; ?> </td>
                                    <td> <?= $rwgetSlName['sl_name']; ?> </td>

                                     <td><?php echo formatSizeUnits($rw['doc_size']); ?></td>
                                    <td><?= $rw['noofpages']; ?></td>

                                    <?php
                                    if (empty($_GET['sq']) || empty($metaIds)) {
                                        $column = mysqli_query($db_con, "select * from tbl_advance_search_userwise where sl_id='$selectedStorage' and user_id='" . $_SESSION['cdes_user_id'] . "'");
                                        if (mysqli_num_rows($column) <= 0) {

                                            $getMetaId = mysqli_query($db_con, "select * from tbl_metadata_to_storagelevel where sl_id='" . $rw['doc_name'] . "'") or die('Error:gg' . mysqli_error($db_con));

                                            while ($rwgetMetaId = mysqli_fetch_assoc($getMetaId)) {

                                                $getMetaName = mysqli_query($db_con, "select * from tbl_metadata_master where id = '$rwgetMetaId[metadata_id]'") or die('Error:' . mysqli_error($db_con));

                                                while ($rwgetMetaName = mysqli_fetch_assoc($getMetaName)) {
                                                    $meta = mysqli_query($db_con, "select `$rwgetMetaName[field_name]` from tbl_document_master where doc_id='$rw[doc_id]'");
                                                    $rwMeta = mysqli_fetch_assoc($meta);

                                                    if ($rwgetMetaName['data_type'] == 'datetime') {
                                                        if (!empty($rwMeta[$rwgetMetaName['field_name']])) {
                                                            ?>
                                                            <td> <?php echo date('d-m-Y H:i:s', strtotime($rwMeta[$rwgetMetaName['field_name']])); ?></td>
                                                            <?php
                                                        } else {
                                                            echo '<td></td>';
                                                        }
                                                    } else {
                                                        echo '<td>' . $rwMeta[$rwgetMetaName['field_name']] . ' </td>';
                                                    }
                                                }
                                            }
                                        } else {

                                            $rwcolumn = mysqli_fetch_assoc($column);
                                            $keywordId = $rwcolumn['metadata_ids'];

                                            $keywordName = mysqli_query($db_con, "select * from tbl_metadata_master where id in($keywordId)") or die('Error:' . mysqli_error($db_con));
                                            while ($rwkeywordName = mysqli_fetch_assoc($keywordName)) {

                                                $metadata = mysqli_query($db_con, "select `$rwkeywordName[field_name]` from tbl_document_master where doc_id='" . $rw['doc_id'] . "'");
                                                $rwmetadata = mysqli_fetch_assoc($metadata);
                                                $rwmetadata[$rwkeywordName['field_name']];
                                                if ($rwkeywordName['data_type'] == 'datetime') {

                                                    if (!empty($rwmetadata[$rwkeywordName['field_name']])) {
                                                        ?>
                                                        <td> <?php echo date('d-m-Y H:i:s', strtotime($rwmetadata[$rwkeywordName['field_name']])); ?></td>
                                                        <?php
                                                    } else {
                                                        echo '<td></td>';
                                                    }
                                                } else {

                                                    echo '<td>' . $rwmetadata[$rwkeywordName['field_name']] . ' </td>';
                                                }
                                            }
                                        }
                                    } else {

                                        $keywordName = mysqli_query($db_con, "select * from tbl_metadata_master where id in($metaIds)") or die('Error:ggg' . mysqli_error($db_con));
                                        while ($rwkeywordName = mysqli_fetch_assoc($keywordName)) {

                                            $metadata = mysqli_query($db_con, "select `$rwkeywordName[field_name]` from tbl_document_master where doc_id='" . $rw['doc_id'] . "'");
                                            $rwmetadata = mysqli_fetch_assoc($metadata);
                                            $rwmetadata[$rwkeywordName['field_name']];
                                            if ($rwkeywordName['data_type'] == 'datetime') {

                                                if (!empty($rwmetadata[$rwkeywordName['field_name']])) {
                                                    ?>
                                                    <td> <?php echo date('d-m-Y H:i:s', strtotime($rwmetadata[$rwkeywordName['field_name']])); ?></td>
                                                    <?php
                                                } else {
                                                    echo '<td></td>';
                                                }
                                            } else {

                                                echo '<td>' . $rwmetadata[$rwkeywordName['field_name']] . ' </td>';
                                            }
                                        }
                                    }
                                    ?>

                                    <td>
                                <li class="dropdown top-menu-item-xs">
                                    <?php
                                    $checkfileLockqry = mysqli_query($db_con, "SELECT * FROM `tbl_locked_file_master` WHERE doc_id='$rw[doc_id]' and is_active='1'");
                                    if (mysqli_num_rows($checkfileLockqry) > 0) {
                                        $checkfileLock = mysqli_query($db_con, "SELECT * FROM `tbl_locked_file_master` WHERE doc_id='$rw[doc_id]' and is_locked='1' and user_id='$_SESSION[cdes_user_id]'");
                                        if (mysqli_num_rows($checkfileLock) > 0) {
                                            ?>
                                            <a href="" class="dropdown-toggle profile waves-effect waves-light" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-gear"></i></a>
                                            <ul class="dropdown-menu pdf gearbody">
                                                <li> 
                                                    <?php
                                                    if ($rw['checkin_checkout'] == 1) {
                                                        //@sk(221118): include view handler to handle different file formats
                                                        $file_row = $rw;
                                                        require 'view-handler.php';
                                                        ?>
                                                    </li>

                                                    <li>
                                                        <?php
                                                        /* ------Lock file code----- */
                                                        if ($rwgetRole['lock_file'] == '1') {
                                                            $checkfileLock = mysqli_query($db_con, "SELECT * FROM `tbl_locked_file_master` WHERE doc_id='$file_row[doc_id]' and user_id='$_SESSION[cdes_user_id]' and is_active='1'");
                                                            if (mysqli_num_rows($checkfileLock) > 0) {
                                                                $fetchdatalock = mysqli_fetch_assoc($checkfileLock);
                                                                if ($fetchdatalock['is_locked'] == "1") {
                                                                    ?>
                                                                    <a href="javascript:void(0)" class ="unlock_file" data="<?php echo $file_row['doc_id']; ?>"> <i class="fa fa-unlock"  title="<?php echo $lang['unlock_file']; ?>"></i> <?php echo $lang['unlock_file']; ?></a>   
                                                                    <?php
                                                                }
                                                            } else {
                                                                ?>
                                                                <a href="javascript:void(0)" class ="lock_file" data="<?php echo $file_row['doc_id'] ?>"> <i class="fa fa-lock"  title="<?php echo $lang['lock_file']; ?>"></i> <?php echo $lang['lock_file']; ?></a>   
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                    </li>
                                                    <?php if ($rwgetRole['view_metadata'] == '1') { ?>

                                                        <li> <a href="javascript:void(0)"  data-toggle="modal" data-target="#con-close-modal-history" onclick="return getFileHistory(<?php echo $file_row['doc_id'] ?>,<?php echo $file_row['doc_name']; ?>)"><i class="fa fa-history"></i> <?php echo $lang['history']; ?></a></li>

                                                    <?php } if ($rwgetRole['file_review'] == '1') { ?>
                                                        <?php if ((strtolower($file_row['doc_extn']) == 'pdf' || strtolower($file_row['doc_extn']) == 'doc' || strtolower($file_row['doc_extn']) == 'docx')) { ?>
                                                            <li> <a href="reviewers?i=<?php echo urlencode(base64_encode($_SESSION['cdes_user_id'])) ?>&doc_Id=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>" id="moveTorw" data="<?php echo $file_row['doc_id']; ?>"> <i class="fa fa fa-send"></i> <?php echo $lang['Review']; ?></a></li><?php } ?>

                                                    <?php } if ($rwgetRole['workflow_initiate_file'] == '1' || $rwgetRole['initiate_file'] == '1') { ?>
                                                        <li> <a href="javascript:void(0)" data-toggle="modal" data-target="#assign-workflow" id="moveToWf" data="<?php echo $file_row['doc_id']; ?>"> <i class="fa fa-plus"></i> <?php echo $lang['Workflow']; ?></a></li>
                                                    <?php } if (strtolower($file_row['doc_extn']) == 'pdf' && $rwgetRole['pdf_annotation']) { ?>
                                                        <li class="isprotected"> <a href="anott/add-delete-page?id1=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>&pn=1" target="_blank" > <i class="fa fa-plus"></i> <?php echo $lang['add_delete_pages']; ?></a></li>
                                                    <?php } ?> 
                                                    
                                                    <?php if ($rwgetRole['checkin_checkout'] == '1') { ?>
                                                        <li><a href="javascript:void(0)" id="checkout" data="<?php echo $file_row['doc_id']; ?>"><i class="fa fa-sign-out"></i> <?php echo $lang['Chk_Out']; ?></a></li>
                                                        <?php
                                                    }if ($rwgetRole['file_delete'] == '1') {
                                                        ?>
                                                        <li><a href="javascript:void(0)" data-toggle="modal" data-target="#con-close-modal2" id="removeRow" data="<?php echo $file_row['doc_id']; ?>"><i class="fa fa-trash"></i> <?php echo $lang['Delete']; ?> </a></li>
                                                        <?php
                                                    }
                                                } else {
                                                    $file_row = $rw;
                                                    require 'checkout-action.php';
                                                }
                                                ?>
                                            </ul>
                                        <?php } else {
                                            ?>
                                            <a href="javascript:void(0)" id="" data="<?php echo $rw['doc_id'] ?>" class="dropdown-toggle profile waves-effect waves-light send_lock_request" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-lock" title="<?php echo $lang['lock_file']; ?>"></i></a>

                                            <?php
                                        }
                                    } else {
                                        ?>
                                        <a href="" class="dropdown-toggle profile waves-effect waves-light" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-gear"></i></a>
                                        <ul class="dropdown-menu pdf gearbody">
                                            <li> 
                                                <?php
                                                if ($rw['checkin_checkout'] == 1) {
                                                    //@sk(221118): include view handler to handle different file formats
                                                    $file_row = $rw;
                                                    require 'view-handler.php';
                                                    ?>
                                                </li>

                                                <li class="isprotected">
                                                    <?php
                                                    /* ------Lock file code----- */
                                                    if ($rwgetRole['lock_file'] == '1') {
                                                        $checkfileLock = mysqli_query($db_con, "SELECT * FROM `tbl_locked_file_master` WHERE doc_id='$file_row[doc_id]' and user_id='$_SESSION[cdes_user_id]' and is_active='1'");
                                                        if (mysqli_num_rows($checkfileLock) > 0) {
                                                            $fetchdatalock = mysqli_fetch_assoc($checkfileLock);
                                                            if ($fetchdatalock['is_locked'] == "1") {
                                                                ?>
                                                                <a href="javascript:void(0)" class ="unlock_file" data="<?php echo $file_row['doc_id']; ?>"> <i class="fa fa-unlock"  title="<?php echo $lang['unlock_file']; ?>"></i> <?php echo $lang['unlock_file']; ?></a>   
                                                                <?php
                                                            }
                                                        } else {
                                                            ?>
                                                            <a href="javascript:void(0)" class ="lock_file" data="<?php echo $file_row['doc_id'] ?>"> <i class="fa fa-lock"  title="<?php echo $lang['lock_file']; ?>"></i> <?php echo $lang['lock_file']; ?></a>   
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </li>
                                                <?php if ($rwgetRole['view_metadata'] == '1') { ?>
                                                    <li class="isprotected"> <a href="javascript:void(0)"  data-toggle="modal" data-target="#con-close-modal-history" onclick="return getFileHistory(<?php echo $file_row['doc_id'] ?>,<?php echo $file_row['doc_name']; ?>)"><i class="fa fa-history"></i> <?php echo $lang['history']; ?></a></li>

                                                <?php } if ($rwgetRole['file_review'] == '1') { ?>
                                                    <?php if ((strtolower($file_row['doc_extn']) == 'pdf' || strtolower($file_row['doc_extn']) == 'doc' || strtolower($file_row['doc_extn']) == 'docx')) { ?>
                                                        <li class="isprotected"> <a href="reviewers?i=<?php echo urlencode(base64_encode($_SESSION['cdes_user_id'])) ?>&doc_Id=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>" id="moveTorw" data="<?php echo $file_row['doc_id']; ?>"> <i class="fa fa fa-send"></i> <?php echo $lang['Review']; ?></a></li><?php } ?>

                                                <?php } if ($rwgetRole['workflow_initiate_file'] == '1' || $rwgetRole['initiate_file'] == '1') { ?>
                                                    <li class="isprotected"> <a href="javascript:void(0)" data-toggle="modal" data-target="#assign-workflow" id="moveToWf" data="<?php echo $file_row['doc_id']; ?>"> <i class="fa fa-plus"></i> <?php echo $lang['Workflow']; ?></a></li>
                                                <?php } if (strtolower($file_row['doc_extn']) == 'pdf' && $rwgetRole['pdf_annotation']) { ?>
                                                    <li class="isprotected"> <a href="anott/add-delete-page?id1=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>&pn=1" target="_blank" > <i class="fa fa-plus"></i> <?php echo $lang['add_delete_pages']; ?></a></li>
                                                <?php } ?> 
                                                <?php if ($rwgetRole['checkin_checkout'] == '1') { ?>
                                                    <li class="isprotected"><a href="javascript:void(0)" id="checkout" data="<?php echo $file_row['doc_id']; ?>"><i class="fa fa-sign-out"></i> <?php echo $lang['Chk_Out']; ?></a></li>
                                                    <?php
                                                }if ($rwgetRole['file_delete'] == '1') {
                                                    ?>
                                                    <li class="isprotected"><a href="javascript:void(0)" data-toggle="modal" data-target="#con-close-modal2" id="removeRow" data="<?php echo $file_row['doc_id']; ?>"><i class="fa fa-trash"></i> <?php echo $lang['Delete']; ?> </a></li>
                                                    <?php
                                                }
                                            } else {
                                                $file_row = $rw;
                                                require 'checkout-action.php';
                                            }
                                            ?>
                                        </ul>
                                    <?php } ?>
                                </li>
                                </td>

                                <?php
                                echo'</tr>';
                                $j++;
                            }
                            echo '</tbody>';
                            mysqli_close($rs3);
                        }
                        ?>
                        <tr>

                            <td colspan="500" id="isprotected">
                                <?php if ($rwFolder['is_protected'] == 0 || $_SESSION['pass'] == $rwFolder['password']) { ?>
                                    <ul class="delete_export">
                                        <input type="hidden" name="slid" id="slid" value="<?php echo $selectedStorage; ?>">
                                        <input type="hidden" name="sty" id="sty" value="<?php echo $selectedStorage; ?>">
                                        <?php if ($rwgetRole['file_delete'] == '1') { ?>
                                            <li><button id="del_file" class="rows_selected btn btn-danger btn-sm" data-toggle="modal"  data-target="#del_send_to_recycle"><i data-toggle="tooltip" title="<?php echo $lang['Delete_files'] ?>" class="fa fa-trash-o"></i></button></li>
                                        <?php } if (($rwgetRole['export_csv'] == '1') && !empty($selectedStorage)) { ?>
                                            <li><button class="btn btn-primary btn-sm" id="export4"  data-toggle="modal"  data-target="#multi-csv-export-model"><i data-toggle="tooltip" title="<?php echo $lang['Export_Data'] ?>" class="fa fa-download"></i></button></li>
                                        <?php } if (($rwgetRole['move_file'] == '1') && !empty($selectedStorage)) { ?>
                                            <li><button id="move_multi" class="rows_selected btn btn-primary btn-sm" data-toggle="modal" data-target="#move-selected-files" > <i data-toggle="tooltip" title="<?php echo $lang['Mve_fles'] ?>" class="fa fa-share-square"></i></button></li>
                                        <?php } if (($rwgetRole['copy_file'] == '1') && !empty($selectedStorage)) { ?>
                                            <li><button class="rows_selected btn btn-primary btn-sm" id="copyFiles" data-toggle="modal" data-target="#copy-selected-files" ><i data-toggle="tooltip" title="<?php echo $lang['Copy_files'] ?>" class="fa fa-copy"></i></button></li>
                                        <?php } if ($rwgetRole['share_file'] == '1' && $rwdocshare['docshare_enable_disable'] == '1') { ?>
                                            <li><button class="rows_selected btn btn-primary btn-sm" id="shareFiles" data-toggle="modal" data-target="#share-selected-files"><i data-toggle="tooltip" title="<?php echo $lang['Share_files']; ?>" class="fa fa-share-alt"></i></button></li>
                                        <?php } if ($rwgetRole['mail_files'] == '1') { ?>
                                            <li><button class="rows_selected btn btn-primary btn-sm" id="mailFiles" data-toggle="modal" data-target="#mail-selected-files"><i data-toggle="tooltip" title="<?php echo $lang['mail_files']; ?>" class="fa fa-envelope-o"></i></button></li>
                                        <?php } if ($rwgetRole['pdf_download'] == '1') { ?>
                                            <li><button class="rows_selected btn btn-primary btn-sm" id="downloadcheckedfile" data-toggle="modal" data-target="#downloadfile"><i class="ti-import" data-toggle="tooltip" title="<?php echo $lang['download_selected_file']; ?>"></i></button></li>
                                        <?php } ?>
                                    </ul>
                                <?php } ?>
                            </td>
                        </tr>

                    </table>
                </div>
                <?php
                //echo $subString="&limit=$limit";

                if ($limit && $start) {
                    $subString = "&start=$start&limit=$limit";
                    $queryString = str_replace($subString, "", $queryString);
                } elseif ($limit) {
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
            </div>

        <?php } else {
            ?>

            <table class="table table-striped table-bordered m-t-20">
                <thead>
                    <tr>
                        <th><?php echo $lang['Sr_No']; ?></th>
                        <th><?php echo $lang['File_Name']; ?></th>
                        <th><?php echo $lang['File_Size']; ?></th>
                        <th><?php echo $lang['No_of_Pages']; ?></th>
                        <th><?php echo $lang['Actions']; ?></th>
                    </tr></thead>
                <tr>
                    <td class="text-center" colspan="5">
                        <strong class="text-danger"><i class="ti-face-sad text-pink"></i> <?php echo $lang['Who0ps!_No_Records_Found']; ?></strong>
                    </td>
                </tr>
                <?php
            }
            ?>
        </table>

        <?php
    }
    ?>


</html>
<script src="assets/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
<!--for multiselect-->
<script type="text/javascript" src="assets/plugins/multiselect/js/jquery.multi-select.js"></script>
<script src="assets/js/jquery.core.js"></script>
<script type="text/javascript" src="assets/multi_function_script.js"></script>
<?php //require 'file-action-html.php';      ?>
<?php require 'file-movement.php'; ?>
<?php require 'file-action-php.php'; ?>
<script type="text/javascript">
 $(".select2").selectpicker();
</script>
<?php
if (isset($_POST['customizedsearch'])) {
    if (!isset($_POST['token'])) {
        header('location:access-denied.html');
        exit();
    }
    $childName = preg_replace("/[^A-Za-z0-9 ]/", "", $_POST['id']);
    $childName = mysqli_real_escape_string($db_con, $childName);
    $fields = $_POST['my_multi_select1'];
    $keywordids = implode(',', $fields);

    $flag = 0;
    if (!empty($childName)) {
        $reset = mysqli_query($db_con, "delete from tbl_advance_search_userwise where sl_id='$childName' and user_id='" . $_SESSION['cdes_user_id'] . "'");
    }
    if (!empty($keywordids)) {
        $metaNames = array();

        if (!empty($childName)) {
            //check meta data assigned or not
            $match = mysqli_query($db_con, "select * from tbl_advance_search_userwise where sl_id='$childName' and metadata_ids in($keywordids)") or die('Error:' . mysqli_error($db_con));
            if (mysqli_num_rows($match) <= 0) {
                //assign meta data
                mysqli_set_charset($db_con, "utf8");
                $create = mysqli_query($db_con, "insert into tbl_advance_search_userwise (`metadata_ids`, `sl_id`, user_id) values('$keywordids','$childName', '$_SESSION[cdes_user_id]')") or die('Error' . mysqli_error($db_con));
                if (isset($sqId) && !empty($sqId)) {
                    $updatesavequery = mysqli_query($db_con, "UPDATE `query` SET `metadata_ids`='$keywordids' where id='$sqId'");
                }
                // find meta data details
                $metan = mysqli_query($db_con, "select * from tbl_metadata_master where id='$keywordids'");
                while ($rwMetan = mysqli_fetch_assoc($metan)) {
                    $metaNames[] = $rwMetan['field_name'];
                }
                $flag = 1;
                $sl_id = $childName;
            } else {
                $sl_id = $childName;
            }
        }

        if ($flag == 1) {
            $metaDataNames = implode(",", $metaNames);
            mysqli_set_charset($db_con, "utf8");
            $metaNames = implode(",", $metaNames);
            $strgeName = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id = '$sl_id'");
            $rwstrgeName = mysqli_fetch_assoc($strgeName);
            $storageName = $rwstrgeName['sl_name'];
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`sl_id`, `action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]', '$selectedStorage','Customized column','$date','$host', 'Table column customized Keyword($metaDataNames)  added as column on $storageName folder')") or die('error : ' . mysqli_error($db_con));

            echo '<script>taskSuccess("' . basename($_SERVER[REQUEST_URI]) . '","' . $lang['Metadata_Assigned'] . '");</script>';
        } else {
            echo '<script>taskFailed("' . basename($_SERVER[REQUEST_URI]) . '","' . $lang['Metadata_failed'] . '");</script>';
        }
    } else {
        echo '<script>taskSuccess("' . basename($_SERVER[REQUEST_URI]) . '","' . $lang['nmdta'] . '");</script>';
    }
    mysqli_close($db_con);
}
?>
<?php
if (isset($_POST['savqry'], $_POST['token'])) {
    $query_name = preg_replace("/[^\w$\x{0080}-\x{FFFF}!(){}+=~@%&#.: -]+/u", "", $_POST['qry_name']);
    $url = mysqli_real_escape_string($db_con, $_POST['url']);
    mysqli_set_charset($db_con, "utf8");
    $chkquery = mysqli_query($db_con, "SELECT * FROM query WHERE query_name='$query_name' and user_id='" . $_SESSION['cdes_user_id'] . "'");
    if (mysqli_num_rows($chkquery) < 1) {
        mysqli_set_charset($db_con, "utf8");
        $metavalue = mysqli_query($db_con, "SELECT metadata_ids FROM tbl_advance_search_userwise WHERE sl_id='$selectedStorage' and user_id='" . $_SESSION['cdes_user_id'] . "'");
        $rwmetavalue = mysqli_fetch_assoc($metavalue);
        $rwmetavalues = $rwmetavalue['metadata_ids'];
        $uri_query = mysqli_query($db_con, "INSERT INTO query SET url='$url',query_name='$query_name', sl_id='" . $selectedStorage . "', user_id='" . $_SESSION['cdes_user_id'] . "', metadata_ids='$rwmetavalues'") or die('error metasearch : ' . mysqli_error($db_con));
        if ($uri_query) {
            $insertedId = mysqli_insert_id($db_con);
            $url = $url . "&sq=" . base64_encode(urlencode($insertedId));
            $updatequery = mysqli_query($db_con, "UPDATE query SET url='$url' where id='$insertedId'");
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Query saved.','$date','$host','Keyword search query $query_name saved.')") or die('error : ' . mysqli_error($db_con));
            echo'<script>taskSuccess("' . basename($_SERVER[REQUEST_URI]) . '","' . $lang['Query_Successfully_Saved'] . '");</script>';
        }
    } else {
        echo'<script>taskFailed("' . basename($_SERVER[REQUEST_URI]) . '","' . $lang['query_exist'] . '");</script>';
    }
}
?>
<script >
    $('document').ready(function () {

        var is_protected = "<?php echo $rwFolder['is_protected']; ?>";
        $("#selected_lock_folder").prop('disabled', true);
        $("#selected_unlock_folder").prop('disabled', true);
        $("#selected_update_fol_pass").prop('disabled', true);
        if (is_protected == 1) {
            $("#unlock_fol").show();
            $("#isprotected").hide();
            $("#update_fol_pass").show();
            $("#copy_fol").hide();
            $("#move_fol").hide();
            $("#share_fol").hide();
            $(".isprotected").hide();
            $(".isprotectedcheckout").show();

        } else {
            $("#unlock_fol").hide();
            $("#lock_fol").show();
            $("#update_fol_pass").hide();
            $("#copy_fol").show();
            $("#move_fol").show();
            $("#share_fol").show();
            $(".isprotectedcheckout").hide();

        }
    });
</script>