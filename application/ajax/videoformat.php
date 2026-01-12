<?php
require_once '../../sessionstart.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout.php");
}
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout.php");
}
if (isset($_SESSION['lang'])) {
    $file = "../../" . $_SESSION['lang'] . ".json";
} else {
    $file = "../../English.json";
}
$data = file_get_contents($file);
$lang = json_decode($data, true);
require_once '../config/database.php';
//$user_id = @$_GET['id']; 
$vid = @$_POST['vid'];
$videopath = mysqli_query($db_con, "select doc_path,old_doc_name,doc_extn,doc_name from tbl_document_master where doc_id = '$vid'") or die('Error in path: ' . mysqli_error($db_con));
$rwvideopath = mysqli_fetch_assoc($videopath);
$fname = $rwvideopath['old_doc_name'];
$doc_extn = $rwvideopath['doc_extn'];
$slid = $rwvideopath['doc_name'];
$filePath = $rwvideopath['doc_path'];
$storage = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id='$slid'") or die('Error');
$rwStor = mysqli_fetch_assoc($storage);

$folderName = "../../temp";
if (!dir($folderName)) {
    mkdir($folderName, 0777, TRUE);
}
$folderName = $folderName . '/' . $_SESSION['cdes_user_id'];
if (!dir($folderName)) {
    mkdir($folderName, 0777, TRUE);
}
$folderName = $folderName . '/' . preg_replace('/[^A-Za-z0-9\-]/', '', $rwStor['sl_name']); //preg_replace('/[^A-Za-z0-9\-]/', '', $string);
if (!dir($folderName)) {
    mkdir($folderName, 0777, TRUE);
}

if (FTP_ENABLED) {

   $localPath = $folderName . '/' . preg_replace('/[^A-Za-z0-9\-]/', '', $fname) . '.' . $doc_extn;

    if (!empty($fname)) {
        require_once '../../classes/ftp.php';
        $ftp = new ftp();
        $ftp->conn("$fileserver", "$port", "$ftpUser", "$ftpPwd");

        $server_path = ROOT_FTP_FOLDER . '/' . $filePath;
        $ftp->get($localPath, $server_path); // download live "$server_path"  to local "$localpath"
        $arr = $ftp->getLogData();
        if ($arr['error'] != "")
        // echo '<h2>Error:</h2>' . implode('<br />', $arr['error']);
            if ($arr['ok'] != "") {
                //echo 'success';
                //header("location:pdf/web/viewer.php?file=$folderName/view_pdf.pdf");
            }
    }
    $localPath = str_replace('../../', '', $localPath);
} else {
    $localPath = 'extract-here/' . $filePath;
}
?>

<div class="modal-body">
    <video width="100%" height="auto" controls>
        <source src="<?php echo BASE_URL.$localPath; ?> " type="video/mp4">
        <source src="<?php echo BASE_URL.$localPath; ?>" type="video/ogg">
        <?php echo $lang['ur_bwsr_ds_nt_sppt_the_elmt']; ?>
    </video>
</div>