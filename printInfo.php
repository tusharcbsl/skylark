<?php
require_once 'sessionstart.php';
require_once 'application/config/database.php';

require_once './loginvalidate.php';
require_once 'anott/fpdf-function.php';
require_once 'classes/ftp.php';
require_once 'application/pages/function.php';
require_once './classes/fileManager.php';

$id1 = base64_decode(urldecode($_GET['id'])); //doc_id
if ($_GET['chk'] == "rw") {
    //@sk(261118):for review log    
    $in_review = " and in_review='0'"; //
    $file = mysqli_query($db_con, "select doc_name, doc_path, doc_extn, old_doc_name from tbl_document_reviewer where doc_id='$id1'") or die('error' . mysqli_error($db_con));
} else {
    //@sk(261118):for review log    
    $in_review = " and in_review='1'"; //
    $file = mysqli_query($db_con, "select doc_name,filename,doc_path,doc_extn,old_doc_name from tbl_document_master where doc_id='$id1'") or die('error' . mysqli_error($db_con));
}
$rwFile = mysqli_fetch_assoc($file);
$fileName = $rwFile['old_doc_name'];
$filePath = $rwFile['doc_path'];
$slid = $rwFile['doc_name'];
$doc_extn = $rwFile['doc_extn'];/* 
$storage = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id='$slid'") or die('Error');
$rwStor = mysqli_fetch_assoc($storage);

$folderName="temp";
if (!dir($folderName)) {
    mkdir($folderName, 0777, TRUE);
}
$folderName=$folderName.'/'.$_SESSION['cdes_user_id'];
if (!dir($folderName)) {
    mkdir($folderName, 0777, TRUE);
}
$folderName = $folderName.'/'.preg_replace('/[^A-Za-z0-9\-]/', '',$rwStor['sl_name']);//preg_replace('/[^A-Za-z0-9\-]/', '', $string);
if (!dir($folderName)) {
    mkdir($folderName, 0777, TRUE);
}
if(FTP_ENABLED){
    $path = $folderName . '/' . preg_replace('/[^A-Za-z0-9\-]/', '',$fileName).'.'.$doc_extn;
    if (!empty($fileName)) {
        require_once './classes/ftp.php';
        $ftp = new ftp();
        $ftp->conn("$fileserver", "$port", "$ftpUser", "$ftpPwd");

        $server_path = ROOT_FTP_FOLDER.'/'.$filePath;

        $ftp->get($path, $server_path); // download live "$server_path"  to local "$localpath"
        $arr = $ftp->getLogData();
        if ($arr['error'] != "")
           // echo '<h2>Error:</h2>' . implode('<br />', $arr['error']);
        if ($arr['ok'] != "") {
            //echo 'success';
            //header("location:pdf/web/viewer.php?file=$folderName/view_pdf.pdf");
        }
    } 
}else{
    $path = 'extract-here/' . $filePath;
} */


$fileManager = new fileManager();
// Connect to file server
$fileManager->conntFileServer();
$localPath = $fileManager->getFile($rwFile);
if(!file_exists($localPath)){
$localPath = "extract-here/" . $filePath;
}

/*
 * if ftp enable
 */
//if(FTP_ENABLED){
//$path="temp/".$_SESSION['cdes_user_id']."/".$rwStor['sl_name']."/".preg_replace('/[^A-Za-z0-9\-]/', '',$fileName).".".$doc_extn;
//}
//else{
//$path='extract-here/'.$filePath;    
//}


/*
 * Print Comment Info
 */
$reviewLog = ""; //Review log string for conacat
$comments = ""; //
$activityLog = "";
$getTiketid = mysqli_query($db_con, "select  ticket_id from tbl_doc_assigned_wf where doc_id='$id1' order by id desc") or die('Error: ' . mysqli_error($db_con));
$rwgetTiketid = mysqli_fetch_assoc($getTiketid);

//get workflow name
$getWfId = mysqli_query($db_con, "select ttm.workflow_id from tbl_doc_assigned_wf daw inner join tbl_task_master ttm on daw.task_id = ttm.task_id where daw.ticket_id='$rwgetTiketid[ticket_id]'");
$rwgetWfId = mysqli_fetch_assoc($getWfId);

$getWfName = mysqli_query($db_con, "select workflow_name from tbl_workflow_master where workflow_id='$rwgetWfId[workflow_id]'");
$rwgetWfName = mysqli_fetch_assoc($getWfName);
$proclist = mysqli_query($db_con, "select * from tbl_doc_assigned_wf where ticket_id='$rwgetTiketid[ticket_id]'");

$rwProclist = mysqli_fetch_assoc($proclist);

if ($_GET['chk'] != 'rw') {
    $comment = mysqli_query($db_con, "select * from tbl_task_comment where tickt_id= '$rwProclist[ticket_id]' order by comment_time desc");
    if (mysqli_num_rows($comment) > 0) {
        $comments = "<h2 align='center'>Comments :- </h2><br><br>"; //comment heading
        while ($rwcomment = mysqli_fetch_assoc($comment)) {
            $usr = mysqli_query($db_con, "select first_name, last_name,profile_picture from tbl_user_master where user_id='$rwcomment[user_id]'");
            $rwUsr = mysqli_fetch_assoc($usr);
            $ext = pathinfo($rwcomment['comment'], PATHINFO_EXTENSION);

            if (!$ext) {
                $comments .= '<strong>' . "Comment : " . '</strong>' . $rwcomment['comment'] . '<br>';
                $comments .= '<strong>' . $rwUsr['first_name'] . ' ' . $rwUsr['last_name'] . '</strong>' . '<br>';
                $comments .= '<strong>' . "Action : " . '</strong>' . $rwcomment['task_status'] . '<br>';
                $comments .= '<strong>' . "Date : " . '</strong>' . date("j F, Y, H:i", strtotime($rwcomment['comment_time'])) . '<br><br>';
            }
        }
    }
}


$pdflogSql = "select * from tbl_ezeefile_logs_wf where doc_id='$id1' ";
$pdflog = mysqli_query($db_con, $pdflogSql);
if (mysqli_num_rows($pdflog) > 0) {
    $activityLog = "<h2 align='center'>Activity Logs :- </h2><br><br>";
    while ($rwpdflog = mysqli_fetch_assoc($pdflog)) {
        $activityLog .= '<span><strong> Action : </strong>' . $rwpdflog['action_name'] . '<br>';
        $activityLog .= '<strong> Action By : </strong>' . $rwpdflog['user_name'] . '<br>';
        $activityLog .= '<strong> Action Time : </strong>' . date('d M Y, H:i', strtotime($rwpdflog['start_date'])) . '</span><br><br>';
    }
}

$rlog_sql = "select rl.*,u.first_name,u.last_name from tbl_reviews_log rl left join tbl_user_master u on rl.user_id=u.user_id where 1=1 " . $in_review;
if ($_SESSION['cdes_user_id'] != 1) {
    //   $rlog_sql.=" and rl.user_id='$_SESSION[cdes_user_id]'";   
}
$rlog_sql .= " and rl.doc_id='$id1' order by id desc";

$rlog_query = mysqli_query($db_con, $rlog_sql);

if (mysqli_num_rows($rlog_query) > 0) {
    $reviewLog = "<h2>Review Logs :- </h2><br><br>"; //Review log string for conacat
    while ($rlog_res = mysqli_fetch_assoc($rlog_query)) {
        $reviewLog .= '<span><strong> Action : </strong> ' . $rlog_res['action_name'] . '<br>';
        $reviewLog .= '<strong> Action By : </strong> ' . $rlog_res['first_name'] . ' ' . $rlog_res[last_name] . '<br>';
        $reviewLog .= '<strong> Action Time : </strong>' . date('d M Y, H:i', strtotime($rlog_res['start_date'])) . '</span><br><br>';
    }
}

//@sk41218 : formatting content              
$reviewLog = ($reviewLog) ? $reviewLog . "<br><br>" : '';
$activityLog = ($activityLog) ? $activityLog . "<br><br>" : '';
$comments = ($comments) ? $comments . "<br><br>" : '';
$content = $reviewLog .
    $activityLog .
    $comments;



//echo $content.'okk';
//die; 


// Prepare file for final print.
printPdf($localPath, $content);
