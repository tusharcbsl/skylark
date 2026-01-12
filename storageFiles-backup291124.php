<?php
error_reporting(0);
//ini_set('display_errors', '1');
require_once './loginvalidate.php';


$perm = mysqli_query($db_con, "select sl_id from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]' group by sl_id");
$rwPerm = mysqli_fetch_assoc($perm);
$slperm = $rwPerm['sl_id'];

require_once './application/config/database.php';
require_once './application/config/validate_client_db.php'; //aggregate table ezeefile saas db con
require_once './application/pages/feature-enable-disable.php';
require_once './application/pages/function.php';
require_once './classes/fileManager.php';
require_once './classes/ftp.php';
$fileManager = new fileManager();


?>
<!DOCTYPE html>
<html>
    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" />
    <link href="assets/plugins/multiselect/css/multi-select.css"  rel="stylesheet" type="text/css" />
    <link href="assets/bootstrap-datetimepicker.css" rel="stylesheet" type="text/css" />
    <?php
    require_once './application/pages/head.php';
    

    if ($rwgetRole['create_storage'] == '0' && $rwgetRole['create_child_storage'] == '0' && $rwgetRole['upload_doc_storage'] == '0' && $rwgetRole['modify_storage_level'] == '0' && $rwgetRole['delete_storage_level'] == '0' && $rwgetRole['move_storage_level'] == '0' && $rwgetRole['copy_storage_level'] == '0') {
        header('Location: ./index');
        exit();
    }
    ?>

    <?php
    mysqli_set_charset($db_con, "utf8");
    if (isset($_GET['id']) && !empty($_GET['id'])) {
         $slid = base64_decode(urldecode(xss_clean($_GET['id'])));


        $folder = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid'");
    } else {
        $folder = mysqli_query($db_con, "select * from tbl_storage_level where sl_depth_level='0'");
    }
    $rwFolder = mysqli_fetch_assoc($folder);
    $slid = $rwFolder['sl_id'];
    $parentid = $rwFolder['sl_parent_id'];
    $sldepthlevel = $rwFolder['sl_depth_level'];
    $slName = $rwFolder['sl_name'];
	
	$exportOcExtn = array('pdf', 'txt', 'jpeg', 'jpg', 'png');
	
	/* $d = findParentfolder($slpermIdes, $slid, $db_con);
	 
	print_r($d); */
	 
	 //die();

    ?>
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
                            $perm = mysqli_query($db_con, "select * from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]' group by sl_id");
                            $rwPerm = mysqli_fetch_assoc($perm);
                            $slperm = $rwPerm['sl_id'];
                            mysqli_set_charset($db_con, "utf8");
                            $sllevel = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slperm'");
                            $rwSllevel = mysqli_fetch_assoc($sllevel);
                            $level = $rwSllevel['sl_depth_level'];
                            ?>
                            <ol class="breadcrumb">
                                <?php
                                parentLevel($slid, $db_con, $slpermIdes, $level, $lang);

                                function parentLevel($slid, $db_con, $slperm, $level, $lang) {
                                    $flag = 0;
                                    $slPermIds = explode(',', $slperm);
                                    if (in_array($slid, $slperm)) {
                                        $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid'") or die('Error' . mysqli_error($db_con));
                                        $rwParent = mysqli_fetch_assoc($parent);

                                        if ($level < $rwParent['sl_depth_level']) {
                                            parentLevel($rwParent['sl_parent_id'], $db_con, $slperm, $level, $lang);
                                        }
                                        $flag = 1;
                                    } else {
                                        $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid' and sl_parent_id='$slperm'") or die('Error' . mysqli_error($db_con));
                                        if (mysqli_num_rows($parent) > 0) {

                                            $rwParent = mysqli_fetch_assoc($parent);
                                            if ($level < $rwParent['sl_depth_level']) {
                                                parentLevel($rwParent['sl_parent_id'], $db_con, $slperm, $level, $lang);
                                            }
                                            $flag = 1;
                                        } else {
                                            $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid'") or die('Error' . mysqli_error($db_con));
                                            $rwParent = mysqli_fetch_assoc($parent);
                                            $getparnt = $rwParent['sl_parent_id'];
                                            if ($level <= $rwParent['sl_depth_level']) {
                                                parentLevel($getparnt, $db_con, $slperm, $level, $lang);
                                                $flag = 1;
                                            } else {
                                                $flag = 0;
                                                //header("Location: ./storage_test?id=" . urlencode(base64_encode($slperm)));
                                            }
                                        }
                                    }
                                    if ($flag == 1) {
                                        echo '<li class="active"><a href="storage?id=' . urlencode(base64_encode($rwParent['sl_id'])) . '">' . $rwParent['sl_name'] . '</a></li>';
                                    } else {
                                        echo '<li class="active"><a href="#">' . $lang['Storage_Manager'] . '</li>';
                                    }
                                }
                                ?>
                                <li> <a href="#" data-toggle="modal" data-target="#help-modal" id="helpview" data="43" title="<?= $lang['help']; ?>"><i class="fa fa-question-circle"></i> </a></li>
                                <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                            </ol>
                        </div>
                        <div class="row">
                            <div class="box box-primary" style="margin-bottom:155px;">
                                <div class="box-body">
                                    <div class="col-sm-3" style="overflow: auto;">
                                        <div class="card-box">
                                           <div id="basicTree">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-9">
                                        <div class="box-header with-border">
                                            <div class="col-sm-10">
                                                <form action="allstoragesearch" method="get">
                                                    <div class="col-sm-6" style="margin-left: 35%;">
                                                        <input type="text" id="srctxt" name="searchText" placeholder="<?php echo $lang['entr_srch_txt_hr']; ?>" class="form-control translatetext" required="required" />
                                                        <input type="hidden" value="<?php echo xss_clean($_GET['id']); ?>" name="id">
                                                    </div>  
                                                    <div class="col-md-1">
                                                        <a href="#" onclick="$(this).closest('form').submit()" class="btn btn-primary" title="<?php echo $lang['Search'] ?>"><i class="fa fa-search"></i></a>
                                                    </div>
                                                </form>
                                            </div>
											<?php if(isFolderReadable($db_con, $slid)){ // check is this folder shared with read only or not ?>
											
												<div class="btn-group pull-right col-sm-3" style="margin-right: -29px; margin-top: -38px;">

													<button type="button" class="btn btn-linkedin dropdown-toggle"  data-toggle="dropdown" ><?php echo $lang['Chse_Action']; ?></button>
													<button type="button" class="btn btn-linkedin dropdown-toggle" data-toggle="dropdown" aria-expanded="true"><span class="caret"></span> </button>
													<ul class="dropdown-menu storage" role="menu">
														<?php if($rwFolder['is_protected']==0 || $_SESSION['pass'] == $rwFolder['password']){ ?>
														
														<?php if ($rwgetRole['export_csv'] == '1') { ?>
															<li><a href="javascript:void(0)" data-toggle="modal" data-target="#export"><?php echo $lang['Export_Csv']; ?></a></li>
														<?php } ?>
															
														<?php if ($rwgetRole['bulk_download'] == '1') { ?>
															<li><a href="javascript:void(0)" data-toggle="modal" data-target="#bulkdownload"><?php echo $lang['Blk_Dwnld_Files']; ?></a></li>
														<?php } ?>
														<?php if ($rwgetRole['upload_doc_storage'] == '1') { ?>
															<li><a href="adddocument?id=<?php echo urlencode(base64_encode($slid)); ?>"><?php echo $lang['Upld_Docmnt']; ?></a></li>
														<?php } ?>
														<?php if ($rwgetRole['create_child_storage'] == '1') { ?>
															<li><a href="javascript:void(0)" data-toggle="modal" data-target="#con-close-modal1"><?php echo $lang['Crt_New_Cld']; ?></a></li>
														<?php } ?>
														<?php if ($rwgetRole['assign_metadata'] == '1') { ?>
															<li><a data-toggle="modal" data-target="#con-close-modal5"><?php echo $lang['Asgn_MetaData']; ?></a></li>
														<?php } ?>

														<?php if ($rwgetRole['modify_storage_level'] == '1') { 
															 if (($parentid != NULL || $parentid != '0') && $sldepthlevel != '0') {
															?>
															<li><a href="javascript:void(0)" data-toggle="modal" data-target="#con-close-modal-modify"><?php echo $lang['Modify_Storage']; ?></a></li>
														<?php } } ?>
														<?php if ($rwgetRole['delete_storage_level'] == '1') { 

															 if (($parentid != NULL || $parentid != '0') && $sldepthlevel != '0') {
															?>

															<li><a href="javascript:void(0)" data-toggle="modal" data-target="#con-close-modal-del"><?php echo $lang['Dlt_Storage']; ?></a></li>
														<?php } } ?>
														<?php
														if ($rwgetRole['move_storage_level'] == '1') {
															if (($parentid != NULL || $parentid != '0') && $sldepthlevel != '0') {
																
																?>
																<li><a href="javascript:void(0)" data-toggle="modal" data-target="#con-close-modal4" id="move_fol"><?php echo $lang['Move_Storage']; ?></a></li>
																<?php
															}
														}
														?>
														<?php
													  
														if ($rwgetRole['copy_storage_level'] == '1') {
															if (($parentid != NULL || $parentid != '0') && $sldepthlevel != '0') {
																?>
																<li><a href="javascript:void(0)" data-toggle="modal" data-target="#con-close-modal6" id="copy_fol"><?php echo $lang['Cpy_Storage']; ?></a></li>
																<?php
															}
														}
													  
														 if ($rwgetRole['share_folder'] == '1') { 
														if (($parentid != NULL || $parentid != '0') && $sldepthlevel != '0') {
														?>
															<li><a href="javascript:void(0)" data-toggle="modal" data-target="#con-close-modal7" id="share_fol"><?php echo $lang['share_folder']; ?></a></li>
													<?php } } 
														 } ?>
															
															<?php if ($rwgetRole['lock_folder'] == '1' && $rwFolder['is_protected']==0) { ?>
															<li><a href="javascript:void(0)" data-toggle="modal" id="lock_fol" data-target="#lock-folder"><?php echo $lang['lock_folder']; ?></a></li> <?php } ?>
															<?php if ($rwgetRole['lock_folder'] == '1' && $rwFolder['is_protected']==2) { ?>
																<li><a href="javascript:void(0)" data-toggle="modal" id="unlock_fol" data-target="#unlock-folder"><?php echo $lang['unlock_folder']; ?></a></li>
															<?php } ?>
															<?php if ($rwgetRole['lock_folder'] == '1' && $rwFolder['is_protected']==2) { ?>
																<li><a href="javascript:void(0)" data-toggle="modal" id="reset_password" data-target="#forgot-password"><?php echo $lang['forgot_pass']; ?></a></li> 
															<?php } ?>
													  
														
													</ul>
												</div> 
											<?php } ?>

                                        </div>
                                        <div class="col-lg-12 m-t-10" style="padding-left: 0;">
                                            <form action="searchdata">
                                                <div class="row" id="multiselect">
                                                    <div class="col-md-3">
                                                        <select  class="form-control select2" id="my_multi_select1" name="metadata[]" required>
                                                            <option disabled selected><?php echo $lang['Select_Metadata']; ?></option>
                                                            <option value="old_doc_name"><?php echo $lang['FileName']; ?></option>
                                                            <option value="noofpages"><?php echo $lang['No_Of_Pages']; ?></option>
                                                            <?php
                                                            $metadatacount = 2;
                                                            $arrarMeta = array();
                                                            mysqli_set_charset($db_con, "utf8");
                                                            $metas = mysqli_query($db_con, "select * from tbl_metadata_to_storagelevel where sl_id='$slid'");
                                                            while ($metaval = mysqli_fetch_assoc($metas)) {
                                                                array_push($arrarMeta, $metaval['metadata_id']);
                                                            }
                                                            $meta = mysqli_query($db_con, "select * from tbl_metadata_master order by field_name asc");
                                                            while ($rwMeta = mysqli_fetch_assoc($meta)) {
                                                                if (in_array($rwMeta['id'], $arrarMeta)) {
                                                                    if ($rwMeta['field_name'] != 'filename') {
                                                                        echo '<option value="' . $rwMeta['field_name'] . '">' . str_replace("_", " ", $rwMeta['field_name']) . '</option>';
                                                                        $metadatacount++;
                                                                    }
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <select class="form-control select2" name="cond[]" required>
                                                            <option disabled selected style="background: #808080; color: #fff;"><?php echo $lang['Slt_Condition']; ?></option>
                                                            <option <?php
                                                            if (isset($_GET['cond']) && !empty($_GET['cond']) && $_GET['cond'] == 'Equal') {
                                                                echo'selected';
                                                            }
                                                            ?> value="Equal"><?php echo $lang['Equal']; ?></option>
                                                            <option <?php
                                                            if (isset($_GET['cond']) && !empty($_GET['cond']) && $_GET['cond'] == 'Contains') {
                                                                echo'selected';
                                                            }
                                                            ?> value="Contains"><?php echo $lang['Contains']; ?></option>
                                                            <option <?php
                                                            if (isset($_GET['cond']) && !empty($_GET['cond']) && $_GET['cond'] == 'Like') {
                                                                echo'selected';
                                                            }
                                                            ?> value="Like"><?php echo $lang['Like']; ?></option>
                                                            <option <?php
                                                            if (isset($_GET['cond']) && !empty($_GET['cond']) && $_GET['cond'] == 'Not Like') {
                                                                echo'selected';
                                                            }
                                                            ?> value="Not Like"><?php echo $lang['Not_Like']; ?></option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="text" class="form-control translatetext" id="searchText1" name="searchText[]" required value="<?php echo $_GET['searchText'] ?>" placeholder="<?php echo $lang['entr_srch_txt_hr']; ?>">
                                                    </div>
                                                    <input type="hidden" value="<?php echo xss_clean($_GET['id']); ?>" name="id" />
                                                    <button type="submit" class="btn btn-primary " id="search" title="<?php echo $lang['Search'] ?>"><i class="fa fa-search"></i></button>
                                                    <a href="javascript:void(0)" class="btn btn-primary" id="addfields" title="<?php echo $lang['Add_more'] ?>"><i class="fa fa-plus"></i></a>
                                                </div>
                                                <div class="row">
                                                    <div class="contents col-lg-12"></div>
                                                </div> 
                                            </form>
                                        </div>

                                        <div class="col-md-12">
                                            <?php
                                            $where = '';
                                            if (isset($_GET['quicksearch']) && !empty($_GET['quicksearch'])) {
                                                $user_id1 = $_SESSION[cdes_user_id];
                                                $chekUsr1 = mysqli_query($db_con, "select * from tbl_bridge_role_to_um where FIND_IN_SET('$user_id1', user_ids) > 0") or die('Error:' . mysqli_error($db_con));
                                                $rwcheckUser1 = mysqli_fetch_assoc($chekUsr1);
                                                if ($rwcheckUser1['role_id'] == 1) {
                                                    $where = "where old_doc_name LIKE '%$_GET[quicksearch]%' and doc_name = '$rwFolder[sl_id]'";
                                                } else {
                                                    $where = "where old_doc_name LIKE '%$_GET[quicksearch]%' and doc_name = '$rwFolder[sl_id]' and flag_multidelete=1";
                                                }
                                            } else {
                                                $user_id1 = $_SESSION[cdes_user_id];
                                                $chekUsr1 = mysqli_query($db_con, "select * from tbl_bridge_role_to_um where FIND_IN_SET('$user_id1', user_ids) > 0") or die('Error:' . mysqli_error($db_con));
                                                $rwcheckUser1 = mysqli_fetch_assoc($chekUsr1);
                                                if ($rwcheckUser1['role_id'] == 1) {
                                                    $where = "where doc_name = '$rwFolder[sl_id]' and flag_multidelete=1";
                                                } else {
                                                    $where = "where doc_name = '$rwFolder[sl_id]' and flag_multidelete=1";
                                                }
                                            }
                                            $constructs = "SELECT doc_id,flag_multidelete FROM tbl_document_master $where";
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
                                                 
                                                $getTpages = "SELECT SUM(noofpages) as totalPages FROM(SELECT noofpages FROM tbl_document_master $where ORDER BY old_doc_name asc LIMIT $start, $per_page) tbl_document_master";

                                                $totalp = mysqli_query($db_con, $getTpages) or die("Error: " . mysqli_error($db_con));

                                                $rowT = mysqli_fetch_assoc($totalp);
                                                $rowT['totalPages'];

                                                $allot = "select * from tbl_document_master $where order by old_doc_name LIMIT $start, $per_page";

                                                $allot_query = mysqli_query($db_con, $allot) or die("Error: " . mysqli_error($db_con));
                                                ?>
                                                <div class="box box-primary" >
                                                    <h4 id="event_result" class="header-title" style="display: inline-block;"><?php echo $lang['Slt_Folder']; ?> : <strong><?php echo $rwFolder['sl_name']; ?></strong></h4>

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
                                                            <label> <?php echo $start + 1 ?> <?php echo $lang['To']; ?> <?php
                                                                if (($start + $per_page) > $foundnum) {
                                                                    echo $foundnum;
                                                                } else {
                                                                    echo ($start + $per_page);
                                                                }
                                                                ?>  <span> <?php echo $lang['Ttal_Rcrds']; ?> : <?php echo $foundnum; ?> |</span>

                                                                <?php echo $lang['Total_Pages']; ?> : <?php echo $rowT['totalPages']; ?></label>

                                                        </div>
                                                    </div>
                                                    <table class="table table-striped table-bordered js-sort-table">
                                                        <thead>
                                                            <tr>
                                                                <th class="js-sort-none" ><div class="checkbox checkbox-primary"><input  type="checkbox" class="checkbox-primary" id="select_all"> <label for="checkbox6"> <strong><?php echo $lang['All']; ?></strong></label></div>  </th>
                                                                <th   ><?php echo $lang['File_Name']; ?></th>
                                                                <th class="js-sort-number"><?php echo $lang['File_Size']; ?></th>
                                                                <th class="js-sort-number" ><?php echo $lang['No_of_Pages']; ?></th>
                                                                <th ><?php echo $lang['Upld_By']; ?></th>
                                                                <th class="js-sort-date"><?php echo $lang['Upld_Date']; ?></th>
                                                                <th class="js-sort-none"><?php echo $lang['Actions']; ?></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>

                                                            <?php
                                                            $n = $start + 1;
                                                            while ($file_row = mysqli_fetch_assoc($allot_query)) {
                                                                $existfile = mysqli_query($db_con, "select old_doc_name,doc_name from tbl_document_master where old_doc_name='$file_row[old_doc_name]' and doc_name='$file_row[doc_name]' and flag_multidelete='1'");

                                                                $rwexistfile = mysqli_fetch_assoc($existfile);
                                                                $exfile = $rwexistfile['old_doc_name'];
    
                                                                if ($rwgetRole['doc_weeding_out'] == '1' && $rwgetInfo['retention_feature_enable'] == '1') {
                                                                    if (isset($file_row['retention_period']) && !empty($file_row['retention_period'])) {
                                                                        $wedDate = $file_row['retention_period'];
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
                                                                    if (isset($file_row['doc_expiry_period']) && !empty($file_row['doc_expiry_period'])) {
                                                                        $docexpDate = $file_row['doc_expiry_period'];
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
																
																$checkoutcolor = ($file_row['checkin_checkout'] == 0)?'#b7f1a3':'';
																$checkoutTitle = ($file_row['checkin_checkout'] == 0)?'File is checkout!':'';
                                                              
																
                                                                $docExpRetentionPeriod = "#a6ecf7";
                                                                $docExpRetentionPrdtitle = $expiryTitle . ' ' . $lang['and'] . $weedTile;
                                                                $shareDid = mysqli_query($db_con, "select doc_ids from tbl_document_share where doc_ids= '$file_row[doc_id]'") or die("Error: " . mysqli_error($db_con));
                                                                $shreCount = mysqli_num_rows($shareDid);
                                                                
                                                                $subscribeid = mysqli_query($db_con, "select * from tbl_document_subscriber where subscribe_docid= '$file_row[doc_id]' and subscriber_userid='" . $_SESSION['cdes_user_id'] . "'") or die("Error: " . mysqli_error($db_con));
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
                                                                        <div class="checkbox checkbox-primary m-r-15"> <input  type="checkbox" class="checkbox-primary emp_checkbox" data-doc-id="<?php echo $file_row['doc_id']; ?>" id="shreId"> <label for="checkbox6"> <?php echo $n . '.'; ?> </label></div>

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
                                                                    <td>
                                                                        <div style="overflow: hidden; max-width:200px;"  title="<?php echo $file_row['old_doc_name']; ?>"><?php if(file_exists('thumbnail/'.base64_encode($file_row['doc_id']).'.jpg')){ ?><div> <img class="thumb-image" src="thumbnail/<?=base64_encode($file_row['doc_id'])?>.jpg?v=<?php echo time(); ?>"> </div>
                                                                        <?php } echo $file_row['old_doc_name']; ?></div>
                                                                    </td>
                                                                    <td><?php echo formatSizeUnits($file_row['doc_size']); ?></td>
                                                                    <td><?php
                                                                        echo $file_row['noofpages'];
                                                                        ?></td>
                                                                    <?php
                                                                    mysqli_set_charset($db_con, "utf8");
                                                                    $userName = "SELECT first_name,last_name FROM tbl_user_master WHERE user_id = '$file_row[uploaded_by]'";
                                                                    $userName_run = mysqli_query($db_con, $userName) or die("Error: " . mysqli_error($db_con));
                                                                    $rwuserName = mysqli_fetch_assoc($userName_run)
                                                                    ?>
                                                                    <td><?php echo $rwuserName['first_name'] . " " . $rwuserName['last_name']; ?></td>
                                                                    <td><?php echo date('d-m-Y H:i:s', strtotime($file_row['dateposted'])); ?></td>
                                                                    <td>
																<li class="dropdown top-menu-item-xs">
                                                                <?php
                                                                $checkfileLockqry = mysqli_query($db_con, "SELECT * FROM `tbl_locked_file_master` WHERE doc_id='$file_row[doc_id]' and is_active='1' and user_id='$_SESSION[cdes_user_id]'");
                                                                if (mysqli_num_rows($checkfileLockqry) > 0) {

                                                                    $checkfileLock = mysqli_query($db_con, "SELECT * FROM `tbl_locked_file_master` WHERE doc_id='$file_row[doc_id]' and is_locked='1' and user_id='$_SESSION[cdes_user_id]'");
                                                                    if (mysqli_num_rows($checkfileLock) > 0) {
                                                                        ?>
                                                                        <a href="" class="dropdown-toggle profile waves-effect waves-light" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-gear" title="<?php echo $lang['view_actions']; ?>"></i></a>

                                                                        <ul class="dropdown-menu pdf gearbody">
                                                                            <li> 
                                                                                <?php
                                                                                if ($file_row['checkin_checkout'] == 1) {
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
                                                                                            <a href="javascript:void(0)" class="lock_file" data="<?php echo $file_row['doc_id'] ?>"> <i class="fa fa-lock"  title="<?php echo $lang['lock_file']; ?>"></i> <?php echo $lang['lock_file']; ?></a>   
                                                                                            <?php
                                                                                        }
                                                                                    }
                                                                                    ?>
                                                                                </li>
                                                                                <?php if ($rwgetRole['view_metadata'] == '1') { ?>
                                                                                    <li> <a href="javascript:void(0)" data="metaData<?php echo $n; ?>" id="viewMeta"><i class="fa fa-eye"></i> <?php echo $lang['View_MetaData']; ?></a></li>
                                                                                    <li> <a href="javascript:void(0)"  data-toggle="modal" data-target="#con-close-modal-history" onclick="return getFileHistory(<?php echo $file_row['doc_id'] ?>,<?php echo $slid ?>)"><i class="fa fa-history"></i> <?php echo $lang['history']; ?></a></li>

                                                                                <?php } if ($rwgetRole['file_review'] == '1') { ?>
                                                                                    <?php if ((strtolower($file_row['doc_extn']) == 'jpeg' || strtolower($file_row['doc_extn']) == 'jpg' ||  strtolower($file_row['doc_extn']) == 'png' ||  strtolower($file_row['doc_extn']) == 'pdf' || strtolower($file_row['doc_extn']) == 'doc' || strtolower($file_row['doc_extn']) == 'docx')) { ?>
                                                                                        <li> <a href="reviewers?i=<?php echo urlencode(base64_encode($_SESSION['cdes_user_id'])) ?>&doc_Id=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>" id="moveTorw" data="<?php echo $file_row['doc_id']; ?>"> <i class="fa fa fa-send"></i> <?php echo $lang['Review']; ?></a></li> <?php } ?>

                                                                                <?php } if ($rwgetRole['workflow_initiate_file'] == '1' || $rwgetRole['initiate_file'] == '1') { ?>
                                                                                    <li> <a href="javascript:void(0)" data-toggle="modal" data-target="#assign-workflow" id="moveToWf" data="<?php echo $file_row['doc_id']; ?>"> <i class="fa fa-plus"></i> <?php echo $lang['Workflow']; ?></a></li>

                                                                                <?php } ?>

                                                                                <?php if (strtolower($file_row['doc_extn']) == 'pdf' && $rwgetRole['splitpdf'] == '1') { ?>
                                                                                    <li><a href="viewer?id=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&i=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>&sp=<?php echo urlencode(base64_encode('1')); ?>" id="fancybox-inner" class="pdfview" target="_blank"><i class="fa fa-sign-out"></i> <?php echo $lang['splitpdf']; ?></a></li>
                                                                                    <?php
                                                                                } ?>

                                                                                <?php  if (strtolower($file_row['doc_extn']) == 'pdf' && $rwgetRole['pdf_annotation']) { ?>
                                                                                <li class="isprotected"> <a href="anott/add-delete-page?id1=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>&pn=1" target="_blank" > <i class="fa fa-plus"></i> <?php echo $lang['add_delete_pages']; ?></a></li>
                                                                            <?php } ?> 

                                                                                <?php if ($rwgetRole['checkin_checkout'] == '1') { ?>
                                                                                    <li><a href="javascript:void(0)" id="checkout" data="<?php echo $file_row['doc_id']; ?>"><i class="fa fa-sign-out"></i> <?php echo $lang['Chk_Out']; ?></a></li>

                                                                                    <?php } if ($rwgetRole['subscribe_document'] == '1') {
                                                                                ?>
                                                                                <li class="isprotected"><a href="javascript:void(0)"  id="singlesubscribe" data-toggle="modal" data-target="#subscribe" data="<?php echo $file_row['doc_id']; ?>"><i class="fa fa-bell-o"></i> <?php echo $lang['subscribe']; ?></a></li>
                                                                                    <?php
																					
																				} if (($rwgetRole['export_ocr'] == '1' && $file_row['ocr']=='1') && in_array($file_row['doc_extn'],$exportOcExtn)) {
																					?>
																					<li><a href="javascript:void(0)" data-toggle="modal" data-target="#exportocr-modal" id="exportocr" data="<?php echo $file_row['doc_id']; ?>"><i class="fa fa-download"></i> <?php echo $lang['exportocr']; ?></a></li>
																					<?php
																				
                                                                                }if ($rwgetRole['file_delete'] == '1') {
                                                                                    ?>
                                                                                    <li><a href="javascript:void(0)" data-toggle="modal" data-target="#con-close-modal2" id="removeRow" data="<?php echo $file_row['doc_id']; ?>"><i class="fa fa-trash"></i> <?php echo $lang['Delete']; ?> </a></li>
                                                                                    <?php
                                                                                }
																				
																				
                                                                                                                                                           //ANKIT 01 june 2023
                                                                                                                                                           if ($rwgetRole['rename_document'] == '1' && ($exfile == $file_row['old_doc_name'])) { ?>
                                                                                                                                                            <li><a href="javascript:void(0)" data-toggle="modal" data-target="#changeFleame" id="renamefile" data="<?php echo $file_row['doc_id']; ?>"><i class="fa fa-edit" aria-hidden="true" title="<?php echo $lang['edit_File_name']; ?>"></i>Rename File</a></li>
                                                                                                                                                <?php }
                                                                                                                                                                //Ankit end 01 june 2023
                                                                
                                                                                }
                                                                            } else {
																				if(isFolderReadable($db_con, $slid)){ // check is this folder shared with read only or not
                                                                                require 'checkout-action.php';
																				}
                                                                            }
                                                                            ?>
                                                                        </ul>
                                                                    <?php } else {
                                                                        ?>
                                                                        <a href="javascript:void(0)"  data="<?php echo $file_row['doc_id'] ?>" class="send_lock_request dropdown-toggle profile waves-effect waves-light" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-lock" title="<?php echo $lang['lock_file']; ?>"></i></a>

                                                                        <?php
                                                                    }
                                                                } else {
                                                                    ?>
                                                                    <a href="" class="dropdown-toggle profile waves-effect waves-light" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-gear" title="<?php echo $lang['view_actions']; ?>"></i></a>

                                                                    <ul class="dropdown-menu pdf gearbody">
                                                                        <li> 
                                                                            <?php
                                                                            if ($file_row['checkin_checkout'] == 1) {
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
                                                                                    <li> <a href="javascript:void(0)" data-toggle="modal" data-target="#filemeta-modal" data="metaData<?php echo $n; ?>" id="viewMeta" onclick="getFileMetaData(<?php echo $file_row['doc_id'] ?>,<?php echo $slid ?>);" ><i class="fa fa-eye"></i> <?php echo $lang['View_MetaData']; ?></a></li>
                                                                                    <li> <a href="javascript:void(0)"  data-toggle="modal" data-target="#con-close-modal-history" onclick="return getFileHistory(<?php echo $file_row['doc_id'] ?>,<?php echo $slid ?>)"><i class="fa fa-history"></i> <?php echo $lang['history']; ?></a></li>

                                                                                <?php } if ($rwgetRole['file_review'] == '1') { ?>
                                                                                    <?php if ((strtolower($file_row['doc_extn']) == 'jpeg' || strtolower($file_row['doc_extn']) == 'jpg' ||  strtolower($file_row['doc_extn']) == 'png' || strtolower($file_row['doc_extn']) == 'pdf' || strtolower($file_row['doc_extn']) == 'doc' || strtolower($file_row['doc_extn']) == 'docx') && $file_row['doc_expiry_period'] == '' && $file_row['retention_period'] == '') { ?>
                                                                                        <li> <a href="reviewers?i=<?php echo urlencode(base64_encode($_SESSION['cdes_user_id'])) ?>&doc_Id=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>" id="moveTorw" data="<?php echo $file_row['doc_id']; ?>"> <i class="fa fa fa-send"></i> <?php echo $lang['Review']; ?></a></li><?php } ?>

                                                                                <?php } if (($rwgetRole['workflow_initiate_file'] == '1' || $rwgetRole['initiate_file'] == '1') && $file_row['doc_expiry_period'] == '' && $file_row['retention_period'] == '') { ?>
                                                                                    <li> <a href="javascript:void(0)" data-toggle="modal" data-target="#assign-workflow" id="moveToWf" data="<?php echo $file_row['doc_id']; ?>"> <i class="fa fa-plus"></i> <?php echo $lang['Workflow']; ?></a></li>

                                                                                <?php } ?>

                                                                                <?php if (strtolower($file_row['doc_extn']) == 'pdf' && $rwgetRole['splitpdf'] == '1') { ?>
                                                                                    <li><a href="viewer?id=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&i=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>&sp=<?php echo urlencode(base64_encode('1')); ?>" id="fancybox-inner" class="pdfview" target="_blank"><i class="fa fa-sign-out"></i> <?php echo $lang['splitpdf']; ?></a></li>
                                                                                    <?php
                                                                                } ?>

                                                                                <?php if (strtolower($file_row['doc_extn']) == 'pdf' && $rwgetRole['pdf_annotation']) { ?>
                                                                                <li class="isprotected"> <a href="anott/add-delete-page?id1=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>&pn=1" target="_blank" > <i class="fa fa-plus"></i> <?php echo $lang['add_delete_pages']; ?></a></li>
                                                                            <?php } ?> 

                                                                                <?php if ($rwgetRole['checkin_checkout'] == '1') { ?>
                                                                                    <li><a href="javascript:void(0)" id="checkout" data="<?php echo $file_row['doc_id']; ?>"><i class="fa fa-sign-out"></i> <?php echo $lang['Chk_Out']; ?></a></li>

                                                                                <?php } if ($rwgetRole['subscribe_document'] == '1') {
                                                                                ?>
                                                                                <li class="isprotected"><a href="javascript:void(0)"  id="singlesubscribe" data-toggle="modal" data-target="#subscribe" data="<?php echo $file_row['doc_id']; ?>"><i class="fa fa-bell-o"></i> <?php echo $lang['subscribe']; ?></a></li>
                                                                                <?php
																				
																				}if (($rwgetRole['export_ocr'] == '1' && $file_row['ocr']=='1') && in_array($file_row['doc_extn'],$exportOcExtn)) {
                                                                                    ?>
																						<li><a href="javascript:void(0)" data-toggle="modal" data-target="#exportocr-modal"  data="<?php echo $file_row['doc_id']; ?>" id="exportocr" ><i class="fa fa-download"></i> <?php echo $lang['exportocr']; ?></a></li>
                                                                                    <?php
                                                                                
                                                                                }if ($rwgetRole['file_delete'] == '1') {
                                                                                    ?>
                                                                                    <!-- <li><a href="javascript:void(0)" data-toggle="modal" data-target="#con-close-modal2" id="removeRow" data="<?php echo $file_row['doc_id']; ?>"><i class="fa fa-trash"></i> <?php echo $lang['Delete']; ?> </a></li> -->
                                                                                    <?php
                                                                                }
																				                                                                  //ANKIT 01 june 2023
                                                                                                                                                  if ($rwgetRole['rename_document'] == '1' && ($exfile == $file_row['old_doc_name'])) { ?>
                                                                                                                                                    <li><a href="javascript:void(0)" data-toggle="modal" data-target="#changeFleame" id="renamefile" data="<?php echo $file_row['doc_id']; ?>"><i class="fa fa-edit" aria-hidden="true" title="<?php echo $lang['edit_File_name']; ?>"></i>Rename File</a></li>
                                                            
                                                                                                                                        <?php }
                                                                                                                                                        //Ankit end 01 june 2023
                                                                                                                                                   
																				
                                                                            }
                                                                        } else {
                                                                            require 'checkout-action.php';
                                                                        }
                                                                        ?>
                                                                    </ul>
                                                                <?php }
                                                                ?>
                                                            </li>
                                                            </td>
                                                            </tr>
                                                            <!--tr style="display:none;">
                                                                <td colspan="7">
                                                                    <div id="metaData<?php echo $n; ?>"  class="metadata">
                                                                        <?php
                                                                        mysqli_set_charset($db_con, "utf8");
                                                                        $getMetaId = mysqli_query($db_con, "select * from tbl_metadata_to_storagelevel where sl_id = '$file_row[doc_name]'") or die('Error:gg' . mysqli_error($db_con));

                                                                        while ($rwgetMetaId = mysqli_fetch_assoc($getMetaId)) {

                                                                            $getMetaName = mysqli_query($db_con, "select * from tbl_metadata_master where id = '$rwgetMetaId[metadata_id]'") or die('Error:' . mysqli_error($db_con));

                                                                            while ($rwgetMetaName = mysqli_fetch_assoc($getMetaName)) {
                                                                                $meta = mysqli_query($db_con, "select `$rwgetMetaName[field_name]` from tbl_document_master where doc_id='$file_row[doc_id]'");
                                                                                $rwMeta = mysqli_fetch_assoc($meta);

                                                                                if ($rwMeta[$rwgetMetaName['field_name']]!="") {
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
                                                        ?>
                                                        
                                                        </tbody>
                                                    </table>
													
													
                                                                <?php if(($rwFolder['is_protected']==0 || $_SESSION['pass'] == $rwFolder['password']) && (isFolderReadable($db_con, $slid))){ ?>
                                                                <ul class="delete_export">
                                                                    <input type="hidden" name="slid" id="slid" value="<?php echo $slid; ?>">
                                                                    <input type="hidden" name="sty" id="sty" value="<?php echo xss_clean($_GET['id']); ?>">
                                                                    <?php if ($rwgetRole['file_delete'] == '1') { ?>
                                                                        <li><button id="del_file" class="rows_selected btn btn-danger btn-sm" data-toggle="modal"  data-target="#del_send_to_recycle"><i data-toggle="tooltip" title="<?php echo $lang['Delete_files'] ?>" class="fa fa-trash-o"></i></button></li>
                                                                    <?php } if ($rwgetRole['export_csv'] == '1') { ?>
                                                                        <li><button class="btn btn-primary btn-sm" id="export4"  data-toggle="modal"  data-target="#multi-csv-export-model"><i data-toggle="tooltip" title="<?php echo $lang['Export_Data'] ?>" class="fa fa-download"></i></button></li>
                                                                    <?php } if ($rwgetRole['move_file'] == '1') { ?>
                                                                        <li><button id="move_multi" class="rows_selected btn btn-primary btn-sm" data-toggle="modal" data-target="#move-selected-files" > <i data-toggle="tooltip" title="<?php echo $lang['Mve_fles'] ?>" class="fa fa-share-square"></i></button></li>
                                                                    <?php } if ($rwgetRole['copy_file'] == '1') { ?>
                                                                        <li><button class="rows_selected btn btn-primary btn-sm" id="copyFiles" data-toggle="modal" data-target="#copy-selected-files" ><i data-toggle="tooltip" title="<?php echo $lang['Copy_files'] ?>" class="fa fa-copy"></i></button></li>
                                                                    <?php } if ($rwgetRole['share_file'] == '1' ) { ?>
                                                                        <li><button class="rows_selected btn btn-primary btn-sm" id="shareFiles" data-toggle="modal" data-target="#share-selected-files"><i data-toggle="tooltip" title="<?php echo $lang['Share_files']; ?>" class="fa fa-share-alt"></i></button></li>
                                                                    <?php } if ($rwgetRole['mail_files'] == '1') { ?>
                                                                        <li><button class="rows_selected btn btn-primary btn-sm" id="mailFiles" data-toggle="modal" data-target="#mail-selected-files"><i data-toggle="tooltip" title="<?php echo $lang['mail_files']; ?>" class="fa fa-envelope-o"></i></button></li>
                                                                    <?php } if ($rwgetRole['pdf_download'] == '1') { ?>
                                                                        <li><button class="rows_selected btn btn-primary btn-sm" id="downloadcheckedfile" data-toggle="modal" data-target="#downloadfile"><i class="ti-import" data-toggle="tooltip" title="<?php echo $lang['download_selected_file']; ?>"></i></button></li>
                                                                    <?php } if ($rwgetRole['subscribe_document'] == '1') { ?>
                                                                        <li><button class="rows_selected btn btn-primary btn-sm" id="subscribecheckedfile" data-toggle="modal" data-target="#subscribefile"><i class="fa fa-bell-o" data-toggle="tooltip" title="<?php echo $lang['subscribe_selected_file']; ?>"></i></button></li>
                                                                    <?php } ?>
                                                                     <!-- //ANKIT 02 june 2023 -->
                                                                     <?php
                                                                            if ($rwgetRole['Multi_rename_document'] == '1') { ?>
                                                                                <button class="btn btn-primary" data-toggle="modal" data-target="#changemultFleame" id="renmefile" data="<?php echo $rw_recyle['doc_id']; ?>"><i class="fa fa-edit" aria-hidden="true" title="<?php echo $lang['edit_File_name']; ?>"></i> </button>

                                                                            <?php } ?>
                                                                            <!-- //Ankit end 02 june 2023 -->

                                                                </ul>
                                                                <?php } ?>
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
                                                             $storageId = xss_clean($_GET['id']); 
                                                            //previous button
                                                            if (!($start <= 0))
                                                                echo " <li><a href='?id=".$storageId."&start=$prev&limit=$per_page'>" . $lang['Prev'] . "</a> </li>";
                                                            else
                                                                echo " <li class='disabled'><a href='javascript:void(0)'>" . $lang['Prev'] . "</a> </li>";
                                                            //pages 
                                                            if ($max_pages < 7 + ($adjacents * 2)) {   //not enough pages to bother breaking it up
                                                                $i = 0;
                                                                for ($counter = 1; $counter <= $max_pages; $counter++) {
                                                                    if ($i == $start) {
                                                                        echo " <li class='active'><a href='?id=".$storageId."&start=$i&limit=$per_page'><b>$counter</b></a> </li>";
                                                                    } else {
                                                                        echo "<li><a href='?id=".$storageId."&start=$i&limit=$per_page'>$counter</a></li> ";
                                                                    }
                                                                    $i = $i + $per_page;
                                                                }
                                                            } elseif ($max_pages > 5 + ($adjacents * 2)) {    //enough pages to hide some
                                                                //close to beginning; only hide later pages
                                                                if (($start / $per_page) < 1 + ($adjacents * 2)) {
                                                                    $i = 0;
                                                                    for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                                                                        if ($i == $start) {
                                                                            echo " <li class='active'><a href='?id=".$storageId."&start=$i&limit=$per_page'><b>$counter</b></a></li> ";
                                                                        } else {
                                                                            echo "<li> <a href='?id=".$storageId."&start=$i&limit=$per_page'>$counter</a> </li>";
                                                                        }
                                                                        $i = $i + $per_page;
                                                                    }
                                                                }
                                                                //in middle; hide some front and some back
                                                                elseif ($max_pages - ($adjacents * 2) > ($start / $per_page) && ($start / $per_page) > ($adjacents * 2)) {
                                                                    echo " <li class='active'><a href='?id=".$storageId."&start=0'>1</a></li> ";
                                                                    echo "<li><a href='?id=".$storageId."&start=$per_page&limit=$per_page'>2</a></li>";
                                                                    echo "<li><a href='javascript:void(0)'>...</a></li>";

                                                                    $i = $start;
                                                                    for ($counter = ($start / $per_page) + 1; $counter < ($start / $per_page) + $adjacents + 2; $counter++) {
                                                                        if ($i == $start) {
                                                                            echo " <li class='active'><a href='?id=".$storageId."&start=$i&limit=$per_page'><b>$counter</b></a></li> ";
                                                                        } else {
                                                                            echo " <li><a href='?id=".$storageId."&start=$i&limit=$per_page'>$counter</a> </li>";
                                                                        }
                                                                        $i = $i + $per_page;
                                                                    }
                                                                }
                                                                //close to end; only hide early pages
                                                                else {
                                                                    echo "<li> <a href='?id=".$storageId."&start=0'>1</a> </li>";
                                                                    echo "<li><a href='?id=".$storageId."&start=$per_page&limit=$per_page'>2</a></li>";
                                                                    echo "<li><a href='javascript:void(0)'>...</a></li>";

                                                                    $i = $start;
                                                                    for ($counter = ($start / $per_page) + 1; $counter <= $max_pages; $counter++) {
                                                                        if ($i == $start) {
                                                                            echo " <li class='active'><a href='?id=".$storageId."&start=$i&limit=$per_page'><b>$counter</b></a></li> ";
                                                                        } else {
                                                                            echo "<li> <a href='?id=".$storageId."&start=$i&limit=$per_page'>$counter</a></li> ";
                                                                        }
                                                                        $i = $i + $per_page;
                                                                    }
                                                                }
                                                            }
                                                            //next button
                                                            if (!($start >= $foundnum - $per_page))
                                                                echo "<li><a href='?id=".$storageId."&start=$next&limit=$per_page'>" . $lang['Next'] . "</a></li>";
                                                            else
                                                                echo "<li class='disabled'><a href='javascript:void(0)'>" . $lang['Next'] . "</a></li>";
                                                            ?>
                                                        </ul>
                                                        <?php
                                                    }
                                                    echo "</center>";
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } else {
                                        ?>
                                        <div class="row p-b-60">
                                            <table class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th><strong><?php echo $lang['SNO']; ?></strong></th>
                                                        <th><?php echo $lang['File_Name']; ?></th>
                                                        <th><?php echo $lang['File_Size']; ?></th>
                                                        <th><?php echo $lang['No_of_Pages']; ?></th>
                                                        <th><?php echo $lang['Upld_By']; ?></th>
                                                        <th><?php echo $lang['Upld_Date']; ?></th>
                                                        <th><?php echo $lang['Actions']; ?></th>
                                                    </tr>
                                                </thead>
                                                <tr>
                                                    <td colspan="7">
                                                <center><strong class="text-danger"><i class="ti-face-sad text-pink"></i> <?php echo $lang['Who0ps!_No_Records_Found']; ?></strong></center>
                                                </td>
                                                </tr>
                                            </table>
                                        </div>
                                    <?php }
                                    ?>
                                </div>
                            </div>
                        </div>				
                    </div>
                </div> <!-- container -->
            </div> <!-- content -->
        </div>  

        <!---assign meta-data model start ---->
        <div id="con-close-modal5" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog"> 
                <div class="modal-content"> 
                    <div class="modal-header"> 
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                        <h4 class="modal-title"><?php echo $rwFolder['sl_name']; ?> : <?php echo $lang['Asgn_MetaData_Fields_to']; ?></h4> 
                    </div> 
                    <form method="post">
                        <div class="modal-body">
                            <div class="row shiv metaa">
                                <span><strong><?php echo $lang['Fld_Slt']; ?></strong></span>
                                <strong style="margin-left: 200px;"><?php echo $lang['Fld_Asnd']; ?></strong>
                                <select multiple="multiple" class="multi-select" id="my_multi_select1" name="my_multi_select1[]" data-plugin="multiselect">
                                    <?php
                                    $arrarMeta = array();
                                    $metas = mysqli_query($db_con, "select * from tbl_metadata_to_storagelevel where sl_id = '$slid'") or die('Error: metadata' . mysqli_error($db_con));
                                    while ($metaval = mysqli_fetch_assoc($metas)) {
                                        array_push($arrarMeta, $metaval['metadata_id']);
                                    }
                                    mysqli_set_charset($db_con, "utf8");
                                    $meta = mysqli_query($db_con, "select * from tbl_metadata_master order by field_name asc");
                                    while ($rwMeta = mysqli_fetch_assoc($meta)) {
                                        if (in_array($rwMeta['id'], $arrarMeta)) {
                                            echo '<option value="' . $rwMeta['id'] . '" selected>' . str_replace("_", " ", $rwMeta['field_name']) . '</option>';
                                        } else {
                                            echo '<option value="' . $rwMeta['id'] . '">' . str_replace("_", " ", $rwMeta['field_name']) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" value="<?php echo base64_decode(urldecode(xss_clean($_GET['id']))); ?>" name="id">
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                            <button class="btn btn-primary waves-effect waves-light" type="submit" name="assignMeta"><?php echo $lang['Submit']; ?></button>
                        </div>
                    </form>

                </div> 
            </div>
        </div>
        <div id="multi-csv-export-model" class="modal fade"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog"> 
                <div class="panel panel-color panel-danger"> 
                    <div class="panel-heading"> 
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                        <h2 class="panel-title" id="unexport"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <?php echo $lang['Hre_msge']; ?></h2>
                        <h2 class="panel-title" id="export_title"><?php echo $lang['xprt_Slt_Dta']; ?></h2>
                    </div>
                    <form action="multi_data_export"  method="post" enctype="multipart/form-data">
                        <div class="panel-body">
                            <div class="row">
                                <label id="export_unselected" style="display:none;"><h5 class="text-alert"> <?php echo $lang['Pls_slt_Files_for_xpt_dta']; ?></h5></label>
                                <div id="export_selected">
                                    <label><?php echo $lang['Slct_Fles_fr_xpt_Frmt']; ?></label>
                                    <select class="form-control select2" name="select_Fm">
                                        <option value="csv"><?php echo $lang['Csv']; ?></option>
                                        <option value="excel"><?php echo $lang['Excel']; ?></option>
                                        <option value="pdf"><?php echo $lang['Pdf']; ?></option> 
                                        <option value="word"><?php echo $lang['Word']; ?></option>
                                    </select>
                                </div>
                            </div>
                            <input type="hidden" name="export_doc_ids" id="export_doc_ids" value="">
                        </div>
                        <div class="modal-footer"> 
                            <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                            <input type="hidden" value="<?php echo base64_decode(urldecode($_GET['id'])); ?>" name="id">
                            <button class="btn btn-primary waves-effect waves-light" type="submit" name="exportData" id="hidexp"> <i class="fa fa-download"></i> <?php echo $lang['Export']; ?></button>
                        </div>
                    </form>
                </div> 
            </div>
        </div>
        <!--ends assign-meta-data modal --> 

        <div id="con-close-modal7" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog"> 
                <div class="modal-content"> 
                    <div class="modal-header"> 
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                        <h4 class="modal-title"><?php echo $lang['share_folder']; ?></h4> 
                    </div> 
                    <form method="post">
                        <div class="modal-body">
                            <div class="form-group">
                                <label><?php echo $lang['Folder_Name']; ?></label>
                                <input type="text" class="form-control specialchaecterlock" name="modify_slname" id="mstore1"  value="<?php echo $rwFolder['sl_name']; ?>" readonly>
                            </div>

                            <div class="form-group">
                                <label><?php echo $lang['share_with']; ?></label>
                                <select name="sharewith[]" id="sharewith" class="form-control select2 multi-select" multiple data-placeholder="<?php echo $lang['Select_User']; ?>" required >
                                    <option value=""><?php echo $lang['Select_User']; ?></option>
                                    <?php
                                    $sameGroupIDs = array();
                                    $group = mysqli_query($db_con, "select * from tbl_bridge_grp_to_um where find_in_set('$_SESSION[cdes_user_id]',user_ids)") or die('Error' . mysqli_error($db_con));
                                    while ($rwGroup = mysqli_fetch_assoc($group)) {
                                        $sameGroupIDs[] = $rwGroup['user_ids'];
                                    }
                                    $sameGroupIDs = array_unique($sameGroupIDs);
                                    sort($sameGroupIDs);
                                    $sameGroupIDs = implode(',', $sameGroupIDs);
                                    $user = mysqli_query($db_con, "select * from tbl_user_master where user_id in($sameGroupIDs) order by first_name asc");
                                    while ($rwUser = mysqli_fetch_assoc($user)) {
                                        if ($rwUser['user_id'] != 1 && $rwUser['user_id'] != $_SESSION['cdes_user_id']) {
                                            echo '<option value="' . $rwUser['user_id'] . '">' . $rwUser['first_name'] . ' ' . $rwUser['last_name'] . '</option>';
                                        } else {
                                            // echo '<option value="' . $rwUser['user_id'] . '">' . $rwUser['first_name'] . ' ' . $rwUser['last_name'] . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
							
							<div class="form-group">
								<div class="form-check">
									<input type="checkbox" name="readonly" class="form-check-input" value="1">
									<label class="form-check-label" for="exampleCheck1"> &nbsp; Read Only</label>
								</div>
							</div>
                        </div>
                        <div class="modal-footer"> 
                            <input value="<?php echo $slid; ?>" name="slId" type="hidden" >
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                            <input type="submit" name="shareFolder" class="btn btn-primary" value="<?php echo $lang['Submit']; ?>">
                        </div>
                    </form>
                </div> 
            </div>
        </div>
         <!-- //ANKIT 01 june 2023 -->
    <div id="changeFleame" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="panel panel-color panel-primary">

                <div class="panel-heading">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h2 class="panel-title"><?php echo $lang['Are_u_confirm']; ?> </h2>
                </div>
                <form method="post">
                    <div class="panel-body" id="refilee">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                        <button type="submit" name="rename" class="btn btn-primary"> <?php echo $lang['Submit']; ?></button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <!-- //Ankit end 01 june 2023 -->
        
        <!-- /.modal --> 
        
        
        <?php require_once './application/pages/footer.php'; ?>      
        <!-- END wrapper -->
       <?php require_once './application/pages/footerForjs.php'; ?>
        <!--for multiselect-->
        <script type="text/javascript" src="assets/plugins/multiselect/js/jquery.multi-select.js"></script>
        <script src="assets/js/jquery.core.js"></script>
        <script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>
        <script src="assets/plugins/jstree/jstree.min.js"></script>
        <script src="assets/pages/jquery.tree.js"></script>
        <script type="text/javascript" src="assets/multi_function_script.js?v=12"></script>
        <script src="assets/moment-with-locales.js"></script>
        <script src="assets/bootstrap-datetimepicker.js"></script>
		
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
        <?php require_once 'file-action-js.php'; ?>      

        <!--modify starts-->
        <div id="con-close-modal-modify" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog"> 
                <div class="modal-content"> 
                    <div class="modal-header"> 
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                        <h4 class="modal-title"><?php echo $lang['Mdfy_Storage_Level']; ?></h4> 
                    </div> 
                    <form method="post">
                        <div class="modal-body">
                            <input class="form-control specialchaecterlock" id="create_child" name="modify_slname" value="<?php echo $rwFolder['sl_name']; ?>" required >
                        </div>
                        <div class="modal-footer"> 
                            <input value="<?php echo $rwFolder['sl_id']; ?>" name="modi" type="hidden" >
                            <input value="<?php echo $rwFolder['sl_parent_id']; ?>" name="modi_parentId" type="hidden" >
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                            <input type="submit" name="update" class="btn btn-primary" value="<?php echo $lang['Save_changes']; ?>">
                        </div>
                    </form>
                </div> 
            </div>
        </div><!-- /.modal -->  
        <!---Create child model start ---->
        <div id="con-close-modal1" class="modal fade"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog"> 
                <div class="modal-content"> 
                    <div class="modal-header"> 
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                        <h4 class="modal-title"><?php echo $rwFolder['sl_name']; ?> (<?php echo $lang['Ad_New_Chld_to']; ?>)</h4> 
                    </div> 
                    <form method="post">
                        <div class="modal-body">
                            <label class="text-primary"><?php echo $lang['Crt_New_Cld']; ?><span class="text-alert">*</span></label>
                            <input class="form-control specialchaecterlock" name="create_child" id="V1" placeholder="<?php echo $lang['Crt_New_Cld']; ?>" required="">
                        </div>
                        <div class="modal-footer"> 
                            <input value="<?php echo $rwFolder['sl_id']; ?>" name="add_child" type="hidden" >
                            <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                            <button type="submit" name="add_storage" class="btn btn-primary"><i class="fa fa-plus"></i> <?php echo $lang['Add']; ?></button>
                        </div>
                    </form>
                </div> 
            </div>
        </div>
        <!--ends Create child modal --> 
        <!--start delete model-->
        <div id="con-close-modal-del" class="modal fade"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog"> 
                <div class="panel panel-color panel-danger"> 
                    <div class="panel-heading"> 
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                        <h2 class="panel-title"><?php echo $lang['Are_u_confirm']; ?></h2> 
                    </div> 
                    <form method="post">
                        <div class="panel-body">
                            <p class="text-alert"><?php echo str_replace('folder_name',$rwFolder['sl_name'], $lang['del_strg_sure_alert']);?>?</p>
                        </div>
                        <div class="modal-footer"> 
                            <input value="<?php echo $rwFolder['sl_id']; ?>" name="delsl" type="hidden" >
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close'] ?></button>

                            <?php if ($rwgetRole['role_id'] == 1) { ?>
                                <button type="submit" name="deleted" class="btn btn-danger" value="yes" ><i class="fa fa-trash-o"  ></i> <?php echo $lang['Delete'] ?> </button>
                            <?php } ?>

                            <button type="submit" id="no" name="deleted" class="btn btn-danger"> <i class="fa fa-recycle"></i>
                                <?php
                                if ($rwgetRole['role_id'] == 1) {
                                    echo $lang['Recycle'];
                                } else {
                                    echo $lang['Delete'];
                                }
                                ?></button>
                        </div>
                    </form>
                </div> 
            </div>
        </div><!--ends delete modal --> 
        <!-- MODAL for addworkflow -->
        <script>

            $("#wfid").change(function () {
                var wfId = $(this).val();
                alert(lbl);
                $.post("application/ajax/workFlstp.php", {wid: wfId}, function (result, status) {
                    if (status == 'success') {
                        $("#stp").html(result);
                    }
                });
            });
            $("#ufw,#verify-comp").click(function (event) {
                if ($("input#myCheck").is(":checked")) {
                    //alert('ok');
                } else {
                    document.querySelector('#inufw').click();
                }
            });
        </script>
        <!-- for move level-->
        <div id="con-close-modal4" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog"> 
                <div class="modal-content"> 
                    <div class="modal-header"> 
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                        <h4 class="modal-title"><?php echo $lang['Move_Storage_Level'] ?></h4> 
                    </div> 
                    <form method="post" class="form-inline">
                        <div class="modal-body">
                            <div class="row">
                                <?php
                                $moveFolderName = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id = $slid") or die('Error in move folder name: ' . mysqli_error($db_con));
                                $rwmoveFolderName = mysqli_fetch_assoc($moveFolderName);
                                ?>     
                                <label><?php echo $rwmoveFolderName['sl_name']; ?> : <?php echo $lang['Move_Fld_File'] ?></label>
                                <br><br>
                                <div class="col-md-12">
                                    <label><?php echo $lang['Move_To'] ?></label>
                                    <select class="form-control select2" data-type="storage" name="lastMoveId" required>

                                        <option selected disabled><?php echo $lang['Sel_Strg_Lvl'] ?></option>

                                        <?php
										  mysqli_set_charset($db_con, "utf8");
                                $perm = mysqli_query($db_con, "select sl_id from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]' group by sl_id");
                                $slperms = array();
                                while ($rwPerm = mysqli_fetch_assoc($perm)) {
                                    $slperms[] = $rwPerm['sl_id'];
                                }
                                $permcount = count($slperms);
                                $sl_perm = implode(',', $slperms);
										$sllevel = mysqli_query($db_con, "select * from tbl_storage_level where sl_id in($slpermIdes) AND delete_status='0' order by sl_name asc");
									  $all_data = mysqli_query($db_con, "select * from tbl_storage_level where  delete_status='0' order by sl_name asc");
                                   $new_arr=mysqli_fetch_all($all_data,MYSQLI_ASSOC);
                                    $parent_id_arr=array();
                            $sl_id_arr=array();
                            foreach($new_arr as $v)
                            {
                                $parent_id_arr[$v['sl_parent_id']][]=$v;
                                $sl_id_arr[$v['sl_id']]=$v;
                                $sl_parent_id_slid[$v['sl_parent_id']][$v['sl_id']]=$v;

                            }
										while ($rwSllevel = mysqli_fetch_assoc($sllevel)) {
											$level = $rwSllevel['sl_depth_level'];
											$SlId = $rwSllevel['sl_id'];
											//findChild($SlId, $level, $SlId, $slid);
                                            findchild1($SlId, $level, $SlId, $slid,$parent_id_arr,$sl_id_arr,$sl_parent_id_slid);

										}
										?>
                                    </select>
                                    <br>            
                                    <div class="row">
                                        <div class="col-md-12" id="child">

                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer"> 
                            <input value="<?php echo $rwFolder['sl_id']; ?>" name="modi" type="hidden" >
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close'] ?></button> 
                            <input type="submit" name="move" class="btn btn-primary" value="<?php echo $lang['Move_Storage'] ?>">
                        </div>

                    </form>

                </div> 
            </div>
        </div>
        
         <!-- for copy level-->
        <div id="con-close-modal6" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog"> 
                <div class="modal-content"> 
                    <div class="modal-header"> 
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                        <h4 class="modal-title"><?php echo $lang['Cpy_Storage']; ?></h4> 
                    </div> 
                    <form method="post" >
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-12">
                                        <label><?php echo $lang['Cpy_Fld']; ?></label>
                                        <input type="text" readonly id="tocopyfolder" name="toCopyFolder" class="form-control" value="<?php echo $tocpyFolder = $rwFolder['sl_name']; ?>">
                                    </div>
                                    <div class="col-md-12">
                                        <p class="text-danger" id="error"></p>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-md-12">
                                        <label> <?php echo $lang['Cpy_To']; ?></label>
                                        <select class="form-control select2" data-type="storage" name="lastCopyToId" required >
                                            <option selected style="background: #808080; color: #121213;" value=""><?php echo $lang['Sel_Strg_Lvl']; ?></option>
                                            <?php
                                              mysqli_set_charset($db_con, "utf8");
                                $perm = mysqli_query($db_con, "select sl_id from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]' group by sl_id");
                                $slperms = array();
                                while ($rwPerm = mysqli_fetch_assoc($perm)) {
                                    $slperms[] = $rwPerm['sl_id'];
                                }
                                $permcount = count($slperms);
                                $sl_perm = implode(',', $slperms);
											$sllevel = mysqli_query($db_con, "select * from tbl_storage_level where sl_id in($slpermIdes) AND delete_status='0' order by sl_name asc");
                                          $all_data = mysqli_query($db_con, "select * from tbl_storage_level where  delete_status='0' order by sl_name asc");
                                   $new_arr=mysqli_fetch_all($all_data,MYSQLI_ASSOC);
                                    $parent_id_arr=array();
                            $sl_id_arr=array();
                            foreach($new_arr as $v)
                            {
                                $parent_id_arr[$v['sl_parent_id']][]=$v;
                                $sl_id_arr[$v['sl_id']]=$v;
                                $sl_parent_id_slid[$v['sl_parent_id']][$v['sl_id']]=$v;

                            }
											while ($rwSllevel = mysqli_fetch_assoc($sllevel)) {
												$level = $rwSllevel['sl_depth_level'];
												$SlId = $rwSllevel['sl_id'];
												//findChild($SlId, $level, $SlId, $slid);
                                                findchild1($SlId, $level, $SlId, $slid,$parent_id_arr,$sl_id_arr,$sl_parent_id_slid);

											}
											?>

                                        </select>
                                    </div>
                                    <div class="clearfix"></div>

                                    <div class="col-md-12">
                                        <span class="" id="FilesCopy">
                                        </span>
                                    </div>

                                </div>
                            </div>      
                            <div class="clearfix"></div>
                        </div>
                        <div class="modal-footer"> 
                            <input value="<?php echo $rwFolder['sl_id']; ?>" name="modi" type="hidden" >
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button> 
                            <input type="submit" name="copyLevel" class="btn btn-primary" value="Copy Storage">
                        </div>
                    </form>
                </div>
            </div> 
        </div>
        <script>
            $(document).ready(function () {
                $('form').parsley();
            });
            
            $("#parentMoveLevel").change(function () {
                var lbl = $(this).val();
                //alert("lbl");
                $.post("application/ajax/parentMoveList.php", {parentId: lbl, levelDepth: 0, sl_id:<?php echo $slid; ?>}, function (result, status) {
                    if (status == 'success') {
                        $("#child").html(result);
                        //alert(result);
                    }
                });
            });

        </script>
       

        <div id="export" class="modal fade"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog"> 
                <div class="panel panel-color panel-primary"> 
                    <div class="panel-heading"> 
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                        <h2 class="panel-title"><?php echo $lang['Export_CSV']; ?></h2>
                    </div> 
                    <form method="post" action="export">
                        <div class="panel-body">
                            <div class="col-md-12">
                                <div class="radio radio-success radio-inline"> 
                                    <input type="radio" name="radExp" id="inlineRadio1" value="all" checked="checked">
                                    <label for="inlineRadio1"><?php echo $lang['Al_Files_in_slt_fld']; ?></label>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer"> 
                            <input value="<?php echo $slid; ?>" name="slid" type="hidden" >
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                            <button type="submit" name="startExport" class="btn btn-primary"><i class="fa fa-download"></i> <?php echo $lang['Strt_xprt']; ?></button>
                        </div>

                    </form>
                </div>
            </div> 
        </div>
        <!-- for bulk downloads files-->
        <?php
        $validate = mysqli_query($db_con, "select doc_path,old_doc_name,doc_extn,doc_id from tbl_document_master where doc_name='$slid' and flag_multidelete=1");
        if (mysqli_num_rows($validate) > 0) {
            ?>
            <div id="bulkdownload" class="modal fade"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog"> 
                    <div class="modal-content"> 
                        <div class="modal-header"> 
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                            <h4 class="modal-title"><?php echo $lang['Dwnld_All_Files_of_slt_fld_only']; ?></h4>
                        </div> 
                        <form method="post" id="bulkdown">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <textarea class="form-control translatetext specialchaecterlock" id="rsn1" name="reason" cols="65" rows="5" placeholder="<?php echo $lang['Wte_Rson_fr_Dnldng_fles']; ?>" required></textarea>
                                        </div>
                                    </div>
                                </div> 
                            </div>
                            <div class="modal-footer"> 
                                <input value="<?php echo $slid; ?>" name="slid" type="hidden" >
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close'] ?></button> 
                                <input type="submit" name="bulkDownload" class="btn btn-primary" value="<?php echo $lang['Download'] ?>">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php } else { ?>
            <div id="bulkdownload" class="modal fade"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog"> 
                    <div class="modal-content"> 
                        <div class="modal-header"> 
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                            <h4 class="modal-title"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i><?php echo $lang['Hre_msge']; ?></h4>
                        </div> 
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <label style="color:red;"><?php echo $lang['No_Files_Ext_in_Slt_Storage']; ?></label>                       
                                </div>
                            </div> 
                        </div>
                        <div class="modal-footer"> 
                            <input value="<?php echo $slid; ?>" name="slid" type="hidden" >
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>  
        
        <script>
            $("#copyToParentId").change(function () {
                var lbl = $(this).val();
                //alert(lbl);
                $.post("application/ajax/parentMoveList_2.php", {type: 'file', parentId: lbl, levelDepth: 0, sl_id:<?php echo $slid; ?>}, function (result, status) {
                    if (status == 'success') {
                        $("#child2").html(result);
                        //alert(result);
                    }
                });
            });

        </script>
        <!-- SHARE SELECTED FILES--->
        <?php
        if (isset($_POST['assignMeta'], $_POST['token'])) {
            $childName = preg_replace("/[^A-Za-z0-9 ]/", "", $_POST['id']);
            $childName = mysqli_real_escape_string($db_con, $childName);
            $fields = $_POST['my_multi_select1'];
            $flag = 0;
            if (!empty($childName)) {
                $reset = mysqli_query($db_con, "delete from tbl_metadata_to_storagelevel where sl_id='$childName'");
            }
            if (!empty($fields)) {
                $metaNames = array();
                foreach ($fields as $field) {
                    if (!empty($childName)) {
                        //check meta data assigned or not
                        $match = mysqli_query($db_con, "select * from tbl_metadata_to_storagelevel where sl_id='$childName' and metadata_id='$field'") or die('Error:' . mysqli_error($db_con));
                        if (mysqli_num_rows($match) <= 0) {
                            //assign meta data
                            mysqli_set_charset($db_con, "utf8");
                            $create = mysqli_query($db_con, "insert into tbl_metadata_to_storagelevel (`metadata_id`, `sl_id`) values('$field','$childName')") or die('Error' . mysqli_error($db_con));
                            // find meta data details
                            $metan = mysqli_query($db_con, "select * from tbl_metadata_master where id='$field'");
                            $rwMetan = mysqli_fetch_assoc($metan);
                            $metaNames[] = $rwMetan['field_name'];
                            //check meta data in table tbl_document_master
                            $checkDoc = mysqli_query($db_con, "SHOW COLUMNS FROM tbl_document_master LIKE '$rwMetan[field_name]'");
                            if (mysqli_num_rows($checkDoc) <= 0) { //if not
                                $metaCreateDoc = mysqli_query($db_con, "ALTER TABLE tbl_document_master ADD `$rwMetan[field_name]` $rwMetan[data_type]($rwMetan[length_data])  null");
                            }
                            $flag = 1;
                            $sl_id = $childName;
                        } else {
                            $sl_id = $childName;
                        }
                    }
                }
                if ($flag == 1) {
                    mysqli_set_charset($db_con, "utf8");
                    $metaNames = implode(",", $metaNames);
                    $strgeName = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id = '$sl_id'");
                    $rwstrgeName = mysqli_fetch_assoc($strgeName);
                    $storageName = $rwstrgeName['sl_name'];
                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null, '$sl_id','MetaData($metaNames)  Assigned on storage $storageName','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
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
        <!--move Storage-->
        <?php
        // if (isset($_POST['move'], $_POST['token'])) {
        //     //echo $_POST['moveToId']; die;
        //     if (!empty($_POST['lastMoveId'])) {
        //         $checkDublteStorage = mysqli_query($db_con, "Select * from tbl_storage_level where sl_id = '$slid'") or die('Error in checkDublteStorage:' . mysqli_error($db_con));

        //         $rwcheckDublteStorage = mysqli_fetch_assoc($checkDublteStorage);
        //         $lmoveid = preg_replace("/[^A-Za-z0-9 ]/", "", $_POST['lastMoveId']);
        //         $sql_child = "select * FROM tbl_storage_level WHERE sl_parent_id = '$lmoveid' AND sl_name = '$rwcheckDublteStorage[sl_name]'";

        //         $sql_child_run = mysqli_query($db_con, $sql_child) or die('Error:' . mysqli_error($db_con));

        //         if (mysqli_num_rows($sql_child_run)) {
        //             $moveToId = preg_replace("/[^A-Za-z0-9 ]/", "", $_POST['lastMoveId']);
        //             $moveToName = mysqli_query($db_con, "Select * from tbl_storage_level where sl_id = '$moveToId'") or die('Error in checkDublteStorage:' . mysqli_error($db_con));
        //             $rwmoveToName = mysqli_fetch_assoc($moveToName);
        //             $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$moveToId','Storage $rwFolder[sl_name] already exist in $rwmoveToName[sl_name].','$date', null,'$host','')") or die('error : ' . mysqli_error($db_con));
        //             echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Strg_Nme_Having_Same_Name_Already_Exist'] . '");</script>';
        //         } else {
        //             mysqli_set_charset($db_con, "utf8");
					
		// 			$sql_child1 = "select sl_depth_level FROM tbl_storage_level WHERE sl_id = '$_POST[lastMoveId]'";

		// 			$levelres = mysqli_query($db_con, $sql_child1); //or die('Error:' . mysqli_error($db_con));
		// 			 $levelrow = mysqli_fetch_assoc($levelres);
					
        //             $moveToId = preg_replace("/[^A-Za-z0-9 ]/", "", $_POST['lastMoveId']);
        //             $lastMoveIdLevel = $levelrow['sl_depth_level'];
        //             $lastMoveIdLevel = $lastMoveIdLevel + 1;
					
        //             //echo "update tbl_storage_level set sl_parent_id = '$moveToId', sl_depth_level = '$lastMoveIdLevel' where sl_id = '$slid'"; die;
        //             $moveStorage = "update tbl_storage_level set sl_parent_id = '$moveToId', sl_depth_level = '$lastMoveIdLevel' where sl_id = '$slid'";
        //             $moveStorage_run = mysqli_query($db_con, $moveStorage) or die('Error in move Stroge : ' . mysqli_error($db_con));

        //             $moveToName = mysqli_query($db_con, "Select * from tbl_storage_level where sl_id = '$moveToId'") or die('Error in checkDublteStorage:' . mysqli_error($db_con));
        //             $rwmoveToName = mysqli_fetch_assoc($moveToName);
					
		// 			$slname = str_replace(" ", "", $rwcheckDublteStorage['sl_name']);
					
		// 			$updir = getStoragePath($db_con, $moveToId, $lastMoveIdLevel);

		// 			if(!empty($updir)){
		// 				$updir = $updir . '/';
		// 			}else{
		// 				$updir = '';
		// 			}
		// 			$folderpath = $updir.$slname;
					
		// 			if (!is_dir($folderpath)) {
		// 				mkdir($folderpath, 0777, TRUE) or die(print_r(error_get_last()));
		// 			}
					
		// 			// Connect to file server
		// 			$fileManager->conntFileServer();
					
		// 			moveStorageFiles($db_con, $rwcheckDublteStorage['sl_id'], $folderpath, $fileManager);
					
		// 			$moveStorage = mysqli_query($db_con, "select * from tbl_storage_level where sl_parent_id = '$slid'"); //or die('Error in checkDublteStorage:' . mysqli_error($db_con));
        //             while($rowm = mysqli_fetch_assoc($moveStorage)){
						
		// 				moveStorage($db_con, $rowm['sl_id'], $lastMoveIdLevel, $fileManager);
		// 			}

        //             $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$moveToId','Storage $rwFolder[sl_name] moved to $rwmoveToName[sl_name]','$date',null,'$host','')") or die('error : ' . mysqli_error($db_con));
        //             echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Strge_Moved_Scesfly'] . '");</script>';
        //         }
        //     }
        //     mysqli_close($db_con);
        // }
        if (isset($_POST['move'], $_POST['token'])) {
            if (!empty($_POST['lastMoveId'])) {
                $checkDublteStorage = mysqli_query($db_con, "Select * from tbl_storage_level where sl_id = '$slid'") or die('Error in checkDublteStorage:' . mysqli_error($db_con));

                $rwcheckDublteStorage = mysqli_fetch_assoc($checkDublteStorage);
                $storageName_From = $rwcheckDublteStorage['sl_name'];
                $storageName_From = str_replace(" ", "", $storageName_From);
                $storageName_From = preg_replace('/[^A-Za-z0-9\-]/', '', $storageName_From);

                $lmoveid = preg_replace("/[^A-Za-z0-9]/", "", $_POST['lastMoveId']);
                $sql_child = "select * FROM tbl_storage_level WHERE sl_parent_id = '$lmoveid' AND sl_name = '$rwcheckDublteStorage[sl_name]'";

                $sql_child_run = mysqli_query($db_con, $sql_child) or die('Error1234:' . mysqli_error($db_con));

                if (mysqli_num_rows($sql_child_run)) {
                    $moveToId = preg_replace("/[^A-Za-z0-9 ]/", "", $_POST['lastMoveId']);
                    $moveToName = mysqli_query($db_con, "Select * from tbl_storage_level where sl_id = '$moveToId'") or die('Error in checkDublteStorage:' . mysqli_error($db_con));
                    $rwmoveToName = mysqli_fetch_assoc($moveToName);
                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$moveToId','Storage $rwFolder[sl_name] already exist in $rwmoveToName[sl_name].','$date', null,'$host','')") or die('error : ' . mysqli_error($db_con));
                    echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Strg_Nme_Having_Same_Name_Already_Exist'] . '");</script>';
                } else {
                    mysqli_set_charset($db_con, "utf8");
                    $moveToId = preg_replace("/[^A-Za-z0-9 ]/", "", $_POST['lastMoveId']);
                    $lastMoveIdLevel = preg_replace("/[^A-Za-z0-9 ]/", "", $_POST['lastMoveIdLevel']);
                    $lastMoveIdLevel = $lastMoveIdLevel + 1;

                    ////////////////////D/////////////////////
                    $moveToDetail = mysqli_query($db_con, "Select * from tbl_storage_level where sl_id = '$moveToId'");
                    $rwmoveToDetail = mysqli_fetch_assoc($moveToDetail);
                    $storageName = $rwmoveToDetail['sl_name'];
                    $storageName = str_replace(" ", "", $storageName);
                    $storageName = preg_replace('/[^A-Za-z0-9\-]/', '', $storageName);

                    $updir_from = getStoragePath($db_con, $rwcheckDublteStorage['sl_parent_id'], $rwcheckDublteStorage['sl_depth_level']);
                    $updir_to = getStoragePath($db_con, $rwmoveToDetail['sl_parent_id'], $rwmoveToDetail['sl_depth_level']);

                    if (!empty($updir_from)) {
                        $updir_from = $updir_from . '/';
                    } else {
                        $updir_from = '';
                    }
                    if (!empty($updir_to)) {
                        $updir_to = $updir_to . '/';
                    } else {
                        $updir_to = '';
                    }

                    $uploaddir_from = $updir_from . $storageName_From;
                    $dir_from = 'extract-here/' . $uploaddir_from;
                    $uploaddir_to = $updir_to . $storageName;
                    $dir_to = 'extract-here/' . $uploaddir_to . '/' . $storageName_From;

                    if (!is_dir(dirname($dir_to))) {
                        mkdir(dirname($dir_to), 0777, true);
                    }

                    rename($dir_from, $dir_to);

                    $uploadInToFTP = false;
                    if (FTP_ENABLED) {

                        $ftp = new ftp();
                        $ftp->conn("$fileserver", "$port", "$ftpUser", "$ftpPwd");
                        $getAllFile = mysqli_query($db_con, "Select * from tbl_document_master where doc_name = '$slid'");
                        while ($fetchAllFile = mysqli_fetch_assoc($getAllFile)) {
                            $doc_EncryptFile = explode('/', $fetchAllFile['doc_path']);
                            $doc_Encrypt_nm = end($doc_EncryptFile);

                            if ($ftp->get($dir_to . '/' . $doc_Encrypt_nm, 'DMS/' . ROOT_FTP_FOLDER . '/' . $uploaddir_from . '/' . $doc_Encrypt_nm)) {

                                $destinationPath = $uploaddir_to . '/' . $storageName_From . '/' . $doc_Encrypt_nm;
                                $sourcePath = $dir_to . '/' . $doc_Encrypt_nm;

                                $uploadfile = $ftp->put('DMS/' . ROOT_FTP_FOLDER . '/' . $destinationPath, $sourcePath);

                                if ($uploadfile) {
                                    $uploadInToFTP = true;
                                    $ftp->singleFileDelete('DMS/' . ROOT_FTP_FOLDER . '/' . $uploaddir_from . '/' . $doc_Encrypt_nm);
                                    unlink($sourcePath);

                                    $moveFile = "update tbl_document_master set doc_path = '$destinationPath' where doc_id = '" . $fetchAllFile['doc_id'] . "'";
                                    $moveFile_run = mysqli_query($db_con, $moveFile) or die('Error in move Stroge : ' . mysqli_error($db_con));
                                } else {
                                    $uploadInToFTP = false;
                                    echo '<h2>Error:</h2>' . implode('<br />', $arr['error']);
                                }
                                $arr = $ftp->getLogData();
                            }
                        }
                    }


                    ////////////////////D/////////////////////

                    $moveStorage = "update tbl_storage_level set sl_parent_id = '$moveToId', sl_depth_level = '$lastMoveIdLevel' where sl_id = '$slid'";
                    $moveStorage_run = mysqli_query($db_con, $moveStorage) or die('Error in move Stroge : ' . mysqli_error($db_con));

                    $moveToName = mysqli_query($db_con, "Select * from tbl_storage_level where sl_id = '$moveToId'") or die('Error in checkDublteStorage:' . mysqli_error($db_con));
                    $rwmoveToName = mysqli_fetch_assoc($moveToName);

                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`, `sl_id`, `action_name`, `start_date`,`system_ip`, `remarks`) values ('" . $_SESSION['cdes_user_id'] . "', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','$moveToId','Storage moved','$date','$host','Storage $rwFolder[sl_name] moved to $rwmoveToName[sl_name]')") or die('error : ' . mysqli_error($db_con));
                    echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Strge_Moved_Scesfly'] . '");</script>';
                }
            }
            mysqli_close($db_con);
        }
        ?>
        <!--copy storage-->
        <?php
//         if (isset($_POST['copyLevel'], $_POST['token'])) {
//             if (isset($_POST['toCopyFolder']) && !empty($_POST['toCopyFolder'])) {
//                 //$toCopyFolder = preg_replace("/[^A-Za-z0-9 ]/", "", $_POST['toCopyFolder']);
//                 $toCopyFolder = trim($_POST['toCopyFolder']);
//                 if (isset($_POST['lastCopyToId']) && !empty($_POST['lastCopyToId'])) {
// //                    $lastCopyToId = preg_replace("/[^A-Za-z0-9 ]/", "", $_POST['lastCopyToId']);
//                     $lastCopyToId = trim($_POST['lastCopyToId']);
// 					// Connect file server
// 					$fileManager->conntFileServer();
//                     copyStorage($slid, $lastCopyToId, $toCopyFolder, $date, $fileManager, $lang);
//                 }
//             }
//             mysqli_close($db_con);
//         }
if (isset($_POST['copyLevel'], $_POST['token'])) {
    if (isset($_POST['toCopyFolder']) && !empty($_POST['toCopyFolder'])) {
        $toCopyFolder = trim($_POST['toCopyFolder']);
        if (isset($_POST['lastCopyToId']) && !empty($_POST['lastCopyToId'])) {
            $lastCopyToId = trim($_POST['lastCopyToId']);
            copyStorage($slid, $lastCopyToId, $toCopyFolder, $date, $host, $fileserver, $port, $ftpUser, $ftpPwd, $lang, $db_con);
        }
    }
    mysqli_close($db_con);
}

        ?>

        <!--modify storage level starts-->
        <?php
        if (isset($_POST['update'], $_POST['token'])) {
            // $sl_id = preg_replace("/[^A-Za-z0-9 ]/", "", $_POST['modi']);
            // $modify = preg_replace("/[^A-Za-z0-9 ]/", "", $_POST['modify_slname']);
            // $parentid = preg_replace("/[^A-Za-z0-9 ]/", "", $_POST['modi_parentId']);
            // $modify = preg_replace('/[^a-zA-Z0-9-_ ]/', '', mysqli_real_escape_string($db_con, $_POST['modify_slname']));
            
            mysqli_set_charset($db_con, 'utf8');
            
            $sl_id = $_POST['modi'];
            
            $modify = trim($_POST['modify_slname']);
            
            $parentid = $_POST['modi_parentId'];

            $modify = trim($_POST['modify_slname']);
            //$slname = "select * from tbl_storage_level where sl_parent_id = '$parentid' AND sl_id != '$sl_id' AND sl_name = '$modify'";
            $checkSlName = mysqli_query($db_con, "select * from tbl_storage_level where sl_parent_id = '$parentid' AND sl_id != '$sl_id' AND sl_name = '$modify'"); //or die('Error in check DublteStorage:' . mysqli_error($db_con));
            if (mysqli_num_rows($checkSlName) > 0) {
                echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Strg_of_Same_Nme_Already_Exst'] . '");</script>';
            } else {
                mysqli_set_charset($db_con, 'utf8');
                $modiStorage = mysqli_query($db_con, "Select * from tbl_storage_level where sl_id = '$sl_id'"); //or die('Error in get sl name:' . mysqli_error($db_con));
                $rwmodiStorage = mysqli_fetch_assoc($modiStorage);
                $updateToName = $rwmodiStorage['sl_name'];
                $sql = "update tbl_storage_level set sl_name = '$modify' WHERE sl_id = '$sl_id' ";
                $sql_run = mysqli_query($db_con, $sql); //or die("error:" . mysqli_errno($db_con));
                if ($sql_run) {
                    mysqli_set_charset($db_con, "utf8");
                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$sl_id','Storage $updateToName rename to $modify.','$date', null,'$host','')"); //or die('error : ' . mysqli_error($db_con));
                    echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Strg_Updtd_Sucsfly'] . '");</script>';
                } else {
                    echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Strg_Updn_Fld'] . '");</script>';
                }
            }
            mysqli_close($db_con);
        }
        ?>
        <!---delete storage level start---->
        <?php
        // if (isset($_POST['deleted'], $_POST['token'])) {
			

        //     $checkdelete = $_POST['deleted'];

        //     $sl_id = preg_replace("/[^A-Za-z0-9 ]/", "", $_POST['delsl']);
        //     $sl_id = mysqli_real_escape_string($db_con, $sl_id);
        //     $deleteStorage = mysqli_query($db_con, "Select * from tbl_storage_level where sl_id = '$sl_id'"); //or die('Error :' . mysqli_error($db_con));
			
        //     $rwdeleteStorage = mysqli_fetch_assoc($deleteStorage);
		// 	$slname = str_replace(" ", "", $rwdeleteStorage['sl_name']);
		// 	$updir = getStoragePath($db_con, $rwdeleteStorage['sl_parent_id'], $rwdeleteStorage['sl_depth_level']);

		// 	if(!empty($updir)){
		// 		$updir = $updir . '/';
		// 	}else{
		// 		$updir = '';
		// 	}
			
        //     $deletStorageName = $rwdeleteStorage['sl_name'];
            
        //     $dirPath = "extract-here/" .$updir.$slname;
        //     $delStrg = mysqli_query($db_con, "Select sl_id from tbl_storagelevel_to_permission where user_id = '$_SESSION[cdes_user_id]'"); //or die('Error :' . mysqli_error($db_con));
        //     $rwdelStrg = mysqli_fetch_assoc($delStrg);
			
			
        //     if ($rwdelStrg['sl_id'] != $sl_id) {
               
        //         mysqli_set_charset($db_con, "utf8");
                
        //         if($checkdelete=='yes'){
		// 			// Connect to file server
		// 			$fileManager->conntFileServer();
					
        //             mysqli_query($db_con, "DELETE FROM tbl_storage_level WHERE sl_id='$sl_id'"); //or die('Error:' . mysqli_error($db_con));

        //             deleteDocument($db_con, $sl_id, $dirPath, $fileManager);

        //             deleteSubFolders($db_con, $sl_id, $fileManager, $checkdelete);

        //             //rmdir($dirPath);
					
        //         }else{

        //             mysqli_query($db_con, "UPDATE tbl_storage_level set delete_status=1 WHERE sl_id='$sl_id'");

        //             moveFilesInRecycleBin($db_con, $sl_id, 3);

        //             moveStorageInRecycleBin($db_con, $sl_id);
        //         }
				
        //         $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$sl_id','Storage Name $deletStorageName deleted.','$date', null,'$host','')") or die('error :' . mysqli_error($db_con));
        //         $delParentId = $rwdeleteStorage['sl_parent_id'];
        //         echo'<script>taskSuccess("storage?id='. urlencode(base64_encode($delParentId)) .'","' . $lang['Strg_Deleted_Successfully'] . '");</script>';
        //     } else {
        //         echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['root_storage_cannot_deleted'] . '");</script>';
        //     }
        //     mysqli_close($db_con);
        // }
        if (isset($_POST['deleted'], $_POST['token'])) {
            $checkdelete = $_POST['deleted'];
            $sl_id = preg_replace("/[^A-Za-z0-9 ]/", "", $_POST['delsl']);
            $sl_id = mysqli_real_escape_string($db_con, $sl_id);
            $deleteStorage = mysqli_query($db_con, "Select * from tbl_storage_level where sl_id = '$sl_id'"); //or die('Error :' . mysqli_error($db_con));
            $rwdeleteStorage = mysqli_fetch_assoc($deleteStorage);
            $deletStorageName = $rwdeleteStorage['sl_name'];
            $dirPath = "extract-here/" . str_replace(" ", "", $deletStorageName);
            $delStrg = mysqli_query($db_con, "Select sl_id from tbl_storagelevel_to_permission where user_id = '" . $_SESSION['cdes_user_id'] . "'"); //or die('Error :' . mysqli_error($db_con));
            $rwdelStrg = mysqli_fetch_assoc($delStrg);
            if ($rwdelStrg['sl_id'] != $sl_id) {
                //delStrg($sl_id, $fileserver, $port, $ftpUser, $ftpPwd);
                mysqli_set_charset($db_con, "utf8");
                if ($checkdelete == 'yes') {
                    mysqli_query($db_con, "DELETE FROM tbl_storage_level WHERE sl_id='$sl_id'"); //or die('Error:' . mysqli_error($db_con));

                    deleteDocument($db_con, $sl_id, $dirPath, $fileserver, $port, $ftpUser, $ftpPwd);

                    deleteSubFolders($db_con, $sl_id, $fileserver, $port, $ftpUser, $ftpPwds, $checkdelete);

                    rmdir($dirPath);
                } else {

                    mysqli_query($db_con, "UPDATE tbl_storage_level set delete_status=1 WHERE sl_id='$sl_id'");

                    moveFilesInRecycleBin($db_con, $sl_id, 3);

                    moveStorageInRecycleBin($db_con, $sl_id);
                }
                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`, `sl_id`, `action_name`, `start_date`, `system_ip`, `remarks`) values ('" . $_SESSION['cdes_user_id'] . "', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','$sl_id','Subfolder deleted','$date','$host','Storage Name $deletStorageName deleted.')") or die('error :' . mysqli_error($db_con));

                echo'<script>taskSuccess("' . basename($_SERVER['SCRIPT_NAME']) . '?id=' . urlencode(base64_encode($delParentId)) . '","' . $lang['Strg_Deleted_Successfully'] . '");</script>';
            } else {
                echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['root_storage_cannot_deleted'] . '");</script>';
            }
            mysqli_close($db_con);
        }
        ?>
        <!--Add Storage Level -->
        <?php
        if (isset($_POST['add_storage'], $_POST['token'])) {
            $sl_id = preg_replace("/[^A-Za-z0-9 ]/", "", $_POST['add_child']);
            $sl_id = mysqli_real_escape_string($db_con, $sl_id);
            // $create = mysqli_real_escape_string($db_con, $create);
            // $create = preg_replace('/[^a-zA-Z0-9-_ ]/', '', mysqli_real_escape_string($db_con, $_POST['create_child']));
            $create = trim($_POST['create_child']);
            $checkLvlName = mysqli_query($db_con, "select * from tbl_storage_level where sl_parent_id='$sl_id' AND sl_name = '$create' and delete_storage=0"); //or die('Error in checkLvlName:' . mysqli_error($db_con));
            if (mysqli_num_rows($checkLvlName) > 0) {

                echo'<script>taskFailed("' . basename($_SERVER[REQUEST_URI]) . '","' . $lang['Storage_Name_Already_Exist'] . '");</script>';

            } else {

                $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$sl_id'"); //or die('Error:' . mysqli_error($db_con));

                $rwParent = mysqli_fetch_assoc($parent);

                $level = $rwParent['sl_depth_level'] + 1;
                if (!empty($create)) {
                    mysqli_set_charset($db_con, "utf8");
                    $new_child_pass = $rwParent['password'];
                    $sql = "insert into tbl_storage_level(sl_id, sl_name, sl_parent_id, sl_depth_level)VALUES (null, '$create', '$sl_id', '$level')";
                    $sql_run = mysqli_query($db_con, $sql); //or die("error:" . mysqli_error($db_con));
                    $newChildId = mysqli_insert_id($db_con);

                    if ($rwParent['is_protected'] == 1) {
                        mysqli_query($db_con, "update tbl_storage_level set is_protected='1',password='$new_child_pass' where sl_id='$newChildId'");
                    }

                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$newChildId','New Sub-folder $create Created.','$date', null,'$host','')"); //or die('error :' . mysqli_error($db_con));
                    echo'<script>taskSuccess("' . basename($_SERVER[REQUEST_URI]) . '","' . $lang['Child_Created_Successfully'] . '");</script>';
                }
            }
            mysqli_close($db_con);
        }
        ?>                
        <!--update metadata value-->

        <script type="text/javascript">
        
            $(document).ready(function () {
                $("#select_all").change(function () {
                    $(".emp_checkbox").prop("checked", $(this).prop("checked"));
                });
            });
            //Extraxt CSV 
            $(document).ready(function () {
                function exportTableToCSV($table, filename) {
                    var $rows = $table.find('tr:has(td),tr:has(th)'),
                            //var $rows = $table.filter('tr:has(:checkbox:checked)').find('tr:has(td),tr:has(th)'),

                            tmpColDelim = String.fromCharCode(11),
                            tmpRowDelim = String.fromCharCode(0),
                            colDelim = '","',
                            rowDelim = '"\r\n"',
                            csv = '"' + $rows.map(function (i, row) {
                                var $row = $(row), $cols = $row.find('td,th');

                                return $cols.map(function (j, col) {
                                    var $col = $(col), text = $col.text();

                                    return text.replace(/"/g, '""');
                                }).get().join(tmpColDelim);

                            }).get().join(tmpRowDelim)
                            .split(tmpRowDelim).join(rowDelim)
                            .split(tmpColDelim).join(colDelim) + '"',
                            csvData = 'data:application/csv;charset=utf-8,' + encodeURIComponent(csv);

                    console.log(csv);

                    if (window.navigator.msSaveBlob) {
                        window.navigator.msSaveOrOpenBlob(new Blob([csv], {type: "text/plain;charset=utf-8;"}), "csvname.csv")
                    } else {
                        $(this).attr({'download': filename, 'href': csvData, 'target': '_blank'});
                    }
                }

                $("#down").on('click', function (event) {

                    exportTableToCSV.apply(this, [$('#home-table'), 'data.csv']);

                });
            });
            
        </script>
        <?php
        //Bulk Download
        if (isset($_POST['bulkDownload'], $_POST['token'])) {
            
            $rad = preg_replace("/[^A-Za-z0-9, ]/", "", $_POST['raddwn']);
            $rad = mysqli_real_escape_string($db_con, $rad);
            $slid = preg_replace("/[^A-Za-z0-9, ]/", "", $_POST['slid']);
            $slid = mysqli_real_escape_string($db_con, $slid);
            //$reason = preg_replace("/[^A-Za-z0-9, ]/", "", $_POST['reason']);
            $reason = xss_clean(trim($_POST['reason']));
            //$reason = mysqli_real_escape_string($db_con, $reason);
            $archive_file_name = $slName . '.zip';

            $download = mysqli_query($db_con, "select doc_path,old_doc_name,doc_extn,doc_id from tbl_document_master where doc_name='$slid' and flag_multidelete=1"); // or die('Error'.mysqli_error($db_con));
            $zip = new ZipArchive();
            //create the file and throw the error if unsuccessful
            if ($zip->open($archive_file_name, ZIPARCHIVE::CREATE) !== TRUE) {
                exit("cannot open <$archive_file_name>\n");
            }
            $zippedFilePath = array();
			// Connect to file server
			$fileManager->conntFileServer();
            while ($row = mysqli_fetch_assoc($download)) {
                mysqli_set_charset($db_con, "utf8");
                $docPath = $row['doc_path'];
                $file_path = 'extract-here/' . substr($docPath, 0, strrpos($docPath, "/") + 1);
                $files = substr($docPath, strrpos($docPath, "/") + 1);
                $comp_folder = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id = '$slid'"); //or die('Error :' . mysqli_error($db_con));
                $rwfolder = mysqli_fetch_assoc($comp_folder);
                $file1 = $row['old_doc_name'];
                //$file1 = $row['old_doc_name'] . '.' . $row['doc_extn'];
				if (!is_dir($file_path)) {
					mkdir($file_path, 0777, TRUE) or die(print_r(error_get_last()));
				}
				if(!file_exists('extract-here/' . $docPath)){
					if($fileManager->downloadFile('DMS/' . ROOT_FTP_FOLDER . '/' . $docPath,  'extract-here/' . $docPath)){
						decrypt_my_file('extract-here/' . $docPath);
						if ($zip->addFile($file_path . $files, $file1)) {
							$zippedFilePath[] = 'extract-here/' . $docPath;
						}
					}
				}else{
						decrypt_my_file('extract-here/' . $docPath);
						if ($zip->addFile($file_path . $files, $file1)) {
							$zippedFilePath[] = 'extract-here/' . $docPath;
						}
				}
            }
            if ($zip->close()) {
                //if (FTP_ENABLED) {
                    /* foreach ($zippedFilePath as $key => $value) {

                        unlink($zippedFilePath[$key]);
                    } */
                //}
            }
            //then send the headers to foce download the zip file
            header("Pragma: public");
            header("Expires: 0");
            header("Content-type: application/zip");
            header("Content-Disposition: attachment; filename=\"" . $archive_file_name . "");
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `doc_id`,`action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$slid', '$row[doc_id]','Storage document $old_doc_name compress to Storage $rwfolder[sl_name] with $row[old_doc_name].','$date',null,'$host','$reason')"); //or die('error : ' . mysqli_error($db_con));
            readfile($archive_file_name);
            unlink($archive_file_name);
            mysqli_close($db_con);
            
        }
        ?>
        <?php
        //comman code @dv 12/03/19
        require_once 'file-movement.php';
        ?>
        <?php
        //comman code @dv 12/03/19
        require_once 'file-action-php.php';
        ?>
        <!-- for shared folder -->
        <?php
        if (isset($_POST['shareFolder'], $_POST['token'])) {
            // print_r($_POST);
            $sharewithUsers = $_POST['sharewith'];
            $readonly = ((!empty($_POST['readonly'])) ? 1 : 0);
			
            $sharewith = implode(',', $sharewithUsers);
            $slId = $_POST['slId'];
            $folder = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid'");
            $rwFolder = mysqli_fetch_assoc($folder);
            $shareby = $_SESSION['cdes_user_id'];
            for ($k = 0; $k < count($sharewithUsers); $k++) {
                $check = mysqli_query($db_con, "select * from tbl_folder_share where share_with='$sharewithUsers[$k]' and slId='$slId'"); //or die('Error :' . mysqli_error($db_con));
                if (mysqli_num_rows($check) > 0) {
                    echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['folder_already_share'] . '");</script>';
                } else {
                    $sql = mysqli_query($db_con, "INSERT INTO tbl_folder_share (slId, share_with, share_by) values('$slId', '$sharewithUsers[$k]', '$shareby')"); //or die('Error :' . mysqli_error($db_con));
                    if ($sql) {
                        $username = mysqli_query($db_con, "select first_name,last_name from tbl_user_master where user_id='$sharewithUsers[$k]'");
                        $rwusername = mysqli_fetch_assoc($username);
                        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `doc_id`,`action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$slid', NULL,'$rwFolder[sl_name] storage Shared with $rwusername[first_name] $rwusername[last_name]','$date',null,'$host',NULL)"); //or die('error : ' . mysqli_error($db_con));
                        if (checkFolderPermission($db_con, $sharewithUsers[$k], $slId)) {

                            $slins = mysqli_query($db_con, "insert into tbl_storagelevel_to_permission(user_id,sl_id,shared,readonly) values('$sharewithUsers[$k]','$slId', '1', '$readonly')");
                            if($readonly){
								$slins = mysqli_query($db_con, "update tbl_storage_level set readonly='$readonly' where sl_id='$slId'");
							}
                        } else {
                            
                        }

                        echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Folder_Share_success'] . '");</script>';
                    }
                }
            }
        }
          //ankit 01 june 2023
    if (isset($_POST['rename'], $_POST['token'])) {
        mysqli_set_charset($db_con, 'utf8');

        $nwfilnme = mysqli_real_escape_string($db_con, $_POST['filename']);
        $docId = mysqli_real_escape_string($db_con, $_POST['docId']);
        $slId = mysqli_real_escape_string($db_con, $_POST['slId']);
        $oldname = mysqli_query($db_con, "select doc_name,old_doc_name, doc_extn from tbl_document_master where doc_id ='$docId'");
        $rwoldname = mysqli_fetch_assoc($oldname);
        $stordocId = $rwoldname['doc_name'];
        $nwfilnme = $nwfilnme . '.' . $rwoldname['doc_extn'];
        $nmevalidate = mysqli_query($db_con, "select old_doc_name, doc_extn from tbl_document_master where old_doc_name ='$nwfilnme'  and doc_name ='$stordocId'");
        
        if (mysqli_num_rows($nmevalidate) == 0) {
            $rename = mysqli_query($db_con, "update tbl_document_master set old_doc_name='$nwfilnme' where doc_id ='$docId'");
            if ($rename) {
                mysqli_set_charset($db_con, 'utf8');
                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`, `action_name`, `start_date`, `system_ip`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Document name $rwoldname[old_doc_name] to $nwfilnme renamed','$date','$host')"); // or die('error : ' . mysqli_error($db_con));
                echo '<script>taskSuccess("' . basename($_SERVER[REQUEST_URI]) . '", "' . $lang['file_rename_success'] . '");</script>';
            } else {
                echo '<script>taskFailed("' . basename($_SERVER[REQUEST_URI]) . '", "' . $lang['file_rename_failed'] . '");</script>';
            }
        } else {
            echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","File Name Already Exists");</script>';
        }

        mysqli_close($db_con);
    }
    //endankit 01 june 2023


    //ankit 05 june 2023
    if (isset($_POST['multirename'], $_POST['token'])) {
        mysqli_set_charset($db_con, 'utf8');
        $nwfilnme = $_POST['filename'];
        $docId = mysqli_real_escape_string($db_con, $_POST['docId']);

        $docId = explode(",", $docId);
        $slId = mysqli_real_escape_string($db_con, $_POST['slId']);
        foreach ($docId as $key => $id) {
            $nwfil = $nwfilnme[$key];
            $oldname = mysqli_query($db_con, "select doc_name,old_doc_name, doc_extn from tbl_document_master where doc_id =' $id'");
         
            $rwoldname = mysqli_fetch_assoc($oldname);
            $stordocId = $rwoldname['doc_name'];
            $nwfilnmed = $nwfil . '.' . $rwoldname['doc_extn'];


            $nmevalidate = mysqli_query($db_con, "select old_doc_name, doc_extn from tbl_document_master where old_doc_name ='$nwfilnmed' and doc_name ='$stordocId'");
          if (mysqli_num_rows($nmevalidate) == 0) {
            $rename = mysqli_query($db_con, "update tbl_document_master set old_doc_name='$nwfilnmed' where doc_id ='$id'");
            if ($rename) {
                mysqli_set_charset($db_con, 'utf8');
                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`, `action_name`, `start_date`, `system_ip`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Document name $rwoldname[old_doc_name] to $nwfilnmed renamed','$date','$host')"); // or die('error : ' . mysqli_error($db_con));
               
            } 
         } 
        
    }
    if ($rename) {echo '<script>taskSuccess("' . basename($_SERVER[REQUEST_URI]) . '", "' . $lang['file_rename_success'] . '");</script>';
    
    } else {
        echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","File Name Already Exists");</script>';
    }
        mysqli_close($db_con);
    }
    //endankit 05 june 2023

        function checkFolderPermission($db_con, $userId, $slId) {
            $result1 = mysqli_query($db_con, "select * from tbl_storagelevel_to_permission where user_id='$userId' and sl_id='$slId'");
            if (mysqli_num_rows($result1) > 0) {
                return false;
            } else {
                $result = mysqli_query($db_con, "select * from tbl_storagelevel_to_permission where user_id='$userId'");
                $slArray = array();
                while ($rowP = mysqli_fetch_assoc($result)) {
                    $checkPermission = mysqli_query($db_con, "select sl_parent_id from tbl_storage_level where sl_parent_id ='" . $rowP['sl_id'] . "' and sl_id='$slId'");
                    if (mysqli_num_rows($checkPermission) > 0) {
                        $slArray[] = $rowP['sl_id'];
                    } else {
                        
                    }
                }
                if (count($slArray) > 0) {
                    return false;
                } else {
                    return true;
                }
            }
        }


        
        ?>

        <!--modal for download doc-->
        <div id="myModal" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <form>
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                            <h4 class="modal-title"><?= $lang['peyp']; ?></h4>
                    </div>
                    <input type="password" class="form-control" placeholder="<?= $lang['peyp']; ?>" id="pass_value" autocomplete="off" autofocus>
                    <div class="modal-footer">
                        <input type="hidden" value="<?php echo $pass_check['password']; ?>" id="doc_pass">			  
                        <input type="hidden" value="" id="docDId">			  
                        <input type="submit" class="btn btn-danger" id="enter_btn" value="Enter" onclick="return password_check(event)">
                    </div>
                    </form>
                </div>

            </div>
        </div>
            <?php require_once './lock_folder_html.php'; ?>
            <?php require_once './lock_folder_php.php'; ?>
        <script>
           
            $("#unlock-folder").on("hidden.bs.modal", function () {
                $("#unlockfolder").html("");
                window.location.reload();
            });
            $("#lock-folder").on("hidden.bs.modal", function () {
                $("#lockfolder").html("");
                window.location.reload();
            });
            $("#update-folder-password").on("hidden.bs.modal", function () {

                $("#old_pass").html("");
                $("#new_pass").html("");
                window.location.reload();
            });
            
                
        </script>
        <script>
            function password_check(event)
            {
                event.preventDefault();

                var pass = $("#pass_value").val();
                var password = $("#doc_pass").val();
                var docDId = $("#docDId").val();
                var fpass = SHA1(pass);

                if (password == fpass)
                {
                    window.open('downloaddoc?file=' + docDId);
                    $('#myModal').modal('hide');

                } else
                {
                    taskFailed("<?php echo basename($_SERVER['REQUEST_URI']); ?>", "<?= $lang['pass_valid']; ?>");
                }
            }
            function setDownloadDocId(docId) {
                $(this).removeData('myModal');
                $("#docDId").val(docId);
            }
            
            /* $("input[name='bulkDownload']").click(function () {
                setTimeout(function(){ location.reload(); }, 2000);

            }); */
            //            
            $("#downloadselectedfile").click(function () {
                //setTimeout(function(){ location.reload(); }, 2000);
            });

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

            //rename single file
            $("a#renamefile").click(function() {
            // alert("okasyyyy");
            var id = $(this).attr('data');
            //alert(id);
            $.post("application/ajax/rename_document_file.php", {
                FID: id
            }, function(result, status) {

                if (status == 'success') {
                    $("#refilee").html(result);
                    //alert(result);
                }
            });
        });
        //Rename multiple files
        $("#renmefile").click(function() {
            var id = $(this).attr('data');
            var file = [];
            $(".emp_checkbox:checked").each(function() {
                file.push($(this).data('doc-id'));
            });

            if (file.length <= 0) {
            $("#unselectdocument").show();
            $("#filedocument").hide();
          
        } else {
            $("#unselectdocument").hide();
            $("#filedocument").show();
    
        }
            var selected_values = file.join(",");
            $.post("application/ajax/Multiple_Document_rename.php", {
                FID: selected_values
            }, function(result, status) {

                if (status == 'success') {
                    $("#multirefilee").html(result);
                }
            });
        });
        </script>

    </body>
</html>
