<?php
require_once '../../sessionstart.php';
if(!isset($_SESSION['cdes_user_id'])){
    header("location:../../logout.php");
}
require './../config/database.php';

$path = $_POST['PATH'];



?>       
<img src="<?php echo $path; ?>" alt="pic" style="width:100%; height:auto;"/>