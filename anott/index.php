<!DOCTYPE html>
<?php
// error_reporting(0);
// ob_start();
// session_start();
require_once '../sessionstart.php';
require_once '../application/config/database.php';
require_once('fpdf-function.php');
require_once '../application/pages/sendSms.php';
require_once '../application/pages/function.php';

require_once '../classes/fileManager.php';

// error_reporting(0);

$pgn = intval($_GET['pn']);
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../logout.php");
}

//  require_once '../application/pages/head.php';
//for user role
$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

$rwgetRole = mysqli_fetch_assoc($chekUsr);

$langDetail = mysqli_query($db_con, "select * from tbl_language where lang_name='" . $_SESSION['lang'] . "'") or die('Error : ' . mysqli_error($db_con));
$langDetail = mysqli_fetch_assoc($langDetail);

$uid = base64_decode(urldecode($_GET['id']));
if ($uid != $_SESSION['cdes_user_id']) {
    //header('Location:../index');
}
if ($rwgetRole['pdf_annotation'] != '1') {
    //header('Location: ../index');
}
$id1 = base64_decode(urldecode($_GET['id1'])); //doc_id

$id = base64_decode(urldecode($_GET['id'])); //doc asign id


$tid = base64_decode(urldecode($_GET['tid'])); //task id

$tid = base64_decode(urldecode($_GET['tid'])); //task id

$chk = strtolower(filter_var($_GET['chk'], FILTER_SANITIZE_STRING)); // check for review
$reid = base64_decode(urldecode($_GET['reid'])); //Review Id

mysqli_set_charset($db_con, "utf8");
//sk@71218: set table name dynamically for review process.
if ($chk == 'rw') {
// annotation condition
    $annot_cond = " and is_inreview='1'";
// Review log condition
    $rwlog_cond = " and in_review='0'";
    $chk_review = '&chk=rw&reid=' . urlencode(base64_encode($reid));
    $file = mysqli_query($db_con, "select doc_name,doc_path,doc_extn,old_doc_name from tbl_document_reviewer where doc_id='$id1'") or die('error' . mysqli_error($db_con));
} else {
// Annotation Condition    
    $annot_cond = " and is_inreview='0'";
// Review log condition
    $rwlog_cond = " and in_review='1'";
    $file = mysqli_query($db_con, "select doc_name,filename,doc_path,doc_extn,old_doc_name from tbl_document_master where doc_id='$id1'") or die('error' . mysqli_error($db_con));
}

$rwFile = mysqli_fetch_assoc($file);
$fileName = $rwFile['old_doc_name'];
$filePath = $rwFile['doc_path'];
$slid = $rwFile['doc_name'];
$doc_extn = $rwFile['doc_extn'];
// for review
$File_Number = isset($rwFile['File_Number']) ? $rwFile['File_Number'] : '';

//echo "select * from tbl_doc_assigned_wf where doc_id='$id1' and (task_status='Pending' or task_status='Approved') ";
$user = mysqli_query($db_con, "select * from tbl_user_master where user_id='$_SESSION[cdes_user_id]'");
$rwUser = mysqli_fetch_assoc($user);
$userSign = $rwUser['user_sign'];
$signpath = "../$userSign";


 $fileManager = new fileManager();
// Connect to file server
$fileManager->conntFileServer();
$localPath = $fileManager->getFile($rwFile, '../');
$folderName = substr($localPath, 0, strrpos($localPath, "/"));
$server_path = ROOT_FTP_FOLDER . '/' . $filePath;


//echo $localPath;
//die;

/*
 * file download end
 */

//sk@10918 restrict pagination beyond last page.
if (file_exists($localPath)) {
    if ($chk == 'rw') {
        //sk@71218: make review file copy and prepare for review
        $localPath = reviewFileCopy($localPath, $id1, $lang);
        //echo $localPath;
        //die;
    }
    if (isset($_POST['add_pageno'])) {
        // Set Page No.
        setPageNo($localPath, $did);
    }
//@sk(311018): Clean temprary given folder. And print function output to check for errors.    
    cleanFolder('../temp/fpdf-temp/');


    if ($pgn > totalfpages($localPath)) {
        $tp = totalfpages($localPath);
        if ($chk == 'rw') {
            header('location:index.php?id=' . base64_encode(urlencode($id)) . '&id1=' . base64_encode(urlencode($id1)) . '&pn=' . $tp . $chk_review);
        } else {
            header('location:index.php?id=' . base64_encode(urlencode($id)) . '&id1=' . base64_encode(urlencode($id1)) . '&tid=' . base64_encode(urlencode($tid)) . '&pn=' . $tp);
        }
    }
} else {
    if ($chk == 'rw') {
        header('location:../reviewintray');
    } else {
        //header('location:../myTask');
    }
//echo '<script>taskFailed("../myTask","Target File does not exist.")</script>';    
}




// for showing group wise  user
$sameGroupIDs = array();
$group = mysqli_query($db_con, "select * from tbl_bridge_grp_to_um where find_in_set('$_SESSION[cdes_user_id]',user_ids)") or die('Error' . mysqli_error($db_con));
while ($rwGroup = mysqli_fetch_assoc($group)) {
    $sameGroupIDs[] = $rwGroup['user_ids'];
}
$sameGroupIDs = array_unique($sameGroupIDs);
sort($sameGroupIDs);
$sameGroupIDs = implode(',', $sameGroupIDs);


$user_id = $_SESSION['cdes_user_id'];
$tid = base64_decode(urldecode($_GET['tid']));

$task = mysqli_query($db_con, "select * from tbl_doc_assigned_wf where id='$tid' and (task_status='Pending' or task_status='Approved') ");
$rwTask = mysqli_fetch_assoc($task);


if ($_SESSION['cdes_user_id'] != '1') {
    $work = mysqli_query($db_con, "select * from tbl_task_master where task_id='$rwTask[task_id]' and (assign_user = '$_SESSION[cdes_user_id]' or alternate_user='$_SESSION[cdes_user_id]' or supervisor='$_SESSION[cdes_user_id]')");
    if (mysqli_num_rows($work) > 0) {
        $rwWork = mysqli_fetch_assoc($work);
        $ltaskName = $rwWork['task_name'];
    } else {
        // header("Location:../index");
    }
} else {
   
    $work = mysqli_query($db_con, "select * from tbl_task_master where task_id='$rwTask[task_id]'");
    if (mysqli_num_rows($work) > 0) {
        $rwWork = mysqli_fetch_assoc($work);
    } else {
        //  header("Location:../index");
    }
}

$assignBy = $rwTask['assign_by'];
$docID = $rwTask['doc_id'];
$ctaskID = $rwWork['task_id'];
$ctaskOrder = $rwWork['task_order'];
$stepId = $rwWork['step_id'];
$wfid = $rwWork['workflow_id'];
$ticket = $rwTask['ticket_id'];
$taskRemark = mysqli_real_escape_string($db_con, $rwTask['task_remarks']); // die();
?>
<?php
if (isset($_SESSION['lang'])) {
    $file = "../" . $_SESSION['lang'] . ".json";
} else {
    $file = "../English.json";
}

$jsFile = file_get_contents($file);
$lang = json_decode($jsFile, true);

//print_r($rwgetRole);
//die;
?>

<html>
    <head>
        <!--script src="//mozilla.github.io/pdf.js/build/pdf.js"></script--> <!--pdf viewer-->
        <script type="text/javascript" src="pdf.js"></script>
        <!--script type="text/javascript" src="http://code.jquery.com/jquery.min.js"></script> -->
        <!--script type="text/javascript" src="jquery.min.js"></script-->
        <link rel="shortcut icon" href="../assets/images/favicon_1.ico">
        <!--  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" /> -->
        <link href="../assets/css/icons.css" rel="stylesheet" type="text/css" />
        <link href="../assets/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/css/components.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/css/core.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/plugins/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css">
        <link href="toolbar.css" rel="stylesheet" type="text/css"/>
        <script src="CanvasInput.js" type="text/javascript"></script>
        <script src="../assets/js/jquery.min.js" type="text/javascript"></script>
        <script src="../assets/js/bootstrap.min.js" type="text/javascript"></script>
        <link href="../assets/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" />   
        <script src="../assets/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
        <script src="../assets/js/jquery.nicescroll.js"></script>
        <script>
            document.title = "<?php echo $fileName; ?>";
        </script>
        <style type="text/css">

            #the-canvas {
                border:1px solid black;
            }

            body {
                background-color: #eee;
                font-family: sans-serif;
                margin: 0;
            }
            #comment-wrapper2{
                position: fixed;
                right: 0;
                top: 40px;
                bottom: 0;
                overflow: auto;
                width: 268px;
                background-color: #eaeaea;
                border-left: 1px solid #d0d0d0; 
            }
            #comment-wrapper h4 {
                margin: 10px;
            }
            #comment-wrapper {
                position: fixed;
                left: 0%;
                top: 45px;
                right: 0;
                bottom: 0;
                overflow: auto;
                width: 280px;
                background: rgb(11, 175, 32);;
                border-left: 1px solid #d0d0d0;
            }
            #comment-wrapper h4 {
                margin: 10px;
            }
            #comment-wrapper .comment-list {
                font-size: 12px;
                position: absolute;
                top: 38px;
                left: 0;
                right: 0;
                bottom: 0;

            }
            .ctext-wrap i {
                float: right;
            }
            .comment-list-item li i {
                float: right;
                margin-left: 6px;

            }
            #comment-wrapper .comment-list-item {
                border-bottom: 1px solid #d0d0d0;
                padding: 10px;
                color:#ffffff;
                list-style-type: none;
            }
            #comment-wrapper .comment-list-container {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 70px;
                overflow: auto;
            }
            #comment-wrapper .comment-list-form {
                position: absolute;
                left: 0;
                right: 0;
                bottom: 0;
                padding: 10px;
            }
            #comment-wrapper .comment-list-form input {
                padding: 5px;
                width: 100%;
            }
            #comment-wrapper .comment-list-form1 {
                position: absolute;
                left: 0;
                right: 0;
                bottom: 0;
                padding: 10px;
            }
            #comment-wrapper .comment-list-form1 input {
                padding: 5px;
                width: 100%;
            }

        </style>
    </head>
    <body>
        <h1></h1>

        <div class="toolbar">
            <div class="row">

                <div class="col-sm-6">
                    <button data-toggle="modal" data-target="#con-close-modal-insert" class="btn btn-primary btn-sm add m-l-5" id="insert_file" style="margin-top:4px;" title="<?php echo $lang['addnewpage']; ?>"><i class="fa fa-plus"></i>  <?php echo $lang['Add']; ?>
                    </button>
                    <?php if ($chk == 'rw'): ?>
                        <form method="post" style="display: inline-block;">
                            <button type="submit" class="btn btn-primary btn-sm add m-l-5" title="<?= $lang['Submit_Review'] ?>" name="submit_review" id="submit_review"> <?= $lang['Submit_Review'] ?> </button></form>
                    <?php endif; ?>

                    <div class="pull-left">
                        <form method="post">
                            <?php if ($rwgetRole['delete_page'] == '1') { ?>
                                <button type="button" data-target="#con-close-modal-del" title="<?= $lang['Delete'] ?>" data-toggle="modal" name="del-btn"  value="Delete"  class="btn btn-danger btn-sm del m-l-5" id="del-btn" style="margin-top:4px;" ><i class="fa fa-trash-o"></i> <?= $lang['Delete']; ?>
                                </button>
                            <?php } ?>
                            <?php if ($chk != 'rw'): ?>
                                <?php if (add_page_no): ?>
                                    <button type="submit" class="btn btn-primary btn-sm add m-l-5" title="<?= $lang['add_page_no'] ?>" name="add_pageno" id="add_pageno" onclick="return pgnConfirm()"> <?= $lang['add_page_no'] ?> </button> 
                                    <?php
                                endif;
                            endif;
                            ?>
                        </form>
                    </div>
                    <?php if ($chk != 'rw'): ?>
                        <?php if (isset($rwTask['letter_type']) && $rwTask['letter_type'] !== '' && $rwTask['letter_type'] !== 'Approval') { ?>
                            <button class="btn btn-primary btn-bloc btn-sm m-l-5 m-t-5" disabled> <?php echo $lang['Aprv_Rjct_Tsk']; ?></button>

                        <?php } else{?>  
                            <button class="btn btn-primary btn-bloc btn-sm m-l-5 m-t-5" data-toggle="modal" data-target="#con-close-modal-act"><?php echo $lang['Aprv_Rjct_Tsk']; ?></button>
                        <?php } ?>
                        <button style="" class="btn btn-primary btn-bloc btn-sm m-l-5 m-t-5" id="comment_button"><?php echo $lang['Comment']; ?></button>
                    <?php endif; ?>
                </div>
                <div class="col-sm-1">
                    <a href="index.php?file=<?php echo $_GET['file']; ?>&id=<?php echo urlencode($_GET['id']); ?>&id1=<?php echo urlencode($_GET['id1']); ?>&tid=<?= base64_encode(urlencode($tid)) ?>&pn=<?php
                    echo (isset($_GET['pn']) && !empty($_GET['pn'])) ? ($_GET['pn'] - 1) : 1;
                    ?><?= $chk_review ?>" id="prev"><?php if (intval($_GET['pn']) != 1) { ?><i class="fa fa-long-arrow-left" aria-hidden="true"></i><?php } ?></a>
                    <span id="page_num"><?php echo (isset($_GET['pn']) && !empty($_GET['pn'])) ? $_GET['pn'] : 1; ?></span> / <span id="page_count"></span>
                    <a href="index.php?file=<?php echo $_GET['file']; ?>&id=<?php echo urlencode($_GET['id']); ?>&id1=<?php echo urlencode($_GET['id1']); ?>&tid=<?= base64_encode(urlencode($tid)) ?>&pn=<?php
                    echo (isset($_GET['pn']) && !empty($_GET['pn'])) ? ($_GET['pn'] + 1) : 1 + 1;
                    ?><?= $chk_review ?>" id="next"><?php if (totalfpages($localPath) != intval($_GET['pn'])) { ?>
                            <i class="fa fa-long-arrow-right" aria-hidden="true"></i><?php } ?></a>

                </div>
                <div class="col-sm-5 m-t-20">
                    <?php if ($rwgetRole['file_anot'] == '1') { ?>
                        <div class="button-anot">
                            <!--button id="save">Save</button-->
                            <button data-toggle="modal" data-target="#con-close-modal2" class="btn btn-primary" id="save" style="margin-bottom: 14px;"><?= $lang['Save']; ?></button>

                            <!-- Eraser
                            <button id="eraser" class="" title="Eraser" data="eraser"><img src="../assets/images/eraser.png" alt="" height="20px" width="30px"></button> 
                            -->

                            <button id="highlight" class="highlight" title="Highlight" data="highlight"></button>

                            <button id="rectangle" class="rectangle" title="Rectangle" data="rectangle"></button>

                            <button id="circle" class="circle" title="Circle" data="circle"></button>
                            <button id="strikeout" class="strikeout"  title="Strikeout" data="strikeout"></button>

                            <button id="text" class="text" title="Text Tool" data="text"></button>
                            <button id="approved" class="approved" title="Approved Stamp" data="approved"><img src="../assets/images/approved.jpg" alt="approved" height="20px" width="70px" id="approveImg"></button>
                            <button id="reject" class="reject" title="Reject Stamp" data="reject"><img src="../assets/images/reject.jpg" alt="reject" height="20px" width="70px" id="rejectImg"></button>
                            <?php
                            if (file_exists($signpath) && !empty($userSign)) {
                                ?>
                                <button id="signature" class="signature" title="Signature Stamp" data="signature"><img src="../<?php echo $userSign; ?>" alt="Signature" height="20px" width="70px" id="signImg"></button>
                            <?php } else {
                                ?>
                                <button data-toggle="modal" data-target="#addsignature" class="btn btn-primary btn-xs text-center m-b-10 m-t-10 font-12"><?php echo $lang['Add_Sign']; ?></button>
                            <?php }
                            ?>   <?php if ($rwgetRole['wf_log'] == '1' || $rwgetRole['review_log'] == '1') { ?>
                                <button id="log" class="btn btn-primary" title="<?php echo $lang['log']; ?>" style="margin-bottom: 7px; padding: 0px 15px;"><?= $lang['log']; ?></button>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>

            </div>

        </div>
        <div class="viewer-wrapper">
            <canvas id="the-canvas"></canvas>
            <canvas id="canvas2"></canvas>
        </div>       

        <div id="comment-wrapper" style="display: none;">
            <h4>Comments</h4>
            <div class="comment-list">
                <div class="comment-list-container">
                    <!--div class="comment-list-item"-->
                    <div id="comentAdd">
                        <?php
                        $getTiketid = mysqli_query($db_con, "select  ticket_id from tbl_doc_assigned_wf where doc_id='$id1' order by id desc") or die('Error: ' . mysqli_error($db_con));
                        $rwgetTiketid = mysqli_fetch_assoc($getTiketid);
                        //get workflow name
                        $getWfId = mysqli_query($db_con, "select ttm.workflow_id from tbl_doc_assigned_wf daw inner join tbl_task_master ttm on daw.task_id = ttm.task_id where daw.ticket_id='$rwgetTiketid[ticket_id]'");
                        $rwgetWfId = mysqli_fetch_assoc($getWfId);

                        $getWfName = mysqli_query($db_con, "select workflow_name from tbl_workflow_master where workflow_id='$rwgetWfId[workflow_id]'");
                        $rwgetWfName = mysqli_fetch_assoc($getWfName);
                        $proclist = mysqli_query($db_con, "select * from tbl_doc_assigned_wf where ticket_id='$rwgetTiketid[ticket_id]'");

                        $rwProclist = mysqli_fetch_assoc($proclist);

                        $comment = mysqli_query($db_con, "select * from tbl_task_comment where tickt_id= '$rwProclist[ticket_id]' order by comment_time desc");
                        if (mysqli_num_rows($comment) > 0) {
                            while ($rwcomment = mysqli_fetch_assoc($comment)) {

                                $usr = mysqli_query($db_con, "select first_name, last_name,profile_picture from tbl_user_master where user_id='$rwcomment[user_id]'");
                                $rwUsr = mysqli_fetch_assoc($usr);
                                $ext = pathinfo($rwcomment['comment'], PATHINFO_EXTENSION);
                                // echo( $ext)."<br>";
                                ?>
                                <div class="chat-conversation">
                                    <div class="comment-list-item"> 
                                        <ul class="conversation-list nicescroll anotecoment" style="height: Auto;">
                                            <li class="clearfix">
                                                <div class="chat-avatar">
                                                    <?php if (!empty($rwUsr['profile_picture'])) { ?>
                                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($rwUsr['profile_picture']); ?>" alt="Image">
                                                    <?php } else { ?>
                                                        <img src="<?= BASE_URL ?>assets/images/avatar.png" alt="Image">
                                                    <?php } ?>

                                                </div>
                                                <div class="conversation-text">

                                                    <div class="ctext-wrap">
                                                        <span><?php
                                                            echo '<strong>' . $rwUsr['first_name'] . ' ' . $rwUsr['last_name'] . '</strong>' . '<br>';
                                                            if (!empty($rwcomment['comment'])) {

                                                                if ($ext) {
                                                                    ?>
                                                                    <a href="view?cid=<?= urlencode(base64_encode($rwcomment['id'])) ?>" target="_blank"><i class="fa fa-file cmt-file"></i></a><?php
                                                                } 
															
                                                                ?>
                                                                <br/>

                                                                <?php
                                                           }
															if(!empty($rwcomment['comment_desc'])){
																	echo $rwcomment['comment_desc'];
																}
																?>
																<br/>
																<?php 
                                                            if (!empty($rwcomment['task_status'])) {
                                                                echo '<strong>Action: </strong>' . $rwcomment['task_status'] . '<br>';
                                                            }
                                                            ?> </span> 
                                                        <div class="clearfix"></div>
                                                        <span>
                                                            <?php echo date("j F, Y, H:i", strtotime($rwcomment['comment_time'])); ?></span>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            ?>
                            <div class="comment-list-item">No comments</div>
                        <?php } ?>
                    </div>
                    <!--/div-->
                </div>
                <?php if ($rwgetRole['file_coment'] == '1' && (isset($rwTask['ticket_id']) && isset($rwTask['task_id']) )) { ?>
                    <div class="row" >

                        <div class="comment-list-form1">
                            <!--                                 <form method="post" enctype="multipart/form-data" id="pdf_comment_form">
                                                            <textarea type="text" cols="40" rows="3" placeholder="Add a Comment" name="comment" id="coment"/></textarea>
                                                            <input type="hidden" name="cfp" id="cfp" value="<?= $cmt_file_path ?>">
                                                            <input type="hidden" name="tktid"  value="<?php echo $rwTask['ticket_id']; ?>" id="tktid"/>
                                                            <input type="hidden" name="tskid"  value="<?php echo $rwTask['task_id'] ?>" id="tskid"/>
                                                            <input type="file" name="comment_file" id="comment_file">
                            
                                                            <input type="submit" value="Submit" class="button-all-f btn-sml-txt" onclick="return postPdfComment(event)"  id="comment_btn">
                                                            </form>-->
                            <button data-toggle="modal" data-target="#con-close-modal-comment" class="btn btn-primary btn-sm add m-l-5" id="comment" style="margin-top:4px;" title="Add Comment">Add Comment </button>

                        </div>

                    </div>
                <?php } ?>

            </div>
        </div>

        <div id="comment-wrapper2" style="display:none;">

            <?php if ($rwgetRole['wf_log'] == '1' || $rwgetRole['review_log'] == '1') { ?>  
                <div class="panel-body">
                    <ul class="nav nav-pills">
                        <?php if ($rwgetRole['wf_log'] == '1') { ?>
                            <li class="active tbs"><a href="#navpills-1" data-toggle="tab" aria-expanded="true"><?= $lang['activity_log']; ?></a></li>
                        <?php } ?>
                        <?php if ($rwgetRole['review_log'] == '1') { ?>
                            <li class="<?= ($rwgetRole['wf_log'] != '1' ? 'active' : '') ?> tbs"><a href="#navpills-2" data-toggle="tab" aria-expanded="false"><?= $lang['Review_Log']; ?></a></li>
                        <?php } ?>
                    </ul>
                    <div class="tab-content br-n pn">
                        <?php if ($rwgetRole['wf_log'] == '1') { ?>
                            <div id="navpills-1" class="tab-pane active">
                                <?php
                                $pdflogSql = "select * from tbl_ezeefile_logs_wf where doc_id='$id1' ";
                                if ($_SESSION[cdes_user_id] != 1) {
                                    // $pdflogSql.=" and user_id='$_SESSION[cdes_user_id]'";   
                                }
                                $pdflog = mysqli_query($db_con, $pdflogSql);

                                if (mysqli_num_rows($pdflog) > 0) {
                                    while ($rwpdflog = mysqli_fetch_assoc($pdflog)) {
                                        echo '<span><strong> ' . $lang['Action'] . ' : </strong>' . $rwpdflog['action_name'] . '<br>';
                                        echo '<strong> ' . $lang['action_by'] . ' : </strong>' . $rwpdflog['user_name'] . '<br>';
                                        echo '<strong> ' . $lang['action_time'] . ' : </strong>' . date('d M Y, H:i', strtotime($rwpdflog['start_date'])) . '</span>';
                                    }
                                } else {
                                    echo '<center>' . $lang['activity_logs'] . '</center><hr style="color:#000;">';
                                }
                                ?>   
                            </div>
                        <?php } ?>
                        <?php if ($rwgetRole['review_log'] == '1') { ?>
                            <div id="navpills-2" class="tab-pane <?= ($rwgetRole['wf_log'] != '1' ? 'active' : '') ?>">
                                <?php
                                $rlog_sql = "select rl.*,u.first_name,u.last_name from tbl_reviews_log rl left join tbl_user_master u on rl.user_id=u.user_id where 1=1" . $rwlog_cond;
                                if ($_SESSION[cdes_user_id] != 1) {
                                    //   $rlog_sql.=" and rl.user_id='$_SESSION[cdes_user_id]'";   
                                }
                                $rlog_sql .= " and rl.doc_id='$id1' order by id desc";

                                $rlog_query = mysqli_query($db_con, $rlog_sql);

                                if (mysqli_num_rows($rlog_query) > 0) {

                                    while ($rlog_res = mysqli_fetch_assoc($rlog_query)) {
                                        echo '<span><strong> ' . $lang['Action'] . ' : </strong> ' . $rlog_res['action_name'] . '<br>';
                                        echo '<strong> ' . $lang['action_by'] . ' : </strong> ' . $rlog_res['first_name'] . ' ' . $rlog_res[last_name] . '<br>';
                                        echo '<strong> ' . $lang['action_time'] . ' : </strong>' . date('d M Y, H:i', strtotime($rlog_res['start_date'])) . '</span>';
                                    }
                                } else {
                                    echo '<center>' . $lang['No_Review_Log_found'] . '</center><hr style="color:#000;">';
                                }
                                ?>
                            </div>
                        <?php } ?>
                    </div>                                    
                </div>
            <?php } ?>



        </div>

        <script>
            // If absolute URL from the remote server is provided, configure the CORS
            // header on that server.
            //var url = "test2.pdf";

            // var url = "../extract-here/<?php echo $filePath; ?>";
            var url = "<?php echo $localPath; ?>";
            //console.log(url);

            var filename = "../<?php echo $localPath; ?>";

//
//            var filename = "<?php echo $localPath; ?>";

            // The workerSrc property shall be specified.
            //PDFJS.workerSrc = '//mozilla.github.io/pdf.js/build/pdf.worker.js';
            PDFJS.workerSrc = 'pdf.worker.js';

            var pdfDoc = null,
                    pageNum = <?php echo (isset($_GET['pn']) && !empty($_GET['pn'])) ? $_GET['pn'] : 1; ?>,
                    pageRendering = false,
                    pageNumPending = null,
                    //scale = 0.8,
                    scale = 1.5,
                    canvas = document.getElementById('the-canvas'),
                    ctx = canvas.getContext('2d');
            canvas2 = document.getElementById('canvas2');
            ctx2 = canvas2.getContext('2d');
            /**
             * Get page info from document, resize canvas accordingly, and render page.
             * @param num Page number.
             */
            var canHight;
            var canWidth;
            function renderPage(num) {
                pageRendering = true;
                // Using promise to fetch the page
                pdfDoc.getPage(num).then(function (page) {
                    var viewport = page.getViewport(scale);
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;
                    canvas2.height = viewport.height;
                    canvas2.width = viewport.width;


                    canWidth = viewport.width;
                    canHight = viewport.height;

                    // Render PDF page into canvas context
                    var renderContext = {
                        canvasContext: ctx,
                        viewport: viewport
                    };
                    var renderTask = page.render(renderContext);

                    // Wait for rendering to finish
                    renderTask.promise.then(function () {
                        pageRendering = false;
                        if (pageNumPending !== null) {
                            // New page rendering is pending
                            renderPage(pageNumPending);
                            pageNumPending = null;
                        }
                    });
                });

                // Update page counters
                // document.getElementById('page_num').textContent = pageNum;
            }

            /**
             * If another page rendering in progress, waits until the rendering is
             * finised. Otherwise, executes rendering immediately.
             */
            function queueRenderPage(num) {
                if (pageRendering) {
                    pageNumPending = num;
                } else {
                    renderPage(num);
                }
            }

            /**
             * Displays previous page.
             */
            function onPrevPage() {
                if (pageNum <= 1) {
                    return;
                }
                pageNum--;
                queueRenderPage(pageNum);
            }
            document.getElementById('prev').addEventListener('click', onPrevPage);

            /**
             * Displays next page.
             */
            function onNextPage() {
                if (pageNum >= pdfDoc.numPages) {
                    return;
                }
                pageNum++;
                queueRenderPage(pageNum);
            }
            document.getElementById('next').addEventListener('click', onNextPage);

            /**
             * Asynchronously downloads PDF.
             */
            PDFJS.getDocument(url).then(function (pdfDoc_) {
                pdfDoc = pdfDoc_;
                document.getElementById('page_count').textContent = pdfDoc.numPages;

                // Initial/first page rendering
                renderPage(pageNum);
            });

        </script>
        <?php //require_once '../application/pages/footerForjs.php';      ?>
<!--sign modal @dv24-apr-20-lockdown-->
        <div id="addsignature" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="custom-width-modalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="post" enctype="multipart/form-data">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h4 class="modal-title" id="custom-width-modalLabel"><?php echo $lang['Add_Sign']; ?></h4>
                        </div>
                        <div class="modal-body">
                            <label><?php echo $lang['Add_Sign']; ?><span class="text-alert">*</span></label>
                            <div class="form-group">
                                <input type="file" name="sign" class="filestyle" accept="image/*" required="">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" name="id" value="<?php echo $rwUser['user_id']; ?>">
                            <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                            <button type="submit" name="addSign" class="btn btn-primary waves-effect waves-light"><?php echo $lang['Save_changes']; ?></button>
                        </div>
                    </form>

                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>
        <div id="con-close-modal2" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog" id="afterClickHide"> 
                <div class="modal-content" > 
                    <div class="modal-header"> 
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                        <h4 class="modal-title"><?= $lang['Update_File']; ?></h4> 
                    </div> 
                    <img src="../assets/images/anote-wait.gif" alt="load" id="anotWt" style="display: none;"/>
                    <form method="post">
                        <div class="modal-body">
                            <?php if ($chk != 'rw') { ?>
                                <p class="text-danger"><?= $lang['Do_u_wt_to_ovrt_ex_fl_or_wnt_to_sve_as_nw_vn']; ?><?php if ($chk != 'rw'): ?><?php endif; ?></p>
                            <?php } else { ?>
                                <p class="text-danger"><?= $lang['Do_u_wt_to_ovrt_file']; ?></p>

                            <?php } ?>
                        </div>
                        <div class="modal-footer"> 
                            <input value="1" name="confrm" type="hidden" >
                            <button type="button"  class="btn btn-success"  id="save1" data="1"><?= $lang['overwrite']; ?></button>
                            <?php if ($chk != 'rw'): ?>
                                <button type="button" class="btn btn-default waves-effect" id="save1" data="2"><?= $lang['save_as_New']; ?></button>
                            <?php endif; ?>
                        </div>
                    </form>

                </div> 
            </div>
        </div>

        <!--//sk@4918 : Insert pdf file popup-->
        <div id="con-close-modal-insert" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog" id="afterClickHide"> 
                <div class="modal-content" > 
                    <div class="modal-header"> 
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                        <h4 class="modal-title">Upload File !</h4> 
                    </div> 
                    <img src="../assets/images/anote-wait.gif" alt="load" id="anotWt" style="display: none;"/>
                    <form method="post" id="insert-pdf" enctype="multipart/form-data">
                        <div class="modal-body">
                            <?php
                            // total pages
                            if (file_exists($localPath)) {
                                $tfp = totalfpages($localPath);
                            }
                            if (add_page_inbtwn) {
                                $pn = $_GET['pn'];
                                ?>
                                <label>Select Page No. :</label>
                                <select name="fpnum" id="fpnum" class="input-sm">
                                    <?php
                                    //$tfp = totalfpages($localPath);
                                    for ($i = 1; $i <= $tfp; $i++) {
                                        ?>
                                        <option <?= ($i == intval($_GET['pn']) ? 'selected="selected"' : '') ?> value="<?= $i ?>"><?= $i ?></option>
                                    <?php } ?>    
                                </select>
                                <label>Position : </label>
                                <select name="fpos" id="fpos" class="input-sm">
                                    <option value="a">After</option>
                                    <option value="b">Before</option>

                                </select><br>
                                <?php
                            } else {
                                $pn = $tfp;
                                ?>
                                <input type="hidden" name="fpos" value="a" class="filestyle">
                                <input type="hidden" name="fpnum" value="<?= $pn ?>" class="filestyle">
                                <p>Note : The file you upload will be added at end of target file.</p><br>
                            <?php } ?>

                            <label>Upload File</label>
                            <input type="hidden" name="tfp" id="tfp" value="3" class="filestyle">
                            <input type="file" name="insertUpload" id="insertUpload" class="filestyle" required>
                            <input type="hidden" name="tktid"  value="<?php echo $rwTask['ticket_id']; ?>"/>
                        </div>
                        <div class="modal-footer"> 
                            <input value="1" name="confrm" type="hidden" >
                            <button type="submit"  class="btn btn-success" value="Submit"  id="inSaveBtn" name="inSaveBtn">Overwrite</button>
                            <?php if ($chk != 'rw') { ?>
                                <button type="submit" id="inSaveAsNew" name="inSaveAsNew" class="btn btn-success">Save as New</button>
                            <?php } ?>
                        </div>
                    </form>

                </div> 
            </div>
        </div>

        <div id="con-close-modal-del" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog" id="afterClickHide"> 
                <div class="modal-content" > 
                    <div class="modal-header"> 
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                        <h4 class="modal-title">Select Page To Delete</h4> 
                    </div> 
                    <img src="../assets/images/anote-wait.gif" alt="load" id="anotWt" style="display: none;"/>
                    <form method="post" id="insert-pdf" enctype="multipart/form-data">
                        <div class="modal-body">
                            <label>Select Page No. :</label>
                            <select name="fpnum" id="delfpnum">
                                <?php
                                $tfp = totalfpages($localPath);
                                ;
                                for ($i = 1; $i <= $tfp; $i++) {
                                    ?>
                                    <option <?= ($i == intval($_GET['pn']) ? 'selected="selected"' : '') ?> value="<?= $i ?>"><?= $i ?></option>
                                <?php } ?>    
                            </select>
                        </div>
                        <div class="modal-footer"> 
                            <input value="1" name="confrm" type="hidden" >
                            <button type="submit" onclick="return delConfirm()"  class="btn btn-success" value="Delete"  id="delete" name="delete">Delete</button>
                            <?php if ($chk != 'rw'): ?>
                                <button type="submit" onclick="return delConfirm()"  id="delSaveAsNew" name="delSaveAsNew" class="btn btn-default waves-effect">Delete & Save As New</button>
                            <?php endif; ?>

                        </div>
                    </form>

                </div> 
            </div>
        </div>
        <!--sk@221018-give textarea with tinymce for comment.--> 
        <div id="con-close-modal-comment" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-lg" id="afterClickHide"> 
                <div class="modal-content" > 
                    <div class="modal-header"> 
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                        <h4 class="modal-title">Add Comment</h4> 
                    </div> 
                    <img src="../assets/images/anote-wait.gif" alt="load" id="anotWt" style="display: none;"/>
                    <form method="post" enctype="multipart/form-data" id="pdf_comment_form">
                        <input type="hidden" name="cfp" id="cfp" value="<?= $cmt_file_path ?>">
                        <input type="hidden" name="tktid"  value="<?php echo $rwTask['ticket_id']; ?>" id="tktid"/>
                        <input type="hidden" name="tskid"  value="<?php echo $rwTask['task_id'] ?>" id="tskid"/>

                        <div class="modal-body">
                            <label>Enter Comment</label>
                            <div class="form-group">
                                <textarea  id="editor1" class="form-control" rows="4"></textarea>


                            </div>
                            <br>
                            <label>Upload File</label>
                            <input type="hidden" name="tfp" id="tfp" value="3" class="filestyle">
                            <input type="file" name="comment_file" id="comment_file" class="filestyle">

                        </div>
                        <div class="modal-footer"> 
                            <input value="1" name="confrm" type="hidden" >
                            <input type="submit" value="Submit" class="btn btn-success" onclick="return postPdfComment(event)"  id="comment_btn">
                        </div>
                    </form>

                </div> 
            </div>
        </div>

        <?php if ($chk != 'rw'): ?>
            <div id="con-close-modal-act" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog modal-full" id="afterSubmt"> 

                    <div class="modal-content" > 
                        <form method="post" enctype="multipart/form-data"  id="forward_form">
                            <div class="modal-header"> 
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                                <h4 class="modal-title"><?php echo $lang['Aprovd/Rjctd_Tsk']; ?></h4> 
                            </div>

                            <div class="modal-body" >
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <div class="col-md-6">
                                            <label style="margin-left:-11px;"><?php echo $lang['UPDAT_DCUMNT']; ?></label>
                                        </div>
                                        <div class="col-md-6">
                                            <input class="filestyle" id="myImage1" name="fileName" data-buttonname="btn-primary" type="file">
                                            <input type="hidden" id="pCount" name="pageCount">
                                        </div>
                                        <p id="mem_msg" style="display:none;"></p>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <div class="col-md-6">
                                            <label style="margin-left:-11px;"><?php echo $lang['TSK_ACTN']; ?></label>
                                        </div>
                                        <div class="col-md-6">
                                            <?php
                                            $display = 'none';
                                            $actions = $rwWork['actions'];
                                            $actions = explode(",", $actions);
                                            if (in_array("Processed", $actions)) {
                                                ?>
                                                <input type="radio" name="app" value="Processed" id="processed" <?php
                                                if ($rwTask['task_status'] == 'Processed') {
                                                    echo'checked';
                                                    $display = 'block';
                                                }
                                                ?>> <label for="processed"><?php echo $lang['Processed']; ?></label>&nbsp;&nbsp;
                                                       <?php
                                                   }
                                                   if (in_array("Approved", $actions)) {
                                                       ?>
                                                <input type="radio" name="app" value="Approved" id="app" <?php
                                                if ($rwTask['task_status'] == 'Approved') {
                                                    echo'checked';
                                                    $display = 'block';
                                                }
                                                ?>> <label for="app"><?php echo $lang['Approved']; ?></label>&nbsp;&nbsp;
                                                       <?php
                                                   }
                                                   if (in_array("Rejected", $actions)) {
                                                       ?>
                                                <input type="radio" name="app" value="Rejected" id="dis" <?php
                                                if ($rwTask['task_status'] == 'Rejected') {
                                                    echo'checked';
                                                }
                                                ?>> <label for="dis"><?php echo $lang['Rejected']; ?></label>&nbsp;&nbsp;
                                                       <?php
                                                   }
                                                   if (in_array("Aborted", $actions)) {
                                                       ?>
                                                <input type="radio" name="app" value="Aborted" id="abort">
                                                <label for="abort"><?php echo $lang['Aborted']; ?></label>&nbsp;&nbsp;
                                                <?php
                                            }
                                            if (in_array("Complete", $actions)) {
                                                ?>
                                                <input type="radio" name="app" value="Complete" id="comp" <?php
                                                if ($rwTask['task_status'] == 'Complete') {
                                                    echo'checked';
                                                }
                                                ?>>
                                                <label for="comp"><?php echo $lang['Complete']; ?></label>
                                                <?php
                                            }
                                            if (in_array("Done", $actions)) {
                                                ?>
                                                <input type="radio" name="app" value="Done" id="done" <?php
                                                if ($rwTask['task_status'] == 'Done') {
                                                    echo'checked';
                                                    $display = 'block';
                                                }
                                                ?>>
                                                <label for="done"><?php echo $lang['Done']; ?></label>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <?php
                                        //$rwTask['task_id'];
                                        $getOwnTask = mysqli_query($db_con, "select * from tbl_task_master where task_id='$rwTask[task_id]'") or die('Error:' . mysqli_error($db_con));
                                        $rwgetOwnTask = mysqli_fetch_assoc($getOwnTask);

                                        $TskStpId = $rwgetOwnTask['step_id'];
                                        $TskWfId = $rwgetOwnTask['workflow_id'];
                                        $TskOrd = $rwgetOwnTask['task_order'];
                                        $TskAsinToId = $rwgetOwnTask['assign_user'];
                                        $cTaskid = $rwgetOwnTask['task_id'];
                                        $cTaskOrd = $TskOrd;

                                        $nextTskId = nextTaskToUpdate($cTaskOrd, $TskWfId, $TskStpId, $db_con);

                                        $getNxtTask = mysqli_query($db_con, "select * from tbl_task_master where task_id='$nextTskId'") or die('Error:' . mysqli_error($db_con));
                                        $rwgetNextTask = mysqli_fetch_assoc($getNxtTask);
                                        $rwgetNextTask['task_order'];
                                        ?>
                                        <div  id="hidden_div">
                                            <label><?php if (!empty($nextTskId)) { ?>EDIT/<?php } ?><?php echo $lang['Add_User']; ?></label>
                                            <div id="createTaskFlowr">
                                            </div>
                                            <div class="form-group">
                                                <a href="#" id="createOwnflowr" class="btn btn-primary" style="margin-top: -40px; float: right;" data=""><i class="fa fa-plus-circle"></i></a>
                                            </div>
                                            <?php if (!empty($nextTskId)) { ?>
                                                <div class="row">
                                                    <div class="col-sm-2">
                                                        <label for=""><?php echo $lang['Order']; ?><span style="color: red;">*</span></label>
                                                        <input type="number" class="form-control" name="taskOrder" min="1" value="<?php echo $rwgetNextTask['task_order']; ?>" style="height:35px;" readonly>
                                                    </div> 
                                                    <div class="col-sm-3">
                                                        <label for="userName"><?php echo $lang['Assign_User']; ?><span style="color: red;">*</span></label>
                                                        <select class="selectpicker" data-live-search="true" name="asiusr" data-style="btn-white" >
                                                            <option selected disabled style="background: #808080; color: #121213;"><?php echo $lang['Select_User']; ?></option>
                                                            <?php
                                                            $user = mysqli_query($db_con, "select * from tbl_user_master where user_id in($sameGroupIDs)") or die('Error in uname' . mysqli_error($db_con));
                                                            while ($rwUser = mysqli_fetch_assoc($user)) {
                                                                ?>
                                                                <?php if ($rwUser['user_id'] != 1 && $_SESSION['cdes_user_id'] != $rwUser['user_id']) { ?>
                                                                    <option <?php
                                                                    if ($rwgetNextTask['assign_user'] == $rwUser['user_id']) {
                                                                        echo 'selected';
                                                                    }
                                                                    ?> value="<?php echo $rwUser['user_id']; ?>"><?php echo $rwUser['first_name'] . ' ' . $rwUser['last_name']; ?>
                                                                    </option>

                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <label for="userName"><?php echo $lang['Alternate_User']; ?><span style="color: red;">*</span></label>
                                                        <select class="selectpicker" data-live-search="true" name="altrUsr" data-style="btn-white">
                                                            <option selected disabled style="background: #808080; color: #121213;"><?php echo $lang['Sl_Altnte_Ur']; ?></option>
                                                            <?php
                                                            $user = mysqli_query($db_con, "select * from tbl_user_master where user_id in($sameGroupIDs)") or die('Error in uname' . mysqli_error($db_con));
                                                            while ($rwUser = mysqli_fetch_assoc($user)) {
                                                                ?>
                                                                <?php if ($rwUser['user_id'] != 1 && $_SESSION['cdes_user_id'] != $rwUser['user_id']) { ?>
                                                                    <option <?php
                                                                    if ($rwgetNextTask['alternate_user'] == $rwUser['user_id']) {
                                                                        echo'selected';
                                                                    }
                                                                    ?> value="<?php echo $rwUser['user_id']; ?>">
                                                                            <?php echo $rwUser['first_name'] . ' ' . $rwUser['last_name']; ?>
                                                                    </option>

                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <label for="userName"><?php echo $lang['Select_Supervisor']; ?><span style="color: red;">*</span></label>
                                                        <select class="selectpicker" data-live-search="true" name="supvsr" data-style="btn-white">
                                                            <option selected disabled style="background: #808080; color: #121213;"><?php echo $lang['Select_Supervisor']; ?></option>
                                                            <?php
                                                            $user = mysqli_query($db_con, "select * from tbl_user_master where user_id in($sameGroupIDs)") or die('Error in uname' . mysqli_error($db_con));
                                                            while ($rwUser = mysqli_fetch_assoc($user)) {
                                                                ?>
                                                                <?php if ($rwUser['user_id'] != 1 && $_SESSION['cdes_user_id'] != $rwUser['user_id']) { ?>
                                                                    <option <?php
                                                                    if ($rwgetNextTask['supervisor'] == $rwUser['user_id']) {
                                                                        echo 'selected';
                                                                    }
                                                                    ?> value="<?php echo $rwUser['user_id']; ?>"><?php echo $rwUser['first_name'] . ' ' . $rwUser['last_name']; ?>
                                                                    </option>

                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            <?php } ?>

                                        </div>
                                    </div>

                                    <div class="form-group col-md-12" style="background:rgb(11, 175, 32); padding:10px; display:<?php echo $display; ?>;" id="hidden_note">
                                        <h4 class="m-t-0 m-b-20 header-title"><b><?php echo $lang['Nte_Shet']; ?></b></h4>
                                        <div class="row">
                                            <div class="col-sm-12 form-group">
                                                <input type="text" name="comment" class="form-control chat-input translatetext" id="input" placeholder="<?php echo $lang['Enter_yr_nte_her']; ?>">
                                            </div>
                                        </div>
                                        <div class="chat-conversation">

                                            <ul class="conversation-list nicescroll" style="height: Auto;">

                                                <?php
                                                //$proclist = mysqli_query($db_con, "select * from tbl_doc_assigned_wf where ticket_id='$rwTask[ticket_id]'");
                                                //$rwProclist = mysqli_fetch_assoc($proclist);
                                                $comment = mysqli_query($db_con, "select id,comment_time, comment,user_id, task_id, comment_desc from tbl_task_comment where tickt_id= '$rwTask[ticket_id]' order by comment_time desc");
                                                while ($rwcomment = mysqli_fetch_assoc($comment)) {
                                                    $ext = pathinfo($rwcomment['comment'], PATHINFO_EXTENSION);

                                                    $usr = mysqli_query($db_con, "select first_name, last_name,profile_picture from tbl_user_master where user_id='$rwcomment[user_id]'");
                                                    $rwUsr = mysqli_fetch_assoc($usr);
                                                    ?><li class="clearfix">
                                                        <div class="chat-avatar">
                                                            <?php if (!empty($rwUsr['profile_picture'])) { ?>
                                                                <img src="data:image/jpeg;base64,<?php echo base64_encode($rwUsr['profile_picture']); ?>" alt="Image">
                                                            <?php } else { ?>
                                                                <img src="<?= BASE_URL ?>assets/images/avatar.png" alt="Image">
                                                            <?php } ?>


                                                        </div>
                                                        <div class="conversation-text">

                                                            <div class="ctext-wrap">
                                                                <p><strong><?php echo $rwUsr['first_name'] . ' ' . $rwUsr['last_name']; ?></strong></p>
                                                                <p>
                                                                    <?php
                                                                    echo '<strong>Comment: </strong>';

                                                                    if ($ext) {
                                                                        ?>
                                                                        <a href="view?cid=<?= urlencode(base64_encode($rwcomment['id'])) ?>" target="_blank"><i class="fa fa-file cmt-file"></i></a><br><?php
                                                                    } 
																	
																	if(!empty($rwcomment['comment_desc'])){
                                                                        echo $rwcomment['comment_desc'];
																	}
                                                                    


                                                                    //get task name
                                                                    $getTaskName = mysqli_query($db_con, "select * from tbl_task_master where task_id='$rwcomment[task_id]'");
                                                                    $rwgetTaskName = mysqli_fetch_assoc($getTaskName);
                                                                    echo '<br/><strong>Task Name: </strong>' . $rwgetTaskName['task_name'];
                                                                    if (!empty($rwgetTaskName['task_description'])) {
                                                                        echo '<br/><strong>Task Description: </strong>' . $rwgetTaskName['task_description'];
                                                                    }
                                                                    ?>
                                                                    <br/><?php echo date("d - M - y, H:i", strtotime($rwcomment['comment_time'])); ?>
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </li>
                                                <?php } ?>
                                            </ul>

                                        </div>

                                    </div>


                                </div>
                            </div>
                            <div class="modal-footer">

                                <input type="hidden" value="<?php echo $rwTask['ticket_id']; ?>" name="tktId"/>
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                                <button type="submit" name="approveTask" class="btn btn-primary waves-effect waves-light" id="hideOnClick"><?php echo $lang['Submit']; ?></button> 
                            </div>

                        </form>
                    </div> 
                </div>
            </div><!-- /.modal -->
        <?php endif; ?>
        <!--show wait gif-->
        <div style=" display: none; background: rgba(0,0,0,0.5); width: 100%; z-index: 2000; position: fixed; top:0;" id="wait">;

            <img src="../assets/images/proceed.gif" alt="load"  style=" margin-left: 48%; margin-top: 250px; width: 100px; height:100px; position: fixed; "/>
        </div> 
        <script src="https://www.google.com/jsapi" type="text/javascript"></script> 
        <script>
                                //for wait gif display after submit
                                var heiht = $(document).height();
                                //alert(heiht);
                                $('#wait').css('height', heiht);
                                $('#save1').click(function () {
                                    $('#wait').show();
                                    //$('#wait').css('height',heiht);
                                    $('#afterClickHide').hide();
                                    return true;
                                });
        </script>

        <script>

            var x1 = new Array();
            var y1 = new Array();
            var x2 = new Array();
            var y2 = new Array();

            var pageN = new Array();

            var typed = new Array();

            var text = new Array();

            var strtX;
            var strtY;
            //var endX;
            //var endY;

            var canvas = document.getElementById('the-canvas'),
                    ctx = canvas.getContext('2d'),
                    rect1 = {},
                    drag = false;

            //strike
            var finalPos = {x: 0, y: 0};
            var startPos = {x: 0, y: 0};
            drawLine = false;

            //circle
            var xc1;
            var yc1;



            var canvas2 = document.getElementById('canvas2');
            var ctx2 = canvas2.getContext('2d');

            function init() {
                canvas.addEventListener('mousedown', mouseDown, false);

                canvas2.addEventListener('mousedown', mouseDown, false);
                canvas2.addEventListener('mouseup', mouseUp, false);
                canvas2.addEventListener('mousemove', mouseMove, false);
            }



            function mouseDown(e) {
                var type = $(":button.active").attr("data");

                rect1.startX = e.pageX - this.offsetLeft;
                rect1.startY = e.pageY - this.offsetTop;
                drag = true;

                //for strike
                drawLine = true;

                //circle
                xc1 = e.pageX - this.offsetLeft;
                yc1 = e.pageY - this.offsetTop;


                $("#canvas2").css({left: 260});
                var posX = $(this).position().left, posY = $(this).position().top;
                //x1.push(e.pageX - posX);
                //y1.push(e.pageY - posY);
                // console.log(x1);
                //console.log(y1);
                strtX = (e.pageX - posX);
                strtY = (e.pageY - posY);

                if (type == "text") {

                    if (hasInput)
                        return;
                    textBox(rect1.startX, rect1.startY);
                    //addInput(rect1.startX, rect1.startY);        
                }


            }

            function mouseUp(event) {
                var type = $(":button.active").attr("data");

                $("#canvas2").css({left: -13000});
                drag = false;


                switch (type) {
                    case "highlight":
                        highlightF();
                        break;
                    case "rectangle":
                        //alert('rect');
                        rectangleF();
                        break;

                    case "text":
                        textS();
                        break;

                    case "strikeout":
                        strikeoutF();
                        break;

                    case "eraser":
                        eraserF();
                        break;
                    case "circle":
                        circleF();
                        break;
                    case "approved":
                        approvedF();
                        break;
                    case "reject":
                        rejectF();
                        break;
                    case "signature":
                        signatureF();
                        break;
                    default:
                        break;
                }
                // var posX = $(this).position().left,posY = $(this).position().top;



                // console.log(x2);
                // console.log(y2);
                //console.log(x1);
                //console.log(y2);
                //console.log(typed);
            }

            function mouseMove(e) {
                var type = $(":button.active").attr("data");
                if (drag) {
                    rect1.w = (e.pageX - this.offsetLeft) - rect1.startX;
                    rect1.h = (e.pageY - this.offsetTop) - rect1.startY;

                    //circle
                    xc2 = e.pageX - this.offsetLeft;
                    yc2 = e.pageY - this.offsetTop;


                    switch (type) {
                        case "highlight":
                            highlight();

                            break;
                        case "rectangle":
                            rectangle();
                            break;

                        case "strikeout":
                            strikeout(e);
                            break;

                        case "eraser":
                            eraser();
                            break;
                        case "circle":
                            circle();
                            break;
                        case "approved":
                            approved();
                            break;
                        case "reject":
                            reject();
                            break;
                        case "signature":
                            signature();
                            break;
                        default:
                            break;
                    }
                }
            }

            function highlight() {
                ctx2.fillStyle = 'rgba(230,230,0,0.5)';
                ctx2.clearRect(rect1.startX, rect1.startY, rect1.w, rect1.h);
                ctx2.fillRect(rect1.startX, rect1.startY, rect1.w, rect1.h);
            }

            function highlightF() {

                pageN.push(pageNum);
                var type1 = $(":button.active").attr("data");
                typed.push(type1);
                x1.push(strtX);
                y1.push(strtY);
                x2.push(rect1.w);
                y2.push(rect1.h);

                ctx.fillStyle = 'rgba(230,230,0,0.5)';
                ctx.fillRect(rect1.startX, rect1.startY, rect1.w, rect1.h);
                ctx2.clearRect(0, 0, canvas2.width, canvas2.height);
            }

            function rectangle() {



                ctx2.strokeStyle = "#ff0000";
                ctx2.clearRect(rect1.startX, rect1.startY, rect1.w, rect1.h);
                ctx2.strokeRect(rect1.startX, rect1.startY, rect1.w, rect1.h);
            }

            function rectangleF() {
                pageN.push(pageNum);
                var type1 = $(":button.active").attr("data");
                typed.push(type1);
                x1.push(strtX);
                y1.push(strtY);
                x2.push(rect1.w);
                y2.push(rect1.h);

                ctx.strokeStyle = "rgba(255,0,0,1)";

                ctx.strokeRect(rect1.startX, rect1.startY, rect1.w, rect1.h);
                ctx2.clearRect(0, 0, canvas2.width, canvas2.height);

            }

            //circle
            function circle() {

                ctx2.clearRect(0, 0, canvas2.width, canvas2.height);
                drawEllipse(xc1, yc1, xc2, yc2, ctx2);

            }
            function circleF() {

                pageN.push(pageNum);
                var type1 = $(":button.active").attr("data");
                typed.push(type1);

                var rX = (xc2 - xc1) * 0.5;   // radius x
                var rY = (yc2 - yc1) * 0.5;    // radius y
                var cX = xc1 + rX;    //center x
                var cY = yc1 + rY;    //center y


                x1.push(cX);
                y1.push(cY);
                x2.push(rX);
                y2.push(rY);
                //    
                drawEllipse(xc1, yc1, xc2, yc2, ctx);
                ctx2.clearRect(0, 0, canvas2.width, canvas2.height);
            }

            function drawEllipse(x1, y1, x2, y2, ctx) {
                var radiusX = (x2 - x1) * 0.5,
                        radiusY = (y2 - y1) * 0.5,
                        centerX = x1 + radiusX,
                        centerY = y1 + radiusY,
                        step = 0.01,
                        a = step,
                        pi2 = Math.PI * 2 - step;

                ctx.beginPath();
                ctx.moveTo(centerX + radiusX * Math.cos(0),
                        centerY + radiusY * Math.sin(0));

                for (; a < pi2; a += step) {
                    ctx.lineTo(centerX + radiusX * Math.cos(a),
                            centerY + radiusY * Math.sin(a));
                }

                ctx.closePath();

                ctx.strokeStyle = 'red';
                ctx.stroke();
            }

            //for strike
            function strikeout(e) {
                if (drawLine === true) {
                    startPos = {x: strtX, y: strtY};
                    finalPos = {x: e.pageX - $('#canvas2').offset().left, y: e.pageY - $('#canvas2').offset().top};
                    ctx2.strokeStyle = 'red';
                    ctx2.lineWidth = 1;
                    ctx2.lineCap = 'round';

                    ctx2.clearRect(0, 0, canvas2.width, canvas2.height);
                    ctx2.beginPath();
                    ctx2.moveTo(startPos.x, startPos.y);
                    ctx2.lineTo(finalPos.x, finalPos.y);
                    ctx2.stroke();

                }
            }

            function strikeoutF() {
                // debugger;

                var type1 = $(":button.active").attr("data");

                if ((finalPos.x - strtX) > 0) {
                    pageN.push(pageNum);
                    typed.push(type1);
                    x1.push(strtX);
                    y1.push(strtY);
                    x2.push(finalPos.x);
                    y2.push(finalPos.y);

                    ctx.strokeStyle = 'red';
                    ctx.lineWidth = 1;
                    ctx.lineCap = 'round';

                    ctx.beginPath();
                    ctx.moveTo(startPos.x, startPos.y);
                    ctx.lineTo(finalPos.x, finalPos.y);
                    ctx.stroke();

                    finalPos = {x: 0, y: 0};
                    startPos = {x: 0, y: 0};
                    drawLine = false;
                }
                ctx2.clearRect(0, 0, canvas2.width, canvas2.height);

            }


            //eraser
            function eraser() {
                //ctx2.globalAlpha = 0.3; // set global alpha

                ctx2.fillStyle = '#ffffff';
                ctx2.clearRect(rect1.startX, rect1.startY, rect1.w, rect1.h);
                ctx2.fillRect(rect1.startX, rect1.startY, rect1.w, rect1.h);
            }
            function eraserF() {
                //ctx.globalAlpha = 0.3; // set global alpha
                pageN.push(pageNum);
                var type1 = $(":button.active").attr("data");
                typed.push(type1);
                x1.push(strtX);
                y1.push(strtY);
                x2.push(rect1.w);
                y2.push(rect1.h);

                ctx.fillStyle = '#ffffff';
                ctx.fillRect(rect1.startX, rect1.startY, rect1.w, rect1.h);
                ctx2.clearRect(0, 0, canvas2.width, canvas2.height);
            }

            //approved stamp
            function approved() {
                ctx2.strokeStyle = "#008000";
                ctx2.clearRect(rect1.startX, rect1.startY, rect1.w, rect1.h);
                ctx2.strokeRect(rect1.startX, rect1.startY, rect1.w, rect1.h);
            }

            function approvedF() {
                var image = document.getElementById('approveImg');
                pageN.push(pageNum);
                var type1 = $(":button.active").attr("data");
                typed.push(type1);
                x1.push(strtX);
                y1.push(strtY);
                x2.push(rect1.w);
                y2.push(rect1.h);

                ctx.strokeStyle = "rgba(0,128,0,1)";
                ctx.drawImage(image, strtX, strtY, rect1.w, rect1.h);
                //ctx.strokeRect(rect1.startX, rect1.startY, rect1.w, rect1.h);
                ctx2.clearRect(0, 0, canvas2.width, canvas2.height);

            }

            //reject stamp
            function reject() {
                ctx2.strokeStyle = "#0000FF";
                ctx2.clearRect(rect1.startX, rect1.startY, rect1.w, rect1.h);
                ctx2.strokeRect(rect1.startX, rect1.startY, rect1.w, rect1.h);
            }

            function rejectF() {
                pageN.push(pageNum);
                var image = document.getElementById('rejectImg');
                var type1 = $(":button.active").attr("data");
                typed.push(type1);
                x1.push(strtX);
                y1.push(strtY);
                x2.push(rect1.w);
                y2.push(rect1.h);

                ctx.strokeStyle = "rgba(0,0,255,1)";
                ctx.drawImage(image, strtX, strtY, rect1.w, rect1.h);
                //ctx.strokeRect(rect1.startX, rect1.startY, rect1.w, rect1.h);
                ctx2.clearRect(0, 0, canvas2.width, canvas2.height);

            }

            //signature stamp
            function signature() {
                ctx2.strokeStyle = "#A52A2A";
                ctx2.clearRect(rect1.startX, rect1.startY, rect1.w, rect1.h);
                ctx2.strokeRect(rect1.startX, rect1.startY, rect1.w, rect1.h);
            }

            function signatureF() {
                pageN.push(pageNum);
                var image = document.getElementById('signImg');
                var type1 = $(":button.active").attr("data");
                typed.push(type1);
                x1.push(strtX);
                y1.push(strtY);
                x2.push(rect1.w);
                y2.push(rect1.h);

                ctx.strokeStyle = "rgba(165,42,42,1)";
                ctx.drawImage(image, strtX, strtY, rect1.w, rect1.h);
                //ctx.strokeRect(rect1.startX, rect1.startY, rect1.w, rect1.h);
                ctx2.clearRect(0, 0, canvas2.width, canvas2.height);

            }

            init();

<?php
//tooltip starts

$crdnt = array();

$getCordnate = mysqli_query($db_con, "select * from tbl_anotation where doc_id = '$id1' and page_no=$_GET[pn]" . $annot_cond) or die('Error:' . mysqli_error($db_con));
if (mysqli_num_rows($getCordnate) > 0) {
    $i = 1;
    while ($rwgetCordnate = mysqli_fetch_assoc($getCordnate)) {
        $antionPageNo = $rwgetCordnate['page_no'];
        $antionCrdnt = $rwgetCordnate['co_ordinate'];
        $crdnt = explode(',', $antionCrdnt);

        $getAntnBy = mysqli_query($db_con, "select * from tbl_user_master where user_id = '$rwgetCordnate[anotation_by]'") or die('Error:' . mysqli_error($db_con));
        $rwgetAntnBy = mysqli_fetch_assoc($getAntnBy);
        ?>

                    //run when no next press
                    if (pageNum == '<?php echo $antionPageNo; ?>') {

                        region = {x: <?php echo $crdnt[0]; ?>, y: <?php echo $crdnt[1]; ?>, w: <?php echo empty(!$crdnt[2]) ? $crdnt[2] : 50; ?>, h: <?php echo empty(!$crdnt[3]) ? $crdnt[3] : 10; ?>};


                        if ("<?php echo $rwgetCordnate['anotation_type']; ?>" === "highlight") {
                            var hglt<?php echo $i; ?> = new ToolTip(canvas, region, "<?php echo $rwgetCordnate['anotation_type'] . ' by ' . $rwgetAntnBy['first_name'] . ' ' . $rwgetAntnBy['last_name'] . ' <br/> ' . date("j F, Y, H:i", strtotime($rwgetCordnate['date_time'])); ?>", 190, 1000, "highlight");
                        }
                        if ("<?php echo $rwgetCordnate['anotation_type']; ?>" === "rectangle") {
                            var rct<?php echo $i; ?> = new ToolTip(canvas, region, "<?php echo $rwgetCordnate['anotation_type'] . ' by ' . $rwgetAntnBy['first_name'] . ' ' . $rwgetAntnBy['last_name'] . ' <br/> ' . date("j F, Y, H:i", strtotime($rwgetCordnate['date_time'])); ?>", 190, 1000, "rectangle");
                        }

                        if ("<?php echo $rwgetCordnate['anotation_type']; ?>" === "text") {
                            var txt<?php echo $i; ?> = new ToolTip(canvas, region, "<?php echo $rwgetCordnate['anotation_type'] . ' by ' . $rwgetAntnBy['first_name'] . ' ' . $rwgetAntnBy['last_name'] . ' <br/> ' . date("j F, Y, H:i", strtotime($rwgetCordnate['date_time'])); ?>", 190, 1000, "text");

                        }
                        if ("<?php echo $rwgetCordnate['anotation_type']; ?>" === "strikeout") {
                            var txt<?php echo $i; ?> = new ToolTip(canvas, region, "<?php echo $rwgetCordnate['anotation_type'] . ' by ' . $rwgetAntnBy['first_name'] . ' ' . $rwgetAntnBy['last_name'] . ' <br/> ' . date("j F, Y, H:i", strtotime($rwgetCordnate['date_time'])); ?>", 190, 1000, "strikeout");

                        }
                        if ("<?php echo $rwgetCordnate['anotation_type']; ?>" === "circle") {
                            var rct<?php echo $i; ?> = new ToolTip(canvas, region, "<?php echo $rwgetCordnate['anotation_type'] . ' by ' . $rwgetAntnBy['first_name'] . ' ' . $rwgetAntnBy['last_name'] . ' <br/> ' . date("j F, Y, H:i", strtotime($rwgetCordnate['date_time'])); ?>", 190, 1000, "circle");
                        }
                        if ("<?php echo $rwgetCordnate['anotation_type']; ?>" === "approved") {
                            var rct<?php echo $i; ?> = new ToolTip(canvas, region, "<?php echo $rwgetCordnate['anotation_type'] . ' by ' . $rwgetAntnBy['first_name'] . ' ' . $rwgetAntnBy['last_name'] . ' <br/>' . date("j F, Y, H:i", strtotime($rwgetCordnate['date_time'])); ?>", 190, 1000, "approved");
                        }
                        if ("<?php echo $rwgetCordnate['anotation_type']; ?>" === "reject") {
                            var rct<?php echo $i; ?> = new ToolTip(canvas, region, "<?php echo $rwgetCordnate['anotation_type'] . ' by ' . $rwgetAntnBy['first_name'] . ' ' . $rwgetAntnBy['last_name'] . ' <br/> ' . date("j F, Y, H:i", strtotime($rwgetCordnate['date_time'])); ?>", 190, 1000, "reject");
                        }
                        if ("<?php echo $rwgetCordnate['anotation_type']; ?>" === "signature") {
                            var rct<?php echo $i; ?> = new ToolTip(canvas, region, "<?php echo $rwgetCordnate['anotation_type'] . ' by ' . $rwgetAntnBy['first_name'] . ' ' . $rwgetAntnBy['last_name'] . ' <br/> ' . date("j F, Y, H:i", strtotime($rwgetCordnate['date_time'])); ?>", 190, 1000, "signature");
                        }
                    }

        <?php
        $i++;
    }
}
?>




            //tooltip message
            function ToolTip(canvas, region, text1, width, timeout, type) {

                var me = this, // self-reference for event handlers
                        div1 = document.createElement("div"), // the tool-tip div

                        parent = canvas.parentNode, // parent node for canvas
                        visible = false;                          // current status

                // set some initial styles, can be replaced by class-name etc.
                div1.style.cssText = "position:fixed;padding:7px;background:gold;pointer-events:none;width:" + width + "px";

                div1.innerHTML = text1;



                // show the tool-tip
                this.show1 = function (pos) {
                    if (!visible) {                             // ignore if already shown (or reset time)
                        visible = true;                           // lock so it's only shown once
                        setDivPos(pos);                           // set position
                        parent.appendChild(div1);                  // add to parent of canvas
                        setTimeout(hide, timeout);                // timeout for hide

                    }
                }

                // hide the tool-tip
                function hide() {
                    visible = false;                            // hide it after timeout
                    parent.removeChild(div1);                    // remove from DOM
                }

                // check mouse position, add limits as wanted... just for example:
                function check1(e) {
                    var pos = getPos(e);
                    var posAbs = {x: e.clientX, y: e.clientY};  // div is fixed, so use clientX/Y

                    if (type === "circle") {
                        var dx = pos.x - region.x;
                        var dy = pos.y - region.y;
                        if (!visible && (dx * dx + dy * dy < region.w * region.h)) {
                            me.show1(posAbs);
                        }
                    }
                    if (!visible &&
                            pos.x >= region.x && pos.x < region.x + region.w &&
                            pos.y >= region.y && pos.y < region.y + region.h) {
                        me.show1(posAbs);                          // show tool-tip at this pos
                    } else
                        setDivPos(posAbs);                     // otherwise, update position
                }

                // get mouse position relative to canvas
                function getPos(e) {
                    var r = canvas.getBoundingClientRect();
                    return {x: e.clientX - r.left, y: e.clientY - r.top}
                }
                // update and adjust div position if needed (anchor to a different corner etc.)
                function setDivPos(pos) {
                    if (visible) {
                        if (pos.x < 0)
                            pos.x = 0;
                        if (pos.y < 0)
                            pos.y = 0;
                        // other bound checks here
                        div1.style.left = pos.x + "px";
                        div1.style.top = pos.y + "px";
                    }
                }
                // we need to use shared event handlers:
                //canvas.addEventListener("mousemove", check);
                $(document).mousemove(function (event1) {
                    check1(event1);
                });

                canvas.addEventListener("click", check1);

            }




            font = '18px Arial',
                    hasInput = false;
            function textBox(X, Y) {

                var input = new CanvasInput({
                    canvas: document.getElementById('canvas2'),
                    x: strtX,
                    y: strtY,
                    fontSize: 18,
                    fontFamily: 'Arial',
                    fontColor: '#f00',
                    fontWeight: 'bold',
                    fontStyle: 'normal',
                    width: 200,
                    padding: 8,
                    borderWidth: 1,
                    borderColor: '#4285f4',
                    borderRadius: 3,
                    boxShadow: '1px 1px 0px #fff',
                    innerShadow: '0px 0px 5px rgba(0, 0, 0, 0.5)',
                    placeHolder: 'Enter message here...',
                });
                input.focus();
                hasInput = true;
                input.onsubmit(function (e) {

                    if (e.keyCode === 13) {
                        pageN.push(pageNum);
                        var type1 = $(":button.active").attr("data");
                        typed.push(type1);
                        x1.push(strtX);
                        y1.push(strtY);
                        x2.push(rect1.startX);
                        y2.push(rect1.startY);
                        drawText(this.value(), parseInt(strtX), parseInt(strtY), x1.length - 1);
                        input.destroy();
                        hasInput = false;
                        $("#canvas2").css({left: -13000});

                        ctx2.clearRect(0, 0, canvas2.width, canvas2.height);
                    }
                });
            }
            function textS() {
                $("#canvas2").css({left: 260});

            }

            $(":button").click(function () {
                $(":button").removeClass("active");
                $(this).addClass("active");
            });


            function drawText(txt, x, y, indx) {
                ctx.textBaseline = 'top';
                ctx.textAlign = 'left';
                ctx.font = font;
                ctx.fillStyle = '#f00';
                ctx.fillText(txt, x - 4, y - 4);

                text[indx] = txt;
                console.log(text[indx]);
            }
            var tkt = $("#tktid").val();
            var tsk = $("#tskid").val();
            var docAsinId = "<?php echo $id; ?>";
            var docId = <?php echo $id1; ?>;
            var existFileConfirm;
            //@sk(271218): Set variable for review file.
            var chk = "<?= ($chk == 'rw' ? 'rw' : '') ?>";

            $("button#save1").click(function () {

                var conf = $(this).attr('data');
                var token = $("input[name='token']").val();
                $.post("../application/ajax/annotation.php", {X1: x1, Y1: y1, X2: x2, Y2: y2, pageNo: pageN, TYPE: typed, TEXT: text, FILEPATH: filename, CANWIDTH: canWidth, CANHEIGHT: canHight, DOCASIGNID: docAsinId, TKTID: tkt, CONFIRM: conf, DOCID: docId, chk: chk, token: token}, function (result, status) {
                    if (status == 'success') {
                        //$("#viewer").html(result);
                        //$('#waitFr').hide();
                        console.log(result);


                        alert('<?= $lang['suceess']; ?>');

                        //alert(result);
                        if (conf == 1) {
                            window.location.reload(false);
                            //location.href='index.php?<?php echo"id=$_GET[id]&id1=$_GET[id1]&pn=$_GET[pn]"; ?>';
                        } else {
                            // window.open('index.php?' + result, '_self');
                            window.location.reload();
                        }
                    }
                });
            });

        </script>
        <script>
            /*$('#coment').keypress(function (e) {
             if (e.which == 13) {
             //alert('ok');
             var coment = $("#coment").val();
             
             $.post("../application/ajax/comentOnPdf.php", {CMNT: coment, TKTID: tkt, TSKID: tsk}, function (result, status) {
             if (status == 'success') {
             
             $("#coment").val("");
             $("#comentAdd").html(result);
             
             }
             });
             return false;    //<---- Add this line
             }
             });
             $(document).ready(function () {
             $("html").bind("contextmenu", function (e) {
             e.preventDefault();
             });
             });
             jQuery(document).bind("keyup keydown", function (e) {
             if (e.ctrlKey && e.keyCode == 80) {
             alert("Please use the Print PDF button on top right of the page for a better rendering on the document");
             return false;
             }
             });*/

        </script>

        <!------- sk@7918----->
        <script>

            function postPdfComment(event) {
                event.preventDefault();
                var formData = new FormData($("#pdf_comment_form")[0]);
                //var formData=new FormData($("#pdf_comment_form")[0]);
				formData.append('comment', $("#editor1").val());
                //formData.append('comment', tinymce.get("editor1").getContent());
                $.ajax({
                    url: "../application/ajax/comentOnPdf.php",
                    //dataType:"json",
                    type: "POST",
                    //data: new FormData($("#pdf_comment_form")[0]) + '&comment=' + tinymce.get("editor").getContent(),
                    data: formData,
                    contentType: false,
                    processData: false,
                    beforeSend: function ()
                    {
                        $('#comment_btn').val('Wait...').prop('disabled', true);
                    },
                    success: function (result, status)
                    {

                        //alert(status); 
                        //if(r.status=='success')
                        //{
                        $("#coment").val("");
                        $("#comment_file").val("");
                        $('#comment_btn').val('Submit').prop('disabled', false);
                        $("#comentAdd").html(result);
                        $('#pdf_comment_form')[0].reset();
                        $('#con-close-modal-comment').modal('hide');
                        // taskSuccess('','Comment added Successfully');

                        //}
                    }
                });
            }

        </script>
        <script type="text/javascript">
            $(document).ready(function (e) {
                // Show Log 
                $("#log").click(function (e) {
                    $('#comment-wrapper2').toggle();
                });

                // Show Comment.
                $("#comment_button").click(function (e) {
                    $('#comment-wrapper').toggle();
                });

            });
        </script>     

        <!--        editor for comment.-->
        <script src="../assets/plugins/tinymce/tinymce.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                if ($("#editor").length > 0) {
                    tinymce.init({
                        selector: "textarea#editor",
                        theme: "modern",
                        height: 200,
                        plugins: [
                            "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
                            "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                            "save table contextmenu directionality emoticons template paste textcolor"
                        ],
                        toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | forecolor backcolor emoticons",
                        style_formats: [
                            {title: 'Bold text', inline: 'b'},
                            {title: 'Red text', inline: 'span', styles: {color: '#ff0000'}},
                            {title: 'Red header', block: 'h1', styles: {color: '#ff0000'}},
                        ]
                    });
                }
            });

        </script>



        <script>
            function delConfirm() {
                var txt;
                //var r = confirm("The current opened page (Page no. <?= intval($_GET['pn']) ?>) from file will be deleted. Are you sure you want to delete ?");
                var r = confirm("Page no. " + $('#delfpnum').val() + " from file will be deleted. Are you sure you want to delete ?");
                if (r == true) {
                    return true;
                } else {
                    return false;
                }
                document.getElementById("demo").innerHTML = txt;
            }

            //@sk251018 - Confirmation to add page no. to the file.
            function pgnConfirm() {
                //var r = confirm("The current opened page (Page no. <?= intval($_GET['pn']) ?>) from file will be deleted. Are you sure you want to delete ?");
                var r = confirm("The Page no. will be added at top right corner of page.\n Are you sure you want to add page no. to the file.");
                if (r == true) {
                    return true;
                } else {
                    return false;
                }

            }

            //@sk291118- Confirmation to add page no. to the file.
            function dsConfirm() {
                var r = confirm("Are you sure you want to Digitally Signed this file.");
                if (r == true) {
                    return true;
                } else {
                    return false;
                }

            }
<?php if (isset($chk) && $chk != 'rw') { ?>
                window.onbeforeunload = function () {

                    $.post("../application/ajax/removeTempFiles.php", {filepath: url}, function (result) {

                    });
                    return;
                };
<?php } ?>
        </script>

        <script src="../assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <script src="../assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
        <!--for searchable select -->
        <script type="text/javascript" src="../assets/plugins/jquery-quicksearch/jquery.quicksearch.js"></script>
        <script src="../assets/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>

        <script type="text/javascript" src="../assets/plugins/parsleyjs/parsley.min.js"></script>
        <!-- Sweet-Alert  -->
        <script src="../assets/plugins/sweetalert2/sweetalert2.min.js"></script>
        <script src="../assets/pages/jquery.sweet-alert.init.js"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                $('form').parsley();

            });
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
            //select user hidden div
            $('input[type=radio]').change(function () {

                if ($(this).attr('id') == 'abort' || $(this).attr('id') == 'dis' || $(this).attr('id') == 'comp') {
                    if ($(this).attr('id') == 'dis') {
                        //$("#input").attr("required","true");
                        $("#input").prop('required', true);
                    }
                    $('#hidden_div').hide();
                    $("#hidden_note").show();
                } else if ($(this).attr('id') == 'app' || $(this).attr('id') == 'processed') {
                    $("#hidden_div").show();

                    $("#hidden_note").show();
                }
            });

            $('#forward_form').on('submit', function() {
                $('#wait').show();
            });
            //image detail              
            $('#myImage1').bind('change', function () {
                //this.files[0].size gets the size of your file.

                //var input = document.getElementById("#myImage");
                var reader = new FileReader();
                reader.readAsBinaryString(this.files[0]);
                reader.onloadend = function () {
                    var count = reader.result.match(/\/Type[\s]*\/Page[^s]/g).length;
                    $("#pageCount").html(count);
                    $("#pCount").val(count);
                    // console.log('Number of Pages:',count );
                }

            });

        </script>
        <!--for ADD NEW USER-->
        <script>
            $("a#createOwnflowr").click(function () {
                var createown = 0;
                // alert(id);
                $("#createOwnflowr").hide();
                $.post("../application/ajax/createownFlow2.php", {ID: createown}, function (result, status) {
                    if (status == 'success') {
                        $("#createTaskFlowr").html(result);
                        // alert(result);
                    }
                });
            });
        </script>

        <?php
        if (isset($_POST['approveTask'], $_POST['token'])) {
            $docID = '0';

            $comment = xss_clean(trim($_POST['comment']));
            $comment = mysqli_real_escape_string($db_con, $comment);

            $tktId = $_POST['tktId'];

            $user_id = $_SESSION['cdes_user_id'];
            $taskId = $rwTask['task_id'];

            if ($_FILES['fileName']['name']) {
                $file_name = $_FILES['fileName']['name'];
                if (strlen($file_name) < 30) {
                    $file_size = $_FILES['fileName']['size'];
                    $file_type = $_FILES['fileName']['type'];
                    $file_tmp = $_FILES['fileName']['tmp_name'];
                    $pageCount = $_POST['pageCount'];

                    $extn = substr($file_name, strrpos($file_name, '.') + 1);
                    $fname = substr($file_name, 0, strrpos($file_name, '.'));

                    $fileExtn = substr($file_name, strrpos($file_name, ".") + 1);

                    $getDocId = mysqli_query($db_con, "select * from tbl_doc_assigned_wf where id = '$tid'") or die('Error:' . mysqli_error($db_con));
                    $rwgetDocId = mysqli_fetch_assoc($getDocId);
                    $doc_id = $rwgetDocId['doc_id'];
                    $dcPath = "extract-here/" . $rwgetDocId['doc_path'];
                    $getDocName = mysqli_query($db_con, "select * from tbl_document_master where doc_id = '$doc_id'") or die('Error:' . mysqli_error($db_con));
                    $rwgetDocName = mysqli_fetch_assoc($getDocName);
                    $docName = $rwgetDocName['doc_name'];
                    $docName = explode("_", $docName);

                    $updateDocName = $docName[0] . '_' . $doc_id . '_' . $docName[1];
                    $chekFileVersion = mysqli_query($db_con, "SELECT * FROM `tbl_document_master` WHERE find_in_set('$updateDocName', doc_name)") or die('Error:' . mysqli_error($db_con));
                    $flVersion = mysqli_num_rows($chekFileVersion);
                    $flVersion = $flVersion + 1;
                    $file_name = $tktId . '_' . $flVersion . '.' . $fileExtn;


                    $strgName = mysqli_query($db_con, "select * from tbl_storage_level where sl_id = '$docName[0]'") or die('Error:' . mysqli_error($db_con));
                    $rwstrgName = mysqli_fetch_assoc($strgName);
                    $storageName = $rwstrgName['sl_name'];
                    $storageName = str_replace(" ", "", $storageName);
                    $storageName = preg_replace('/[^A-Za-z0-9\-]/', '', $storageName);
                    $uploaddir = "../extract-here/images/" . $storageName . '/';
                    if (!is_dir($uploaddir)) {
                        mkdir($uploaddir, 0777, TRUE) or die(print_r(error_get_last()));
                    }

                    $fname = preg_replace('/[^A-Za-z0-9_\-]/', '', $fname);
                    // $filenameEnct=$fname.'.'.$extn;// urlencode(base64_encode($fname)).'.'.$extn;
                    $filenameEnct = urlencode(base64_encode($fname));
                    $filenameEnct = preg_replace('/[^A-Za-z0-9_\-]/', '', $filenameEnct);
                    $filenameEnct = $filenameEnct . '.' . $extn;
                    $filenameEnct = time() . $filenameEnct;

                    //  $image_path = "images/" . $file_name;
                    $uploaddir = $uploaddir . $filenameEnct;
                    $upload = move_uploaded_file($file_tmp, $uploaddir) or die(print_r(error_get_last()));
//                $logTaskName = mysqli_query($db_conn, "select task_name from tbl_task_master where task_id = '$taskId'") or die('Erorr getting Name:' . mysqli_error($db_conn));
//                $rwlogTaskName = mysqli_fetch_assoc($logTaskName);
//                $ltaskName = $rwlogTaskName['task_name'];
                    // if(FTP_ENABLED){

                    if ($upload) {
                        if (FTP_ENABLED) {
                            require_once '../classes/ftp.php';

                            $ftp = new ftp();
                            $ftp->conn("$fileserver", "$port", "$ftpUser", "$ftpPwd");

                            $ftp->put(ROOT_FTP_FOLDER . '/images/' . $storageName . '/' . $filenameEnct, $uploaddir);
                            $arr = $ftp->getLogData();
                            if ($arr['error'] != "") {
                                echo '<h2>Error:</h2>' . implode('<br />', $arr['error']);
                            }
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

                        //"INSERT INTO tbl_document_master($cols) select $cols from tbl_document_master where doc_id='$doc_id'";
                        $createVrsn = mysqli_query($db_con, "INSERT INTO tbl_document_master($cols) select $cols from tbl_document_master where doc_id='$doc_id'") or die('Error insert:' . mysqli_error($db_con));
                        $insertDocID = mysqli_insert_id($db_con);
                        //$createVrsn = mysqli_query($db_con, "INSERT INTO tbl_document_master(doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages, dateposted) VALUES ('$updateDocName', '$file_name', '$fileExtn', 'images/$storageName/$filenameEnct', '$user_id', '$file_size', '$pageCount', '$date')") or die('Error:' . mysqli_error($db_con));
                        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `doc_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null, '$doc_id','Versioning Document $file_name Added in task $ltaskName','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                        if ($createVrsn) {
                            $updateNew = mysqli_query($db_con, "update tbl_document_master set doc_name='$updateDocName', workflow_id='$wfid' where doc_id='$insertDocID'");
                            $updateOld = mysqli_query($db_con, "update tbl_document_master set old_doc_name='$file_name', workflow_id='$wfid',  filename='$fname', doc_extn='$extn', doc_path='images/$storageName/$filenameEnct', uploaded_by='$user_id', doc_size='$file_size', noofpages='$pageCount', dateposted='$date' where doc_id='$doc_id'");
                            echo'<script>taskSuccess("process_task?id=' . $_GET[id] . ((isset($_GET[start])) ? ('&start=' . $_GET[start]) : '') . '","Updated Successfully !");</script>';
                        }
                    }
                } else {
                    echo '<script>taskFailed("' . basename($_SERVER[REQUEST_URI]) . '", "' . $lang['file_name _too_long'] . '")</script>';
                }
            }

            if (!empty($_POST['app'])) {
                $app = $_POST['app'];

                if (!empty($comment)) {
                    //$user_id = $_SESSION['cdes_user_id'];
                    $cmttask = "INSERT INTO tbl_task_comment (`id`, `tickt_id`, `user_id`, `comment`, task_status, `comment_time`, task_id) VALUES (null,'$tktId', '$user_id','$comment', '$app', '$date', '$taskId')";
                    $run = mysqli_query($db_con, $cmttask) or die('Error query failed' . mysqli_error($db_con));
                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,' Comment $comment Added in task $ltaskName','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                }



                if ($app == 'Approved' || $app == 'Processed' || $app == 'Done') {

                    $run = mysqli_query($db_con, "UPDATE tbl_doc_assigned_wf SET task_status = '$app', action_by = '$user_id', action_time = '$date' where id='$tid' ") or die('Error query failed' . mysqli_error($db_con));
                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'$ltaskName task $app ','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                    $assignBy = $rwTask['assign_by'];

                    if (!empty($rwTask['doc_id'])) {
                        $docID = $rwTask['doc_id'];
                    }
                    $ctaskID = $rwWork['task_id'];
                    $ctaskOrder = $rwWork['task_order'];
                    $stepId = $rwWork['step_id'];
                    $wfid = $rwWork['workflow_id'];
                    $ticket = $rwTask['ticket_id'];

                    $taskRemark = mysqli_real_escape_string($db_con, $rwTask['task_remarks']);

                    $backgroundData = array(
                        'ticket' => $ticket,
                        'ctaskOrder' => $ctaskOrder,
                        'docID' => $docID,
                        'assignBy' => $assignBy,
                        'ctaskID' => $ctaskID,
                        'stepId' => $stepId,
                        'wfid' => $wfid
                    );

                    if (function_exists('curl_init')) {
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, BASE_URL . 'approvalWorker.php');
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($backgroundData));
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
                        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID=' . session_id());
                        
                        $response = curl_exec($ch);
                        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        $curlError = curl_error($ch);
                        
                        if ($curlError) {
                            error_log('approvalWorker.php cURL Error: ' . $curlError);
                        }
                        if ($httpCode != 200) {
                            error_log('approvalWorker.php HTTP Error Code: ' . $httpCode . ', Response: ' . $response);
                        }
                        
                        curl_close($ch);
                    }

                    //$tskAsinTOUsrId = $rwWork['assign_user'];

                    $getTskName = mysqli_query($db_con, "select * from tbl_task_master where task_id = '$ctaskID' ") or die('Error' . mysqli_error($db_con));
                    $rwgetTskName = mysqli_fetch_assoc($getTskName);

                    //send sms to mob
//                    $getMobNum = mysqli_query($db_con, "select * from tbl_user_master where user_id = '$assignBy'") or die('Error:' . mysqli_error($db_con));
//                    $rwgetMobNum = mysqli_fetch_assoc($getMobNum);
//                    $submtByMob = $rwgetMobNum['phone_no'];
//                    $msg = 'Your Ticket Id ' . $ticket . ' is Approved in Task ' . $rwgetTskName['task_name'] . '.';
//                    $sendMsgToMbl = smsgatewaycenter_com_Send($submtByMob, $msg, $debug = false);
                    //
                    // $tt = taskAssignToUser($db_con, $stepId, $ctaskOrder, $docID, $ctaskID, $assignBy, $tid, $wfid, $ticket, $taskRemark);
                    //upadte own Created user and order
                    //if (!empty($_POST['asiusr']) && !empty($_POST['altrUsr']) && !empty($_POST['supvsr'])) {
                    if (!empty($_POST['asiusr'])) {
                        $taskOrder = $_POST['taskOrder'];
                        $assiUsers = $_POST['asiusr'];
                        $altrusr = $_POST['altrUsr'];
                        $supvsr = $_POST['supvsr'];
                        $updOwnTask = mysqli_query($db_con, "update tbl_task_master set assign_user='$assiUsers', alternate_user='$altrusr', supervisor='$supvsr', task_order='$taskOrder' where task_id = '$nextTskId'") or die('Error hhh' . mysqli_error($db_con));
                        //$updOwnTask = mysqli_query($db_con, "update tbl_task_master set assign_user='$assiUsers', alternate_user='$altrusr', deadline='$deadLine', deadline_type='$deadlineType', supervisor='$supvsr', task_order='$taskOrder', task_created_date='$date' where task_id = '$taskId") or die('Error' . mysqli_error($db_con));
                        //$log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Assign User order updated in $ltaskName','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                    }
                    //Add new user to asign task
                    //if (!empty($_POST['assignUsrAdd']) && !empty($_POST['altrUsrAdd']) && !empty($_POST['supvsrAdd']) && !empty($_POST['radio'])) {
                    if (!empty($_POST['assignUsrAdd'])) {
                        $assiUsersAdd = $_POST['assignUsrAdd'];
                        $altrusrAdd = $_POST['altrUsrAdd'];
                        $supvsrAdd = $_POST['supvsrAdd'];
                        $deadlineType = $_POST['radio'];

                        if ($deadlineType == 'Date') {

                            $daterange = $_POST['daterangeAdd'];

                            $daterangee = explode("To", $daterange);

                            $startDate = date('Y-m-d H:i:s', strtotime($daterangee[0]));

                            $endDate = date('Y-m-d H:i:s', strtotime($daterangee[1]));

                            $date1 = new DateTime($startDate);
                            $date2 = new DateTime($endDate);
                            //print_r($date1);
                            // print_r($date2);
                            $diff = $date1->diff($date2);

                            $deadLineAdd = $diff->h * 60 + $diff->days * 24 * 60 + $diff->i;  //convert in minute
                            //echo $deadLine=$deadLine.'.'.$diff->i;
                            //echo   $deadLine=round($deadLine/60*60,1);
                            // die('ok');
                            //echo $deadLine; 
                        } else if ($deadlineType == 'Days') {
                            $deadLinee = $_POST['daysAdd'];
                            $deadLineAdd = $deadLinee;
                        } else if ($deadlineType == 'Hrs') {

                            $deadLinee = $_POST['hrsAdd'];
                            $deadLineAdd = $deadLinee * 60;
                        }
                        // echo 'ok1';
                        $cTskOrd = $TskOrd;
                        $cTskId = $rwTask['task_id'];
                        //echo $TskWfId;
                        //$TskStpId = $rwgetOwnTask['step_id'];
                        //$TskWfId = $rwgetOwnTask['workflow_id'];
                        //$TskOrd = $rwgetOwnTask['task_order'];
                        // echo 'dedline: ';
                        // echo $deadLineAdd;
                        $addUsr = addNewTskUsr($cTskId, $TskWfId, $TskStpId, $cTskOrd, $assiUsersAdd, $altrusrAdd, $supvsrAdd, $deadLineAdd, $deadlineType, $date, $db_con);

                        $getTask = mysqli_query($db_con, "select * from tbl_task_master where task_id = '$cTskId'") or die('Error:' . mysqli_error($db_con));
                        $rwgetTask = mysqli_fetch_assoc($getTask);
                        $TskStpId = $rwgetTask['step_id'];
                        $TskWfId = $rwgetTask['workflow_id'];
                        $TskOrd = $rwgetTask['task_order'];
                        $TskAsinToId = $rwgetTask['assign_user'];
                        $nextTaskOrd = $TskOrd + 1;

                        nextTaskAsin($nextTaskOrd, $TskWfId, $TskStpId, $docID, $date, $user_id, $db_con, $taskRemark, $ticket);
                    }

                    echo 'stepid: ' . $stepId;
                    //echo "Task Completed successfully before";
                    taskAssignToUser($db_con, $stepId, $ctaskOrder, $docID, $ctaskID, $assignBy, $tid, $wfid, $ticket, $taskRemark, $date);
                    //echo "Task Completed successfully after";


                    echo '<script>taskSuccess("../myTask","Task Completed successfully !");</script>';
                } else if ($app == 'Rejected') {
                    if (!empty($comment)) {
                        //$ticket_query= mysqli_query($db_con, "SELECT NextTask,ticket_id FROM tbl_doc_assigned_wf where id='$tid' ") or die('Error query failed pp:' . mysqli_error($db_con));
                        //$row_ticket_id=mysqli_fetch_array($ticket_query);
                        $run = mysqli_query($db_con, "UPDATE tbl_doc_assigned_wf SET task_status = '$app', action_by = '$user_id',action_time = '$date' where id='$tid' ") or die('Error query failed pp:' . mysqli_error($db_con));

                        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'$ltaskName task $app ','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                        $getTskName = mysqli_query($db_con, "select * from tbl_task_master where task_id = '$ctaskID' ") or die('Error' . mysqli_error($db_con));
                        $rwgetTskName = mysqli_fetch_assoc($getTskName);

                        backToPrevTsk($db_con, $stepId, $ctaskOrder, $docID, $ctaskID, $assignBy, $tid, $wfid, $tktId, $taskRemark, $date, $projectName);

                        require_once '../mail-for-anott.php';
                        //echo 'mail send id = '.$tid; die;
                        $mail = rejectTask($tid, $ctaskID, $tktId, $db_con, $projectName, $comment, $doc_id);
                        //$delete = mysqli_query($db_con, "DELETE FROM tbl_doc_assigned_wf WHERE ticket_id='$row_ticket_id[ticket_id]' AND NextTask=2") or die('Error query failed pp:' . mysqli_error($db_con));
                        if ($mail) {



                            //send sms to mob
//                        $getMobNum = mysqli_query($db_con, "select * from tbl_user_master where user_id = '$assignBy'") or die('Error:' . mysqli_error($db_con));
//                        $rwgetMobNum = mysqli_fetch_assoc($getMobNum);
//                        $submtByMob = $rwgetMobNum['phone_no'];
//                        $msg = 'Your Ticket Id ' . $ticket . ' is Rejected in Task ' . $rwgetTskName['task_name'] . '.';
//                        $sendMsgToMbl = smsgatewaycenter_com_Send($submtByMob, $msg, $debug = false);
                            //

                            echo '<script>taskSuccess("../myTask", "Task has been rejected !");</script>';
                        } else {
                            echo '<script>taskFailed("../myTask", "Opps!! Task is not rejected !")</script>';
                        }
                    } else {
                        echo '<script>taskFailed("process_task", "Reason is mandatory in comment")</script>';
                    }
                    exit();
                } else if ($app == 'Aborted') {

                    $getTskName = mysqli_query($db_con, "select * from tbl_task_master where task_id = '$ctaskID' ") or die('Error' . mysqli_error($db_con));
                    $rwgetTskName = mysqli_fetch_assoc($getTskName);

                    $update = mysqli_query($db_con, "update tbl_doc_assigned_wf set task_status='$app', action_by='$user_id',action_time='$date',NextTask='5' where id='$tid'");
                    $delete = mysqli_query($db_con, "DELETE FROM tbl_doc_assigned_wf WHERE ticket_id='$tktId' AND NextTask=2") or die('Error query failed pp:' . mysqli_error($db_con));
                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'$ltaskName task $app','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                    if ($update) {
                        require_once '../mail-for-anott.php';
                       // require_once '../mail.php';
                        //$mailSent = abortTask($ticket, $tid, $wfid, $db_con, $projectName);

                        if(MAIL_BY_SOCKET){

                            $paramsArray = array(
                                'ticket' => $ticket,
                                'id' => $tid,
                                'wfid' => $wfid,
                                'db_con' => $db_con,
                                'projectName' => $projectName,
                                'action' => 'abortTask'
                            );

                            mailBySocket($paramsArray);
                            
                        }else{

                            $mailSent = abortTask($ticket, $tid, $wfid, $db_con, $projectName);
                        }
                        //if ($mailSent) {

                            //send sms to mob
//                            $getMobNum = mysqli_query($db_con, "select * from tbl_user_master where user_id = '$assignBy'") or die('Error:' . mysqli_error($db_con));
//                            $rwgetMobNum = mysqli_fetch_assoc($getMobNum);
//                            $submtByMob = $rwgetMobNum['phone_no'];
//                            $msg = 'Your Ticket Id ' . $ticket . ' is Aborted in Task ' . $rwgetTskName['task_name'] . '.';
//                            $sendMsgToMbl = smsgatewaycenter_com_Send($submtByMob, $msg, $debug = false);
                            //


                            echo '<script>taskSuccess("../myTask", "Task has been aborted !");</script>';
                        // } else {
                        //     echo '<script>taskFailed("../myTask", "Opps!! Task is not aborted !")</script>';
                        // }
                    } else {
                        echo '<script>taskFailed("../myTask", "Opps!! Task is not aborted !")</script>';
                    }
                } else if ($app == 'Complete') {
                    $run = mysqli_query($db_con, "UPDATE tbl_doc_assigned_wf SET task_status = '$app', action_by = '$user_id', action_time = '$date', NextTask='1' where id='$tid'") or die('Error query failed' . mysqli_error($db_con));
                    $ticket = mysqli_query($db_con, "SELECT NextTask,ticket_id FROM tbl_doc_assigned_wf where id='$tid' ") or die('Error query failed pp:' . mysqli_error($db_con));
                    $row_ticket_id = mysqli_fetch_array($ticket);
                    $delete = mysqli_query($db_con, "DELETE FROM tbl_doc_assigned_wf WHERE ticket_id='$row_ticket_id[ticket_id]' AND NextTask=2") or die('Error query failed pp:' . mysqli_error($db_con));
                    if ($delete) {
                        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'$ltaskName task $app ','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                        $assignBy = $rwTask['assign_by'];

                        if (!empty($rwTask['doc_id'])) {
                            $docID = $rwTask['doc_id'];
                        }
                        $ctaskID = $rwWork['task_id'];
                        $ctaskOrder = $rwWork['task_order'];
                        $stepId = $rwWork['step_id'];
                        $wfid = $rwWork['workflow_id'];
                        $ticket = $rwTask['ticket_id'];

                        $taskRemark = mysqli_real_escape_string($db_con, $rwTask['task_remarks']);

                        //$tskAsinTOUsrId = $rwWork['assign_user'];
                        if (!empty($docID)) {
                            $updateDocMaster = mysqli_query($db_con, "update tbl_document_master set doc_name=replace(doc_name,'_$wfid','') where doc_id='$docID'");
                            //$update = mysqli_query($db_con, "update tbl_document_master set doc_name=replace(doc_name,'_$wfid','') where substring_index(doc_name,'_',-2)=$docID");
                            $update = mysqli_query($db_con, "update tbl_document_master set doc_name=CONCAT(TRIM(TRAILING '_$wfid' FROM doc_name), '') where substring_index(doc_name,'_',-2)=$docID");
                            //view version in storage after workflow complete
                        }
                        $getTskName = mysqli_query($db_con, "select * from tbl_task_master where task_id = '$ctaskID' ") or die('Error' . mysqli_error($db_con));
                        $rwgetTskName = mysqli_fetch_assoc($getTskName);
                        require_once '../mail-for-anott.php';
                        $mailSent = completeTask($ticket, $tid, $wfid, $db_con, $projectName);
                        if ($mailSent) {
                            echo '<script>taskSuccess("../myTask","Task Completed successfully !");</script>';
                        } else {
                            echo '<script>taskFailed("../myTask","Task Completion Failed !");</script>';
                        }

                        //taskAssignToUser($db_con, $stepId, $ctaskOrder, $docID, $ctaskID, $assignBy, $tid, $wfid, $ticket, $taskRemark, $date);
                    } else {
                        echo '<script>taskFailed("../myTask","Next Task Deletion Failed !");</script>';
                    }
                }
            }
            mysqli_close($db_con);
        }

        //end own user created and order

        function taskAssignToUser($db_con, $stepId, $ctaskOrder, $docID, $ctaskID, $assignBy, $tid, $wfid, $ticket, $taskRemark, $date) {

            $nextTaskIds = array();
            require_once '../application/pages/sendSms.php';
            require_once '../mail-for-anott.php';

            //echo "stepId :";
            // echo $stepId;
            $checkTaskNext = mysqli_query($db_con, "select * from tbl_task_master where step_id='$stepId' ORDER BY task_order");
            $k = 0;
            while ($rwCheckTask = mysqli_fetch_assoc($checkTaskNext)) {
                if ($rwCheckTask['task_order'] > $ctaskOrder) {
                    array_push($nextTaskIds, $rwCheckTask['task_id']);
                    $k++;
                }
                if ($k > 1) {
                    break;
                }
            }

            //print_r($nextTaskIds);
            if (!empty($nextTaskIds)) {

                $i = 0;
                foreach ($nextTaskIds as $nextTaskId) {
                    //echo "next task id: ";
                    echo $nextTaskId . 'id';
                    $nxtTaskDetail = mysqli_query($db_con, "select * from tbl_task_master where task_id='$nextTaskId'");

                    if (mysqli_num_rows($nxtTaskDetail) > 0) {
                        $rwNxtTaskDeatil = mysqli_fetch_assoc($nxtTaskDetail);

                        if ($rwNxtTaskDeatil['deadline_type'] == 'Days') {
                            $endDate = date('Y-m-d H:i:s', (strtotime($date) + ($rwNxtTaskDeatil['deadline'] * 24 * 60 * 60)));
                        } else if ($rwTaskn['deadline_type'] == 'Date') {
                            $endDate = date('Y-m-d H:i:s', (strtotime($date) + ($rwTaskn['deadline'] * 60)));
                        } else {
                            $endDate = date('Y-m-d H:i:s', (strtotime($date) + ($rwTaskn['deadline'] * 60 * 60)));
                        }



                        $taskCheck = mysqli_query($db_con, "select * from tbl_doc_assigned_wf where task_id='$nextTaskId' and doc_id='$docID' and ticket_id = '$ticket'");

                        if (mysqli_num_rows($taskCheck) < 1) {
                            //echo $nextTaskId; die();
                            if ($i == 0) {//insert to next task
                                if (!empty($docID) && $docID != 0) {
                                    $assignToNextWf = mysqli_query($db_con, "insert into tbl_doc_assigned_wf(`id`, `task_id`, `doc_id`, `start_date`, `end_date`, `task_status`, `assign_by`,`NextTask`,`ticket_id`,`task_remarks`)"
                                                    . "values(null,'$nextTaskId','$docID','$date','$endDate','Pending','$assignBy','0','$ticket','$taskRemark')") or die('Error to move next 1' . mysqli_error($db_con));
                                } else {
                                    $assignToNextWf = mysqli_query($db_con, "insert into tbl_doc_assigned_wf(`id`, `task_id`, `start_date`, `end_date`, `task_status`, `assign_by`,`NextTask`,`ticket_id`,`task_remarks`)"
                                                    . "values(null,'$nextTaskId','$date','$endDate','Pending','$assignBy','0','$ticket','$taskRemark')") or die('Error to move next 2' . mysqli_error($db_con));
                                }
                            } else if ($i == 1) {
                                if (!empty($docID) && $docID != 0) {
                                    $assignToNextWf = mysqli_query($db_con, "insert into tbl_doc_assigned_wf(`id`, `task_id`, `doc_id`, `start_date`, `end_date`, `task_status`, `assign_by`,`NextTask`,`ticket_id`,`task_remarks`)"
                                                    . "values(null,'$nextTaskId','$docID','$date','$endDate','Pending','$assignBy','2','$ticket','$taskRemark')") or die('Error to move next3' . mysqli_error($db_con));
                                } else {
                                    $assignToNextWf = mysqli_query($db_con, "insert into tbl_doc_assigned_wf(`id`, `task_id`, `start_date`, `end_date`, `task_status`, `assign_by`,`NextTask`,`ticket_id`,`task_remarks`)"
                                                    . "values(null,'$nextTaskId','$date','$endDate','Pending','$assignBy','2','$ticket','$taskRemark')") or die('Error to move next4' . mysqli_error($db_con));
                                }
                            }
                            $idnxt = mysqli_insert_id($db_con);


                            if ($assignToNextWf) {
                                //update current task flag and completion time
                                $update = mysqli_query($db_con, "update tbl_doc_assigned_wf set NextTask='1' , end_date='$endDate' where id='$tid'") or die('Error to update old' . mysqli_error($db_con));


                                assignTask($ticket, $idnxt, $db_con, $projectName);

                                //send sms to mob task asin to user

                                $getNextTaskId = mysqli_query($db_con, "select * from tbl_task_master where task_id = '$nextTaskId'") or die('Error:' . mysqli_error($db_con));
                                $rwgetNextTaskId = mysqli_fetch_assoc($getNextTaskId);
                                $taskName = $rwgetNextTaskId['task_name'];

//                                $getMobNumAsinTo = mysqli_query($db_con, "select * from tbl_user_master where user_id = '$rwgetNextTaskId[assign_user]'") or die('Error:' . mysqli_error($db_con));
//                                $rwgetMobNumAsinTo = mysqli_fetch_assoc($getMobNumAsinTo);
//                                $AsinToMob = $rwgetMobNumAsinTo['phone_no'];
//                                $msgAsinTo = 'New Task With Ticket Id ' . $ticket . ' and Task Name ' . $taskName . ' has been assingned to you.';
//                                $sendMsgToMbl = smsgatewaycenter_com_Send($AsinToMob, $msgAsinTo, $debug = false);
                                // 
                            }
                        } else {
                            if ($i == 0) {

                                $assignToNextWf = mysqli_query($db_con, "update tbl_doc_assigned_wf set NextTask='0' , task_status='Pending' where task_id='$nextTaskId' and doc_id='$docID' and ticket_id = '$ticket'") or die('Error to move next update' . mysqli_error($db_con));
                            } else {
                                $assignToNextWf = mysqli_query($db_con, "update tbl_doc_assigned_wf set NextTask='2' , task_status='Pending' where task_id='$nextTaskId' and doc_id='$docID' and ticket_id = '$ticket'") or die('Error to move next update' . mysqli_error($db_con));
                            }
                            $rwtaskCheck = mysqli_fetch_assoc($taskCheck);
                            $idnxt = $rwtaskCheck['id'];

                            if ($assignToNextWf) {
                                $update = mysqli_query($db_con, "update tbl_doc_assigned_wf set NextTask='1' , end_date='$endDate' where id='$tid'");
                                assignTask($ticket, $idnxt, $db_con, $projectName);

                                //send sms to mob task asin to user

                                $getNextTaskId = mysqli_query($db_con, "select * from tbl_task_master where task_id = '$nextTaskId'") or die('Error:' . mysqli_error($db_con));
                                $rwgetNextTaskId = mysqli_fetch_assoc($getNextTaskId);
                                $taskName = $rwgetNextTaskId['task_name'];


//                                $getMobNumAsinTo = mysqli_query($db_con, "select * from tbl_user_master where user_id = '$rwgetNextTaskId[assign_user]'") or die('Error:' . mysqli_error($db_con));
//                                $rwgetMobNumAsinTo = mysqli_fetch_assoc($getMobNumAsinTo);
//                                $AsinToMob = $rwgetMobNumAsinTo['phone_no'];
//                                $msgAsinTo = 'New Task With Ticket Id ' . $ticket . ' and Task Name ' . $taskName . ' has been assingned to you.';
//                                $sendMsgToMbl = smsgatewaycenter_com_Send($AsinToMob, $msgAsinTo, $debug = false);
                                // 
                            }
                        }
                    }
                    $i++;
                    //echo 'kkk'.$nextTaskId.$docID; die();
                }
            } else {

                $nextStepIds = array();
                $stepo = mysqli_query($db_con, "select * from tbl_step_master where step_id='$stepId'");
                $rwStepo = mysqli_fetch_assoc($stepo);
                $step = mysqli_query($db_con, "select * from tbl_step_master where workflow_id='$wfid'");
                $s = 0;
                while ($rwStep = mysqli_fetch_assoc($step)) {
                    //echo $rwStep['step_id'].'/'.$rwStep['step_order'].'<br>';
                    //echo $rwStepo['step_id'].'/'.$rwStepo['step_order'];
                    if ($rwStep['step_order'] > $rwStepo['step_order']) {
                        array_push($nextStepIds, $rwStep['step_id']);
                        $s++;
                    }
                    if ($s > 1) {
                        break;
                    }
                }

                //print_r($nextStepIds);

                if (!empty($nextStepIds)) {

                    $i = 0;
                    foreach ($nextStepIds as $nextStepId) {
                        $taskn = mysqli_query($db_con, "select * from tbl_task_master where step_id='$nextStepId' order by task_order asc limit 2");

                        if (mysqli_num_rows($taskn) > 0) {

                            while ($rwTaskn = mysqli_fetch_assoc($taskn)) {

                                /* echo */ $nextTaskId = $rwTaskn['task_id'];

                                if ($rwTaskn['deadline_type'] == 'Days') {
                                    $endDate = date('Y-m-d H:i:s', (strtotime($date) + ($rwTaskn['deadline'] * 24 * 60 * 60)));
                                } else if ($rwTaskn['deadline_type'] == 'Date') {
                                    $endDate = date('Y-m-d H:i:s', (strtotime($date) + ($rwTaskn['deadline'] * 60)));
                                } else {
                                    $endDate = date('Y-m-d H:i:s', (strtotime($date) + ($rwTaskn['deadline'] * 60 * 60)));
                                }

                                $taskCheck = mysqli_query($db_con, "select * from tbl_doc_assigned_wf where task_id='$nextTaskId' and doc_id='$docID' and ticket_id = '$ticket'") or die('Error:' . mysqli_error($db_con));
                                //echo 'helo';  
                                // mysqli_num_rows($taskCheck); 

                                if (mysqli_num_rows($taskCheck) < 1) {
                                    echo 'ok ' . $i . ' ' . $docID;
                                    if ($i == 0) {
                                        if (!empty($docID) && $docID != 0) { //echo $endDate;
                                            $assignToNextWf = mysqli_query($db_con, "insert into tbl_doc_assigned_wf(`id`, `task_id`, `doc_id`, `start_date`, `end_date`, `task_status`, `assign_by`,`NextTask`,`ticket_id`,`task_remarks`)"
                                                            . "values(null,'$nextTaskId','$docID','$date','$endDate','Pending','$assignBy','0','$ticket','$taskRemark')") or die('Error to move next5' . mysqli_error($db_con));
                                        } else {
                                            $assignToNextWf = mysqli_query($db_con, "insert into tbl_doc_assigned_wf(`id`, `task_id`, `start_date`, `end_date`, `task_status`, `assign_by`,`NextTask`,`ticket_id`,`task_remarks`)"
                                                            . "values(null,'$nextTaskId','$date','$endDate','Pending','$assignBy','0','$ticket','$taskRemark')") or die('Error to move next6' . mysqli_error($db_con));
                                        }
                                    } else if ($i == 1) {
                                        if (!empty($docID) && $docID != 0) {
                                            $assignToNextWf = mysqli_query($db_con, "insert into tbl_doc_assigned_wf(`id`, `task_id`, `doc_id`, `start_date`, `end_date`, `task_status`, `assign_by`,`NextTask`,`ticket_id`,`task_remarks`)"
                                                            . "values(null,'$nextTaskId','$docID','$date','$endDate','Pending','$assignBy','2','$ticket','$taskRemark')") or die('Error to move next7' . mysqli_error($db_con));
                                        } else {
                                            $assignToNextWf = mysqli_query($db_con, "insert into tbl_doc_assigned_wf(`id`, `task_id`, `start_date`, `end_date`, `task_status`, `assign_by`,`NextTask`,`ticket_id`,`task_remarks`)"
                                                            . "values(null,'$nextTaskId','$date','$endDate','Pending','$assignBy','0','$ticket','$taskRemark')") or die('Error to move next 8' . mysqli_error($db_con));
                                        }
                                    }
                                    $idnxt = mysqli_insert_id($db_con);
                                    if ($assignToNextWf) {
                                        $update = mysqli_query($db_con, "update tbl_doc_assigned_wf set NextTask='1' , end_date='$endDate' where id='$tid'");
                                        assignTask($ticket, $idnxt, $db_con, $projectName);

                                        //send sms to mob task asin to user

                                        $getNextTaskId = mysqli_query($db_con, "select * from tbl_task_master where task_id = '$nextTaskId'") or die('Error:' . mysqli_error($db_con));
                                        $rwgetNextTaskId = mysqli_fetch_assoc($getNextTaskId);
                                        $taskName = $rwgetNextTaskId['task_name'];

//                                        $getMobNumAsinTo = mysqli_query($db_con, "select * from tbl_user_master where user_id = '$rwgetNextTaskId[assign_user]'") or die('Error:' . mysqli_error($db_con));
//                                        $rwgetMobNumAsinTo = mysqli_fetch_assoc($getMobNumAsinTo);
//                                        $AsinToMob = $rwgetMobNumAsinTo['phone_no'];
//                                        $msgAsinTo = 'New Task With Ticket Id ' . $ticket . ' and Task Name ' . $taskName . ' has been assingned to you.';
//                                        $sendMsgToMbl = smsgatewaycenter_com_Send($AsinToMob, $msgAsinTo, $debug = false);
                                        // 
                                    }
                                } else {
                                    if ($i == 0) {
                                        $assignToNextWf = mysqli_query($db_con, "update tbl_doc_assigned_wf set NextTask='0', task_status='Pending'  where task_id='$nextTaskId' and doc_id='$docID' and ticket_id = '$ticket'");
                                    } else {
                                        $assignToNextWf = mysqli_query($db_con, "update tbl_doc_assigned_wf set NextTask='2' where task_id='$nextTaskId' and doc_id='$docID' and ticket_id = '$ticket'");
                                    }
                                    $rwtaskCheck = mysqli_fetch_assoc($taskCheck);
                                    $idnxt = $rwtaskCheck['id'];
                                    if ($assignToNextWf) {
                                        $update = mysqli_query($db_con, "update tbl_doc_assigned_wf set NextTask='1' , end_date='$endDate' where id='$tid'");
                                        assignTask($ticket, $idnxt, $db_con, $projectName);


                                        //send sms to mob task asin to user
//                                        $getNextTaskId = mysqli_query($db_con, "select * from tbl_task_master where task_id = '$nextTaskId'") or die('Error:' . mysqli_error($db_con));
//                                        $rwgetNextTaskId = mysqli_fetch_assoc($getNextTaskId);
//                                        $taskName = $rwgetNextTaskId['task_name'];
//
//                                        $getMobNumAsinTo = mysqli_query($db_con, "select * from tbl_user_master where user_id = '$rwgetNextTaskId[assign_user]'") or die('Error:' . mysqli_error($db_con));
//                                        $rwgetMobNumAsinTo = mysqli_fetch_assoc($getMobNumAsinTo);
//                                        $AsinToMob = $rwgetMobNumAsinTo['phone_no'];
//
//                                        $msgAsinTo = 'New Task With Ticket Id ' . $ticket . ' and Task Name ' . $taskName . ' has been assingned to you.';
//                                        $sendMsgToMbl = smsgatewaycenter_com_Send($AsinToMob, $msgAsinTo, $debug = false);
                                        // 
                                    }
                                }
                            }
                        }
                        if (mysqli_num_rows($taskn) > 1) {
                            break;
                        }
                        $i++;
                    }
                } else {
                    $assignToNextWf = mysqli_query($db_con, "update tbl_doc_assigned_wf set NextTask='1' where id='$tid'");
                    if ($assignToNextWf) {
                        'doc id' . $docID;
                        if (!empty($docID)) {
                            $updateDocMaster = mysqli_query($db_con, "update tbl_document_master set doc_name=replace(doc_name,'_$wfid','') where doc_id='$docID'");
                            //$update = mysqli_query($db_con, "update tbl_document_master set doc_name=replace(doc_name,'_$wfid','') where substring_index(doc_name,'_',-2)=$docID");
                            $update = mysqli_query($db_con, "update tbl_document_master set doc_name=CONCAT(TRIM(TRAILING '_$wfid' FROM doc_name), '') where substring_index(doc_name,'_',-2)=$docID");
                            //view version in storage after workflow complete
                        }

                        completeTask($ticket, $tid, $wfid, $db_con, $projectName);
                        //send sms to mob
//                        $getMobNum = mysqli_query($db_con, "select * from tbl_user_master where user_id = '$assignBy'") or die('Error:' . mysqli_error($db_con));
//                        $rwgetMobNum = mysqli_fetch_assoc($getMobNum);
//                        $submtByMob = $rwgetMobNum['phone_no'];
//                        $msg = 'Your Ticket Id ' . $ticket . ' is Approved Successfully.';
//                        $sendMsgToMbl = smsgatewaycenter_com_Send($submtByMob, $msg, $debug = false);
                        //
                        return TRUE;
                    }
                }
            }
        }

        //back to prev task when reject
        function backToPrevTsk($db_con, $stepId, $ctaskOrder, $docID, $ctaskID, $assignBy, $tid, $wfid, $ticket, $taskRemark, $date, $projectName) {
            $nextTaskIds = array();

            require_once '../mail-for-anott.php';
            $checkTaskNext = mysqli_query($db_con, "select * from tbl_task_master where step_id='$stepId' order by task_order desc");
            $k = 0;
            while ($rwCheckTask = mysqli_fetch_assoc($checkTaskNext)) {
                if ($rwCheckTask['task_order'] < $ctaskOrder) {
                    array_push($nextTaskIds, $rwCheckTask['task_id']);
                    $k++;
                }
                if ($k > 0) {
                    break;
                }
            }

            if (!empty($nextTaskIds)) {
                foreach ($nextTaskIds as $nextTaskId) {

                    echo '1->' . $nextTaskId;

                    $setflg = mysqli_query($db_con, "update tbl_doc_assigned_wf set NextTask='2' where id = '$tid'") or die('Error:' . mysqli_error($db_con));
                    $updateTaskPrev = mysqli_query($db_con, "UPDATE tbl_doc_assigned_wf SET task_status = 'Approved', action_by = '$_SESSION[cdes_user_id]', action_time = '$date', NextTask = '0' where task_id='$nextTaskId' and ticket_id = '$ticket' ") or die('Error query failed 1 ' . mysqli_error($db_con));
                }
            } else {
                $nextStepIds = array();
                $stepo = mysqli_query($db_con, "select * from tbl_step_master where step_id='$stepId'");
                $rwStepo = mysqli_fetch_assoc($stepo);
                $step = mysqli_query($db_con, "select * from tbl_step_master where workflow_id='$wfid' order by step_order desc");
                $s = 0;
                while ($rwStep = mysqli_fetch_assoc($step)) {
                    //echo $rwStep['step_id'].'/'.$rwStep['step_order'].'<br>';
                    //echo $rwStepo['step_id'].'/'.$rwStepo['step_order'];
                    if ($rwStep['step_order'] < $rwStepo['step_order']) {
                        array_push($nextStepIds, $rwStep['step_id']);
                        $s++;
                    }
                    if ($s > 1) {
                        break;
                    }
                }

                //print_r($nextStepIds);

                if (!empty($nextStepIds)) {


                    foreach ($nextStepIds as $nextStepId) {
                        $taskn = mysqli_query($db_con, "select * from tbl_task_master where step_id='$nextStepId' order by task_order desc limit 1");

                        if (mysqli_num_rows($taskn) > 0) {

                            echo '2->' . $nextTaskId;

                            $getPrevTskId = mysqli_fetch_assoc($taskn);
                            $setflg = mysqli_query($db_con, "update tbl_doc_assigned_wf set NextTask='2' where id = '$tid'") or die('Error to move next update' . mysqli_error($db_con));
                            $updateTaskPrev = mysqli_query($db_con, "UPDATE tbl_doc_assigned_wf SET task_status = 'Approved', action_by = '$_SESSION[cdes_user_id]', action_time = '$date', NextTask = '0' where task_id='$getPrevTskId[task_id]' and ticket_id = '$ticket' ") or die('Error query failed2' . $_SESSION[cdes_user_id] . mysqli_error($db_con));
                        }
                    }
                } else {
                    $setflg = mysqli_query($db_con, "update tbl_doc_assigned_wf set NextTask='2' where id = '$tid'") or die('Error to move next update' . mysqli_error($db_con));
                }
            }
        }
        ?>
        <script>
            $("a#showPic").click(function () {
                var path = $(this).attr('data');
                // alert(id);

                $.post("../application/ajax/displayImage.php", {PATH: path}, function (result, status) {
                    if (status == 'success') {
                        $("#Display").html(result);
                        //alert(result);
                    }
                });
            });

            $("a#video").click(function () {
                var id = $(this).attr('data');

                $.post("../application/ajax/videoformat.php", {vid: id}, function (result, status) {
                    if (status == 'success') {
                        $("#videofor").html(result);
                        //alert(result);

                    }
                });
            });
            $("a#audio").click(function () {
                var id = $(this).attr('data');

                $.post("../application/ajax/audioformat.php", {aid: id}, function (result, status) {
                    if (status == 'success') {
                        $("#foraudio").html(result);
                        //alert(result);

                    }
                });
            });
            jQuery(document).ready(function () {
                $('.selectpicker').selectpicker();

            });
        </script>

        <div id="full-width-modal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myLargeModalLabel"><?php echo $lang['Image_viewer']; ?></h4>
                    </div>
                    <div class="modal-body">
                        <div id="Display"></div>
                    </div>
                    <div class="modal-footer">

                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                    </div>
                </div>
            </div>

        </div>
        <div id="modal-audio" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel"><?php echo $lang['Play/Dwnld_Ado']; ?></h4>
                    </div>
                    <div id="foraudio">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>

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
                        <h4 class="modal-title" id="myModalLabel"><?php echo $lang['Play_video']; ?></h4>
                    </div>
                    <div  id="videofor">


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>

                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

    </body>
    <script src="assets/js/jquery.core.js"></script>
    <script src="assets/plugins/notifyjs/js/notify.js"></script>
    <script src="assets/plugins/notifications/notify-metro.js"></script>    
    <script>
            $(document).ready(function (e) {
                //file button validation
                $("#con-close-modal-act").delegate("#myImage1", "change", function (e) {
                    var size = document.getElementById("myImage1").files[0].size;
                    // alert(size);
                    var name = document.getElementById("myImage1").files[0].name;
                    //alert(lbl);
                    if (name.length < 100)
                    {
                        $.post("../application/ajax/valiadate_client_memory.php", {size: size}, function (result, status) {
                            if (status == 'success') {
                                //$("#stp").html(result);
                                var res = JSON.parse(result);
                                if (res.status == "true")
                                {
                                    // $("#memoryres").html("<span style=color:green>" + res.msg + "</span>");
                                    // $.Notification.autoHideNotify('success', 'top center', 'Success', res.msg)
                                    $("#mem_msg").fadeIn().addClass("mem_msg_success").html(res.msg);
                                } else {
                                    $("#mem_msg").fadeIn().addClass("mem_msg_fail").html(res.msg);
                                    $("#hideOnClick").prop('disabled', true)
                                    //$.Notification.autoHideNotify('warning', 'top center', 'Oops', res.msg)
                                    //$("#memoryres").html("<span style=color:red>" + res.msg + "</span>");
                                }

                            }
                        });
                    } else {
                        var input = $("#myImage1");
                        var fileName = input.val();

                        if (fileName) { // returns true if the string is not empty
                            input.val('');
                        }
                        //$.Notification.autoHideNotify('error', 'top center', 'Error', "File Name Too Long");
                        $("#mem_msg").fadeIn().addClass("mem_msg_fail").html('File Name Too Long');
                    }

                });
            })



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
                // var ids = ["searchText", "submail"];
                var elements = document.getElementsByClassName('translatetext');
                control.makeTransliteratable(elements);


                // Show the transliteration control which can be used to toggle between
                // English and Hindi and also choose other destination language.
                // control.showControl('translControl');

            }
            google.setOnLoadCallback(onLoad);
    </script>

    <script>
        $(document).ready(function () {
            $('<input>').attr({type: 'hidden', value: '<?php echo csrfToken::generate(); ?>', name: 'token'}).appendTo('form');
        });
        function getToken() {

            $.post("application/ajax/common.php", {action: 'getToken'}, function (result, status) {
                if (status == 'success') {
                    var myObj = JSON.parse(result);

                    $("input[name='token']").val(myObj.token);
                }
            });
        }
    </script>
</html>
<?php
//sk@5918

if (isset($_POST['inSaveBtn'], $_POST['token'])) {
    if (!empty($_FILES['insertUpload']['name'])) {
        $file_name = $_FILES['insertUpload']['name'];
        $file_size = $_FILES['insertUpload']['size'];
        $file_type = $_FILES['insertUpload']['type'];
        $file_tmp = $_FILES['insertUpload']['tmp_name'];

        $allowed = ALLOWED_EXTN;
        $allowext = implode(", ", $allowed);
        $ext = pathinfo($file_name, PATHINFO_EXTENSION);
        if (!in_array(strtolower($ext), $allowed)) {

            echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '", "' . str_replace("ext", $allowext, $lang['document_allowed']) . '")</script>';
            exit();
        }
        //$pnum = $_POST['pnum'];
        $pnum = $_POST['fpnum'];
        $fpos = $_POST['fpos'];
        $extn = substr($file_name, strrpos($file_name, '.') + 1);
        $fname = substr($file_name, 0, strrpos($file_name, '.'));
        $fileExtn = substr($file_name, strrpos($file_name, ".") + 1);
        $fextn = strtolower($fileExtn);
        $allowExtn = array('pdf', 'jpg', 'jpeg', 'png', 'gif');
        if (in_array($fextn, $allowExtn)) {

            $nfilename = preg_replace('/[^A-Za-z0-9_\-]/', '', $file_name);

            $filenameEnct = urlencode(base64_encode($nfilename));
            $filenameEnct = preg_replace('/[^A-Za-z0-9_\-]/', '', $filenameEnct);
            $filenameEnct = $filenameEnct . '.' . $extn;
            $filenameEnct = time() . $filenameEnct;

            $foldername = '../temp/';
            if (!dir($foldername)) {
                mkdir($foldername, 0777, TRUE);
            }
            // temporary upload
            $uploaddir = '../temp/fpdf-temp/';
            if (!dir($uploaddir)) {
                mkdir($uploaddir, 0777, TRUE);
            }
			
            $uploaddir = $uploaddir . $filenameEnct;
            $upload = move_uploaded_file($file_tmp, $uploaddir) or die(print_r(error_get_last()));
            if ($upload) {
                if ($fextn == 'pdf') {
                    mergePdf($localPath, $pnum, $uploaddir, $fpos, $id1);
                    //update page count
                }
                if ($fextn == 'jpg' || $fextn == 'jpeg' || $fextn == 'png' || $fextn == 'gif') {
                    mergeImagePdf($localPath, $pnum, $uploaddir, $fpos, $id1);
                }
            }
        } else {
            echo '<script>alert("Only pdf, jpg, png files are allowed.");</script>';
        }
    }
}

//sk@5918
if (isset($_POST['inSaveAsNew'], $_POST['token'])) {
    //set version flag

    $is_version = 1;
    $tktId = $rwTask['ticket_id'];
    //echo $tktId;
    //die;
    if (!empty($_FILES['insertUpload']['name'])) {
        $file_name = $_FILES['insertUpload']['name'];
        $file_size = $_FILES['insertUpload']['size'];
        $file_type = $_FILES['insertUpload']['type'];
        $file_tmp = $_FILES['insertUpload']['tmp_name'];
        //$pnum = $_POST['pnum'];
        $pnum = $_POST['fpnum'];
        $fpos = $_POST['fpos'];
        $extn = substr($file_name, strrpos($file_name, '.') + 1);
        $fname = substr($file_name, 0, strrpos($file_name, '.'));
        $fileExtn = substr($file_name, strrpos($file_name, ".") + 1);
        $fextn = strtolower($fileExtn);
        $allowExtn = array('pdf', 'jpg', 'jpeg', 'png', 'gif');
        if (in_array($fextn, $allowExtn)) {

            $nfilename = preg_replace('/[^A-Za-z0-9_\-]/', '', $file_name);

            $filenameEnct = urlencode(base64_encode($nfilename));
            $filenameEnct = preg_replace('/[^A-Za-z0-9_\-]/', '', $filenameEnct);
            $filenameEnct = $filenameEnct . '.' . $extn;
            $filenameEnct = time() . $filenameEnct;

            $foldername = '../temp/';
            if (!dir($foldername)) {
                mkdir($foldername, 0777, TRUE);
            }
            // temporary upload
            $uploaddir = '../temp/fpdf-temp/';

            if (!dir($uploaddir)) {
                mkdir($uploaddir, 0777, TRUE);
            }

            $uploaddir = $uploaddir . $filenameEnct;
            $upload = move_uploaded_file($file_tmp, $uploaddir) or die(print_r(error_get_last()));
            if ($upload) {

                if ($fextn == 'pdf') {
                    mergePdf($localPath, $pnum, $uploaddir, $fpos, $id1, $is_version);
                    //update page count
                }
                if ($fextn == 'jpg' || $fextn == 'jpeg' || $fextn == 'png' || $fextn == 'gif') {
                    mergeImagePdf($localPath, $pnum, $uploaddir, $fpos, $id1, $is_version);
                }
            }
        } else {
            echo '<script>alert("Only pdf, jpg, png files are allowed.");</script>';
        }
    }
}


if (isset($_POST['delete'], $_POST['token'])) {
    $pnum = $_POST['fpnum'];
    deletePdf($localPath, $pnum, $id1);
}
// delete page and Save asd
if (isset($_POST['delSaveAsNew'])) {
    $is_version = 1;
    $tktId = $rwTask['ticket_id'];
    $pnum = $_POST['fpnum'];
    deletePdf($localPath, $pnum, $id1, $is_version);
}


// sk@71218 : Document Submission after Review.
if (isset($_POST['submit_review'], $_POST['token']) && $chk == 'rw') {

    $revId = base64_decode(urldecode($_GET['reid']));

    //echo $lang;
    // echo 'run';
    $status = createVersions($tfp, $fileName, $filePath, $slid, $doc_extn, $doc_temp_extn, $db_con, $date, $File_Number, $id1, $revId, $host, $lang, $projectName, $doc_desc, $localPath);
    if ($status['status']) {

        // Commit transaction
        mysqli_commit($status['conn']);
        echo "<script>$('.hides').hide();alert('$status[msg]');window.location.href='../reviewintray';</script>";
    } else {
        echo "<script>alert('$status[msg]');</script>";
    }
}

function createVersions($tfp, $fileName, $filePath, $slid, $doc_extn, $doc_temp_extn, $db_con, $date, $File_Number, $id1, $revId, $host, $lang, $projectName, $doc_desc, $localPath) {
    if (!empty($fileName)) {
        if (!empty($filePath)) {
            mysqli_autocommit($db_con, FALSE);
            $slid = $slid . "_" . $id1;
            $docVersionCount = mysqli_query($db_con, "select count(doc_id) as num from tbl_document_reviewer where doc_name='$slid'");
            $lastDoc = mysqli_fetch_assoc($docVersionCount);
            $uploadedBy = $_SESSION['cdes_user_id'];
            $fnameExpload = explode("_", $fileName);
            $fXplode = isset($fnameExpload[2]) && !empty($fnameExpload[2]) ? $fnameExpload[2] + 1 : "1";
            //$fname = $fnameExpload[0]. "_" . ($lastDoc['num']+1);
            $fname = setFileVersionName($fileName, $lastDoc['num']);
            $newfilePath = explode("/", $filePath);
            $indexCount = count($newfilePath);
            unset($newfilePath[$indexCount - 1]);
            $docDesc = json_decode($doc_desc, TRUE);
            $subject = $docDesc['subject'];
            /*
             * Filter File Name remove unknown chars
             */
            $filterename = preg_replace('/[^A-Za-z]/', '', $fname);
            /*
             * Encrypted File Name
             */
            $fileEncName = urlencode(base64_encode($filterename));
            $fileEncName = strtotime($date) . (preg_replace('/[^A-Za-z0-9]/', '', $fileEncName));
            $normalPath = implode("/", $newfilePath) . "/" . $fileEncName . "." . $doc_extn;
            
            $ExtractPath = "../extract-here/" . implode("/", $newfilePath) . "/" . $fileEncName . "." . $doc_extn;

            //echo $ExtractPath;
            //die;
            $fFileByte = copy($localPath, $ExtractPath);
            if ($fFileByte) {
                //sk@81218: remove review copy.
                var_dump(rmdir(substr($localPath, 0, strrpos($localPath, "/")))); //end
                $Fsize = filesize($ExtractPath);
                $Fsize = round(($Fsize / 1000), 2);
                $fetchReview = mysqli_query($db_con, "select * from `tbl_doc_review` where id='$revId'");
                if (mysqli_num_rows($fetchReview) > 0) {
                    $reviewInfo = mysqli_fetch_assoc($fetchReview);
                    $ticketId = $reviewInfo['ticket_id'];
                    $currentOredr = $reviewInfo['review_order'];
                    $maxOrderCurrentTicket = mysqli_query($db_con, "select max(review_order) as lastOrder from `tbl_doc_review` where ticket_id='$ticketId'");
                    $lastOrder = mysqli_fetch_assoc($maxOrderCurrentTicket);
                    $nextOredr = (($currentOredr + 1) < $lastOrder['lastOrder']) ? $currentOredr + 1 : $lastOrder['lastOrder'];
                    $updatenextReview = mysqli_query($db_con, "update tbl_doc_review set next_task='0' where review_order='$nextOredr' and ticket_id='$ticketId'");
                    echo "norder" . $nextOredr;
                    $updateReview = mysqli_query($db_con, "update tbl_doc_review set next_task='1',review_status='1',task_status='Reviewed',action_time='$date' where id='$revId'");
                    $fetchQry = mysqli_query($db_con, "select id from `tbl_doc_review` where review_order='$nextOredr' and ticket_id='$ticketId' and next_task='0'");
                    if (mysqli_num_rows($fetchQry) > 0) {
                        $idins = mysqli_fetch_assoc($fetchQry);
                        $nextOrderAvai = 1;
                    } else {

                        $nextOrderAvai = 0;
                    }
                    //echo 'running';
                    if ($updateReview) {
                        if ($updateReview) {
                            $cols = '';
                            $columns = mysqli_query($db_con, "SHOW COLUMNS FROM tbl_document_reviewer");
                            while ($rwCols = mysqli_fetch_array($columns)) {
                                if ($rwCols['Field'] != 'doc_id') {
                                    if (empty($cols)) {
                                        $cols = '`' . $rwCols['Field'] . '`';
                                    } else {
                                        $cols = $cols . ',`' . $rwCols['Field'] . '`';
                                    }
                                }
                            }
                            $createVrsn = mysqli_query($db_con, "INSERT INTO tbl_document_reviewer($cols) select $cols from tbl_document_reviewer where doc_id='$id1'") or die('Error:' . mysqli_error($db_con));
                            $insertDocID = mysqli_insert_id($db_con);
                            if ($createVrsn) {
                                $updateNew = mysqli_query($db_con, "update tbl_document_reviewer set doc_name='$slid' where doc_id='$insertDocID'");
                                $qry = mysqli_query($db_con, "update tbl_document_reviewer set old_doc_name='$fname', doc_extn='$doc_extn', doc_path='$normalPath', uploaded_by='$uploadedBy', doc_size='$Fsize', noofpages='$tfp', dateposted='$date',File_Number='$File_Number' where doc_id='$id1'");


                                //$qry = mysqli_query($db_con, "insert into `tbl_document_reviewer`(`doc_name`,`old_doc_name`,`doc_extn`,`doc_path`,`uploaded_by`,`doc_size`,`noofpages`,`dateposted`,`File_Number`)values('$slid','$fname','docx','$normalPath','$uploadedBy','$Fsize','1','$date','$File_Number')");
                                if ($qry) {
                                    $qry = mysqli_query($db_con, "INSERT INTO `tbl_reviews_log`(`user_id`,`doc_id`,`action_name`,`start_date`,`end_date`,`system_ip`,`remarks`)values('$_SESSION[cdes_user_id]','$id1','Document Reviewed ','$date','$date','$host','')");
                                    if ($qry) {
                                        if (uploadFileInFtpServer($normalPath, $ExtractPath)) {
											
                                            require '../mail.php';

                                            if ($nextOrderAvai == 1) {
                                                echo "run1";
                                                $mail = assignNextReview($ticketId, $idins['id'], $db_con, $projectName, $subject);
                                            } else {
                                                echo "run2";
                                                $mail = completeReview($ticketId, $db_con, $projectName, $subject);
                                            }

                                            if ($mail) {
                                                return array("status" => True, "msg" => $lang['Document_Review_Successfully'], "conn" => $db_con);
                                            } else {
                                                return array("status" => False, "msg" => "Mail Not Sent");
                                            }
                                        } else {
                                            return array("status" => False, "msg" => $lang['Document_Upload_Failed']);
                                        }
                                    } else {
                                        return array("status" => False, "msg" => $lang['Log_Create_Failed']);
                                    }
                                } else {
                                    return array("status" => False, "msg" => $lang['Failed_To_Register_Document']);
                                }
                            } else {
                                return array("status" => False, "msg" => $lang['Failed_To_Version_Document']);
                            }
                        } else {
                            return array("status" => False, "msg" => $lang['Update_Review_Failed']);
                        }
                    } else {
                        return array("status" => False, "msg" => $lang['Invalid_Order_ID']);
                    }
                } else {
                    return array("status" => False, "msg" => $lang['Invalid_Reviewer']);
                }
            } else {
                return array("status" => False, "msg" => $lang['File_Export_Failed']);
            }
        } else {
            return array("status" => False, "msg" => $lang['Invalid_File_Location']);
        }
    } else {
        return array("status" => False, "msg" => $lang['Invalid_File_Name']);
    }
}

function uploadFileInFtpServer($destinationPath, $sourcePath) {
    
	$fileManager = new fileManager();
	// Connect to file server
	$fileManager->conntFileServer();
	if($fileManager->uploadFile($sourcePath, ROOT_FTP_FOLDER . '/' . $destinationPath)){
		 return true;
	}else{
		 return false;
	}
}

function setFileVersionName($filename, $vno) {
    //check for if file name is with extension
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    // List of Extension to be considered. 
    $allow_exts = array('doc', 'docx', 'pdf');
    if (!empty($ext)) {
        if (in_array($ext, $allow_exts)) {
            // set filename without extension
            $new_filename = basename($filename, ".$ext");
        } else {
            $new_filename = $filename;
            $ext = '';
        }
    } else {
        $new_filename = $filename;
    }
    $exploded_filename = explode('_', $new_filename);
    $new_vno = $vno + 1;
    if ($vno > 0) {
        if (end($exploded_filename) == $vno) {
            array_pop($exploded_filename);
            $new_filename = implode("_", $exploded_filename);
        }
    }
    //echo $new_filename;
    $version_filename = $new_filename . "_" . $new_vno . (!empty($ext) ? '.' . $ext : '');
    //echo $version_filename;
    return $version_filename;
}
?>


<?php
//@dv addsignature if not uploaded
if (isset($_POST['addSign'], $_POST['token'])) {
    $id = $_POST['id'];
    $destination = '../userSign';
    if (!dir($destination)) {
        mkdir($destination, 0777, TRUE);
    }
    $filename = $_FILES['sign']['name'];
    $allowedSize = $_FILES['sign']['size']; // image size in byte

    $extn = substr($filename, strrpos($filename, '.') + 1);
    $extn = strtolower($extn);
    $destination = $destination . '/' . $id . '.' . $extn;
    $destinationpath = 'userSign' . '/' . $id . '.' . $extn;
    $allowedExtn = array('jpg', 'png', 'jpeg');
    if (in_array($extn, $allowedExtn)) {
      $upload = move_uploaded_file($_FILES['sign']['tmp_name'], $destination) or die(print_r(error_get_last()));
       $update = mysqli_query($db_con, "update tbl_user_master set user_sign='$destinationpath' where user_id='$id'") or die('Error : ' . mysqli_error($db_con));
        if ($upload && $update) {
            mysqli_set_charset($db_con, "utf8");
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Sign updated','$date','$host', 'You have changed your sign.')") or die('error : ' . mysqli_error($db_con));
            echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['sign_updated_successfully'] . '");</script>';
        }
        mysqli_close($db_con);
    } else {
        echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['sign_must_be_in_jpg_png'] . '");</script>';
    }
}
?>