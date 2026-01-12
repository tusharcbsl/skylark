<?php
require_once './application/config/database.php';
require_once './application/pages/function.php';
require_once './classes/fileManager.php';

$id = base64_decode(urldecode($_GET['file']));
$file_ids = explode(" ",$id);
$emailId = base64_decode(urldecode($_GET['em']));
// var_dump($file_ids);
// die();
foreach ($file_ids as $ids) 
{
    $current_url = BASE_URL.'downloaddoc?file='.urlencode(base64_encode(trim($ids)));
?>
    <script type="text/javascript">
       window.open('<?= $current_url ?>', '_blank');
    </script>
<?php
}
?>