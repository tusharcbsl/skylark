<?php

require_once 'connection.php';


if (isset($_POST['mulMove_lastMoveId'])&&!empty($_POST['mulMove_lastMoveId'])
		&&isset($_POST['mulMove_doc_id_smove_multi'])&&!empty($_POST['mulMove_doc_id_smove_multi'])
		&&isset($_POST['mulMove_moveToParentId'])&&!empty($_POST['mulMove_moveToParentId'])
		&&isset($_POST['mulMove_sl_id_move_multi'])&&!empty($_POST['mulMove_sl_id_move_multi'])
		&&isset($_POST['mulMove_username'])&&!empty($_POST['mulMove_username'])
		&&isset($_POST['mulMove_ip'])&&!empty($_POST['mulMove_ip'])
		&&isset($_POST['mulMove_userid'])&&!empty($_POST['mulMove_userid']))
		

{
                $to = $_POST['mulMove_lastMoveId'];//jisme karani hai file move user doc to ezeefile(slid)
							  
	              
	               $dpthQry =  mysqli_query($con, "SELECT sl_depth_level FROM tbl_storage_level where sl_id='$to'") or die('Error' . mysqli_error($con));
	
                 $dpthlevel = mysqli_fetch_assoc($dpthQry);
	         
	             $level =$dpthlevel['sl_depth_level'];//ezzefile level depth
	             	                      
                $mutiId = $_POST['mulMove_doc_id_smove_multi'];// docids
	            $mutiId = json_decode($mutiId);
	            
	         
	
	           $mutiId = implode(",",$mutiId);
		
                $doc_id_smove_multi = explode(',', $mutiId);
                $moveToParentId = $_POST['mulMove_moveToParentId'];//user manual doc (slid)
                $sl_id_move = $_POST['mulMove_sl_id_move_multi'];//user manual doc
	              $userid = $_POST['mulMove_userid'];
	              $username = $_POST['mulMove_username'];
	              $host = $_POST['mulMove_ip']; 
	
	               date_default_timezone_set("Asia/Kolkata");
                 $date = date("Y-m-d H:i");   
	
	 $message='';
	
	  $qry = mysqli_query($con,"SELECT * FROM tbl_storage_level where sl_id ='$to'");
	        $fname = mysqli_fetch_assoc($qry);
	        $fName= $fname['sl_name'];
	
                $length = count($doc_id_smove_multi);
                if (isset($moveToParentId) && isset($doc_id_smove_multi)) {


                    foreach ($doc_id_smove_multi as $doc_id_smove_multis) 

                    {
						//echo "select old_doc_name,doc_path from tbl_document_master where doc_id in($mutiId)";
						//die;
                         $from_moveDocNm = mysqli_query($con, "select old_doc_name,doc_path from tbl_document_master where doc_id in($mutiId)") or die('Error' . mysqli_error($con));
                       
					
						$from_rwMoveNm = mysqli_fetch_assoc($from_moveDocNm);

						//print_r($from_rwMoveNm);
						//die;

                        $fromDocPath = "../extract-here/" . $from_rwMoveNm['doc_path'];

                       //echo $fromDocPath;
                       //die;
									
					    $updateMoveDoc = "update tbl_document_master set doc_name = '$to' where doc_id ='$doc_id_smove_multis'";
                        mysqli_query($con, $updateMoveDoc) or die('Error' . mysqli_error($con));
							//echo "select old_doc_name from tbl_document_master where doc_id in($mutiId)";
							//die;
                        $moveDocNm = mysqli_query($con, "select old_doc_name from tbl_document_master where doc_id in($mutiId)") or die('Error' . mysqli_error($con));
                        $rwMoveNm = mysqli_fetch_assoc($moveDocNm);
                        $movestrgeNm = mysqli_query($con, "select sl_name from tbl_storage_level where sl_id ='$to'") or die('Error' . mysqli_error($con));
                        $rwmovestrgeNm = mysqli_fetch_assoc($movestrgeNm);
                        $doc_EncryptFile = explode('/', $fromDocPath);
                        $doc_Encrypt_nm = end($doc_EncryptFile);

                      // echo $doc_Encrypt_nm;

                        //die;

                        $dir_to = "../extract-here/" . $rwmovestrgeNm['sl_name'];

                        
                        if (!is_dir($dir_to)) {
                            mkdir($dir_to,0777,TRUE);
                        }


                        $dir = "extract-here/" . $rwmovestrgeNm['sl_name'];
                        $doc_Path_copy_to = $dir . "/" . $doc_Encrypt_nm;
                        $pathArray = explode('/', $doc_Path_copy_to);

                        //print_r($pathArray);

                        //die;

                        


                        array_shift($pathArray);

                       // print_r($pathArray);
                        $db_copy_Path_to = implode('/', $pathArray);
                        
                       //  echo $fromDocPath."\n";
                       //  echo $doc_Path_copy_to;


                        //die;

                        copy($fromDocPath,$doc_Path_copy_to);

                        unlink($fromDocPath);


                        mysqli_query($con, "update tbl_document_master set doc_path = '$db_copy_Path_to' where doc_id ='$doc_id_smove_multis'") or die('Error' . mysqli_error($con));
						$log = mysqli_query($con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`,`doc_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$userid', '$username',null,'$to', '$mutiId','$fName Storage Document $rwMoveNm[old_doc_name] moved to Storage $rwmovestrgeNm[sl_name]','$date',null,'$host','')") or die('error : ' . mysqli_error($con));
                        if ($log) {
							
                            $message = 1;
                        }
						
					
						
						/*else{
						
			$res = array();
			$res['message'] = 'Failed to move Files !';
			$res['error'] = 'true';
			echo json_encode($res);
                    }*/
							
						
						}
					
					
					if ($message == 1) {
                      //  echo'<script>taskSuccess("storageFiles?id=' . $pageid . ((isset($_GET[start])) ? ('&start=' . $_GET[start]) : '') . '","Files moved Successfully !");</script>';
											
												$res = array();
			$res['message'] = 'Files moved Successfully !';
			$res['error'] = 'false';
			echo json_encode($res);
											
                    } else {
                        //echo'<script>taskFailed("storageFiles?id=' . $pageid . ((isset($_GET[start])) ? ('&start=' . $_GET[start]) : '') . '","Failed to move Files !");</script>';
											
			$res = array();
			$res['message'] = 'Failed to move Files !';
			$res['error'] = 'true';
			echo json_encode($res);
                    }
						
                     
                       
                    }
                    
               
            }


/** Copy Multiple  files **/


if (isset($_POST['mulCopy_toSlid'])&&!empty($_POST['mulCopy_toSlid'])
		&&isset($_POST['mulCopy_doc_ids'])&&!empty($_POST['mulCopy_doc_ids'])
		&&isset($_POST['mulCopy_copyToParentId'])&&!empty($_POST['mulCopy_copyToParentId'])
		&&isset($_POST['mulCopy_fromSlid'])&&!empty($_POST['mulCopy_fromSlid'])
		&&isset($_POST['mulCopy_username'])&&!empty($_POST['mulCopy_username'])
		&&isset($_POST['mulCopy_ip'])&&!empty($_POST['mulCopy_ip'])
		&&isset($_POST['mulCopy_userid'])&&!empty($_POST['mulCopy_userid']))
{
	
	          $host = $_POST['mulCopy_ip'];
	          $username = $_POST['mulCopy_username'];
	          $userid = $_POST['mulCopy_userid'];
	
	          $to = $_POST['mulCopy_toSlid'];// destination slid 
            $to = mysqli_real_escape_string($con, $to);
	          $dpthQry =  mysqli_query($con, "SELECT sl_depth_level FROM tbl_storage_level where sl_id='$to'") or die('Error' . mysqli_error($con));
	          $dpthlevel = mysqli_fetch_assoc($dpthQry);
	          $level =$dpthlevel['sl_depth_level'];//ezzefile level depth
            //$level = $_POST['lastMoveIdLevel'];
	          $level = mysqli_real_escape_string($con, $level);
            $doc_ids = $_POST['mulCopy_doc_ids'];
	        $doc_ids = json_decode($doc_ids); 
	        $doc_ids = implode(",",$doc_ids); 
	
	      
	
            //$doc_names = $_POST['doc_names'];
            $doc_ids = mysqli_real_escape_string($con, $doc_ids);
            //$doc_names = mysqli_real_escape_string($con, $doc_names);
            $copyToParentId = $_POST['mulCopy_copyToParentId'];//parent id means root folder id 
            $copyToParentId = mysqli_real_escape_string($con, $copyToParentId);
            $sl_id4 = $_POST['mulCopy_fromSlid'];//current folder slid	
            $sl_id4 = mysqli_real_escape_string($con, $sl_id4);
            $meta = mysqli_query($con, "select * from tbl_metadata_to_storagelevel where sl_id='$sl_id4'"); //?
           // $test=  "select * from tbl_document_master where doc_id in($doc_ids) and doc_name in($doc_names)";
            // echo'<script>alert("'.$doc_names.' ");</script>';
           $fetchresult = mysqli_query($con, "select * from tbl_document_master where doc_id in($doc_ids) and doc_name='$sl_id4'");
             //$fetchresult = mysqli_query($con, "select * from tbl_document_master where doc_id in($doc_ids) and doc_name in($doc_names)") or die('Error :' . mysqli_error($con));
	
	            date_default_timezone_set("Asia/Kolkata");
                 $date = date("Y-m-d H:i");     
	
	
            $copyLaststrg = mysqli_query($con, "select sl_name from tbl_storage_level where sl_id = '$to'") or die('Error :' . mysqli_error($con));
            $rwcopyLaststrg = mysqli_fetch_assoc($copyLaststrg);
            $rowcount = mysqli_num_rows($fetchresult);

            $rowmultifield = mysqli_fetch_field($fetchresult);
            
            while ($rowmulticopy = mysqli_fetch_array($fetchresult)) {
               
                $doc_extn = $rowmulticopy['doc_extn'];
                $old_doc_name = $rowmulticopy['old_doc_name'];
                $doc_path = "../extract-here/" . $rowmulticopy['doc_path'];
				
				if(file_exists($doc_path))

					

				{
				
				//echo 'ok file exists';
					
				$uploaded_by = $rowmulticopy['uploaded_by'];
                $doc_size = $rowmulticopy['doc_size'];

                $doc_EncryptFile = explode('/', $doc_path);
                $doc_Encrypt_nm = end($doc_EncryptFile);
                $dir_to = "extract-here/" . $rwcopyLaststrg['sl_name'];

                if (!is_dir($dir_to)) {
                    mkdir($dir_to,0777,TRUE);
                }



                $dir = "extract-here/" . $rwcopyLaststrg['sl_name'];

              // echo $dir;

               // die;

                $doc_Path_copy_to = $dir . "/" . $doc_Encrypt_nm;
                $pathArray = explode('/', $doc_Path_copy_to);




                array_shift($pathArray);

                $db_copy_Path_to = implode('/', $pathArray);
                 
                //echo '../../'.$db_copy_Path_to;

              //  die;

                // echo "before copy";

                copy($doc_path, '../../'.$doc_Path_copy_to);

                  // echo "after copy";

                //die;

 
                $sql2 = "INSERT INTO tbl_document_master SET";
                $sql2 .= " doc_name='$to',old_doc_name='$old_doc_name',doc_extn='$doc_extn',doc_path='$db_copy_Path_to',uploaded_by='$uploaded_by',doc_size='$doc_size',dateposted='$rowmulticopy[dateposted]',noofpages='$rowmulticopy[noofpages]'";


               // echo 'inserted succesfully';

                while ($rwMeta = mysqli_fetch_assoc($meta)) {


                    $metan = mysqli_query($con, "select field_name from tbl_metadata_master where id='$rwMeta[metadata_id]'");
                    $rwMetan = mysqli_fetch_assoc($metan);

                    $field = $rwMetan['field_name'];
                    $value = $rowmulticopy[$field];
                    $sql2 .= ",$field ='$value'";

                }
              
                $multicopyinsert = mysqli_query($con, $sql2);

               // echo 'multicopy inserted';


                if ($multicopyinsert) {
                    $log = mysqli_query($con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `doc_id`,`action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$userid', '$username',null,'$to', '$rowmulticopy[doc_id]','Storage document $old_doc_name copy to Storage $rwcopyLaststrg[sl_name].','$date',null,'$host','')") or die('Error DB: ' . mysqli_error($con));

                    //  echo 'multicopy log inserted';

                    if ($log) {

                      // echo 'yes';


                        $message = "yes";
                    }
					
					
					  if ($message == "yes") {
							
                //echo'<script>taskSuccess("storageFiles?id=' . $_GET[id] . '","Document Copy Successfully !");</script>';
							
			 $res = array();
			$res['message'] = 'Document Copy Successfully !';
			$res['error'] = 'false';
			echo json_encode($res);
							
							
							
            }
		else{
				
				
			$res = array();
			$res['message'] = 'Document Copy  Failed !';
			$res['error'] = 'true';
			echo json_encode($res);
				
				}		   
					
					
					
                }
					
					
				
				}
				   
				
				   
                  
           
}
}

/** Delete Multiple  files (working)**/ 

if(isset($_POST['delMultiple'])&&!empty($_POST['delMultiple'])
	 &&isset($_POST['delSlid'])&&!empty($_POST['delSlid'])
	 &&isset($_POST['delFileDocids'])&&!empty($_POST['delFileDocids'])
	 &&isset($_POST['delUserid'])&&!empty($_POST['delUserid'])
	 &&isset($_POST['delIp'])&&!empty($_POST['delIp'])
	 &&isset($_POST['delUsername'])&&!empty($_POST['delUsername'])
		
	)
{
	$permission = $_POST['delMultiple'];//permission alloted to the user
	$del_sl_id = $_POST['delSlid'];//slid of the folder
	
		
	$docDelete = $_POST['delFileDocids'];// array of delfileids 
	$doclist = json_decode($docDelete);
	$doclist = array_unique($doclist);
	$docDelete = implode(",",$doclist);
	
	//print_r($docDelete);
	//die;
	$user_id4 = $_POST['delUserid'];//userid
	$host = $_POST['delIp'];//ip 
	$username = $_POST['delUsername'];
	date_default_timezone_set("Asia/Kolkata");
    $date = date("Y-m-d H:i");        
     
	
	
	$chekUsr = mysqli_query($con, "select * from tbl_bridge_role_to_um where FIND_IN_SET('$user_id4', user_ids) > 0") or die('Error:' . mysqli_error($con));
	$rwcheckUser = mysqli_fetch_assoc($chekUsr);
	$getDocPath = mysqli_query($con, "select doc_path,old_doc_name,doc_name from tbl_document_master where doc_id in($docDelete)") or die('Error:' . mysqli_error($con));
	while ($rwgetDocPath = mysqli_fetch_assoc($getDocPath)) {
		$filePath[] = $rwgetDocPath['doc_path'];
		$path = substr($rwgetDocPath['doc_path'], 0, strrpos($rwgetDocPath['doc_path'], '/') + 1);
		$pathtxt[] = '../extract-here/' . $path;
		$filename[] = $rwgetDocPath['old_doc_name'];
		$storgId = $rwgetDocPath['doc_name'];
	}
	if ($rwcheckUser['role_id'] == 1 && $permission == "Yes") {
		$del = mysqli_query($con, "DELETE FROM tbl_document_master WHERE doc_id in($docDelete)") or die('Error:' . mysqli_error($con));
		foreach ($filePath as $filePaths) {
			$path = '../extract-here/' . $filePaths;
	
          
			unlink($path);
		}
		if ($del) {
			foreach ($filename as $filenames) {
				$log = mysqli_query($con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$user_id4', '$username',null,null,'Storage Document $filenames Deleted','$date',null,'$host',null)") or die('error : ' . mysqli_error($con));
			}

			//echo'<script>taskSuccess("storageFiles?id=' . urlencode(base64_encode($storgId)) . '","Document Deleted Successfully !");</script>';
			
			$res = array();
			$res['message'] = 'Document Deleted Successfully !';
			$res['error'] = 'false';
			echo json_encode($res);
			
			
			
			
		} else {
			
			$res = array();
			$res['message'] = 'Document not Deleted  !';
			$res['error'] = 'true';
			echo json_encode($res);
			
			//echo'<script>taskFailed("storageFiles?id=' . urlencode(base64_encode($storgId)) . '","Document not Deleted  !");</script>';
		}
	} elseif ($rwcheckUser['role_id'] == 1 && $permission == "No") {
		
		$deletefilename1 = mysqli_query($con, "UPDATE tbl_document_master SET flag_multidelete=0 WHERE doc_id in($docDelete)") or die('Error:' . mysqli_error($con));
		if ($deletefilename1) {
			foreach ($filename as $filenames) {
				$log = mysqli_query($con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$user_id4', '$username',null,null,'Storage Document $filenames Deleted','$date',null,'$host',null)") or die('error : ' . mysqli_error($con));
			}

			//echo'<script>taskSuccess("storageFiles?id=' . urlencode(base64_encode($storgId)) . '","Document Deleted Successfully !");</script>';
			$res = array();
			$res['message'] = 'Document Deleted Successfully !';
			$res['error'] = 'false';
			echo json_encode($res);
			
			
			
			
		} else {
			//echo'<script>taskFailed("storageFiles?id=' . urlencode(base64_encode($storgId)) . '","Document not Deleted  !");</script>';
			$res = array();
			$res['message'] = 'Document not Deleted  !';
			$res['error'] = 'true';
			echo json_encode($res);
			
		}
	} else {
		$deletefilename1 = mysqli_query($con, "UPDATE tbl_document_master SET flag_multidelete=0 WHERE doc_id in($docDelete)") or die('Error:' . mysqli_error($con));
		if ($deletefilename1) {
			foreach ($filename as $filenames) {
				$log = mysqli_query($con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$user_id4', '$username',null,null,'Storage Document $filenames Deleted','$date',null,'$host',null)") or die('error : ' . mysqli_error($con));
			}
			//echo'<script>taskSuccess("storageFiles?id=' . urlencode(base64_encode($storgId)) . '","Document Deleted Successfully !");</script>';
			
			$res = array();
			$res['message'] = 'Document Deleted Successfully !';
			$res['error'] = 'false';
			echo json_encode($res);
			
			
		} else {
			//echo'<script>taskFailed("storageFiles?id=' . urlencode(base64_encode($storgId)) . '","Document not Deleted  !");</script>';
			
			$res = array();
			$res['message'] = 'Document not Deleted  !';
			$res['error'] = 'true';
			echo json_encode($res);
		}
	
	
}
	
}

?>