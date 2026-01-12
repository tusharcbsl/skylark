<?php
require_once './application/config/database.php';
require_once './loginvalidate.php';
if(isset($_POST['lang'])){
    $_SESSION['lang'] = $_POST['lang'];
    $UpdateLang=mysqli_query($db_con,"UPDATE tbl_user_master SET lang='$_SESSION[lang]' WHERE user_id='$_SESSION[cdes_user_id]'");
}

