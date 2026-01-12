<?php
require_once '../../sessionstart.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout.php");
}
require './../config/database.php';
if (isset($_SESSION['lang'])) {
    $file = "../../" . $_SESSION['lang'] . ".json";
} else {
    $file = "../../English.json";
}
$data = file_get_contents($file);
$lang = json_decode($data, true);
$keyname = $_POST['key'];
$slperm = $_POST['slperm'];
if (!empty($_POST["keyword"]) || $_POST["keyword"] == '0') {
    $query = mysqli_query($db_con, "SELECT distinct $keyname FROM tbl_document_master WHERE doc_name ='$slperm' and $keyname like '" . $_POST["keyword"] . "%' AND flag_multidelete='1' ORDER BY $keyname LIMIT 0,5");
    $resultset = array();
    while ($row = mysqli_fetch_assoc($query)) {
        $resultset[] = $row[$keyname];
    }
    if (!empty($resultset)) {
        ?>
        <ul id="keyword-list">
            <?php
            foreach ($resultset as $keywords) {
                ?>
                <li onClick="selectKeyword('<?php echo $keywords; ?>', '<?php echo $keyname; ?>', );"><?php echo $keywords; ?></li>
            <?php } ?>
        </ul>
    <?php } else {
        ?>
        <ul id="keyword-list">
            <li><?php echo $lang['No_Rcrds_Fnd']; ?></li>
        </ul>
        <?php
    }
}
?>