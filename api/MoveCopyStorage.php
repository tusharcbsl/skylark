<?php

         require_once 'connection.php';         

//move storage
        if (isset($_POST['rootFolderSlid'])&&!empty($_POST['rootFolderSlid'])
			  	&&isset($_POST['destinationFolderSlid'])&&!empty($_POST['destinationFolderSlid'])
				&&isset($_POST['moveIp'])&&!empty($_POST['moveIp'])
				&&isset($_POST['moveUsername'])&&!empty($_POST['moveUsername'])
				&&isset($_POST['moveUserId'])&&!empty($_POST['moveUserId'])
		  
			  ) {
			  
			  $userid = $_POST['moveUserId'];
			  $host = $_POST['moveIp'];
			  $rootFolderSlid= $_POST['rootFolderSlid'];
			  $moveToId = $_POST['destinationFolderSlid'];
			  $username = $_POST['moveUsername']; 
			
			 date_default_timezone_set("Asia/Kolkata");
             $date = date("Y-m-d H:i"); 
			 
            //echo $_POST['moveToId']; die;
			  
                $checkDublteStorage = mysqli_query($con, "Select * from tbl_storage_level where sl_id = '$rootFolderSlid'") or die('Error in checkDublteStorage:' . mysqli_error($con));

			  
                $rwcheckDublteStorage = mysqli_fetch_assoc($checkDublteStorage);
                
			       $rootDepthlevel = $rwcheckDublteStorage['sl_depth_level'];
			       $rootFoldername = $rwcheckDublteStorage['sl_name'];
			
                $sql_child = "select * FROM tbl_storage_level WHERE sl_parent_id = '$rootFolderSlid' AND sl_name = '$rwcheckDublteStorage[sl_name]'";

                $sql_child_run = mysqli_query($con, $sql_child) or die('Error:' . mysqli_error($con));

                if (mysqli_num_rows($sql_child_run)) {
                    //$moveToId = $_POST['lastMoveId'];
                    $moveToName = mysqli_query($con, "Select * from tbl_storage_level where sl_id = '$moveToId'") or die('Error in checkDublteStorage:' . mysqli_error($con));
                    $rwmoveToName = mysqli_fetch_assoc($moveToName);
                    $log = mysqli_query($con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$userid', '$username',null,'$moveToId','Storage  $rootFoldername already exist in $rwmoveToName[sl_name].','$date', null,'$host',null)") or die('error : ' . mysqli_error($con));
						 
                   // echo'<script>taskFailed("storage","Storage Name Having Same Name Already Exist !");</script>';
						 
						 $response = array();
						 $response['error'] = 'true';
						 $response['message'] = 'Storage Name Having Same Name Already Exist !';
						 echo json_encode($response);
						 
                } else {
						 						  					 
                    $moveToId = $_POST['destinationFolderSlid'];
                   // $lastMoveIdLevel = $_POST['lastMoveIdLevel'];
						  $lastMoveIdLevel = $rootDepthlevel;
                    $lastMoveIdLevel = $lastMoveIdLevel + 1;

                    $moveStorage = "update tbl_storage_level set sl_parent_id = '$moveToId', sl_depth_level = '$lastMoveIdLevel' where sl_id = '$rootFolderSlid'";
                    $moveStorage_run = mysqli_query($con, $moveStorage) or die('Error in move Stroge : ' . mysqli_error($con));

                    $moveToName = mysqli_query($con, "Select * from tbl_storage_level where sl_id = '$moveToId'") or die('Error in checkDublteStorage:' . mysqli_error($con));
                    $rwmoveToName = mysqli_fetch_assoc($moveToName);

                    $log = mysqli_query($con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$userid', '$username',null,'$moveToId','Storage  $rootFoldername moved to $rwmoveToName[sl_name]','$date',null,'$host',null)") or die('error : ' . mysqli_error($con));
						 if($log){
						  $response = array();
						 $response['error'] = 'false';
						 $response['message'] = 'Storage Moved Successfully !';
						 echo json_encode($response);
                }
						 
						 
						 }
						 
                    //echo'<script>taskSuccess("storage","Storage Moved Successfully !");</script>';
						
			  
			   mysqli_close($con);
            }

//copy storage


  if (isset($_POST['copyrootFolderSlid'])&&!empty($_POST['copyrootFolderSlid'])
			  	&&isset($_POST['copydestinationFolderSlid'])&&!empty($_POST['copydestinationFolderSlid'])
		 	   &&isset($_POST['copydestinationFoldername'])&&!empty($_POST['copydestinationFoldername'])
				&&isset($_POST['copyIp'])&&!empty($_POST['copyIp'])
				&&isset($_POST['copyUsername'])&&!empty($_POST['copyUsername'])
				&&isset($_POST['copyUserId'])&&!empty($_POST['copyUserId'])
		  
			  ){

	  
    // $chkFileStorage = mysqli_query($con, "SELECT * FROM tbl_document_master WHERE doc_name='$copyParentId'") or die('Error:' . mysqli_error($con));
    // $rwchkFileStorage = mysqli_fetch_assoc($chkFileStorage);
    // if(mysqli_num_rows($chkFileStorage) == 0){
	  
           $userid = $_POST['copyUserId'];
			  $host = $_POST['copyIp'];
			  $lastCopyToId= $_POST['copyrootFolderSlid'];
			  $toCopyFolderId = $_POST['copydestinationFolderSlid'];
			  $username = $_POST['copyUsername']; 
	        $toCopyFolderName = $_POST['copydestinationFoldername'];
			
	  date_default_timezone_set("Asia/Kolkata");
          $date = date("Y-m-d H:i"); 
	  
	  
	  
	  copyStorage($toCopyFolderId,$lastCopyToId,$toCopyFolderName,$date,$host);
	  
  }
	  function copyStorage($toCopyFolderId, $lastCopyToId, $toCopyFolderName, $date, $host) {
    // $chkFileStorage = mysqli_query($con, "SELECT * FROM tbl_document_master WHERE doc_name='$copyParentId'") or die('Error:' . mysqli_error($con));
    // $rwchkFileStorage = mysqli_fetch_assoc($chkFileStorage);
    // if(mysqli_num_rows($chkFileStorage) == 0){

    global $con;
	 global $toCopyFolderId;
	 global $toCopyFolderName;
	 global $host;
	 global $username;
    global $userid;
	 global $lastCopyToId;
		  
		  
		  
		  

    $storageCopyName = mysqli_query($con, "select * from tbl_storage_level where sl_id='$toCopyFolderId'") or die('Error: 1' . mysqli_error($con));

    $rwstorageCopyName = mysqli_fetch_assoc($storageCopyName);

    //echo $copyName = $rwstorageCopyName['sl_name'];

    if ($toCopyFolderName) {
        $copyName = $toCopyFolderName;
    }


    $storageCopyToCheck = mysqli_query($con, "select * from tbl_storage_level where sl_parent_id='$lastCopyToId' AND sl_name = '$copyName'") or die('Error:' . mysqli_error($con));

    // echo mysqli_num_rows($storageCopyToCheck); die;

    if (mysqli_num_rows($storageCopyToCheck) < 1) {

        $storageCopyTo = mysqli_query($con, "select * from tbl_storage_level where sl_id='$lastCopyToId'") or die('Error:' . mysqli_error($con));

        $rwstorageCopyTo = mysqli_fetch_assoc($storageCopyTo);

        $copyToName = $rwstorageCopyTo['sl_name'];
        $copyToLevel = $rwstorageCopyTo['sl_depth_level'];
        $copyToLevel = $copyToLevel + 1;
            
      //  echo  $copyToName." ". $copyToLevel." ". $lastCopyToId; 
            
       // die;    

        $insertTo = mysqli_query($con, "insert into tbl_storage_level (sl_name, sl_parent_id, sl_depth_level) values('$copyName', '$lastCopyToId', '$copyToLevel')") or die('Error ss:' . mysqli_error($con));
        $insertId = mysqli_insert_id($con);

        //insert doc of copy record

        $storageCopyNameDoc = mysqli_query($con, "select * from tbl_document_master where doc_name='$toCopyFolderId'") or die('Error: ' . mysqli_error($con));

        $rowcount = mysqli_num_rows($storageCopyNameDoc);

        $result = mysqli_fetch_all($storageCopyNameDoc);

        for ($i = 0; $i < $rowcount; $i++) {

            unset($result[$i][0]); //Remove ID from array
            // print_r($result[$i]); 
            $qrystr = " INSERT INTO tbl_document_master";

            $qrystr .= " VALUES (null, '" . implode("', '", array_values($result[$i])) . "')";

            $insertCopyDoc = mysqli_query($con, $qrystr) or die('Error insert: ' . mysqli_error($con));

            $insertCopyDocId = mysqli_insert_id($con);

            $updateDoc = "update tbl_document_master set doc_name = '$insertId' where doc_id = '$insertCopyDocId'";
            mysqli_query($con, $updateDoc) or die('Error' . mysqli_error($con));
        }
        $sql_child = "select * FROM tbl_storage_level WHERE sl_parent_id = '$toCopyFolderId' ";

        $sql_child_run = mysqli_query($con, $sql_child) or die('Error update:' . mysqli_error($con));



        if (mysqli_num_rows($sql_child_run) > 0) {

            //echo "$child"; die;

            while ($rwchild = mysqli_fetch_assoc($sql_child_run)) {

                $child = $rwchild['sl_id'];

                $childCopyname = $rwchild['sl_name'];

                copyStorage($child, $insertId, null, $date, $host);
            }
        }



        $log = mysqli_query($con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$userid', '$username',null,'$toCopyFolderId','Storage level $copyName copy to $copyToName.','$date',null,'$host','')") or die('error : ' . mysqli_error($con));
       // echo'<script>taskSuccess("storage","Storage Copy Successfully !");</script>';
            
            if($log){
            
                     $response = array();
						 $response['error'] = 'true';
						 $response['message'] = 'Storage Copy Successfully';
						 echo json_encode($response);
            
            }
            
            
            
    } else {
        $storageCopyTo = mysqli_query($con, "select * from tbl_storage_level where sl_id='$lastCopyToId'") or die('Error:' . mysqli_error($con));
        $rwstorageCopyTo = mysqli_fetch_assoc($storageCopyTo);
        $copyToName = $rwstorageCopyTo['sl_name'];

        $log = mysqli_query($con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$userid', '$username',null,'$toCopyFolderId','Storage $copyName exist in $copyToName rename to copy .','$date',null,'$host','')") or die('error : ' . mysqli_error($con));
       // echo'<script>taskFailed("storage","Storage already exist. please rename storage before copy storage.");</script>';
            
            if($log){
            
                $response = array();
						 $response['error'] = 'false';
						 $response['message'] = 'Storage already exist. please rename storage before copy storage';
						 echo json_encode($response);
            }
          
            
    }
}


           
    
  ?>
