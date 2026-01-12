<!DOCTYPE html>
<?php
session_start();
require_once '../sessionstart.php';
require_once '../application/config/database.php';
require('fpdf-function.php');

$pgn=intval($_GET['pn']);
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout.php");
}
//  require_once '../application/pages/head.php';
//for user role
$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

$rwgetRole = mysqli_fetch_assoc($chekUsr);

$uid = base64_decode(urldecode($_GET['id']));
if ($uid != $_SESSION['cdes_user_id']) {
    // header('Location:../index');
}
if ($rwgetRole['pdf_annotation'] != '1') {
    header('Location: ../index');
}
$id1 = base64_decode(urldecode($_GET['id1'])); //doc_id
 
$id = base64_decode(urldecode($_GET['id'])); //doc asign id
$file = mysqli_query($db_con, "select doc_name,filename,doc_path,doc_extn,old_doc_name from tbl_document_master where doc_id='$id1'") or die('error' . mysqli_error($db_con));
$rwFile = mysqli_fetch_assoc($file);
$fileName = $rwFile['old_doc_name'];
$filePath = $rwFile['doc_path'];
$slid=$rwFile['doc_name'];
$doc_extn=$rwFile['doc_extn'];
$task = mysqli_query($db_con, "select * from tbl_doc_assigned_wf where doc_id='$id1' and (task_status='Pending' or task_status='Approved') ");
$rwTask = mysqli_fetch_assoc($task);
//echo "select * from tbl_doc_assigned_wf where doc_id='$id1' and (task_status='Pending' or task_status='Approved') ";
$user = mysqli_query($db_con, "select * from tbl_user_master where user_id='$_SESSION[cdes_user_id]'");
$rwUser = mysqli_fetch_assoc($user);
$userSign = $rwUser['user_sign'];

$storage = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id='$slid'") or die('Error');
$rwStor = mysqli_fetch_assoc($storage);

$folderName="../temp";
if (!dir($folderName)) {
    mkdir($folderName, 777, TRUE);
}
$folderName=$folderName.'/'.$_SESSION['cdes_user_id'];
if (!dir($folderName)) {
    mkdir($folderName, 777, TRUE);
}
$folderName = $folderName.'/'.preg_replace('/[^A-Za-z0-9\-]/', '',$rwStor['sl_name']);//preg_replace('/[^A-Za-z0-9\-]/', '', $string);
if (!dir($folderName)) {
    mkdir($folderName, 777, TRUE);
}
//sk@12918 set path for comment file;
$cmt_file_path=base64_encode($folderName);

$localPath = $folderName . '/' . preg_replace('/[^A-Za-z0-9\-]/', '',$fileName).'.'.$doc_extn;


//$filepath1 = '../../extract-here/'.$filePath;
 //echo substr($filepath1, strrpos($filepath1, "/")+1); 
 //echo $path=substr($filePath,0, strrpos($filePath, "/"));
 //
 
if (!empty($fileName)) {
    require_once '../classes/ftp.php';
    $ftp = new ftp();
    $ftp->conn("$fileserver", "$port", "$ftpUser", "$ftpPwd");
    
   $server_path = ROOT_FTP_FOLDER.'/'.$filePath;
 
    $ftp->get($localPath, $server_path); // download live "$server_path"  to local "$localpath"
    $arr = $ftp->getLogData();
    if ($arr['error'] != "")
       //echo '<h2>Error:</h2>' . implode('<br />', $arr['error']);
    if ($arr['ok'] != "") {
        //echo 'success';
        //header("location:pdf/web/viewer.php?file=$folderName/view_pdf.pdf");
    }
} 

/*
 * file download end
 */


//sk@10918 restrict pagination beyond last page.
if($pgn>totalfpages($localPath)){
$tp=totalfpages($localPath);    
header('location:index.php?id='.base64_encode(urlencode($id)).'&id1='.base64_encode(urlencode($id1)).'&pn='.$tp);
}



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
        <link href="toolbar.css" rel="stylesheet" type="text/css"/>
        <script src="CanvasInput.js" type="text/javascript"></script>
        <script src="../assets/js/jquery.min.js" type="text/javascript"></script>
        <script src="../assets/js/bootstrap.min.js" type="text/javascript"></script>
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
                bottom: 47px;
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
           <div class="pull-left">
               &nbsp;&nbsp;&nbsp;&nbsp; <button data-toggle="modal" data-target="#con-close-modal-insert" class="btn btn-primary btn-sm add" id="insert_file" style="margin-top:4px;" title="Add New Page"><i class="fa fa-plus"></i>  Add
                </button>&nbsp;&nbsp;&nbsp;&nbsp;
            </div>
           <?php if($rwgetRole['delete_page']=='1'){?>
            <div class="pull-left">
                <form method="post">
                    <button type="button" data-target="#con-close-modal-del" title="Delete This Page from PDF File." data-toggle="modal" name="del-btn"  value="Delete"  class="btn btn-danger btn-sm del" id="del-btn" style="margin-top:4px;" ><i class="fa fa-trash-o"></i> Delete
                    </button>
                </form>
            </div>
          <?php } ?>
            

            <div class="text-center">
                <a href="index.php?file=<?php echo $_GET['file']; ?>&id=<?php echo urlencode($_GET['id']); ?>&id1=<?php echo urlencode($_GET['id1']); ?>&pn=<?php
                echo (isset($_GET['pn']) && !empty($_GET['pn'])) ? ($_GET['pn'] - 1) : 1;
                ;
                ?>" id="prev"><?php if(intval($_GET['pn'])!=1){?><i class="fa fa-long-arrow-left" aria-hidden="true"></i><?php } ?></a>
                <span id="page_num"><?php echo (isset($_GET['pn']) && !empty($_GET['pn'])) ? $_GET['pn'] : 1; ?></span> / <span id="page_count"></span>
                <a href="index.php?file=<?php echo $_GET['file']; ?>&id=<?php echo urlencode($_GET['id']); ?>&id1=<?php echo urlencode($_GET['id1']); ?>&pn=<?php
                   echo (isset($_GET['pn']) && !empty($_GET['pn'])) ? ($_GET['pn'] + 1) : 1 + 1;
                   ;
                   ?>" id="next"><?php if(totalfpages($localPath)!=intval($_GET['pn'])){?>
                <i class="fa fa-long-arrow-right" aria-hidden="true"></i><?php } ?></a>
            </div>
                 <?php if ($rwgetRole['file_anot'] == '1') { ?>
                <div class="button-anot">
                    <!--button id="save">Save</button-->
                    <button data-toggle="modal" data-target="#con-close-modal2" class="btn btn-default" id="save" style="margin-bottom: 14px;">Save</button>

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
                    <button id="signature" class="signature" title="Signature Stamp" data="signature"><img src="../<?php echo $userSign; ?>" alt="reject" height="20px" width="70px" id="signImg"></button>
                </div>
<?php } ?>
        </div>
        <div id="comment-wrapper">
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
                                $ext=pathinfo($rwcomment['comment'], PATHINFO_EXTENSION);
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
                                                        <img src="../assets/images/avatar.png" alt="Image">
                                                    <?php } ?>

                                                </div>
                                                <div class="conversation-text">

                                                    <div class="ctext-wrap">
                                                        <span><?php
                                                            echo '<strong>' . $rwUsr['first_name'] . ' ' . $rwUsr['last_name'] . '</strong>' . '<br>';
                                                            if (!empty($rwcomment['comment'])) {
                                                                
                                                            if($ext){?>
                                                            <a href="<?=$rwcomment['comment']?>" target="_blank"><img src="../temp/file.png"></a><?php } else{ echo $rwcomment['comment']; }?>
                                                            <br/>
                                                               
                                                           <?php }
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
                   <form method="post" enctype="multipart/form-data" id="pdf_comment_form">
                    <div class="comment-list-form1">
                        <textarea type="text" cols="40" rows="3" placeholder="Add a Comment" name="comment" id="coment"/></textarea>
                        <input type="hidden" name="cfp" id="cfp" value="<?=$cmt_file_path?>">
                        <input type="hidden" name="tktid"  value="<?php echo $rwTask['ticket_id']; ?>" id="tktid"/>
                        <input type="hidden" name="tskid"  value="<?php echo $rwTask['task_id'] ?>" id="tskid"/>
                        <input type="file" name="comment_file" id="comment_file">
                        <button class="button-all-f btn-sml-txt" onclick="return postPdfComment()" role="button" id="comment_btn">Submit</button>

                    </div>
                    </form>
<?php } ?>

            </div>
        </div>
        <div class="viewer-wrapper">
            <canvas id="the-canvas"></canvas>
            <canvas id="canvas2"></canvas>
        </div>
        <div id="comment-wrapper2">
            <h4 class="text-center" style="color:#000;">Activity Log</h4>
            <?php
            $pdflog = mysqli_query($db_con, "select * from tbl_ezeefile_logs_wf where doc_id='$id1' and user_id!='$_SESSION[cdes_user_id]'");
            
            if(mysqli_num_rows($pdflog)>0){
                
                while ($rwpdflog = mysqli_fetch_assoc($pdflog)) {
                echo '<span>' . $rwpdflog['action_name'] . '<br>';
                echo '<strong> Action By :</strong>' . $rwpdflog['user_name'] . '<br>';
                echo '<strong> Action Time :</strong>' . $rwpdflog['start_date'] . '</span>';
            }
            }
            else{
                echo '<center>No activity</center><hr style="color:#000;">';
            }
            ?>
        </div>
        <script>
            // If absolute URL from the remote server is provided, configure the CORS
            // header on that server.
            //var url = "test2.pdf";

          // var url = "../extract-here/<?php echo $filePath; ?>";
             var filename = "../../extract-here/<?php echo $filePath; ?>";
            
            
            var url = "<?php echo $localPath; ?>";
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
<?php //require_once '../application/pages/footerForjs.php';   ?>

        <div id="con-close-modal2" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog" id="afterClickHide"> 
                <div class="modal-content" > 
                    <div class="modal-header"> 
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                        <h4 class="modal-title">Update File !</h4> 
                    </div> 
                    <img src="../assets/images/anote-wait.gif" alt="load" id="anotWt" style="display: none;"/>
                    <form method="post">
                        <div class="modal-body">
                            <p style="color: red;">Do you want to overwrite existing file? or want to save as new version.</p>
                        </div>
                        <div class="modal-footer"> 
                            <input value="1" name="confrm" type="hidden" >
                            <button type="button"  class="btn btn-success"  id="save1" data="1">Overwrite</button>
                            <button type="button" class="btn btn-default waves-effect" id="save1" data="2">Save as New</button>

                        </div>
                    </form>

                </div> 
            </div>
        </div>

        //sk@4918 : Insert pdf file popup
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
                            <label>Select Page No. :</label>
                            <select name="fpnum" id="fpnum">
                            <?php
                            $tfp=totalfpages($localPath);
                            for($i=1;$i<=$tfp;$i++){ ?>
                            <option <?=($i==intval($_GET['pn']) ? 'selected="selected"' : '')?> value="<?=$i?>"><?=$i?></option>
                            <?php }?>    
                            </select>
                            <label>Position : </label>
                            <select name="fpos" id="fpos">
                            <option value="a">After</option>
                            <option value="b">Before</option>
                            
                                
                            </select><br>
                             <label>Upload File</label>
                             <input type="hidden" name="tfp" id="tfp" value="3" class="filestyle">
                            <input type="file" name="insertUpload" id="insertUpload" class="filestyle">
                            <input type="hidden" name="pnum" id="pnum" value="<?=$_GET['pn']?>" class="filestyle">
                        </div>
                        <div class="modal-footer"> 
                            <input value="1" name="confrm" type="hidden" >
                            <button type="submit"  class="btn btn-success" value="Submit"  id="inSaveBtn" name="inSaveBtn">Submit</button>
                            <button type="reset" data-dismiss="modal" class="btn btn-default waves-effect">Cancel</button>

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
                            $tfp=totalfpages($localPath);;
                            for($i=1;$i<=$tfp;$i++){ ?>
                            <option <?=($i==intval($_GET['pn']) ? 'selected="selected"' : '')?> value="<?=$i?>"><?=$i?></option>
                            <?php }?>    
                            </select>
                        </div>
                        <div class="modal-footer"> 
                            <input value="1" name="confrm" type="hidden" >
                            <button type="submit" onclick="return delConfirm()"  class="btn btn-success" value="Delete"  id="delete" name="delete">Delete</button>
                            <button type="reset" data-dismiss="modal" class="btn btn-default waves-effect">Cancel</button>

                        </div>
                    </form>

                </div> 
            </div>
        </div>

        <!--show wait gif-->
        <div style=" display: none; background: rgba(0,0,0,0.5); width: 100%; z-index: 2000; position: fixed; top:0;" id="wait">;

            <img src="../assets/images/proceed.gif" alt="load"  style=" margin-left: 48%; margin-top: 250px; width: 100px; height:100px; position: fixed; "/>
        </div> 
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

$getCordnate = mysqli_query($db_con, "select * from tbl_anotation where doc_id = '$id1' and page_no=$_GET[pn]") or die('Error:' . mysqli_error($db_con));
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

            $("button#save1").click(function () {
                
               
                var conf = $(this).attr('data');
                $.post("../application/ajax/annotation.php", {X1: x1, Y1: y1, X2: x2, Y2: y2, pageNo: pageN, TYPE: typed, TEXT: text, FILEPATH: filename, CANWIDTH: canWidth, CANHEIGHT: canHight, DOCASIGNID: docAsinId, TKTID: tkt, CONFIRM: conf, DOCID: docId}, function (result, status) {
                    if (status == 'success') {
                        //$("#viewer").html(result);
                        //$('#waitFr').hide();
                        
                        
                        alert('Saved!! please use ctrl+f5 to view annotations.');
                        //alert(result);
                        if (conf == 1) {
                            window.location.reload(false);
                            //location.href='index.php?<?php echo"id=$_GET[id]&id1=$_GET[id1]&pn=$_GET[pn]"; ?>';
                        } else {
                            window.open('index.php?' + result, '_self');
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
                
        function postPdfComment(){ 
             event.preventDefault();
              $.ajax({
              url:"../application/ajax/comentOnPdf.php",
              //dataType:"json",
              type:"POST",
              data:new FormData($("#pdf_comment_form")[0]),
              contentType:false,
              processData:false,
              beforeSend: function()
              {
                  $('#comment_btn').html('Wait...').prop('disabled',true);
              },
              success:function(result)
              {
                  //if(r.status=='success')
                  //{
                  $("#coment").val("");
                  $("#comment_file").val("");
                  $('#comment_btn').html('Submit').prop('disabled',false);
                  $("#comentAdd").html(result);  
                      
                     
                      
                  //}
              }
            });
        }

        </script>


        
        <script>
            function delConfirm() {
                var txt;
                //var r = confirm("The current opened page (Page no. <?= intval($_GET['pn']) ?>) from file will be deleted. Are you sure you want to delete ?");
               var r = confirm("Page no. "+$('#delfpnum').val()+" from file will be deleted. Are you sure you want to delete ?");
                if (r == true) {
                    return true;
                } else {
                    return false;
                }
                document.getElementById("demo").innerHTML = txt;
            }
            
            window.onbeforeunload = function () {
                      
            $.post("../application/ajax/removeTempFiles.php", {filepath:url}, function (result) {

            });
            return;
        };
        </script>

    </body>
</html>
<?php

//sk@5918
if (isset($_POST['inSaveBtn'])) {
    if (!empty($_FILES['insertUpload']['name'])) {
        $file_name = $_FILES['insertUpload']['name'];
        $file_size = $_FILES['insertUpload']['size'];
        $file_type = $_FILES['insertUpload']['type'];
        $file_tmp = $_FILES['insertUpload']['tmp_name'];
        //$pnum = $_POST['pnum'];
        $pnum=$_POST['fpnum'];
        $fpos=$_POST['fpos'];
        $extn = substr($file_name, strrpos($file_name, '.') + 1);
        $fname = substr($file_name, 0, strrpos($file_name, '.'));
        $fileExtn = substr($file_name, strrpos($file_name, ".") + 1);
        $fextn = strtolower($fileExtn);
        $allowExtn = array('pdf', 'jpg', 'jpeg', 'png', 'gif');
        if(in_array($fextn, $allowExtn)){
        
            $nfilename = preg_replace('/[^A-Za-z0-9_\-]/', '', $file_name);

            $filenameEnct = urlencode(base64_encode($nfilename));
            $filenameEnct = preg_replace('/[^A-Za-z0-9_\-]/', '', $filenameEnct);
            $filenameEnct = $filenameEnct . '.' . $extn;
            $filenameEnct = time() . $filenameEnct;

            // temporary upload
            $uploaddir = '../temp/fpdf-temp/';

            $uploaddir = $uploaddir . $filenameEnct;
            $upload = move_uploaded_file($file_tmp, $uploaddir) or die(print_r(error_get_last()));
            if ($upload) {

                if ($fextn == 'pdf') {
                    mergePdf($localPath,$pnum,$uploaddir,$fpos);
                }
                if ($fextn == 'jpg' || $fextn == 'jpeg' || $fextn == 'png' || $fextn == 'gif') {
                    mergeImagePdf($localPath,$pnum,$uploaddir,$fpos);
                }
            }
        }
        else{
            echo '<script>alert("Only pdf, jpg, png files are allowed.");</script>';
        }
        
    }
}


if (isset($_POST['delete'])) {
    $pnum=$_POST['fpnum'];
    deletePdf($localPath,$pnum);
}
?>