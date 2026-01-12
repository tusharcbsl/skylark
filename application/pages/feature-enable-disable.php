<?php
//doc share feature check
$docshare = mysqli_query($db_con, "SELECT * FROM `tbl_default_docshare_setting`");
$rwdocshare = mysqli_fetch_assoc($docshare);
//document expiry check
$getexpInfo = mysqli_query($db_con, "SELECT * FROM `tbl_expiry_default_setting`");
$rwgetexpInfo = mysqli_fetch_assoc($getexpInfo);
//document retention check
$getInfo = mysqli_query($db_con, "SELECT * FROM `tbl_retention_default_setting`");
$rwgetInfo = mysqli_fetch_assoc($getInfo);
//document password policy check
$getInfopass = mysqli_query($db_con, "SELECT * FROM `tbl_pass_policy`");
$rwInfoPolicy = mysqli_fetch_assoc($getInfopass);
?>

