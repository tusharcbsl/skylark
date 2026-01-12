<?php

if(isset($_POST['userid']) && !empty($_POST['userid']) &&  !empty($_POST['token']) && isset($_POST['token'])){
	

	$userid = $_POST['userid'];
	$tokenid = $_POST['tokenid'];

     $updateToken= mysqli_query($con,"update tbl_user_master set fb_tokenid= '$tokenid' where user_id='$userid'");
      
      if($updateToken){
         
        $temp = array();
        $temp['msg'] = 'Token updated succesfully';
        $temp['error'] = 'false';

        echo json_encode($temp);



      }

      else{

         $temp = array();
        $temp['msg'] = 'Token updattion failed';
        $temp['error'] = 'true';

        echo json_encode($temp);

      }


}


?>