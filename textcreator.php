<?php

require_once './sessionstart.php';
require 'application/config/database.php';
$text = $_POST['TEXT'];
$text = mysqli_real_escape_string($db_con, $text);
$path = $_POST['PATH'];
$path = mysqli_real_escape_string($db_con, $path);
$doc_id = $_POST['ID'];
$doc_id = mysqli_real_escape_string($db_con, $doc_id);
$path = $path . '/TXT/';
if (!dir($path)) {
    mkdir($path, 0777, TRUE) or die("Error local folder:" . print_r(error_get_last()));
}
echo $filePath = $path . $doc_id . '.txt';
$myfile = fopen("$filePath", "w") or die("Unable to open file!");
$txt = $text;
fwrite($myfile, $txt);
fclose($myfile);
?>

