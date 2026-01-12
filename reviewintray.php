<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    require_once './application/config/database.php';
    require_once './application/pages/head.php';
    $loginUser = $_SESSION[cdes_user_id];
    $sameGroupIDs = array();
    $group = mysqli_query($db_con, "select * from tbl_bridge_grp_to_um where find_in_set('$_SESSION[cdes_user_id]',user_ids)") or die('Error' . mysqli_error($db_con));
    while ($rwGroup = mysqli_fetch_assoc($group)) {
        $sameGroupIDs[] = $rwGroup['user_ids'];
    }
    $sameGroupIDs = implode(',', $sameGroupIDs);
    $sameGroupIDs = explode(',', $sameGroupIDs);
    $sameGroupIDs = array_unique($sameGroupIDs);
    sort($sameGroupIDs);
    $sameGroupIDs = implode(',', $sameGroupIDs);
    

    if ($rwgetRole['review_intray'] != '1') {
        header('Location: ./index');
    }
    ?>
    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />

    <!-- Plugin Css-->
    <link rel="stylesheet" href="assets/plugins/magnific-popup/css/magnific-popup.css" />
    <link rel="stylesheet" href="assets/plugins/jquery-datatables-editable/datatables.css" />

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
                                        <a href="#"><?php echo $lang['reviewer']; ?> </a>
                                    </li>
                                    <li>
                                        <a href="reviewintray"><?php echo $lang['reviewintray']; ?> </a>
                                    </li>
                                    <li> <a href="#" data-toggle="modal" data-target="#help-modal" id="helpview" data="36" title="<?= $lang['help']; ?>"><i class="fa fa-question-circle" style="font-size: 23px"></i> </a></li>

                                    <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                                </ol>
                            </div>
                        </div>
                        <div class="panel">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <div class="col-sm-12">
                                        <div class="col-sm-3">
                                            <input type="text" class="form-control specialchaecterlock" name="review_doc" id="review_doc" value="<?php echo xss_clean($_GET['review_doc']); ?>" parsley-trigger="change" placeholder="<?php echo $lang['Enter_Ticket_Id']; ?>" required />
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <div class="input-daterange input-group"  id="date-range">
                                                        <input type="text" class="form-control" name="startDate" value="<?php echo $_GET['startDate']; ?>" id="strtdate" placeholder="<?php echo $lang['dd_mm_yyyy']; ?>" title="<?php echo $lang['dd_mm_yyyy']; ?>" autocomplete="off"/>
                                                        <span class="input-group-addon bg-custom b-0 text-white"><?php echo $lang['to']; ?></span>
                                                        <input type="text" class="form-control" name="endDate" value="<?php echo $_GET['endDate']; ?>" id="enddate"   placeholder="<?php echo $lang['dd_mm_yyyy']; ?>" title="<?php echo $lang['dd_mm_yyyy']; ?>"  autocomplete="off"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <select class="select2" data-live-search="true" name="user" data-style="btn-white" id="users">
                                                <option selected disabled style="background: #808080; color: #fff;"><?php echo $lang['select_assigne']; ?></option>
                                                <?php
                                                mysqli_set_charset($db_con, "utf8");
                                                $user = mysqli_query($db_con, "select * from tbl_user_master where user_id in($sameGroupIDs) and active_inactive_users='1' order by first_name, last_name asc");
                                                while ($rwUser = mysqli_fetch_assoc($user)) {
                                                    if ($rwUser['user_id'] != 1 && $_SESSION['cdes_user_id'] != $rwUser['user_id']) {
                                                        ?>
                                                        <option <?php
                                                        if ($_GET['user'] == $rwUser['user_id']) {
                                                            echo 'selected';
                                                        }
                                                        ?> value="<?php echo $rwUser['user_id']; ?>"><?php echo $rwUser['first_name'] . ' ' . $rwUser['last_name']; ?></option>

                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-2">
                                            <button type="submit" class="btn btn-primary" id="shareddoc"><i class="fa fa-search"></i> <?php echo $lang['Search']; ?></button>
                                            <a href="reviewintray" class="btn btn-warning"><i class="fa fa-refresh"></i></a>
                                        </div>

                                    </div>

                                </div>
                                <div class="panel-body">
                                    <?php
                                    if (in_array("1", $privileges) && $_SESSION['cdes_user_id'] == 1) {
                                        $where = "WHERE  assign_by in($sameGroupIDs)";
                                    } else {
                                        $where = "WHERE  action_by = '$_SESSION[cdes_user_id]' and review_status='0' and next_task='0'";
                                    }
                                    
                                    $startDate ="";
                                    $endDate = "";
                                    $uid ="";
                                    $searchTxt = "";
                                    
                                    if (isset($_GET['review_doc']) && !empty($_GET['review_doc'])) {
                                        $searchTxt = xss_clean($_GET['review_doc']);
                                        $where .= " and ticket_id like '%$searchTxt%'";
                                    }
                                    if (!empty($_GET['startDate']) && !empty($_GET['endDate'])) {
                                        $startDate = date("Y-m-d", strtotime($_GET['startDate']));
                                        $endDate = date("Y-m-d", strtotime($_GET['endDate']));
                                        ;
                                         $where .= " and DATE(start_date)>='$startDate' AND DATE(start_date)<='$endDate'";
                                    }
                                    if (isset($_GET['user']) && !empty($_GET['user'])) {
                                        $uid = $_GET['user'];
                                        $where .= " and assign_by = '$uid'";
                                    }
                                    if (in_array("1", $privileges) && $_SESSION['cdes_user_id'] == 1) {

                                        $sql = "SELECT distinct ticket_id,doc_id,start_date,task_status,task_remarks  FROM  `tbl_doc_review`  $where order by id desc";
                                    } else {
                                        $sql = "SELECT distinct ticket_id,doc_id,start_date,task_status,task_remarks,review_order,id FROM  `tbl_doc_review`  $where  order by id desc";
                                    }

                                    $retval = mysqli_query($db_con, $sql) or die('Could not get data: ' . mysqli_error($db_con));
                                    $foundnum = mysqli_num_rows($retval);
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

                                        <div class="box-body limit">

                                            <?= $lang['show_lst']; ?> <select id="limit" class="input-sm m-b-10">
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
                                            </select> <?= $lang['Documents']; ?>
                                            <div class="pull-right record">
                                                <?php echo $start + 1 ?> <?= $lang['to']; ?> <?php
                                                if (($start + $per_page) > $foundnum) {
                                                    echo $foundnum;
                                                } else {
                                                    echo ($start + $per_page);
                                                };
                                                ?> <span><?php echo $lang['Ttal_Rcrds']; ?> : <?php echo $foundnum; ?></span>
                                            </div>
                                            <table class="table table-striped table-bordered js-sort-table">
                                                <?php
                                                if (in_array("1", $privileges) && $_SESSION['cdes_user_id'] == 1) {
                                                    mysqli_set_charset($db_con, "utf8");
                                                    $ShDocId = "SELECT distinct ticket_id,doc_id,start_date,task_status,task_remarks,id FROM  `tbl_doc_review` $where order by id desc LIMIT $start, $per_page";
                                                } else {
                                                    mysqli_set_charset($db_con, "utf8");
                                                    $ShDocId = "SELECT distinct ticket_id,doc_id,start_date,task_status,task_remarks,review_order,id  FROM  `tbl_doc_review` $where  order by id desc LIMIT $start, $per_page";
                                                }
                                                $ShDocId = mysqli_query($db_con, $ShDocId);

                                                if (mysqli_num_rows($ShDocId) > 0) {
                                                    ?>
                                                    <thead>
                                                        <tr>
                                                            <th class="sort-js-none" ><?php echo $lang['SNO']; ?></th>
                                                            <th><?php echo $lang['Ticket_Id']; ?></th>
                                                            <th><?php echo $lang['File_Name']; ?></th>
                                                            <th><?php echo $lang['ReviewStatus']; ?></th>
                                                            <th class="sort-js-date" ><?php echo $lang['Ticket_Date_Time']; ?></th>
                                                            <th class="sort-js-number" ><?php echo $lang['File_Number']; ?></th>

                                                        </tr>
                                                    </thead>
                                                    <tbody> 
                                                        <?php
                                                        $i = $start + 1;
                                                        while ($rwTaskDoc = mysqli_fetch_assoc($ShDocId)) {
                                                            ?>
                                                            <tr class="gradeX" style="vertical-align: middle;">
                                                                <td><?php echo $i . '.'; ?></td>

                                                                <td><?php echo $rwTaskDoc['ticket_id']; ?></td>
                                                                <td>

                                                                    


                                                                        <?php
                                                                    if (!empty($rwTaskDoc['doc_id'])) {
                                                                        $doc = mysqli_query($db_con, "select * from tbl_document_reviewer where doc_id='$rwTaskDoc[doc_id]'") or die('Error' . mysqli_error($db_con));
                                                                        $rwDoc = mysqli_fetch_assoc($doc);
                                                                        $fnumber = $rwDoc['File_Number'];
                                                                        //echo substr($rwDoc['old_doc_name'], strpos($rwDoc['old_doc_name']) + 0);


                                                                        ?>

                                                                        <?php if($rwDoc['storage_doc_id']=="" && file_exists('thumbnail/review/'.base64_encode($rwDoc['doc_id']).'.jpg')){ ?>
                                                                            <div> <img class="thumb-image" src="thumbnail/review/<?=base64_encode($rwDoc['doc_id'])?>.jpg"> </div>
                                                                        <?php } ?>

                                                                        <?php if(file_exists('thumbnail/'.base64_encode($rwDoc['storage_doc_id']).'.jpg')){ ?>
                                                                            <div> <img class="thumb-image" src="thumbnail/<?=base64_encode($rwDoc['storage_doc_id'])?>.jpg"> </div>
                                                                        <?php } ?>


                                                                        <?php if (strtolower($rwDoc['doc_extn']) == 'pdf') {
                                                                            ?>
                                                                            <a title="<?php echo $lang['view_copy_print']; ?>" href="viewer?id=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])) ?>&i=<?php echo urlencode(base64_encode($rwDoc['doc_id'])); ?>&pn=1&chk=rw" class="pdfview" target="_blank">
                                                                                <?php echo $rwDoc['old_doc_name']; ?> <i class="fa fa fa-file-text-o" ></i></a>
                                                                            <a href="anott/index?id=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])) ?>&id1=<?php echo urlencode(base64_encode($rwDoc['doc_id'])); ?>&pn=1&chk=rw&reid=<?php echo urlencode(base64_encode($rwTaskDoc['id'])) ?>" class="pdfview" target="_blank">
                                                                                <i class="fa fa-edit" title="<?php echo $lang['word_edit']; ?>"></i></a>

                                                                            <?php
                                                                        } else if ($rwgetRole['image_file'] == '1' && (strtolower($rwDoc['doc_extn']) == 'jpg' || strtolower($rwDoc['doc_extn']) == 'jpeg' || strtolower($rwDoc['doc_extn']) == 'png' || strtolower($rwDoc['doc_extn']) == 'gif' || strtolower($rwDoc['doc_extn']) == 'bmp')) {
                                                                            ?>
                                                                            
                                                                            <a href="imageviewer?uid=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&i=<?php echo urlencode(base64_encode($rwDoc['doc_id'])); ?>&chk=rw"  target="_blank"><?php echo $rwDoc['old_doc_name']; ?>  <i class="fa fa-picture-o"></i></a>
                                                                        <a href="imageAnnotation?uid=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&i=<?php echo urlencode(base64_encode($rwDoc['doc_id'])); ?>&reid=<?php echo urlencode(base64_encode($rwTaskDoc['id'])); ?>&chk=rw"  target="_blank"> <i class="ti-marker-alt" data-toggle="tooltip" title="<?php echo $lang['image_file']; ?>"></i></a>
                                                                                <?php } else if (strtolower($rwDoc['doc_extn']) == 'tif' || strtolower($rwDoc['doc_extn']) == 'tiff') { ?>
                                                                            <a href="tiff-viewer?id=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&i=<?php echo urlencode(base64_encode($rwDoc['doc_id'])); ?>" target="_blank" >
                                                                                <?php if ($rwgetRole['tif_file'] == '1') { ?>
                                                                                    <?php echo substr($rwDoc['old_doc_name'], strpos($rwDoc['old_doc_name']) + 0); ?> <i class="fa fa-picture-o"></i>
                                                                                </a>
                                                                            <?php } ?>

                                                                        <?php } else if (strtolower($rwDoc['doc_extn']) == 'rtf' || strtolower($rwfileVersion['doc_extn']) == 'odt') { ?>
                                                                            <a href="viewword?i=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&id=<?php echo urlencode(base64_encode($rwfileVersion['doc_id'])); ?>&chk=rw&reid=<?php echo urlencode(base64_encode($rwTaskDoc['id'])) ?>" target="_blank">
                                                                                <?php echo $rwfileVersion['old_doc_name']; ?> <i class="fa fa-file-word-o"></i></a>

                                                                            <!-- psd version viewer-->
                                                                        <?php } else if (strtolower($rwDoc['doc_extn']) == 'xlsx') {
                                                                            ?>
                                                                            <a href="xlsx?uid=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&file=<?php echo urlencode(base64_encode($rwDoc['doc_id'])); ?>&chk=rw" target="_blank">
                                                                                <?php if ($rwgetRole['excel_file'] == '1') { ?>
                                                                                    <?php echo substr($rwDoc['old_doc_name'], strpos($rwDoc['old_doc_name']) + 0); ?> <i class="fa fa-file-excel-o"></i></a>
                                                                                <?php } ?>

                                                                        <?php } else if (strtolower($rwDoc['doc_extn']) == 'xls') {
                                                                            ?>
                                                                            <a href="xls?uid=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&file=<?php echo urlencode(base64_encode($rwDoc['doc_id'])); ?>&chk=rw" target="_blank">
                                                                                <?php if ($rwgetRole['excel_file'] == '1') { ?>
                                                                                    <?php echo substr($rwDoc['old_doc_name'], strpos($rwDoc['old_doc_name']) + 0); ?> <i class="fa fa-file-excel-o"></i></a>
                                                                                <?php } ?>

                                                                        <?php } else if (strtolower($rwDoc['doc_extn']) == 'doc' || strtolower($rwDoc['doc_extn']) == 'docx') { ?>
                                                                            <a href="phpWordOffice/wordviewer?i=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&id=<?php echo urlencode(base64_encode($rwDoc['doc_id'])); ?>&chk=rw&reid=<?php echo urlencode(base64_encode($rwTaskDoc['id'])) ?>" target="_blank">
                                                                                <?php if ($rwgetRole['doc_file'] == '1') { ?>
                                                                                    <?php echo $rwDoc['old_doc_name']; ?> <i class="fa fa-file-word-o"></i></a>
                                                                                <?php } ?>

                                                                        <?php } else if (strtolower($rwDoc['doc_extn']) == 'mp3' || strtolower($rwDoc['doc_extn']) == 'wav') { ?>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          <!--a class="" href="#modal-audio" data-uk-modal=""><i class="fa fa-music"></i> </a-->
                                                                            <a href="javascript:void(0)" data-toggle="modal" data-target="#modal-audio" data="<?php echo $rwDoc['doc_id']; ?>" id="audio">
                                                                                <?php if ($rwgetRole['audio_file'] == '1') { ?>
                                                                                    <?php echo substr($rwDoc['old_doc_name'], strpos($rwDoc['old_doc_name']) + 0); ?> <i class="fa fa-music"></i></a>
                                                                                <?php } ?>

                                                                        <?php } else if (strtolower($rwDoc['doc_extn']) == 'mp4' || strtolower($rwDoc['doc_extn']) == '3gp') { ?>
                                                                            <a href="javascript:void(0)" data-toggle="modal" data-target="#modal-video" data="<?php echo $rwDoc['doc_id']; ?>" id="video">
                                                                                <?php if ($rwgetRole['video_file'] == '1') { ?>
                                                                                    <?php echo substr($rwDoc['old_doc_name'], strpos($rwDoc['old_doc_name']) + 0); ?> <i class="fa fa-video-camera"></i></a>
                                                                                <?php } ?>                                                                        
                                                                            <?php } else {
                                                                                ?>
                                                                            <a href="downloaddoc?file=<?php echo urlencode(base64_encode($rwDoc['doc_id'])) ?>&chk=rw" id="fancybox-inner" target="_blank"> <i class="fa fa-download"></i> <?php echo $rwDoc['old_doc_name']; ?>
                                                                            </a>
                                                                            <?php
                                                                        }
                                                                        $docName = $rwDoc['doc_name'];
                                                                        $docName = explode("_", $docName);
                                                                        $updateDocName = $docName[0] . '_' . $rwDoc['doc_id'] . ((!empty($docName[1])) ? '_' . $docName[1] : '');
                                                                        $fileVersion = mysqli_query($db_con, "SELECT * FROM `tbl_document_reviewer` WHERE doc_name='$updateDocName' ") or die('Error:' . mysqli_error($db_con));
                                                                        while ($rwfileVersion = mysqli_fetch_assoc($fileVersion)) {
                                                                            ?>
                                                                            <div>

                                                                                <?php if($rwfileVersion['storage_doc_id']=="" &&  file_exists('thumbnail/review/'.base64_encode($rwfileVersion['doc_id']).'.jpg')){ ?>
                                                                            <div> <img class="thumb-image" src="thumbnail/review/<?=base64_encode($rwfileVersion['doc_id'])?>.jpg"> </div>
                                                                        <?php } ?>

                                                                         <?php if(file_exists('thumbnail/'.base64_encode($rwfileVersion['storage_doc_id']).'.jpg')){ ?>
                                                                            <div> <img class="thumb-image" src="thumbnail/<?=base64_encode($rwfileVersion['storage_doc_id'])?>.jpg"> </div>
                                                                        <?php } ?>

                                                                                <?php
                                                                                //versioning view start here
                                                                                if ((strtolower($rwfileVersion['doc_extn']) == 'pdf') && $rwgetRole['pdf_file'] == '1') {
                                                                                    ?>
                                                                                    <a title="view or print" href="viewer?id=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])) ?>&i=<?php echo urlencode(base64_encode($rwfileVersion['doc_id'])); ?>&pn=1&chk=rw" class="pdfview" target="_blank">
                                                                                        <?php echo $rwfileVersion['old_doc_name']; ?> <i class="fa fa fa-eye"></i></a>
                                                                                        <?php
                                                                                    } else if ((strtolower($rwfileVersion['doc_extn']) == 'gif' || strtolower($rwfileVersion['doc_extn']) == 'jpeg' || strtolower($rwfileVersion['doc_extn']) == 'jpg' || strtolower($rwfileVersion['doc_extn']) == 'png' || strtolower($rwfileVersion['doc_extn']) == 'bmp') && $rwgetRole['image_file'] == '1') {
                                                                                        ?> 
                                                                                    <a href="imageviewer?uid=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&i=<?php echo urlencode(base64_encode($rwfileVersion['doc_id'])); ?>&chk=rw" target="_blank">
                                                                                        <?php echo $rwfileVersion['old_doc_name']; ?> <i class="fa fa-picture-o"></i></a> 
                                                                                    <!--viewer for version tiff start-->
                                                                                <?php } else if ((strtolower($rwfileVersion['doc_extn']) == 'tif' || strtolower($rwfileVersion['doc_extn']) == 'tiff') && $rwgetRole['tif_file'] == '1') { ?>
                                                                                    <a href="tiff-viewer?id=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&i=<?php echo urlencode(base64_encode($rwfileVersion['doc_id'])); ?>&chk=rw" target="_blank">
                                                                                        <?php echo $rwfileVersion['old_doc_name']; ?> <i class="fa fa-picture-o"></i></a>


                                                                                    <!--viewer for excel versioning-->
                                                                                <?php } else if (strtolower($rwfileVersion['doc_extn']) == 'xlsx') {
                                                                                    ?>
                                                                                    <a href="xlsx?uid=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&file=<?php echo urlencode(base64_encode($rwfileVersion['doc_id'])); ?>&chk=rw" target="_blank">
                                                                                        <?php if ($rwgetRole['excel_file'] == '1') { ?>
                                                                                            <?php echo substr($rwfileVersion['old_doc_name'], strpos($rwDoc['old_doc_name']) + 0); ?> <i class="fa fa-file-excel-o"></i></a>
                                                                                        <?php } ?>

                                                                                <?php } else if (strtolower($rwfileVersion['doc_extn']) == 'xls') {
                                                                                    ?>
                                                                                    <a href="xls?uid=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&file=<?php echo urlencode(base64_encode($rwfileVersion['doc_id'])); ?>&chk=rw" target="_blank">
                                                                                        <?php if ($rwgetRole['excel_file'] == '1') { ?>
                                                                                            <?php echo substr($rwfileVersion['old_doc_name'], strpos($rwfileVersion['old_doc_name']) + 0); ?> <i class="fa fa-file-excel-o"></i></a>
                                                                                        <?php } ?>
                                                                                    <!--viewer for excel versioning ends -->

                                                                                    <!-- doc version viewer-->
                                                                                <?php } else if ((strtolower($rwfileVersion['doc_extn']) == 'doc' || strtolower($rwfileVersion['doc_extn']) == 'docx' || strtolower($rwfileVersion['doc_extn']) == 'rtf' || strtolower($rwfileVersion['doc_extn']) == 'odt') && $rwgetRole['word_edit'] == '1') { ?>
                                                                                    <a href="viewword?i=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&id=<?php echo urlencode(base64_encode($rwfileVersion['doc_id'])); ?>&chk=rw&reid=<?php echo urlencode(base64_encode($rwTaskDoc['id'])) ?>" target="_blank">
                                                                                        <?php echo $rwfileVersion['old_doc_name']; ?> <i class="fa fa-file-word-o"></i></a>

                                                                                    <!-- psd version viewer-->
                                                                                <?php } else if (strtolower($rwfileVersion['doc_extn']) == 'psd') { ?>
                                                                                    <?php if ($rwgetRole['view_psd'] == '1') { ?>
                                                                                        <a href="viewword?i=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&id=<?php echo urlencode(base64_encode($rwfileVersion['doc_id'])); ?>&chk=rw&reid=<?php echo urlencode(base64_encode($rwTaskDoc['id'])) ?>" target="_blank">
                                                                                            <?php echo $rwfileVersion['old_doc_name']; ?> <img src="<?= BASE_URL ?>assets/images/psd.png"></a>
                                                                                    <?php } ?>

                                                                                    <!-- CDR version viewer-->
                                                                                <?php } else if (strtolower($rwfileVersion['doc_extn']) == 'cdr') { ?>
                                                                                    <?php if ($rwgetRole['view_cdr'] == '1') { ?>
                                                                                        <a href="viewword?i=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&id=<?php echo urlencode(base64_encode($rwfileVersion['doc_id'])); ?>&chk=rw&reid=<?php echo urlencode(base64_encode($rwTaskDoc['id'])) ?>" target="_blank">
                                                                                            <?php echo $rwfileVersion['old_doc_name']; ?> <img src="<?= BASE_URL ?>assets/images/cdr.png"></a>
                                                                                    <?php } ?>
                                                                                    <!--for audio/video viewer version-->
                                                                                <?php } else if (strtolower($rwfileVersion['doc_extn']) == 'mp3') { ?>
                                                                                    <a href="audioplayer?id=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&id=<?php echo urlencode(base64_encode($rwfileVersion['doc_id'])); ?>" target="_blank"> <?php echo $rwfileVersion['old_doc_name']; ?> <i class="fa fa-music"></i></a>

                                                                                <?php } else if (strtolower($rwfileVersion['doc_extn']) == 'mp4') { ?>
                                                                                    <a href="video-player?id=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&id=<?php echo urlencode(base64_encode($rwfileVersion['doc_id'])); ?>" target="_blank" > <?php echo $rwfileVersion['old_doc_name']; ?> <i class="fa fa-video-camera"></i></a>
                                                                                <?php } else {
                                                                                    ?>
                                                                                    <a href="downloaddoc?file=<?php echo urlencode(base64_encode($rwfileVersion['doc_id'])) ?>&chk=rw" id="fancybox-inner" target="_blank"> <?php echo $rwfileVersion['old_doc_name']; ?> <i class="fa fa-download"></i></a>
                                                                                    <?php
                                                                                }
                                                                                ?>
                                                                            </div>
                                                                            <?php
                                                                        }
                                                                    } else {
                                                                        echo $allot_row['task_remarks'];
                                                                    }
                                                                    ?>
                                                                </td>
                                                                <?php
                                                                if ($rwTaskDoc['task_status'] == 'Aborted') {
                                                                    echo '<td><span class="label label-danger fnt">' . $rwTaskDoc['task_status'] . '</span></td> ';
                                                                } else if ($rwTaskDoc['task_status'] == 'Pending') {
                                                                    echo '<td><span class="label label-warning fnt">' . $rwTaskDoc['task_status'] . '</span></td> ';
                                                                } else {
                                                                    echo '<td><span class="label label-success fnt">' . $rwTaskDoc['task_status'] . '</span></td> ';
                                                                }
                                                                ?>
                                                                <td><?php echo date('d-m-Y h:i:s', strtotime($rwTaskDoc['start_date'])); ?></td>
                                                                <td><?php
                                                                    if (!empty($fnumber)) {
                                                                        echo '<span class="label label-primary fnt">' . $fnumber . '</span>';
                                                                    } else {
                                                                        echo '<span class="label label-danger fnt">' . $lang['No_File_Number'] . '</span>';
                                                                    }
                                                                    ?></td>

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
                                                                echo " <li><a href='?start=$prev&review_doc=" . $searchTxt. "&limit=" . $per_page . "&startDate=".$startDate."&endDate=".$endDate."&user=".$uid."'>$lang[Prev]</a> </li>";
                                                            else
                                                                echo " <li class='disabled'><a href='javascript:void(0)'>$lang[Prev]</a> </li>";
                                                            //pages 
                                                            if ($max_pages < 7 + ($adjacents * 2)) {   //not enough pages to bother breaking it up
                                                                $i = 0;
                                                                for ($counter = 1; $counter <= $max_pages; $counter++) {
                                                                    if ($i == $start) {
                                                                        echo "<li class='active'><a href='?start=$i&limit=$per_page&review_doc=" . $searchTxt . "&startDate=".$startDate."&endDate=".$endDate."&user=".$uid."'><b>$counter</b></a> </li>";
                                                                    } else {
                                                                        echo "<li><a href='?start=$i&limit=$per_page&review_doc=" . $searchTxt . "&startDate=".$startDate."&endDate=".$endDate."&user=".$uid."'>$counter</a></li> ";
                                                                    }
                                                                    $i = $i + $per_page;
                                                                }
                                                            } elseif ($max_pages > 5 + ($adjacents * 2)) {    //enough pages to hide some
                                                                //close to beginning; only hide later pages
                                                                if (($start / $per_page) < 1 + ($adjacents * 2)) {
                                                                    $i = 0;
                                                                    for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                                                                        if ($i == $start) {
                                                                            echo " <li class='active'><a href='?start=$i&limit=$per_page&review_doc=" . $searchTxt . "&startDate=".$startDate."&endDate=".$endDate."&user=".$uid."'><b>$counter</b></a></li> ";
                                                                        } else {
                                                                            echo "<li> <a href='?start=$i&limit=$per_page&review_doc=" . $searchTxt . "&startDate=".$startDate."&endDate=".$endDate."&user=".$uid."'>$counter</a> </li>";
                                                                        }
                                                                        $i = $i + $per_page;
                                                                    }
                                                                }
                                                                //in middle; hide some front and some back
                                                                elseif ($max_pages - ($adjacents * 2) > ($start / $per_page) && ($start / $per_page) > ($adjacents * 2)) {
                                                                    echo " <li><a href='?start=0&limit=$per_page&review_doc=" . $searchTxt. "&startDate=".$startDate."&endDate=".$endDate."&user=".$uid."'>1</a></li> ";
                                                                    echo "<li><a href='?start=$per_page&limit=$per_page&review_doc=" . $searchTxt . "&startDate=".$startDate."&endDate=".$endDate."&user=".$uid."'>2</a></li>";
                                                                    echo "<li><a href='javascript:void(0)'>...</a></li>";

                                                                    $i = $start;
                                                                    for ($counter = ($start / $per_page) + 1; $counter < ($start / $per_page) + $adjacents + 2; $counter++) {
                                                                        if ($i == $start) {
                                                                            echo " <li class='active'><a href='?start=$i&limit=$per_page&review_doc=" . $searchTxt . "&startDate=".$startDate."&endDate=".$endDate."&user=".$uid."'><b>$counter</b></a></li> ";
                                                                        } else {
                                                                            echo " <li><a href='?start=$i&limit=$per_page&review_doc=" . $searchTxt . "&startDate=".$startDate."&endDate=".$endDate."&user=".$uid."'>$counter</a> </li>";
                                                                        }
                                                                        $i = $i + $per_page;
                                                                    }
                                                                }
                                                                //close to end; only hide early pages
                                                                else {
                                                                    echo "<li> <a href='?start=0&limit=$per_page&review_doc=" . $searchTxt . "&startDate=".$startDate."&endDate=".$endDate."&user=".$uid."'>1</a> </li>";
                                                                    echo "<li><a href='?start=$per_page&limit=$per_page&review_doc=" . $searchTxt . "&startDate=".$startDate."&endDate=".$endDate."&user=".$uid."'>2</a></li>";
                                                                    echo "<li><a href='javascript:void(0)'>...</a></li>";

                                                                    $i = $start;
                                                                    for ($counter = ($start / $per_page) + 1; $counter <= $max_pages; $counter++) {
                                                                        if ($i == $start) {
                                                                            echo " <li class='active'><a href='?start=$i&limit=$per_page&review_doc=" . $searchTxt . "'><b>$counter</b></a></li> ";
                                                                        } else {
                                                                            echo "<li> <a href='?start=$i&limit=$per_page&review_doc=" . $searchTxt . "'>$counter</a></li> ";
                                                                        }
                                                                        $i = $i + $per_page;
                                                                    }
                                                                }
                                                            }
                                                            //next button
                                                            if (!($start >= $foundnum - $per_page))
                                                                echo "<li><a href='?start=$next&review_doc=" . $searchTxt . "&limit=" . $per_page . "&startDate=".$startDate."&endDate=".$endDate."&user=".$uid."'>$lang[Next]</a></li>";
                                                            else
                                                                echo "<li class='disabled'><a href='javascript:void(0)'>$lang[Next]</a></li>";
                                                            ?>
                                                    </ul>

                                                    <?php
                                                }
                                                echo "</center>";
                                            }
                                        } else {
                                            ?>
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th><?php echo $lang['SNO']; ?></th>
                                                        <th><?php echo $lang['Ticket_Id']; ?></th>
                                                        <th><?php echo $lang['File_Name']; ?></th>
                                                        <th><?php echo $lang['ReviewStatus']; ?></th>
                                                        <th><?php echo $lang['Ticket_Date_Time']; ?></th>
                                                        <th><?php echo $lang['File_Number']; ?></th>

                                                    </tr>
                                                </thead>
                                                <?php
                                                if (isset($_GET['review_doc']) && !empty($_GET['review_doc'])) {
                                                    echo '<tr><td colspan="6"><label style="font-weight:600; color:red; margin-left: 440px;"> <strong>' . $lang['no_serch_result'] . '</strong></label></td></tr>';
                                                } else {
                                                    echo '<tr><td colspan="6"><label style="font-weight:600; color:red; margin-left: 440px;">' . $lang['No_Review_Fles'] . '</label></td></tr>';
                                                }
                                            }
                                            ?>
                                        </table>
                                    </div>
                                </div>
                                <!-- end: page -->
                            </div> <!-- end Panel -->
                        </div> <!-- container -->

                    </div> <!-- content -->

                    <?php require_once './application/pages/footer.php'; ?>

                </div>
                <!-- ============================================================== -->
                <!-- End Right content here -->
                <!-- ============================================================== -->
                <!-- Right Sidebar -->
                <?php require_once './application/pages/rightSidebar.php'; ?>
                <!-- /Right-bar -->
                <!-- MODAL -->
                <div id="dialog" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog"> 
                        <div class="panel panel-color panel-danger"> 
                            <div class="panel-heading"> 
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                                <label><h2 class="panel-title"><?php echo $lang['Are_u_confirm'] ?></h2></label> 
                            </div> 
                            <form method="post">
                                <div class="panel-body">
                                    <p style="color: red;"><?php echo $lang['Are_you_sure_that_you_want_to_Undo_shared_Documents'] ?></p>
                                </div>
                                <div class="modal-footer">
                                    <div class="col-md-12 text-right">
                                        <input type="hidden" id="undo" name="undo">
                                        <button type="submit" name="undoshare" id="dialogConfirm" class="btn btn-danger waves-effect waves-light"><?php echo $lang['confirm'] ?></button>
                                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $lang['Close'] ?></button> 
                                    </div>
                                </div>
                            </form>
                        </div> 
                    </div>
                </div>
                <!-- end Modal -->  
            </div>
            <!-- END wrapper -->

            <?php require_once './application/pages/footerForjs.php'; ?>
            <script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
            <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
            <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
            <script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
            <script src="assets/plugins/moment/moment.js"></script>
            <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>  
            <script src="assets/plugins/timepicker/bootstrap-timepicker.js"></script>
            <script src="assets/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
            <script src="assets/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
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
                                        $(document).ready(function () {
                                            $("#shareddoc").click(function () {
                                                var $shared = $("#review_doc").val();
                                                var strtdate = $("#strtdate").val();
                                                var enddate = $("#enddate").val();
                                                var users = $("#users").val();
                                                url = removeParam("start", url);
                                                url = removeParam("review_doc", url);
                                                url = removeParam("startDate", url);
                                                url = removeParam("endDate", url);
                                                url = removeParam("user", url);
                                                if ($shared != '') {
                                                    url = url + "&review_doc=" + $shared;
                                                }
                                                if (strtdate != "" && enddate != "")
                                                {
                                                    url = url + "&startDate=" + strtdate + "&endDate=" + enddate;
                                                }
                                                if (users != '' && users != null) {
                                                    url = url + "&user=" + users;
                                                }
                                                window.open(url, "_parent");
                                            })

                                        })
                                        $('.select2').select2();
                                        jQuery('#date-range').datepicker({
                                            toggleActive: true
                                        });

            </script>
            <script type="text/javascript">

                $(document).ready(function () {
                    $('form').parsley();
                });
            </script>


            <!-- for audio model-->
            <div id="modal-audio" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h4 class="modal-title" id="myModalLabel"><?php echo $lang['Play_Audio'] ?></h4>
                        </div>
                        <div id="foraudio">

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close'] ?></button>

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
                            <h4 class="modal-title" id="myModalLabel"><?php echo $lang['Play_video'] ?></h4>
                        </div>
                        <div  id="videofor">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close'] ?></button>

                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
            <script>
                //for undo share
                $("a#undoShare").click(function () {
                    var id = $(this).attr('data');
                    // alert(id);
                    $("#undo").val(id);
                });
            </script>
    </body>
</html>