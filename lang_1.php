<?php
require_once './application/config/database.php';
require_once './sessionstart.php';
echo $_POST['lang'];
if(isset($_POST['lang'])){
    $_SESSION['lang'] = $_POST['lang'];
}

