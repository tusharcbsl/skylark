<?php
require_once './application/config/database.php';
require_once './loginvalidate.php';

//for user role
$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

$rwgetRole = mysqli_fetch_assoc($chekUsr);

if ($rwgetRole['file_view'] != '1') {
    header('Location: ../index');
}?>
<?php
 $id = base64_decode(urldecode($_GET['file'])); 
$imagePath = mysqli_query($db_con, "SELECT * FROM tbl_document_master where doc_id='$id'") or die("error:" . mysqli_errno($db_con));
$rwimagePath = mysqli_fetch_assoc($imagePath);
$doc_path = "extract-here/". $rwimagePath['doc_path'];
?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>

        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    </head>
    <body>
        <iframe src="<?php echo $doc_path; ?>" frameborder="0"></iframe>



    </body>
</html>
</head>
