<?php

$file = "hindi.json";
$data = file_get_contents($file);
$arraydata = json_decode($data, true);
//print_r($arraydata);
//echo $arraydata['hindi']['Navigation'];
?>