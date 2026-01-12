<?php
require_once '../../sessionstart.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout");
}

require_once '../config/database.php';
//for user role
/*
  $chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:'. mysqli_error($db_con));

  $rwgetRole = mysqli_fetch_assoc($chekUsr);

  // echo $rwgetRole['dashboard_mydms']; die;
  if($rwgetRole['workflow_initiate_file'] != '1'){
  header('Location: ../../index');
  }
 */
if (isset($_SESSION['lang'])) {
    $file = "../../" . $_SESSION['lang'] . ".json";
} else {
    $file = "../../English.json";
}
//for user role
$data = file_get_contents($file);
$lang = json_decode($data, true);
$cmnt = $_POST['CMNT'];
$tktId = $_POST['TKTID'];
$tskId = $_POST['TSKID'];
?>

<?php
if (!empty($cmnt)) {
    $user_id = $_SESSION['cdes_user_id'];
    $cmttask = "INSERT INTO tbl_task_comment (`tickt_id`, `user_id`, `comment`, task_status, `comment_time`, task_id) VALUES ('$tktId', '$user_id','$cmnt', 'comment', '$date', '$tskId')";
    $run = mysqli_query($db_con, $cmttask) or die('Error query failed: ' . mysqli_error($db_con));
    echo '<script>uploadSuccess("process_task?id=' . urlencode($_GET['id']) . '", "Comment Added Successfully !");</script>';
}
?>

<div id="comentAdd">
    <?php
    $proclist = mysqli_query($db_con, "select * from tbl_doc_assigned_wf where ticket_id='$tktId'");

    $rwProclist = mysqli_fetch_assoc($proclist);

    $comment = mysqli_query($db_con, "select comment_time, comment,user_id from tbl_task_comment where tickt_id= '$rwProclist[ticket_id]' order by comment_time desc");
    if (mysqli_num_rows($comment) > 0) {
        while ($rwcomment = mysqli_fetch_assoc($comment)) {

            $usr = mysqli_query($db_con, "select first_name, last_name,profile_picture from tbl_user_master where user_id='$rwcomment[user_id]'");
            $rwUsr = mysqli_fetch_assoc($usr);
            ?>
            <div class="comment-list-item">   
                <li class="clearfix">
                    <div class="ctext-wrap">
                        <span style="float:left;">   <?php echo $rwcomment['comment']; ?> </span> <br/>
                        <span style="float:right;">
                            <i><?php echo $rwUsr['first_name'] . ' ' . $rwUsr['last_name']; ?></i>
                            <br/>
                            <?php echo date("j F, Y, H:i", strtotime($rwcomment['comment_time'])); ?></span>
                    </div>
                </li>
            </div>

        <?php
        }
    } else {
        ?>
        <div class="comment-list-item"><?php echo $lang['No_Cmnt']; ?></div>

<?php } ?>
</div>