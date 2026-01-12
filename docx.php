<?php
require_once './sessionstart.php';
require_once './application/config/database.php';
require_once './loginvalidate.php';

//for user role
$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));
$uid = base64_decode(urldecode($_GET['uid']));
$rwgetRole = mysqli_fetch_assoc($chekUsr);
if($uid!=$_SESSION['cdes_user_id']){
    header('Location:index');
}
if ($rwgetRole['doc_file'] != '1') {
    header('Location: ../index');
}?>
<?php
$id = base64_decode(urldecode($_GET['file']));

$file = mysqli_query($db_con, "SELECT * FROM tbl_document_master where doc_id='$id'") or die("error:" . mysqli_errno($db_con));
$rwFile = mysqli_fetch_assoc($file);
$fileName = $rwFile['old_doc_name'];
$filePath = $rwFile['doc_path'];
$slid=$rwFile['doc_name'];
$doc_extn=$rwFile['doc_extn'];
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
    $localPath = $folderName . '/' . preg_replace('/[^A-Za-z0-9\-]/', '',$fileName).'.'.$doc_extn;
    if (!empty($fileName)) {
        require_once './classes/ftp.php';
        $ftp = new ftp();
        $ftp->conn("$fileserver", "$port", "$ftpUser", "$ftpPwd");

        $server_path = ROOT_FTP_FOLDER.'/'.$filePath;
        $ftp->get($localPath, $server_path); // download live "$server_path"  to local "$localpath"
        $arr = $ftp->getLogData();
        if ($arr['error'] != "")
           // echo '<h2>Error:</h2>' . implode('<br />', $arr['error']);
        if ($arr['ok'] != "") {
            //echo 'success';
            //header("location:pdf/web/viewer.php?file=$folderName/view_pdf.pdf");
        }
    }
}else{
    $localPath = 'extract-here/' . $filePath;
} 

/*
 * file download end
 */
?>

<html>
    <head>

        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    </head>
    <body>

        <iframe src="<?php echo $localPath; ?>" frameborder="0"></iframe>
    </body>
</html>
</head>
<script>
    window.onbeforeunload = function () {

        $.post("application/ajax/removeTempFiles.php", {filepath:"<?php echo '../'.$localPath; ?>"}, function (result) {

        });
        return;
    };
</script>
