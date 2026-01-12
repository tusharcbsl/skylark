<!DOCTYPE html>
<?php
require '../sessionstart.php';
require_once '../application/config/database.php';
require_once '../loginvalidate.php';
require_once '../application/pages/function.php';
require_once '../classes/fileManager.php';
//  require_once '../application/pages/head.php';
//for user role
$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

$rwgetRole = mysqli_fetch_assoc($chekUsr);
$uid = base64_decode(urldecode($_GET['id']));
if ($uid != $_SESSION['cdes_user_id']) {
    header('Location:../index');
}
if ($rwgetRole['pdf_annotation'] != '1') {
    header('Location: ../index.php');
}
 $id1 = base64_decode(urldecode($_GET['id1'])); //doc_id
//$id = base64_decode(urldecode($_GET['id']));  //doc asign id
if($_GET['chk']=="rw")
{
     $file = mysqli_query($db_con, "select doc_name, doc_path, doc_extn, old_doc_name from tbl_document_reviewer where doc_id='$id1'") or die('error' . mysqli_error($db_con));   

}
 else {
  $file = mysqli_query($db_con, "select doc_name, filename, doc_path, doc_extn, old_doc_name from tbl_document_master where doc_id='$id1'") or die('error' . mysqli_error($db_con));   
   
}

$rwFile = mysqli_fetch_assoc($file);
$fileName = $rwFile['old_doc_name'];
$filePath = $rwFile['doc_path'];
$slid=$rwFile['doc_name'];
$doc_extn=$rwFile['doc_extn'];

$user = mysqli_query($db_con, "select * from tbl_user_master where user_id='$_SESSION[cdes_user_id]'");
$rwUser = mysqli_fetch_assoc($user);
$userSign = $rwUser['user_sign'];

$fileManager = new fileManager();
// Connect to file server
$fileManager->conntFileServer();
$localPath = $fileManager->getFile($rwFile);

/*
 * file download end
 */
?>

<html>
    <head>
        <!--script src="//mozilla.github.io/pdf.js/build/pdf.js"></script--> <!--pdf viewer-->
        <script type="text/javascript" src="pdf.js"></script>
        <!--script type="text/javascript" src="http://code.jquery.com/jquery.min.js"></script> -->
        <!--script type="text/javascript" src="jquery.min.js"></script-->
        <link rel="shortcut icon" href="../assets/images/favicon_1.ico">
        <link rel="icon" type="image/png" href="assets/images/favicons//favicon-32x32.png" sizes="32x32">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
        <link href="../assets/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="toolbar.css" rel="stylesheet" type="text/css"/>
        <script src="CanvasInput.js" type="text/javascript"></script>
        <script src="../assets/js/jquery.min.js" type="text/javascript"></script>
        <script src="../assets/js/bootstrap.min.js" type="text/javascript"></script>
        <style type="text/css">

            #the-canvas {
                border:1px solid black;
            }

            body {
                background-color: #eee;
                font-family: sans-serif;
                margin: 0;
            }
            #comment-wrapper {
                position: fixed;
                left: 0%;
                top: 45px;
                right: 0;
                bottom: 0;
                overflow: auto;
                width: 250px;
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
            <div class="text-center">
                <a href="index.php?file=<?php echo $_GET['file']; ?>&id=<?php echo urlencode($_GET['id']); ?>&id1=<?php echo urlencode($_GET['id1']); ?>&pn=<?php
                echo (isset($_GET['pn']) && !empty($_GET['pn'])) ? ($_GET['pn'] - 1) : 1;
                ;
                ?>" id="prev"><i class="fa fa-long-arrow-left" aria-hidden="true"></i></a>
                <span id="page_num"><?php echo (isset($_GET['pn']) && !empty($_GET['pn'])) ? $_GET['pn'] : 1; ?></span> / <span id="page_count"></span>
                <a href="index.php?file=<?php echo $_GET['file']; ?>&id=<?php echo urlencode($_GET['id']); ?>&id1=<?php echo urlencode($_GET['id1']); ?>&pn=<?php
                echo (isset($_GET['pn']) && !empty($_GET['pn'])) ? ($_GET['pn'] + 1) : 1 + 1;
                ;
                ?>" id="next"><i class="fa fa-long-arrow-right" aria-hidden="true"></i></a>
            </div>
            <?php if ($rwgetRole['file_anot'] == '1') { ?>
                <div class="button-anot">
                    <a  class="btn btn-default" <?php
                    if ($rwgetRole['pdf_print'] == '1') {
                        echo 'onclick="PrintElem(\'#printid\')"';
                    } else {
                        echo'disabled';
                    }
                    ?>><i class="fa fa-print"></i></a>
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
                        // echo '<script>alert("'.$id1.'")</script>';
                        $getTiketid = mysqli_query($db_con, "select distinct ticket_id from tbl_doc_assigned_wf where doc_id='$id1' order by id desc") or die('Error: ' . mysqli_error($db_con));
                        $rwgetTiketid = mysqli_fetch_assoc($getTiketid);
                        //get workflow name
                        $getWfId = mysqli_query($db_con, "select ttm.workflow_id from tbl_doc_assigned_wf daw inner join tbl_task_master ttm on daw.task_id = ttm.task_id where daw.ticket_id='$rwgetTiketid[ticket_id]'");
                        $rwgetWfId = mysqli_fetch_assoc($getWfId);

                        $getWfName = mysqli_query($db_con, "select workflow_name from tbl_workflow_master where workflow_id='$rwgetWfId[workflow_id]'");
                        $rwgetWfName = mysqli_fetch_assoc($getWfName);

                        //
                        $proclist = mysqli_query($db_con, "select * from tbl_doc_assigned_wf where ticket_id='$rwgetTiketid[ticket_id]'");

                        $rwProclist = mysqli_fetch_assoc($proclist);

                        $comment = mysqli_query($db_con, "select * from tbl_task_comment where tickt_id= '$rwProclist[ticket_id]' order by comment_time desc");

                        //workflow name upon comment
                        //echo '<span style="color: white;"><strong>Workflow Name: </strong>' . $rwgetWfName['workflow_name'] . '</span>';

                        if (mysqli_num_rows($comment) > 0) {
                            while ($rwcomment = mysqli_fetch_assoc($comment)) {
                             $ext = pathinfo($rwcomment['comment'], PATHINFO_EXTENSION);   
                                $usr = mysqli_query($db_con, "select first_name, last_name,profile_picture from tbl_user_master where user_id='$rwcomment[user_id]'");
                                $rwUsr = mysqli_fetch_assoc($usr);
                                ?>
                                <div class="comment-list-item">   
                                    <li class="clearfix">
                                        <div class="conversation-text">

                                            <div class="ctext-wrap">
                                                <span style="float:left;">   <?php 
                                                if(!empty($rwcomment['comment'])){
                                                                echo '<strong>Comment: </strong>';
                                                                }
                                                                if ($ext){
                                                                ?>
                                                                    <a href="../anott/view?cid=<?= urlencode(base64_encode($rwcomment['id'])) ?>" target="_blank"><i class="fa fa-file cmt-file"></i></a><br><?php } else {
                                                    echo $rwcomment['comment'].'<br>';
                                                   } 
                                                                if(!empty($rwcomment['task_status'])){
                                                                echo '<strong>Action: </strong>' . $rwcomment['task_status'].'<br>';
                                                                }
                                                                ?> </span> <div class="clearfix"></div>
                                                <span style="float:right;">
                                                    <i><?php echo $rwUsr['first_name'] . ' ' . $rwUsr['last_name']; ?></i>
                                                    <br/>
                                                    <?php echo date("j F, Y, H:i", strtotime($rwcomment['comment_time'])); ?></span>
                                            </div>
                                        </div>
                                    </li>
                                </div>

                                <?php
                            }
                        } else {
                            ?>
                            <div class="comment-list-item">No comments</div>
                            <?php
                        }
                        ?>
                    </div>
                    <!--/div-->
                </div>
                <?php if ($rwgetRole['file_coment'] == '1') { ?>
                    <!--  <div class="comment-list-form1">
                          <input type="text" placeholder="Add a Comment" name="comment" id="coment"/>
                          <input type="hidden"  value="<?php //echo $rwTask['ticket_id'];         ?>" id="tktid"/>
                          <input type="hidden"  value="<?php //echo $rwTask['task_id']        ?>" id="tskid"/>

                      </div> -->
                <?php } ?>
                <?php
                // if(isset($_POST['comment']) && !empty($_POST['comment'])){
                // echo " <script>alert('helo');</script>";
                // }
                ?>
            </div>
        </div>
        <div class="viewer-wrapper" id="printid">
            <canvas id="the-canvas"></canvas>
            <canvas id="canvas2"></canvas>
        </div>
        <script type="text/javascript">
            function PrintElem(elem)
            {
                window.print();
                //Popup($(elem).html());
            }

            function Popup(data)
            {
                var mywindow = window.open('', 'new div', 'height=800,width=1200');
                mywindow.document.write('<html><head><title>QR Code</title>');
                /*optional stylesheet*/ //mywindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
                mywindow.document.write('</head><body >');
                mywindow.document.write(data);
                mywindow.document.write('</body></html>');
                mywindow.print();
                mywindow.close();
                return true;
            }
        </script>
        <script>
            // If absolute URL from the remote server is provided, configure the CORS
            // header on that server.
            //var url = "test2.pdf";
            document.title = "<?php echo $fileName; ?>";
            var url = "<?php echo $localPath; ?>";

            var filename = "<?php echo $localPath; ?>";

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

        <!--show wait gif-->
        <div style=" display: none; background: rgba(0,0,0,0.5); width: 100%; z-index: 2000; position: fixed; top:0;" id="wait">;

            <img src="../assets/images/proceed.gif" alt="load"  style=" margin-left: 35%; margin-top: 100px; width: 30%; position: fixed; "/>
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
                pageN.push(pageNum);
                var type1 = $(":button.active").attr("data");
                typed.push(type1);
                x1.push(strtX);
                y1.push(strtY);
                x2.push(rect1.w);
                y2.push(rect1.h);

                ctx.strokeStyle = "rgba(0,128,0,1)";

                ctx.strokeRect(rect1.startX, rect1.startY, rect1.w, rect1.h);
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
                var type1 = $(":button.active").attr("data");
                typed.push(type1);
                x1.push(strtX);
                y1.push(strtY);
                x2.push(rect1.w);
                y2.push(rect1.h);

                ctx.strokeStyle = "rgba(0,0,255,1)";

                ctx.strokeRect(rect1.startX, rect1.startY, rect1.w, rect1.h);
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
                var type1 = $(":button.active").attr("data");
                typed.push(type1);
                x1.push(strtX);
                y1.push(strtY);
                x2.push(rect1.w);
                y2.push(rect1.h);

                ctx.strokeStyle = "rgba(165,42,42,1)";

                ctx.strokeRect(rect1.startX, rect1.startY, rect1.w, rect1.h);
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
                            var rct<?php echo $i; ?> = new ToolTip(canvas, region, "<?php echo $rwgetCordnate['anotation_type'] . ' by ' . $rwgetAntnBy['first_name'] . ' ' . $rwgetAntnBy['last_name'] . ' <br/> ' . date("j F, Y, H:i", strtotime($rwgetCordnate['date_time'])); ?>", 190, 1000, "approved");
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
            var docAsinId = <?php echo '0'; //echo $id;          ?>;
            var docId = <?php echo $id1; ?>;
            var existFileConfirm;



        </script>
        <script>
            $('#coment').keypress(function (e) {
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
            });
        </script>

    </body>
</html>