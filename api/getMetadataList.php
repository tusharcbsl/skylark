<?php
require'connection.php';
if(isset($_POST['slid'])&& !empty($_POST['slid'])){

$slid=$_POST['slid'];
 $metadatacount = 2;
  $arrarMeta = array();
	  $metadata = array();

   $metas = mysqli_query($con, "select * from tbl_metadata_to_storagelevel where sl_id='$slid'");
 while ($metaval = mysqli_fetch_assoc($metas)) {
     array_push($arrarMeta, $metaval['metadata_id']);
 }
   $meta = mysqli_query($con, "select * from tbl_metadata_master order by field_name asc");
   while ($rwMeta = mysqli_fetch_assoc($meta)) {
     if (in_array($rwMeta['id'], $arrarMeta)) {
   if ($rwMeta['field_name'] != 'filename') {
  array_push($metadata,$rwMeta['field_name']);
  $metadatacount++;
		
  }
  }
 }

$result=json_encode($metadata);
echo $result;

}





















?>