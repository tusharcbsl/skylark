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

// $cmnt = $_POST['CMNT'];
// $tktId = $_POST['TKTID'];
// $tskId = $_POST['TSKID'];



$tktId = xss_clean($_POST['tktid']);
$tskId = preg_replace("/[^0-9 ]/", "", $_POST['tskid']);
$cfp = base64_decode($_POST['cfp']);
//echo $cfp;
//var_dump($_POST);
//die;


// file
 if (!empty($_FILES['comment_file']['name'])) {
        $file_name = $_FILES['comment_file']['name'];
        $file_size = $_FILES['comment_file']['size'];
        $file_type = $_FILES['comment_file']['type'];
        $file_tmp = $_FILES['comment_file']['tmp_name'];
        
        $extn = substr($file_name, strrpos($file_name, '.') + 1);
        $fname = substr($file_name, 0, strrpos($file_name, '.'));
        $fileExtn = substr($file_name, strrpos($file_name, ".") + 1);
        $fextn = strtolower($fileExtn);
        $allowExtn = array('pdf', 'jpg', 'jpeg', 'png', 'gif','txt','doc','docx');
        if(in_array($fextn, $allowExtn)){
        
            $nfilename = preg_replace('/[^A-Za-z0-9_\-]/', '', $file_name);

            $filenameEnct = urlencode(base64_encode($nfilename));
            $filenameEnct = preg_replace('/[^A-Za-z0-9_\-]/', '', $filenameEnct);
            $filenameEnct = $filenameEnct . '.' . $extn;
            $filenameEnct = time() . $filenameEnct;

            // temporary upload
            $uploaddir = '../'.$cfp.'/comment';
            if (!dir($uploaddir)) {
                mkdir($uploaddir, 0777, TRUE);
            }
            $upd2=$cfp.'/comment/'. $filenameEnct;

            
            if (!dir($uploaddir)) {
                mkdir($uploaddir, 0777, TRUE);
            }

            $uploaddir = $uploaddir.'/'.$filenameEnct;
            $upload = move_uploaded_file($file_tmp, $uploaddir) or die(print_r(error_get_last()));
            if ($upload) {
            $commnetasfile = $upd2;
               
            }
        }
        else{
            echo '<script>alert("Only pdf, jpg, gif, png, text, doc and docx files are allowed.");</script>';
        }
        
    }
  $cmnt = mysqli_real_escape_string($db_con, $_POST['comment']);   
?>

<?php
if (!empty($cmnt) || !empty($commnetasfile)) {
    $user_id = $_SESSION['cdes_user_id'];
    $cmttask = "INSERT INTO tbl_task_comment (`tickt_id`, `user_id`, `comment`, task_status, `comment_time`, `task_id`,`comment_desc`) VALUES ('$tktId', '$user_id','$commnetasfile', 'comment', '$date', '$tskId', '$cmnt')";

    $run = mysqli_query($db_con, $cmttask) or die('Error query failed: ' . mysqli_error($db_con));
    //echo '<script>uploadSuccess("process_task?id=' . urlencode($_GET['id']) . '", "Comment Added Successfully !");</script>';
}
?>
<link href="../assets/css/components.css" rel="stylesheet" type="text/css"/>
<style>
    .anotecoment{
        padding: 0;
        color: #020202;
    }
</style>

<div id="comentAdd">
    <?php
    $proclist = mysqli_query($db_con, "select * from tbl_doc_assigned_wf where ticket_id='$tktId'");

    $rwProclist = mysqli_fetch_assoc($proclist);

    $comment = mysqli_query($db_con, "select id,comment_time, comment,user_id from tbl_task_comment where tickt_id= '$rwProclist[ticket_id]' order by comment_time desc");
    if (mysqli_num_rows($comment) > 0) {
        while ($rwcomment = mysqli_fetch_assoc($comment)) {
            $ext=pathinfo($rwcomment['comment'], PATHINFO_EXTENSION);
            $usr = mysqli_query($db_con, "select first_name, last_name,profile_picture from tbl_user_master where user_id='$rwcomment[user_id]'");
            $rwUsr = mysqli_fetch_assoc($usr);
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
                                <?php if($ext){?>
                                <span><a href="view?cid=<?=urlencode(base64_encode($rwcomment['id']))?>" target="_blank"><i class="fa fa-file cmt-file"></i></a></span><?php } else{?><span><?php echo $rwcomment['comment']; ?></span><?php }?>
                                 <br/>
                                <span>
                                    <?php echo $rwUsr['first_name'] . ' ' . $rwUsr['last_name']; ?>
                                    <br/>
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
