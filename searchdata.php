<!DOCTYPE html>
<html>
    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" />
    <link href="assets/plugins/multiselect/css/multi-select.css"  rel="stylesheet" type="text/css" />
    <!-- Plugin Css-->
    <link rel="stylesheet" href="assets/plugins/magnific-popup/css/magnific-popup.css" />
    <?php
    set_time_limit(0);
    require_once './loginvalidate.php';
    require_once './application/pages/head.php';
    require_once './application/pages/function.php';
	require_once './classes/fileManager.php';
	$fileManager = new fileManager();

   
    ?>
    <?php
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $slid = base64_decode(urldecode($_GET['id']));
        $folder = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid'");
    } else {
        $folder = mysqli_query($db_con, "select * from tbl_storage_level where sl_depth_level='0'");
    }
    
    $rwFolder = mysqli_fetch_assoc($folder);
    $slid = $rwFolder['sl_id'];
    $parentid = $rwFolder['sl_parent_id'];
    $rwFolder['is_protected'];
 
    $sllid = "select * from tbl_storage_level where sl_id = '$slid'";
    $sllid_run = mysqli_query($db_con, $sllid) or die("error:" . mysqli_errno($db_con));
    $namesl = mysqli_fetch_assoc($sllid_run);

    $result = "SELECT count(*) as total from tbl_document_master where doc_name = '$namesl[sl_name]'";
    $sql_run = mysqli_query($db_con, $result) or die("error:" . mysqli_errno($db_con));
    $data = mysqli_fetch_assoc($sql_run);
    $data['total'];

$exportOcExtn = array('pdf', 'txt', 'jpeg', 'jpg', 'png');
    ?>
    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/jstree/style.css" rel="stylesheet" type="text/css" />
    <body class="fixed-left">
        <!-- Begin page -->
        <div id="wrapper">
            <!-- Top Bar Start -->
            <?php require_once './application/pages/topBar.php'; ?>
            <!-- Top Bar End -->
            <!-- ========== Left Sidebar Start ========== 1001/10556/00959 12/12/2011 14:33:58-->

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
                            <?php
                            mysqli_set_charset($db_con, "utf8");
                            $perm = mysqli_query($db_con, "select * from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]' group by sl_id");
                            $rwPerm = mysqli_fetch_assoc($perm);
                            $slperm = $rwPerm['sl_id'];
                            $sllevel = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slperm'");
                            $rwSllevel = mysqli_fetch_assoc($sllevel);
                            $level = $rwSllevel['sl_depth_level'];
                            ?>
                            <ol class="breadcrumb">
                                <li><a href="storage?id=<?php echo urlencode(base64_encode($slperm)); ?>"><?php echo $lang['Storage_Manager']; ?></a></li>

                                <?php
                                parentLevel($slid, $db_con, $slpermIdes, $level);

                                function parentLevel($slid, $db_con, $slperm, $level) {
                                    $flag = 0;
                                    $slPermIds = explode(',', $slperm);
                                    if (in_array($slid, $slperm)) {
                                        $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid'") or die('Error' . mysqli_error($db_con));
                                        $rwParent = mysqli_fetch_assoc($parent);

                                        if ($level < $rwParent['sl_depth_level']) {
                                            parentLevel($rwParent['sl_parent_id'], $db_con, $slperm, $level);
                                        }
                                        $flag = 1;
                                    } else {
                                        $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid' and sl_parent_id='$slperm'") or die('Error' . mysqli_error($db_con));
                                        if (mysqli_num_rows($parent) > 0) {

                                            $rwParent = mysqli_fetch_assoc($parent);
                                            if ($level < $rwParent['sl_depth_level']) {
                                                parentLevel($rwParent['sl_parent_id'], $db_con, $slperm, $level);
                                            } $flag = 1;
                                            $flag = 1;
                                        } else {
                                            $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid'") or die('Error' . mysqli_error($db_con));
                                            $rwParent = mysqli_fetch_assoc($parent);
                                            $getparnt = $rwParent['sl_parent_id'];
                                            if ($level <= $rwParent['sl_depth_level']) {
                                                parentLevel($getparnt, $db_con, $slperm, $level);
                                                $flag = 1;
                                            } else {
                                                $flag = 0;
                                                //header('Location: ./index.php');
                                                // header("Location: ./storage_test?id=" . urlencode(base64_encode($slperm)));
                                            }
                                        }
                                    }
                                    if ($flag == 1) {
                                        echo '<li class="active"><a href="storage?id=' . urlencode(base64_encode($rwParent['sl_id'])) . '">' . $rwParent['sl_name'] . '</a></li>';
                                    }
                                }
                                ?>
                                <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                            </ol>

                        </div>

                        <div class="row">
                            <div class="box box-primary">

                                <div class="box-body">
                                    <div class="col-md-3" style="overflow: auto;">
                                        <div class="card-box">
                                            <div id="basicTree">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-9" style="padding-left: 0;">
                                        <form>
                                            <?php
                                            for ($j = 0; $j < count($_GET['searchText']); $j++) {
                                                ?>
                                                <div class="form-group row numid-<?= $j; ?> " id="multiselect">
                                                    <div class="col-md-3">

                                                        <select  class="form-control select2" id="my_multi_select1" name="metadata[]" required>
                                                            <option selected disabled value=""><?php echo $lang['Select_Metadata']; ?></option>
                                                            <option value="old_doc_name" <?php
                                                            if ($_GET['metadata'][$j] == "old_doc_name") {
                                                                echo'selected';
                                                            }
                                                            ?>><?php echo $lang['FileName']; ?></option>
                                                            <option value="noofpages"   <?php
                                                            if ($_GET['metadata'][$j] == "noofpages") {
                                                                echo'selected';
                                                            }
                                                            ?>><?php echo $lang['No_Of_Pages']; ?></option>
                                                                    <?php
                                                                    $metadatacount = 2;
                                                                    $arrarMeta = array();
                                                                    $metas = mysqli_query($db_con, "select * from tbl_metadata_to_storagelevel where sl_id='$slid'");
                                                                    while ($metaval = mysqli_fetch_assoc($metas)) {
                                                                        array_push($arrarMeta, $metaval['metadata_id']);
                                                                    }
                                                                    $meta = mysqli_query($db_con, "select * from tbl_metadata_master order by field_name asc");
                                                                    while ($rwMeta = mysqli_fetch_assoc($meta)) {
                                                                        if (in_array($rwMeta['id'], $arrarMeta)) {
                                                                            if ($rwMeta['field_name'] != 'filename') {
                                                                                if ($_GET['metadata'][$j] == $rwMeta['field_name']) {
                                                                                    echo '<option value="' . $rwMeta['field_name'] . '" selected>' . str_replace("_", " ", $rwMeta['field_name']) . '</option>';
                                                                                } else {
                                                                                    echo '<option value="' . $rwMeta['field_name'] . '">' . str_replace("_", " ", $rwMeta['field_name']) . '</option>';
                                                                                }
                                                                                $metadatacount++;
                                                                            }
                                                                        }
                                                                    }
                                                                    //$metadatacount = $metadatacount - count($_GET['metadata']);
                                                                    ?>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <select class="form-control select2" name="cond[]" required>
                                                            <option disabled selected style="background: #808080; color: #121213;"><?php echo $lang['Slt_Condition']; ?></option>
                                                            <option value="Equal" <?php
                                                            if (isset($_GET['cond'][$j]) && !empty($_GET['cond'][$j]) && $_GET['cond'][$j] == 'Equal') {
                                                                echo'selected';
                                                            }
                                                            ?>><?php echo $lang['Equal']; ?></option>
                                                            <option value="Contains" <?php
                                                            if (isset($_GET['cond'][$j]) && !empty($_GET['cond'][$j]) && $_GET['cond'][$j] == 'Contains') {
                                                                echo'selected';
                                                            }
                                                            ?>><?php echo $lang['Contains']; ?></option>
                                                            <option value="Like" <?php
                                                            if (isset($_GET['cond'][$j]) && !empty($_GET['cond'][$j]) && $_GET['cond'][$j] == 'Like') {
                                                                echo'selected';
                                                            }
                                                            ?>><?php echo $lang['Like']; ?></option>
                                                            <option value="Not Like" <?php
                                                            if (isset($_GET['cond'][$j]) && !empty($_GET['cond'][$j]) && $_GET['cond'][$j] == 'Not Like') {
                                                                echo'selected';
                                                            }
                                                            ?>><?php echo $lang['Not_Like']; ?></option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="text" class="form-control translatetext searchdata" name="searchText[]" required value="<?php echo xss_clean($_GET['searchText'][$j]); ?>" placeholder="<?php echo $lang['entr_srch_txt_hr']; ?>">
                                                    </div>
                                                    <input type="hidden" value="<?php echo $_GET['id']; ?>" name="id" />
                                                    <?php
                                                    if ($j == 0) {
                                                        ?>  
                                                        <button type="submit" class="btn btn-primary" id="search" onclick="functionHide();"><i class="fa fa-search"></i></button>
                                                        <a href="javascript:void(0)" class="btn btn-primary" id="addfields"><i class="fa fa-plus"></i></a>
                                                    <?php } else { ?>
                                                        <div onclick="incrementCount()"> <a href="javascript:void(0)" class="btn btn-primary " id="<?= $j; ?>" onclick="invisible(<?= $j; ?>)" ><i class='fa fa-minus-circle' aria-hidden='true'></i></a></div>

                                                    <?php } ?>
                                                </div>
                                            <?php } ?>
                                            <div class="row">
                                                <div class="contents col-lg-12"></div>
                                            </div> 
                                        </form>
                                        <?php
                                        if (isset($_GET['searchText'])) {

                                            $metadata = xss_clean($_GET['metadata']);
                                            $cond = xss_clean($_GET['cond']);
                                            $searchText = xss_clean($_GET['searchText']);
                                            // print_r($searchText);
                                            $slid = base64_decode(urldecode($_GET['id']));
                                            $limit = preg_replace("/[^0-9 ]/", "", $_GET['limit']);
                                            $start = preg_replace("/[^0-9 ]/", "", $_GET['start']);
                                            $queryString = $_SERVER["QUERY_STRING"];
                                            $searchText = mysqli_real_escape_string($db_con, $searchText);

                                            $res = searchAllDB($searchText, $cond, $metadata, $slid, $db_con, $rwgetRole, $limit, $start, $queryString, $lang, $rwFolder);


                                        }
                                        ?>	

                                        <?php
                                        $count = "SELECT count(*) as total from tbl_document_master where doc_name = '$namesl[sl_id]'";
                                        $count_run = mysqli_query($db_con, $count) or die("error:" . mysqli_errno($db_con));
                                        $count_data = mysqli_fetch_assoc($count_run);

                                        $contFile = mysqli_query($db_con, "select sum(doc_size) as total from tbl_document_master where doc_name = '$namesl[sl_id]'") or die('Error:' . mysqli_error($db_con));
                                        $rwcontFile = mysqli_fetch_assoc($contFile);
                                        $totalFSize = $rwcontFile['total'];
                                        $totalFSize = round($totalFSize / 1000, 2);
                                        ?>
                                        <?php



                                        //print_r($res);
                                        function searchAllDB($search, $cond, $metadata, $slid, $db_con, $rwgetRole, $limit, $start, $queryString, $lang, $rwFolder) {

                                            $table = "tbl_document_master";
                                            $sql_search = "select * from " . $table . " where flag_multidelete=1 and doc_name='$slid'";
                                            $sql_search_fields = Array();
                                            /* for ($i = 0; $i < count($_GET['searchText']); $i++) {
                                              if (preg_replace("/[^A-Za-z0-9_ ]/", "", $_GET['cond'][$i]) == 'Like') {
                                              $sql_search_fields[] = 'CONVERT(`' . preg_replace("/[^A-Za-z0-9_ ]/", "", $_GET['metadata'][$i]) . "` USING utf8) like('%" . preg_replace("/[^A-Za-z0-9-_. ]/", "", $_GET['searchText'][$i]) . "%')";
                                              } else if (preg_replace("/[^A-Za-z0-9_ ]/", "", $_GET['cond'][$i]) == 'Not Like') {
                                              $sql_search_fields[] = 'CONVERT(`' . preg_replace("/[^A-Za-z0-9_ ]/", "", $_GET['metadata'][$i]) . "` USING utf8) not like('%" . preg_replace("/[^A-Za-z0-9-_. ]/", "", $_GET['searchText'][$i]) . "%')";
                                              } else if (preg_replace("/[^A-Za-z0-9_ ]/", "", $_GET['cond'][$i]) == 'Contains') {
                                              $sql_search_fields[] = 'CONVERT(`' . preg_replace("/[^A-Za-z0-9_ ]/", "", $_GET['metadata'][$i]) . "` USING utf8) like('%" . preg_replace("/[^A-Za-z0-9-_. ]/", "", $_GET['searchText'][$i]) . "%')";
                                              } else if (preg_replace("/[^A-Za-z0-9_ ]/", "", $_GET['cond'][$i]) == 'Equal') {
                                              $sql_search_fields[] = "`" . preg_replace("/[^A-Za-z0-9_ ]/", "", $_GET['metadata'][$i]) . "` ='" . preg_replace("/[^A-Za-z0-9-_. ]/", "", $_GET['searchText'][$i]) . "'";
                                              }
                                              } */
                                            for ($i = 0; $i < count($_GET['searchText']); $i++) {
                                                if ($_GET['cond'][$i] == 'Like') {
                                                    $sql_search_fields[] = ' CONVERT(`' . $_GET['metadata'][$i] . "` USING utf8) like('%" . $_GET['searchText'][$i] . "%')";
                                                } else if ($_GET['cond'][$i] == 'Not Like') {
                                                    $sql_search_fields[] = ' CONVERT(`' . $_GET['metadata'][$i] . "` USING utf8) not like('%" . $_GET['searchText'][$i] . "%')";
                                                } else if ($_GET['cond'][$i] == 'Contains') {
                                                    $sql_search_fields[] = ' CONVERT(`' . $_GET['metadata'][$i] . "` USING utf8) like('%" . $_GET['searchText'][$i] . "%')";
                                                } else if ($_GET['cond'][$i] == 'Equal') {
                                                    $sql_search_fields[] = "`" . $_GET['metadata'][$i] . "` ='" . $_GET['searchText'][$i] . "'";
                                                }
                                            }




                                            $sql_search .= ' and (';
                                            $sql_search .= implode(" and", $sql_search_fields);
                                            $sql_search .= ')';
                                            //echo $sql_search;

                                            
                                            ?>

                                            <?php
                                            $totalrowSql = $sql_search;
                                            $foundnumQuery = mysqli_query($db_con, $totalrowSql);
                                            $foundnum = mysqli_num_rows($foundnumQuery);
                                            if ($foundnum > 0) {
                                                 $limit =  preg_replace("/[^0-9 ]/", "", $_GET['limit']);
                                                if (is_numeric($limit)) {
                                                    $per_page = $limit;
                                                } else {
                                                    $per_page = 10;
                                                }
                                               $start =  preg_replace("/[^0-9 ]/", "", $_GET['start']);
                                                $start = isset($start) ? ($start > 0) ? $start : 0 : 0;
                                                $max_pages = ceil($foundnum / $per_page);
                                                if (!$start) {
                                                    $start = 0;
                                                }

                                                $rs3 = mysqli_query($db_con, $sql_search);
                                                ?>
                                                <div class="box-body">
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
                                                    <div class="pull-right record">
                                                        <label><?php echo $start + 1 ?> <?php echo $lang['To']; ?> <?php
                                                            if (($start + $per_page) > $foundnum) {
                                                                echo $foundnum;
                                                            } else {
                                                                echo ($start + $per_page);
                                                            }
                                                            ?>  <span> <?php echo $lang['Ttal_Rcrds']; ?> : <?php echo $foundnum; ?></span></label>
                                                    </div>
                                                </div>
                                                <?php
                                                $table = "tbl_document_master";
                                                $sql_search = "select * from " . $table . " where flag_multidelete=1 and doc_name=";
                                                $sql_search_fields = Array();
                                                ?>
                                                <table class="table table-striped table-bordered js-sort-table">
                                                    <thead>
                                                        <tr>
                                                            <th width="51px"><div class="checkbox checkbox-primary m-r-15"><input  type="checkbox" class="checkbox-primary" id="select_all"> <label for="checkbox6"> <strong><?php echo $lang['All']; ?></strong></label> </th>
                                                            <th><?php echo $lang['File_Name']; ?></th>
                                                            <th><?php echo $lang['File_Size']; ?></th>
                                                            <th><?php echo $lang['No_of_Pages']; ?></th>
                                                            <th><?php echo $lang['Upld_By']; ?></th>
                                                            <th><?php echo $lang['Upld_Date']; ?></th>
                                                            <th><?php echo $lang['Actions']; ?></th>
                                                        </tr>
                                                    </thead>
                                                    <?php
                                                    for ($i = 0; $i < count($_GET['searchText']); $i++) {
                                                        if ($_GET['cond'][$i] == 'Like') {
                                                            $sql_search_fields[] = 'CONVERT(`' . xss_clean($_GET['metadata'][$i]) . "` USING utf8) like('%" . xss_clean($_GET['searchText'][$i]) . "%')";
                                                        } else if ($_GET['cond'][$i] == 'Not Like') {
                                                            $sql_search_fields[] = 'CONVERT(`' . xss_clean($_GET['metadata'][$i]) . "` USING utf8) not like('%" . xss_clean($_GET['searchText'][$i]) . "%')";
                                                        } else if ($_GET['cond'][$i] == 'Contains') {
                                                            $sql_search_fields[] = 'CONVERT(`' . xss_clean($_GET['metadata'][$i]) . "` USING utf8) like('%" . xss_clean($_GET['searchText'][$i]) . "%')";
                                                        } else if ($_GET['cond'][$i] == 'Equal') {
                                                            $sql_search_fields[] = "`" . xss_clean($_GET['metadata'][$i]) . "` ='" . xss_clean($_GET['searchText'][$i]) . "'";
                                                        }
                                                    }
                                                    $sql_search .= "'$slid'";
                                                    $sql_search .= ' and (';
                                                    $sql_search .= implode(" and ", $sql_search_fields);
                                                    $sql_search .= ')';
                                                    $sql_search .= "limit $start,$per_page";
                                                    mysqli_set_charset($db_con, "utf8");





                                                    $rs3 = mysqli_query($db_con, $sql_search);
                                                    //$out .= mysqli_num_rows($rs3)."\n ok";
                                                    echo'<tbody>';
                                                    if (mysqli_num_rows($rs3) > 0) {

                                                        $n = 1;
                                                        $n += $start;
                                                        while ($rw = mysqli_fetch_assoc($rs3)) {

                                                            if ($rw['doc_name'] == $slid) {
																
                                                                 if ($rwgetRole['doc_weeding_out'] == '1' && $rwgetInfo['retention_feature_enable'] == '1') {
                                                                    if (isset($rw['retention_period']) && !empty($rw['retention_period'])) {
                                                                        $wedDate = $rw['retention_period'];
                                                                        $weedDate = strtotime($wedDate);
                                                                        $todate = strtotime(date("Y-m-d H:i:s"));
                                                                        if ($todate >= ($weedDate - 30 * 24 * 60 * 60)) {
                                                                            //if ($weedDate <= ($todate)) {
                                                                         
                                                                           $weed = '#FFAAAA';
                                                                            $weedTile = $lang['retention_time_msg'] . ' : ' . date('d-m-Y H:i:s', $weedDate);
                                                                        }
                                                                    } else {
                                                                         // echo 'deve';
                                                                        $weed = '';
                                                                        $weedTile = '';
                                                                    }
                                                                }
                                                                if ($rwgetRole['doc_expiry_time'] == '1' && $rwgetexpInfo['exp_feature_enable'] == '1') {
                                                                    if (isset($rw['doc_expiry_period']) && !empty($rw['doc_expiry_period'])) {
                                                                        $docexpDate = $rw['doc_expiry_period'];
                                                                        $docexpDate = strtotime($docexpDate);
                                                                        $todaydate = strtotime(date("Y-m-d H:i:s"));
                                                                        if ($todaydate >= ($docexpDate - 30 * 24 * 60 * 60)) {
                                                                            //if ($weedDate <= ($todate)) {
                                                                            $docexpcolor = '#f5ca7f';
                                                                            $expiryTitle = $lang['expiry_time_msg'] . ' : ' . date('d-m-Y H:i:s', $docexpDate);
                                                                        }
                                                                    } else {
                                                                        $docexpcolor = '';
                                                                        $expiryTitle = '';
                                                                    }
                                                                }
																
																$checkoutcolor = ($rw['checkin_checkout'] == 0)?'#b7f1a3':'';
																$checkoutTitle = ($rw['checkin_checkout'] == 0)?'File is checkout!':'';
                                                              
																
                                                                $docExpRetentionPeriod = "#a6ecf7";
                                                                $docExpRetentionPrdtitle = $expiryTitle . ' ' . $lang['and'] . $weedTile;
                                                                $shareDid = mysqli_query($db_con, "select doc_ids from tbl_document_share where doc_ids= '$rw[doc_id]'") or die("Error: " . mysqli_error($db_con));
                                                                $shreCount = mysqli_num_rows($shareDid);
                                                                
                                                                $subscribeid = mysqli_query($db_con, "select * from tbl_document_subscriber where subscribe_docid= '$rw[doc_id]' and subscriber_userid='" . $_SESSION['cdes_user_id'] . "'") or die("Error: " . mysqli_error($db_con));
                                                                $subsCountId = mysqli_num_rows($subscribeid);
                                                                
                                                                ?>
                                                                <?php if ((!empty($weed) && !empty($weedTile)) && (!empty($docexpcolor) && !empty($expiryTitle))) { ?>
                                                                    <tr class="gradeX" style="background-color: <?php echo $docExpRetentionPeriod; ?> !important" data-toggle="tooltip" title="<?php echo $docExpRetentionPrdtitle; ?>">
                                                                    <?php } else if (!empty($docexpcolor) && !empty($expiryTitle)) { ?>
                                                                    <tr class="gradeX" style="background-color: <?php echo $docexpcolor; ?> !important" data-toggle="tooltip" title="<?php echo $expiryTitle; ?>">         
                                                                    <?php } else if (!empty($weed) && !empty($weedTile)) { ?>
                                                                    <tr class="gradeX" style="background-color: <?php echo $weed; ?> !important" data-toggle="tooltip" title="<?php echo $weedTile; ?>">         
                                                                   <?php } else if (!empty($checkoutcolor) && !empty($checkoutTitle)) { ?>
                                                                    <tr class="gradeX" style="background-color: <?php echo $checkoutcolor; ?> !important; color:#000;" data-toggle="tooltip" title="<?php echo $checkoutTitle; ?>">         
                                                                    <?php } else { ?>
                                                                        
                                                                    <tr class="gradeX">           
                                                                    <?php } ?>
                                                                    <td>
                                                                        <div class="checkbox checkbox-primary m-r-15"> <input  type="checkbox" class="checkbox-primary emp_checkbox" data-doc-id="<?php echo $rw['doc_id']; ?>"><label for="checkbox6"> <?php echo $n . '.'; ?> </label></div>
                                                                        <?php
                                                                        if ($shreCount > 0) {
                                                                            ?>
                                                                            <span class="md md-share" style="font-size: 15px; color: #193860;" title="Shared document"></span>
                                                                        <?php } ?>
                                                                        <?php
                                                                        if ($subsCountId > 0) {
                                                                            ?>
                                                                            <span class="fa fa-bell-o" style="font-size: 15px; color: #193860;" title="Subscribe document"></span>
                                                                        <?php } ?>
                                                                    
                                                                    </td>
                                                                     <td> <?php if(file_exists('thumbnail/'.base64_encode($rw['doc_id']).'.jpg')){ ?><div> <img class="thumb-image" src="thumbnail/<?=base64_encode($rw['doc_id'])?>.jpg"> </div>
                                                                        <?php } echo $rw['old_doc_name']; ?>
                                                                    </td>
                                                                    <td><?php echo formatSizeUnits($rw['doc_size']); ?></td>
                                                                    <td><?php echo $rw['noofpages']; ?></td>
                                                                    <?php
                                                                    $userName = "SELECT * FROM tbl_user_master WHERE user_id = '$rw[uploaded_by]'";
                                                                    $userName_run = mysqli_query($db_con, $userName) or die("Error: " . mysqli_error($db_con));

                                                                    $rwuserName = mysqli_fetch_assoc($userName_run)
                                                                    ?>
                                                                    <td><?php echo $rwuserName['first_name'] . " " . $rwuserName['last_name']; ?></td>
                                                                    <td><?php echo $rw['dateposted']; ?></td>
                                                                    <td>
																	<li class="dropdown top-menu-item-xs">
                                                                    <?php
                                                                    $checkfileLockqry = mysqli_query($db_con, "SELECT * FROM `tbl_locked_file_master` WHERE doc_id='$rw[doc_id]' and is_active='1' and user_id='$_SESSION[cdes_user_id]'");
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
                                                                                     <?php if(($rwFolder['is_protected']==0 || $_SESSION['pass'] == $rwFolder['password']) && (isFolderReadable($db_con, $slid))){ ?>
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
                                                                                        <li> <a href="javascript:void(0)"  data-toggle="modal" data-target="#filemeta-modal"  onclick="getFileMetaData(<?php echo $file_row['doc_id'] ?>,<?php echo $file_row['doc_name'] ?>);" data="metaData<?php echo $n; ?>" id="viewMeta"><i class="fa fa-eye"></i> <?php echo $lang['View_MetaData']; ?></a></li>
                                                                                        <li> <a href="javascript:void(0)"  data-toggle="modal" data-target="#con-close-modal-history" onclick="return getFileHistory(<?php echo $file_row['doc_id'] ?>,<?php echo  preg_replace("/[^0-9 ]/", "",base64_decode(urldecode($_GET['id']))); ?>)"><i class="fa fa-history"></i> <?php echo $lang['history']; ?></a></li>

                                                                                    <?php } if ($rwgetRole['file_review'] == '1') { ?>
                                                                                        <?php if ((strtolower($file_row['doc_extn']) == 'pdf' || strtolower($file_row['doc_extn']) == 'doc' || strtolower($file_row['doc_extn']) == 'docx')) { ?>
                                                                                            <li> <a href="reviewers?i=<?php echo urlencode(base64_encode($_SESSION['cdes_user_id'])) ?>&doc_Id=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>" id="moveTorw" data="<?php echo $file_row['doc_id']; ?>"> <i class="fa fa fa-send"></i> <?php echo $lang['Review']; ?></a></li><?php } ?>

                                                                                    <?php } if ($rwgetRole['workflow_initiate_file'] == '1' || $rwgetRole['initiate_file'] == '1') { ?>
                                                                                        <li> <a href="javascript:void(0)" data-toggle="modal" data-target="#assign-workflow" id="moveToWf" data="<?php echo $file_row['doc_id']; ?>"> <i class="fa fa-plus"></i> <?php echo $lang['Workflow']; ?></a></li>

                                                                                        <?php } if (strtolower($file_row['doc_extn']) == 'pdf' && $rwgetRole['splitpdf'] == '1') { ?>
                                                                                        <li><a href="viewer?id=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&i=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>&sp=<?php echo urlencode(base64_encode('1')); ?>" id="fancybox-inner" class="pdfview" target="_blank"><i class="fa fa-sign-out"></i> <?php echo $lang['splitpdf']; ?></a></li>
                                                                                        <?php
                                                                                    } ?>

                                                                                    <?php if (strtolower($file_row['doc_extn']) == 'pdf' && $rwgetRole['pdf_annotation']) { ?>
                                                                                <li class="isprotected"> <a href="anott/add-delete-page?id1=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>&pn=1" > <i class="fa fa-plus"></i> <?php echo $lang['add_delete_pages']; ?></a></li>
                                                                            <?php } ?>
                                                                                    <?php if ($rwgetRole['checkin_checkout'] == '1') { ?>
                                                                                        <li><a href="javascript:void(0)" id="checkout" data="<?php echo $file_row['doc_id']; ?>"><i class="fa fa-sign-out"></i> <?php echo $lang['Chk_Out']; ?></a></li>
                                                                                        <?php } if ($rwgetRole['subscribe_document'] == '1') {
                                                                                ?>
                                                                                <li class="isprotected"><a href="javascript:void(0)"  id="singlesubscribe" data-toggle="modal" data-target="#subscribe" data="<?php echo $file_row['doc_id']; ?>"><i class="fa fa-bell-o"></i> <?php echo $lang['subscribe']; ?></a></li>
                                                                                     <?php
																					} if ($rwgetRole['export_ocr'] == '1'  && in_array($file_row['doc_extn'],$exportOcExtn)) {
																							?>
																							<li><a href="javascript:void(0)" data-toggle="modal" data-target="#exportocr-modal" id="exportocr" data="<?php echo $file_row['doc_id']; ?>"><i class="fa fa-download"></i><?php echo $lang['exportocr']; ?></a></li>
																							<?php
																						
																						}if ($rwgetRole['file_delete'] == '1') {
																						?>
                                                                                        <li><a href="javascript:void(0)" data-toggle="modal" data-target="#con-close-modal2" id="removeRow" data="<?php echo $file_row['doc_id']; ?>"><i class="fa fa-trash"></i> <?php echo $lang['Delete']; ?> </a></li>
                                                                                        <?php
                                                                                    }
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
                                                                                 <?php if(($rwFolder['is_protected']==0 || $_SESSION['pass'] == $rwFolder['password']) && (isFolderReadable($db_con, $slid))){ ?>
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
                                                                                    <li> <a href="javascript:void(0)" data-toggle="modal" data-target="#filemeta-modal"  onclick="getFileMetaData(<?php echo $file_row['doc_id']; ?>,<?php echo $file_row['doc_name'] ?>);" data="metaData<?php echo $n; ?>" id="viewMeta"><i class="fa fa-eye"></i> <?php echo $lang['View_MetaData']; ?></a></li>
                                                                                    <li> <a href="javascript:void(0)"  data-toggle="modal" data-target="#con-close-modal-history" onclick="return getFileHistory(<?php echo $file_row['doc_id'] ?>,<?php echo  preg_replace("/[^0-9 ]/", "",base64_decode(urldecode($_GET['id']))); ?>)"><i class="fa fa-history"></i> <?php echo $lang['history']; ?></a></li>

                                                                                <?php } if ($rwgetRole['file_review'] == '1') { ?>
                                                                                    
                                                                                    <?php if ((strtolower($file_row['doc_extn']) == 'jpg' || strtolower($file_row['doc_extn']) == 'jpeg' || strtolower($file_row['doc_extn']) == 'png' || strtolower($file_row['doc_extn']) == 'pdf' || strtolower($file_row['doc_extn']) == 'doc' || strtolower($file_row['doc_extn']) == 'docx')) { ?>
                                                                                       
                                                                                        <li> <a href="reviewers?i=<?php echo urlencode(base64_encode($_SESSION['cdes_user_id'])) ?>&doc_Id=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>" id="moveTorw" data="<?php echo $file_row['doc_id']; ?>"> <i class="fa fa fa-send"></i> <?php echo $lang['Review']; ?></a></li>
                                                                                    
                                                                                    <?php } ?>

                                                                                <?php } if ($rwgetRole['workflow_initiate_file'] == '1' || $rwgetRole['initiate_file'] == '1') { ?>
                                                                                    <li> <a href="javascript:void(0)" data-toggle="modal" data-target="#assign-workflow" id="moveToWf" data="<?php echo $file_row['doc_id']; ?>"> <i class="fa fa-plus"></i> <?php echo $lang['Workflow']; ?></a></li>

                                                                                <?php } if (strtolower($file_row['doc_extn']) == 'pdf' && $rwgetRole['splitpdf'] == '1') { ?>
                                                                                    <li><a href="viewer?id=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&i=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>&sp=<?php echo urlencode(base64_encode('1')); ?>" id="fancybox-inner" class="pdfview" target="_blank"><i class="fa fa-sign-out"></i> <?php echo $lang['splitpdf']; ?></a></li>
                                                                                    <?php
                                                                                } ?>

                                                                               <?php if (strtolower($file_row['doc_extn']) == 'pdf' && $rwgetRole['pdf_annotation']) { ?>
                                                                                <li class="isprotected"> <a href="anott/add-delete-page?id1=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>&pn=1" > <i class="fa fa-plus"></i> <?php echo $lang['add_delete_pages']; ?></a></li>
                                                                            <?php } ?>
                                                                                <?php if ($rwgetRole['checkin_checkout'] == '1') { ?>
                                                                                    <li><a href="javascript:void(0)" id="checkout" data="<?php echo $file_row['doc_id']; ?>"><i class="fa fa-sign-out"></i> <?php echo $lang['Chk_Out']; ?></a></li>
                                                                                    <?php } if ($rwgetRole['subscribe_document'] == '1') {
                                                                                ?>
                                                                                <li class="isprotected"><a href="javascript:void(0)"  id="singlesubscribe" data-toggle="modal" data-target="#subscribe" data="<?php echo $file_row['doc_id']; ?>"><i class="fa fa-bell-o"></i> <?php echo $lang['subscribe']; ?></a></li>
                                                                                  <?php
											} if ($rwgetRole['export_ocr'] == '1' && in_array($file_row['doc_extn'],$exportOcExtn)) {
													?>
													<li><a href="javascript:void(0)" data-toggle="modal" data-target="#exportocr-modal" id="exportocr" data="<?php echo $file_row['doc_id']; ?>"><i class="fa fa-download"></i><?php echo $lang['exportocr']; ?></a></li>
													<?php
												
												}if ($rwgetRole['file_delete'] == '1') {
												?>
                                                                                    <li><a href="javascript:void(0)" data-toggle="modal" data-target="#con-close-modal2" id="removeRow" data="<?php echo $file_row['doc_id']; ?>"><i class="fa fa-trash"></i> <?php echo $lang['Delete']; ?> </a></li>
                                                                                    <?php
                                                                                 }
                                                                                 
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
                                                                </tr>
                                                                <!--tr>
                                                                    <td colspan="7">
                                                                        <div id="metaData<?php echo $n; ?>"  class="metadata">
                                                                            <?php
                                                                            $getMetaId = mysqli_query($db_con, "select * from tbl_metadata_to_storagelevel where sl_id = '$file_row[doc_name]'") or die('Error:gg' . mysqli_error($db_con));

                                                                            while ($rwgetMetaId = mysqli_fetch_assoc($getMetaId)) {

                                                                                $getMetaName = mysqli_query($db_con, "select * from tbl_metadata_master where id = '$rwgetMetaId[metadata_id]'") or die('Error:' . mysqli_error($db_con));

                                                                                while ($rwgetMetaName = mysqli_fetch_assoc($getMetaName)) {
                                                                                    $meta = mysqli_query($db_con, "select `$rwgetMetaName[field_name]` from tbl_document_master where doc_id='$file_row[doc_id]'");
                                                                                    $rwMeta = mysqli_fetch_assoc($meta);

                                                                                    if (!empty($rwMeta[$rwgetMetaName['field_name']])) {
                                                                                        if ($rwgetMetaName['field_name'] == 'noofpages' || $rwgetMetaName['field_name'] == 'filename') {
                                                                                            
                                                                                        } else {
                                                                                            echo "<label>" . $rwgetMetaName['field_name'] . "</label> : ";
                                                                                            if ($rwMeta[$rwgetMetaName['field_name']] != '0000-00-00 00:00:00') {

                                                                                                echo $rwMeta[$rwgetMetaName['field_name']];
                                                                                            }
                                                                                            echo " | ";
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }
                                                                            ?>
                                                                        </div>
                                                                    </td>
                                                                </tr-->
                                                                <?php
                                                                $n++;
                                                            }
                                                        }
                                                        echo '</tbody>';
                                                        ?>
                                                        <tr>
                                                            <td colspan="7">
                                                                 <?php if(($rwFolder['is_protected']==0 || $_SESSION['pass'] == $rwFolder['password']) && (isFolderReadable($db_con, $slid))){ ?>
                                                                <ul class="delete_export">
                                                                    <input type="hidden" name="slid" id="slid" value="<?php echo $slid; ?>">
                                                                    <input type="hidden" name="sty" id="sty" value="<?php echo  preg_replace("/[^0-9 ]/", "",$_GET['id']); ?>">
                                                                    <?php if ($rwgetRole['file_delete'] == '1') { ?>
                                                                    <li><button id="del_file" class="rows_selected btn btn-danger btn-sm" data-toggle="modal"  data-target="#del_send_to_recycle"><i data-toggle="tooltip" title="<?php echo $lang['Delete_files'] ?>" class="fa fa-trash-o"></i></button></li>
                                                                    <?php } if ($rwgetRole['export_csv'] == '1') { ?>
                                                                        <li><button class="btn btn-primary btn-sm" id="export4"  data-toggle="modal"  data-target="#multi-csv-export-model"><i data-toggle="tooltip" title="<?php echo $lang['Export_Data'] ?>" class="fa fa-download"></i></button></li>
                                                                    <?php } if ($rwgetRole['move_file'] == '1') { ?>
                                                                        <li><button id="move_multi" class="rows_selected btn btn-primary btn-sm" data-toggle="modal" data-target="#move-selected-files" > <i data-toggle="tooltip" title="<?php echo $lang['Mve_fles'] ?>" class="fa fa-share-square"></i></button></li>
                                                                    <?php } if ($rwgetRole['copy_file'] == '1') { ?>
                                                                        <li><button class="rows_selected btn btn-primary btn-sm" id="copyFiles" data-toggle="modal" data-target="#copy-selected-files" ><i data-toggle="tooltip" title="<?php echo $lang['Copy_files'] ?>" class="fa fa-copy"></i></button></li>
                                                                    <?php } if ($rwgetRole['share_file'] == '1') { ?>
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
                                                        <?php
                                                        mysqli_close($rs3);
                                                    } else {
                                                        ?>
                                                        <table class="table table-striped table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th><?php echo $lang['SNO']; ?></th>
                                                                    <th><?php echo $lang['File_Name']; ?></th>
                                                                    <th><?php echo $lang['File_Size']; ?></th>
                                                                    <th><?php echo $lang['No_of_Pages']; ?></th>
                                                                    <th><?php echo $lang['Upld_By']; ?></th>
                                                                    <th><?php echo $lang['Upld_Date']; ?></th>
                                                                    <th><?php echo $lang['Actions']; ?></th>
                                                                </tr>
                                                            </thead>
                                                            <tr><td colspan="7"><label style="font-weight:600; color:red; margin-left: 240px;"><i class="ti-face-sad text-pink"></i> <?php echo $lang['Who0ps!_No_Records_Found']; ?></label></td></tr>
                                                        </table>

                                                    <?php }
                                                    ?>
                                                </table>
                                                <?php
                                                //echo $subString="&limit=$limit";
                                                $queryString = $_SERVER["QUERY_STRING"];
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
                                                        if (!($start <= 0)) {
                                                            echo " <li><a href='?$queryString&start=$prev&limit=$limit'>" . $lang['Prev'] . "</a> </li>";
                                                        } else {
                                                            echo " <li class='disabled'><a href='javascript:(0)'>" . $lang['Prev'] . "</a> </li>";
                                                        }
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
                                                                echo " <li class='active'><a href='?$queryString&start=0&limit=$limit'>1</a></li> ";
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
                                                            echo "<li><a href='?$queryString&start=$next&limit=$limit'>" . $lang['Next'] . "</a></li>";
                                                        else
                                                            echo "<li class='disabled'><a href='javascript:(0)'>" . $lang['Next'] . "</a></li>";
                                                        ?>
                                                    </ul>
                                                    <?php
                                                }
                                                echo "</center>";
                                                ?>
                                            <?php } else {
                                                ?>
                                                <table class="table table-striped table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th><?php echo $lang['SNO']; ?></th>
                                                            <th><?php echo $lang['File_Name']; ?></th>
                                                            <th><?php echo $lang['File_Size']; ?></th>
                                                            <th><?php echo $lang['No_of_Pages']; ?></th>
                                                            <th><?php echo $lang['Upld_By']; ?></th>
                                                            <th><?php echo $lang['Upld_Date']; ?></th>
                                                            <th><?php echo $lang['Actions']; ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tr><td colspan="7"><label style="font-weight:600; color:red; margin-left: 240px;"><i class="ti-face-sad text-pink"></i> <?php echo $lang['Who0ps!_No_Records_Found']; ?></label></td></tr>
                                                </table>
                                            <?php }
                                            ?>
                                        <?php } ?>

                                    </div>

                                </div>				
                            </div>
                        </div> <!-- container -->

                    </div> <!-- content -->
                    <div id="del_send_to_recycle" class="modal fade"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog"> 
                            <div class="panel panel-color panel-danger"> 
                                <div class="panel-heading"> 
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                    <h2 class="panel-title" style="display:none;" id="hid"> <i class="fa fa-exclamation-triangle" aria-hidden="true"></i><?php echo $lang['Hre_msge']; ?></h2>
                                    <h2 class="panel-title" id="confirm"><?php echo $lang['Are_u_confirm']; ?></h2> 
                                </div>
                                <form method="post">
                                    <div class="panel-body">
                                        <span id="errmessage" style="display:none;"> <h5 class="text-alert"><?php echo $lang['Pls_slt_fles_for_Del']; ?></h5></span>
                                        <label class="text-danger" id="hide"><?php echo $lang['r_u_sue_wnt_to_Del_tis_Docs'] ?> </label>
                                    </div> 
                                    <div class="modal-footer">
                                        <input type="hidden" id="sl_id1" name="sl_id1">
                                        <input type="hidden" id="reDel" name="DelFile">
                                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?= $lang['Close']; ?></button> 
                                        <?php
                                        if ($rwgetRole['role_id'] == 1) {
                                            ?>
                                            <button type="submit" id="yes" name="Delmultiple" class="btn btn-danger" value="Yes"> <i class="fa fa-trash-o"></i> <?php echo $lang['Delete']; ?></button>
                                            <?php
                                        }
                                        ?>
                                        <button type="submit" id="no" name="Delmultiple" class="btn btn-danger"> <i class="fa fa-recycle"></i>
                                            <?php
                                            if ($rwgetRole['role_id'] == 1) {
                                                echo $lang['Recycle'];
                                            } else {
                                                echo $lang['Delete'];
                                            }
                                            ?>
                                        </button> 
                                    </div>
                                </form>

                            </div> 
                        </div>
                    </div>
                    <?php require_once './application/pages/footer.php'; ?>
                </div>
            </div>
            <!-- END wrapper -->
            <?php require_once './application/pages/footerForjs.php'; ?>       
        </div>
        <!--for multiselect-->
        <script src="assets/js/jquery.core.js"></script>
        <!---end-->
        <script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>
        <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
        <script src="assets/plugins/jstree/jstree.min.js"></script>
        <script src="assets/pages/jquery.tree.js"></script>  
        <script type="text/javascript" src="assets/multi_function_script.js"></script>
		<script>
			$('#basicTree').jstree({
				'core' : {
				  'data' :{
					'url' : function (node) {
					  return node.id === '#' ?
						'application/ajax/rootStorage.php?slid='+<?php echo $slid; ?>:
						'application/ajax/childStorage.php?slid='+<?php echo $slid; ?>;
					},
					'data' : function (node) {
					  return { 'id' : node.id };
					}
				  } 
				},
				'types': {
					'default': {
						'icon': 'md md-folder'
					},
					'file': {
						'icon': 'md md-my-library-books'
					}
				},
				'plugins': ['types']
				
			});

			$('#basicTree').bind("select_node.jstree", function (e, data) {
				var href = data.node.a_attr.href;
				window.location.href = href;
			});
		</script>
        <script>
            $(document).ready(function () {
                var max_fields = <?= $metadatacount; ?>; //maximum input boxes allowed
                var wrapper = $(".contents"); //Fields wrapper
                var add_button = $("#addfields"); //Add button ID
                var id =<?= $slid ?>;

                var x = 1; //initlal text box count
                $(add_button).click(function (e) { //on add input button click
                    e.preventDefault();

                    if (x < max_fields) { //max input box allowed
                        x++;
                        //text box increment
                        $.ajax({url: "application/ajax/addmultimetadataStoregefile?id=" + id, success: function (result) {
                                $(wrapper).append("<div class='col-lg-12' style='margin-bottom:17px'>" + result + "<button class='remove_field btn btn-primary' title='Remove'><i class='fa fa-minus-circle' aria-hidden='true'></i></a>" + "</div>"); //add input box


                            }});

                    } else
                    {
                        alert("<?php echo $lang['No_Mor_mta_dat_avlbl']; ?>");
                        $("#addfields").hide();
                    }
                });

                $(wrapper).on("click", ".remove_field", function (e) { //user click on remove text
                    e.preventDefault();
                    $(this).parent('div').remove();
                    x--;
                    $("#addfields").show();
                })
            });
            
            function invisible(id){
                $(".numid-"+id).remove();
            }
            
            </script>
        <?php require_once 'file-movement.php'; ?>
        <?php require_once 'file-action-js.php'; ?>
    </body>
</html>