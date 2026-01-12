<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//require_once './application/config/database.php';

require_once './application/config/database.php';
require_once './mailServerInt.php';
date_default_timezone_set("Asia/Kolkata"); 
$date=date("d-M-Y");
//if(!isset($_POST['token'], $_POST['ID'])){
//   echo "Unauthrized Access";  
//}

if(isset($_POST['ID'])){
    $users=mysqli_query($db_con,"select * from tbl_email_config where user_id='$_POST[ID]'") or die('error');
}else{
    $users=mysqli_query($db_con,"select * from tbl_email_config") or die('error');
}

while($rwUsers=mysqli_fetch_assoc($users)){
    $server=$rwUsers['mailServer'];
    $port=$rwUsers['port'];
    $ssl=$rwUsers['ssl'];
    if($ssl==1){
        $ssl="ssl";
    }else{
        $ssl="";
    }
    $valid=$rwUsers['validate'];
    if($valid==1){
        $valid="";
    }else{
        $valid="novalidate-cert";
    }
    $userName=$rwUsers['user_email'];
    $pwd= base64_decode(urldecode($rwUsers['password']));
    $userid=$rwUsers['user_id'];
    //echo $filters='SINCE "'.$date.'"FROM "ashutosh.prabhat@cbslgroup.in"';
    $filters=$rwUsers['filters'];
    $filters= str_replace(",", "",$filters);
    if(empty($filters)){
       $filters='SINCE "'.$date.'"'; 
    }
        
    
   webmail("$server", "$port", "$ssl", "$valid", "$userName", "$pwd",$userid,$db_con,$filters);
   // connectionCheck($server, $port, $ssl, $valid, $userName, $pwd);
}
?>
<script>
setInterval(function(){ location.href='insertMailDMS.php'; }, 3600000);
</script>