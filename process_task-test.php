<?php
require_once './loginvalidate.php';
require_once './application/config/database.php';
require_once './application/pages/head.php';
require_once './application/pages/sendSms.php';
require_once './application/pages/function.php';

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
$id = base64_decode(urldecode($_GET['id']));
$task = mysqli_query($db_con, "select * from tbl_doc_assigned_wf where id='$id' and (task_status='Pending' or task_status='Approved') ");
$rwTask = mysqli_fetch_assoc($task);

if ($_SESSION['cdes_user_id'] != '1') {
    $work = mysqli_query($db_con, "select * from tbl_task_master where task_id='$rwTask[task_id]' and (assign_user = '$_SESSION[cdes_user_id]' or alternate_user='$_SESSION[cdes_user_id]' or supervisor='$_SESSION[cdes_user_id]')");
    if (mysqli_num_rows($work) > 0) {
        $rwWork = mysqli_fetch_assoc($work);
        $ltaskName = $rwWork['task_name'];
    } else {
        header("Location:index");
    }
} else {
    $rwTask[task_id];
    $work = mysqli_query($db_con, "select * from tbl_task_master where task_id='$rwTask[task_id]'");
    if (mysqli_num_rows($work) > 0) {
        $rwWork = mysqli_fetch_assoc($work);
    } else {
        header("Location:index");
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
    $file = "../../" . $_SESSION['lang'] . ".json";
} else {
    $file = "../../English.json";
}
?>
<link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<!--for searchable select-->
<link href="assets/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" />

<!-- Plugin Css-->
<link rel="stylesheet" href="assets/plugins/magnific-popup/css/magnific-popup.css" />
<link rel="stylesheet" href="assets/plugins/jquery-datatables-editable/datatables.css" />


<a class="btn btn-primary btn-block" data-toggle="modal" data-target="#con-close-modal"><?php echo $lang['Aprv_Rjct_Tsk']; ?></a>
<div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg" id="afterSubmt"> 

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
                                    <input type="text" name="comment" class="form-control chat-input" id="input" placeholder="<?php echo $lang['Enter_yr_nte_her']; ?>">
                                </div>
                            </div>
                            <div class="chat-conversation">

                                <ul class="conversation-list nicescroll" style="height: Auto;">

                                    <?php
                                    //$proclist = mysqli_query($db_con, "select * from tbl_doc_assigned_wf where ticket_id='$rwTask[ticket_id]'");
                                    //$rwProclist = mysqli_fetch_assoc($proclist);
                                    $comment = mysqli_query($db_con, "select comment_time, comment,user_id, task_id from tbl_task_comment where tickt_id= '$rwTask[ticket_id]' order by comment_time desc");
                                    while ($rwcomment = mysqli_fetch_assoc($comment)) {

                                        $usr = mysqli_query($db_con, "select first_name, last_name,profile_picture from tbl_user_master where user_id='$rwcomment[user_id]'");
                                        $rwUsr = mysqli_fetch_assoc($usr);
                                        ?><li class="clearfix">
                                            <div class="chat-avatar">
                                                <?php if (!empty($rwUsr['profile_picture'])) { ?>
                                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($rwUsr['profile_picture']); ?>" alt="Image">
                                                <?php } else { ?>
                                                    <img src="assets/images/avatar.png" alt="Image">
                                                <?php } ?>


                                            </div>
                                            <div class="conversation-text">

                                                <div class="ctext-wrap">
                                                    <i><?php echo $rwUsr['first_name'] . ' ' . $rwUsr['last_name']; ?></i>
                                                    <p>
                                                        <?php
                                                        echo '<strong>Comment: </strong>' . $rwcomment['comment'];


                                                        //get task name
                                                        $getTaskName = mysqli_query($db_con, "select * from tbl_task_master where task_id='$rwcomment[task_id]'");
                                                        $rwgetTaskName = mysqli_fetch_assoc($getTaskName);
                                                        echo '<br/><strong>Task Name: </strong>' . $rwgetTaskName['task_name'];
                                                        if (!empty($rwgetTaskName['task_description'])) {
                                                            echo '<br/><strong>Task Description: </strong>' . $rwgetTaskName['task_description'];
                                                        }
                                                        ?>
                                                        <br/><?php echo date("d - M - y, H:i A", strtotime($rwcomment['comment_time'])); ?>
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

<?php require_once './application/pages/footerForjs.php'; ?>
<script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
<script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
<!--for searchable select -->
<script type="text/javascript" src="assets/plugins/jquery-quicksearch/jquery.quicksearch.js"></script>
<script src="assets/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>

<script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>
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
        $.post("application/ajax/createownFlow2.php", {ID: createown}, function (result, status) {
            if (status == 'success') {
                $("#createTaskFlowr").html(result);
                // alert(result);
            }
        });
    });
</script>

<?php
if (isset($_POST['approveTask'])) {

    $docID = '0';
    $comment = mysqli_real_escape_string($db_con, $_POST['comment']);

    $tktId = $_POST['tktId'];

    $user_id = $_SESSION['cdes_user_id'];
    $taskId = $rwTask['task_id'];

    if ($_FILES['fileName']['name']) {

        $file_name = $_FILES['fileName']['name'];
        $file_size = $_FILES['fileName']['size'];
        $file_type = $_FILES['fileName']['type'];
        $file_tmp = $_FILES['fileName']['tmp_name'];
        $pageCount = $_POST['pageCount'];

        $extn = substr($file_name, strrpos($file_name, '.') + 1);
        $fname = substr($file_name, 0, strrpos($file_name, '.'));

        $fileExtn = substr($file_name, strrpos($file_name, ".") + 1);

        $getDocId = mysqli_query($db_con, "select * from tbl_doc_assigned_wf where id = '$id'") or die('Error:' . mysqli_error($db_con));
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
        $uploaddir = "extract-here/images/" . $storageName . '/';
        if (!is_dir($uploaddir)) {
            mkdir($uploaddir, 777, TRUE) or die(print_r(error_get_last()));
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

        if ($upload) {

            require_once './classes/ftp.php';

            $ftp = new ftp();
            $ftp->conn("$fileserver", "$port", "$ftpUser", "$ftpPwd");

            $ftp->put(ROOT_FTP_FOLDER . '/images/' . $storageName . '/' . $filenameEnct, $uploaddir);
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

            //"INSERT INTO tbl_document_master($cols) select $cols from tbl_document_master where doc_id='$doc_id'";
            $createVrsn = mysqli_query($db_con, "INSERT INTO tbl_document_master($cols) select $cols from tbl_document_master where doc_id='$doc_id'") or die('Error insert:' . mysqli_error($db_con));
            $insertDocID = mysqli_insert_id($db_con);
            //$createVrsn = mysqli_query($db_con, "INSERT INTO tbl_document_master(doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages, dateposted) VALUES ('$updateDocName', '$file_name', '$fileExtn', 'images/$storageName/$filenameEnct', '$user_id', '$file_size', '$pageCount', '$date')") or die('Error:' . mysqli_error($db_con));
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `doc_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null, '$doc_id','Versioning Document $file_name Added in task $ltaskName','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
            if ($createVrsn) {
                $updateNew = mysqli_query($db_con, "update tbl_document_master set doc_name='$updateDocName' where doc_id='$insertDocID'");
                $updateOld = mysqli_query($db_con, "update tbl_document_master set old_doc_name='$file_name',filename='$fname', doc_extn='$extn', doc_path='images/$storageName/$filenameEnct', uploaded_by='$user_id', doc_size='$file_size', noofpages='$pageCount', dateposted='$date' where doc_id='$doc_id'");
                echo'<script>taskSuccess("process_task?id=' . $_GET[id] . ((isset($_GET[start])) ? ('&start=' . $_GET[start]) : '') . '","Updated Successfully !");</script>';
            }
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

            $run = mysqli_query($db_con, "UPDATE tbl_doc_assigned_wf SET task_status = '$app', action_by = '$user_id', action_time = '$date' where id='$id' ") or die('Error query failed' . mysqli_error($db_con));
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
                    // $tt = taskAssignToUser($db_con, $stepId, $ctaskOrder, $docID, $ctaskID, $assignBy, $id, $wfid, $ticket, $taskRemark);
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

                    $daterangee = explode("To", $daterange[$i]);

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
                    $deadLineAdd = $deadLinee[$i];
                } else if ($deadlineType == 'Hrs') {

                    $deadLinee = $_POST['hrsAdd'];
                    $deadLineAdd = $deadLinee[$i] * 60;
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
            taskAssignToUser($db_con, $stepId, $ctaskOrder, $docID, $ctaskID, $assignBy, $id, $wfid, $ticket, $taskRemark, $date);

            echo '<script>taskSuccess("myTask","Task Completed successfully !");</script>';
        } else if ($app == 'Rejected') {
            if (!empty($comment)) {
                //$ticket_query= mysqli_query($db_con, "SELECT NextTask,ticket_id FROM tbl_doc_assigned_wf where id='$id' ") or die('Error query failed pp:' . mysqli_error($db_con));
                //$row_ticket_id=mysqli_fetch_array($ticket_query);
                $run = mysqli_query($db_con, "UPDATE tbl_doc_assigned_wf SET task_status = '$app', action_by = '$user_id',action_time = '$date' where id='$id' ") or die('Error query failed pp:' . mysqli_error($db_con));

                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'$ltaskName task $app ','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                $getTskName = mysqli_query($db_con, "select * from tbl_task_master where task_id = '$ctaskID' ") or die('Error' . mysqli_error($db_con));
                $rwgetTskName = mysqli_fetch_assoc($getTskName);

                backToPrevTsk($db_con, $stepId, $ctaskOrder, $docID, $ctaskID, $assignBy, $id, $wfid, $tktId, $taskRemark, $date, $projectName);

                require_once './mail.php';
                //echo 'mail send id = '.$id; die;
                $mail = rejectTask($id, $ctaskID, $tktId, $db_con, $projectName, $comment, $doc_id);
                //$delete = mysqli_query($db_con, "DELETE FROM tbl_doc_assigned_wf WHERE ticket_id='$row_ticket_id[ticket_id]' AND NextTask=2") or die('Error query failed pp:' . mysqli_error($db_con));
                if ($mail) {



                    //send sms to mob
//                        $getMobNum = mysqli_query($db_con, "select * from tbl_user_master where user_id = '$assignBy'") or die('Error:' . mysqli_error($db_con));
//                        $rwgetMobNum = mysqli_fetch_assoc($getMobNum);
//                        $submtByMob = $rwgetMobNum['phone_no'];
//                        $msg = 'Your Ticket Id ' . $ticket . ' is Rejected in Task ' . $rwgetTskName['task_name'] . '.';
//                        $sendMsgToMbl = smsgatewaycenter_com_Send($submtByMob, $msg, $debug = false);
                    //

                        echo '<script>taskSuccess("myTask", "Task has been rejected !");</script>';
                } else {
                    echo '<script>taskFailed("myTask", "Opps!! Task is not rejected !")</script>';
                }
            } else {
                echo '<script>taskFailed("process_task", "Reason is mandatory in comment")</script>';
            }
            exit();
        } else if ($app == 'Aborted') {



            $getTskName = mysqli_query($db_con, "select * from tbl_task_master where task_id = '$ctaskID' ") or die('Error' . mysqli_error($db_con));
            $rwgetTskName = mysqli_fetch_assoc($getTskName);

            $update = mysqli_query($db_con, "update tbl_doc_assigned_wf set task_status='$app', action_by='$user_id',action_time='$date',NextTask='5' where id='$id'");
            $delete = mysqli_query($db_con, "DELETE FROM tbl_doc_assigned_wf WHERE ticket_id='$tktId' AND NextTask=2") or die('Error query failed pp:' . mysqli_error($db_con));
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'$ltaskName task $app','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
            if ($update) {
                require_once './mail.php';
                $mailSent = abortTask($ticket, $id, $wfid, $db_con, $projectName);
                if ($mailSent) {

                    //send sms to mob
//                            $getMobNum = mysqli_query($db_con, "select * from tbl_user_master where user_id = '$assignBy'") or die('Error:' . mysqli_error($db_con));
//                            $rwgetMobNum = mysqli_fetch_assoc($getMobNum);
//                            $submtByMob = $rwgetMobNum['phone_no'];
//                            $msg = 'Your Ticket Id ' . $ticket . ' is Aborted in Task ' . $rwgetTskName['task_name'] . '.';
//                            $sendMsgToMbl = smsgatewaycenter_com_Send($submtByMob, $msg, $debug = false);
                    //


                            echo '<script>taskSuccess("myTask", "Task has been aborted !");</script>';
                } else {
                    echo '<script>taskFailed("myTask", "Opps!! Task is not aborted !")</script>';
                }
            } else {
                echo '<script>taskFailed("myTask", "Opps!! Task is not aborted !")</script>';
            }
        } else if ($app == 'Complete') {
            $run = mysqli_query($db_con, "UPDATE tbl_doc_assigned_wf SET task_status = '$app', action_by = '$user_id', action_time = '$date', NextTask='1' where id='$id'") or die('Error query failed' . mysqli_error($db_con));
            $ticket = mysqli_query($db_con, "SELECT NextTask,ticket_id FROM tbl_doc_assigned_wf where id='$id' ") or die('Error query failed pp:' . mysqli_error($db_con));
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

                $getTskName = mysqli_query($db_con, "select * from tbl_task_master where task_id = '$ctaskID' ") or die('Error' . mysqli_error($db_con));
                $rwgetTskName = mysqli_fetch_assoc($getTskName);
                require_once './mail.php';
                $mailSent = completeTask($ticket, $id, $wfid, $db_con, $projectName);
                if ($mailSent) {
                    echo '<script>taskSuccess("myTask","Task Completed successfully !");</script>';
                } else {
                    echo '<script>taskFailed("myTask","Task Completion Failed !");</script>';
                }

                //taskAssignToUser($db_con, $stepId, $ctaskOrder, $docID, $ctaskID, $assignBy, $id, $wfid, $ticket, $taskRemark, $date);
            } else {
                echo '<script>taskFailed("myTask","Next Task Deletion Failed !");</script>';
            }
        }
    }
    mysqli_close($db_con);
}

//end own user created and order

function taskAssignToUser($db_con, $stepId, $ctaskOrder, $docID, $ctaskID, $assignBy, $id, $wfid, $ticket, $taskRemark, $date) {

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
            //echo $nextTaskId;
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
                        $update = mysqli_query($db_con, "update tbl_doc_assigned_wf set NextTask='1' , end_date='$endDate' where id='$id'") or die('Error to update old' . mysqli_error($db_con));


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
                        $update = mysqli_query($db_con, "update tbl_doc_assigned_wf set NextTask='1' , end_date='$endDate' where id='$id'");
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

                        echo $nextTaskId = $rwTaskn['task_id'];

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
                                $update = mysqli_query($db_con, "update tbl_doc_assigned_wf set NextTask='1' , end_date='$endDate' where id='$id'");
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
                                $assignToNextWf = mysqli_query($db_con, "update tbl_doc_assigned_wf set NextTask='0' where task_id='$nextTaskId' and doc_id='$docID' and ticket_id = '$ticket'");
                            } else {
                                $assignToNextWf = mysqli_query($db_con, "update tbl_doc_assigned_wf set NextTask='2' where task_id='$nextTaskId' and doc_id='$docID' and ticket_id = '$ticket'");
                            }
                            $rwtaskCheck = mysqli_fetch_assoc($taskCheck);
                            $idnxt = $rwtaskCheck['id'];
                            if ($assignToNextWf) {
                                $update = mysqli_query($db_con, "update tbl_doc_assigned_wf set NextTask='1' , end_date='$endDate' where id='$id'");
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
            $assignToNextWf = mysqli_query($db_con, "update tbl_doc_assigned_wf set NextTask='1' where id='$id'");
            if ($assignToNextWf) {
                'doc id' . $docID;
                if (!empty($docID)) {
                    $updateDocMaster = mysqli_query($db_con, "update tbl_document_master set doc_name=replace(doc_name,'_$wfid','') where doc_id='$docID'");
                    $update = mysqli_query($db_con, "update tbl_document_master set doc_name=replace(doc_name,'_$wfid','') where substring_index(doc_name,'_',-2)=$docID");
                    //view version in storage after workflow complete
                }
                completeTask($ticket, $id, $wfid, $db_con, $projectName);
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
function backToPrevTsk($db_con, $stepId, $ctaskOrder, $docID, $ctaskID, $assignBy, $id, $wfid, $ticket, $taskRemark, $date, $projectName) {
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

            $setflg = mysqli_query($db_con, "update tbl_doc_assigned_wf set NextTask='2' where id = '$id'") or die('Error:' . mysqli_error($db_con));
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
                    $setflg = mysqli_query($db_con, "update tbl_doc_assigned_wf set NextTask='2' where id = '$id'") or die('Error to move next update' . mysqli_error($db_con));
                    $updateTaskPrev = mysqli_query($db_con, "UPDATE tbl_doc_assigned_wf SET task_status = 'Approved', action_by = '$_SESSION[cdes_user_id]', action_time = '$date', NextTask = '0' where task_id='$getPrevTskId[task_id]' and ticket_id = '$ticket' ") or die('Error query failed2' . $_SESSION[cdes_user_id] . mysqli_error($db_con));
                }
            }
        } else {
            $setflg = mysqli_query($db_con, "update tbl_doc_assigned_wf set NextTask='2' where id = '$id'") or die('Error to move next update' . mysqli_error($db_con));
        }
    }
}
?>
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

    $("a#video").click(function () {
        var id = $(this).attr('data');

        $.post("application/ajax/videoformat.php", {vid: id}, function (result, status) {
            if (status == 'success') {
                $("#videofor").html(result);
                //alert(result);

            }
        });
    });
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
</html>

<?php
if (isset($_POST['addComent'])) {
    $comment = mysqli_real_escape_string($db_con, $_POST['comment']);
    if (!empty($comment)) {
        $user_id = $_SESSION['cdes_user_id'];
        $cmttask = "INSERT INTO tbl_task_comment (tickt_id, user_id, comment, task_status, comment_time, task_id) VALUES ('$rwTask[ticket_id]', '$user_id','$comment', 'comment', '$date', '$rwTask[task_id]')";
        $run = mysqli_query($db_con, $cmttask) or die('Error query failed' . mysqli_error($db_con));
        echo '<script>uploadSuccess("process_task?id=' . urlencode($_GET['id']) . '", "Comment Added Successfully !");</script>';
    }
    mysqli_close($db_con);
}
//if (isset($_POST['editpdf'])) {
//    
//    $dc_id = mysqli_real_escape_string($db_con, $_POST['dc_id']);
//    $dc_path=explode('/',$_POST['dc_path']);
//    $dc_path=array_pop($dc_path);
//    echo $dc_path=implode('/',$dc_path);
//    echo "<scritp>alert('".$dc_path."')</script>";
//    $doc=$_FILES['fileName']['name'];
//    $tmp=$_FILES['fileName']['tmp_name'];
//    $targetPath=$dc_path;
//    $docPath=$targetPath.$doc;
//    $upload=move_uploaded_file($tmp,$targetPath.$doc);
//    if ($upload) {
//        //$user_id = $_SESSION['cdes_user_id'];
//        echo $updateDoc = "UPDATE tbl_document_master SET old_doc_name='$doc',doc_path='$docPath',doc_id='$dc_id'";
//        $Docrun = mysqli_query($db_con, $updateDoc) or die('Error query failed' . mysqli_error($db_con));
//        if($Docrun){
//        echo '<script>uploadSuccess("process_task?id=' . urlencode($_GET['id']) . '", "Pdf Update Successfully !");</script>';
//        }
//    }else{
//        echo "<script>alert('Error')</script>";
//    }
//    mysqli_close($db_con);
//}
?>   