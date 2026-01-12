<?php
require_once './loginvalidate.php';
require_once './classes/ftp.php';

session_start();

// (A) FUNCTION TO FORMULATE SERVER RESPONSE
function verbose($ok=1,$info=""){
  // THROW A 400 ERROR ON FAILURE
  if ($ok==0) { http_response_code(400); }
  die(json_encode(["ok"=>$ok, "info"=>$info]));
}

function PageCount_DOCX($file) {
    $pageCount = 0;

    $zip = new ZipArchive();

    if ($zip->open($file) === true) {
        if (($index = $zip->locateName('docProps/app.xml')) !== false) {
            $data = $zip->getFromIndex($index);
            $zip->close();
            $xml = new SimpleXMLElement($data);
            $pageCount = $xml->Pages;
        }
        $zip->close();
    }

    return $pageCount;
}

function count_pages($pdfname) {

    $pdftext = file_get_contents($pdfname);
    $num = preg_match_all("/\/Page\W/", $pdftext, $dummy);

    return $num;
}

// (B) INVALID UPLOAD
if (empty($_FILES) || $_FILES['file']['error']) {
  verbose(0, "Failed to move uploaded file.");
}

$slID = $_SESSION['upld_slid'];
$sl = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slID'");
$rwSl = mysqli_fetch_assoc($sl);
$storageName = $rwSl['sl_name'];
$storageName = str_replace(" ", "", $storageName);
$storageName = preg_replace('/[^A-Za-z0-9\-]/', '', $storageName);

if (!is_dir("uploadLogs")) {
    mkdir("uploadLogs", 0777, true);
}
$logs = fopen('uploadLogs/' . date('Ymdhis') . '.dat', "a");


// (C) UPLOAD DESTINATION
// ! CHANGE FOLDER IF REQUIRED !
$filePath = __DIR__ . DIRECTORY_SEPARATOR . "extract-here" . DIRECTORY_SEPARATOR . $storageName;

if (!file_exists($filePath)) { 
  if (!mkdir($filePath, 0777, true)) {
    verbose(0, "Failed to create $filePath");
  }
}
$filename = isset($_REQUEST["name"]) ? $_REQUEST["name"] : $_FILES["file"]["name"];
$size = isset($_REQUEST["size"]) ? $_REQUEST["size"] : $_FILES["file"]["size"];
$targetPath = $filePath . DIRECTORY_SEPARATOR . $filename;
$extn = substr($filename, strrpos($filename, '.') + 1);
$fname = substr($filename, 0, strrpos($filename, '.'));

$fname1 = preg_replace('/[^A-Za-z0-9_\-]/', '', $fname);
// $filenameEnct=$fname.'.'.$extn;// urlencode(base64_encode($fname)).'.'.$extn;
$filenameEnct = urlencode(base64_encode($fname1));
$filenameEnct = preg_replace('/[^A-Za-z0-9_\-]/', '', $filenameEnct) . '.' . $extn;
$filePath = $filePath . DIRECTORY_SEPARATOR . $filenameEnct;

$check = mysqli_query($db_con, "select * from tbl_document_master where old_doc_name='$filename' and doc_name='$slID' and flag_multidelete=1");
$rwCheck = mysqli_fetch_assoc($check);
if (mysqli_num_rows($check) > 0) {
  fwrite($logs, '<p class="text-danger">' . $date . " : $filename : already exist in $storageName storage.\n</p>");
  verbose(0, "Same File already exist! '$filename'");
}

// (D) DEAL WITH CHUNKS
$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
$out = @fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
if ($out) {
  $in = @fopen($_FILES['file']['tmp_name'], "rb");
  if ($in) {
    while ($buff = fread($in, 4096)) { fwrite($out, $buff); }
  } else {
    verbose(0, "Failed to open input stream");
  }
  @fclose($in);
  @fclose($out);
  @unlink($_FILES['file']['tmp_name']);
} else {
  verbose(0, "Failed to open output stream");
}

// (E) CHECK IF FILE HAS BEEN UPLOADED
if (!$chunks || $chunk == $chunks - 1) {
  rename("{$filePath}.part", $filePath);

  if ($extn == "pdf") {
      $noofPages = count_pages($filePath);
  } elseif ($extn == "docx") {
      $noofPages = PageCount_DOCX($filePath);
  } else {
      $noofPages = 1;
  }

  $query = "insert into tbl_document_master set doc_name='$slID',old_doc_name='$filename',doc_extn='$extn',doc_path='$storageName/$filenameEnct',uploaded_by='$_SESSION[cdes_user_id]',doc_size='$size',dateposted='$date',noofpages='$noofPages',mail_sent='0'  ";
  $fileins = mysqli_query($db_con, $query) or die('Error' . mysqli_error($db_con));
  $doc_id = mysqli_insert_id($db_con);

  $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$slID','Document $filename uploaded in $storageName','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));

  // $ex= exec("php ". ROOT_PATH ."/upload-file-to-ftp.php 2>&1",$out);

}

verbose(1, "Upload OK");