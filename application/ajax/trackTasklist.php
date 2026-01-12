<?php
require_once '../../sessionstart.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout");
}
require './../config/database.php';

//for user role
$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

$rwgetRole = mysqli_fetch_assoc($chekUsr);

if ($rwgetRole['workflow_task_track'] != '1') {
    header('Location: ../../index');
}

if(!isset($_POST['token'], $_POST['ID'])){
   echo "Unauthrized Access";  
}

$tkt = preg_replace("/[^0-9a-zA-Z_ ]/", "", $_POST['ID']);
?>       
<?php
mysqli_set_charset($db_con, "utf8");
$taskTrack = "SELECT * FROM tbl_doc_assigned_wf where ticket_id = '$tkt'";
$runTskTrk = mysqli_query($db_con, $taskTrack) or die('Error' . mysqli_error($db_con));
if (isset($_SESSION['lang'])) {
    $file = "../../" . $_SESSION['lang'] . ".json";
} else {
    $file = "../../English.json";
}
//for user role
$data = file_get_contents($file);
$lang = json_decode($data, true);
?>
<div class="row">
    <div class="col-sm-12">
        <ol class="breadcrumb">
            <li>
                <a href="taskTrack"><span><?php echo $lang['Tkt_Num']; ?> :</span> <?php echo $tkt; ?></a>
            </li>
        </ol>
    </div>
</div>
<div class="stepwizard">
    <div class="stepwizard-row setup-panel">
        <?php
        $i = 1;
        while ($rwgettsk = mysqli_fetch_assoc($runTskTrk)) {
            $getTaskOrdr = mysqli_query($db_con, "select * from  tbl_task_master where task_id = '$rwgettsk[task_id]'") or die('Error:' . mysqli_error($db_con));
            $rwgetTaskOrdr = mysqli_fetch_assoc($getTaskOrdr);
            ?>
            <div class="stepwizard-step">
                <a href="#step-<?php echo $i; ?>" type="button" class="btn <?php
                if ($rwgettsk['task_status'] == 'Approved' || $rwgettsk['task_status'] == 'Complete' || $rwgettsk['task_status'] == 'Done' || $rwgettsk['task_status'] == 'Processed') {
                    echo'btn-success';
                } else if ($rwgettsk['task_status'] == 'Pending') {
                    echo 'btn-warning';
                } else {
                    echo'btn-danger';
                }
                ?> btn-circle" <?php
//                   if ($rwgettsk['task_status'] != 'Approved') {
//                       echo'ok';
//                   }
                   ?>>T<?php echo $rwgetTaskOrdr['task_order']; ?></a>
                <h5><?php echo $rwgetTaskOrdr['task_name']; ?></h5>
            </div>
            <?php
            $i++;
        }
        ?>
    </div>
</div>
<?php

$gettsk = mysqli_query($db_con, "select b.assign_user,a.task_status,a.task_id,a.doc_id,a.start_date,a.end_date,a.task_deadline,a.assign_by,a.action_by,a.action_time,a.ticket_id,a.NextTask from tbl_doc_assigned_wf a inner join tbl_task_master b on a.task_id=b.task_id where ticket_id = '$tkt'") or die('Error:' . mysqli_error($db_con));
$j = 1;

while ($rwgetsk = mysqli_fetch_assoc($gettsk)) {
    $assign_user_id = $rwgetsk['assign_user'];
    $usr = mysqli_query($db_con, "select first_name, last_name,profile_picture from tbl_user_master where user_id='$rwgetsk[action_by]' OR user_id='$assign_user_id'") or die('Error:' . mysqli_error($db_con));
    $rwUsr = mysqli_fetch_assoc($usr);
    ?>
    <div class="row setup-content" id="step-<?php echo $j; ?>">
        <div class="container">
            <label> <?php
                if (($rwgetsk['task_status'] == 'Approved') || ($rwgetsk['task_status'] == 'Complete') || ($rwgetsk['task_status'] == 'Done') || ($rwgetsk['task_status'] == 'Processed')) {
                    echo '<span class="label label-success">' . $rwgetsk['task_status'] . ' By : ' . $rwUsr['first_name'] . ' ' . $rwUsr['last_name'] . '</span>';
                } elseif (($rwgetsk['task_status'] == 'Rejected') || ($rwgetsk['task_status'] == 'Aborted')) {
                    echo '<span class="label label-danger">' . $rwgetsk['task_status'] . ' By : ' . $rwUsr['first_name'] . ' ' . $rwUsr['last_name'] . '</span>';
                } else {
                    echo '<label>' . $lang['Task_Status'] . ':</label> <span class="label label-warning">' . $rwgetsk['task_status'] . ' On : ' . $rwUsr['first_name'] . ' ' . $rwUsr['last_name'] . '</span>';
                }
                ?></label>
            <?php
            if (($rwgetsk['task_status'] == 'Approved') || ($rwgetsk['task_status'] == 'Rejected') || ($rwgetsk['task_status'] == 'Aborted') || ($rwgetsk['task_status'] == 'Complete') || ($rwgetsk['task_status'] == 'Processed') || ($rwgetsk['task_status'] == 'Done')) {
                echo '<p><label> On : </label> ' . $rwgetsk['action_time'] . '</p>';
            } else {
                echo '';
            }
            ?>
            <div class="chat-conversation">
                <ul class="conversation-list nicescroll" style="height: Auto;">
                    <?php
                    $comment = mysqli_query($db_con, "select * from tbl_task_comment where tickt_id='$tkt' and task_id='$rwgetsk[task_id]'");
                    if (mysqli_num_rows($comment) > 0) {
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
                                        <p><?php echo $rwcomment['comment']; ?></p>
                                        <p><?php echo date("d - M - y, H:i A", strtotime($rwcomment['comment_time'])); ?></p>
                                    </div>
                                </div>
                            </li>
                            <?php
                        }
                    }
                    ?>
                </ul>

            </div>
        </div>
    </div>
    <?php
    $j++;
}
?>
<!--Form Wizard-->
<script src="assets/jsCustom/wizard.js"></script>


