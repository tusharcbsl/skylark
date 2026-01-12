<?php
require_once './application/config/database.php';
require_once './application/pages/feature-enable-disable.php';


if ($rwdocshare['docshare_enable_disable'] == '1') {
//get document which time is over from valid shared time
   
    $getdocumet = mysqli_query($db_con, "SELECT * FROM `tbl_document_share`");
    while ($rwgetdocument = mysqli_fetch_assoc($getdocumet)) {
        $validdatetime = ($rwgetdocument['doc_share_valid_upto']!='0000-00-00 00:00:00')?$rwgetdocument['doc_share_valid_upto']:"";
        if (isset($validdatetime) && !empty($validdatetime)) {
            
            echo "deleted";
            $deletesharedoc = mysqli_query($db_con, "DELETE FROM `tbl_document_share` WHERE doc_share_valid_upto <='$date' and doc_ids='".$rwgetdocument['doc_ids']."'");
        }else{
            echo "here";
        }
    }
}
?>