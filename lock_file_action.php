<?php

if (isset($_POST['lockfile'], $_POST['token'])) {
    mysqli_autocommit($db_con, FALSE);
    $docid = $_POST['lockdoc_id'];
    $userids = isset($_POST['userid']) ? $_POST['userid'] : array(); //if user select create array else create array
    array_push($userids, 1); //add super user every lock file
    array_push($userids, $_SESSION['cdes_user_id']); //add current user in user id
    $userdata = array_unique($userids);
    for ($i = 0; $i < count($userdata); $i++) {
        $status = ($_SESSION['cdes_user_id'] == $userdata[$i]) ? 1 : 0;
        $qry = mysqli_query($db_con, "INSERT INTO `tbl_locked_file_master`(`doc_id`,`user_id`,`is_locked`,`locked_date`) values('$docid','$userdata[$i]','$status','$date')")or die(mysqli_error($db_con));
    }
    $slid = !empty($slid) ? $slid : 0;

    if ($qry) {

        $qrydocmaster = mysqli_query($db_con, "SELECT * FROM `tbl_document_master` WHERE doc_id='$docid'")or die(mysqli_error($db_con));
        $fetchdocdata = mysqli_fetch_assoc($qrydocmaster);
        $files = $fetchdocdata['old_doc_name'];
        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `doc_id`,`action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$slid', '$docid','Document $files is locked','$date',null,'$host',NULL)") or die('error : ' . mysqli_error($db_con));

        mysqli_commit($db_con);
        echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['doc_locked_successfully'] . '");</script>';
    } else {
        echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI'])  . '","' . $lang['doc_locked_failed'] . '");</script>';
    }
}
if (isset($_POST['unlockfile'], $_POST['token'])) {
    mysqli_autocommit($db_con, FALSE);
    $docid = $_POST['unlockdoc_id'];

    $qry = mysqli_query($db_con, "UPDATE `tbl_locked_file_master`  SET is_active='0'  WHERE doc_id='$docid'")or die(mysqli_error($db_con));
    $updaterequest = mysqli_query($db_con, "UPDATE `tbl_lock_file_request_master` SET request_status='1' WHERE doc_id='$docid'");
    $slid = !empty($slid) ? $slid : 0;


    if ($qry) {
        $qrydocmaster = mysqli_query($db_con, "SELECT * FROM `tbl_document_master` WHERE doc_id='$docid'")or die(mysqli_error($db_con));
        $fetchdocdata = mysqli_fetch_assoc($qrydocmaster);
        $files = $fetchdocdata['old_doc_name'];
        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `doc_id`,`action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$slid', '$docid','Document $files is unlocked','$date',null,'$host',NULL)") or die('error : ' . mysqli_error($db_con));

        mysqli_commit($db_con);
        echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI'])  . '","' . $lang['doc_unlocked_successfully'] . '");</script>';
    } else {
        echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['doc_unlocked_failed'] . '");</script>';
    }
}

if (isset($_POST['req_unlockfile'], $_POST['token'])) {
    mysqli_autocommit($db_con, FALSE);
    $docid = $_POST['req_unlockdoc_id'];
    $validateqry=mysqli_query($db_con, "SELECT * FROM `tbl_lock_file_request_master` WHERE doc_id='$docid' and requester_userid='$_SESSION[cdes_user_id]' and request_status='0'")or die(mysqli_error($db_con));
    if(mysqli_num_rows($validateqry)==0)
    {
    $qry = mysqli_query($db_con, "SELECT * FROM `tbl_locked_file_master` WHERE doc_id='$docid' and is_locked='1' and is_active='1'")or die(mysqli_error($db_con));
    $fetchdata = mysqli_fetch_assoc($qry);
    $userid = $fetchdata['user_id'];

    $insertqry = mysqli_query($db_con, "INSERT INTO `tbl_lock_file_request_master`(`doc_id`,`locker_userid`,`requester_userid`,`request_date`)VALUES('$docid','$userid','$_SESSION[cdes_user_id]','$date')");
    require_once './mail.php';

    $qryusermaster = mysqli_query($db_con, "SELECT * FROM `tbl_user_master` WHERE user_id='$userid'")or die(mysqli_error($db_con));
    $fetchuserdata = mysqli_fetch_assoc($qryusermaster);
  
    $username = $_SESSION['admin_user_name'] . ' ' . $_SESSION['admin_user_last'];
    $to = $fetchuserdata['user_email_id'];
    $toname = $fetchuserdata['first_name'] . ' ' . $fetchuserdata['last_name'];

    $qrydocmaster = mysqli_query($db_con, "SELECT * FROM `tbl_document_master` WHERE doc_id='$docid'")or die(mysqli_error($db_con));
    $fetchdocdata = mysqli_fetch_assoc($qrydocmaster);
    $files = $fetchdocdata['old_doc_name'];
    $sl_id = $fetchdocdata['doc_name'];

    /* -------storage name------------- */
    $qrystoragemaster = mysqli_query($db_con, "SELECT * FROM `tbl_storage_level` WHERE sl_id='$sl_id'")or die(mysqli_error($db_con));
    $fetchstoragedata = mysqli_fetch_assoc($qrystoragemaster);
    $storagename = $fetchstoragedata['sl_name'];


    if (requestUnlockDocument($projectName, $username, $to, $toname, $files, $storagename)) {
        if ($insertqry) {
            $slid = !empty($slid) ? $slid : 0;
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `doc_id`,`action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$slid', '$docid','Document $files unlocked request sent','$date',null,'$host',NULL)") or die('error : ' . mysqli_error($db_con));
            mysqli_commit($db_con);
            echo "ok1";
            echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI'])  . '","' . $lang['unlock_req_sent_successfully'] . '");</script>';
        } else {
            echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI'])  . '","' . $lang['unlock_req_sent_failed'] . '");</script>';
        }
    } else {
        echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI'])  . '","' . $lang['unlock_req_sent_failed'] . '");</script>';
    }
    }else{
       echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI'])  . '","'.$lang['alrady_request_unlockfile'].'");</script>'; 
    }
}