<?php

require_once 'connection.php';

 if(isset($_POST['userid'])&&!empty($_POST['userid'])){

 	$userid = $_POST['userid'];

    $chekUsr = mysqli_query($con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$userid', user_ids) > 0") or die('Error:' . mysqli_error($con));
    //$rwgetRole = mysqli_fetch_assoc($chekUsr);
 

    
    if(mysqli_num_rows($chekUsr)>0){

    $userrole = mysqli_fetch_all($chekUsr,MYSQLI_ASSOC);
    $response = array();
    $response['msg'] = 'Role fetched Succesfully';
    $response['error'] = 'false';
    $response['roles'] = $userrole;
    echo json_encode($response);   
    }

     else{

    $response = array();
    $response['msg'] = 'Role Fetched Failed No user Found';
    $response['error'] = 'true';
    echo json_encode($response);   

    }


 }
  

?>