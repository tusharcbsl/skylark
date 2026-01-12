<?php

require 'connection.php';

//getting the files list of recyclebin items
if(isset($_POST['id'])&&!empty($_POST['id'])){
	

 $recycle = mysqli_query($con, "SELECT * FROM `tbl_document_master` WHERE flag_multidelete=0 order by dateposted desc") or die('Error:' . mysqli_error($con));
 
 $row=mysqli_fetch_all($recycle,MYSQLI_ASSOC);
 echo json_encode($row);    
 
 } 
//code for restoring firls from recyclebin
if (isset($_POST['resdocid'])&&!empty($_POST['resdocid'])
 && isset($_POST['resuserid'])&&!empty($_POST['resuserid'])
  && isset($_POST['resusername'])&&!empty($_POST['resusername'])
   && isset($_POST['resip'])&&!empty($_POST['resip'])
) {
    //$DocId = filter_input(INPUT_POST, "DocId");
	 $DocId = $_POST['resdocid'];
	 $userid = $_POST['resuserid'];
	 $host = $_POST['resip'];
	 $username = $_POST['resusername'];
    date_default_timezone_set("Asia/Kolkata");
    $date = date("Y-m-d H:i");
	 
	 
    $DocName = mysqli_query($con, "SELECT old_doc_name FROM `tbl_document_master` WHERE doc_id = '$DocId'") or die('Error in old name' . mysqli_error($con));
    $rwDocName = mysqli_fetch_assoc($DocName) or die('Error old doc name' . mysqli_error($con));
    $RestoreFile = ("UPDATE `tbl_document_master` SET flag_multidelete=1 WHERE doc_id = '$DocId'") or die('Error in update database' . mysqli_error($con));
    $rwRestoreFile = mysqli_query($con, $RestoreFile) or die('Error faq' . mysqli_error($con));
    if ($rwRestoreFile) {
        $log = mysqli_query($con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$userid', '$username',null,null,'Document $rwDocName[old_doc_name]; Recycle','$date',null,'$host',null)") or die('error : ' . mysqli_error($con));
       // echo'<script>taskSuccess("recycle", "document Restore Successfully !");</script>';
           
        $response = array();
        $response['error'] ='false';
        $response['message'] ='Document Restore Successfully !';
        echo json_encode($response);

		  
    } else {

         $response = array();
        $response['error'] ='true';
        $response['message'] ='Document Recycling Failed ! !';
         echo json_encode($response);

       // echo'<script>taskFailed("recycle", "document Recycling Failed !");</script>';
    }
    mysqli_close($con);
}



//Delete recycle bin files
if (isset($_POST['deldocid'])&&!empty($_POST['deldocid'])
 && isset($_POST['deluserid'])&&!empty($_POST['deluserid'])
  && isset($_POST['delusername'])&&!empty($_POST['delusername'])
   && isset($_POST['delip'])&&!empty($_POST['delip'])
) {
    $reDel = $_POST['deldocid'];
    $userid = $_POST['deluserid'];
	 $host = $_POST['delip'];
	 $username = $_POST['delusername'];
    date_default_timezone_set("Asia/Kolkata");
    $date = date("Y-m-d H:i");

    $DelRestore = mysqli_query($con, "SELECT old_doc_name FROM `tbl_document_master` WHERE doc_id = '$reDel'") or die('Error in delete file' . mysqli_error($con));
    $rwDelRestore = mysqli_fetch_assoc($DelRestore) or die('Error file del fetch' . mysqli_error($con));
    $delfrmShre = mysqli_query($con, "DELETE FROM `tbl_document_share` WHERE doc_ids='$reDel'") or die("Error in del" . mysqli_error($con));
    $delrecycle = "DELETE FROM `tbl_document_master` WHERE doc_id = '$reDel'" or die('Error in dd' . mysqli_error($con));
    $Rwdelrecycle = mysqli_query($con, $delrecycle) or die('Error file del' . mysqli_error($con));

    if ($Rwdelrecycle) {
        $log = mysqli_query($con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$userid', '$username',null,null,'Document $rwDelRestore[old_doc_name]; Deleted from Recycle Bin','$date',null,'$host',null)") or die('error : ' . mysqli_error($con));
        //echo'<script>taskSuccess("recycle", "file Deleted Successfully !");</script>';
 $response = array();
        $response['error'] ='false';
        $response['message'] ='File Deleted Successfully !';
echo json_encode($response);

    } else {
       // echo'<script>taskFailed("recycle", "Failed to Delete!");</script>';
        $response = array();
        $response['error'] ='true';
        $response['message'] ='Failed to Delete!';
        echo json_encode($response);

    }
    mysqli_close($con);
}


//multiple restore
if (isset($_POST['mulResdocid'])&&!empty($_POST['mulResdocid'])
 && isset($_POST['mulResuserid'])&&!empty($_POST['mulResuserid'])
  && isset($_POST['mulResusername'])&&!empty($_POST['mulResusername'])
   && isset($_POST['mulResip'])&&!empty($_POST['mulResip'])
) {
    //$DocIds = filter_input(INPUT_POST, "reDel");
 
	
	//$DocIdsArray = array();
    $DocIdsArray = json_decode($_POST['mulResdocid'],true);
	
	//print_r($DocIds);
	//echo json_encode($DocIds);
	
	//die;
	 $userid = $_POST['mulResuserid'];
	 $host = $_POST['mulResip'];
	 $username = $_POST['mulResusername'];
     date_default_timezone_set("Asia/Kolkata");
     $date = date("Y-m-d H:i");
	
	   $DocIds = $DocIdsArray;
	   $DocIds = implode(",",$DocIdsArray);
	

    $respMultiRes = array();
	
	for($i=0;$i<count($DocIds);$i++){
    $DocName = mysqli_query($con, "SELECT old_doc_name FROM `tbl_document_master` WHERE doc_id in($DocIds)") or die('Error in old name' . mysqli_error($con));


    $RestoreFile = mysqli_query($con, "UPDATE `tbl_document_master` SET flag_multidelete=1 WHERE doc_id in($DocIds)") or die('Error in update database' . mysqli_error($con));

    if ($RestoreFile) {
        while ($rwDocName = mysqli_fetch_assoc($DocName)) {
            $log = mysqli_query($con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$userid', '$username',null,null,'Document $rwDocName[old_doc_name]; Recycle','$date',null,'$host',null)") or die('error : ' . mysqli_error($con));
            if ($log) {
                $message = 1;
            }
        }
        if ($message == 1) {

            //echo'<script>taskSuccess("recycle", "document Restore Successfully !");</script>';
            $temp = array();
            $temp['message']= "Files Restored successfully"; 
            $temp['error'] ='false';
            array_push($respMultiRes,$temp);
            
              
        } else {
          //  echo'<script>taskFailed("recycle", "document Restore Failed !");</script>';
            $temp = array();
            $temp['message']="Files Restored failed"; 
            $temp['error'] ='true';
            array_push($respMultiRes,$temp);  



        }
    } 
	
	}
	
	/*else {
        //echo'<script>taskFailed("recycle", "document Restore Failed !");</script>';
            $temp = array();
            $temp[message]= $DocName." "."restored failed"; 
            $temp[error] ='true';
            array_push($respMultiRes,$temp);  
    }*/
    
   echo json_encode($respMultiRes);
	
    mysqli_close($con);
}

//multi delete 

if (isset($_POST['mDocid'])&&!empty($_POST['mDocid'])
 && isset($_POST['mUserid'])&&!empty($_POST['mUserid'])
  && isset($_POST['mUsername'])&&!empty($_POST['mUsername'])
   && isset($_POST['mIp'])&&!empty($_POST['mIp'])
) {                                                 
    
	
    $DocIdsArray =array();
	
    $user_id4 = $_POST['mUserid'];
	$host = $_POST['mIp'];
	$username = $_POST['mUsername'];  
    $DocIdsArray = json_decode($_POST['mDocid'],true);
	 $docDelete = implode(",",$DocIdsArray);
	 date_default_timezone_set("Asia/Kolkata");
	 $date = date('Y-m-d H:i:s');	 
	//print_r($docDelete);
     //$docDelete = explode(',',$docDel); 

   // = $_POST['muldeldocid'];

  
 //  print_r($user_id4);
   // print_r($docDelete);

    $chekUsr = mysqli_query($con, "select * from tbl_bridge_role_to_um where FIND_IN_SET('$user_id4', user_ids) > 0") or die('Error:' . mysqli_error($con));
    $rwcheckUser = mysqli_fetch_assoc($chekUsr);
	
	//print_r($docDelete);
	
    $getDocPath = mysqli_query($con, "select doc_path,old_doc_name,doc_name from tbl_document_master where doc_id in ($docDelete)") or die('Error:' . mysqli_error($con));
    while ($rwgetDocPath = mysqli_fetch_assoc($getDocPath)) {
        $filePath[] = $rwgetDocPath['doc_path'];
        $path = substr($rwgetDocPath['doc_path'], 0, strrpos($rwgetDocPath['doc_path'], '/') + 1);
        $pathtxt[] = 'extract-here/' . $path;
        $filename[] = $rwgetDocPath['old_doc_name'];
        $storgId = $rwgetDocPath['doc_name'];
    }
    if ($rwcheckUser['role_id'] != 1) {
      
   
       $temp = array();
        foreach ($filePath as $filePaths) {
            $path = 'extract-here/' . $filePaths;

            unlink($path);
        }
        $del = mysqli_query($con, "DELETE FROM tbl_document_master WHERE doc_id in($docDelete)") or die('Error:' . mysqli_error($con));

        if ($del) {
            foreach ($filename as $filenames) {
                $log = mysqli_query($con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$user_id4', ' $username',null,null,'Storage Document $filenames Deleted','$date',null,'$host',null)") or die('error : ' . mysqli_error($con));
            }
          
          if($log){
          
           $response= array();
            $response['message'] ='Document Deleted Successfully !';  
            $response['error'] = 'false';
            array_push($temp,$response);
          	 echo json_encode($temp);
          }
          
          else{
           $response = array();
            $response['message'] ='Document not Deleted  !';  
            $response['error'] = 'true';
            array_push($temp,$response);
          	echo json_encode($temp);
          
          }
          
           
      
			//echo json_encode($temp);
            //echo json_encode($response);
 
            //echo'<script>taskSuccess("Recycle","Document Deleted Successfully !");</script>';
        } else {
          
            
            $response = array();
            $response['message'] ='Document not Deleted  !';  
            $response['error'] = 'true';
            array_push($temp,$response);
          	echo json_encode($temp);
			 
           

            //echo'<script>taskFailed("Recycle","Document not Deleted  !");</script>';
        }
		
	
    }
  

}





?>