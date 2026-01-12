<!DOCTYPE html>
<html>
    <?php
    ini_set('memory_limit', '-1');
    set_time_limit(0);
    require_once './loginvalidate.php';
    //require_once './application/config/database.php';
    require_once './application/pages/head.php';
    require_once './application/pages/function.php';
    require_once './classes/ftp.php';
    require_once './application/pages/feature-enable-disable.php';
    require_once './excel-viewer/excel_reader.php';
    require_once './classes/fileManager.php';

	$fileManager = new fileManager();
	
    $sharefeatureenable = $rwdocshare['docshare_enable_disable'];
  
    if ($rwgetRole['metadata_quick_search'] != '1') {
        header('Location: ./index');
    }
    if (isset($_GET['id']) && !empty($_GET['id'])) {

        $slid = urldecode(base64_decode($_GET['id']));
        $selectedStorage = preg_replace('/[^0-9]/', '', $slid);
        $folder = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$selectedStorage'");
        $rwFolder = mysqli_fetch_assoc($folder);
        $slid = $rwFolder['sl_id'];
        $parentid = $rwFolder['sl_name'];
    }
	
	$exportOcExtn = array('pdf', 'txt', 'jpeg', 'jpg', 'png');
	
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
                                        <a href="search"><?php echo $lang['Search']; ?></a>
                                    </li>
                                    <li class="active">
                                        <?php echo $lang['quich_search']; ?>
                                    </li>
                                    <li> <a href="#" data-toggle="modal" data-target="#help-modal" id="helpview" data="47" title="<?= $lang['help']; ?>"><i class="fa fa-question-circle"></i> </a></li>
                                    <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                                </ol>
                            </div>
                        </div>
                        <div class="box box-primary">

                            <div class="box-body ">
                                <div class="card-box">
                                    <div class="row">
                                        <form method="get">
                                            <div class="col-md-12">
                                                <div class="col-md-4">
                                                    <input type="text" id="searchText1" class="form-control translatetext" name="searchText" value="<?php echo $_GET['searchText'] ?>" placeholder="<?php echo $lang['entr_srch_txt_hr']; ?>">
                                                </div>

                                                <!-- <div class="col-md-4"> -->
                                                    <!-- <select class="form-control parent select2" data-live-search="true" id="parent" name="id">
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
                                                    </select>  -->
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

                                                <!-- </div> -->

                                                <div class="col-md-6">
                                                    <div class="radio-filter">
														<input type="radio" id="files" name="searchby" value="files" class="radio" <?php if (isset($_GET['searchby']) and $_GET['searchby'] == 'files') { ?> checked="checked" <?php }else{ ?> checked="checked" <?php } ?> >
                                                        <label for="files">Files Only</label> 
														
                                                        <input type="radio" id="both" name="searchby" value="both" class="radio" <?php if ((isset($_GET['searchby']) and $_GET['searchby'] == 'both')) { ?> checked="checked" <?php } ?>>
                                                        <label for="both">Files & Folders</label>

                                                        

                                                        <input type="radio" id="folder" name="searchby" value="folder" class="radio" <?php if (isset($_GET['searchby']) and $_GET['searchby'] == 'folder') { ?> checked="checked" <?php } ?>>
                                                        <label for="folder">Folders Only</label>
                                                    </div>
                                                </div>

                                                <div class="col-md-2">
													
                                                    <button type="submit" class="btn btn-primary" id="search" title="<?php echo $lang['Search']; ?>"><i class="fa fa-search"></i></button>
                                                    <a href="search" class="btn btn-warning" title="<?php echo $lang['Reset']; ?>"><?php echo $lang['Reset']; ?></a>
                                                </div>
                                            </div>

                                        </form>
                                    </div>
                                </div>
                                <div class="container">
                                    <?php
                                    if (isset($slid) && !empty($slid)) {
                                        $slprem = $slid;
                                    } else {
                                        $perm = mysqli_query($db_con, "select sl_id from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]' group by sl_id");
                                        $rwPerm = mysqli_fetch_assoc($perm);
                                        $slperm = $rwPerm['sl_id'];
                                        $slprem = preg_replace("/[^0-9]/", "", $slperm);
                                    }
                                    $sllevel = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slperm' and delete_status=0");
                                    $rwSllevel = mysqli_fetch_assoc($sllevel);
                                    $level = $rwSllevel['sl_depth_level'];
                                    $level = preg_replace("/[^0-9]/", "", $level);
                                    //echo $slperm;

                                    if (!isset($_GET['searchby']) or $_GET['searchby'] == 'folder' or $_GET['searchby'] == 'both') {
                                        $searchby = trim($_GET['searchby']);
                                        $searchby = preg_replace("/[^\w$\x{0080}-\x{FFFF}!(){}+=~@%&#.-_ ]+/u", "", $searchby);
                                        $searchby = mysqli_real_escape_string($db_con, $searchby);

                                        $searchText = trim($_GET['searchText']);
                                        // $searchText = preg_replace("/[^\w$\x{0080}-\x{FFFF}!(){}+=~@%&#.-_ ]+/u", "", $searchText);
                                        $searchText = mysqli_real_escape_string($db_con, $searchText);


                                        $allchild = findChildIds($slperm);
                                        $childInString = implode("','", $allchild);
										
                                        
                                    }


                                    if (isset($_GET['searchText'])) {
										
										
										$st = storageLevelNameByslid($db_con, $lang, $childInString, $searchText);
										if ($st == '0') {
											echo '<div class="text-center text-danger">No Folder found</div>';
										} else {
											?>
											<div class="m-b-90"></div>
											<?php
										}
										
                                        if ((isset($_GET['searchby']) and $_GET['searchby'] != 'folder') or ! isset($_GET['searchby'])) {
											
											
											
                                            $searchText = trim($_GET['searchText']);
                                            // $searchText = preg_replace("/[^\w$\x{0080}-\x{FFFF}!(){}+=~@%&#.-_ ]+/u", "", $searchText);

                                            $searchText = mysqli_real_escape_string($db_con, $searchText);
                                            // echo $searchText;exit;
                                            $res = searchAllDB($searchText, $db_con, $slpermIdes, $lang, $rwgetRole, $sharefeatureenable, $slid);
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
        </div>
        <!-- END wrapper -->
        <?php require_once './application/pages/footerForjs.php'; ?>
        <script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>
        <?php
        $aa = array();

        function findChildIds($slperm) {
            global $db_con;
            global $aa;
			foreach(explode(",", $slperm) as $slperm){
				
				$aa[] = preg_replace("/[^A-Za-z0-9, ]/", "", $slperm);
				$sql_child = "select * FROM tbl_storage_level WHERE sl_parent_id in('$slperm') and delete_status=0 ";
				$sql_child_run = mysqli_query($db_con, $sql_child) or die('Error:' . mysqli_error($db_con));
				if (mysqli_num_rows($sql_child_run) > 0) {

					while ($rwchild = mysqli_fetch_assoc($sql_child_run)) {

						$child = preg_replace("/[^A-Za-z0-9 ]/", "", $rwchild['sl_id']);
						//$aa[] = $child;

						$clagain = findChildIds($child);
					}
				}
				
			}
			return $aa;
        }

        function searchAllDB($search, $db_con, $slperm, $lang, $rwgetRole, $sharefeatureenable, $slid) {
            ?>
            <table class="table table-striped table-bordered" id="datatable">
                <?php
                $searchFlag = false;

                $table = "tbl_document_master";
                $sql_search = "select doc_extn,doc_name,doc_id,doc_path,old_doc_name,doc_size,noofpages,checkin_checkout from " . $table . " where ";
                // findChild($slperm);
                $sql_search_fields = Array();
                $fields = array();
                $sql2 = "SHOW COLUMNS FROM " . $table;
                mysqli_set_charset($db_con, 'utf8');


                $rs2 = mysqli_query($db_con, $sql2);

                if (mysqli_num_rows($rs2) > 0) {
                    echo '<thead><tr>';
                    ?>
                    <th>
                        <div class="checkbox checkbox-primary"><input  type="checkbox" class="checkbox-primary" id="select_all"> <label for="select_all"> <strong><?php echo $lang['All']; ?></strong></label></div> 

                    </th>
                    <?php
                    echo '<th>' . $lang['File_Name'] . '</th>';
                    echo '<th>' . $lang['File_Size'] . '</th>';
                    echo '<th>' . $lang['No_of_Pages'] . '</th>';
                    echo '<th>' . $lang['Storage_Name'] . '</th>';

                    echo '<th>' . $lang['MetaData'] . '</th>';
                    echo '<th>' . $lang['Action'] . '</th>';
                    while ($r2 = mysqli_fetch_array($rs2)) {
                        $colum = $r2[0];
                        if ($colum != 'doc_id') {
                            $sql_search_fields[] = 'CONVERT(`' . $colum . "` USING utf8) like('%" . $search . "%')";
                            $fields[] = $colum;
                        }
                    }
                    echo'</tr></thead>';
                    mysqli_close($rs2);
                }
                if (empty($slid)) {
                    $marray = findChildIds($slperm);
                    $sql_search .= "( doc_name=";
                    $sql_search .= implode(" OR doc_name=", $marray);
                    $sql_search .= ") and (";
                    $sql_search .= implode(" OR ", $sql_search_fields);
                } else {
                    $marray = $slid;
                    $sql_search .= "( doc_name='$marray'";
                    $sql_search .= ") and (";
                    $sql_search .= implode(" OR ", $sql_search_fields);
                }
               $sql_search .= ") and flag_multidelete=1";
				
			

                $rs3 = mysqli_query($db_con, $sql_search);

                echo'<tbody>';
                if (mysqli_num_rows($rs3) > 0) {

                    $searchFlag = true;
                    //echo'Database search';

                    $i = 1;
                    $img_array = array('jpg', 'jpeg', 'png', 'bmp', 'pnm', 'jfif', 'jpeg', 'tiff');
                    while ($rw = mysqli_fetch_assoc($rs3)) {
                        $shareDid = mysqli_query($db_con, "select doc_ids from tbl_document_share where doc_ids= '$rw[doc_id]'") or die("Error: " . mysqli_error($db_con));
                        $shreCount = mysqli_num_rows($shareDid);
                        $subscribeid = mysqli_query($db_con, "select * from tbl_document_subscriber where subscribe_docid= '$rw[doc_id]' and subscriber_userid='" . $_SESSION['cdes_user_id'] . "'") or die("Error: " . mysqli_error($db_con));
                        $subsCountId = mysqli_num_rows($subscribeid);

                        $sl_name = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$rw[doc_name]'");
                        $sl_row = mysqli_fetch_assoc($sl_name);
                        $storageName = str_replace(" ", "", $sl_row['sl_name']);
                        $storageName = preg_replace('/[^A-Za-z0-9\-_]/', '', $storageName);
                        $updir = getStoragePath($db_con, $sl_row['sl_parent_id'], $sl_row['sl_depth_level']);
                        if (!empty($updir)) {
                            $updir = $updir . '/';
                        } else {
                            $updir = '';
                        }

                        if ($rw['doc_extn'] == 'pdf' || in_array(strtolower($rw['doc_extn']), $img_array)) {


                            $foundPages = array();
                            for ($k = 0; $k < $rw['noofpages']; $k++) {

                                $file = "extract-here/" . $updir . $storageName . "/TXT/" . $rw['doc_id'] . ".txt";

                                $contents = file_get_contents($file);

                                if (stripos($contents, $search) != false) {
                                    $foundPages[] = $k;
                                }
                            }
                        } else if ($rw['doc_extn'] == 'text' || $rw['doc_extn'] == 'txt' || $rw['doc_extn'] == "xls" || strtolower($rw['doc_extn']) == 'xlsx' || $rw['doc_extn'] == "doc" || $rw['doc_extn'] == "docx" || $rw['doc_extn'] == "pptx" || $rw['doc_extn'] == "ppt") {
                            $file = "extract-here/" . $updir . $storageName . "/TXT/" . $rw['doc_id'] . ".txt";
                            $contents = file_get_contents($file);
                        }
                        $doc_name = explode('_', $rw['doc_name']);
                        if (count($doc_name) == 1) {

                            if (stripos($contents, $search) != false || count($foundPages) > 0) {
                                $pageNumbers = implode(',', $foundPages);
                                
								
								
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
                                        <div class="checkbox checkbox-primary m-r-15"> <input  type="checkbox" class="checkbox-primary emp_checkbox" data-doc-id="<?php echo $rw['doc_id']; ?>" id="shreId<?php echo $i; ?>"> <label for="shreId<?php echo $i; ?>"> <?php echo $i . '.'; ?> </label></div>

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
                    <?php
                    //                                        $checkfileLock = mysqli_query($db_con, "SELECT * FROM `tbl_locked_file_master` WHERE doc_id='" . $rw['doc_id'] . "' and is_active='1' and user_id='" . $_SESSION['cdes_user_id'] . "'");
                    //                                        $rwcheckfileLock = mysqli_fetch_assoc($checkfileLock);
                    //                                        if ($rwcheckfileLock['doc_id'] != $rw['doc_id']) {

                    $chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));
                    $rwgetRole = mysqli_fetch_assoc($chekUsr);


                    echo $rw['old_doc_name'];
                    $file_row = $rw;

                    require 'view-handler-search.php';
//                                        } else {
//                                            echo $rw['old_doc_name'];
                    ?>
                                            <!--a href="javascript:void(0)"  data="<?php echo $rw['doc_id'] ?>" class="send_lock_request" data-toggle="dropdown" aria-expanded="true"><i class="md md-lock" title="<?php //echo $lang['lock_file']; ?>"></i></a-->  
                                        <?php
                                        //}
                                        '</td>';
                                        echo'<td>' . formatSizeUnits($rw['doc_size']) . '</td>';
                                        echo'<td>' . $rw['noofpages'] . '</td>';
                                        ?>

                                    <td>
                                        <?php
                                        //storage name
                                        $strgName = mysqli_query($db_con, "select * from tbl_storage_level where sl_id = '" . $rw['doc_name'] . "'") or die('Error:' . mysqli_error($db_con));
                                        $rwstrgName = mysqli_fetch_assoc($strgName);
                                        //echo $rwstrgName['sl_name'];
                                        ?>
                                        <a href="storageFiles?id=<?php
                                        echo urlencode(base64_encode($rw['doc_name']));
                                        ?>"><i class="md md-my-library-books"></i> <?php echo $rwstrgName['sl_name']; ?> </a>
                                    </td>

                                    <td>

                    <?php
                    $getMetaId = mysqli_query($db_con, "select * from tbl_metadata_to_storagelevel where sl_id = '$rw[doc_name]'") or die('Error:' . mysqli_error($db_con));

                    while ($rwgetMetaId = mysqli_fetch_assoc($getMetaId)) {

                        $getMetaName = mysqli_query($db_con, "select * from tbl_metadata_master where id = '$rwgetMetaId[metadata_id]'") or die('Error:' . mysqli_error($db_con));

                        while ($rwgetMetaName = mysqli_fetch_assoc($getMetaName)) {

                            echo "<strong>" . $rwgetMetaName['field_name'] . "</strong>: ";
                            mysqli_set_charset($db_con, 'utf8');
                            //echo "select `$rwgetMetaName[field_name]` from tbl_document_master where doc_id='$rw[doc_id]'";
                            $meta = mysqli_query($db_con, "select `$rwgetMetaName[field_name]` from tbl_document_master where doc_id='$rw[doc_id]'");
                            $rwMeta = mysqli_fetch_array($meta);
                            if ($rwgetMetaName['data_type'] == 'datetime') {
                                if (!empty($rwMeta[$rwgetMetaName['field_name']])) {
                                    echo date('d-m-Y H:i', strtotime($rwMeta[$rwgetMetaName['field_name']]));
                                }
                            } else {
                                echo $rwMeta[$rwgetMetaName['field_name']];
                            }

                            echo " | ";
                        }
                    }
                    ?>
                                    </td>
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
                                            if ($rw['checkin_checkout'] == 1) {
                                                //require 'view-handler.php';
                                                ?>   
                                                    </li>
                                                    <li class="isprotected">
                                                        <?php
                                                        /* ------Lock file code----- */
														if(isFolderReadable($db_con, $sl_row['sl_id'])){
															if ($rwgetRole['lock_file'] == '1') {
																$checkfileLock = mysqli_query($db_con, "SELECT * FROM `tbl_locked_file_master` WHERE doc_id='$rw[doc_id]' and user_id='$_SESSION[cdes_user_id]' and is_active='1'");
																if (mysqli_num_rows($checkfileLock) > 0) {
																	$fetchdatalock = mysqli_fetch_assoc($checkfileLock);
																	if ($fetchdatalock['is_locked'] == "1") {
																		?>
																		<a href="javascript:void(0)" class="unlock_file" data="<?php echo $rw['doc_id']; ?>"> <i class="fa fa-unlock"  title="<?php echo $lang['unlock_file']; ?>"></i> <?php echo $lang['unlock_file']; ?></a>   
																		<?php
																	}
																} else {
																	?>
																	<a href="javascript:void(0)" class="lock_file" data="<?php echo $rw['doc_id'] ?>"> <i class="fa fa-lock"  title="<?php echo $lang['lock_file']; ?>"></i> <?php echo $lang['lock_file']; ?></a>   
																	<?php
																}
															}
														}
                                                        ?>
                                                    </li>
                                                        <?php if ($rwgetRole['view_metadata'] == '1') { ?>

                                                        <li class="isprotected"> <a href="javascript:void(0)"  data-toggle="modal" data-target="#con-close-modal-history" onclick="return getFileHistory(<?php echo $rw['doc_id'] ?>,<?php echo $rw['doc_name']; ?>)"><i class="fa fa-history"></i> <?php echo $lang['history']; ?></a></li>

                                                    <?php } if ($rwgetRole['file_review'] == '1') { ?>
                                                        <?php if ((strtolower($rw['doc_extn']) == 'pdf' || strtolower($rw['doc_extn']) == 'doc' || strtolower($rw['doc_extn']) == 'docx') && $rw['doc_expiry_period'] == '' && $rw['retention_period'] == '') { ?>
                                                            <li class="isprotected"> <a href="reviewers?i=<?php echo urlencode(base64_encode($_SESSION['cdes_user_id'])) ?>&doc_Id=<?php echo urlencode(base64_encode($rw['doc_id'])); ?>" id="moveTorw" data="<?php echo $rw['doc_id']; ?>"> <i class="fa fa fa-send"></i> <?php echo $lang['Review']; ?></a></li><?php } ?>

                                                    <?php } if (($rwgetRole['workflow_initiate_file'] == '1' || $rwgetRole['initiate_file'] == '1') && $rw['doc_expiry_period'] == '' && $rw['retention_period'] == '') { ?>
                                                        <li class="isprotected"> <a href="javascript:void(0)" data-toggle="modal" data-target="#assign-workflow" id="moveToWf" data="<?php echo $rw['doc_id']; ?>"> <i class="fa fa-plus"></i> <?php echo $lang['Workflow']; ?></a></li>

                                <?php } if (strtolower($rw['doc_extn']) == 'pdf' && $rwgetRole['splitpdf'] == '1') { ?>
                                                        <li><a href="viewer?id=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&i=<?php echo urlencode(base64_encode($rw['doc_id'])); ?>&sp=<?php echo urlencode(base64_encode('1')); ?>" id="fancybox-inner" class="pdfview" target="_blank"><i class="fa fa-sign-out"></i> <?php echo $lang['splitpdf']; ?></a></li>
                                                        <?php }
                                                    ?>
                                                    <?php if (strtolower($rw['doc_extn']) == 'pdf' && $rwgetRole['pdf_annotation']) { ?>
                                                        <li class="isprotected"> <a href="anott/add-delete-page?id1=<?php echo urlencode(base64_encode($rw['doc_id'])); ?>&pn=1"  target="_blank" > <i class="fa fa-plus"></i> <?php echo $lang['add_delete_pages']; ?></a></li>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['checkin_checkout'] == '1') { ?>
                                                        <li class="isprotected"><a href="javascript:void(0)" id="checkout" data="<?php echo $rw['doc_id']; ?>"><i class="fa fa-sign-out"></i> <?php echo $lang['Chk_Out']; ?></a></li>
                                                    <?php }if ($rwgetRole['subscribe_document'] == '1') {
                                                        ?>
                                                        <li class="isprotected"><a href="javascript:void(0)"  id="singlesubscribe" data-toggle="modal" data-target="#subscribe" data="<?php echo $rw['doc_id']; ?>"><i class="fa fa-bell-o"></i> <?php echo $lang['subscribe']; ?></a></li>
                                                        <?php
                                                    }if ($rwgetRole['file_delete'] == '1') {
                                                        ?>
                                                        <li class="isprotected"><a href="javascript:void(0)" data-toggle="modal" data-target="#con-close-modal2" id="removeRow" data="<?php echo $rw['doc_id']; ?>"><i class="fa fa-trash"></i> <?php echo $lang['Delete']; ?> </a></li>
                                                        <?php
                                                    }
                                                } else {
                                                    require 'checkout-action.php';
                                                }
                                                ?>
                                            </ul>
                                            <?php } else { ?>

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
                                                    <?php if (($sl_row['is_protected'] == 0 || $_SESSION['pass'] == $sl_row['password']) && (isFolderReadable($db_con, $sl_row['sl_id']))) { ?>
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
                                                        <li> <a href="javascript:void(0)" data="metaData<?php echo $n; ?>" id="viewMeta"><i class="fa fa-eye"></i> <?php echo $lang['View_MetaData']; ?></a></li>
                                                        <li> <a href="javascript:void(0)"  data-toggle="modal" data-target="#con-close-modal-history" onclick="return getFileHistory(<?php echo $file_row['doc_id'] ?>,<?php echo $slid ?>)"><i class="fa fa-history"></i> <?php echo $lang['history']; ?></a></li>

                                                        <?php } if ($rwgetRole['file_review'] == '1') { ?>
                                                        <?php if ((strtolower($file_row['doc_extn']) == 'pdf' || strtolower($file_row['doc_extn']) == 'doc' || strtolower($file_row['doc_extn']) == 'docx') && $file_row['doc_expiry_period'] == '' && $file_row['retention_period'] == '') { ?>
                                                            <li> <a href="reviewers?i=<?php echo urlencode(base64_encode($_SESSION['cdes_user_id'])) ?>&doc_Id=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>" id="moveTorw" data="<?php echo $file_row['doc_id']; ?>"> <i class="fa fa fa-send"></i> <?php echo $lang['Review']; ?></a></li><?php } ?>

                                <?php } if (($rwgetRole['workflow_initiate_file'] == '1' || $rwgetRole['initiate_file'] == '1') && $file_row['doc_expiry_period'] == '' && $file_row['retention_period'] == '') { ?>
                                                        <li> <a href="javascript:void(0)" data-toggle="modal" data-target="#assign-workflow" id="moveToWf" data="<?php echo $file_row['doc_id']; ?>"> <i class="fa fa-plus"></i> <?php echo $lang['Workflow']; ?></a></li>

                                                    <?php } ?>

                                                    <?php if (strtolower($file_row['doc_extn']) == 'pdf' && $rwgetRole['splitpdf'] == '1') { ?>
                                                        <li><a href="viewer?id=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&i=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>&sp=<?php echo urlencode(base64_encode('1')); ?>" id="fancybox-inner" class="pdfview" target="_blank"><i class="fa fa-sign-out"></i> <?php echo $lang['splitpdf']; ?></a></li>
                                    <?php }
                                ?>

                                                    <?php if (strtolower($file_row['doc_extn']) == 'pdf' && $rwgetRole['pdf_annotation']) { ?>
                                                        <li class="isprotected"> <a href="anott/add-delete-page?id1=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>&pn=1" target="_blank" > <i class="fa fa-plus"></i> <?php echo $lang['add_delete_pages']; ?></a></li>
                                                    <?php } ?> 

                                                    <?php if ($rwgetRole['checkin_checkout'] == '1') { ?>
                                                        <li><a href="javascript:void(0)" id="checkout" data="<?php echo $file_row['doc_id']; ?>"><i class="fa fa-sign-out"></i> <?php echo $lang['Chk_Out']; ?></a></li>

                                                    <?php } if ($rwgetRole['subscribe_document'] == '1') {
                                                        ?>
                                                        <li class="isprotected"><a href="javascript:void(0)"  id="singlesubscribe" data-toggle="modal" data-target="#subscribe" data="<?php echo $file_row['doc_id']; ?>"><i class="fa fa-bell-o"></i> <?php echo $lang['subscribe']; ?></a></li>
                                                        <?php
                                                    }if ($rwgetRole['file_delete'] == '1') {
                                                        ?>
                                                        <li><a href="javascript:void(0)" data-toggle="modal" data-target="#con-close-modal2" id="removeRow" data="<?php echo $file_row['doc_id']; ?>"><i class="fa fa-trash"></i> <?php echo $lang['Delete']; ?> </a></li>
                                                        <?php
                                                    }
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
                            <!-- </tr> -->
                                    <?php
                                    echo'</tr>';
                                    $i++;
                                } else {
									
									
									
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
                                    <div class="checkbox checkbox-primary m-r-15"> <input  type="checkbox" class="checkbox-primary emp_checkbox" data-doc-id="<?php echo $rw['doc_id']; ?>" id="shreId<?php echo $i; ?>"> <label for="shreId<?php echo $i; ?>"> <?php echo $i . '.'; ?> </label></div>

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
                                    <?php
//                                    $checkfileLock = mysqli_query($db_con, "SELECT * FROM `tbl_locked_file_master` WHERE doc_id='" . $rw['doc_id'] . "' and is_active='1' and user_id='" . $_SESSION['cdes_user_id'] . "'");
//                                    $rwfileLock = mysqli_fetch_assoc($checkfileLock);
//                                    if ($rwfileLock['doc_id'] != $rw['doc_id']) {
                                    $chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));
                                    $rwgetRole = mysqli_fetch_assoc($chekUsr);

                                    $file_row = $rw;

                                    require 'view-handler-search.php';
                                    echo $rw['old_doc_name'];

//                                    } else {
//                                        echo $rw['old_doc_name'];
                                    ?>
                                        <!--a href="javascript:void(0)"  data="<?php echo $rw['doc_id'] ?>" class="send_lock_request" data-toggle="dropdown" aria-expanded="true"><i class="md md-lock" title="<?php //echo $lang['lock_file']; ?>"></i></a-->  
                                    <?php
                                    // }
                                    '</td>';
                                    ?>
                                <td><?php echo formatSizeUnits($rw['doc_size']); ?></td>
                                    <?php
                                    echo'<td>' . $rw['noofpages'] . '</td>';
                                    ?>
                                <td>
                                    <?php
                                    //storage name
                                    $strgName = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id = '" . $rw['doc_name'] . "' and delete_status=0") or die('Error:' . mysqli_error($db_con));
                                    $rwstrgName = mysqli_fetch_assoc($strgName);
                                    //echo $rwstrgName['sl_name'];
                                    ?>
                                    <a href="storageFiles?id=<?php
                    echo urlencode(base64_encode($rw['doc_name']));
                                    ?>"><i class="md md-my-library-books"></i> <?php echo $rwstrgName['sl_name']; ?> </a>
                                </td>

                                <td>
                                    <?php
                                    $getMetaId = mysqli_query($db_con, "select metadata_id from tbl_metadata_to_storagelevel where sl_id = '$rw[doc_name]'") or die('Error:' . mysqli_error($db_con));

                                    while ($rwgetMetaId = mysqli_fetch_assoc($getMetaId)) {
                                        mysqli_set_charset($db_con, 'utf8');
                                        $getMetaName = mysqli_query($db_con, "select field_name from tbl_metadata_master where id = '$rwgetMetaId[metadata_id]'") or die('Error:' . mysqli_error($db_con));

                                        while ($rwgetMetaName = mysqli_fetch_assoc($getMetaName)) {

                                            echo "<label>" . $rwgetMetaName['field_name'] . "</label> : ";
                                            $meta = mysqli_query($db_con, "select `$rwgetMetaName[field_name]` from tbl_document_master where doc_id='$rw[doc_id]'");
                                            $rwMeta = mysqli_fetch_array($meta);
                                            if ($rwgetMetaName['data_type'] == 'datetime') {
                                                if (!empty($rwMeta[$rwgetMetaName['field_name']])) {
                                                    echo date('d-m-Y H:i', strtotime($rwMeta[$rwgetMetaName['field_name']]));
                                                }
                                            } else {
                                                echo $rwMeta[$rwgetMetaName['field_name']];
                                            }
                                            echo " | ";
                                        }
                                    }
                                    ?>
                                </td>
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
                                        if ($rw['checkin_checkout'] == 1) {
                                            //require 'view-handler.php';
                                            ?>   
                                                </li>
                                                <li class="isprotected">
                                <?php
                                /* ------Lock file code----- */
                                if ($rwgetRole['lock_file'] == '1') {
                                    $checkfileLock = mysqli_query($db_con, "SELECT * FROM `tbl_locked_file_master` WHERE doc_id='$rw[doc_id]' and user_id='$_SESSION[cdes_user_id]' and is_active='1'");
                                    if (mysqli_num_rows($checkfileLock) > 0) {
                                        $fetchdatalock = mysqli_fetch_assoc($checkfileLock);
                                        if ($fetchdatalock['is_locked'] == "1") {
                                            ?>
                                                                <a href="javascript:void(0)" class="unlock_file" data="<?php echo $rw['doc_id']; ?>"> <i class="fa fa-unlock"  title="<?php echo $lang['unlock_file']; ?>"></i> <?php echo $lang['unlock_file']; ?></a>   
                                                                <?php
                                                            }
                                                        } else {
                                                            ?>
                                                            <a href="javascript:void(0)" class="lock_file" data="<?php echo $rw['doc_id'] ?>"> <i class="fa fa-lock"  title="<?php echo $lang['lock_file']; ?>"></i> <?php echo $lang['lock_file']; ?></a>   
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </li>
                                                    <?php if ($rwgetRole['view_metadata'] == '1') { ?>

                                                    <li class="isprotected"> <a href="javascript:void(0)"  data-toggle="modal" data-target="#con-close-modal-history" onclick="return getFileHistory(<?php echo $rw['doc_id'] ?>,<?php echo $rw['doc_name']; ?>)"><i class="fa fa-history"></i> <?php echo $lang['history']; ?></a></li>

                                                    <?php } if ($rwgetRole['file_review'] == '1') { ?>
                                                        <?php if ((strtolower($rw['doc_extn']) == 'pdf' || strtolower($rw['doc_extn']) == 'doc' || strtolower($rw['doc_extn']) == 'docx') && $rw['doc_expiry_period'] == '' && $rw['retention_period'] == '') { ?>
                                                        <li class="isprotected"> <a href="reviewers?i=<?php echo urlencode(base64_encode($_SESSION['cdes_user_id'])) ?>&doc_Id=<?php echo urlencode(base64_encode($rw['doc_id'])); ?>" id="moveTorw" data="<?php echo $rw['doc_id']; ?>"> <i class="fa fa fa-send"></i> <?php echo $lang['Review']; ?></a></li><?php } ?>

                                                    <?php } if (($rwgetRole['workflow_initiate_file'] == '1' || $rwgetRole['initiate_file'] == '1') && $rw['doc_expiry_period'] == '' && $rw['retention_period'] == '') { ?>
                                                    <li class="isprotected"> <a href="javascript:void(0)" data-toggle="modal" data-target="#assign-workflow" id="moveToWf" data="<?php echo $rw['doc_id']; ?>"> <i class="fa fa-plus"></i> <?php echo $lang['Workflow']; ?></a></li>

                                                <?php } ?>

                                <?php if (strtolower($rw['doc_extn']) == 'pdf' && $rwgetRole['splitpdf'] == '1') { ?>
                                                    <li><a href="viewer?id=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&i=<?php echo urlencode(base64_encode($rw['doc_id'])); ?>&sp=<?php echo urlencode(base64_encode('1')); ?>" id="fancybox-inner" class="pdfview" target="_blank"><i class="fa fa-sign-out"></i> <?php echo $lang['splitpdf']; ?></a></li>
                                                    <?php }
                                                ?>
                                <?php if (strtolower($rw['doc_extn']) == 'pdf' && $rwgetRole['pdf_annotation']) { ?>
                                                    <li class="isprotected"> <a href="anott/add-delete-page?id1=<?php echo urlencode(base64_encode($rw['doc_id'])); ?>&pn=1"  target="_blank" > <i class="fa fa-plus"></i> <?php echo $lang['add_delete_pages']; ?></a></li>
                                                <?php } ?>
                                <?php if ($rwgetRole['checkin_checkout'] == '1') { ?>
                                                    <li class="isprotected"><a href="javascript:void(0)" id="checkout" data="<?php echo $rw['doc_id']; ?>"><i class="fa fa-sign-out"></i> <?php echo $lang['Chk_Out']; ?></a></li>
                                                <?php }if ($rwgetRole['subscribe_document'] == '1') {
                                                    ?>
                                                    <li class="isprotected"><a href="javascript:void(0)"  id="singlesubscribe" data-toggle="modal" data-target="#subscribe" data="<?php echo $rw['doc_id']; ?>"><i class="fa fa-bell-o"></i> <?php echo $lang['subscribe']; ?></a></li>
                                                    <?php
                                                }if ($rwgetRole['file_delete'] == '1') {
                                                    ?>
                                                    <li class="isprotected"><a href="javascript:void(0)" data-toggle="modal" data-target="#con-close-modal2" id="removeRow" data="<?php echo $rw['doc_id']; ?>"><i class="fa fa-trash"></i> <?php echo $lang['Delete']; ?> </a></li>
                                                    <?php
                                                }
                                            } else {
												
                                                require 'checkout-action.php';
                                            }
                                            ?>
                                        </ul>
                                        <?php } else { ?>

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
                                        <?php if (($sl_row['is_protected'] == 0 || $_SESSION['pass'] == $sl_row['password']) && (isFolderReadable($db_con, $sl_row['sl_id'])) ) { ?>
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
                                                    <li> <a href="javascript:void(0)" data="metaData<?php echo $n; ?>" id="viewMeta"><i class="fa fa-eye"></i> <?php echo $lang['View_MetaData']; ?></a></li>
                                                    <li> <a href="javascript:void(0)"  data-toggle="modal" data-target="#con-close-modal-history" onclick="return getFileHistory(<?php echo $file_row['doc_id'] ?>,<?php echo $slid ?>)"><i class="fa fa-history"></i> <?php echo $lang['history']; ?></a></li>

                                                    <?php } if ($rwgetRole['file_review'] == '1') { ?>
                                                        <?php if ((strtolower($file_row['doc_extn']) == 'pdf' || strtolower($file_row['doc_extn']) == 'doc' || strtolower($file_row['doc_extn']) == 'docx') && $file_row['doc_expiry_period'] == '' && $file_row['retention_period'] == '') { ?>
                                                        <li> <a href="reviewers?i=<?php echo urlencode(base64_encode($_SESSION['cdes_user_id'])) ?>&doc_Id=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>" id="moveTorw" data="<?php echo $file_row['doc_id']; ?>"> <i class="fa fa fa-send"></i> <?php echo $lang['Review']; ?></a></li><?php } ?>

                                                    <?php } if (($rwgetRole['workflow_initiate_file'] == '1' || $rwgetRole['initiate_file'] == '1') && $file_row['doc_expiry_period'] == '' && $file_row['retention_period'] == '') { ?>
                                                    <li> <a href="javascript:void(0)" data-toggle="modal" data-target="#assign-workflow" id="moveToWf" data="<?php echo $file_row['doc_id']; ?>"> <i class="fa fa-plus"></i> <?php echo $lang['Workflow']; ?></a></li>

                                                <?php } ?>

                                <?php if (strtolower($file_row['doc_extn']) == 'pdf' && $rwgetRole['splitpdf'] == '1') { ?>
                                                    <li><a href="viewer?id=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&i=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>&sp=<?php echo urlencode(base64_encode('1')); ?>" id="fancybox-inner" class="pdfview" target="_blank"><i class="fa fa-sign-out"></i> <?php echo $lang['splitpdf']; ?></a></li>
                                                    <?php }
                                                ?>

                                <?php if (strtolower($file_row['doc_extn']) == 'pdf' && $rwgetRole['pdf_annotation']) { ?>
                                                    <li class="isprotected"> <a href="anott/add-delete-page?id1=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>&pn=1" target="_blank" > <i class="fa fa-plus"></i> <?php echo $lang['add_delete_pages']; ?></a></li>
                                                <?php } ?> 

                                                <?php if ($rwgetRole['checkin_checkout'] == '1') { ?>
                                                    <li><a href="javascript:void(0)" id="checkout" data="<?php echo $file_row['doc_id']; ?>"><i class="fa fa-sign-out"></i> <?php echo $lang['Chk_Out']; ?></a></li>

                                                <?php } if ($rwgetRole['subscribe_document'] == '1') {
                                                    ?>
                                                    <li class="isprotected"><a href="javascript:void(0)"  id="singlesubscribe" data-toggle="modal" data-target="#subscribe" data="<?php echo $file_row['doc_id']; ?>"><i class="fa fa-bell-o"></i> <?php echo $lang['subscribe']; ?></a></li>
                                                    <?php
                                                }if ($rwgetRole['file_delete'] == '1') {
                                                    ?>
                                                    <li><a href="javascript:void(0)" data-toggle="modal" data-target="#con-close-modal2" id="removeRow" data="<?php echo $file_row['doc_id']; ?>"><i class="fa fa-trash"></i> <?php echo $lang['Delete']; ?> </a></li>
                                                    <?php
                                                }
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
                                    <?php
                                    echo'</tr>';
                                    $i++;
                                }
                            }
                        }

                        //echo $foundPages;
                        mysqli_close($rs3);
                    } else {
						
						
                        //echo 'content search';
                        $sql_cont_search = "select doc_extn,doc_name,doc_id,doc_path,old_doc_name,doc_size,noofpages from " . $table . " where ";
                        $marray = findChildIds($slperm);
                        //print_r($marray);
                        $sql_cont_search .= "( doc_name=";
                        $sql_cont_search .= implode(" OR doc_name=", $marray);
                        //$sql_search .= ' and '.findChild($slperm);
                        $sql_cont_search .= ") and flag_multidelete=1";

                        //echo  $sql_cont_search;
                        $rs4 = mysqli_query($db_con, $sql_cont_search);

                        $i = 1;
                        $img_array = array('jpg', 'jpeg', 'png', 'bmp', 'pnm', 'jfif', 'jpeg', 'tiff');
                        while ($rw_content_serch = mysqli_fetch_assoc($rs4)) {

                            $shareDid = mysqli_query($db_con, "select doc_ids from tbl_document_share where doc_ids= '$rw_content_serch[doc_id]'") or die("Error: " . mysqli_error($db_con));
                            $shreCount = mysqli_num_rows($shareDid);
                            $subscribeid = mysqli_query($db_con, "select * from tbl_document_subscriber where subscribe_docid= '$rw_content_serch[doc_id]' and subscriber_userid='" . $_SESSION['cdes_user_id'] . "'") or die("Error: " . mysqli_error($db_con));
                            $subsCountId = mysqli_num_rows($subscribeid);


                            $sl_name = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$rw_content_serch[doc_name]'");
                            $sl_row = mysqli_fetch_assoc($sl_name);
                            $strName = str_replace(" ", "", $sl_row['sl_name']);
                            $sl_names = preg_replace('/[^A-Za-z0-9\-]/', '', $strName);

                            $updir = getStoragePath($db_con, $sl_row['sl_parent_id'], $sl_row['sl_depth_level']);
                            if (!empty($updir)) {
                                $updir = $updir . '/';
                            } else {
                                $updir = '';
                            }

                            if ($rw_content_serch['doc_extn'] == 'pdf' || in_array(strtolower($rw_content_serch['doc_extn']), $img_array)) {



                                $foundPages = array();
                                for ($k = 0; $k < $rw_content_serch['noofpages']; $k++) {

                                    $file = "extract-here/" . $updir . $sl_names . "/TXT/" . $rw_content_serch['doc_id'] . ".txt";
                                    //$file = "extract-here/DMS/" . $sl_row['sl_name'] . "/TXT/" . $rw_content_serch['doc_id'] . "/" . $k . ".txt";
                                    //echo $file."<br>";
                                    //die;

                                    $contents = file_get_contents($file);

                                    if (stripos($contents, $search) != false) {
                                        $foundPages[] = $k;
                                    }
                                }
                                echo $foundPages[0];

                                //for single upload pdf file content search.
                                    $file = "extract-here/" . $updir . $sl_names . "/TXT/" . $rw_content_serch['doc_id'] . ".txt";
                                $contents = file_get_contents($file);
                            } else if ($rw_content_serch['doc_extn'] == 'text' || $rw_content_serch['doc_extn'] == 'txt' || $rw_content_serch['doc_extn'] == "xls" || strtolower($rw_content_serch['doc_extn']) == 'xlsx' || $rw_content_serch['doc_extn'] == "doc" || $rw_content_serch['doc_extn'] == "docx" || $rw_content_serch['doc_extn'] == "pptx" || $rw_content_serch['doc_extn'] == "ppt") {
                                $file = "extract-here/" . $updir . $sl_names . "/TXT/" . $rw_content_serch['doc_id'] . ".txt";
                                $contents = file_get_contents($file);
                            }

                            $doc_name = explode('_', $rw_content_serch['doc_name']);
                            if (count($doc_name) == 1) {
                                if (stripos($contents, $search) != false || count($foundPages) > 0) {
									
                                   if ($rwgetRole['doc_weeding_out'] == '1' && $rwgetInfo['retention_feature_enable'] == '1') {
									if (isset($rw_content_serch['retention_period']) && !empty($rw_content_serch['retention_period'])) {
										$wedDate = $rw_content_serch['retention_period'];
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
									if (isset($rw_content_serch['doc_expiry_period']) && !empty($rw_content_serch['doc_expiry_period'])) {
										$docexpDate = $rw_content_serch['doc_expiry_period'];
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
								
								$checkoutcolor = ($rw_content_serch['checkin_checkout'] == 0)?'#b7f1a3':'';
								$checkoutTitle = ($rw_content_serch['checkin_checkout'] == 0)?'File is checkout!':'';
							  
								
								$docExpRetentionPeriod = "#a6ecf7";
								$docExpRetentionPrdtitle = $expiryTitle . ' ' . $lang['and'] . $weedTile;
								$shareDid = mysqli_query($db_con, "select doc_ids from tbl_document_share where doc_ids= '$rw_content_serch[doc_id]'") or die("Error: " . mysqli_error($db_con));
								$shreCount = mysqli_num_rows($shareDid);
								
								$subscribeid = mysqli_query($db_con, "select * from tbl_document_subscriber where subscribe_docid= '$rw_content_serch[doc_id]' and subscriber_userid='" . $_SESSION['cdes_user_id'] . "'") or die("Error: " . mysqli_error($db_con));
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
                                <div class="checkbox checkbox-primary m-r-15"> <input  type="checkbox" class="checkbox-primary emp_checkbox" data-doc-id="<?php echo $rw_content_serch['doc_id']; ?>" id="shreId<?php echo $i; ?>"> <label for="shreId<?php echo $i; ?>"> <?php echo $i . '.'; ?> </label></div>

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
                                <?php
//                                $checkfileLock = mysqli_query($db_con, "SELECT * FROM `tbl_locked_file_master` WHERE doc_id='" . $rw_content_serch['doc_id'] . "' and is_active='1' and user_id='" . $_SESSION['cdes_user_id'] . "'");
//                                $rwcheckfileLock = mysqli_fetch_assoc($checkfileLock);
//                                if ($rwcheckfileLock['doc_id'] != $rw_content_serch['doc_id']) {
                                $chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));
                                $rwgetRole = mysqli_fetch_assoc($chekUsr);
                                // print_r($rwgetRole);
                                $pageNumbers = implode(',', $foundPages);

                                $file_row = $rw_content_serch;
                                $chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));
                                $rwgetRole = mysqli_fetch_assoc($chekUsr);
                                require 'view-handler-search.php';
                                echo $rw_content_serch['old_doc_name'];

//                                } else {
//                                    echo $rw['old_doc_name'];
                                ?>
                                    <!--a href="javascript:void(0)"  data="<?php echo $rw_content_serch['doc_id'] ?>" class="send_lock_request" data-toggle="dropdown" aria-expanded="true"><i class="md md-lock" title="<?php //echo $lang['lock_file']; ?>"></i></a-->  
                                <?php
                                // }
                                '</td>';
                                echo'<td>' . formatSizeUnits($rw_content_serch['doc_size']) . '</td>';
                                echo'<td>' . $rw_content_serch['noofpages'] . '</td>';
                                //echo'</tr>';
                                ?>
                    <!-- <tr> -->
                            <td>
                                <?php
                                //storage name
                                $strgName = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id = '$rw_content_serch[doc_name]' and delete_status=0") or die('Error:' . mysqli_error($db_con));
                                $rwstrgName = mysqli_fetch_assoc($strgName);
                                //echo $rwstrgName['sl_name'];
                                ?>
                                <a href="storageFiles?id=<?php
                                echo urlencode(base64_encode($rw_content_serch['doc_name']));
                                ?>"><i class="md md-my-library-books"></i> <?php echo $rwstrgName['sl_name']; ?> </a>
                            </td>
                            <td>
                                <?php
                                $getMetaId = mysqli_query($db_con, "select metadata_id from tbl_metadata_to_storagelevel where sl_id = '$rw_content_serch[doc_name]'") or die('Error:' . mysqli_error($db_con));

                                while ($rwgetMetaId = mysqli_fetch_assoc($getMetaId)) {

                                    $getMetaName = mysqli_query($db_con, "select field_name from tbl_metadata_master where id = '$rwgetMetaId[metadata_id]'") or die('Error:' . mysqli_error($db_con));

                                    while ($rwgetMetaName = mysqli_fetch_assoc($getMetaName)) {

                                        echo "<strong>" . $rwgetMetaName['field_name'] . "</strong>: ";
                                        $meta = mysqli_query($db_con, "select `$rwgetMetaName[field_name]` from tbl_document_master where doc_id='$rw_content_serch[doc_id]'");
                                        $rwMeta = mysqli_fetch_array($meta);
                                        if ($rwgetMetaName['data_type'] == 'datetime') {
                                            if (!empty($rwMeta[$rwgetMetaName['field_name']])) {
                                                echo date('d-m-Y H:i', strtotime($rwMeta[$rwgetMetaName['field_name']]));
                                            }
                                        } else {
                                            echo $rwMeta[$rwgetMetaName['field_name']];
                                        }
                                        echo " | ";
                                    }
                                }
                                ?>
                            </td>
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
                            if ($rw_content_serch['checkin_checkout'] == 1) {
                                //require 'view-handler.php';
                                ?>   
                                            </li>
                                            <li class="isprotected">
                                        <?php
                                        /* ------Lock file code----- */
                                        if ($rwgetRole['lock_file'] == '1') {
                                            $checkfileLock = mysqli_query($db_con, "SELECT * FROM `tbl_locked_file_master` WHERE doc_id='$rw_content_serch[doc_id]' and user_id='$_SESSION[cdes_user_id]' and is_active='1'");
                                            if (mysqli_num_rows($checkfileLock) > 0) {
                                                $fetchdatalock = mysqli_fetch_assoc($checkfileLock);
                                                if ($fetchdatalock['is_locked'] == "1") {
                                                    ?>
                                                            <a href="javascript:void(0)" class="unlock_file" data="<?php echo $rw_content_serch['doc_id']; ?>"> <i class="fa fa-unlock"  title="<?php echo $lang['unlock_file']; ?>"></i> <?php echo $lang['unlock_file']; ?></a>   
                                                            <?php
                                                        }
                                                    } else {
                                                        ?>
                                                        <a href="javascript:void(0)" class="lock_file" data="<?php echo $rw_content_serch['doc_id'] ?>"> <i class="fa fa-lock"  title="<?php echo $lang['lock_file']; ?>"></i> <?php echo $lang['lock_file']; ?></a>   
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </li>
                                                <?php if ($rwgetRole['view_metadata'] == '1') { ?>

                                                <li class="isprotected"> <a href="javascript:void(0)"  data-toggle="modal" data-target="#con-close-modal-history" onclick="return getFileHistory(<?php echo $rw_content_serch['doc_id'] ?>,<?php echo $rw_content_serch['doc_name']; ?>)"><i class="fa fa-history"></i> <?php echo $lang['history']; ?></a></li>

                                                <?php } if ($rwgetRole['file_review'] == '1') { ?>
                                                    <?php if ((strtolower($rw_content_serch['doc_extn']) == 'pdf' || strtolower($rw_content_serch['doc_extn']) == 'doc' || strtolower($rw_content_serch['doc_extn']) == 'docx') && $rw_content_serch['doc_expiry_period'] == '' && $rw_content_serch['retention_period'] == '') { ?>
                                                    <li class="isprotected"> <a href="reviewers?i=<?php echo urlencode(base64_encode($_SESSION['cdes_user_id'])) ?>&doc_Id=<?php echo urlencode(base64_encode($rw_content_serch['doc_id'])); ?>" id="moveTorw" data="<?php echo $rw_content_serch['doc_id']; ?>"> <i class="fa fa fa-send"></i> <?php echo $lang['Review']; ?></a></li><?php } ?>

                                                <?php } if (($rwgetRole['workflow_initiate_file'] == '1' || $rwgetRole['initiate_file'] == '1') && $rw_content_serch['doc_expiry_period'] == '' && $rw_content_serch['retention_period'] == '') { ?>
                                                <li class="isprotected"> <a href="javascript:void(0)" data-toggle="modal" data-target="#assign-workflow" id="moveToWf" data="<?php echo $rw_content_serch['doc_id']; ?>"> <i class="fa fa-plus"></i> <?php echo $lang['Workflow']; ?></a></li>

                                                <?php } ?>

                                                <?php if (strtolower($rw_content_serch['doc_extn']) == 'pdf' && $rwgetRole['splitpdf'] == '1') { ?>
                                                <li><a href="viewer?id=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&i=<?php echo urlencode(base64_encode($rw_content_serch['doc_id'])); ?>&sp=<?php echo urlencode(base64_encode('1')); ?>" id="fancybox-inner" class="pdfview" target="_blank"><i class="fa fa-sign-out"></i> <?php echo $lang['splitpdf']; ?></a></li>
                                                <?php }
                                            ?>
                                <?php if (strtolower($rw_content_serch['doc_extn']) == 'pdf' && $rwgetRole['pdf_annotation']) { ?>
                                                <li class="isprotected"> <a href="anott/add-delete-page?id1=<?php echo urlencode(base64_encode($rw_content_serch['doc_id'])); ?>&pn=1"  target="_blank" > <i class="fa fa-plus"></i> <?php echo $lang['add_delete_pages']; ?></a></li>
                                            <?php } ?>
                                            <?php if ($rwgetRole['checkin_checkout'] == '1') { ?>
                                                <li class="isprotected"><a href="javascript:void(0)" id="checkout" data="<?php echo $rw_content_serch['doc_id']; ?>"><i class="fa fa-sign-out"></i> <?php echo $lang['Chk_Out']; ?></a></li>
                                <?php }if ($rwgetRole['subscribe_document'] == '1') {
                                    ?>
                                                <li class="isprotected"><a href="javascript:void(0)"  id="singlesubscribe" data-toggle="modal" data-target="#subscribe" data="<?php echo $rw_content_serch['doc_id']; ?>"><i class="fa fa-bell-o"></i> <?php echo $lang['subscribe']; ?></a></li>
                                    <?php
                                } if ($rwgetRole['export_ocr'] == '1' && in_array($rw_content_serch['doc_extn'],$exportOcExtn)) {
										?>
										<li><a href="javascript:void(0)" data-toggle="modal" data-target="#exportocr-modal" id="exportocr" data="<?php echo $rw_content_serch['doc_id']; ?>"><i class="fa fa-download"></i><?php echo $lang['exportocr']; ?></a></li>
										<?php
									
									}if ($rwgetRole['file_delete'] == '1') {
                                    ?>
                                                <li class="isprotected"><a href="javascript:void(0)" data-toggle="modal" data-target="#con-close-modal2" id="removeRow" data="<?php echo $rw_content_serch['doc_id']; ?>"><i class="fa fa-trash"></i> <?php echo $lang['Delete']; ?> </a></li>
                                                <?php
                                            }
                                        } else {
                                            require 'checkout-action.php';
                                        }
                                        ?>
                                    </ul>
                                    <?php } else { ?>

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
                            <?php if (($sl_row['is_protected'] == 0 || $_SESSION['pass'] == $sl_row['password']) && (isFolderReadable($db_con, $sl_row['sl_id']))) { ?>
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
                                                <li> <a href="javascript:void(0)" data="metaData<?php echo $n; ?>" id="viewMeta"><i class="fa fa-eye"></i> <?php echo $lang['View_MetaData']; ?></a></li>
                                                <li> <a href="javascript:void(0)"  data-toggle="modal" data-target="#con-close-modal-history" onclick="return getFileHistory(<?php echo $file_row['doc_id'] ?>,<?php echo $slid ?>)"><i class="fa fa-history"></i> <?php echo $lang['history']; ?></a></li>

                                                <?php } if ($rwgetRole['file_review'] == '1') { ?>
                                                    <?php if ((strtolower($file_row['doc_extn']) == 'pdf' || strtolower($file_row['doc_extn']) == 'doc' || strtolower($file_row['doc_extn']) == 'docx') && $file_row['doc_expiry_period'] == '' && $file_row['retention_period'] == '') { ?>
                                                    <li> <a href="reviewers?i=<?php echo urlencode(base64_encode($_SESSION['cdes_user_id'])) ?>&doc_Id=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>" id="moveTorw" data="<?php echo $file_row['doc_id']; ?>"> <i class="fa fa fa-send"></i> <?php echo $lang['Review']; ?></a></li><?php } ?>

                                                <?php } if (($rwgetRole['workflow_initiate_file'] == '1' || $rwgetRole['initiate_file'] == '1') && $file_row['doc_expiry_period'] == '' && $file_row['retention_period'] == '') { ?>
                                                <li> <a href="javascript:void(0)" data-toggle="modal" data-target="#assign-workflow" id="moveToWf" data="<?php echo $file_row['doc_id']; ?>"> <i class="fa fa-plus"></i> <?php echo $lang['Workflow']; ?></a></li>

                                                <?php } ?>

                                                <?php if (strtolower($file_row['doc_extn']) == 'pdf' && $rwgetRole['splitpdf'] == '1') { ?>
                                                <li><a href="viewer?id=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&i=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>&sp=<?php echo urlencode(base64_encode('1')); ?>" id="fancybox-inner" class="pdfview" target="_blank"><i class="fa fa-sign-out"></i> <?php echo $lang['splitpdf']; ?></a></li>
                                                    <?php }
                                                ?>

                                <?php if (strtolower($file_row['doc_extn']) == 'pdf' && $rwgetRole['pdf_annotation']) { ?>
                                                <li class="isprotected"> <a href="anott/add-delete-page?id1=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>&pn=1" target="_blank" > <i class="fa fa-plus"></i> <?php echo $lang['add_delete_pages']; ?></a></li>
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
                                        require 'checkout-action.php';
                                    }
                                    ?>
                                </ul>
                    <?php }
                    ?>
                        </li></td>
                                <?php
                                echo'</tr>';
                                $i++;
                            }
                        }
                    }


                    mysqli_close($rs3);
                }
                ?>


    </tbody>

            <?php if ($searchFlag) { ?>
        <tr>
            <td colspan="8">
                <ul class="delete_export">
                    <input type="hidden" name="slid" id="slid" value="<?php echo $slid; ?>">
                    <input type="hidden" name="sty" id="sty" value="<?php echo $slid; ?>">
        <?php if ($rwgetRole['file_delete'] == '1') { ?>
                        <li><button id="del_file" class="rows_selected btn btn-danger btn-sm" data-toggle="modal"  data-target="#del_send_to_recycle"><i data-toggle="tooltip" title="<?php echo $lang['Delete_files'] ?>" class="fa fa-trash-o"></i></button></li>
     
        
        <?php } if ($rwgetRole['share_file'] == '1' && $sharefeatureenable == '1') { ?>
                        <li><button class="rows_selected btn btn-primary btn-sm" id="shareFiles" data-toggle="modal" data-target="#share-selected-files"><i data-toggle="tooltip" title="<?php echo $lang['Share_files']; ?>" class="fa fa-share-alt"></i></button></li>
        <?php } if ($rwgetRole['mail_files'] == '1') { ?>
                        <li><button class="rows_selected btn btn-primary btn-sm" id="mailFiles" data-toggle="modal" data-target="#mail-selected-files"><i data-toggle="tooltip" title="<?php echo $lang['mail_files']; ?>" class="fa fa-envelope-o"></i></button></li>
        <?php } if ($rwgetRole['pdf_download'] == '1') { ?>
                        <li><button class="rows_selected btn btn-primary btn-sm" id="downloadcheckedfile" data-toggle="modal" data-target="#downloadfile"><i class="ti-import" data-toggle="tooltip" title="<?php echo $lang['download_selected_file']; ?>"></i></button></li>
        <?php } if ($rwgetRole['subscribe_document'] == '1') { ?>
                        <li><button class="rows_selected btn btn-primary btn-sm" id="subscribecheckedfile" data-toggle="modal" data-target="#subscribefile"><i class="fa fa-bell-o" data-toggle="tooltip" title="<?php echo $lang['subscribe_selected_file']; ?>"></i></button></li>
                    <?php } ?>
                </ul>
            </td>
        </tr>

                <?php } ?>
    </table> 
                <?php
                //return $out;
            }
            ?>

<!-- End Edit Location -->

<!--for data table-->
<script src="assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="assets/plugins/datatables/dataTables.bootstrap.js"></script>

<?php require_once 'file-action-js.php'; ?>
<script type="text/javascript" src="assets/multi_function_script.js"></script>
<script type="text/javascript">

                $(document).ready(function () {
                $('form').parsley();
// $('#datatable').dataTable();
//sk@241218 : For multilingual implementation in datatable.
                $('#datatable').dataTable({
				<?= (!empty($pageLength) ? '"pageLength":' . $pageLength . ',' : '') ?>
                "language": {
                "paginate": {
                "previous": "<?= $lang['Prev'] ?>",
                        "next": "<?= $lang['Next'] ?>",
                },
                        "emptyTable": "<?= $lang['No_Rcrds_Fnd'] ?>",
                        "sEmptyTable": "<?= $lang['No_Rcrds_Fnd'] ?>",
                        "sInfo": "<?= $lang['sInfo'] ?>",
                        "sInfoEmpty": "<?= $lang['sInfoEmpty'] ?>",
                        "sSearch": "<?= $lang['Search'] ?>",
                        "sLengthMenu": "<?= $lang['sLengthMenu'] ?>",
                        "sInfoFiltered": "<?= $lang['sInfoFiltered'] ?>",
                        "sZeroRecords": "<?= $lang['sZeroRecords'] ?>",
                }
                });
                });</script>

<script>
    /*function searchData(searchby){
     
     var searchText = "<?= $searchText ?>";
     var slperm = "<?= $slperm ?>";
     var sharefeatureenable = "<?= $sharefeatureenable ?>";
     var slid ="<?= $slid ?>";
     $.post("application/ajax/searchbyAjax.php", {searchby: searchby, searchText: searchText, slperm: slperm, sharefeatureenable: sharefeatureenable, slid: slid}, function (result, status) {
     if (status == 'success') {
     $("#child2").html(result);
     console.log(result);
     }
     });
     }*/


</script>

<?php require 'file-movement.php'; ?>
<?php require 'file-action-php.php'; ?>
</body>
</html>

