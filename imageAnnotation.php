<?php
require_once './loginvalidate.php';
require_once './application/pages/function.php';
require_once './classes/fileManager.php';

if ($rwgetRole['image_file'] != '1' || empty($_GET['i'])) {
    header('Location:index');
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


$chk = strtolower(filter_var($_GET['chk'], FILTER_SANITIZE_STRING)); // check for review
$docId = urldecode(base64_decode($_GET['i']));

$color = base64_decode(urldecode($_GET['color']));
$uid = urldecode(base64_decode($_GET['uid']));
$tid = base64_decode(urldecode($_GET['tid'])); //task id

if ($chk == 'rw') {
// annotation condition
    $annot_cond = " and is_inreview='1'";
// Review log condition
    $rwlog_cond = " and in_review='0'";
     
    $file = mysqli_query($db_con, "select doc_name,doc_path,doc_extn,old_doc_name from tbl_document_reviewer where doc_id='$docId'") or die('error' . mysqli_error($db_con));
} else {
// Annotation Condition    
    $annot_cond = " and is_inreview='0'";
// Review log condition
    $rwlog_cond = " and in_review='1'";
    $file = mysqli_query($db_con, "select doc_name,filename,doc_path,doc_extn,old_doc_name from tbl_document_master where doc_id='$docId'") or die('error' . mysqli_error($db_con));
}

$rwFile = mysqli_fetch_assoc($file);
$filePath = $rwFile['doc_path'];
$fname = $rwFile['old_doc_name'];
$doc_extn = $rwFile['doc_extn'];
$slid = $rwFile['doc_name'];

$slid = reset(explode('_', $slid));

$status = 0;
$checkfileLockqry = mysqli_query($db_con, "SELECT * FROM `tbl_locked_file_master` WHERE doc_id='$docId' and is_active='1' and user_id='$_SESSION[cdes_user_id]'");
if (mysqli_num_rows($checkfileLockqry) > 0) {
    
    $checkfileLock = mysqli_query($db_con, "SELECT * FROM `tbl_locked_file_master` WHERE doc_id='$docId' and is_locked='1'  and user_id='$_SESSION[cdes_user_id]'");
    if (mysqli_num_rows($checkfileLock) > 0) {
        $status = 1;
    } else {
        $status = 0;
    }
} else {
    $status = 1;
}


mysqli_set_charset($db_con, "utf8");

$fileManager = new fileManager();
// Connect to file server
$fileManager->conntFileServer();
$localPath = $fileManager->getFile($rwFile);



$task = mysqli_query($db_con, "select * from tbl_doc_assigned_wf where id='$tid' and (task_status='Pending' or task_status='Approved') ");
$rwTask = mysqli_fetch_assoc($task);
$taskDocId = $rwTask['doc_id'];
if ($_SESSION['cdes_user_id'] != '1') {
    $work = mysqli_query($db_con, "select * from tbl_task_master where task_id='$rwTask[task_id]' and (assign_user = '$_SESSION[cdes_user_id]' or alternate_user='$_SESSION[cdes_user_id]' or supervisor='$_SESSION[cdes_user_id]')");
    if (mysqli_num_rows($work) > 0) {
        $rwWork = mysqli_fetch_assoc($work);
        $ltaskName = $rwWork['task_name'];
    } else {
        // header("Location:../index");
    }
} else {
    $rwTask[task_id];
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


if (isset($_SESSION['lang'])) {
    $file = "./" . $_SESSION['lang'] . ".json";
} else {
    $file = "./English.json";
}

$jsFile = file_get_contents($file);
$lang = json_decode($jsFile, true);
?>



<!DOCTYPE html>
<html>
    <head>
        <title>
            Image Annotation
        </title>

        <style>
            body {
                margin: 0;
                font-family: Arial, Helvetica, sans-serif;
            }

            .top-container {
                background-color: #808080a6;
                padding: 30px;
                text-align: center;
            }

            .header {
                height: 35px;
                background: #555;
                color: #f1f1f1;
            }

            .content {
                padding: 16px;
            }

            .sticky {
                position: fixed;
                top: 0;
                width: 100%;
            }

            .sticky + .content {
                padding-top: 102px;
            }
            .tab-content{
                margin-top:10px;
            }
            
            .color-radio {
              display: none;
            }

          .button {
            display: inline-block;
            position: relative;
            width: 32px;
            height: 32px;
            cursor: pointer;
          }

          .button span {
            display: block;
            position: absolute;
            width: 25px;
            height: 25px;
            padding: 0;
            top: 50%;
            left: 50%;
            -webkit-transform: translate(-50%, -50%);
            -ms-transform: translate(-50%, -50%);
            -o-transform: translate(-50%, -50%);
            transform: translate(-50%, -50%);
            border-radius: 100%;
            background: #eeeeee;
            box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.26);
            transition: ease .3s;
          }

          .button span:hover {
            padding: 10px;
          }

          .red .button span {
            background: #ff2222;
          }

          .green .button span {
            background: #0baf36;
          }

          .white .button span {
            background: #fffff;
          }
          .blue .button span {
            background: #2196F3;
          }


          
        </style>
        <link href="assets/css/icons.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <!--link href="assets/css/components.css" rel="stylesheet" type="text/css"/-->
        <link rel="stylesheet" type="text/css" href="assets/css/annotate.css">
        <link href="assets/plugins/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css">
        <link href="assets/css/core.css" rel="stylesheet" type="text/css"/>
         <link href="anott/toolbar.css" rel="stylesheet" type="text/css"/>
         <link href="assets/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" />   
       
        
    </head>

    <body  class="loadingInProgress" style="background: aliceblue;">
     
        <div class="header sticky" id="myHeader" style="z-index:999;">
             <?php if ($chk != 'rw'): ?>
            <button class="export-image btn btn-primary  annotate-redo" style="float:right;" data-toggle="modal" data-target="#exampleModal">Save Image</button>
       
            <button class="btn btn-primary" style="float:right;"  data-toggle="modal" data-target="#con-close-modal-act"><?php echo $lang['Aprv_Rjct_Tsk']; ?></button>
            
        <?php endif; ?>
            
            <?php if ($chk == 'rw'): ?>
                        <form method="post" style="display: inline-block; float:right;">
                            <button type="button" class="export-image btn btn-primary"  style="float:right;"  data-toggle="modal" data-target="#reviewModal" title="<?= $lang['Submit_Review'] ?>" name="submit_review" id="submit_review"> <?= $lang['Submit_Review'] ?> </button></form>
                        </form>    
            <?php endif; ?>
        <div class="pull-right">  
            <label class="red">
            <input type="radio" class="color-radio" name="color" value="red" onclick="setColor();" <?php echo ($color=='red')?"checked":""; ?> >
           
            <div class="button"><span></span></div>
          </label>
            
        <label class="white">
            <input type="radio" class="color-radio" vname="color" value="white" onclick="setColor();" <?php echo ($color=='white')?"checked":""; ?> >
           
            <div class="button"><span></span></div>
          </label>
            <label class="green">
            <input type="radio" class="color-radio" name="color" value="green" onclick="setColor();" <?php echo ($color=='green')?"checked":""; ?> >
           
            <div class="button"><span></span></div>
          </label>

          <label class="blue">
            <input type="radio" class="color-radio" name="color" value="blue" onclick="setColor();" <?php echo ($color=='blue')?"checked":""; ?>>
          
            <div class="button"><span></span></div>
          </label>
    </div>
        </div>
        <div id="mainContainer content">
            <div class="col-md-9">
            <div class="my-image-selector" style="float:right; margin-top:-35px; margin-right: 200px;">

            </div>
            <div style="overflow:auto; min-height: 570px; border: 5px solid #d3d3d3;">
               <div id="myCanvas" style="position:relative; width: auto;"></div>
            </div>
            </div>
             <div class="col-md-3" >
            <div id="comment-wrapper2" style="display:block;">

                <?php if ($rwgetRole['wf_log'] == '1' || $rwgetRole['review_log'] == '1') { ?>  
                    <div class="panel-body">
                        <ul class="nav nav-pills">
                            <?php if ($rwgetRole['wf_log'] == '1') { ?>
                                <li class="active tbs"><a href="#navpills-1" data-toggle="tab" aria-expanded="true">Activity Log</a></li>
                            <?php } ?>
                            <?php if ($rwgetRole['review_log'] == '1') { ?>
                                <li class="<?= ($rwgetRole['wf_log'] != '1' ? 'active' : '') ?> tbs"><a href="#navpills-2" data-toggle="tab" aria-expanded="false" style="padding: 0px 11px;">Review Log</a></li>
                            <?php } ?>
                        </ul>
                        <div class="tab-content br-n pn">
                            <?php if ($rwgetRole['wf_log'] == '1') { ?>
                                <div id="navpills-1" class="tab-pane active">
                                    <?php
                                    $pdflogSql = "select * from tbl_ezeefile_logs_wf where doc_id='$taskDocId' ";

                                    if ($_SESSION[cdes_user_id] != 1) {
                                        // $pdflogSql.=" and user_id='$_SESSION[cdes_user_id]'";   
                                    }
                                    $pdflog = mysqli_query($db_con, $pdflogSql);

                                    if (mysqli_num_rows($pdflog) > 0 && $tid!="") {
                                        while ($rwpdflog = mysqli_fetch_assoc($pdflog)) {
                                            echo '<span><strong> Action : </strong>' . $rwpdflog['action_name'] . '<br>';
                                            echo '<strong> Action By : </strong>' . $rwpdflog['user_name'] . '<br>';
                                            echo '<strong> Action Time : </strong>' . date('d M Y, H:i', strtotime($rwpdflog['start_date'])) . '</span>';
                                        }
                                    } else {
                                        echo '<center>No activity log found</center><hr style="color:#000;">';
                                    }
                                    ?>   
                                </div>
                            <?php } ?>
                            <?php if ($rwgetRole['review_log'] == '1') { ?>
                                <div id="navpills-2" class="tab-pane <?= ($rwgetRole['wf_log'] != '1' ? 'active' : '') ?>">
                                    <?php
                                  $rlog_sql = "select rl.*,u.first_name,u.last_name from tbl_reviews_log rl left join tbl_user_master u on rl.user_id=u.user_id where 1=1 " . $rwlog_cond;
                                    if ($_SESSION[cdes_user_id] != 1) {
                                        //   $rlog_sql.=" and rl.user_id='$_SESSION[cdes_user_id]'";   
                                    }
                                   $rlog_sql .= " and rl.doc_id='$docId' order by id desc";

                                    $rlog_query = mysqli_query($db_con, $rlog_sql);

                                    if (mysqli_num_rows($rlog_query) > 0) {

                                        while ($rlog_res = mysqli_fetch_assoc($rlog_query)) {
                                            echo '<span><strong> Action : </strong> ' . $rlog_res['action_name'] . '<br>';
                                            echo '<strong> Action By : </strong> ' . $rlog_res['first_name'] . ' ' . $rlog_res[last_name] . '<br>';
                                            echo '<strong> Action Time : </strong>' . date('d M Y, H:i', strtotime($rlog_res['start_date'])) . '</span>';
                                        }
                                    } else {
                                        echo '<center>No Review Log found</center><hr style="color:#000;">';
                                    }
                                    ?>
                                </div>
                            <?php } ?>
                        </div>                                    
                    </div>
                <?php } ?>
            </div>
             </div>
            
        </div>
        
        
        
        <div style=" display: none; background: rgba(0,0,0,0.5); width: 100%; z-index: 2000; position: fixed; top:0; height: 1304px;" id="wait">;

            <img src="assets/images/proceed.gif" alt="load"  style=" margin-left: 48%; margin-top: 250px; width: 100px; height:100px; position: fixed; "/>
        </div> 

        <script type="text/javascript" src="assets/js/jquery.min.js"></script>
        <script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="assets/js/djaodjin-annotate.js?v=<?php echo time(); ?>"></script>
        <script src="assets/plugins/sweetalert2/sweetalert2.min.js"></script>
        <script src="assets/pages/jquery.sweet-alert.init.js"></script>
        <script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <script src="assets/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
        <script>
            
            //Restric/disabled F5/Cntrl+F5 click in whole application js
			// slight update to account for browsers not supporting e.which
				function disableF5(e) {
					if ((e.which || e.keyCode) == 116 || e.ctrlKey && e.keyCode == 82)
						e.preventDefault();
				}
				$(document).ready(function () {
					$(document).on("keydown", disableF5);
				});
				//Restric/disabled right click in whole application js
				$(document).ready(function () {
					$("html").bind("contextmenu", function (e) {
						e.preventDefault();
					});
				});

						
						
						
            $(document).ready(function () {
                var counter = 0;
                $('#myCanvas').on("annotate-image-added", function (event, id, path) {
                    //$(".my-image-selector").append("<label><input type=\"radio\" name=\"image-selector\" class=\"annotate-image-select\" value=\"" + path + "\" checked id=\"" + id + "\"><img src=\"" + path + "\" width=\"35\" height=\"35\"></label>");
                });
                
         
                
               
                
                $('#myCanvas').annotate({
                    color: $("input[name='color']:checked").val(),
                    bootstrap: true,
                    images: ['<?php echo $localPath; ?>'],
                    onExport: function (image) {
                        
                        if ($("#exported-image").length > 0) {
                            $("#exported-image").remove();
                        }

                    <?php if($chk == 'rw'){ ?>
                             reviewDocument(image);
                    <?php }else{ ?>
                            saveImage(image);
                    <?php } ?>

                        //$("body").append("<img src=\"" + image + "\" id=\"exported-image\">");
                    }
                });
                

                function saveImage(image) {

                    var annotform = $("#annotform")[0];
                    var formData = new FormData(annotform);
                    var action = $("#action").val();
                    formData.append('image', image);
                    formData.append('action', action);
                    formData.append('ticket', '<?php echo $ticket; ?>');
                    formData.append('chk', '<?php echo $chk; ?>');
                    formData.append('tid', '<?php echo $tid; ?>');
                    formData.append('annot_action', $("input[name='tool_option_myCanvas']:checked").attr('data-tool'));
                    $("#wait").show();
                    $.ajax({
                        url: 'application/ajax/saveAnnotImage',
                        data: formData,
                        type: 'POST',
                        contentType: false,
                        processData: false,
                        beforeSend: function () {
                            
                        },
                        success: function (data) {
                            
                             $("#wait").hide();
                            
                            var result =  JSON.parse(data);
                            
                            if(result.status=="success"){
                                
                               
                                taskSuccess(result.redirurl,result.msg);
                               
                                
                            }else{
                                taskFailed("<?php echo basename($_REQUEST['REQUEST_URI']); ?>",result.msg);
                            }
                            
                        }
                    });
                }
                
                
                function reviewDocument(image) {

                    var annotform = $("#reviewform")[0];
                    var formData = new FormData(annotform);
                    formData.append('image', image);
                    $("#wait").show();
                    $.ajax({
                        url: 'reviewImage',
                        data: formData,
                        type: 'POST',
                        contentType: false,
                        processData: false,
                        beforeSend: function () {
                            
                        },
                        success: function (data) {
                            
                             $("#wait").hide();
                             
                            var result =  JSON.parse(data);
                            
                            if(result.status=="success"){
                                
                                 taskSuccess('<?php echo 'reviewintray'; ?>', result.msg);
                                
                            }else{
                                taskFailed("<?php echo basename($_REQUEST['REQUEST_URI']); ?>",result.msg);
                            }
                            
                        }
                    });
                }
                
                
                

                $(".push-new-image").click(function (event) {

                    if (counter === 0) {
                        //$('#myCanvas').annotate("push", "images/test_2.jpg");
                        counter += 1;
                    } else {
                        //$('#myCanvas').annotate("push", {id:"unique_identifier", path:"images/test.jpg"});
                    }
                });

                $(".saveimage").click(function (event) {
                    $("#action").val($(this).attr("name"));
                    $('#myCanvas').annotate("export", {type: "image/jpeg", quality: 0.2}, function (d) {
                        console.log(d);
                    });
                });


            });
            
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
            
             function setColor(){
                var color =  $("input[name='color']:checked").val();
                color =  btoa(encodeURI(color));
                url = removeParam("color", url);
                url = url + "&color=" + color;
                window.open(url, "_parent");
             }
            
           
             


        </script>

        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form method="post" id="annotform">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h5 class="modal-title">Save Image</h5>

                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want save this image ?</p>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" name="docId" value="<?php echo $docId; ?>">
                            <input type="hidden" name="action" id="action" >
                            <input type="hidden" name="activities[]" id="drawtext" >
                            <input type="hidden" name="activities[]" id="drawrec" >
                            <input type="hidden" name="activities[]" id="drawpen" >
                            <input type="hidden" name="activities[]" id="drawcircle" >
                            <input type="hidden" name="activities[]" id="drawarrow" >
                            <input type="hidden" name="annot" id="annot" >
                            <input type="button" class="btn btn-primary saveimage" name="override" value="Override">
                            <input type="button" class="btn btn-primary saveimage" name="savenew" value="Save As New">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
         <div class="modal fade" id="reviewModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form method="post" id="reviewform">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h5 class="modal-title">Review</h5>

                        </div>
                        <div class="modal-body">
                            <p>Do you want to submit review ?</p>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" name="docId" value="<?php echo $docId; ?>">
                            <input type="hidden" name="reid" value="<?php echo urldecode(base64_decode($_GET['reid']))?? ""; ?>">
                            <input type="button" class="btn btn-primary saveimage" name="reviewdoc" value="Submit">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
                                            <input class="filestyle" id="myImage1" name="fileName" data-buttonname="btn-primary" type="file" >
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
                                                ?> checked > <label for="app"><?php echo $lang['Approved']; ?></label>&nbsp;&nbsp;
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
                                       
                                        $getOwnTask = mysqli_query($db_con, "select * from tbl_task_master where task_id='$rwTask[task_id]'") or die('Error task:' . mysqli_error($db_con));
                                        $rwgetOwnTask = mysqli_fetch_assoc($getOwnTask);

                                        $TskStpId = $rwgetOwnTask['step_id'];
                                        $TskWfId = $rwgetOwnTask['workflow_id'];
                                        $TskOrd = $rwgetOwnTask['task_order'];
                                        $TskAsinToId = $rwgetOwnTask['assign_user'];
                                        $cTaskid = $rwgetOwnTask['task_id'];
                                        $cTaskOrd = $TskOrd;

                                        $nextTskId = nextTaskToUpdate($cTaskOrd, $TskWfId, $TskStpId, $db_con);

                                        $getNxtTask = mysqli_query($db_con, "select * from tbl_task_master where task_id='$nextTskId'") or die('Error task1:' . mysqli_error($db_con));
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
                                                $comment = mysqli_query($db_con, "select id,comment_time, comment,user_id, task_id from tbl_task_comment where tickt_id= '$rwTask[ticket_id]' order by comment_time desc");
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
                                                                    } else {
                                                                        echo $rwcomment['comment'];
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
            
            
            <script>
                jQuery(document).ready(function () {
                $('.selectpicker').selectpicker();

            });
            
             $("a#createOwnflowr").click(function () {
                var createown = 0;
                // alert(id);
                $("#createOwnflowr").hide();
                $.post("application/ajax/createownFlow2.php", {ID: createown}, function (result, status) {
                    if (status == 'success') {
                        $("#createTaskFlowr").html(result);
                        // alert(result);
                    }
                });
            });
                </script>
    </body>
</html>


<?php
if (isset($_POST['approveTask'])) {
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
				// Connect to file server
				$fileManager->conntFileServer();
			
				$uploadInToFTP = $fileManager->uploadFile($uploaddir, ROOT_FTP_FOLDER . '/images/' . $storageName . '/' . $filenameEnct);
				
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
                    $updateOld = mysqli_query($db_con, "update tbl_document_master set old_doc_name='$file_name', workflow_id='$wfid',  filename='$fname', doc_extn='$extn', doc_path='images/$storageName/$filenameEnct', uploaded_by='$user_id', doc_size='$file_size', noofpages='$pageCount', dateposted='$date', ftp_done='1' where doc_id='$doc_id'");
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


            echo '<script>taskSuccess("./myTask","Task Completed successfully !");</script>';
        } else if ($app == 'Rejected') {
            if (!empty($comment)) {
                //$ticket_query= mysqli_query($db_con, "SELECT NextTask,ticket_id FROM tbl_doc_assigned_wf where id='$tid' ") or die('Error query failed pp:' . mysqli_error($db_con));
                //$row_ticket_id=mysqli_fetch_array($ticket_query);
                $run = mysqli_query($db_con, "UPDATE tbl_doc_assigned_wf SET task_status = '$app', action_by = '$user_id',action_time = '$date' where id='$tid' ") or die('Error query failed pp:' . mysqli_error($db_con));

                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'$ltaskName task $app ','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                $getTskName = mysqli_query($db_con, "select * from tbl_task_master where task_id = '$ctaskID' ") or die('Error' . mysqli_error($db_con));
                $rwgetTskName = mysqli_fetch_assoc($getTskName);

                backToPrevTsk($db_con, $stepId, $ctaskOrder, $docID, $ctaskID, $assignBy, $tid, $wfid, $tktId, $taskRemark, $date, $projectName);

                require_once './mail.php';
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

                    echo '<script>taskSuccess("./myTask", "Task has been rejected !");</script>';
                } else {
                    echo '<script>taskFailed("./myTask", "Opps!! Task is not rejected !")</script>';
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
                require_once './mail.php';
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


                    echo '<script>taskSuccess("./myTask", "Task has been aborted !");</script>';
                // } else {
                //     echo '<script>taskFailed("../myTask", "Opps!! Task is not aborted !")</script>';
                // }
            } else {
                echo '<script>taskFailed("./myTask", "Opps!! Task is not aborted !")</script>';
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
                require_once './mail.php';
                $mailSent = completeTask($ticket, $tid, $wfid, $db_con, $projectName);
                if ($mailSent) {
                    echo '<script>taskSuccess("./myTask","Task Completed successfully !");</script>';
                } else {
                    echo '<script>taskFailed("./myTask","Task Completion Failed !");</script>';
                }

                //taskAssignToUser($db_con, $stepId, $ctaskOrder, $docID, $ctaskID, $assignBy, $tid, $wfid, $ticket, $taskRemark, $date);
            } else {
                echo '<script>taskFailed("./myTask","Next Task Deletion Failed !");</script>';
            }
        }
    }
    mysqli_close($db_con);
    }
    
    
    //end own user created and order

    function taskAssignToUser($db_con, $stepId, $ctaskOrder, $docID, $ctaskID, $assignBy, $tid, $wfid, $ticket, $taskRemark, $date) {

        $nextTaskIds = array();
        require_once './application/pages/sendSms.php';
        require_once './mail.php';

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

        require_once './mail.php';
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
