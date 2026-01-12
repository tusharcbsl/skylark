<?php

require_once 'connection.php';

//list of group members
if(isset($_POST['userid'])&&!empty($_POST['userid'])){

	 $userid=$_POST['userid'];
	 $sameGroupIDs=array();
	// echo "select * from tbl_bridge_grp_to_um where find_in_set('$userid',user_ids)"; 
     $group= mysqli_query($con, "select * from tbl_bridge_grp_to_um where find_in_set('$userid',user_ids)") or die('Error'. mysqli_error($con));
		
    while($rwGroup= mysqli_fetch_assoc($group)){
        $sameGroupIDs[]=$rwGroup['user_ids'];
    }
	
	$sameGroupIDs= implode(',', $sameGroupIDs);
	
    $sameGroupIDs= explode(",", $sameGroupIDs);
	
    $sameGroupIDs=array_unique($sameGroupIDs);
    sort($sameGroupIDs);
    $sameGroupIDs= implode(',', $sameGroupIDs);
// print_r($sameGroupIDs);
	
   $user = "SELECT distinct user_name,user_id FROM tbl_ezeefile_logs where user_id in($sameGroupIDs) AND user_id!=1  order by user_name asc";	
// print_r($user);
	
	$user_run = mysqli_query($con, $user) or die('Error:' . mysqli_error($con));
    
	$result =array();
    while ($rwUser = mysqli_fetch_assoc($user_run)){
    if($rwUser['user_id']!=1){
		
	$ch=$rwUser['user_name']."&&".$ch1=$rwUser['user_id'] ;
	  array_push($result,$ch);
	}
		  

   }
	
	echo json_encode($result);
	
	
}

//shared Files list 

if(isset($_POST['userID'])&&!empty($_POST['userID'])
  // &&isset($_POST['startIndex'])&&isset($_POST['lastIndex'])
   ){
	
	$userid = $_POST['userID'];
	//$startIndex =$_POST['startIndex'];
   // $lastIndex = $_POST['lastIndex'];

    //echo "SELECT * FROM `tbl_document_share` WHERE from_id = '$userid' limit $startIndex,$lastIndex";
   
   //die;
	$ShDocId = mysqli_query($con, "SELECT * FROM `tbl_document_share` WHERE from_id = '$userid' order by dateShare desc") or die('Error in share id fetch' . mysqli_error($con));

    $totalfiles = mysqli_query($con, "SELECT * FROM `tbl_document_share` WHERE from_id = '$userid'") or die('Error in share id fetch' . mysqli_error($con));


   $totalFileCount = mysqli_num_rows($totalfiles);

   // print_r($totalFileCount);
   // die;
	
	        
	   	  $temp = array();


		  // $res = array();
		  
	
	  if(mysqli_num_rows($ShDocId)> 0){

     
       $response = array();	


	  while ($rwShDocId = mysqli_fetch_assoc($ShDocId)){
		  
		 
		   $doc_ids = $rwShDocId['doc_ids'];
           $ToUserId = $rwShDocId['to_ids'];
		   $dateshared = $rwShDocId['dateShare'];
		  
		   $FromUserName = mysqli_query($con, "SELECT * FROM `tbl_user_master` WHERE user_id = '$userid'") or die('Error in share userNane fetch' . mysqli_error($con));
           $rwFromUserName = mysqli_fetch_assoc($FromUserName);
		  
		   $fromUsername = $rwFromUserName['first_name']." ".$rwFromUserName['last_name'];
		  
           $ShUserName = mysqli_query($con, "SELECT * FROM `tbl_user_master` WHERE user_id = '$ToUserId'") or die('Error in share userNane fetch' . mysqli_error($con));
           $rwUserName = mysqli_fetch_assoc($ShUserName); 
		  
		   $Tousername = $rwUserName['first_name']." ".$rwUserName['last_name'];
		   $toUserId = $rwUserName['user_id'];
		  
		   $docName = mysqli_query($con, "SELECT * FROM tbl_document_master where doc_id ='$doc_ids' ") or die('Error in share userNane fetch' . mysqli_error($con));
		   
		   $docname = mysqli_fetch_assoc($docName);
		  
		   $old_doc_name = $docname['old_doc_name'];
		   $docSlid = $docname['doc_name'];
		   $noOfPages = $docname['noofpages'];
		   $fileType = $docname['doc_extn'];
		   $filepath = $docname['doc_path'];
		   $docid  = $docname['doc_id'];
		   
		   $storagename = mysqli_query($con, "SELECT * FROM tbl_storage_level where sl_id = '$docSlid' ") or die('Error in share userNane fetch' . mysqli_error($con));
		  
		   $strgname = mysqli_fetch_assoc($storagename);
		   $sname =$strgname['sl_name'];
		 
		   //checking is it present in recyclebin (means the file is deleted or not from the main dms)

		   $qryCheckDel = "SELECT count(*) as count  FROM `tbl_document_master` where  flag_multidelete=0 and doc_id='$docid'";
		   $qryRun = mysqli_query($con, $qryCheckDel);
		   $count = mysqli_fetch_assoc($qryRun);
		   $c =  $count['count'];

          // $c = print_r($c);
		  // echo $c;
           //echo ($qryRun);
          // die;
		  

		   if($c==0){

         // echo 'ok';
		   $response['storagename'] = $sname;
		   $response['FromUsername'] = $fromUsername;
		   $response['Tousername'] = $Tousername;
		   $response['Touserid'] = $toUserId;
		   $response['docname'] = $old_doc_name;
		   $response['filetype'] = $fileType;
		   $response['noofpages'] = $noOfPages;
		   $response['filepath'] = $filepath;
		   $response['dateshared'] = $dateshared;
		   $response['docid']= $docid;
		   $response['error'] = 'false';

           array_push($temp,$response); 
		

		   }
           else{

/*echo "no";
*/
           }

		 

		 /*  $response = array();
		   $response['storagename'] = $sname;
		   $response['FromUsername'] = $fromUsername;
		   $response['Tousername'] = $Tousername;
		   $response['Touserid'] = $toUserId;
		   $response['docname'] = $old_doc_name;
		   $response['filetype'] = $fileType;
		   $response['noofpages'] = $noOfPages;
		   $response['filepath'] = $filepath;
		   $response['dateshared'] = $dateshared;
		   $response['docid']= $docid;
		   $response['error'] = 'false';

		   array_push($temp,$response); */
		  
		
	  }

			 
	 }
	
			 		 
		 if(count($temp)==0||empty($temp)){
		 
			 $res = new StdClass;
			 $res->message = "No shared files";
			 $res ->error = "true";
			// $res['message'] = 'No shared files';
			// $res['error'] = 'true';
			 //array_push($temp,$res);
			 echo json_encode($res);
		 
		 }
	
	else{

     $res = new StdClass; 
      $res ->error = "false";
     $res-> totalfiles =$totalFileCount;
     $res-> files = $temp; 
	 
	 echo json_encode($res);
	}
		
		
	

}


//check is there data updated or not 

  if (isset($_POST['USERID'])&&!empty($_POST['USERID'])){

        $userid=$_POST['USERID'];

          	
   $sql= mysqli_query($con, "SELECT * FROM tbl_document_share where from_id ='$userid' order by dateShare desc") or die('Error:' . mysqli_error($con));

  if($sql){


     if(mysqli_affected_rows($con)>0){

     $response= array();
     $response['error'] = 'false'; 
     $response['numrows'] = mysqli_affected_rows($con);
		 echo json_encode($response);
}
else{

     $response= array();
     $response['error'] = 'true'; 
     $response['numrows'] ='0';
  echo json_encode($response);
}


}

}

//Undo shared files


  if (isset($_POST['UndoUserid'])&&!empty($_POST['UndoUserid'])
   &&isset($_POST['UndoDocid'])&&!empty($_POST['UndoDocid'])
	&&isset($_POST['UndoUsername'])&&!empty($_POST['UndoUsername'])	
	&&isset($_POST['UndoIp'])&&!empty($_POST['UndoIp'])
	 
			 )
			 {
       
                $id = $_POST['UndoUserid'];
				$host = $_POST['UndoIp'];
				$username = $_POST['UndoUsername'];
				$docid = $_POST['UndoDocid'];
		
		          date_default_timezone_set("Asia/Kolkata");
	             $date = date('Y-m-d H:i:s');
				
            $undoId = mysqli_query($con, "SELECT * FROM `tbl_document_share` WHERE doc_ids='$docid'") or die('Error:' . mysqli_error($con));
            $rwUndo = mysqli_fetch_assoc($undoId);
            $shareId = $rwUndo['id'];
          
            $undo = mysqli_query($con, "SELECT old_doc_name FROM `tbl_document_master` WHERE doc_id ='$docid'") or die('Error:' . mysqli_error($con));
            $rwundoDocNm = mysqli_fetch_assoc($undo);
            $undoShare = mysqli_query($con, "delete from `tbl_document_share` where id='$shareId' and doc_ids='$docid'") or die('Error:' . mysqli_error($con));
            if ($undoShare) {
                        $log = mysqli_query($con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$id', '$username',null,null,'You undo share Documents $rwundoDocNm[old_doc_name]','$date',null,'$host',null)") or die('error : ' . mysqli_error($con));
                       // echo'<script>taskSuccess("shared-files", "Undo Share Document Successfully !");</script>';
				
				        $response = array();		
						$response['error']="false";
					    $response['message']="Undo Share Document Successfully !";
						$temp = array();
						array_push($temp,$response);		
					    echo json_encode($temp);	
				
                      
                    } else {
                       // echo '<script>taskSuccess("shared-files","Share Document Not Undo")</script>';
				
				        $response = array();		
						$response['error']="true";
					    $response['message']="Share Document Not Undo !";
						$temp = array();
						array_push($temp,$response);		
					    echo json_encode($temp);	
                    }
        }
				 
		


//share files 

 if (isset($_POST['Userid'])&&!empty($_POST['Userid'])
	&&isset($_POST['userids'])&&!empty($_POST['userids'])
   &&isset($_POST['docids'])&&!empty($_POST['docids'])
	&&isset($_POST['username'])&&!empty($_POST['username'])	
	&&isset($_POST['ip'])&&!empty($_POST['ip'])
	 
			 )  {
	             $userid = $_POST['Userid'];
                $fromUser = $_POST['Userid'];
		        $username = $_POST['username'];	 
	 
	            $UseridsArray = json_decode($_POST['userids'],true);     
	            $DocidsArray = json_decode($_POST['docids'],true);     
	 
              //  $ToUser = $_POST['userids'];
	 
	              $ToUser = $UseridsArray; 
	            date_default_timezone_set("Asia/Kolkata");
	            $date = date('Y-m-d H:i:s');
	            $host = $_POST['ip'];
		        $ToUser = implode(",", $ToUser);
	            

	         // echo "To user imploded :".$ToUser;
	 
               // $shareDocIds = $_POST['docids'];
	             $shareDocIds = $DocidsArray;
	             $shareDocIds = implode(",",$shareDocIds);
	 
	         //   echo "sharedocids imploded :".$shareDocIds;
	 
	          
                $shareDocIds = explode(',', $shareDocIds);
                $myuser = explode(',', $ToUser);
	 
	             $temp = array();
	          
	      
                foreach ($shareDocIds as $shareId) {
					
					   
                    foreach ($myuser as $myuserid) {

                        $chkDocId = mysqli_query($con, "select * from tbl_document_share where doc_ids='$shareId' and to_ids ='$myuserid'") or die('Error in check' . mysqli_error($con));
                     
							 $ShUserName = mysqli_query($con, "SELECT * FROM `tbl_user_master` WHERE user_id = '$myuserid'") or die('Error in check' . mysqli_error($con));
           $rwUserName = mysqli_fetch_assoc($ShUserName); 
		   $Tousername = $rwUserName['first_name']." ".$rwUserName['last_name'];
						
						 $ShDocIDname = mysqli_query($con, "SELECT * FROM tbl_document_master where doc_id='$shareId';") or die('Error in check' . mysqli_error($con));
           $rwDocIDname = mysqli_fetch_assoc($ShDocIDname); 
		  $docid =$rwDocIDname['doc_id'];
		  $docname = $rwDocIDname['old_doc_name'];				
							
                        if (mysqli_num_rows($chkDocId) > 0) {
							
								//$response['error']="true";
					            //$response['message']="Document Already Shared !";
					            //echo json_encode($response);	
							
		
					    $response = array();		
						$response['error']="null";
					    $response ['toUsername'] = $Tousername; 
							   $response ['docid'] = $docid;    $response ['docname'] = $docname; 
					    $response['message']="Document Already Shared !";
					
						 array_push($temp,$response);		
					   // echo json_encode($temp);	
							
                            //echo'<script>taskFailed("storageFiles?id=' . $_GET[id] . '","Document Already Shared !");</script>';
                        } else {

                            $shareFiles = mysqli_query($con, "INSERT INTO `tbl_document_share`(`from_id`, `to_ids`, `doc_ids`, `dateShare`) VALUES ('$fromUser','$myuserid','$shareId', '$date')") or die('Error in insert share document' . mysqli_error($con));

                          
                            $shareDocNm = mysqli_query($con, "select old_doc_name from tbl_document_master where doc_id = '$shareId'") or die('Error :' . mysqli_error($con));
                            while ($rwshareDocNm = mysqli_fetch_assoc($shareDocNm)) {
										 
                          $sh=implode(',',$shareDocIds);
							
                                if ($shareFiles) {
                                    $log = mysqli_query($con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `doc_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$userid','$username',null,null, '$sh', 'Storage Document $rwshareDocNm[old_doc_name] Shared','$date',null,'$host',null)") or die('error : ' . mysqli_error($con));
                                    if ($log) {
                                        $message = "Y";
                                    }
                                }
                            }
                            if ($message == "Y") {
						
				           $response = array();
					   	$response['error']="false";
					$response ['toUsername'] = $Tousername; 	
								  $response ['docid'] = $docid;    $response ['docname'] = $docname; 
					    $response['message']="Document shared Successfully !";
						
						 array_push($temp,$response);		
					  //  echo json_encode($temp);	
										 
                        
                            } else {
								
						$response = array();		
					    $response['error']="true";
					    $response['message']="Document not shared !";
					
						array_push($temp,$response);		
					   // echo json_encode($temp);	
										 
						 
					     // echo json_encode($response);	
                              
                            }
                        }
                    }
		 }
	 
	 echo json_encode($temp);
 
 
 }

//shared with me file list
if(isset($_POST['SWMuserid'])&&!empty($_POST['SWMuserid'])){


  $response = array();	
  $userid =$_POST['SWMuserid'];
	
  
  $ShDocId = mysqli_query($con, "SELECT * FROM `tbl_document_share` where to_ids='$userid' order by dateShare desc") or die('Error in share id fetch' . mysqli_error($con));
	
  
  while ($rwShDocId = mysqli_fetch_assoc($ShDocId)) {
		
		 $fromid = $rwShDocId['from_id'];	 
		 $docid = $rwShDocId['doc_ids'];
	     $sharedDate = $rwShDocId['dateShare'];
		 
		 
		  $docName = mysqli_query($con, "SELECT * FROM `tbl_document_master` where doc_id='$docid'") or die('Error in share id fetch' . mysqli_error($con));
	      $rwShDocName = mysqli_fetch_assoc($docName);
		  $docname = $rwShDocName['old_doc_name'];
		  $doctype = $rwShDocName['doc_extn'];
		  $docsize = $rwShDocName['doc_size'];
	      $noOfpages = $rwShDocName['noofpages']; 
		  $docpath = $rwShDocName['doc_path'];
	      $docExt = $rwShDocName['doc_extn'];
	      $slid = $rwShDocName['doc_name'];


       $Shstorage = mysqli_query($con, "SELECT * FROM tbl_storage_level where sl_id = '$slid'") or die('Error in share userNane fetch' . mysqli_error($con)); 
		 $rwStorage = mysqli_fetch_assoc($Shstorage);
		 $storagename = $rwStorage['sl_name'];
		   
		
	    $ShUserName = mysqli_query($con, "SELECT * FROM `tbl_user_master` WHERE user_id = '$fromid'") or die('Error in share userNane fetch' . mysqli_error($con)); 
		 $rwUserName = mysqli_fetch_assoc($ShUserName);
		 $username = $rwUserName['first_name']." ".$rwUserName['last_name'];


		  //checking is it present in recyclebin (means the file is deleted or not from the main dms)

		   $qryCheckDel = "SELECT count(*) as count  FROM `tbl_document_master` where  flag_multidelete=0 and doc_id='$docid'";
		   $qryRun = mysqli_query($con, $qryCheckDel);
		   $count = mysqli_fetch_assoc($qryRun);
		   $c =  $count['count'];
	   
	    
	    if($c==0){

        $temp = array();
	    $temp['docname'] = $docname;
	    $temp['doctype'] = $doctype;
	    $temp['docsize'] = $docsize;
	    $temp['docpath'] = $docpath;
	    $temp['docid'] = $docid;
	    $temp['pagecount']=$noOfpages;
	    $temp['fromUsername']=$username;
	    $temp['shareddate']=$sharedDate;
	    $temp['docExtn'] = $docExt;
	    $temp['storagename'] = $storagename;
	    $temp['error'] ='false';
	  
	    array_push($response,$temp);

	    }
	    else{



	    }

	   /*   $temp = array();
	    $temp['docname'] = $docname;
	    $temp['doctype'] = $doctype;
	    $temp['docsize'] = $docsize;
	    $temp['docpath'] = $docpath;
	    $temp['pagecount']=$noOfpages;
	    $temp['fromUsername']=$username;
	    $temp['shareddate']=$sharedDate;
	    $temp['docExtn'] = $docExt;
	    $temp['docid'] =$docid;
	    $temp['error'] ='false';
	  
	    array_push($response,$temp);*/
	    
		}
  

   echo json_encode($response);



}
  
                

?>