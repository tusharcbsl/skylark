<?php 
    require_once '../../mailServerInt.php';
    $mailServer=$_POST['mail'];
    $username=$_POST['email'];
    $password=$_POST['pwd'];
    $port=$_POST['port'];
    $ssl=$_POST['ssl'];
    if($ssl==1){
        $ssl="ssl";
    }else{
        $ssl="";
    }
    $validate=$_POST['valid'];
    if($validate==0){
        $validate="novalidate-cert";
    }else{
        $validate="";
    }
    
    $conEmail=connectionCheck($mailServer, $port, $ssl, $validate, $username, $password);// or die(print_r(error_get_last()));
    if($conEmail){
        echo '1';
    } else {
       echo'0'; 
    }
?>