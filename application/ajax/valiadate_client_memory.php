<?php

require_once '../../sessionstart.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout");
}

require './../config/database.php';
require './../config/validate_client_db.php';
$decKey = decryptLicenseKey($clientKey);
$decKey = explode("%", $decKey);

//function fileSize
$check_validity_qry = mysqli_query($db_valid_con, "select * from  tbl_client_master where client_id='$decKey[1]' and license_key='$clientKey'"); //Query get validity of particular company user
$validity_date = mysqli_fetch_assoc($check_validity_qry); //fetch validity timestamp from client table
// $plantype_qry = mysqli_query($db_valid_con, "select * from tbl_plantype where plantype='$validity_date[plan_type]'");
// $total_memory_allot = mysqli_fetch_assoc($plantype_qry);

$size = $validity_date['total_memory']; //total user allow 
$selected_file = $_POST['size'];

// $size=$_SESSION['total_memory'];
function formatSizeUnits($size) {
//        $newSize= str_replace(" ", "-", $size);
//        $type=explode("-",$newSize);
    //print_r($type);
    if ($type[1] == "MB") {
        $bytes = 1000 * 1000 * $size;
    } else {
        $bytes = 1000 * 1000 * 1000 * $size;
    }


    return $bytes;
}

$total = mysqli_query($db_con, "select sum(doc_size) as totals from `tbl_document_master`");
$total_fsize = mysqli_fetch_assoc($total);
$total_memory_consume = $total_fsize['totals'];

function remaingSizeConvert($bytes) {
    if ($bytes >= 1000000000000) {
        $bytes = number_format($bytes / 1000000000000, 2) . ' TB';
    } elseif ($bytes >= 1000000000) {
        $bytes = number_format($bytes / 1000000000, 2) . ' GB';
    } elseif ($bytes >= 1000000) {
        $bytes = number_format($bytes / 1000000, 2) . ' MB';
    } elseif ($bytes >= 1000) {
        $bytes = number_format($bytes / 1000, 2) . ' KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' bytes';
    } else {
        $bytes = '0 bytes';
    }

    return $bytes;
}

$total_memory_alot = formatSizeUnits($size);

$free_memory = $total_memory_alot - $total_memory_consume;
if ($total_memory_alot <= ($total_memory_consume + $selected_file)) {

    $final = json_encode(array("status" => "false", "msg" => "File Size(" . remaingSizeConvert($selected_file) . ")Larger Than Available Storage(" . remaingSizeConvert($free_memory) . ")"));
} else {
    $free_memory = $total_memory_alot - ($total_memory_consume + $selected_file);
    $final = json_encode(array("status" => "true", "msg" => "Remaining Storage(" . remaingSizeConvert($free_memory) . ") After Uploading This File"));
}
echo $final;

function decryptLicenseKey($licenseKey) {
    $key = '987654123';
    $c = base64_decode($licenseKey);
    $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
    $iv = substr($c, 0, $ivlen);
//$hmac = substr($c, $ivlen, $sha2len=32);
    $ciphertext_raw = substr($c, $ivlen/* +$sha2len */);
    $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, $options = OPENSSL_RAW_DATA, $iv);
    return $original_plaintext;
}
