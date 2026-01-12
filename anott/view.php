<?php
/* ob_start();
session_start(); */
require_once '../sessionstart.php';
require_once '../application/config/database.php';



if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../logout.php");
}
//  require_once '../application/pages/head.php';
//for user role
$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

$rwgetRole = mysqli_fetch_assoc($chekUsr);

$uid = base64_decode(urldecode($_GET['id']));
if ($uid != $_SESSION['cdes_user_id']) {
    // header('Location:../index');
}
if ($rwgetRole['pdf_annotation'] != '1') {
   // header('Location: ../index');
}


$cid = base64_decode(urldecode($_GET['cid'])); //doc asign id
//$cid=51;
$comment=mysqli_fetch_assoc(mysqli_query($db_con,"select * from tbl_task_comment where id='$cid'"));
$cfile='../application/'.$comment['comment'];
$ext= strtolower(pathinfo($cfile, PATHINFO_EXTENSION));

if($ext=='doc' || $ext=='docx'){
    header("location:../viewcomment?id=".urlencode(base64_encode($cid))); 
}

switch( $ext ) {
    case "gif": $ctype="image/gif"; break;
    case "png": $ctype="image/png"; break;
    case "jpeg":
    case "jpg": $ctype="image/jpeg"; break;
    case "pdf": $ctype="application/pdf"; break;
    case "txt": $ctype="text/plain"; break;
    default:
}

header('Content-type: ' . $ctype);
readfile($cfile);



