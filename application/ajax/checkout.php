<?php

require_once '../../sessionstart.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout.php");
}
require './../config/database.php';

$checkoutId = preg_replace("/[^0-9 ]/", "",  $_POST['CHECKOUT']);

$checkout = mysqli_query($db_con, "UPDATE tbl_document_master set checkin_checkout=0 WHERE doc_id='$checkoutId'");
?>       