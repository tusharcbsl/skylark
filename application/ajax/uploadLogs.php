<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$path=$_POST['path'];
//$file= fopen("uploadLogs/".$path, $mode);
$csv = array_map('str_getcsv', file("../../uploadLogs/".$path));
foreach ($csv as $data){
    echo $data[0].'<br>'.'<br>';
}
?>