<!DOCTYPE html>
<?php
//ob_start();
//session_start();
require_once '../sessionstart.php';
require_once '../application/config/database.php';
require_once('fpdf-function.php');
require_once '../application/pages/sendSms.php';
require_once '../application/pages/function.php';
require_once '../classes/fileManager.php';
error_reporting(0);
$pgn = intval($_GET['pn']);
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../logout.php");
}



//error_reporting(E_ALL);

//require_once '../application/pages/head.php';
//for user role
$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

$rwgetRole = mysqli_fetch_assoc($chekUsr);

$langDetail = mysqli_query($db_con, "select * from tbl_language where lang_name='" . $_SESSION['lang'] . "'") or die('Error : ' . mysqli_error($db_con));
$langDetail = mysqli_fetch_assoc($langDetail);


if ($_SESSION['cdes_user_id']=="") {
    header('Location:../index');
}
if ($rwgetRole['pdf_annotation'] != '1') {
    //header('Location: ../index');
}
$id1 = base64_decode(urldecode($_GET['id1'])); //doc_id
$chk="st";
// $tid = base64_decode(urldecode($_GET['tid'])); //task id

// $tid = base64_decode(urldecode($_GET['tid'])); //task id

// $chk = strtolower(filter_var($_GET['chk'], FILTER_SANITIZE_STRING)); // check for review
// $reid = base64_decode(urldecode($_GET['reid'])); //Review Id

mysqli_set_charset($db_con, "utf8");
//sk@71218: set table name dynamically for review process.

// Annotation Condition    
    $annot_cond = " and is_inreview='0'";
// Review log condition
    $rwlog_cond = " and in_review='1'";
    $file = mysqli_query($db_con, "select doc_name,filename,doc_path,doc_extn,old_doc_name from tbl_document_master where doc_id='$id1'") or die('error' . mysqli_error($db_con));


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

$newpath=change_Pdf_Version11($localPath);
if($newpath!=$localPath){
    rename($newpath, $localPath);
}
//die('here');
//echo $localPath;
//die('ok');


/*
 * file download end
 */

//@sk(311018): Clean temprary given folder. And print function output to check for errors.    
    cleanFolder('../temp/fpdf-temp/');

//sk@10918 restrict pagination beyond last page.

if (file_exists($localPath)) {  
    
    if (isset($_POST['add_pageno'])) {
        // Set Page No.
        setPageNo($localPath, $did);
    }
//@sk(311018): Clean temprary given folder. And print function output to check for errors.    
   // cleanFolder('../temp/fpdf-temp/');


    if ($pgn > totalfpages($localPath)) {

    
        $tp = totalfpages($localPath);

      
        $msg= ($_GET['msg'])?"&msg=d":"";

        header('location:add-delete-page.php?id1=' . $_GET['id1'] .'&pn=' . $tp.$msg);
        
    }

} else {
    // if ($chk == 'rw') {
    //     header('location:../reviewintray');
    // } else {
    //     header('location:../myTask');
    // }
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
        <link href="../assets/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
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
                width: 260px;
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
            #editkeyword-wrapper{
                position: fixed;
                left: 0;
                top: 45px;
                bottom: 0;
                overflow: auto;
                width: 260px;
                background-color: #eaeaea;
                border-left: 1px solid #d0d0d0; 
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
                    

                    <div class="pull-left">
                        <form method="post">
                            <?php if ($rwgetRole['delete_page'] == '1') { ?>
                                <button type="button" data-target="#con-close-modal-del" title="<?= $lang['Delete'] ?>" data-toggle="modal" name="del-btn"  value="Delete"  class="btn btn-danger btn-sm del m-l-5" id="del-btn" style="margin-top:4px;" ><i class="fa fa-trash-o"></i> <?= $lang['Delete']; ?>
                                </button>
                            <?php } ?>
                           
                        </form>
                    </div>
                    
                    <button id="zoomOut" onclick="zoomin()" class="btn btn-primary btn-sm" title="Zoom In">
                        <i class="fa fa-plus-circle"></i>
                    </button>
                    <button id="zoomOut" onclick="zoomout()" class="btn btn-primary btn-sm" title="Zoom Out">
                        <i class="fa fa-minus-circle"></i>
                    </button>
                </div>
                <div class="col-sm-1">
                    <a href="add-delete-page?id1=<?php echo urlencode($_GET['id1']); ?>&pn=<?php
                    echo (isset($_GET['pn']) && !empty($_GET['pn'])) ? ($_GET['pn'] - 1) : 1;
                    ?><?= $chk_review ?>" id="prev"><?php if (intval($_GET['pn']) != 1) { ?><i class="fa fa-long-arrow-left" aria-hidden="true"></i><?php } ?></a>
                    <span id="page_num"><?php echo (isset($_GET['pn']) && !empty($_GET['pn'])) ? $_GET['pn'] : 1; ?></span> / <span id="page_count"></span>
                    <a href="add-delete-page?id1=<?php echo urlencode($_GET['id1']); ?>&pn=<?php
                    echo (isset($_GET['pn']) && !empty($_GET['pn'])) ? ($_GET['pn'] + 1) : 1 + 1;
                    ?><?= $chk_review ?>" id="next"><?php if (totalfpages($localPath) != intval($_GET['pn'])) { ?>
                            <i class="fa fa-long-arrow-right" aria-hidden="true"></i><?php } ?></a>
                </div>
                
            </div>
        </div>
        <div class="viewer-wrapper">
            <canvas id="the-canvas"></canvas>
            <canvas id="canvas2"></canvas>
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
                            <input type="hidden" name="tktid"  value="<?php //echo $rwTask['ticket_id']; ?>"/>
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
                                // Show editkeyword box.
                                $("#editkeyword").click(function (e) {
                                    $('#editkeyword-wrapper').toggle();
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
        <!------- sk@7918----->
        <script>

            function postPdfComment(event) {
                event.preventDefault();
                var formData = new FormData($("#pdf_comment_form")[0]);
                //var formData=new FormData($("#pdf_comment_form")[0]);
                formData.append('comment', tinymce.get("editor").getContent());
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
        <script src="../assets/moment-with-locales.js"></script>
        <script type="text/javascript" src="../assets/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
        <!-- Sweet-Alert  -->
        <script src="../assets/plugins/sweetalert2/sweetalert2.min.js"></script>
        <script src="../assets/pages/jquery.sweet-alert.init.js"></script>
        <script type="text/javascript">

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
                } else if ($(this).attr('id') == 'app' || $(this).attr('id') == 'processed' || $(this).attr('id') == 'done') {
                    $("#hidden_div").show();

                    $("#hidden_note").show();
                }
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
    <script type="text/javascript">

        $(".selectpicker").selectpicker();
        $(document).ready(function () {
            $('.datetimepicker').datetimepicker({
                //language:  'fr',
                weekStart: 1,
                todayBtn: 1,
                autoclose: 1,
                todayHighlight: 1,
                startView: 2,
                forceParse: 0,
                showMeridian: 1,
                //startDate: '+0d',
                format: "dd-mm-yyyy H:ii"
            });
            $('.datepicker').datetimepicker({
                minView: 2,
                autoclose: 1,
                format: "dd-mm-yyyy"
            });
            $(".datetimepicker").keydown(function (e) {
                e.preventDefault();

            });
            $(".datepicker").keydown(function (e) {
                e.preventDefault();

            });
        });
        function setCheckboxValue(row, fieldname) {

            var metadatavalues = $("input[name='checkbox" + row + "[]']:checked").map(function () {
                return this.value;
            }).get().join(",");
            $("." + fieldname).val(metadatavalues);

        }
        // for binary metadata value
        (function ($) {
            $.fn.inputFilter = function (inputFilter) {
                return this.on("input keydown keyup mousedown mouseup select contextmenu drop", function () {
                    if (inputFilter(this.value)) {
                        this.oldValue = this.value;
                        this.oldSelectionStart = this.selectionStart;
                        this.oldSelectionEnd = this.selectionEnd;
                    } else {
                        this.value = "";
                    }
                });
            };
        }(jQuery));
        $(".intLimit").inputFilter(function (value) {
            return /^\d*$/.test(value) && (value === "" || parseInt(value) <= 1);
        });
        $(".bit").keyup(function () {
            var bitVal = $(this).val();
            if (bitVal == 0 || bitVal == 1)
            {

                $(".nextBtn").removeAttr("disabled", "disabled");
                $("#errormsg").html("");
            } else {
                $(".nextBtn").attr("disabled", "disabled");
                $("#errormsg").html("Invalid Value!Value should be 0 or 1");
            }
        })
        $('.char').keyup(function ()
        {
            var GrpNme = $(this).val();
            re = /[`12345679890~!@#$%^*|+?;:'",.<>\{\}\[\]]/gi;
            var isSplChar = re.test(GrpNme);
            if (isSplChar)
            {
                var no_spl_char = GrpNme.replace(/[`~!@#$%^*|0-9+?;:'",.<>\{\}\[\]]/gi, '');
                $(this).val(no_spl_char);
            }
        });
        $('.char').bind(function () {
            $(this).val($(this).val().replace(/[<>]/g, ""))
        });

        $("input.intvl").keypress(function (e) {
            //if the letter is not digit then display error and don't type anything
            if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                //display error message
                return false;
            }
            str = $(this).val();
            str = str.split(".").length - 1;
            if (str > 0 && e.which == 46) {
                return false;
            }
        });

        $('.varchar').keyup(function ()
        {
            var groupName = $(this).val();
            re = /[`1234567890~!@#$%^*|+?'"<>\{\}\[\]]/gi;
            var isSplChar = re.test(groupName);
            if (isSplChar)
            {
                var no_spl_char = groupName.replace(/[`~!@#$%^*|+?'"<>\{\}\[\]]/gi, '');
                $(this).val(no_spl_char);
            }
        });
        $('.varchar').bind(function () {
            $(this).val($(this).val().replace(/[<>]/g, ""))
        });
        function zoomin() {
            var myImg = document.getElementById("the-canvas");
            var currWidth = myImg.clientWidth;
            if (currWidth == 500) {
                alert("Maximum zoom-in level reached.");
            } else {
                myImg.style.width = (currWidth + 50) + "px";
            }
        }
        function zoomout() {
            var myImg = document.getElementById("the-canvas");
            var currWidth = myImg.clientWidth;
            if (currWidth == 50) {
                alert("Maximum zoom-out level reached.");
            } else {
                myImg.style.width = (currWidth - 50) + "px";
            }
        }
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
    // print_r($_REQUEST);
    // print_r($_FILES);
    // die("rrrr");
    if (!empty($_FILES['insertUpload']['name'])) {
        $file_name = $_FILES['insertUpload']['name'];
        $file_size = $_FILES['insertUpload']['size'];
        $file_type = $_FILES['insertUpload']['type'];
        $file_tmp = $_FILES['insertUpload']['tmp_name'];

//        $allowed = ALLOWED_EXTN;
//        $allowext = implode(", ", $allowed);
//        $ext = pathinfo($file_name, PATHINFO_EXTENSION);
//        if (!in_array(strtolower($ext), $allowed)) {
//
//            echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '", "' . str_replace("ext", $allowext, $lang['document_allowed']) . '")</script>';
//            exit();
//        }
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
                $newpath=change_Pdf_Version11($uploaddir);
if($newpath!=$uploaddir){
    rename($newpath, $uploaddir);
}
                if ($fextn == 'pdf') {
                    mergePdf($localPath, $pnum, $uploaddir, $fpos, $id1);
                    //update page count
                    //die("okk");
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

if(isset($_GET['msg'])){

   $url = "add-delete-page?id1=".$_GET['id1']."&pn=1";
   
   // if version file than redirect to storage
   if (strpos($slid, '_') !== false) {
		$slidarray = explode("_", $slid);
		$url = '../storageFiles?id='. urlencode(base64_encode($slidarray[0]));
	}

    if($_GET['msg']=="a"){

    echo '<script>taskSuccess("' . $url . '","' . $lang['page_added_successfully'] . '");</script>';

    }else if($_GET['msg']=="d"){

     //echo '<script>taskSuccess("' . $url . '","' . $lang['page_deleted_successfully'] . '");</script>';
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
                $newpath=change_Pdf_Version11($uploaddir);
if($newpath!=$uploaddir){
    rename($newpath, $uploaddir);
}

                if ($fextn == 'pdf') {
                    mergePdf($localPath, $pnum, $uploaddir, $fpos, $id1, $is_version);
                    //update page count
                }
                if ($fextn == 'jpg' || $fextn == 'jpeg' || $fextn == 'png' || $fextn == 'gif') {
                    mergeImagePdf($localPath, $pnum, $uploaddir, $fpos, $id1, $is_version);
                }

             //echo '<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['page_added_successfully'] . '");</script>';
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
	//error_reporting(E_ALL);
    deletePdf($localPath, $pnum, $id1, $is_version);

   //echo "<script> alert('Page Deleted Successfully'); </script>";

     
}





// sk@71218 : Document Submission after Review.
if (isset($_POST['submit_review'], $_POST['token']) && $chk == 'rw') {

    $revId = base64_decode(urldecode($_GET['reid']));

    //echo $lang;
    // echo 'run';
    $status = createVersions($tfp, $fileName, $filePath, $slid, $doc_extn, $doc_temp_extn, $db_con, $date, $File_Number, $fileserver, $port, $ftpUser, $ftpPwd, $id1, $revId, $host, $lang, $projectName, $doc_desc, $localPath);
    if ($status['status']) {

        // Commit transaction
        mysqli_commit($status['conn']);
        echo "<script>$('.hides').hide();alert('$status[msg]');window.location.href='../reviewintray';</script>";
    } else {
        echo "<script>alert('$status[msg]');</script>";
    }
}

function createVersions($tfp, $fileName, $filePath, $slid, $doc_extn, $doc_temp_extn, $db_con, $date, $File_Number, $fileserver, $port, $ftpUser, $ftpPwd, $id1, $revId, $host, $lang, $projectName, $doc_desc, $localPath) {
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
                    //echo "norder" . $nextOredr;
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
                                         
                                                $mail = assignNextReview($ticketId, $idins['id'], $db_con, $projectName, $subject);
                                            } else {
                                     
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
