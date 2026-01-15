<?php
require_once '../../sessionstart.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout.php");
}
require './../config/database.php';
if (isset($_SESSION['lang'])) {
    $file = "../../" . $_SESSION['lang'] . ".json";
} else {
    $file = "../../English.json";
}
require_once '../../application/pages/sendSms.php';
require_once '../../application/pages/function.php';
$langDetail = mysqli_query($db_con, "select * from tbl_language where lang_name='".$_SESSION['lang']."'") or die('Error : ' . mysqli_error($db_con));
$langDetail = mysqli_fetch_assoc($langDetail);

//for user role
$data = file_get_contents($file);
$lang = json_decode($data, true);
$sameGroupIDs = array();
$group = mysqli_query($db_con, "select * from tbl_bridge_grp_to_um where find_in_set('$_SESSION[cdes_user_id]',user_ids)") or die('Error' . mysqli_error($db_con));
while ($rwGroup = mysqli_fetch_assoc($group)) {
    $sameGroupIDs[] = $rwGroup['user_ids'];
}
$sameGroupIDs = array_unique($sameGroupIDs);
sort($sameGroupIDs);
$sameGroupIDs = implode(',', $sameGroupIDs);

//for user role
$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));
$rwgetRole = mysqli_fetch_assoc($chekUsr);

if(!isset($_POST['token'], $_POST['tid'])){
   echo "Unauthrized Access";  
}

//task process
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
$id = preg_replace("/[^0-9 ]/", "", $_POST['tid']);
mysqli_set_charset($db_con, "utf8");
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
    mysqli_set_charset($db_con, "utf8");
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
<div class="modal-content"> 
    <form method="post" enctype="multipart/form-data"  id="forward_form">
        <input type="hidden" id="token" name="token">
        <input type="hidden" name="tid" value="<?= $id ?>">
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
                    <!-- <p id="mem_msg" style="display:none;"></p> -->
                </div>
                <div class="form-group col-md-12">
                    <div class="col-md-6">
                        <label style="margin-left:-11px;"><?php echo $lang['TSK_ACTN']; ?></label>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                        <?php
                        $display = 'none';
                        $actions = $rwWork['actions'];
                        $actions = explode(",", $actions);
                        if (in_array("Processed", $actions)) {
                            ?>
                        <div class="radio radio-success radio-inline">
                            <input type="radio" name="app" value="Processed" id="processed" <?php
                            if ($rwTask['task_status'] == 'Processed') {
                                echo'checked';
                                $display = 'block';
                            }
                            ?>> <label for="processed"><?php echo $lang['Processed']; ?></label>&nbsp;&nbsp;
                        </div>
                                   <?php
                               }
                               if (in_array("Approved", $actions)) {
                                   ?>
                        <div class="radio radio-success radio-inline">
                            <input type="radio" name="app" value="Approved" id="app" <?php
                            if ($rwTask['task_status'] == 'Approved') {
                                echo'checked';
                                $display = 'block';
                            }
                            ?>> <label for="app"><?php echo $lang['Approved']; ?></label>&nbsp;&nbsp;
                        </div>
                                   <?php
                               }
                               if (in_array("Rejected", $actions)) {
                                   ?>
                        <div class="radio radio-danger radio-inline">
                            <input type="radio" name="app" value="Rejected" id="dis" <?php
                            if ($rwTask['task_status'] == 'Rejected') {
                                echo'checked';
                            }
                            ?>> <label for="dis"><?php echo $lang['Rejected']; ?></label>&nbsp;&nbsp;
                        </div>
                                   <?php
                               }
                               if (in_array("Aborted", $actions)) {
                                   ?>
                        <div class="radio radio-danger radio-inline">
                            <input type="radio" name="app" value="Aborted" id="abort">
                            <label for="abort"><?php echo $lang['Aborted']; ?></label>&nbsp;&nbsp;
                        </div>
                            <?php
                        }
                        if (in_array("Complete", $actions)) {
                            ?>
                        <div class="radio radio-success radio-inline">
                            <input type="radio" name="app" value="Complete" id="comp" <?php
                            if ($rwTask['task_status'] == 'Complete') {
                                echo'checked';
                            }
                            ?>>
                            <label for="comp"><?php echo $lang['Complete']; ?></label>
                        </div>
                            <?php
                        }
                        if (in_array("Done", $actions)) {
                            ?>
                        <div class="radio radio-success radio-inline">
                            <input type="radio" name="app" value="Done" id="done" <?php
                            if ($rwTask['task_status'] == 'Done') {
                                echo'checked';
                                $display = 'block';
                            }
                            ?>>
                            <label for="done"><?php echo $lang['Done']; ?></label>
                        </div>
                            <?php
                        }
                        ?>
                    </div>
                    </div>
                </div>
                <div class="form-group col-md-12">
                    <?php
                    //$rwTask['task_id'];
                    mysqli_set_charset($db_con, "utf8");
                    $getOwnTask = mysqli_query($db_con, "select * from tbl_task_master where task_id='$rwTask[task_id]'") or die('Error:' . mysqli_error($db_con));
                    $rwgetOwnTask = mysqli_fetch_assoc($getOwnTask);
                    $TskStpId = $rwgetOwnTask['step_id'];
                    $TskWfId = $rwgetOwnTask['workflow_id'];
                    $TskOrd = $rwgetOwnTask['task_order'];
                    $TskAsinToId = $rwgetOwnTask['assign_user'];
                    $cTaskid = $rwgetOwnTask['task_id'];
                    $cTaskOrd = $TskOrd;
                    $nextTskId = nextTaskToUpdate($cTaskOrd, $TskWfId, $TskStpId, $db_con);
                    mysqli_set_charset($db_con, "utf8");
                    $getNxtTask = mysqli_query($db_con, "select * from tbl_task_master where task_id='$nextTskId'") or die('Error:' . mysqli_error($db_con));
                    $rwgetNextTask = mysqli_fetch_assoc($getNxtTask);
                    $rwgetNextTask['task_order'];
                    //print_r($rwgetNextTask);
                    ?>
                    <div  id="hidden_div">
                        <label><?php if (!empty($nextTskId)) { ?>EDIT/<?php } ?><?php echo $lang['Add_User']; ?></label>
                        <div id="createTaskFlowr">
                        </div>
                        <div class="form-group">
                            <a href="#" id="createOwnflowr" class="btn btn-primary" style="margin-top: -40px; float: right;" data=""><i class="fa fa-plus-circle" title="<?= $lang['Add_more']; ?>"></i></a>
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
                                        mysqli_set_charset($db_con, "utf8");
                                        $user = mysqli_query($db_con, "select * from tbl_user_master where user_id in($sameGroupIDs) order by first_name, last_name asc") or die('Error in uname' . mysqli_error($db_con));
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
                                        mysqli_set_charset($db_con, "utf8");
                                        $user = mysqli_query($db_con, "select * from tbl_user_master where user_id in($sameGroupIDs) order by first_name, last_name asc") or die('Error in uname' . mysqli_error($db_con));
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
                                        mysqli_set_charset($db_con, "utf8");
                                        $user = mysqli_query($db_con, "select * from tbl_user_master where user_id in($sameGroupIDs) order by first_name, last_name asc") or die('Error in uname' . mysqli_error($db_con));
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
                            mysqli_set_charset($db_con, "utf8");
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
                                            <img src="assets/images/avatar.png" alt="Image">
                                        <?php } ?>


                                    </div>
                                    <div class="conversation-text">

                                        <div class="ctext-wrap">
                                            <i><?php echo $rwUsr['first_name'] . ' ' . $rwUsr['last_name']; ?></i>
                                            <p>
                                                <?php
                                                echo '<strong>Comment: </strong>';
                                                if ($ext) {
                                                    ?>
                                                    <a href="anott/view?cid=<?= urlencode(base64_encode($rwcomment['id'])) ?>&id='<?= urlencode(base64_encode($_SESSION['cdes_user_id'])) ?>'" target="_blank"><i class="fa fa-file cmt-file"></i></a><?php
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
            <button type="submit" name="approveTask" value="Yes" class="btn btn-primary waves-effect waves-light" id="hideOnClick"><?php echo $lang['Submit']; ?></button> 
        </div>

    </form>
</div>

<!-- Loader for form submission -->
<div style="display: none; background: rgba(0,0,0,0.5); width: 100%; height: 100%; z-index: 2000; position: fixed; top: 0; left: 0;" id="wait">
    <img src="assets/images/proceed.gif" alt="loading" style="margin-left: 48%; margin-top: 250px; width: 100px; height: 100px; position: fixed;" />
</div>
<script src="<?= BASE_URL ?>assets/js/jquery.min.js"></script>    
<script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>           
<script src="<?= BASE_URL ?>assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?= BASE_URL ?>assets/plugins/parsleyjs/parsley.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('form').parsley();

    });
    $(".selectpicker").select2();
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
    
    // Show loader on form submission
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
        $.post("<?= BASE_URL ?>application/ajax/createownFlow2.php", {ID: createown}, function (result, status) {
            if (status == 'success') {
                $("#createTaskFlowr").html(result);
                // alert(result);
            }
        });
    });
</script>



