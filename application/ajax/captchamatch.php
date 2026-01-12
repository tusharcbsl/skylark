<?php
session_start();

if(isset($_GET['ccaptha']))
{
 $_SESSION['captcha_code']=$_GET['ccaptha'];
 echo  $_SESSION['captcha_code'];
}
$ccode=$_SESSION['captcha_code'] ;
if(isset($_GET['id']))
{
$res=$_GET['id']; 
if($res==$ccode)
{
   $data= array("status"=>"true");
  echo  json_encode($data);
}
else
{
    $data= array("status"=>"false");
  echo  json_encode($data); 
}
}

