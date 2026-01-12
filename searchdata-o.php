<!DOCTYPE html>
<html>
    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" />
    <link href="assets/plugins/multiselect/css/multi-select.css"  rel="stylesheet" type="text/css" />
    <!-- Plugin Css-->
    <link rel="stylesheet" href="assets/plugins/magnific-popup/css/magnific-popup.css" />
    <!--   <link rel="stylesheet" href="assets/plugins/jquery-datatables-editable/datatables.css" />-->
    <link href="assets/plugins/datatables/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <?php
    set_time_limit(0);
    require_once './loginvalidate.php';
    require_once './application/config/database.php';
    require_once './application/pages/head.php';
    require_once './classes/ftp.php';
    require_once './application/pages/function.php';

    //for user role
    $chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

    $rwgetRole = mysqli_fetch_assoc($chekUsr);

    // echo $rwgetRole['dashboard_mydms']; die;
    
 //for user role

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
    ?>
    <?php
    $sllid = "select * from tbl_storage_level where sl_id = '$slid'";
    $sllid_run = mysqli_query($db_con, $sllid) or die("error:" . mysqli_errno($db_con));
    $namesl = mysqli_fetch_assoc($sllid_run);

    $result = "SELECT count(*) as total from tbl_document_master where doc_name = '$namesl[sl_name]'";
    $sql_run = mysqli_query($db_con, $result) or die("error:" . mysqli_errno($db_con));
    $data = mysqli_fetch_assoc($sql_run);
    $data['total'];
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
                            $perm = mysqli_query($db_con, "select * from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]' group by sl_id");
                            $rwPerm = mysqli_fetch_assoc($perm);
                            $slperm = $rwPerm['sl_id'];
                            $sllevel = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slperm'");
                            $rwSllevel = mysqli_fetch_assoc($sllevel);
                            $level = $rwSllevel['sl_depth_level'];
                            ?>
                            <ol class="breadcrumb">
                                <li><a href="storage?id=<?php echo urlencode(base64_encode($slperm)); ?>"><?php echo $lang['Storage_Manager'];?></a></li>

                                <?php
                                parentLevel($slid, $db_con);

                                function parentLevel($slid, $db_con) {
                                    $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid'") or die('Error' . mysqli_error($db_con));
                                    $rwParent = mysqli_fetch_assoc($parent);
                                    if ($rwParent['sl_depth_level'] != 0) {
                                        parentLevel($rwParent['sl_parent_id'], $db_con);
                                    }
                                    echo '<li class="active"><a href="storage?id=' . urlencode(base64_encode($rwParent['sl_id'])) . '">' . $rwParent['sl_name'] . '</a></li>';
                                }
                                ?>

                            </ol>

                        </div>

                        <div class="row">
                            <div class="box box-primary">

                                <div class="box-body">
                                    <div class="col-md-3" style="overflow: auto;">
                                        <div class="card-box">
                                            <div id="basicTree">
                                                <ul>
                                                    <?php
                                                    storageLevelS($level, $db_con, $slid, $parentid, $slperm);
                                                    ?>
                                                </ul>
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
                                                            <option selected disabled value=""><?php echo $lang['Select_Metadata'];?></option>
                                                            <option value="old_doc_name" <?php
                                                            if ($_GET['metadata'][$j] == "old_doc_name") {
                                                                echo'selected';
                                                            }
                                                            ?>><?php echo $lang['FileName'];?></option>
                                                            <option value="noofpages"   <?php
                                                            if ($_GET['metadata'][$j] == "noofpages") {
                                                                echo'selected';
                                                            }
                                                            ?>><?php echo $lang['No_Of_Pages'];?></option>
                                                                    <?php
                                                                    $metadatacount = 3;
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
                                                                                    echo '<option selected>' . $rwMeta['field_name'] . '</option>';
                                                                                } else {
                                                                                    echo '<option>' . $rwMeta['field_name'] . '</option>';
                                                                                }
                                                                                $metadatacount++;
                                                                            }
                                                                        }
                                                                    }
                                                                    $metadatacount = $metadatacount - count($_GET['metadata']);
                                                                    ?>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <select class="form-control" name="cond[]" required>
                                                            <option disabled selected style="background: #808080; color: #121213;"><?php echo $lang['Slt_Condition'];?></option>
                                                            <option <?php
                                                            if (isset($_GET['cond'][$j]) && !empty($_GET['cond'][$j]) && $_GET['cond'][$j] == 'Equal') {
                                                                echo'selected';
                                                            }
                                                            ?>><?php echo $lang['Equal'];?></option>
                                                            <option <?php
                                                            if (isset($_GET['cond'][$j]) && !empty($_GET['cond'][$j]) && $_GET['cond'][$j] == 'Contains') {
                                                                echo'selected';
                                                            }
                                                            ?>><?php echo $lang['Contains'];?></option>
                                                            <option <?php
                                                            if (isset($_GET['cond'][$j]) && !empty($_GET['cond'][$j]) && $_GET['cond'][$j] == 'Like') {
                                                                echo'selected';
                                                            }
                                                            ?>><?php echo $lang['Like'];?></option>
                                                            <option <?php
                                                            if (isset($_GET['cond'][$j]) && !empty($_GET['cond'][$j]) && $_GET['cond'][$j] == 'Not Like') {
                                                                echo'selected';
                                                            }
                                                            ?>><?php echo $lang['Not_Like'];?></option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="text" class="form-control" name="searchText[]" required value="<?php echo $_GET['searchText'][$j] ?>" placeholder="<?php echo $lang['entr_srch_txt_hr'];?>">
                                                    </div>
                                                    <input type="hidden" value="<?php echo $_GET['id']; ?>" name="id" />
                                                    <?php
                                                    if ($j == 0) {
                                                        ?>  

                                                        <button type="submit" class="btn btn-primary" id="search" onclick="functionHide();"><i class="fa fa-search"></i></button>
                                                        <a href="javascript:void(0)" class="btn btn-primary" id="addfields"><i class="fa fa-plus"></i></a>
                                                    <?php } else { ?>

                                                        <div onclick="incrementCount()"> <a href="javascript:void(0)" class="btn btn-primary " id="<?= $j; ?>" onclick="invisible(this.id)" ><i class='fa fa-minus-circle' aria-hidden='true'></i></a></div>

                                                    <?php } ?>
                                                </div>
                                            <?php } ?>
                                            <div class="row">
                                                <div class="contents col-lg-12"></div>
                                            </div> 
                                        </form>
                                        <?php
                                        if (isset($_GET['searchText'])) {

                                            $metadata = $_GET['metadata'];
                                            $cond = $_GET['cond'];
                                            $searchText = $_GET['searchText'];
                                            // print_r($searchText);
                                            $slid = base64_decode(urldecode($_GET['id']));
                                            $searchText = mysqli_real_escape_string($db_con, $searchText);
                                            $res = searchAllDB($searchText, $cond, $metadata, $slid, $db_con, $rwgetRole, $lang);
                                        }
                                        ?>	

                                        <?php
                                        $count = "SELECT count(*) as total from tbl_document_master where doc_name = '$namesl[sl_id]'";
                                        $count_run = mysqli_query($db_con, $count) or die("error:" . mysqli_errno($db_con));
                                        $count_data = mysqli_fetch_assoc($count_run);

                                        $contFile = mysqli_query($db_con, "select sum(doc_size) as total from tbl_document_master where doc_name = '$namesl[sl_id]'") or die('Error:' . mysqli_error($db_con));
                                        $rwcontFile = mysqli_fetch_assoc($contFile);
                                        $totalFSize = $rwcontFile['total'];
                                        $totalFSize = round($totalFSize / 1024, 2);
                                        ?>
                                        <?php

                                        //print_r($res);
                                        function searchAllDB($search, $cond, $metadata, $slid, $db_con, $rwgetRole, $lang) {
                                            //  $out = "";
                                            ?>
                                            <table class="table table-striped  dataTable no-footer" id="datatable" role="grid" aria-describedby="datatable_info">
                                                <?php
                                                $table = "tbl_document_master";
                                                //$out .= $table.";";
                                                $sql_search = "select * from " . $table . " where flag_multidelete=1 and doc_name=";
                                                $sql_search_fields = Array();

                                                echo '<thead>
                                                    <tr>
                                                        <th width="51px"><input  type="checkbox" class="checkbox-primary" id="select_all"> '.$lang['All'].' </th>
                                                        <th>'.$lang['File_Name'].'</th>
                                                        <th>'.$lang['File_Size'].'</th>
                                                        <th>'.$lang['No_of_Pages'].'</th>
                                                        <th>'.$lang['Upld_By'].'</th>
                                                        <th>'.$lang['Upld_Date'].'</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>';

                                                for ($i = 0; $i < count($_GET['searchText']); $i++) {
                                                    if ($_GET['cond'][$i] == 'Like') {
                                                        $sql_search_fields[] = 'CONVERT(`' . $_GET['metadata'][$i] . "` USING utf8) like('%" . $_GET['searchText'][$i] . "%')";
                                                    } else if ($_GET['cond'][$i] == 'Not Like') {
                                                        $sql_search_fields[] = 'CONVERT(`' . $_GET['metadata'][$i] . "` USING utf8) not like('%" . $_GET['searchText'][$i] . "%')";
                                                    } else if ($_GET['cond'][$i] == 'Contains') {
                                                        $sql_search_fields[] = 'CONVERT(`' . $_GET['metadata'][$i] . "` USING utf8) like('%" . $_GET['searchText'][$i] . "%')";
                                                    } else if ($_GET['cond'][$i] == 'Equal') {
                                                        $sql_search_fields[] = "`" . $_GET['metadata'][$i] . "` ='" . $_GET['searchText'][$i] . "'";
                                                    }
                                                }
                                                $sql_search .= $slid;
                                                $sql_search .= ' and (';
                                                $sql_search .= implode(" OR ", $sql_search_fields);
                                                $sql_search .= ')';

                                                //echo $sql_search;

                                                $rs3 = mysqli_query($db_con, $sql_search);
                                                //$out .= mysqli_num_rows($rs3)."\n ok";
                                                echo'<tbody>';
                                                if (mysqli_num_rows($rs3) > 0) {

                                                    $n = 1;
                                                    while ($rw = mysqli_fetch_assoc($rs3)) {

                                                        if ($rw['doc_name'] == $slid) {
                                                            ?>
                                                            <tr class="gradeX">
                                                                <td>
                                                                    <input  type="checkbox" class="checkbox-primary emp_checkbox" data-doc-id="<?php echo $rw['doc_id']; ?>">
                                                                    <?php echo $n; ?>
                                                                </td>
                                                                <td> <?php echo $rw['old_doc_name']; ?></td>
                                                                <td ><?php echo round($rw['doc_size'] / 1024 / 1024, 2); ?> MB</td>
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
                                                                <a href="" class="dropdown-toggle profile waves-effect waves-light" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-gear"></i></a>
                                                                <ul class="dropdown-menu pdf gearbody">
                                                                    <li> 
                                                                        <?php
                                                                        if ($rw['checkin_checkout'] == 1) {
                                                                            if ($rw['doc_extn'] == 'pdf') {
                                                                                ?>
                                                                                <?php if ($rwgetRole['pdf_file'] == '1') { ?>
                                                                                    <a href="flipflop-viewer?i=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])) ?>&i=<?php echo urlencode(base64_encode($rw['doc_id'])); ?>" id="fancybox-inner" class="pdfview" target="_blank">
                                                                                        <i class="ti-book" style="font-size: 18px;"></i></a>

                                                                                    <a href="viewer?id=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])) ?>&i=<?php echo urlencode(base64_encode($rw['doc_id'])); ?>" id="fancybox-inner" class="pdfview"  target="_blank">
                                                                                        <i class="fa fa-file-pdf-o"></i></a>
                                                                                <?php } ?>
                                                                                <!--for tool tip on pdf-->   
                                                                                <?php if ($rwgetRole['pdf_annotation'] == '1') { ?>
                                                                                    <a href="anott/index?id=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&id1=<?php echo urlencode(base64_encode($rw['doc_id'])); ?>&pn=1" class="pdfview" target="blank">  <i class="fa fa fa-file-text-o"></i></a>
                                                                                    <?php
                                                                                }
                                                                            } else if ($rw['doc_extn'] == 'jpg' || $rw['doc_extn'] == 'png' || $rw['doc_extn'] == 'gif'){
                                                                                ?>
                                                                               
                                                                                   <a href="imageviewer?uid=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&i=<?php echo urlencode(base64_encode($rw['doc_id'])); ?>">
                                                                                            <?php if($rwgetRole['image_file'] == '1') { ?>
                                                                                        <i class="fa fa-file-image-o"></i> <?php echo $lang['Image'];?></a>
                                                                                <?php } ?> 
                                                                            <?php } else if ($rw['doc_extn'] == 'tif' || $rw['doc_extn'] == 'tiff') { ?>
                                                                                <a href="file?uid=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&file=<?php echo urlencode(base64_encode($rw['doc_id'])); ?>" target="_blank" >
                                                                                    <?php if ($rwgetRole['tif_file'] == '1') { ?>
                                                                                        <i class="fa fa-picture-o"></i>
                                                                                    </a>
                                                                                <?php } ?>

                                                                            <?php } else if (strtolower($file_row['doc_extn']) == 'xlsx') {
                                                                                ?>
                                                                                <a href="xlsx?uid=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&file=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>" target="_blank">
                                                                                    <?php if ($rwgetRole['excel_file'] == '1') { ?>
                                                                                        <i class="fa fa-file-excel-o"></i> <?php echo $lang['Execl_file'];?></a>
                                                                                <?php } ?>

                                                                            <?php } else if (strtolower($file_row['doc_extn']) == 'xls') {
                                                                                ?>
                                                                                <a href="xls?uid=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&file=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>" target="_blank">
                                                                                    <?php if ($rwgetRole['excel_file'] == '1') { ?>
                                                                                        <i class="fa fa-file-excel-o"></i> <?php echo $lang['Execl_file'];?></a>
                                                                                <?php } ?>
                                                                            <?php } else if ($rw['doc_extn'] == 'doc' || $rw['doc_extn'] == 'docx') { ?>
                                                                               <a href="viewword?i=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&id=<?php echo urlencode(base64_encode($rw['doc_id'])); ?>" target="_blank">
                                                                                    <?php if ($rwgetRole['doc_file'] == '1') { ?>
                                                                                        <i class="fa fa-file-word-o"></i> <?php echo $lang['Word_file'];?></a>
                                                                                <?php } ?>

                                                                            <?php } else if ($rw['doc_extn'] == 'mp3' || $rw['doc_extn'] == 'wav') { ?>
                                                                                                <!--a class="" href="#modal-audio" data-uk-modal=""><i class="fa fa-music"></i> </a-->
                                                                                <a href="javascript:void(0)" data-toggle="modal" data-target="#modal-audio" data="<?php echo $rw['doc_id']; ?>" id="audio">
                                                                                    <?php if ($rwgetRole['audio_file'] == '1') { ?>
                                                                                        <i class="fa fa-music"></i> <?php echo $lang['Audio'];?> </a>
                                                                                <?php } ?>

                                                                            <?php } else if ($rw['doc_extn'] == 'mp4' || $rw['doc_extn'] == '3gp') { ?>
                                                                                <a href="javascript:void(0)" data-toggle="modal" data-target="#modal-video" data="<?php echo $rw['doc_id']; ?>" id="video">
                                                                                    <?php if ($rwgetRole['video_file'] == '1') { ?>
                                                                                        <i class="fa fa-video-camera"></i> <?php echo $lang['Video'];?></a>
                                                                                <?php } ?>                                                                        
                                                                            <?php } else {
                                                                                ?>
                                                                                <a href="extract-here/<?php echo $rw['doc_path']; ?>" id="fancybox-inner" target="_blank"> <i class="fa fa-download"></i> <?php echo $rw['old_doc_name']; ?>
                                                                                </a>
                                                                            <?php }
                                                                            ?>
                                                                        </li>

                                                                        <li> <a href="javascript:void(0)" data="metaData<?php echo $n; ?>" id="viewMeta"><i class="fa fa-eye"></i> <?php echo $lang['View_MetaData'];?></a></li>
                                                                        <?php if ($rwgetRole['file_delete'] == '1') { ?>
                                                                            <li><a href="javascript:void(0)" data-toggle="modal" data-target="#con-close-modal2" id="removeRow" data="<?php echo $rw['doc_id']; ?>"><i class="fa fa-trash-o"></i> <?php echo $lang['Delete'];?> </a></li>
                                                                        <?php } ?>
                                                                        <?php if ($rwgetRole['workflow_initiate_file'] == '1' || $rwgetRole['initiate_file'] == '1') { ?>
                                                                            <li> <a href="javascript:void(0)" data-toggle="modal" data-target="#assign-workflow" id="moveToWf" data="<?php echo $rw['doc_id']; ?>"><i class="fa fa-plus"></i> <?php echo $lang['Workflow'];?></a></li>
                                                                        <?php } ?>
                                                                        <?php if ($rwgetRole['checkin_checkout'] == '1') { ?>
                                                                            <li><a href="javascript:void(0)" id="checkout" data="<?php echo $rw['doc_id']; ?>"><i class="fa fa-sign-out"></i> <?php echo $lang['Chk_Out'];?></a></li>
                                                                            <?php
                                                                        }
                                                                    } else {
                                                                        ?>
                                                                        <?php if ($rwgetRole['checkin_checkout'] == '1') { ?>
                                                                            <li> <a href="javascript:void(0)" data-toggle="modal" data-target="#editmetadata" id="editMdata" data="<?php echo $rw['doc_id']; ?>"><i class="fa fa-sign-in"></i> <?php echo $lang['Chk_In'];?></a></li>
                                                                            <?php
                                                                        }
                                                                    }
                                                                    ?>
                                                                </ul>
                                                            </li>
                                                            </td>
                                                            </tr>
                                                            <td colspan="50">
                                                                <div id="metaData<?php echo $n; ?>"  class="metadata">
                                                                    <?php
                                                                    
                                                                   $versionView = mysqli_query($db_con, "SELECT * FROM tbl_document_master where substring_index(doc_name,'_',-1)='$rw[doc_id]' and substring_index(doc_name,'_',1)='$slid' ") or die("Error: test" . mysqli_error($db_con));
                                                                    if (mysqli_num_rows($versionView) > 0) {

                                                                        $i = 1.0;
                                                                        while ($rwView = mysqli_fetch_assoc($versionView)) {
                                                                            if ($rwgetRole['file_version'] == '1') {
                                                                                    if ($i > 0) {

                                                                                        echo 'Version ' . $i . '-';
                                                                                    }
                                                                                    ?>

                                                                                    <?php if (strtolower($rwView['doc_extn']) == 'pdf') { ?>

                                                                                        <a href="viewer?id=<?php echo urlencode(base64_encode($_SESSION['cdes_user_id'])); ?>&i=<?php echo urlencode(base64_encode($rwView['doc_id'])); ?>" id="fancybox-inner" target="_blank">
                                                                                            <?php echo $rwView['old_doc_name']; ?>
                                                                                        </a>

                                                                                    <?php } else if (strtolower($rwView['doc_extn']) == 'jpg' || strtolower($rwView['doc_extn']) == 'png' || strtolower($rwView['doc_extn']) == 'gif') { ?>
                                                                                          <a href="imageviewer?uid=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&i=<?php echo urlencode(base64_encode($rwView['doc_id'])); ?>"  target="_blank">
                                                                                            <?php echo $rwView['old_doc_name']; ?>
                                                                                        </a>
                                                                                    <?php } else if (strtolower($rwView['doc_extn']) == 'tif' || strtolower($rwView['doc_extn']) == 'tiff') { ?>
                                                                                        <a href="file?uid=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&file=<?php echo urlencode(base64_encode($rwView['doc_id'])); ?>" target="_blank" >
                                                                                            <?php if ($rwgetRole['tif_file'] == '1') { ?>
                                                                                                <i class="fa fa-picture-o"></i>
                                                                                            </a>
                                                                                        <?php } ?>

                                                                                    <?php } else if (strtolower($rwView['doc_extn']) == 'xlsx') {
                                                                                        ?>
                                                                                        <a href="xlsx?uid=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&file=<?php echo urlencode(base64_encode($rwView['doc_id'])); ?>" target="_blank">
                                                                                            <?php if ($rwgetRole['excel_file'] == '1') { ?>
                                                                                                <i class="fa fa-file-excel-o"></i></a>
                                                                                        <?php } ?>

                                                                                    <?php } else if (strtolower($rwView['doc_extn']) == 'xls') {
                                                                                        ?>
                                                                                        <a href="xls?uid=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&file=<?php echo urlencode(base64_encode($rwView['doc_id'])); ?>" target="_blank">
                                                                                            <?php if ($rwgetRole['excel_file'] == '1') { ?>
                                                                                                <i class="fa fa-file-excel-o"></i></a>
                                                                                        <?php } ?>

                                                                                    <?php } else if (strtolower($rwView['doc_extn']) == 'doc' || strtolower($rwView['doc_extn']) == 'docx') { ?>
                                                                                      <a href="viewword?i=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&id=<?php echo urlencode(base64_encode($rwView['doc_id'])); ?>" target="_blank">
                                                                                            <?php if ($rwgetRole['doc_file'] == '1') { ?>
                                                                                                <i class="fa fa-file-word-o"></i></a>
                                                                                        <?php } ?>
                                                                                    <?php } else if (strtolower($rwView['doc_extn']) == 'mp3') { ?>
                                                                                        <a href="javascript:void(0)" data-toggle="modal" data-target="#modal-audio" data="<?php echo $rwView['doc_id']; ?>" id="audio">
                                                                                            <?php echo $rwView['old_doc_name']; ?>
                                                                                        </a>
                                                                                    <?php } else if (strtolower($rwView['doc_extn']) == 'mp4') { ?>
                                                                                        <a href="javascript:void(0)" data-toggle="modal" data-target="#modal-video" data="<?php echo $rwView['doc_id']; ?>" id="video">
                                                                                            <?php echo $rwView['old_doc_name']; ?>
                                                                                        </a>

                                                                                    <?php } else {
                                                                                        ?>
                                                                                        <a href="extract-here/<?php echo $rwView['doc_path']; ?>" id="fancybox-inner" target="_blank" download>
                                                                                            <?php echo $rwView['old_doc_name']; ?>
                                                                                        </a>
                                                                                        <?php
                                                                                    }
                                                                                }
                                                                                if ($rwgetRole['delete_version'] == '1') {
                                                                                    ?>
                                                                                    <a href="javascript:void(0)" data="<?php echo $rwView['doc_id']; ?>" data-toggle="modal" data-target="#deleteVersion" id="deleteVersionDoc"><i class="fa fa-trash"></i></a>
                                                                                    <?php
                                                                                }
                                                                                $i = $i + 0.1;
                                                                        }
                                                                    }
                                                                    ?>
                                                                    <?php
                                                                    $getMetaId = mysqli_query($db_con, "select * from tbl_metadata_to_storagelevel where sl_id = '$rw[doc_name]'") or die('Error:' . mysqli_error($db_con));

                                                                    while ($rwgetMetaId = mysqli_fetch_assoc($getMetaId)) {

                                                                        $getMetaName = mysqli_query($db_con, "select * from tbl_metadata_master where id = '$rwgetMetaId[metadata_id]'") or die('Error:' . mysqli_error($db_con));

                                                                        while ($rwgetMetaName = mysqli_fetch_assoc($getMetaName)) {
                                                                            $meta = mysqli_query($db_con, "select `$rwgetMetaName[field_name]` from tbl_document_master where doc_id='$rw[doc_id]'");
                                                                            $rwMeta = mysqli_fetch_array($meta);
                                                                            if (!empty($rwMeta[$rwgetMetaName['field_name']])) {
                                                                                if ($rwgetMetaName['field_name'] == 'noofpages' || $rwgetMetaName['field_name'] == 'filename') {
                                                                                    
                                                                                } else {
                                                                                    echo "<label>" . $rwgetMetaName['field_name'] . "</label> : ";

                                                                                    echo $rwMeta[$rwgetMetaName['field_name']];
                                                                                    echo " | ";
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                    ?>

                                                                </div>
                                                            </td>
                                                            <?php
                                                            $n++;
                                                        }
                                                    }
                                                    echo '</tbody>';
                                                    ?>
                                                    <tr>
                                                        <td colspan="50">
                                                            <ul class="delete_export">
                                                                <input type="hidden" name="slid" id="slid" value="<?php echo $slid; ?>">
                                                                <input type="hidden" name="sty" id="sty" value="<?php echo $_GET['id']; ?>">
                                                                <?php if ($rwgetRole['file_delete'] == '1') { ?>
                                                                <li><button id="del_file" class="rows_selected btn btn-danger" data-toggle="modal" data-target="#del_send_to_recycle" title="<?php echo $lang['Delete_files']; ?>"><i class="fa fa-trash-o"></i></button></li>
                                                                <?php } if ($rwgetRole['export_csv'] == '1') { ?>
                                                                    <li><button class="btn btn-primary fa fa-download" id="export4"  data-toggle="modal"  data-target="#multi-csv-export-model" title="<?php echo $lang['Export_Data']?>"></button></li>
                                                                <?php } if ($rwgetRole['move_file'] == '1') { ?>
                                                                    <li><button id="move_multi" class="rows_selected btn btn-primary " data-toggle="modal" data-target="#move-selected-files" title="<?php echo $lang['Move_Files']; ?>"> <i class="fa fa-share-square"></i></button></li>
                                                                <?php } if ($rwgetRole['copy_file'] == '1') { ?>
                                                                    <li><button class="rows_selected btn btn-primary" id="copyFiles" data-toggle="modal" data-target="#copy-selected-files" title="<?php echo $lang['Copy_files']; ?> " ><i class="fa fa-copy"></i></button></li>
                                                                <?php } if ($rwgetRole['share_file'] == '1') { ?>
                                                                    <li><button class="rows_selected btn btn-primary" id="shareFiles" data-toggle="modal" data-target="#share-selected-files" title="<?php echo $lang['Share_files']; ?> " ><i class="fa fa-share-alt"></i></button></li>
                                                                <?php } if ($rwgetRole['share_file'] == '1') { ?>
                                                                        <li><button class="rows_selected btn btn-primary fa fa-envelope-o" id="mailFiles" data-toggle="modal" data-target="#mail-selected-files" title="<?php echo $lang['mail_files'];?>"></button></li>
                                                                    <?php } ?>
                                                            </ul>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                    mysqli_close($rs3);
                                                } else {
                                                    echo '<tr><td colspan="10"><label style="font-weight:600; color:red; margin-left: 240px;">'.$lang['No_Rcrds_Fnd'].' !</label></td></tr>';
                                                }
                                                ?>
                                            </table>
                                            <?php
                                        }
                                        ?>
                                    </div>

                                </div>				
                            </div>
                        </div> <!-- container -->

                    </div> <!-- content -->
                    <!--share files with users-->
                    <div id="share-selected-files" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog"> 
                            <div class="modal-content"> 
                                <div class="modal-header"> 
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                    <h4 class="modal-title" id="shr"> <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <?php echo $lang['Hre_msge'];?>!</h4>
                                    <h4 class="modal-title" style="display:none;" id="stitle"> <?php echo $lang['Share_Docmnt_With'];?></h4> 
                                </div>
                                <div id="unseshare">
                                    <div class="modal-body">
                                        <h5 class="text-alert"><?php echo $lang['Pls_slt_Files_for_Share'];?>.</h5>
                                    </div>
                                    <div class="modal-footer"> 
                                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close'];?></button> 
                                    </div>
                                </div>
                                <div id="selected2">
                                    <form method="post" >
                                        <div class="modal-body" >
                                            <div class="form-group">
                                                <label><?php echo $lang['Select_User'];?></label>
                                                <select class="select2 select2-multiple" multiple data-placeholder="<?php echo $lang['Select_User'];?>" name="userid[]" required>
                                                    <?php
                                                    $user = mysqli_query($db_con, "select * from tbl_user_master ");
                                                    while ($rwUser = mysqli_fetch_assoc($user)) {
                                                        echo '<option value="' . $rwUser['user_id'] . '">' . $rwUser['first_name'] . ' ' . $rwUser['last_name'] . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div> 
                                        <div class="modal-footer">
                                            <input type="hidden" id="share_docids" name="shareFile">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close'];?></button>

                                            <button type="submit" name="shareFiles" class="btn btn-primary"> <i class="fa fa-share-alt"></i> <?php echo $lang['Share'];?></button>

                                            </button> 
                                        </div>
                                    </form>
                                </div>
                            </div> 
                        </div>
                    </div><!-- /.modal -->
                    
                    <div id="mail-selected-files" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog"> 
                    <div class="modal-content"> 
                        <div class="modal-header"> 
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                            <h4 class="modal-title" id="mailf"> <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <?php echo $lang['Hre_msge'];?></h4>
                            <h4 class="modal-title" style="display:none;" id="mtitle"> <?php echo $lang['mail_document'];?></h4> 
                        </div>
                        <div id="unmail">
                            <div class="modal-body">
                                <h5 class="text-alert"><?php echo $lang['Pls_slct_Fles_for_mail'];?></h5>
                            </div>
                            <div class="modal-footer"> 
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close'];?></button> 
                            </div>
                        </div>
                        <div id="selected3">
                            <form method="post" >
                                <div class="modal-body" >
                                     <div class="row">
                                        <div class="form-group">
                                            <label><?php echo $lang['Select_User']; ?></label>
                                            <select class="select2 select2-multiple" multiple data-placeholder="Select Users" name="userid[]" required>
                                                <?php
                                                $sameGroupIDs = array();
                                                $group = mysqli_query($db_con, "select * from tbl_bridge_grp_to_um where find_in_set('$_SESSION[cdes_user_id]',user_ids)") or die('Error' . mysqli_error($db_con));
                                                while ($rwGroup = mysqli_fetch_assoc($group)) {
                                                    $sameGroupIDs[] = $rwGroup['user_ids'];
                                                }
                                                $sameGroupIDs = array_unique($sameGroupIDs);
                                                sort($sameGroupIDs);
                                                $sameGroupIDs = implode(',', $sameGroupIDs);

                                                $user = mysqli_query($db_con, "select * from tbl_user_master where user_id in($sameGroupIDs)");
                                                while ($rwUser = mysqli_fetch_assoc($user)) {
                                                    if ($rwUser['user_id'] != 1 && $rwUser['user_id'] != $_SESSION['cdes_user_id']) {
                                                        echo '<option value="' . $rwUser['user_id'] . '">' . $rwUser['first_name'] . ' ' . $rwUser['last_name'] . '</option>';
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group">
                                            <label><?php echo $lang['subject']; ?></label>
                                            <input type="text" name="subject" id="subject" class="form-control" placeholder="<?php echo $lang['enter_subject']; ?>" required>
                                        </div>
                                        
                                    </div>
                                    <div class="row">
                                        <div class="form-group">
                                            <label><?php echo $lang['description']; ?></label>
                                            <textarea name="mailbody" id="mailbody" class="form-control" placeholder="<?php echo $lang['enter_description']; ?>" required ></textarea>
                                        </div>
                                        
                                    </div>
                                </div> 
                                <div class="modal-footer">
                                    <input type="hidden" id="mail_docids" name="mailFile">
                                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']?></button>

                                    <button type="submit" name="mailFiles" class="btn btn-primary"> <?php echo $lang['Send']?></button>

                                    </button> 
                                </div>
                            </form>
                        </div>
                    </div> 
                </div>
            </div><!-- /.modal -->
                    <?php require_once './application/pages/footer.php'; ?>
                </div>

            </div>
            <!-- END wrapper -->
            <?php require_once './application/pages/footerForjs.php'; ?>
            <!--multi select files for copy move csv export share document---->
            <script type="text/javascript" src="assets/multi_function_script.js"></script>

            <!--for multiselect-->
            <script type="text/javascript" src="assets/plugins/multiselect/js/jquery.multi-select.js"></script>
            <script src="assets/js/jquery.core.js"></script>
            <!---end-->
            <script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
            <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>
            <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
            <script src="assets/plugins/jstree/jstree.min.js"></script>
            <script src="assets/pages/jquery.tree.js"></script>              

            <script src="assets/plugins/datatables/jquery.dataTables.min.js"></script>
            <script src="assets/plugins/datatables/dataTables.bootstrap.js"></script>

            <script>
                                                            //for workflow
                $("a#moveToWf").click(function () {
                    var id = $(this).attr('data');
                    // alert(id);
                    $("#mTowf").val(id);
                    //$('#datatable').dataTable();
                });
                $(document).ready(function () {

                        $(".select2").select2();
                    });
            </script>

            <script type="text/javascript">

                $("a#editMdata").click(function () {
                    var $id = $(this).attr('data');
                    var $row = $(this).closest('tr');
                    var name = '';
                    var values = [];
                    values = $row.find('td:nth-child(2)').map(function () {
                        var $this = $(this);
                        if ($this.hasClass('actions')) {

                        } else {
                            name = $.trim($this.text());

                        }

                        $("#editmetadata .modal-title").html("Update Meta Data of File: <strong>" + name + "</strong>");
                        $.post("application/ajax/editMdataValue.php", {ID: $id}, function (result, status) {
                            if (status == 'success') {
                                $("#modalModifyMvalue").html(result);
                            }
                        });
                    });
                });

                $("a#checkout").click(function () {
                    var path = $(this).attr('data');
                    // alert(id);
                    $.post("application/ajax/checkout.php", {CHECKOUT: path}, function (result, status) {
                        window.location.href = "<?php echo basename($_SERVER['PHP_SELF']) . '?' . $_SERVER['QUERY_STRING']; ?>";
                    });
                });
                $("a#editMdata").click(function () {
                    var id = $(this).attr('data');
                    // alert(id);
                    $.post("application/ajax/checkin.php", {CHECKIN: id}, function (result, status) {

                    });
                });
            </script> 
            <script type="text/javascript">
                $('#basicTree')
                        // listen for event
                        .on('changed.jstree', function (e, data) {
                            if (data.node) {
                                //debugger;
                                var nodeID = data.node.id + '_anchor';

                                var href = $("#" + nodeID).attr('href');
                                //history.pushState(null, null, href);
                                window.location.href = href;
                            }
                            var i, j, r = [];
                            for (i = 0, j = data.selected.length; i < j; i++) {
                                r.push(data.instance.get_node(data.selected[i]).text);
                            }
                            //$('#event_result').html('Selected : <strong>' + r.join(', ') + '</strong>');

                        })
                        // create the instance
                        .jstree({
                            'core': {
                                'themes': {
                                    'responsive': false
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
                $(document).ready(function () {

                    //Disable mouse right click
                    $("body").on("contextmenu", function (e) {
                        // return false;
                    });
                });
                $("a#viewMeta").click(function () {

                    if ($(this).find('i').hasClass('fa-eye')) {
                        $(".metadata").css('display', 'none');
                        $("a#viewMeta").find('i').removeClass('fa-eye');
                        $("a#viewMeta").find('i').addClass('fa-eye');
                        var mid = $(this).attr("data");
                        $("#" + mid).css('display', 'block');
                        $(this).find('i').removeClass('fa-eye');
                        $(this).find('i').addClass('fa-eye')
                    } else {
                        $(".metadata").css('display', 'none');
                        $("a#viewMeta").find('i').removeClass('fa-eye');
                        $("a#viewMeta").find('i').addClass('fa-eye');
                    }
                });

                $("a#removeRow").click(function () {
                    var id = $(this).attr('data');

                    // alert(id);
                    $("#uid").val(id);
                });
            </script>
            
             <div id="multi-csv-export-model" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog"> 
                    <div class="modal-content"> 
                        <div class="modal-header"> 
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="close" style="display:none;">×</button>
                            <h4 class="modal-title" id="unexport"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <?php echo $lang['Hre_msge'];?></h4>
                            <!--<h4 class="modal-title" style="display:none;" id="export_title"> Export Selected Rows </h4>--> 
                        </div>
                        <div id="export_unselected" style="display:none;">
                            <div class="modal-body">
                                <h5 class="text-alert"> <?php echo $lang['Pls_slt_Files_for_xpt_dta'];?></h5>
                            </div>

                            <div class="modal-footer"> 
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close'];?></button> 
                            </div>
                        </div>
                        <div id="export_selected">
                            <div class="modal-header"> 
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                                <h4 class="modal-title"><?php echo $lang['xprt_Slt_Dta'];?></h4> 
                            </div> 

                            <form action="multi_data_export" data-parsley-validate novalidate method="post" enctype="multipart/form-data">
                                <div class="modal-body row">

                                    <div class="col-md-12 shiv metaa">
                                        <span><strong><?php echo $lang['Select Files for Export Format'];?></strong></span>

                                        <select  class="multi-select" id="my_multi_select1" name="select_Fm">
                                            <option value="csv"><?php echo $lang['Csv'];?></option>
                                            <option value="excel"><?php echo $lang['Excel'];?></option>
                                            <option value="pdf"><?php echo $lang['Pdf'];?></option> 
                                            <option value="word"><?php echo $lang['Word'];?></option>
                                        </select>

                                    </div>
                                    <input type="hidden" name="export_doc_ids" id="export_doc_ids" value="">
                                </div>
                                <div class="modal-footer">
                                    <input type="hidden" value="<?php echo base64_decode(urldecode($_GET['id'])); ?>" name="id">
                                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close'];?></button> 
                                    <button class="btn btn-primary waves-effect waves-light fa fa-download" type="submit" name="exportData"><?php echo $lang['Export'];?></button>
                                </div>
                            </form>
                        </div>
                    </div> 
                </div>
            </div>

            <div id="full-width-modal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h4 class="modal-title" id="myLargeModalLabel"><?php echo $lang['Image_viewer'];?></h4>
                        </div>
                        <div class="modal-body">
                            <div id="Display"></div>
                        </div>
                        <div class="modal-footer">

                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close'];?></button>
                        </div>
                    </div>
                </div>

            </div>

            <!---assign workflow---->
            <div id="assign-workflow" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h4 class="modal-title"><?php echo $lang['Asgn_in_Wrk_flow'];?></h4> 
                        </div>
                        <form method="post" class="form-inline" id="wfasign">
                            <div class="modal-body">
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <label><?php echo $lang['Assign_To'];?>:</label>
                                        <select class="form-control" class="selectpicker" data-live-search="true" id="wfid" data-style="btn-white" style="" name="wfid">
                                            <option selected disabled style="background: #808080; color: #121213;"><?php echo $lang['Sl_Wf'];?></option>
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
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close'];?></button> 
                                <input type="submit" name="assignTo" class="btn btn-primary" value="Submit">
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!--display wait gif image after submit-->
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
                $("a#deleteVersionDoc").click(function () {
                    var id = $(this).attr("data");
                    $("#docid").val(id);
                });
            </script>

            <!--start delete model-->
            <div id="con-close-modal2" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog"> 
                    <div class="modal-content"> 
                        <div class="modal-header"> 
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                            <h4 class="modal-title"><?php echo $lang['Dlt_Docment'];?></h4> 
                        </div> 
                        <form method="post">
                            <div class="modal-body">
                                <p style="color: red;"><?php echo $lang['r_u_sr_tht_u_wnt_to_dl_ts_Dc'];?></p>
                            </div>
                            <div class="modal-footer"> 
                                <input type="hidden" id="uid" name="uid">
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close'];?></button>
                                <input type="submit" name="deleteDoc" class="btn btn-danger" value="<?php echo $lang['Delete'];?>">
                            </div>
                        </form>
                    </div> 
                </div>
            </div><!--ends delete modal -->
            
            <!--start delete Version of Document model-->
            <div id="deleteVersion" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog"> 
                    <div class="modal-content"> 
                        <div class="modal-header"> 
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                            <h4 class="modal-title"><?php echo $lang['Dlt_Vrsn_of_Docment'];?></h4> 
                        </div> 
                        <form method="post">
                            <div class="modal-body">
                                <p style="color: red;"><?php echo $lang['r_u_sr_tht_u_wt_to_dl_ts_vsn_of_Dc_th_dc_wl_b_dlt_pnt'];?></p>
                            </div>
                            <div class="modal-footer"> 
                                <input type="hidden" id="docid" name="docid">
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close'];?></button>
                                <input type="submit" name="deleteVersionDoc" class="btn btn-danger" value="<?php echo $lang['Delete'];?>">
                            </div>
                        </form>
                    </div> 
                </div>
            </div><!--ends delete modal -->
            <!--Edit metadata-->
            <div id="editmetadata" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog modal-lg"> 
                    <div class="modal-content"> 
                        <form method="post" enctype="multipart/form-data">
                            <div class="modal-header"> 
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                                <h4 class="modal-title"><?php echo $lang['Edit_MetaData'];?></h4> 
                            </div>

                            <div class="modal-body" id="modalModifyMvalue">
                                <img src="assets/images/load.gif" alt="load" class="img-responsive center-block" width="50px"/>
                            </div> 
                            <div class="modal-footer">

                                <button type="button" class="btn btn-danger" data-dismiss="modal"><?php echo $lang['Close'];?></button> 
                                <button type="submit" name="editMetaValue" class="btn btn-primary"><?php echo $lang['Save'];?> </button> 
                            </div>
                        </form>

                    </div> 
                </div>
            </div>
            <!-- for audio model-->
            <div id="modal-audio" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h4 class="modal-title" id="myModalLabel"><?php echo $lang['Play/Dwnld_Ado'];?></h4>
                        </div>
                        <div id="foraudio">

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close'];?></button>

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
                            <h4 class="modal-title" id="myModalLabel"><?php echo $lang['Play_video'];?></h4>
                        </div>
                        <div  id="videofor">


                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close'];?></button>

                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
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
                //for video clip
                $("a#video").click(function () {
                    var id = $(this).attr('data');

                    $.post("application/ajax/videoformat.php", {vid: id}, function (result, status) {
                        if (status == 'success') {
                            $("#videofor").html(result);
                            //alert(result);

                        }
                    });
                });
                //for audio player
                $("a#audio").click(function () {
                    var id = $(this).attr('data');

                    $.post("application/ajax/audioformat.php", {aid: id}, function (result, status) {
                        if (status == 'success') {
                            $("#foraudio").html(result);
                            //alert(result);

                        }
                    });
                });

            </script>
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
            <!--for move & other-->

            <!-- move selected files---->
            <div id="move-selected-files" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog">
                    <div class="modal-content" > 
                        <div class="modal-header"> 
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h4 class="modal-title" id="unseMove"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <?php echo $lang['Hre_msge'];?>!</h4>
                            <h4 class="modal-title" style="display:none;" id="mov"> <?php echo $lang['Move_Slt_Files'];?> </h4> 
                        </div>
                        <div id="unselected" style="display:none;">
                            <div class="modal-body">
                                <h5 class="text-alert"> <?php echo $lang['Pls_slt_Files_for_move'];?>.</h5>
                            </div>

                            <div class="modal-footer"> 
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close'];?></button> 
                            </div>
                        </div>
                        <div id="selected">
                            <form method="post" class="form-inline">
                                <div class="modal-body">
                                    <input type="hidden" name="doc_id_smove_multi" id="doc_id_smove_multi" value="">
                                    <input type="hidden" name="sl_id_move_multi" id="sl_id_move_multi" value="">
                                    <div class="form-group">
                                        <?php
                                        $moveFolderName = mysqli_query($db_con, "select * from tbl_storage_level where sl_id = $slid") or die('Error in move folder name: ' . mysqli_error($db_con));
                                        $rwmoveFolderName = mysqli_fetch_assoc($moveFolderName);
                                        ?>     
                                        <label><?php echo $lang['Move_Fld_File'];?>: </label>  <label> <?php echo $rwmoveFolderName['sl_name']; ?></label>
                                        <br><br>
                                        <div class="col-md-12">
                                            <label> <?php echo $lang['Move_To'];?>: &nbsp;</label>
                                            <select class="form-control" name="moveToParentId" id="moveToParentId">

                                                <option selected disabled style="background: #808080; color: #121213;"><?php echo $lang['Sel_Strg_Lvl'];?></option>

                                                <?php
                                                $storeName = mysqli_query($db_con, "select * from tbl_storage_level where sl_depth_level= '0' ") or die('Error in move store: ' . mysqli_error($db_con));

                                                while ($rwstoreName = mysqli_fetch_assoc($storeName)) {
                                                    ?>
                                                    <option value="<?php echo $rwstoreName['sl_id']; ?>"><?php echo $rwstoreName['sl_name']; ?></option>
                                                <?php } ?>
                                            </select>
                                            <br>            
                                            <div class="row">
                                                <div class="col-md-3"></div>

                                                <div class="col-md-9">
                                                    <span class="" id="child1">

                                                    </span>

                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer"> 
                                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php $lang['Close'];?></button> 
                                    <input type="submit" name="movemulti" class="btn btn-primary" value="Move files">
                                </div>

                            </form>
                        </div>
                    </div> 
                </div>
            </div>
            <script>

                $("#moveToParentId").change(function () {
                    var lbl = $(this).val();
                    //alert(lbl);
                    $.post("application/ajax/parentMoveList_1.php", {parentId: lbl, levelDepth: 0, sl_id:<?php echo $slid; ?>}, function (result, status) {
                        if (status == 'success') {
                            $("#child1").html(result);
                            //alert(result);
                        }
                    });
                });

                //filter limit

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

            <!--copy selected files--->
            <div id="copy-selected-files" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog"> 
                    <div class="modal-content"> 
                        <div class="modal-header"> 
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                            <h4 class="modal-title" id="cop"> <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <?php echo $lang['Hre_msge'];?>!</h4> 
                            <h4 class="modal-title" style="display:none;" id="ctitle"><?php echo $lang['Cpy_Slt_Files_in_Storage'];?></h4> 
                        </div> 
                       <!--<script type="text/javascript" src="./assets/jsCustom/selectcheckbox.js"></script>-->
                        <div id="unselected1" style="display:none;">
                            <div class="modal-body">
                                <h5 class="text-alert"><?php echo $lang['Pls_slt_Files_for_Cpy'];?>.</h5>
                            </div>
                            <div class="modal-footer"> 
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close'];?></button> 
                            </div>
                        </div>
                        <div id="selected1">
                            <form method="post" class="form-inline">
                                <div class="modal-body" id="csf">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-6 form-group">
                                                <label><?php echo $lang['Copy_files'];?>:</label>
                                                <input type="text" readonly id="tocopyfolder" name="toCopyFolder" class="form-control" value="<?php echo $tocpyFolder = $rwFolder['sl_name']; ?>">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <p class="text-danger" id="error"></p>
                                            </div>
                                            <div class="clearfix"></div>

                                            <div class="col-md-6 form-group">
                                                <input type="hidden" name="doc_ids" id="doc_ids" values="">
                                                <input type="hidden" name="sl_id4" id="sl_id4" values="">

                                                <label> <?php echo $lang['Cpy_To'];?>: &nbsp;</label>
                                                <select class="form-control" name="copyToParentId" id="copyToParentId" style="width: 100%">

                                                    <option selected style="background: #808080; color: #121213;">Select Storage Level</option>

                                                    <?php
                                                    $storeName = mysqli_query($db_con, "select * from tbl_storage_level where sl_depth_level= '0'") or die('Error in move store: ' . mysqli_error($db_con));

                                                    $rwstoreName = mysqli_fetch_assoc($storeName)
                                                    ?>
                                                    <option value="<?php echo $rwstoreName['sl_id']; ?>"><?php echo $rwstoreName['sl_name']; ?></option>
                                                </select>
                                            </div>
                                            <div class="clearfix"></div>

                                            <div class="col-md-6 form-group">
                                                <span class="" id="child2">
                                                </span>
                                            </div>

                                        </div>
                                    </div>   


                                    <div class="clearfix"></div>
                                </div>
                                <div class="modal-footer"> 
                                    <input value="<?php echo $rwFolder['sl_id']; ?>" name="modi" type="hidden" >
                                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close'];?></button> 
                                    <input type="submit" name="copyFiles" class="btn btn-primary" value="Copy Files">
                                </div>

                            </form>
                        </div>
                    </div>
                </div> 
            </div>
            <script>

                $("#copyToParentId").change(function () {
                    var lbl = $(this).val();
                    //alert(lbl);
                    $.post("application/ajax/parentMoveList_2.php", {parentId: lbl, levelDepth: 0, sl_id:<?php echo $slid; ?>}, function (result, status) {
                        if (status == 'success') {
                            $("#child2").html(result);
                            //alert(result);
                        }
                    });
                });

                //filter limit

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
            <!-- /.modal -->
            <div id="del_send_to_recycle" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog"> 
                    <div class="modal-content"> 
                        <form method="post" >
                            <div class="modal-header"> 
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h4 class="modal-title" style="display:none;" id="hid"> <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Here's a message!</h4>
                                <h4 class="modal-title" id="confirm"> Are You Sure?</h4> 
                            </div>
                            <div class="modal-body">
                                <span id="errmessage" style="display:none;"> <h5 class="text-alert">Please select Files for Delete.</h5></span>
                                <label class="text-danger" id="hide">Are You Sure Want to Delete This<?php if ($rwgetRole['role_id'] == 1) { ?>  <strong>Document Permanently.</strong><?php } ?>?</label>
                            </div> 
                            <div class="modal-footer">
                                <input type="hidden" id="sl_id1" name="sl_id1">
                                <input type="hidden" id="reDel" name="DelFile">
                                <!--                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close'];?></button> -->
                                <?php
                                if ($rwgetRole['role_id'] == 1) {
                                    ?>
                                    <button type="submit" id="yes" name="Delmultiple" class="btn btn-danger" value="Yes"> <i class="fa fa-trash-o"></i> Yes</button>
                                    <?php
                                }
                                ?>
                                <button type="submit" id="no" name="Delmultiple" class="btn btn-danger"> <i class="fa fa-trash-o"></i>
                                    <?php
                                    if ($rwgetRole['role_id'] == 1) {
                                        echo "No";
                                    } else {
                                        echo "Delete";
                                    }
                                    ?>

                                </button> 
                            </div>
                        </form>

                    </div> 
                </div>
            </div>

            <div id="csv_export_model" class="modal">
                <div class="modal-dialog">
                    <div class="modal-content"> 
                        <div class="modal-header"> 
                            <h4 class="modal-title"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Here's a message!</h4> 
                        </div>
                        <div class="modal-body">
                            <h5 class="text-alert">Please select Files for Export CSV.</h5>

                        </div>
                        <div class="modal-footer"> 
                            <button onclick="document.getElementById('csv_export_model').style.display = 'none'" class="btn btn-default waves-effect"><?php echo $lang['Close'];?></button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <?php
//        if (isset($_POST['editMetaValue'])) {
//
//            $getMetaId = mysqli_query($db_con, "select * from tbl_document_master where doc_id = '$_POST[metaId]'") or die('Error:' . mysqli_error($db_con));
//            $meta_row = mysqli_fetch_assoc($getMetaId);
//            $getMetaId = mysqli_query($db_con, "select * from tbl_metadata_to_storagelevel where sl_id = '$meta_row[doc_name]'") or die('Error:' . mysqli_error($db_con));
//            $i = 1;
//            while ($rwgetMetaId = mysqli_fetch_assoc($getMetaId)) {
//                $getMetaName = mysqli_query($db_con, "select * from tbl_metadata_master where id = '$rwgetMetaId[metadata_id]'") or die('Error:' . mysqli_error($db_con));
//
//                while ($rwgetMetaName = mysqli_fetch_assoc($getMetaName)) {
//                    $meta = mysqli_query($db_con, "select `$rwgetMetaName[field_name]` from tbl_document_master where doc_id='$meta_row[doc_id]'");
//                    $rwMeta = mysqli_fetch_array($meta);
//                    //echo $i; echo '-';
//                    if ($rwgetMetaName['field_name'] == 'noofpages') {
//                        
//                    } else {
//                        $fieldValue = $_POST['fieldName' . $i];
//                        $updateMeta = mysqli_query($db_con, "update tbl_document_master set $rwgetMetaName[field_name] = '$fieldValue' where doc_id = '$_POST[metaId]'") or die('Error' . mysqli_error($db_con));
//                        if ($updateMeta) {
//                            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'MetaData Value $fieldValue Updated','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
//                            echo'<script>taskSuccess("searchdata?metadata=' . $_GET['metadata'] . '&cond=' . $_GET['cond'] . '&searchText=' . $_GET['searchText'] . '&id=' . $_GET[id] . '","'.$lang['MtaDta_Updtd_Sucesfly'].'");</script>';
//                        }
//                    }
//                }
//
//                $i++;
//            }
//            mysqli_close($db_con);
//        }

        if (isset($_POST['editMetaValue'])) {
                if (!empty($_FILES['fileName']['name'])) {
                    $user_id = $_SESSION['cdes_user_id'];
                    $doc_id = preg_replace("/[^A-Za-z0-9 ]/", "", $_POST['docid']);
                    $doc_id = mysqli_real_escape_string($db_con, $doc_id);
                    $file_name = $_FILES['fileName']['name'];
                    $file_size = $_FILES['fileName']['size'];
                    $file_type = $_FILES['fileName']['type'];
                    $file_tmp = $_FILES['fileName']['tmp_name'];
                    $pageCount = $_POST['pageCount'];
                    if ($pageCount <= 0) {
                        $pageCount = 1;
                    }
                    $extn = substr($file_name, strrpos($file_name, '.') + 1);
                    $fname = substr($file_name, 0, strrpos($file_name, '.'));

                    $fileExtn = substr($file_name, strrpos($file_name, ".") + 1);
                    $getDocName = mysqli_query($db_con, "select * from tbl_document_master where doc_id = '$doc_id'") or die('Error:' . mysqli_error($db_con));
                    $rwgetDocName = mysqli_fetch_assoc($getDocName);
                    $docName = $rwgetDocName['doc_name'];
                    //$docName = explode("_", $docName);
                    $old_file_name = $rwgetDocName['old_doc_name'];
                    $oldextn = substr($old_file_name, strrpos($old_file_name, '.') + 1); // old file extn
                    $oldfname = substr($old_file_name, 0, strrpos($old_file_name, '.')); // old file name

                    $updateDocName = $docName . '_' . $doc_id; //storage id followed by doc id
                    $chekFileVersion = mysqli_query($db_con, "SELECT * FROM `tbl_document_master` WHERE find_in_set('$updateDocName', doc_name)") or die('Error:' . mysqli_error($db_con));
                    $flVersion = mysqli_num_rows($chekFileVersion);
                    $flVersion = $flVersion + 1;
                    $nfilename = $oldfname . '_' . $flVersion;

                    $strgName = mysqli_query($db_con, "select * from tbl_storage_level where sl_id = '$docName'") or die('Error:' . mysqli_error($db_con));
                    $rwstrgName = mysqli_fetch_assoc($strgName);
                    $storageName = $rwstrgName['sl_name'];
                    $storageName = str_replace(" ", "", $storageName);
                    $storageName = preg_replace('/[^A-Za-z0-9\-]/', '', $storageName);
                    $uploaddir = "extract-here/" . $storageName . '/';
                    if (!is_dir($uploaddir)) {
                        mkdir($uploaddir, 0777, TRUE) or die(print_r(error_get_last()));
                    }
                    $nfilename = preg_replace('/[^A-Za-z0-9_\-]/', '', $nfilename);
                    // $filenameEnct=$fname.'.'.$extn;// urlencode(base64_encode($fname)).'.'.$extn;
                    $filenameEnct = urlencode(base64_encode($nfilename));
                    $filenameEnct = preg_replace('/[^A-Za-z0-9_\-]/', '', $filenameEnct);
                    $filenameEnct = $filenameEnct . '.' . $extn;
                    $filenameEnct = time() . $filenameEnct;

                    //  $image_path = "images/" . $file_name;
                    $uploaddir = $uploaddir . $filenameEnct;
                    $upload = move_uploaded_file($file_tmp, $uploaddir) or die(print_r(error_get_last()));

                    if ($upload) {

                        $ftp = new ftp();
                        $ftp->conn("$fileserver", "$port", "$ftpUser", "$ftpPwd");
                        //$ftp->get(ROOT_FTP_FOLDER.'/'.$doc_Path_copy_to,$doc_path); 

                        $filepath = $storageName . '/' . $filenameEnct;
                        $ftp->put(ROOT_FTP_FOLDER . '/' . $filepath, $uploaddir);
                        $arr = $ftp->getLogData();
                        if ($arr['error'] != "") {
                            echo '<h2>Error:</h2>' . implode('<br />', $arr['error']);
                        }
                        $cols = '';
                        $columns = mysqli_query($db_con, "SHOW COLUMNS FROM tbl_document_master");
                        while ($rwCols = mysqli_fetch_array($columns)) {
                            if ($rwCols['Field'] != 'doc_id') {
                                if (empty($cols)) {
                                    $cols = '`' . $rwCols['Field'] . '`';
                                } else {
                                    $cols = $cols . ',`' . $rwCols['Field'] . '`';
                                }
                            }
                        }

                        $createVrsn = mysqli_query($db_con, "INSERT INTO tbl_document_master($cols) select $cols from tbl_document_master where doc_id='$doc_id'") or die('Error:' . mysqli_error($db_con));
                        $insertDocID = mysqli_insert_id($db_con);
                        $getMetaId = mysqli_query($db_con, "select * from tbl_document_master where doc_id = '$_POST[docid]'") or die('Error:' . mysqli_error($db_con));
                        //echo "select * from tbl_document_master where doc_id = '$_POST[docid]'";
                        $meta_row = mysqli_fetch_assoc($getMetaId);
                        $getMetaId = mysqli_query($db_con, "select * from tbl_metadata_to_storagelevel where sl_id = '$meta_row[doc_name]'") or die('Error:' . mysqli_error($db_con));
                        $i = 1;
                        while ($rwgetMetaId = mysqli_fetch_assoc($getMetaId)) {
                            $getMetaName = mysqli_query($db_con, "select * from tbl_metadata_master where id = '$rwgetMetaId[metadata_id]'") or die('Error:' . mysqli_error($db_con));
                            $StorageNme = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id='$rwgetMetaId[sl_id]'");
                            $rwStrName = mysqli_fetch_assoc($StorageNme);
                            while ($rwgetMetaName = mysqli_fetch_assoc($getMetaName)) {
                                $meta = mysqli_query($db_con, "select `$rwgetMetaName[field_name]` from tbl_document_master where doc_id='$meta_row[doc_id]'");
                                $rwMeta = mysqli_fetch_array($meta);
                                if ($rwgetMetaName['field_name'] == 'noofpages') {
                                    
                                } else {
                                    $fieldValue = $_POST['fieldName' . $i];
                                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Versioning Document $file_name Added','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                                    if ($createVrsn) {
                                        //echo "update tbl_document_master set `$rwgetMetaName[field_name]` = '$fieldValue', doc_name='$updateDocName' where doc_id='$insertDocID'";
                                        //echo "update tbl_document_master set old_doc_name='$file_name',filename='$fname', doc_extn='$extn', doc_path='$storageName/$filenameEnct', uploaded_by='$user_id', doc_size='$file_size', noofpages='$pageCount', dateposted='$date' where doc_id='$_POST[docid]'";
                                        //die;
                                        $updateNew = mysqli_query($db_con, "update tbl_document_master set `$rwgetMetaName[field_name]` = '$fieldValue', doc_name='$updateDocName' where doc_id='$insertDocID'");
                                        $updateOld = mysqli_query($db_con, "update tbl_document_master set `$rwgetMetaName[field_name]` = '$fieldValue', old_doc_name='$file_name',filename='$fname', doc_extn='$extn', doc_path='$storageName/$filenameEnct', uploaded_by='$user_id', doc_size='$file_size', noofpages='$pageCount', dateposted='$date' where doc_id='$_POST[docid]'");
                                        if ($updateNew && $updateOld) {
                                             echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","'.$lang['Updtd_Sfly'].'");</script>';
                                        }
                                    }
                                }
                            }
                        }
                    }
                } else {

                    $getMetaId = mysqli_query($db_con, "select * from tbl_document_master where doc_id = '$_POST[docid]'") or die('Error:' . mysqli_error($db_con));
                    //echo "select * from tbl_document_master where doc_id = '$_POST[docid]'";
                    $meta_row = mysqli_fetch_assoc($getMetaId);
                    $getMetaId = mysqli_query($db_con, "select * from tbl_metadata_to_storagelevel where sl_id = '$meta_row[doc_name]'") or die('Error:' . mysqli_error($db_con));
                    //echo "select * from tbl_metadata_to_storagelevel where sl_id = '$meta_row[doc_name]'";
                    $i = 1;

                    while ($rwgetMetaId = mysqli_fetch_assoc($getMetaId)) {

                        $getMetaName = mysqli_query($db_con, "select * from tbl_metadata_master where id = '$rwgetMetaId[metadata_id]'") or die('Error:' . mysqli_error($db_con));
                        $StorageNme = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id='$rwgetMetaId[sl_id]'");
                        $rwStrName = mysqli_fetch_assoc($StorageNme);
                        while ($rwgetMetaName = mysqli_fetch_assoc($getMetaName)) {
                            $meta = mysqli_query($db_con, "select `$rwgetMetaName[field_name]` from tbl_document_master where doc_id='$meta_row[doc_id]'");
                            $rwMeta = mysqli_fetch_array($meta);
                            //$metadatValue = $rwMeta[''];
                            //echo $i; echo '-';
                            if ($rwgetMetaName['field_name'] == 'noofpages') {
                                
                            } else {

                                $fieldValue = $_POST['fieldName' . $i];
                                //echo "update tbl_document_master set `$rwgetMetaName[field_name]` = '$fieldValue' where doc_id = '$_POST[metaId]' or (substring_index(doc_name,'_',-1)='$_POST[metaId]' and substring_index(doc_name,'_',1)='$_POST[metaId]')";
                                $updateMeta = mysqli_query($db_con, "update tbl_document_master set `$rwgetMetaName[field_name]` = '$fieldValue' where doc_id = '$_POST[docid]' or (substring_index(doc_name,'_',-1)='$_POST[docid]' and substring_index(doc_name,'_',1)='$_POST[docid]')") or die('Error' . mysqli_error($db_con));
                                if ($updateMeta) {
                                    //metadata update log
                                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'MetaData Value $fieldValue Assign in MetaData Field $rwgetMetaName[field_name] in $rwStrName[sl_name]','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                                    echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","'.$lang['Mtadta_Updted_sucsfly'].'");</script>';
                                }
                            }
                        }

                        $i++;
                    }
                    mysqli_close($db_con);
                }
            }
//delete doc
        
        if (isset($_POST['deleteDoc'])) {
            $id = preg_replace("/[^A-Za-z0-9 ]/","",$_POST['uid']);
            $getDocPath = mysqli_query($db_con, "select * from tbl_document_master where doc_id='$id'") or die('Error:' . mysqli_error($db_con));
            $rwgetDocPath = mysqli_fetch_assoc($getDocPath);
            $filePath = $rwgetDocPath['doc_path'];
            $fileName = $rwgetDocPath['old_doc_name'];
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Document $fileName Deleted','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
            $del = mysqli_query($db_con, "delete from tbl_document_master where doc_id='$id'") or die('Error:' . mysqli_error($db_con));
           
            if ($del) {
                
                $ftp = new ftp();
                $ftp->conn("$fileserver", "$port", "$ftpUser", "$ftpPwd");
                $ftp->singleFileDelete(ROOT_FTP_FOLDER.'/'.$filePath);
                $arr = $ftp->getLogData();
                if ($arr['error'] != ""){

                    echo '<h2>Error:</h2>' . implode('<br />', $arr['error']);
                }
                    unlink('extract-here/' . $filePath);
                echo'<script>taskSuccess("searchdata?metadata=' . preg_replace("/[^A-Za-z0-9]/","",$_GET['metadata']) . '&cond=' . preg_replace("/[^A-Za-z0-9]/","",$_GET['cond']) . '&searchText=' . preg_replace("/[^A-Za-z0-9]/","",$_GET['searchText']) . '&id=' . preg_replace("/[^A-Za-z0-9]/","",$_GET[id]) . '","'.$lang['Doc_Nt_Dltd'].'");</script>';
            } else {
                echo'<script>taskFailed("searchdata?metadata=' . preg_replace("/[^A-Za-z0-9]/","",$_GET['metadata']) . '&cond=' . preg_replace("/[^A-Za-z0-9]/","",$_GET['cond']) . '&searchText=' . preg_replace("/[^A-Za-z0-9]/","",$_GET['searchText']) . '&id=' . preg_replace("/[^A-Za-z0-9]/","",$_GET[id]) . '","'.$lang['Doc_Dltd_Sucesfly'].'");</script>';
            }
            mysqli_close($db_con);
        }
        ?>

        <?php
        //asign doc to workflow
        if (isset($_POST['assignTo'])) {

            echo 'workflow id: ' . $wfid = $_POST['wfid'];
            echo 'doc id: ' . $dcId = $_POST['mTowf'];

            $wfd = mysqli_query($db_con, "select * from tbl_workflow_master where workflow_id='$wfid'");
            $rwWfd = mysqli_fetch_assoc($wfd);
            $workFlowName = $rwWfd['workflow_name'];
            $workFlowArray = explode(" ", $workFlowName);
            $ticket = '';
            for ($w = 0; $w < count($workFlowArray); $w++) {
                $name = $workFlowArray[$w];
                $ticket = $ticket . substr($name, 0, 1);
            }
            $user_id = $_SESSION['cdes_user_id'];
            $ticket = $ticket . '_' . $user_id . '_' . strtotime($date);
            //get storage id from doc id
            $strgId = mysqli_query($db_con, "select * from tbl_document_master where doc_id='$dcId'");
            $rwstrgId = mysqli_fetch_assoc($strgId);

            $id = $rwstrgId['doc_name'];
            $id = $id . '_' . $wfid;


            if (!empty($wfid)) {

                $chkrw = mysqli_query($db_con, "select * from tbl_task_master where workflow_id = '$wfid'") or die('Error:' . mysqli_error($db_con));

                if (mysqli_num_rows($chkrw) > 0) {

                    $uptDocName = mysqli_query($db_con, "UPDATE tbl_document_master SET doc_name = '$id' where doc_id = '$dcId'") or die('error update:' . mysqli_error($db_con));

                    $getStep = mysqli_query($db_con, "select * from tbl_step_master where workflow_id = '$wfid' ORDER BY step_order ASC LIMIT 1") or die('Error:' . mysqli_error($db_con));
                    $getStpId = mysqli_fetch_assoc($getStep);
                    $stpId = $getStpId['step_id'];

                    $getTask = mysqli_query($db_con, "select * from tbl_task_master where step_id = '$stpId' ORDER BY task_order ASC LIMIT 1") or die('Error:' . mysqli_error($db_con));
                    $getTaskId = mysqli_fetch_assoc($getTask);
                    $tskId = $getTaskId['task_id'];

                    $getTaskDl = mysqli_query($db_con, "select * from tbl_task_master where task_id='$tskId'") or die('Error:' . mysqli_error($db_con));
                    $rwgetTaskDl = mysqli_fetch_assoc($getTaskDl);

                    if ($rwgetTaskDl['deadline_type'] == 'Date' || $rwgetTaskDl['deadline_type'] == 'Hrs') {

                        $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 60));
                    }
                    if ($rwgetTaskDl['deadline_type'] == 'Days') {

                        $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 24 * 60));
                    }
                    $insertInTask = mysqli_query($db_con, "INSERT INTO tbl_doc_assigned_wf(task_id, doc_id, start_date, end_date, task_status, assign_by, task_remarks,ticket_id) VALUES ('$tskId', '$dcId', '$date', '$endDate', 'Pending', '$user_id', '$taskRemark','$ticket')") or die('Erorr: hh' . mysqli_error($db_con));
                    $idins = mysqli_insert_id($db_con);

                    $getTask = mysqli_query($db_con, "select * from tbl_task_master where task_id = '$tskId'") or die('Error:' . mysqli_error($db_con));
                    $rwgetTask = mysqli_fetch_assoc($getTask);
                    $TskStpId = $rwgetTask['step_id'];
                    $TskWfId = $rwgetTask['workflow_id'];
                    $TskOrd = $rwgetTask['task_order'];
                    $nextTaskOrd = $TskOrd + 1;
                    nextTaskAsin($nextTaskOrd, $TskWfId, $TskStpId, $dcId, $date, $user_id, $db_con, $taskRemark, $ticket);
                    if ($insertInTask) {

                        require_once './mail.php';
                        $mail = assignTask($ticket, $idins, $db_con);
                        if ($mail) {

                            echo '<script>taskSuccess("searchdata", "'.$lang['Sumitd_Sucsfly'].'");</script>';
                        } else {

                            echo '<script>taskFailed("searchdata", "'.$lang['Ops_Ml_nt_snt'].'")</script>';
                        }
                    } else {
                        echo '<script>taskFailed("searchdata", "'.$lang['Opps_Sbmsn_fld'].'")</script>';
                    }
                } else {
                    echo '<script>taskFailed("searchdata", "'.$lang['Tre_is_no_tsk_in_ts_wfw'].'")</script>';
                }
            } else {
                echo'<script>taskFailed("searchdata","'.$lang['Pls_Slt_WF'].'");</script>';
            }
            mysqli_close($db_con);
        }
        ?>
        <?php
        if (isset($_POST['shareFiles'])) {
            $fromUser = $_SESSION[cdes_user_id];
            $ToUser = $_POST['userid'];
            $date = date('Y-m-d H:i:s');
            $ToUser = implode(",", $ToUser);
            $shareDocIds = $_POST['shareFile'];
            $shareDocIds = explode(',', $shareDocIds);
            foreach ($shareDocIds as $shareId) {

                $shareFiles = mysqli_query($db_con, "INSERT INTO `tbl_document_share`(`from_id`, `to_ids`, `doc_ids`, `dateShare`) VALUES ('$fromUser','$ToUser','$shareId', '$date')") or die('Error in insert share document' . mysqli_error($db_con));
            }
            $shareDocNm = mysqli_query($db_con, "select old_doc_name from tbl_document_master where doc_id = '$shareId'") or die('Error :' . mysqli_error($db_con));
            while ($rwshareDocNm = mysqli_fetch_assoc($shareDocNm)) {

                if ($shareFiles) {
                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `doc_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null, '$shareDocIds', 'Storage Document $rwshareDocNm[old_doc_name] Shared','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                    if ($log) {
                        $message = "Y";
                    }
                }
            }
            if ($message == "Y") {
                echo'<script>taskSuccess("storageFiles?id=' . $_GET[id] . '","'.$lang['Doc_shared_Sfly'].'");</script>';
            } else {
                echo'<script>taskFailed("storageFiles?id=' . $_GET[id] . '","'.$lang['Doc_Cpy_Sfly'].'");</script>';
            }
            mysqli_close($db_con);
        }
        if (isset($_POST['copyFiles'])) {
                $to = preg_replace("/[^A-Za-z0-9, ]/","",$_POST['lastMoveId']);
                $to = mysqli_real_escape_string($db_con, $to);
                $level = preg_replace("/[^A-Za-z0-9, ]/","",$_POST['lastMoveIdLevel']);
                $level = mysqli_real_escape_string($db_con, $level);
                $doc_ids = preg_replace("/[^A-Za-z0-9, ]/","",$_POST['doc_ids']);
                $doc_ids = mysqli_real_escape_string($db_con, $doc_ids);
                $copyToParentId =preg_replace("/[^A-Za-z0-9, ]/","", $_POST['copyToParentId']);
                $copyToParentId = mysqli_real_escape_string($db_con, $copyToParentId);
                $sl_id4 = preg_replace("/[^A-Za-z0-9, ]/","",$_POST['sl_id4']);
                $sl_id4 = mysqli_real_escape_string($db_con, $sl_id4);
                $meta = mysqli_query($db_con, "select * from tbl_metadata_to_storagelevel where sl_id='$sl_id4'"); //?
                //echo "select * from tbl_metadata_to_storagelevel where sl_id='$sl_id4'";
                $fetchresult = mysqli_query($db_con, "select * from tbl_document_master where doc_id in($doc_ids) and doc_name='$sl_id4'");
                $copyLaststrg = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id = '$to'") or die('Error :' . mysqli_error($db_con));
                $rwcopyLaststrg = mysqli_fetch_assoc($copyLaststrg);
                $rowcount = mysqli_num_rows($fetchresult);

                $rowmultifield = mysqli_fetch_field($fetchresult);

                while ($rowmulticopy = mysqli_fetch_array($fetchresult)) {
                    $doc_extn = $rowmulticopy['doc_extn'];
                    $old_doc_name = $rowmulticopy['old_doc_name'];
                    $doc_path = "extract-here/" . $rowmulticopy['doc_path'];
                    $uploaded_by = $rowmulticopy['uploaded_by'];
                    $doc_size = $rowmulticopy['doc_size'];

                    $doc_EncryptFile = explode('/', $doc_path);
                   $doc_Encrypt_nm = end($doc_EncryptFile);
                    $dir_to = "extract-here/" . $rwcopyLaststrg['sl_name'];

                    if (!is_dir($dir_to)) {
                        mkdir($dir_to);
                    }
                    $dir = "extract-here/" . $rwcopyLaststrg['sl_name'];

                    $doc_Path_copy_to = $dir . "/" . $doc_Encrypt_nm;
                    $pathArray = explode('/', $doc_Path_copy_to);

                    array_shift($pathArray);

                    $db_copy_Path_to = implode('/', $pathArray);

                    copy($doc_path, $doc_Path_copy_to);
                    
                    $ftp = new ftp();
                    $ftp->conn("$fileserver", "$port", "$ftpUser", "$ftpPwd");
                    //$ftp->get(ROOT_FTP_FOLDER.'/'.$doc_Path_copy_to,$doc_path); 
                   
                   $filepath = $rwcopyLaststrg['sl_name'].'/'.$doc_Encrypt_nm;
                    $ftp->put(ROOT_FTP_FOLDER.'/'.$filepath,$doc_path);
                    $arr = $ftp->getLogData();
                    if ($arr['error'] != ""){
                        echo '<h2>Error:</h2>' . implode('<br />', $arr['error']);
                    }

                    $sql2 = "INSERT INTO tbl_document_master SET";
                    $sql2 .= " doc_name='$to',old_doc_name='$old_doc_name',doc_extn='$doc_extn',doc_path='$db_copy_Path_to',uploaded_by='$uploaded_by',doc_size='$doc_size',dateposted='$rowmulticopy[dateposted]',noofpages='$rowmulticopy[noofpages]'";
                    while ($rwMeta = mysqli_fetch_assoc($meta)) {
                        $metan = mysqli_query($db_con, "select field_name from tbl_metadata_master where id='$rwMeta[metadata_id]'");
                        $rwMetan = mysqli_fetch_assoc($metan);

                        $field = $rwMetan['field_name'];
                        $value = $rowmulticopy[$field];
                        $sql2 .= ",$field='$value'";
                    }
                   //echo $sql2;
                    $multicopyinsert = mysqli_query($db_con, $sql2)or die("". mysqli_error($db_Con));
                    if ($multicopyinsert) {
                        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `doc_id`,`action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$to', '$rowmulticopy[doc_id]','Storage document $old_doc_name copy to Storage $rwcopyLaststrg[sl_name].','$date',null,'$host','')") or die('Error DB: ' . mysqli_error($db_con));
                        if ($log) {
                            $message = "yes";
                        }
                    }
                }

                if ($message == "yes") {
                    echo'<script>taskSuccess("storageFiles?id=' . $pageid . '","'.$lang['Doc_Cpy_Sfly'].'");</script>';
                }
                mysqli_close($db_con);
            }

        //for move multi files
        if (isset($_POST['movemulti'])) {
            $to = preg_replace("/[^A-Za-z0-9 ]/","",$_POST['lastMoveId']);
            $level = preg_replace("/[^A-Za-z0-9 ]/","",$_POST['lastMoveIdLevel']);
            $mutiId = $_POST['doc_id_smove_multi'];
            $doc_id_smove_multi = explode(',', $mutiId);
            $moveToParentId =preg_replace("/[^A-Za-z0-9 ]/","", $_POST['moveToParentId']);
            $sl_id_move = $_POST['sl_id_move_multi'];
            $length = count($doc_id_smove_multi);
            if (isset($moveToParentId) && isset($doc_id_smove_multi)) {
                foreach ($doc_id_smove_multi as $doc_id_smove_multis) {
                    $doc_id_smove_multis=preg_replace("/[^A-Za-z0-9 ]/","",$doc_id_smove_multis);
                    $updateMoveDoc = "update tbl_document_master set doc_name = '$to' where doc_id ='$doc_id_smove_multis'";

                    mysqli_query($db_con, $updateMoveDoc) or die('Error' . mysqli_error($db_con));
                    $moveDocNm = mysqli_query($db_con, "select * from tbl_document_master where doc_id in($doc_id_smove_multis)") or die('Error' . mysqli_error($db_con));
                    $rwMoveNm = mysqli_fetch_assoc($moveDocNm);
                    $fromDocPath = "extract-here/" . $rwMoveNm['doc_path'];
                    $doc_EncryptFile = explode('/', $fromDocPath);
                    $doc_Encrypt_nm = end($doc_EncryptFile);
                    $movestrgeNm = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id ='$to'") or die('Error' . mysqli_error($db_con));
                    $rwmovestrgeNm = mysqli_fetch_assoc($movestrgeNm);
                    
                    $destinationPath = $rwmovestrgeNm['sl_name'].'/'.$doc_Encrypt_nm;
                    $sourcePath = $fromDocPath;


                    $ftp = new ftp();
                    $ftp->conn("$fileserver", "$port", "$ftpUser", "$ftpPwd");

                    $ftp->put(ROOT_FTP_FOLDER.'/'.$destinationPath,$sourcePath); 

                   $ftp->singleFileDelete(ROOT_FTP_FOLDER.'/'.$rwMoveNm['doc_path']);
                    $arr = $ftp->getLogData();
                    if ($arr['error'] != ""){

                        echo '<h2>Error:</h2>' . implode('<br />', $arr['error']);
                    }
                    
                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`,`doc_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$to', '$mutiId','$rwFolder[sl_name] Storage Document $rwMoveNm[old_doc_name] moved to Storage $rwmovestrgeNm[sl_name]','$date',null,'$host','')") or die('error : ' . mysqli_error($db_con));
                    if ($log) {
                        $message = 1;
                    }
                }
                if ($message == 1) {
                    echo'<script>taskSuccess("storageFiles?id=' . preg_replace("/[^A-Za-z0-9]/","",$_GET[id]) . ((isset($_GET[start])) ? ('&start=' . $_GET[start]) : '') . '","'.$lang['Fls_mvd_Scsfly'].'");</script>';
                } else {
                    echo'<script>taskFailed("storageFiles?id=' . preg_replace("/[^A-Za-z0-9]/","",$_GET[id]) . ((isset($_GET[start])) ? ('&start=' . $_GET[start]) : '') . '","'.$lang['Fld_to_mv_Fls'].'");</script>';
                }
            }
            mysqli_close($db_con);
        }
        //multi select delete files
         if (isset($_POST['Delmultiple'])) {
                $filePath =array();
                $pathtxt =array();
                $filename =array();
                $permission = trim($_POST['Delmultiple']);
                $del_sl_id = explode($_POST['sl_id1']);
                $docDelete = trim($_POST['DelFile']);
                $user_id4 = $_SESSION['cdes_user_id'];
                $chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um where FIND_IN_SET('$user_id4', user_ids) > 0") or die('Error:' . mysqli_error($db_con));
                $rwcheckUser = mysqli_fetch_assoc($chekUsr);
                $getDocPath = mysqli_query($db_con, "select doc_path,old_doc_name,doc_name from tbl_document_master where doc_id in($docDelete)") or die('Error:' . mysqli_error($db_con));
                while ($rwgetDocPath = mysqli_fetch_assoc($getDocPath)) {
                    $filePath[] = $rwgetDocPath['doc_path'];
                    $path = substr($rwgetDocPath['doc_path'], 0, strrpos($rwgetDocPath['doc_path'], '/') + 1);
                    $pathtxt[] = 'extract-here/' . $path;
                    $filename[] = $rwgetDocPath['old_doc_name'];
                    $storgId = $rwgetDocPath['doc_name'];
                }
                if ($rwcheckUser['role_id'] == 1 && $permission == "Yes") {
                    
                    $del = mysqli_query($db_con, "DELETE FROM tbl_document_master WHERE doc_id in($docDelete)") or die('Error:' . mysqli_error($db_con));
                    $delshareDoc = mysqli_query($db_con, "DELETE FROM tbl_document_share WHERE doc_ids in($docDelete)") or die('Error:' . mysqli_error($db_con));
                    foreach ($filePath as $filePaths) {
                        $path = 'extract-here/' . $filePaths;
                        $ftppath  = explode('/', $filePaths);
                      
                        $ftp = new ftp();
                        $ftp->conn("$fileserver", "$port", "$ftpUser", "$ftpPwd");
                        $ftp->singleFileDelete(ROOT_FTP_FOLDER.'/'.$filePaths);
                        $arr = $ftp->getLogData();
                        if ($arr['error'] != ""){

                            echo '<h2>Error:</h2>' . implode('<br />', $arr['error']);
                        }

                        unlink($path);
                    }
                    if ($del) {
                        
                        foreach ($filename as $filenames) {
                            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Storage Document $filenames Deleted','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                        }

                        echo'<script>taskSuccess("storageFiles?id=' . urlencode(base64_encode($storgId)) . '","'.$lang['Doc_Dltd_Sucesfly'].'");</script>';
                    } else {
                        echo'<script>taskFailed("storageFiles?id=' . urlencode(base64_encode($storgId)) . '","'.$lang['Doc_Nt_Dltd'].'");</script>';
                    }
                } elseif ($rwcheckUser['role_id'] == 1 && $permission == "No") {
                    $deletefilename1 = mysqli_query($db_con, "UPDATE tbl_document_master SET flag_multidelete=0 WHERE doc_id in($docDelete)") or die('Error:' . mysqli_error($db_con));
                    if ($deletefilename1) {
                        foreach ($filename as $filenames) {
                            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Storage Document $filenames Deleted','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                        }

                        echo'<script>taskSuccess("storageFiles?id=' . urlencode(base64_encode($storgId)) . '","'.$lang['Doc_Dltd_Sucesfly'].'");</script>';
                    } else {
                        echo'<script>taskFailed("storageFiles?id=' . urlencode(base64_encode($storgId)) . '","'.$lang['Doc_Nt_Dltd'].'");</script>';
                    }
                } else {
                    $deletefilename1 = mysqli_query($db_con, "UPDATE tbl_document_master SET flag_multidelete=0 WHERE doc_id in($docDelete)") or die('Error:' . mysqli_error($db_con));
                    if ($deletefilename1) {
                        foreach ($filename as $filenames) {
                            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Storage Document $filenames Deleted','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                        }
                        echo'<script>taskSuccess("storageFiles?id=' . urlencode(base64_encode($storgId)) . '","'.$lang['Doc_Dltd_Sucesfly'].'");</script>';
                    } else {
                        echo'<script>taskFailed("storageFiles?id=' . urlencode(base64_encode($storgId)) . '","'.$lang['Doc_Nt_Dltd'].'");</script>';
                    }
                }
                mysqli_close($db_con);
            }

        //update version document
        if (isset($_POST['updateDoc'])) {
            $user_id = $_SESSION['cdes_user_id'];
            if (!empty($_FILES['fileName']['name'])) {
                $doc_id = $_POST['docid'];
                $file_name = $_FILES['fileName']['name'];
                $file_size = $_FILES['fileName']['size'];
                $file_type = $_FILES['fileName']['type'];
                $file_tmp = $_FILES['fileName']['tmp_name'];
                $pageCount = $_POST['pageCount'];
                if ($pageCount <= 0) {
                    $pageCount = 1;
                }
                $extn = substr($file_name, strrpos($file_name, '.') + 1);
                $fname = substr($file_name, 0, strrpos($file_name, '.'));

                $fileExtn = substr($file_name, strrpos($file_name, ".") + 1);
                $getDocName = mysqli_query($db_con, "select * from tbl_document_master where doc_id = '$doc_id'") or die('Error:' . mysqli_error($db_con));
                $rwgetDocName = mysqli_fetch_assoc($getDocName);
                $docName = $rwgetDocName['doc_name'];
                //$docName = explode("_", $docName);
                $old_file_name = $rwgetDocName['old_doc_name'];
                $oldextn = substr($old_file_name, strrpos($old_file_name, '.') + 1); // old file extn
                $oldfname = substr($old_file_name, 0, strrpos($old_file_name, '.')); // old file name

                $updateDocName = $docName . '_' . $doc_id; //storage id followed by doc id
                $chekFileVersion = mysqli_query($db_con, "SELECT * FROM `tbl_document_master` WHERE find_in_set('$updateDocName', doc_name)") or die('Error:' . mysqli_error($db_con));
                $flVersion = mysqli_num_rows($chekFileVersion);
                $flVersion = $flVersion + 1;
                $nfilename = $oldfname . '_' . $flVersion;

                $strgName = mysqli_query($db_con, "select * from tbl_storage_level where sl_id = '$docName'") or die('Error:' . mysqli_error($db_con));
                $rwstrgName = mysqli_fetch_assoc($strgName);
                $storageName = $rwstrgName['sl_name'];
                $storageName = str_replace(" ", "", $storageName);
                $storageName = preg_replace('/[^A-Za-z0-9\-]/', '', $storageName);
                $uploaddir = "extract-here/images/" . $storageName . '/';
                if (!is_dir($uploaddir)) {
                    mkdir($uploaddir, 0777, TRUE) or die(print_r(error_get_last()));
                }
                $nfilename = preg_replace('/[^A-Za-z0-9_\-]/', '', $nfilename);
                // $filenameEnct=$fname.'.'.$extn;// urlencode(base64_encode($fname)).'.'.$extn;
                $filenameEnct = urlencode(base64_encode($nfilename));
                $filenameEnct = preg_replace('/[^A-Za-z0-9_\-]/', '', $filenameEnct);
                $filenameEnct = $filenameEnct . '.' . $extn;
                $filenameEnct = time() . $filenameEnct;

                //  $image_path = "images/" . $file_name;
                $uploaddir = $uploaddir . $filenameEnct;
                $upload = move_uploaded_file($file_tmp, $uploaddir) or die(print_r(error_get_last()));

                if ($upload) {
                    $cols = '';
                    $columns = mysqli_query($db_con, "SHOW COLUMNS FROM tbl_document_master");
                    while ($rwCols = mysqli_fetch_array($columns)) {
                        if ($rwCols['Field'] != 'doc_id') {
                            if (empty($cols)) {
                                $cols = '`' . $rwCols['Field'] . '`';
                            } else {
                                $cols = $cols . ',`' . $rwCols['Field'] . '`';
                            }
                        }
                    }

                    $createVrsn = mysqli_query($db_con, "INSERT INTO tbl_document_master($cols) select $cols from tbl_document_master where doc_id='$doc_id'") or die('Error:' . mysqli_error($db_con));
                    $insertDocID = mysqli_insert_id($db_con);
                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Versioning Document $file_name Added','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                    if ($createVrsn) {
                        $updateNew = mysqli_query($db_con, "update tbl_document_master set doc_name='$updateDocName' where doc_id='$insertDocID'");
                        $updateOld = mysqli_query($db_con, "update tbl_document_master set old_doc_name='$file_name',filename='$fname', doc_extn='$extn', doc_path='images/$storageName/$filenameEnct', uploaded_by='$user_id', doc_size='$file_size', noofpages='$pageCount', dateposted='$date' where doc_id='$doc_id'");
                        echo'<script>taskSuccess("storageFiles?id=' . $_GET[id] . ((isset($_GET[start])) ? ('&start=' . $_GET[start]) : '') . '","'.$lang['Updtd_Sfly'].'");</script>';
                    }
                }
            }
            mysqli_close($db_con);
        }
        
        
        if(isset($_POST['mailFiles'])){
                
                $userids = $_POST['userid']; 
                $subject = $_POST['subject']; 
                $mailbody = $_POST['mailbody']; 
                $doc_ids = $_POST['mailFile'];
                $doc_path =  array();
                $docdetails = mysqli_query($db_con, "select * from tbl_document_master where doc_id in($doc_ids)");
                while($docRow = mysqli_fetch_assoc($docdetails))
                {
                    $doc_path[] = "extract-here/" . $docRow['doc_path'];
                }
                
                foreach($userids as $key => $value){
                    
                    $user_id= $userids[$key];
                    
                    $userdetails = mysqli_query($db_con, "SELECT first_name, last_name, user_email_id from tbl_user_master where user_id='$user_id'") or die('error : ' . mysqli_error($db_con));
                    $row = mysqli_fetch_assoc($userdetails);
                   $username = $row['first_name'].' '.$row['last_name'];
                    $email  = $row['user_email_id'];
                   require_once './mail.php'; 
                   if(mailDocuments($projectName, $subject, $mailbody, $username, $email, $doc_path)){
                       
                         echo'<script>taskSuccess("'.$_SERVER['RESQUEST_URI'].'","'.$lang['document_send'].'");</script>';
                    } else {
                        echo'<script>taskFailed("'.$_SERVER['RESQUEST_URI'].'","'.$lang['error_occured_mail_doc'].'");</script>';
                    }
                }
            }
            
            if (isset($_POST['updateDoc'])) {

                $user_id = $_SESSION['cdes_user_id'];
                if (!empty($_FILES['fileName']['name'])) {
                    $doc_id = $_POST['docid'];
                    $file_name = $_FILES['fileName']['name'];
                    $file_size = $_FILES['fileName']['size'];
                    $file_type = $_FILES['fileName']['type'];
                    $file_tmp = $_FILES['fileName']['tmp_name'];
                    $pageCount = $_POST['pageCount'];
                    if ($pageCount <= 0) {
                        $pageCount = 1;
                    }
                    $extn = substr($file_name, strrpos($file_name, '.') + 1);
                    $fname = substr($file_name, 0, strrpos($file_name, '.'));

                    $fileExtn = substr($file_name, strrpos($file_name, ".") + 1);
                    $getDocName = mysqli_query($db_con, "select * from tbl_document_master where doc_id = '$doc_id'") or die('Error:' . mysqli_error($db_con));
                    $rwgetDocName = mysqli_fetch_assoc($getDocName);
                    $docName = $rwgetDocName['doc_name'];
                    //$docName = explode("_", $docName);
                    $old_file_name = $rwgetDocName['old_doc_name'];
                    $oldextn = substr($old_file_name, strrpos($old_file_name, '.') + 1); // old file extn
                    $oldfname = substr($old_file_name, 0, strrpos($old_file_name, '.')); // old file name

                    $updateDocName = $docName . '_' . $doc_id; //storage id followed by doc id
                    $chekFileVersion = mysqli_query($db_con, "SELECT * FROM `tbl_document_master` WHERE find_in_set('$updateDocName', doc_name)") or die('Error:' . mysqli_error($db_con));
                    $flVersion = mysqli_num_rows($chekFileVersion);
                    $flVersion = $flVersion + 1;
                    $nfilename = $oldfname . '_' . $flVersion;

                    $strgName = mysqli_query($db_con, "select * from tbl_storage_level where sl_id = '$docName'") or die('Error:' . mysqli_error($db_con));
                    $rwstrgName = mysqli_fetch_assoc($strgName);
                    $storageName = $rwstrgName['sl_name'];
                    $storageName = str_replace(" ", "", $storageName);
                    $storageName = preg_replace('/[^A-Za-z0-9\-]/', '', $storageName);
                    $uploaddir = "extract-here/" . $storageName . '/';
                    if (!is_dir($uploaddir)) {
                        mkdir($uploaddir, 0777, TRUE) or die(print_r(error_get_last()));
                    }
                    $nfilename = preg_replace('/[^A-Za-z0-9_\-]/', '', $nfilename);
                    // $filenameEnct=$fname.'.'.$extn;// urlencode(base64_encode($fname)).'.'.$extn;
                    $filenameEnct = urlencode(base64_encode($nfilename));
                    $filenameEnct = preg_replace('/[^A-Za-z0-9_\-]/', '', $filenameEnct);
                    $filenameEnct = $filenameEnct . '.' . $extn;
                    $filenameEnct = time() . $filenameEnct;

                    //  $image_path = "images/" . $file_name;
                    $uploaddir = $uploaddir . $filenameEnct;
                    $upload = move_uploaded_file($file_tmp, $uploaddir) or die(print_r(error_get_last()));

                    if ($upload) {
                        $ftp = new ftp();
                        $ftp->conn("$fileserver", "$port", "$ftpUser", "$ftpPwd");
                        //$ftp->get(ROOT_FTP_FOLDER.'/'.$doc_Path_copy_to,$doc_path); 

                        $filepath = $storageName . '/' . $filenameEnct;
                        $ftp->put(ROOT_FTP_FOLDER . '/' . $filepath, $uploaddir);
                        $arr = $ftp->getLogData();
                        if ($arr['error'] != "") {
                            echo '<h2>Error:</h2>' . implode('<br />', $arr['error']);
                        }
                        $cols = '';
                        $columns = mysqli_query($db_con, "SHOW COLUMNS FROM tbl_document_master");
                        while ($rwCols = mysqli_fetch_array($columns)) {
                            if ($rwCols['Field'] != 'doc_id') {
                                if (empty($cols)) {
                                    $cols = '`' . $rwCols['Field'] . '`';
                                } else {
                                    $cols = $cols . ',`' . $rwCols['Field'] . '`';
                                }
                            }
                        }
                        $createVrsn = mysqli_query($db_con, "INSERT INTO tbl_document_master($cols) select $cols from tbl_document_master where doc_id='$doc_id'") or die('Error:' . mysqli_error($db_con));
                        $insertDocID = mysqli_insert_id($db_con);
                        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Versioning Document $file_name Added','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                        if ($createVrsn) {
                            $updateNew = mysqli_query($db_con, "update tbl_document_master set doc_name='$updateDocName' where doc_id='$insertDocID'");
                            $updateOld = mysqli_query($db_con, "update tbl_document_master set old_doc_name='$file_name',filename='$fname', doc_extn='$extn', doc_path='$storageName/$filenameEnct', uploaded_by='$user_id', doc_size='$file_size', noofpages='$pageCount', dateposted='$date' where doc_id='$doc_id'");
                            echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","'.$lang['Updtd_Sfly'].'");</script>';
                        }
                    }
                }
            }
            
            if (isset($_POST['deleteVersionDoc'])) {
                $id =preg_replace("/[^A-Za-z0-9 ]/","", $_POST['docid']);
                $id = mysqli_real_escape_string($db_con, $id);
                $getDocPath = mysqli_query($db_con, "select * from tbl_document_master where doc_id='$id'") or die('Error:' . mysqli_error($db_con));
                $rwgetDocPath = mysqli_fetch_assoc($getDocPath);
                $filePath = $rwgetDocPath['doc_path'];
                $delvrsnfile = $rwgetDocPath['old_doc_name'];
                $del = mysqli_query($db_con, "delete from tbl_document_master where doc_id='$id'") or die('Error:' . mysqli_error($db_con));
                unlink('extract-here/' . $filePath);
                if ($del) {
					
                    $ftp = new ftp();
                    $ftp->conn("$fileserver", "$port", "$ftpUser", "$ftpPwd");
                    $ftp->singleFileDelete(ROOT_FTP_FOLDER.'/'.$filePath);
                    $arr = $ftp->getLogData();
                    if ($arr['error'] != ""){

                            echo '<h2>Error:</h2>' . implode('<br />', $arr['error']);
                    }
                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Storage Document $delvrsnfile Deleted','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));

                    $docName = explode("_", $rwgetDocPath['doc_name']);
                    $storgId = $docName[0];
                    echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","'.$lang['file_version_delt_success'].'");</script>';
                    //echo'<script>taskSuccess("storageFiles","Document Deleted Successfully !");</script>';
                } else {
                    echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","'.$lang['file_version_not_delt'].'")</script>';
                }
                mysqli_close($db_con);
            }
        ?>
        <!-- for add and search metaData---> 
        <script>
            function invisible(myid)
            {

                $(".numid-" + myid).remove();
                $("#addfields").show();

            }
            var max_fields = <?= $metadatacount; ?>; //maximum input boxes allowed  
            function incrementCount()
            {

                max_fields = max_fields + 1;
                //alert(max_fields);
            }
            ;


            $(document).ready(function () {
                var wrapper = $(".contents"); //Fields wrapper
                var add_button = $("#addfields"); //Add button ID
                var id =<?= $slid ?>;
                var x = 1; //initlal text box count




                $(add_button).click(function (e) { //on add input button click
                    e.preventDefault();

                    if (x < max_fields) { //max input box allowed
                        x++;
                        //text box increment
                        $.ajax({url: "application/ajax/addMultipleMeataDtaSearch?id=" + id, success: function (result) {
                                $(wrapper).append("<div class='col-lg-12' style='margin-bottom:17px'>" + result + "<button class='remove_field btn btn-primary'><i class='fa fa-minus-circle' aria-hidden='true'></i></a>" + "</div>"); //add input box


                            }});

                    } else
                    {
                        alert("No. More meta data available");
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


        </script>
        <!---end add and search metadata-->
    </body>
</html>